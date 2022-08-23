<?php
    //
    // lockthread.php
    //
    // This will lock or unlock a forum thread.
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

    // grab the thread information
    $query = sprintf ("SELECT flags FROM threads WHERE id='%s'", $threadid);
    list ($threadflags) = db_fetch_results (db_query ($query));

    // need to show the lock/unlock page?
    if ($_REQUEST["action"] == "") {
	// yes. is this thread currently locked?
	if (($threadflags & FLAG_THREAD_LOCKED) != 0) {
	    // yes. show the unlock page
	    $template = "unlockthread_page";
	} else {
	    // no. show the lock page
	    $template = "lockthread_page";
	}

	// show the page
        $VAR["threadid"] = $threadid;
	ShowBaseForumPage ($template, $threadid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // fetch the thread's forum ID
    $VAR["forumid"] = GetThreadForumID ($threadid);

    // check forum restrictions
    HandleRestrictedForum ($VAR["forumid"]);

    // for the next steps, we have to be a moderator. are we one?
    if (IsForumMod ($VAR["forumid"]) == 0) {
	// no. complain
	FatalError("error_accessdenied");
    }

    // need to actually lock a thread?
    if ($_REQUEST["action"] == "lockthread") {
	// yes. let's lock the thread
	$query = sprintf ("UPDATE threads SET flags=flags|%s,lockerid='%s' WHERE id='%s'", FLAG_THREAD_LOCKED, $GLOBALS["userid"], $threadid);
	db_query ($query);

	// it worked. now, show the 'yay' page
        $VAR["threadid"] = $threadid;
	ShowForumPage("lockthread_ok");
	exit;
    }

    // need to unlock a thread?
    if ($_REQUEST["action"] == "unlockthread") {
	// yes. let's unlock the thread
	$query = sprintf ("UPDATE threads SET flags=flags&(~%s),lockerid=0,destforum=0 WHERE id='%s'", FLAG_THREAD_LOCKED, $threadid);
	db_query ($query);

	// it worked. now, show the 'yay' page
        $VAR["threadid"] = $threadid;
	ShowForumPage("unlockthread_ok");
	exit;
    }
 ?>
