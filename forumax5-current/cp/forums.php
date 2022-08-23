<?php 
    //
    // forums.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the editing of forums.
    //

    // we need our library, too
    require "lib.php";

    // FORUMS_PER_PAGE will indicate how much forums we will list per page
    define (FORUMS_PER_PAGE, 20);

    // $forum_flag[$flag] = $description will list all forum flags. They will
    // be used when editing.
    $forum_flag[FLAG_FORUM_ALLOWHTML] = "Allow HTML code in this forum";
    $forum_flag[FLAG_FORUM_ALLOWMAX] = "Allow MaX code in this forum";
    $forum_flag[FLAG_FORUM_DENYEVILHTML] = "Block Javascript code and dangerous HTML tags";
    $forum_flag[FLAG_FORUM_NOIMAGES] = "Images disabled";
    $forum_flag[FLAG_FORUM_ALLOWPOLLS] = "Allow polls to be posted";
    $forum_flag[FLAG_FORUM_UNREGPOST] = "Allow unregistered member posts";

    //
    // Overview()
    //
    // This will show an overview of all forums.
    //
    function 
    Overview() {
	// show the header
	cpShowHeader("Forum Maintenance", "Overview");

	// always default to page one
	$page = preg_replace ("/\D/", "", $_REQUEST["page"]);
	if ($page == "") { $page = 1; }

	// count the forums
	$query = sprintf ("SELECT COUNT(id) FROM forums");
	list ($numforums) = db_fetch_results (db_query ($query));

	// calculate the number of pages
	$nofpages = floor ($numforums / FORUMS_PER_PAGE);
	if (($numforums * FORUMS_PER_PAGE) != $nofpages) { $nofpages++; }

	// calculate the number of forums to list
	if ($page == "0") {
	    // we need to show 'em all. do it
	    $fromno = 0; $howmuch = $numforums;
	} else {
	    // we only need to list FORUMS_PER_PAGE ones. do it
	    $fromno = ($page - 1) * FORUMS_PER_PAGE; $howmuch = FORUMS_PER_PAGE;
	}

	// build the table
 ?></small><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doorder">
<table width="100%" cellspacing="2" cellpadding="3" border="0" class="tab1">
<tr>
  <td colspan=3><small><?php
	// now, create the page [] thingies.
	print "<small>Page ";
	for ($i = 1; $i <= $nofpages; $i++) {
	    // is this one currently selected?
	    if ($page == $i) {
	 	// yes. don't hyperlink it, make it bold instead.
		printf ("[<b>%s</b>] ",$i);
	    } else {
		// no. just make it a hyperlink
		printf ("[<a class=\"sml\" href=\"%s?page=%s\">%s</a>] ", $_SERVER["PHP_SELF"], $i, $i);
	    }
	}

	// add the 'All' link
	if ($page == "0") {
	    print "[<b>All</b>]";
	} else {
	    // no. just make it a hyperlink
	    printf ("[<a class=\"sml\" href=\"%s?page=0\">All</a>]", $_SERVER["PHP_SELF"]);
	}
 ?></small></small></td>
</tr>
<tr>
  <td width="1%" class="tab3">Order</td>
  <td width="59%" class="tab3">&nbsp;Forum name</td>
  <td width="40%" class="tab3">&nbsp;Forum flags</td>
</tr>
<?php

	// select the forums we need
	$query = sprintf ("SELECT id,orderno,name,flags FROM forums ORDER BY orderno ASC LIMIT %s,%s",$fromno,$howmuch);
	$res = db_query ($query);

	// add the forums to the list
	while (list ($id, $orderno, $name, $flags) = db_fetch_results ($res)) {
	    // construct the forum flags
	    $forumflags = "";
	    if (($flags & FLAG_FORUM_ALLOWHTML) != 0) { $forumflags[] = "<font color=\"#00ff00\">HTML allowed</font>"; };
	    if (($flags & FLAG_FORUM_ALLOWMAX) != 0) { $forumflags[] = "<font color=\"#00ff00\">MaX allowed</font>"; };
	    if (($flags & FLAG_FORUM_DENYEVILHTML) != 0) { $forumflags[] = "<font color=\"#ff0000\">Potentially dangerous HTML disabled</font>"; };
	    if (($flags & FLAG_FORUM_NOIMAGES) != 0) { $forumflags[] = "<font color=\"#ff0000\">Images disallowed</font>"; };
	    if (($flags & FLAG_FORUM_UNREGPOST) != 0) { $forumflags[] = "<font color=\"#00ff00\">Allow Unregistered Postings</font>"; };

	    // still no flags?
	    if ($forumflags == "") {
		// yes. add 'none'
		$forumflags[] = "None";
	    }

	    // add the forum
	    printf ("<tr class=\"tab2\"><td class=\"tn\"><input type=\"text\" size=5 name=\"order[%s]\" value=\"%s\"></td><td class=\"tn\">&nbsp;<a href=\"%s?action=edit&id=%s\">%s</a></td><td class=\"tn\">&nbsp;%s</td></tr>", $id, $orderno, $_SERVER["PHP_SELF"], $id, $name, implode (", ", $forumflags));
	}
 ?></table>
<p><center><input type="submit" value="Activate changes"></center></form><p>
<center><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="add">
<input type="submit" value="Add a forum">
</form></center>
<?php
	cpShowFooter();
    }

    //
    // DoOrder()
    //
    // This will actually submit the new forum order.
    //
    function
    DoOrder() {
	// browse them all
	while (list ($forumid, $orderno) = each ($_REQUEST["order"])) {
	    // build the query
	    $query = sprintf ("UPDATE forums SET orderno='%s' WHERE id='%s'", $orderno, $forumid);
	    db_query ($query);
	}

	// this worked. show the 'wohoo' page
	cpShowHeader("Forum Maintenance", "Forum Order Modification");
 ?>Thank you, the forum order has successfully been updated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to forum overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will show the page for editing a specific forum.
    //
    function
    Edit() {
	global $forum_flag;

	// show the header
	cpShowHeader("Forum Maintenance", "Edit Forum");

	// fetch the forum id
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// grab the database entry
	$query = sprintf ("SELECT name,flags,description,catno,image FROM forums WHERE id='%s'", $id);
	list ($name, $flags, $desc, $catno, $image) = db_fetch_results (db_query ($query));

	// build the page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
<tr class="tab2">
  <td width="20%"><b>Forum name</b></td>
  <td width="80%"><input type="text" name="the_forumname" value="<?php echo htmlspecialchars ($name); ?>"></td>
</tr>
<tr class="tab2">
  <td width="20%"><b>Category</b></td>
  <td width="80%"><select name="catid"><option value="0"<?php if ($catno == 0) { echo " selected"; } ?>>No category</option>
   <?php
	// build a list of all categories
	$query = sprintf ("SELECT id,name FROM categories ORDER BY name ASC");
	$res = db_query ($query);

	// add them all, as needed
	while (list ($catid, $catname) = db_fetch_results ($res)) {
	    printf ("<option value=\"%s\"", $catid);
	    if ($catid == $catno) { echo " selected"; };
	    printf (">%s</option>", $catname);
	}
    ?></select></td>
</tr>
<tr class="tab2">
  <td><b>Moderators</b><br><small>Moderators are users that can delete, edit and lock any thread or post in a forum. If you preceed a name with a @, it will be interprented as a group</small></td>
  <td><?php
	// grab all mods for this forum
	$query = sprintf ("SELECT id,userid,flags FROM mods WHERE forumid='%s'", $id);
        BuildUserFields ("mod", db_query ($query));
?></td>
</tr>
<tr class="tab2">
  <td><b>Restricted users</b><br><small>If this is not empty, anyone listed can access the forums. Administrators can always access any forum. If you preceed a name with a @, it will be interprented as a group</small></td>
  <td><?php
	// grab all restricted users for this forum
	$query = sprintf ("SELECT id,userid,flags FROM restricted WHERE forumid='%s'", $id);
        BuildUserFields ("restricted", db_query ($query));
?></td>
</tr>
<tr class="tab2">
  <td><b>Users to notify</b><br><small>If this is not empty, anyone listed here will be sent an email if a new thread or post is created in this forum. If you preceed a name with a @, it will be interprented as a group</small></td>
  <td><?php
	// grab all notified users for this forum
	$query = sprintf ("SELECT id,userid,flags FROM notify WHERE forumid='%s'", $id);
        BuildUserFields ("notify", db_query ($query));
 ?>
</tr>
<tr class="tab2">
  <td><b>Forum Image</b><br><small>If this is set, this will be used instead of te default logo when browsing through a forum. It has to reside in the path specified in the skin variable <u>images_url</u></small></td>
  <td><input type="text" name="image" value="<?php echo $image; ?>"></td>
</tr>
<tr class="tab2">
  <td colspan=2 height=12></td>
</tr>
<tr class="tab2">
  <td valign="top"><b>Description</b></td>
  <td><textarea name="the_desc" rows=10 cols=40><?php echo htmlspecialchars ($desc); ?></textarea></td>
</tr>
<tr class="tab2">
  <td colspan=2 height=12></td>
</tr>
<tr class="tab2">
  <td valign="top"><b>Flags</b></td>
  <td><?php
    // add the flags
    while (list ($bit, $desc) = each ($forum_flag)) {
	printf ("<input type=\"checkbox\" name=\"f_bit[%s]\"", $bit);
	if (($flags & $bit) != 0) { echo " checked"; };
	printf (">&nbsp;%s</input><br>", $desc);
    }
 ?></td>
</tr>
</table><p>
<table width="100%">
 <tr valign="top">
  <td width="50%" align="center"><input type="submit" value="Submit forum changes"></form></td>
  <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="submit" value="Delete forum"></td>
 </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually edit a certain forum.
    //
    function
    DoEdit() {
	// fetch the values
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);
	$the_forumname = $_REQUEST["the_forumname"];
	$catid = preg_replace ("/\D/", "", $_REQUEST["catid"]);
	$the_desc = $_REQUEST["the_desc"];
	$image = $_REQUEST["image"];

	// show the header
	cpShowHeader("Forum Maintenance", "Edit forum");

	// handle the moderators
	HandleUserFields ("UPDATE mods SET userid=[objectid],flags=[flags] WHERE id=[id]", "DELETE FROM mods WHERE id='%s'", $id, $_REQUEST["mod"]);
	HandleUserFields ("INSERT INTO mods VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["newmod"]);

	// handle the restricted list
	HandleUserFields ("UPDATE restricted SET userid=[objectid],flags=[flags] WHERE id=[id]", "DELETE FROM restricted WHERE id='%s'", $id, $_REQUEST["restricted"]);
	HandleUserFields ("INSERT INTO restricted VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["newrestricted"]);

	// handle the notification list
	HandleUserFields ("UPDATE notify SET userid=[objectid],flags=[flags] WHERE id=[id]", "DELETE FROM notify WHERE id='%s'", $id, $_REQUEST["notify"]);
	HandleUserFields ("INSERT INTO notify VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["newnotify"]);

	// build the flags
	$flags = 0;
	while (list ($bit, $on) = @each ($_REQUEST["f_bit"])) {
	    // is this box checked?
	    if ($on != "") {
		// yes. add the flag
		$flags |= $bit;
	    }
	}

	// build a query
	$query = sprintf ("UPDATE forums SET name='%s',description='%s',flags=%s,catno=%s,image='%s' WHERE id='%s'",$the_forumname,$the_desc,$flags,$catid,$image,$id);
	db_query ($query);

	// it worked. show the 'yay' page
 ?>The forum has successfully been modified.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to forum overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Delete()
    //
    // This will delete a forum.
    //
    function
    Delete() {
	// show the header
	cpShowHeader("Forum Maintenance", "Delete forum");

	// fetch the forum id
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// zap the forum entry
	$query = sprintf ("DELETE FROM forums WHERE id='%s'", $id);
	db_query ($query);

	// delete all forum threads
	$query = sprintf ("DELETE FROM threads WHERE forumid='%s'", $id);
	db_query ($query);

	// delete all forum posts
	$query = sprintf ("DELETE FROM posts WHERE forumid='%s'", $id);
	db_query ($query);

	// delete all forum moderators too
	$query = sprintf ("DELETE FROM mods WHERE forumid='%s'", $id);
	db_query ($query);

	// yay, it worked. show the 'yahoo' page
 ?>The forum has successfully been deleted.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to forum overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Add()
    //
    // This will show the 'add forum' page.
    //
    function
    Add() {
	global $forum_flag;

	// build the page
	cpShowHeader("Forum Maintenance", "Add forum");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doadd">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
<tr class="tab2">
  <td width="20%">Forum name</td>
  <td width="80%"><input type="text" name="the_forumname"></td>
</tr>
<tr class="tab2">
  <td width="20%">Category</td>
  <td width="80%"><select name="catid"><option value="0">No category</option>
   <?php
	// build a list of all categories
	$query = sprintf ("SELECT id,name FROM categories ORDER BY name ASC");
	$res = db_query ($query);

	// add them all, as needed
	while (list ($catid, $catname) = db_fetch_results ($res)) {
	    printf ("<option value=\"%s\">%s</option>", $catid, $catname);
	}
    ?></select></td>
</tr>
<tr class="tab2">
  <td>Moderators<br><font size=1>Moderators are users that can delete, edit and lock any thread or post in a forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><input type="text" name="mod[0]"> <input type="text" name="mod[1]"></td>
</tr>
<tr class="tab2">
  <td>Restricted users<br><font size=1>If this is not empty, anyone listed can access the forums. Administrators can always access any forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><input type="text" name="restricted[0]"> <input type="text" name="restricted[1]"></td>
</tr>
<tr class="tab2">
  <td>Users to notify<br><font size=1>If this is not empty, anyone listed here will be sent an email if a new thread or post is created in this forum. If you preceed a name with a @, it will be interprented as a group</td>
  <td><input type="text" name="notify[0]"> <input type="text" name="notify[1]"></td>
</tr>
<tr class="tab2">
  <td colspan=2>&nbsp;</td>
</tr>
<tr class="tab2">
  <td valign="top">Description</td>
  <td><textarea name="the_desc" rows=10 cols=40></textarea></td>
</tr>
<tr class="tab2">
  <td colspan=2>&nbsp;</td>
</tr>
<tr class="tab2">
  <td valign="top">Flags</td>
  <td><?php
    // add the flags
    while (list ($bit, $desc) = each ($forum_flag)) {
	printf ("<input type=\"checkbox\" name=\"f_bit[%s]\">%s</input><br>", $bit, $desc);
    }
 ?></td>
</tr>
</table><p>
<center><input type="submit" value="Add forum"></center></form>
<?php
	cpShowFooter();
    }

    //
    // DoAdd()
    //
    // This will actually add a forum.
    //
    function
    DoAdd() {
	// fetch the values
	$the_forumname = $_REQUEST["the_forumname"];
	$catid = preg_replace ("/\D/", "", $_REQUEST["catid"]);
	$the_desc = $_REQUEST["the_desc"];
	$image = $_REQUEST["image"];

	// show the header
	cpShowHeader("Forum Maintenance", "Add forum");

	// is this forum name already in use?
	$query = sprintf ("SELECT id FROM forums WHERE name='%s'", $the_forumname);
	$res = db_query ($query);
	if (db_nof_results ($res) != 0) {
	    // yes. complain
 ?>This forum name (<b><?php echo $the_forumname; ?></b>) is already in use. Forum names must be unique.<?php
	    cpShowFooter();
	    exit;
	}

	// build the flags
	$flags = 0;
	while (list ($bit, $on) = @each ($_REQUEST["f_bit"])) {
	    // is this box checked?
	    if ($on != "") {
		// yes. add the flag
		$flags |= $bit;
	    }
	}

	// get the last forum number
	$query = sprintf ("SELECT MAX(id) FROM forums");
	list ($forumcount) = db_fetch_results (db_query ($query));
	
	// increment it
	$forumcount++;

	// build a query
	$query = sprintf ("INSERT INTO forums VALUES (NULL,'%s',%s,'%s',0,0,'',0,'',%s,%s,'')",$the_forumname,$flags,$the_desc,$catid,$forumcount);
	db_query ($query);
	$id = db_get_insert_id();

	// add all moderators
	HandleUserFields ("INSERT INTO mods VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["mod"]);

	// add all restricted accounts
	HandleUserFields ("INSERT INTO restricted VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["restricted"]);

	// add all accounts to be notified
	HandleUserFields ("INSERT INTO notify VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["notify"]);

	// it worked. show the 'yay' page
 ?>The forum has successfully been added.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to forum overview">
</form><?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_FORUMS);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // need to go to the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. do it
	Overview();
    } elseif ($action == "edit") {
	// edit a forum
	Edit();
    } elseif ($action == "doedit") {
	// actually edit a forum
	DoEdit();
    } elseif ($action == "add") {
	// add a forum
	Add();
    } elseif ($action == "doadd") {
	// actually edit the forum
	DoAdd();
    } elseif ($action == "delete") {
	// delete the forum
	Delete();
    } elseif ($action == "doorder") {
	// change the forum order
	DoOrder();
    } elseif ($action == "add") {
	// add a forum
	Add();
    } elseif ($action == "doadd") {
	// actually add the forum
	DoAdd();
    }
 ?>
