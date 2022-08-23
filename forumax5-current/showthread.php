<?php
    //
    // showthread.php
    //
    // This will display a thread, along with all replies and such.
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

    // figure out the forum id
    $query = sprintf ("SELECT forumid,flags,title,lockerid,destforum,nofreplies,lastdate FROM threads WHERE id='%s'", $threadid);
    $res = db_query ($query);
    list ($VAR["forumid"], $thread_flags, $VAR["threadtitle"], $lockerid, $VAR["destforum"], $VAR["nofreplies"], $VAR["lastdate"]) = db_fetch_results ($res);
    $VAR["threadtitle"] = CensorText ($VAR["threadtitle"]);

    // is there a thread title?
    if (trim ($VAR["threadtitle"]) == "") {
	// no. revert to the default one
	$VAR["threadtitle"] = $CONFIG["default_topic"];
    }

    // increment the number of pageviews
    $query = sprintf ("UPDATE threads SET nofviews=nofviews+1 WHERE id='%s'", $threadid);
    db_query ($query);

    // calculate the number of pages
    $nofpages = floor (($VAR["nofreplies"] + 1) / $CONFIG["page_size"]);
    if (($nofpages * $CONFIG["page_size"]) != ($VAR["nofreplies"] + 1)) { $nofpages++; };
    $VAR["nofpages"] = $nofpages;

    // secure the page number too, just in case
    $page = trim (preg_replace ("/\D/", "", $_REQUEST["page"]));

    // was a page given?
    if ($page == "") {
	// no. default to the last page
	$page = $nofpages;
    } 
    $VAR["page"] = $page;

    // grab the forum name
    $query = sprintf ("SELECT name,flags,catno,image FROM forums WHERE id='%s'", $VAR["forumid"]);
    $res = db_query ($query);
    list ($VAR["forumname"], $forumflags, $VAR["catid"], $forum_image) = db_fetch_results ($res);

    // did this work?
    if (db_nof_results ($res) == 0) {
	// no. complain
	FatalError ("error_nosuchforum");
    }

    // handle the restrictions, if needed
    HandleRestrictedForum ($VAR["forumid"]);

    // grab the forum names
    $postlist = "";

    // build an array of all custom fields we need
    $query = sprintf ("SELECT id,name,type,visible FROM customfields");
    $res = db_query ($query);
    $custom_record = ""; $i = 0;
    while (list ($id, $name, $type, $visible) = db_fetch_results ($res)) {
	$custom_record .= "extra" . $id . ",";
	$custom_map[$id] = $i;
	$custom_name[$id] = $name;
	$custom_type[$id] = $type;
	$custom_visible[$id] = $visible;
	$i++;
    }
    if ($custom_record != "") { $custom_record = "," . $custom_record; }
    $custom_record = preg_replace ("/,$/", "", $custom_record);

    // select all threads here from the database
    $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"] + 0;
    $query = sprintf ("SELECT id,authorid,post,icon,DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp)+%s),'%s'),DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(edittime)+%s),'%s'),editid,flags,authorname FROM posts WHERE threadid=%s ORDER BY id ASC LIMIT %s,%s", $timezone, $CONFIG["post_timestamp_format"],$timezone,$CONFIG["post_timestamp_format"],$threadid, ($page - 1) * $CONFIG["page_size"], $CONFIG["page_size"]);

    $res = db_query ($query);

    // construct flags for sig fixing
    $sigflags = 0;
    if ($CONFIG["allow_sig_max"] != 0) { $sigflags |= FLAG_FORUM_ALLOWMAX; };
    if ($CONFIG["allow_sig_html"] != 0) { $sigflags |= FLAG_FORUM_ALLOWHTML; };
    if ($CONFIG["block_sig_img"] != 0) { $sigflags |= FLAG_FORUM_NOIMAGES; };
    if ($CONFIG["block_sig_js"] != 0) { $sigflags |= FLAG_FORUM_DENYEVILHTML; };

    // does this user want to see any images?
    if ($GLOBALS["sig_option"] == 1) {
	// no. kill them
	$sigflags |= FLAG_FORUM_NOIMAGES;
    }

    // while there are threads, add them
    while (list ($VAR["postid"], $VAR["authorid"], $VAR["message"], $VAR["icon"], $VAR["timestamp"], $VAR["edit_timestamp"], $VAR["edit_accountid"], $postflags, $VAR["authorname"]) = db_fetch_results ($res)) {
	// fix up the message
	$VAR["message"] = FixupMessage ($VAR["message"], $forumflags);

	// is this an unregisted account?
	if ($VAR["authorid"] != 0) {
	    // no. is this author already cached?
	    if ($AUTHOR_CACHE[$VAR["authorid"]] == "") {
		// no. grab the author's record
		$query = sprintf ("SELECT accountname,nofposts,DATE_FORMAT(joindate,'%s'),sig,avatar%s FROM accounts WHERE id='%s'", $CONFIG["joindate_timestamp_format"], $custom_record, $VAR["authorid"]);
		$author_res = db_query ($query); $author_result = db_fetch_results ($author_res);
		$VAR["customfields"] = "";

		// grab the values
		if (db_nof_results ($author_res) > 0) {
		    // store the values
		    $AUTHOR_CACHE[$VAR["authorid"]] = GetMemberStatus ($VAR["authorid"]) . "|^|" . implode ("|^|", $author_result);
		} else {
		    $AUTHOR_CACHE[$VAR["authorid"]] = "|^|";
		}
	    }

	    // do we have a valid cache entry?
	    if ($AUTHOR_CACHE[$VAR["authorid"]] != "|^|") {
		// yes. fetch the entry from the cache
		$author_fields = explode ("|^|", $AUTHOR_CACHE[$VAR["authorid"]]);
		list ($VAR["author_status"], $VAR["author"], $VAR["author_nofposts"], $VAR["author_joindate"], $VAR["author_sig"], $author_avatar) = $author_fields;

		// grab the custom fields
		@reset ($custom_map); $customfields = "";
		while (list ($customid) = @each ($custom_map)) {
		    // get the template
		    $template = GetSkinTemplate ("viewcustom_" . $custom_type[$customid]);
		    $VAR["fieldname"] = $custom_name[$customid];
		    $VAR["fieldvalue"] = $author_fields[$custom_map[$customid] + 6];

		    // is this field visible?
		    if ($custom_visible[$customid] != 0) {
			// yes. grab the actual field contents
			if ($VAR["fieldvalue"] != "") {
			    $VAR["customfields"] .= InsertSkinVars ($template);
			}
		    } else {
			if ($VAR["fieldvalue"] != "") {
			    $VAR["customtype" . $custom_type[$customid]] = InsertSkinVars ($template);
			} else {
			    $VAR["custom" . $customid] = "";
			    $VAR["customtype" . $custom_type[$customid]] = "";
			}
		    }
		}
	    } else {
		// who is this?
		$VAR["author"] = $CONFIG["delmem_name"];
		$VAR["author_status"] = $CONFIG["unknown_title"];
		$VAR["author_nofposts"] = $CONFIG["delmem_postcount"];
		$VAR["author_joindate"] = $CONFIG["delmem_joindate"];
		$VAR["author_sig"] = "";
		$VAR["customfields"] = ""; $VAR["customtype"] = array ();
	    }
	} else {
	    // unregistered account
	    $VAR["author"] = $VAR["authorname"];
	    $VAR["author_status"] = $CONFIG["unknown_title"];
	    $VAR["author_nofposts"] = $CONFIG["delmem_postcount"];
	    $VAR["author_joindate"] = $CONFIG["delmem_joindate"];
	    $VAR["author_sig"] = "";
	    $VAR["customfields"] = "";
	    $VAR["customtype"] = array ();
	}

	// has this item been edited?
	$VAR["edited"] = "";
	if ($VAR["edit_accountid"] != 0) {
	    // yes. do we need to notify the user of this?
	    if ($CONFIG["notify_edit"] != 0) {
		// yes. generate the message and append it
	        $VAR["edit_accountname"] = GetMemberName ($VAR["edit_accountid"]);
		$VAR["edited"] = InsertSkinVars (GetSkinTemplate ("editpost_editfooter"));
	    }
	}

	// need to append the signature?
	if ((($postflags & FLAG_POST_SIG) != 0) and ($CONFIG["allow_sig"] != 0) and ($GLOBALS["sig_option"] != 2) and ($VAR["authorid"] != 0)) {
	    // yes. do it
	    $VAR["message"] .= GetSkinTemplate ("sig_sep");
	    $VAR["message"] .= FixupMessage ($author_sig, $sigflags);
	}

	// censor the message
	$VAR["message"] = CensorText ($VAR["message"]);

	// need to apply the smilies?
	if (($postflags & FLAG_POST_NOSMILIES) == 0) {
	    // yes. apply the smilies	
	    $VAR["message"] = ApplySmilies ($VAR["message"]);
	}

	// construct the avatar (XXX)
	// eval ("\$avatar = stripslashes (\"" . $avatar_template . "\");");

	// construct the 'edit post' button
	$VAR["editpost"] = InsertSkinVars (GetSkinTemplate ("editpost"));

	// construct the 'delete post' button
	$VAR["deletepost"] = InsertSkinVars (GetSkinTemplate ("deletepost"));

	// construct the 'quote post' button
	$VAR["quotepost"] = InsertSkinVars (GetSkinTemplate ("quotepost"));

	// construct the 'pm user' button
	$VAR["pmuser"] = InsertSkinVars (GetSkinTemplate ("pmuser"));

	// construct the 'view ip' button
	$VAR["viewip"] = InsertSkinVars (GetSkinTemplate ("viewip"));

	// evaluate the result
	$VAR["postlist"] .= InsertSkinVars (GetSkinTemplate ("post_list"));
    }

    // is the thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. no replies allowed
        $replytext = GetSkinTemplate ("reply_no");
	$thread_locked = GetSkinTemplate ("postpage_locked");

	// grab the name of the mover/locker
	$VAR["lockername"] = GetMemberName ($lockerid);

	// is this thread moved to another forum?
	if ($VAR["destforum"] != 0) {
	    // yes. grab the 'thread moved' template instead
	    $locktext_template = GetSkinTemplate ("page_threadmoved");

	    // grab the destination forum name
	    $query = sprintf ("SELECT name FROM forums WHERE id='%s'",$VAR["destforum"]);
	    list ($VAR["destforumname"], $VAR["destforumid"]) = db_fetch_results (db_query ($query));
	} else {
	    // no. the thread has only been moved.
	    $locktext_template = GetSkinTemplate ("page_threadlocked");
	}
    } else {
        // no. replying is allowed
        $replytext = GetSkinTemplate ("reply_ok");
	$thread_locked = GetSkinTemplate ("postpage_canreply");
	$locktext_template = "";
    }

    // HTML-ize the name of the person who locked the thread.
    $VAR["lockerurl"] = rawurlencode ($locker);

    $VAR["locktext"] = InsertSkinVars ($locktext_template);
    $VAR["replytext"] = InsertSkinVars ($replytext);

    // can we post a new topic?
    if (0 == 1) {
        // yes. no replies allowed
        $newtopictext = GetSkinTemplate ("newtopic_no");
    } else {
        // no. replying is allowed
        $newtopictext = GetSkinTemplate ("newtopic_ok");
    }
    $VAR["newtopictext"] = InsertSkinVars ($newtopictext);

    // is this subject multiple pages long?
    if ($nofpages > 1) {
	// yes. build the list of pages
	$pageslist = "";

	// do we have pages before this one?
	if (($page - $CONFIG["page_display_range"] + 1) > 0) {
	    // yes. show the 'first link' and the dots
	    $VAR["pageslist"] = InsertSkinVars (GetSkinTemplate ("thread_page_firstpage"));
	    $VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ("thread_page_range_separator"));
	}

	// now, figure out the page range
	$page_from = $page - floor ($CONFIG["page_display_range"] / 2);
	if ($page_from == 0) { $page_from = 1; };
	$page_to = $page_from + $CONFIG["page_display_range"] - 1;
	if ($page_to > $nofpages) { $page_to = $nofpages; };

	// list them
	$curpage = $page;
	for ($VAR["page"] = $page_from; $VAR["page"] <= $page_to; $VAR["page"]++) {
	    // is this thing selected?
	    if ($VAR["page"] == $curpage) {
		// yes. use the selected template
		$template = "thread_page_sel";
	    } else {
		// no. use the unselected template
		$template = "thread_page_unsel";
	    }

	    $VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ($template));

	    // the last page?
	    if ($VAR["page"] != $page_to) {
		// no. add the separator
	        $VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ("thread_page_separator"));
	    }
	}

	// do we have more pages?
	if ($page_to < $nofpages) {
	    // yes. show the last page link, too
	    $VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ("thread_page_range_separator"));
	    $VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ("thread_page_lastpage"));
	}

	$VAR["pagelist"] = InsertSkinVars (GetSkinTemplate ("thread_pagelist"));
    }

    // build the hopto list
    $VAR["hopto_list"] = BuildHopto();

    // grab some generic values
    $VAR["forums_title"] = $CONFIG["forumtitle"];

    // do we have a next thread?
    $query = sprintf ("SELECT id FROM threads WHERE forumid='%s' AND lastdate>'%s' ORDER BY lastdate ASC LIMIT 1", $VAR["forumid"], $VAR["lastdate"]);
    $res = db_query ($query); list ($VAR["id"]) = db_fetch_results ($res);
    if (db_nof_results ($res) > 0) {
	// yes. show the link
	$VAR["links"] .= InsertSkinVars (GetSkinTemplate ("link_nextthread"));
    }

    // do we have a previous thread?
    $query = sprintf ("SELECT id FROM threads WHERE forumid='%s' AND lastdate<'%s' ORDER BY lastdate DESC LIMIT 1", $VAR["forumid"], $VAR["lastdate"]);
    $res = db_query ($query); list ($VAR["id"]) = db_fetch_results ($res);
    if (db_nof_results ($res) > 0) {
	// yes. do we already have a link?
	if ($VAR["links"] != "") {
	    // yes. add the seperator
	    $VAR["links"] .= InsertSkinVars (GetSkinTemplate ("link_sep"));
	}
	$VAR["links"] .= InsertSkinVars (GetSkinTemplate ("link_prevthread"));
    }

    // does this thread have a poll attached to it?
    $query = sprintf ("SELECT id,question,flags,totalvotes FROM polls WHERE threadid=%s", $threadid);
    $res = db_query ($query);
    if (db_nof_results ($res) > 0) {
	// yes. get the results
	list ($VAR["pollid"], $VAR["poll_question"], $poll_flags, $VAR["poll_totalvotes"]) = db_fetch_results ($res);

	// are we logged in?
	if ($GLOBALS["logged_in"] != 0) {
	    // have we already voted here?
	    $query = sprintf ("SELECT id FROM poll_votes WHERE pollid='%s' AND accountid='%s'", $VAR["pollid"], $GLOBALS["userid"]);
	    if (db_nof_results (db_query ($query)) > 0) {
		// yes, we have. load the appropriate skin
	        $vote_template = GetSkinTemplate ("pollvote_alreadyvoted");
	    } else {
		// no. load the appropriate skin
	        $vote_template = GetSkinTemplate ("pollvote_vote");
	    }
	} else {
	    // we can't vote if not logged in
	    $vote_template = GetSkinTemplate ("pollvote_notloggedin");
	}

	// grab all options
	$query = sprintf ("SELECT id,optiontext,nofvotes FROM poll_options WHERE pollid='%s'", $VAR["pollid"]);
	$res = db_query ($query);

	// show them all
	$no = 1;
	while (list ($VAR["optionid"], $VAR["poll_option"], $VAR["poll_numvotes"]) = db_fetch_results ($res)) {
	    // do we have any votes?
	    if ($VAR["poll_numvotes"] != 0) {
		// yes. insert these votes
		$VAR["poll_pct"] = round ($VAR["poll_numvotes"] / $VAR["poll_totalvotes"] * 100);
		$VAR["width"] = $VAR["poll_pct"] * 2;
		$VAR["poll_result"] = InsertSkinVars (GetSkinTemplate ("bar_" . $no));
	    } else {
		$VAR["poll_result"] = ""; $VAR["poll_pct"] = 0;
	    }

	    // build the vote template
	    $VAR["poll_vote"] = InsertSkinVars ($vote_template);

	    // add the template
	    $VAR["poll_list"] .= InsertSkinVars (GetSkinTemplate ("poll_list"));

	    // next template
	    $no++;
	}

	// grab the poll template
	$VAR["poll"] .= InsertSkinVars (GetSkinTemplate ("poll_overview"));
    }

    // fix up the restrictions
    BuildForumRestrictions ($VAR["forumid"]);

    // are we logged in ?
    if ($GLOBALS["logged_in"] != 0) {
	// yes. have we already rated this thread?
	$query = sprintf ("SELECT id FROM threadsrated WHERE threadid='%s' AND accountid='%s'", $threadid, $GLOBALS["userid"]);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. set the variable
	    $VAR["rated"] = "yes";
	}
    } else {
	// no. we may not rate when not logged in
	$VAR["rated"] = "yes";
    }

    // build a total variable so we can check for them
    $VAR["locklinks"] = $VAR["locktext"] . $VAR["links"];

    // evaluate the result
    ShowBaseForumPage ("postpage", $threadid, $VAR["forumid"]);
 ?>
