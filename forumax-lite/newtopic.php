<?php
    //
    // newtopic.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle posting new topics.
    //

    // we need our library
    require "lib.php";

    // is a forumid supplied?
    $forumid = trim ($forumid);
    if ($forumid == "") {
	// no. complain
	Error ("No Forum ID supplied. Check your link and try again");
    }

    // get the forum information
    $query = sprintf ("SELECT name FROM forums WHERE id=%s", $forumid);
    $res = db_query ($query);
    list ($forumname) = db_fetch_result ($res);

    // does the forum really exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	Error ("No such forum. Check your link and try again");
    }

    // is an action supplied?
    if ($action == "") {
	// no. show the page for creating new topics
	ShowHeader ($forumname . " - Post New Thread");
 ?><table width="100%">
 <tr>
  <td width="100%" align="left" class="fnormal"><a href="index.php"><?php echo $GLOBALS["forum_title"]; ?></a> > <a href="showforum.php?forumid=<?php echo $forumid; ?>"><?php echo $forumname; ?></a> > <b>Post New Thread</b></td>
 </tr>
</table>
<form action="newtopic.php" method="post">
<input type="hidden" name="action" value="newtopic">
<input type="hidden" name="forumid" value="<?php echo $forumid; ?>">
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr>
  <td colspan="2" align="center" class="fheading">Post New Thread</td>
 </tr>
 <tr class="content">
  <td width="20%" class="fnormal"><b>Username</b></td>
  <td width="80%"><input type="text" name="username" value="<?php echo htmlspecialchars ($GLOBALS["my_accountname"]); ?>"></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Password</b></td>
  <td><input type="password" name="password" value="<?php echo htmlspecialchars ($GLOBALS["my_password"]); ?>"></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Subject</b></td>
  <td><input type="text" name="subject"></td>
 <tr class="content" valign="top">
  <td class="fnormal"><b>Message</b></td>
  <td><textarea rows=15 cols=50 name="message"></textarea></td>
 </tr>
 <tr class="content">
  <td colspan="2" align="center"><br><input type="submit" value="Post New Topic"><p></td>
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
    $subject = trim ($subject); $message = trim ($message);
    if (($subject == "") or ($message == "")) {
	// no. complain
	Error ("Not all fields are filled in");
    }

    $iconid = 0;

    // ok, all looks good. create the thread
    $query = sprintf ("INSERT INTO threads VALUES (NULL,%s,'%s',%s,%s,now(),0,0)", $forumid, $subject, $GLOBALS["my_userid"], $iconid);
    db_query ($query); $threadid = db_get_insert_id();

    // create the post
    $query = sprintf ("INSERT INTO posts VALUES (NULL,%s,%s,now(),'%s')", $threadid, $GLOBALS["my_userid"], $message);
    db_query ($query);

    // grab the last post details from the forum
    $query = sprintf ("SELECT timestamp FROM posts WHERE threadid=%s ORDER BY timestamp DESC LIMIT 1", $threadid);
    list ($lastpost) = db_fetch_result (db_query ($query));

    // update the thread
    $query = sprintf ("UPDATE threads SET lastpost='%s' WHERE id=%s", $lastpost, $threadid);
    db_query ($query);

    // update the forum
    $query = sprintf ("UPDATE forums SET nofthreads=nofthreads+1,nofposts=nofposts+1,lastpost='%s' WHERE id=%s", $lastpost, $forumid);
    db_query ($query);

    // increment the user's posts
    $query = sprintf ("UPDATE accounts SET nofposts=nofposts+1,lastpost='%s' WHERE id=%s", $lastpost, $GLOBALS["my_userid"]);
    db_query ($query);

    // it worked. inform the user
    ShowHeader ($forumname . " - Thread successfully created", "showforum.php?forumid=" . $forumid);
 ?><p><table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr>
   <td class="fheading" align="center" colspan="2">Thread succesfully created</td>
 </tr>
 <tr class="content">
  <td class="fnormal" align="center">The thread has successfully been created. Please wait 2 seconds or click <a href="showforum.php?forumid=<?php echo $forumid; ?>">here</a> to return to the thread overview.</td>
 </tr>
</table>
<?php
    ShowFooter()
 ?>
