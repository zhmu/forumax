<?php
    //
    // ratethread.php
    //
    // This will handle thread rating.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is the thread id and rating given?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    $rating = trim (preg_replace ("/\D/", "", $_REQUEST["rating"]));
    if (($threadid == "") or ($rating == "")) {
	// no. quit
	FatalError ("error_badrequest");
    }

    // are we logged in?
    if ($GLOBALS["logged_in"] == 0) {	
	// no. complain
	FatalError ("error_logintorate");
    }

    // get the thread information
    $query = sprintf ("SELECT flags,forumid FROM threads WHERE id='%s'", $threadid);
    $res = db_query ($query);
    list ($thread_flags, $forumid) = db_fetch_results ($res);

    // handle forum restrictions, if any
    HandleRestrictedForum ($forumid);

    // does this thread exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	FatalError("error_nosuchthread");
    }

    // is the thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
	FatalError("error_cannotratelocked");
    }

    // have we already rated this thread?
    $query = sprintf ("SELECT id FROM threadsrated WHERE threadid='%s' AND accountid='%s'", $threadid, $GLOBALS["userid"]);
    if (db_nof_results (db_query ($query)) > 0) {
	// yes. complain
	FatalError("error_alreadyrated");
    }

    // all is looking good. add the rating
    $query = sprintf ("INSERT INTO threadsrated VALUES (NULL,'%s','%s','%s')", $threadid, $GLOBALS["userid"], $rating);
    db_query ($query);

    // grab the new average
    $query = sprintf ("SELECT AVG(rating) FROM threadsrated WHERE threadid='%s'", $threadid);
    list ($new_rating) = db_fetch_results (db_query ($query));
    $new_rating += 0;

    // update the thread rating
    $query = sprintf ("UPDATE threads SET rating='%s' WHERE id='%s'", $new_rating, $threadid);
    db_query ($query);

    // it worked! inform the user
    $VAR["forumid"] = $forumid;
    ShowForumPage ("rateokpage");
 ?>
