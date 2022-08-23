<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    //
    // BuildPageRange ($from, $to)
    //
    // This build build the page range from $from to $to.
    //
    function
    BuildPageRange ($from, $to) {
	global $threadid;

	// build the list of pages
        $pagetemplate = AddSlashes (GetSkinTemplate ("page_firstno"));
        $page_nextemplate = AddSlashes (GetSkinTemplate ("page_moreno"));
	$pages = ""; $page = $from;
	while ($page <= $to) {
	    eval ("\$pages .= stripslashes (\"" . $pagetemplate . "\");");
	    $pagetemplate = $page_nextemplate;
	    $page++;
	}

	return $pages;
    }

    // grab this forum's title
    $query = sprintf ("select name,catno,image from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumtitle = $result[0]; $catid = $result[1]; $forum_image = $result[2];

    // do we have a category id?
    if ($catid != 0) {
	// yes. grab the category name
	$query = sprintf ("select name from categories where id=%s", $catid);
        $res = db_query ($query); $tmp = db_fetch_results ($res);
	$cat_title = $tmp[0];
    }

    // is this forum restricted?
    $query = sprintf ("select id from restricted where forumid=%s limit 1",$forumid);
    if (db_nof_results (db_query ($query)) > 0) {
	// yes. are we logged in?
	if ($GLOBALS["logged_in"] == 0) {
	    // no. request the user to log in
	    ShowHeader ("page_restrictedlogin");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("page_restrictedlogin")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}

	// we are logged in. should we be allowed access?
	if (CanVisitRestrictedForum ($forumid) == 0) {
	    // access is denied. complain
	    ShowHeader ("error_restrictedenied");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_restrictedenied")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}
    }

    // grab the announcements
    $announcementlist = "";
    $query = sprintf ("select title,authorid from announcements where ((forumid = %s) or (forumid = 0)) and ((now() >= startdate) and (enddate >= now())) order by id desc" , $forumid);
    $res = db_query ($query);

    // grab the template
    $annclist_template = addslashes (GetSkinTemplate ("announcement_list"));

    // while there are threads, add them
    while ($result = db_fetch_results ($res)) {
	// grab the values
	$announcement_title = $result[0]; $announcement_authorid = $result[1];

	// grab the author name
	$query = sprintf ("select accountname from accounts where id=%s",$announcement_authorid);
	$tmp = db_fetch_results (db_query ($query)); $authorname = $tmp[0];

	// evaluate the result
	eval ("\$tmp = stripslashes (\"" . $annclist_template . "\");");
	$announcementlist .= $tmp;
    }

    // grab the forum names
    $threadlist = "";

    // grab the templates
    $threadlist_template = addslashes (GetSkinTemplate ("thread_list"));
    $lockedthread_template = addslashes (GetSkinTemplate ("lockedthread"));

    // was no span given?
    if ($dayspan == "") {
	// no. use the default
	$dayspan = $CONFIG["topicspan"];
    }

    // select all threads here from the database
    $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"];
    if ($timezone == "") { $timezone = 0; };
    $query = sprintf ("select id,title,authorid,nofreplies,from_unixtime(unix_timestamp(lastdate)+%s),lastposterid,flags,lockerid,icon,nofviews from threads where forumid=%s", $timezone, $forumid);

    // do we have to limit the topics by date?
    if ($dayspan != 0) {
	// yes. add the query
	$query .= " and to_days(now())-to_days(lastdate)<=" . $dayspan;
     }

     // append the sorting rule
     $query .= " order by lastdate desc";
     $res = db_query ($query);

    // grab some generic values
    $forums_title = $CONFIG["forumtitle"];

    // grab the page templates
    $pagetemplate = AddSlashes (GetSkinTemplate ("page_firstno"));
    $page_nextemplate = AddSlashes (GetSkinTemplate ("page_moreno"));

    // show the welcome pag
    ShowHeader ("threadpage");

    // while there are threads, add them
    while ($result = db_fetch_results ($res)) {
	// grab the values
	$threadid = $result[0]; $threadtitle = $result[1];
	$authorid = $result[2]; $nofreplies = $result[3];
	$lastreply = $result[4]; $lastreplyerid = $result[5];
	$flags = $result[6]; $lockerid = $result[7]; $icon = $result[8];
	$nofviews = $result[9];

	// grab the author and last replyer's usernames
        $authorname = GetMemberName ($authorid);
	$lastreplyer = GetMemberName ($lastreplyerid);
	if (($flags & FLAG_THREAD_LOCKED) != 0) {
	    $lockername = GetMemberName ($lockerid);
	} else {
	    $lockername = "";
        }

	// is this thread locked?
	if (($flags & FLAG_THREAD_LOCKED) != 0) {
	    // yes. alter the special text
	    eval ("\$lockedthread = stripslashes (\"" . $lockedthread_template . "\");");
	} else {
	    // no. no special lock text
	    $lockedthread = "&nbsp;";
	}

	// is this a thread with a load of replies?
	$pagelist = "";
	if ($nofreplies >= $CONFIG["page_size"]) {
	    // yes. calculate the number of pages
	    $nofpages = floor (($nofreplies + 1) / $CONFIG["page_size"]);
	    if (($nofpages * $CONFIG["page_size"]) != ($nofreplies + 1)) { $nofpages++; };

	    // do we have over 2 * page_range pages?
	    if ($nofpages > (2 * $CONFIG["page_display_range"])) {
		// yes. build the special list
	        $pages = BuildPageRange (1, $CONFIG["page_display_range"]);
		eval ("\$pages .= stripslashes (\"" . addslashes (GetSkinTemplate ("page_range_separator")) ."\");");
	        $pages .= BuildPageRange ($nofpages - $CONFIG["page_display_range"] + 1, $nofpages);
	    } else {
		// no. just list all pages
	        $pages = BuildPageRange (1, $nofpages);
	    }

	    eval ("\$pagelist = stripslashes (\"" . addslashes (GetSkinTemplate ("page_list")) . "\");");
	}

	// evaluate the result
	eval ("\$tmp = stripslashes (\"" . $threadlist_template . "\");");
	$threadlist .= $tmp;
    }

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

    // build the moderator list
    $modlist = "";

    // grab the moderator templates
    $mod_usertemplate = AddSlashes (GetSkinTemplate ("thread_usermod"));
    $mod_grouptemplate = AddSlashes (GetSkinTemplate ("thread_groupmod"));
    $mod_splitemplate = AddSlashes (GetSkinTemplate ("thread_splitmod"));

    // grab the mods
    $query = sprintf ("select userid,flags from mods where forumid=%s",$forumid);
    $res = db_query ($query);

    // add all mods
    $modlist = "";
    while ($tmp = db_fetch_results ($res)) {
	// build the list. is this an group?
	$objectid = $tmp[0]; $add = "";
	if ($tmp[1] & FLAG_USERLIST_GROUP) {
	    // no. grab the group name
	    $query = sprintf ("select name from groups where id=%s", $tmp[0]);
	    $res2 = db_query ($query); $result = db_fetch_results ($res2);

	    // did we have any valid results?
	    if (db_nof_results ($res) != 0) {
	        // yes. add the user
	        $objectname = $result[0]; 
                eval ("\$add = stripslashes (\"" . $mod_grouptemplate . "\");");
	    }
	    
	} else {
	    // yes. grab the account name
	    $query = sprintf ("select accountname from accounts where id=%s",$tmp[0]);
	    $res2 = db_query ($query); $result = db_fetch_results ($res2);

	    // did we have any valid results?
	    if (db_nof_results ($res) != 0) {
	        // yes. add the user
	        $objectname = $result[0]; 
                eval ("\$add = stripslashes (\"" . $mod_usertemplate . "\");");
	    }
	}

	// need to add something?
	if ($add != "") {
	    // yes. is this the first one?
	    if ($modlist != "") {
		// no. add the separator too
		eval ("\$modlist .= \"" . $mod_splitemplate . "\";");
	    }

	    // add this entry
	    $modlist .= $add;
	}
    }

    // did we have any actual mods?
    if ($modlist == "") {
	// no. use the default
	$modlist = GetSkinTemplate ("list_nomod");
    }

    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("threadpage")) . "\");");
    print $tmp;
    ShowFooter();
 ?>
