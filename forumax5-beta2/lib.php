<?php
    //
    // lib.php
    // (c) 2000 Rink Springer, www.forumax.com
    //
    // This is the main ForuMAX library, which does generic, useful stuff.
    //

    // FLAG_ADMIN is the bit you need to be an admin
    define (FLAG_ADMIN, 1);

    // FLAG_MMOD is the bit you need to be a mega mod
    define (FLAG_MMOD, 2);

    // FLAG_DISABLED is the bit you need to be a disabled account
    define (FLAG_DISABLED, 4);

    // FLAG_HIDEMAIL is the bit you need to have your email address hidden
    define (FLAG_HIDEMAIL, 8);

    // FLAG_SKIN_DEFAULT is the bit a skin needs to be the default skin
    define (FLAG_SKIN_DEFAULT, 1);

    // FLAG_THREAD_LOCKED is the bit that indicates a thread is locked
    define (FLAG_THREAD_LOCKED, 1);

    // FLAG_THREAD_REPORTED is the bit that indicates a thread has been reported
    define (FLAG_THREAD_REPORTED, 2);

    // FLAG_FORUM_ALLOWHTML is the bit that allows HTML to be used within a
    // forum
    define (FLAG_FORUM_ALLOWHTML, 1);

    // FLAG_FORUM_ALLOWMAX is the bit that allow MaX codes to be used within
    // a forum
    define (FLAG_FORUM_ALLOWMAX, 2);

    // FLAG_FORUM_DENYEVILHTML is the bit that will block evil HTML tags
    define (FLAG_FORUM_DENYEVILHTML, 4);

    // FLAG_FORUM_HIDDEN is the bit that will make a forum invisible
    define (FLAG_FORUM_HIDDEN, 8);

    // FLAG_FORUM_DISABLED is the bit that will disable a forum
    define (FLAG_FORUM_DISABLED, 16);

    // FLAG_FORUM_NOIMAGES is the bit that will disable any image
    define (FLAG_FORUM_NOIMAGES, 32);

    // FLAG_ONLINE_INVISIBLE is the bit that will hide your presence
    define (FLAG_ONLINE_INVISIBLE, 1);

    // FLAG_FORUM_ALLPRIVS is the bitmask that will allow any kind of activity
    // within a forum. It is used for announcements
    define (FLAG_FORUM_ALLPRIVS, FLAG_FORUM_ALLOWHTML | FLAG_FORUM_ALLOWMAX);

    // FLAG_PM_READ is the flag that will mark a private message as read
    define (FLAG_PM_READ, 1);

    // FLAG_POST_SIG is the flag that indicates the user's signature should be
    // appended to the post made.
    define (FLAG_POST_SIG, 1);

    // FLAG_USERLIST_GROUP is the flag an userlist needs in order to contain
    // a group
    define (FLAG_USERLIST_GROUP, 1);

    // FORUMAX_VERSION is the ForuMAX version. Don't touch this!
    define (FORUMAX_VERSION, "5.0 BETA 2");

    // figure out the default skin name.
    $query = sprintf ("select name,id from skins where (flags & " . FLAG_SKIN_DEFAULT . ")!=0");
    $res = db_query ($query); $result = db_fetch_results ($res);
    $GLOBALS["skin"] = $result[0]; $GLOBALS["skinid"] = $result[1];

    // do we have a skin?
    if ($GLOBALS["skinid"] != "") {
        // yes. grab all skin values and dump them into $SKIN_VALUE[].
        $query = sprintf ("select name,content from skinvars_%s",$GLOBALS["skinid"]);
        $res = db_query ($query);

        // now, copy them all to $SKIN_VALUE[]
        global $SKIN_VALUE;
        while ($result = db_fetch_results ($res)) {
	    // set the value
	    $SKIN_VALUE[$result[0]] = $result[1];
        }
    }

    // default to not logged in
    $GLOBALS["logged_in"] = 0;

    // are we logged in?
    if ($authid != "") {
	// yes. verify the identity
	$idcookie = explode (":", $authid);

	if (VerifyPassword ($idcookie[0], $idcookie[1]) != 0) {
	    // yes. we are logged in!
	    $GLOBALS["logged_in"] = 1;
	} else {
	    // crumble the cookie
	    SetCookie ("authid", "", 0);
	    $authid = "";
	}
    }

    // grab all configuration values and dump them into $CONFIG[].
    global $CONFIG;
    $query = sprintf ("select name,content from config");
    $res = db_query ($query);

    // now, copy them all to $CONFIG[]
    while ($result = db_fetch_results ($res)) {
	// set the value
	$CONFIG[$result[0]] = $result[1];
    }

    // grab the ip address
    $ipaddress = getenv ("REMOTE_ADDR");

    //
    // VerifyPassword ($username, $password)
    //
    // This will verify the username and password, and set severnal
    // $GLOBALS[] values if it's correct. It returns zero if the password is
    // incorrect, otherwise non-zero.
    //
    function
    VerifyPassword($username,$password) {
	$query = sprintf ("select password,flags,id,timediff from accounts where accountname='%s'", $username);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// do we have an account record and is the password correct?
	if ((db_nof_results ($res) > 0) and ($password == $result[0])) {
	    // yes. we are logged in!
	    $GLOBALS["logged_in"] = 1; $GLOBALS["username"] = $username;
	    $GLOBALS["password"] = $result[0]; $GLOBALS["flags"] = $result[1];
	    $GLOBALS["userid"] = $result[2]; $GLOBALS["timediff"] = $result[3];

	    // it was correct.
	    return 1;
	}

	// it was not correct.
	return 0;
    }

    //
    // GetSkinFields ($name, $skinfield)
    //
    // This will retrieve field $field from template $name from the currently
    // selected skin and return it.
    //
    function
    GetSkinFields ($name, $skinfield) {
	// build the query
	$query = sprintf ("select %s from skin_%s where name='%s'", $skinfield, $GLOBALS["skinid"], $name);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// did this actually wield any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    die ("Error: Template <code>" . $name . "</code> could not be found within skin <code>" . $GLOBALS["skin"] . "</code>");
	}

	// return the result
	return $result;
    }

    //
    // GetSkinTemplate ($name,$insertvars=1)
    //
    // This will retrieve template $name from the currently selected skin and
    // return it. It will cache all templates requested. If $insertvars is non-
    // zero, the skin variables will be filled in.
    //
    function
    GetSkinTemplate ($name,$insertvars = 1) {
	global $skincache;

	// was this thing already cached?
	if (($skincache[$name] == "") or ($insertvars == 0)) {
	    // no. grab the skin
	    $tmp = GetSkinFields ($name, "content");
	    if ($insertvars != 0) {
		$tmp2 = InsertSkinVars ($tmp[0]);
	    } else {
		$tmp2 = $tmp[0];
	    }

	    // add it to the cache
	    $skincache[$name] = $tmp2;

	    // return the skin
	    return $tmp2;
	}

	// return return the cached entry
	return $skincache[$name];
    }

    //
    // GetSkinTitle ($name)
    //
    // This will retrieve the title of template $name from the currently
    // selected skin and return it.
    //
    function
    GetSkinTitle ($name) {
	$tmp = GetSkinFields ($name, "title");
	return $tmp[0];
    }

    //
    // ShowHeader($template)
    //
    // This will show the generic forum header. The page will receive the title
    // and refresh URL's from $template.
    //
    function
    ShowHeader($template) {
	global $forumtitle, $threadtitle, $forumname, $CONFIG, $threadid;
	global $forumid, $SKIN_VALUE, $forum_image;

	// was a template given?
	if ($template != "") {
	    // yes. grab the values
	    $temp = GetSkinFields ($template, "title,refresh_url");

	    // set the values
	    eval ("\$tmp = \"" . $temp[0] . "\";"); $title = $tmp;
	    eval ("\$tmp = \"" . $temp[1] . "\";"); $refreshurl = $tmp;
	} else {
	    // make them blank
	    $title = ""; $refreshurl = "";
	}

	// show the HTML header
 // <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
 ?><html><head><title><?php
    // is there no title selected?
    if ($title == "") {
	// no. just use the forum titles.
	$title = $CONFIG["forumtitle"];
    } else {
	// yes. use it, but prepend the forum titles and a dash
	$title = $CONFIG["forumtitle"] . " - " . $title;
    }
    eval ("\$tmp = \"" . $title . "\";"); echo $tmp; ?></title><style type="text/css">
<?php
	// try to grab a 'stylesheet' template from our skin
	$query = sprintf ("select content from skin_%s where name='stylesheet'", $GLOBALS["skinid"]);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// print it
	print InsertSkinVars ($result[0]) . "\n";
 ?></style>
<?php
	// do we have a refresh URL?
	if ($refreshurl != "") {
	    // yes. make sure our page refreshes
            printf ("<meta http-equiv=\"Refresh\" content=\"2; url=%s\">", $refreshurl);
	}
?></head><body><?php
	// figure out the number of forum members
	$query = sprintf ("select count(*) from accounts");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofmembers = $tmp[0];

	// figure out the number of threads in total
	$query = sprintf ("select count(*) from threads");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofthreads = $tmp[0];

	// figure out the number of threads in total
	$query = sprintf ("select count(*) from posts");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$nofposts = $tmp[0];

	// grab the username and forum title
	$username = $GLOBALS["username"];
	$forums_title = $CONFIG["forum_title"];

	// are we logged in?
	$template = "header_";
	if ($GLOBALS["logged_in"] != 0) {
	    // yes. use the members template
	    $template .= "member";
	} else {
	    // no. use the visitor template
	    $template .= "visitor";
	}

	// grab the newest member name and id
	$query = sprintf ("select id,accountname from accounts order by id desc limit 1");
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$newmemberid = $tmp[0]; $newmembername = $tmp[1];

	// build the private messages list
	$newpm = PMBuildList();

	// do we have a forum image?
	if ($forum_image == "") {
	    // no. default to the one in the skin profile
	    $forum_image = $SKIN_VALUE["default_image"];
	} else {
	    // append the "/images/" thingy
	    $forum_image = $SKIN_VALUE["images_url"] . $forum_image;
	}

	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ($template)) . "\");");
	print $tmp;
    }

    //
    // ShowFooter()
    //
    // This will show the generic forum footer.
    //
    function
    ShowFooter() {
	// are we logged in?
	$template = "footer_";
	if ($GLOBALS["logged_in"] != 0) {
	    // yes. use the members template
	    $template .= "member";
	} else {
	    // no. use the visitor template
	    $template .= "visitor";
	}

	$VERSION = FORUMAX_VERSION;
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ($template)) . "\");");
	print $tmp;
 ?></body></html>
<?php
    }

    //
    // IsForumMod ($forumid)
    //
    // This will return zero if the currently logged-in user is not a moderator
    // of forum $forumid, otherwise non-zero.
    //
    function
    IsForumMod ($forumid) {
	// are we an admin?
        if (($GLOBALS["flags"] & FLAG_ADMIN) != 0) {
	    // yes. we can moderate anything we want
	    return 1;
	}

	// are we a mega moderator?
        if (($GLOBALS["flags"] & FLAG_MMOD) != 0) {
	    // yes. we can moderate anything we want
	    return 1;
	}

	// build the query
	$query = sprintf ("select id from mods where forumid=%s and userid=%s and not (flags and 1)limit 1",$forumid,$GLOBALS["userid"]);
	$res = db_query ($query);

	// got any mods?
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we're a moderator here
	    return 1;
	}

	// figure out the forum category id
	$query = sprintf ("select catno from forums where id=%s", $forumid);
	$res = db_query ($query); $tmp = db_fetch_results ($res);
	$catid = $tmp[0];

	// grab the category moderators
	$query = sprintf ("select id from catmods where forumid=%s and userid=%s limit 1",$catid,$GLOBALS["userid"]);
	// got any mods?
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we're a moderator here
	    return 1;
	}

	// are we a moderator by use of a group?
	$query = sprintf ("select groupmembers.userid from mods inner join groupmembers on mods.userid=groupmembers.groupid where (mods.flags and 1) and groupmembers.userid=%s and mods.forumid=%s limit 1", $GLOBALS["userid"], $forumid);
	$res = db_query ($query);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we can moderate this
	    return 1;
	}

	// check whether we are a category moderator by use of a group
	$query = sprintf ("select groupmembers.userid from catmods inner join groupmembers on catmods.userid=groupmembers.groupid where (catmods.flags and 1) and groupmembers.userid=%s and catmods.forumid=%s limit 1", $GLOBALS["userid"], $catid);
	return (db_nof_results (db_query ($query)));
    }

    //
    // MatchVar ($val1, $val2, $var)
    //
    // This will return $var if ($val1 == $val2), otherwise nothing
    //
    function
    MatchVar ($val1, $val2, $var) {
	if ($val1 == $val2) { return $var; };
	return "";
    }

    //
    // InsertSkinVars($text)
    //
    // This will insert the currently the skin variables in $text.
    //
    function
    InsertSkinVars($text) {
	// WARNING: only the variables here can be checked using [[]]
	global $SKIN_VALUE, $GLOBALVARS, $dayspan, $fieldvalue, $timezone;
	global $month, $day, $privemail;

	// replace all values we know of
	$tmp = preg_replace ("/\{((\S)*)\}/e", '$SKIN_VALUE["\\1"]', $text);

	// handle all [[]] thingies
	$tmp = preg_replace ("/\[\[(.*?)==(.*?)\&\&(.*?)\]\]/e", 'MatchVar ("\\1","\\2","\\3")', $tmp);

	return $tmp;
    }

    //
    // CanVisitRestrictedForum ($forumid)
    //
    // This will return zero if the current logged-in user cannot view forum
    // $forumid, otherwise non-zero.
    //
    function
    CanVisitRestrictedForum ($forumid) {
	// are we an administrator?
        if (($GLOBALS["flags"] & FLAG_ADMIN) != 0) {
	    // yes. we can visit any forum we want
	    return 1;
	}

	// it all depends on the list now
	$query = sprintf ("select id from restricted where forumid=%s and userid=%s limit 1", $forumid, $GLOBALS["userid"]);
	return db_nof_results (db_query ($query));
    }

    //
    // ApplyMaXCodes ($text, $flags)
    //
    // This will apply MaX codes to $text. $flags are the forum flags.
    //
    function
    ApplyMaXCodes ($text, $flags) {
	// standard bold, italic and underlines stuff
	$text = preg_replace ("/\[b\](.*)\[\/b\]/sU", stripslashes (GetSkinTemplate ("maxcode_bold")), $text);
	$text = preg_replace ("/\[u\](.*)\[\/u\]/sU", stripslashes (GetSkinTemplate ("maxcode_underline")), $text);
	$text = preg_replace ("/\[i\](.*)\[\/i\]/sU", stripslashes (GetSkinTemplate ("maxcode_italic")), $text);

	// links
	$text = preg_replace ("/\[email\](.*)\[\/email\]/sU", stripslashes (GetSkinTemplate ("maxcode_email")), $text);
	$text = preg_replace ("/\[url\](.*)\[\/url\]/sU", stripslashes (GetSkinTemplate ("maxcode_url")), $text);
	$text = preg_replace ("/\[url=(.*)\]((.)*)\[\/url\]/sU", stripslashes (GetSkinTemplate ("maxcode_exturl")), $text);

	// quote and code
	$text = preg_replace ("/\[quote\](.*)\[\/quote\]/sU", stripslashes (GetSkinTemplate ("maxcode_quote")), $text);
	$text = preg_replace ("/\[code\](.*)\[\/code\]/sU", stripslashes (GetSkinTemplate ("maxcode_code")), $text);

	// are forum images ok?
	if (($flags & FLAG_FORUM_NOIMAGES) == 0) {
	    // yes. allow them
	    $text = preg_replace ("/\[img\](.*)\[\/img\]/sU", stripslashes (GetSkinTemplate ("maxcode_img")), $text);
	}
	return $text;
    }

    // 
    // RemoveHTMLTags ($text)
    //
    // This will render HTML useless in $text.
    //
    function
    RemoveHTMLTags ($text) {
	$text = preg_replace ("/\</", "&lt;", $text);
	$text = preg_replace ("/\>/", "&gt;", $text);
	return $text;
    }

    //
    // FixupMessage ($text, $flags)
    //
    // This will fixup message $text, according to forum flags $flags.
    //
    function
    FixupMessage ($text, $flags) {
	// need to get rid of HTML?
	if (($flags & FLAG_FORUM_ALLOWHTML) == 0) {
	    // yes. do it
	    $text = RemoveHTMLTags ($text);
	}

	// need to apply MaX codes?
	if (($flags & FLAG_FORUM_ALLOWMAX) != 0) {
	    // yes. do it
	    $text = ApplyMaXCodes ($text, $flags);
        }

	// need to get rid of images?
	if (($flags & FLAG_FORUM_NOIMAGES) != 0) {
	    // yes. get rid of them
	    $text = preg_replace ("/\<img/", "&lt;img", $text);
	}

	// need to get rid of javascript?
	if (($flags & FLAG_FORUM_DENYEVILHTML) != 0) {
	    // yes. kill it
	    $text = preg_replace ("/javascript/", "java-script", $text);

	    // grab the evil HTML tags
	    $tmp = preg_replace ("/\r/", "", GetSkinTemplate ("evil_html_tags"));
	    $eviltags = explode ("\n", $tmp);
	    while (list (, $eviltag) = each ($eviltags)) {
		// get rid of this tag
		$text = preg_replace ("/<" . $eviltag . "/", "&lt;$eviltag", $text);
	    }
	}

	// return the message
	return nl2br ($text);
    }

    //
    // GetCustomStatus ($userid)
    //
    // This will return the custom status of $userid or a blank string if the
    // user does not have one. It will cache any requests made.
    //
    function
    GetCustomStatus ($userid) {
	global $customfieldid;

	// is this user cached?
	if ($customcache[$userid] != "") {
	    // yes. get rid of the dash and return the item
	    return preg_replace ("/^\-/", "", $customcache[$userid]);
	}

	// if we have 'no' in the custom field id cache, there is no custom
	// field
	if ($customfieldid == "no") {
	    // there are no custom fields here.
	    return "";
	}

	// do we have a custom field id caches??
	if ($customfieldid == "") {
	    // no. do we have a custom status field?
	    $query = sprintf ("select id from customfields where type=7 limit 1");
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    if (db_nof_results ($res) == 0) {
	        // no. we never have a custom status
		$customfieldid = "no";
	        return "";
	    }

	    // grab the field
	    $customfieldid = $result[0];
	}
 
	// grab the custom name
	$query = sprintf ("select extra%s from accounts where id=%s", $customfieldid, $userid);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// cache this status
	$customcache[$userid] = "-" . $result[0];

	return $result[0];
    }

    //
    // GetMemberStatus ($userid);
    //
    // This will return the member status of user $userid. It was cache all
    // results.
    //
    function
    GetMemberStatus ($userid) {
	global $CONFIG;
	global $customcache;

	// grab the custom status
	$tmp = GetCustomStatus ($userid);

	// did this yield any results?
	if ($tmp != "") {
	    // yes. return the status
	    return $tmp;
	}

	// grab the flags
	$query = sprintf ("select flags from accounts where id=%s", $userid);
	$res = db_query ($query);

	// did this yield any results?
	if (db_nof_results ($res) == 0) {
	    // no. return the unknown status
	    $customcache[$userid] = "-" . $GLOBALS["unknown_title"];
	    return $GLOBALS["unknown_title"];
	}

	// yes. get the results
	$tmp = db_fetch_results ($res); $flags = $tmp[0];

	// are we an administrator?
        if (($flags & FLAG_ADMIN) != 0) {
	    // yes. return the correct status	
	    $customcache[$userid] = "-" . $GLOBALS["admin_title"];
	    return $CONFIG["admin_title"];
	}

	// are we a mega mod?
        if (($flags & FLAG_MMOD) != 0) {
	    // yes. return the correct status
	    $customcache[$userid] = "-" . $GLOBALS["megamod_title"];
	    return $CONFIG["megamod_title"];
	}

	// are we a category mod?
        if (IsCategoryMod ($userid) != 0) {
	    // yes. return the correct status	
	    $customcache[$userid] = "-" . $GLOBALS["catmod_title"];
	    return $CONFIG["catmod_title"];
	}

	// are we a mod?
        if (IsMod ($userid) != 0) {
	    // yes. return the correct status
	    $customcache[$userid] = "-" . $GLOBALS["mod_title"];
	    return $CONFIG["mod_title"];
	}

	// we're just an ordinary member. return that	
	$customcache[$userid] = "-" . $GLOBALS["member_title"];
	return $CONFIG["member_title"];
    }

    //
    // GetMemberNameSimple ($userid)
    //
    // This will return the member name of user $userid, or "" if it could
    // not be found.
    //
    function GetMemberNameSimple ($userid) {
	// grab the username
	$query = sprintf ("select accountname from accounts where id=%s",$userid);
	$author_res = db_query ($query);

	// did this work?
	if (db_nof_results ($author_res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the member name
        $tmp = db_fetch_results ($author_res);
	return $tmp[0];
    }

    //
    // GetGroupNameSimple ($groupid)
    //
    // This will retrieve the group name of group $groupid. If the group
    // could not be found, a blank string will be returned.
    //
    function
    GetGroupNameSimple ($groupid) {
	// grab the groupname
	$query = sprintf ("select name from groups where id=%s",$groupid);
	$group_res = db_query ($query);

	// did this work?
	if (db_nof_results ($group_res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the group name
        $tmp = db_fetch_results ($group_res);
	return $tmp[0];
    }

    //
    // GetMemberName ($userid)
    //
    // This will return the member name of user $userid, or
    // $GLOBALS["delmem_name"] if the user could not be found.
    //
    function GetMemberName ($userid) {
	global $CONFIG;

	// grab the username
	$tmp = GetMemberNameSimple ($userid);

	// did this work?
	if ($tmp == "") {
	    // no. use the name for deleted members
	    $tmp = $CONFIG["delmem_name"];
	}

	// return the name
	return $tmp;
    }

    //
    // GetMemberID ($username)
    //
    // This will return the user ID of account $username. If the account does
    // not exist, it will return a blank string.
    //
    function
    GetMemberID ($username) {
	// build the query
	$query = sprintf ("select id from accounts where accountname='%s'", $username);
	$res = db_query ($query);

	// did this work?
	if (db_nof_results ($res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the member id
        $tmp = db_fetch_results ($res);
	return $tmp[0];
    }

    //
    // GetGroupID ($groupname)
    //
    // This will return the group ID of a group called [groupname]. If the
    // group does not exist, a blank string with be returned.
    //
    function
    GetGroupID ($groupname) {
	// build the query
	$query = sprintf ("select id from groups where name='%s'", $groupname);
	$res = db_query ($query);

	// did this work?
	if (db_nof_results ($res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the group id
        $tmp = db_fetch_results ($res);
	return $tmp[0];
    }

    //
    // IsMod ($userid)
    //
    // This will return non-zero if user $userid is a moderator, otherwise zero.
    //
    function
    IsMod ($userid) {
	// check whether we are a moderator by use of groups
	$query = sprintf ("select groupmembers.userid from mods inner join groupmembers on mods.userid=groupmembers.groupid where (mods.flags and 1) and groupmembers.userid=%s", $userid);
	$res = db_query ($query);
	if (db_nof_results (db_query ($query))) {
	    // we're a moderator already
	    return 1;
	}

	// browse all forums	
	$query = sprintf ("select id from mods where userid=%s and not (mods.flags and 1) limit 1",$userid);
	return db_nof_results (db_query ($query));
    }

    //
    // IsCategoryMod ($userid)
    //
    // This will return non-zero if user $userid is a category moderator,
    // otherwise zero.
    //
    function
    IsCategoryMod ($userid) {
	// check whether we are a moderator by use of groups
	$query = sprintf ("select groupmembers.userid from catmods inner join groupmembers on catmods.userid=groupmembers.groupid where (catmods.flags and 1) and groupmembers.userid=%s", $userid);
	$res = db_query ($query);
	if (db_nof_results (db_query ($query))) {
	    // we're a moderator already
	    return 1;
	}

	// browse all forums	
	$query = sprintf ("select id from catmods where userid=%s limit 1",$userid);
	return db_nof_results (db_query ($query));
    }

    //
    // BuildIconList ()
    //
    // This will build the icon list for posting new topics or replies.
    //
    function
    BuildIconList() {
	global $CONFIG, $SKIN_VALUE;

	// grab the generic icon template
	$icon_template = addslashes (GetSkinTemplate ("posticon_firstitem"));
	$icon_newtemplate = addslashes (GetSkinTemplate ("posticon_item"));
	$icon_newline = addslashes (GetSkinTemplate ("posticon_newline"));
	$result = "";

	// construct the icon list
	for ($no = 1; $no < $CONFIG["nof_icons"]; $no++) {
	    eval ("\$tmp = stripslashes (\"" . $icon_template . "\");");
	    $result .= $tmp;
	    if ((($no % $SKIN_VALUE["posticons_per_line"]) == 0) and ($no != 0)) {
		$result .= $icon_newline;
	    }
	    $icon_template = $icon_newtemplate;
	}

	return $result;
    }

    //
    // RefreshLogins()
    //
    // This will refresh the list of currently logged-in users.
    //
    function
    RefreshLogins() {
	global $CONFIG, $ipaddress;

        // now, update the online database. are we logged in?
        if ($GLOBALS["logged_in"] != 0) {
	    // yes. use the real account id
	    $accountid = $GLOBALS["userid"];
        } else {
	    // no. use zero
	    $accountid = 0;
        }

	// got login flags?
	if ($GLOBALS["login_flags"] == "") {
	    // no. default to none
	    $GLOBALS["login_flags"] = 0;
	}

        // are we already in this list?
        $query = sprintf ("select id from curusers where accountid=%s and ipaddr='%s'", $accountid, $ipaddress);
        $res = db_query ($query);

        // any results?
        if (db_nof_results ($res) == 0) {
            // no. insert our record
            $query = sprintf ("insert into curusers values (NULL,%s,now(),'%s',%s)",$accountid,$ipaddress,$GLOBALS["login_flags"]);
        } else {
	    // update our record
	    $tmp = db_fetch_results ($res);
	    $query = sprintf ("update curusers set timestamp=now() where id=%s",$tmp[0]);
        }
        db_query ($query);

        // delete all inactive members
        $query = sprintf("delete from curusers where now()-timestamp>%s", $CONFIG["online_timeout"]);
        db_query ($query);
    }

    //
    // HandleLogin ($username, $password)
    //
    // This will handle the setting of a cookie and current online status.
    //
    function
    HandleLogin ($username, $password) {
	global $authid, $ipaddress;

	// yup. verify the username and password
	if (VerifyPassword ($username, $password) == 0) {
	    // it was not correct. complain
	    ShowHeader ("error_accessdenied");
	    print GetSkinTemplate ("error_accessdenied");
	    exit;
	}

        // it was correct. the user is nice, give him a cookie :)
        $newid = $username . ":" . $password;

	// do we need a new cookie?
	if ($newid != $authid) {
	    // yes, use it
            SetCookie ("authid", $newid, time() + 3600);
	}

	// yes. get rid of the guest login
	$query = sprintf ("delete from curusers where accountid=0 and ipaddr='%s'", $ipaddress);
	db_query ($query);

	// refresh the login list
	RefreshLogins();
    }

    //
    // IsSuperMod ($userid)
    //
    // This will return non-zero if user $userid is a super moderator,
    // otherwise zero.
    //
    function
    IsSuperMod ($userid) {
	// browse all forums	
	$query = sprintf ("select supermods from categories");
	$res = db_query ($query);

	// check them all
	while ($result = db_fetch_results ($res)) {
	    // is the user a mod here?
	    $smods = explode (",", $result[0]);
	    while (list (, $smodid) = each ($smods)) {
		// is the user a mod here?
		if ($smodid == $userid) {
		    // yes, the user is a super moderator
		    return 1;
		}
	    }
	}

	// the user is not a super moderator	
	return 0;
    }

    //
    // NotifyUsers ($forumid, $threadid, $messageid)
    //
    // This will notify all users in the notification list of forum $forumid
    // that user $GLOBALS["userid"] has created a new thread ($threadid != 0)
    // or a new post ($messageid != 0).
    //
    function
    NotifyUsers ($forumid, $threadid, $messageid) {
	global $CONFIG;

	// any users to notify?
	$query = sprintf ("select userid,flags from notify where forumid=%s", $forumid);
	$res = db_query ($query);
	if (db_nof_results ($res) == 0) {
	    // no. just get out of here
	    return;
	}

	// build the list of users to notify
	while ($result = db_fetch_results ($res)) {
	   // is this a group?
	   if (($result[1] & FLAG_USERLIST_GROUP) != 0) {
		// yes. add all members
		$query = sprintf ("select userid from groupmembers where groupid=%s", $result[0]);
		$res2 = db_query ($query);
		while ($tmp = db_fetch_results ($res2)) {
		    // add this user
		    $notifylist[$tmp[0]] = "!";
		}
	   } else {
		// no. just add this user
		$notifylist[$result[0]] = "!";
           }
	}

	// grab the thread title
	$query = sprintf ("select title from threads where id=%s", $threadid);
	$res2 = db_query ($query); $tmp = db_fetch_results ($res2);
	$threadtitle = $tmp[0];

	// there are. carve up the email
	if ($messageid == 0) {
	    // grab the template
	    $tmp = GetSkinFields ("notify_newthread", "title,content");
	} else {
	    $tmp = GetSkinFields ("notify_newmessage", "title,content");
	}

	// grab this user's 
	$url = $CONFIG["forum_url"]; $forumtitle = $CONFIG["forumtitle"];
	$postusername = $GLOBALS["username"]; $postuserid = $GLOBALS["userid"];
	$subject = $tmp[0];

	// now, handle all users
	while (list ($id) = @each ($notifylist)) {
	    // is this the user himself who posted a reply?
	    if ($id != $GLOBALS["userid"]) {
	        // no. grab the username and email address
	        $query = sprintf ("select accountname,email from accounts where id=%s",$id);
	        $res2 = db_query ($query); $result2 = db_fetch_results ($res2);

	        // did this work?
	        if (db_nof_results ($res2) > 0) {
	   	    // yes. finish the email and send it
		    $username = $result2[0]; $email = $result2[1];
	            eval ("\$body = stripslashes (\"" . addslashes ($tmp[1]) . "\");");

		    Mail ($email, $subject, $body, "From: " . $CONFIG["admin_email"] . "\nContent-Type: text/html");
	        }
	    }
	}
    }

    //
    // PMBuildList()
    //
    // This will build the list of private messages we have not yet read.
    //
    function
    PMBuildList() {
	// are we logged in?
	if ($GLOBALS["logged_in"] == 0) {
	    // no. get out of here
	    return "";
	}

	// grab all unread private messages for this account
	$query = sprintf ("select id from privatemessages where userid=%s and not (flags and %s)", $GLOBALS["userid"], FLAG_PM_READ);
	$res = db_query ($query); $nofmessages = db_nof_results ($res);

	// did this return any results?
	if ($nofmessages > 0) {
	    // yes. we have new messages! build the template
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("pm_newlist")) . "\");");
	    return $tmp;
	}

	// no new messages for us
	return "";
    }

    //
    // ResolveUserIDFlags ($userid, $flags);
    //
    // This will resolve $userid and $flags to a usuable userid, which will be
    // returned. 0 will be returned if a group has no members.
    //
    function
    ResolveUserIDFlags ($userid, $flags) {
	// is this a group?
	if (($flags and FLAG_USERLIST_GROUP) != 0) {
	    // yes. get the very first userid
            $query = sprintf ("select userid from groupmembers where groupid=%s order by id asc limit 1", $userid);
            $res = db_query ($query); $tmp = db_fetch_results ($res);

            // did this yield any results?
            if (db_nof_results ($res) == 0) {
		// no. this group has no members
		return 0;
            }

            // return the member id
	    return $tmp[0];
        }

	// just return this user id
	return $userid;
    }

    //
    // GetFirstMod ($forumid)
    //
    // This will retrieve the first moderator of the $forumid forum and return
    // the user ID.
    //
    function
    GetFirstMod ($forumid) {
	global $CONFIG;

	// select the very first mod
	$query = sprintf ("select userid,flags from mods where forumid=%s order by id asc limit 1", $forumid);
	$res = db_query ($query); $tmp = db_fetch_results ($res);

        // did this give any results?
        if (db_nof_results ($res) == 0) {
            // no. perhaps the category mod then?
            $query = sprintf ("select catno from forums where id=%s", $forumid);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
            $catid = $tmp[0];

            // grab the first category mod
            $query = sprintf ("select userid,flags from catmods where forumid=%s order by id asc limit 1",$catid);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
            $userid = $tmp[0]; $flags = $tmp[1];
        } else {
            // yeppee, we've got the mod
            $userid = $tmp[0]; $flags = $tmp[1];
        }

	// did we receive any direct results?
        if (db_nof_results ($res) == 0) {
            // no. default to the person at the control panel
            $userid = $CONFIG["report_defaultid"];
            $flags = $CONFIG["report_defaultflags"];
        }

        // resolve this to a usuabe userid
        $resultid = ResolveUserIDFlags ($userid, $flags);
        if ($resultid != 0) { return $resultid; };

	// this did not work. return the default userid/flags
        return ResolveUserIDFlags ($CONFIG["report_defaultid"], $CONFIG["report_defaultflags"]);
    }

    //
    // SendPM ($destuserid, $subject, $body)
    //
    // This will actually send a private message to user with ID $destuserid.
    // The message sent will have subject $subject and body $body. This will
    // return zero on success or 1 if the user's message quota exceeded.
    //
    function
    SendPM ($destuserid, $subject, $body) {
	global $CONFIG;

	// does this user have too much unread messages by this user?
	$query = sprintf ("select count(id) from privatemessages where userid=%s and flags=flags and %s and senderid=%s", $destuserid, FLAG_PM_READ, $GLOBALS["userid"]);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$nofmsgs = $result[0];
	if (db_nof_results ($res) == 0) { $nofmsgs = 0; };
	if ($nofmsgs >= $CONFIG["pm_per_user"]) {
	    // yes. complain
	    return 1;
	}

	// okay, all looks OK. send the message!
	$query = sprintf ("insert into privatemessages values (NULL,%s,%s,'%s','%s',now(),0,'')",$destuserid,$GLOBALS["userid"],addslashes ($subject),addslashes ($body));
	db_query ($query);

	// all went ok
	return 0;
    }

    //
    // IsDoublePost ($body)
    //
    // This will check whether a post with body $body is a double post or not.
    // It will return non-zero if it is or zero if not.
    //
    function
    IsDoublePost ($body) {
	// grab the last post we ever made
	$query = sprintf ("select lastmessage from accounts where id=%s", $GLOBALS["userid"]);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$lastmsg = $result[0];

	if (($lastmsg != "") and ($lastmsg != 0)) {
	    // we have made a last post. grab it
	    $query = sprintf ("select post from posts where id=%s", $lastmsg);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    if (db_nof_results ($res) > 0) {
		// the message really exists. is it equal to the one we're
		// about to post?
		if ($result[0] == $body) {
		    // yes. return positive status
		    return 1;
		}
	    }
	}

	// the message is different
	return 0;
    }

    //
    // GetModPositions ($modtable, $accountid)
    //
    // This will return an array of all forums which user $accountid moderates.
    // Only table $modtable will be checked.
    //
    function
    GetModPositions ($modtable, $accountid) {
	// figure out which forums this user moderates	
	$query = sprintf ("select forumid from %s where userid=%s and !(flags&%s)", $modtable, $accountid, FLAG_USERLIST_GROUP);
	$res = db_query ($query);

	// add the forum to the list
	while ($result = db_fetch_results ($res)) {
	    $mod[$result[0]] = "!";
	}

	// figure out which forums this user moderates by use of groups
	$query = sprintf ("select forumid from %s inner join groupmembers on %s.userid=groupmembers.groupid and groupmembers.userid=%s where %s.flags&%s", $modtable, $modtable, $accountid, $modtable, FLAG_USERLIST_GROUP);
	$res = db_query ($query);

	// add these forums too
	while ($result = db_fetch_results ($res)) {
	    $mod[$result[0]] = "!";
	}

	// return the array
	return $mod;
    }

    //
    // GetForumsModded ($accountid)
    //
    // This will build a list of all forums user $accountid moderates.
    //
    function
    GetForumsModded ($accountid) {
	// get all moderator positions
        $mods = GetModPositions ("mods", $accountid);

	// get all category moderator positions
        $catmods = GetModPositions ("catmods", $accountid);

	// add all forums in these categories
	while (list ($catid) = @each ($catmods)) {
	    // get all forums in this category
	    $query = sprintf ("select id from forums where catno=%s", $catid);
	    $res = db_query ($query);
	    while ($tmp = db_fetch_results ($res)) {
		// add the forum
		$mods[$tmp[0]] = "!";
	    }
	}

	// return the array
	return $mods;
    }

    // refresh the current login list
    RefreshLogins();
 ?>
