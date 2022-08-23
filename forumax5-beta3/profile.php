<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // need to show the profile login page?
    if ($action == "") {
	// yes. grab some values
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// show the page
	ShowHeader("page_profilelogin");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("page_profilelogin")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // need to show the 'edit profile' page?
    if ($action == "editprofile") {
	// yes. show it
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// construct the custom fields
	$customfields = "";
	$query = sprintf ("select id,name,perms,type from customfields");
	$res = db_query ($query);

	// show them
	while ($result = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($result[2] == 0) {
		// yes. grab the custom field data
		$query = sprintf ("select extra%s from accounts where id=%s", $result[0], $GLOBALS["userid"]);
		$tmp = db_fetch_results (db_query ($query)); 

		// add it to the list
		$fieldname = $result[1]; $fieldid = $result[0];
		$fieldvalue = $tmp[0];
		eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("editcustom_" . $result[3])) . "\");");
	        $customfields .= $tmp;
	    }
	}

	// grab the user's timezone and signature
	$query = sprintf ("select sig,timediff,date_format(birthday,'%%Y'),date_format(birthday,'%%c'),date_format(birthday,'%%e'),flags,email,skinid from accounts where id=%s", $GLOBALS["userid"]);
	$result = db_fetch_results (db_query ($query));
 	$the_sig = $result[0]; $timezone = $result[1];
	$year = $result[2]; $month = $result[3]; $day = $result[4];
	$flags = $result[5]; $email = $result[6]; $userskinid = $result[7];

	// do we have a private email address?
	if (($flags & FLAG_HIDEMAIL) == 0) {
	    // no. disable the flag
	    $privemail = 0;
	} else {
	    // yes. set the flag
	    $privemail = 1;
	}

	// get the skins
	$userskin_template = addslashes (GetSkinTemplate ("profile_userskin"));
	$userskinsel_template = addslashes (GetSkinTemplate ("profile_userskin_sel"));
	$userskins = "";
	$query = sprintf ("select id,name from skins");
	$res = db_query ($query);
	while (list ($theskinid, $theskinname) = db_fetch_results ($res)) {
	    if ($userskinid == $theskinid) {
	        eval ("\$tmp = stripslashes (\"" . $userskinsel_template . "\");");
	    } else {
	        eval ("\$tmp = stripslashes (\"" . $userskin_template . "\");");
	    }
	    $userskins .= $tmp;
	}

	// show the page
	ShowHeader("page_editprofile");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("page_editprofile")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually edit the profile?
    if ($action == "doeditprofile") {
	// are the two passwords equal?
	if ($newpassword1 != $newpassword2) {
	    // they are not. complain
	    ShowHeader ("error_differentpasswords");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_differentpasswords")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}

	// get the old flags
	$query = sprintf ("select flags from accounts where accountname='%s'", $the_accountname);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$flags = $tmp[0];

	// get rid of the 'hide email' page
	$flags = $flags & ~FLAG_HIDEMAIL;
	if ($private_email == "yes") {
	    // re-add the flag
	    $flags |= FLAG_HIDEMAIL;
	}

	// build the new date
	if ($month < 10) { $month = "0" . $month; };
	if ($day < 10) { $day = "0" . $day; };
	if ($year < 1000) { $year += 1900; };
	$date = $year . "-" . $month . "-" . $day;

	// has the email address changed?
	$query = sprintf ("select email from accounts where accountname='%s'", $the_accountname);
	list ($old_email) = db_fetch_results (db_query ($query));
	if ($email != $old_email) {
	    // yes, it has. generate a new activate id
	    $activateid = md5 (uniqid (rand()));

	    // build the email
  	    $tmp = GetSkinFields ("template_email", "title,content");
	    $subject = $tmp[0]; $username = $the_accountname;
	    $password = $newpassword1; $url = $CONFIG["forum_url"];
	    $forumtitle = $CONFIG["forumtitle"];
	    eval ("\$body = stripslaSHES (\"" . addslashes ($tmp[1]) . "\");");

	    // send the email
	    Mail ($email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

	    // change the password to the activation id
	    $newpassword1 = $activateid;

	    // show a different 'done' page
	    $done_template = "editprofile_emailok";

	    // get rid of the authentication cookie
            SetCookie ("authid", "", 0);
	} else {
	    // show the generic 'done' page
	    $done_template = "editprofile_ok";

	    // use a new cookie
            SetCookie ("authid", $the_accountname . ":" . $newpassword1, 3600);
	}

	// update the entry
	$query = sprintf ("update accounts set password='%s',email='%s',sig='%s',timediff='%s',birthday='%s',flags=%s,skinid=%s where accountname='%s'", $newpassword1, $email, $the_sig, $timezone, $date, $flags, $userskinid, $the_accountname);
	db_query ($query);

	// construct the custom fields
	$customfields = "";
	$query = sprintf ("select id,perms from customfields");
	$res = db_query ($query);

	// show them
	while ($result = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($result[1] == 0) {
		// yes. set the custom field data
		$query = sprintf ("update accounts set extra%s='%s' where id=%s", $result[0], $field[$result[0]], $GLOBALS["userid"]);
		db_query ($query);
	    }
	}

	// reload the skin (may have changed)
	LoadSkin();

	// it worked. show the 'wohoo' page
	ShowHeader($done_template);
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ($done_template)) . "\");");
	print $tmp;
	ShowFooter();
    }
 ?>
