<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the forum name
    $query = sprintf ("select name,flags from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumname = $result[0]; $forumflags = $result[1];

    // show the welcome page
    ShowHeader("announcementpage");

    // grab the forum names
    $annclist = "";

    // grab the templates neededj
    $annclist_template = addslashes (GetSkinTemplate ("announcement_displaylist"));

    // grab all announcements
    $query = sprintf ("select title,authorid,content,date_format(startdate,'%s'),date_format(enddate,'%s') from announcements  where ((forumid = %s) or (forumid = 0)) and ((now() >= startdate) and (enddate >= now())) order by id desc" , $CONFIG["annc_timestamp_format"], $CONFIG["annc_timestamp_format"],$forumid);
    $res = db_query ($query);

    // while there are threads, add them
    while ($result = db_fetch_results ($res)) {
	// grab the values
	$announcement_title = $result[0]; $announcement_authorid = $result[1];
	$message = FixupMessage ($result[2], FLAG_FORUM_ALLPRIVS);
	$begin = $result[3]; $end = $result[4];

	// grab the author's record
	$query = sprintf ("select accountname,nofposts,date_format(joindate,'%s') from accounts where id=%s", $CONFIG["joindate_timestamp_format"], $announcement_authorid);
	$author_res = db_query ($query); $author_result = db_fetch_results ($author_res);

	// grab the values
	if (db_nof_results ($author_res) > 0) {
	    $author = $author_result[0];
	    $author_status = GetMemberStatus ($announcement_authorid);
	    $author_nofposts = $author_result[1];
      	    $author_joindate = $author_result[2];
	} else {
	    $author = $CONFIG["delmem_name"];
	    $author_status = $CONFIG["unknown_title"];
	    $author_nofposts = $CONFIG["delmem_postcount"];
	    $author_joindate = $CONFIG["delmem_joindate"];
	}

	// evaluate the result
	eval ("\$tmp = stripslashes (\"" . $annclist_template . "\");");
	$annclist .= $tmp;
    }

    // grab some generic values
    $forums_title = $CONFIG["forumtitle"];
    // evaluate the result
    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("announcementpage")) . "\");");
    print $tmp;
    ShowFooter();
 ?>
