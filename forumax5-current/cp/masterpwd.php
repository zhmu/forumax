<?php 
    //
    // masterpwd.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the master password.
    //

    // we need our library, too
    require "lib.php";

    //
    // Overview()
    //
    // This will show the page for editing the master password.
    //
    function
    Overview() {
	global $CONFIG;

	// show the page for changing the password
	cpShowHeader ("Master Password", "Change Master Password");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="changemail">
This will enable you to change your master password. This password will allow you <b>complete and unrestricted</b> access to the entire ForuMAX control panel, with or without a valid user account. Therefore, it is of uttermost important that you keep this password very secure and extremely hard to guess.<p>
If you need a secure password, click the <b>Generate and email random password</b> button. This will cause the forum to generate a random master password and email it to the forum administrator (currently set to <a href="mailto:<?php echo $CONFIG["admin_email"]; ?>"><?php echo $CONFIG["admin_email"]; ?></a>)<p>
<center><table width="50%" cellspacing=1 cellpadding=3 class="tab5">
  <tr class="tab2">
    <td width="47%" align="right">Old password&nbsp;</td>
    <td width="6%" align="center">&nbsp;</txd>
    <td width="47%" align="left"><input type="password" name="old_pwd"></td>
  </tr>
  <tr class="tab2">
    <td align="right">New password&nbsp;</td>
    <td align="center">&nbsp;</txd>
    <td align="left"><input type="password" name="new_pwd1"></td>
  </tr>
  <tr class="tab2">
    <td align="right">Retype new password&nbsp;</td>
    <td align="center">&nbsp;</txd>
    <td align="left"><input type="password" name="new_pwd2"></td>
  </tr>
</table></center><p>
<table width="100%">
  <tr valign="top">
    <td align="center" width="50%"><input type="submit" value="Change Password"><td>
    <td align="center" width="50%"><input type="submit" name="generatemail" value="Generate and email password"></td>
  </tr>
</table>
</form>
<?php

	cpShowFooter();
    }

    //
    // ChangeMail()
    //
    // This will change the password or generate a new one and email that.
    //
    function
    ChangeMail() {
	global $CONFIG, $ipaddress;

	// show the header
	cpShowHeader ("Master Password", "Change Master Password");

	// fetch the values
	$old_pwd  = $_REQUEST["old_pwd"];
	$new_pwd1 = $_REQUEST["new_pwd1"];
	$new_pwd2 = $_REQUEST["new_pwd2"];
	$generatemail = $_REQUEST["generatemail"];

	// is the currently supplied master password ok?
	if ($CONFIG["master_password"] != md5 ($old_pwd)) {
	    // no. complain
	    print "Sorry, but the master password you typed is <b>NOT</b> correct";
	    cpShowFooter();
	    exit;
	}

	// do we need to generate and email a password?
	if ($generatemail != "") {
	    // yes. is the email address valid?
	    list ($a, $b, $c) = explode ("@", $CONFIG["admin_email"]);
	    if (($a == "") or ($b == "") or ($c != "")) {
		// no. complain
		print "Sorry, but this forum does not have a valid administrator email address. You can change it at the forum options screen.";
		cpShowFooter();
		exit;
	    }

	    // generate a password
	    $tmp = "";
	    for ($i = 0; $i < 10; $i++) {
		// add a random char
		$tmp .= chr (33 + rand (0, 93));
	    }

	    // build the email
	    $subject = "New master password for " . $CONFIG["forum_url"];
	    $body = "Hello,

You've just generated a new master password for the forums at " . $CONFIG["forum_url"] . ". The new master password is:

" . $tmp . "

MAKE SURE you do not lose this password! ForuMAX staff cannot look it up for you. For your information, the request to take this action came from IP address " . $ipaddress . "

ForuMAX";

	    // send the email
	    mail ($CONFIG["admin_email"], $subject, $body, "From: " . $CONFIG["admin_email"]);

	    // ok, looks good. update the password
	    $query = sprintf ("UPDATE config SET content='%s' WHERE name='master_password'", md5 ($tmp));
	    db_query ($query);

	    // inform the user
	    print "A new master password has <b>SUCCESSFULLY</b> been generated and emailed!";
	} else {
	    // no. are the passwords equal?
	    if ($new_pwd1 != $new_pwd2) {
		// no. complain
		print "Sorry, but the two new passwords aren't equal";
		cpShowFooter();
		exit;
	    }

	    // are they long enough?
	    if (strlen ($new_pwd1) < 6) {
		// no. complain
		print "Sorry, but your password is not long enough. It must be at least 7 charachters";
		cpShowFooter();
		exit;
	    } 

	    // ok, looks good. update the password
	    $query = sprintf ("UPDATE config SET content='%s' WHERE name='master_password'", md5 ($new_pwd1));
	    db_query ($query);

	    // build the email
	    $subject = "New master password for " . $CONFIG["forum_url"];
	    $body = "Hello,

You've just changed your master password for the forums at " . $CONFIG["forum_url"] . ". The new master password is:

" . $new_pwd1 . "

MAKE SURE you do not lose this password! ForuMAX staff cannot look it up for you. For your information, the request to take this action came from IP address " . $ipaddress . "

ForuMAX.com";

	    // send the email
	    mail ($CONFIG["admin_email"], $subject, $body, "From: " . $CONFIG["admin_email"]);

	    // ok, this worked. inform the user
	    print "The master password has <b>SUCCESSFULLY</b> been updated!";
	}

	cpShowFooter();
    }

    // seed the random generator
    srand ((double)microtime() * 1000000);

    // verify the rights
    cpVerifyAccess (CPOPTION_MASTERPWD);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // is an action given?
    if ($action == "") {
	// no. show the overview
	Overview();
    } elseif ($action == "changemail") {
	// change or email the password
	ChangeMail();
    }
 ?>
