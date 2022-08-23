<?php
    //
    // newtopic.php
    //
    // This will create a new topic in a forum.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is a forum id given?
    $forumid = trim (preg_replace ("/\D/", "", $_REQUEST["forumid"]));
    if ($forumid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }
    $VAR["forumid"] = $forumid;

    // need to show the 'new topic' page?
    if ($_REQUEST["action"] == "") {
        // yup. do it
	$VAR["iconlist"] = BuildIconList();

	// set the auto-sig checkbox as needed
	$VAR["autosig"] = "no";
	if (($GLOBALS["flags"] & FLAG_AUTOSIG) != 0) { $VAR["autosig"] = "yes"; };

	// grab the forum restritions
	BuildForumRestrictions ($forumid);

	// show the page
 	ShowBaseForumPage ("newtopicpage", 0, $forumid);
	exit;
    }

    // handle forum restrictions, if any
    HandleRestrictedForum ($forumid);

    // get the forum flags
    $query = sprintf ("SELECT flags FROM forums WHERE id='%s'", $forumid);
    list ($forum_flags) = db_fetch_results (db_query ($query));

    // is unregistered posting OK?
    if (($forum_flags & FLAG_FORUM_UNREGPOST) != 0) {
	// yes. is a password supplied?
	if ($_REQUEST["the_password"] == "") {
	    // no. does this user account actually exist?
	    if (GetMemberID ($_REQUEST["the_username"]) != "") {
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

    // need to actually post the new topic?
    if ($_REQUEST["action"] == "newtopic") {
	// yes. render any HTML from the thread title useless
	$the_message = $_REQUEST["the_message"];
	$the_subject = trim ($_REQUEST["the_subject"]);
	$the_subject = str_replace ("<", "&lt;", $the_subject);
	$the_subject = str_replace (">", "&gt;", $the_subject);

	// are both fields filled in?
	if (($the_subject == "") or ($the_message == "")) {
	    // no. complain
	    FatalError("error_emptyfields");
	}

	// is this a double post (attempt to post the same message again) ?
	$id = IsDoublePost ($the_message);
	if ($id != 0) {
	    // yes. lie and say we posted it (it's a double post)
	    $VAR["threadid"] = $id;
	    ShowForumPage ("newtopicokpage");
	    exit;
	}

	// fetch the icon number and secure it, just in case
	$icon_no = preg_replace ("/\D/", "", $_REQUEST["icon_no"]) + 0;

	// insert the new thread
	$query = sprintf ("INSERT INTO threads VALUES (NULL,'%s','%s','%s',0,NOW(),'%s','%s',0,'%s','%s','',0,0,0)",$forumid,$the_subject, $icon_no,$GLOBALS["userid"],$_REQUEST["the_accountname"],$GLOBALS["userid"],$_REQUEST["the_accountname"]);
	db_query ($query);
	$threadid = db_get_insert_id(); $VAR["threadid"] = $threadid;

	// build the flags
	$flags = 0;
	if ($_REQUEST["f_sig"] != "") { $flags = $flags | FLAG_POST_SIG; };
	if ($_REQUEST["f_nosmilies"] != "") { $flags = $flags | FLAG_POST_NOSMILIES; };

	// create the post
	$query = sprintf ("INSERT INTO posts VALUES (NULL,'%s','%s','%s','%s',NOW(),'%s','',0,'%s','%s','%s')",$GLOBALS["userid"],$_REQUEST["the_accountname"],$forumid,$threadid,$the_message,$icon_no,$ipaddress,$flags);
	db_query ($query);
	$messageid = db_get_insert_id();

	// increment the forum post count
	$query = sprintf ("UPDATE forums SET nofthreads=nofthreads+1,nofposts=nofposts+1,lastpost=NOW(),lastposterid='%s',lastpostername='%s' WHERE id='%s'",$GLOBALS["userid"],$_REQUEST["the_accountname"],$forumid);
	db_query ($query);

	// do we have a registered member?
	if ($GLOBALS["userid"] != 0) {
	    // increment the number of posts and last posting date of this user
	    $query = sprintf ("UPDATE accounts SET nofposts=nofposts+1,lastpost=NOW(),lastmessage='%s' WHERE id='%s'", $messageid, $GLOBALS["userid"]);
	    db_query ($query);
	}

	// send out notifications as required
	NotifyUsers ($forumid, $threadid, 0);

	// do we need to create a poll?
	if ($_REQUEST["post_poll"] != "") {
	    // yes. forward to the poll creation script
	    Header ("Location: createpoll.php?threadid=" . $threadid);
	    exit;
	}

	// it worked. show the 'yay' page
	ShowForumPage ("newtopicokpage");
    }
 ?>
