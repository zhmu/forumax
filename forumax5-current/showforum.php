<?php
    //
    // showforum.php
    //
    // This will display all threads in a forum.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    //
    // BuildPageRange ($threadid, $from, $to)
    //
    // This build build the page range from $from to $to for thread $threadid.
    //
    function
    BuildPageRange ($threadid, $from, $to) {
	global $VAR;

	// build the list of pages
	$pagetemplate = GetSkinTemplate ("page_firstno");
	$pages = ""; $VAR["page"] = $from;
	while ($VAR["page"] <= $to) {
	    $pages .= InsertSkinVars ($pagetemplate);
	    $pagetemplate = GetSkinTemplate ("page_moreno");
	    $VAR["page"]++;
	}

	return $pages;
    }

    //
    // BuildThreads($query)
    //
    // This will build a list of all threads as found by query $query.
    //
    function
    BuildThreads($query) {
	global $CONFIG, $VAR;

	// query the database
	$res = db_query ($query);

	// while there are threads, fetch them
	$threadlist = "";
	while (list ($VAR["threadid"], $VAR["threadtitle"], $VAR["authorid"], $VAR["nofreplies"], $VAR["lastreply"], $VAR["lastreplyerid"], $VAR["flags"], $VAR["lockerid"], $VAR["icon"], $VAR["nofviews"], $VAR["ratingno"], $VAR["authorname"], $VAR["lastreplyer"]) = db_fetch_results ($res)) {
	    // default to the generic template
	    $template = GetSkinTemplate ("thread_list");

	    // censor the title
	    $VAR["threadtitle"] = CensorText ($VAR["threadtitle"]);

	    // is a title given?
	    if (trim ($VAR["threadtitle"]) == "") {
		// no. use the default one
		$VAR["threadtitle"] = $CONFIG["default_topic"];
	    }

	    // grab the author and last replyer's usernames
	    if ($VAR["authorid"] != 0) {
	        $VAR["authorname"] = GetMemberName ($VAR["authorid"]);
	    }
	    if ($VAR["lastreplyerid"] != 0) {
	        $VAR["lastreplyer"] = GetMemberName ($VAR["lastreplyerid"]);
	    }

	    // is this thread locked?
	    if (($VAR["flags"] & FLAG_THREAD_LOCKED) != 0) {
		// yes. get the locker's accountname and update the template
		$VAR["lockername"] = GetMemberName ($VAR["lockerid"]);
		$VAR["lockedthread"] = InsertSkinVars (GetSkinTemplate ("lockedthread"));
	    } else {
		// no. make sure nothing special will be displayed
		$VAR["lockername"] = "";
		$VAR["lockedthread"] = "&nbsp;";
	    }

	    // is this thread a poll?
	    if (($VAR["flags"] & FLAG_THREAD_POLL) != 0) {
		// yes. switch over to the other template
		$template = GetSkinTemplate ("poll_thread_list");
	    }

	    // is this thread sticky ?
	    if (($VAR["flags"] & FLAG_THREAD_STICKY) != 0) {
		// yes. switch over to the other template
		$template = GetSkinTemplate ("sticky_thread_list");

		// poll too?
		if (($VAR["flags"] & FLAG_THREAD_POLL) != 0) {
		    // yes. use the special template now
		    $template = GetSkinTemplate ("sticky_poll_thread_list");
		}
	    }

	    // is this a thread with a load of replies?
	    $VAR["pagelist"] = "";
	    if ($VAR["nofreplies"] >= $CONFIG["page_size"]) {
		// yes. calculate the number of pages
		$nofpages = floor (($VAR["nofreplies"] + 1) / $CONFIG["page_size"]);
		if (($nofpages * $CONFIG["page_size"]) != ($VAR["nofreplies"] + 1)) { $nofpages++; };

		// do we have over 2 * page_range pages?
		if ($nofpages > (2 * $CONFIG["page_display_range"])) {
		    // yes. build the special list
		    $VAR["pages"] = BuildPageRange ($threadid, 1, $CONFIG["page_display_range"]);
		    $VAR["pages"] .= InsertSkinVars (GetSkinTemplate ("page_range_separator"));
		    $VAR["pages"] .= BuildPageRange ($threadid, $nofpages - $CONFIG["page_display_range"] + 1, $nofpages);
		} else {
		    // no. just list all pages
		    $VAR["pages"] = BuildPageRange ($threadid, 1, $nofpages);
		}
		$VAR["pagelist"] = InsertSkinVars (GetSkinTemplate ("page_list"));
	    }

	    // is this thread rated?
	    if ($VAR["ratingno"] == 0) {
		// no. mark it as unrated
		$VAR["rating"] = InsertSkinVars (GetSkinTemplate ("rate_unrated"));
	    } else {
		// calculate the rating and build it
		$VAR["rating"] = "";
		for ($i = 0; $i < $VAR["ratingno"]; $i++) {
		    $VAR["rating"] .= InsertSkinVars (GetSkinTemplate ("rate_rated"));
		}
	    }

	    // add the variables in the result
	    $threadlist .= InsertSkinVars ($template);
        }

	// return the thread list
	return $threadlist;
    }

    // is a forum id given?
    $forumid = trim (preg_replace ("/\D/", "", $_REQUEST["forumid"]));
    if ($forumid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }
    $VAR["forumid"] = $forumid;

    // grab the announcements
    $announcementlist = "";
    $query = sprintf ("SELECT id,title,authorid FROM announcements WHERE ((forumid=%s) OR (forumid=0)) AND ((NOW() >= startdate) AND (enddate >= NOW())) ORDER BY id DESC" , $forumid);
    $res = db_query ($query);

    // while there are threads, add them
    while (list ($VAR["announcement_id"], $VAR["announcement_title"], $VAR["announcement_authorid"]) = db_fetch_results ($res)) {
	// honor censoring
	$VAR["announcement_title"] = CensorText ($VAR["announcement_title"]);

	// grab the author name
	$VAR["authorname"] = GetMemberName ($VAR["announcement_authorid"]);

	// evaluate the result
	$VAR["announcementlist"] .= InsertSkinVars (GetSkinTemplate ("announcement_list"));
    }

    // was no span given?
    $VAR["dayspan"] = preg_replace ("/\D/", "", $_REQUEST["dayspan"]);
    if ($VAR["dayspan"] == "") {
	// no. use the default
	$VAR["dayspan"] = $CONFIG["topicspan"];
    }

    // select all threads here from the database
    $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"] + 0;

    // build the query for the normal threads
    $query = sprintf ("SELECT id,title,authorid,nofreplies,FROM_UNIXTIME(UNIX_TIMESTAMP(lastdate)+%s),lastposterid,flags,lockerid,icon,nofviews,rating,authorname,lastpostername FROM threads WHERE forumid='%s'", $timezone, $forumid);

    // build the special query for the sticky threads
    $sticky_query = sprintf ("%s AND (flags&%s) ORDER BY lastdate DESC", $query, FLAG_THREAD_STICKY);

    // build the count query, too
    $count_query = sprintf ("SELECT COUNT(id) FROM threads WHERE forumid=%s", $forumid);

    // make sure the 'usual' query doesn't show the sticky threads
    $query .= " AND NOT (flags&" . FLAG_THREAD_STICKY . ")";

    // do we have to limit the topics by date?
    if ($VAR["dayspan"] != 0) {
	// yes. add the query
	$timestamp .= "TO_DAYS(NOW())-TO_DAYS(lastdate)<=" . $VAR["dayspan"];
	$query .= " AND " . $timestamp;
	$count_query .= " AND " . $timestamp;
    }

    // secure the page number too, just in case
    $pageno = trim (preg_replace ("/\D/", "", $_REQUEST["pageno"]));

    // is a page number given?
    if ($pageno == "") {
	// no. default to page #1
	$pageno = 1;
    }

    // append the sorting rule
    $query .= " AND NOT (flags&" . FLAG_THREAD_STICKY . ") ORDER BY lastdate DESC";
    $count_query .= " AND NOT (flags&" . FLAG_THREAD_STICKY . ") ORDER BY lastdate DESC";

    // do we have a page?
    if ($pageno != 0) {
	// yes. fix up the query
	$query .= " LIMIT " . ($pageno - 1) * $CONFIG["forum_pagesize"] . "," . $CONFIG["forum_pagesize"];
    }

    // grab the total number of threads
    list ($nofthreads) = db_fetch_results (db_query ($count_query));

    // figure out the number of pages
    $nofpages = floor ($nofthreads / $CONFIG["forum_pagesize"]);
    if (($nofpages * $CONFIG["forum_pagesize"]) != $nofthreads) { $nofpages++; };

    // do we have multiple pages?
    $VAR["threadpage_list"] = "";
    if ($nofpages > 1) {
	// yes. do we have pages before this one?
	$threadpage_list = "";
	if (($pageno - $CONFIG["page_display_range"] + 1) > 0) {
	    // yes. show the 'first link' and the dots
	    $VAR["threadpage_list"] = InsertSkinVars (GetSkinTemplate ("forumpage_firstpage"));
	    $VAR["threadpage_list"] .= InsertSkinVars (GetSkinTemplate ("forumpage_sep_range"));
	}

	// now, figure out the page range
	$page_from = $pageno - floor ($CONFIG["page_display_range"] / 2);
	if ($page_from == 0) { $page_from = 1; };
	$page_to = $page_from + $CONFIG["page_display_range"] - 1;
	if ($page_to > $nofpages) { $page_to = $nofpages; };

	// list them
	$curpage = $VAR["page"];
	for ($VAR["page"] = 1; $VAR["page"] <= $page_to; $VAR["page"]++) {
	    // is this thing selected?
	    if ($VAR["page"] == $pageno) {
		// yes. use the selected template
		$template = GetSkinTemplate ("forumpage_sel");
	    } else {
		// no. use the unselected template
		$template = GetSkinTemplate ("forumpage_unsel");
	    }

	    // add the page
	    $VAR["threadpage_list"] .= InsertSkinVars ($template);

	    // not the last page?
	    if ($VAR["page"] != $page_to) {
		// yes, add the separator
	        $VAR["threadpage_list"] .= InsertSkinVars (GetSkinTemplate ("forumpage_separator"));
	    }
	}

	// do we have more pages?
	if ($page_to < $nofpages) {
	    // yes. show the last page link, too
	    $VAR["threadpage_list"] .= InsertSkinVars (GetSkinTemplate ("forumpage_sep_range"));
	    $VAR["page"] = $nofpages;
	    $VAR["threadpage_list"] .= InsertSkinVars (GetSkinTemplate ("forumpage_lastpage"));
	}

	// build the page list template
	$VAR["nofpages"] = $nofpages;
	$VAR["threadpage_pagelist"] = InsertSkinVars (GetSkinTemplate ("forumpage_pagelist"));
    }

    // grab the sticky threads, first
    $VAR["threadlist"] = BuildThreads ($sticky_query);

    // grab the normal threads, too
    $VAR["threadlist"] .= BuildThreads ($query);

    // can we post a new topic?
    if (0 == 1) {
        // yes. no replies allowed
	$newtopictext = GetSkinTemplate ("newtopic_no");
    } else {
        // no. replying is allowed
	$newtopictext = GetSkinTemplate ("newtopic_ok");
    }
    $VAR["newtopictext"] = InsertSkinVars ($newtopictext);

    // grab the mods
    $query = sprintf ("SELECT userid,flags FROM mods WHERE forumid='%s'",$forumid);
    $res = db_query ($query);

    // add all mods
    while (list ($VAR["objectid"], $flags) = db_fetch_results ($res)) {
	// build the list. is this an group?
	$add = "";
	if ($flags & FLAG_USERLIST_GROUP) {
	    // no. grab the group name
	    $VAR["objectname"] = GetGroupNameSimple ($VAR["objectid"]);

	    // did we have any results?
	    if ($VAR["objectname"] != "") {
	        // yes. add the group
		$add = InsertSkinVars (GetSkinTemplate ("thread_groupmod"));
	    }
	    
	} else {
	    // yes. grab the account name
	    $VAR["objectname"] = GetMemberNameSimple ($VAR["objectid"]);

	    // did we have any valid results?
	    if ($VAR["objectname"] != "") {
	        // yes. add the user
		$add = InsertSkinVars (GetSkinTemplate ("thread_usermod"));
	    }
	}

	// need to add something?
	if ($add != "") {
	    // yes. is this the first one?
	    if ($VAR["modlist"] != "") {
		// no. add the separator too
		$VAR["modlist"] .= InsertSkinVars (GetSkinTemplate ("thread_splitmod"));
	    }

	    // add this entry
	    $VAR["modlist"] .= $add;
	}
    }

    // did we have any actual mods?
    if ($VAR["modlist"] == "") {
	// no. use the default
	$VAR["modlist"] = GetSkinTemplate ("list_nomod");
    }

    // build the hopto list
    $VAR["hopto_list"] = BuildHopto();

    // fix up the restrictions
    BuildForumRestrictions ($forumid);

    // show the page
    ShowBaseForumPage ("threadpage", 0, $forumid);
 ?>
