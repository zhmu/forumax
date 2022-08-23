<?php
    // we need our nice library
    require "cp_lib.php";

    // FORUMS_PER_PAGE will indicate how much forums we will list per page
    define (FORUMS_PER_PAGE, 20);

    // GROUPS_PER_PAGE will indicate how much groups we will list per page
    define (GROUPS_PER_PAGE, 20);

    // $account_flag[$flag] = $description will list all account flags. They
    // will be used when editing 
    $account_flag[FLAG_ADMIN] = "Administrator";
    $account_flag[FLAG_MMOD] = "Mega Moderator";
    $account_flag[FLAG_DISABLED] = "Disabled";
    $account_flag[FLAG_DENYPOST] = "Deny Posting";

    // $forum_flag[$flag] = $description will list all forum flags. They will
    // be used when editing.
    $forum_flag[FLAG_FORUM_ALLOWHTML] = "Allow HTML code in this forum";
    $forum_flag[FLAG_FORUM_ALLOWMAX] = "Allow MaX code in this forum";
    $forum_flag[FLAG_FORUM_DENYEVILHTML] = "Block Javascript code and bad HTML tags";
    $forum_flag[FLAG_FORUM_NOIMAGES] = "Block images";

    // $exfield_type[] are the extra field types we know of
    $exfield_type[0] = "(delete this field)";
    $exfield_type[1] = "Text";
    $exfield_type[2] = "URL";
    $exfield_type[3] = "AIM";
    $exfield_type[4] = "Yahoo! ID";
    $exfield_type[5] = "Gender";
    $exfield_type[6] = "Homepage URL";
    $exfield_type[7] = "Custom Status";
    $exfield_type[8] = "MSN";
    $exfield_type[9] = "ICQ";
    $exfield_type[10] = "Country";

    // $exfield_perm[] are the extra field permissions
    $exfield_perm[0] = "User and admins can modify";
    $exfield_perm[1] = "Only admins can modify";

    //
    // Intro()
    //
    // This will show the intro page.
    //
    function
    Intro() {
	CPShowHeader();
 ?>
Welcome to the ForuMAX Control Panel for administrators, <b><?php echo $GLOBALS["username"] ?></b>! This control panel will aid you in completely configuring your forum. Whether it are accounts, forums, skins or anything you want to modify, everything can be done using this utility.<p>
There also is a slightly less powerful version available, which is designed for moderators. It will give moderators the ability to post announcements into their forum, prune posts and the likes. As an administrator, you will also have access to perform these tasks.<p>
You can select what you would like to modify by clicking on the appropriate link in the cell on your left.
<?php
	CPShowFooter();
    }

    //
    // Accounts()
    //
    // This will take care of the forum account management.
    //
    function
    Accounts() {
	CPShowHeader();
 ?>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="searchaccounts">
<input type="hidden" name="page" value="1">
You can search the members database for any type of member.<p>
<b>How do you want to search?</b><br>
<input type="radio" name="type" value="or" checked>Search for members according to this criteria, having <b><u>one or more of these values</u></b> (match one or more)</input><br>
<input type="radio" name="type" value="and">Search for members according to this criteria, having <b><u>all of these options</u></b> (exact match)</input><br>
<input type="radio" name="type" value="all">List all members</input>
<p>
<b>For which conditions would you like to check?</b><br>
<input type="checkbox" name="uname">Username must contain or be equal to</input> <input type="text" name="theuname"><br>
<input type="checkbox" name="email">Email address must contain or be equal to</input> <input type="text" name="themail"><br>
<input type="checkbox" name="admin">User must be an <b>Administrator</b></input><br>
<input type="checkbox" name="mmod">User must be a <b>Mega Moderator</b></input><br>
<input type="checkbox" name="disabled">User must be a <b>Disabled</b></input><br>
<input type="checkbox" name="denypost">User must be a <b>Denied Posting</b></input><br>
<input type="checkbox" name="checkposts">User must have <select name="countype"><option value="le">Less than</option><option value="eq">Exactly</option><option value="ge">More than</option></select> <input type="text" name="nofposts"> posts.</input><p>
List <select name="accountsperpage"><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select> accounts per page.<p>
<input type="submit" value="Search!">
</form>
<?php
	CPShowFooter();
    }

    //
    // SearchAccounts()
    //
    // This will actually search for the accounts.
    //
    function
    SearchAccounts() {
	global $type, $admin, $mmod, $smod, $mod, $countype, $checkposts;
	global $uname, $theuname, $nofposts, $page, $accountsperpage;
	global $account_flag, $email, $themail, $disabled, $denypost;

	// build the query
	$query = sprintf ("select id,accountname,flags from accounts");

	// do we need to scan for a critiria?
	if ($type != "all") {
	    // yes. build the query
	    $query_ext .= " where ";
	    if ($uname != "") { $uname_op = "!="; } else { $uname_op = "=="; };
	    if ($email != "") { $email_op = "!="; } else { $email_op = "=="; };
	    if ($admin != "") { $admin_op = "!="; } else { $admin_op = "=="; };
	    if ($mmod != "") { $mmod_op = "!="; } else { $mmod_op = "=="; };
	    if ($disabled != "") { $disabled_op = "!="; } else { $disabled_op = "=="; };
	    if ($denypost != "") { $denypost_op = "!="; } else { $denypost_op = "=="; };
	    $set = 0;
	    if ($uname != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= "((accountname rlike '$theuname') $uname_op 0)"; }
	    if ($email != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= "((email rlike '$themail') $email_op 0)"; }
	    if ($admin != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= "((flags & " . FLAG_ADMIN . ") $admin_op 0)"; }
	    if ($mmod != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_MMOD . ") $mmod_op 0)"; }
	    if ($disabled != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_DISABLED . ") $disabled_op 0)"; }
	    if ($denypost != "") { if ($set != 0) { $query_ext .= " $type "; }; $set = 1; $query_ext .= " ((flags & " . FLAG_DENYPOST . ") $denypost_op 0)"; }

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
	if ($query_ext == " where ") { $query_ext = ""; };
	$query .= $query_ext;

	$query .= " limit " . ($page - 1) * $accountsperpage . ",$accountsperpage";

	CPShowHeader();

	// first, grab the overall count
	$tmpquery = "select count(accountname) from accounts" . $query_ext;

	// grab the total number of accounts
	$tmpres = db_query ($tmpquery); $tmp = db_fetch_results ($tmpres);
	$nofresults = $tmp[0];

	// now, do the real query
	$res = db_query ($query);

	$from = ($page - 1) * $accountsperpage + 1;
	$to = $page * $accountsperpage;
	if ($to > $nofresults) { $to = $nofresults; };
	
	print "A total of <b>$nofresults</b> account";
	if ($nofresults != 1) { print "s"; }
	print " matched your query, listing accounts <b>$from</b> to <b>$to</b><p>";

	// calculate the number of pages
	$nofpages = floor ($nofresults / $accountsperpage);
	if (($nofpages * $accountsperpage) != $nofresults) { $nofpages++; };

	print "<font size=2>Page: ";
	for ($i = 1; $i <= $nofpages; $i++) {
	    if ($i == $page) {
		print "[<b>$i</b>] ";
	    } else {
		print "[<a href=\"cp_admin.php?action=searchaccounts&page=$i&type=$type&theuname=$theuname&countype=$countype&nofposts=$nofposts&accountsperpage=$accountsperpage\">$i</a>] ";
	    }
	}
	print "</font>";
 ?><p>
<table width="100%">
  <tr>
    <td width="60%"><b>Username</b></td>
    <td width="40%"><b>Flags</b></td>
  </tr>
<?php
	// add all tables
	while ($result = db_fetch_results ($res)) {
	    // construct the flags
	    $flags = $result[2]; $tmp = "";
	    reset ($account_flag);
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
	    printf ("<tr><td><a href=\"cp_admin.php?action=editaccount&accountid=%s\">%s</a></td><td>%s</td></tr>", $result[0], $result[1], $temp);
	}

	print "</table>";

	CPShowFooter();
    }

    //
    // EditAccount()
    //
    // This will show the page for editing account $accountname.
    //
    function
    EditAccount() {
	global $accountid, $account_flag;

	// build the query
	$query = sprintf ("select accountname,password,flags,nofposts,email,parent_email,parent_password,sig from accounts where id=%s", $accountid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$flags = $result[2];

	CPShowHeader();

	// did we have any results?
	if (db_nof_results ($res) == 0) {
	    // no. die
	    die ("Account " . $accountid . " doesn't exist");
	}
	
	print "<form action=\"cp_admin.php\" method=\"post\">";
	print "<input type=\"hidden\" name=\"action\" value=\"doeditaccount\">";
	print "<input type=\"hidden\" name=\"accountid\" value=\"$accountid\">";

	print "You are editing account <b>" . $result[0] . "</b><p>";

	print "<table width=\"100%\">";
	printf ("<tr><td width=\"20%%\">Account name</td><td width=\"80%%\"><input type=\"text\" name=\"the_accountname\" value=\"%s\"></td></tr>", $result[0]);

	// are we a Master?
	if (($GLOBALS["flags"] & FLAG_MASTER) == 0) {
	    // no. don't show the password
	    $result[1] = "";
	}

	printf ("<tr><td>Password</td><td><input type=\"text\" name=\"the_password\" value=\"%s\"></td></tr>", $result[1]);
	printf ("<tr><td>Email address</td><td><input type=\"text\" name=\"the_email\" value=\"%s\"></td></tr>", $result[4]);
	printf ("<tr><td>Number of posts</td><td><input type=\"text\" name=\"the_nofposts\" value=\"%s\"></td></tr>", $result[3]);
	printf ("<tr><td>Parental email address</td><td><input type=\"text\" name=\"the_parent_email\" value=\"%s\"></td></tr>", $result[5]);
	printf ("<tr><td>Parental password</td><td><input type=\"text\" name=\"the_parent_password\" value=\"%s\"></td></tr>", $result[6]);
	printf ("<tr><td valign=\"top\">Signature</td><td><textarea name=\"the_sig\" rows=5 cols=30 name=\"the_sig\">%s</textarea></td></tr>", htmlspecialchars ($result[7]));

	// now, do all custom fields
	$query = sprintf ("select id,name from customfields");
	$res = db_query ($query);
	while ($result = db_fetch_results ($res)) {
	    // grab the custom field contents
	    $query = sprintf ("select extra%s from accounts where id=%s",$result[0],$accountid);
	    $res2 = db_query ($query); $tmp = db_fetch_results ($res2);

	    // add the custom field
	    printf ("<tr><td>%s</td><td><input type=\"text\" name=\"field[%s]\" value=\"%s\"></td></tr>", $result[1], $result[0], $tmp[0]);
	}
	print "</table><p>";

	// add checkboxes for all flags we know
	while (list ($the_flag, $the_desc) = each ($account_flag)) {
	    print "<input type=\"checkbox\" name=\"f_flag_$the_flag\"";
	    if (($flags & $the_flag) != 0) { print " checked"; }
	    print ">$the_desc</input><br>";
	}

	print "<p><input type=\"submit\" value=\"OK\">";
	print "</form>";

	CPShowFooter();
    }

    //
    // DoEditAccount()
    //
    // This will actually edit a specific account.
    //
    function
    DoEditAccount() {
	global $accountid, $the_accountname, $the_password, $the_email;
	global $the_nofposts, $the_parent_email, $the_parent_password, $the_sig;
	global $account_flag, $field;
	
	CPShowHeader();

	// first of all, grab the original flags
	$query = sprintf ("select flags from accounts where id=%s", $accountid);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$flags = $tmp[0];

	// did this yield any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    print "This account doesn't appear to exist. Perhaps another administrator deleted the account before you could edit it?";
	    CPShowFooter();
	    exit;
	}

	// add checkboxes for all flags we know
	while (list ($the_flag, $the_desc) = each ($account_flag)) {
	    // is it set?
	    $tmp = "f_flag_" . $the_flag;
	    global $$tmp;
	    if ($$tmp != "") {
		// yes, it is. activate the flag
		$flags |= $the_flag;
	    } else {
		// no. get rid of the flag
	        $flags &= (~$the_flag);
	    }
	}

	// build the new custom stuff
	$query = sprintf ("select id,name from customfields");
	$res = db_query ($query); $newcustom = "";
	while ($result = db_fetch_results ($res)) {
	    // construct the new custom fields
	    $newcustom .= "extra" . $result[0] . "='" . $field[$result[0]] . "',";
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
	$query = sprintf ("update accounts set %saccountname='%s',email='%s',nofposts='%s',parent_email='%s',sig='%s',flags=%s%s where id=%s",$newcustom,$the_accountname,$the_email,$the_nofposts,$the_parent_email,$the_sig,$flags,$extra,$accountid);
	db_query ($query);

	// show the 'yay' page
 ?>
Account <b><?php echo $the_accountname; ?></b> has successfully been updated.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="accounts">
<input type="submit" value="Back to account management">
</form>
<?php

	CPShowFooter();
    }

    //
    // Skins()
    //
    // This will take care the skin selection.
    //
    function
    Skins() {
	CPShowHeader();

	// build the query
	$query = sprintf ("select name,description,flags,id from skins");
	$res = db_query ($query);

	// build the table
 ?><table width="100%" cellspacing=0 cellpadding=0>
<tr>
  <td width="100%" colspan=2><b>Skin name</b></td>
</tr>
<?php
	// add all skins
	while ($result = db_fetch_results ($res)) {
	    $extra = ""; $inuse = "";
	    if (($result[2] and FLAG_SKIN_DEFAULT) != 0) {
		// this the default skin. modify the table attribute
		$extra = " bgcolor=\"#000000\"";
		$setdef = "(<b>default skin</b>)";
	    } else {
	        $extra = "";
		$setdef = "(<a href=\"cp_admin.php?action=setskindefault&id=" . $result[3] . "\">set as default</a>)";
	    }

	    printf ("<tr$extra><td width=\"85%%\"><a href=\"cp_admin.php?action=editskin&id=%s\">%s</a></td><td width=\"15%%\" align=\"right\">$setdef</td></tr>", $result[3], $result[0]);
	}

	print "</table>";
?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="importskin">
<input type="submit" value="Import skin">
</form>
<?php
	CPShowFooter();
    }
    
    //
    // EditSkin()
    //
    // This will show the page for skin editing.
    //
    function
    EditSkin() {
	global $id;
	
	CPShowHeader();

	// grab the name of the skin
	$query = sprintf ("select name from skins where id=%s", $id);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$skinname = $tmp[0];

	// grab all skin information for the database
	$query = sprintf ("select name,content from skin_%s order by name", $id);
	$res = db_query ($query); $nofmatches = db_nof_results ($res);

	// show how much hits we have
	print "This skin, <b>$skinname</b>, has <b>$nofmatches</b> template";
	if ($nofmatches != 1) { echo "s"; };
	print "<p><ul>";
	
	// show them all
	while ($result = mysql_fetch_row ($res)) {
	    printf ("<li><a href=\"cp_admin.php?action=editskintemplate&id=%s&template=%s\">%s</a></li>", $id, rawurlencode ($result[0]), $result[0]);
	}

	print "</ul><p>";
 ?><table width="100%">
 <tr>
  <td width="25%" align="center">
    <form action="cp_admin.php" method="post">
    <input type="hidden" name="action" value="addskintemplate">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" value="Add template">
    </form>
  </td>
  <td width="25%" align="center">
    <form action="cp_admin.php" method="post">
    <input type="hidden" name="action" value="editskinvars">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" value="Edit skin variables">
    </form>
  </td>
  <td width="25%" align="center">
    <form action="cp_admin.php" method="post">
    <input type="hidden" name="action" value="deleteskin">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" value="Delete skin">
    </form>
  </td>
  <td width="25%" align="center">
    <form action="cp_admin.php" method="post">
    <input type="hidden" name="action" value="exportskin">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" value="Export skin">
    </form>
  </td>
 </tr>
</table>
<?php
	CPShowFooter();
    }

    //
    // EditSkinTemplate()
    //
    // This will edit a skin template for us.
    //
    function
    EditSkinTemplate() {
	global $id, $template;

	CPShowHeader();

	// grab the contents from the database
	$query = sprintf ("select content,title,refresh_url from skin_%s where name='%s'", $id, $template);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// build the layout
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doeditskintemplate">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="template" value="<?php echo $template; ?>">
<table width="100%">
<tr>
  <td width="20%">Template name</td>
  <td width="80%"><input type="text" name="the_template" value="<?php echo $template; ?>"></td>
</tr>
<tr>
  <td>Page Title</td>
  <td><input type="text" name="the_title" value="<?php echo $result[1]; ?>"></td>
</tr>
<tr>
  <td>Refresh URL</td>
  <td><input type="text" name="the_refreshurl" value="<?php echo $result[2]; ?>"></td>
</tr>
<tr>
  <td valign="top">Content</td>
  <td><textarea name="the_content" rows=15 cols=50><?php echo htmlspecialchars ($result[0]); ?></textarea></td>
</tr></table><p>
<input type="checkbox" name="delete">Check this to delete this template</input><br>
<input type="checkbox" name="revert">Check this to revert this template to the default</input><p>
<input type="submit" value="OK">
</form>
<?php
	print "</table>";

	CPShowFooter();
    }

    //
    // DoEditSkinTemplate()
    //
    // This will actually modify the skin template.
    //
    function
    DoEditSkinTemplate() {
	global $id, $template, $the_template, $the_content, $delete;
	global $the_title, $the_refreshurl, $revert;

	CPShowHeader();

	// need to delete?	
	if ($delete == "") {
	    // no. need to revert it?
	    if ($revert == "") {
		// no. just modify it
	        $query = sprintf ("update skin_%s set name='%s',content='%s',title='%s',refresh_url='%s' where name='%s'", $id, $the_template, $the_content, $the_title, $the_refreshurl, $template);
	        db_query ($query);

		// show the 'yay' page
		print "Thank you, the template has successfully been updated.<p>";
	    } else {
		// yes. read the default skin file
		$fp = fopen ("defaultskin.php", "r");
		$defaultskin = "";
		while (!feof ($fp)) {
		    $defaultskin .= fread ($fp, 65535);
		}

		// evaluate the default skin
		eval ($defaultskin);

		// okay, look up the new values
		$content = $SKIN[$template];
		$title = $SKINTITLE[$template];
		$refresh = $SKINREFRESH[$template];

		$name = addslashes ($template);
		$content = addslashes ($SKIN[$template]);
		$refresh = addslashes ($SKINREFRESH[$template]);
		$title = addslashes ($SKINTITLE[$template]);
		$content = str_replace ("[[DOUBLEBaCKSLASH]]", "\\\\\\\\\\\\\\", $content);

		// update the skin
	        $query = sprintf ("update skin_%s set content='%s',title='%s',refresh_url='%s' where name='%s'", $id, $content, $title, $refresh, $template);
	        db_query ($query);

		// show the 'yay' page
		print "Thank you, the template has successfully been reverted.<p>";
	    }
	} else {
	    // yes. delete it
	    $query = sprintf ("delete from skin_%s where name='%s'", $id, $template);
	    db_query ($query);

	    // show the 'yay' page
 	    print "Thank you, the template has successfully been deleted.<p>";
	}
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="editskin">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="submit" value="Return to skin editing">
</form>
<?php

	CPShowFooter();
    }

    //
    // AddSkinTemplate()
    //
    // This will add the skin template.
    //
    function
    AddSkinTemplate() {
	global $id;

	CPShowHeader();

 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doaddskintemplate">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%">
<tr>
  <td width="20%">Template name</td>
  <td width="80%"><input type="text" name="the_template"></td>
</tr>
<tr>
  <td>Page Title</td>
  <td><input type="text" name="the_title" value="<?php echo $result[1]; ?>"></td>
</tr>
<tr>
  <td>Refresh URL</td>
  <td><input type="text" name="the_refreshurl" value="<?php echo $result[2]; ?>"></td>
</tr>
<tr>
  <td valign="top">Content</td>
  <td><textarea name="the_content" rows=15 cols=50></textarea></td>
</tr></table><p>
<input type="submit" value="Add template">
</form>
<?php
	print "</table>";

	CPShowFooter();
    }

    //
    // DoAddSkinTemplate()
    //
    // This will actually add a skin template.
    //
    function
    DoAddSkinTemplate() {
	global $id, $the_template, $the_content, $the_refreshurl;
	global $the_title;

	CPShowHeader();

	// build the query
	$query = sprintf ("insert into skin_%s values ('%s','%s','%s','%s')", $id, $the_template, $the_content, $the_title, $the_refreshurl);
	db_query ($query);

	// show the 'yay' page
	print "Thank you, the template has successfully added.<p>";
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="editskin">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="submit" value="Return to skin editing">
</form>
<?php

	CPShowFooter();
    }

    //
    // QuoteString ($str)
    //
    // This will quote ", ' and $ in $str.
    //
    function
    QuoteString ($str) {
	$str = str_replace ("$", "\\$", $str);
	$str = str_replace ("\"", "\\\"", $str);
	$str = str_replace ("\'", "\\\'", $str);
	return $str;
    }

    //
    // ExportSkin()
    //
    // This will export the skin.
    //
    function
    ExportSkin() {
	global $id;

	CPShowHeader();

	// build the query
	$query = sprintf ("select name,content,title,refresh_url from skin_%s", $id);
	$res = db_query ($query);

	// build the page
 ?>The textbox below contains the exported skin. This text can be imported by another ForuMAX user and be applied to their forum. You are free to exchange skins as you please, but take into account that removing any copyright notices is <u>not</u> allowed!<p><textarea rows=10 cols=50>
<?php
	// add all fields
	$export = "";
	while ($result = db_fetch_results ($res)) {
	    // add it to the skin
	    $result[1] = str_replace ("/\r/", "", $result[1]);
	    $result[1] = str_replace ("/\n/", "\\n", $result[1]);
	    $result[1] = str_replace ("\\\\", "[[DOUBLEBaCKSLASH]]", $result[1]);
	    $export .= "\$SKIN[\"" . QuoteString ($result[0]) . "\"]=\"" . htmlspecialchars (QuoteString ($result[1])) . "\";\n";
	    $export .= "\$SKINTITLE[\"" . QuoteString ($result[0]) . "\"]=\"" . QuoteString (htmlspecialchars ($result[2])) . "\";\n";
	    $export .= "\$SKINREFRESH[\"" . QuoteString ($result[0]) . "\"]=\"" . QuoteString (htmlspecialchars ($result[3])) . "\";\n";
	}

	// add the skin variables
	$query = sprintf ("select name,content from skinvars_%s", $id);
	$res = db_query ($query);
	// add all fields
	while ($result = db_fetch_results ($res)) {
	    // add it to the skin
	    $result[1] = addslashes ($result[1]);
	    $result[1] = preg_replace ("/\r/", "", $result[1]);
	    $result[1] = preg_replace ("/\n/", "\\n", $result[1]);

	    $export .= "\$SKINVAR[\"" . htmlspecialchars ($result[0]) . "\"]=\"" . htmlspecialchars ($result[1]) . "\";\n";
	}

	// add the exported skin
	print $export;

	print "</textarea><p>";

 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="editskin">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="submit" value="Return to skin editing">
</form>
<?php
	CPShowFooter();
    }

    //
    // ImportSkin()
    //
    // This will show the page for importing skins
    //
    function
    ImportSkin() {
	CPShowHeader();
 ?>Please insert a name for the skin in the <i>Skin Name</i> text box. The exported skin data has to be within the <i>Exported Information</i> text area.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doimportskin">
<table width="100%">
 <tr>
  <td width="20%">Skin name</td>
  <td width="80%"><input type="text" name="skinname"></td>
 </tr>
 <tr>
  <td>Skin description</td>
  <td><input type="text" name="skindesc"></td>
 </tr>
 <tr>
  <td valign="top">Exported information</td>
  <td><textarea rows=20 cols=70 name="skinimport"></textarea></td>
 </tr>
</table><p>
<input type="submit" value="Import this skin">
</form>
<?php

	CPShowFooter();
    }

    //
    // DoImportSkin()
    //
    // This will actually import the forum skin.
    //
    function
    DoImportSkin() {
	global $skinname, $skindesc, $skinimport;

	CPShowHeader();

	// get rid of the slashes (for eval())
	$skinimport = stripslashes ($skinimport);

	// now, dump it nicely into variables
	eval ($skinimport);

	// add the skin to the list of available skins
	$query = sprintf ("insert into skins values (NULL,'%s','%s',0)", $skinname, skindesc);
	$res = db_query ($query);
	$id = db_get_insert_id ($res);

	// build the skin database
	$query = sprintf ("create table skin_%s (name varchar(64) not null primary key,content text not null,title varchar(64) not null,refresh_url varchar(64) not null)", $id);
	db_query ($query);

	$fd = fopen ("/tmp/sql.sql", "wt");

	// browse all templates
	$tmp = "";
	while (list ($name, $content) = each ($SKIN)) {
	    // feed them into the database
	    $name = addslashes ($name); $content = addslashes ($content);
	    $refresh = addslashes ($SKINREFRESH[$name]);
	    $title = addslashes ($SKINTITLE[$name]);
	    $content = str_replace ("[[DOUBLEBaCKSLASH]]", "\\\\\\\\\\\\\\", $content);
	    $query = sprintf ("insert into skin_%s values ('%s','%s','%s','%s')", $id, $name, $content, $title, $refresh);
	    fputs ($fd, $query . "\n");
	    db_query ($query);
	}

	fclose ($fd);

	// also add the variables
	$query = sprintf ("create table skinvars_%s (name varchar(64) not null primary key,content varchar(128) not null)", $id);
	db_query ($query);

	// browse all variables
	while (list ($name, $content) = each ($SKINVAR)) {
	    // feed them into the database
	    $query = sprintf ("insert into skinvars_%s values ('%s','%s')", $id, $name, $content);
	    db_query ($query);
	}

	// it worked. show the 'yay' message
 ?>The skin has successfully been added.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="skins">
<input type="submit" value="Return to skin overview">
</form>
<?php

	CPShowFooter();
    }

    //
    // DeleteSkin()
    //
    // This will delete a skin.
    //
    function
    DeleteSkin() {
	global $id;

	// build the query
	$query = sprintf("delete from skins where id=%s", $id);
	db_query ($query);

	// kill the skin table
	$query = sprintf ("drop table skin_%s", $id);
	db_query ($query);

	// kill the skin variable table
	$query = sprintf ("drop table skinvars_%s", $id);
	db_query ($query);


	// it worked. show the 'yay' page
	CPShowHeader();
 ?>The skin has successfully been deleted.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="skins">
<input type="submit" value="Return to skin overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // Options()
    //
    // This will show the forum options.
    //
    function
    Options() {
	global $CONFIG;

	CPShowHeader();

 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="do_options">
<table width="100%" border=1 cellspacing=1 cellpadding=1>
  <tr><td colspan=2 bgcolor="#000000" align="center"><font color="#ffff00"><b>General Options</b></font></td></tr>
  <tr><td width="50%"><b>Forums title</b><br>This will be the generic forum title. All pages will use this as a title, appening a sub-title after it (with a dash between them)</td><td width="50%"><input type="text" name="forum_title" value="<?php echo $CONFIG["forumtitle"]; ?>"></td></tr>
  <tr><td width="50%"><b>IP logging</b><br>The forum is capable of logging IP addresses. You may restrict viewing by anyone, moderators or admins, or completely turn logging off.</td><td width="50%"><input type="radio" name="ip_log" value="0"<?php if ($CONFIG["ip_log"] == 0) { echo " checked"; } ?>>Do not log IP's</input><br><input type="radio" name="ip_log" value="1"<?php if ($CONFIG["ip_log"] == 1) { echo " checked"; } ?>>Log IP's, viewable by admins</input><br><input type="radio" name="ip_log" value="2"<?php if ($CONFIG["ip_log"] == 2) { echo " checked"; } ?>>Log IP's, viewable by moderators and admins</input><br><input type="radio" name="ip_log" value="3"<?php if ($CONFIG["ip_log"] == 3) { echo " checked"; } ?>>Log IP's, viewable by anyone</input></td></tr>
  <tr><td width="50%"><b>Add notication when message has been edited</b><br>If this is enabled, the forum will add a <code>Edited at <i>timestamp</i> by <i>username</i></code> line whenever a message has been edited</td><td><input type="radio" name="notify_edit" value="0"<?php if ($CONFIG["notify_edit"] == 0) { echo " checked"; } ?>>Do not add timestamp when message has been edited</input><br><input type="radio" name="notify_edit" value="1"<?php if ($CONFIG["notify_edit"] == 1) { echo " checked"; } ?>>Add timestamp when message has been edited</input></td></tr>
  <tr><td width="50%"><b>Edit timestamp format</b><br>This is the timestamp format for the date that will be appeneded to an edited message. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="edit_timestamp_format" value="<?php echo $CONFIG["edit_timestamp_format"]; ?>"></td></tr>
  <tr><td width="50%"><b>Joindate timestamp format</b><br>This is the timestamp format for the date that will be appeneded to an edited message. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="joindate_timestamp_format" value="<?php echo $CONFIG["joindate_timestamp_format"]; ?>"></td></tr>
  <tr><td width="50%"><b>Announcement timestamp format</b><br>This is the timestamp format that will be used for all announcement timestamps. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="annc_timestamp_format" value="<?php echo $CONFIG["annc_timestamp_format"]; ?>"></td></tr>
  <tr><td width="50%"><b>Forum post timestamp format</b><br>This is the timestamp format that will be used for all post timestamps. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="post_timestamp_format" value="<?php echo $CONFIG["post_timestamp_format"]; ?>"></td></tr>
  <tr><td width="50%"><b>Allow registration</b><br>The forum is capable of allowing users to register an account on the fly. You can turn this option on or off.</td><td width="50%"><input type="radio" name="allow_register" value="0"<?php if ($CONFIG["allow_register"] == 0) { echo " checked"; } ?>>Do not allow registrations</input><br><input type="radio" name="allow_register" value="1"<?php if ($CONFIG["allow_register"] == 1) { echo " checked"; } ?>>Allow registrations</input></td></tr>
  <tr><td width="50%" valign="top"><b>Bulletin board rules</b><br>This is the policy all users have to agree with before an account will be granted</td><td width="50%"><textarea rows=10 cols=40 name="bb_rules"><?php echo htmlspecialchars ($CONFIG["rules"]); ?></textarea></td></tr>
  <tr><td width="50%" valign="top"><b>Censored Words</b><br>These are all the words which have been censored. They will be replaced by *'s (for example, dog becomes ***). Words between {} will only censor the word itself and not when it's surrounded with other charachters. Seperate each word by a space</td><td width="50%"><textarea rows=10 cols=40 name="censored_words"><?php echo htmlspecialchars ($CONFIG["censored_words"]); ?></textarea></td></tr>
  <tr><td width="50%"><b>Administrator email address</b><br>This email address will be used as the original of any forum email</td><td width="50%"><input type="text" name="admin_email" value="<?php echo $CONFIG["admin_email"]; ?>"></td></tr>
  <tr><td width="50%"><b>Forum URL</b><br>This is the URL to the forums. This should only be the directory, and not include a trailing slash</td><td width="50%"><input type="text" name="forum_url" value="<?php echo $CONFIG["forum_url"]; ?>"></td></tr>
  <tr><td width="50%"><b>Number of icons</b><br>The forum is able to show icons for a post or reply. Please select the number of icons you have in your images directory (in the form of icon<i>no</i>.gif)</td><td width="50%"><input type="text" name="nof_icons" value="<?php echo $CONFIG["nof_icons"]; ?>"></td></tr>
  <tr><td width="50%"><b>Online status timeout</b><br>This is the number of seconds you have to be inactive before your name will be deleted from the online users list</td><td width="50%"><input type="text" name="online_timeout" value="<?php echo $CONFIG["online_timeout"]; ?>"></td></tr>
  <tr><td width="50%"><b>Introduction type</b><br>This is the first screen an user will see.</td><td width="50%"><input type="radio" name="intro_type" value="0"<?php if ($CONFIG["intro_type"] == 0) { echo " checked"; }; ?>>Forum list</input><br><input type="radio" name="intro_type" value="1"<?php if ($CONFIG["intro_type"] == 1) { echo " checked"; }; ?>>Category list</input><br><input type="radio" name="intro_type" value="2"<?php if ($CONFIG["intro_type"] == 2) { echo " checked"; }; ?>>Forums with category headings</input></td></tr>
  <tr><td width="50%"><b>Page size</b><br>If the number of thread replies exceed this number, the thread will be split in pages of this size</td><td width="50%"><input type="text" name="page_size" value="<?php echo $CONFIG["page_size"]; ?>"></td></tr>
  <tr><td width="50%"><b>Page display range</b><br>If you have more pages than 2 times this number, the forum will show this amount of numbers at both sides, separated by dots)</td><td width="50%"><input type="text" name="page_display_range" value="<?php echo $CONFIG["page_display_range"]; ?>"></td></tr>
  <tr><td width="50%"><b>Allow users to edit their own posts?</b><br>If this is enabled, users will be able to edit their own posts. Moderators and up will always be able to edit posts not made by them (proven they are listed as moderator)</td><td width="50%"><input type="radio" name="user_allowedit" value="0"<?php if ($CONFIG["user_allowedit"] == 0) { echo " checked"; }; ?>>Do not allow user editing of own posts</input><br><input type="radio" name="user_allowedit" value="1"<?php if ($CONFIG["user_allowedit"] == 1) { echo " checked"; }; ?>>Allow user editing of own posts</input></td></tr>
  <tr><td width="50%"><b>Allow users to delete their own posts and threads?</b><br>If this is enabled, users will be able to delete their own posts/threads. Moderators and up will always be able to delete posts and threads not made by them (proven they are listed as moderator)</td><td width="50%"><input type="radio" name="user_allowdelete" value="0"<?php if ($CONFIG["user_allowdelete"] == 0) { echo " checked"; }; ?>>Do not allow user deletion of own posts and threads</input><br><input type="radio" name="user_allowdelete" value="1"<?php if ($CONFIG["user_allowdelete"] == 1) { echo " checked"; }; ?>>Allow user deletion of own posts and threads</input></td></tr>
  <tr><td width="50%"><b>Default user to report threads to</b><br>If an user reports a thread in a forum which has absolutely no (category) moderators, this user will receive the email. If you preceed this entry with a @, the first member of this group will be notified.</td><td width="50%"><input type="text" name="report_defaultuser" value="<?php echo BuildUserGroupString ($CONFIG["report_defaultid"], $CONFIG["report_defaultflags"]); ?>"></td></tr>
  <td><b>Default new topic timespan</b><br>This will indicate how how old topics can be until we display them upon entering a forum.</td><td><input type="radio" name="topicspan" value="1"<?php if ($CONFIG["topicspan"] == 1) { echo " checked"; }; ?>>last day</input><br><input type="radio" name="topicspan" value="2"<?php if ($CONFIG["topicspan"] == 2) { echo " checked"; }; ?>>last two days</input><br><input type="radio" name="topicspan" value="7"<?php if ($CONFIG["topicspan"] == 7) { echo " checked"; }; ?>>last week</input><br><input type="radio" name="topicspan" value="31"<?php if ($CONFIG["topicspan"] == 31) { echo " checked"; }; ?>>last month</input><br><input type="radio" name="topicspan" value="365"<?php if ($CONFIG["topicspan"] == 365) { echo " checked"; }; ?>>last year</input><br><input type="radio" name="topicspan" value="0"<?php if ($CONFIG["topicspan"] == 0) { echo " checked"; }; ?>>show all topics</input></td></tr>
  <td><b>Server Timezone</b><br>This is the server's timezone. This will be used when calculating time to user timezones</td><td>GMT + <input type="text" name="timezone" value="<?php echo (int)($CONFIG["timezone"] / 3600) . ":" . abs ($CONFIG["timezone"] % 3600) / 60; ?>"></td></tr>
  <td><b>COPPA compliance</b><br>COPPA is a law in the USA which requires childeren under the age of 13 to have parental permission before they can sign up on interactive sites. If this is enabled, you need to have a country field as well. If the user who is registering is below 13 and lives in the USA, he will be redirected to the <i>coppa_page</i> skin template, which should represent futher instructions</td><td><input type="radio" name="coppa_compliance" value="0"<?php if ($CONFIG["coppa_enabled"] == 0) { echo " checked"; }; ?>>Do not enable COPPA compliance</input><br><input type="radio" name="coppa_compliance" value="1"<?php if ($CONFIG["coppa_enabled"] == 1) { echo " checked"; }; ?>>Enable COPPA compliance (requires a Country custom field)</input></td></tr>
  <td><b>Default topic title</b><br>This is the topic title that will be used for thread with a blank title</td><td><input type="text" name="default_topic" value="<?php echo $CONFIG["default_topic"]; ?>"></td></tr>
  <td>
</table><p>
<table width="100%" border=1 cellspacing=1 cellpadding=1>
  <tr><td colspan=2 bgcolor="#000000" align="center"><font color="#ffff00"><b>Membership Options</b></font></td></tr>
  <tr><td width="50%"><b>Administrator status</b><br>This is the status an administrator will get by default</td><td width="50%"><input type="text" name="admin_title" value="<?php echo $CONFIG["admin_title"]; ?>"></td></tr>
  <tr><td width="50%"><b>Mega Moderator status</b><br>This is the status a mega moderator will get by default</td><td width="50%"><input type="text" name="megamod_title" value="<?php echo $CONFIG["megamod_title"]; ?>"></td></tr>
  <tr><td width="50%"><b>Category Moderator status</b><br>This is the status a category moderator will get by default</td><td width="50%"><input type="text" name="catmod_title" value="<?php echo $CONFIG["catmod_title"]; ?>"></td></tr>
  <tr><td width="50%"><b>Moderator status</b><br>This is the status a moderator will get by default</td><td width="50%"><input type="text" name="mod_title" value="<?php echo $CONFIG["mod_title"]; ?>"></td></tr>
  <tr><td width="50%"><b>Member status</b><br>This is the status an ordinary member will get by default</td><td width="50%"><input type="text" name="member_title" value="<?php echo $CONFIG["member_title"]; ?>"></td></tr>
  <tr><td width="50%"><b>Deleted member name</b><br>This will be displayed as a username for any deleted member</td><td width="50%"><input type="text" name="delmem_name" value="<?php echo $CONFIG["delmem_name"]; ?>"></td></tr>
  <tr><td width="50%"><b>Deleted member status</b><br>This is the status anyone not in the database will get</td><td width="50%"><input type="text" name="unknown_title" value="<?php echo $CONFIG["unknown_title"]; ?>"></td></tr>
  <tr><td width="50%"><b>Deleted member post count</b><br>This will be displayed as a post count for any deleted member</td><td width="50%"><input type="text" name="delmem_postcount" value="<?php echo $CONFIG["delmem_postcount"]; ?>"></td></tr>
  <tr><td width="50%"><b>Deleted member join date</b><br>This will be displayed as a join date for any deleted member</td><td width="50%"><input type="text" name="delmem_joindate" value="<?php echo $CONFIG["delmem_joindate"]; ?>"></td></tr>
</table><p>
<table width="100%" border=1 cellspacing=1 cellpadding=1>
  <tr><td colspan=2 bgcolor="#000000" align="center"><font color="#ffff00"><b>Private Messaging Options</b></font></td></tr>
  <tr><td width="50%"><b>Allow private messaging</b><br>Private Messaging allows users to send messages to other users. You can enable or disable this feature</td><td width="50%"><input type="radio" name="allow_pm" value="0"<?php if ($CONFIG["allow_pm"] == 0) { echo " checked"; } ?>>Do not allow private messages</input><br><input type="radio" name="allow_pm" value="1"<?php if ($CONFIG["allow_pm"] == 1) { echo " checked"; } ?>>Allow private messages</input></td></tr>
  <tr><td width="50%"><b>Maximal unread messages per user</b><br>This is the number of messages one user can send to another user. If you try to send a message and the user has this number of unread messages from you, the send request will be denied</td><td width="50%"><input type="text" name="pm_per_user" value="<?php echo $CONFIG["pm_per_user"]; ?>"></td></tr>
</table><p>
<table width="100%" border=1 cellspacing=1 cellpadding=1>
  <tr><td colspan=2 bgcolor="#000000" align="center"><font color="#ffff00"><b>Signature Options</b></font></td></tr>
  <tr><td width="50%"><b>Allow signatures</b><br>A signature is an user-customizable piece of text that can be appended to posts made. If this option is enabled, users will be able to edit their own signature. If this is disabled, signatures will never show up in the forums.</td><td width="50%"><input type="radio" name="allow_sig" value="0"<?php if ($CONFIG["allow_sig"] == 0) { echo " checked"; } ?>>Do not allow signatures</input><br><input type="radio" name="allow_sig" value="1"<?php if ($CONFIG["allow_sig"] == 1) { echo " checked"; } ?>>Allow signatures</input></td></tr>
  <tr><td width="50%"><b>Allow MaX code in signatures</b><br>You may chose to allow MaX codes in signatures. If this is disabled, MaX codes in signatures will show up as normal text.</td><td width="50%"><input type="radio" name="allow_sig_max" value="0"<?php if ($CONFIG["allow_sig_max"] == 0) { echo " checked"; } ?>>Do not allow MaX code in signatures</input><br><input type="radio" name="allow_sig_max" value="1"<?php if ($CONFIG["allow_sig_max"] == 1) { echo " checked"; } ?>>Allow MaX code in signatures</input></td></tr>
  <tr><td width="50%"><b>Allow HTML in signatures</b><br>You may chose to allow HTML in signatures. If this is disabled, HTML in signatures will show up as normal text.</td><td width="50%"><input type="radio" name="allow_sig_html" value="0"<?php if ($CONFIG["allow_sig_html"] == 0) { echo " checked"; } ?>>Do not allow HTML in signatures</input><br><input type="radio" name="allow_sig_html" value="1"<?php if ($CONFIG["allow_sig_html"] == 1) { echo " checked"; } ?>>Allow HTML in signatures</input></td></tr>
  <tr><td width="50%"><b>Block images from signatures</b><br>You may chose to disable images in signatures. If this is enabled, signatures will not be permitted to allow images.</td><td width="50%"><input type="radio" name="block_sig_img" value="0"<?php if ($CONFIG["block_sig_img"] == 0) { echo " checked"; } ?>>Allow images in signatures</input><br><input type="radio" name="block_sig_img" value="1"<?php if ($CONFIG["block_sig_img"] == 1) { echo " checked"; } ?>>Block images from signatures</input></td></tr>
  <tr><td width="50%"><b>Block JavaScript and bad HTML tags from signatures</b><br>You may chose to harmful code in signatures. If this is enabled, signatures will not be permitted to contain javascript and severnal HTML tags.</td><td width="50%"><input type="radio" name="block_sig_js" value="0"<?php if ($CONFIG["block_sig_js"] == 0) { echo " checked"; } ?>>Allow JavaScript and evil HTML in signatures</input><br><input type="radio" name="block_sig_js" value="1"<?php if ($CONFIG["block_sig_js"] == 1) { echo " checked"; } ?>>Block JavaScript and evil HTML from signatures</input></td></tr>
</table><p>
<center><center><input type="submit" value="Submit Changes"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoOptions()
    //
    // This will actually activate the new options.
    //
    function
    DoOptions() {
	global $forum_title, $ip_log, $notify_edit, $edit_timestamp_format;
	global $joindate_timestamp_format, $allow_register, $bb_rules;
	global $admin_email, $forum_url, $annc_timestamp_format;
	global $admin_title, $megamod_title, $catmod_title, $mod_title;		global $member_title, $unknown_title, $delmem_postcount;
	global $delmem_joindate, $delmem_name, $nof_icons;
	global $post_timestamp_format, $online_timeout, $intro_type, $page_size;
	global $page_display_range, $allow_pm, $pm_per_user, $allow_sig;
	global $allow_sig_html, $allow_sig_max, $block_sig_js;
	global $block_sig_img, $user_allowedit, $user_allowdelete;
        global $report_defaultuser, $topicspan, $timezone, $coppa_compliance;
	global $default_topic, $censored_words;

        // figure out the id and flags of the new user we report to by default
	// does the field start with a @?
	$flags = 0;
        if (preg_match ("/^\@/", $report_defaultuser)) {
	    // yes. destroy the @ and grab the group id
            $report_defaultuser = preg_replace ("/^\@/", "", $report_defaultuser);
            $objectid = GetGroupID ($report_defaultuser);
            $flags = FLAG_USERLIST_GROUP;

            // did this work?
            if ($objectid == "") {
		CPShowHeader();
		print "Group <b>" . $report_defaultuser . "</b> does not exist";
                CPShowFooter();
                exit;
            }
	} else {
            // no. grab the new user id
            $objectid = GetMemberID ($report_defaultuser); $flags = 0;

            // did this work?
            if ($objectid == "") {
		CPShowHeader();
		print "User <b>" . $report_defaultuser . "</b> does not exist";
                CPShowFooter();
                exit;
            }
	}

	// insert the new stuff into the database
	$query = sprintf ("update config set content='%s' where name='report_defaultid'", $objectid); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='report_defaultflags'", $flags); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='forumtitle'", $forum_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='ip_log'", $ip_log); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='notify_edit'", $notify_edit); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='edit_timestamp_format'", $edit_timestamp_format); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='joindate_timestamp_format'", $joindate_timestamp_format); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='allow_register'", $allow_register); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='rules'", $bb_rules); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='admin_email'", $admin_email); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='forum_url'", $forum_url); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='nof_icons'", $nof_icons); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='annc_timestamp_format'", $annc_timestamp_format); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='post_timestamp_format'", $post_timestamp_format); db_query ($query);

	$query = sprintf ("update config set content='%s' where name='admin_title'", $admin_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='megamod_title'", $megamod_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='catmod_title'", $catmod_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='mod_title'", $mod_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='member_title'", $member_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='unknown_title'", $unknown_title); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='delmem_joindate'", $delmem_joindate); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='delmem_postcount'", $delmem_postcount); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='delmem_name'", $delmem_name); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='online_timeout'", $online_timeout); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='intro_type'", $intro_type); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='page_size'", $page_size); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='page_display_range'", $page_display_range); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='allow_pm'", $allow_pm); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='pm_per_user'", $pm_per_user); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='allow_sig'", $allow_sig); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='allow_sig_max'", $allow_sig_max); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='allow_sig_html'", $allow_sig_html); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='block_sig_js'", $block_sig_js); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='block_sig_img'", $block_sig_img); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='user_allowedit'", $user_allowedit); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='user_allowdelete'", $user_allowdelete); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='topicspan'", $topicspan); db_query ($query);
	list ($hour, $min) = explode (":", $timezone);
	$timezone = (abs ($hour) * 3600) + (abs ($min) * 60);
	if ($hour < 0) { $timezone = -$timezone; };
	$query = sprintf ("update config set content='%s' where name='timezone'", $timezone); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='coppa_enabled'", $coppa_compliance); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='default_topic'", $default_topic); db_query ($query);
	$query = sprintf ("update config set content='%s' where name='censored_words'", $censored_words); db_query ($query);

	// show the 'yay' page
	CPShowHeader();
 ?>The new options have successfully been activated.<p>
<?php
	CPShowFooter();
    }

    //
    // EditSkinVars()
    //
    // This will list the available skin variables available for editing.
    //
    function
    EditSkinVars() {
	global $id;

	CPShowHeader();

	// grab all skin information for the database
	$query = sprintf ("select name from skinvars_%s", $id);
	$res = db_query ($query); $nofmatches = db_nof_results ($res);

	// show how much hits we have
	print "This skin has <b>$nofmatches</b> variable";
	if ($nofmatches != 1) { echo "s"; };
	print "<p><ul>";
	
	// show them all
	while ($result = mysql_fetch_row ($res)) {
	    printf ("<li><a href=\"cp_admin.php?action=editskinvar&id=%s&destvar=%s\">%s</a></li>", $id, rawurlencode ($result[0]), $result[0]);
	}

	print "</ul><p>";
 ?><table width="100%">
 <tr>
  <td width="50%" align="center">
   <form action="cp_admin.php" method="post">
   <input type="hidden" name="action" value="addskinvar">
   <input type="hidden" name="id" value="<?php echo $id; ?>">
   <input type="submit" value="Add skin variable">
   </form>
  </td>
  <td width="50%" align="center">
   <form action="cp_admin.php" method="post">
   <input type="hidden" name="action" value="editskin">
   <input type="hidden" name="id" value="<?php echo $id; ?>">
   <input type="submit" value="Back to skin overview">
   </form>
  </td>
 </tr>
</table>
<?php
	CPShowFooter();
    }

    //
    // EditSkinVar()
    //
    // This will edit a skin variable.
    //
    function
    EditSkinVar() {
	global $id,$destvar;

	// grab this variable
	$query = sprintf ("select content from skinvars_%s where name='%s'", $id,$destvar);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// build the page
	CPShowHeader();
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doeditskinvar">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="destvar" value="<?php echo $destvar; ?>"><table>
<tr>
  <td width="20%">Variable name</td>
  <td width="80%"><input type="text" name="the_varname" value="<?php echo $destvar; ?>"></td>
</tr>
<tr>
  <td>Value</td>
  <td><input type="text" name="the_value" value="<?php echo $result[0]; ?>"></td>
</tr>
</table><p>
<input type="checkbox" name="delete">Check this to delete this variable</input><p>
<input type="submit" value="OK"></form>
<?php
	CPShowFooter();
    }

    //
    // DoEditSkinVar()
    //
    // This will actually modify the skin variable.
    //
    function
    DoEditSkinVar() {
	global $id, $destvar, $the_varname, $the_value, $delete;

	CPShowHeader();

	// need to delete?	
	if ($delete == "") {
	    // no, just modify it
	    $query = sprintf ("update skinvars_%s set name='%s',content='%s' where name='%s'", $id, $the_varname, $the_value, $destvar);
	    db_query ($query);

	    // show the 'yay' page
 	    print "Thank you, the variable has successfully been updated.<p>";
	} else {
	    // yes. delete it
	    $query = sprintf ("delete from skinvars_%s where name='%s'", $id, $the_varname);
	    db_query ($query);

	    // show the 'yay' page
 	    print "Thank you, the variable has successfully been deleted.<p>";
	}
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="editskinvars">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="submit" value="Back to variable overview">
</form>
<?php
    }

    //
    // AddSkinVar()
    //
    // This will add the skin template.
    //
    function
    AddSkinVar() {
	global $id;

	CPShowHeader();

 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doaddskinvar">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%">
<tr>
  <td width="20%">Variable name</td>
  <td width="80%"><input type="text" name="the_varname"></td>
</tr>
<tr>
  <td valign="top">Value</td>
  <td width="80%"><input type="text" name="the_value"></td>
</tr></table><p>
<input type="submit" value="Add skin variable">
</form>
<?php
	print "</table>";

	CPShowFooter();
    }

    //
    // DoAddSkinVar()
    //
    // This will actually add a skin variable.
    //
    function
    DoAddSkinVar() {
	global $id, $the_varname, $the_value;

	CPShowHeader();

	// build the query
	$query = sprintf ("insert into skinvars_%s values ('%s','%s')", $id, $the_varname, $the_value);
	db_query ($query);

	// show the 'yay' page
	print "Thank you, the variable has successfully been added.<p>";
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="editskinvars">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="submit" value="Return to skin variable overview">
</form>
<?php

	CPShowFooter();
    }

    //
    // SetSkinDefault()
    //
    // This will set a skin as the default skin.
    //
    function
    SetSkinDefault () {
	global $id;

	CPShowHeader();

	// flag the current default skin as no longer default
	$query = sprintf ("update skins set flags = flags and not %s", FLAG_SKIN_DEFAULT);
	db_query ($query);

	// activate the new default skin
	$query = sprintf ("update skins set flags = flags or %s where id=%s", FLAG_SKIN_DEFAULT, $id);
	db_query ($query);

	// show the 'yay' page
	print "Thank you, the skin has successfully been activated as default skin";
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="skins">
<input type="submit" value="Return to skin management">
</form>
<?php
	CPShowFooter();
    }

    //
    // Forums()
    //
    // This will show the page for editing one forum.
    //
    function 
    Forums() {
	global $page;

	CPShowHeader();

	// always default to page one
	if ($page == "") { $page = 1; }

	// count the forums
	$query = sprintf ("select count(id) from forums");
	$res = db_query ($query); $result = db_fetch_results ($res);
	$noforums = $result[0];

	// calculate the number of pages
	$nofpages = floor ($noforums / FORUMS_PER_PAGE);
	if (($noforums * FORUMS_PER_PAGE) != $nofpages) { $nofpages++; }

	// calculate the number of forums to list
	if ($page == "0") {
	    // we need to show 'em all. do it
	    $fromno = 0; $howmuch = $noforums;
	} else {
	    // we only need to list FORUMS_PER_PAGE ones. do it
	    $fromno = ($page - 1) * FORUMS_PER_PAGE; $howmuch = FORUMS_PER_PAGE;
	}

	// now, create the page [] thingies.
	print "Page ";
	for ($i = 1; $i <= $nofpages; $i++) {
	    // is this one currently selected?
	    if ($page == $i) {
	 	// yes. don't hyperlink it, make it bold instead.
		printf ("[<b>%s</b>] ",$i);
	    } else {
		// no. just make it a hyperlink
		printf ("[<a href=\"cp_admin.php?action=forums&page=%s\">%s</a>] ",$i,$i);
	    }
	}

	// add the 'All' link
	if ($page == "0") {
	    print "[<b>All</b>]";
	} else {
	    // no. just make it a hyperlink
	    print "[<a href=\"cp_admin.php?action=forums&page=0\">All</a>]";
	}

	// build the table
 ?><table width="100%" border=1>
<tr>
  <td width="60%"><b>Forum name</b></td>
  <td width="40%"><b>Forum flags</b></td>
</tr>
<?php

	// select the forums we need
	$query = sprintf ("select id,name,flags from forums order by name asc limit %s,%s",$fromno,$howmuch);
	$res = db_query ($query);

	// add the forums to the list
	while ($result = db_fetch_results ($res)) {
	    // construct the forum flags
	    $forumflags = ""; $flags = $result[2];
	    if (($flags & FLAG_FORUM_ALLOWHTML) != 0) { $forumflags[] = "<font color=\"#00ff00\">HTML allowed</font>"; };
	    if (($flags & FLAG_FORUM_ALLOWMAX) != 0) { $forumflags[] = "<font color=\"#00ff00\">MaX allowed</font>"; };
	    if (($flags & FLAG_FORUM_DENYEVILHTML) != 0) { $forumflags[] = "<font color=\"#800000\">Deny evil HTML</font>"; };
	    if (($flags & FLAG_FORUM_NOIMAGES) != 0) { $forumflags[] = "<font color=\"#800000\">Images disabled</font>"; };

	    // still no flags?
	    if ($forumflags == "") {
		// yes. add 'none'
		$forumflags[] = "None";
	    }

	    // add the forum
	    printf ("<tr><td><a href=\"cp_admin.php?action=editforum&forumid=%s\">%s</a></td><td>%s</td></tr>",$result[0],$result[1], implode (", ", $forumflags));
	}
 ?></table>
<center><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="addforum">
<input type="submit" value="Add a forum">
</form></center>
<?php
	CPShowFooter();
    }

    //
    // BuildUserFields ($fieldname,$res)
    //
    // This will build user field, using name $name[$index]. $res is assumed
    // to be a database resource, in the form $id : $userid.
    //
    function
    BuildUserFields ($fieldname,$res) {
	// if we have users, build the list
	while ($result = db_fetch_results ($res)) {
	    // does this user/groupname correctly resolve?
	    $name = BuildUserGroupString ($result[1], $result[2]);
	    if ($name != "") {
		// yes. add this user/group to the list
		printf ("<input type=\"text\" name=\"%s[%s]\" value=\"%s\"> ",$fieldname,$result[0], $name);
	    }
	}
        printf ("<input type=\"text\" name=\"new%s[0]\">",$fieldname);
    }

    //
    // EditForum()
    //
    // This will show the page for editing a specific forum.
    //
    function
    EditForum() {
	global $forumid, $forum_flag;

	CPShowHeader();

	// grab the database entry
	$query = sprintf ("select name,flags,description,catno,image from forums where id=%s", $forumid);
	$res = db_query ($query); $forumresult = db_fetch_results ($res);

	// do we have any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
 ?>This forum doesn't appear to exist. Perhaps someone deleted it before you coukd?
<?php
	    CPShowFooter();
	    exit;
	}

	// build the page
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doeditforum">
<input type="hidden" name="forumid" value="<?php echo $forumid; ?>">
<table width="100%">
<tr>
  <td width="20%">Forum name</td>
  <td width="80%"><input type="text" name="the_forumname" value="<?php echo $forumresult[0]; ?>"></td>
</tr>
<tr>
  <td width="20%">Category</td>
  <td width="80%"><select name="catid"><option value="0"<?php if ($forumresult[3] == 0) { echo " selected"; } ?>>No category</option>
   <?php
	// build a list of all categories
	$query = sprintf ("select id,name from categories order by name asc");
	$res2 = db_query ($query);

	// add them all, as needed
	while ($tmp = db_fetch_results ($res2)) {
	    printf ("<option value=\"%s\"", $tmp[0]);
	    if ($tmp[0] == $forumresult[3]) { echo " selected"; };
	    printf (">%s</option>", $tmp[1]);
	}
    ?></select></td>
</tr>
<tr>
  <td>Moderators<br><font size=1>Moderators are users that can delete, edit and lock any thread or post in a forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><?php
	// grab all mods for this forum
	$query = sprintf ("select id,userid,flags from mods where forumid=%s", $forumid);
	$res = db_query ($query);
        BuildUserFields ("mod", $res);
?></td>
</tr>
<tr>
  <td>Restricted users<br><font size=1>If this is not empty, anyone listed can access the forums. Administrators can always access any forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><?php
	// grab all restricted users for this forum
	$query = sprintf ("select id,userid,flags from restricted where forumid=%s", $forumid);
	$res = db_query ($query);
        BuildUserFields ("restricted", $res);
?></td>
</tr>
<tr>
  <td>Users to notify<br><font size=1>If this is not empty, anyone listed here will be sent an email if a new thread or post is created in this forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><?php
	// grab all notified users for this forum
	$query = sprintf ("select id,userid,flags from notify where forumid=%s", $forumid);
	$res = db_query ($query);
        BuildUserFields ("notify", $res);
?></td>
</tr>
<tr>
  <td>Forum Image<br><font size=1>If this is set, this will be used instead of te default logo when browsing through a forum. It has to reside in the path specified in the skin variable <i>images_url</i></td>
  <td><input type="text" name="image" value="<?php echo $forumresult[4]; ?>"></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td valign="top">Description</td>
  <td><textarea name="the_desc" rows=10 cols=40><?php echo htmlspecialchars ($forumresult[2]); ?></textarea></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td valign="top">Flags</td>
  <td><?php
    // add the flags
    while (list ($bit, $desc) = each ($forum_flag)) {
	printf ("<input type=\"checkbox\" name=\"f_bit[%s]\"", $bit);
	if (($forumresult[1] & $bit) != 0) { echo " checked"; };
	printf (">%s</input><br>", $desc);
    }
 ?></td>
</tr>
</table><p>
<table width="100%">
 <tr>
  <td width="50%" align="center"><input type="submit" value="Submit forum changes"></form></td>
  <td width="50%" align="center"><form action="cp_admin.php" method="post"><input type="hidden" name="action" value="deleteforum"><input type="hidden" name="forumid" value="<?php echo $forumid; ?>"><input type="submit" value="Delete forum"></td>
 </tr>
</table>
<?php
	CPShowFooter();
    }

    //
    // HandleUserFields ($modifyquery, $deletequery, $forumid, $field)
    //
    // This will perform query $modifyquery or $deletequery on forum $forumid
    // for array $field.
    //
    function
    HandleUserFields ($modifyquery, $deletequery, $forumid, $field) {
	// activate the new mods
	while (list ($id, $name) = @each ($field)) {
	    // is the new moderator name blank?
	    if ($name == "") {
		// yes. get rid of this entry
		$query = sprintf ($deletequery, $id);
		if ($query != "") { db_query ($query); };
	    } else {
		// no. does the field start with a @?
		if (preg_match ("/^\@/", $name)) {
		    // yes. destroy the @ and grab the group id
		    $name = preg_replace ("/^\@/", "", $name);
		    $objectid = GetGroupID ($name);
		    $flags = FLAG_USERLIST_GROUP;

		    // did this work?
		    if ($objectid == "") {
			print "Group <b>" . $name . "</b> does not exist";
			CPShowFooter();
			exit;
		    }
		} else {
		    // no. grab the new user id
		    $objectid = GetMemberID ($name); $flags = 0;
	
		    // did this work?
		    if ($objectid == "") {
			print "User <b>" . $name . "</b> does not exist";
			CPShowFooter();
			exit;
		    }
		}

		// activate the new user/group
		$query = $modifyquery;
		$query = preg_replace ("/\[objectid\]/", $objectid, $query);
		$query = preg_replace ("/\[flags\]/", $flags, $query);
		$query = preg_replace ("/\[id\]/", $id, $query);
		if ($query != "") { db_query ($query); };
	    }
	}
    }

    //
    // DoEditForum()
    //
    // This will actually edit a certain forum.
    //
    function
    DoEditForum() {
	global $the_forumname, $forumid, $mod, $restricted, $f_bit;
	global $catid, $newmod, $newrestricted, $notify, $newnotify, $image;
	global $the_desc;

	$the_desc = rawurldecode ($the_desc);

	CPShowHeader();

	// handle the moderators
	HandleUserFields ("update mods set userid=[objectid],flags=[flags] where id=[id]", "delete from mods where id=%s", $forumid, $mod);
	HandleUserFields ("insert into mods values (NULL," . $forumid . ",[objectid],[flags])", "", $forumid, $newmod);

	// handle the restricted list
	HandleUserFields ("update restricted set userid=[objectid],flags=[flags] where id=[id]", "delete from restricted where id=%s", $forumid, $restricted);
	HandleUserFields ("insert into restricted values (NULL," . $forumid . ",[objectid],[flags])", "", $forumid, $newrestricted);

	// handle the notification list
	HandleUserFields ("update notify set userid=[objectid],flags=[flags] where id=[id]", "delete from notify where id=%s", $forumid, $notify);
	HandleUserFields ("insert into notify values (NULL," . $forumid . ",[objectid],[flags])", "", $forumid, $newnotify);

	// build the flags
	$flags = 0;
	while (list ($bit, $on) = @each ($f_bit)) {
	    // is this box checked?
	    if ($on != "") {
		// yes. add the flag
		$flags |= $bit;
	    }
	}

	// build a query
	$query = sprintf ("update forums set name='%s',description='%s',flags=%s,catno=%s,image='%s' where id=%s",$the_forumname,$the_desc,$flags,$catid,$image,$forumid);
	db_query ($query);

	// it worked. show the 'yay' page
 ?>The forum has successfully been modified.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="forums">
<input type="submit" value="Back to forum overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // DeleteForum()
    //
    // This will delete a forum.
    //
    function
    DeleteForum() {
	global $forumid;

	CPShowHeader();

	// first, make sure the forum exists
	$query = sprintf ("select id from forums where id=%s", $forumid);
	$res = db_query ($query);
	if (db_nof_results ($res) == 0) {
	    // it doesn't exist. complain
 ?>We're sorry, but this forum doesn't appear to exist. Perhaps it was deleted by someone else before you could?
<?php
	    CPShowFooter();
	    exit;
	}

	// zap the forum entry
	$query = sprintf ("delete from forums where id=%s", $forumid);
	db_query ($query);

	// delete all forum threads
	$query = sprintf ("delete from threads where forumid=%s", $forumid);
	db_query ($query);

	// delete all forum posts
	$query = sprintf ("delete from posts where forumid=%s", $forumid);
	db_query ($query);

	// yay, it worked. show the 'yahoo' page
 ?>The forum has successfully been deleted.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="forums">
<input type="submit" value="Back to forum overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // AddForum()
    //
    // This will show the 'add forum' page.
    //
    function
    AddForum() {
	global $forum_flag;

	// build the page
	CPShowHeader();
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doaddforum">
<table width="100%">
<tr>
  <td width="20%">Forum name</td>
  <td width="80%"><input type="text" name="the_forumname"></td>
</tr>
<tr>
  <td width="20%">Category</td>
  <td width="80%"><select name="catid"><option value="0">No category</option>
   <?php
	// build a list of all categories
	$query = sprintf ("select id,name from categories order by name asc");
	$res2 = db_query ($query);

	// add them all, as needed
	while ($tmp = db_fetch_results ($res2)) {
	    printf ("<option value=\"%s\">%s</option>", $tmp[0], $tmp[1]);
	}
    ?></select></td>
</tr>
<tr>
  <td>Moderators<br><font size=1>Moderators are users that can delete, edit and lock any thread or post in a forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><input type="text" name="mod[0]"> <input type="text" name="mod[1]"></td>
</tr>
<tr>
  <td>Restricted users<br><font size=1>If this is not empty, anyone listed can access the forums. Administrators can always access any forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><input type="text" name="restricted[0]"> <input type="text" name="restricted[1]"></td>
</tr>
<tr>
  <td>Users to notify<br><font size=1>If this is not empty, anyone listed here will be sent an email if a new thread or post is created in this forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><input type="text" name="notify[0]"> <input type="text" name="notify[1]"></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td valign="top">Description</td>
  <td><textarea name="the_desc" rows=10 cols=40></textarea></td>
</tr>
<tr>
  <td colspan=2>&nbsp;</td>
</tr>
<tr>
  <td valign="top">Flags</td>
  <td><?php
    // add the flags
    while (list ($bit, $desc) = each ($forum_flag)) {
	printf ("<input type=\"checkbox\" name=\"f_bit[%s]\"", $bit);
	if (($forumresult[1] & $bit) != 0) { echo " checked"; };
	printf (">%s</input><br>", $desc);
    }
 ?></td>
</tr>
</table><p>
<center><input type="submit" value="Add forum"></center></form>
<?php
	CPShowFooter();
    }

    //
    // DoAddForum()
    //
    // This will actually add a forum.
    //
    function
    DoAddForum() {
	global $the_forumname, $mod, $restricted, $f_bit, $the_desc, $catid;
	global $notify;
	$the_desc = rawurldecode ($the_desc);

	CPShowHeader();

	// is this forum name already in use?
	$query = sprintf ("select id from forums where name='%s'", $the_forumname);
	$res = db_query ($query);
	if (db_nof_results ($res) != 0) {
	    // yes. complain
 ?>This forum name (<b><?php echo $the_forumname; ?></b>) is already in use. Forum names must be unique.<?php
	    CPShowFooter();
	    exit;
	}

	// build the flags
	$flags = 0;
	while (list ($bit, $on) = @each ($f_bit)) {
	    // is this box checked?
	    if ($on != "") {
		// yes. add the flag
		$flags |= $bit;
	    }
	}

	// build a query
	$query = sprintf ("insert into forums values (NULL,'%s',%s,'%s',0,0,'','',%s,0,'')",$the_forumname,$flags,$the_desc,$catid);
	db_query ($query);
	$forumid = db_get_insert_id();

	// add all moderators
	HandleUserFields ("insert into mods values (NULL," . $forumid . ",[objectid],[flags])", "", $forumid, $mod);

	// add all restricted ccounts
	HandleUserFields ("insert into restricted values (NULL," . $forumid . ",[objectid],[flags])", "", $forumid, $restricted);

	// add all accounts to be notified
	HandleUserFields ("insert into notify values (NULL," . $forumid . ",[objectid],[flags])", "", $forumid, $notify);

	// it worked. show the 'yay' page
 ?>The forum has successfully been added.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="forums">
<input type="submit" value="Back to forum overview">
</form><?php
	CPShowFooter();
    }

    //
    // Cats()
    //
    // This will list all available categories.
    //
    function
    Cats() {
	// build the page
	CPShowHeader();
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="docats">
<table width="100%">
<tr>
  <td width="1%"><b>Order</b></td>
  <td width="99%"><b>Category Name</b></td>
</tr>
<?php
	// list all categories
	$query = sprintf ("select id,orderno,name from categories order by orderno asc");
	$res = db_query ($query);

	while ($tmp = db_fetch_results ($res)) {
	    // show the category
	    printf ("<tr><td><input type=\"text\" size=5 name=\"order[%s]\" value=\"%s\"></td><td><a href=\"cp_admin.php?action=editcat&catid=%s\">%s</a></td></tr>",$tmp[0],$tmp[1],$tmp[0],$tmp[2]);
	}
	print "</table>";

 ?><p><center><input type="submit" value="Activate changes"></center></form><p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="addcat">
<center><input type="submit" value="Add category"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoCats()
    //
    // This will actually submit the new category order.
    //
    function
    DoCats() {
	global $order;

	CPShowHeader();

	// browse them all
	while (list ($catid, $orderno) = each ($order)) {
	    // build the query
	    $query = sprintf ("update categories set orderno='%s' where id=%s", $orderno, $catid);
	    db_query ($query);
	}

	// this worked. show the 'wohoo' page
 ?>Thank you, the category order has successfully been updated.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="cats">
<input type="submit" value="Return to category overview">
</form>
<?php

	CPShowFooter();
    }

    //
    // EditCat()
    //
    // This will show the page for editing a category.
    //
    function
    EditCat() {
	global $catid;

	CPShowHeader();

	// grab the category information
	$query = sprintf ("select name from categories where id=%s", $catid);
	$res = db_query ($query);

	// did this work?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    print "Sorry, but this category appears to be deleted. Perhaps it was deleted before you could load this page?";
	    CPShowFooter();
	    exit;
	}

	// grab the results
	$result = db_fetch_results ($res);

	// build the page
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doeditcat">
<input type="hidden" name="catid" value="<?php echo $catid ?>">
<table width="100%">
  <tr>
    <td width="20%">Category name</td>
    <td width="80%"><input type="text" name="catname" value="<?php echo $result[0]; ?>"></td>
  </tr>
  <tr>
    <td width="20%">Category Moderators<br><font size=1>Category Moderators (also known as Super Moderators) are capable of moderating all forums within this category</td>
    <td width="80%"><?php
	// grab the list of category mods
	$query = sprintf ("select id,userid,flags from catmods where forumid=%s", $catid);
	$res = db_query ($query);
        BuildUserFields ("mod", $res);
?></td>
  </tr>
</table><p>
<input type="checkbox" name="f_delete">Check this to delete this category</input><p>
<center><input type="submit" value="Submit changes"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoEditCat()
    //
    // This will actually modify the category.
    //
    function
    DoEditCat() {
	global $catid, $catname, $mod, $newmod, $f_delete;

	CPShowHeader();

	// grab the category info
	$query = sprintf ("select id from categories where id=%s", $catid);
	$res = db_query ($query);

	// got any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    print "Sorry, but this category seems to have been deleted. Perhaps someone deleted it before you could submit your changes?";
	    CPShowFooter();
	    exit;
	}

	// need to get rid of this category?
	if ($f_delete != "") {
	    // yes. delete it
	    $query = sprintf ("delete from categories where id=%s", $catid);
	    db_query ($query);
	} else {
	    // handle the moderators
	    HandleUserFields ("update catmods set userid=[objectid],flags=[flags] where id=[id]", "delete from catmods where id=%s", $catid, $mod);
	    HandleUserFields ("insert into catmods values (NULL," . $catid . ",[objectid],[flags])", "", $forumid, $newmod);
	}

	// activate the changes
	$query = sprintf ("update categories set name='%s' where id=%s", $catname, $catid);
	db_query ($query);

	// all has been updated. show the 'yay' page
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="cats">
Thank you, the category has successfully been updated.<p>
<center><input type="submit" value="Return to category overview"></center>
</form>
<?php

	CPShowFooter();
    }

    //
    // AddCat()
    //
    // This will show the page for adding a category.
    //
    function
    AddCat() {
	CPShowHeader();
 ?>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doaddcat">
<table width="100%">
  <tr>
    <td width="20%">Category name</td>
    <td width="80%"><input type="text" name="catname"></td>
  </tr>
  <tr>
    <td width="20%">Category Moderators<br><font size=1>Category Moderators (also known as Super Moderators) are capable of moderating all forums within this category</td>
    <td><input type="text" name="mod[0]"> <input type="text" name="mod[1]"></td>
  </tr>
</table><p>
<center><input type="submit" value="Add Category"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoAddCat()
    //
    // This will actually add a category.
    //
    function
    DoAddCat() {
	global $catname, $mod;

	CPShowHeader();

	// do we have an actual name?
	if (trim ($catname) == "") {
	    // no. complain
	    print "You must supply a name for the category";
	    CPShowFooter();
	    exit;
	}

	// count the number of categories
	$query = sprintf ("select count(id) from categories");
	list ($catcount) = db_fetch_results (db_query ($query));
	
	// add the category
	$query = sprintf ("insert into categories values (NULL,'%s',%s)", $catname, $catcount);
	db_query ($query);
	$catid = db_get_insert_id();

	// add all super mods
	HandleUserFields ("insert into catmods values (NULL," . $catid . ",[objectid],[flags])", "", $forumid, $mod);

	// all went ok. show the 'yay' page
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="cats">
Thank you, the category has successfully been edited<p>
<center><input type="submit" value="Return to category overview"></center>
</form>
<?php

	CPShowFooter();
    }

    //
    // BuildFieldTypes ($curtype)
    //
    // This will build a dropdown list of all field types known, with $curtype
    // selected.
    //
    function
    BuildFieldType ($curtype) {
	global $exfield_type; reset ($exfield_type);

	// build the list
	$tmp = "";
	while (list ($no, $desc) = each ($exfield_type)) {
	    $tmp .= "<option value=\"" . $no . "\"";
	    if ($curtype == $no) { $tmp .= " selected"; }
	    $tmp .= ">" . $desc . "</option>";
	}

	// return the list
	return $tmp;
    }

    //
    // BuildFieldPerm ($curperm)
    //
    // This will build a dropdown list of all permissions, with $curperm
    // selected.
    //
    function
    BuildFieldPerm ($curperm) {
	global $exfield_perm; reset ($exfield_perm);

	// build the list
	$tmp = "";
	while (list ($no, $desc) = each ($exfield_perm)) {
	    $tmp .= "<option value=\"" . $no . "\"";
	    if ($curperm == $no) { $tmp .= " selected"; }
	    $tmp .= ">" . $desc . "</option>";
	}

	// return the list
	return $tmp;
    }

    //
    // ExtraFields()
    //
    // This will show the page for extra field editing.
    //
    function
    ExtraFields() {
	CPShowHeader();

	// build the layout
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doextrafields">
<table width="100%" border=1>
<tr>
  <td width="5%"><b>ID</b></td>
  <td width="10%"><b>Visible?</b></td>
  <td width="35%"><b>Name</b></td>
  <td width="20%"><b>Type</b></td>
  <td width="30%"><b>Permissions</b></td>
</tr>
<?php
	// grab all custom fields
	$query = sprintf ("select id,name,type,visible,perms from customfields");
	$res = db_query ($query);

	// browse them all
	while ($result = db_fetch_results ($res)) {
	    // add the field
	    printf ("<tr><td align=\"center\">%s</td>", $result[0]);
	    printf ("<td align=\"center\"><input type=\"checkbox\" name=\"visible[%s]\"", $result[0]);
	    if ($result[3] != 0) { echo " checked"; }
	    printf ("></td><td><input type=\"text\" name=\"name[%s]\" value=\"%s\"></td>", $result[0], $result[1]);
	    printf ("<td><select name=\"type[%s]\">%s</select></td>", $result[0], BuildFieldType ($result[2]));
	    printf ("<td><select name=\"perm[%s]\">%s</select></td></tr>", $result[0], BuildFieldPerm ($result[4]));
	}

 ?></table><p>
<table width="100%" border=1>
<tr>
  <td width="10%"><center><b>Add?</b></center></td>
  <td width="70%"><b>Number of fields to add</b></td>
</tr>
<tr>
  <td align="center"><input type="checkbox" name="add"></td>
  <td><input type="text" name="numadd"></td>
</tr>
</table><p>
<center><input type="submit" value="Submit Changes"></center></form>
<?php
	CPShowFooter();
    }

    //
    // DoExtraFields()
    //
    // This will actually take care of the extra fields.
    //
    function
    DoExtraFields() {
	global $visible, $name, $type, $perm, $add, $numadd;

	// do we have any types?
	if (is_array ($type) != 0) {
	    // yes. modify them
	    while (list ($no, $value) = each ($type)) {
	        // do we need to get rid of this field?
	        if ($value == 0) {
	   	    // yes. do it
		    $query = sprintf ("alter table accounts drop column extra%s", $no);
		    db_query ($query);
		    $query = sprintf ("delete from customfields where id=%s", $no);
		    db_query ($query);
	        } else {
		    // no. just modify it
	            $vis = 0; if ($visible[$no] != "") { $vis = 1; };
		    $query = sprintf ("update customfields set visible=%s,name='%s',type=%s,perms=%s where id=%s", $vis, $name[$no], $type[$no], $perm[$no], $no);
		    db_query ($query);
	        }
	    }
	}

	// need to add fields?
	if ($add != "") {
	    // yes. do it
	    for ($i = 0; $i < $numadd; $i++) {
		// add a new extra field
		$query = sprintf ("insert into customfields values (NULL,'New field',1,0,0)");
		db_query ($query);

		// grab the new id
		$no = db_get_insert_id();

		// add the column for it
		$query = sprintf ("alter table accounts add extra%s varchar(128)", $no);
		db_query ($query);
	    }
	}

	CPShowHeader();

	// it worked. show the 'yay' page
 ?>Thank you, the extra fields have successfully been updated.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="extrafields">
<input type="submit" value="Back to extra field overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // FetchForumFile ($name)
    //
    // This will fetch forum file $name using license information $name. It
    // will return the file's contents.
    //
    function
    FetchForumFile ($name) {
	global $lic_username, $lic_password;

	// construct the url
	$url = sprintf (GRABFILE_URL, $lic_username, $lic_password, $name);
	$fp = @fopen ($url, "rb") or die (" Failure, could not establish link to server</li></ul>");
	$data = "";
	while (!feof ($fp)) { $data .= fread ($fp, 1024); };
	fclose ($fp);

	// is this an error line?
	if (preg_match ("/\#ERR\#/", $data)) {
	    // yes. complain
	    $errmsg = preg_replace ("/\#ERR\# /", "", $data);
	    print " <b>Failure</b> (file is <b>$name</b>, error message is <b>$errmsg</b>, please contact technical support)";
	    exit;
	}

	return $data;
    }

    //
    // Groups()
    //
    // This will list all groups we have.
    //
    function
    Groups() {
	CPShowHeader();

	// figure out the total number of groups
	$query = sprintf ("select count(id) from groups");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$totalgroups = $tmp[0];

	// calculate the number of pages
	$nofpages = floor ($totalgroups / GROUPS_PER_PAGE);
	if (($nofpages * GROUPS_PER_PAGE) != $totalgroups) { $nofpages++; };

	// was a page actually given?
	if ($page == "") {
	    // no. default to the first one
	    $page = 1;
	}

	// build the from and to numbers
	$from = ($page - 1) * GROUPS_PER_PAGE + 1;
	$to = $from + GROUPS_PER_PAGE;
	if ($to > $nofpages) { $to = $nofpages; };

	// build the page
 ?><table width="100%" border=0>
 <tr>
  <td width="30%">Listing groups <b><?php echo $from; ?></b> to <b><?php echo $to; ?></b></td>
  <td width="70%" align="right"><?php
	echo "Page: ";
	// add them all
	for ($i = 1; $i <= $nofpages; $i++) {
	    // is the page being selected?
	    if ($page == $i) {
		// yes. display bold text for it
		printf ("[<b>%s</b>] ", $i);
	    } else {
		// no. create a hyperlink for it
		printf ("[<a href=\"cp_admin.php?action=groups&page=%s\">%s</a>] ", $i, $i);
	    }
	}
 ?></td>
 </tr>
</table><table width="100%" border=0>
<tr>
 <td width="30%"><b>Group Name</b></td>
 <td width="70%"><b>Description</b></td>
</tr>
<?php
	// build the query
	$query = sprintf ("select id,name,description from groups limit %s,%s", $from - 1, GROUPS_PER_PAGE);
	$res = db_query ($query);

	// list all groups
	while ($result = db_fetch_results ($res)) {
	    printf ("<tr><td><a href=\"cp_admin.php?action=editgroup&groupid=%s\">%s</a></td><td>%s</td></tr>", $result[0], $result[1], $result[2]);
	}
 ?></table><p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="addgroup">
<center><input type="submit" value="Add group"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // EditGroup()
    //
    // This will edit a group.
    //
    function
    EditGroup() {
	global $groupid;

	CPShowHeader();

	// grab the group
	$query = sprintf ("select name,description from groups where id=%s", $groupid);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// build the page
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doeditgroup">
<input type="hidden" name="groupid" value="<?php echo $groupid; ?>">
<table width="100%" border=0>
 <tr>
  <td width="20%">Group name</td>
  <td width="80%"><input type="text" name="groupname" size=50 value="<?php echo htmlspecialchars ($result[0]); ?>"></td>
 </tr>
 <tr>
  <td>Description</td>
  <td><input type="text" name="groupdesc" size=50 value="<?php echo htmlspecialchars ($result[1]); ?>"></td>
 </tr>
 <tr>
  <td>Members</td>
  <td><?php
	// grab the group members
	$query = sprintf ("select id,userid from groupmembers where groupid=%s", $groupid);
	$res = db_query ($query);

	// list them all
	while ($result = db_fetch_results ($res)) {
	    printf ("<input type=\"text\" name=\"member[%s]\" value=\"%s\"> ", $result[0], GetMemberName ($result[1]));
	}

	// okay, now add three blank ones
	for ($i = 0; $i < 3; $i++) {
	    printf ("<input type=\"text\" name=\"newmember[%s]\"> ", $i);
	}
 ?></td>
 </tr>
</table><p>
<table width="100%">
 <tr>
  <td width="50%" align="center"><input type="submit" value="Submit Changes"></form></td>
  <td width="50%" align="center"><form action="cp_admin.php" method="post"><input type="hidden" name="action" value="deletegroup"><input type="hidden" name="groupid" value="<?php echo $groupid; ?>"><input type="submit" value="Delete Group"></form></td>
 </tr>
</table>
<?php
	CPShowFooter();
    }

    //
    // DoEditGroup()
    //
    // This will actually edit a group.
    //
    function
    DoEditGroup() {
	global $groupid, $member, $newmember, $groupname, $groupdesc;

	CPShowHeader();

	// does the group still exist?
	$query = sprintf ("select id from groups where id=%s", $groupid);
	if (db_nof_results (db_query ($query)) == 0) {
	    // no. complain
 ?>We're sorry, but this group has appearantly been deleted.
<?php
	    CPShowFooter();
	    exit;
	}

	// update the group record
	$query = sprintf ("update groups set name='%s',description='%s' where id=%s", $groupname, $groupdesc, $groupid);
	db_query ($query);

	// make sure all users listed exist
	while (list (, $username) = each ($member)) {
	    // is this username blank?
	    if ($username != "") {
		// no. look up the username
	        $memberid[$username] = GetMemberID ($username);
	        if ($memberid[$username] == "") {
		    // this failed. complain
 ?>We're sorry, but user <b><?php echo $username; ?></b> does not seem to exist.
<?php
		    CPShowFooter();
		    exit;
		}
	    }
	}

	// check the 'new' list too
	while (list (, $username) = each ($newmember)) {
	    // is this username blank?
	    if ($username != "") {
		// no. look up the username
	        $memberid[$username] = GetMemberID ($username);
	        if ($memberid[$username] == "") {
		    // this failed. complain
 ?>We're sorry, but user <b><?php echo $username; ?></b> does not seem to exist.
<?php
		    CPShowFooter();
		    exit;
		}
	    }
	}

	// now, change the users
	reset ($member);
	while (list ($id, $username) = each ($member)) {
	    // need to get rid of this user?
	    if ($username == "") {
		// yes. do it
		$query = sprintf ("delete from groupmembers where id=%s", $id);
	    } else {
		// no. change the user
		$query = sprintf ("update groupmembers set userid=%s where id=%s and groupid=%s", $memberid[$username], $id, $groupid);
	    }
	    db_query ($query);
	}

	// add all new members, too
	reset ($newmember);
	while (list (, $username) = each ($newmember)) {
	    // is there an actual username here?
	    if ($username != "") {
		// yes. add the member
		$query = sprintf ("insert into groupmembers values (NULL,%s,%s)", $groupid, $memberid[$username]);
		db_query ($query);
	    }
	}

	// yay, this worked. tell the user about it
 ?>Thank you, the group has successfully been updated.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="groups">
<input type="submit" value="Return to group overview">
</form>
<?php

	CPShowFooter();
    }

    //
    // DeleteGroup()
    //
    // This will delete a group.
    //
    function
    DeleteGroup() {
	global $groupid;

	CPShowHeader();

	// get rid of the group
	$query = sprintf ("delete from groups where id=%s", $groupid);
	db_query ($query);
	$query = sprintf ("delete from groupmembers where groupid=%s", $groupid);
	db_query ($query);

 ?>The group has successfully been deleted.
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="groups">
<input type="submit" value="Return to group overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // AddGroup()
    //
    // This will add a group.
    //
    function
    AddGroup() {
	CPShowHeader();

	// build the page
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="doaddgroup">
<table width="100%">
 <tr>
  <td width="20%">Group name</td>
  <td width="80%"><input type="text" name="groupname"></td>
 </tr>
 <tr>
  <td>Group description</td>
  <td><input type="text" name="groupdesc"></td>
 </tr>
 <tr>
  <td>Group members</td>
  <td><?php
    // add the boxes for members
    for ($i = 0; $i < 3; $i++) {
	printf ("<input type=\"text\" name=\"newmember[%s]\"> ", $i);
    }
 ?></td>
 </tr>
</table><p>
<center><input type="submit" value="Add group"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoAddGroup()
    //
    // This will actually add the group.
    //
    function
    DoAddGroup() {
	global $groupname, $groupdesc, $newmember;

	CPShowHeader();

	// build the group names
	while (list (, $username) = each ($newmember)) {
	    // is this account name in use?
	    if ($username != "") {
		// yes. look it up
		$nameid[$username] = GetMemberID ($username);
		if ($nameid[$username] == "") {
		    // this account does not exist. complain
 ?>We're sorry, but user <b><?php echo $username; ?></b> does not seem to exist.
<?php
		    CPShowFooter();
		    exit;
		}
	    }
	}

	// add the group
	$query = sprintf ("insert into groups values (NULL,'%s','%s')",$groupname,$groupdesc);
	db_query ($query);
	$groupid = db_get_insert_id ();

	// add the users
	reset ($newmember);
	while (list (, $username) = @each ($newmember)) {
	    // is this username blank?
	    if ($username != "") {
		// no. add it
	        $query = sprintf ("insert into groupmembers values (NULL,%s,%s)", $groupid, $nameid[$username]);
		db_query ($query);
	    }
	}

	// yay, this worked. tell the user about it
 ?>Thank you, the group has successfully been added.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="groups">
<input type="submit" value="Return to group overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // Smilies()
    //
    // This will edit the smilies.
    //
    function
    Smilies() {
	global $SKIN_VALUE;

	// get all smilies
	$query = sprintf ("select id,smilie,image from smilies");
	$res = db_query ($query);

	// build the page
	CPShowHeader();
 ?><form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="dosmilies">
<table width="100%">
<tr>
 <td width="10%" align="center"><b>Delete?</b></td>
 <td width="30%" align="center"><b>Smilie Text</b></td>
 <td width="30%" align="center"><b>Image</b></td>
 <td width="30%" align="center"><b>Image preview</b></td>
</tr>
<?php
	// list them all
	while (list ($id, $smilie, $img) = db_fetch_results ($res)) {
	    // list the smilie
	    printf ("<tr><td align=\"center\"><input type=\"checkbox\" name=\"delete[%s]\"></td><td align=\"center\"><input type=\"text\" name=\"smilie[%s]\" value=\"%s\"></td><td align=\"center\"><input type=\"text\" name=\"img[%s]\" value=\"%s\"></td><td align=\"center\"><img src=\"%s\" alt=\"[Smilie\"></td></tr>", $id, $id, $smilie, $id, $img, "../" . $SKIN_VALUE["images_url"] . "/" . $img);
	}
 ?></table><p>
<table width="100%">
 <tr>
  <td width="10%" align="center"><b>Add?</b></td>
  <td width="90%"><b>Number of smilies to add</b></td>
 </tr>
 <tr>
  <td align="center"><input type="checkbox" name="add"></td>
  <td><input type="text" name="nofsmilies"></td>
 </tr>
</table>
<center><input type="submit" value="Submit Changes"></center>
</form>
</table>
<?php
	CPShowFooter();
    }

    //
    // DoSmilies()
    //
    // This will actually edit the smilies.
    //
    function
    DoSmilies() {
	global $add, $delete, $smilie, $img, $nofsmilies;

	// get all id's
	$query = sprintf ("select id from smilies");
	$res = db_query ($query);
	while (list ($id) = db_fetch_results ($res)) {
	    // we have a smilie id. does it need to be deleted?
	    if ($delete[$id] != "") {
		// yes. get rid of it
		$query = sprintf ("delete from smilies where id=%s", $id);
		db_query ($query);
	    } else {
		// no. update it
		$query = sprintf ("update smilies set smilie='%s',image='%s' where id=%s", $smilie[$id], $img[$id], $id);
		db_query ($query);
	    }
	}

	// need to add some smilies?
	if ($add != "") {
	    // yes. do it
	    for ($i = 0; $i < $nofsmilies; $i++) {
		// add a smilie
		$query = sprintf ("insert into smilies values (NULL,'','')");
		db_query ($query);
	    }
	}

	// it worked. inform the user
	CPShowHeader();
 ?>The smilie settings have successfully been activated.<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="smilies">
<input type="submit" value="Return to smilie overview">
</form>
<?php
	CPShowFooter();
    }

    // are we an admin?
    if (($GLOBALS["flags"] & FLAG_ADMIN) == 0) {
	// no. complain
	die ("You're not an admin");
    }

    // any action given?
    if ($action == "") {
	// no. show the generic, boring intro
	Intro();
	exit;
    }

    // need to do the accounts?
    if ($action == "accounts") {
	// yup, handle 'em
	Accounts();
	exit;
    }

    // need to actually search for the accounts?
    if ($action == "searchaccounts") {
	// yup, do it
	SearchAccounts();
	exit;
    }

    // need to edit a specific account?
    if ($action == "editaccount") {
	// yup. do it
	EditAccount();	
	exit;
    }

    // need to actually edit a specific account?
    if ($action == "doeditaccount") {
	// yup. do it
	DoEditAccount();
	exit;
    }

    // need to handle the skins?
    if ($action == "skins") {
	// yup. do it
	Skins();
	exit;
    }

    // need to edit the skins?
    if ($action == "editskin") {
	// yup. do it
	EditSkin();
	exit;
    }

    // need to edit a skin template?
    if ($action == "editskintemplate") {
	// yup. do it
	EditSkinTemplate();
	exit;
    }

    // need to actually submit the skin template?
    if ($action == "doeditskintemplate") {
	// yup. do it
	DoEditSkinTemplate();
	exit;
    }

    // need to add a skin template?
    if ($action == "addskintemplate") {
	// yup. do it
	AddSkinTemplate();
	exit;
    }

    // need to actually add the template?
    if ($action == "doaddskintemplate") {
	// yup. do it
	DoAddSkinTemplate();
	exit;
    }

    // need to export this skin?
    if ($action == "exportskin") {
	// yup. do it
	ExportSkin();
	exit;
    }

    // need to import a skin?
    if ($action == "importskin") {
	// yup. do it
	ImportSkin();
	exit;
    }

    // need to actually import the skin?
    if ($action == "doimportskin") {	
	// yup. do it
	DoImportSkin();
	exit;
    }

    // need to delete a skin?
    if ($action == "deleteskin") {
	// yup. do it
	DeleteSkin();
	exit;
    }

    // need to do the global options?
    if ($action == "options") {
	// yup. show the page
	Options();
	exit;
    }

    // need to actually submit the options?
    if ($action == "do_options") {
	// yup. do it
	DoOptions();
	exit;
    }

    // need to edit the skin contants?
    if ($action == "editskinvars") {
	// yup. do it
	EditSkinVars();
	exit;
    }

    // need to edit a skin constant?
    if ($action == "editskinvar") {
	// yup. do it
	EditSkinVar();
	exit;
    }

    // need to edit a skin constant?
    if ($action == "doeditskinvar") {
	// yup. do it
	DoEditSkinVar();
	exit;
    }

    // need to add a skin variable?
    if ($action == "addskinvar") {
	// yup. do it
	AddSkinVar();
	exit;
    }

    // need to actually add a skin variable?
    if ($action == "doaddskinvar") {
	// yup. do it
	DoAddSkinVar();
	exit;
    }

    // need to set a skin as default?
    if ($action == "setskindefault") {
	// yup. do it
	SetSkinDefault();
	exit;
    }

    // need to modify the forums?
    if ($action == "forums") {
	// yup. do it
	Forums();
	exit;
    }

    // need to edit a specific forum?
    if ($action == "editforum") {
	// yes. do it
	EditForum();
	exit;
    }

    // need to actually edit the forum?
    if ($action == "doeditforum") {
	// yes. do it
	DoEditForum();
	exit;
    }

    // need to delete a forum?
    if ($action == "deleteforum") {
	// yes. do it
	DeleteForum();
	exit;
    }

    // need to add a forum?
    if ($action == "addforum") {
	// yup. do it
	AddForum();
	exit;
    }

    // need to actually add the forum?
    if ($action == "doaddforum") {
	// yup. do it
	DoAddForum();
	exit;
    }

    // need to view the categories?
    if ($action == "cats") {
	// yup. do it
	Cats();
	exit;
    }

    // need to actually submit the categories?
    if ($action == "docats") {
	// yup. do it
	DoCats();
	exit;
    }

    // need to actually edit a category?
    if ($action == "editcat") {
	// yup. do it
	EditCat();
	exit;
    }

    // need to actually submit the category changes?
    if ($action == "doeditcat") {
	// yup. do it
	DoEditCat();
	exit;
    }

    // need to add a category?
    if ($action == "addcat") {
	// yup. do it
	AddCat();
	exit;
    }

    // need to actually add a category?
    if ($action == "doaddcat") {
	// yup. do it
	DoAddCat();
	exit;
    }

    // need to handle extra fields?
    if ($action == "extrafields") {
	// yup. do it
	ExtraFields();
	exit;
    }

    // need to actually submit the extra fields?
    if ($action == "doextrafields") {
	// yup. do it
	DoExtraFields();
	exit;
    }

    // need to list the groups?
    if ($action == "groups") {
	// yes. do it
	Groups();
	exit;
    }

    // need to actually edit a group?
    if ($action == "editgroup") {
	// yes. do it
	EditGroup();
	exit;
    }

    // need to actually edit the group?
    if ($action == "doeditgroup") {
	// yes. do it
	DoEditGroup();
	exit;
    }

    // need to actually delete a group?
    if ($action == "deletegroup") {
	// yes. do it
	DeleteGroup();
	exit;
    }

    // need to add a group?
    if ($action == "addgroup") {
	// yes. do it
	AddGroup();
	exit;
    }

    // need to actually edit the group?
    if ($action == "doaddgroup") {
	// yes. do it
	DoAddGroup();
	exit;
    }

    // need to edit the smilies?
    if ($action == "smilies") {
	// yes. do it
	Smilies();
	exit;
    }

    // need to actually edit the smilies?
    if ($action == "dosmilies") {
	// yes. do it
	DoSmilies();
	exit;
    }
 ?>
