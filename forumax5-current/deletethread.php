<?php
    //
    // deletethread.php
    //
    // This will delete a thread.
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

    // need to show the 'are you sure' page?
    if ($_REQUEST["action"] == "") {
	// yes. build the page and show it
	$VAR["threadid"] = $threadid;
	ShowBaseForumPage("deletethread_page", $threadid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // grab the forum ID
    $forumid = GetThreadForumID ($threadid);

    // no access by default
    $ok = 0;

    // if we are a forum mod, we can delete the post
    if (IsForumMod ($forumid) != 0) { $ok = 1; };
    // if we created this post and deleting is allowed, it's ok too
    if (($GLOBALS["userid"] == $authorid) and ($CONFIG["user_allowdelete"] != 0)) { $ok = 1; };

    // is it ok?
    if ($ok == 0) {
	// no. complain
	FatalError("error_accessdenied");
    }

    // need to actually delete the thread?
    if ($_REQUEST["action"] == "deletethread") {
	// yes. first, delete all replies to this thread
	$query = sprintf ("DELETE FROM posts WHERE threadid='%s'", $threadid);
	db_query ($query);

	// is a poll associated with this thread?
	$query = sprintf ("SELECT id FROM polls WHERE threadid='%s'", $threadid);
	$res = db_query ($query); list ($pollid) = db_fetch_results ($res);
	if (db_nof_results ($res) > 0) {
	    // yes. get rid of the poll
	    $query = sprintf ("DELETE FROM polls WHERE id='%s'", $pollid);
	    db_query ($query);

	    // get rid of the poll options
	    $query = sprintf ("DELETE FROM poll_options WHERE pollid='%s'",$pollid);
	    db_query ($query);

	    // get rid of the votes
	    $query = sprintf ("DELETE FROM poll_votes WHERE pollid='%s'",$pollid);
	    db_query ($query);
	}

	// query the number of replies first
	$query = sprintf ("SELECT nofreplies FROM threads WHERE id='%s'", $threadid);
	list ($nofreplies) = db_fetch_results (db_query ($query));

	// now, zap the thread itself
	$query = sprintf ("DELETE FROM threads WHERE id='%s'", $threadid);
	db_query ($query);

	// grab the lastest thread poster
	$query = sprintf ("SELECT lastposterid,lastdate,lastpostername FROM threads WHERE forumid='%s' ORDER BY lastdate DESC LIMIT 1", $forumid);
	list ($lastposterid, $lastdate, $lastpostername) = db_fetch_results (db_query ($query));

	// update the post count and last reply dates
	$query = sprintf ("UPDATE forums SET nofposts=nofposts-%s,nofthreads=nofthreads-1,lastposterid='%s',lastpost='%s',lastpostername='%s' WHERE id='%s'", $nofreplies+1,$lastposterid, $lastdate, $lastpostername, $forumid);
	db_query ($query);

	// all worked perfectly. show the 'yay' page
	$VAR["forumid"] = $forumid;
	ShowForumPage("deletethread_ok");
    }
 ?>
