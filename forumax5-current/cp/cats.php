<?php 
    //
    // cats.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the editing of categories.
    //

    // we need our library, too
    require "lib.php";

    // CATS_PER_PAGE will indicate how much categories we will list per page
    define (CATS_PER_PAGE, 20);

    //
    // Overview()
    //
    // This will list all available categories.
    //
    function
    Overview() {
	// build the page
	cpShowHeader("Category Maintenance", "Overview");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doorder">
<table width="100%" cellspacing="2" cellpadding="3" border="0" class="tab1">
<tr>
  <td class="tab3" width="1%"><b>Order</b></td>
  <td class="tab3" width="99%"><b>Category Name</b></td>
</tr>
<?php
	// list all categories
	$query = sprintf ("SELECT id,orderno,name FROM categories ORDER BY orderno ASC");
	$res = db_query ($query);

	while (list ($id, $order, $name) = db_fetch_results ($res)) {
	    // show the category
	    printf ("<tr class=\"tab2\"><td class=\"tn\"><input type=\"text\" size=5 name=\"order[%s]\" value=\"%s\"></td><td class=\"tn\"><a href=\"%s?action=edit&id=%s\">%s</a></td></tr>", $id, $order, $_SERVER["PHP_SELF"], $id, $name);
	}
	print "</table>";

 ?><p><center><input type="submit" value="Activate changes"></center></form><p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="add">
<center><input type="submit" value="Add category"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // DoOrder()
    //
    // This will actually submit the new category order.
    //
    function
    DoOrder() {
	// show the header
	cpShowHeader ("Category Maintenance", "Category Order Modification");

	// browse them all
	while (list ($catid, $orderno) = each ($_REQUEST["order"])) {
	    // build the query
	    $query = sprintf ("UPDATE categories SET orderno='%s' WHERE id='%s'", $orderno, $catid);
	    db_query ($query);
	}

	// this worked. show the 'wohoo' page
 ?>Thank you, the category order has successfully been updated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to category overview">
</form>
<?php

	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will show the page for editing a category.
    //
    function
    Edit() {
	// display the header
	cpShowHeader ("Category Maintenance", "Edit category");
	
	// grab the id
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// grab the category information
	$query = sprintf ("SELECT name,description FROM categories WHERE id=%s", $id);
	list ($name, $desc) = db_fetch_results (db_query ($query));

	// build the page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
  <tr class="tab2">
    <td width="20%"><b>Category name</b></td>
    <td width="80%"><input type="text" name="the_name" value="<?php echo htmlspecialchars ($name); ?>"></td>
  </tr>
  <tr class="tab2">
    <td width="20%"><b>Category Moderators</b><br><font size=1>Category Moderators (also known as Super Moderators) are capable of moderating all forums within this category</td>
    <td width="80%"><?php
	// grab the list of category mods
	$query = sprintf ("SELECT id,userid,flags FROM catmods WHERE forumid='%s'", $id);
	$res = db_query ($query);
        BuildUserFields ("mod", $res);
?></td>
  </tr>
  <tr class="tab2">
    <td width="20%" valign="top"><b>Description</b></td>
    <td width="80%"><textarea name="the_desc" rows=10 cols=40><?php echo htmlspecialchars ($desc); ?></textarea></td>
  </tr>
</table><p>
<table width="100%">
 <tr>
  <td width="50%" align="center"><input type="submit" value="Submit changes"></form></td>
  <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="submit" value="Delete category"></form></td>
 </tr>
</form>
<?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually modify the category.
    //
    function
    DoEdit() {
	// build the header
	cpShowHeader("Category Maintenance", "Edit category");

	// fetch the values
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// handle the moderators
	HandleUserFields ("UPDATE catmods SET userid=[objectid],flags=[flags] WHERE id=[id]", "DELETE FROM catmods WHERE id=%s", $id, $_REQUEST["mod"]);
	HandleUserFields ("INSERT INTO catmods VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["newmod"]);

	// activate the changes
	$query = sprintf ("UPDATE categories SET name='%s',description='%s' WHERE id='%s'", $_REQUEST["the_name"], $_REQUEST["the_desc"], $id);
	db_query ($query);

	// all has been updated. show the 'yay' page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Thank you, the category has successfully been updated.<p>
<center><input type="submit" value="Return to category overview"></center>
</form>
<?php

	cpShowFooter();
    }

    //
    // Add()
    //
    // This will show the page for adding a category.
    //
    function
    Add() {
	// build the page
	cpShowHeader("Category Maintenance", "Add category");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doadd">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
  <tr class="tab2">
    <td width="20%"><b>Category name</b></td>
    <td width="80%"><input type="text" name="the_name"></td>
  </tr>
  <tr class="tab2">
    <td width="20%"><b>Category Moderators</b><br><font size=1>Category Moderators (also known as Super Moderators) are capable of moderating all forums within this category</td>
    <td><input type="text" name="mod[0]"> <input type="text" name="mod[1]"></td>
  </tr>
  <tr class="tab2">
    <td width="20%" valign="top"><b>Description</b></td>
    <td width="80%"><textarea name="the_desc" rows=10 cols=40></textarea></td>
  </tr>
</table><p>
<center><input type="submit" value="Add Category"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // DoAdd()
    //
    // This will actually add a category.
    //
    function
    DoAdd() {
	// build the header
	cpShowHeader("Category Maintenance", "Add category");

	// fetch the values
	$the_name = $_REQUEST["the_name"];
	$the_desc = $_REQUEST["the_desc"];

	// do we have an actual name?
	if (trim ($the_name) == "") {
	    // no. complain
	    print "You must supply a name for the category";
	    cpShowFooter();
	    exit;
	}

	// select the last category number
	$query = sprintf ("SELECT MAX(id) FROM categories");
	list ($catcount) = db_fetch_results (db_query ($query));

	// increment it
	$catcount++;
	
	// add the category
	$query = sprintf ("INSERT INTO categories VALUES (NULL,'%s','%s','%s')", $the_name, $the_desc, $catcount);
	db_query ($query);
	$id = db_get_insert_id();

	// add all super mods
	HandleUserFields ("INSERT INTO catmods VALUES (NULL," . $id . ",[objectid],[flags])", "", $id, $_REQUEST["mod"]);

	// all went ok. show the 'yay' page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Thank you, the category has successfully been created<p>
<center><input type="submit" value="Return to category overview"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // Delete()
    //
    // This will delete a category.
    //
    function
    Delete() {
	// create the header
	cpShowHeader("Category Maintenance", "Delete category");

	// fetch the category id
	$id = preg_replace ("/\D/", "", $_REQUEST["id"]);

	// zap all associated moderators
	$query = sprintf ("DELETE FROM catmods WHERE forumid='%s'", $id);
	db_query ($query);

	// get rid of the category
  	$query = sprintf ("DELETE FROM categories WHERE id='%s'", $id);
	db_query ($query);

	// inform the user
?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
The category has successfully been deleted.<p>
<center><input type="submit" value="Return to category overview"></center>
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_CATEGORIES);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // need to go to the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. do it
	Overview();
    } elseif ($action == "edit") {
	// edit a category
	Edit();
    } elseif ($action == "doedit") {
	// actually edit a category
	DoEdit();
    } elseif ($action == "add") {
	// add a category
	Add();
    } elseif ($action == "doadd") {
	// actually edit the categoy
	DoAdd();
    } elseif ($action == "delete") {
	// delete the category
	Delete();
    } elseif ($action == "doorder") {
	// change the forum order
	DoOrder();
    }
 ?>
