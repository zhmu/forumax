<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the thread information
    $query = sprintf ("select forumid,title from threads where id=%s", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0]; $threadtitle = CensorText ($result[1]);

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // need to show the 'post reply' page?
    if ($action == "") {
        // yup. is there a thread title?
	if (trim ($threadtitle) == "") {
	    // no. revert to the default one
	    $threadtitle = $CONFIG["default_topic"];
	}


	// grab the forum title
	$query = sprintf ("select name,catno from forums where id=%s", $forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$forumname = $result[0]; $catid = $result[1];

	// do we have a category id?
	if ($catid != 0) {
	    // yes. grab the category name
	    $query = sprintf ("select name from categories where id=%s", $catid);
 	    $res = db_query ($query); $tmp = db_fetch_results ($res);
 	    $cat_title = $tmp[0];
        }

	// do we need to quote a post?
	if ($quotefrom != "") {
	    // yes. grab the post
	    $query = sprintf ("select post,authorid from posts where id=%s", $quotefrom);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
	    $message = htmlspecialchars ($tmp[0]);

	    // get the author's username
	    $poster_username = GetMemberName ($tmp[1]);

	    // append the template settings to it
	    $tmp = GetSkinTemplate ("quoted_post");
	    eval ("\$message = stripslashes (\"" . htmlspecialchars ($tmp) . "\");");
	}

	// fill in the fields
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];
	$iconlist = BuildIconList();

	// build the page and show it
        ShowHeader("replypage");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("replypage")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // is posting disabled for our account?
    if (($GLOBALS["flags"] & FLAG_DENYPOST) != 0) {
	// yes. complain
	ShowHeader("error_postingdenied");
	print GetSkinTemplate ("error_postingdenied");
	ShowFooter();
	exit;
    }

    // need to post a reply?
    if ($action == "postreply") {
	// yes. is the reply field empty?
	$the_message = trim ($the_message);
	if ($the_message == "") {
	    // yes. complain
	    ShowHeader("error_emptyfields");
	    eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_emptyfields")) . "\");");
	    ShowFooter();
	    exit;
	}

	// figure out the forum id
	$query = sprintf ("select forumid,flags from threads where id=%s", $threadid);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$forumid = $tmp[0]; $thread_flags = $tmp[1];

	// is this thread locked?
	if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	    // yes. complain
	    ShowHeader("error_threadlocked");
	    print GetSkinTemplate ("error_threadlocked");
	    ShowFooter();
	    exit;
	}

	// is this a double post (attempt to post the same message again) ?
	if (IsDoublePost ($the_message) != 0) {
	    // yes. lie and say we posted it (it's a double post)
            ShowHeader("replyokpage");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("replyokpage")) . "\");");
	    print $tmp;

	    ShowFooter();
	    exit;
	}

	// build the forum flags
	$post_flags = 0;
	if ($f_sig != "") { $post_flags = $post_flags | FLAG_POST_SIG; };

	// insert the forum post
	$query = sprintf ("insert into posts values (NULL,%s,%s,%s,now(),'%s','',0,%s,'%s',%s)", $GLOBALS["userid"], $forumid, $threadid, $the_message, $icon_no, $ipaddress, $post_flags);
	db_query ($query);
	$messageid = db_get_insert_id();

	// now, increment the thread count and date
	$query = sprintf ("update threads set nofreplies=nofreplies+1,lastdate=now(),lastposterid=%s where id=%s",$GLOBALS["userid"],$threadid);
	db_query ($query);

	// increment the forum post count
	$query = sprintf ("update forums set nofposts=nofposts+1,lastpost=now(),lastposterid=%s where id=%s",$GLOBALS["userid"],$forumid);
	db_query ($query);

	// increment the number of posts and lasting posting date of this user
	$query = sprintf ("update accounts set nofposts=nofposts+1,lastpost=now(),lastmessage=%s where id=%s", $messageid, $GLOBALS["userid"]);
	db_query ($query);

	// do we need to lock this thread?
	if (($f_close != "") and (IsForumMod ($forumid) != 0)) {
	    // yes. close it
	    $query = sprintf ("update threads set flags=flags or %s,lockerid=%s where id=%s", FLAG_THREAD_LOCKED, $GLOBALS["userid"], $threadid);
            db_query ($query);
	}

	// email out notifications as needed
	NotifyUsers ($forumid, $threadid, $messageid);

	// build the page and show it
        ShowHeader("replyokpage");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("replyokpage")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }
 ?>
