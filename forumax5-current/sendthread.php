<?php
    //
    // sendthread.php
    //
    // This will email a link to a thread to a friend.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is the thread id valid?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. complain
	FatalError ("error_badrequest");
    }

    // need to show the 'send thread' page?
    if ($_REQUEST["action"] == "") {
 	// yes. build the page and show it
	$VAR["threadid"] = $threadid;
	ShowBaseForumPage("sendthread_page", $threadid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // grab the forum id
    $forumid = GetThreadForumID ($threadid);
    HandleRestrictedForum ($forumid);
    $VAR["forumid"] = $forumid;

    // need to actually send the thread?
    if ($_REQUEST["action"] == "sendthread") {
	// yep. do we have any blank fields?
	$recip_email = trim ($recip_email); $recip_name = trim ($recip_name);
	if (($recip_email == "") or ($recip_name == "")) {
	    // yes. complain
	    FatalError("error_emptyfields");
	}

	// get the subject of the thread
	$query = sprintf ("SELECT title FROM threads WHERE id=%s",$threadid);
	list ($VAR["threadtitle"]) = db_fetch_results (db_query ($query));

	// build the email
	$query = sprintf ("SELECT email FROM accounts WHERE id='%s'", $userid);
	list ($email) = db_fetch_results (db_query ($query));

	list ($subject, $body) = GetSkinFields ("email_sendthread", "title,content");
	$VAR["username"] = $the_accountname;
	$VAR["userid"] = $GLOBALS["userid"];

	$VAR["url"] = $CONFIG["forum_url"];
	$VAR["forumtitle"] = $CONFIG["forumtitle"];
	$subject = InsertSkinVars ($subhect);
	$body = InsertSkinVars ($body);

	// send the email
	Mail ($recip_email, $subject, $body, "From: " . $email . "\nContent-Type: text/html");

	// all worked perfectly. show the 'yay, it worked' page
	$VAR["threadid"] = $threadid;
	ShowForumPage("sendthread_ok");
    }
?>
