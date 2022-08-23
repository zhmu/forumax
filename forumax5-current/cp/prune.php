<?php 
    //
    // prune.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the pruning of topics.
    //

    // we need our library, too
    require "lib.php";

    //
    // Overview()
    //
    // This will show the page for pruning messages.
    //
    function
    Overview() {
	// build the page
	cpShowHeader("Prune posts", "Overview");
 ?><center>You can prune by date or by username, in any forum you moderate. Please select how you'd like to prune</center><br>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="prune">
<center><table width="100%" bgcolor="#ffffff" cellspacing=1 cellpadding=3 class="tab5">
  <tr class="tab3">
    <td colspan=3 align="center">Prune</tr>
  </tr>
  <tr class="tab2">
    <td width="5%" align="center"><input type="radio" name="prunetype" value="date" checked></td>
    <td width="20%" align="left">&nbsp;Prune by date</td>
    <td width="75%" align="left">&nbsp;Delete all messages older than <input type="text" name="nofdays"> days</td>
  </tr>
  <tr class="tab2">
    <td align="center"><input type="radio" name="prunetype" value="username"></td>
    <td align="left">&nbsp;Prune by username</td>
    <td align="left">&nbsp;Delete all messages posted by <input type="text" name="destusername"></td>
  </tr>
  <tr class="tab2">
    <td colspan=3 align="center">
      <br>Destination forum:<br>
      <select name="destforum"><?php BuildForumList (0); ?></select><p>
    </td>
  </tr>
</table><p>
<center><input type="submit" value="Prune"></center><p>
</form>
<?php
	cpShowFooter();
    }

    //
    // Prune()
    //
    // This will actually prune the messages
    //
    function
    Prune() {
	// fetch the arguments
	$prunetype = $_REQUEST["prunetype"];
	$nofdays = $_REQUEST["nofdays"];
	$destusername = $_REQUEST["destusername"];
	$destforum = $_REQUEST["destforum"];

	// show the header
	cpShowHeader("Prune posts", "Status");

	// prune by date?
	if ($prunetype == "date") {
	    // yes. do it
	    $where_posts = "to_days(now())-to_days(timestamp)>=" . $nofdays;
	    $where_threads = "to_days(now())-to_days(lastdate)>=" . $nofdays;
	
	    // got a number of days?
	    if (($nofdays == "") or (preg_replace ("/\D/", "", $nofdays) != $nofdays)) {
		// no. complain
 ?>If you chose to prune by date, you must enter a valid timespan. This must be an numeric value.
<?php
		cpShowFooter();
		exit;
	    }
	} else {
	    // no. prune by username. get the member id
	    $authorid = GetMemberID ($destusername);

	    // did this work?
	    if ($authorid == "") {
		// no. complain
 ?>Account name <b><?php echo $destusername; ?></b> could not be found.
<?php
		cpShowFooter();
		exit;
	    }

	    $where_posts = "authorid=" . $authorid;
	    $where_threads = "authorid=" . $authorid;
	}

	// is a forum selected?
	if ($destforum != 0) {
	    // yes. do we moderate this forum?
	    if ((IsForumMod ($destforum) == 0) or ($GLOBALS["MASTER_ACCESS"] != 0)) {
	        $where_posts .= " and forumid=" . $destforum;
	        $where_threads .= " and forumid=" . $destforum;
	    } else {
		// no. complain	
		print "We're sorry, but you are not a moderator of that forum";
		cpShowFooter();
		exit;
	    }
	} else {
	    // are we an admin, megamod or master?
	    if (($GLOBALS["ADMIN_ACCESS"] == 0) and (($GLOBALS["cp_accountflags"] & FLAG_MMOD) == 0) and ($GLOBALS["MASTER_ACCESS"] == 0)) {
		// no. complain
		print "We're sorry, but in order to be able to prune from all forums you have to be an administrator, mega moderator or master";
		cpShowFooter();
		exit;
	    }
	}

	// get the time
 	$mtime = microtime();
	$mtime = explode(" ",$mtime);
 	$start = $mtime[1] + $mtime[0];

	// build a list of all posts to kill
	$query = sprintf ("SELECT id,threadid,forumid FROM posts WHERE " . $where_posts);
	$res = db_query ($query); $threadkill = 0; $postkill = 0;

	// delete the posts
	while ($result = db_fetch_results ($res)) {
	    // grab the information
	    $postid = $result[0]; $threadid = $result[1]; $forumid = $result[2];

	    // get rid of the post
	    $query = sprintf ("DELETE FROM posts WHERE id=%s", $postid);
	    db_query ($query);

	    // get the last thread poster
	    $query = sprintf ("SELECT authorid,timestamp FROM posts WHERE threadid=%s ORDER BY timestamp DESC LIMIT 1", $threadid);
	    $res2 = db_query ($query); $tmp = db_fetch_results ($res2);

	    // decrement the post count and update the thread
	    if ($tmp[0] == "") { $tmp[0] = 0; }
	    $query = sprintf ("UPDATE threads SET nofreplies=nofreplies-1,lastdate='%s',lastposterid=%s WHERE id=%s", $tmp[1], $tmp[0], $threadid);
	    db_query ($query);

            // grab the last forum poster
            $query = sprintf ("SELECT lastposterid,lastdate FROM threads WHERE forumid=%s ORDER BY lastdate DESC LIMIT 1", $forumid);
            $res2 = db_query ($query); $result = db_fetch_results ($res2);

            // update the post count and last reply dates
	    if ($tmp[0] == "") { $tmp[0] = 0; }
            $query = sprintf ("UPDATE forums SET nofposts=nofposts-1,lastposterid=%s,lastpost='%s' WHERE id=%s",$result[0],$result[1],$forumid);
	    db_query ($query);

	    // increment the counter
	    $postkill++;
	}

	// do the threads
	$query = sprintf ("SELECT id,forumid FROM threads WHERE " . $where_threads);
	$res = db_query ($query);

	// delete the posts
	while ($result = db_fetch_results ($res)) {
	    // grab the information
	    $threadid = $result[0]; $forumid = $result[1];

	    // get rid of this thread
	    $query = sprintf ("DELETE FROM threads WHERE id=%s", $threadid);
	    db_query ($query);

	    // grab the last forum poster
            $query = sprintf ("SELECT lastposterid,lastdate FROM threads WHERE forumid=%s ORDER BY lastdate DESC LIMIT 1", $forumid);
            $res2 = db_query ($query); $tmp = db_fetch_results ($res2);
	    if ($tmp[0] == "") { $tmp[0] = 0; }

            // update the post count and last reply dates
            $query = sprintf ("UPDATE forums SET nofthreads=nofthreads-1,lastposterid=%s,lastpost='%s' WHERE id=%s",$tmp[0],$tmp[1],$forumid);
	    db_query ($query);

	    // increment the counter
	    $threadkill++;
	}

	// figure out how long it took us
	$mtime = explode (" ", microtime());
	$prunetime = sprintf ("%f", ($mtime[1] + $mtime[0]) - $GLOBALS["startime"]);

	// it worked. show the 'yay' page
 ?>Thank you, we have successfully pruned <b><?php echo $postkill . "</b> post"; if ($postkill != 1) { echo "s"; } ?> and <b><?php echo $threadkill . "</b> thread"; if ($threadkill != 1) { echo "s"; } ?> in <?php echo $prunetime ?> seconds.<p>
<form action="<?php echo $PHP_SELF; ?>" method="post">
<center><input type="submit" value="Return to prune overview"></center>
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_PRUNE);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // need to show the overview?
    if (($action == "") or ($action == "overview")) {
	// yes. show the overview
	Overview();
    } elseif ($action == "prune") {
	// we need to prune. do it
	Prune();
    }
 ?>
