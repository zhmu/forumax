<?php
    //
    // forgotpass.php
    //
    // This will send out account passwords by email on request.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // need to show the 'insert username' page?
    if ($_REQUEST["action"] == "") {
	// build the page and show it
	ShowForumPage("forgotpass_page");
	exit;
    }

    // need to actually send the password?
    if ($_REQUEST["action"] == "emailpass") {
	// get the password
	$query = sprintf ("SELECT password,email,mailpassword_date FROM accounts WHERE accountname='%s'", $_REQUEST["the_accountname"]);
	$res = db_query ($query);
	list ($password, $email, $pw_date) = db_fetch_results ($res);

	// has the password already been sent today?
	if ($pw_date == date ("Y-m-d")) {
	    // yes. complain
	    FatalError ("error_passalreadysent");
	}

	// did we have any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    FatalError ("error_nosuchuser");
	}

	// do we have an email address?
	if ($email == "") {
  	    // no. complain
	    FatalError ("error_noemailaddress");
	}

	// yes. build the email
	list ($subject, $body) = GetSkinFields ("email_forgotpass", "title,content");
	$VAR["username"] = $the_accountname;
	$VAR["password"] = $password;
	$VAR["url"] = $CONFIG["forum_url"];
	$VAR["forumtitle"] = $CONFIG["forumtitle"];
	$body = InsertSkinVars ($body);

	// set the last password mail date to today
	$query = sprintf ("UPDATE accounts SET mailpassword_date=NOW() WHERE accountname='%s'", $_REQUEST["the_accountname"]);
	db_query ($query);

	// send the email
	Mail ($email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

	// all worked perfectly. show the 'yay, it worked' page
	ShowForumPage ("forgotpass_ok");
    }
?>
