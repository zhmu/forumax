<?php
    //
    // register.php
    //
    // This will handle new account registrations.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is new account registration disabled?
    if (($CONFIG["allow_register"] == 0) and ($_REQUEST["action"] != "activate")) {
	// yes. complain
	FatalError ("error_registerdisabled");
    }

    // were we asked to show the registration page?
    if ($_REQUEST["action"] == "") {
	// yes, grab the rules	
	$VAR["rules"] = $CONFIG["rules"];

	// show them
	ShowForumPage("rules_page");
	exit;
    }

    // need to show the actual registration page?
    if ($_REQUEST["action"] == "register") {
	// yes. build the custom fields
	$customfields = "";
	$query = sprintf ("SELECT id,name,perms,type FROM customfields");
	$res = db_query ($query);

	// build the custom fields
	while (list ($VAR["fieldid"], $VAR["fieldname"], $fieldperms, $fieldtype) = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($fieldperms == 0) {
		// yes. add it to the list
		$VAR["customfields"] .= InsertSkinVars (GetSkinTemplate ("editcustom_" . $fieldtype));
	    }
	}

	// get the skins
	$query = sprintf ("SELECT id,name FROM skins");
	$res = db_query ($query);
	while (list ($VAR["theskinid"], $VAR["theskinname"]) = db_fetch_results ($res)) {
	    $VAR["userskins"] .= InsertSkinVars (GetSkinTemplate ("profile_userskin"));
	}

	// grab the maximum posts we'll ever show
	$VAR["maxbacklog"] = $CONFIG["reply_maxbacklog"];

	// show the page
	ShowForumPage("register_page");
	exit;
    }

    // need to actually generate the account?
    if ($_REQUEST["action"] == "doregister") {
	// yes. make sure CensorText() works
	$GLOBALS["flags"] = 0;

	// are all fields filled in?
	$the_accountname = trim ($_REQUEST["the_accountname"]);
	$the_password = trim ($_REQUEST["the_password"]);
	$the_password2 = trim ($_REQUEST["the_password2"]);
	$the_email = trim ($_REQUEST["the_email"]);
        $userskinid = trim (preg_replace ("/\D/", "", $_REQUEST["userskinid"]));
	if (($the_accountname == "") or ($the_password == "") or ($the_email == "")) {
	    // no. complain
	    FatalError ("error_emptyfields");
	}

	// does this account already exist?
	$query = sprintf ("SELECT id FROM accounts WHERE accountname='%s'", $the_accountname);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. show the error
	    FatalError ("error_accountalreadyexists");
	}

	// is the account name banned?
	if ((CensorText ($the_accountname) != $the_accountname) or (IsAccountNameBanned ($the_accountname) != 0)) {
	    // yes. show the error
	    FatalError ("error_invalidaccountname");
	}

	// is this email already in use?
	$query = sprintf ("SELECT id FROM accounts WHERE email='%s' LIMIT 1", $the_email);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. show the error
	    FatalError ("error_emailalreadyinuse");
	}

	// is this email address banned?
	if (IsEmailBanned ($the_email) != 0) {
	    // yes. show the error
	    FatalError ("error_emailbanned");
	}

	// are the passwords equal?
	if ($the_password != $the_password2) {
	    // they are not. complain
	    FatalError ("error_differentpasswords");
	}

	// grab the custom fields we need
	$query = sprintf ("SELECT id FROM customfields");
	$extra = "";
	$res = db_query ($query);
	while ($result = db_fetch_results ($res)) {
	    $extra .= ",''";
	}

	// build the flags
	$flags = 0;
	if ($_REQUEST["private_email"] == "yes") { $flags |= FLAG_HIDEMAIL; };
	if ($_REQUEST["censoring"] == "off") { $flags |= FLAG_DONTCENSOR; };
	if ($_REQUEST["autosig"] != "") { $flags |= FLAG_AUTOSIG; };

	// build the new birthdate
	$month = $_REQUEST["month"]; $day = $_REQUEST["day"]; $year = $_REQUEST["year"];
	if ($month < 10) { $month = "0" . $month; };
	if ($day < 10) { $day = "0" . $day; };
	if ($year < 1000) { $year += 1900; };
	$birthday = $year . "-" . $month . "-" . $day;

	// all looks OK so far. generate the account
	$activatekey = md5 (uniqid (rand()));
	$query = sprintf ("INSERT INTO accounts VALUES (NULL,'%s','%s',%s,0,'%s','','','%s',now(),NULL,0,%s,'%s',%s,%s,0,'%s','%s',''%s)", $the_accountname, $the_password, $flags, $the_email, $the_sig, $userskinid, $birthday, $timezone, $sig_option, $activatekey, $reply_backlog, $extra);
	db_query ($query); $accountid = db_get_insert_id();

	// fill in the custom fields
	$customfields = "";
	$query = sprintf ("SELECT id,perms FROM customfields");
	$res = db_query ($query);

	// show them
	while (list ($customid, $customperms) = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($customperms == 0) {
		// yes. set the custom field data
		$query = sprintf ("UPDATE accounts SET extra%s='%s' WHERE id=%s", $customid, $_REQUEST["field[$customid]"], $accountid);
		db_query ($query);
	    }
	}

	// is coppa compliance enabled?
	if ($CONFIG["coppa_enabled"] != 0) {
	    // yes. scan for the country
	    $query = sprintf ("SELECT id FROM customfields WHERE type=10");
	    $res = db_query ($query); $tmp = db_fetch_results ($res);

	    // did this yield any actual results?
	    if (db_nof_results ($res) > 0) {
		// yes. is this user residing in the USA?
		if ($_REQUEST["field[" . $tmp[0] . "]"] == "United States") {
		    // yes. is the user also below 13?
                    $query = sprintf ("SELECT UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(birthday) FROM accounts WHERE id='%s'", $accountid);
                    $res = db_query ($query);
                    $tmp = db_fetch_results ($res);
                    $age = (int)($tmp[0] / (365 * 3600 * 24));
		    if ($age < 13) {
			// this user is below 13 and in the USA. disable the
			// account and show the coppa page
			$query = sprintf ("UPDATE accounts SET flags=flags|%s,activatekey='' WHERE id='%s'", FLAG_DISABLED | FLAG_COPPA | FLAG_HIDEMAIL, $accountid);
			db_query ($query);

			// now, show the special COPPA page
			ShowForumPage("coppa_page");
			exit;
		    }
		}
	    }
	}

	// build the email
	list ($subject, $body) = GetSkinFields ("email_activate", "title,content");
	$VAR["username"] = $the_accountname;
	$VAR["password"] = $the_password;
	$VAR["url"] = $CONFIG["forum_url"];
	$VAR["forumtitle"] = $CONFIG["forumtitle"];
	$body = InsertSkinVars ($body);

	// send the email
	Mail ($the_email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

	// all worked perfectly. show the 'yay, it worked' page
	ShowForumPage("registerok_page");
	exit;
    }

    // need to activate the account?
    if ($_REQUEST["action"] == "activate") {
	// yes. are both the user id and activate key given?
        $accountid = trim (preg_replace ("/\D/", "", $_REQUEST["accountid"]));
	$activatekey = trim ($_REQUEST["activatekey"]);
	if (($accountid == "") or ($activatekey == "")) {
	    // no. complain
	    FatalError ("error_badrequest");
	}

	// does this account exist?
	$query = sprintf ("SELECT id FROM accounts WHERE id='%s' AND activatekey='%s'", $accountid, $activatekey);
	if (db_nof_results (db_query ($query)) == 0) {
	    // no, it does not. complain
	    FatalError ("error_cannotactivate");
	}

	// just change the password
	$query = sprintf ("UPDATE accounts SET activatekey='' WHERE id='%s'", $accountid);
	db_query ($query);

	// it worked. show the 'yay' page
	ShowForumPage("activateok_page");
        exit;
    }

    // need to send the coppa email?
    if (($_REQUEST["action"] == "coppa_email") and ($CONFIG["coppa_enabled"] != 0)) {
	// yes. is the account id given?
	$accountid = trim (preg_replace ("/\D/", "", $_REQUEST["accountid"]));
	if ($accountid == "") {
	    // no. complain
	    FatalError ("error_badrequest");
	}

	// get the user's information
        $query = sprintf ("SELECT parent_email,accountname,password,email,flags from accounts WHERE id='%s'", $accountid);
	list ($parent_email, $VAR["the_username"], $VAR["the_password"], $the_email, $the_flags) = db_fetch_results (db_query ($query));

	$VAR["faxno"] = $CONFIG["coppa_fax_no"];

	// is this account flagged as coppa?
	if (($the_flags & FLAG_COPPA) == 0) {
	    // no. complain
	    FatalError ("error_nocoppauser");
	}

	//  do we already have a parent email address?
	if ($parent_email != "") {
	    // yes. complain
	    FatalError ("error_parentemailed");
	}

	// build the coppa email
	list ($subject, $body) = GetSkinFields ("email_coppa", "title,content");
	$VAR["username"] = $the_accountname;
	$VAR["password"] = $the_password;
	$VAR["url"] = $CONFIG["forum_url"];
	$VAR["forumtitle"] = $CONFIG["forumtitle"];
	$body = InsertSkinVars ($body);

	// send the email
	Mail ($parent_email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

	// activate the new parent's email address
	$query = sprintf ("UPDATE accounts SET parent_email='%s' WHERE id=%s", $parent_email, $accountid);
	db_query ($query);

	// yay, all went ok. show the 'wohoo' page
	ShowForumPage("coppa_parentemailed");
    }
 ?>
