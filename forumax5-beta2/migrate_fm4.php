<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // $step["action"] = "description" are all forums steps, with the
    // appropriate action and description
    $step["intro"] = "Introduction";
    $step["getinfo"] = "Request information";
    $step["analyze"] = "Analyze forum";
    $step["options"] = "Collect migration options";
    $step["migrate"] = "Migrate!";

    // $global_values[$value] = $ataction are the global values that will be
    // carried through the entire installer. A value will not be passed if
    // $ataction is equal to $action.
    $global_values["cgidir"] = "getinfo";

    //
    // InitPage()
    //
    // This will initialize the HTML page.
    //
    function
    InitPage() {
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><head>
<title><?php global $step, $action; echo "ForuMAX migration - " . $step[$action]; ?></title>
</head><body text="#ffffff" link="#ffff00" vlink="#ffff00" alink="#ffff00">
<table width="100%" border=1 bgcolor="#4a6ea5" cellspacing=1 cellpadding=1>
<tr>
  <td width="20%" align="center"><?php
    global $step, $action;

    // add all steps
    while (list ($theaction, $thedesc) = each ($step)) {
	// is this step currently selected?
	if ($action == $theaction) {
	    // yup. make it yellow and bold
	    print "<font color=\"#ffff00\"><b>$thedesc</b></font>";
	} else {
	    // no, just print it
	    print "$thedesc";
	}

	// add proper spacing
	print "<p>";
    }
?></td>
  <td width="90%"><center><font size=5><b>ForuMAX Migration Utility</b></font></center><p>
<?php
    }

    //
    // DonePage()
    //
    // This will deinitialize a HTML page.
    //
    function
    DonePage() {
 ?>
</td></tr>
</table><p>
<center><font color="#000000" size=1>ForuMAX is &copy; 1999-2001 Rink Springer</font></center>
</body></html>
<?php
    }

    //
    // BackNextButton($next_form)
    //
    // This will show the 'back' and 'next' buttons, as needed. If $next_form
    // is non-zero, this will also add the <form> tags for the Next button.
    //
    function
    BackNextButton($next_form) {
	global $step, $action, $global_values;

	// grab the number of the action
	$tmp = array_keys ($step);

	// figure out the number
	$no = 0; reset ($step);
	while (list ($theaction) = each ($step)) {
	    if ($theaction == $action) { break; }
	    $no++;
	}

	// grab the previous and next action
	$prev_action = $tmp[$no - 1];
	$next_action = $tmp[$no + 1];

	// build a table
 ?><table width="100%">
<tr><td width="50%" align="left"><?php
	// do we have a previous action?
	if ($prev_action != "") {
	    // yup. add the button
	    print "<input type=\"submit\" value=\"<< Previous\" onClick=\"javascript: history.go (-1); \">";
	}
 ?></td><td width="50%" align="right"><?php
	// do we have a next action?
	if ($next_action != "") {
	    // yup. print it
	    if ($next_form != 0) {
		// add the form
		print "<form action=\"migrate_fm4.php\" method=\"post\">";
	    }
	    print "<input type=\"hidden\" name=\"action\" value=\"$next_action\">";
	    // add all global values
	    while (list ($thevar, $ataction) = each ($global_values)) {
		if ($ataction != $action) {
		    global $$thevar;
		    printf ("<input type=\"hidden\" name=\"$thevar\" value=\"%s\">", $$thevar);
		}
	    }

	    print "<input type=\"submit\" value=\"Next >>\">";
	}
 ?></td></tr></table>
<?php
    }

    //
    // Intro()
    //
    // This will show a small introduction about our script.
    //
    function
    Intro() {
	InitPage();
 ?>Greetings!<p>

This utility will aid you in migrating an existing ForuMAX 4.1 forum to ForuMAX 5.0. It will refuse to migrate any older version. Therefore, if you want to migrate a ForuMAX which is older than 4.1, please upgrade that ForuMAX first.<p>
<?php
	BackNextButton (1);

	DonePage();
    }

    //
    // GetInfo()
    //
    // This will request forum information.
    //
    function
    GetInfo() {
	InitPage();

 ?><form action="migrate_fm4.php" method="post">
<input type="hidden" name="action" value="analyze">
Please tell me where your ForuMAX CGI-BIN directory is. This must be the absolute path and <b>not</b> the URL!<p>
<table width="100%">
 <tr>
  <td width="25%">ForuMAX CGI-BIN Directory</td>
  <td width="75%"><input type="text" name="cgidir"></td>
 </tr>
</table><p>
<?php

	BackNextButton (0);
	DonePage();
    }

    //
    // ReadConfig ()
    //
    // This will read the ForuMAX configuration file and set some global
    // variables. It will return a blank string on success or an error
    // description on failure.
    //
    function
    ReadConfig () {
	global $cgidir, $extrafields;

	// try to read the forum options file
	$tmp = file ($cgidir . "/forum_options.pl");
	if (!isset ($tmp)) {
	    // this failed. complain
	    return "Unable to open <b>forum_options.pl</b>";
	    DonePage();
	    exit;
	}

	// turn this array into a string
	$config = "";
	while (list (, $line) = each ($tmp)) {
	    $config .= $line;
	}

	// get the forum data directory
	if (!preg_match ("/\\\$FORUM_DIR=qq\~(.+)\~;\n/", $config, $tmp)) {
	    // this failed. return an error
	    return "forum_options.pl is corrupted, no \$FORUM_DIR=qq~(...)~; line found";
	}
	$GLOBALS["forumdir"] = $tmp[1];

	// now, build the custom fields array
	if (!preg_match ("/\\\$EXTRA_PROFILE_FIELDS=qq\~(.+)\~;\n/", $config, $tmp)) { return "forum_options.pl is corrupted, no \$EXTRA_PROFILE_FIELDS=qq~(...)~; line found"; }
	$extra_profile_fields = explode ("|", $tmp[1]);
	if (!preg_match ("/\\\$EXTRA_PROFILE_TYPES=qq\~(.+)\~;\n/", $config, $tmp)) { return "forum_options.pl is corrupted, no \$EXTRA_PROFILE_TYPES=qq~(...)~; line found"; }
	$extra_profile_types = explode ("|", $tmp[1]);
	if (!preg_match ("/\\\$EXTRA_PROFILE_HIDDEN=qq\~(.+)\~;\n/", $config, $tmp)) { return "forum_options.pl is corrupted, no \$EXTRA_PROFILE_HIDDEN=qq~(...)~; line found"; }
	$extra_profile_hidden = explode ("|", $tmp[1]);
	if (!preg_match ("/\\\$EXTRA_PROFILE_PERMS=qq\~(.+)\~;\n/", $config, $tmp)) { return "forum_options.pl is corrupted, no \$EXTRA_PROFILE_PERMS=qq~(...)~; line found"; }
	$extra_profile_perms = explode ("|", $tmp[1]);

	// construct the custom fields array
	$i = 0;
	while (list (, $name) = each ($extra_profile_fields)) {
	    // add it to the array
	    $extrafields[$i] = $name . "|" . $extra_profile_types[$i] . "|" . $extra_profile_hidden[$i] . "|" . $extra_profile_perms[$i];
	    $i++;
	}

	// grab the library file
	$tmp = file ($cgidir . "/forum_lib.pl");
	if (!isset ($tmp)) {
	    // this failed. complain
	    return "Unable to open <b>forum_lib.pl</b>";
	}

	// turn this array into a string
	$lib = "";
	while (list (, $line) = each ($tmp)) {
	    $lib .= $line;
	}

	// get the forum version number
	if (!preg_match ("/\\\$FORUM_VERSION=\"(.+)\";\n/", $lib, $tmp)) {
	    // this failed. return an error
	    return "forum_lib.pl is corrupted, no \$FORUM_VERSION=\"(...)\" line found";
	}
	$GLOBALS["forumversion"] = $tmp[1];

	// grab the user database file
	$tmp = file ($cgidir . "/user_db.pl");
	if (!isset ($tmp)) {
	    // this failed. complain
	    return "Unable to open <b>user_db.pl</b>";
	}

	// turn this array into a string
	$lib = "";
	while (list (, $line) = each ($tmp)) {
	    $lib .= $line;
	}

	// get the forum version number
	if (!preg_match ("/\\\$USERDB_DESC=\"(.+)\";\n/", $lib, $tmp)) {
	    // this failed. return an error
	    return "user_db.pl is corrupted, no \$USERDB_DESC=\"(...)\" line found";
	}
	$GLOBALS["userdb"] = $tmp[1];

	// do we have the MySQL  database module?
	if (preg_match ("/ForuMAX MySQL-driven user database/i", $GLOBALS["userdb"])) {
	    // yeah. grab that config, too
	    $tmp = file ($cgidir . "/userdb_conf_mysql.pl");
	    if (!isset ($tmp)) {
		// this failed. complain
		return "Unable to open <b>userdb_conf_mysql.pl</b>";
	    }

	    // turn this array into a string
	    $dbconfig = "";
	    while (list (, $line) = each ($tmp)) {
		$dbconfig .= $line;
	    }

	    // get the MySQL username
	    if (!preg_match ("/\\\$MYSQL_USERNAME=qq\~(.+)\~;\n/", $dbconfig, $tmp)) {
		// this failed. return an error
		return "userdb_conf_mysql is corrupted, no \$MYSQL_USERNAME=\~(...)\~; line found";
	    }
	    $GLOBALS["mysql_username"] = $tmp[1];

	    // password
	    if (!preg_match ("/\\\$MYSQL_PASSWORD=qq\~(.+)\~;\n/", $dbconfig, $tmp)) {
		// this failed. return an error
		return "userdb_conf_mysql is corrupted, no \$MYSQL_PASSWORD=\~(...)\~; line found";
	    }
	    $GLOBALS["mysql_password"] = $tmp[1];

	    // host name
	    if (!preg_match ("/\\\$MYSQL_HOST=qq\~(.+)\~;\n/", $dbconfig, $tmp)) {
		// this failed. return an error
		return "userdb_conf_mysql is corrupted, no \$MYSQL_HOST=\~(...)\~; line found";
	    }
	    $GLOBALS["mysql_host"] = $tmp[1];

	    // database name
	    if (!preg_match ("/\\\$MYSQL_DBNAME=qq\~(.+)\~;\n/", $dbconfig, $tmp)) {
		// this failed. return an error
		return "userdb_conf_mysql is corrupted, no \$MYSQL_DBNAME=\~(...)\~; line found";
	    }
	    $GLOBALS["mysql_dbname"] = $tmp[1];

	    // users table
	    if (!preg_match ("/\\\$MYSQL_USERTABLENAME=qq\~(.+)\~;\n/", $dbconfig, $tmp)) {
		// this failed. return an error
		return "userdb_conf_mysql is corrupted, no \$MYSQL_USERTABLENAME=\~(...)\~; line found";
	    }
	    $GLOBALS["mysql_user_table"] = $tmp[1];

	    // group table
	    if (!preg_match ("/\\\$MYSQL_GROUPTABLENAME=qq\~(.+)\~;\n/", $dbconfig, $tmp)) {
		// this failed. return an error
		return "userdb_conf_mysql is corrupted, no \$MYSQL_GROUPTABLENAME=\~(...)\~; line found";
	    }
	    $GLOBALS["mysql_group_table"] = $tmp[1];
	}

	return "";
    }
  
    //
    // Analyze()
    //
    // This will analyze the old forum's datafiles
    //
    function
    Analyze() {
	global $cgidir;

	InitPage();

	// show our progress
	print "<li>Analyzing configuration files... ";
	$i = ReadConfig ();
	if ($i != "") {
	    // this failed. complain
	    print "Failure: " . $i;
	    DonePage();
	    exit;
	}
	print " OK</li><p>";

	// inform the user
	printf ("You are currently running <b>ForuMAX %s</b><br>", $GLOBALS["forumversion"]);
	printf ("The internal ForuMAX data directory is <b><code>%s</code></b><br>", $GLOBALS["forumdir"]);
	printf ("Your current ForuMAX user database module is <b><code>%s</code></b><p>", $GLOBALS["userdb"]);

	// is this ForuMAX 4.1 or better?
	list ($ver, $subver) = explode (".", $GLOBALS["forumversion"]);
	$ok = 1;
	if ($ver < 4) { $ok = 0; };
	if (($ver == 4) and ($subver < 1)) { $ok = 0; };
	if ($ok == 0) {
	    // no. complain
 ?>We're sorry, but you need to use ForuMAX 4.1 or better to be able to upgrade to ForuMAX 5.0
<?php
	    DonePage();
	    exit;
	}

	// do we have a nice user database module?
	if (!preg_match ("/ForuMAX MySQL-driven user database/i", $GLOBALS["userdb"])) {
	    // no. complain
 ?>Sorry, but only the ForuMAX MySQL-driven user database is currently supported
<?php
	    DonePage();
	    exit;
	}

	// yay, this version of ForuMAX will do. inform the user
 ?>Congratulations -- this version of ForuMAX can be upgraded to ForuMAX 5.0!<p>
When you now click on <i>Next</i>, we'll start informing on severnal migration options<p>
<?php
	BackNextButton (1);

	DonePage();
    }

    //
    // BuildPermissions($i, $val)
    //
    // This will build a permissions dropbox according to value $val. $i will
    // be used as index.
    //
    function
    BuildPermissions ($i, $val) {
	print "<select name=\"perms[$i]\">";
	echo "<option value=\"0\""; if ($val == 0) { echo " selected"; };
	print ">Modifyable by user and admins</option>";
	echo "<option value=\"1\""; if ($val == 1) { echo " selected"; };
	print ">Modifyable by admins only</option></select>";
    }

    //
    // BuildType ($i, $type)
    //
    // This will build a type dropbox according to value $val. $i will be used
    // as index.
    //
    function
    BuildType ($i, $type) {
	// $TYPES[$no] = $desc are the type names and descriptions
	$TYPES[0] = "Text";
	$TYPES[1] = "URL";
	$TYPES[2] = "ICQ";
	$TYPES[3] = "AIM";
	$TYPES[4] = "Yahoo! ID";
	$TYPES[5] = "Gender";
	$TYPES[6] = "Homepage URL";
	$TYPES[7] = "Custom Status";
	$TYPES[8] = "Joining Date";

	// build the list
	print "<select name=\"type[$i]\">";

	// build the type
	while (list ($id, $desc) = each ($TYPES)) {
	    echo "<option value=\"$id\""; if ($id == $type) { echo " selected"; };
	    print ">$desc</option>";
	}

	// end the list
	print "</select>";
    }

    //
    // Options()
    //
    // This will prompt for migration options.
    //
    function
    Options() {
	global $cgidir, $extrafields;
	InitPage();

	// show our progress
	print "<li>Analyzing configuration files... ";
	$i = ReadConfig ();
	if ($i != "") {
	    // this failed. complain
	    print "Failure: " . $i;
	    DonePage();
	    exit;
	}
	print " OK</li><p>";

 ?><form action="migrate_fm4.php" method="post">
<input type="hidden" name="action" value="migrate">
<input type="hidden" name="cgidir" value="<?php echo $cgidir; ?>">
Please select your custom field options:<p>
<table width="100%" border=0>
 <tr>
  <td width="15%"><b>Migrate field</b></td>
  <td width="30%"><b>Name</b></td>
  <td width="30%"><b>Type</b></td>
  <td width="10%"><b>Hidden</b></td>
  <td width="15%"><b>Permissions</b></td>
 </tr>
<?php
	// browse them all
	$i = 0;
	while (list (, $tmp) = @each ($extrafields)) {
	    list ($name, $type, $hidden, $perms) = explode ("|", $tmp);

	    // build the checkbox values for hidden
	    $hflag = "";
	    if ($hidden == "YES") { $hflag = "checked"; };

	    // build the entry
	    printf ("<tr><td align=\"center\"><input type=\"checkbox\" name=\"migrate[$i]\" checked></td><td><input type=\"text\" name=\"name[$i]\" value=\"%s\"></td><td>", $name);
	    BuildType ($i, $type);
;	    printf ("</td><td align=\"center\"><input type=\"checkbox\" name=\"hidden[$i]\" %s></td><td>", $hflag, $i);
            BuildPermissions ($i, $perms);
            echo "</td></tr>";

	    $i++;
	}
 ?></table><p>
Please select the forums you'd like to migrate:<p>
<select name="forum[]" multiple size=10>
<?php
    // grab the user database file
    $forums = file ($GLOBALS["forumdir"] . "/forumdata");
    if (!isset ($forums)) {
        // this failed. complain
        printf ("Unable to open <b>%s/forumdata</b>" . $GLOBALS["forumdir"]);
	DonePage();
	exit;
    }

    // list all forums
    while (list (, $line) = each ($forums)) {
	// grab only the forum names
	$tmp = explode (":", $line);
	printf ("<option value=\"%s\" selected>%s</option>", $tmp[0], $tmp[0]);
    }
 ?>
</select><p>
Please select what you'd like to migrate<p>
<input type="checkbox" name="mig_accounts" checked>Migrate accounts</input><br>
<input type="checkbox" name="mig_groups" checked>Migrate groups</input><br>
<input type="checkbox" name="mig_cats" checked>Migrate categories</input><p>
<b>NOTE</b> Pushing <i>Next</i> will <b>MIGRATE YOUR FORUM</b>. We take <b>NO</b> responsibility for failed migrations!<p>
<?php

	BackNextButton (0);

	DonePage();
    }

    //
    // FixupString ($string)
    //
    // This will convert all weird ForuMAX 4 |(blah)| tags to normal strings.
    //
    function
    FixupString ($string) {
	$string = str_replace ("|CLN|", ":", $string);
	$string = str_replace ("|AMP|", "&", $string);
	$string = str_replace ("|C|", "\n", $string);
	$string = str_replace ("|R|", "\r", $string);
	return $string;
    }

    //
    // GetMemberIDZero ($accountname)
    //
    // This will get the member ID of user $accountname, or zero if the account
    // doesn't exist.
    //
    function
    GetMemberIDZero ($accountname) {
	global $MEMBERID_CACHE;

	// do we already have this name?
	if ($MEMBERID_CACHE[$accountname] != "") {
	    // yes. return it
	    return $MEMBERID_CACHE[$accountname];
	}

	// get the member ID
	$tmp = GetMemberID ($accountname);

	// is this a blank string?
	if ($tmp == "") {
	    // yes. return zero instead
	    $MEMBERID_CACHE[$accountname] = 0;
	    return 0;
	}

	// no. return the member id
	$MEMBERID_CACHE[$accountname] = $tmp;
	return $tmp;
    }

    //
    // GetForumIDZero ($forumname)
    //
    // This will get the forum ID of forum $forumname, or zero if the forum
    // doesn't exist.
    //
    function
    GetForumIDZero ($forumname) {
	global $FORUMID_CACHE;

	// do we have this name cached?
	if ($FORUMID_CACHE[$forumname] != "") {
	    // yes. return it
	    return $FORUMID_CACHE[$forumname];
	}

	// grab the forum
	$query = sprintf ("select id from forums where name='%s'", $forumname);
	$res = db_query ($query); $tmp = db_fetch_results ($res);

	// does such a entry exist?
	if (db_nof_results ($res) == 0) {
	    // no. return zero instead
	    $FORUMID_CACHE[$forumname] = 0;
	    return 0;
	}

	// yes. return the forum id
	$FORUMID_CACHE[$forumname] = $tmp[0];
	return $tmp[0];
    }

    //
    // FixupDate ($date, $time)
    //
    // This will fixup the ForuMAX 4 dates $date and $time to a format SQL
    // understands.
    //
    function
    FixupDate ($date, $time) {
	$temptime = preg_replace ("/\|/", " ", $date . ":" . $time);
	$datetime = explode (" ", $temptime);

	// build the date
	$tmp = explode ("-", $datetime[0]);
	$date = $tmp[2] . "-" . $tmp[0] . "-" . $tmp[1];

	// build the time
	$hourminute = explode (":", $datetime[1]);

	// if we are PM, add 12 hours
	if ($datetime[2][0] == "P") {
	    $hourminute[0] += 12;
	}

	// return the new, fixed-up date
	return $date . " " . $hourminute[0] . ":" . $hourminute[1];
    }

    //
    // Migrate()
    //
    // This will actually migrate the forum. Aiieee!
    //
    function
    Migrate() {
	global $migrate, $name, $hidden, $type, $perms, $forum;	
	global $mig_accounts, $mig_groups, $mig_cats;

	// $type_map[] is the mapping from the ForuMAX 4 -> ForuMAX 5 types
	$type_map[0] = 1;		// text
	$type_map[1] = 2;		// url
	$type_map[2] = 9;		// icq
	$type_map[3] = 3;		// aim
	$type_map[4] = 4;		// yahoo! id
	$type_map[5] = 5;		// gender
	$type_map[6] = 6;		// homepage url
	$type_map[7] = 7;		// custom status

	// make sure out script can run up to 12 hours
	set_time_limit (12 * 3600);

	InitPage();

	// print the big warning
 ?><center><b>WARNING We are now MIGRATING your ForuMAX 4 forum to ForuMAX 5. DO NOT INTERRUPT THIS PROCESS!</b></center><p>
<?php
	// show our progress
	print "<li>Analyzing configuration files... ";
	$i = ReadConfig ();
	if ($i != "") {
	    // this failed. complain
	    print "Failure: " . $i;
	    DonePage();
	    exit;
	}
	print " OK</li><p>";

	// get all current custom fields
	$query = sprintf ("select id from customfields");
	$res = db_query ($query);

	// build a list for them
	$allfields = "";
	while ($tmp = db_fetch_results ($res)) {
	    // add the entry
	    $allfields[$tmp[0]] = "!";
	}

	// build the custom fields
	$joindate = "";
	while (list ($i, $yesno) = @each ($migrate)) {
	    // need to migrate this field?
	    if ($yesno != "") {
		// yes. is this a joining date field?
		if ($type[$i] == 8) {
		    // yes. this one is special!
		    $joinfield = $i;
		} else {
		    // is this a gender field?
		    if ($type[$i] == 5) {
			// yes. store it
			$genderfields[$i] = "!";
		    }
	
		    // does this field already exists?
		    $query = sprintf ("select id from customfields where name='%s'", addslashes ($name[$i]));
		    $res = db_query ($query); $tmp = db_fetch_results ($res);

		    // got a match?
		    if (db_nof_results ($res) == 0) {
		        // no. build the visibility flag
			$vis = 1;
			if ($hidden[$i] != "") { $vis = 0; };

			// create the field
		        $query = sprintf ("insert into customfields values (NULL,'%s',%s,%s,%s)", addslashes ($name[$i]), $type_map[$type[$i]], $vis, $perms[$i]);
			db_query ($query);

		        // build the field mapping
		        $field_map[$i] = db_get_insert_id();

			// now, add the cell to the accounts database as well
			$query = sprintf ("alter table accounts add extra%s varchar(128)", $field_map[$i]);
			db_query ($query);
		    } else {
		        // yay, we've got the field. get rid of the entry in
			// cur_field
	    	        $allfields[$tmp[0]] = "";
			$field_map[$i] = $tmp[0];
		    }
		}
	    }
	}

	// do we use the MySQL database module?
	if (preg_match ("/ForuMAX MySQL-driven user database/i", $GLOBALS["userdb"])) {
	    // yeah. set the flag
	    $GLOBALS["accounts_mysql"] = 1;
	}

	// okay, need to upgrade MySQL accounts?
	if ($GLOBALS["accounts_mysql"] == 1) {
	    // yeah. open the database link
	    print "<li>Opening MySQL database link... </li>";
	    $acct_dbh = mysql_connect ($GLOBALS["mysql_host"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"]);
	    if (!$acct_dbh) {
		// this failed. complain
		print "Failure, can't connect";
		DonePage();
		exit;
	    }

	    // select the database
	    if (!mysql_select_db ($GLOBALS["mysql_dbname"], $acct_dbh)) {
		// this failed. complain
		print "Failure, can't select database";
		DonePage();
		exit;
	    }

	    // start grabbing the accounts
	    $query = sprintf ("select * from %s", $GLOBALS["mysql_user_table"]);
	    $account_res = mysql_query ($query, $acct_dbh);

	    print " OK</li><p>";
	} else {
	    // ... TODO ...
	}

	// migrate the accounts!
	print "<li>Migrating accounts";

	// need to migrate the accounts?
	if ($mig_accounts != "") {
	    // yes. do it
	    $skipped = 0; $added = 0;
	    do {
	        // do we use MySQL accounts?
	        if ($GLOBALS["accounts_mysql"] == 1) {
		    // yes. grab one account
		    $data = mysql_fetch_row ($account_res);

		    // did this query work?
		    if (!$data) {
		        // no. get out of here
		        break;
		    } else {
		        // yes. construct the account line
		        $account_line = implode (":", $data);
	  	    }
	        } else {
		    // ...TODO...
		    break;
	        }

	        // split this line
	        list ($username, $password, $account_flags, $nofposts, $fullname, $email, $sig, $customfields, $parent) = explode (":", $account_line);
	        $custom = explode ("|^|", $customfields);

	        // do we have a join date?
	        if ($joinfield != "") {
		    // yes. copy it to the join date
		    list ($month, $day, $year) = explode ("-", $custom[$joinfield]);
		    $joindate = $year . "-" . $month . "-" . $day;

		    // was this a real string?
		    if ($joindate == "") {
		        // no. use NULL
		        $joindate = "NULL";
		    }
	        }

	        // build the custom field stuff
	        @reset ($field_map); $customfields = "";
	        while (list ($id, $dbid) = @each ($field_map)) {
		    $customfield[$dbid] = "'" . addslashes (FixupString ($custom[$id])) . "'";

		    // is this a gender field?
		    if ($genderfields[$id] != "") {
		        // yes. set up the gender
		        $val = "Unspecified";
		        if ($custom[$id] == "m") { $val = "Male"; };
		        if ($custom[$id] == "f") { $val = "Female"; };
		        $customfield[$dbid] = "'" . addslashes ($val) . "'";
		    }
	        }
	        @reset ($allfields);
	        while (list ($id, $data) = @each ($allfields)) {
		    // got an actual value here?
		    if ($data != "") {
		        // yeah. clear the field
		        $customfield[$id] = "''";
		   }
	        }

	        // construct the custom query string
  	        $customquery = @implode (",", $customfield);
	        if (isset ($customquery)) { $customquery = "," . $customquery; };

	        // get the parental email and password
	        list ($parentemail, $parentpass) = explode ("|^|", FixupString ($parent));

	        // build the account flags
	        $flags = 0;
	        if (preg_match ("/A/", $account_flags)) { $flags |= FLAG_ADMIN; };
	        if (preg_match ("/D/", $account_flags)) { $flags |= FLAG_DISABLED; };
	        if (preg_match ("/M/", $account_flags)) { $flags |= FLAG_MMOD; };

	        // do we already have such an user?
	        $query = sprintf ("select id from accounts where accountname='%s'", FixupString (addslashes ($username)));
	        if (db_nof_results (db_query ($query)) == 0) {
	            // no. add the user
	            $query = sprintf ("insert into accounts values (NULL,'%s','%s',%s,%s,'%s','%s','%s','%s','%s',NULL,0,0,0,0%s)", addslashes (FixupString ($username)), addslashes (FixupString ($password)), $flags, $nofposts, addslashes (FixupString ($email)), addslashes ($parentemail), addslashes ($parentpass), addslashes (FixupString ($sig)), addslashes ($joindate), $customquery);
		    db_query ($query);

		    // user successfully added
		    $added += 1;
	        } else {
		    // yes. skip the user
		    $skipped += 1;
	        }
	    } while (1);
	    printf ("... DONE, %s added and %s skipped</li><p>", $added, $skipped);
	} else {
	    printf ("... SKIPPED<p>");
	}

	// migrate the groups
	print "<li>Migrate groups...";

	// need to migrate the groups?
	if ($mig_accounts != "") {
	    // yes. do we use the MySQL database?
	    if ($GLOBALS["accounts_mysql"] == 1) {
	        // yes. start grabbing the groups
	        $query = sprintf ("select * from %s.%s", $GLOBALS["mysql_dbname"], $GLOBALS["mysql_group_table"]);
	        $group_res = mysql_query ($query, $acct_dbh) or die (mysql_error());
	    }

	    do {
	        // do we use MySQL accounts?
	        if ($GLOBALS["accounts_mysql"] == 1) {
		    // yes. grab one account
		    $data = mysql_fetch_row ($group_res);

		    // did this query work?
		    if (!$data) {
		        // no. get out of here
		        break;
		    } else {
		        // yes. construct the group line
		        $group_line = implode (":", $data);
		    }
	        } else {
		    // ...TODO...
		    break;
	        }

	        // split this line
	        list ($groupname, $groupid, $groupdesc, $groupmembers) = explode (":", $group_line);

	        // do we already have such a group?
	        $query = sprintf ("select id from groups where name='%s'", $groupname);
	        if (db_nof_results (db_query ($query)) == 0) {
		   // no. add the group
		    $query = sprintf ("insert into groups values (NULL,'%s','%s')", addslashes (FixupString ($groupname)), addslashes (FixupString ($groupdesc)));
		    db_query ($query); $groupid = db_get_insert_id();

		    // insert all members
		    $members = explode (",", $groupmembers);
		    while (list (, $name) = @each ($members)) {
		        // get the user name
		        $memberid = GetMemberID (addslashes ($name));

		        // got a result?
		        if ($memberid != "") {
			    // yes. add the user to the group
			    $query = sprintf ("insert into groupmembers values (NULL,%s,%s)", $groupid, $memberid);
			    db_query ($query);
		        }
		    }
	        }
	    } while (1);
	    print " DONE</li><p>";
	} else {
	    print " SKIPPED</li><p>";
	}

	// migrate the categories
	print "<li>Migrating categories...";

	if ($mig_cats != "") {
	    // open the categories datafile
	    $cat_fd = @fopen ($GLOBALS["forumdir"] . "/cats", "r");
	    if ($cat_fd) {
	        // yay, we've got the file. add all categories
	        do {
	            // get a line
		    $line = fgets ($cat_fd, 4096); $line = chop ($line);

		    // split the line
		    list ($catname, $catno, $catmods, $desc) = explode (":", $line);

		    // is this line blank?
		    if ($line != "") {
		        // no. do we already have this category?
			$catname = FixupString ($catname);
		        $query = sprintf ("select id from categories where name='%s'", addslashes ($catname));
		        $res = db_query ($query); $tmp = db_fetch_results ($res);
		        $catid = $tmp[0];
		        if (db_nof_results ($res) == 0) {
		            // no. create it
		            $query = sprintf ("insert into categories values (NULL,'%s',0)", addslashes ($catname));
		            db_query ($query); $catid = db_get_insert_id();

		            // insert the category moderators
		            $mod = explode (",", $catmods);

		            // add all these mods
		            while (list (, $name) = @each ($mod)) {
			        // does this entry begin with a @?
			        if (preg_match ("/^\@/", $name)) {
			            // yes. get rid of the @ and get the group id
			            $name = preg_replace ("/^\@/", "", $name);
			            $objectid = GetGroupID (addslashes ($name));
			            $flags = FLAG_USERLIST_GROUP;
			        } else {
			            // no. grab the user id
			            $objectid = GetMemberID (addslashes ($name)); $flags = 0;
			        }

			        // do we have a real object id?
			        if ($objectid != "") {
			            // yes. add the moderator
			            $query = sprintf ("insert into catmods values (NULL,%s,%s,%s)", $catid, $objectid, $flags);
			            db_query ($query);
			        }
			    }
		        }

		        // build the category ID map
		        $catmap[$catno] = $catid;
		    }
	        } while (!feof ($cat_fd));
	        print " DONE</li><p>";
	    } else {
	        // this failed. complain
	        printf ("Failed, unable to open <b>%s</b></li><p>", $GLOBALS["forumdir"] . "/cats");
	    }
	} else {
	    print " SKIPPED</li><p>";
	}

	// migrate the forums!
	print "<li>Migrating forums</li><ul>";

	// read the forum datafile
	$forums = file ($GLOBALS["forumdir"] . "/forumdata");
	if (!isset ($forums)) {
	    // this failed. complain
	    printf ("Unable to open <b>%s/forumdata</b>" . $GLOBALS["forumdir"]);
	    DonePage();
	    exit;
	}

	// build the array
	while (list (, $line) = each ($forums)) {
	    list ($name) = explode (":", $line);
	    $forumdata[$name] = $line;
	}

	while (list (, $name) = @each ($forum)) {
	    // we need to migrate this forum
	    $name = stripslashes (FixupString ($name));
	    printf ("<li>Creating forum <b>%s</b>...", $name);

	    // open the forum topic datafile
	    $thread_fd = @fopen ($GLOBALS["forumdir"] . "/" . $name . ".forum", "r");

	    if ($thread_fd) {
		// this worked. get the datafile record
		list (, $nofreplies, $modlist, $restricted, $d1, $d2, $forum_flags, $desc, $catno) = explode (":", $forumdata[$name]);

		// do we already have such a forum?
		$query = sprintf ("select id from forums where name='%s'", addslashes ($name));
		$res = db_query ($query); $tmp = db_fetch_results ($res);
		$FORUMID_CACHE[$name] = $tmp[0];

		if (db_nof_results ($res) == 0) {
		    // no. build the flags
		    $flags = 0;
		    if (preg_match ("/H/", $forum_flags)) { $flags |= FLAG_FORUM_ALLOWHTML; };
		    if (preg_match ("/M/", $forum_flags)) { $flags |= FLAG_FORUM_ALLOWMAX; };
		    if (preg_match ("/i/", $forum_flags)) { $flags |= FLAG_FORUM_NOIMAGES; };
		    if (preg_match ("/D/", $forum_flags)) { $flags |= FLAG_FORUM_DISABLED; };
		    if (preg_match ("/h/", $forum_flags)) { $flags |= FLAG_FORUM_HIDDEN; };
		    if (preg_match ("/s/", $forum_flags)) { $flags |= FLAG_FORUM_DENYEVILHTML; };

		    // get the category number
		    $catid = $catmap[$catno];
		    if ($catid == "") { $catid = 0; };

		    // create the forum
		    $query = sprintf ("insert into forums values (NULL,'%s',%s,'%s',0,0,now(),0,%s,0,'')", addslashes ($name), $flags, addslashes (FixupString ($desc)), $catid);
		    db_query ($query); $forumid = db_get_insert_id();
		    $FORUMID_CACHE[$name] = $forumid;

		    // now, grab the name of all mods
		    $mod = explode (",", $modlist);

		    // add all these mods
		    while (list (, $name) = @each ($mod)) {
			// does this entry begin with a @?
			if (preg_match ("/^\@/", $name)) {
			    // yes. get rid of the @ and get the group id
			    $name = preg_replace ("/^\@/", "", $name);
			    $objectid = GetGroupID (addslashes ($name));
			    $flags = FLAG_USERLIST_GROUP;
			} else {
			    // no. grab the user id
			    $objectid = GetMemberID (addslashes ($name)); $flags = 0;
			}

			// do we have a real object id?
			if ($objectid != "") {
			    // yes. add the moderator
			    $query = sprintf ("insert into mods values (NULL,%s,%s,%s)", $forumid, $objectid, $flags);
			    db_query ($query);
			}
		    }

		    // now, grab the name of all mods
		    $rest = explode (",", $restricted);

		    // add all these restricted objects
		    while (list (, $name) = @each ($rest)) {
			// does this entry begin with a @?
			if (preg_match ("/^\@/", $name)) {
			    // yes. get rid of the @ and get the group id
			    $name = preg_replace ("/^\@/", "", $name);
			    $objectid = GetGroupID (addslashes ($name));
			    $flags = FLAG_USERLIST_GROUP;
			} else {
			    // no. grab the user id
			    $objectid = GetMemberID (addslashes ($name)); $flags = 0;
			}

			// do we have a real object id?
			if ($objectid != "") {
			    // yes. add the moderator
			    $query = sprintf ("insert into restricted values (NULL,%s,%s,%s)", $forumid, $objectid, $flags);
			    db_query ($query);
			}
		    }
		}
		printf (" done</li>");
	    } else {
		printf (" skipped, couldn't open %s</li>", $GLOBALS["forumdir"] . "/" . $name . ".forum");
	    }
	}

	// second pass (we have to do this in two steps because the forum name
	// would be unknown)
	@reset ($forum);
	while (list (, $name) = @each ($forum)) {
	    // we need to migrate this forum
	    $name = stripslashes (FixupString ($name));
	    printf ("<li>Migrating forum <b>%s</b>...", $name);

	    // open the forum topic datafile
	    $thread_fd = @fopen ($GLOBALS["forumdir"] . "/" . $name . ".forum", "r");
	    if ($thread_fd) {
		// this worked. grab the forum id
		$forumid = $FORUMID_CACHE[$name];

		// got any matches?
		if ($forumid != "") {
		    // yeah. handle all threads
		    while (!feof ($thread_fd)) {
			// grab the line
			$line = fgets ($thread_fd, 4096);
			$line = chop ($line);

			// is this not a blank line?
			if ($line != "") {
			    // yes. split the line
			    list ($fileid, $subject, $nofposts, $date1, $date2, $author, $icon, $newforum, $forum_flags, $lastposter, $locker) = explode (":", $line);
			    // try to open the topic file
			    $topic_fd = @fopen ($GLOBALS["forumdir"] . "/" . $name . "/" . $fileid, "r");
			    if ($topic_fd) {
		  	        // yay, this worked. figure out the values to set
			        $authorid = GetMemberIDZero (addslashes ($author));
			        $destforum = GetForumIDZero (addslashes ($newforum));
			        $lastauthorid = GetMemberIDZero (addslashes ($lastauthor));
			        $lockerid = GetMemberIDZero (addslashes ($locker));

			        // build the flags
			        $thread_flags = 0;
			        if (preg_match ("/L/", $forum_flags)) { $thread_flags |= FLAG_THREAD_LOCKED; };

			        // create the topic
			        $query = sprintf ("insert into threads values (NULL,%s,'%s','%s',0,'%s',%s,%s,%s,%s,%s,0)", $forumid, addslashes ($subject), addslashes ($icon), FixupDate ($date1, $date2), $authorid, $thread_flags, $lastauthorid, $lockerid, $destforum);
			        db_query ($query);

			        // get the topic ID
			        $threadid = db_get_insert_id ();

				// now, handle all messages
			        $first = 0;
			        while (!feof ($topic_fd)) {
			   	    // grab the line
				    $line = fgets ($topic_fd, 4096);
				    $line = chop ($line);

				    // does this line begin with a .: pair?
				    if (preg_match ("/^\.\:/", $line)) {
				        // yes. need to insert a previous one?
				        if ($first != 0) {
					    // yeah. do it
					    $post = chop ($post);
			    	            $query = sprintf ("insert into posts values (NULL,%s,%s,%s,'%s','%s','',0,1,'%s',0)", $authorid, $forumid, $threadid, FixupDate ($d1, $d2), addslashes ($post), $ipaddr);
					    db_query ($query);
				        }

				        // it's a special data line
				        list ($dot, $author, $d1, $d2, $icon, $ipaddr) = explode (":", $line);
				        // not an IP specified?
				        if ($ipaddr == "") {
					    // no. probably not the first line
					    list ($dot, $author, $d1, $d2, $ipaddr) = explode (":", $line);
				        }

				        // get the author id
				        $authorid = GetMemberIDZero (addslashes ($author));

				        // not the first message anymore
				        $first++; $post = "";
				    } else {
				        // just append the line
				        $post .= $line . "\n";
				    }
			        }

			        // insert the final one
			        $post = chop ($post);
			        $query = sprintf ("insert into posts values (NULL,%s,%s,%s,'%s','%s','',0,1,'%s',0)", $authorid, $forumid, $threadid, FixupDate ($d1, $d2), addslashes ($post), $ipaddr);
			        db_query ($query);

			        // now, fix the post count
			        $query = sprintf ("update threads set lastposterid='%s',nofreplies=%s where id=%s", $authorid, $first - 1, $threadid);
			        db_query ($query);

			        fclose ($topic_fd);
		            } else {
			        printf ("Skipped thread <b>%s</b>, datafile %s couldn't be opened<br>", $subject, $GLOBALS["forumdir"] . "/" . $name . "/" . $threadid);
			    }
			}
		    }
		} else {
		    printf (" skipped, created forum <b>%s</b> doesn't exist? Looks like an internal error, contact support</li>", $name);
		}

		// select the very last forum poster and date
		$query = sprintf ("select lastposterid,lastdate from threads where forumid=%s order by lastdate desc limit 1", $forumid);
		$res = db_query ($query); $result = db_fetch_results ($res);
		if ($result[0] == "") { $result[0] = 0; };

		// select the number of threads
		$query = sprintf ("select count(id) from threads where forumid=%s",$forumid);
		$res = db_query ($query); $tmp = db_fetch_results ($res);
		$nofthreads = $tmp[0];

		// select the number of posts
		$query = sprintf ("select count(id) from posts where forumid=%s",$forumid);
		$res = db_query ($query); $tmp = db_fetch_results ($res);
		$nofposts = $tmp[0];

		// set the last poster
		$query = sprintf ("update forums set lastpost='%s',lastposterid=%s,nofthreads=%s,nofposts=%s where id=%s", $result[1],$result[0],$nofthreads,$nofposts,$forumid);
		db_query ($query);

		fclose ($thread_fd);

	        printf (" done</li>");
	    } else {
		// forum skipped
		printf (" skipped, couldn't open %s</li>", $GLOBALS["forumdir"] . "/" . $name . ".forum");
	    }

	    printf ("</li>");
	}
	echo " DONE</ul>";

	DonePage();
    }

    // is there an action supplied?
    if ($action == "") {
	// no. default to the intro
	$action = "intro";
    }

    // need to show the intro?
    if ($action == "intro") {
	// yes. do it
	Intro();
	exit;
    }

    // need to get the information?
    if ($action == "getinfo") {
	// yes. do it
	GetInfo();
	exit;
    }

    // need to analyze the old forum?
    if ($action == "analyze") {
	// yes. do it
	Analyze();
	exit;
    }

    // need to collect migration info?
    if ($action == "options") {
	// yes. do it
	Options();
	exit;
    }

    // need to migrate?
    if ($action == "migrate") {
	// yes. do it
	Migrate();
	exit;
    }
 ?>
