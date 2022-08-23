<?php
    //
    // vote.php
    //
    // This will handle voting in polls.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is the poll id and option id given?
    $pollid = trim (preg_replace ("/\D/", "", $_REQUEST["pollid"]));
    $optionid = trim (preg_replace ("/\D/", "", $_REQUEST["optionid"]));
    if (($pollid == "") or ($optionid == "")) {
	// no. quit
	FatalError ("error_badrequest");
    }

    // are we logged in?
    if ($GLOBALS["logged_in"] == 0) {
	// no. quit
	FatalError ("error_logintovote");
    }

    // does this poll exist?
    $query = sprintf ("SELECT threadid FROM polls WHERE id='%s'", $pollid);
    $res = db_query ($query); list ($threadid) = db_fetch_results ($res);
    if (db_nof_results ($res) == 0) {
	// no. complain
	FatalError ("error_nosuchpoll");
    }

    // grab the thread's forum ID
    $forumid = GetThreadForumID ($threadid);

    // handle forum restrictions, if any
    HandleRestrictedForum ($forumid);

    // does this option exist?
    $query = sprintf ("SELECT id FROM poll_options WHERE pollid=%s AND id=%s", $pollid, $optionid);
    if (db_nof_results (db_query ($query)) == 0) {
	// no. complain
	FatalError ("error_nosuchpoll");
    }

    // have we already voted in this poll?
    $query = sprintf ("SELECT id FROM poll_votes WHERE pollid=%s AND accountid=%s", $pollid, $GLOBALS["userid"]);
    if (db_nof_results (db_query ($query)) > 0) {
	// no. complain
	FatalError ("error_alreadyvoted");
    }

    // okay, all seems to be in order here. add the vote
    $query = sprintf ("UPDATE poll_options SET nofvotes=nofvotes+1 WHERE id='%s'", $optionid);
    db_query ($query);

    // increment the total number of votes
    $query = sprintf ("UPDATE polls SET totalvotes=totalvotes+1 WHERE id='%s'", $pollid);
    db_query ($query);

    // this account has voted now
    $query = sprintf ("INSERT INTO poll_votes VALUES (NULL,'%s','%s')", $pollid, $GLOBALS["userid"]);
    db_query ($query);

    // it worked. inform the user
    $VAR["threadid"] = $threadid;
    ShowForumPage("voteokpage");
 ?>
