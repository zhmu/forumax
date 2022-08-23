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
    $forumid = $result[0]; $flags = $result[1];
    $threadtitle = CensorText ($result[2]); $lockerid = $result[3];
    $destforum = $result[4]; $nofreplies = $result[5];

    // is there a thread title?
    if (trim ($threadtitle) == "") {
	// no. revert to the default one
	$threadtitle = $CONFIG["default_topic"];
    }

    // increment the number of pageviews
    $query = sprintf ("update threads set nofviews=nofviews+1 where id=%s", $threadid);
    db_query ($query);

    // calculate the number of pages
    $nofpages = floor (($nofreplies + 1) / $CONFIG["page_size"]);
    if (($nofpages * $CONFIG["page_size"]) != ($nofreplies + 1)) { $nofpages++; };

    // was a page given?
    if ($page == "") {
	// no. default to the last page
	$page = $nofpages;
    }

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
    ShowHeader("postpage");

    // grab the forum names
    $postlist = "";

    // grab the templates needed
    $postlist_template = addslashes (GetSkinTemplate ("post_list"));
    $deletepost_template = addslashes (GetSkinTemplate ("deletepost"));
    $editpost_template = addslashes (GetSkinTemplate ("editpost"));
    $viewip_template = addslashes (GetSkinTemplate ("viewip"));
    $editedpost_template = addslashes (GetSkinTemplate ("editpost_editfooter"));
    $quotepost_template = addslashes (GetSkinTemplate ("quotepost"));

    // is private messaging allowed?
    if ($CONFIG["allow_pm"] != 0) {
	// yes. get the template
        $pmuser_template = addslashes (GetSkinTemplate ("pmuser"));
    }

    // build an array of all custom fields we need
    $query = sprintf ("select id,name,type,visible from customfields");
    $res = db_query ($query);
    $custom_record = ""; $i = 4;
    while ($id = db_fetch_results ($res)) {
	$custom_record .= "extra" . $id[0] . ",";
	$custom_map[$id[0]] = $i;
	$custom_name[$id[0]] = $id[1];
	$custom_type[$id[0]] = $id[2];
	$custom_visible[$id[0]] = $id[3];
	$i++;
    }
    if ($custom_record != "") { $custom_record = "," . $custom_record; }
    $custom_record = preg_replace ("/,$/", "", $custom_record);

    // select all threads here from the database
    $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"];
    if ($timezone == "") { $timezone = 0; };
    $query = sprintf ("select id,authorid,post,icon,date_format(from_unixtime(unix_timestamp(timestamp)+%s),'%s'),date_format(from_unixtime(unix_timestamp(edittime)+%s),'%s'),editid,flags from posts where threadid=%s order by id asc limit %s,%s", $timezone, $CONFIG["post_timestamp_format"],$timezone,$CONFIG["post_timestamp_format"],$threadid, ($page - 1) * $CONFIG["page_size"], $CONFIG["page_size"]);
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
	$query = sprintf ("select accountname,nofposts,date_format(joindate,'%s'),sig%s from accounts where id=%s", $CONFIG["joindate_timestamp_format"], $custom_record, $authorid);
	$author_res = db_query ($query); $author_result = db_fetch_results ($author_res);
	$customfields = "";

	// grab the values
	if (db_nof_results ($author_res) > 0) {
	    $author = $author_result[0];
	    $author_status = GetMemberStatus ($authorid);
	    $author_nofposts = $author_result[1];
      	    $author_joindate = $author_result[2];
	    $author_sig = $author_result[3];

	    // grab the custom fields
	    @reset ($custom_map);
	    while (list ($customid) = @each ($custom_map)) {
		// get the template
		$template = addslashes (GetSkinTemplate ("viewcustom_" . $custom_type[$customid]));
		$fieldname = $custom_name[$customid];
		$fieldvalue = $author_result[$custom_map[$customid]];

		// is this field visible?
		if ($custom_visible[$customid] != 0) {
		    // yes. grab the template

		    // grab the actual field contents
		    if ($fieldvalue != "") { eval ("\$customfields .= stripslashes (\"" . $template . "\");"); };
		} else {
		    if ($fieldvalue != "") {
		        eval ("\$custom[$customid] = stripslashes (\"" . $template . "\");");
		        eval ("\$customtype[$custom_type[$customid]] = stripslashes (\"" . $template . "\");");
		    } else {
		        eval ("\$custom[$customid] = \"\";");
		        eval ("\$customtype[$custom_type[$customid]] = \"\";");
		    }
		}
	    }
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

	// censor the message
	$message = CensorText ($message);

	// apply the smilies	
	$message = ApplySmilies ($message);

	// construct the 'edit post' button
	eval ("\$editpost = stripslashes (\"" . $editpost_template . "\");");

	// construct the 'delete post' button
	eval ("\$deletepost = stripslashes (\"" . $deletepost_template . "\");");
	// construct the 'quote post' button
	eval ("\$quotepost = stripslashes (\"" . $quotepost_template . "\");");

	// construct the 'pm user' button
	eval ("\$pmuser = stripslashes (\"" . $pmuser_template . "\");");

	// construct the 'view ip' button
	eval ("\$viewip = stripslashes (\"" . $viewip_template . "\");");

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

    // can we post a new topic?
    if (0 == 1) {
        // yes. no replies allowed
        $newtopictext = AddSlashes (GetSkinTemplate ("newtopic_no"));
    } else {
        // no. replying is allowed
        $newtopictext = AddSlashes (GetSkinTemplate ("newtopic_ok"));
    }
    eval ("\$tmp = stripslashes (\"" . $newtopictext . "\");");
    $newtopictext = $tmp;

    // is this subject multiple pages long?
    if ($nofpages > 1) {
	// yes. build the list of pages
	$pageslist = "";

	// do we have pages before this one?
	if (($page - $CONFIG["page_display_range"] + 1) > 0) {
	    // yes. show the 'first link' and the dots
	    eval ("\$pageslist = stripslashes (\"" . AddSlashes (GetSkinTemplate ("thread_page_firstpage")) . "\");");
	    eval ("\$pageslist .= stripslashes (\"" . AddSlashes (GetSkinTemplate ("thread_page_range_separator")) . "\");");
	}

	// now, figure out the page range
	$page_from = $page - floor ($CONFIG["page_display_range"] / 2);
	if ($page_from == 0) { $page_from = 1; };
	$page_to = $page_from + $CONFIG["page_display_range"] - 1;
	if ($page_to > $nofpages) { $page_to = $nofpages; };

	// list them
	$curpage = $page;
	for ($page = $page_from; $page <= $page_to; $page++) {
	    // is this thing selected?
	    if ($page == $curpage) {
		// yes. use the selected template
		$template = "thread_page_sel";
	    } else {
		// no. use the unselected template
		$template = "thread_page_unsel";
	    }

	    eval ("\$pageslist .= stripslashes (\"" . AddSlashes (GetSkinTemplate ($template)) . "\");");

	    // not the last page?
	    if ($page != $page_to) {
		// yes, add the separator
	        eval ("\$pageslist .= stripslashes (\"" . AddSlashes (GetSkinTemplate ("thread_page_separator")) . "\");");
	    }
	}

	// do we have more pages?
	if ($page_to < $nofpages) {
	    // yes. show the last page link, too
	    eval ("\$pageslist .= stripslashes (\"" . AddSlashes (GetSkinTemplate ("thread_page_range_separator")) . "\");");
	    eval ("\$pageslist .= stripslashes (\"" . AddSlashes (GetSkinTemplate ("thread_page_lastpage")) . "\");");
	}

	eval ("\$pagelist = stripslashes (\"" . AddSlashes (GetSkinTemplate ("thread_pagelist")) . "\");");
    }

    // grab some generic values
    $forums_title = $CONFIG["forumtitle"];
    // evaluate the result
    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("postpage")) . "\");");
    print $tmp;
    ShowFooter();
 ?>
