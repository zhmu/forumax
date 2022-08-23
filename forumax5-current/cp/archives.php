<?php 
    //
    // archives.php
    //
    // This will handle the maintenance of archives.
    //
    // (c) 1999-2002 NextFuture, http://www.next-future.nl
    //

    // we need our library, too
    require "lib.php";

    // ARCHIVES_PER_PAGE will indicate how much archives we will list per page
    define (ARCHIVES_PER_PAGE, 20);

    //
    // Overview()
    //
    // This will show an overview of all archives.
    //
    function 
    Overview() {
	// show the header
	cpShowHeader("Archive Maintenance", "Overview");

	// fetch the page number
	$page = preg_replace ("/\D/", "", $_REQUEST["page"]);

	// always default to page one
	if ($page == "") { $page = 1; }

	// count the archives
	$query = sprintf ("SELECT COUNT(id) FROM archives");
	list ($nofarchives) = db_fetch_results (db_query ($query));

	// calculate the number of pages
	$nofpages = floor ($noarchives / ARCHIVES_PER_PAGE);
	if (($nofpages * ARCHIVES_PER_PAGE) != $nofarchives) { $nofpages++; }

	// calculate the number of archives to list
	if ($page == "0") {
	    // we need to show 'em all. do it
	    $fromno = 0; $howmuch = $nofarchives;
	} else {
	    // we only need to list ARCHIVES_PER_PAGE ones. do it
	    $fromno = ($page - 1) * ARCHIVES_PER_PAGE;
	    $howmuch = ARCHIVES_PER_PAGE;
	}

	// build the table
 ?><table width="100%" cellspacing="2" cellpadding="3" border="0" class="tab1">
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
  <td width="100%" class="tab3">&nbsp;Archive name</td>
</tr>
<?php
	// select the archives we need
	$query = sprintf ("SELECT id,name FROM archives ORDER BY name ASC LIMIT %s,%s",$fromno,$howmuch);
	$res = db_query ($query);

	// add the archives to the list
	while (list ($id, $name) = db_fetch_results ($res)) {
	    // add the archive
	    printf ("<tr class=\"tab2\"><td class=\"tn\">&nbsp;<a href=\"%s?action=edit&archiveid=%s\">%s</a></td></tr>", $_SERVER["PHP_SELF"], $id, $name);
	}
 ?></table>
<center><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="add">
<input type="submit" value="Add an archive">
</form></center>
<?php
	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will show the page for editing a specific archive.
    //
    function
    Edit() {
	// show the header
	cpShowHeader("Archive Maintenance", "Edit Archive");

	// fetch the values
	$archiveid = preg_replace ("/\D/", "", $_REQUEST["archiveid"]);

	// grab the database entry
	$query = sprintf ("SELECT name,description FROM archives WHERE id='%s'", $archiveid);
	$res = db_query ($query);
	list ($name, $description) = db_fetch_results ($res);

	// do we have any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
 ?>This archive doesn't appear to exist.
<?php
	    cpShowFooter();
	    exit;
	}

	// build the page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="archiveid" value="<?php echo $archiveid; ?>">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
<tr class="tab2">
  <td width="20%"><b>Archive name</b></td>
  <td width="80%"><input type="text" name="the_archivename" value="<?php echo htmlspecialchars ($name); ?>"></td>
</tr>
<tr class="tab2">
  <td valign="top"><b>Description</b></td>
  <td><textarea name="the_desc" rows=10 cols=40><?php echo htmlspecialchars ($description); ?></textarea></td>
</tr>
</table><p>
<table width="100%">
 <tr valign="top">
  <td width="50%" align="center"><input type="submit" value="Submit archive changes"></form></td>
  <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="archiveid" value="<?php echo $archiveid; ?>"><input type="submit" value="Delete archive"></td>
 </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually edit a certain archive.
    //
    function
    DoEdit() {
	// show the header
	cpShowHeader("Archive Maintenance", "Edit archive");

	// fetch the values
	$archiveid = preg_replace ("/\D/", "", $_REQUEST["archiveid"]);
	$the_archivename = $_REQUEST["the_archivename"];
	$the_desc = $_REQUEST["the_desc"];

	// build the query
	$query = sprintf ("UPDATE archives SET name='%s',description='%s' WHERE id=%s",$the_archivename, $the_desc, $archiveid);
	db_query ($query);

	// it worked. show the 'yay' page
 ?>The archive has successfully been modified.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to archive overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Delete()
    //
    // This will delete an archive.
    //
    function
    Delete() {
	// show the header
	cpShowHeader("Archive Maintenance", "Delete archive");

	// fetch the value
	$archiveid = preg_replace ("/\D/", "", $_REQUEST["archiveid"]);

	// first, make sure the archive exists
	$query = sprintf ("SELECT id FROM archives WHERE id='%s'", $archiveid);
	$res = db_query ($query);
	if (db_nof_results ($res) == 0) {
	    // it doesn't exist. complain
 ?>We're sorry, but this forum doesn't appear to exist.
<?php
	    cpShowFooter();
	    exit;
	}

	// remove the archive
	$query = sprintf ("DELETE FROM archives WHERE id='%s'", $archiveid);
	db_query ($query);

	// delete all archive threads
	$query = sprintf ("DELETE FROM archive_threads WHERE archiveid='%s'", $archiveid);
	db_query ($query);

	// delete all forum posts
	$query = sprintf ("DELETE FROM archive_posts WHERE archiveid='%s'", $archiveid);
	db_query ($query);

	// yay, it worked. show the 'yahoo' page
 ?>The archive has successfully been deleted.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to archive overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Add()
    //
    // This will show the 'add archive' page.
    //
    function
    Add() {
	// build the page
	cpShowHeader("Archive Maintenance", "Add archive");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doadd">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4' border="0">
<tr class="tab2">
  <td width="20%">Archive name</td>
  <td width="80%"><input type="text" name="the_archivename"></td>
</tr>
<tr class="tab2">
  <td valign="top">Description</td>
  <td><textarea name="the_desc" rows=10 cols=40></textarea></td>
</tr>
</table><p>
<center><input type="submit" value="Add archive"></center></form>
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
	// build the header
	cpShowHeader("Archive Maintenance", "Add archive");

	// fetch the values
	$the_archivename = $_REQUEST["the_archivename"];
	$the_desc = $_REQUEST["the_des"];

	// is this archive name already in use?
	$query = sprintf ("SELECT id FROM archives WHERE name='%s'", $the_archivename);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. complain
 ?>This archive name (<b><?php echo $the_archivename; ?></b>) is already in use. Archive names must be unique.<?php
	    cpShowFooter();
	    exit;
	}

	// build the query
	$query = sprintf ("INSERT INTO archives VALUES (NULL,'%s','%s',0,0)", $the_archivename, $the_desc);
	db_query ($query);

	// it worked. show the 'yay' page
 ?>The archive has successfully been added.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to archive overview">
</form><?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_ARCHIVES);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // need to go to the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. do it
	Overview();
    } elseif ($action == "edit") {
	// edit an archive
	Edit();
    } elseif ($action == "doedit") {
	// actually edit an archive
	DoEdit();
    } elseif ($action == "add") {
	// add an archive
	Add();
    } elseif ($action == "doadd") {
	// actually edit the archive
	DoAdd();
    } elseif ($action == "delete") {
	// delete the archive
	Delete();
    } elseif ($action == "add") {
	// add an archive
	Add();
    } elseif ($action == "doadd") {
	// actually add the archive
	DoAdd();
    }
 ?>
