<?php
    //
    // deletepost.php
    //
    // This will delete a post in a thread.
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

    // grab the post's thread
    $query = sprintf ("SELECT threadid FROM posts WHERE id='%s'", $postid);
    $res = db_query ($query); list ($threadid) = db_fetch_results ($res);
    if (db_nof_results ($res) == 0) {
	// there is no such post. complain
	FatalError ("error_nosuchpost");
    }

    // is this the first post in the thread?
    $query = sprintf ("SELECT id FROM posts WHERE threadid='%s' ORDER BY id ASC LIMIT 1", $threadid);
    list ($firstpostid) = db_fetch_results (db_query ($query));
    if ($firstpostid == $postid) {
	// yes. redirect to the thread deletion
	Header ("Location: deletethread.php?threadid=" . $threadid);
	exit;
    }

    // need to show the 'are you sure' page?
    if ($_REQUEST["action"] == "") {
	// yes. build the page and show it
	$VAR["postid"] = $postid;
	ShowBaseForumPage("deletepost_page", $threadid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // fetch the forum ID
    $forumid = GetThreadForumID ($threadid);

    $ok = 0;

    // if we are a forum mod, we can delete the post
    if (IsForumMod ($forumid) != 0) { $ok = 1; };

    // if we created this post and deleting is allowed, it's ok too
    if (($GLOBALS["userid"] == $authorid) and ($CONFIG["user_allowdelete"] != 0)) { $ok = 1; };

    // was it ok?
    if ($ok == 0) {
	// no. complain
	FatalError("error_accessdenied");
    }

    // fetch the thread flags
    $query = sprintf ("SELECT flags FROM threads WHERE id='%s'", $threadid);
    list ($thread_flags) = db_fetch_results (db_query ($query));

    // is this thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
	FatalError("error_canteditlock");
    }

    // need to actually delete the post?
    if ($_REQUEST["action"] == "deletepost") {
	// yes. do it
	$query = sprintf ("DELETE FROM posts WHERE id='%s'",$postid);
	db_query ($query);

	// grab the new last poster
	$query = sprintf ("SELECT authorid,timestamp,authorname FROM posts WHERE threadid='%s' ORDER BY timestamp DESC LIMIT 1", $threadid);
	list ($lastposterid, $lastdate, $lastpostername) = db_fetch_results (db_query ($query));

	// update the thread
	$query = sprintf ("UPDATE threads SET nofreplies=nofreplies-1,lastdate='%s',lastposterid=%s,lastpostername='%s' WHERE id='%s'",$lastdate,$lastposterid,$lastpostername,$threadid);
	db_query ($query);

	// grab the lastest forum poster
	$query = sprintf ("SELECT lastposterid,lastpostername,lastdate FROM threads WHERE forumid='%s' ORDER BY lastdate DESC LIMIT 1", $forumid);
	list ($lastposterid, $lastpostername, $lastdate) = db_fetch_results (db_query ($query));

	// update the post count and last reply dates
	$query = sprintf ("UPDATE forums SET nofposts=nofposts-1,lastposterid='%s',lastpost='%s',lastpostername='%s' WHERE id='%s'",$lastposterid,$lastdate,$lastpostername,$forumid);
	db_query ($query);

	// all worked perfectly. show the 'yay' page
	$VAR["threadid"] = $threadid;
	ShowForumPage("deletepost_ok");
    }
 ?>
