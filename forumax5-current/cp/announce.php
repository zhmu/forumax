<?php 
    //
    // announcements.php
    //
    // (c) 2000-2002 Next-Future, www.next-future.nl
    //
    // This will handle the editing of announcements.
    //

    // we need our library, too
    require "lib.php";

    //
    // CanEditAnnouncement ($userid)
    //
    // This will return non-zero if the current user can edit an announcement
    // with user id $userid, otherwise zero.
    //
    function 
    CanEditAnnouncement ($userid) {
	// are we a master?
	if ($GLOBALS["MASTER_ACCESS"] != 0) {
	    // yes. we can do anything we want with announcements
	    return 1;
	}

	// are we an administrator?
	if ($GLOBALS["ADMIN_ACCESS"] != 0) {
	    // yes. we can do anything we want with announcements
	    return 1;
	}

	// is the user id equal to our own?
	if ($GLOBALS["cp_accountid"] == $userid) {
	    // yes. we can mess with this announcement
	    return 1;
	}

	// we do not have access
	return 0;
    }

    //
    // Overview()
    //
    // This will take care of the announcements.
    //
    function
    Overview() {
	// display our nice header
	cpShowHeader("Announcement Maintenance", "Announcement overview");

	// grab all announcements
	$query = sprintf ("SELECT id,forumid,startdate,enddate,title,authorid FROM announcements");
	$res = db_query ($query);

	// build the layout
 ?><table width="100%" cellspacing="2" cellpadding="3" border="0" class="tab1">
<tr class="tab3">
  <td width="20%"><b>Subject</b></td>
  <td width="20%"><b>Author</b></td>
  <td width="20%"><b>Forum</b></td>
  <td width="20%"><b>Start date</b></td>
  <td width="20%"><b>End date</b></td>
</tr>
<?php

	// now, add all of them
	while (list ($id, $forumid, $startdate, $enddate, $title, $authorid) = db_fetch_results ($res)) {
	    // resolve the forum id
	    if ($forumid == 0) {
		// it's a wildcard
		$destforum = "<i>All forums</i>";
	    } else {
		// grab the forum name
		$query = sprintf ("SELECT name FROM forums WHERE id='%s'", $forumid);
		list ($destforum) = db_fetch_results (db_query ($query));
	    }

	    // do we have enough rights to do this?
	    if (CanEditAnnouncement ($authorid) != 0) {
	        // add it to the list
		printf ("<tr class=\"tab2\"><td><a href=\"%s?action=edit&anncid=%s\">%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $_SERVER["PHP_SELF"], $id, $title, GetMemberName ($authorid), $destforum, $startdate, $enddate);
	    }
	}
 ?></table><p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="create">
<center><input type="submit" value="Create announcement"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // Edit()
    //
    // This will edit an announcement.
    //
    function
    Edit() {
	// show the header
	cpShowHeader ("Announcement Maintenance", "Edit announcement");

	// fetch the announcement id
	$anncid = preg_replace ("/\D/", "", $_REQUEST["anncid"]);

	// grab the information
	$query = sprintf ("SELECT title,forumid,startdate,enddate,content,authorid FROM announcements WHERE id='%s'", $anncid);
	list ($title, $forumid, $startdate, $enddate, $content, $authorid) = db_fetch_results (db_query ($query));

	// do we have rights to do this?
	if (CanEditAnnouncement ($authorid) == 0) {
	    // no. complain
	    print "Sorry, but you do not have rights to edit this announcement";
	    cpShowFooter();
	    exit;
	}

	// build the page
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="anncid" value="<?php echo $anncid; ?>">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
 <tr class="tab2">
  <td width="20%">Author</td>
  <td width="80%"><b><?php $tmp = GetMemberNameSimple ($authorid); echo ($tmp == "") ? "?" : $tmp; ?></b></td>
 </tr>
 <tr class="tab2">
  <td width="20%">Title</td>
  <td width="80%"><input type="text" name="the_title" value="<?php echo htmlspecialchars ($title); ?>"></td>
 </tr>
 <tr class="tab2">
  <td width="20%">Forum</td>
  <td width="80%"><select name="the_forum"><?php BuildForumList ($forumid); ?></td>
 <tr class="tab2">
   <td>Start date</td>
   <td><input type="text" name="the_startdate" value="<?php echo htmlspecialchars ($startdate); ?>"></td>
 </tr>
 <tr class="tab2">
   <td>End date</td>
   <td><input type="text" name="the_enddate" value="<?php echo htmlspecialchars ($enddate); ?>"></td>
  </tr>
  <tr class="tab2" valign="top">
    <td>Message</td>
    <td><textarea name="the_message" rows=10 cols=40><?php echo htmlspecialchars ($content); ?></textarea></td>
  </tr>
</table><p>
<table width="100%">
  <tr>
    <td width="50%" align="center"><input type="submit" value="Submit modifications"></form></td>
    <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="anncid" value="<?php echo $anncid; ?>"><input type="submit" value="Delete announcement"></form></td>
  </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // DoEdit()
    //
    // This will actually edit the announcement.
    //
    function
    DoEdit() {
	// show the header
	cpShowHeader("Announcement Maintenance", "Edit announcement");

	// fetch the values
	$anncid = preg_replace ("/\D/", "", $_REQUEST["anncid"]);
	$the_title = $_REQUEST["the_title"];
	$the_forum = preg_replace ("/\D/", "", $_REQUEST["the_forum"]);
	$the_startdate = $_REQUEST["the_startdate"];
	$the_enddate = $_REQUEST["the_enddate"];
	$the_message = $_REQUEST["the_message"];

	// is the destination forum ok?
	if ($the_forum == 0) {
	    // this is an announcement for all forums. are we an admin,
	    // megamod or master?
	    if (($GLOBALS["ADMIN_ACCESS"] == 0) and (($GLOBALS["cp_accountflags"] & FLAG_MMOD) == 0) and ($GLOBALS["MASTER_ACCESS"] == 0)) {
		// no, we aren't. complain
 ?>We're sorry, but only administrators and mega moderators can create and edit announcements for all forums.
<?php
		cpShowFooter();
		exit;
	    }
	} else {
	    // are we a moderator in that forum?
	    if (($GLOBALS["MASTER_ACCESS"] == 0) and ($GLOBALS["ADMIN_ACCESS"] == 0) and (IsForumMod ($the_forum) == 0)) {
		// no. complain
 ?>We're sorry, but you must be a moderator of the forums in which you want to display this announcement.
<?php
		cpShowFooter();
		exit;
	    }
	}

	// grab the announcement author
	$query = sprintf ("SELECT authorid FROM announcements WHERE id='%s'", $anncid);
	list ($authorid) = db_fetch_results (db_query ($query));

	// do we have rights to do this?
	if (CanEditAnnouncement ($authorid) == 0) {
	    // no. complain
	    print "Sorry, but you do not have rights to edit this announcement";
	    cpShowFooter();
	    exit;
	}

	// all is ok. build the query
	$query = sprintf ("UPDATE announcements SET title='%s',forumid=%s,startdate='%s',enddate='%s',content='%s' WHERE id='%s'",$the_title,$the_forum,$the_startdate,$the_enddate,$the_message,$anncid);
	db_query ($query);

	// it worked. show the 'victory!' page
 ?>The announcement has successfully been modified.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to announcement overview">
</form>
<?php
	cpShowFooter();
    }

    //
    // Create()
    //
    // This will show the page for announcement creation.
    //
    function
    Create() {
	// create the header
	cpShowHeader("Announcement Maintenance", "Create announcement");

	// are we a master?
	if ($GLOBALS["MASTER_ACCESS"] != 0) {
	    // yes. we cannot create announcements
	    print "Sorry, but masters cannot create announcements";
	    cpShowFooter();
	    exit;
	}

 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="docreate">
<table width="100%" class="tab5" cellspacing="1" cellpadding="4" border="0">
 <tr class="tab2">
  <td width="20%">Title</td>
  <td width="80%"><input type="text" name="the_title"></td>
 </tr>
 <tr class="tab2">
  <td>Forum</td>
  <td><select name="the_forum"><?php BuildForumList (0); ?></select></td>
 </tr>
 <tr class="tab2">
  <td>Start date</td>
  <td><input type="text" name="the_startdate" value="<?php echo date ("Y-m-d H:i:s"); ?>"></td>
 </tr>
 <tr class="tab2">
  <td>End date</td>
  <td><input type="text" name="the_enddate" value="9999-12-31 23:59:59"></td>
 </tr>
 <tr class="tab2" valign="top">
  <td>Message</td>
  <td><textarea name="the_message" rows=10 cols=40></textarea></td>
 </tr>
</table><p>
<table width="100%">
  <tr>
    <td width="50%" align="center"><input type="submit" value="Create Announcement"></form></td>
    <td width="50%" align="center"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"><input type="submit" value="Cancel"></form></td>
  </tr>
</table>
<?php
	cpShowFooter();
    }

    //
    // DoCreate()
    //
    // This will actually create the announcement
    //
    function
    DoCreate() {
	// show the title
	cpShowHeader("Announcement Maintenance", "Create announcement");

	// fetch the arguments
	$the_title = $_REQUEST["the_title"];
	$the_forum = $_REQUEST["the_forum"];
	$the_startdate = $_REQUEST["the_startdate"];
	$the_enddate = $_REQUEST["the_enddate"];
	$the_message = $_REQUEST["the_message"];

	// are we a master?
	if ($GLOBALS["MASTER_ACCESS"] != 0) {
	    // yes. we cannot create announcements
	    print "Sorry, but masters cannot create announcements";
	    cpShowFooter();
	    exit;
	}

	// is the destination forum ok?
	if ($the_forum == 0) {
	    // this is an announcement for all forums. are we an admin or
	    // megamod?
	    if (($GLOBALS["ADMIN_ACCESS"] == 0) and ($GLOBALS["MMOD_ACCESS"] == 0) and ($GLOBALS["MASTER_ACCESS"] == 0)) {
		// no, we aren't. complain
 ?>We're sorry, but only administrators and mega moderators can create announcements for all forums.
<?php
		cpShowFooter();
		exit;
	    }
	} else {
	    // are we a moderator in that forum?
	    if (($GLOBALS["MASTER_ACCESS"] == 0) and ($GLOBALS["ADMIN_ACCESS"] == 0) and (IsForumMod ($the_forum) == 0)) {
		// no. complain
 ?>We're sorry, but you must be a moderator of the forums in which you want to create an announcement.
<?php
		cpShowFooter();
		exit;
	    }
	}

	// all is ok. build the query
	$query = sprintf ("INSERT INTO announcements VALUES (NULL,'%s',%s,'%s','%s',%s,'%s',0)",$the_title,$GLOBALS["cp_accountid"],$the_startdate,$the_enddate,$the_forum,$the_message);
	db_query ($query);

	// it worked. show the 'victory!' page
 ?>The announcement has successfully been created.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to announcement overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // Delete()
    //
    // This will delete an announcement.
    //
    function
    Delete() {
	// show the header
	cpShowHeader ("Announcement Maintenance", "Delete announcement");

	// fetch the header
	$anncid = preg_replace ("/\D/", "", $_REQUEST["anncid"]);

	// zap it
	$query = sprintf ("DELETE FROM announcements WHERE id='%s'", $anncid);
	db_query ($query);

	// yay, this worked. inform the user
 ?>The announcement has successfully been deleted.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to announcement overview">
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_ANNOUNCE);

    // fetch the action
    $action = $_REQUEST["action"];

    // need to show the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. show the overview
	Overview();
    } elseif ($action == "edit") {
	// show the edit page for an announcement
	Edit();
    } elseif ($action == "doedit") {
	// actually edit the announcement
	DoEdit();
    } elseif ($action == "create") {
	// show the create announcement page
	Create();
    } elseif ($action == "docreate") {
	// actually create the announcement
	DoCreate();
    } elseif ($action == "delete") {
	// delete the announcement
	Delete();
    }
 ?>
