<?php
    //
    // edittitle.php
    //
    // This will edit the title of a thread.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is a thread id given?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }

    // get the forum id
    $forumid = GetThreadForumID ($threadid); $VAR["forumid"] = $forumid;

    // need to show the 'edit title' page?
    if ($_REQUEST["action"] == "") {
        // yup. build the page and show it
	$VAR["threadid"] = $threadid;
	ShowBaseForumPage("editthread_page", $threadid, $forumid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // now, we need to be a forum mod. are we one?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
	FatalError("error_accessdenied");
    }

    // get the thread flags
    $query = sprintf ("SELECT flags FROM threads WHERE id='%s'", $threadid);
    list ($thread_flags) = db_fetch_results (db_query ($query));

    // is this thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
	FatalError("error_canteditlock");
    }

    // need to actually edit the title?
    if ($_REQUEST["action"] == "edittitle") {
	// yes. render any HTML from the thread title useless
	$the_title = $_REQUEST["the_title"];
	$the_title = preg_replace ("/\</", "&lt;", $the_title);
	$the_title = preg_replace ("/\>/", "&gt;", $the_title);

	// yes. build the query
	$query = sprintf ("UPDATE threads SET title='%s' WHERE id='%s'",$the_title,$threadid);
	db_query ($query);

	// it worked. show the 'yay' page
	$VAR["threadid"] = $threadid;
	ShowForumPage ("edittitle_ok");
    }
 ?>
