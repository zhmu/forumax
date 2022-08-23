<?php
    //
    // editpost.php
    //
    // This will edit a post inside a thread.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is a post id given?
    $postid = trim (preg_replace ("/\D/", "", $_REQUEST["postid"]));
    if ($postid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }
    $VAR["postid"] = $postid;

    // get the post information
    $query = sprintf ("SELECT forumid,threadid,post,flags FROM posts WHERE id='%s'",$postid);
    list ($forumid, $threadid, $VAR["message"], $post_flags) = db_fetch_results (db_query ($query));

    // need to show the 'post reply' page?
    if ($_REQUEST["action"] == "") {
        // yup. fix any special chars
	$VAR["message"] = htmlspecialchars ($VAR["message"]);

	// build the checkbox settings
	$VAR["f_sig"] = "no"; $VAR["f_nosmilies"] = "no";
	if (($post_flags & FLAG_POST_SIG) != 0) { $VAR["f_sig"] = "yes"; };
	if (($post_flags & FLAG_POST_NOSMILIES) != 0) { $VAR["f_nosmilies"] = "yes"; };

	// build the page and show it
	ShowBaseForumPage ("editpost_page", $threadid, $forumid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // handle the forum restrictions, if needed
    HandleRestrictedForum ($forumid);

    // do we have enough access to do this?
    $ok = 0;

    // if we are a forum mod, we can edit the post
    if (IsForumMod ($forumid) != 0) { $ok = 1; };

    // if we created this post and editing is allowed, it's ok too
    if (($GLOBALS["userid"] == $authorid) and ($CONFIG["user_allowedit"] != 0)) { $ok = 1; };

    // now, we need to be a forum mod. are we one?
    if ($ok == 0) {
	// no. complain
	FatalError("error_accessdenied");
    }

    // get the thread flags
    $query = sprintf ("SELECT flags FROM threads WHERE id='%s'",$VAR["threadid"]);
    list ($thread_flags) = db_fetch_results (db_query ($query));

    // is this thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
	FatalError("error_canteditlock");
    }

    // need to actually edit the post?
    if ($_REQUEST["action"] == "editpost") {
	// yes. build the new post flags
	$post_flags = 0;
	if ($_REQUEST["f_sig"] != "") {
	    $post_flags = $post_flags | FLAG_POST_SIG;
	}
	if ($_REQUEST["f_nosmilies"] != "") {
	    $post_flags = $post_flags | FLAG_POST_NOSMILIES;
	}

	// insert this into the database
	$query = sprintf ("UPDATE posts SET post='%s',edittime=NOW(),editid='%s',flags='%s' WHERE id='%s'",$_REQUEST["the_message"],$GLOBALS["userid"],$post_flags,$postid);
	db_query ($query);

	// all worked perfectly. show the 'yay' page
	$VAR["threadid"] = $threadid; $VAR["forumid"] = $forumid;
	ShowForumPage("editpost_ok");
    }
 ?>
