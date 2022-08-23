<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // get the forum id
    $query = sprintf ("select forumid from threads where id=%s",$threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0];

    // get the image
    $query = sprintf ("select image from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forum_image = $result[0];

    // need to show the 'edit title' page?
    if ($action == "") {
        // yup. grab the thread information
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
        ShowHeader("editthread_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("editthread_page")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // grab the thread information
    $query = sprintf ("select forumid from threads where id=%s",$threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0];

    // now, we need to be a forum mod. are we one?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
        ShowHeader("error_accessdenied");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_accessdenied")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually edit the title?
    if ($action == "edittitle") {
	// yes. render any HTML from the thread title useless
	$the_title = preg_replace ("/\</", "&lt;", $the_title);
	$the_title = preg_replace ("/\>/", "&gt;", $the_title);

	// yes. build the query
	$query = sprintf ("update threads set title='%s' where id=%s",$the_title,$threadid);
	db_query ($query);

	// it worked. show the 'yay' page
        ShowHeader("edittitle_ok");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("edittitle_ok")) . "\");");
	print $tmp;
	ShowFooter();
    }
 ?>
