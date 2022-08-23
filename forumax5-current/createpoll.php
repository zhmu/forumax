<?php
    //
    // createpoll.php
    //
    // This will create a poll for a certain forum thread.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is the thread id given?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }
    $VAR["threadid"] = $threadid;

    // grab the thread information
    $query = sprintf ("SELECT forumid,flags,authorid FROM threads WHERE id='%s'", $threadid);
    $res = db_query ($query);
    list ($forumid, $threadflags, $authorid) = db_fetch_results ($res);

    // does this thread exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	FatalError("error_nosuchthread");
    }

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // need to show the page for creating polls?
    if ($_REQUEST["action"] == "") {
	// build the page and show it
	ShowBaseForumPage("newpollpage", $threadid, $forumid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // fetch the question
    $the_question = strip_tags (trim ($_REQUEST["the_question"]));

    // count the number of valid poll options
    $no = 0;
    while (list ($id, $text) = @each ($_REQUEST["the_answer"])) {
	if (strip_tags (trim ($text)) != "") { $no++; }
    }

    // do we have a question and at least 3 options?
    if (($no < 2) or ($the_question == "")) {
	// no. complain
	FatalError ("error_emptyfields");
    }

    // are we the original thread poster?
    if ($authorid != $GLOBALS["userid"]) {
	// no. complain
	FatalError ("error_notcreator");
    }

    // is the thread locked?
    if (($threadflags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
	FatalError ("error_threadlocked");
    }

    // does this thread already have a poll?
    if (($threadflags & FLAG_THREAD_POLL) != 0) {
	// yes. complain
	FatalError ("error_alreadypoll");
    }

    // all looking good! create the poll
    $query = sprintf ("INSERT INTO polls VALUES (NULL,'%s','%s',0,0)", $threadid, $the_question);
    db_query ($query);
    $pollid = db_get_insert_id();

    // create the options
    @reset ($_REQUEST["the_answer"]);
    while (list ($id, $text) = @each ($_REQUEST["the_answer"])) {
	// is this a valid option?
	$text = strip_tags (trim ($text));
	if ($text != "") {
	    // yes. add it
	    $query = sprintf ("INSERT INTO poll_options VALUES (NULL,'%s','%s',0)", $pollid, $text);
	    db_query ($query);
	}
    }

    // update the thread
    $query = sprintf ("UPDATE threads SET flags=flags|%s WHERE id='%s'", FLAG_THREAD_POLL, $threadid);
    db_query ($query);

    // yay, it worked! inform the user
    ShowForumPage("pollokpage");
 ?>
