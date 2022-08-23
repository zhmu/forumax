<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is new account registration disabled?
    if ($CONFIG["allow_register"] == 0) {
	// yes. complain
	ShowHeader("error_registerdisabled");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_registerdisabled")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // were we asked to show the registration page?
    if ($action == "") {
	// Yes./ grab the rules	
	$rules = $CONFIG["rules"];

	// show them
	ShowHeader("rules_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("rules_page")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to show the actual registration page?
    if ($action == "register") {
	// yes. build the custom fields
	$customfields = "";
	$query = sprintf ("select id,name,perms,type from customfields");
	$res = db_query ($query);

	// get the skins
	$userskin_template = addslashes (GetSkinTemplate ("profile_userskin"));
	$userskins = "";
	$query = sprintf ("select id,name from skins");
	$res = db_query ($query);
	while (list ($theskinid, $theskinname) = db_fetch_results ($res)) {
	    eval ("\$tmp = stripslashes (\"" . $userskin_template . "\");");
	    $userskins .= $tmp;
	}

	// show the page
	ShowHeader("register_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("register_page")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually generate the account?
    if ($action == "doregister") {
	// yes. are all fields filled in?
	$the_accountname = trim ($the_accountname);
	$the_password = trim ($the_password);
	$the_password2 = trim ($the_password2);
	$the_email = trim ($the_email);
	$userskinid = trim ($userskinid);
	if (($the_accountname == "") or ($the_password == "") or ($the_email == "")) {
	    // no. complain
	    ShowHeader("error_emptyfields");
	    eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_emptyfields")) . "\");");
	    ShowFooter();
	    exit;
	}

	// does this account already exist?
	$query = sprintf ("select id from accounts where accountname='%s'", $the_accountname);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. show the error
	    ShowHeader ("error_accountalreadyexists");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_accountalreadyexists")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}

	// is this email already in use?
	$query = sprintf ("select id from accounts where email='%s'", $the_email);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. show the error
	    ShowHeader ("error_emailalreadyinuse");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_emailalreadyinuse")) . "\");");
	    print $tmp;
	    ShowFooter();
            exit;
	}

	// are the passwords equal?
	if ($the_password != $the_password2) {
	    // they are not. complain
	    ShowHeader ("error_differentpasswords");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_differentpasswords")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}

	// grab the custom fields we need
	$query = sprintf ("select id from customfields");
	$extra = "";
	$res = db_query ($query);
	while ($result = db_fetch_results ($res)) {
	    $extra .= ",''";
	}

	// build the flags
	$flags = 0;
	if ($private_email == "yes") { $flags |= FLAG_HIDEMAIL; };

	// build the new birthdate
	if ($month < 10) { $month = "0" . $month; };
	if ($day < 10) { $day = "0" . $day; };
	if ($year < 1000) { $year += 1900; };
	$birthday = $year . "-" . $month . "-" . $day;

	// all looks OK so far. generate the account
	$activateid = md5 (uniqid (rand()));
	$query = sprintf ("insert into accounts values (NULL,'%s','%s',%s,0,'%s','','','%s',now(),NULL,0,%s,'%s',%s%s)", $the_accountname, $activateid, $flags, $the_email, $the_sig, $userskinid, $birthday, $timezone, $extra);
	db_query ($query); $userid = db_get_insert_id();

	// fill in the custom fields
	$customfields = "";
	$query = sprintf ("select id,perms from customfields");
	$res = db_query ($query);

	// show them
	while ($result = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($result[1] == 0) {
		// yes. set the custom field data
		$query = sprintf ("update accounts set extra%s='%s' where id=%s", $result[0], $field[$result[0]], $userid);
		db_query ($query);
	    }
	}

	// is coppa compliance enabled?
	if ($CONFIG["coppa_enabled"] != 0) {
	    // yes. scan for the country
	    $query = sprintf ("select id from customfields where type=10");
	    $res = db_query ($query); $tmp = db_fetch_results ($res);

	    // did this yield any actual results?
	    if (db_nof_results ($res) > 0) {
		// yes. is this user residing in the USA?	
		if ($field[$tmp[0]] == "United States") {
		    // yes. is the user also below 13?
                    $query = sprintf ("select unix_timestamp(now())-unix_timestamp(birthday) from accounts where id=%s", $userid);
                    $res = db_query ($query);
                    $tmp = db_fetch_results ($res);
                    $age = (int)($tmp[0] / (365 * 3600 * 24));
		    if ($age < 13) {
			// this user is below 13 and in the USA. disable the
			// account, but fix the password
			$query = sprintf ("update accounts set flags=flags|%s,password='%s' where id=%s", FLAG_DISABLED, $the_password, $userid);
			db_query ($query);

			// now, show the special COPPA page
			ShowHeader ("coppa_page");
			eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("coppa_page")) . "\");");
			print $tmp;
			ShowFooter();
			exit;
		    }
		}
	    }
	}

	// build the email
	$tmp = GetSkinFields ("activate_email", "title,content");
	$subject = $tmp[0]; $username = $the_accountname;
	$password = $the_password; $url = $CONFIG["forum_url"];
	$forumtitle = $CONFIG["forumtitle"];
	eval ("\$body = stripslashes (\"" . addslashes ($tmp[1]) . "\");");

	// send the email
	Mail ($the_email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

	// all worked perfectly. show the 'yay, it worked' page
	ShowHeader ("registerok_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("registerok_page")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to activate the account?
    if ($action == "activate") {
	// yes. does this account exist?
	$query = sprintf ("select password from accounts where id=%s and password='%s'", $userid, $activateid);
	if (db_nof_results (db_query ($query)) == 0) {
	    // no, it does not. complain
	    ShowHeader ("error_cannotactivate");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_cannotactivate")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}

	// just change the password
	$query = sprintf ("update accounts set password='%s' where id=%s and password='%s'", $password, $userid, $activateid);
	$res = db_query ($query);

	// it worked. show the 'yay' page
	ShowHeader ("activateok_page");
        eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("activateok_page")) . "\");");
        print $tmp;
        ShowFooter();
        exit;
    }

    // need to send the coppa email?
    if (($action == "coppa_email") and ($CONFIG["coppa_enabled"] != 0)) {
	// yes. get the user's information
        $query = sprintf ("select unix_timestamp(now())-unix_timestamp(birthday),parent_email,accountname,password,email from accounts where id=%s", $userid);
        $res = db_query ($query);
        $tmp = db_fetch_results ($res);
        $age = (int)($tmp[0] / (365 * 3600 * 24));
	if ($age < 13) {
	    // the age is correct. now, do we already have a parent email
	    // address?
	    if ($tmp[1] != "") {
		// yes. complain
		ShowHeader ("error_parentemailed");
		eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_parentemailed")) . "\");");
		print $tmp;
		ShowFooter();
		exit;
	    }

	    // build the coppa email
	    $the_username = $tmp[2]; $the_password = $tmp[3];
	    $the_email = $tmp[4];
	    $tmp = GetSkinFields ("coppa_email", "title,content");
	    $subject = $tmp[0]; $username = $the_accountname;
	    $password = $the_password; $url = $CONFIG["forum_url"];
	    $forumtitle = $CONFIG["forumtitle"];
	    eval ("\$body = stripslashes (\"" . addslashes ($tmp[1]) . "\");");

	    // send the email
	    Mail ($parent_email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

	    // activate the new parent's email address
	    $query = sprintf ("update accounts set parent_email='%s' where id=%s", $parent_email, $userid);
	    db_query ($query);

	    // yay, all went ok. show the 'wohoo' page
	    ShowHeader ("coppa_parentemailed");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("coppa_parentemailed")) . "\");");
	    print $tmp;
	    ShowFooter();
	}
    }
 ?>
