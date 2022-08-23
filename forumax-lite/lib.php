<?php
    //
    // lib.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This contains all kinds of useful functions and definitions for
    // ForuMAX-LITE.
    //

    // we need the database information
    require "dbconfig.php";

    // we need our database module, too
    require "db_" . DB_MODULE . ".php";

    // FLAG_ADMIN is the bit you need in order to be an administrator
    define (FLAG_ADMIN, 1);

    // FLAG_THREAD_LOCKED is the bit a thread will have when it's locked
    define (FLAG_THREAD_LOCKED, 1);

    //
    // ShowHeader ($title, $refresh='')
    //
    // This will build and display the forum page header, with title $title.
    // If $refresh is set, the page will be set up to refresh to this location.
    //
    function
    ShowHeader ($title,$refresh='') {
 ?><html><head><title><?php echo $title; ?></title>
<link href="style.css" rel="Stylesheet" type="text/css"></head>
<body>
<table width="100%">
  <tr>
    <td width="60%"><a href="index.php"><img src="images/fmlite.gif" alt="" border=0></a></td>
    <td width="40%" align="center" class="fsmall"><?php
	// logged in?
	if ($GLOBALS["logged_in"] == 1) {
 ?>Logged in as <b><?php echo $GLOBALS["my_accountname"]; ?></b> | <a href="logout.php" class="sml">Log out</a><?php
	} else {
 ?><a href="login.php" class="sml">Log in</a><?php
	}
 ?> | <a href="profile.php" class="sml">Edit profile</a> | <a href="register.php" class="sml">Register</a></td>
  </tr>
</table>
<?php
    }

    //
    // ShowFooter()
    //
    // This will build and display the forum footer.
    //
    function
    ShowFooter() {
	// figure out how long it took us to create this page
	$mtime = explode (" ", microtime());
	$buildtime = sprintf ("%f", ($mtime[1] + $mtime[0]) - $GLOBALS["startime"]);
 ?><p><table width="100%" align="center" border="0">
  <tr>
    <td align="center" class="fsmall">This page was generated in <?php echo $buildtime; ?> seconds<br><br>
    <!-- do not remove this copyright notice! -->
    Powered by <a class="sml" href="http://www.forumax.com">ForuMAX</a> 5.0 - L<small>ITE</small><br>© 1999-2002 <a class="sml" href="http://www.internet-factory.nl">The Internet Factory</a>
    <!-- do not remove this copyright notice! -->
    </td>
  </tr>
</table>
</body></html>
<?php
    }

    //
    // GetAccountName ($id)
    //
    // This will return the name of account $id, or '?' if the account is not
    // known.
    //
    function
    GetAccountName ($id) {
	global $NAME_CACHE;

	// is this name cached?
	if ($NAME_CACHE[$id] != "") {
	    // no. return the cached entry
	    return $NAME_CACHE[$id];
	}

	// query the database for the name
	$query = sprintf ("SELECT name FROM accounts WHERE id=%s", $id);
	$res = db_query ($query); list ($name) = db_fetch_result ($res);

	// got any results?
	if (db_nof_results ($res) == 0) {
	    // no. default to a single question mark
	    $name = "?";
	}

	// add the entry to the cache
	$NAME_CACHE[$id] = $name;

	// return the name
	return $name;
    }

    //
    // Error ($msg)
    //
    // This will display error $msg and exit;
    //
    function
    Error ($msg) {
 ?><center><table width="90%" border=1>
 <tr>
  <td align="center" width="100%"><br><b>Error</b><p><?php echo $msg; ?><p></td>
 </tr>
</table>
<?php
	exit;
    }

    //
    // VerifyPassword ($username, $password)
    //
    // This will verify accountname $username with password $password. It will
    // return 0 on success, 1 on no account and 2 on bad password.
    //
    function
    VerifyPassword ($username, $password) {
	// get the account details
	$query = sprintf ("SELECT id,password,flags FROM accounts WHERE name='%s'", $username);
	$res = db_query ($query)
;	list ($GLOBALS["my_userid"], $my_password, $GLOBALS["my_flags"]) = db_fetch_result ($res);

	// did this yield any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    return 1;
	}

	// is the password correct?
	if ($password != $my_password) {
	    // no. complain
	    return 2;
	}
    }

    //
    // VerifyAccount ($username, $password)
    //
    // This will verify accountname $username with password $password. Any error
    // will cause the forum to exit with an error message.
    //
    function
    VerifyAccount ($username, $password) {
	// verify the details
	$i = VerifyPassword ($username, $password);

	// error?
	if ($i != 0) {
	    // yes. complain
	    if ($i == 1) { Error ("That account does not seem to exist"); }
	    if ($i == 2) { Error ("The password supplied is not correct"); }

	    // unknown error, but still an error
	    Error ("Access denied");
	}

	// yay, it worked.
	$GLOBALS["my_accountname"] = $username;
    }

    //
    // GetAccountStatus($userid, $userflags)
    //
    // This will return return the account status for account $userid with
    // status $userflags.
    //
    function
    GetAccountStatus($userid, $userflags) {
	global $STATUS_CACHE;

	// account already queried?
	if ($STATUS_CACHE[$userid] != "") {
	    // yes. just return the cached entry
	    return $STATUS_CACHE[$userid];
	}

	// is the user an administrator?
	if (($userflags & FLAG_ADMIN) != 0) {
	    // yes. we're an administrator
	    $status = "Administrator";
	} else {
	    // no. moderator perhaps?
	    $query = sprintf ("SELECT id FROM forums WHERE moderator='%s' LIMIT 1", $userid);
	    if (db_nof_results (db_query ($query)) > 0) {
		// yes. we're a moderator
		$status = "Moderator";
	    } else {
		// no. just a normal member then
		$status = "Member";
	    }
	}

	// add the status to the cache
	$STATUS_CACHE[$userid] = $status;

	// return the status
	return $status;
    }

    //
    // IsForumMod ($forumid)
    //
    // This will check whether account ID $GLOBALS["my_userid"] with flags
    // $GLOBALS["my_flags"] has moderator access to $forumid. It will return
    // zero on failure or non-zero on success.
    //
    function
    IsForumMod ($forumid) {
	// is the account an administrator?
	if (($GLOBALS["my_flags"] & FLAG_ADMIN) != 0) {
	    // yes. auto-mod access then
	    return 1;
	}

	// do we have moderator rights for that forum?
	$query = sprintf ("SELECT moderator FROM forums WHERE id=%s", $forumid);
	list ($modid) = db_fetch_result (db_query ($query));
	if ($modid == $GLOBALS["my_userid"]) {
	    // yes. we have access
	    return 1;
	}

	// sorry, no access
	return 0;
    }

    // authentication cookie given?
    if ($auth_cookie != "") {
	// yes. split it
	list ($username, $password) = explode ("|^|", $auth_cookie);

	// correct?
        if (VerifyPassword ($username, $password) == 0) {
	    // yes! we're logged in now
	    $GLOBALS["my_accountname"] = $username;
	    $GLOBALS["my_password"] = $password;
	    $GLOBALS["logged_in"] = 1;
	} else {
	    // zap the cookie
	    SetCookie ("auth_cookie", "", 0);
	}
    }

    // get the time
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $GLOBALS["startime"] = $mtime[1] + $mtime[0];

    // forum title (XXX)
    $GLOBALS["forum_title"] = "Your Forums";
 ?>
