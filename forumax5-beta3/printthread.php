<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // figure out the forum id
    $query = sprintf ("select forumid,flags,title,lockerid,destforum,nofreplies from threads where id=%s", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0]; $flags = $result[1]; $threadtitle = $result[2];
    $lockerid = $result[3]; $destforum = $result[4]; $nofreplies = $result[5];

    // is there a thread title?
    if (trim ($threadtitle) == "") {
	// no. revert to the default one
	$threadtitle = $CONFIG["default_topic"];
    }

    // increment the number of pageviews
    $query = sprintf ("update threads set nofviews=nofviews+1 where id=%s", $threadid);
    db_query ($query);

    // grab the forum name
    $query = sprintf ("select name,flags,catno,image from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumname = $result[0]; $forumflags = $result[1]; $catid = $result[2];
    $forum_image = $result[3];

    // is this forum in a category?
    // do we have a category id?
    if ($catid != 0) {
	// yes. grab the category name
	$query = sprintf ("select name from categories where id=%s", $catid);
        $res = db_query ($query); $tmp = db_fetch_results ($res);
	$cat_title = $tmp[0];
    }

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // show the welcome page
    ShowHeader("postpage_print");

    // grab the forum names
    $postlist = "";

    // grab the template needed
    $postlist_template = addslashes (GetSkinTemplate ("post_list_print"));

    // is private messaging allowed?
    if ($CONFIG["allow_pm"] != 0) {
	// yes. get the template
        $pmuser_template = addslashes (GetSkinTemplate ("pmuser"));
    }

    // select all threads here from the database
    $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"];
    if ($timezone == "") { $timezone = 0; };
    $query = sprintf ("select id,authorid,post,icon,date_format(from_unixtime(unix_timestamp(timestamp)+%s),'%s'),date_format(from_unixtime(unix_timestamp(edittime)+%s),'%s'),editid,flags from posts where threadid=%s order by id asc", $timezone, $CONFIG["post_timestamp_format"],$timezone,$CONFIG["post_timestamp_format"],$threadid);
    $res = db_query ($query);

    // construct flags for sig fixing
    $sigflags = 0;
    if ($CONFIG["allow_sig_max"] != 0) { $sigflags |= FLAG_FORUM_ALLOWMAX; };
    if ($CONFIG["allow_sig_html"] != 0) { $sigflags |= FLAG_FORUM_ALLOWHTML; };
    if ($CONFIG["block_sig_img"] != 0) { $sigflags |= FLAG_FORUM_NOIMAGES; };
    if ($CONFIG["block_sig_js"] != 0) { $sigflags |= FLAG_FORUM_DENYEVILHTML; };

    // while there are threads, add them
    while ($result = db_fetch_results ($res)) {
	// grab the values
	$postid = $result[0]; $authorid = $result[1]; $icon = $result[3];
	$author_url = rawurlencode ($result[1]); $timestamp = $result[4];
	$edit_timestamp = $result[5]; $edit_accountid = $result[6];
	$postflags = $result[7];
	$message = FixupMessage ($result[2], $forumflags);

	// grab the author's record
	$query = sprintf ("select accountname,nofposts,date_format(joindate,'%s'),sig from accounts where id=%s", $CONFIG["joindate_timestamp_format"], $authorid);
	$author_res = db_query ($query); $author_result = db_fetch_results ($author_res);

	// grab the values
	if (db_nof_results ($author_res) > 0) {
	    $author = $author_result[0];
	    $author_status = GetMemberStatus ($authorid);
	    $author_nofposts = $author_result[1];
      	    $author_joindate = $author_result[2];
	    $author_sig = $author_result[3];
	} else {
	    $author = $CONFIG["delmem_name"];
	    $author_status = $CONFIG["unknown_title"];
	    $author_nofposts = $CONFIG["delmem_postcount"];
	    $author_joindate = $CONFIG["delmem_joindate"];
	    $author_sig = "";
	}

	// has this item been edited?
	if ($edit_accountid != 0) {
	    // yes. do we need to notify the user of this?
	    if ($CONFIG["notify_edit"] != 0) {
		// yes. generate the message and append it
	        $edit_accountname = GetMemberName ($edit_accountid);
	        eval ("\$tmp = stripslashes (\"" . $editedpost_template . "\");");
		$message .= $tmp;
	    }
	}

	// need to append the signature?
	if ((($postflags & FLAG_POST_SIG) != 0) && ($CONFIG["allow_sig"] != 0)) {
	    // yes. do it
	    $message .= GetSkinTemplate ("sig_sep");
	    $message .= FixupMessage ($author_sig, $sigflags);
	}

	// evaluate the result
	eval ("\$tmp = stripslashes (\"" . $postlist_template . "\");");
	$postlist .= $tmp;
    }

    // is the thread locked?
    if (($flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. no replies allowed
        $replytext = AddSlashes (GetSkinTemplate ("reply_no"));
	$thread_locked = AddSlashes (GetSkinTemplate ("postpage_locked"));

	// grab the name of the mover/locker
	$query = sprintf ("select accountname from accounts where id=%s",$lockerid);
	$tmp = db_fetch_results (db_query ($query)); $lockername = $tmp[0];

	// is this thread moved to another forum?
	if ($destforum != 0) {
	    // yes. grab the 'thread moved' template instead
	    $locktext_template = AddSlashes (GetSkinTemplate ("page_threadmoved"));

	    // grab the destination forum name
	    $query = sprintf ("select name from forums where id=%s",$destforum);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    $destforumname = $result[0]; $destforumid = $destforum;
	} else {
	    // no. the thread has only been moved.
	    $locktext_template = AddSlashes (GetSkinTemplate ("page_threadlocked"));
	}
    } else {
        // no. replying is allowed
        $replytext = AddSlashes (GetSkinTemplate ("reply_ok"));
	$thread_locked = AddSlashes (GetSkinTemplate ("postpage_canreply"));
	$locktext_template = "";
    }

    // HTML-ize the name of the person who locked the thread.
    $lockerurl = rawurlencode ($locker);

    eval ("\$locktext = stripslashes (\"" . $locktext_template . "\");");
    eval ("\$tmp = stripslashes (\"" . $replytext . "\");");
    $replytext = $tmp;

    // grab some generic values
    $forums_title = $CONFIG["forumtitle"];
    // evaluate the result
    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("postpage_print")) . "\");");
    print $tmp;
    ShowFooter();
 ?>
