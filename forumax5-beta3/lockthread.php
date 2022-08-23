<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the thread information
    $query = sprintf ("select title,forumid,flags from threads where id=%s", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $threadtitle = CensorText ($result[0]); $forumid = $result[1];
    $threadflags = $result[2];

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // get the forum image
    $query = sprintf ("select image from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forum_image = $result[0];

    // need to show the lock/unlock page?
    if ($action == "") {
	// yes. build the page
	// is this thread currently locked?
	if (($threadflags & FLAG_THREAD_LOCKED) != 0) {
	    // yes. show the unlock page
	    $whichpage = "unlockthread_page";
	} else {
	    // no. show the lock page
	    $whichpage = "lockthread_page";
	}

	// grab the forum name
	$query = sprintf ("select name,catno from forums where id=%s", $forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$forumname = $result[0]; $catid = $result[1];

        // do we have a category id?
        if ($catid != 0) {
            // yes. grab the category name
            $query = sprintf ("select name from categories where id=%s", $catid);
            $res = db_query ($query); $tmp = db_fetch_results ($res);
            $cat_title = $tmp[0];
        }

	// grab some generic values
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// build the page and show it
        ShowHeader($whichpage);
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ($whichpage)) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // for the next steps, we have to be a moderator. are we one?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
        ShowHeader("error_accessdenied");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_accessdenied")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually lock a thread?
    if ($action == "lockthread") {
	// yes. let's lock the thread
	$query = sprintf ("update threads set flags=flags | %s,lockerid=%s where id=%s", FLAG_THREAD_LOCKED, $GLOBALS["userid"], $threadid);
	db_query ($query);

	// it worked. now, show the 'yay' page
        ShowHeader("lockthread_ok");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("lockthread_ok")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to unlock a thread?
    if ($action == "unlockthread") {
	// yes. let's unlock the thread
	$query = sprintf ("update threads set flags=flags & (!%s),lockerid='',destforum=0 where id=%s", FLAG_THREAD_LOCKED, $threadid);
	db_query ($query);

	// it worked. now, show the 'yay' page
        ShowHeader("unlockthread_ok");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("unlockthread_ok")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }
 ?>
