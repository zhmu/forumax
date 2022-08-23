<?php
    //
    // lockthread.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the locking and unlocking of threads.
    //

    // we need our library
    require "lib.php";

    // is a threadid supplied?
    $threadid = trim ($threadid);
    if ($threadid == "") {
	// no. complain
	Error ("No Thread ID supplied. Check your link and try again");
    }

    // get the thread information
    $query = sprintf ("SELECT name,forumid,flags FROM threads WHERE id=%s", $threadid);
    $res = db_query ($query); list ($threadname, $forumid, $thread_flags) = db_fetch_result ($res);
    if (db_nof_results ($res) == 0) {
	// there's no such thread. complain
	Error ("No such thread. Please check your link");
    }

    // get the forum information
    $query = sprintf ("SELECT name FROM forums WHERE id=%s", $forumid);
    $res = db_query ($query); list ($forumname) = db_fetch_result ($res);

    // is an action supplied?
    if ($action == "") {
	// no. need to lock the thread?
	if (($thread_flags & FLAG_THREAD_LOCKED) == 0) {
	    //  no. show the page for locking threads
	    ShowHeader ($forumname . " - " . $threadname . " - Lock Thread");
 ?><table width="100%">
 <tr>
  <td width="100%" align="left" class="fnormal"><a href="index.php"><?php echo $GLOBALS["forum_title"]; ?></a> > <a href="showforum.php?forumid=<?php echo $forumid; ?>"><?php echo $forumname; ?></a> > <b><?php echo $threadname; ?></b></td>
 </tr>
</table>
<form action="lockthread.php" method="post">
<input type="hidden" name="action" value="lockthread">
<input type="hidden" name="threadid" value="<?php echo $threadid; ?>">
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr>
   <td class="fheading" align="center" colspan="2">Lock thread: <?php echo $threadname; ?></td>
 </tr>
 <tr class="content">
  <td colspan="2" class="fnormal" align="center"><br>Are you sure you want to lock this thread? This will disable anyone replying to it. Only administrators and moderators can lock a thread.<p></td>
 </tr>
 <tr class="content">
  <td width="20%" class="fnormal">&nbsp;<b>Username</b></td>
  <td width="80%"><input type="text" name="username" value="<?php echo htmlspecialchars ($GLOBALS["my_accountname"]); ?>"></td>
 </tr>
 <tr class="content">
  <td class="fnormal">&nbsp;<b>Password</b></td>
  <td><input type="password" name="password" value="<?php echo htmlspecialchars ($GLOBALS["my_password"]); ?>"></td>
 </tr>
 <tr class="content">
  <td colspan="2" align="center"><br><input type="submit" value="Lock thread"><p></td>
 </tr>
</table>
</form>
<?php
	} else {
	    // yes. show the page for unlocking threads
	    ShowHeader ($forumname . " - " . $threadname . " - Unlock Thread");
 ?><table width="100%">
 <tr>
  <td width="100%" align="left" class="fnormal"><a href="index.php"><?php echo $GLOBALS["forum_title"]; ?></a> > <a href="showforum.php?forumid=<?php echo $forumid; ?>"><?php echo $forumname; ?></a> > <b><?php echo $threadname; ?></b></td>
 </tr>
</table><p>
<center>Are you sure you want to unlock this thread? This will enable anyone to reply to it. Only administrators and moderators can unlock a thread.<p>
<form action="lockthread.php" method="post">
<input type="hidden" name="action" value="unlockthread">
<input type="hidden" name="threadid" value="<?php echo $threadid; ?>">
<table width="100%" border=1>
 <tr>
  <td width="20%">&nbsp;Username</td>
  <td width="80%"><input type="text" name="username" value="<?php echo htmlspecialchars ($GLOBALS["my_accountname"]); ?>"></td>
 <tr>
  <td width="20%">&nbsp;Password</td>
  <td width="80%"><input type="password" name="password" value="<?php echo htmlspecialchars ($GLOBALS["my_password"]); ?>"></td>
 </tr>
</table><p>
<center><input type="submit" value="Unlock thread"></center>
</form>
<?php
	}

	ShowFooter();

	exit;
    }

    // verify the username and password pair
    VerifyAccount ($username, $password);

    // are we a moderator of this forum?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
	Error ("Sorry, but you are not a moderator of this forum");
    }

    // okay, need to lock or unlock the thread?
    if ($action == "lockthread") {
	// lock
	$query = sprintf ("UPDATE threads SET flags=flags|%s WHERE id=%s", FLAG_THREAD_LOCKED, $threadid);
	$done = "locked";
    } else {
	// unlock
	$query = sprintf ("UPDATE threads SET flags=flags&(~%s) WHERE id=%s", FLAG_THREAD_LOCKED, $threadid);
	$done = "unlocked";
    }

    // execute the query
    db_query ($query);

    // yay, it worked. inform the user
    ShowHeader ($forumname . " - " . $threadname . " - Thread successfully " . $done, "showforum.php?forumid=" . $forumid);
 ?>The thread has successfully been <?php echo $done; ?>. Please wait 2 seconds or click <a href="showforum.php?forumid=<?php echo $forumid; ?>">here</a> to return to the thread overview.
<?php
    ShowFooter();
 ?>
