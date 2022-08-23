<?php
    //
    // lib.php
    //
    // (c) 200002002 NextFuture, www.next-future.nl
    //
    // This is the control panel library. It contains a lot of useful functions
    // that are needed for the control panel.
    //

    // we need our database setting
    require "../dbconfig.php";

    // we need our database module too
    require "../" . $GLOBALS["db_mod"];

    // set a flag indicating this is the control panel (and thus can't be
    // closed)
    $GLOBALS["in_control_panel"] = "yes";

    // the general library would be useful too
    require "../lib.php";

    // RIGHT_ADMIN and RIGHT_MOD are the rights you need for each control panel
    // task.
    define (RIGHT_ADMIN, 1);
    define (RIGHT_MOD, 2);
    define (RIGHT_MEGAMOD, 4);

    // CPOPTION_xxx are the control panel rights
    define (CPOPTION_ACCOUNTS, 1);
    define (CPOPTION_GROUPS, 2);
    define (CPOPTION_FORUMS, 3);
    define (CPOPTION_CATEGORIES, 4);
    define (CPOPTION_OPTIONS, 5);
    define (CPOPTION_SKINS, 6); 
    define (CPOPTION_FIELDS, 7);
    define (CPOPTION_SMILIES, 8);
    define (CPOPTION_AVATARS, 9); 
    define (CPOPTION_UPDATE, 10);
    define (CPOPTION_MASTERPWD, 11);
    define (CPOPTION_ANNOUNCE, 12);
    define (CPOPTION_PRUNE, 13);
    define (CPOPTION_ACCESS, 14);
    define (CPOPTION_ARCHIVES, 15);

    // CP_MENU[$option] = "file:description" are the control panel menu options
    $CP_MENU[CPOPTION_ACCESS] = "access.php:Access Control";
    $CP_MENU[CPOPTION_ACCOUNTS] = "accounts.php:Accounts";
    $CP_MENU[CPOPTION_ANNOUNCE] = "announce.php:Announcements";
    $CP_MENU[CPOPTION_ARCHIVES] = "archives.php:Archives";
    $CP_MENU[CPOPTION_AVATARS] = "avatars.php:Avatars";
    $CP_MENU[CPOPTION_CATEGORIES] = "cats.php:Categories";
    $CP_MENU[CPOPTION_FIELDS] = "fields.php:Extra Fields";
    $CP_MENU[CPOPTION_FORUMS] = "forums.php:Forums";
    $CP_MENU[CPOPTION_GROUPS] = "groups.php:Groups";
    $CP_MENU[CPOPTION_MASTERPWD] = "masterpwd.php:Master Password";
    $CP_MENU[CPOPTION_OPTIONS] = "options.php:Options";
    $CP_MENU[CPOPTION_PRUNE] = "prune.php:Prune";
    $CP_MENU[CPOPTION_SKINS] = "skins.php:Skins";
    $CP_MENU[CPOPTION_SMILIES] = "smilies.php:Smilies";
    $CP_MENU[CPOPTION_UPDATE] = "update.php:Update Forum";

    //
    // cpCheckAccess ($option)
    //
    // This will check control panel access to option $option. It will return
    // zero on failure or non-zero on success.
    //
    function
    cpCheckAccess ($option) {
	global $CP_ACCESS;

	// are we a master?
	if ($GLOBALS["MASTER_ACCESS"] != 0) {
	    // yes. we have access to anything	
	    return 1;
	}

	// can a moderator use this option?
	if (($CP_ACCESS[$option] & RIGHT_MOD) != 0) {
	    // yes. are we a moderator?
	    if ($GLOBALS["MOD_ACCESS"] != 0) {
		// yes, we have the rights
		return 1;
	    }
	}

	// can a mega moderator use this option?
	if (($CP_ACCESS[$option] & RIGHT_MEGAMOD) != 0) {
	    // yes. are we a moderator?
	    if ($GLOBALS["MMOD_ACCESS"] != 0) {
		// yes, we have the rights
		return 1;
	    }
	}

	// can an administrator use this option?
	if (($CP_ACCESS[$option] & RIGHT_ADMIN) != 0) {
	    // yes. do we have administrator rights?
	    if ($GLOBALS["ADMIN_ACCESS"] != 0) {
		// yes. we have access then
		return 1;
	    }
	}

	// access denied
	return 0;
    }

    //
    // cpVerifyAccess ($option)
    //
    // This will verify whether the user holds access right $option. If not,
    // an error will be shown and the script will quit, otherwise script
    // execution will continue.
    //
    function
    cpVerifyAccess ($option) {
	// do we have access?
	if (cpCheckAccess ($option) != 0) {
	    // yes. just leave
	    return;
	}

	// access is denied.
	cpShowHeader();
 ?><table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab2">
    <td width="100%" align="center"><br><b>We're sorry, but you do not have enough rights to access this part of the control panel</b><p></td>
  </tr>
</table><p>
<form action="index.php" method="post">
<center><input type="submit" value="Return to the control panel"></center>
</form>
<?php
	cpShowFooter();
	exit;
    }

    //
    // cpShowHeader ($section='',$subsection='')
    //
    // This will show the control panel page, in which $section is the current
    // section and $subsection the subsection.
    //
    function
    cpShowHeader ($section='',$subsection='') {
	global $CP_ACCESS, $CP_MENU;

	// what's our current level of access?
	if ($GLOBALS["MASTER_ACCESS"] != 0) {
	    // it's master access... yay!
	    $title = "Control panel for masters";
	} elseif ($GLOBALS["ADMIN_ACCESS"] != 0) {
	    // it's administrative access... yay!
	    $title = "Control panel for administrators";
	} elseif ($GLOBALS["MMOD_ACCESS"] != 0) {
	    // it's mega moderator access... yay!
	    $title = "Control panel for mega moderators";
	} else {
	    // it's moderator access... yay!
	    $title = "Control panel for moderators";
	}

	// create the 'where are you' line
	$where = $title;

	// do we have a section?
	if ($section != "") {
	    // yes. show it
	    $where .= sprintf (" &gt; <a href=\"%s\">%s</a>", $_SERVER["PHP_SELF"], $section);
	    $title .= " &gt; " . $section;

	    // do we have a subsection?
	    if ($subsection != "") {
		// yes. show it too
		$where .= sprintf (" &gt; <b>%s</b>", $subsection);
		$title .= " &gt; " . $subsection;
	    }
	}

	// build the layout
 ?><html><head><title><?php echo $title; ?></title>
<link href="style.css" rel="Stylesheet" type="text/css">
</head><body><table width="100%" cellspacing=0 cellpadding=0>
  <tr bgcolor="#ff0000">
    <td width="2%">&nbsp;</td>
    <td width="70%" class="text"><b>ForuMAX &gt; <?php echo $where; ?></b></td>
    <td width="26%" align="right" class="text"><small>Logged in as <?php echo $GLOBALS["cp_accountname"]; ?> - [<a href="index.php?action=logout"><small><b>Logout</b></small></a>]</small></td>
    <td width="2%" align="center">&nbsp;</td>
  </tr>
</table><table width="100%" cellpadding=0 cellspacing=0>
 <tr>
  <td valign="top" width="15%" align="center" bgcolor="#000000"><font color="#ffffff"><br><?php
	// browse them all
	while (list ($option, $tmp) = each ($CP_MENU)) {
	    // split the $tmp thing
	    list ($file, $desc) = explode (":", $tmp);

	    // do we have rights to do this?
	    if (cpCheckAccess ($option) != 0) {
		// yes. show it
		printf ("<a href=\"%s\"><b>%s</b></a><p>", $file, $desc);
	    }
	}
    ?><p></font></td>
  <td width="80%">
    <table width="100%">
      <tr>
	<td width="1%">&nbsp;</td>
	<td width="98%" class="text">
<?php
    }

    //
    // cpShowFooter()
    //
    // This will show the control panel footer.
    //
    function
    cpShowFooter() {
 ?>	</td>
	<td width="1%">&nbsp;</td>
      </tr>
    </table>
   </td>
 </tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0>
  <tr bgcolor="#ff0000">
    <td width="2%">&nbsp;</td>
    <td width="96%" align="center"><font size=2 color="#ffffff">Powered by <a href="http://www.forumax.com"><b>ForuMAX <?php echo FORUMAX_VERSION; ?></b></a> -  &copy; 1999-2002 <a href="http://www.next-future.nl">Next<i>Future</i></a></font></td>
    <td width="2%" align="center">&nbsp;</td>
  </tr>
</table>
</body></html>
<?php
    }

    //
    // RequestAuth($msg = '')
    //
    // This will request control panel authentication. It will display message
    // $msg if it is supplied in red. If the authentication cookie is present,
    // it will be deleted.
    //
    function
    RequestAuth($msg='') {
	// do we have a cookie ?
	if ($_COOKIE["cp_authcookie"] != "") {
	    // yes. zap it
	    SetCookie ("cp_authcookie", "", 0);
	}

 ?><html><head><title>ForuMAX Control Panel</title></head>
<link href="style.css" rel="Stylesheet" type="text/css">
<body>
<table width="100%">
  <tr>
    <td width="10%">&nbsp;</td>
    <td width="80%"><table width="100%" border=0><tr><td width="100%" align="center"><br><?php
	// got a message to display?
	if ($msg != "") {
	    // yes. show it
 ?><table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab3">
    <td align="center" class="fheading"><font size=4 color="#ff0000"><br><b><?php echo $msg; ?></b></font><p></td>
  </tr>
</table>
<?php
	}
 ?><form action="<?php echo $PHP_SELF; ?>" method="post">
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab3">
    <td colspan="2" align="center" class="fheading">Control Panel - Log in</td>
  </tr>
  <tr class="tab2">
    <td colspan="2" align="center" class="fnormal"><br>Before you can use this control panel, you have to authenticate yourself.<br>Please type your username and password in the boxes below.<br>You may also type the master password, it will give you access as well.<p></td>
  </tr>
  <tr class="tab2">
    <td width="50%" align="right" class="fnormal"><b>Username</b>&nbsp;</td>
    <td width="50%" align="left"><input class="textbox" type="text" name="cp_username"></td>
  </tr>
  <tr class="tab2">
    <td align="right" class="fnormal"><b>Password</b>&nbsp;</td>
    <td align="left"><input class="textbox" type="password" name="cp_password"></td>
  </tr>
  <tr class="tab2">
    <td align="right" class="fnormal"><b>Master Password</b>&nbsp;</td>
    <td align="left"><input class="textbox" type="password" name="cp_masterpassword"></td>
  </tr>
  <tr class="tab2">
    <td colspan="2" align="center"><br><input class="bttn1" type="submit" value="Log On"></form><p></td>
  </tr>
</table></td>
 </tr>
</table>
    <td width="10%">&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php
    }

    //
    // CheckAuth()
    //
    // This will confirm the control panel access or bug the user for a correct
    // username and password pair.
    //
    function
    CheckAuth() {
	// no access by default :)
	$GLOBALS["MASTER_ACCESS"] = 0; $GLOBALS["ADMIN_ACCESS"] = 0;
	$GLOBALS["MOD_ACCESS"] = 0; $GLOBALS["MMOD_ACCESS"] = 0;

	// fetch the arguments
	$cp_username = $_REQUEST["cp_username"];
	$cp_password = $_REQUEST["cp_password"];
	$cp_masterpassword = $_REQUEST["cp_masterpassword"];
	$cp_authcookie = $_COOKIE["cp_authcookie"];

	// do we have a cookie?
	if ($cp_authcookie != "") {
	    // yes. split it
	    list ($cp_username, $cp_password, $cp_masterpassword) = explode (":", $cp_authcookie);
	}

	// is an username/password string given?
	if (($cp_username != "") and ($cp_password != "") or ($cp_masterpassword != "")) {
	    // yes. is the master password given?
	    if ($cp_masterpassword != "") {
		// yes. is it correct?
		$query = sprintf ("SELECT content FROM config WHERE name='master_password'");
		list ($tmp) = db_fetch_results (db_query ($query));

		// is the master password correct?
		if (md5 ($cp_masterpassword) == $tmp) {
		    // yes! we've got access now.
		    $GLOBALS["MASTER_ACCESS"] = 1; $GLOBALS["ADMIN_ACCESS"] = 1;
		    $GLOBALS["MOD_ACCESS"] = 1; $GLOBALS["MMOD_ACCESS"] = 1;
		    $GLOBALS["cp_accountid"] = 0;
		    $GLOBALS["cp_accountname"] = "<b>Master</b>";

		    // do we have a cookie ?
		    if ($cp_authcookie == "") {
			// no. create one
			SetCookie ("cp_authcookie", $cp_username . ":" . $cp_password . ":" . $cp_masterpassword, time() + 7200);
		    }
		    return;
		}
	    }

	    // check the password and flags combination
	    $query = sprintf ("SELECT id,password,flags FROM accounts WHERE accountname='%s'", $cp_username);
	    $res = db_query ($query);

	    // got any results?
	    if (db_nof_results ($res) == 0) {
		// no. request authentication
		RequestAuth ("Access denied");
		exit;
	    }

	    // grab the information
	    list ($GLOBALS["cp_accountid"], $tmp, $GLOBALS["cp_accountflags"]) = db_fetch_results ($res);

	    // is the password correct?
	    if ($tmp != $cp_password) {
		// no. request authentication
		RequestAuth ("Access denied");
		exit;
	    }

	    // fix up the account name
	    $GLOBALS["cp_accountname"] = $cp_username;

 	    // do we have a cookie ?
	    if ($cp_authcookie == "") {
		// no. create one
		SetCookie ("cp_authcookie", $cp_username . ":" . $cp_password . ":" . $cp_masterpassword, time() + 7200);
	    }

	    // are we an administrator?
	    if (($GLOBALS["cp_accountflags"] & FLAG_ADMIN) != 0) {
		// yes. we have access
		$GLOBALS["ADMIN_ACCESS"] = 1; $GLOBALS["MOD_ACCESS"] = 1;
		$GLOBALS["MMOD_ACCESS"] = 1;
		return;
	    }

	    // mega moderator?
	    if (($GLOBALS["cp_accountflags"] & FLAG_MMOD) != 0) {
		// yes. we have access
		$GLOBALS["MOD_ACCESS"] = 1; $GLOBALS["MMOD_ACCESS"] = 1;
		return;
	    }

	    // are we a moderator, perhaps?
	    if ((IsMod ($GLOBALS["cp_accountid"]) != 0) or (IsCategoryMod ($GLOBALS["cp_accountid"]) != 0)) {
		// yes. we have access
		$GLOBALS["MOD_ACCESS"] = 1;
		return;
	    }

	    // not enough rights
	    RequestAuth("Permission denied");
	    exit;
	}

	// no access. request for it
	RequestAuth();
	exit;
    }

    //
    // BuildUserGroupString ($userid, $flags)
    //
    // This will build an string indicating an user or group, according to
    // userid $userid and flags $flags. If an entry could not be found, a blank
    // string will be returned.
    //
    function
    BuildUserGroupString ($userid, $flags) {
	// do we have a blank user id?
	if ($userid == "" ) {
	    // yes. this always fails
	    return "";
	}

	// is this a group?
	if (($flags & FLAG_USERLIST_GROUP) == 0) {
	    // no. return the moderator name
	    return GetMemberNameSimple ($userid);
	}

	// grab the group name
	$groupname = GetGroupNameSimple ($userid);

	// got any results?
	if ($groupname != "") {
	    // yes. return the entry
	    return "@" . $groupname;
	}

	// no. return a blank string
	return "";
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
		printf ("<input type=\"text\" name=\"%s[%s]\" value=\"%s\"> ",$fieldname,$result[0], htmlspecialchars ($name));
	    }
	}
        printf ("<input type=\"text\" name=\"new%s[0]\">",$fieldname);
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
			cpShowFooter();
			exit;
		    }
		} else {
		    // no. grab the new user id
		    $objectid = GetMemberID ($name); $flags = 0;
	
		    // did this work?
		    if ($objectid == "") {
			print "User <b>" . $name . "</b> does not exist";
			cpShowFooter();
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
    // BuildForumList ($cursel)
    //
    // This will build the actual forum list you can modify. $cursel will be
    // selected as default choice.
    //
    function
    BuildForumList($cursel) {
	// are we an admin, megamod or master?
	if (($GLOBALS["ADMIN_ACCESS"] != 0) or (($GLOBALS["cp_accountflags"] & FLAG_MMOD) != 0) or ($GLOBALS["MASTER_ACCESS"] != 0)) {
	    // yes. add all forums
	    $query = sprintf ("SELECT id,name FROM forums");
	    $res = db_query ($query);
	    while ($tmp = db_fetch_results ($res)) {
		// add the entry
	        printf ("<option value=\"%s\"", $tmp[0]);
		if ($tmp[0] == $cursel) { print " selected"; };
		printf (">%s</option>", $tmp[1]);
	    }

	    // and the special 'all forums' entry too
	    printf ("<option value=\"0\"");
	    if ($cursel == 0) { print " selected"; };
	    printf (">All forums</option>");
	} else {
	    // no. add only the forums we have moderator rights in
	    $tmp = GetForumsModded ($GLOBALS["cp_accountid"]);

	    // add all forums
	    while (list ($forumid) = @each ($tmp)) {
	        // get the forum name
	        $query = sprintf ("SELECT name FROM forums WHERE id=%s", $forumid);
	        $res = db_query ($query); $result = db_fetch_results ($res);

	        // did we have any results?
	        if (db_nof_results ($res) != 0) {
		    // yes. add the entry
	            printf ("<option value=\"%s\"", $forumid);
		    if ($forumid == $cursel) { echo " selected"; };
		    printf (">%s</option>", $result[0]);
	        }
	    }
	}
 ?></select><?php
    }

    // verify our access
    CheckAuth();

    // fetch the access list
    $query = sprintf ("SELECT cp_option,access FROM cp_access");
    $res = db_query ($query);

    // grab them all
    while (list ($cp_option, $access) = db_fetch_results ($res)) {
	// put it in the array
	$CP_ACCESS[$cp_option] = $access;
    }
 ?>
