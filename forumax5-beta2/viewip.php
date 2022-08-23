<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // get the forum i0d
    $query = sprintf ("select forumid from posts where id=%s",$postid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0];

    // grab the forum name
    $query = sprintf ("select image from forums where id=%s",$forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forum_image = $result[0];

    // need to show the 'edit title' page?
    if ($action == "") {
        // yup. grab the post information
	$query = sprintf ("select threadid from posts where id=%s", $postid);
        $res = db_query ($query); $result = db_fetch_results ($res);
	$threadid = $result[0];

	// grab the thread information
        $query = sprintf ("select title from threads where id=%s",$threadid);
        $res = db_query ($query); $result = db_fetch_results ($res);
        $threadtitle = $result[0];

        // grab the forum name
        $query = sprintf ("select name,catno from forums where id=%s",$forumid);
        $res = db_query ($query); $result = db_fetch_results ($res);
        $forumname = $result[0]; $catid = $result[1];

	// do we have a category id?
	if ($catid != 0) {
	    // yes. grab the category name
	    $query = sprintf ("select name from categories where id=%s", $catid);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
	    $cat_title = $tmp[0];
	}

	// fill in the fields
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// build the page and show it
        ShowHeader("viewip_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("viewip_page")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // grab the post information
    $query = sprintf ("select threadid from posts where id=%s", $postid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $threadid = $result[0];

    // grab the thread information
    $query = sprintf ("select forumid from threads where id=%s",$threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0];

    // do we need to be an admin or mod?
    if ($CONFIG["ip_log"] != 3) {
	// yes. do we need to be an admin?
	if ($CONFIG["ip_log"] == 1) {
	    // yes. are we one?
	    if (($GLOBALS["flags"] & FLAG_ADMIN) == 0) {
		ShowHeader("error_accessdenied");
		eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_accessdenied")) . "\");");
		print $tmp;
		ShowFooter();
		exit;
	    }
	} else {
            // no. we need to be a forum mod. are we one?
            if (IsForumMod ($forumid) == 0) {
		// no. complain
		ShowHeader("error_accessdenied");
		eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_accessdenied")) . "\");");
		print $tmp;
		ShowFooter();
		exit;
	    }
	}
    }

    // need to actually edit the title?
    if ($action == "viewip") {
	// yes. build the query
	$query = sprintf ("select threadid,ipaddr,forumid from posts where id=%s",$postid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$threadid = $result[0]; $dest_ipaddress = $result[1];
	$forumid = $result[2];

	// resolve the ip address
	$dest_hostname = @gethostbyaddr ($dest_ipaddress);

	// grab the thread title
	$query = sprintf ("select title from threads where id=%s",$threadid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$threadtitle = $result[0];

	// grab the forum name
	$query = sprintf ("select name from forums where id=%s",$forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$forumname = $result[0];

	// grab some other values
        $forums_title = $CONFIG["forumtitle"];

	// it worked. show the 'yay' page
        ShowHeader("viewipresult_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("viewipresult_page")) . "\");");
	print $tmp;
	ShowFooter();
    }
 ?>
