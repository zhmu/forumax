<?php
    //
    // index.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the displayal of forum threads.
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
    $query = sprintf ("SELECT name,moderator FROM forums WHERE id=%s", $forumid);
    $res = db_query ($query);
    list ($forumname, $forummod) = db_fetch_result ($res);

    // does the forum really exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	Error ("No such forum. Check your link and try again");
    }

    // show the header
    ShowHeader($forumname);

    // build the layout
 ?><table width="100%">
  <tr>
    <td width="60%" align="left" class="fnormal"><a href="index.php"><?php echo $GLOBALS["forum_title"]; ?></a> > <b><?php echo $forumname; ?></b></td>
    <td width="40%" align="right"><a href="newtopic.php?forumid=<?php echo $forumid; ?>"><b>[New Thread]</b></a></td>
  </tr>
</table>
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
  <tr class="content">
    <td width="3%"  align="center" class="fheading">&nbsp;</td>
    <td width="3%"  align="center" class="fheading">&nbsp;</td>
    <td width="47%" align="center" class="fheading">Thread Name</td>
    <td width="20%" align="center" class="fheading">Author</td>
    <td width="7%"  align="center" class="fheading">Replies</td>
    <td width="20%" align="center" class="fheading">Last reply</td>
  </tr>
<?php
    // grab all threads from the database
    $query = sprintf ("SELECT id,name,authorid,nofreplies,lastpost,iconid,flags FROM threads WHERE forumid=%s ORDER BY lastpost DESC", $forumid);
    $res = db_query ($query);

    // list them all
    while (list ($threadid, $threadname, $authorid, $nofreplies, $lastpost, $iconid, $flags) = db_fetch_result ($res)) {
	// grab the author name
	$authorname = GetAccountName ($authorid);

	// locked thread?
	if (($flags & FLAG_THREAD_LOCKED) != 0) {
	    // yes. grab the icon
	    $locked = "<img src=\"images/lock.gif\" alt=\"\">";
	} else {
	    // no. nothing then
	    $locked = "&nbsp;";
	}

	// list the entry
	printf ("<tr class=\"content\"><td align=\"center\">%s</td><td align=\"center\">&nbsp;</td><td align=\"left\" class=\"fnormal\"><a href=\"showthread.php?threadid=%s\" class=\"threadlink\">%s</a></td><td align=\"center\" class=\"fnormal\" valign=\"center\"><a href=\"finger.php?accountid=%s\">%s</a></td><td align=\"center\" class=\"fnormal\"  valign=\"center\">%s</td><td align=\"center\" class=\"fnormal\"  valign=\"center\">%s</td></tr>", $locked, $threadid, $threadname, $authorid, $authorname, $nofreplies, $lastpost);
    }
 ?></table>
<?php
    // show the footer
    ShowFooter();
 ?>
