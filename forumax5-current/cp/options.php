<?php 
    //
    // options.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the editing of options.
    //

    // we need our library, too
    require "lib.php";

    //
    // Overview()
    //
    // This will show the forum options.
    //
    function
    Overview() {
	global $CONFIG;

	// build the page
	cpShowHeader("Forum Options", "Edit global options");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="submit">
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab3"><td colspan=2 align="center">General Options</td></tr>
  <tr class="tab2"><td width="50%"><b>Forum closed</b><br>You can temponary shut down the forum for maintance if you like. No one can use the forums until someone enables them again.</td><td width="50%"><input type="radio" name="bb_closed" value="0"<?php if ($CONFIG["bb_closed"] == 0) { echo " checked"; } ?>>Forum is opened</input><br><input type="radio" name="bb_closed" value="1"<?php if ($CONFIG["bb_closed"] == 1) { echo " checked"; } ?>>Forum is closed</input></td></tr>
  <tr class="tab2"><td width="50%" valign="top"><b>Reason for board closed</b><br>When the forum is closed, this message will be displayed so users know why the forums are closed</td><td width="50%"><textarea rows=10 cols=40 name="bb_close_reason"><?php echo htmlspecialchars ($CONFIG["bb_close_reason"]); ?></textarea></td></tr>

  <tr class="tab2"><td width="50%"><b>Forums title</b><br>This will be the generic forum title. All pages will use this as a title, appening a sub-title after it (with a dash between them)</td><td width="50%"><input type="text" name="forum_title" value="<?php echo $CONFIG["forumtitle"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>IP logging</b><br>The forum is capable of logging IP addresses. You may restrict viewing by anyone, moderators or admins, or completely turn logging off.</td><td width="50%"><input type="radio" name="ip_log" value="0"<?php if ($CONFIG["ip_log"] == 0) { echo " checked"; } ?>>Do not log IP's</input><br><input type="radio" name="ip_log" value="1"<?php if ($CONFIG["ip_log"] == 1) { echo " checked"; } ?>>Log IP's, viewable by admins</input><br><input type="radio" name="ip_log" value="2"<?php if ($CONFIG["ip_log"] == 2) { echo " checked"; } ?>>Log IP's, viewable by moderators and admins</input><br><input type="radio" name="ip_log" value="3"<?php if ($CONFIG["ip_log"] == 3) { echo " checked"; } ?>>Log IP's, viewable by anyone</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Add notication when message has been edited</b><br>If this is enabled, the forum will add a <code>Edited at <i>timestamp</i> by <i>username</i></code> line whenever a message has been edited</td><td><input type="radio" name="notify_edit" value="0"<?php if ($CONFIG["notify_edit"] == 0) { echo " checked"; } ?>>Do not add timestamp when message has been edited</input><br><input type="radio" name="notify_edit" value="1"<?php if ($CONFIG["notify_edit"] == 1) { echo " checked"; } ?>>Add timestamp when message has been edited</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Edit timestamp format</b><br>This is the timestamp format for the date that will be appeneded to an edited message. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="edit_timestamp_format" value="<?php echo $CONFIG["edit_timestamp_format"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Joindate timestamp format</b><br>This is the timestamp format for the date that will be appeneded to an edited message. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="joindate_timestamp_format" value="<?php echo $CONFIG["joindate_timestamp_format"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Birthdate timestamp format</b><br>This is the timestamp format for an user's birthdate that will show up when looking up the user information. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="birthdate_timestamp_format" value="<?php echo $CONFIG["birthdate_timestamp_format"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Announcement timestamp format</b><br>This is the timestamp format that will be used for all announcement timestamps. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="annc_timestamp_format" value="<?php echo $CONFIG["annc_timestamp_format"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Forum post timestamp format</b><br>This is the timestamp format that will be used for all post timestamps. Check out <a href="http://www.php.net/manual/function.strftime.php" target="_blank">this</a> for all allowed formats.</td><td width="50%"><input type="text" name="post_timestamp_format" value="<?php echo $CONFIG["post_timestamp_format"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Allow registration</b><br>The forum is capable of allowing users to register an account on the fly. You can turn this option on or off.</td><td width="50%"><input type="radio" name="allow_register" value="0"<?php if ($CONFIG["allow_register"] == 0) { echo " checked"; } ?>>Do not allow registrations</input><br><input type="radio" name="allow_register" value="1"<?php if ($CONFIG["allow_register"] == 1) { echo " checked"; } ?>>Allow registrations</input></td></tr>
  <tr class="tab2"><td width="50%" valign="top"><b>Bulletin board rules</b><br>This is the policy all users have to agree with before an account will be granted</td><td width="50%"><textarea rows=10 cols=40 name="bb_rules"><?php echo htmlspecialchars ($CONFIG["rules"]); ?></textarea></td></tr>
  <tr class="tab2"><td width="50%" valign="top"><b>Censored Words</b><br>These are all the words which have been censored. They will be replaced by *'s (for example, dog becomes ***). Words between {} will only censor the word itself and not when it's surrounded with other charachters. Seperate each word by a space</td><td width="50%"><textarea rows=10 cols=40 name="censored_words"><?php echo htmlspecialchars ($CONFIG["censored_words"]); ?></textarea></td></tr>
  <tr class="tab2"><td width="50%" valign="top"><b>Banned email addresses</b><br>These are the email addresses which you chose to disallow. They will not be allowed to be used for any account. You may enter a complete address (like user@forumax.com) or a domain name (forumax.com). The former will disallow the exact match, the latter will disallow the entire domain. Seperate each address by a space</td><td width="50%"><textarea rows=10 cols=40 name="banned_email"><?php echo htmlspecialchars ($CONFIG["banned_email"]); ?></textarea></td></tr>
  <tr class="tab2"><td width="50%" valign="top"><b>Banned IP addresses</b><br>These are the IP addresses which you chose to disallow. They will not be allowed to use the forum. You may enter a complete address (like 1.2.3.4) or a partial address (Like 1., 1.2. and 1.2.3.). The former will disallow the exact match, the latter will disallow the entire class. A banned IP address will still be able to access the control panel. Seperate each address by a space</td><td width="50%"><textarea rows=10 cols=40 name="banned_ip"><?php echo htmlspecialchars ($CONFIG["banned_ip"]); ?></textarea></td></tr>
  <tr class="tab2"><td width="50%" valign="top"><b>Banned account names</b><br>These are the account names which you chose to disallow. If you put a name between {}, the exact name will be disallowed (If you enter {dog}, account name dog will be disallowed but not dogma or adoggie). Otherwise an account name with word in it will be disallowed (for example, dog will disallow dog, dogma and adoggie). Seperate each account name by a space</td><td width="50%"><textarea rows=10 cols=40 name="banned_accountname"><?php echo htmlspecialchars ($CONFIG["banned_accountname"]); ?></textarea></td></tr>
  <tr class="tab2"><td width="50%"><b>Administrator email address</b><br>This email address will be used as the original of any forum email</td><td width="50%"><input type="text" name="admin_email" value="<?php echo $CONFIG["admin_email"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Forum URL</b><br>This is the URL to the forums. This should only be the directory, and not include a trailing slash</td><td width="50%"><input type="text" name="forum_url" value="<?php echo $CONFIG["forum_url"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Number of icons</b><br>The forum is able to show icons for a post or reply. Please select the number of icons you have in your images directory (in the form of icon<i>no</i>.gif)</td><td width="50%"><input type="text" name="nof_icons" value="<?php echo $CONFIG["nof_icons"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Online status timeout</b><br>This is the number of seconds you have to be inactive before your name will be deleted from the online users list</td><td width="50%"><input type="text" name="online_timeout" value="<?php echo $CONFIG["online_timeout"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Introduction type</b><br>This is the first screen an user will see.</td><td width="50%"><input type="radio" name="intro_type" value="0"<?php if ($CONFIG["intro_type"] == 0) { echo " checked"; }; ?>>Forum list</input><br><input type="radio" name="intro_type" value="1"<?php if ($CONFIG["intro_type"] == 1) { echo " checked"; }; ?>>Category list</input><br><input type="radio" name="intro_type" value="2"<?php if ($CONFIG["intro_type"] == 2) { echo " checked"; }; ?>>Forums with category headings</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Page size</b><br>If the number of thread replies exceed this number, the thread will be split in pages of this size</td><td width="50%"><input type="text" name="page_size" value="<?php echo $CONFIG["page_size"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Forum Page size</b><br>If the number of threads exceed this number in a single forum, the threads will be split in pages of this size</td><td width="50%"><input type="text" name="forum_pagesize" value="<?php echo $CONFIG["forum_pagesize"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Page display range</b><br>If you have more pages than 2 times this number, the forum will show this amount of numbers at both sides, separated by dots). This must be an odd number.</td><td width="50%"><input type="text" name="page_display_range" value="<?php echo $CONFIG["page_display_range"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Allow users to edit their own posts?</b><br>If this is enabled, users will be able to edit their own posts. Moderators and up will always be able to edit posts not made by them (proven they are listed as moderator)</td><td width="50%"><input type="radio" name="user_allowedit" value="0"<?php if ($CONFIG["user_allowedit"] == 0) { echo " checked"; }; ?>>Do not allow user editing of own posts</input><br><input type="radio" name="user_allowedit" value="1"<?php if ($CONFIG["user_allowedit"] == 1) { echo " checked"; }; ?>>Allow user editing of own posts</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Allow users to delete their own posts and threads?</b><br>If this is enabled, users will be able to delete their own posts/threads. Moderators and up will always be able to delete posts and threads not made by them (proven they are listed as moderator)</td><td width="50%"><input type="radio" name="user_allowdelete" value="0"<?php if ($CONFIG["user_allowdelete"] == 0) { echo " checked"; }; ?>>Do not allow user deletion of own posts and threads</input><br><input type="radio" name="user_allowdelete" value="1"<?php if ($CONFIG["user_allowdelete"] == 1) { echo " checked"; }; ?>>Allow user deletion of own posts and threads</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Default user to report threads to</b><br>If an user reports a thread in a forum which has absolutely no (category) moderators, this user will receive the email. If you preceed this entry with a @, the first member of this group will be notified.</td><td width="50%"><input type="text" name="report_defaultuser" value="<?php echo BuildUserGroupString ($CONFIG["report_defaultid"], $CONFIG["report_defaultflags"]); ?>"></td></tr>
 <tr class="tab2"><td><b>Default new topic timespan</b><br>This will indicate how how old topics can be until we display them upon entering a forum.</td><td><input type="radio" name="topicspan" value="1"<?php if ($CONFIG["topicspan"] == 1) { echo " checked"; }; ?>>last day</input><br><input type="radio" name="topicspan" value="2"<?php if ($CONFIG["topicspan"] == 2) { echo " checked"; }; ?>>last two days</input><br><input type="radio" name="topicspan" value="7"<?php if ($CONFIG["topicspan"] == 7) { echo " checked"; }; ?>>last week</input><br><input type="radio" name="topicspan" value="31"<?php if ($CONFIG["topicspan"] == 31) { echo " checked"; }; ?>>last month</input><br><input type="radio" name="topicspan" value="365"<?php if ($CONFIG["topicspan"] == 365) { echo " checked"; }; ?>>last year</input><br><input type="radio" name="topicspan" value="0"<?php if ($CONFIG["topicspan"] == 0) { echo " checked"; }; ?>>show all topics</input></td></tr>
  <tr class="tab2"><td><b>Server Timezone</b><br>This is the server's timezone. This will be used when calculating time to user timezones</td><td>GMT + <input type="text" name="timezone" value="<?php echo (int)($CONFIG["timezone"] / 3600) . ":" . abs ($CONFIG["timezone"] % 3600) / 60; ?>"></td></tr>
  <tr class="tab2"><td><b>COPPA compliance</b><br>COPPA is a law in the USA which requires childeren under the age of 13 to have parental permission before they can sign up on interactive sites. If this is enabled, you need to have a country field as well. If the user who is registering is below 13 and lives in the USA, he/she will be asked for a parents email address. This parent will receive the <code>email_coppa</code> email, which should contain additional instructions</td><td><input type="radio" name="coppa_compliance" value="0"<?php if ($CONFIG["coppa_enabled"] == 0) { echo " checked"; }; ?>>Do not enable COPPA compliance</input><br><input type="radio" name="coppa_compliance" value="1"<?php if ($CONFIG["coppa_enabled"] == 1) { echo " checked"; }; ?>>Enable COPPA compliance (requires a Country custom field)</input></td></tr>
  <tr class="tab2"><td width="50%"><b>COPPA Fax Number</b><br>This is the fax number to which parents should send their approval</td><td width="50%"><input type="text" name="coppa_fax_no" value="<?php echo $CONFIG["coppa_fax_no"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Default memberlist size</b><br>This is the default number of accounts listed per a page in the memberlist</td><td width="50%"><input type="text" name="default_memberlist_size" value="<?php echo $CONFIG["default_memberlist_size"]; ?>"></td></tr>
  <tr class="tab2"><td><b>Avatar options</b><br>Avatars are cute images who reside just below an user's username. You can set up how you want avatars in your forum</td><td><input type="radio" name="avatar_option" value="0"<?php if ($CONFIG["avatar_option"] == 0) { echo " checked"; }; ?>>No avatars</input><br><input type="radio" type="radio" name="avatar_option" value="1"<?php if ($CONFIG["avatar_option"] == 1) { echo " checked"; }; ?>>Allow avatars, selectable from a list compiled by the admin</input><br><input type="radio" name="avatar_option" value="2"<?php if ($CONFIG["avatar_option"] == 2) { echo " checked"; }; ?>>Allow avatars, uploadable by users</input><br><input type="radio" name="avatar_option" value="3"<?php if ($CONFIG["avatar_option"] == 3) { echo " checked"; }; ?>>Allow avatars, uploadable by users or selectable from a list</input></td></tr>
</table><p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab3"><td colspan=2 align="center">Membership Options</td></tr>
  <tr class="tab2"><td width="50%"><b>Administrator status</b><br>This is the status an administrator will get by default</td><td width="50%"><input type="text" name="admin_title" value="<?php echo $CONFIG["admin_title"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Mega Moderator status</b><br>This is the status a mega moderator will get by default</td><td width="50%"><input type="text" name="megamod_title" value="<?php echo $CONFIG["megamod_title"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Category Moderator status</b><br>This is the status a category moderator will get by default</td><td width="50%"><input type="text" name="catmod_title" value="<?php echo $CONFIG["catmod_title"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Moderator status</b><br>This is the status a moderator will get by default</td><td width="50%"><input type="text" name="mod_title" value="<?php echo $CONFIG["mod_title"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Member status</b><br>This is the status an ordinary member will get by default</td><td width="50%"><input type="text" name="member_title" value="<?php echo $CONFIG["member_title"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Delete member name</b><br>This will be displayed as a username for any deleted member</td><td width="50%"><input type="text" name="delmem_name" value="<?php echo $CONFIG["delmem_name"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Unregistered/Deleted member status</b><br>This is the status anyone not in the database will get</td><td width="50%"><input type="text" name="unknown_title" value="<?php echo $CONFIG["unknown_title"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Deleted member post count</b><br>This will be displayed as a post count for any deleted member</td><td width="50%"><input type="text" name="delmem_postcount" value="<?php echo $CONFIG["delmem_postcount"]; ?>"></td></tr>
  <tr class="tab2"><td width="50%"><b>Deleted member join date</b><br>This will be displayed as a join date for any deleted member</td><td width="50%"><input type="text" name="delmem_joindate" value="<?php echo $CONFIG["delmem_joindate"]; ?>"></td></tr>
</table><p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab3"><td colspan=2 align="center">Private Messaging Options</td></tr>
  <tr class="tab2"><td width="50%"><b>Allow private messaging</b><br>Private Messaging allows users to send messages to other users. You can enable or disable this feature</td><td width="50%"><input type="radio" name="allow_pm" value="0"<?php if ($CONFIG["allow_pm"] == 0) { echo " checked"; } ?>>Do not allow private messages</input><br><input type="radio" name="allow_pm" value="1"<?php if ($CONFIG["allow_pm"] == 1) { echo " checked"; } ?>>Allow private messages</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Maximal unread messages per user</b><br>This is the number of messages one user can send to another user. If you try to send a message and the user has this number of unread messages from you, the send request will be denied</td><td width="50%"><input type="text" name="pm_per_user" value="<?php echo $CONFIG["pm_per_user"]; ?>"></td></tr>
</table><p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
  <tr class="tab3"><td colspan=2 align="center">Signature Options</td></tr>
  <tr class="tab2"><td width="50%"><b>Allow signatures</b><br>A signature is an user-customizable piece of text that can be appended to posts made. If this option is enabled, users will be able to edit their own signature. If this is disabled, signatures will never show up in the forums.</td><td width="50%"><input type="radio" name="allow_sig" value="0"<?php if ($CONFIG["allow_sig"] == 0) { echo " checked"; } ?>>Do not allow signatures</input><br><input type="radio" name="allow_sig" value="1"<?php if ($CONFIG["allow_sig"] == 1) { echo " checked"; } ?>>Allow signatures</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Allow MaX code in signatures</b><br>You may chose to allow MaX codes in signatures. If this is disabled, MaX codes in signatures will show up as normal text.</td><td width="50%"><input type="radio" name="allow_sig_max" value="0"<?php if ($CONFIG["allow_sig_max"] == 0) { echo " checked"; } ?>>Do not allow MaX code in signatures</input><br><input type="radio" name="allow_sig_max" value="1"<?php if ($CONFIG["allow_sig_max"] == 1) { echo " checked"; } ?>>Allow MaX code in signatures</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Allow HTML in signatures</b><br>You may chose to allow HTML in signatures. If this is disabled, HTML in signatures will show up as normal text.</td><td width="50%"><input type="radio" name="allow_sig_html" value="0"<?php if ($CONFIG["allow_sig_html"] == 0) { echo " checked"; } ?>>Do not allow HTML in signatures</input><br><input type="radio" name="allow_sig_html" value="1"<?php if ($CONFIG["allow_sig_html"] == 1) { echo " checked"; } ?>>Allow HTML in signatures</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Block images from signatures</b><br>You may chose to disable images in signatures. If this is enabled, signatures will not be permitted to allow images.</td><td width="50%"><input type="radio" name="block_sig_img" value="0"<?php if ($CONFIG["block_sig_img"] == 0) { echo " checked"; } ?>>Allow images in signatures</input><br><input type="radio" name="block_sig_img" value="1"<?php if ($CONFIG["block_sig_img"] == 1) { echo " checked"; } ?>>Block images from signatures</input></td></tr>
  <tr class="tab2"><td width="50%"><b>Block JavaScript and bad HTML tags from signatures</b><br>You may chose to harmful code in signatures. If this is enabled, signatures will not be permitted to contain javascript and severnal HTML tags.</td><td width="50%"><input type="radio" name="block_sig_js" value="0"<?php if ($CONFIG["block_sig_js"] == 0) { echo " checked"; } ?>>Allow JavaScript and evil HTML in signatures</input><br><input type="radio" name="block_sig_js" value="1"<?php if ($CONFIG["block_sig_js"] == 1) { echo " checked"; } ?>>Block JavaScript and evil HTML from signatures</input></td></tr>
</table><p>
<center><center><input type="submit" value="Submit Changes"></center>
</form>
<?php
	cpShowFooter();
    }

    //
    // SetValue ($name, $content)
    //
    // This will change option $name to $content.
    //
    function
    SetValue ($name, $content) {
	// build the query
	$query = sprintf ("UPDATE config SET content='%s' WHERE name='%s'", $content, $name);
	db_query ($query);
    }

    //
    // Submit()
    //
    // This will actually activate the new options.
    //
    function
    Submit() {
	// fetch the default user to report to
	$report_defaultuser = $_REQUEST["report_defaultuser"];

        // figure out the id and flags of the new user we report to by default
	// does the field start with a @?
	$flags = 0;
        if (preg_match ("/^\@/", $report_defaultuser)) {
	    // yes. destroy the @ and grab the group id
            $report_defaultuser = preg_replace ("/^\@/", "", $report_defaultuser);
            $objectid = GetGroupID ($report_defaultuser);
            $flags = FLAG_USERLIST_GROUP;

            // did this work?
            if ($objectid == "") {
		CPShowHeader();
		print "Group <b>" . $report_defaultuser . "</b> does not exist";
                CPShowFooter();
                exit;
            }
	} else {
            // no. grab the new user id
            $objectid = GetMemberID ($report_defaultuser); $flags = 0;

            // did this work?
            if ($objectid == "") {
		CPShowHeader();
		print "User <b>" . $report_defaultuser . "</b> does not exist";
                CPShowFooter();
                exit;
            }
	}

	// make sure $page_display_range is odd
	if (($_REQUEST["page_display_range"] % 2) == 0) {
	    $_REQUEST["page_display_range"]++;
	}

	// insert the new stuff into the database
	SetValue ("report_defaultid", $objectid);
	SetValue ("report_defaultflags", $flags);
	SetValue ("bb_closed", $_REQUEST["bb_closed"]);
	SetValue ("bb_close_reason", $_REQUEST["bb_close_reason"]);
	SetValue ("forumtitle", $_REQUEST["forum_title"]);
	SetValue ("ip_log", $_REQUEST["ip_log"]);
	SetValue ("notify_edit", $_REQUEST["notify_edit"]);
	SetValue ("edit_timestamp_format", $_REQUEST["edit_timestamp_format"]);
	SetValue ("joindate_timestamp_format", $_REQUEST["joindate_timestamp_format"]);
	SetValue ("birthdate_timestamp_format", $_REQUEST["birthdate_timestamp_format"]);
	SetValue ("allow_register", $_REQUEST["allow_register"]);
	SetValue ("rules", $_REQUEST["bb_rules"]);
	SetValue ("admin_email", $_REQUEST["admin_email"]);
	SetValue ("forum_url", $_REQUEST["forum_url"]);
	SetValue ("nof_icons", $_REQUEST["nof_icons"]);
	SetValue ("annc_timestamp_format", $_REQUEST["annc_timestamp_format"]);
	SetValue ("post_timestamp_format", $_REQUEST["post_timestamp_format"]);

	SetValue ("admin_title", $_REQUEST["admin_title"]);
	SetValue ("megamod_title", $_REQUEST["megamod_title"]);
	SetValue ("catmod_title", $_REQUEST["catmod_title"]);
	SetValue ("mod_title", $_REQUEST["mod_title"]);
	SetValue ("member_title", $_REQUEST["member_title"]);
	SetValue ("unknown_title", $_REQUEST["unknown_title"]);
	SetValue ("delmem_joindate", $_REQUEST["delmem_joindate"]);
	SetValue ("delmem_postcount", $_REQUEST["delmem_postcount"]);
	SetValue ("delmem_name", $_REQUEST["delmem_name"]);
	SetValue ("online_timeout", $_REQUEST["online_timeout"]);
	SetValue ("intro_type", $_REQUEST["intro_type"]);
	SetValue ("page_size", $_REQUEST["page_size"]);
	SetValue ("forum_pagesize", $_REQUEST["forum_pagesize"]);
	SetValue ("page_display_range", $_REQUEST["page_display_range"]);
	SetValue ("allow_pm", $_REQUEST["allow_pm"]);
	SetValue ("pm_per_user", $_REQUEST["pm_per_user"]);
	SetValue ("allow_sig", $_REQUEST["allow_sig"]);
	SetValue ("allow_sig_max", $_REQUEST["allow_sig_max"]);
	SetValue ("allow_sig_html", $_REQUEST["allow_sig_html"]);
	SetValue ("block_sig_js", $_REQUEST["block_sig_js"]);
	SetValue ("block_sig_img", $_REQUEST["block_sig_img"]);
	SetValue ("user_allowedit", $_REQUEST["user_allowedit"]);
	SetValue ("user_allowdelete", $_REQUEST["user_allowdelete"]);
	SetValue ("topicspan", $_REQUEST["topicspan"]);
	list ($hour, $min) = explode (":", $_REQUEST["timezone"]);
	$timezone = (abs ($hour) * 3600) + (abs ($min) * 60);
	if ($hour < 0) { $timezone = -$timezone; };
	SetValue ("timezone", $timezone);
	SetValue ("coppa_enabled", $_REQUEST["coppa_compliance"]);
	SetValue ("coppa_fax_no", $_REQUEST["coppa_fax_no"]);
	SetValue ("default_topic", $_REQUEST["default_topic"]);
	SetValue ("censored_words", $_REQUEST["censored_words"]);
	SetValue ("default_memberlist_size", $_REQUEST["default_memberlist_size"]);
	SetValue ("avatar_option", $_REQUEST["avatar_option"]);
	SetValue ("banned_email", $_REQUEST["banned_email"]);
	SetValue ("banned_ip", $_REQUEST["banned_ip"]);
	SetValue ("banned_accountname", $_REQUEST["banned_accountname"]);

	// show the 'yay' page
	cpShowHeader("Forum Options", "Edit global options");
 ?>The new options have successfully been activated.<p>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_OPTIONS);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // is an action given?
    if ($action == "") {
	// no. view the options
	Overview();
    } elseif ($action == "submit") {
	// we need to submit the forum options. do it
	Submit();
    }
 ?>
