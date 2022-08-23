<?php 
    //
    // access.php
    //
    // (c) 2000-2002 Next-Future, www.next-future.nl
    //
    // This will handle control panel access.
    //

    // we need our library, too
    require "lib.php";

    //
    // Overview()
    //
    // This will show the overview of access options.
    //
    function
    Overview() {
	global $CP_MENU, $CONFIG;

	// build the page
	cpShowHeader ("Access Maintenance", "Overview");
 ?>ForuMAX 5.0's control panel can be configured to restrict access of all settings you see in the left sidebar. You can control whether moderators, administrators or the both of them will be allowed access. The Forum Master will always have access to everything.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="submit">
<table width="100%" bgcolor="#ffffff" border=0 cellpadding=1 cellspacing=1 class="tab5">
  <tr class="tab3">
    <td width="25%">&nbsp;</td>
    <td width="25%" align="center" class="text"><b>Administrator Access</b></td>
    <td width="25%" align="center" class="text"><b>Moderator Access</b></td>
    <td width="25%" align="center" class="text"><b>Mega Moderator Access</b></td>
  </tr>
<?php
	// list all options we know
	reset ($CP_MENU);
	while (list ($id, $tmp) = each ($CP_MENU)) {
	    // grab the information
	    list (, $desc) = explode (":", $tmp);

	    // grab the access information
	    $query = sprintf ("SELECT access FROM cp_access WHERE cp_option='%s'", $id);
	    list ($access) = db_fetch_results (db_query ($query));

	    // figure out the status
	    $admin_ok = ($access & RIGHT_ADMIN) != 0 ? "yes" : "no";
	    $mod_ok = ($access & RIGHT_MOD) != 0 ? "yes" : "no";
	    $mmod_ok = ($access & RIGHT_MEGAMOD) != 0 ? "yes" : "no";

	    // list the entry
	    printf ("<tr class=\"tab2\"><td class=\"text\">&nbsp;<b>%s</b></td><td align=\"center\" class=\"text\"><input type=\"checkbox\" name=\"admin_access[%s]\"%s>Grant access</input></td><td align=\"center\" class=\"text\"><input type=\"checkbox\" name=\"mod_access[%s]\"%s>Grant access</input></td><td align=\"center\" class=\"text\"><input type=\"checkbox\" name=\"mmod_access[%s]\"%s>Grant access</td></tr>", $desc, $id, ($admin_ok == "yes") ? " checked" : "", $id, ($mod_ok == "yes") ? " checked" : "", $id, ($mmod_ok == "yes") ? "checked" : "");
	}
 ?></table><p>
<center><b>Warning: Submitting these options will cause the forum administrator (<a href="mailto:<?php echo $CONFIG["admin_email"]; ?>"><?php echo $CONFIG["admin_email"]; ?></a>) to be notified!</b><p>
<input type="submit" value="Submit Modifications"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // Submit()
    //
    // This will handle submission of the forum options.
    //
    function
    Submit() {
	global $CP_MENU, $CONFIG, $ipaddress;

	// show the header
	cpShowHeader ("Access Maintenance", "Settings Changed");

	// go for it
	reset ($CP_MENU);
	while (list ($id, $tmp) = each ($CP_MENU)) {
	    // grab the information
	    list (, $desc) = explode (":", $tmp);

	    // build the access mask
	    $mask = 0;
	    if ($_REQUEST["admin_access"][$id] != "") { $mask = $mask | RIGHT_ADMIN; };
	    if ($_REQUEST["mod_access"][$id] != "") { $mask = $mask | RIGHT_MOD; };
	    if ($_REQUEST["mmod_access"][$id] != "") { $mask = $mask | RIGHT_MEGAMOD; };

	    // update the access level
	    $query = sprintf ("UPDATE cp_access SET access='%s' WHERE cp_option='%s'", $mask, $id);
	    db_query ($query);

	    // create a log for the admin email
	    $priv = "";
	    if ($_REQUEST["admin_access"][$id] != "") { $mask = $mask | RIGHT_ADMIN; $priv = "Administrator"; };
	    if ($_REQUEST["mod_access"][$id] != "") {
		if ($priv != "") {
		    $priv .= ", ";
		}
		$priv .= "Moderator";
	    }
	    if ($_REQUEST["mmod_access"][$id] != "") {
		if ($priv != "") {
		    $priv .= ", ";
		}
		$priv .= "Mega Moderator";
	    }
	    if ($priv == "") { $priv = "Forum master only"; };
	    $log .= sprintf ("%s: %s\n", $desc, $priv);
	}

	// carve up the email
	$subject = "New access levels for " . $CONFIG["forum_url"];
	$body = "Hello,

New settings for forum access levels have just been submitted. The request to do this came from " . $ipaddress . ". They have been changed to:

" . $log . "

Thank you,
ForuMAX";

	// send the email
	mail ($CONFIG["admin_email"], $subject, $body, "From: " . $CONFIG["admin_email"]);

	// it worked. inform the user
 ?>Thank you, the access control options have successfully been updated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to access overview">
</form>
<?php

	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_ACCESS);

    // grab the action
    $action = trim ($_REQUEST["action"]);

    // need to show the overview?
    if ($action == "") {
	// yes. do it
	Overview();
    } elseif ($action == "submit") {
	// submit the changes
	Submit();
    }
 ?>
