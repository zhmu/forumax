<?php
    //
    // deletepoll.php
    //
    // This will delete a forum thread's associated poll.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is the poll id given?
    $pollid = trim (preg_replace ("/\D/", "", $_REQUEST["pollid"]));
    if ($pollid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }

    // get the poll information
    $query = sprintf ("SELECT threadid FROM polls WHERE id='%s'", $pollid);
    $res = db_query ($query); list ($threadid) = db_fetch_results ($res);
    if (db_nof_results ($res) == 0) {
	// there's no such poll. complain
	FatalError ("error_nosuchpoll");
    }

    // need to show the page for creating polls?
    if ($_REQUEST["action"] == "") {
	// yes. build the page and show it
	$VAR["pollid"] = $pollid;
	ShowBaseForumPage("deletepoll_page", $threadid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // fetch the forum ID
    $forumid = GetThreadForumID ($threadid);
    $VAR["forumid"] = $forumid;

    // handle forum restrictions, if any
    HandleRestrictedForum ($forumid);

    // are we a moderator here?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
	FatalError ("error_noaccess");
    }

    // get rid of the poll itself
    $query = sprintf ("DELETE FROM polls WHERE id='%s'", $pollid);
    db_query ($query);

    // get rid of all options
    $query = sprintf ("DELETE FROM poll_options WHERE pollid='%s'", $pollid);
    db_query ($query);

    // get rid of all votes
    $query = sprintf ("DELETE FROM poll_votes WHERE pollid='%s'", $pollid);
    db_query ($query);

    // the thread no longer has a poll attached to it now
    $query = sprintf ("UPDATE threads SET flags=flags&(~%s) WHERE id='%s'", FLAG_THREAD_POLL, $threadid);
    db_query ($query);

    // it worked. inform the user
    ShowForumPage("deletepoll_ok");
 ?>
