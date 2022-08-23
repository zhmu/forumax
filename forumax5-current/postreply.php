<?php
    //
    // postreply.php
    //
    // This will handle replying to threads.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is a thread id given?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }
    $VAR["threadid"] = $threadid;

    // grab the thread's forum ID
    $forumid = GetThreadForumID ($threadid);
    $VAR["forumid"] = $forumid;

    // get the forum flags
    $query = sprintf ("SELECT flags FROM forums WHERE id='%s'", $forumid);
    list ($forum_flags) = db_fetch_results (db_query ($query));

    // need to show the 'post reply' page?
    if ($_REQUEST["action"] == "") {
        // yup. do we need to quote a post?
	$quotefrom = $_REQUEST["quotefrom"];
	if ($quotefrom != "") {
	    // yes. grab the post
	    $query = sprintf ("SELECT post,authorid,authorname,forumid FROM posts WHERE id='%s'", $quotefrom);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
	    list ($VAR["message"], $poster_id, $VAR["poster_username"], $post_forumid) = db_fetch_results (db_query ($query));
	    $VAR["message"] = htmlspecialchars ($VAR["message"]);

	    // get the author's username
	    if ($poster_id != 0) {
	        $VAR["poster_username"] = GetMemberName ($poster_id);
	    }

	    // make sure the user can read that page
	    HandleRestrictedForum ($post_forumid);

	    // append the template settings to it
	    $VAR["message"] = InsertSkinVars (GetSkinTemplate ("quoted_post"));
	}

	// fill in the fields
	$VAR["iconlist"] = BuildIconList();

	// are we logged in?
	if ($GLOBALS["logged_in"] != 0) {
	    // yes. grab the account thread backlog setting
	    $query = sprintf ("SELECT reply_backlog FROM accounts WHERE id='%s'", $GLOBALS["userid"]);
	    list ($reply_backlog) = db_fetch_results (db_query ($query));
	} else {
	    // use the default setting
	    $reply_backlog = 1;
	}

	// resolve the backlog into LIMIT values
	$blog_limit = 0;
	if ($reply_backlog == 0) { $blog_limit = $CONFIG["reply_maxbacklog"]; };
	if ($reply_backlog == 1) { $blog_limit = 10; };
	if ($reply_backlog == 2) { $blog_limit = 5; };

	// do we have a backlog?
	if ($blog_limit != 0) {
	    // yes. grab it from the database
	    $query = sprintf ("SELECT authorid,post FROM posts WHERE threadid='%s' ORDER BY timestamp DESC LIMIT %s", $threadid, $blog_limit);
	    $res = db_query ($query);

	    // add the backlog
	    while (list ($VAR["authorid"], $back_message) = db_fetch_results ($res)) {
		// grab the author name
		$VAR["authorname"] = GetMemberName ($VAR["authorid"]);
		$VAR["back_message"] = CensorText (FixupMessage ($back_message, $forum_flags));
		$VAR["backlog"] .= InsertSkinVars (GetSkinTemplate ("postreply_backlog"));
	    }
	}

	// set the autosig status as needed
	$VAR["autosig"] = "no";
	if (($GLOBALS["flags"] & FLAG_AUTOSIG) != 0) { $VAR["autosig"] = "yes"; };

	// grab the forum restrictions
	BuildForumRestrictions ($forumid);

	// build the page and show it
	ShowBaseForumPage("replypage", $threadid, $forumid);
	exit;
    }

    // need to post a reply?
    if ($_REQUEST["action"] == "postreply") {
	// yes. is the reply field empty?
	$the_message = trim ($_REQUEST["the_message"]);
	if ($the_message == "") {
	    // yes. complain
	    FatalError ("error_emptyfields");
	}

	// is unregistered posting allowed?
        if (($forum_flags & FLAG_FORUM_UNREGPOST) != 0) {
	    // yes. is a password supplied?
	    if ($_REQUEST["the_password"] == "") {
		// no. does this user account actually exist?
		$query = sprintf ("SELECT id FROM accounts WHERE accountname='%s'", $_REQUEST["the_accountname"]);
		if (db_nof_results (db_query ($query)) > 0) {
		    // yes. complain
		    FatalError ("error_accountregged");
		}

		// unregistered user
		$GLOBALS["userid"] = 0;
	    } else {
		// yes. verify our username and password
		HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);
	    }
	} else {
	    // verify our username and password
	    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);
	}

	// is posting disabled for our account?
	if (($GLOBALS["flags"] & FLAG_DENYPOST) != 0) {
	    // yes. complain
	    FatalError ("error_postingdenied");
	}

	// figure out the thread flags
	$query = sprintf ("SELECT flags FROM threads WHERE id='%s'", $threadid);
	list ($thread_flags) = db_fetch_results (db_query ($query));

	// is this thread locked?
	if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	    // yes. complain
	    FatalError("error_threadlocked");
	    exit;
	}

	// is this a double post (attempt to post the same message again) ?
	$tmp = IsDoublePost ($_REQUEST["the_message"]);
	if ($tmp != 0) {
	    // yes. lie and say we posted it (it's a double post)
	    $VAR["threadid"] = $tmp;
	    ShowForumPage("replyokpage");
	    exit;
	}

	// build the forum flags
	$post_flags = 0;
	if ($_REQUEST["f_sig"] != "") { $post_flags = $post_flags | FLAG_POST_SIG; };
	if ($_REQUEST["f_nosmilies"] != "") { $post_flags = $post_flags | FLAG_POST_NOSMILIES; };

	// insert the forum post
	$query = sprintf ("INSERT INTO posts VALUES (NULL,'%s','%s','%s','%s',NOW(),'%s','',0,'%s','%s','%s')", $GLOBALS["userid"], $_REQUEST["the_accountname"], $forumid, $threadid, $_REQUEST["the_message"], $_REQUEST["icon_no"] + 0, $ipaddress, $post_flags);
	db_query ($query);
	$messageid = db_get_insert_id();

	// now, increment the thread count and date
	$query = sprintf ("UPDATE threads SET nofreplies=nofreplies+1,lastdate=NOW(),lastposterid=%s,lastpostername='%s' WHERE id='%s'",$GLOBALS["userid"],$the_accountname,$threadid);
	db_query ($query);

	// increment the forum post count
	$query = sprintf ("UPDATE forums SET nofposts=nofposts+1,lastpost=NOW(),lastposterid='%s',lastpostername='%s' WHERE id='%s'",$GLOBALS["userid"],$the_accountname,$forumid);
	db_query ($query);

	// posted with a registered account?
	if ($GLOBALS["userid"] != 0) {
	    // yes. increment the number of posts and lasting posting date of this user
	    $query = sprintf ("UPDATE accounts SET nofposts=nofposts+1,lastpost=NOW(),lastmessage='%s' WHERE id='%s'", $messageid, $GLOBALS["userid"]);
	    db_query ($query);
	}

	// do we need to lock this thread?
	if (($f_close != "") and (IsForumMod ($forumid) != 0)) {
	    // yes. close it
	    $query = sprintf ("UPDATE threads SET flags=flags|%s,lockerid='%s' WHERE id='%s'", FLAG_THREAD_LOCKED, $GLOBALS["userid"], $threadid);
            db_query ($query);
	}

	// email out notifications as needed
	NotifyUsers ($forumid, $threadid, $messageid);

	// build the page and show it
	ShowForumPage("replyokpage");
    }
 ?>
