<?php
    //
    // index.php
    //
    // This will display the forum and category listing.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

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
	global $VAR;

	// process them all
	$modlist = "";
	while (list ($VAR["objectid"], $VAR["objectflags"]) = db_fetch_results ($res)) {
	    // build the moderator list. is this a group?
	    $add = "";
	    if ($VAR["objectflags"] & FLAG_USERLIST_GROUP) {
		// yes. look up the groupname 
		$VAR["objectname"] = GetGroupNameSimple ($VAR["objectid"]);

		// did we have any results?
		if ($VAR["objectname"] != "") {
		    // yes. add the group name
		    $add .= InsertSkinVars (GetSkinTemplate ("list_groupmod"));
		}
	    } else {
		// no. look up the username
		$VAR["objectname"] = GetMemberNameSimple ($VAR["objectid"]);

		// did we have any results?
		if ($VAR["objectname"] != "") {
		    // yes. add it to the list
		    $add .= InsertSkinVars (GetSkinTemplate ("list_usermod"));
		}
	    }

	    // need to add anything?
	    if ($add != "") {
		// yes. is this the first one?
		if ($modlist != "") {
		    // no. add the separator too
		    $modlist .= GetSkinTemplate ("list_splitmod");
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
	global $CONFIG, $VAR;

	// build the forum list
        $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"] + 0;
	$query = sprintf ("SELECT id,name,nofposts,nofthreads,FROM_UNIXTIME(UNIX_TIMESTAMP(lastpost)+%s),lastposterid,description,lastpostername FROM forums %s ORDER BY orderno ASC", $timezone, $where);
	$res = db_query ($query);

	// handle all forums
	while (list ($VAR["forumid"], $VAR["forumname"], $VAR["nofposts"], $VAR["nofthreads"], $VAR["lastpost"], $VAR["lastposterid"], $VAR["description"], $VAR["lastposter"]) = db_fetch_results ($res)) {
	    // grab all moderator names
	    $query = sprintf ("SELECT userid,flags FROM mods WHERE forumid='%s'", $VAR["forumid"]);
	    $VAR["mods"] = BuildMods (db_query ($query));

	    // grab the name of the last forum poster	
	    if ($VAR["lastposterid"] != 0) {
	        $VAR["lastposter"] = GetMemberName ($VAR["lastposterid"]);
	    }

            // grab the id of the last thread in the forum
            $query = sprintf ("SELECT id FROM threads WHERE forumid=%s ORDER BY id DESC LIMIT 1", $VAR["forumid"]);
	    list ($VAR["newestthread"]) = db_fetch_results (db_query ($query));

	    // do we have posts in this forum?
	    if ($VAR["nofposts"] != 0) {
		// yes. build the template
		$VAR["lastpost"] = InsertSkinVars (GetSkinTemplate ("lastpost"));
	    } else {
		// no. use the 'lastpost_none' template
		$VAR["lastpost"] = InsertSkinVars (GetSkinTemplate ("lastpost_none"));
	    }

	    // build the template
	    $forumlist .= InsertSkinVars (GetSkinTemplate ("forum_list"));
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
	global $VAR;

	// build the query
	$query = sprintf ("SELECT id,name,description FROM categories ORDER BY orderno ASC");
	$res = db_query ($query);

	// handle all categories
	while (list ($VAR["catid"], $VAR["catname"], $VAR["description"]) = db_fetch_results ($res)) {
	    // grab the category mods
	    $query = sprintf ("SELECT userid,flags FROM catmods WHERE forumid=%s",$catid);
	    $VAR["catmods"] = BuildMods (db_query ($query));

	    // grab the forum posts
	    $query = sprintf ("SELECT SUM(nofposts),SUM(nofthreads),COUNT(id) FROM forums WHERE catno='%s'", $VAR["catid"]);
	    list ($VAR["nofposts"], $VAR["nofthreads"], $VAR["noforums"]) = db_fetch_results (db_query ($query));

	    // if there are no actual values, default to zero
	    $VAR["nofposts"] += 0; $VAR["nofthreads"] += 0;

	    // format the template
	    $catlist .= InsertSkinVars (GetSkinTemplate ("cat_list"));
	}

	return $catlist;
    }

    //
    // BuildCatForumList()
    //
    // This will build the forum list with category headings.
    //
    function
    BuildCatForumList() {
	global $VAR;

	// build the query
	$query = sprintf ("SELECT id,name,description FROM categories ORDER BY orderno ASC");
	$res = db_query ($query);

	// handle all categories
	while (list ($VAR["catid"], $VAR["catname"], $VAR["description"]) = db_fetch_results ($res)) {
	    // format the template
	    $catforumlist .= InsertSkinVars (GetSkinTemplate ("catforum_cat_list"));

	    // grab the category mods
	    $query = sprintf ("SELECT userid,flags FROM catmods WHERE forumid='%s'",$VAR["catid"]);
	    $VAR["catmods"] = BuildMods (db_query ($query));
 
            // build the forum list
            $timezone = $GLOBALS["timediff"] + $CONFIG["timezone"] + 0;

	    $query = sprintf ("SELECT id,name,nofposts,nofthreads,FROM_UNIXTIME(UNIX_TIMESTAMP(lastpost)+%s),lastposterid,description,lastpostername FROM forums WHERE catno='%s' ORDER BY orderno ASC",$timezone, $VAR["catid"]);
	    $res2 = db_query ($query);
            while (list ($VAR["forumid"], $VAR["forumname"], $VAR["nofposts"], $VAR["nofthreads"], $VAR["lastpost"], $lastposterid, $VAR["description"], $VAR["lastposter"]) = db_fetch_results ($res2)) {
	         // grab all moderator names
	         $query = sprintf ("SELECT userid,flags FROM mods WHERE forumid='%s'", $VAR["forumid"]);
	         $VAR["mods"] = BuildMods (db_query ($query));

	         // grab the name of the last forum poster
		 if ($lastposterid != 0) {
		     $VAR["lastposter"] = GetMemberName ($lastposterid);
		 }

                 // grab the threadid of the last thread in the forum
                 $query = sprintf ("SELECT id FROM threads WHERE forumid='%s' ORDER BY id DESC LIMIT 1", $VAR["forumid"]);
                 list ($VAR["newestthread"]) = db_fetch_results (db_query ($query));

	         // do we have posts in this forum?
	         if ($VAR["nofposts"] != 0) {
	            // yes. build the template
	            $VAR["lastpost"] = InsertSkinVars (GetSkinTemplate ("lastpost"));
	         } else {
	            // no. use the 'lastpost_none' template
	            $VAR["lastpost"] = InsertSkinVars (GetSkinTemplate ("lastpost_none"));
	         }

                 // format the template
                 $catforumlist .= InsertSkinVars (GetSkinTemplate ("catforum_forum_list"));
             }
	}

	return $catforumlist;
    }

    // need to actually log in?
    if ($_REQUEST["action"] == "dologin") {
	// yes. verify the username and password
	VerifyPassword ($_REQUEST["the_accountname"], $_REQUEST["the_password"], 1);

	// the username/password is ok. our user is nice, feed him a
	// cookie :)
	SetCookie ("authid", $_REQUEST["the_accountname"] . ":" . $_REQUEST["the_password"], time() + $cookie_duration);
	$GLOBALS["logged_in"] = 1; $_REQUEST["action"] = "";

	// get rid of our current guest login
	$query = sprintf ("DELETE FROM curusers WHERE accountid=0 AND ipaddr='%s'", $ipaddress);
	db_query ($query);

	// need to hide ourselves from presence?
	if ($invisible != "") {
	    // yes. do it
	    $GLOBALS["login_flags"] = FLAG_ONLINE_INVISIBLE;
	}

	// refresh the login list
	RefreshLogins();

	// was a forum id passed?
	$forumid = trim (preg_replace ("/\D/", "", $forumid));
	if ($forumid != "") {
	    // yes. chain to the appropriate page
	    Header ("Location: showforum.php?forumid=" . $forumid);
	    exit;
	}
    }

    // need to log out?
    if ($_REQUEST["action"] == "logout") {
	// yes. crush the cookie
	SetCookie ("authid", "", 0);

	// get rid of our guest login
	if ($GLOBALS["logged_in"] != 0) {
	    $query = sprintf ("DELETE FROM curusers WHERE accountid='%s' AND ipaddr='%s'", $GLOBALS["userid"], $ipaddress);
	    db_query ($query);
	}

	// chain to the generic show forum thing
	$GLOBALS["logged_in"] = 0; $_REQUEST["action"] = ""; $id = "";

	// get rid of our username global fields
	$GLOBALS["username"] = ""; $GLOBALS["userid"] = "";

	// reload the skin
	LoadSkin();

	// refresh the login list
	RefreshLogins();
    }

    // any action given?
    if ($_REQUEST["action"] == "") {
	// no. figure out the template to show
        $catid = trim (preg_replace ("/\D/", "", $catid));
	if ($catid == "") {
	    if ($CONFIG["intro_type"] == 0) {
	        $template = "welcome_forumlist";

	        // grab the forum names
	        $VAR["forumlist"] = BuildForumList("");
	    }
	    if ($CONFIG["intro_type"] == 1) {
	        $template = "welcome_catlist";

	        // grab the category names
	        $VAR["catlist"] = BuildCatList();
	    }
	    if ($CONFIG["intro_type"] == 2) {
	        $template = "welcome_catforum";

                // grab the category and forum names
                $VAR["catforumlist"] = BuildCatForumList();
	    }
	} else {
	    // we need to list the forums from a category. do it
	    $template = "welcome_forumlist";

	    // grab the category name
	    $query = sprintf ("SELECT name FROM categories WHERE id='%s'", $catid);
	    list ($VAR["cat_title"]) = db_fetch_results (db_query ($query));

	    // grab the forum names
	    $VAR["forumlist"] = BuildForumList("WHERE catno=" . $catid);
	}

	// figure out the number of forum members
	$query = sprintf ("SELECT COUNT(id) FROM accounts");
	list ($VAR["nofmembers"]) = db_fetch_results (db_query ($query));

	// figure out the number of threads in total
	$query = sprintf ("SELECT COUNT(id) FROM threads");
	list ($VAR["nofthreads"]) = db_fetch_results (db_query ($query));

	// figure out the number of threads in total
	$query = sprintf ("SELECT COUNT(id) FROM posts");
	list ($VAR["nofposts"]) = db_fetch_results (db_query ($query));

	// figure out the number of online guests
	$query = sprintf ("SELECT COUNT(id) FROM curusers WHERE accountid=0 OR (flags&%s)",FLAG_ONLINE_INVISIBLE);
	list ($VAR["nofonlineguests"]) = db_fetch_results (db_query ($query));

	// figure out the number of online members
	$query = sprintf ("SELECT COUNT(id) FROM curusers WHERE accountid!=0 AND NOT (flags&%s)",FLAG_ONLINE_INVISIBLE);
	list ($VAR["nofonlinemembers"]) = db_fetch_results (db_query ($query));

	// grab the newest member name and id
	$query = sprintf ("SELECT id,accountname FROM accounts ORDER BY id DESC LIMIT 1");
	list ($VAR["newmemberid"], $VAR["newmembername"]) = db_fetch_results (db_query ($query));

	// grab the templates
        $online_template = GetSkinTemplate ("online_firstmember");

	// construct the list of online members
	$query = sprintf ("SELECT accountid FROM curusers WHERE accountid!=0 AND NOT (flags AND %s)",FLAG_ONLINE_INVISIBLE);
	$res = db_query ($query);
	while (list ($VAR["accountid"]) = db_fetch_results ($res)) {
	    // grab the account name
	    $VAR["accountname"] = GetMemberNameSimple ($VAR["accountid"]);
	    if ($VAR["accountname"] != "") {
	        // this worked. add the member to the list
		$VAR["onlinemembers"] .= InsertSkinVars ($online_template);
	        $online_template = GetSkinTemplate ("online_moremember");
	    }
	}

	// build the birthdays
	$query = sprintf ("SELECT id,UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(birthday) FROM accounts WHERE DAYOFYEAR(birthday)=DAYOFYEAR(now())");
	$res = db_query ($query); 
	while (list ($TAG["accountid"], $daysold) = db_fetch_results ($res)) {
	    // get the account name
	    $accountname = GetMemberNameSimple ($VAR["accountid"]);
	    if ($accountname != "") {
		// this worked. calculate the age
		$VAR["age"] = (int)($daysold / (365 * 3600 * 24));

		// build the list
                $birthdays .= InsertSkinVars ($birthday_template);
		$birthday_template = GetSkinTemplate ("birthday_moremem");
	    }
	}

	// got any birthdays?
	if ($VAR["birthdays"] != "") {
	    // yes. list them
	    $VAR["birthdays"] = InsertSkinVars (GetSkinTemplate ("birthday_list"));
	}

	// build the hopto list
	$VAR["hopto_list"] = BuildHopto();
	
	// grab the maximum online user count
	$VAR["max_online"] = $CONFIG["max_online"];
	$VAR["max_online_timestamp"] = $CONFIG["max_online_timestamp"];
	ShowForumPage($template);
	exit;
    }

    // need to show the login page?
    if ($_REQUEST["action"] == "login") {
	// yes. do it
	ShowForumPage("page_login");
	exit;
    }

    // need to hop to a forum/category?
    if ($_REQUEST["action"] == "hopto") {
	// yes. is it a forum?
	$dest = $_REQUEST["dest"];
	if (preg_match ("/^CATEGORY\:/", $dest)) {
	    // no. hop to a category
	    $dest = str_replace ("CATEGORY:", "", $dest);
	    Header ("Location: index.php?catid=" . $dest);
	    exit;
	}

	// hop to a forum
	$dest = str_replace ("FORUM:", "", $dest);
	Header ("Location: showforum.php?forumid=" . $dest);
	exit;
    }
 ?>
