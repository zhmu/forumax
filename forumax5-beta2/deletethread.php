<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the thread information
    $query = sprintf ("select title,forumid,nofreplies,authorid from threads where id=%s", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $threadtitle = $result[0]; $forumid = $result[1]; $nofreplies = $result[2];
    $authorid = $result[3];

    // grab the lastest thread poster
    $query = sprintf ("select name,catno,image from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumname = $result[0]; $catid = $result[1]; $forum_image = $result[2];

    // do we have a category id?
    if ($catid != 0) {
        // yes. grab the category name
        $query = sprintf ("select name from categories where id=%s", $catid);
        $res = db_query ($query); $tmp = db_fetch_results ($res);
        $cat_title = $tmp[0];
    }

    // need to show the 'are you sure' page?
    if ($action == "") {
	// yes. do it
	// grab some generic values
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// build the page and show it
        ShowHeader("deletethread_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("deletethread_page")) . "\");");
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

    // is it ok?
    if ($ok == 0) {
	// no. complain
        ShowHeader("error_noaccess");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_noaccess")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually delete the thread?
    if ($action == "deletethread") {
	// yes. first, zap all replies to this thread
	$query = sprintf ("delete from posts where threadid=%s", $threadid);
	db_query ($query);

	// now, zap the thread itself
	$query = sprintf ("delete from threads where id=%s", $threadid);
	db_query ($query);

	// grab the lastest thread poster
	$query = sprintf ("select lastposterid,lastdate from threads where forumid=%s order by lastdate desc limit 1", $forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	if ($result[0] == "") { $result[0] = 0; };

	// update the post count and last reply dates
	$query = sprintf ("update forums set nofposts=nofposts-%s,nofthreads=nofthreads-1,lastposterid=%s,lastpost='%s' where id=%s", $nofreplies+1,$result[0],$result[1],$forumid);
	db_query ($query);

	// all worked perfectly. show the 'yay' page
        ShowHeader("deletethread_ok");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("deletethread_ok")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }
 ?>
