<?php
    //
    // stickthread.php
    //
    // This will mark a thread sticky or unsticky.
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

    // need to show the stick/unstick page?
    if ($_REQUEST["action"] == "") {
	// yes. fetch the thread flags
	$query = sprintf ("SELECT flags FROM threads WHERE id='%s'", $threadid);
	list ($threadflags) = db_fetch_results (db_query ($query));

	// is this thread currently sticky?
	if (($threadflags & FLAG_THREAD_STICKY) != 0) {
	    // yes. show the 'make unsticky' page
	    $whichpage = "unstickthread_page";
	} else {
	    // no. show the 'make sticky' page
	    $whichpage = "stickthread_page";
	}

	// build the page and show it
	$VAR["threadid"] = $threadid;
	ShowBaseForumPage($whichpage, $threadid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // grab the forum ID
    $forumid = GetThreadForumID ($threadid);
    $VAR["forumid"] = $forumid;

    // check forum restrictions, if any
    HandleRestrictedForum ($forumid);

    // for the next step, we have to be a moderator. are we one?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
	FatalError("error_accessdenied");
	exit;
    }

    // need to actually stick a thread?
    if ($_REQUEST["action"] == "stickthread") {
	// yes. let's lock the thread
	$query = sprintf ("UPDATE threads SET flags=flags|%s WHERE id='%s'", FLAG_THREAD_STICKY, $threadid);
	db_query ($query);

	// it worked. now, show the 'yay' page
	ShowForumPage("stickthread_ok");
	exit;
    }

    // need to unstick a thread?
    if ($_REQUEST["action"] == "unstickthread") {
	// yes. let's unstick the thread
	$query = sprintf ("UPDATE threads SET flags=flags&(~%s) WHERE id='%s'", FLAG_THREAD_STICKY, $threadid);
	db_query ($query);

	// it worked. now, show the 'yay' page
	ShowForumPage("unstickthread_ok");
	exit;
    }
 ?>
