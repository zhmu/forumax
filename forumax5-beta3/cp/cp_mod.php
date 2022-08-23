<?php
    // we need our nice library
    require "cp_lib.php";

    // FORUMS_PER_PAGE will indicate how much forums we have per page
    define (FORUMS_PER_PAGE, 20);

    //
    // Intro()
    //
    // This will show the intro page.
    //
    function
    Intro() {
	CPShowHeader();
 ?>
Welcome to the ForuMAX Control Panel for moderators, <b><?php echo $GLOBALS["username"] ?></b>! This control panel will aid you in basical moderators of the forums. This means you will be able to perform tasks like<p>
<ul>
  <li>Announcement (creating, editing and deleting)</li>
  <li>Mass-deleting (pruning) posts from your forum</li>
</ul>
You can select what you would like to modify by clicking on the appropriate link in the cell on your left.
<?php
	CPShowFooter();
    }

    //
    // BuildForumList ($cursel)
    //
    // This will build the actual forum list you can modify. $cursel will be
    // selected as default choice.
    //
    function
    BuildForumList($cursel) {
	// are we an admin or megamod?
	if ((($GLOBALS["flags"] & FLAG_ADMIN) != 0) or (($GLOBALS["flags"] & FLAG_MMOD) != 0)) {
	    // yes. add all forums
	    $query = sprintf ("select id,name from forums");
	    $res = db_query ($query);
	    while ($tmp = db_fetch_results ($res)) {
		// add the entry
	        printf ("<option value=\"%s\"", $tmp[0]);
		if ($tmp[0] == $cursel) { print " selected"; };
		printf (">%s</option>", $tmp[1]);
	    }

	    // and the special 'all forums' entry too
	    printf ("<option value=\"0\"");
	    if ($cursel == 0) { print " selected"; };
	    printf (">All forums</option>");
	} else {
	    // no. add only the forums we have moderator rights in
	    $tmp = GetForumsModded ($GLOBALS["userid"]);

	    // add all forums
	    while (list ($forumid) = @each ($tmp)) {
	        // get the forum name
	        $query = sprintf ("select name from forums where id=%s", $forumid);
	        $res = db_query ($query); $result = db_fetch_results ($res);

	        // did we have any results?
	        if (db_nof_results ($res) != 0) {
		    // yes. add the entry
	            printf ("<option value=\"%s\"", $forumid);
		    if ($forumid == $cursel) { echo " selected"; };
		    printf (">%s</option>", $result[0]);
	        }
	    }
	}
 ?></select><?php
    }

    //
    // Announcements()
    //
    // This will take care of the announcements.
    //
    function
    Announcements() {
	CPShowHeader();

	// grab all announcements
	$query = sprintf ("select id,forumid,startdate,enddate,title from announcements");
	$res = db_query ($query);

	// build the layout
 ?>
<table width="100%">
<tr>
  <td width="25%"><b>Subject</b></td>
  <td width="25%"><b>Forum</b></td>
  <td width="25%"><b>Start date</b></td>
  <td width="25%"><b>End date</b></td>
</tr>
<?php

	// now, add all of them
	while ($result = db_fetch_results ($res)) {
	    // resolve the forum id
	    if ($result[1] == 0) {
		// it's a wildcard
		$destforum = "<i>All forums</i>";
	    } else {
		// grab the forum name
		$query = sprintf ("select name from forums where id=%s", $result[1]);
		$res2 = db_query ($query); $tmp2 = db_fetch_results ($res2);
		$destforum = $tmp2[0];
	    }

	    // add it to the list
	    printf ("<tr><td><a href=\"cp_mod.php?action=editannc&anncid=%s\">%s</a></td><td>%s</td><td>%s</td><td>%s</td></tr>", $result[0], $result[4], $destforum, $result[2], $result[3]);
	}
 ?></table><p>
<form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="createannc">
<input type="submit" value="Create announcement">
</form>
<?php

	CPShowFooter();
    }

    //
    // EditAnnc()
    //
    // This will edit an announcement.
    //
    function
    EditAnnc() {
	global $anncid;

	// grab the information
	$query = sprintf ("select title,forumid,startdate,enddate,content from announcements where id=%s", $anncid);
	$res = db_query ($query); $result = db_fetch_results ($res);

	CPShowHeader();

	// did we get any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    print "This announcement doesn't appear to exist. Perhaps it was deleted by another moderator or administrator?";
	    CPShowFooter();
	    exit;
	}

	// build the page
 ?><form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="doeditannc">
<input type="hidden" name="anncid" value="<?php echo $anncid; ?>">
<table width="100%">
 <tr>
  <td width="20%">Title</td>
  <td width="80%"><input type="text" name="the_title" value="<?php echo $result[0]; ?>"></td>
 </tr>
  <td width="20%">Forum</td>
  <td width="80%"><select name="the_forum"><?php BuildForumList ($result[1]); ?></td>
 <tr>
   <td>Start date</td>
   <td><input type="text" name="the_startdate" value="<?php echo $result[2]; ?>"></td>
 </tr>
 <tr>
   <td>End date</td>
   <td><input type="text" name="the_enddate" value="<?php echo $result[3]; ?>"></td>
  </tr>
  <tr valign="top">
    <td>Message</td>
    <td><textarea name="the_message" rows=10 cols=40><?php echo htmlspecialchars ($result[4]); ?></textarea></td>
  </tr>
</table><p>
<input type="checkbox" name="f_delete">Delete this announcement</input><p>
<center><input type="submit" value="Submit modifications"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoEditAnnc()
    //
    // This will actually edit the announcement.
    //
    function
    DoEditAnnc() {
	global $anncid, $the_title, $the_forum, $the_startdate, $the_enddate;
	global $the_message, $f_delete;

	CPShowHeader();

	// do we need to delete this announcement?
	if ($f_delete != "") {
	    // yes. kill it
	    $query = sprintf ("delete from announcements where id=%s",$anncid);
	    db_query ($query);

	    // all went ok. show the 'yay' page
 ?>The announcement has successfully been deleted.<p>
<form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="announcements">
<input type="submit" value="Back to announcement overview">
</form>
<?php
	    CPShowFooter();
	    exit;
	}

	// we only need to modify this announcement. do it

	// is the destination forum ok?
	if ($the_forum == 0) {
	    // this is an announcement for all forums. are we an admin or
	    // megamod?
	    if ((($GLOBALS["flags"] & FLAG_ADMIN) == 0) and (($GLOBALS["flags"] & FLAG_MMOD) == 0)) {
		// no, we aren't. complain
 ?>We're sorry, but only administrators and mega moderators can create announcements for all forums.
<?php
		CPShowFooter();
		exit;
	    }
	} else {
	    // are we a moderator in that forum?
	    if (IsForumMod ($the_forum) == 0) {
		// no. complain
 ?>We're sorry, but you must be a moderator of the forums in which you want to create an announcement.
<?php
		CPShowFooter();
		exit;
	    }
	}

	// all is ok. build the query
	$query = sprintf ("update announcements set title='%s',forumid=%s,startdate='%s',enddate='%s',content='%s' where id=%s",$the_title,$the_forum,$the_startdate,$the_enddate,$the_message,$anncid);
	db_query ($query);

	// it worked. show the 'victory!' page
 ?>The announcement has successfully been modified.<p>
<form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="announcements">
<input type="submit" value="Back to announcement overview">
</form>
<?php
	CPShowFooter();
    }

    //
    // CreateAnnc()
    //
    // This will show the page for announcement creation.
    //
    function
    CreateAnnc() {
	CPShowHeader();

 ?><form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="docreateannc">
<table width="100%">
 <tr>
  <td width="20%">Title</td>
  <td width="80%"><input type="text" name="the_title"></td>
 </tr>
  <td width="20%">Forum</td>
  <td width="80%"><select name="the_forum"><?php BuildForumList (0); ?></select></td>
 </tr>
 <tr>
   <td>Start date</td>
   <td><input type="text" name="the_startdate" value="<?php echo date ("Y-m-d H:i:s"); ?>"></td>
 </tr>
 <tr>
   <td>End date</td>
   <td><input type="text" name="the_enddate" value="9999-12-31 23:59:59"></td>
  </tr>
  <tr valign="top">
    <td>Message</td>
    <td><textarea name="the_message" rows=10 cols=40></textarea></td>
  </tr>
</table><p>
<center><input type="submit" value="Create Announcement"></center>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoCreateAnnc()
    //
    // This will actually add the announcement
    //
    function
    DoCreateAnnc() {
	global $anncid, $the_title, $the_forum, $the_startdate, $the_enddate;
	global $the_message, $f_delete;

	CPShowHeader();

	// is the destination forum ok?
	if ($the_forum == 0) {
	    // this is an announcement for all forums. are we an admin or
	    // megamod?
	    if ((($GLOBALS["flags"] & FLAG_ADMIN) == 0) and (($GLOBALS["flags"] & FLAG_MMOD) == 0)) {
		// no, we aren't. complain
 ?>We're sorry, but only administrators and mega moderators can create announcements for all forums.
<?php
		CPShowFooter();
		exit;
	    }
	} else {
	    // are we a moderator in that forum?
	    if (IsForumMod ($the_forum) == 0) {
		// no. complain
 ?>We're sorry, but you must be a moderator of the forums in which you want to create an announcement.
<?php
		CPShowFooter();
		exit;
	    }
	}

	// all is ok. build the query
	$query = sprintf ("insert into announcements values (NULL,'%s',%s,'%s','%s',%s,'%s',0)",$the_title,$GLOBALS["userid"],$the_startdate,$the_enddate,$the_forum,$the_message);
	db_query ($query);

	// it worked. show the 'victory!' page
 ?>The announcement has successfully been created.<p>
<form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="announcements">
<input type="submit" value="Back to announcement overview">
<?php
	CPShowFooter();
    }

    //
    // Prune()
    //
    // This will show the page for pruning messages.
    //
    function
    Prune() {
	CPShowHeader();
 ?><center>You can prune by date or by username, in any forum you moderate. Please select how you'd like to prune</center><br>
<form action="cp_mod.php" method="post">
<input type="hidden" name="action" value="doprune">
<table width="100%" border=0>
<tr>
  <td width="50%" align="center">
    <input type="radio" name="prunetype" value="date" checked>Prune by date</input><br>
    Delete all messages older than <input type="text" name="nofdays"> days
  </td>
  <td align="center">
    <input type="radio" name="prunetype" value="username">Prune by username</input><br>
    Delete all messages posted by <input type="text" name="destusername">
  </td>
</tr>
<tr>
  <td colspan=2 align="center">
    <br>Destination forum:<br>
    <select name="destforum"><?php BuildForumList (0); ?></select></td>
</tr>
</table><p>
<center><input type="submit" value="Prune"></center><p>
</form>
<?php
	CPShowFooter();
    }

    //
    // DoPrune()
    //
    // This will actually prune the messages
    //
    function
    DoPrune() {
	global $prunetype, $nofdays, $destusername, $destforum;

	CPShowHeader();

	// prune by date?
	if ($prunetype == "date") {
	    // yes. do it
	    $where_posts = "to_days(now())-to_days(timestamp)>=" . $nofdays;
	    $where_threads = "to_days(now())-to_days(lastdate)>=" . $nofdays;
	} else {
	    // no. prune by username. get the member id
	    $query = sprintf ("select id from accounts where accountname='%s'", $destusername);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    $authorid = $result[0];

	    // did this work?
	    if (db_nof_results ($res) == 0) {
		// no. complain
 ?>Account name <b><?php echo $destusername; ?></b> could not be found. Perhaps it was already deleted?
<?php
		CPShowFooter();
		exit;
	    }

	    $where_posts = "authorid=" . $authorid;
	    $where_threads = "authorid=" . $authorid;
	}

	if ($destforum != 0) {
	    $where_posts .= " and forumid=" . $destforum;
	    $where_threads .= " and forumid=" . $destforum;
	}

	// build a list of all posts to kill
	$query = sprintf ("select id,threadid,forumid from posts where " . $where_posts);
	$res = db_query ($query); $threadkill = 0; $postkill = 0;

	// delete the posts
	while ($result = db_fetch_results ($res)) {
	    // grab the information
	    $postid = $result[0]; $threadid = $result[1]; $forumid = $result[2];

	    // get rid of the post
	    $query = sprintf ("delete from posts where id=%s", $postid);
	    db_query ($query);

	    // get the last thread poster
	    $query = sprintf ("select authorid,timestamp from posts where threadid=%s order by timestamp desc limit 1", $threadid);
	    $res2 = db_query ($query); $tmp = db_fetch_results ($res2);

	    // decrement the post count and update the thread
	    if ($tmp[0] == "") { $tmp[0] = 0; }
	    $query = sprintf ("update threads set nofreplies=nofreplies-1,lastdate='%s',lastposterid=%s where id=%s", $tmp[1], $tmp[0], $threadid);
	    db_query ($query);

            // grab the last forum poster
            $query = sprintf ("select lastposterid,lastdate from threads where forumid=%s order by lastdate desc limit 1", $forumid);
            $res2 = db_query ($query); $result = db_fetch_results ($res2);

            // update the post count and last reply dates
	    if ($tmp[0] == "") { $tmp[0] = 0; }
            $query = sprintf ("update forums set nofposts=nofposts-1,lastposterid=%s,lastpost='%s' where id=%s",$result[0],$result[1],$forumid);
	    db_query ($query);

	    // increment the counter
	    $postkill++;
	}

	// do the threads
	$query = sprintf ("select id,forumid from threads where " . $where_threads);
	$res = db_query ($query);

	// delete the posts
	while ($result = db_fetch_results ($res)) {
	    // grab the information
	    $threadid = $result[0]; $forumid = $result[1];

	    // get rid of this thread
	    $query = sprintf ("delete from threads where id=%s", $threadid);
	    db_query ($query);

	    // grab the last forum poster
            $query = sprintf ("select lastposterid,lastdate from threads where forumid=%s order by lastdate desc limit 1", $forumid);
            $res2 = db_query ($query); $tmp = db_fetch_results ($res2);
	    if ($tmp[0] == "") { $tmp[0] = 0; }

            // update the post count and last reply dates
            $query = sprintf ("update forums set nofthreads=nofthreads-1,lastposterid=%s,lastpost='%s' where id=%s",$tmp[0],$tmp[1],$forumid);
	    db_query ($query);

	    // increment the counter
	    $threadkill++;
	}

	// it worked. show the 'yay' page
 ?>Thank you, we have successfully pruned <?php echo $postkill . " post"; if ($postkill != 1) { echo "s"; } ?> and <?php echo $threadkill . " thread"; if ($threadkill != 1) { echo "s"; } ?>.
<?php
    }

    //
    // DestroyCookie();
    //
    // This will get rid of the cookie
    //
    function
    DestroyCookie() {
	setcookie ("cp_authid", "", time() + 3600);

	Header ("Location: ../index.php");
    }

    // are we a mod (or admin)?
    if ((($GLOBALS["flags"] & FLAG_ADMIN) == 0) and (IsMod ($GLOBALS["userid"]) == 0) and (IsCategoryMod ($GLOBALS["userid"]) == 0) and (($GLOBALS["flags"] & FLAG_MMOD) == 0)) {
	// no. get rid of the cookie and try again
	setcookie ("cp_authid", "", time() + 3600);
	Header ("Location: ../index.php");
	exit;
    }

    // any action given?
    if ($action == "") {
	// no. show the generic, boring intro
	Intro();
	exit;
    }

    // do we need to do the announcements?
    if ($action == "announcements") {
	// yes. do them
	Announcements();
	exit;
    }

    // do we need to edit an announcement?
    if ($action == "editannc") {
	// yes. do it
	EditAnnc();
	exit;
    }

    // do we need to actually edit the announcement?
    if ($action == "doeditannc") {
	// yes. do it
	DoEditAnnc();
	exit;
    }

    // do we need to create an announcement?
    if ($action == "createannc") {
	// yes. do it
	CreateAnnc();
	exit;
    }

    // do we need to actually add the announcement?
    if ($action == "docreateannc") {
	// yes. do it
	DoCreateAnnc();
	exit;
    }

    // do we need to prune messages?
    if ($action == "prune") {
	// yes. do it
	Prune();
	exit;
    }

    // do we need to actually prune the messages?
    if ($action == "doprune") {
	// yes. do it
	DoPrune();
	exit;
    }

    // need to destroy the cookie?
    if ($action == "destroycookie") {
	// yes. do it
	DestroyCookie();
	exit;
    }
 ?>
