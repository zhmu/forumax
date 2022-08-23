<?php
    //
    // showannouncement.php
    //
    // This will display a forum announcement.
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
    $announcementid = trim (preg_replace ("/\D/", "", $_REQUEST["announcementid"]));
    if ($announcementid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }

    // grab all announcements
    $query = sprintf ("SELECT title,authorid,content,DATE_FORMAT(startdate,'%s'),DATE_FORMAT(enddate,'%s'),forumid FROM announcements WHERE id='%s'", $CONFIG["annc_timestamp_format"], $CONFIG["annc_timestamp_format"],$announcementid);
    $res = db_query ($query);
    list ($VAR["announcement_title"], $VAR["authorid"], $VAR["message"], $VAR["begin"], $VAR["end"], $forumid) = db_fetch_results ($res);

    // does the announcement really exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	FatalError("error_nosuchannouncement");
    }

    // show the announcement
    $VAR["announcement_title"] = CensorText ($VAR["announcement_title"]);
    $VAR["message"] = ApplySmilies (CensorText (FixupMessage ($VAR["message"], FLAG_FORUM_ALLPRIVS)));

    // grab the author's record
    $query = sprintf ("SELECT accountname,nofposts,DATE_FORMAT(joindate,'%s') FROM accounts WHERE id='%s'", $CONFIG["joindate_timestamp_format"], $VAR["authorid"]);
    $author_res = db_query ($query); $author_result = db_fetch_results ($author_res);

    // grab the values
    if (db_nof_results ($author_res) > 0) {
	$VAR["author"] = $author_result[0];
	$VAR["author_status"] = GetMemberStatus ($VAR["authorid"]);
	$VAR["author_nofposts"] = $author_result[1];
	$VAR["author_joindate"] = $author_result[2];
    } else {
	$VAR["author"] = $CONFIG["delmem_name"];
	$VAR["author_status"] = $CONFIG["unknown_title"];
	$VAR["author_nofposts"] = $CONFIG["delmem_postcount"];
	$VAR["author_joindate"] = $CONFIG["delmem_joindate"];
    }

    // evaluate the result
    $VAR["annclist"] = InsertSkinVars (GetSkinTemplate ("announcement_displaylist"));

    // evaluate the result
    ShowBaseForumPage ("announcementpage", 0, $forumid);
 ?>
