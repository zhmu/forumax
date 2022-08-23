<?php
    //
    // profile.php
    //
    // This will handle user account modifications.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // need to show the profile login page?
    if ($_REQUEST["action"] == "") {
	// yes. grab some values
	$VAR["the_accountname"] = $GLOBALS["username"];
	$VAR["the_password"] = $GLOBALS["password"];

	// show the page
	ShowForumPage("page_profilelogin");
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // need to show the 'edit profile' page?
    if ($_REQUEST["action"] == "editprofile") {
	// yes. show it
	$VAR["the_accountname"] = $GLOBALS["username"];
	$VAR["the_password"] = $GLOBALS["password"];

	// construct the custom fields
	$query = sprintf ("SELECT id,name,perms,type FROM customfields");
	$res = db_query ($query);

	// show them
	while (list ($VAR["fieldid"], $VAR["fieldname"], $fieldperms, $fieldtype) = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($fieldperms != 0) {
		// yes. grab the custom field data
		$query = sprintf ("SELECT extra%s FROM accounts WHERE id='%s'", $VAR["fieldid"], $GLOBALS["userid"]);
		list ($VAR["fieldvalue"]) = db_fetch_results (db_query ($query)); 

		// add it to the list
		$VAR["customfields"] .= InsertSkinVars (GetSkinTemplate ("editcustom_" . $fieldtype));
	    }
	}

	// grab the user's timezone and signature
	$query = sprintf ("SELECT sig,timediff,DATE_FORMAT(birthday,'%%Y'),DATE_FORMAT(birthday,'%%c'),DATE_FORMAT(birthday,'%%e'),flags,email,skinid,sig_option,reply_backlog FROM accounts WHERE id='%s'", $GLOBALS["userid"]);
	$result = db_fetch_results (db_query ($query));
	list ($VAR["the_sig"], $VAR["timezone"], $VAR["year"], $VAR["month"], $VAR["day"], $flags, $VAR["email"], $VAR["userskinid"], $VAR["sig_option"], $VAR["reply_backlog"]) = db_fetch_results (db_query ($query));

	// figure out our user options
	$VAR["privemail"] = (($flags & FLAG_HIDEMAIL) == 0) ? 0 : 1;
	$VAR["censoring"] = (($flags & FLAG_DONTCENSOR) == 0) ? "on" : "off";
	$VAR["autosig"] = (($flags & FLAG_AUTOSIG) == 0) ? "off" : "on";

	// build the skin table
	$query = sprintf ("SELECT id,name FROM skins");
	$res = db_query ($query);
	while (list ($VAR["theskinid"], $VAR["theskinname"]) = db_fetch_results ($res)) {
	    if ($GLOBALS["userskinid"] == $VAR["theskinid"]) {
		$VAR["userskins"] .= InsertSkinVars (GetSkinTemplate ("profile_userskin_sel"));
	    } else {
		$VAR["userskins"] .= InsertSkinVars (GetSkinTemplate ("profile_userskin"));
	    }
	}

	// grab the maximum posts we'll ever show
	$VAR["maxbacklog"] = $CONFIG["reply_maxbacklog"];

	// show the page
	ShowForumPage("page_editprofile");
	exit;
    }

    // need to actually edit the profile?
    if ($_REQUEST["action"] == "doeditprofile") {
	// are the two passwords equal?
	if ($_REQUEST["newpassword1"] != $_REQUEST["newpassword2"]) {
	    // they are not. complain
	    FatalError("error_differentpasswords");
	}

	// is this email address banned?
	if (IsEmailBanned ($_REQUEST["email"]) != 0) {
	    // yes. show the error
	    FatalError ("error_emailbanned");
	}

	// get the old flags
	$query = sprintf ("SELECT flags FROM accounts WHERE accountname='%s'", $_REQUEST["the_accountname"]);
	list ($flags) = db_fetch_results (db_query ($query));

	// get rid of the 'hide email' and 'censoring' flags
	$flags = $flags & ~FLAG_HIDEMAIL;
	$flags = $flags & ~FLAG_DONTCENSOR;
	$flags = $flags & ~FLAG_AUTOSIG;
	if ($_REQUEST["private_email"] == "yes") {
	    // re-add the flag
	    $flags |= FLAG_HIDEMAIL;
	}
	if ($_REQUEST["censoring"] == "off") {
	    // re-add the flag
	    $flags |= FLAG_DONTCENSOR;
	}
	if ($_REQUEST["autosig"] != "") {
	    // re-add the flag
	    $flags |= FLAG_AUTOSIG;
	}

	// build the new date
	if ($month < 10) { $month = "0" . $month; };
	if ($day < 10) { $day = "0" . $day; };
	if ($year < 1000) { $year += 1900; };
	$date = $year . "-" . $month . "-" . $day;

	// has the email address changed?
	$query = sprintf ("SELECT id,email FROM accounts WHERE accountname='%s'", $the_accountname);
	list ($accountid, $old_email) = db_fetch_results (db_query ($query));
	if ($_REQUEST["email"] != $old_email) {
	    // yes, it has. generate a new activate id
	    $activatekey = md5 (uniqid (rand()));

	    // build the email
  	    $tmp = GetSkinFields ("email_changemail", "title,content");
	    list ($subject, $body) = $tmp;
	    $VAR["username"] = $the_accountname;
	    $VAR["url"] = $CONFIG["forum_url"];
	    $VAR["forumtitle"] = $CONFIG["forumtitle"];
	    $body = InsertSkinVars ($body);

	    // send the email
	    Mail ($email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");

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
	$query = sprintf ("UPDATE accounts SET password='%s',email='%s',sig='%s',timediff='%s',birthday='%s',flags=%s,skinid=%s,sig_option=%s,activatekey='%s',reply_backlog='%u' WHERE accountname='%s'", $_REQUEST["newpassword1"], $_REQUEST["email"], $_REQUEST["the_sig"], $_REQUEST["timezone"], $date, $flags, $_REQUEST["userskinid"], $_REQUEST["sig_display"], $activatekey, $_REQUEST["reply_backlog"], $the_accountname);
	db_query ($query);

	// construct the custom fields
	$customfields = "";
	$query = sprintf ("SELECT id,perms FROM customfields");
	$res = db_query ($query);

	// show them
	while (list ($id, $perms) = db_fetch_results ($res)) {
	    // is this thing editable?
	    if ($perms == 0) {
		// yes. set the custom field data
		$query = sprintf ("UPDATE accounts SET extra%s='%s' WHERE id='%s'", $id, $_REQUEST["field[$id]"], $GLOBALS["userid"]);
		db_query ($query);
	    }
	}

	// reload the skin (may have changed)
	LoadSkin();

	// it worked. show the 'wohoo' page
	ShowForumPage($done_template);
    }
 ?>
