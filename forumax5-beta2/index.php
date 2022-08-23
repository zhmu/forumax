<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    //
    // BuildMods ($res)
    //
    // This will build a list of moderators, based on result identified $res.
    //
    function
    BuildMods ($res) {
	// grab the moderator templates
	$mod_usertemplate = AddSlashes (GetSkinTemplate ("list_usermod"));
	$mod_grouptemplate = AddSlashes (GetSkinTemplate ("list_groupmod"));
	$mod_splitemplate = AddSlashes (GetSkinTemplate ("list_splitmod"));

	// process them all
	$modlist = "";
	while ($tmp = db_fetch_results ($res)) {
	    // build the moderator list. is this a group?
	    $add = ""; $objectid = $tmp[0];
	    if ($tmp[1] & FLAG_USERLIST_GROUP) {
		// yes. look up the groupname 
		$query = sprintf ("select name from groups where id=%s", $tmp[0]);
		$res3 = db_query ($query); $result2 = db_fetch_results ($res3);

		// did we have any results?
		if (db_nof_results ($res3) != 0) {
		    // yes. add the group name
	            $objectname = $result2[0]; 
                    eval ("\$add = stripslashes (\"" . $mod_grouptemplate . "\");");
		}
	    } else {
		// no. look up the username
		$query = sprintf ("select accountname from accounts where id=%s", $tmp[0]);
		$res3 = db_query ($query); $result2 = db_fetch_results ($res3);

		// did we have any results?
		if (db_nof_results ($res3) != 0) {
		    $query = sprintf ("select accountname from accounts where id=%s", $tmp[0]);
		    $res3 = db_query ($query); $result2 = db_fetch_results ($res3);
	            $objectname = $result2[0]; 
                    eval ("\$add = stripslashes (\"" . $mod_usertemplate . "\");");
		}
	    }

	    // need to add anything?
	    if ($add != "") {
		// yes. is this the first one?
		if ($modlist != "") {
		    // no. add the separator too
		    eval ("\$modlist .= \"" . $mod_splitemplate . "\";");
		}

		// add the string
		$modlist .= $add;
	    }
	}

	// did we have any actual templates?
	if ($modlist == "") {
	    // no. use the 'blank'
	    $modlist = GetSkinTemplate ("list_nomod");
	}

	// return the moderator list
	return $modlist;
    }

    //
    // BuildForumList ($where)
    //
    // This will build a forum list, for forums matching where statement $where.
    // The statement must include WHERE.
    //
    function
    BuildForumList($where) {
	global $CONFIG;

	// grab the template
	$forumlist_template = addslashes (GetSkinTemplate ("forum_list"));

	// build the forum list
        $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"];
        if ($timezone == "") { $timezone = 0; };
	$query = sprintf ("select id,name,nofposts,nofthreads,from_unixtime(unix_timestamp(lastpost)+%s),lastposterid,description from forums %s", $timezone, $where);
	$res = db_query ($query);

	// grab the moderator templates
	$mod_usertemplate = AddSlashes (GetSkinTemplate ("list_usermod"));
	$mod_grouptemplate = AddSlashes (GetSkinTemplate ("list_groupmod"));
	$mod_splitemplate = AddSlashes (GetSkinTemplate ("list_splitmod"));

	// handle all forums
	while ($result = db_fetch_results ($res)) {
	    // format the stuff
	    $forumid = $result[0]; $forumname = $result[1];
   	    $nofposts = $result[2]; $nofthreads = $result[3];
	    $lastpost = $result[4]; $lastposterid = $result[5];
	    $lastposter_url = rawurlencode ($lastposter);
	    $description = $result[6];

	    // grab all moderator names
	    $query = sprintf ("select userid,flags from mods where forumid=%s", $forumid);
	    $mods = BuildMods (db_query ($query));

	    // grab the name of the last forum poster
	    $query = sprintf ("select accountname from accounts where id=%s",$lastposterid);
	    $tmp = db_fetch_results (db_query ($query));
	    $lastposter = $tmp[0];

	    eval ("\$tmp = stripslashes (\"" . $forumlist_template . "\");");
	    $forumlist .= $tmp;
	}

	return $forumlist;
    }

    //
    // BuildCatList()
    //
    // This will build the category list.
    //
    function
    BuildCatList() {
	// grab the template
	$catlist_template = addslashes (GetSkinTemplate ("cat_list"));
	$catlist = "";

	// build the query
	$query = sprintf ("select id,name from categories");
	$res = db_query ($query);

	// handle all categories
	while ($result = db_fetch_results ($res)) {
	    // get the data
	    $catid = $result[0]; $catname = $result[1]; $catmods = "";

	    // grab the category mods
	    $query = sprintf ("select userid,flags from catmods where forumid=%s",$catid);
	    $catmods = BuildMods (db_query ($query));

	    // grab the forum posts
	    $query = sprintf ("select sum(nofposts),sum(nofthreads),count(id) from forums where catno=%s", $catid);
	    $res2 = db_query ($query); $tmp = db_fetch_results ($res2);
	    $nofposts = $tmp[0]; $nofthreads = $tmp[1]; $noforums = $tmp[2];

	    // if no actual values, default to zero
	    if ($nofposts == "") { $nofposts = "0"; }
	    if ($nofthreads == "") { $nofthreads = "0"; }

	    // format the template
	    eval ("\$tmp = stripslashes (\"" . $catlist_template . "\");");
	    $catlist .= $tmp;
	}

	return $catlist;
    }

    // need to actually log in?
    if ($action == "dologin") {
	// yes. verify the username and password
	if (VerifyPassword ($the_accountname, $the_password) != 0) {
	    // the username/password is ok. our user is nice, feed him a
	    // cookie :)
	    SetCookie ("authid", $the_accountname . ":" . $the_password, time() + $cookie_duration);
	    $GLOBALS["logged_in"] = 1; $action = "";

	    // get rid of our current guest login
	    $query = sprintf ("delete from curusers where accountid=0 and ipaddr='%s'", $ipaddress);
	    db_query ($query);

	    // need to hide ourselves from presence?
	    if ($invisible != "") {
		// yes. do it
		$GLOBALS["login_flags"] = FLAG_ONLINE_INVISIBLE;
	    }

	    // refresh the login list
	    RefreshLogins();
	} else {
	    // sorry, but this was not correct
	    ShowHeader("");
	    echo GetSkinTemplate ("error_accessdenied");
	    ShowFooter();
	    exit;
	}

	// was a valid forum id passed?
	if ($forumid != "") {
	    // yes. chain to the appropriate page
	    Header ("Location: showforum.php?forumid=" . $forumid);
	    exit;
	}
    }

    // need to log out?
    if ($action == "logout") {
	// yes. crumble the cookie
	SetCookie ("authid", "", 0);

	// get rid of our guest login
	if ($GLOBALS["logged_in"] != 0) {
	    $query = sprintf ("delete from curusers where accountid=%s and ipaddr='%s'", $GLOBALS["userid"], $ipaddress);
	    db_query ($query);
	}

	// chain to the generic show forum thing
	$GLOBALS["logged_in"] = 0; $action = ""; $id = "";

	// refresh the login list
	RefreshLogins();
    }

    // any action given?
    if ($action == "") {
	// no. figure out the template to show
	if ($catid == "") {
	    if ($CONFIG["intro_type"] == 0) {
	        $template = "welcome_forumlist";

	        // grab the forum names
	        $forumlist = BuildForumList("");
	    }
	    if ($CONFIG["intro_type"] == 1) {
	        $template = "welcome_catlist";

	        // grab the category names
	        $catlist = BuildCatList();
	    }
	    if ($CONFIG["intro_type"] == 2) {
	        $template = "welcome_catforum";
	    }
	} else {
	    // we need to list the forums from a category. do it
	    $template = "welcome_forumlist";

	    // grab the forum names
	    $forumlist = BuildForumList("where catno=" . $catid);
	}

	// show the welcome page
	ShowHeader($template);

	// grab some stuff the user might want to display
	$forums_title = $CONFIG["forumtitle"];

	// figure out the number of forum members
	$query = sprintf ("select count(accountname) from accounts");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofmembers = $tmp[0];

	// figure out the number of threads in total
	$query = sprintf ("select count(id) from threads");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofthreads = $tmp[0];

	// figure out the number of threads in total
	$query = sprintf ("select count(id) from posts");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofposts = $tmp[0];

	// figure out the number of online guests
	$query = sprintf ("select count(timestamp) from curusers where accountid=0 or flags and %s",FLAG_ONLINE_INVISIBLE);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofonlineguests = $tmp[0];

	// figure out the number of online members
	$query = sprintf ("select count(timestamp) from curusers where accountid!=0 and not (flags and %s)",FLAG_ONLINE_INVISIBLE);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofonlinemembers = $tmp[0];

	// grab the newest member name and id
	$query = sprintf ("select id,accountname from accounts order by id desc limit 1");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$newmemberid = $tmp[0]; $newmembername = $tmp[1];

	// is a category id given?
	if ($catid != "") {
	    // yes. get the category name
	    $query = sprintf ("select name from categories where id=%s", $catid);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
	    $cat_title = $tmp[0];
	}

	// grab the templates
        $membertemplate = AddSlashes (GetSkinTemplate ("online_firstmember"));
        $member_nextemplate = AddSlashes (GetSkinTemplate ("online_moremember"));

	// construct the list of online members
	$onlinemembers = "";
	$query = sprintf ("select accountid from curusers where accountid!=0 and not (flags and %s)",FLAG_ONLINE_INVISIBLE);
	$res = db_query ($query);
	while ($tmp = db_fetch_results ($res)) {
	    $accountid = $tmp[0];

   	    // grab the account name
	    $query = sprintf ("select accountname from accounts where id=%s",$accountid);
	    $res2 = db_query ($query); $result = db_fetch_results ($res2);

	    // did we have any valid results?
	    if (db_nof_results ($res2) != 0) {
	        // yes. add the user
		$accountname = $result[0];
                eval ("\$tmp = stripslashes (\"" . $membertemplate . "\");");
		$onlinemembers .= $tmp;
	        $membertemplate = $member_nextemplate;
	    }
	}

	// grab the templates
        $birthday_template = AddSlashes (GetSkinTemplate ("birthday_firstmem"));
        $birthday_nextemplate = AddSlashes (GetSkinTemplate ("birthday_moremem"));

	// build the birthdays
	$query = sprintf ("select id,unix_timestamp(now())-unix_timestamp(birthday) from accounts where dayofyear(birthday)=dayofyear(now())");
	$res = db_query ($query); $birthdays = "";
	while ($tmp = db_fetch_results ($res)) {
	    $accountid = $tmp[0]; $accountname = GetMemberNameSimple ($accountid);
	    if ($accountname != "") {
		// calculate the age
		$age = (int)($tmp[1] / (365 * 3600 * 24));

		// build the fields
                eval ("\$birthdays .= stripslashes (\"" . $birthday_template . "\");");
		$birthday_template = $birthday_nextemplate;
	    }
	}

	// got any birthdays?
	if ($birthdays != "") {
	    // yes. list them
	    eval ("\$birthdays = stripslashes (\"" . addslashes (GetSkinTemplate ("birthday_list")) . "\");");
	}

	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ($template)) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to show the login page?
    if ($action == "login") {
	// yes. do it
	ShowHeader("page_login");
	print GetSkinTemplate ("page_login");
	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);
 ?>
