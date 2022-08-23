<?php 
    //
    // groups.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the editing of groups.
    //

    // we need our library, too
    require "lib.php";

    // GROUPS_PER_PAGE will indicate how much groups we will list per page
    define (GROUPS_PER_PAGE, 20);

    //
    // Overview()
    //
    // This will show the group overview.
    //
    function
    Overview() {
	// show the header
	cpShowHeader("Group Maintenance", "Group overview");

	// figure out the total number of groups
	$query = sprintf ("SELECT COUNT(id) FROM groups");
	list ($totalgroups) = db_fetch_results (db_query ($query));

	// calculate the number of pages
	$nofpages = floor ($totalgroups / GROUPS_PER_PAGE);
	if (($nofpages * GROUPS_PER_PAGE) != $totalgroups) { $nofpages++; };

	// was a page actually given?
	$page = preg_replace ("/\D/", "", $_REQUEST["page"]);
	if ($page == "") {
	    // no. default to the first one
	    $page = 1;
	}

	// build the from and to numbers
	$from = ($page - 1) * GROUPS_PER_PAGE + 1;
	$to = $from + GROUPS_PER_PAGE;
	if ($to > $nofpages) { $to = $nofpages; };

	// build the page
 ?><table width="100%" cellspacing="2" cellpadding="3" border="0" class="tab1">
<tr>
  <td colspan=2><small><?php
	// handle the page[] thingies
	echo "<small>Page: ";

	// add them all
	for ($i = 1; $i <= $nofpages; $i++) {
	    // is the page being selected?
	    if ($page == $i) {
		// yes. display bold text for it
		printf ("[<b>%s</b>] ", $i);
	    } else {
		// no. create a hyperlink for it
		printf ("[<a class=\"sml\" href=\"%s?page=%s\">%s</a>] ", $_SERVER["PHP_SELF"], $i, $i);
	    }
	}
 ?></td>
 </tr>
 <tr>
  <td class="tab3" width="30%"><b>Group Name</b></td>
  <td class="tab3" width="70%"><b>Description</b></td>
 </tr>
<?php
	// build the query
	$query = sprintf ("SELECT id,name,description FROM groups LIMIT %s,%s", $from - 1, GROUPS_PER_PAGE);
	$res = db_query ($query);

	// list all groups
	while (list ($id, $name, $desc) = db_fetch_results ($res)) {
	    printf ("<tr class=\"tab2\"><td class=\"tn\"><a href=\"%s?action=edit&id=%s\">%s</a></td><td class=\"tn\">%s</td></tr>", $_SERVER["PHP_SELF"], $id, $name, $desc);
	}
 ?></table><p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="add">
<center><input type="submit" value="Add group"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will edit a group.
    //
    function
    Edit() {
	// show the header
	cpShowHeader("Group Maintenance", "Edit group");

	// fetch the group id
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// grab the group
	$query = sprintf ("SELECT name,description from groups WHERE id='%s'", $id);
	list ($name, $desc) = db_fetch_results (db_query ($query));

	// build the page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
 <tr class="tab2">
  <td width="20%"><b>Group name</b></td>
  <td width="80%"><input type="text" name="groupname" size=50 value="<?php echo htmlspecialchars ($name); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Description</b></td>
  <td><input type="text" name="groupdesc" size=50 value="<?php echo htmlspecialchars ($desc); ?>"></td>
 </tr>
 <tr class="tab2">
  <td><b>Members</b></td>
  <td><?php
	// grab the group members
	$query = sprintf ("SELECT id,userid FROM groupmembers WHERE groupid='%s'", $id);
	$res = db_query ($query);

	// list them all
	while (list ($memberid, $userid) = db_fetch_results ($res)) {
	    printf ("<input type=\"text\" name=\"member[%s]\" value=\"%s\"> ", $memberid, GetMemberName ($userid));
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
  <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="submit" value="Delete Group"></form></td>
 </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually edit a group.
    //
    function
    DoEdit() {
	// show the header
	cpShowHeader("Group Maintenance", "Edit group");

	// fetch the values
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);
	$groupname = $_REQUEST["groupname"];
	$groupdesc = $_REQUEST["groupdesc"];

	// does the group still exist?
	$query = sprintf ("SELECT id FROM groups WHERE id='%s'", $id);
	if (db_nof_results (db_query ($query)) == 0) {
	    // no. complain
 ?>We're sorry, but this group has appearantly been deleted.
<?php
	    cpShowFooter();
	    exit;
	}

	// make sure all users listed exist
	while (list (, $username) = each ($_REQUEST["member"])) {
	    // is this username blank?
	    if ($username != "") {
		// no. look up the username
	        $memberid[$username] = GetMemberID ($username);
	        if ($memberid[$username] == "") {
		    // this failed. complain
 ?>We're sorry, but user <b><?php echo $username; ?></b> does not seem to exist. Modifications cancelled.
<?php
		    cpShowFooter();
		    exit;
		}
	    }
	}

	// check the 'new' list too
	while (list (, $username) = each ($_REQUEST["newmember"])) {
	    // is this username blank?
	    if ($username != "") {
		// no. look up the username
	        $memberid[$username] = GetMemberID ($username);
	        if ($memberid[$username] == "") {
		    // this failed. complain
 ?>We're sorry, but user <b><?php echo $username; ?></b> does not seem to exist. Modifications cancelled.
<?php
		    cpShowFooter();
		    exit;
		}
	    }
	}

	// update the group record
	$query = sprintf ("UPDATE groups SET name='%s',description='%s' WHERE id='%s'", $groupname, $groupdesc, $id);
	db_query ($query);

	// now, change the users
	reset ($_REQUEST["member"]);
	while (list ($memid, $username) = each ($_REQUEST["member"])) {
	    // need to get rid of this user?
	    if ($username == "") {
		// yes. do it
		$query = sprintf ("DELETE FROM groupmembers WHERE id='%s'", $memid);
	    } else {
		// no. change the user
		$query = sprintf ("UPDATE groupmembers SET userid='%s' WHERE id='%s' AND groupid='%s'", $memberid[$username], $memid, $id);
	    }
	    db_query ($query);
	}

	// add all new members, too
	reset ($_REQUEST["newmember"]);
	while (list (, $username) = each ($_REQUEST["newmember"])) {
	    // is there an actual username here?
	    if ($username != "") {
		// yes. add the member
		$query = sprintf ("INSERT INTO groupmembers VALUES (NULL,'%s','%s')", $id, $memberid[$username]);
		db_query ($query);
	    }
	}

	// yay, this worked. tell the user about it
 ?>Thank you, the group has successfully been updated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to group overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Delete()
    //
    // This will delete a group.
    //
    function
    Delete() {
	// fetch the group id
	$groupid = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// show the header
	cpShowHeader ("Group Maintenance", "Delete group");

	// get rid of the group
	$query = sprintf ("DELETE FROM groups WHERE id='%s'", $groupid);
	db_query ($query);
	$query = sprintf ("DELETE FROM groupmembers WHERE groupid='%s'", $groupid);
	db_query ($query);

 ?>The group has successfully been deleted.
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to group overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Add()
    //
    // This will add a group.
    //
    function
    Add() {
	// build the page
	cpShowHeader("Group Maintenance", "Add group");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doadd">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
 <tr class="tab2">
  <td width="20%"><b>Group name</b></td>
  <td width="80%"><input type="text" name="groupname"></td>
 </tr>
 <tr class="tab2">
  <td><b>Group description</b></td>
  <td><input type="text" name="groupdesc"></td>
 </tr>
 <tr class="tab2">
  <td><b>Group members</b></td>
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
	cpShowFooter();
    }

    //
    // DoAdd()
    //
    // This will actually add the group.
    //
    function
    DoAdd() {
	// fetch the values
	$groupname = $_REQUEST["groupname"];
	$groupdesc = $_REQUEST["groupdesc"];

	// show the header
	cpShowHeader("Group Maintenance", "Add group");

	// build the group names
	while (list (, $username) = each ($_REQUEST["newmember"])) {
	    // is this account name in use?
	    if ($username != "") {
		// yes. look it up
		$nameid[$username] = GetMemberID ($username);
		if ($nameid[$username] == "") {
		    // this account does not exist. complain
 ?>We're sorry, but user <b><?php echo $username; ?></b> does not seem to exist.
<?php
		    cpShowFooter();
		    exit;
		}
	    }
	}

	// add the group
	$query = sprintf ("INSERT INTO groups VALUES (NULL,'%s','%s')",$groupname,$groupdesc);
	db_query ($query);
	$groupid = db_get_insert_id ();

	// add the users
	reset ($_REQUEST["newmember"]);
	while (list (, $username) = @each ($_REQUEST["newmember"])) {
	    // is this username blank?
	    if ($username != "") {
		// no. add it
	        $query = sprintf ("INSERT INTO groupmembers VALUES (NULL,%s,%s)", $groupid, $nameid[$username]);
		db_query ($query);
	    }
	}

	// yay, this worked. tell the user about it
 ?>Thank you, the group has successfully been added.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to group overview">
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_GROUPS);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // need to go to the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. do it
	Overview();
    } elseif ($action == "edit") {
	// edit a group
	Edit();
    } elseif ($action == "doedit") {
	// actually edit a group
	DoEdit();
    } elseif ($action == "add") {
	// add a group
	Add();
    } elseif ($action == "doadd") {
	// actually edit the group
	DoAdd();
    } elseif ($action == "delete") {
	// delete the group
	Delete();
    }
 ?>
