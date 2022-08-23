<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the post information
    $query = sprintf ("select forumid,threadid,authorid from posts where id=%s",$postid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0]; $threadid = $result[1]; $authorid = $result[2];

    // grab the author name
    $query = sprintf ("select accountname from accounts where id=%s",$authorid);
    $tmp = db_fetch_results (db_query ($query)); $author = $tmp[0];

    // grab the thread title
    $query = sprintf ("select title from threads where id=%s",$threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $threadtitle = $result[0];

    // grab the forum name
    $query = sprintf ("select name,catno,image from forums where id=%s",$forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumname = $result[0]; $catid = $result[1]; $forum_image = $result[2];

    // do we have a category id?
    if ($catid != 0) {
	// yes. grab the category name
	$query = sprintf ("select name from categories where id=%s", $catid);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$cat_title = $tmp[0];
    }

    // is this the first post in the thread?
    $query = sprintf ("select id from posts where threadid=%s order by id asc limit 1", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    if ($postid == $result[0]) {
	// yes. redirect to the thread deletion
	Header ("Location: deletethread.php?threadid=" . $threadid);
	exit;
    }

    // need to show the 'are you sure' page?
    if ($action == "") {
	// yes. grab some generic values
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// build the page and show it
        ShowHeader("deletepost_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("deletepost_page")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    $ok = 0;

    // if we are a forum mod, we can delete the post
    if (IsForumMod ($forumid) != 0) { $ok = 1; };
    // if we created this post and deleting is allowed, it's ok too
    if (($GLOBALS["userid"] == $authorid) and ($CONFIG["user_allowdelete"] != 0)) { $ok = 1; };

    // was it ok?
    if ($ok == 0) {
	// no. complain
        ShowHeader("error_noaccess");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_noaccess")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually delete the post?
    if ($action == "deletepost") {
	// yes. do it
	$query = sprintf ("delete from posts where id=%s",$postid);
	db_query ($query);

	// grab the new last poster
	$query = sprintf ("select authorid,timestamp from posts where threadid=%s order by timestamp desc limit 1", $threadid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$lastposterid = $result[0]; $lastdate = $result[1];

	// update the thread
	$query = sprintf ("update threads set nofreplies=nofreplies-1,lastdate='%s',lastposterid=%s where id=%s",$lastdate,$lastposterid,$threadid);
	db_query ($query);

	// grab the lastest thread poster
	$query = sprintf ("select lastposterid,lastdate from threads where forumid=%s order by lastdate desc limit 1", $forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// update the post count and last reply dates
	$query = sprintf ("update forums set nofposts=nofposts-1,lastposterid=%s,lastpost='%s' where id=%s",$result[0],$result[1],$forumid);
	db_query ($query);

	// all worked perfectly. show the 'yay' page
        ShowHeader("deletepost_ok");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("deletepost_ok")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }
 ?>
