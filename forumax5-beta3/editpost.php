<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // always get the forum id
    $query = sprintf ("select forumid,threadid,post from posts where id=%s",$postid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0]; $threadid = $result[1]; $message = $result[2];

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // grab the forum image
    $query = sprintf ("select image from forums where id=%s",$forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forum_image = $result[0];

    // need to show the 'post reply' page?
    if ($action == "") {
        // yup. grab the post information
        $query = sprintf ("select threadid,post from posts where id=%s",$postid);
        $res = db_query ($query); $result = db_fetch_results ($res);
        $threadid = $result[0]; $message = $result[1];

	// also, fix any special chars
	$message = htmlspecialchars ($message);

        // grab the thread title
        $query = sprintf ("select title from threads where id=%s",$threadid);
        $res = db_query ($query); $result = db_fetch_results ($res);
        $threadtitle = CensorText ($result[0]);

	// is there a thread title?
	if (trim ($threadtitle) == "") {
	    // no. revert to the default one
	    $threadtitle = $CONFIG["default_topic"];
	}


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
        ShowHeader("editpost_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("editpost_page")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // now, grab the thread id
    $query = sprintf ("select forumid,authorid from posts where id=%s",$postid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumid = $result[0]; $authorid = $result[1];

    // do we have enough access to do this?
    $ok = 0;

    // if we are a forum mod, we can edit the post
    if (IsForumMod ($forumid) != 0) { $ok = 1; };
    // if we created this post and editing is allowed, it's ok too
    if (($GLOBALS["userid"] == $authorid) and ($CONFIG["user_allowedit"] != 0)) { $ok = 1; };

    // now, we need to be a forum mod. are we one?
    if ($ok == 0) {
	// no. complain
        ShowHeader("error_noaccess");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_noaccess")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // get the thread flags
    $query = sprintf ("select flags from threads where id=%s", $threadid);
    list ($thread_flags) = db_fetch_results (db_query ($query));

    // is this thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
        ShowHeader("error_canteditlock");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_canteditlock")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually edit the post?
    if ($action == "editpost") {
	// yes. alter the message

	// insert this into the database
	$query = sprintf ("update posts set post='%s',edittime=now(),editid=%s where id=%s",$the_message,$GLOBALS["userid"],$postid);
	db_query ($query);

	// now, grab the thread id
        $query = sprintf ("select threadid from posts where id=%s",$postid);
        $res = db_query ($query); $result = db_fetch_results ($res);
        $threadid = $result[0];

	// it worked. show the 'yay' page
	// all worked perfectly. show the 'yay' page
        ShowHeader("editpost_ok");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("editpost_ok")) . "\");");
	print $tmp;
	ShowFooter();
    }
 ?>
