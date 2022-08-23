$SKIN["welcome_forumlist"]="<font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <b>\$cat_title</b></font><br>
<table width=\"100%\">
  <tr>
    <td width=\"50%\" align=\"left\"><font size=1 face=\"{fontface}\">Hi <b>\$username</b>!<br>\$birthdays</font></td>
    <td width=\"50%\" align=\"right\"><font size=1 face=\"{fontface}\"><b>\$nofthreads</b> threads and <b>\$nofposts</b> posts<br>Number of members: <b>\$nofmembers</b><br>Greetings to our newest member, <a href=\"finger.php?accountid=\$newmemberid\">\$newmembername</a></font></td>
  </tr>
</table>
<!-- forum list -->
<table width=\"100%\" cellspacing=1 cellpadding=3>
<tr bgcolor=\"#2020f0\">
  <td width=\"30%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Forum Name</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Posts</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Threads</b></font></td>
  <td width=\"30%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Last post</b></font></td>
  <td width=\"20%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Moderator</b></font></td>
</tr>
<!-- show the actual listing of the forums -->
\$forumlist
</table><p>
<!-- show the online users -->
<table width=\"100%\" cellspacing=1 cellpadding=1 border=0>
<tr bgcolor=\"#2020f0\">
 <td width=\"100%\" colspan=5><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">There are currently \$nofonlinemembers member(s) and \$nofonlineguests guest(s) online</td>
</tr>
<tr bgcolor=\"#cfd9ff\">
 <td><font face=\"{fontface}\" size=1>\$onlinemembers</font></td>
</tr>
</table>";
$SKINTITLE["welcome_forumlist"]="\$forums_title Powered by ForuMAX";
$SKINREFRESH["welcome_forumlist"]="";
$SKIN["stylesheet"]="a { font-family: \"{fontface}\"; color: #0000ff; }
a:hover { text-decoration: none; font; }
.forumlink { color: #202080; font: bold; font-family: \"{fontface}\"; font-size: 13 px; }
.pmlink { color: #ffff00; font-family: \"{fontface}\"; font-size: 13 px; }
.pm_userlink { color: #ffff00; font-family: \"{fontface}\"; font-size: 13 px; }
.threadlink { font-family: \"{fontface}\"; font-size: 13 px; }";
$SKINTITLE["stylesheet"]="";
$SKINREFRESH["stylesheet"]="";
$SKIN["forum_list"]="<tr bgcolor=\"#cfd9ff\">
  <td valign=\"top\"><font face=\"{fontface}\" size=2><a href=\"showforum.php?forumid=\$forumid\" class=\"forumlink\">\$forumname</a></font><br><font face=\"{fontface}\" size=1>\$description</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$nofposts</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$nofthreads</font></td>
  <td valign=\"top\" align=\"center\"><font size=1 face=\"{fontface}\" color=\"#000000\">\$lastpost</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$mods</font></td>
</tr>";
$SKINTITLE["forum_list"]="";
$SKINREFRESH["forum_list"]="";
$SKIN["lastpost"]="\$lastpost<br>by <a href=\"finger.php?accountid=\$lastposterid\">\$lastposter</a>";
$SKINTITLE["lastpost"]="";
$SKINREFRESH["lastpost"]="";
$SKIN["threadpage"]="<form action=\"showforum.php\" method=\"post\"><input type=\"hidden\" name=\"forumid\" value=\"\$forumid\"><font face=\"{fontface}\" size=2><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <b>\$forumtitle</b></font><p>
<font face=\"{fontface}\" size=2>Show topics from <select name=\"dayspan\"><option value=\"1\" [[\$dayspan==1&&selected]]>last day</option><option value=\"2\" [[\$dayspan==2&&selected]]>last 2 days</option><option value=\"7\" [[\$dayspan==7&&selected]]>last week</option><option value=\"31\" [[\$dayspan==31&&selected]]>last month</option><option value=\"365\" [[\$dayspan==365&&selected]]>last year</option><option value=\"0\" [[\$dayspan==0&&selected]]>all topics</option></select></font> <input type=\"submit\" value=\"OK\"><br>
<table width=\"100%\">
 <tr>
  <td width=\"50%\" align=\"left\"><font size=1 face=\"{fontface}\">(Moderated by \$modlist)</td>
  <td width=\"40%\" align=\"right\">\$newtopictext</td>
  <td width=\"10%\" align=\"center\">&nbsp;</td>
 </tr>
</table><table width=\"100%\" cellspacing=1 cellpadding=2>
<tr bgcolor=\"#2020f0\">
  <td width=\"2%\" align=\"center\">&nbsp;</td>
  <td width=\"2%\" align=\"center\">&nbsp;</td>
  <td width=\"32%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Thread Name</b></font></td>
  <td width=\"12%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Author</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Replies</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Views</b></font></td>
  <td width=\"15%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Last reply</b></font></td>
</tr>
<!-- show the actual listing of the announcements -->
\$announcementlist
<!-- show the actual listing of the threads -->
\$threadlist
</table></form>";
$SKINTITLE["threadpage"]="\$forumtitle";
$SKINREFRESH["threadpage"]="";
$SKIN["thread_usermod"]="<a href=\"finger.php?accountid=\$objectid\">\$objectname</a>";
$SKINTITLE["thread_usermod"]="";
$SKINREFRESH["thread_usermod"]="";
$SKIN["thread_groupmod"]="the <a href=\"finger.php?groupid=\$objectid\">\$objectname</a> group";
$SKINTITLE["thread_groupmod"]="";
$SKINREFRESH["thread_groupmod"]="";
$SKIN["thread_list"]="<tr bgcolor=\"#cfd9ff\">
  <td align=\"center\">\$lockedthread</td>
  <td align=\"center\"><img src=\"{images_url}/icon\$icon.gif\"></td>
  <td><a href=\"showthread.php?threadid=\$threadid\" class=\"threadlink\">\$threadtitle</a> \$pagelist</td>
  <td valign=\"top\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$authorname</font></td>
  <td valign=\"top\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$nofreplies</font></td>
  <td valign=\"top\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$nofviews</font></td>
  <td valign=\"top\" align=\"center\"><font size=1 face=\"{fontface}\" color=\"#000000\">\$lastreply<br>by <a href=\"finger.php?accountid=\$lastreplyerid\">\$lastreplyer</a></font></td>
</tr>";
$SKINTITLE["thread_list"]="";
$SKINREFRESH["thread_list"]="";
$SKIN["postpage"]="<table width=\"100%\">
 <tr>
  <td width=\"50%\" align=\"left\"><font face=\"{fontface}\" size=2><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"40%\" align=\"right\">\$newtopictext \$replytext</td>
  <td width=\"10%\" align=\"center\">&nbsp;</td>
 </tr>
</table>
\$locktext
\$pagelist
<table width=\"100%\" cellspacing=1 cellpadding=2>
<tr bgcolor=\"#2020f0\">
  <td width=\"15%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Author</b></font></td>
  <td width=\"85%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Message</b></font></td>
</tr>
<!-- show the actual listing of the post -->
\$postlist
</table>
\$pagelist<br>
<center>\$thread_locked</center><p>
<table width=\"100%\">
  <tr valign=\"top\">
    <td width=\"50%\" align=\"left\"><font size=1 face=\"{fontface}\"><b>User options</b><br><a href=\"reportthread.php?threadid=\$threadid\">Report this thread to a moderator</a><br><a href=\"printthread.php?threadid=\$threadid\">Print this thread</a></font></td>
    <td width=\"50%\" align=\"right\"><font size=1 face=\"{fontface}\"><b>Admin Options</b><br>
<a href=\"lockthread.php?threadid=\$threadid\">Lock / Unlock Thread</a><br>
<a href=\"movethread.php?threadid=\$threadid\">Move Thread</a><br>
<a href=\"deletethread.php?threadid=\$threadid\">Delete Thread</a><br><a href=\"edittitle.php?threadid=\$threadid\">Edit Thread Title</a></font></td>
  </tr>
</table>";
$SKINTITLE["postpage"]="\$forumname - \$threadtitle";
$SKINREFRESH["postpage"]="";
$SKIN["post_list"]="<tr bgcolor=\"#cfd9ff\">
  <td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\"><a href=\"finger.php?accountid=\$authorid\">\$author</a></font><br>
\$editpost \$deletepost \$quotepost \$pmuser \$customtype[3] \$customtype[4] \$customtype[6] \$customtype[9]<br>
<font color=\"#000000\" size=1 face=\"{fontface}\">\$author_status<br>
<b>Number of posts: </b>\$author_nofposts<br>
<b>Joined on: </b>\$author_joindate</font><br>
\$customfields</td>
  <td valign=\"top\"><table width=\"100%\"><tr><td width=\"95%\"><font size=1 face=\"{fontface}\" color=\"#000000\"><img src=\"{images_url}/icon\$icon.gif\"> Posted at \$timestamp</font></td><td width=\"5%\" align=\"right\">\$viewip</td></tr></table><hr><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$message</font></td>
</tr>";
$SKINTITLE["post_list"]="";
$SKINREFRESH["post_list"]="";
$SKIN["header_visitor"]="<table width=\"100%\">
  <tr>
    <td width=\"50%\"><a href=\"index.php\"><img src=\"\$forum_image\" border=0 alt=\"[ForuMAX]\"></a></td>
    <td width=\"40%\" align=\"right\"><font size=1 face=\"{fontface}\"><a href=\"index.php?action=login\">Log in</a> | <a href=\"profile.php\">Edit profile</a> | <a href=\"register.php\">Register</a></font></td>
    <td width=\"10%\"> </td>
  </tr>
</table><p>";
$SKINTITLE["header_visitor"]="";
$SKINREFRESH["header_visitor"]="";
$SKIN["footer_visitor"]="<p><center><font size=1 face=\"{fontface}\">This page was generated in \$buildtime seconds<p>
<!-- do not remove this copyright notice! -->
Powered by <a href=\"http://www.forumax.com\">ForuMAX</a> \$VERSION<br>© 1999-2001 Rink Springer</font></center>
<!-- do not remove this copyright notice! -->";
$SKINTITLE["footer_visitor"]="";
$SKINREFRESH["footer_visitor"]="";
$SKIN["header_member"]="<table width=\"100%\">
  <tr>
    <td width=\"50%\"><a href=\"index.php\"><img src=\"\$forum_image\" border=0 alt=\"[ForuMAX]\"></a></td>
    <td width=\"40%\" align=\"right\"><font size=1 face=\"{fontface}\">Logged in as <b>\$username</b> | <a href=\"profile.php\">Edit profile</a> | <a href=\"cp/\">Control Panel</a> | <a href=\"pm.php\">Private Messaging</a> | <a href=\"index.php?action=logout\">Logout</a><br>\$newpm</font></td>
    <td width=\"10%\"> </td>
  </tr>
</table><p>";
$SKINTITLE["header_member"]="";
$SKINREFRESH["header_member"]="";
$SKIN["footer_member"]="<p><center><font size=1 face=\"{fontface}\">This page was generated in \$buildtime seconds<p>
<!-- do not remove this copyright notice! -->
Powered by <a href=\"http://www.forumax.com\">ForuMAX</a> \$VERSION<br>© 1999-2001 Rink Springer</font></center>
<!-- do not remove this copyright notice! -->";
$SKINTITLE["footer_member"]="";
$SKINREFRESH["footer_member"]="";
$SKIN["page_login"]="<form action=\"index.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"dologin\">
<font size={fontsize} face=\"{fontface}\" color=\"#000000\">When you log in, a cookie will be set so the forum will remember who you are, saving you the trouble of typing your password and such. Make sure no one has access to this computer, though, or they'll be able to retrieve your password!</font><p>
<table>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Account name</font></td>
    <td><input type=\"text\" name=\"the_accountname\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Password</font></td>
    <td><input type=\"password\" name=\"the_password\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Cookie duration</font></td>
    <td><select name=\"cookie_duration\"><option value=\"3600\" selected>One hour</option><option value=\"7200\">Two hours</option><option value=\"86400\">One day</option><option value=\"604800\">One week</option><option value=\"2678400\">One month</option><option value=\"31536000\">One year</option></select></td>
  </tr>
</table><p>
<input type=\"checkbox\" name=\"invisible\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Invisible mode: do not show your online status to others</font></input><p>
<input type=\"submit\" value=\"Log in!\">
</form>";
$SKINTITLE["page_login"]="Log in to the forums";
$SKINREFRESH["page_login"]="";
$SKIN["newtopic_ok"]="<a href=\"newtopic.php?forumid=\$forumid\"><img src=\"{images_url}/newtop.jpg\" alt=\"[New Topic]\" border=0></a>";
$SKINTITLE["newtopic_ok"]="";
$SKINREFRESH["newtopic_ok"]="";
$SKIN["newtopic_no"]="<i>New topic denied</i>";
$SKINTITLE["newtopic_no"]="";
$SKINREFRESH["newtopic_no"]="";
$SKIN["replyokpage"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The reply has successfully been posted. Please wait 2 seconds or click <a href=\"showthread.php?threadid=\$threadid\">here</a> to return to the thread.</font>";
$SKINTITLE["replyokpage"]="Reply posted successfully";
$SKINREFRESH["replyokpage"]="showthread.php?threadid=\$threadid";
$SKIN["reply_ok"]="<a href=\"postreply.php?threadid=\$threadid\"><img src=\"{images_url}/postre.jpg\" alt=\"[Reply]\" border=0></a>";
$SKINTITLE["reply_ok"]="";
$SKINREFRESH["reply_ok"]="";
$SKIN["reply_no"]="<font size=1 face=\"Verdana\"><i>Replies disabled</i></font>";
$SKINTITLE["reply_no"]="";
$SKINREFRESH["reply_no"]="";
$SKIN["replypage"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Any registered user can post a reply</a></td>
 </tr>
</table>
<form action=\"postreply.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"postreply\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Post reply</td></tr>
 <tr>
  <td width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Icon</font></b></td>
  <td><table><tr>\$iconlist</tr></table></td>
 </tr>
 <tr>
  <td valign=\"top\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Message</font></b></td>
  <td><textarea name=\"the_message\" rows=15 cols=50>\$message</textarea></td>
 </tr>
 <tr>
  <td valign=\"top\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Options</font></b></td>
  <td><input type=\"checkbox\" name=\"f_sig\"><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Append signature to post</input><br><input type=\"checkbox\" name=\"f_close\"><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Close topic after posting reply <i>Moderators and admins only!</i></font></input></td>
 </tr>
 <tr>
  <td colspan=2 width=\"100%\" align=\"center\"><br><input type=\"submit\" value=\"Post Reply\"><p></td>
 </tr>
</table></form>";
$SKINTITLE["replypage"]="Post message";
$SKINREFRESH["replypage"]="";
$SKIN["newtopicpage"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Any registered user can create a new topic</a></td>
 </tr>
</table>
<form action=\"newtopic.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"newtopic\">
<input type=\"hidden\" name=\"forumid\" value=\"\$forumid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">New Topic</td></tr>
 <tr>
  <td width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Subject</font></b></td>
  <td><input type=\"text\" name=\"the_subject\"></td>
 </tr>
 <tr>
  <td><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Icon</font></b></td>
  <td><table><tr>\$iconlist</tr></table></td>
 </tr>
 <tr>
  <td valign=\"top\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Message</font></b></td>
  <td><textarea name=\"the_message\" rows=20 cols=60 wrap=\"virtual\"></textarea></td>
 </tr>
 <tr>
  <td>&nbsp;</td>
  <td><input type=\"checkbox\" name=\"f_sig\"><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Append signature to this message</input></td>
 </tr>
 <tr>
  <td colspan=2 width=\"100%\" align=\"center\"><br><input type=\"submit\" value=\"Post New Topic\"><p></td>
 </tr>
</table></form>";
$SKINTITLE["newtopicpage"]="Post new topic";
$SKINREFRESH["newtopicpage"]="";
$SKIN["newtopicokpage"]="<font size=2 face=\"Verdana\" color=\"#000000\">The thread has successfully been posted. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread.</font>";
$SKINTITLE["newtopicokpage"]="New topic posted successfully";
$SKINREFRESH["newtopicokpage"]="showforum.php?forumid=\$forumid";
$SKIN["lockedthread"]="<img src=\"{images_url}/lock.gif\" alt=\"[Locked by \$lockername]\">";
$SKINTITLE["lockedthread"]="";
$SKINREFRESH["lockedthread"]="";
$SKIN["error_threadlocked"]="<p><font face=\"{fontface}\" size={fontsize}>We're sorry, but this thread has been locked. You may not reply to it.</font>";
$SKINTITLE["error_threadlocked"]="Thread locked";
$SKINREFRESH["error_threadlocked"]="";
$SKIN["error_accessdenied"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but access has been denied to you. Perhaps your username/password combination is not correct?</font>";
$SKINTITLE["error_accessdenied"]="Access Denied";
$SKINREFRESH["error_accessdenied"]="";
$SKIN["postpage_locked"]="<font face=\"{fontface}\" size={fontsize}><i>This thread is locked. No more replies allowed.</i></font>";
$SKINTITLE["postpage_locked"]="";
$SKINREFRESH["postpage_locked"]="";
$SKIN["postpage_canreply"]="";
$SKINTITLE["postpage_canreply"]="";
$SKINREFRESH["postpage_canreply"]="";
$SKIN["lockthread_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderators and administrators can lock threads</a></td>
 </tr>
</table>
<form action=\"lockthread.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"lockthread\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Lock thread</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>Are you sure you want to lock thread <b>\$threadtitle</b>? This will make sure no one can reply to it until it is unlocked again.<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"Lock the thread\"></center>
</form>";
$SKINTITLE["lockthread_page"]="Lock Thread";
$SKINREFRESH["lockthread_page"]="";
$SKIN["unlockthread_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderators and administrators can lock threads</a></td>
 </tr>
</table>
<form action=\"lockthread.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"unlockthread\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Lock thread</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>Are you sure you want to unlock thread <b>\$threadtitle</b>? This will make it available for replies until it is locked again.<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"Unlock the thread\"></center>
</form>";
$SKINTITLE["unlockthread_page"]="Unlock Thread";
$SKINREFRESH["unlockthread_page"]="";
$SKIN["lockthread_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The thread has successfully been locked. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread list.</font>";
$SKINTITLE["lockthread_ok"]="Thread successfully locked";
$SKINREFRESH["lockthread_ok"]="showforum.php?forumid=\$forumid";
$SKIN["unlockthread_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The thread has successfully been unlocked. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread.</font>";
$SKINTITLE["unlockthread_ok"]="Thread successfully unlocked";
$SKINREFRESH["unlockthread_ok"]="showforum.php?forumid=\$forumid";
$SKIN["deletethread_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderators and administrators can delete threads</a></td>
 </tr>
</table>
<form action=\"deletethread.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"deletethread\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Delete thread</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>Are you sure you want to delete thread <b>\$threadtitle</b>? This will completely delete the thread, which cannot be undone!<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"Delete the thread\"></center>
</form>";
$SKINTITLE["deletethread_page"]="Delete Thread";
$SKINREFRESH["deletethread_page"]="";
$SKIN["deletethread_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The thread has successfully been deleted. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread list.</font>";
$SKINTITLE["deletethread_ok"]="Thread successfully deleted";
$SKINREFRESH["deletethread_ok"]="showforum.php?forumid=\$forumid";
$SKIN["deletepost"]="<a href=\"deletepost.php?postid=\$postid\"><img src=\"{images_url}/del.gif\" alt=\"[Delete Post]\" border=0></a>";
$SKINTITLE["deletepost"]="";
$SKINREFRESH["deletepost"]="";
$SKIN["editpost"]="<a href=\"editpost.php?postid=\$postid\"><img src=\"{images_url}/edit.gif\" alt=\"[Edit Post]\" border=0></a>";
$SKINTITLE["editpost"]="";
$SKINREFRESH["editpost"]="";
$SKIN["deletepost_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderators, administrators and the original poster can delete posts</a></td>
 </tr>
</table>
<form action=\"deletepost.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"deletepost\">
<input type=\"hidden\" name=\"postid\" value=\"\$postid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Delete post</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>Are you sure you want to delete this post? This cannot be undone!<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"Delete the post\"></center>
</form>";
$SKINTITLE["deletepost_page"]="Delete Post";
$SKINREFRESH["deletepost_page"]="";
$SKIN["deletepost_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The message has successfully been deleted. Please wait 2 seconds or click <a href=\"showthread.php?threadid=\$threadid\">here</a> to return to the thread.</font>";
$SKINTITLE["deletepost_ok"]="Message successfully deleted";
$SKINREFRESH["deletepost_ok"]="showthread.php?threadid=\$threadid";
$SKIN["editpost_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderators, administrators and the original poster can edit posts</a></td>
 </tr>
</table>
<form action=\"editpost.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"editpost\">
<input type=\"hidden\" name=\"postid\" value=\"\$postid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Edit post</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>If you are a moderator or administrator, or you have originally posted this message, you may edit it as you please<p></td></tr>
 <tr>
  <td width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td valign=\"top\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Message</font></b></td>
  <td><textarea name=\"the_message\" rows=15 cols=50>\$message</textarea></td>
 </tr>
 <tr>
  <td colspan=2 width=\"100%\" align=\"center\"><br><input type=\"submit\" value=\"Edit the message\"><p></td>
 </tr>
</table></form>";
$SKINTITLE["editpost_page"]="Edit Message";
$SKINREFRESH["editpost_page"]="";
$SKIN["editpost_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The message has successfully been edited. Please wait 2 seconds or click <a href=\"showthread.php?threadid=\$threadid\">here</a> to return to the thread.</font>";
$SKINTITLE["editpost_ok"]="Post successfully edited";
$SKINREFRESH["editpost_ok"]="showthread.php?threadid=\$threadid";
$SKIN["editpost_editfooter"]="<table width=\"100%\"><tr><td align=\"right\"><font size=1 face=\"{fontface}\">Edited at \$edit_timestamp by <a href=\"finger.php?accountid=\$edit_accountid\">\$edit_accountname</a></font></td></tr></table>";
$SKINTITLE["editpost_editfooter"]="";
$SKINREFRESH["editpost_editfooter"]="";
$SKIN["page_threadlocked"]="<font size={fontsize} face=\"{fontface}\">This thread has been locked by <a href=\"finger.php?accountid=\$lockerid\">\$lockername</a></font><br>";
$SKINTITLE["page_threadlocked"]="";
$SKINREFRESH["page_threadlocked"]="";
$SKIN["page_threadmoved"]="<font size={fontsize} face=\"{fontface}\">This thread has been moved to the <a href=\"showforum.php?forumid=\$destforumid\">\$destforumname</a> by <a href=\"finger.php?accountid=\$lockerid\">\$lockername</a></font>";
$SKINTITLE["page_threadmoved"]="";
$SKINREFRESH["page_threadmoved"]="";
$SKIN["forumlist"]="<option value=\"\$forumid\">\$forumname</option>";
$SKINTITLE["forumlist"]="";
$SKINREFRESH["forumlist"]="";
$SKIN["movethread_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderator and administrators can move threads</a></td>
 </tr>
</table>
<form action=\"movethread.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"movethread\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Move thread</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>If you are a moderator or administrator, you may move this thread to any forum you like. Please select how you would like to move the thread:<p>
<input type=\"radio\" name=\"how\" value=\"lock\" checked><b>Copy</b> this thread to the destination forum, and <b>lock</b> the original one</input><br><input type=\"radio\" name=\"how\" value=\"move\"><b>Move</b> this thread to the destination forum, and <b>delete</b> the original one</input><p></td></tr>
 <tr>
  <td align=\"center\" width=\"50%\"><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\"><b>Source forum</b><br>
<a href=\"showforum.php?forumid=\$forumid\" class=\"forumlink\">\$forumname</a></td>
   <td align=\"center\" width=\"50%\"><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\"><b>Destination forum</b><br>
<select name=\"destforum\">\$forumlist</select></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2 width=\"100%\" align=\"center\"><br><input type=\"submit\" value=\"Move the thread\"><p></td>
 </tr>
</table></form>";
$SKINTITLE["movethread_page"]="Move Thread";
$SKINREFRESH["movethread_page"]="";
$SKIN["movethread_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The thread has successfully been moved. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread.</font>";
$SKINTITLE["movethread_ok"]="Thread successfully moved";
$SKINREFRESH["movethread_ok"]="showforum.php?forumid=\$forumid";
$SKIN["error_nosuchthread"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but this thread doesn't seem to exist. Perhaps it was deleted or moved by someone else?</font>";
$SKINTITLE["error_nosuchthread"]="No such thread";
$SKINREFRESH["error_nosuchthread"]="";
$SKIN["page_restrictedlogin"]="<form action=\"index.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"dologin\">
<input type=\"hidden\" name=\"forumid\" value=\"\$forumid\">
<font size={fontsize} face=\"{fontface}\" color=\"#000000\">This is a restricted forum. In order to grant you access, we will need to determine who you are. This information will be stored inside a cookie, and will be used through the entire forum, until you log out or the cookie expires. Make sure no one has access to this computer, though, or they'll be able to retrieve your password!</font><p>
<table>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Account name</font></td>
    <td><input type=\"text\" name=\"the_accountname\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Password</font></td>
    <td><input type=\"password\" name=\"the_password\"></td>
  </tr>
</table><p>
<input type=\"submit\" value=\"Visit restricted forum\">
</form>";
$SKINTITLE["page_restrictedlogin"]="Forum is restricted - please log in";
$SKINREFRESH["page_restrictedlogin"]="";
$SKIN["error_restrictedenied"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but you are not allowed to access this restricted forum. This is most likely a restriction the forum administrator has deliberately set up.</font>";
$SKINTITLE["error_restrictedenied"]="Access denied to restricted forum";
$SKINREFRESH["error_restrictedenied"]="";
$SKIN["evil_html_tags"]="script
embed
object
applet
frame
iframe
server
meta
style
!--
--
link";
$SKINTITLE["evil_html_tags"]="";
$SKINREFRESH["evil_html_tags"]="";
$SKIN["rules_page"]="<font size=\"{fontsize}\" face=\"{fontface}\">Before we can allow you to register on this board, first we must ensure you agree with our rules. These rules are:</font><p>

<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0 border=1><tr><td width=\"100%\"><font size=\"{fontsize}\" face=\"{fontface}\" color=\"#ffffff\">\$rules</font></td></tr></table>

<p><center><form action=\"register.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"register\">
<input type=\"submit\" value=\"I agree\"></input>
</form><p>
<form action=\"index.php\" method=\"post\">
<input type=\"submit\" value=\"I disagree\"></input>
</form></center>";
$SKINTITLE["rules_page"]="Bulletin Board Rules";
$SKINREFRESH["rules_page"]="";
$SKIN["register_page"]="<font size=\"{fontsize}\" face=\"{fontface}\">Please fill in the following fields in order for us to create an account for you. All fields marked with a <b>*</b> are required</font><p>

<form action=\"register.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"doregister\">
<table width=\"100%\" cellpadding=0 cellspacing=0>
<tr><td width=\"20%\"><font size=\"{fontsize}\" face=\"{fontface}\">Account name <b>*</b></td><td width=\"80%\"><input type=\"text\" name=\"the_accountname\"></td></tr>
<tr><td width=\"20%\"><font size=\"{fontsize}\" face=\"{fontface}\">Password <b>*</b></td><td width=\"80%\"><input type=\"password\" name=\"the_password\"></td></tr>
<tr><td width=\"20%\"><font size=\"{fontsize}\" face=\"{fontface}\">Retype password <b>*</b></td><td width=\"80%\"><input type=\"password\" name=\"the_password2\"></td></tr>
<tr><td width=\"20%\"><font size=\"{fontsize}\" face=\"{fontface}\">Email address <b>*</b></td><td width=\"80%\"><input type=\"text\" name=\"the_email\"></td></tr><tr><td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Preferred layout</font></td><td><select name=\"userskinid\"><option value=\"0\" [[\$userskinid==0&&selected]]>Site default</option>\$userskins</select></td>
</tr><tr><td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Signature</font></td>    <td><textarea rows=10 cols=40 name=\"the_sig\">\$the_sig</textarea></td></tr>
<tr><td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Timezone</font></td>
<td><select name=\"timezone\"><option value=\"-43200\">GMT - 12</option><option value=\"-39600\">GMT - 11</option><option value=\"-36000\">GMT - 10</option><option value=\"-32400\">GMT - 9</option><option value=\"-28800\">GMT - 8</option><option value=\"-25200\">GMT - 7</option><option value=\"-21600\">GMT - 6</option><option value=\"-18000\">GMT - 5</option><option value=\"-14400\">GMT - 4</option><option value=\"-12600\">GMT - 3</option><option value=\"-10800\">GMT - 3</option><option value=\"-7200\"[>GMT - 2</option><option value=\"-3600\"[>GMT - 1</option><option value=\"0\" selected>GMT</option><option value=\"3600\">GMT + 1</option><option value=\"7200\">GMT + 2</option><option value=\"10800\">GMT + 3</option><option value=\"12600\">GMT + 3.30</option><option value=\"14400\">GMT + 4</option><option value=\"16200\">GMT + 4.30</option><option value=\"18000\">GMT + 5</option><option value=\"19800\">GMT + 5.30</option><option value=\"20700\"[[\$timezone==-20700&&selected]]>GMT + 5.45</option><option value=\"21600\">GMT + 6</option><option value=\"23400\">GMT + 6.30</option><option value=\"25200\">GMT + 7</option><option value=\"28800\">GMT + 8</option><option value=\"32400\">GMT + 9</option><option value=\"34200\">GMT + 9.30</option><option value=\"36000\">GMT + 10</option><option value=\"39600\">GMT + 11</option><option value=\"43200\">GMT + 12</option><option value=\"46800\">GMT + 13</option></select></td>  
  </tr>
  <tr>
    <td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Birthday</font></td>
    <td><select name=\"month\"><option value=\"0\" selected>---</option><option value=\"1\">January</option><option value=\"2\" [[\$month==2&&selected]]>February</option><option value=\"3\">March</option><option value=\"4\">April</option><option value=\"5\">May</option><option value=\"6\">June</option><option value=\"7\">July</option><option value=\"8\">August</option><option value=\"9\">September</option><option value=\"10\">October</option><option value=\"11\">November</option><option value=\"12\">December</option></select> <select name=\"day\"><option value=\"0\">---</option><option value=\"1\">1</option><option value=\"2\" >2</option><option value=\"3\">3</option><option value=\"4\">4</option><option value=\"5\" >5</option><option value=\"6\">6</option><option value=\"7\">7</option><option value=\"8\" >8</option><option value=\"9\">9</option><option value=\"10\">10</option><option value=\"11\">11</option><option value=\"12\">12</option><option value=\"13\">13</option><option value=\"14\">14</option><option value=\"15\">15</option><option value=\"16\">16</option><option value=\"17\">17</option><option value=\"18\">18</option><option value=\"19\">19</option><option value=\"20\">20</option><option value=\"21\">21</option><option value=\"22\">22</option><option value=\"23\">23</option><option value=\"24\">24</option><option value=\"25\">25</option><option value=\"26\">26</option><option value=\"27\">27</option><option value=\"28\">28</option><option value=\"29\">29</option><option value=\"30\">30</option><option value=\"31\">31</option></select> <input type=\"text\" name=\"year\" value=\"2000\" size=4></td>
  </tr>
  <tr valign=\"top\">
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Public email address </font></td>
    <td><input type=\"radio\" name=\"private_email\" value=\"no\" [[\$privemail==0&&checked]]>Anyone can view my email address</input><br><input type=\"radio\" name=\"private_email\" value=\"yes\" [[\$privemail==1&&checked]]>Keep my email address private</input></tr>
<tr><td celspan=2>&nbsp;</td></tr>
\$customfields
</table><p>

<font size=\"{fontsize}\" face=\"{fontface}\"><b>Note</b> Your must supply a valid email address! An account activation link will be emailed to the email address you supply. If you fail to activate your account, you will be unable to use it.</font>

<p><center><input type=\"submit\" value=\"Register my account\"></input>
</form><p>";
$SKINTITLE["register_page"]="Account Information";
$SKINREFRESH["register_page"]="";
$SKIN["error_accountalreadyexists"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but this account name has already been used by someone else. Please use another one.</font>";
$SKINTITLE["error_accountalreadyexists"]="Account name already in use";
$SKINREFRESH["error_accountalreadyexists"]="";
$SKIN["error_emailalreadyinuse"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but this email address has already been used by someone else. Email addresses must be unique. Please use another one.</font>";
$SKINTITLE["error_emailalreadyinuse"]="Email address already in use";
$SKINREFRESH["error_emailalreadyinuse"]="";
$SKIN["error_differentpasswords"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but these passwords are not equal to eachother. Please correct this and try again.</font>";
$SKINTITLE["error_differentpasswords"]="Passwords do not match";
$SKINREFRESH["error_differentpasswords"]="";
$SKIN["registerok_page"]="<font size=\"{fontsize}\" face=\"{fontface}\">Your account has successfully been registered. Please check your email and click the activation link. You will not be able to post before your account has been activated</font><p>

<form action=\"index.php\" method=\"post\">
<center><input type=\"submit\" value=\"Return to the forums\"></input></center>
</form>";
$SKINTITLE["registerok_page"]="Registration successful";
$SKINREFRESH["registerok_page"]="";
$SKIN["activate_email"]="Hi,<p>

You have just registered for an account at <a href=\"\$url\">\$forumtitle</a>. Your details are:<p>

Username: <code>\$username</code><br>
Password: <code>\$password</code><p>

Please keep in mind that you must first activate your account before you can actually access your account. Your account will be activated when you click <a href=\"\$url/register.php?action=activate&userid=\$userid&activateid=\$activateid&password=\$password\">this link</a>.<p>

Thank you!<br>
The administrators at \$forumtitle<br>";
$SKINTITLE["activate_email"]="Account activation";
$SKINREFRESH["activate_email"]="";
$SKIN["error_cannotactivate"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but we could not activate this account. Perhaps this account was already activated?</font>";
$SKINTITLE["error_cannotactivate"]="Unable to activate account";
$SKINREFRESH["error_cannotactivate"]="";
$SKIN["activateok_page"]="<font size=\"{fontsize}\" face=\"{fontface}\">Your account has successfully been activated. You will now be able to use it.<p>

<form action=\"index.php\" method=\"post\">
<center><input type=\"submit\" value=\"Return to the forums\"></input></center>
</form>";
$SKINTITLE["activateok_page"]="Account successfully activated";
$SKINREFRESH["activateok_page"]="";
$SKIN["announcement_list"]="<tr bgcolor=\"#cfd9ff\">
  <td align=\"center\" colspan=2>&nbsp;</td>
  <td><font size=\"{fontsize}\" face=\"{fontface}\">Announcement: <a href=\"showannouncement.php?forumid=\$forumid\" class=\"threadlink\">\$announcement_title</a></td>
  <td valign=\"top\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$authorname</font></td>
  <td valign=\"top\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">-</font></td>
  <td valign=\"top\" align=\"center\"><font size=1 face=\"{fontface}\" color=\"#000000\">-</font></td>
  <td valign=\"top\" align=\"center\"><font size=1 face=\"{fontface}\" color=\"#000000\">-</font></td>
</tr>";
$SKINTITLE["announcement_list"]="";
$SKINREFRESH["announcement_list"]="";
$SKIN["announcementpage"]="<table width=\"100%\">
 <tr>
  <td width=\"50%\" align=\"left\"><font face=\"{fontface}\" size=2><a href=\"index.php\">\$forums_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>Announcements</b></font></td>
  <td width=\"40%\" align=\"right\">\$newtopictext \$replytext</td>
  <td width=\"10%\" align=\"center\">&nbsp;</td>
 </tr>
</table>
\$locktext<p>
<table width=\"100%\" cellspacing=1 cellpadding=1>
<tr bgcolor=\"#2020f0\">
  <td width=\"15%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Author</b></font></td>
  <td width=\"85%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Message</b></font></td>
</tr>
<!-- show the actual listing of the post -->
\$annclist
</table><p>";
$SKINTITLE["announcementpage"]="\$forums_title - Annoucements";
$SKINREFRESH["announcementpage"]="";
$SKIN["announcement_displaylist"]="<tr bgcolor=\"#cfd9ff\">
  <td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\"><a href=\"finger.php?accountid=\$announcement_authorid\">\$author</a></font><br>
<font color=\"#000000\" size=1 face=\"{fontface}\">\$author_status<br>
<b>Number of posts: </b>\$author_nofposts<br>
<b>Joined at: </b>\$author_joindate</font></td>
  <td valign=\"top\"><font size=1 face=\"{fontface}\"><b>\$announcement_title</b> posted by \$author (\$begin to \$end)</font><hr><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$message</font></td>
</tr>";
$SKINTITLE["announcement_displaylist"]="";
$SKINREFRESH["announcement_displaylist"]="";
$SKIN["editthread_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only moderators and administrators can edit thread titles</a></td>
 </tr>
</table>
<form action=\"edittitle.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"edittitle\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Edit thread title</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>If you are an administrator or moderator, you can edit the title of this thread as you please<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Thread title&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"text\" name=\"the_title\" value=\"\$threadtitle\"></td> 
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"Edit the title\"></center>
</form>";
$SKINTITLE["editthread_page"]="Edit Thread Title";
$SKINREFRESH["editthread_page"]="";
$SKIN["edittitle_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The title has successfully been edited. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread list.</font>";
$SKINTITLE["edittitle_ok"]="Title successfully edited";
$SKINREFRESH["edittitle_ok"]="showforum.php?forumid=\$forumid";
$SKIN["posticon_item"]="<td width=\"10%\"><input type=\"radio\" name=\"icon_no\" value=\"\$no\"><img src=\"{images_url}/icon\$no.gif\" border=0></input></td>";
$SKINTITLE["posticon_item"]="";
$SKINREFRESH["posticon_item"]="";
$SKIN["posticon_newline"]="</tr><td></td><tr>";
$SKINTITLE["posticon_newline"]="";
$SKINREFRESH["posticon_newline"]="";
$SKIN["posticon_firstitem"]="<td width=\"10%\"><input type=\"radio\" name=\"icon_no\" value=\"\$no\" checked><img src=\"{images_url}/icon\$no.gif\" border=0></input></td>";
$SKINTITLE["posticon_firstitem"]="";
$SKINREFRESH["posticon_firstitem"]="";
$SKIN["viewip"]="<a href=\"viewip.php?postid=\$postid\"><img src=\"{images_url}/ip.gif\" alt=\"[View IP]\" border=0></a>";
$SKINTITLE["viewip"]="";
$SKINREFRESH["viewip"]="";
$SKIN["viewip_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only administrators can edit view IP addresses</a></td>
 </tr>
</table>
<form action=\"viewip.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"viewip\">
<input type=\"hidden\" name=\"postid\" value=\"\$postid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">View IP address</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>If you are an administrator, you can view the IP address of the original poster of this thread.<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</td>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"View IP address\"></center>
</form>";
$SKINTITLE["viewip_page"]="View IP Address";
$SKINREFRESH["viewip_page"]="";
$SKIN["viewipresult_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Only administrators can edit view IP addresses</a></td>
 </tr>
</table>
<form action=\"showthread.php\" method=\"post\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">View IP address</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>The requested IP address is <b>\$dest_ipaddress</b><p>
The name of that host is <b>\$dest_hostname</b><p></td></tr>
</table><p>
<center><input type=\"submit\" value=\"Return to thread\"></center>
</form>";
$SKINTITLE["viewipresult_page"]="View IP address result";
$SKINREFRESH["viewipresult_page"]="";
$SKIN["online_firstmember"]="<a href=\"finger.php?accountid=\$accountid\">\$accountname</a>";
$SKINTITLE["online_firstmember"]="";
$SKINREFRESH["online_firstmember"]="";
$SKIN["online_moremember"]=", <a href=\"finger.php?accountid=\$accountid\">\$accountname</a>";
$SKINTITLE["online_moremember"]="";
$SKINREFRESH["online_moremember"]="";
$SKIN["page_profilelogin"]="<form action=\"profile.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"editprofile\">
<font size={fontsize} face=\"{fontface}\" color=\"#000000\">Please submit the identification of the user you wish to edit the profile for</font><p>
<table>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Account name</font></td>
    <td><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Password</font></td>
    <td><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
  </tr>
</table><p>
<input type=\"submit\" value=\"Edit profile\">
</form>";
$SKINTITLE["page_profilelogin"]="Login to profile";
$SKINREFRESH["page_profilelogin"]="";
$SKIN["page_editprofile"]="<form action=\"profile.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"doeditprofile\">
<input type=\"hidden\" name=\"the_accountname\" value=\"\$the_accountname\">
<input type=\"hidden\" name=\"the_password\" value=\"\$the_password\">
<font size={fontsize} face=\"{fontface}\" color=\"#000000\">Editing profile of <b>\$the_accountname</b></td></font><p>
<table>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Accountname</font></td>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$the_accountname</font></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Email address</font></td>
    <td><input type=\"text\" name=\"email\" value=\"\$email\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Password</font></td>
    <td><input type=\"password\" name=\"newpassword1\" value=\"\$the_password\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Password again</font></td>
    <td><input type=\"password\" name=\"newpassword2\" value=\"\$the_password\"></td>
  </tr>
  <tr>
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Preferred layout</font></td>
    <td><select name=\"userskinid\"><option value=\"0\" [[\$userskinid==0&&selected]]>Site default</option>\$userskins</select></td>
  </tr>
  <tr>
    <td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Signature</font></td>
    <td><textarea rows=10 cols=40 name=\"the_sig\">\$the_sig</textarea></td>
  </tr>
  <tr>
    <td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Timezone</font></td>
    <td><select name=\"timezone\"><option value=\"-43200\" [[\$timezone==-43200&&selected]]>GMT - 12</option><option value=\"-39600\"[[\$timezone==-39600&&selected]]>GMT - 11</option><option value=\"-36000\"[[\$timezone==-36000&&selected]]>GMT - 10</option><option value=\"-32400\"[[\$timezone==-32400&&selected]]>GMT - 9</option><option value=\"-28800\"[[\$timezone==-28800&&selected]]>GMT - 8</option><option value=\"-25200\"[[\$timezone==-25200&&selected]]>GMT - 7</option><option value=\"-21600\"[[\$timezone==-21600&&selected]]>GMT - 6</option><option value=\"-18000\"[[\$timezone==-18000&&selected]]>GMT - 5</option><option value=\"-14400\"[[\$timezone==-14400&&selected]]>GMT - 4</option><option value=\"-12600\"[[\$timezone==-12600&&selected]]>GMT - 3</option><option value=\"-10800\"[[\$timezone==-10800&&selected]]>GMT - 3</option><option value=\"-7200\"[[\$timezone==-7200&&selected]]>GMT - 2</option><option value=\"-3600\"[[\$timezone==-3600&&selected]]>GMT - 1</option><option value=\"0\"[[\$timezone==0&&selected]]>GMT</option><option value=\"3600\"[[\$timezone==3600&&selected]]>GMT + 1</option><option value=\"7200\"[[\$timezone==7200&&selected]]>GMT + 2</option><option value=\"10800\"[[\$timezone==10800&&selected]]>GMT + 3</option><option value=\"12600\"[[\$timezone==12600&&selected]]>GMT + 3.30</option><option value=\"14400\"[[\$timezone==14400&&selected]]>GMT + 4</option><option value=\"16200\"[[\$timezone==16200&&selected]]>GMT + 4.30</option><option value=\"18000\"[[\$timezone==18000&&selected]]>GMT + 5</option><option value=\"19800\"[[\$timezone==19800&&selected]]>GMT + 5.30</option><option value=\"20700\"[[\$timezone==20700&&selected]]>GMT + 5.45</option><option value=\"21600\"[[\$timezone==21600&&selected]]>GMT + 6</option><option value=\"23400\"[[\$timezone==23400&&selected]]>GMT + 6.30</option><option value=\"25200\"[[\$timezone==25200&&selected]]>GMT + 7</option><option value=\"28800\"[[\$timezone==28800&&selected]]>GMT + 8</option><option value=\"32400\"[[\$timezone==32400&&selected]]>GMT + 9</option><option value=\"34200\"[[\$timezone==34200&&selected]]>GMT + 9.30</option><option value=\"36000\"[[\$timezone==36000&&selected]]>GMT + 10</option><option value=\"39600\"[[\$timezone==39600&&selected]]>GMT + 11</option><option value=\"43200\"[[\$timezone==43200&&selected]]>GMT + 12</option><option value=\"46800\"[[\$timezone==46800&&selected]]>GMT + 13</option></select></td>  
  </tr>
  <tr>
    <td valign=\"top\"><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Birthday</font></td>
    <td><select name=\"month\"><option value=\"0\" [[\$month==0&&selected]]>---</option><option value=\"1\" [[\$month==1&&selected]]>January</option><option value=\"2\" [[\$month==2&&selected]]>February</option><option value=\"3\" [[\$month==3&&selected]]>March</option><option value=\"4\" [[\$month==4&&selected]]>April</option><option value=\"5\" [[\$month==5&&selected]]>May</option><option value=\"6\" [[\$month==6&&selected]]>June</option><option value=\"7\" [[\$month==7&&selected]]>July</option><option value=\"8\" [[\$month==8&&selected]]>August</option><option value=\"9\" [[\$month==1&&selected]]>September</option><option value=\"10\" [[\$month==10&&selected]]>October</option><option value=\"11\" [[\$month==11&&selected]]>November</option><option value=\"12\" [[\$month==12&&selected]]>December</option></select> <select name=\"day\"><option value=\"0\" [[\$day==0&&selected]]>---</option><option value=\"1\" [[\$day==1&&selected]]>1</option><option value=\"2\" [[\$day==2&&selected]]>2</option><option value=\"3\" [[\$day==3&&selected]]>3</option><option value=\"4\" [[\$day==4&&selected]]>4</option><option value=\"5\" [[\$day==5&&selected]]>5</option><option value=\"6\" [[\$day==6&&selected]]>6</option><option value=\"7\" [[\$day==7&&selected]]>7</option><option value=\"8\" [[\$day==8&&selected]]>8</option><option value=\"9\" [[\$day==9&&selected]]>9</option><option value=\"10\" [[\$day==10&&selected]]>10</option><option value=\"11\" [[\$day==11&&selected]]>11</option><option value=\"12\" [[\$day==12&&selected]]>12</option><option value=\"13\" [[\$day==13&&selected]]>13</option><option value=\"14\" [[\$day==14&&selected]]>14</option><option value=\"15\" [[\$day==15&&selected]]>15</option><option value=\"16\" [[\$day==16&&selected]]>16</option><option value=\"17\" [[\$day==17&&selected]]>17</option><option value=\"18\" [[\$day==18&&selected]]>18</option><option value=\"19\" [[\$day==19&&selected]]>19</option><option value=\"20\" [[\$day==20&&selected]]>20</option><option value=\"21\" [[\$day==21&&selected]]>21</option><option value=\"22\" [[\$day==22&&selected]]>22</option><option value=\"23\" [[\$day==23&&selected]]>23</option><option value=\"24\" [[\$day==24&&selected]]>24</option><option value=\"25\" [[\$day==25&&selected]]>25</option><option value=\"26\" [[\$day==26&&selected]]>26</option><option value=\"27\" [[\$day==27&&selected]]>27</option><option value=\"28\" [[\$day==28&&selected]]>28</option><option value=\"29\" [[\$day==29&&selected]]>29</option><option value=\"30\" [[\$day==30&&selected]]>30</option><option value=\"31\" [[\$day==31&&selected]]>31</option></select> <input type=\"text\" name=\"year\" value=\"\$year\" size=4></td>
  </tr>
  <tr valign=\"top\">
    <td><font size={fontsize} face=\"{fontface}\" color=\"#000000\">Public email address </font></td>
    <td><input type=\"radio\" name=\"private_email\" value=\"no\" [[\$privemail==0&&checked]]>Anyone can view my email address</input><br><input type=\"radio\" name=\"private_email\" value=\"yes\" [[\$privemail==1&&checked]]>Keep my email address private</input></tr>
  <tr>
    <td celspan=2>&nbsp;</td>
  </tr>
\$customfields
</table><p>
<b>Notice</b> If you change your email address, you will need to re-activate your account to ensure you have a valid email address! Make sure the email address you supply is 100% correct!<p>
<input type=\"submit\" value=\"Edit the profile\">
</form>";
$SKINTITLE["page_editprofile"]="Edit Profile";
$SKINREFRESH["page_editprofile"]="";
$SKIN["welcome_catlist"]="<table width=\"100%\">
  <tr>
    <td width=\"50%\" align=\"left\"><font size=1 face=\"{fontface}\">Hi <b>\$username</b>!<br>\$birthdays</font><p></td>
    <td width=\"50%\" align=\"right\"><font size=1 face=\"{fontface}\"><b>\$nofthreads</b> threads and <b>\$nofposts</b> posts<br>Number of members: <b>\$nofmembers</b><br>Greetings to our newest member, <a href=\"finger.php?accountid=\$newmemberid\">\$newmembername</a></font></td>
  </tr>
</table>
<!-- category list -->
<table width=\"100%\" cellspacing=1 cellpadding=3>
<tr bgcolor=\"#2020f0\">
  <td width=\"30%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Category Name</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Posts</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Threads</b></font></td>
  <td width=\"20%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Number of forums</b></font></td>
  <td width=\"30%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" size=\"{fontsize}\" face=\"{fontface}\"><b>Category Moderators</b></font></td>
</tr>
<!-- show the actual listing of the categories -->
\$catlist
</table><p>

<!-- show the online users -->
<table width=\"100%\" cellspacing=1 cellpadding=1>
<tr bgcolor=\"#2020f0\">
 <td width=\"100%\" colspan=5><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">There are currently \$nofonlinemembers member(s) and \$nofonlineguests guest(s) online</td>
</tr>
<tr bgcolor=\"#cfd9ff\">
 <td><font face=\"{fontface}\" size=1>\$onlinemembers</font></td>
</tr>
</table>";
$SKINTITLE["welcome_catlist"]="\$forums_title Powered by ForuMAX";
$SKINREFRESH["welcome_catlist"]="";
$SKIN["cat_list"]="<tr bgcolor=\"#cfd9ff\">
  <td><a href=\"index.php?catid=\$catid\" class=\"forumlink\">\$catname</a></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$nofposts</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$nofthreads</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$noforums</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$catmods</font></td>
</tr>";
$SKINTITLE["cat_list"]="";
$SKINREFRESH["cat_list"]="";
$SKIN["editprofile_ok"]="<font size=\"{fontsize}\" face=\"{fontface}\">Your profile has successfully been edited. </font><p>

<form action=\"index.php\" method=\"post\">
<center><input type=\"submit\" value=\"Return to the forums\"></input></center>
</form>";
$SKINTITLE["editprofile_ok"]="Profile successfully edited";
$SKINREFRESH["editprofile_ok"]="";
$SKIN["editcustom_1"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_1"]="";
$SKINREFRESH["editcustom_1"]="";
$SKIN["viewcustom_1"]="<font size=1 face=\"{fontface}\"><b>\$fieldname</b>: \$fieldvalue</font><br>";
$SKINTITLE["viewcustom_1"]="Plain Text";
$SKINREFRESH["viewcustom_1"]="";
$SKIN["viewcustom_2"]="<font size=1 face=\"{fontface}\"><b>\$fieldname</b>: <a href=\"\$fieldname\">\$fieldvalue</a></font><br>";
$SKINTITLE["viewcustom_2"]="URL";
$SKINREFRESH["viewcustom_2"]="";
$SKIN["viewcustom_3"]="<a href=\"aim:goim?screenname=\$fieldvalue&message
=Hi!+Are+You+There?\"><img src=\"{images_url}/aim.gif\" alt=\"[AIM]\" border=0></a>";
$SKINTITLE["viewcustom_3"]="AIM";
$SKINREFRESH["viewcustom_3"]="";
$SKIN["viewcustom_4"]="<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=\$fieldvalue&.src=pg\" target=\"_blank\"><img src=\"http://opi.yahoo.com/online?u=\$fieldvalue&m=g&t=0\" alt=\"[YID]\" border=0></a>";
$SKINTITLE["viewcustom_4"]="Yahoo! ID";
$SKINREFRESH["viewcustom_4"]="";
$SKIN["viewcustom_5"]="<font size=1 face=\"{fontface}\"><b>\$fieldname</b>: \$fieldvalue</font><br>";
$SKINTITLE["viewcustom_5"]="Gender";
$SKINREFRESH["viewcustom_5"]="";
$SKIN["viewcustom_6"]="<a href=\"\$fieldvalue\" target=\"_blank\"><img src=\"{images_url}/house.gif\" alt=\"[Homepage]\" border=0></a>";
$SKINTITLE["viewcustom_6"]="Homepage URL";
$SKINREFRESH["viewcustom_6"]="";
$SKIN["viewcustom_7"]="<font size=1 face=\"{fontface}\"><b>\$fieldname</b>: \$fieldvalue</font><br>";
$SKINTITLE["viewcustom_7"]="MSN";
$SKINREFRESH["viewcustom_7"]="";
$SKIN["viewcustom_8"]="<font size=1 face=\"{fontface}\"><b>\$fieldname</b>: \$fieldvalue</font><br>";
$SKINTITLE["viewcustom_8"]="";
$SKINREFRESH["viewcustom_8"]="";
$SKIN["editcustom_2"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_2"]="";
$SKINREFRESH["editcustom_2"]="";
$SKIN["editcustom_3"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_3"]="";
$SKINREFRESH["editcustom_3"]="";
$SKIN["editcustom_4"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_4"]="";
$SKINREFRESH["editcustom_4"]="";
$SKIN["editcustom_5"]="<tr valign=\"top\">
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><input type=\"radio\" name=\"field[\$fieldid]\" value=\"Male\" [[\$fieldvalue==Male&&checked]]>Male</input><br><input type=\"radio\" name=\"field[\$fieldid]\" value=\"Female\" [[\$fieldvalue==Female&&checked]]>Female</input><br><input type=\"radio\" name=\"field[\$fieldid]\" value=\"Unspecified\" [[\$fieldvalue==Unspecified&&checked]]>Unspecified</input></td>
</tr>";
$SKINTITLE["editcustom_5"]="";
$SKINREFRESH["editcustom_5"]="";
$SKIN["editcustom_6"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_6"]="";
$SKINREFRESH["editcustom_6"]="";
$SKIN["editcustom_7"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_7"]="";
$SKINREFRESH["editcustom_7"]="";
$SKIN["editcustom_8"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_8"]="";
$SKINREFRESH["editcustom_8"]="";
$SKIN["viewcustom_9"]="<a href=\"http://wwp.icq.com/scripts/con
tact.dll?msgto=\$fieldvalue\"><img src=\"http://online.mirabilis.com/scripts/online.dll?icq=\$fieldvalue&img=5\" width=18 height=18 alt=\"[ICQ]\" border=0></a>";
$SKINTITLE["viewcustom_9"]="ICQ";
$SKINREFRESH["viewcustom_9"]="";
$SKIN["editcustom_9"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><input type=\"text\" name=\"field[\$fieldid]\" value=\"\$fieldvalue\"></td>
</tr>";
$SKINTITLE["editcustom_9"]="";
$SKINREFRESH["editcustom_9"]="";
$SKIN["error_noaccess"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but you do not have enough access to perform this action. If you think this is wrong, please consult the forum administrator.</font>";
$SKINTITLE["error_noaccess"]="No access to perform this action";
$SKINREFRESH["error_noaccess"]="";
$SKIN["pm_reply"]="<form action=\"pm.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"docompose\">
<table width=\"100%\" bgcolor=\"#4a6ea5\" border=0  cellspacing=1 cellpadding=0>
<tr bgcolor=\"#000000\">
 <td colspan=2><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Reply to a message</font></td>
</tr>
<tr>
 <td width=\"15%\"><font face=\"{fontface}\" size=2 color=\"#ffff00\">To</font></td>
 <td width=\"85%\"><input type=\"text\" name=\"to\" value=\"\$to\"></td>
</tr>
<tr>
 <td><font face=\"{fontface}\" size=2 color=\"#ffff00\">Subject</font></td>
 <td><input type=\"text\" name=\"subject\" value=\"Re: \$subject\"></td>
</tr>
<tr>
 <td valign=\"top\"><font face=\"{fontface}\" size=2 color=\"#ffff00\">Message</font></td>
 <td><textarea rows=20 cols=60 name=\"message\">\$message</textarea></td>
</tr>
<tr>
 <td colspan=2 align=\"center\">&nbsp;</td>
</tr>
<tr>
 <td colspan=2 align=\"center\"><input type=\"submit\" value=\"Send reply\"></form></td>
</tr>
</table>";
$SKINTITLE["pm_reply"]="Reply to a message";
$SKINREFRESH["pm_reply"]="";
$SKIN["notify_newthread"]="Hi \$username,<p>

User <a href=\"\$url/finger.php?accountid=\$postuserid\">\$postusername</a>
 has created a new thread, <a href=\"\$url/showthread.php?threadid=\$threadid\">\$threadtitle</a>.<p>

The forum administrator at <a href=\"\$url\">\$forumtitle</a>";
$SKINTITLE["notify_newthread"]="Notification of a new thread";
$SKINREFRESH["notify_newthread"]="";
$SKIN["notify_newmessage"]="Hi \$username,<p>

User <a href=\"\$url/finger.php?accountid=\$postuserid\">\$postusername</a> has posted a new message in the <a href=\"\$url/showthread.php?threadid=\$threadid\">\$threadtitle</a> thread.<p>

The forum administrator at <a href=\"\$url\">\$forumtitle</a>
";
$SKINTITLE["notify_newmessage"]="Notification of a new message";
$SKINREFRESH["notify_newmessage"]="";
$SKIN["page_list"]="<font size=1 face=\"{fontface}\" color=\"#000000\">(Page: \$pages)</font>";
$SKINTITLE["page_list"]="";
$SKINREFRESH["page_list"]="";
$SKIN["page_firstno"]="<a href=\"showthread.php?threadid=\$threadid&page=\$page\">\$page</a>";
$SKINTITLE["page_firstno"]="";
$SKINREFRESH["page_firstno"]="";
$SKIN["page_moreno"]=", <a href=\"showthread.php?threadid=\$threadid&page=\$page\">\$page</a>";
$SKINTITLE["page_moreno"]="";
$SKINREFRESH["page_moreno"]="";
$SKIN["page_range_separator"]=" ... ";
$SKINTITLE["page_range_separator"]="";
$SKINREFRESH["page_range_separator"]="";
$SKIN["page_thread_sel"]="<b>[\$pageno]</b>";
$SKINTITLE["page_thread_sel"]="";
$SKINREFRESH["page_thread_sel"]="";
$SKIN["page_thread_unsel"]="[<a href=\"showthread.php?threadid=\$threadid&page=\$page\">\$page</a>]";
$SKINTITLE["page_thread_unsel"]="";
$SKINREFRESH["page_thread_unsel"]="";
$SKIN["page_thread_separator"]=", ";
$SKINTITLE["page_thread_separator"]="";
$SKINREFRESH["page_thread_separator"]="";
$SKIN["page_thread_range_separator"]=" ... ";
$SKINTITLE["page_thread_range_separator"]="";
$SKINREFRESH["page_thread_range_separator"]="";
$SKIN["thread_pagelist"]="<font face=\"{fontface}\" size=2>This topic is \$nofpages pages long. \$pageslist</font>";
$SKINTITLE["thread_pagelist"]="";
$SKINREFRESH["thread_pagelist"]="";
$SKIN["thread_page_separator"]=", ";
$SKINTITLE["thread_page_separator"]="";
$SKINREFRESH["thread_page_separator"]="";
$SKIN["thread_page_sel"]="<b>\$page</b>";
$SKINTITLE["thread_page_sel"]="";
$SKINREFRESH["thread_page_sel"]="";
$SKIN["thread_page_unsel"]="<a href=\"showthread.php?threadid=\$threadid&page=\$page\">\$page</a>";
$SKINTITLE["thread_page_unsel"]="";
$SKINREFRESH["thread_page_unsel"]="";
$SKIN["thread_page_range_separator"]=" ... ";
$SKINTITLE["thread_page_range_separator"]="";
$SKINREFRESH["thread_page_range_separator"]="";
$SKIN["thread_page_firstpage"]="<a href=\"showthread.php?threadid=\$threadid&page=1\"><< First Page</a>";
$SKINTITLE["thread_page_firstpage"]="";
$SKINREFRESH["thread_page_firstpage"]="";
$SKIN["thread_page_lastpage"]="<a href=\"showthread.php?threadid=\$threadid\">Last Page >></a>";
$SKINTITLE["thread_page_lastpage"]="";
$SKINREFRESH["thread_page_lastpage"]="";
$SKIN["pm_newlist"]="<a href=\"pm.php\">You have \$nofmessages new private message(s)</a><p>";
$SKINTITLE["pm_newlist"]="";
$SKINREFRESH["pm_newlist"]="";
$SKIN["pm_overview"]="<font face=\"{fontface}\" size={fontsize}>There are currently <b>\$nofmessages</b> private message(s) in your inbox. You may read a message by clicking on it in the list below:</font>
<form action=\"pm.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"deletemulti\">
<table width=\"100%\" bgcolor=\"#4a6ea5\" border=0  cellspacing=1 cellpadding=0>
<tr bgcolor=\"#000000\">
 <td width=\"2%\">&nbsp;</td>
 <td width=\"5%\" align=\"center\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">New?</font></td>
 <td width=\"15%\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Author</font></td>
 <td width=\"58%\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Subject</font></td>
 <td width=\"20%\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Timestamp</font></td>
</tr>
\$pmlist
</table>
<table width=\"100%\" border=0>
 <tr>
  <td width=\"33%\" align=\"left\"><input type=\"submit\" value=\"Delete Message(s)\"></form></td>
  <td width=\"33%\" align=\"center\"><form action=\"pm.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"compose\">
<input type=\"submit\" value=\"Compose a new message\"></form></td>
  <td width=\"34%\" align=\"center\">&nbsp;</td>
 </tr>
</table>";
$SKINTITLE["pm_overview"]="Private Messaging";
$SKINREFRESH["pm_overview"]="";
$SKIN["pm_entry"]="<tr>
 <td align=\"center\"><input type=\"checkbox\" name=\"delete[\$messageid]\"></td>
 <td align=\"center\">\$readunread</td>
 <td><a class=\"pmlink\" href=\"finger.php?accountid=\$senderid\">\$sendername</a></td>
 <td><a class=\"pmlink\" href=\"pm.php?action=readmessage&messageid=\$messageid\">\$messagetitle</a></td>
 <td><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">\$messagetime</font></td>
</tr>";
$SKINTITLE["pm_entry"]="";
$SKINREFRESH["pm_entry"]="";
$SKIN["pm_unread"]="<img src=\"{images_url}/icon7.gif\" alt=\"[New!]\">";
$SKINTITLE["pm_unread"]="";
$SKINREFRESH["pm_unread"]="";
$SKIN["pm_read"]="&nbsp;";
$SKINTITLE["pm_read"]="";
$SKINREFRESH["pm_read"]="";
$SKIN["pm_readmessage"]="<table width=\"100%\" border=0>
 <tr>
  <td width=\"33%\" align=\"center\"><form action=\"pm.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"reply\"><input type=\"hidden\" name=\"messageid\" value=\"\$messageid\"><input type=\"submit\" value=\"Reply\"></form></td>
  <td width=\"34%\" align=\"center\"><form action=\"pm.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"delete\"><input type=\"hidden\" name=\"messageid\" value=\"\$messageid\"><input type=\"submit\" value=\"Delete\"></form></td>
  <td width=\"33%\" align=\"center\"><form action=\"pm.php\" method=\"post\"><input type=\"submit\" value=\"Close\"></form></td>
 </tr>
</table>
<table width=\"100%\" bgcolor=\"#4a6ea5\" border=0  cellspacing=1 cellpadding=0>
<tr bgcolor=\"#000000\">
 <td width=\"100%\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">From: <a class=\"pm_userlink\" href=\"finger.php?accountid=\$senderid\">\$sendername</a><br>
To: <a href=\"finger.php?accountid=\$accountid\" class=\"pm_userlink\">\$accountname</a><br>
Subject: <b>\$subject</b><br>
Date: <b>\$timestamp</b></font></td>
</tr>
<tr>
 <td><font face=\"{fontface}\" size=2 color=\"#ffff00\">\$message</font></td>
</tr>
</table><p>
<table width=\"100%\" border=0>
 <tr>
  <td width=\"33%\" align=\"center\"><form action=\"pm.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"reply\"><input type=\"hidden\" name=\"messageid\" value=\"\$messageid\"><input type=\"submit\" value=\"Reply\"></form></td>
  <td width=\"34%\" align=\"center\"><form action=\"pm.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"delete\"><input type=\"hidden\" name=\"messageid\" value=\"\$messageid\"><input type=\"submit\" value=\"Delete\"></form></td>
  <td width=\"33%\" align=\"center\"><form action=\"pm.php\" method=\"post\"><input type=\"submit\" value=\"Close\"></form></td>
 </tr>
</table>";
$SKINTITLE["pm_readmessage"]="Private Messages - Reading message";
$SKINREFRESH["pm_readmessage"]="";
$SKIN["error_nosuchmessage"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but not message with this message ID, readable to you, could be found</font>";
$SKINTITLE["error_nosuchmessage"]="";
$SKINREFRESH["error_nosuchmessage"]="No such message";
$SKIN["pm_compose"]="<form action=\"pm.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"docompose\">
<table width=\"100%\" bgcolor=\"#4a6ea5\" border=0  cellspacing=1 cellpadding=0>
<tr bgcolor=\"#000000\">
 <td colspan=2><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Compose a new message</font></td>
</tr>
<tr>
 <td width=\"15%\"><font face=\"{fontface}\" size=2 color=\"#ffff00\">To</font><br><font face=\"{fontface}\" size=1 color=\"#ffff00\">Seperate user names by commas</font></td>
 <td width=\"85%\"><input type=\"text\" name=\"to\" value=\"\$destusername\"></td>
</tr>
<tr>
 <td><font face=\"{fontface}\" size=2 color=\"#ffff00\">Subject</font></td>
 <td><input type=\"text\" name=\"subject\"></td>
</tr>
<tr>
 <td valign=\"top\"><font face=\"{fontface}\" size=2 color=\"#ffff00\">Message</font></td>
 <td><textarea rows=20 cols=60 name=\"message\"></textarea></td>
</tr>
<tr>
 <td colspan=2 align=\"center\">&nbsp;</td>
</tr>
<tr>
 <td colspan=2 align=\"center\"><input type=\"submit\" value=\"Send message\"></form></td>
</tr>
</table>";
$SKINTITLE["pm_compose"]="Private Messaging - Compose Message";
$SKINREFRESH["pm_compose"]="";
$SKIN["error_nosuchuser"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but the destination username your specified does not exist. Please check the spelling and try again.</font>";
$SKINTITLE["error_nosuchuser"]="User not found";
$SKINREFRESH["error_nosuchuser"]="";
$SKIN["error_emptyfields"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but you have not filled in all fields. Please <a href=\"javascript:history.go(-1);\">go back</a> and solve this problem.</font>";
$SKINTITLE["error_emptyfields"]="Not all fields are filled in";
$SKINREFRESH["error_emptyfields"]="";
$SKIN["error_mailboxfull"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but \$destusername's mailbox is full. Please be patient until this member reads his mailbox and deletes some items from it.</font>";
$SKINTITLE["error_mailboxfull"]="Mail box full";
$SKINREFRESH["error_mailboxfull"]="";
$SKIN["pm_composeok"]="<font face=\"{fontface}\" size={fontsize}>The private message has successfully been sent. Please wait 2 seconds or click <a href=\"pm.php\">here</a> to return to the private messaging overview</font>";
$SKINTITLE["pm_composeok"]="Message successfully sent";
$SKINREFRESH["pm_composeok"]="pm.php";
$SKIN["pm_deleteok"]="<font face=\"{fontface}\" size={fontsize}>The selected private messages have successfully been deleted. Please wait 2 seconds or click <a href=\"pm.php\">here</a> to return to the private messaging overview</font>";
$SKINTITLE["pm_deleteok"]="Message successfully deleted";
$SKINREFRESH["pm_deleteok"]="pm.php";
$SKIN["error_pmdisabled"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but the administrator has disabled Private Messaging on this forum.</font>";
$SKINTITLE["error_pmdisabled"]="Private Messaging Disabled";
$SKINREFRESH["error_pmdisabled"]="";
$SKIN["error_notloggedin"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but you must be logged in before you can use private messaging. Please <a href=\"index.php?action=login\">log in</a> and try again.</font>";
$SKINTITLE["error_notloggedin"]="You must be logged in before you can use this";
$SKINREFRESH["error_notloggedin"]="";
$SKIN["sig_sep"]="<hr>";
$SKINTITLE["sig_sep"]="(This separates signatures from an actual post)";
$SKINREFRESH["sig_sep"]="";
$SKIN["pm_reply_line"]="; ";
$SKINTITLE["pm_reply_line"]="(will be appended before every line when replying)";
$SKINREFRESH["pm_reply_line"]="";
$SKIN["finger_emailhidden"]="<i>Hidden</i>";
$SKINTITLE["finger_emailhidden"]="";
$SKINREFRESH["finger_emailhidden"]="";
$SKIN["finger_email"]="<a href=\"mailto:\$email\">\$email</a>";
$SKINTITLE["finger_email"]="";
$SKINREFRESH["finger_email"]="";
$SKIN["finger_lastpost"]="<a href=\"showthread.php?threadid=\$threadid\">\$threadname</a>, posted at \$timestamp";
$SKINTITLE["finger_lastpost"]="";
$SKINREFRESH["finger_lastpost"]="";
$SKIN["thread_splitmod"]=", ";
$SKINTITLE["thread_splitmod"]="";
$SKINREFRESH["thread_splitmod"]="";
$SKIN["list_usermod"]="\$objectname";
$SKINTITLE["list_usermod"]="";
$SKINREFRESH["list_usermod"]="";
$SKIN["list_groupmod"]="\$objectname group";
$SKINTITLE["list_groupmod"]="";
$SKINREFRESH["list_groupmod"]="";
$SKIN["list_splitmod"]=", ";
$SKINTITLE["list_splitmod"]="";
$SKINREFRESH["list_splitmod"]="";
$SKIN["list_nomod"]="no one";
$SKINTITLE["list_nomod"]="";
$SKINREFRESH["list_nomod"]="";
$SKIN["reportthread_page"]="<table width=\"100%\">
 <tr>
  <td width=\"80%\"><font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font></td>
  <td width=\"20%\" align=\"right\"><font face=\"{fontface}\" size=1>Anyone who has a valid username/password can report threads</a></td>
 </tr>
</table>
<form action=\"reportthread.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"reportthread\">
<input type=\"hidden\" name=\"threadid\" value=\"\$threadid\">
<table bgcolor=\"#4a6ea5\" width=\"100%\" cellpadding=0 cellspacing=0>
 <tr><td width=\"100%\" colspan=2 bgcolor=\"#000000\"><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">Report thread</td></tr>
 <tr><td colspan=2 align=\"center\"><font color=\"#ffffff\" face=\"{fontface}\" size={fontsize}><br>Are you sure you want to report thread <b>\$threadtitle</b> to a moderator? This will send the moderator a private message, asking to look at the thread<p></td></tr>
 <tr>
  <td align=\"right\" width=\"15%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your user name&nbsp;</font></b></td>
  <td width=\"85%\"><input type=\"text\" name=\"the_accountname\" value=\"\$the_accountname\"></td>
 </tr>
 <tr>
  <td align=\"right\" width=\"50%\"><b><font color=\"#ffffff\" size={fontsize} face=\"{fontface}\">Your password&nbsp;</font></b></td>
  <td align=\"left\" width=\"50%\"><input type=\"password\" name=\"the_password\" value=\"\$the_password\"></td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</tr>
 </tr>
</table><p>
<center><input type=\"submit\" value=\"Report the thread\"></center>
</form>";
$SKINTITLE["reportthread_page"]="Report thread to moderator";
$SKINREFRESH["reportthread_page"]="";
$SKIN["error_alreadyreported"]="<font face=\"{fontface}\" size={fontsize}>This thread has already been reported to a moderator. It is not allowed to report a thread more than one time.</font>";
$SKINTITLE["error_alreadyreported"]="Thread already reported";
$SKINREFRESH["error_alreadyreported"]="";
$SKIN["reportthread_ok"]="<font size={fontsize} face=\"{fontface}\" color=\"#000000\">The thread has successfully been reported. Please wait 2 seconds or click <a href=\"showforum.php?forumid=\$forumid\">here</a> to return to the thread list.</font>";
$SKINTITLE["reportthread_ok"]="Thread successfully reported";
$SKINREFRESH["reportthread_ok"]="showforum.php?forumid=\$forumid";
$SKIN["template_reportthread"]="Hi \$modusername,<br>

User <a class=\"pmlink\" href=\"\$url/finger.php?accountid=\$destuserid\">\$destusername</a> has reported thread <a class=\"pmlink\"  href=\"\$url/showthread.php?threadid=\$threadid\">\$threadtitle</a> within the <a class=\"pmlink\"  href=\"\$url/showforum.php?forumid=\$forumid\">\$forumname</a> forum. You'll probably want to check this out.<br>

The forum administrator";
$SKINTITLE["template_reportthread"]="Reported thread";
$SKINREFRESH["template_reportthread"]="";
$SKIN["error_reportthread"]="<font face=\"{fontface}\" size={fontsize}>This thread could not be reported to the appropriate moderator. This probably is because the user's mailbox is full. Please wait some time until the moderator has had time to clean it up. Thank you</font>";
$SKINTITLE["error_reportthread"]="Unable to report thread";
$SKINREFRESH["error_reportthread"]="";
$SKIN["error_reportlocked"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but locked threads cannot be reported. When a thread is locked, it has usually already been dealt with.</font>";
$SKINTITLE["error_reportlocked"]="Locked threads cannot be reported";
$SKINREFRESH["error_reportlocked"]="";
$SKIN["fingerpage_account"]="<table width=\"100%\">
 <tr>
  <td width=\"20%\"><font face=\"{fontface}\" size=2>Account name</font></td>
  <td width=\"80%\"><font face=\"{fontface}\" size=2>\$accountname</td>
 </tr>
 <tr>
  <td><font face=\"{fontface}\" size=2>Status</font></td>
  <td><font face=\"{fontface}\" size=2>\$status</font></td>
 </tr>
 <tr>
  <td><font face=\"{fontface}\" size=2>Email address</font></td>
  <td><font face=\"{fontface}\" size=2>\$email</font></td>
 </tr>
 <tr>
  <td><font face=\"{fontface}\" size=2>Number of posts</font></td>
  <td><font face=\"{fontface}\" size=2>\$nofposts</font></td>
 </tr>
 <tr>
  <td><font face=\"{fontface}\" size=2>Joining date</font></td>
  <td><font face=\"{fontface}\" size=2>\$joindate</font></td>
 </tr>
 <tr valign=\"top\">
  <td><font face=\"{fontface}\" size=2>Categories moderated</font></td>
  <td><font face=\"{fontface}\" size=2>\$catsmodded</font></td>
 </tr>
 <tr valign=\"top\">
  <td><font face=\"{fontface}\" size=2>Forums moderated</font></td>
  <td><font face=\"{fontface}\" size=2>\$forumsmodded</font></td>
 </tr>
 <tr>
  <td><font face=\"{fontface}\" size=2>Last post</font></td>
  <td><font face=\"{fontface}\" size=2>\$lastpost</font></td>
 </tr>
 \$customfields
</table>";
$SKINTITLE["fingerpage_account"]="Finger results";
$SKINREFRESH["fingerpage_account"]="";
$SKIN["finger_nolastpost"]="<i>None</i>";
$SKINTITLE["finger_nolastpost"]="";
$SKINREFRESH["finger_nolastpost"]="";
$SKIN["finger_viewcustom1"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom1"]="";
$SKINREFRESH["finger_viewcustom1"]="";
$SKIN["finger_viewcustom2"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom2"]="";
$SKINREFRESH["finger_viewcustom2"]="";
$SKIN["finger_viewcustom3"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom3"]="";
$SKINREFRESH["finger_viewcustom3"]="";
$SKIN["finger_viewcustom4"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom4"]="";
$SKINREFRESH["finger_viewcustom4"]="";
$SKIN["finger_viewcustom5"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom5"]="";
$SKINREFRESH["finger_viewcustom5"]="";
$SKIN["finger_viewcustom6"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom6"]="";
$SKINREFRESH["finger_viewcustom6"]="";
$SKIN["finger_viewcustom7"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom7"]="";
$SKINREFRESH["finger_viewcustom7"]="";
$SKIN["finger_viewcustom8"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom8"]="";
$SKINREFRESH["finger_viewcustom8"]="";
$SKIN["finger_viewcustom9"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom9"]="";
$SKINREFRESH["finger_viewcustom9"]="";
$SKIN["fingerpage_group"]="<table width=\"100%\">
 <tr>
  <td width=\"20%\"><font face=\"{fontface}\" size=2>Group Name</font></td>
  <td width=\"80%\"><font face=\"{fontface}\" size=2>\$groupname</td>
 </tr>
 <tr>
  <td><font face=\"{fontface}\" size=2>Group Description</font></td>
  <td><font face=\"{fontface}\" size=2>\$groupdesc</font></td>
 </tr>
 <tr valign=\"top\">
  <td><font face=\"{fontface}\" size=2>Group Members</font></td>
  <td><font face=\"{fontface}\" size=2>\$groupmembers</font></td>
 </tr>
</table>";
$SKINTITLE["fingerpage_group"]="Finger results";
$SKINREFRESH["fingerpage_group"]="";
$SKIN["finger_group_member"]="<li><a href=\"finger.php?accountid=\$accountid\">\$accountname</a></li>";
$SKINTITLE["finger_group_member"]="";
$SKINREFRESH["finger_group_member"]="";
$SKIN["finger_group_nomembers"]="<li><i>None</i></li>";
$SKINTITLE["finger_group_nomembers"]="";
$SKINREFRESH["finger_group_nomembers"]="";
$SKIN["birthday_list"]="<font face=\"{fontface}\" size=1>Birthdays: \$birthdays</font>";
$SKINTITLE["birthday_list"]="";
$SKINREFRESH["birthday_list"]="";
$SKIN["birthday_firstmem"]="<a href=\"finger.php?accountid=\$accountid\">\$accountname</a> (\$age)";
$SKINTITLE["birthday_firstmem"]="";
$SKINREFRESH["birthday_firstmem"]="";
$SKIN["birthday_moremem"]=", <a href=\"finger.php?accountid=\$accountid\">\$accountname</a> (\$age)";
$SKINTITLE["birthday_moremem"]="";
$SKINREFRESH["birthday_moremem"]="";
$SKIN["error_registerdisabled"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but registration of new accounts has been disabled by the forum administrator.</font>";
$SKINTITLE["error_registerdisabled"]="Registration of new accounts is disabled";
$SKINREFRESH["error_registerdisabled"]="";
$SKIN["finger_forum_mod"]="<li><a href=\"showforum.php?forumid=\$forumid\">\$forumname</a></li>";
$SKINTITLE["finger_forum_mod"]="";
$SKINREFRESH["finger_forum_mod"]="";
$SKIN["editcustom_10"]="<tr>
 <td><font face=\"{fontface}\" size={fontsize}>\$fieldname</font></td>
 <td><select name=\"field[\$fieldid]\"><option value=\"Afghanistan\" [[\$fieldvalue==Afghanistan&&selected]]>Afghanistan</option><option value=\"Albania\" [[\$fieldvalue==Albania&&selected]]>Albania</option><option value=\"Algeria\" [[\$fieldvalue==Algeria&&selected]]>Algeria</option><option value=\"American Samoa\" [[\$fieldvalue==American Samoa&&selected]]>American Samoa</option><option value=\"Andorra\" [[\$fieldvalue==Andorra&&selected]]>Andorra</option><option value=\"Angola\" [[\$fieldvalue==Angola&&selected]]>Angola</option><option value=\"Anguilla\" [[\$fieldvalue==Anguilla&&selected]]>Anguilla</option><option value=\"Antarctica\" [[\$fieldvalue==Antarctica&&selected]]>Antarctica</option><option value=\"Antigua and Barbuda\" [[\$fieldvalue==Antigua and Barbuda&&selected]]>Antigua and Barbuda</option><option value=\"Argentina\" [[\$fieldvalue==Argentina&&selected]]>Argentina</option><option value=\"Armenia\" [[\$fieldvalue==Armenia&&selected]]>Armenia</option><option value=\"Aruba\" [[\$fieldvalue==Aruba&&selected]]>Aruba</option><option value=\"Australia\" [[\$fieldvalue==Australia&&selected]]>Australia</option><option value=\"Austria\" [[\$fieldvalue==Austria&&selected]]>Austria</option><option value=\"Azerbaijan\" [[\$fieldvalue==Azerbaijan&&selected]]>Azerbaijan</option><option value=\"Bahamas\" [[\$fieldvalue==Bahamas&&selected]]>Bahamas</option><option value=\"Bahrain\" [[\$fieldvalue==Bahrain&&selected]]>Bahrain</option><option value=\"Bangladesh\" [[\$fieldvalue==Bangladesh&&selected]]>Bangladesh</option><option value=\"Barbados\" [[\$fieldvalue==Barbados&&selected]]>Barbados</option><option value=\"Belarus\" [[\$fieldvalue==Belarus&&selected]]>Belarus</option><option value=\"Belgium\" [[\$fieldvalue==Belgium&&selected]]>Belgium</option><option value=\"Belize\" [[\$fieldvalue==Belize&&selected]]>Belize</option><option value=\"Bermuda\" [[\$fieldvalue==Bermuda&&selected]]>Bermuda</option><option value=\"Bhutan\" [[\$fieldvalue==Bhutan&&selected]]>Bhutan</option><option value=\"Bolivia\" [[\$fieldvalue==Bolivia&&selected]]>Bolivia</option><option value=\"Bosnia and Herzegovina\" [[\$fieldvalue==Bosnia and Herzegovina&&selected]]>Bosnia and Herzegovina</option><option value=\"Botswana\" [[\$fieldvalue==Botswana&&selected]]>Botswana</option><option value=\"Bouvet Island\" [[\$fieldvalue==Bouvet Island&&selected]]>Bouvet Island</option><option value=\"Brazil\" [[\$fieldvalue==Brazil&&selected]]>Brazil</option><option value=\"British Indian Ocean Territory\" [[\$fieldvalue==British Indian Ocean Territory&&selected]]>British Indian Ocean Territory</option><option value=\"Brunei Darussalam\" [[\$fieldvalue==Brunei Darussalam&&selected]]>Brunei Darussalam</option><option value=\"Bulgaria\" [[\$fieldvalue==Bulgaria&&selected]]>Bulgaria</option><option value=\"Burkina Faso\" [[\$fieldvalue==Burkina Faso&&selected]]>Burkina Faso</option><option value=\"Burundi\" [[\$fieldvalue==Burundi&&selected]]>Burundi</option><option value=\"Cambodia\" [[\$fieldvalue==Cambodia&&selected]]>Cambodia</option><option value=\"Cameroon\" [[\$fieldvalue==Cameroon&&selected]]>Cameroon</option><option value=\"Canada\" [[\$fieldvalue==Canada&&selected]]>Canada</option><option value=\"Cape Verde\" [[\$fieldvalue==Cape Verde&&selected]]>Cape Verde</option><option value=\"Cayman Islands\" [[\$fieldvalue==Cayman Islands&&selected]]>Cayman Islands</option><option value=\"Central African Republic\" [[\$fieldvalue==Central African Republic&&selected]]>Central African Republic</option><option value=\"Chad\" [[\$fieldvalue==Chad&&selected]]>Chad</option><option value=\"Chile\" [[\$fieldvalue==Chile&&selected]]>Chile</option><option value=\"China\" [[\$fieldvalue==China&&selected]]>China</option><option value=\"Christmas Island\" [[\$fieldvalue==Christmas Island&&selected]]>Christmas Island</option><option value=\"Cocos (Keeling Islands)\" [[\$fieldvalue==Cocos (Keeling Islands)&&selected]]>Cocos (Keeling Islands)</option><option value=\"Colombia\" [[\$fieldvalue==Colombia&&selected]]>Colombia</option><option value=\"Comoros\" [[\$fieldvalue==Comoros&&selected]]>Comoros</option><option value=\"Congo\" [[\$fieldvalue==Congo&&selected]]>Congo</option><option value=\"Cook Islands\" [[\$fieldvalue==Cook Islands&&selected]]>Cook Islands</option><option value=\"Costa Rica\" [[\$fieldvalue==Costa Rica&&selected]]>Costa Rica</option><option value=\"Cote D'Ivoire (Ivory Coast)\" [[\$fieldvalue==Cote D'Ivoire (Ivory Coast)&&selected]]>Cote D'Ivoire (Ivory Coast)</option><option value=\"Croatia (Hrvatska)\" [[\$fieldvalue==Croatia (Hrvatska)&&selected]]>Croatia (Hrvatska)</option><option value=\"Cuba\" [[\$fieldvalue==Cuba&&selected]]>Cuba</option><option value=\"Cyprus\" [[\$fieldvalue==Cyprus&&selected]]>Cyprus</option><option value=\"Czech Republic\" [[\$fieldvalue==Czech Republic&&selected]]>Czech Republic</option><option value=\"Denmark\" [[\$fieldvalue==Denmark&&selected]]>Denmark</option><option value=\"Djibouti\" [[\$fieldvalue==Djibouti&&selected]]>Djibouti</option><option value=\"Dominican Republic\" [[\$fieldvalue==Dominican Republic&&selected]]>Dominican Republic</option><option value=\"Dominica\" [[\$fieldvalue==Dominica&&selected]]>Dominica</option><option value=\"East Timor\" [[\$fieldvalue==East Timor&&selected]]>East Timor</option><option value=\"Ecuador\" [[\$fieldvalue==Ecuador&&selected]]>Ecuador</option><option value=\"Egypt\" [[\$fieldvalue==Egypt&&selected]]>Egypt</option><option value=\"El Salvador\" [[\$fieldvalue==El Salvador&&selected]]>El Salvador</option><option value=\"Equatorial Guinea\" [[\$fieldvalue==Equatorial Guinea&&selected]]>Equatorial Guinea</option><option value=\"Eritrea\" [[\$fieldvalue==Eritrea&&selected]]>Eritrea</option><option value=\"Estonia\" [[\$fieldvalue==Estonia&&selected]]>Estonia</option><option value=\"Ethiopia\" [[\$fieldvalue==Ethiopia&&selected]]>Ethiopia</option><option value=\"Falkland Islands (Malvinas)\" [[\$fieldvalue==Falkland Islands (Malvinas)&&selected]]>Falkland Islands (Malvinas)</option><option value=\"Faroe Islands\" [[\$fieldvalue==Faroe Islands&&selected]]>Faroe Islands</option><option value=\"Fiji\" [[\$fieldvalue==Fiji&&selected]]>Fiji</option><option value=\"Finland\" [[\$fieldvalue==Finland&&selected]]>Finland</option><option value=\"France, Metropolitan\" [[\$fieldvalue==France, Metropolitan&&selected]]>France, Metropolitan</option><option value=\"France\" [[\$fieldvalue==France&&selected]]>France</option><option value=\"French Guiana\" [[\$fieldvalue==French Guiana&&selected]]>French Guiana</option><option value=\"French Polynesia\" [[\$fieldvalue==French Polynesia&&selected]]>French Polynesia</option><option value=\"French Southern Territories\" [[\$fieldvalue==French Southern Territories&&selected]]>French Southern Territories</option><option value=\"Gabon\" [[\$fieldvalue==Gabon&&selected]]>Gabon</option><option value=\"Gambia\" [[\$fieldvalue==Gambia&&selected]]>Gambia</option><option value=\"Georgia\" [[\$fieldvalue==Georgia&&selected]]>Georgia</option><option value=\"Germany\" [[\$fieldvalue==Germany&&selected]]>Germany</option><option value=\"Ghana\" [[\$fieldvalue==Ghana&&selected]]>Ghana</option><option value=\"Gibraltar\" [[\$fieldvalue==Gibraltar&&selected]]>Gibraltar</option><option value=\"Greece\" [[\$fieldvalue==Greece&&selected]]>Greece</option><option value=\"Greenland\" [[\$fieldvalue==Greenland&&selected]]>Greenland</option><option value=\"Grenada\" [[\$fieldvalue==Grenada&&selected]]>Grenada</option><option value=\"Guadeloupe\" [[\$fieldvalue==Guadeloupe&&selected]]>Guadeloupe</option><option value=\"Guam\" [[\$fieldvalue==Guam&&selected]]>Guam</option><option value=\"Guatemala\" [[\$fieldvalue==Guatemala&&selected]]>Guatemala</option><option value=\"Guinea-Bissau\" [[\$fieldvalue==Guinea-Bissau&&selected]]>Guinea-Bissau</option><option value=\"Guinea\" [[\$fieldvalue==Guinea&&selected]]>Guinea</option><option value=\"Guyana\" [[\$fieldvalue==Guyana&&selected]]>Guyana</option><option value=\"Haiti\" [[\$fieldvalue==Haiti&&selected]]>Haiti</option><option value=\"Heard and McDonald Islands\" [[\$fieldvalue==Heard and McDonald Islands&&selected]]>Heard and McDonald Islands</option><option value=\"Honduras\" [[\$fieldvalue==Honduras&&selected]]>Honduras</option><option value=\"Hong Kong\" [[\$fieldvalue==Hong Kong&&selected]]>Hong Kong</option><option value=\"Hungary\" [[\$fieldvalue==Hungary&&selected]]>Hungary</option><option value=\"Iceland\" [[\$fieldvalue==Iceland&&selected]]>Iceland</option><option value=\"India\" [[\$fieldvalue==India&&selected]]>India</option><option value=\"Indonesia\" [[\$fieldvalue==Indonesia&&selected]]>Indonesia</option><option value=\"Iran\" [[\$fieldvalue==Iran&&selected]]>Iran</option><option value=\"Iraq\" [[\$fieldvalue==Iraq&&selected]]>Iraq</option><option value=\"Ireland\" [[\$fieldvalue==Ireland&&selected]]>Ireland</option><option value=\"Israel\" [[\$fieldvalue==Israel&&selected]]>Israel</option><option value=\"Italy\" [[\$fieldvalue==Italy&&selected]]>Italy</option><option value=\"Jamaica\" [[\$fieldvalue==Jamaica&&selected]]>Jamaica</option><option value=\"Japan\" [[\$fieldvalue==Japan&&selected]]>Japan</option><option value=\"Jordan\" [[\$fieldvalue==Jordan&&selected]]>Jordan</option><option value=\"Kazakhstan\" [[\$fieldvalue==Kazakhstan&&selected]]>Kazakhstan</option><option value=\"Kenya\" [[\$fieldvalue==Kenya&&selected]]>Kenya</option><option value=\"Kiribati\" [[\$fieldvalue==Kiribati&&selected]]>Kiribati</option><option value=\"Korea (North)\" [[\$fieldvalue==Korea (North)&&selected]]>Korea (North)</option><option value=\"Korea (South)\" [[\$fieldvalue==Korea (South)&&selected]]>Korea (South)</option><option value=\"Kuwait\" [[\$fieldvalue==Kuwait&&selected]]>Kuwait</option><option value=\"Kyrgyzstan\" [[\$fieldvalue==Kyrgyzstan&&selected]]>Kyrgyzstan</option><option value=\"Laos\" [[\$fieldvalue==Laos&&selected]]>Laos</option><option value=\"Latvia\" [[\$fieldvalue==Latvia&&selected]]>Latvia</option><option value=\"Lebanon\" [[\$fieldvalue==Lebanon&&selected]]>Lebanon</option><option value=\"Lesotho\" [[\$fieldvalue==Lesotho&&selected]]>Lesotho</option><option value=\"Liberia\" [[\$fieldvalue==Liberia&&selected]]>Liberia</option><option value=\"Libya\" [[\$fieldvalue==Libya&&selected]]>Libya</option><option value=\"Liechtenstein\" [[\$fieldvalue==Liechtenstein&&selected]]>Liechtenstein</option><option value=\"Lithuania\" [[\$fieldvalue==Lithuania&&selected]]>Lithuania</option><option value=\"Luxembourg\" [[\$fieldvalue==Luxembourg&&selected]]>Luxembourg</option><option value=\"Macau\" [[\$fieldvalue==Macau&&selected]]>Macau</option><option value=\"Macedonia\" [[\$fieldvalue==Macedonia&&selected]]>Macedonia</option><option value=\"Madagascar\" [[\$fieldvalue==Madagascar&&selected]]>Madagascar</option><option value=\"Malawi\" [[\$fieldvalue==Malawi&&selected]]>Malawi</option><option value=\"Malaysia\" [[\$fieldvalue==Malaysia&&selected]]>Malaysia</option><option value=\"Maldives\" [[\$fieldvalue==Maldives&&selected]]>Maldives</option><option value=\"Mali\" [[\$fieldvalue==Mali&&selected]]>Mali</option><option value=\"Malta\" [[\$fieldvalue==Malta&&selected]]>Malta</option><option value=\"Marshall Islands\" [[\$fieldvalue==Marshall Islands&&selected]]>Marshall Islands</option><option value=\"Martinique\" [[\$fieldvalue==Martinique&&selected]]>Martinique</option><option value=\"Mauritania\" [[\$fieldvalue==Mauritania&&selected]]>Mauritania</option><option value=\"Mauritius\" [[\$fieldvalue==Mauritius&&selected]]>Mauritius</option><option value=\"Mayotte\" [[\$fieldvalue==Mayotte&&selected]]>Mayotte</option><option value=\"Mexico\" [[\$fieldvalue==Mexico&&selected]]>Mexico</option><option value=\"Micronesia\" [[\$fieldvalue==Micronesia&&selected]]>Micronesia</option><option value=\"Moldova\" [[\$fieldvalue==Moldova&&selected]]>Moldova</option><option value=\"Monaco\" [[\$fieldvalue==Monaco&&selected]]>Monaco</option><option value=\"Mongolia\" [[\$fieldvalue==Mongolia&&selected]]>Mongolia</option><option value=\"Montserrat\" [[\$fieldvalue==Montserrat&&selected]]>Montserrat</option><option value=\"Morocco\" [[\$fieldvalue==Morocco&&selected]]>Morocco</option><option value=\"Mozambique\" [[\$fieldvalue==Mozambique&&selected]]>Mozambique</option><option value=\"Myanmar\" [[\$fieldvalue==Myanmar&&selected]]>Myanmar</option><option value=\"Namibia\" [[\$fieldvalue==Namibia&&selected]]>Namibia</option><option value=\"Nauru\" [[\$fieldvalue==Nauru&&selected]]>Nauru</option><option value=\"Nepal\" [[\$fieldvalue==Nepal&&selected]]>Nepal</option><option value=\"Netherlands Antilles\" [[\$fieldvalue==Netherlands Antilles&&selected]]>Netherlands Antilles</option><option value=\"Netherlands\" [[\$fieldvalue==Netherlands&&selected]]>Netherlands</option><option value=\"New Caledonia\" [[\$fieldvalue==New Caledonia&&selected]]>New Caledonia</option><option value=\"New Zealand\" [[\$fieldvalue==New Zealand&&selected]]>New Zealand</option><option value=\"Nicaragua\" [[\$fieldvalue==Nicaragua&&selected]]>Nicaragua</option><option value=\"Nigeria\" [[\$fieldvalue==Nigeria&&selected]]>Nigeria</option><option value=\"Niger\" [[\$fieldvalue==Niger&&selected]]>Niger</option><option value=\"Niue\" [[\$fieldvalue==Niue&&selected]]>Niue</option><option value=\"Norfolk Island\" [[\$fieldvalue==Norfolk Island&&selected]]>Norfolk Island</option><option value=\"Northern Mariana Islands\" [[\$fieldvalue==Northern Mariana Islands&&selected]]>Northern Mariana Islands</option><option value=\"Norway\" [[\$fieldvalue==Norway&&selected]]>Norway</option><option value=\"Oman\" [[\$fieldvalue==Oman&&selected]]>Oman</option><option value=\"Pakistan\" [[\$fieldvalue==Pakistan&&selected]]>Pakistan</option><option value=\"Palau\" [[\$fieldvalue==Palau&&selected]]>Palau</option><option value=\"Panama\" [[\$fieldvalue==Panama&&selected]]>Panama</option><option value=\"Papua New Guinea\" [[\$fieldvalue==Papua New Guinea&&selected]]>Papua New Guinea</option><option value=\"Paraguay\" [[\$fieldvalue==Paraguay&&selected]]>Paraguay</option><option value=\"Peru\" [[\$fieldvalue==Peru&&selected]]>Peru</option><option value=\"Philippines\" [[\$fieldvalue==Philippines&&selected]]>Philippines</option><option value=\"Pitcairn\" [[\$fieldvalue==Pitcairn&&selected]]>Pitcairn</option><option value=\"Poland\" [[\$fieldvalue==Poland&&selected]]>Poland</option><option value=\"Portugal\" [[\$fieldvalue==Portugal&&selected]]>Portugal</option><option value=\"Puerto Rico\" [[\$fieldvalue==Puerto Rico&&selected]]>Puerto Rico</option><option value=\"Qatar\" [[\$fieldvalue==Qatar&&selected]]>Qatar</option><option value=\"Reunion\" [[\$fieldvalue==Reunion&&selected]]>Reunion</option><option value=\"Romania\" [[\$fieldvalue==Romania&&selected]]>Romania</option><option value=\"Russian Federation\" [[\$fieldvalue==Russian Federation&&selected]]>Russian Federation</option><option value=\"Rwanda\" [[\$fieldvalue==Rwanda&&selected]]>Rwanda</option><option value=\"S. Georgia and S. Sandwich Isls.\" [[\$fieldvalue==S. Georgia and S. Sandwich Isls.&&selected]]>S. Georgia and S. Sandwich Isls.</option><option value=\"Saint Kitts and Nevis\" [[\$fieldvalue==Saint Kitts and Nevis&&selected]]>Saint Kitts and Nevis</option><option value=\"Saint Lucia\" [[\$fieldvalue==Saint Lucia&&selected]]>Saint Lucia</option><option value=\"Saint Vincent and The Grenadines\" [[\$fieldvalue==Saint Vincent and The Grenadines&&selected]]>Saint Vincent and The Grenadines</option><option value=\"Samoa\" [[\$fieldvalue==Samoa&&selected]]>Samoa</option><option value=\"San Marino\" [[\$fieldvalue==San Marino&&selected]]>San Marino</option><option value=\"Sao Tome and Principe\" [[\$fieldvalue==Sao Tome and Principe&&selected]]>Sao Tome and Principe</option><option value=\"Saudi Arabia\" [[\$fieldvalue==Saudi Arabia&&selected]]>Saudi Arabia</option><option value=\"Senegal\" [[\$fieldvalue==Senegal&&selected]]>Senegal</option><option value=\"Seychelles\" [[\$fieldvalue==Seychelles&&selected]]>Seychelles</option><option value=\"Sierra Leone\" [[\$fieldvalue==Sierra Leone&&selected]]>Sierra Leone</option><option value=\"Singapore\" [[\$fieldvalue==Singapore&&selected]]>Singapore</option><option value=\"Slovak Republic\" [[\$fieldvalue==Slovak Republic&&selected]]>Slovak Republic</option><option value=\"Slovenia\" [[\$fieldvalue==Slovenia&&selected]]>Slovenia</option><option value=\"Solomon Islands\" [[\$fieldvalue==Solomon Islands&&selected]]>Solomon Islands</option><option value=\"Somalia\" [[\$fieldvalue==Somalia&&selected]]>Somalia</option><option value=\"South Africa\" [[\$fieldvalue==South Africa&&selected]]>South Africa</option><option value=\"Spain\" [[\$fieldvalue==Spain&&selected]]>Spain</option><option value=\"Sri Lanka\" [[\$fieldvalue==Sri Lanka&&selected]]>Sri Lanka</option><option value=\"St. Helena\" [[\$fieldvalue==St. Helena&&selected]]>St. Helena</option><option value=\"St. Pierre and Miquelon\" [[\$fieldvalue==St. Pierre and Miquelon&&selected]]>St. Pierre and Miquelon</option><option value=\"Sudan\" [[\$fieldvalue==Sudan&&selected]]>Sudan</option><option value=\"Suriname\" [[\$fieldvalue==Suriname&&selected]]>Suriname</option><option value=\"Svalbard and Jan Mayen Islands\" [[\$fieldvalue==Svalbard and Jan Mayen Islands&&selected]]>Svalbard and Jan Mayen Islands</option><option value=\"Swaziland\" [[\$fieldvalue==Swaziland&&selected]]>Swaziland</option><option value=\"Sweden\" [[\$fieldvalue==Sweden&&selected]]>Sweden</option><option value=\"Switzerland\" [[\$fieldvalue==Switzerland&&selected]]>Switzerland</option><option value=\"Syria\" [[\$fieldvalue==Syria&&selected]]>Syria</option><option value=\"Taiwan\" [[\$fieldvalue==Taiwan&&selected]]>Taiwan</option><option value=\"Tajikistan\" [[\$fieldvalue==Tajikistan&&selected]]>Tajikistan</option><option value=\"Tanzania\" [[\$fieldvalue==Tanzania&&selected]]>Tanzania</option><option value=\"Thailand\" [[\$fieldvalue==Thailand&&selected]]>Thailand</option><option value=\"Togo\" [[\$fieldvalue==Togo&&selected]]>Togo</option><option value=\"Tokelau\" [[\$fieldvalue==Tokelau&&selected]]>Tokelau</option><option value=\"Tonga\" [[\$fieldvalue==Tonga&&selected]]>Tonga</option><option value=\"Trinidad and Tobago\" [[\$fieldvalue==Trinidad and Tobago&&selected]]>Trinidad and Tobago</option><option value=\"Tunisia\" [[\$fieldvalue==Tunisia&&selected]]>Tunisia</option><option value=\"Turkey\" [[\$fieldvalue==Turkey&&selected]]>Turkey</option><option value=\"Turkmenistan\" [[\$fieldvalue==Turkmenistan&&selected]]>Turkmenistan</option><option value=\"Turks and Caicos Islands\" [[\$fieldvalue==Turks and Caicos Islands&&selected]]>Turks and Caicos Islands</option><option value=\"Tuvalu\" [[\$fieldvalue==Tuvalu&&selected]]>Tuvalu</option><option value=\"US Minor Outlying Islands\" [[\$fieldvalue==US Minor Outlying Islands&&selected]]>US Minor Outlying Islands</option><option value=\"Uganda\" [[\$fieldvalue==Uganda&&selected]]>Uganda</option><option value=\"Ukraine\" [[\$fieldvalue==Ukraine&&selected]]>Ukraine</option><option value=\"United Arab Emirates\" [[\$fieldvalue==United Arab Emirates&&selected]]>United Arab Emirates</option><option value=\"United Kingdom\" [[\$fieldvalue==United Kingdom&&selected]]>United Kingdom</option><option value=\"United States\" [[\$fieldvalue==United States&&selected]]>United States</option><option value=\"Uruguay\" [[\$fieldvalue==Uruguay&&selected]]>Uruguay</option><option value=\"Uzbekistan\" [[\$fieldvalue==Uzbekistan&&selected]]>Uzbekistan</option><option value=\"Vanuatu\" [[\$fieldvalue==Vanuatu&&selected]]>Vanuatu</option><option value=\"Vatican City State\" [[\$fieldvalue==Vatican City State&&selected]]>Vatican City State</option><option value=\"Venezuela\" [[\$fieldvalue==Venezuela&&selected]]>Venezuela</option><option value=\"Vietnam\" [[\$fieldvalue==Vietnam&&selected]]>Vietnam</option><option value=\"Virgin Islands (British)\" [[\$fieldvalue==Virgin Islands (British)&&selected]]>Virgin Islands (British)</option><option value=\"Virgin Islands (US)\" [[\$fieldvalue==Virgin Islands (US)&&selected]]>Virgin Islands (US)</option><option value=\"Wallis and Futuna Islands\" [[\$fieldvalue==Wallis and Futuna Islands&&selected]]>Wallis and Futuna Islands</option><option value=\"Western Sahara\" [[\$fieldvalue==Western Sahara&&selected]]>Western Sahara</option><option value=\"Yemen\" [[\$fieldvalue==Yemen&&selected]]>Yemen</option><option value=\"Yugoslavia\" [[\$fieldvalue==Yugoslavia&&selected]]>Yugoslavia</option><option value=\"Zaire\" [[\$fieldvalue==Zaire&&selected]]>Zaire</option><option value=\"Zambia\" [[\$fieldvalue==Zambia&&selected]]>Zambia</option><option value=\"Zimbabwe\" [[\$fieldvalue==Zimbabwe&&selected]]>Zimbabwe</option></select></td>
</tr>";
$SKINTITLE["editcustom_10"]="";
$SKINREFRESH["editcustom_10"]="";
$SKIN["finger_cat_mod"]="<li><a href=\"index.php?catid=\$catid\">\$forumname</a></li>";
$SKINTITLE["finger_cat_mod"]="";
$SKINREFRESH["finger_cat_mod"]="";
$SKIN["finger_nomod"]="<li><i>Nothing</i></li>";
$SKINTITLE["finger_nomod"]="";
$SKINREFRESH["finger_nomod"]="";
$SKIN["finger_viewcustom10"]="<tr>
 <td><font face=\"{fontface}\" size=2>\$fieldname</font></td>
 <td><font face=\"{fontface}\" size=2>\$fieldvalue</font></td>
</tr>";
$SKINTITLE["finger_viewcustom10"]="";
$SKINREFRESH["finger_viewcustom10"]="";
$SKIN["viewcustom_10"]="<font size=1 face=\"{fontface}\"><b>\$fieldname</b>: \$fieldvalue</font><br>";
$SKINTITLE["viewcustom_10"]="Location field";
$SKINREFRESH["viewcustom_10"]="";
$SKIN["coppa_page"]="<font face=\"{fontface}\" size={fontsize}>Your account has successfully been created, but since you haven't yet turned 13 and are an inhabitant of the United States, you won't be able to use it.<p>
Please fill in the email address of your parents in the box below. They will get an email requesting to send a fax indicating their agreement with your signup.<p>
<form action=\"register.php\" method=\"post\">
<input type=\"hidden\" name=\"action\" value=\"coppa_email\">
<input type=\"hidden\" name=\"userid\" value=\"\$userid\">
<table width=\"100%\">
 <tr>
  <td width=\"49%\" align=\"right\"><font face=\"{fontface}\" size=2>Parent email address</font></td>
  <td width=\"1%\" align=\"center\">&nbsp;</td>
  <td width=\"51%\"><input type=\"text\" name=\"parent_email\"></td>
 </tr>
</table><p>
<center>We thank you for your understanding on this matter.<p>
<input type=\"submit\" value=\"Submit\"></center>
</form></font>";
$SKINTITLE["coppa_page"]="COPPA compliance";
$SKINREFRESH["coppa_page"]="";
$SKIN["coppa_email"]="Hi,<p>

We have just received a request for the following registration on our boards at <a href=\"\$url\">\$forumtitle</a>. The details are:<p>

Username: <code>\$the_username</code><br>
Password: <code>\$the_password</code><br>
Email: <a href=\"mailto:\$email\">\$the_email</a></code><p>

This person has listed your email address as the parent or legal guardian's email address. Therefore, it is up to you whether we will let this person post messages in our boards or not<p>

Should you agree with this person having a forum account, please print this email, write your signature on the line, and fax it to us. This will legally allow us to process your signup.<p>

<hr>
I hereby agree that my minor accesses these forums. Neither the staff of this web site nor other visitors of the website are responsible for the person's behavoir.<p>

I'd like to use the following password in order to modify my minor's profile<p>

<br><br><br><br>

Signature<br><br><br><br><br><br><br><br>
<hr>

Our fax number is [FILL THIS IN]<p>

We thank you for your cooperation.<br>
The administrators at \$forumtitle<br>";
$SKINTITLE["coppa_email"]="COPPA compliance";
$SKINREFRESH["coppa_email"]="";
$SKIN["error_alreadyparentemailed"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but our records indicate we have already emailed you parent</font>";
$SKINTITLE["error_alreadyparentemailed"]="Parent already emailed";
$SKINREFRESH["error_alreadyparentemailed"]="";
$SKIN["error_parentemailed"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but our records indicate we have already emailed your parent</font>";
$SKINTITLE["error_parentemailed"]="Parent already emailed";
$SKINREFRESH["error_parentemailed"]="";
$SKIN["coppa_parentemailed"]="<font face=\"{fontface}\" size={fontsize}>Thank you, we have successfully emailed your parent. We will process the fax that should be sent to us as soon as we can get. Thank you for your cooperation</font>";
$SKINTITLE["coppa_parentemailed"]="Parent successfully emailed";
$SKINREFRESH["coppa_parentemailed"]="";
$SKIN["quotepost"]="<a href=\"postreply.php?threadid=\$threadid&quotefrom=\$postid\"><img src=\"images/quote.gif\" alt=\"[Quote Post]\" border=0></a>";
$SKINTITLE["quotepost"]="";
$SKINREFRESH["quotepost"]="";
$SKIN["quoted_post"]="Originally posted by \$poster_username
[quote]\$message [/quote]";
$SKINTITLE["quoted_post"]="";
$SKINREFRESH["quoted_post"]="";
$SKIN["maxcode_quote"]="<blockquote><font size=1>quote:</font><hr>[[DOUBLEBaCKSLASH]]\1<hr></blockquote>";
$SKINTITLE["maxcode_quote"]="";
$SKINREFRESH["maxcode_quote"]="";
$SKIN["maxcode_code"]="<blockquote><font size=1>code:</font><hr>[[DOUBLEBaCKSLASH]]\1<hr></blockquote>";
$SKINTITLE["maxcode_code"]="";
$SKINREFRESH["maxcode_code"]="";
$SKIN["maxcode_bold"]="<b>[[DOUBLEBaCKSLASH]]\1</b>";
$SKINTITLE["maxcode_bold"]="";
$SKINREFRESH["maxcode_bold"]="";
$SKIN["maxcode_italic"]="<i>[[DOUBLEBaCKSLASH]]\1</i>";
$SKINTITLE["maxcode_italic"]="";
$SKINREFRESH["maxcode_italic"]="";
$SKIN["maxcode_underline"]="<u>[[DOUBLEBaCKSLASH]]\1</u>";
$SKINTITLE["maxcode_underline"]="";
$SKINREFRESH["maxcode_underline"]="";
$SKIN["maxcode_email"]="<a href=\"mailto:[[DOUBLEBaCKSLASH]]\1\">[[DOUBLEBaCKSLASH]]\1</a>";
$SKINTITLE["maxcode_email"]="";
$SKINREFRESH["maxcode_email"]="";
$SKIN["maxcode_url"]="<a target=\"_blank\" href=\"[[DOUBLEBaCKSLASH]]\1\">[[DOUBLEBaCKSLASH]]\1</a>";
$SKINTITLE["maxcode_url"]="";
$SKINREFRESH["maxcode_url"]="";
$SKIN["maxcode_exturl"]="<a target=\"_blank\" href=\"[[DOUBLEBaCKSLASH]]\1\">][[DOUBLEBaCKSLASH]]\2</a>";
$SKINTITLE["maxcode_exturl"]="";
$SKINREFRESH["maxcode_exturl"]="";
$SKIN["maxcode_img"]="<img src=\"[[DOUBLEBaCKSLASH]]\1\" alt=\"Image\" border=0>";
$SKINTITLE["maxcode_img"]="";
$SKINREFRESH["maxcode_img"]="";
$SKIN["changemail_email"]="Hi,<p>

You have just changed your email address at <a href=\"\$url\">\$forumtitle</a>. In order to ensure validity of your new email address, please re-activate your account by clicking <a href=\"\$url/register.php?action=activate&userid=\$userid&activateid=\$activateid&password=\$password\">this link</a>. You won't be able to post at the forums if you don't do this.

Thank you!<br>
The administrators at \$forumtitle<br>";
$SKINTITLE["changemail_email"]="Account reactivation";
$SKINREFRESH["changemail_email"]="";
$SKIN["pmuser"]="<a href=\"pm.php?action=compose&destid=\$authorid\"><img src=\"images/pm.gif\" alt=\"Private Message\" border=0></a>";
$SKINTITLE["pmuser"]="";
$SKINREFRESH["pmuser"]="";
$SKIN["postpage_print"]="<font face=\"{fontface}\" size=2><a href=\"index.php\">\$forums_title</a> > <a href=\"index.php?catid=\$catid\">\$cat_title</a> > <a href=\"showforum.php?forumid=\$forumid\">\$forumname</a> > <b>\$threadtitle</b></font><p>
\$locktext
<table width=\"100%\" cellspacing=1 cellpadding=2>
<tr bgcolor=\"#000000\">
  <td width=\"15%\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#ffffff\"><b>Author</b></font></td>
  <td width=\"85%\" align=\"center\"><font size={fontsize} face=\"{fontface}\" color=\"#ffffff\"><b>Message</b></font></td>
</tr>
<!-- show the actual listing of the post -->
\$postlist
</table>
\$pagelist<br>
<center>\$thread_locked</center><p>";
$SKINTITLE["postpage_print"]="\$forumname - \$threadtitle";
$SKINREFRESH["postpage_print"]="";
$SKIN["post_list_print"]="<tr>
  <td valign=\"top\"><font size={fontsize} face=\"{fontface}\"><a href=\"finger.php?accountid=\$authorid\">\$author</a></font><br>
<font size=1 face=\"{fontface}\">\$author_status<br>
<b>Number of posts: </b>\$author_nofposts<br>
<b>Joined on: </b>\$author_joindate</font><br>
\$customfields</td>
  <td valign=\"top\"><font size=1 face=\"{fontface}\" color=\"#000000\"><img src=\"{images_url}/icon\$icon.gif\"> Posted at \$timestamp</font><hr><font size={fontsize} face=\"{fontface}\" color=\"#000000\">\$message</font></td>
</tr>";
$SKINTITLE["post_list_print"]="";
$SKINREFRESH["post_list_print"]="";
$SKIN["template_email"]="Hi,<p>

You have just changed your email address at <a href=\"\$url\">\$forumtitle</a>. In order for us to ensure it is valid, you have received this email with an appropriate activation link.

Your account will be disabled until you activate it. Please click <a href=\"\$url/register.php?action=activate&userid=\$userid&activateid=\$activateid&password=\$password\">activate your account</a> in order to be allowed to use our forums again.<p>

Thank you!<br>
The administrators at \$forumtitle<br>";
$SKINTITLE["template_email"]="Email address change";
$SKINREFRESH["template_email"]="";
$SKIN["editprofile_emailok"]="<font size=\"{fontsize}\" face=\"{fontface}\">Your profile has successfully been edited.<p><b>Notice</b> Since you changed your email address, you must first re-activate your account before you can use it. We have sent you an email with futher instructions</font><p>

<form action=\"index.php\" method=\"post\">
<center><input type=\"submit\" value=\"Return to the forums\"></input></center>
</form>";
$SKINTITLE["editprofile_emailok"]="";
$SKINREFRESH["editprofile_emailok"]="";
$SKIN["profile_userskin"]="<option value=\"\$theskinid\">\$theskinname</option>";
$SKINTITLE["profile_userskin"]="";
$SKINREFRESH["profile_userskin"]="";
$SKIN["profile_userskin_sel"]="<option value=\"\$theskinid\" selected>\$theskinname</option>";
$SKINTITLE["profile_userskin_sel"]="";
$SKINREFRESH["profile_userskin_sel"]="";
$SKIN["error_canteditlock"]="<font face=\"{fontface}\" size={fontsize}>Sorry, but you can't edit messages in locked threads</font>";
$SKINTITLE["error_canteditlock"]="";
$SKINREFRESH["error_canteditlock"]="";
$SKIN["error_postingdenied"]="<font face=\"{fontface}\" size={fontsize}>We're sorry, but your forum account does not have posting rights. Please consult the forum administrator if you think this is incorrect.</font>";
$SKINTITLE["error_postingdenied"]="Posting rights revoked";
$SKINREFRESH["error_postingdenied"]="";
$SKIN["lastpost_none"]="None";
$SKINTITLE["lastpost_none"]="";
$SKINREFRESH["lastpost_none"]="";
$SKIN["catforum_cat_list"]="<tr bgcolor=\"#2020f0\">
  <td colspan=6><a href=\"index.php?catid=\$catid\" class=\"forumlink\"><font size={fontsize} color=\"#ffffff\" face=\"{fontface}\"><b>\$catname</b></font></a></td>
</tr>";
$SKINTITLE["catforum_cat_list"]="";
$SKINREFRESH["catforum_cat_list"]="";
$SKIN["catforum_forum_list"]="<tr bgcolor=\"#cfd9ff\">
  <td valign=\"center\" align=\"center\">&nbsp;</td>
  <td valign=\"top\"><font face=\"{fontface}\" size=2><a href=\"showforum.php?forumid=\$forumid\" class=\"forumlink\">\$forumname</a></font><br><font face=\"{fontface}\" size=1>\$description</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$nofposts</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$nofthreads</font></td>
  <td valign=\"top\" align=\"center\"><font size=1 face=\"{fontface}\" color=\"#000000\">\$lastpost</font></td>
  <td valign=\"top\" align=\"center\"><font size=2 face=\"{fontface}\" color=\"#000000\">\$mods</font></td>
</tr>";
$SKINTITLE["catforum_forum_list"]="";
$SKINREFRESH["catforum_forum_list"]="";
$SKIN["welcome_catforum"]="<font size={fontsize} face=\"{fontface}\"><a href=\"index.php\">\$forums_title</a> > <b>\$cat_title</b></font><br>
<table width=\"100%\">
  <tr>
    <td width=\"50%\" align=\"left\"><font size=1 face=\"{fontface}\">Hi <b>\$username</b>!<br>\$birthdays</font></td>
    <td width=\"50%\" align=\"right\"><font size=1 face=\"{fontface}\"><b>\$nofthreads</b> threads and <b>\$nofposts</b> posts<br>Number of members: <b>\$nofmembers</b><br>Greetings to our newest member, <a href=\"finger.php?accountid=\$newmemberid\">\$newmembername</a></font></td>
  </tr>
</table>
<!-- forum list -->
<table width=\"100%\" cellspacing=1 cellpadding=3>
<tr bgcolor=\"#2020f0\">
  <td width=\"5%\" align=\"center\">&nbsp;</td>
  <td width=\"25%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Forum Name</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Posts</b></font></td>
  <td width=\"10%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Threads</b></font></td>
  <td width=\"30%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Last post</b></font></td>
  <td width=\"20%\" align=\"center\"><font size={fontsize} color=\"#ffff00\" face=\"{fontface}\"><b>Moderator</b></font></td>
</tr>
<!-- show the actual listing of the forums -->
\$catforumlist
</table><p>
<!-- show the online users -->
<table width=\"100%\" cellspacing=1 cellpadding=1 border=0>
<tr bgcolor=\"#2020f0\">
 <td width=\"100%\" colspan=5><font face=\"{fontface}\" size={fontsize} color=\"#ffff00\">There are currently \$nofonlinemembers member(s) and \$nofonlineguests guest(s) online</td>
</tr>
<tr bgcolor=\"#cfd9ff\">
 <td><font face=\"{fontface}\" size=1>\$onlinemembers</font></td>
</tr>
</table>";
$SKINTITLE["welcome_catforum"]="\$forums_title Powered by ForuMAX";
$SKINREFRESH["welcome_catforum"]="";
$SKINVAR["fontface"]="Verdana,Tahoma,Arial";
$SKINVAR["fontsize"]="2";
$SKINVAR["posticons_per_line"]="10";
$SKINVAR["default_image"]="images/forumh.jpg";
$SKINVAR["images_url"]="images";
$SKINVAR["body_tags"]="";
