<?php 
    //
    // accounts.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the editing of accounts.
    //

    // we need our library, too
    require "lib.php";

    // $account_flag[$flag] = $description will list all account flags. They
    // will be used when editing 
    $account_flag[FLAG_ADMIN] = "Administrator";
    $account_flag[FLAG_MMOD] = "Mega Moderator";
    $account_flag[FLAG_DISABLED] = "Disabled";
    $account_flag[FLAG_DENYPOST] = "Deny Posting";
    $account_flag[FLAG_COPPA] = "Account is below 13";
    $account_flag[FLAG_DONTCENSOR] = "Don't censor for this account";
    $account_flag[FLAG_AUTOSIG] = "Sig checkbox auto on";

    //
    // Overview()
    //
    // This will take care of the forum account management.
    //
    function
    Overview() {
	// build the page
	cpShowHeader("Account Maintenance", "Overview");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="search">
<input type="hidden" name="page" value="1">
You can search the members database for any type of member.<p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
 <tr class="tab2">
  <td colspan="2"><b>How do you want to search?</b></td>
 </tr>
 <tr class="tab2">
  <td width="5%" align="center"><input type="radio" name="type" value="or" checked></input></td>
  <td width="95%">&nbsp;Search for members according to this criteria, having <b><u>one or more of these values</u></b> (match one or more)</td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="radio" name="type" value="and"></input></td>
  <td>&nbsp;Search for members according to this criteria, having <b><u>all of these options</u></b> (exact match)</td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="radio" name="type" value="all"></input></td>
  <td>&nbsp;List all members</td>
 </tr>
 <tr class="tab2">
  <td colspan="2">&nbsp;</td>
 </tr>
 <tr class="tab2">
  <td colspan="2">&nbsp;<b>For which conditions would you like to check?</b></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="uname"></input></td>
  <td>Username must contain or be equal to <input type="text" name="theuname"></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="email"></input></td>
  <td>Email address must contain or be equal to</input> <input type="text" name="themail"></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="admin"></input></td>
  <td>User must be an <b>Administrator</b></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="mmod"></input></td>
  <td>User must be a <b>Mega Moderator</b></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="disabled"></input></td>
  <td>User must be <b>Disabled</b></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="denypost"></input></td>
  <td>User must be <b>Denied Posting</b></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="coppa"></input></td>
  <td>User must have the <b>Under 13</b> flag</td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="checkposts"></input></td>
  <td>User must have <select name="countype"><option value="le">Less than</option><option value="eq">Exactly</option><option value="ge">More than</option></select> <input type="text" name="nofposts"> posts.</td>
 </tr>
 <tr class="tab2">
  <td colspan="2">&nbsp;</td>
 </tr>
 <tr class="tab2">
  <td colspan="2">&nbsp;<b>Results per page</b></td>
 </tr>
 <tr class="tab2">
  <td>&nbsp;</td>
  <td>&nbsp;List <select name="accountsperpage"><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select> accounts per page.</td>
 </tr>
</table><p>
<table width="100%">
 <tr valign="top">
  <td align="center" width="50%"><input type="submit" value="Search!"></form><form></form></td>
  <td align="center" width="50%"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="add"><input type="submit" value="Add Account"></form></td>
 </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // Search()
    //
    // This will actually search for the accounts.
    //
    function
    Search() {
	global $account_flag, $PHP_SELF;

	// build the query
	$query = sprintf ("SELECT id,accountname,flags FROM accounts");

	// grab all values
	$type = $_REQUEST["type"]; $uname = $_REQUEST["uname"];
	$theuname = $_REQUEST["theuname"]; $email = $_REQUEST["email"];
	$themail = $_REQUEST["themail"]; $admin = $_REQUEST["admin"];
	$mmod = $_REQUEST["mmod"]; $disabled = $_REQUEST["disabled"];
	$denypost = $_REQUEST["denypost"]; $coppa = $_REQUEST["coppa"];
	$checkposts = $_REQUEST["checkposts"]; $nofposts = $_REQUEST["nofposts"];
	$countype = $_REQUEST["countype"];
	$page = preg_replace ("/\D/", "", $_REQUEST["page"]);
	$accountsperpage = preg_replace ("/\D/", "", $_REQUEST["accountsperpage"]) + 0;

	// do we need to scan for a critiria?
	if ($type != "all") {
	    // yes. build the query
	    $query_ext .= " WHERE ";
	    if ($uname != "") { $uname_op = "!="; } else { $uname_op = "=="; };
	    if ($email != "") { $email_op = "!="; } else { $email_op = "=="; };
	    if ($admin != "") { $admin_op = "!="; } else { $admin_op = "=="; };
	    if ($mmod != "") { $mmod_op = "!="; } else { $mmod_op = "=="; };
	    if ($disabled != "") { $disabled_op = "!="; } else { $disabled_op = "=="; };
	    if ($denypost != "") { $denypost_op = "!="; } else { $denypost_op = "=="; };
	    if ($coppa != "") { $coppa_op = "!="; } else { $coppa_op = "=="; };

	    $set = 0;
	    if ($uname != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= "((accountname REGEXP '$theuname') $uname_op 0)"; }
	    if ($email != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= "((email REGEXP '$themail') $email_op 0)"; }
	    if ($admin != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= "((flags & " . FLAG_ADMIN . ") $admin_op 0)"; }
	    if ($mmod != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_MMOD . ") $mmod_op 0)"; }
	    if ($disabled != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_DISABLED . ") $disabled_op 0)"; }
	    if ($denypost != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_DENYPOST . ") $denypost_op 0)"; }
	    if ($coppa != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_COPPA . ") $coppa_op 0)"; }

	    // do we need to check post counts too?
	    if ($checkposts != "") {
		// yup. append it to the query
	 	if ($set != 0) { $query_ext .= " $type"; }
		if ($countype == "le") { $count_op = "<"; };
		if ($countype == "ge") { $count_op = ">"; };
		if ($countype == "eq") { $count_op = "="; };
		$query_ext .= " nofposts $count_op $nofposts";
		$set = 1;
	    }
	}

	// if we have something useful to add, add it
	if ($query_ext == " WHERE ") { $query_ext = ""; };
	$query .= $query_ext;

	// limit the result
	$query .= " LIMIT " . ($page - 1) * $accountsperpage . "," . $accountsperpage;

	// build the page
	cpShowHeader("Accounts Maintaince", "Search results");

	// first, grab the overall count
	$tmpquery = "SELECT COUNT(accountname) FROM accounts" . $query_ext;
	list ($nofresults) = db_fetch_results (db_query ($tmpquery));

	// now, do the real query
	$res = db_query ($query);

	// figure the range out
	$from = ($page - 1) * $accountsperpage + 1;
	$to = $page * $accountsperpage;
	if ($to > $nofresults) { $to = $nofresults; };

	// be verbose	
	print "A total of <b>$nofresults</b> account";
	if ($nofresults != 1) { print "s"; }
	print " matched your query, listing accounts <b>$from</b> to <b>$to</b><p>";

	// calculate the number of pages
	$nofpages = floor ($nofresults / $accountsperpage);
	if (($nofpages * $accountsperpage) != $nofresults) { $nofpages++; };

	// show the results
 ?><table width="100%" cellspacing="2" cellpadding="3" border="0" class="tab1">
<tr>
  <td colspan=2><small><?php
	// now, create the page [] thingies.
	print "<small>Page ";

	for ($i = 1; $i <= $nofpages; $i++) {
	    if ($i == $page) {
		print "[<b>$i</b>] ";
	    } else {
		printf ("[<a class=\"sml\" href=\"%s?action=search&page=$i&type=$type&theuname=$theuname&countype=$countype&nofposts=$nofposts&accountsperpage=$accountsperpage\">$i</a>] ", $_SERVER["PHP_SELF"]);
	    }
	}
 ?></small></small></tr>
  </tr>
  <tr>
    <td width="60%" class="tab3"><b>Username</b></td>
    <td width="40%" class="tab3"><b>Flags</b></td>
  </tr>
<?php
	// add all accounts
	while (list (, $flags) = $result = db_fetch_results ($res)) {
	    // construct the flags
	    reset ($account_flag); $tmp = "";
	    while (list ($the_flag, $the_desc) = each ($account_flag)) {
		// is this flag set?
		if ($flags & $the_flag) {
		    // yes. add it to the list
		    $tmp[] = $the_desc;
		}
	    }

	    // show the line
	    $temp = "None";
	    if (is_array ($tmp)) { $temp = implode (", ", $tmp); }
	    printf ("<tr class=\"tab2\"><td class=\"tn\"><a href=\"$PHP_SELF?action=edit&accountid=%s\">%s</a></td><td class=\"tn\">%s</td></tr>", $result[0], $result[1], $temp);
	}
	print "</table>";

	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will show the page for editing account $accountname.
    //
    function
    Edit() {
	global $PHP_SELF, $account_flag;

	// get the account id
	$accountid = preg_replace ("/\D/", "", $_REQUEST["accountid"]);

	// build the query
	$query = sprintf ("SELECT accountname,password,flags,nofposts,email,parent_email,parent_password,sig FROM accounts WHERE id='%s'", $accountid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	list ($accountname, $password, $flags, $nofposts, $email, $parent_email, $parent_password, $sig) = db_fetch_results (db_query ($query));

	// show the header
	cpShowHeader("Account Maintenance", "Edit account <u>" . $accountname . "</u>");

	// are we a Master?
	if ($GLOBALS["MASTER_ACCESS"] == 0) {
	    // no. don't show the passwords
	    $password = ""; $parent_password = "";
	}

	// list the details
 ?><form action="<?php echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
 <tr class="tab3">
  <td colspan=2 align="center" class="text">You are editing account <?php echo $result[0]; ?></td>
 </tr>
 <tr class="tab2">
  <td width="20%"><b>Account name</b></td>
  <td width="80%"><input type="text" name="the_accountname" size=50 value="<?php echo htmlspecialchars ($accountname); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Password</b></td>
  <td><input type="text" name="the_password" size=50 value="<?php echo htmlspecialchars ($password); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Email address</b></td>
  <td><input type="text" name="the_email" size=50 value="<?php echo htmlspecialchars ($email); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Number of posts</b></td>
  <td><input type="text" name="the_nofposts" size=50 value="<?php echo htmlspecialchars ($nofposts); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Parental email address</b></td>
  <td><input type="text" name="the_parent_email" size=50 value="<?php echo htmlspecialchars ($parent_email); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Parental password</b></td>
  <td><input type="text" name="the_parent_password" size=50 value="<?php echo htmlspecialchars ($parent_password); ?>"></td>
 </tr>
 <tr class="tab2" valign="top">
  <td><b>Signature</b></td>
  <td><textarea name="the_sig" rows=5 cols=38><?php echo htmlspecialchars ($sig); ?></textarea></td>
 </tr>
<?php
	// now, do all custom fields
	$query = sprintf ("SELECT id,name FROM customfields");
	$res = db_query ($query);
	while (list ($customid, $customname) = db_fetch_results ($res)) {
	    // grab the custom field contents
	    $query = sprintf ("SELECT extra%s FROM accounts WHERE id='%s'", $customid, $accountid);
	    list ($value) = db_fetch_results (db_query ($query));

	    // add the custom field
	    printf ("<tr class=\"tab2\"><td><b>%s</b></td><td><input type=\"text\" name=\"field[%s]\" size=50 value=\"%s\"></td></tr>", $customname, $customid, $value);
	}
	print "<tr class=\"tab2\" valign=\"top\"><td><b>Options</b></td><td>";

	// add checkboxes for all flags we know
	while (list ($the_flag, $the_desc) = each ($account_flag)) {
	    print "<input type=\"checkbox\" name=\"f_flag[$the_flag]\"";
	    if (($flags & $the_flag) != 0) { print " checked"; }
	    print ">$the_desc</input><br>";
	}

 ?></td></tr></table><p>
<table width="100%">
 <tr>
  <td width="50%" align="center"><input type="submit" value="Submit Modifications"></form></td>
  <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="submit" value="Cancel Changes"></form></td>
 </tr>
</table><?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually edit a specific account.
    //
    function
    DoEdit() {
	global $account_flag;

	// show the header
	cpShowHeader("Account Maintenance", "Edit account");

	// fetch the account id and all other values
	$accountid = preg_replace ("/\D/", "", $_REQUEST["accountid"]);
	$the_accountname = $_REQUEST["the_accountname"];
	$the_password = $_REQUEST["the_password"];
	$the_email = $_REQUEST["the_email"];
	$the_nofposts = preg_replace ("/\D/", "", $_REQUEST["the_nofposts"]);
	$the_parent_email = $_REQUEST["the_parent_email"];
	$the_parent_password = $_REQUEST["the_parent_password"];
	$the_sig = $_REQUEST["the_sig"];

	// first of all, grab the original flags
	$query = sprintf ("SELECT flags FROM accounts WHERE id='%s'", $accountid);
	$flags = db_fetch_results (db_query ($query));

	// add checkboxes for all flags we know
	while (list ($the_flag, $the_desc) = each ($account_flag)) {
	    // is it set?
	    if ($_REQUEST["f_flag"][$the_flag] != "") {
		// yes, it is. activate the flag
		$flags |= $the_flag;
	    } else {
		// no. get rid of the flag
	        $flags &= (~$the_flag);
	    }
	}

	// build the new custom stuff
	$query = sprintf ("SELECT id FROM customfields");
	$res = db_query ($query); $newcustom = "";
	while (list ($customid) = db_fetch_results ($res)) {
	    // construct the new custom fields
	    $newcustom .= "extra" . $customid . "='" . $_REQUEST["field[" . $customid . "]"] . "',";
	}

	// need to change the password?
	$extra = "";
	if ($the_password != "") {
	    // yes. do it
	    $extra = ",password='" . $the_password . "'";
	}
	if ($the_parent_password != "") {
	    // yes. do it
	    $extra .= ",parent_password='" . $the_parent_password . "'";
	}

	// build the query
	$query = sprintf ("UPDATE accounts SET %saccountname='%s',email='%s',nofposts='%s',parent_email='%s',sig='%s',flags=%s%s WHERE id='%s'",$newcustom,$the_accountname,$the_email,$the_nofposts,$the_parent_email,$the_sig,$flags,$extra,$accountid);
	db_query ($query);

	// show the 'yay' page
 ?>
Account <b><?php echo stripslashes ($the_accountname); ?></b> has successfully been updated.<p>
<form action="<?php echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="action" value="overview">
<input type="submit" value="Back to account management">
</form>
<?php
	cpShowFooter();
    }

    //
    // Add()
    //
    // This will show the page to add an account.
    //
    function
    Add() {
        global $account_flag;

	// build the page
        cpShowHeader("Account Maintenance", "Add account");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doadd">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
 <tr class="tab2">
  <td width="20%">Account name</td>
  <td width="80%"><input type="text" name="the_accountname"></td>
 </tr>
 <tr class="tab2">
  <td>Password</td>
  <td><input type="text" name="the_password"></td>
 </tr>
 <tr class="tab2">
  <td>Email address</td>
  <td><input type="text" name="the_email"></td>
  </tr>
 <tr class="tab2">
  <td>Number of posts</td>
  <td><input type="text" name="the_nofposts"></td>
 </tr>
 <tr class="tab2">
  <td>Parental email address</td>
  <td><input type="text" name="the_parent_email"></td>
 </tr>
 <tr class="tab2">
  <td>Parental password</td>
  <td><input type="text" name="the_parent_password"></td>
 </tr>
 <tr class="tab2">
  <td valign="top">Signature</td>
  <td><textarea name="the_sig" rows=5 cols=38><?php echo htmlspecialchars ($the_sig); ?></textarea></td>
 </tr>
<?php
	// now, do all custom fields
	$query = sprintf ("SELECT id,name FROM customfields");
	$res = db_query ($query);
	while (list ($customid, $customname) = db_fetch_results ($res)) {
	    // add the custom field
	    printf ("<tr class=\"tab2\"><td>%s</td><td><input type=\"text\" name=\"field[%s]\"></td></tr>", $customname, $customid);
	}
	print "<tr class=\"tab2\" valign=\"top\"><td><b>Options</b></td><td>";

	// add checkboxes for all flags we know
	while (list ($the_flag, $the_desc) = each ($account_flag)) {
	    print "<input type=\"checkbox\" name=\"f_flag_$the_flag\"";
	    print ">$the_desc</input><br>";
	}
 ?></td></tr>
</table><p>
<center><input type="submit" value="Add the account"></center>
</form>
<?php
        cpShowFooter();
    }

    //
    // DoAdd()
    //
    // This will actually add a new account.
    //
    function
    DoAdd() {
	global $account_flag;

	// show the header
        cpShowHeader("Account Maintenance", "Add account");

	// fetch the information
	$the_accountname = $_REQUEST["the_accountname"];
	$the_password = $_REQUEST["the_password"];
	$the_email = $_REQUEST["the_email"];
	$the_nofposts = preg_replace ("/\D/", "", $_REQUEST["the_nofposts"]);
	$the_parent_email = $_REQUEST["the_parent_email"];
	$the_parent_password = $_REQUEST["the_parent_password"];
	$the_sig = $_REQUEST["the_sig"];

	// does this account already exist?
	$query = sprintf ("SELECT id FROM accounts WHERE accountname='%s'", $the_accountname);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. complain
	    print "This account already exists.";
	    cpShowFooter();
	    exit;
	}

	// build the new custom stuff
	$query = sprintf ("SELECT id FROM customfields ORDER BY ID ASC");
	$res = db_query ($query); $newcustom = "";
	while (list ($customid) = db_fetch_results ($res)) {
	    // construct the new custom fields
	    $newcustom .= ",'" . $_REQUEST["field[" . $customid . "]"] . "'";
	}

	// add checkboxes for all flags we know
	while (list ($the_flag, $the_desc) = each ($account_flag)) {
	    // is it set?
	    $tmp = "f_flag_" . $the_flag;
	    $tmp = $_REQUEST[$tmp];
	    if ($tmp != "") {
		// yes, it is. activate the flag
		$flags |= $the_flag;
	    } else {
		// no. get rid of the flag
	        $flags &= (~$the_flag);
	    }
	}

	// update the information
        $query = sprintf ("INSERT INTO accounts VALUES (NULL,'%s','%s','%s',0,'%s','%s','%s','%s',now(),NULL,0,0,'',0,0,0,'',0,''%s)", $the_accountname, $the_password, $flags, $the_email, $the_parent_email, $the_parent_password, $the_sig, $newcustom);
        db_query ($query);

	// yay, this worked. inform the user
 ?>Account <b><?php echo stripslashes ($the_accountname); ?></b> successfully added.<p>

<form action="<?php echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="action" value="overview">
<input type="submit" value="Back to Account Maintenance">
</form><?php
        cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_ACCOUNTS);

    // grab the action
    $action = trim ($_REQUEST["action"]);

    // need to go to the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. do it
	Overview();
    } elseif ($action == "search") {
	// search for the accounts
	Search();
    } elseif ($action == "edit") {
	// edit an account
	Edit();
    } elseif ($action == "doedit") {
	// actually edit the account
	DoEdit();
    } elseif ($action == "add") {
	// add the account
	Add();
    } elseif ($action == "doadd") {
	// actually add the account
	DoAdd();
    }
 ?>
