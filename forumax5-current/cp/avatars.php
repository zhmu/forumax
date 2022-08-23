<?php 
    //
    // avatars.php
    //
    // (c) 2000-2002 Next-Future, www.next-future.nl
    //
    // This will handle the editing of avatars.
    //

    // we need our library, too
    require "lib.php";

    // AVATARS_PER_LINE will indicate how much avatars we will show per line
    define (AVATARS_PER_LINE, 5);

    //
    // Overview()
    //
    // This will show the avatar overview.
    //
    function
    Overview() {
	global $SKIN_VALUE;

	// create the header
	CPShowHeader("Avatar Maintenance", "Overview");

	// select all avatars from the database
	$query = sprintf ("SELECT id,flags,userid FROM avatar");
	$res = db_query ($query); $nofavatars = db_nof_results ($res);

	// list them all
 ?><table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
<tr class="tab3" align="center"><td colspan="<?php echo AVATARS_PER_LINE; ?>">Avatar Overview</td></tr><?php

	$i = 1;
	while (list ($id, $flags) = db_fetch_results ($res)) {
	    if ($i == 1) { echo "<tr class=\"tab2\">"; };
	    printf ("<td valign=\"center\" width=\"%s%%\" align=\"center\"><br><a href=\"%s?action=edit&id=%s\"><img border=0 src=\"../%s/avatars/%s.gif\" alt=\"[Avatar]\"><p></a></td>", 100 / AVATARS_PER_LINE, $_SERVER["PHP_SELF"], $id, $SKIN_VALUE["images_url"], $id);
	    if ($i == AVATARS_PER_LINE) { echo "</tr>"; $i = 0; };
	    $i++;
	}

	// fill up the empty columns
	$i = AVATARS_PER_LINE - $i + 1;
	while ($i > 0) {
	   echo "<td>&nbsp;</td>";
	   $i--;
	}
 ?></tr></table><p><center>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="add">
<input type="submit" value="Add avatar">
</form></center>
<?php
	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will edit an avatar.
    //
    function
    Edit() {
	global $SKIN_VALUE;

	// fetch the arguments
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// get the information
	$query = sprintf ("SELECT flags,userid FROM avatar WHERE id='%s'", $id);
	list ($flags, $userid) = db_fetch_results (db_query ($query));

	// show the information
	cpShowHeader("Avatar Maintenance", "Edit avatar");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
 <tr class="tab2">
  <td width="80%" align="left">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" class="tab5">
      <tr class="tab2">
	<td width="20%">Avatar ID</td>
	<td width="80%"><b><?php echo $id; ?></b></td>
      </tr>
      <tr class="tab2">
	<td>Flags</td>
	<td><input type="checkbox" name="adminonly" <?php if (($flags & FLAG_AVATAR_ADMINONLY) != 0) { echo " checked"; }; ?>>Administrator only (not selectable by users)</input></td>
      </tr>
      <tr class="tab2">
	<td>Image (only if changing)</td>
	<td><input type="file" name="newimage"></td>
      </tr>
    </table><p>
  </td>
  <td width="20%" align="center">
    <?php printf ("<img src=\"../%s/avatars/%s.gif\" alt=\"[Avatar]\">", $SKIN_VALUE["images_url"], $id); ?>
  </td>
 </tr>
</table><p>
<table width="100%">
 <tr valign="top">
  <td width="50%" align="center"><input type="submit" value="Submit Changes"></form></td>
  <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="submit" value="Delete Avatar"></form></td>
 </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually edit an avatar.
    //
    function
    DoEdit() {
	global $SKIN_VALUE;

	// create a header
	cpShowHeader("Avatar Maintenance", "Edit avatar");

	// grab the id
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// build the new flags
	$new_flags = 0;
	if ($_REQUEST["adminonly"] != "") { $new_flags |= FLAG_AVATAR_ADMINONLY; };

	// update the avatar
	$query = sprintf ("UPDATE avatar SET flags='%s' WHERE id='%s'", $new_flags, $id);
	db_query ($query);

	// is a new image supplied?
	if (is_uploaded_file ($_FILES["newimage"]["tmp_name"])) {
	    // yes. move the file over
	    move_uploaded_file ($_FILES["newimage"]["tmp_name"], "../" . $SKIN_VALUE["images_url"] . "/avatars/" . $id . ".gif");
	}

 ?>The avatar has successfully been updated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to the avatar overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Add()
    //
    // This will add an avatar.
    //
    function
    Add() {
	// build the page
	cpShowHeader("Avatar Maintenance", "Add avatar");
 ?><form action="<?php echo $PHP_SELF; ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="doadd">
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
 <tr class="tab2">
  <td>Flags</td>
  <td><input type="checkbox" name="adminonly">Administrator only (not selectable by users)</input></td>
 </tr>
 <tr class="tab2">
  <td>Image</td>
  <td><input type="file" name="newimage"></td>
 </tr>
</table><p>
<center><input type="submit" value="Submit Changes"></center></form>
<?php
	cpShowFooter();
    }

    //
    // DoAdd()
    //
    // This will actually add the avatar.
    //
    function
    DoAdd() {
	global $SKIN_VALUE;

	// display the header
	cpShowHeader("Avatar Maintenance", "Add avatar");

	// is a new image supplied?
	if (!is_uploaded_file ($_FILES["newimage"]["tmp_name"])) {
	    // no. complain
	    print "Sorry, but an image must be supplied";
	    cpShowFooter();
	    exit;
	}

	// build the new flags
	$new_flags = 0;
	if ($_REQUEST["adminonly"] != "") { $new_flags |= FLAG_AVATAR_ADMINONLY; };

	// insert the avatar
	$query = sprintf ("INSERT INTO avatar VALUES (NULL,'%s',0)", $new_flags);
	db_query ($query);

	// get the new ID
	$id = db_get_insert_id();

	// yes. move the image file over
	move_uploaded_file ($_FILES["newimage"]["tmp_name"], "../" . $SKIN_VALUE["images_url"] . "/avatars/" . $id . ".gif");

 ?>The avatar has successfully been added.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to the avatar overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // DeleteAvatar()
    //
    // This will actually delete the avatar.
    //
    function
    DeleteAvatar() {
	global $id, $SKIN_VALUE;

	// show the header
	cpShowHeader("Avatar Maintenance", "Delete avatar");

	// kill anything non-numeric from the ID
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// get rid of the avatar
	$query = sprintf ("DELETE FROM avatar WHERE id='%s'", $id);
	db_query ($query);

	// get rid of the image, too
	@unlink ("../" . $SKIN_VALUE["images_url"] . "/avatars/" . $id . ".gif");

	// inform the user
 ?>The avatar has successfully been deleted.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to the avatar overview">
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_AVATARS);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // is there an action given?
    if (($action == "") or ($action == "overview")) {
	// no. show the overview
	Overview();
    } elseif ($action == "edit") {
	// edit an avatar
	Edit();
    } elseif ($action == "doedit") {
	// actually edit an avatar
	DoEdit();
    } elseif ($action == "add") {
	// add an avatar
	Add();
    } elseif ($action == "doadd") {
	// actually add an avatar
	DoAdd();
    } elseif ($action == "delete") {
	// delete an avatar
	DeleteAvatar();
    }
 ?>
