<?php
    //
    // postreply.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the posting of replies.
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
    $res = db_query ($query); list ($threadname, $forumid, $flags) = db_fetch_result ($res);
    if (db_nof_results ($res) == 0) {
	// there's no such thread. complain
	Error ("No such thread. Please check your link");
    }

    // is this thread locked?
    if (($flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. complain
	Error ("Sorry, but this thread has been locked. No more replies allowed");
    }

    // get the forum information
    $query = sprintf ("SELECT name FROM forums WHERE id=%s", $forumid);
    $res = db_query ($query); list ($forumname) = db_fetch_result ($res);

    // is an action supplied?
    if ($action == "") {
	// no. show the page for posting replies
	ShowHeader ($forumname . " - " . $threadname . " - Post Reply");

 ?><table width="100%">
 <tr>
  <td width="100%" align="left" class="fnormal"><a href="index.php"><?php echo $GLOBALS["forum_title"]; ?></a> > <a href="showforum.php?forumid=<?php echo $forumid; ?>"><?php echo $forumname; ?></a> > <a href="showthread.php?threadid=<?php echo $threadid; ?>"><?php echo $threadname; ?></a> > <b>Post New Reply</b></td>
 </tr>
</table>
<form action="postreply.php" method="post">
<input type="hidden" name="action" value="postreply">
<input type="hidden" name="threadid" value="<?php echo $threadid; ?>">
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr class="content">
  <td colspan="2" align="center" class="fheading">Post Reply</td>
 </tr>
 <tr class="content">
  <td width="20%" class="fnormal"><b>Username</b></td>
  <td width="80%"><input type="text" name="username" value="<?php echo htmlspecialchars ($GLOBALS["my_accountname"]); ?>"></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Password</b></td>
  <td><input type="password" name="password" value="<?php echo htmlspecialchars ($GLOBALS["my_password"]); ?>"></td>
 </tr>
 <tr class="content" valign="top">
  <td class="fnormal"><b>Message</b></td>
  <td><textarea rows=15 cols=50 name="message"></textarea></td>
 </tr>
 <tr class="content">
  <td align="center" colspan="2"><br><input type="submit" value="Post Reply"><p></td>
 </tr>
</table>
</form>
<?php
	ShowFooter();

	exit;
    }

    // verify the username and password pair
    VerifyAccount ($username, $password);

    // are all fields filled in?
    $message = trim ($message);
    if ($message == "") {
	// no. complain
	Error ("Not all fields are filled in");
    }

    // ok, all looks good. post the reply
    $query = sprintf ("INSERT INTO posts VALUES (NULL,%s,%s,now(),'%s')", $threadid, $GLOBALS["my_userid"], $message);
    db_query ($query);

    // grab the last date
    $query = sprintf ("SELECT timestamp FROM posts WHERE threadid=%s ORDER BY timestamp DESC LIMIT 1", $threadid);
    list ($lastpost) = db_fetch_result (db_query ($query));

    // update the thread details
    $query = sprintf ("UPDATE threads SET lastpost='%s',nofreplies=nofreplies+1 WHERE id=%s", $lastpost, $threadid);
    db_query ($query);

    // update the forum details
    $query = sprintf ("UPDATE forums SET nofposts=nofposts+1,lastpost='%s' WHERE id=%s", $lastpost, $forumid);
    db_query ($query);

    // increment the user's posts
    $query = sprintf ("UPDATE accounts SET nofposts=nofposts+1,lastpost='%s' WHERE id=%s", $lastpost, $GLOBALS["my_userid"]);
    db_query ($query);

    // it worked. inform the user
    ShowHeader ($forumname . " - " . $threadname . " - Reply successfully posted", "showthread.php?threadid=" . $threadid);
 ?><p><table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr>
   <td class="fheading" align="center" colspan="2">Reply succesfully posted</td>
 </tr>
 <tr class="content">
  <td class="fnormal" align="center">The reply has successfully been posted. Please wait 2 seconds or click <a href="showthread.php?threadid=<?php echo $threadid; ?>">here</a> to return to the thread.</td>
 </tr>
</table>
<?php
    ShowFooter()
 ?>
