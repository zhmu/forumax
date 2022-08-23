<?php
    //
    // printthread.php
    //
    // This will show a printer-friendly view of a thread.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // do we have a valid thread id?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. complain
	FatalError ("error_badrequest");
    }

    // figure out the forum id
    $query = sprintf ("SELECT forumid,flags,title,lockerid,destforum FROM threads WHERE id='%s'", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    list ($VAR["forumid"], $flags, $VAR["threadtitle"], $VAR["lockerid"], $VAR["destforum"]) = db_fetch_results (db_query ($query));

    // is there a thread title?
    if (trim ($VAR["threadtitle"]) == "") {
	// no. revert to the default one
	$VAR["threadtitle"] = $CONFIG["default_topic"];
    }

    // increment the number of pageviews
    $query = sprintf ("UPDATE threads SET nofviews=nofviews+1 WHERE id='%s'", $threadid);
    db_query ($query);

    // grab the template needed
    $postlist_template = addslashes (GetSkinTemplate ("post_list_print"));

    // is private messaging allowed?
    if ($CONFIG["allow_pm"] != 0) {
	// yes. get the template
        $pmuser_template = addslashes (GetSkinTemplate ("pmuser"));
    }

    // select all threads here from the database
    $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"] + 0;
    $query = sprintf ("SELECT id,authorid,post,icon,DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp)+%s),'%s'),DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(edittime)+%s),'%s'),editid,flags FROM posts WHERE threadid='%s' ORDER BY id ASC", $timezone, $CONFIG["post_timestamp_format"],$timezone,$CONFIG["post_timestamp_format"],$threadid);
    $res = db_query ($query);

    // while there are threads, add them
    while (list ($VAR["postid"], $VAR["authorid"], $VAR["message"], $VAR["icon"], $VAR["timestamp"], $VAR["edit_timestamp"], $VAR["edit_accountid"], $postflags) = db_fetch_results ($res)) {
	// grab the values
	$VAR["message"] = FixupMessage ($VAR["message"], $forumflags);

	// grab the author's record
	$query = sprintf ("SELECT accountname,nofposts,DATE_FORMAT(joindate,'%s') FROM accounts WHERE id='%s'",$CONFIG["joindate_timestamp_format"],$VAR["authorid"]);
	$author_res = db_query ($query);

	// got any results?
	if (db_nof_results ($author_res) > 0) {
	    // yes. grab the values
	    list ($VAR["author"], $VAR["author_nofposts"], $VAR["author_joindate"]) = db_fetch_results ($author_res);
	    $VAR["author_status"] = GetMemberStatus ($VAR["authorid"]);
	} else {
	    // no. treat the user as unregistered
	    $VAR["author"] = $CONFIG["delmem_name"];
	    $VAR["author_status"] = $CONFIG["unknown_title"];
	    $VAR["author_nofposts"] = $CONFIG["delmem_postcount"];
	    $VAR["author_joindate"] = $CONFIG["delmem_joindate"];
	}

	// has this item been edited?
	if ($VAR["edit_accountid"] != 0) {
	    // yes. do we need to notify the user of this?
	    if ($CONFIG["notify_edit"] != 0) {
		// yes. generate the message and append it
	        $VAR["edit_accountname"] = GetMemberName ($VAR["edit_accountid"]);
		$VAR["message"] .= InsertSkinVars (GetSkinTemplate ("editpost_editfooter"));
	    }
	}

	// evaluate the result
	$VAR["postlist"] .= InsertSkinVars (GetSkinTemplate ("post_list_print"));
    }

    // is the thread locked?
    if (($flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. no replies allowed
        $replytext = AddSlashes (GetSkinTemplate ("reply_no"));
	$thread_locked = AddSlashes (GetSkinTemplate ("postpage_locked"));

	// grab the name of the mover/locker
	$VAR["lockername"] = GetMemberName ($VAR["lockerid"]);

	// is this thread moved to another forum?
	if ($VAR["destforum"] != 0) {
	    // yes. grab the 'thread moved' template instead
	    $locktext_template = GetSkinTemplate ("page_threadmoved");

	    // grab the destination forum name
	    $query = sprintf ("SELECT name FROM forums WHERE id='%s'",$VAR["destforum"]);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    list ($VAR["destforumname"]) = db_fetch_results (db_query ($query));
	    $VAR["destforumid"] = $VAR["destforum"];
	} else {
	    // no. the thread has only been locked.
	    $locktext_template = GetSkinTemplate ("page_threadlocked");
	}
    } else {
        // no. the thread is not locked
	$locktext_template = "";
    }

    // build the 'thread locked' template
    $VAR["locktext"] = InsertSkinVars ($locktext_template);

    // evaluate the result
    ShowBaseForumPage ("postpage_print", $threadid, $VAR["forumid"]);
 ?>
