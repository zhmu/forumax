<?php
    //
    // install.php
    //
    // This will set up a new ForuMAX installation.
    //
    // TODO: Use the $_REQUEST[] stuff here!
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // $step["action"] = "description" are all forums steps, with the
    // appropriate action and description
    $step["intro"] = "Introduction";
    $step["dbquery"] = "Request database settings";
    $step["accountinfo"] = "Request forum account information";
    $step["buildtables"] = "Create forum tables";

    // $global_values[$value] = $ataction are the global values that will be
    // carried through the entire installer. A value will not be passed if
    // $ataction is equal to $action.
    $global_values["admin_username"] = "accountinfo";
    $global_values["admin_pass1"] = "accountinfo";
    $global_values["admin_pass2"] = "accountinfo";
    $global_values["master_pwd1"] = "accountinfo";
    $global_values["master_pwd2"] = "accountinfo";
    $global_values["dbname"] = "dbquery";
    $global_values["dbhostname"] = "dbquery";
    $global_values["username"] = "dbquery";
    $global_values["password"] = "dbquery";
    $global_values["db_mod"] = "dbquery";

    // $db_module["filename"] = "description" are all database modules
    // available.
    $db_module["mod_mysql.php"] = "MySQL database";

    // $DEFAULT_CONFIG["name"] = "content" are all default configuration
    // options.
    $DEFAULT_CONFIG["forumtitle"] = "Your forums";
    $DEFAULT_CONFIG["ip_log"] = "1";
    $DEFAULT_CONFIG["notify_edit"] = "1";
    $DEFAULT_CONFIG["edit_timestamp_format"] = "%m-%d-%Y %I:%M %p";
    $DEFAULT_CONFIG["joindate_timestamp_format"] = "%e %b %Y";
    $DEFAULT_CONFIG["allow_register"] = "1";
    $DEFAULT_CONFIG["rules"] = "[Insert your bulletin board rules here]";
    $DEFAULT_CONFIG["admin_email"] = $SERVER_ADMIN;
    $DEFAULT_CONFIG["forum_url"] = "http://" . preg_replace ("/\/install.php/", "", $HTTP_HOST . $PHP_SELF);
    $DEFAULT_CONFIG["annc_timestamp_format"] = "%m-%d-%Y";
    $DEFAULT_CONFIG["admin_title"] = "Administrator";
    $DEFAULT_CONFIG["megamod_title"] = "Mega Moderator";
    $DEFAULT_CONFIG["catmod_title"] = "Category Moderator";
    $DEFAULT_CONFIG["mod_title"] = "Moderator";
    $DEFAULT_CONFIG["member_title"] = "Member";
    $DEFAULT_CONFIG["unknown_title"] = "Unregistered Member";
    $DEFAULT_CONFIG["delmem_joindate"] = "N/A";
    $DEFAULT_CONFIG["delmem_postcount"] = "N/A";
    $DEFAULT_CONFIG["delmem_name"] = "?";
    $DEFAULT_CONFIG["nof_icons"] = "13";
    $DEFAULT_CONFIG["post_timestamp_format"] = "%m-%d-%Y %I:%i:%s %p";
    $DEFAULT_CONFIG["online_timeout"] = "900";
    $DEFAULT_CONFIG["intro_type"] = "0";
    $DEFAULT_CONFIG["page_size"] = "20";
    $DEFAULT_CONFIG["page_display_range"] = "3";
    $DEFAULT_CONFIG["allow_pm"] = "1";
    $DEFAULT_CONFIG["pm_per_user"] = "3";
    $DEFAULT_CONFIG["allow_sig"] = "1";
    $DEFAULT_CONFIG["allow_sig_html"] = "1";
    $DEFAULT_CONFIG["allow_sig_max"] = "1";
    $DEFAULT_CONFIG["block_sig_js"] = "1";
    $DEFAULT_CONFIG["block_sig_img"] = "0";
    $DEFAULT_CONFIG["user_allowedit"] = "1";
    $DEFAULT_CONFIG["user_allowdelete"] = "1";
    $DEFAULT_CONFIG["report_defaultid"] = "1";
    $DEFAULT_CONFIG["report_defaultflags"] = "0";
    $DEFAULT_CONFIG["topicspan"] = "7";
    $DEFAULT_CONFIG["timezone"] = "0";
    $DEFAULT_CONFIG["coppa_enabled"] = "0";
    $DEFAULT_CONFIG["censored_words"] = "";
    $DEFAULT_CONFIG["default_topic"] = "(no subject)";
    $DEFAULT_CONFIG["default_memberlist_size"] = "50";
    $DEFAULT_CONFIG["avatar_option"] = "0";
    $DEFAULT_CONFIG["forumax_version"] = "5.0 BETA #4";
    $DEFAULT_CONFIG["birthday_timestamp_format"] = "%e %b %Y";
    $DEFAULT_CONFIG["max_online"] = "0";
    $DEFAULT_CONFIG["forum_pagesize"] = "30";
    $DEFAULT_CONFIG["reply_maxbacklog"] = "20";
    $DEFAULT_CONFIG["coppa_fax_no"] = "[FILL THIS IN!]";
    $DEFAULT_CONFIG["bb_close_reason"] = "Forum is closed due to maintenance";
    $DEFAULT_CONFIG["bb_closed"] = "0";
    $DEFAULT_CONFIG["banned_accountname"] = "";
    $DEFAULT_CONFIG["banned_email"] = "";
    $DEFAULT_CONFIG["banned_ip"] = "";
    $DEFAULT_CONFIG["max_online_timestamp"] = "";

    //
    // AddSkin ($fname, $name, $desc)
    //
    // This will add skin $fname as name $name and description $desc and return
    // the ID of it.
    //
    function
    AddSkin ($fname, $name, $desc) {
	// read the skin file
	$fd = @fopen ($fname, "rt");
	if (!$fd) {
 ?>We could not open the skin file (<code><?php echo $skinsource; ?></code>). This may be a problem with your web server, please consult your system administrator.
<?php
	    cpShowFooter();
	    exit;
	}

	// read everything
	$skindata = "";
	while (!feof ($fd)) {
	    $skindata .= fread ($fd, 65536);
	}

	// close the file
	fclose ($fd);

	// evaluate the skin
	eval ($skindata);

	// okay, do we have a skin version now?
	if ($SKINVERSION == "") {
	    // no. complain
 ?>This does not appear to be a valid ForuMAX skin file.
<?php
	    cpShowFooter();
	    exit;
	}

	// add the skin to the list of available skins
	$query = sprintf ("INSERT INTO skins VALUES (NULL,'%s','%s',0)", $name, $desc);
	$res = db_query ($query);
	$id = db_get_insert_id ($res);

	// build the skin database
	$query = sprintf ("CREATE TABLE skin_%s (name VARCHAR(64) NOT NULL PRIMARY KEY,content TEXT NOT NULL,title VARCHAR(64) NOT NULL,refresh_url VARCHAR(64) NOT NULL,description VARCHAR(128) NOT NULL)", $id);
	db_query ($query);

	// browse all templates
	$tmp = "";
	while (list ($name, $content) = each ($SKIN)) {
	    // feed them into the database
	    $name = addslashes ($name); $content = addslashes ($content);
	    $refresh = addslashes ($SKINREFRESH[$name]);
	    $title = addslashes ($SKINTITLE[$name]);
	    $desc = addslashes ($SKINDESC[$name]);

	    // fix up the content
	    $content = str_replace ("\\[1]", "\\\\1", $content);
	    $content = str_replace ("\\[2]", "\\\\2", $content);

	    // insert them into the database
	    $query = sprintf ("INSERT INTO skin_%s VALUES ('%s','%s','%s','%s','%s')", $id, $name, $content, $title, $refresh, $desc);
	    db_query ($query);
	}

	// also add the variables
	$query = sprintf ("CREATE TABLE skinvars_%s (name VARCHAR(64) NOT NULL PRIMARY KEY,content VARCHAR(128) NOT NULL,description VARCHAR(128) NOT NULL)", $id);
	db_query ($query);

	// browse all variables
	while (list ($name, $content) = each ($SKINVAR)) {
	    // grab the description, too
	    $desc = $SKINVAR_DESC[$name];

	    // feed them into the database
	    $query = sprintf ("INSERT INTO skinvars_%s VALUES ('%s','%s','%s')", $id, $name, $content, $desc);
	    db_query ($query);
	}

	// fix up the values
	$content = preg_replace ("/\{((\S)*)\}/e", '$SKINVAR["\\1"]', $SKIN["stylesheet"]);

	// put them into a file
	$fd = @fopen ("styles/skin" . $id . ".css", "w");
	if (!$fd) {
	    // this failed. complain
 ?>Sorry, but we could not create the style file. This is most likely due to a file permission problem.
<?php
	    cpShowFooter();
	}

	// put the data into it
	fputs ($fd, "/* This file is automatically generated, do not edit */\n");
	fputs ($fd, $content);

	// close the file
	fclose ($fd);

	// paranoia (but it can't hurt :)
	@chmod ("styles/skin" . $id . ".css", 0600);

	return $id;
    }

    //
    // InitPage()
    //
    // This will initialize the HTML page.
    //
    function
    InitPage() {
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><head>
<title><?php global $step; echo "ForuMAX configuration - " . $step[$_REQUEST["action"]]; ?></title>
</head><body text="#ffffff" link="#ffff00" vlink="#ffff00" alink="#ffff00">
<table width="100%" border=1 bgcolor="#4a6ea5" cellspacing=1 cellpadding=1>
<tr>
  <td width="20%" align="center"><?php
    global $step, $action;

    // add all steps
    while (list ($theaction, $thedesc) = each ($step)) {
	// is this step currently selected?
	if ($_REQUEST["action"] == $theaction) {
	    // yup. make it yellow and bold
	    print "<font color=\"#ffff00\"><b>$thedesc</b></font>";
	} else {
	    // no, just print it
	    print "$thedesc";
	}

	// add proper spacing
	print "<p>";
    }
?></td>
  <td width="90%"><center><font size=5><b>ForuMAX Configuration Utility</b></font></center><p>
<?php
    }

    //
    // DonePage()
    //
    // This will deinitialize a HTML page.
    //
    function
    DonePage() {
 ?>
</td></tr>
</table><p>
<center><font color="#000000" size=1>ForuMAX is &copy; 1999-2002 <a href="http://www.internet-factory.nl">The Internet Factory</a></font></center>
</body></html>
<?php
    }

    //
    // BackNextButton($next_form)
    //
    // This will show the 'back' and 'next' buttons, as needed. If $next_form
    // is non-zero, this will also add the <form> tags for the Next button.
    //
    function
    BackNextButton($next_form) {
	global $step, $global_values;

	// fetch the action
	$action = $_REQUEST["action"];

	// grab the number of the action
	$tmp = array_keys ($step);

	// figure out the number
	$no = 0; reset ($step);
	while (list ($theaction) = each ($step)) {
	    if ($theaction == $action) { break; }
	    $no++;
	}

	// grab the previous and next action
	$prev_action = $tmp[$no - 1];
	$next_action = $tmp[$no + 1];

	// build a table
 ?><table width="100%">
<tr><td width="50%" align="left"><?php
	// do we have a previous action?
	if ($prev_action != "") {
	    // yup. add the button
	    print "<input type=\"submit\" value=\"<< Previous\" onClick=\"javascript: history.go (-1); \">";
	}
 ?></td><td width="50%" align="right"><?php
	// do we have a next action?
	if ($next_action != "") {
	    // yup. print it
	    if ($next_form != 0) {
		// add the form
		print "<form action=\"install.php\" method=\"post\">";
	    }
	    print "<input type=\"hidden\" name=\"action\" value=\"$next_action\">";
	    // add all global values
	    while (list ($thevar, $ataction) = each ($global_values)) {
		if ($ataction != $action) {
		    printf ("<input type=\"hidden\" name=\"$thevar\" value=\"%s\">", $GLOBALS[$thevar]);
		}
	    }

	    print "<input type=\"submit\" value=\"Next >>\">";
	}
 ?></td></tr></table>
<?php
    }

    //
    // Intro()
    //
    // This will show an introduction about the installer.
    //
    function
    Intro() {
	InitPage("");
 ?>
Welcome to the ForuMAX/PHP configuration utility! This utility will configure the PHP version of ForuMAX, by creating the appropriate database and setting it up. This configuration utility will guide you through all steps needed to set the forum up. All steps can be seen in the cell to your left.<p>
<?php
	BackNextButton (1);

	DonePage();
    }

    //
    // DBQuery()
    //
    // This will query the user for the database settings.
    //
    function
    DBQuery() {
	InitPage();
 ?>
We need to know what kind of database system you intend to use for interaction with the forum database, as well as the username and password for the account that has access to it. Please fill in the fields below.<p>
<form action="install.php" method="post">
<table width="100%">
<tr><td width="20%">Database module</td><td width="80%"><select name="db_mod"><?php
        // add all modules known
	global $db_module;
        while (list ($themod, $thedesc) = each ($db_module)) {
	    print "<option value=\"$themod\">$thedesc [$themod]</option>";
        }
 ?></select></td></tr>
<tr><td>Database server</td><td><input type="text" name="dbhostname"></td></tr>
<tr><td>Database name</td><td><input type="text" name="dbname"></td></tr>
<tr><td>User name</td><td><input type="text" name="username"></td></tr>
<tr><td>Password</td><td><input type="password" name="password"></td></tr>
</table><p>
<?php
	BackNextButton (0);

	DonePage();
    }

    //
    // AccountInfo()
    //
    // This will request information about the user's desired administrator
    // username and password.
    //
    function
    AccountInfo() {
	InitPage();
 ?>
You will need to pick an administrator username and password. A forum administrator is an account with unrestricted access to every part of the forum. You will always be able to modify the account later, but you will need one in order to access the forum Control Panel and such. Please fill in the fields below:<p>
<form action="install.php" method="post">
<table width="100%">
<tr><td width="20%">User name</td><td width="80%"><input type="text" name="admin_username"></td></tr>
<tr><td>Password</td><td><input type="password" name="admin_pass1"></td></tr>
<tr><td>Repeat password</td><td><input type="password" name="admin_pass2"></td></tr>
</table><p>
You will also need to set a Master Password. This password will override any IP banning checks and such, and will give you extra options in the control panel. It can always be changed later on.<p>
<table width="100%">
<tr><td width="20%">Master Password</td><td width="80%"><input type="password" name="master_pwd1"></td></tr>
<tr><td>Retype Master Password</td><td><input type="password" name="master_pwd2"></td></tr>
</table><p>
The next step will actually create all database entries.<p>
<?php
	BackNextButton (0);

	DonePage();
    }

    //
    // BuildTables()
    //
    // This will build the forum tables in the database.
    //
    function
    BuildTables() {
	global $DEFAULT_CONFIG;

	// fetch all variables
	$admin_username = $_REQUEST["admin_username"];
	$admin_pass1 = $_REQUEST["admin_pass1"];
	$admin_pass2 = $_REQUEST["admin_pass2"];
	$master_pwd1 = $_REQUEST["master_pwd1"];
	$master_pwd2 = $_REQUEST["master_pwd2"];

	// initialize the page
	InitPage();

	// set some fields up for the database module
	$db_mod = $_REQUEST["db_mod"];
	$GLOBALS["db_hostname"] = $_REQUEST["dbhostname"];
	$GLOBALS["db_username"] = $_REQUEST["username"];
	$GLOBALS["db_password"] = $_REQUEST["password"];
	$GLOBALS["db_dbname"] = $_REQUEST["dbname"];

	// are the admin passwords equal?
	if ($admin_pass1 != $admin_pass2) {
	    // no. complain
	    die ("Administrator passwords are not equal");
	}

	// are the master passwords equal?
	if ($master_pwd1 != $master_pwd2) {
	    // no. complain
	    die ("Master passwords are not equal");
	}

	// step 0: load the default skin
	$fp = fopen ("cp/defaultskin.php", "r");
	$defaultskin = "";
	while (!feof ($fp)) {
	    // get the lines
	    $defaultskin .= fread ($fp, 65535);
	}

	// evaluate the default skin
	eval ($defaultskin);

	// step 1: load the db module and kill all current tables
	print "<ul>";
	print "<li>Loading database module <code>$db_mod</code> and deleting all old tables";

	// grab the database module
	require $db_mod;

	db_query ("drop table if exists accounts");
	db_query ("drop table if exists forums");
	db_query ("drop table if exists threads");
	db_query ("drop table if exists posts"); 
	db_query ("drop table if exists skins"); 
	db_query ("drop table if exists skin_1"); 
	db_query ("drop table if exists skinvars_1"); 
	db_query ("drop table if exists config");
	db_query ("drop table if exists announcements");
	db_query ("drop table if exists curusers");
	db_query ("drop table if exists categories");
	db_query ("drop table if exists customfields");
	db_query ("drop table if exists mods");
	db_query ("drop table if exists catmods");
	db_query ("drop table if exists restricted");
	db_query ("drop table if exists notify");
	db_query ("drop table if exists privatemessages");
	db_query ("drop table if exists groups");
	db_query ("drop table if exists groupmembers");
	db_query ("drop table if exists smilies");
	db_query ("drop table if exists cp_access");
	db_query ("drop table if exists avatar");
	db_query ("drop table if exists polls");
	db_query ("drop table if exists poll_options");
	db_query ("drop table if exists poll_votes");
	db_query ("drop table if exists threadsrated");

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 2: create the user accounts
	print "<ul><li>Creating user accounts table</li>";

	// build the accounts table
	$query = sprintf ("create table accounts (id bigint not null primary key auto_increment,accountname varchar(128) not null,password varchar(128) not null,flags bigint not null,nofposts bigint not null,email varchar(128) not null,parent_email varchar(128) not null,parent_password varchar(128) not null,sig text not null,joindate datetime,lastpost datetime,lastmessage bigint not null,skinid bigint not null,birthday date not null,timediff bigint not null,sig_option tinyint not null,avatar bigint not null,activatekey varchar(32) not null,reply_backlog tinyint not null,index(accountname))");
	db_query ($query);

	// create the admin account
	$query = sprintf ("insert into accounts values (null,'%s','%s',1,0,'','','','',now(),now(),0,0,0,0,0,0,'',0)", $admin_username, $admin_pass1);
	db_query ($query); $admin_userid = db_get_insert_id();

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 3: create the forums table
	print "<ul><li>Creating forum information table</li>";

	$query = sprintf ("create table forums (id bigint not null primary key auto_increment,name varchar(128) not null,flags bigint not null,description text not null,nofposts bigint not null,nofthreads bigint not null,lastpost datetime not null,lastposterid bigint not null,lastpostername varchar(64) not null,catno bigint not null,orderno bigint not null,image varchar(128) not null)");
	db_query ($query);

	// insert a general forum here
	$query = sprintf ("insert into forums values (NULL,'General Forum',0,'Default forum created by the installation',1,1,now(),%s,'%s',1,1,'')",$admin_userid, $admin_username);
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 4: create threads table
	print "<ul><li>Creating threads table</li>";

	$query = sprintf ("create table threads (id bigint not null primary key auto_increment,forumid bigint not null,title varchar(128) not null,icon int not null,nofreplies bigint not null,lastdate datetime not null,authorid bigint not null,authorname varchar(64) not null,flags bigint not null,lastposterid bigint not null,lastpostername varchar(64) not null,lockerid bigint not null,destforum bigint not null,nofviews bigint not null,rating float not null,index (forumid))");
	db_query ($query);

	$query = sprintf ("insert into threads values (NULL,1,'Welcome!',1,0,now(),%s,'%s',0,%s,'%s',0,0,0,0)",$admin_userid,$admin_username,$admin_userid,$admin_username);
	db_query ($query);
	
	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 5: create posts table
	print "<ul><li>Creating posts table</li>";
	
	$query = sprintf ("create table posts (id bigint not null primary key auto_increment,authorid bigint not null,authorname varchar(64) not null,forumid bigint not null,threadid bigint not null,timestamp datetime not null,post mediumtext not null,edittime datetime not null,editid bigint not null,icon int not null,ipaddr varchar(32) not null,flags bigint not null,index(threadid),index(forumid))");
	db_query ($query);

	$query = sprintf ("insert into posts values (NULL,1,%s,'%s',1,now(),'Welcome to the forums! This is a test message to show you the forum system works. You can safely delete this thread if you like\n\nThe ForuMAX Team',now(),0,1,'%s',0)",$admin_userid, $admin_username, getenv ("REMOTE_ADDR"));
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 5: create skins table
	print "<ul><li>Creating skins table</li>";

	$query = sprintf ("create table skins (id bigint not null primary key auto_increment,name varchar(128) not null,description varchar(128) not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 6: create announcement table
	print "<ul><li>Creating announcements table</li>";

	$query = sprintf ("create table announcements (id bigint not null primary key auto_increment,title varchar(64) not null,authorid bigint not null,startdate datetime not null,enddate datetime not null,forumid bigint not null,content mediumtext not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 7: create configuration table
	print "<ul><li>Creating configuration table</li>";

	$query = sprintf ("create table config (name varchar(128) not null primary key,content text not null)");
	db_query ($query);

	// insert all entries
	while (list ($name, $content) = each ($DEFAULT_CONFIG)) {
	    // insert the entry
	    $query = sprintf ("insert into config values ('%s','%s')",$name,$content);
	    db_query ($query);
	}

	// add the master password
	$query = sprintf ("insert into config values ('master_password','%s')",md5 ($master_pwd1));
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 8: create curusers table
	print "<ul><li>Creating table of currently logged-in users</li>";

	$query = sprintf ("create table curusers (id bigint not null primary key auto_increment,accountid bigint not null,timestamp datetime not null,ipaddr varchar(32) not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 9: create category table
	print "<ul><li>Creating table of categories</li>";

	$query = sprintf ("create table categories (id bigint not null primary key auto_increment,name varchar(128) not null,description text not null,orderno bigint not null)");
	db_query ($query);

	// insert the generic one
	$query = sprintf ("insert into categories values (NULL,'Forums','Main forums',1)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 10: create custom fields table
	print "<ul><li>Creating table for custom fields</li>";
	$query = sprintf ("create table customfields (id bigint not null primary key auto_increment,name varchar(64) not null,type bigint not null,visible int not null,perms int not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 12: create moderators table
	print "<ul><li>Creating moderation table</li>";
	$query = sprintf ("create table mods (id bigint not null primary key auto_increment,forumid bigint not null,userid bigint not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 13: create restricted users table
	print "<ul><li>Creating restricted users table</li>";
	$query = sprintf ("create table restricted (id bigint not null primary key auto_increment,forumid bigint not null,userid bigint not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 14: new post notify table
	print "<ul><li>Creating notification table</li>";

	$query = sprintf ("create table notify (id bigint not null primary key auto_increment,forumid bigint not null,userid bigint not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 15: create category moderators table
	print "<ul><li>Creating category moderation table</li>";
	$query = sprintf ("create table catmods (id bigint not null primary key auto_increment,forumid bigint not null,userid bigint not null,flags bigint not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 16: create private messaging table
	print "<ul><li>Creating private messaging table</li>";

	$query = sprintf ("create table privatemessages (id bigint not null primary key auto_increment,userid bigint not null,senderid bigint not null,subject varchar(128) not null,message mediumtext not null,timestamp datetime not null,flags bigint not null,senderemail varchar(128) not null,index(userid))");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 17: create group tables
	print "<ul><li>Creating group tables</li>";

        $query = sprintf ("create table groups (id bigint not null primary key auto_increment,name varchar(64) not null,description varchar(128) not null)");
        db_query ($query);

        $query = sprintf ("create table groupmembers (id bigint not null primary key auto_increment,groupid bigint not null,userid bigint not null,index(groupid),index(userid))");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 18: create smilies table
	print "<ul>Creating smilies table</li>";

	// create the appropriate table
	$query = sprintf ("create table smilies (id bigint not null primary key auto_increment,smilie varchar(32) not null,image varchar(128) not null)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 19: create avatar table
	print "<ul>Creating avatars table</li>";

	// create the appropriate table
        $query = sprintf ("create table avatar (id bigint not null primary key auto_increment,flags bigint not null,userid bigint not null,index(userid))");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 20: set up control panel access
	print "<ul><li>Setting up control panel access</li>";
        $query = sprintf ("create table cp_access (id bigint not null primary key auto_increment,cp_option bigint not null,access bigint not null)");
	db_query ($query);

	// set up the access levels
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,1,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,2,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,3,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,4,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,5,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,6,1)");
	db_query ($query);
 	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,7,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,8,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,9,1)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,10,0)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,11,0)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,12,7)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,13,7)");
	db_query ($query);
	$query = sprintf ("INSERT INTO cp_access VALUES (NULL,14,0)");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 21: creating poll tables
	print "<ul><li>Creating poll tables</li>";
	$query = sprintf ("create table polls (id bigint not null primary key auto_increment,threadid bigint not null,question varchar(128) not null,flags bigint not null,totalvotes bigint not null,index(threadid))");
	db_query ($query);
	$query = sprintf ("create table poll_options (id bigint not null primary key auto_increment,pollid bigint not null,optiontext varchar(128) not null,nofvotes bigint not null,index(pollid))");
	db_query ($query);
	$query = sprintf ("create table poll_votes (id bigint not null primary key auto_increment,pollid bigint not null,accountid bigint not null,index(pollid))");
	db_query ($query);

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 22: creating thread rating tables
	print "<ul><li>Creating thread rating tables</li>";

	$query = sprintf ("create table threadsrated (id bigint not null primary key auto_increment,threadid bigint not null,accountid bigint not null,rating float not null,index(threadid),index(accountid))");
	db_query ($query); 

	print " <b><i>Done</i></b></li>";
	print "</ul>";

	// step 23: install default skin
	print "<ul><li>Installing default skin</li>";

 	$id = AddSkin ("cp/defaultskin.php", "Default ForuMAX " . FORUMAX_VERSION . " skin", "New skin created by the installation utility");
        $query = sprintf ("UPDATE skins SET flags=1 WHERE id=%s", $id);
	db_query ($query);

	print " <b><i>Done</i></b></li></ul>";

	// step 24: create backup directories
	print "<ul><li>Create backup directories</li>";
	if (!is_dir ("backup")) {
	    mkdir ("backup", 0700);
	}
	if (!is_dir ("backup/cp")) {
	    mkdir ("backup/cp", 0700);
	}
	print " <b><i>Done</i></b></li></ul>";

	// step 25: write config file
	print "<ul><li>Writing configuration file <code>dbconfig.php</code></li>";
	$fp = fopen ("dbconfig.php", "w");

	// got an error?
	if (!$fp) {
	    // yup. complain
	    die ("Cannot create config file <code>dbconfig.php</code>");
	}

	// write all our data
	fwrite ($fp, '<?php
    //
    // dbconfig.php
    // (c) 2000 Rink Springer, www.forumax.com
    //
    // This will hold the generic database configuration.
    //

    // $GLOBALS["db_mod"] is the database module in use
    $GLOBALS["db_mod"] = "' . $GLOBALS["db_mod"] . '";

    // $GLOBALS["db_hostname"] is the database server host name or IP address
    $GLOBALS["db_hostname"] = "' . $GLOBALS["db_hostname"] . '";

    // $GLOBALS["db_dbname"] is the database name
    $GLOBALS["db_dbname"] = "' . $GLOBALS["db_dbname"] . '";

    // $GLOBALS["db_username"] is the username to connect to the database
    // server.
    $GLOBALS["db_username"] = "' . $GLOBALS["db_username"] . '";

    // $GLOBALS["db_password"] is the password to connect to the database
    // server.
    $GLOBALS["db_password"] = "' . $GLOBALS["db_password"] . '";

 ?>');
	
	fclose ($fp);

	// hide the file contents from prying eyes
	chmod ("dbconfig.php", 0600);

	print " <b><i>Done</i></b></li>";
	print "</ul>";
 ?>Congratulations, you have successfully set up your forum system. You are now able to <a href="index.php">browse the forums</a> or <a href="cp/">use the control panel</a>. Have fun!<p>
The ForuMAX Staff
<?php

	DonePage();
    }

    // is the forum already set up?
    if (@is_file ("dbconfig.php")) {
	// yes. complain
 ?>This forum is already configured. If you prefer to re-configure it, please delete the <code>dbconfig.php</code> file.
<?php
	exit;
    }

    // fetch the action
    $action = $_REQUEST["action"];

    if (($action == "") or ($action == "intro")) {
	// introduction
	$_REQUEST["action"] = "intro";
	Intro();
	exit;
    }

    if ($action == "dbquery") {
	// query the database settings
	DBQuery();
	exit;
    }

    if ($action == "accountinfo") {
	// query for the administrator's account information
	AccountInfo();
	exit;
    }

    if ($action == "buildtables") {
	// build the forum tables
	BuildTables();
	exit;
    }
 ?>
