<?php
    //
    // index.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the displayal of the forum itself.
    //

    // we need our library
    require "lib.php";

    // show the header
    ShowHeader("Powered by ForuMAX-LITE");

    // build the layout
 ?><table width="100%">
  <tr>
    <td width="100%" align="left" class="fnormal"><b><?php echo $GLOBALS["forum_title"]; ?></b></td>
  </tr>
</table>
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
  <tr>
    <td width="5%"  align="center" class="fheading" height="25"></td>
    <td width="35%" align="center" class="fheading">Forum Name</td>
    <td width="8%"  align="center" class="fheading">Posts</td>
    <td width="8%"  align="center" class="fheading">Threads</td>
    <td width="22%" align="center" class="fheading">Last post</td>
    <td width="22%" align="center" class="fheading">Moderator</td>
  </tr>
<?php
    // select all forums from the database
    $query = sprintf ("SELECT id,name,nofthreads,nofposts,lastpost,moderator FROM forums");
    $res = db_query ($query);

    // list them all
    while (list ($forumid, $forumname, $nofthreads, $nofposts, $lastpost, $modid) = db_fetch_result ($res)) {
	// get the moderator name
	$moderator = GetAccountName ($modid);

	// list the forum
	printf ("<tr class=\"content\"><td valign=\"center\" align=\"center\">&nbsp;</td><td valign=\"center\" class=\"fnormal\"><a href=\"showforum.php?forumid=%s\" class=\"threadlink\">%s</td><td valign=\"center\" align=\"center\" class=\"fnormal\">%s</font></td><td valign=\"center\" align=\"center\" class=\"fnormal\">%s</td><td valign=\"center\" align=\"center\" class=\"fnormal\">%s</td><td valign=\"center\" align=\"center\" class=\"fnormal\">%s</td></tr>", $forumid, $forumname, $nofposts, $nofthreads, $lastpost, $moderator);
    }

 ?></table><p>
<?php

    ShowFooter();
 ?>
