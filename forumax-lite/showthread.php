<?php
    //
    // showthread.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the displayal of forum threads.
    //

    // we need our library
    require "lib.php";

    // is a threadid given?
    $threadid = trim ($threadid);
    if ($threadid == "") {
	// no. complain
	Error ("No Thread ID supplied. Check your link and try again");
    }

    // get the thread information
    $query = sprintf ("SELECT name,forumid,flags FROM threads WHERE id=%s", $threadid);
    $res = db_query ($query);
    list ($threadname, $forumid, $thread_flags) = db_fetch_result ($res);

    // does the thread really exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	Error ("No such thread. Check your link and try again");
    }

    // get the forum information
    $query = sprintf ("SELECT name FROM forums WHERE id=%s", $forumid);
    list ($forumname) = db_fetch_result (db_query ($query));

    // show the header
    ShowHeader($forumname . " - "  . $threadname);

    // build the new topic/post reply links
    $links = "<a href=\"newtopic.php?forumid=$forumid\"><b>[New Topic]</b></a>  ";

    // is thread locked?
    if (($thread_flags & FLAG_THREAD_LOCKED) != 0) {
	// yes. we can't reply here
	$links .= "<b>[<font color=\"#ff0000\">Thread Locked</font>]</b>";
    } else {
	// no. it's ok to reply here
	$links .= "<a href=\"postreply.php?threadid=$threadid\"><b>[Reply]</b></a>";
    }

    // build the layout
 ?><table width="100%">
  <tr>
    <td width="60%" align="left" class="fnormal"><a href="index.php"><?php echo $GLOBALS["forum_title"]; ?></a> > <a href="showforum.php?forumid=<?php echo $forumid; ?>"><?php echo $forumname; ?></a> > <b><?php echo $threadname; ?></b></td>
    <td width="40%" align="right" class="fnormal"><?php echo $links; ?></td>
</table>
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
  <tr>
    <td class="fheading" width="20%" align="center">Author</td>
    <td class="fheading" width="80%" align="center">Message</td>
  </tr>
<?php
    // grab all posts from the database
    $query = sprintf ("SELECT authorid,content,timestamp FROM posts WHERE threadid=%s ORDER BY timestamp ASC", $threadid);
    $res = db_query ($query);

    // list them all
    while (list ($authorid, $content, $timestamp) = db_fetch_result ($res)) {
	// account information already received?
	if ($ACCOUNT_CACHE[$authorid] == "") {
	    // no. get the information
	    $query = sprintf ("SELECT name,joindate,nofposts,flags FROM accounts WHERE id=%s", $authorid);
	    $resx = db_query ($query); list ($user_name, $user_joindate, $user_nofposts, $user_flags) = db_fetch_result ($resx);

	    // got any results?
	    if (db_nof_results ($resx) == 0) {
		// no. go to the defaults
		$user_name = "?"; $user_joindate = "?";
		$user_nofposts = "?";
	    }

	    // add it to the cache
	    $ACCOUNT_CACHE[$authorid] = $user_name . "|^|" . $user_joindate . "|^|" . $user_nofposts . "|^|" . $user_flags;
	} else {
	    // grab it from the cache
	    list ($user_name, $user_joindate, $user_nofposts, $user_flags) = explode ("|^|", $ACCOUNT_CACHE[$authorid]);
	}

	// list the entry
	printf ("<tr class=\"content\"><td valign=\"top\" class=\"fsmall\"><a class=\"sml\" href=\"finger.php?accountid=%s\"><b>%s</b></a><br><small><b>%s</b><br><b>Number of posts</b>: %s<br><b>Join date</b>: %s</td><td valign=\"top\" class=\"fnormal\"><img src=\"images/icon1.gif\" alt=\"\"> <small>Posted at %s</small><hr width=\"99%%\" size=\"1\">%s</td></tr>", $authorid, $user_name, GetAccountStatus ($authorid, $user_flags), $user_nofposts, $user_joindate, $timestamp, nl2br ($content));
    }
 ?></table><table width="100%">
  <tr>
    <td width="100%" align="right" class="fnormal"><?php echo $links; ?></td>
  </tr>
</table>
<table width="100%">
 <tr>
  <td width="100%" class="fsmall"><b>Options</b><br><a class="sml" href="lockthread.php?threadid=<?php echo $threadid; ?>">Lock / Unlock Thread</a></td>
 </tr>
</table>
<?php
    // show the footer
    ShowFooter();
 ?>
