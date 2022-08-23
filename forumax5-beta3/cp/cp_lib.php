<?php
    // we need the database settings!
    require "../dbconfig.php";

    // we need the database module as well
    require "../" . $GLOBALS["db_mod"];

    // we need the generic library too
    require "../lib.php";

    // $master_actions[$action] = "desc" are all master actions
    $master_actions["cp_master.php?action=update"] = "Update ForuMAX";
    $master_actions["cp_master.php?action=password"] = "Master Password";

    // $admin_actions[$action] = "desc" are all admin cp actions we know
    $admin_actions["cp_admin.php?action=accounts"] = "Accounts";
    $admin_actions["cp_admin.php?action=groups"] = "Groups";
    $admin_actions["cp_admin.php?action=forums"] = "Forums";
    $admin_actions["cp_admin.php?action=cats"] = "Categories";
    $admin_actions["cp_admin.php?action=options"] = "Options";
    $admin_actions["cp_admin.php?action=skins"] = "Skins";
    $admin_actions["cp_admin.php?action=extrafields"] = "Extra Fields";
    $admin_actions["cp_admin.php?action=smilies"] = "Smilies";

    // $mod_actions[$action] = "desc" are all moderator cp actions we know
    $mod_actions["cp_mod.php?action=announcements"] = "Announcements";
    $mod_actions["cp_mod.php?action=prune"] = "Prune";

    $mod_actions["cp_mod.php?action=destroycookie"] = "Logout";

    // do we have an username/password pair?
    if ((($username . $password != "") or ($master_pw != ""))) {
	// yes. set the id cookie
	$cp_authid = $username . ":" . $password . ":" . md5 ($master_pw);
	$cookie = 0;
    } else {
	$cookie = 1;
    }

    // is the user currently logged in?
    if ($cp_authid != "") {
	// yes. is this username/password pair valid?
	$ok = 0;
	$idcookie = explode (":", $cp_authid);

	// do we have a master password?
	if ($idcookie[2] != md5 ("")) {
	    // yes. get the master password from the database
	    $query = sprintf ("select content from config where name='master_password'");
	    $result = db_fetch_results (db_query ($query));

	    // is the master password correct?
	    if ($idcookie[2] != $result[0]) {
		// not quite. destroy the master password part
	        $cp_authid = $username . ":" . $password;
	    } else {
		// the master password is ok
	        $GLOBALS["username"] = "Master";
	        $GLOBALS["userid"] = 0;
	        $GLOBALS["flags"] = FLAG_ADMIN | FLAG_MASTER;
		$ok = 1;
	    }
	}

	// was the master password ok?
	if ($ok == 0) {
	    // no. verify the username/password pair
	    $query = sprintf ("select password,flags,id from accounts where accountname='%s'", $idcookie[0]);
	    $res = db_query ($query); $result = db_fetch_results ($res);

	    // is the username/password pair ok?
	    if ((db_nof_results ($res) == 0) or ($idcookie[1] != $result[0])) {
	        // no, not quite. destroy the cookie
	        $cp_authid = "";

	        // show an error message
 ?>
<b>We're terribly sorry, but this username/password combination is not correct.</b><p>
<?php
	    } else {
	        // yes, it's perfectly valid. is this account enabled?
		if (($result[1] & FLAG_DISABLED) == 0) {
		    // yes. set the username and flags global fields. always get rid
		    // of the FLAG_MASTER flag.
	            $GLOBALS["username"] = $idcookie[0];
	            $GLOBALS["flags"] = $result[1] & (~FLAG_MASTER);
	            $GLOBALS["userid"] = $result[2];
		    $ok = 1;
		}
	    }
	}
    }

    // do we have to set the cookie?
    if (($cookie == 0) and ($cp_authid != "")) {
	// yes. do it
	setcookie ("cp_authid", $cp_authid, time() + 3600);
    }

    // do we need to show the 'login' page?
    if ($cp_authid == "") {
	// yes. do it
 ?>
Before you can use this control panel, you must identificate yourself. Please fill in the username and password boxes below. If you know it, you may also use hte Master Password, which will also grant you access<p>
<form action="index.php" method="post">
<table>
 <tr>
  <td>Username</td>
  <td><input type="text" name="username"></td>
 </tr>
 <tr>
  <td>Password</td>
  <td><input type="password" name="password"></td>
 </tr>
 <tr>
  <td>Master Password<br>(<i>not required</i>)</td>
  <td><input type="password" name="master_pw"></td>
 </tr>
</table><p>
<input type="submit" value="OK">
</form>
<?php
	exit;
    }

    //
    // CPShowHeader()
    //
    // This will build the page header and show it.
    //
    function
    CPShowHeader() {
 ?><title>ForuMAX control panel</title>
</head><body text="#ffffff" link="#ffff00" vlink="#ffff00" alink="#ffff00">
<table width="100%" border=1 bgcolor="#4a6ea5" cellspacing=1 cellpadding=1>
<tr>
  <td width="20%" align="center" valign="top"><br><?php
    global $admin_actions, $mod_actions, $master_actions;

    // are we an admin?
    if (($GLOBALS["flags"] & FLAG_ADMIN) != 0) {
	// yes. we can do any action we like
	$actions = $admin_actions;

	// are we a master?
        if (($GLOBALS["flags"] & FLAG_MASTER) != 0) {
	    // yes. append the master actions
	    while (list ($name, $value) = each ($master_actions)) {
	        $actions[$name] = $value;
	    }
	}

	// append the moderator actions
	while (list ($name, $value) = each ($mod_actions)) {
	    $actions[$name] = $value;
	}
    } else {
	// no. we need to stick to mod-only actions now
	$actions = $mod_actions;
    }

    // add all steps
    while (list ($theaction, $thedesc) = each ($actions)) {
	print "<a href=\"$theaction\">$thedesc</a><p>";
    }?>
  <td width="90%"><center><font size=5><b>ForuMAX Control Panel</b></font></center><p>
<?php
    }

    //
    // CPShowFooter()
    //
    // This will build the page footer and show it.
    //
    function
    CPShowFooter() {
 ?>
</td></tr>
</table><p>
<center><font color="#000000" size=1>ForuMAX is &copy; 1999-2001 Rink Springer</font></center>
</body></html>
<?php
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
 ?>
