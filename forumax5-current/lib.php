<?php
    //
    // lib.php
    //
    // This contains a lot of generic, useful features.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // FLAG_ADMIN is the bit you need to be an admin
    define (FLAG_ADMIN, 1);

    // FLAG_MMOD is the bit you need to be a mega mod
    define (FLAG_MMOD, 2);

    // FLAG_DISABLED is the bit you need to be a disabled account
    define (FLAG_DISABLED, 4);

    // FLAG_HIDEMAIL is the bit you need to have your email address hidden
    define (FLAG_HIDEMAIL, 8);

    // FLAG_MASTER is the bit you need to have a master forum account. Can't
    // be set.
    define (FLAG_MASTER, 16);

    // FLAG_DENYPOST is the bit you need to in order to be denied posting.
    define (FLAG_DENYPOST, 32);

    // FLAG_COPPA is the bit that will indicate the account is below 13
    define (FLAG_COPPA, 64);

    // FLAG_DONTCENSOR is the bit that will disable censoring for you
    define (FLAG_DONTCENSOR, 128);

    // FLAG_AUTOSIG is the bit that will turn your signature automatically on
    define (FLAG_AUTOSIG, 256);

    // FLAG_SKIN_DEFAULT is the bit a skin needs to be the default skin
    define (FLAG_SKIN_DEFAULT, 1);

    // FLAG_THREAD_LOCKED is the bit that indicates a thread is locked
    define (FLAG_THREAD_LOCKED, 1);

    // FLAG_THREAD_REPORTED is the bit that indicates a thread has been reported
    define (FLAG_THREAD_REPORTED, 2);

    // FLAG_THREAD_STICKY is the bit that indicates a thread is sticky
    define (FLAG_THREAD_STICKY, 4);

    // FLAG_THREAD_POLL the bit that indicates a thread has a poll too
    define (FLAG_THREAD_POLL, 8);

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

    // FLAG_FORUM_ALLOWPOLLS is the bit that will allow polls
    define (FLAG_FORUM_ALLOWPOLLS, 64);

    // FLAG_FORUM_UNREGPOST is the bit that will allow unregistered posting
    define (FLAG_FORUM_UNREGPOST, 128);

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

    // FLAG_POST_NOSMILIES is the flag that indicates the user's smilies
    // should not be shown
    define (FLAG_POST_NOSMILIES, 2);

    // FLAG_USERLIST_GROUP is the flag an userlist needs in order to contain
    // a group
    define (FLAG_USERLIST_GROUP, 1);

    // FLAG_AVATAR_ADMINONLY is an avatar which is not user-selectable
    define (FLAG_AVATAR_ADMINONLY, 1);

    // FORUMAX_VERSION is the ForuMAX version. Don't touch this!
    define (FORUMAX_VERSION, "5.0 BETA #4");

    //
    // LoadSkin()
    //
    // This will load the skin as needed.
    //
    function
    LoadSkin ($forceflag=0) {
	global $SKIN_VALUE, $SKINCACHE;

	// are we logged in?
	if ($GLOBALS["logged_in"] != 0) {
	    // yes. do we have a preferred skin id?
	    $query = sprintf ("SELECT skinid FROM accounts WHERE id='%s'", $GLOBALS["userid"]);
	    list ($GLOBALS["skinid"]) = db_fetch_results (db_query ($query));

	    // is this skin zero?
	    if ($GLOBALS["skinid"] != 0) {
	        // no. does this skin exist?
		$query = sprintf ("SELECT id FROM skins WHERE id='%s'", $GLOBALS["skinid"]);
		if (db_nof_results (db_query ($query)) == 0) {
		    // no. use the default skin instead
		    $GLOBALS["skinid"] = 0;
		}
	    }
	} else {
	    // always use the default
	    $GLOBALS["skinid"] = 0;
	}

	// need to get the default skin?
	if ($GLOBALS["skinid"] == 0) {
	    // yes. figure out the default skin id
	    $query = sprintf ("SELECT id FROM skins WHERE (flags&" . FLAG_SKIN_DEFAULT . ")");
	    list ($GLOBALS["skinid"]) = db_fetch_results (db_query ($query));
	}

	// do we have a real default skin?
	if ($GLOBALS["skinid"] == "") {
	    // no. use the first skin we see
	    $query = sprintf ("SELECT id FROM skins LIMIT 1");
	    list ($GLOBALS["skinid"]) = db_fetch_results (db_query ($query));
	}

	// grab all skin values and dump them into $SKIN_VALUE[].
	$query = sprintf ("SELECT name,content FROM skinvars_%s", $GLOBALS["skinid"]);
	$res = db_query ($query);

        // now, copy them all to $SKIN_VALUE[]
        while (list ($name, $val) = db_fetch_results ($res)) {
	    // set the value
	    $SKIN_VALUE[$name] = $val;
        }

	// get rid of the cached skin templates...
	$SKINCACHE = array();
    }

    //
    // VerifyPassword ($username, $password, $showerror)
    //
    // This will verify the username and password, and set severnal
    // $GLOBALS[] values if it's correct. It returns zero if the password is
    // incorrect, otherwise non-zero. If $showerror is non-zero, it will
    // automatically display an error message and quit.
    //
    function
    VerifyPassword($username,$password,$showerror) {
	// get rid of any slashes and trailing whitespace
	$username = trim (stripslashes ($username));
	$password = trim (stripslashes ($password));

	// query the account information
	$query = sprintf ("SELECT password,flags,id,timediff,sig_option,activatekey FROM accounts WHERE accountname='%s'", $username);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// do we have an account record and is the password correct?
	if ((db_nof_results ($res) > 0) and ($password == $result[0])) {
	    // yes. the username and password are correct. is the account
	    // activated?
	    if ($result[5] != "") {
		// no. need to display an error?
		if ($showerror != 0) {
		    // yes. display an error and exit
		    FatalError ("error_accountnotactivated");
		} else {
		    // no. just return an error code
		    return 0;
		}
	    }

	    // is this account enabled?
	    if (($result[1] & FLAG_DISABLED) == 0) {
	        // yes. we are logged in!
	        $GLOBALS["logged_in"] = 1; $GLOBALS["username"] = $username;
	        $GLOBALS["password"] = $result[0]; $GLOBALS["flags"] = $result[1];
	        $GLOBALS["userid"] = $result[2]; $GLOBALS["timediff"] = $result[3];
		$GLOBALS["sig_option"] = $result[4];
		$GLOBALS["skinid"] = $result[5];

		// reload the skin
		LoadSkin();

	        // it was correct.
	        return 1;
	    } else {
		// no. return an error
		if ($showerror != 0) {
		    // display an error and exit
		    FatalError ("error_accountdisabled");
		}
		return 0;
	    }
	}

	// need to show an error?
	if ($showerror != 0) {
	    // yes. complain
	    FatalError ("error_badlogin");
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
	$query = sprintf ("SELECT %s FROM skin_%s WHERE name='%s'", $skinfield, $GLOBALS["skinid"], $name);
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
    // GetSkinTemplate ($name)
    //
    // This will retrieve template $name from the currently selected skin and
    // return it. It will cache all templates requested.
    //
    function
    GetSkinTemplate ($name) {
	global $SKINCACHE;

	// was this thing already cached?
	if ($SKINCACHE[$name] == "") {
	    // no. grab the skin and put it in the cache
	    list ($SKINCACHE[$name]) = GetSkinFields ($name, "content");

	    // return the skin
	    return $SKINCACHE[$name];
	}

	// return the cached entry
	return $SKINCACHE[$name];
    }

    //
    // GetSkinTitle ($name)
    //
    // This will retrieve the title of template $name from the currently
    // selected skin and return it.
    //
    function
    GetSkinTitle ($name) {
	list ($tmp) = GetSkinFields ($name, "title");
	return $tmp;
    }

    //
    // ShowHeader($template)
    //
    // This will show the generic forum header. The page will receive the title
    // and refresh URL's from $template.
    //
    function
    ShowHeader($template) {
	global $CONFIG, $SKIN_VALUE, $VAR;

	// was a template given?
	if ($template != "") {
	    // yes. grab the title and refresh URL
	    list ($title, $refreshurl) = GetSkinFields ($template, "title,refresh_url");
	}

	// show the HTML header
 ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
 <html><head><title><?php
    // is there no title selected?
    if ($title == "") {
	// no. just use the forum titles.
	$title = $CONFIG["forumtitle"];
    } else {
	// yes. use it, but prepend the forum title and a dash
	$title = $CONFIG["forumtitle"] . " - " . $title;
    }
    echo InsertSkinVars ($title); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript" type="text/javascript" src="lib.js"></script>
<link href="styles/skin<?php echo $GLOBALS["skinid"]; ?>.css" rel="Stylesheet" type="text/css">
<?php
	// do we have a refresh URL?
	if ($refreshurl != "") {
	    // yes. make sure our page refreshes
            printf ("<meta http-equiv=\"Refresh\" content=\"2; url=%s\">", InsertSkinVars ($refreshurl));
	}
?></head><body <?php echo $SKIN_VALUE["body_tags"]; ?>><?php
	// grab the username and forum title
	$VAR["username"] = $GLOBALS["username"];
	$VAR["forums_title"] = $CONFIG["forumtitle"];

	// are we logged in?
	$template = "header_";
	if ($GLOBALS["logged_in"] != 0) {
	    // yes. use the members template
	    $template .= "member";
	} else {
	    // no. use the visitor template
	    $template .= "visitor";
	}

	// build the private messages list
	$VAR["newpm"] = PMBuildList();

        // do we have a forum image?
        if ($forum_image == "") {
            // no. default to the one in the skin profile
            $VAR["forum_image"] = $SKIN_VALUE["default_image"];
        } else {
            // is this image in the form http:// ?
            if (!preg_match ("/^http\:\/\//", $forum_image)) {
                // no. append the "/images/" thingy
                $forum_image = $SKIN_VALUE["images_url"] . $forum_image;
            }
	    $VAR["forum_image"] = $forum_image;
        }

	echo InsertSkinVars (GetSkinTemplate ($template));
    }

    //
    // ShowFooter()
    //
    // This will show the generic forum footer.
    //
    function
    ShowFooter() {
	global $VAR;

	// are we logged in?
	$template = "footer_";
	if ($GLOBALS["logged_in"] != 0) {
	    // yes. use the members template
	    $template .= "member";
	} else {
	    // no. use the visitor template
	    $template .= "visitor";
	}

	// get the time
	$mtime = explode (" ", microtime());
	$VAR["buildtime"] = sprintf ("%f", ($mtime[1] + $mtime[0]) - $GLOBALS["startime"]);

	$VAR["VERSION"] = FORUMAX_VERSION;
	echo InsertSkinVars (GetSkinTemplate ($template));
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
	$query = sprintf ("SELECT id FROM mods WHERE forumid='%s' AND userid='%s' AND NOT (flags&%s) LIMIT 1",$forumid,$GLOBALS["userid"],FLAG_USERLIST_GROUP);

	// got any mods?
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we're a moderator here
	    return 1;
	}

	// figure out the forum category id
	$query = sprintf ("SELECT catno FROM forums WHERE id='%s'", $forumid);
	list ($catid) = db_fetch_results (db_query ($query));

	// grab the category moderators
	$query = sprintf ("SELECT id FROM catmods WHERE forumid='%s' AND userid='%s' LIMIT 1",$catid,$GLOBALS["userid"]);
	// got any mods?
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we're a moderator here
	    return 1;
	}

	// are we a moderator by use of a group?
	$query = sprintf ("SELECT groupmembers.userid FROM mods INNER JOIN groupmembers ON mods.userid=groupmembers.groupid WHERE (mods.flags&%s) AND groupmembers.userid='%s' AND mods.forumid='%s' LIMIT 1", FLAG_USERLIST_GROUP, $GLOBALS["userid"], $forumid);
	$res = db_query ($query);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we can moderate this
	    return 1;
	}

	// check whether we are a category moderator by use of a group
	$query = sprintf ("SELECT groupmembers.userid FROM catmods INNER JOIN groupmembers ON catmods.userid=groupmembers.groupid WHERE (catmods.flags&%s) AND groupmembers.userid='%s' AND catmods.forumid='%s' LIMIT 1", FLAG_USERLIST_GROUP, $GLOBALS["userid"], $catid);
	return (db_nof_results (db_query ($query)));
    }

    //
    // MatchVar ($val1, $val2, $var, $op)
    //
    // This will return $var if ($val1 $op $val2), otherwise ''.
    //
    function
    MatchVar ($val1, $val2, $var, $op) {	
	//echo "<u>$val1</u>==<u>$val2</u>&&<i>$var2</i><hr>";
	if ($op == "==") {
	    return ($val1 == $val2) ? $var : $var2;
	} else {
	    return ($val1 != $val2) ? $var : $var2;
	}
    }

    //
    // InsertSkinVars($text)
    //
    // This will insert the currently the skin variables in $text.
    //
    function
    InsertSkinVars($text) {
	global $SKIN_VALUE, $VAR;

	// replace all values we know of
	$tmp = preg_replace ("/\{((\S)*)\}/e", '$SKIN_VALUE["\\1"]', $text);

	// handle the $ references
	$tmp = preg_replace ("/\\$(\w*)/e", '$VAR["\\1"]', $tmp);

	// handle all [[]] thingies
	$tmp = preg_replace ("/\[\[(.*)(([=|\!])=)(.*)\&\&(.*)\]\]/esU", 'MatchVar("\\1","\\4","\\5","\\2")', $tmp);
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

	// do we have access because of an user list?
	$query = sprintf ("SELECT id FROM restricted WHERE forumid='%s' AND userid='%s' AND NOT (flags&%s) LIMIT 1", $forumid, $GLOBALS["userid"], FLAG_USERLIST_GROUP);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. we're in
	    return 1;
	}

	// check whether we have access by use of groups
	$query = sprintf ("SELECT groupmembers.userid FROM restricted INNER JOIN groupmembers ON restricted.userid=groupmembers.groupid WHERE (restricted.flags&%s) AND groupmembers.userid='%s' AND restricted.forumid='%s' LIMIT 1", FLAG_USERLIST_GROUP, $GLOBALS["userid"], $forumid);
	return db_nof_results (db_query ($query));
    }

    //
    // ApplyMaXCodes ($text, $flags)
    //
    // This will apply MaX codes to $text. $flags are the forum flags.
    //
    function
    ApplyMaXCodes ($text, $flags) {
	// standard bold, italic, underlines, big and small stuff
	$text = preg_replace ("/\[b\](.*)\[\/b\]/sU", stripslashes (GetSkinTemplate ("maxcode_bold")), $text);
	$text = preg_replace ("/\[u\](.*)\[\/u\]/sU", stripslashes (GetSkinTemplate ("maxcode_underline")), $text);
	$text = preg_replace ("/\[i\](.*)\[\/i\]/sU", stripslashes (GetSkinTemplate ("maxcode_italic")), $text);
	$text = preg_replace ("/\[big\](.*)\[\/big\]/sU", stripslashes (GetSkinTemplate ("maxcode_big")), $text);
	$text = preg_replace ("/\[small\](.*)\[\/small\]/sU", stripslashes (GetSkinTemplate ("maxcode_small")), $text);

	// links
	$text = preg_replace ("/\[email\](.*)\[\/email\]/sU", stripslashes (GetSkinTemplate ("maxcode_email")), $text);
	$text = preg_replace ("/\[url\](.*)\[\/url\]/sU", stripslashes (GetSkinTemplate ("maxcode_url")), $text);
	$text = preg_replace ("/\[url=(.*)\]((.)*)\[\/url\]/sU", stripslashes (GetSkinTemplate ("maxcode_exturl")), $text);

	// quote and code
	$text = preg_replace ("/(\[quote\])(.*)(\[\/quote\])/sU", stripslashes (GetSkinTemplate ("maxcode_quote")), $text);
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
	$text = str_replace ("<", "&lt;", $text);
	$text = str_replace (">", "&gt;", $text);
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
	    $text = str_replace ("<img", "&lt;img", $text);
	}

	// need to get rid of javascript?
	if (($flags & FLAG_FORUM_DENYEVILHTML) != 0) {
	    // yes. kill it
	    $text = str_replace ("javascript", "java-script", $text);

	    // grab the evil HTML tags
	    $tmp = str_replace ("\r", "", GetSkinTemplate ("evil_html_tags"));
	    $eviltags = explode ("\n", $tmp);
	    while (list (, $eviltag) = each ($eviltags)) {
		// get rid of this tag
		$text = str_replace ("<" . $eviltag, "&lt;$eviltag", $text);
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
	global $customfieldid, $CUSTOMCACHE;

	// is this user cached?
	if ($CUSTOMCACHE[$userid] != "") {
	    // yes. get rid of the dash and return the item
	    return preg_replace ("/^\-/", "", $CUSTOMCACHE[$userid]);
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
	    $query = sprintf ("SELECT id FROM customfields WHERE type=7 LIMIT 1");
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
	$query = sprintf ("SELECT extra%s FROM accounts WHERE id='%s'", $customfieldid, $userid);
	list ($tmp) = db_fetch_results (db_query ($query));

	// cache this status
	$CUSTOMCACHE[$userid] = "-" . $tmp;

	return $tmp;
    }

    //
    // GetMemberStatus ($userid);
    //
    // This will return the member status of user $userid. It was cache all
    // results.
    //
    function
    GetMemberStatus ($userid) {
	global $CONFIG, $customcache;

	// grab the custom status
	$tmp = GetCustomStatus ($userid);

	// did this yield any results?
	if ($tmp != "") {
	    // yes. return the status
	    return $tmp;
	}

	// grab the flags
	$query = sprintf ("SELECT flags FROM accounts WHERE id='%s'", $userid);
	$res = db_query ($query);

	// did this yield any results?
	if (db_nof_results ($res) == 0) {
	    // no. return the unknown status
	    $customcache[$userid] = "-" . $GLOBALS["unknown_title"];
	    return $GLOBALS["unknown_title"];
	}

	// yes. get the results
	list ($flags) = db_fetch_results ($res);

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
	global $MEMBERNAME_CACHE;

	// is this name cached?
	if ($MEMBERNAME_CACHE[$userid] != "") {
	    // yes. return the name
	    return $MEMBERNAME_CACHE[$userid];
	}

	// grab the username
	$query = sprintf ("SELECT accountname FROM accounts WHERE id='%s'",$userid);
	$res = db_query ($query); list ($name) = db_fetch_results ($res);
	$MEMBERNAME_CACHE[$userid] = $name;

	// did this work?
	if (db_nof_results ($res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the member name
	return $name;
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
	$query = sprintf ("SELECT name FROM groups WHERE id='%s'",$groupid);
	$group_res = db_query ($query);

	// did this work?
	if (db_nof_results ($group_res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the group name
        list ($tmp) = db_fetch_results ($group_res);
	return $tmp;
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
	$query = sprintf ("SELECT id FROM accounts WHERE accountname='%s'", $username);
	$res = db_query ($query);

	// did this work?
	if (db_nof_results ($res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the member id
        list ($tmp) = db_fetch_results ($res);
	return $tmp;
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
	$query = sprintf ("SELECT id FROM groups WHERE name='%s'", $groupname);
	$res = db_query ($query);

	// did this work?
	if (db_nof_results ($res) == 0) {
	    // no. return an empty string
	    return "";
	}

	// yes. return the group id
        list ($tmp) = db_fetch_results ($res);
	return $tmp;
    }

    //
    // IsMod ($userid)
    //
    // This will return non-zero if user $userid is a moderator, otherwise zero.
    //
    function
    IsMod ($userid) {
	// check whether we are a moderator
	$query = sprintf ("SELECT id FROM mods WHERE userid='%s' AND NOT (mods.flags&%s) LIMIT 1",$userid, FLAG_USERLIST_GROUP);

	if (db_nof_results (db_query ($query))) {
	    // we're a moderator already
	    return 1;
	}

	// check whether we are a moderator by use of groups
	$query = sprintf ("SELECT groupmembers.userid FROM mods INNER JOIN groupmembers ON mods.userid=groupmembers.groupid WHERE (mods.flags&%s) AND groupmembers.userid='%s'",FLAG_USERLIST_GROUP,$userid);
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
	$query = sprintf ("SELECT groupmembers.userid FROM catmods INNER JOIN groupmembers ON catmods.userid=groupmembers.groupid WHERE (catmods.flags&%s) AND groupmembers.userid='%s'", FLAG_USERLIST_GROUP, $userid);
	$res = db_query ($query);
	if (db_nof_results (db_query ($query))) {
	    // we're a moderator already
	    return 1;
	}

	// browse all forums	
	$query = sprintf ("SELECT id FROM catmods WHERE userid='%s' LIMIT 1",$userid);
	return db_nof_results (db_query ($query));
    }

    //
    // BuildIconList ()
    //
    // This will build the icon list for posting new topics or replies.
    //
    function
    BuildIconList() {
	global $CONFIG, $SKIN_VALUE, $VAR;

	// grab the generic icon template
	$icon_template = GetSkinTemplate ("posticon_firstitem");
	$result = ""; $icon_width_pct = round (100 / $CONFIG["nof_icons"]);

	// construct the icon list
	for ($VAR["no"] = 1; $VAR["no"] < $CONFIG["nof_icons"]; $VAR["no"]++) {
	    $result .= InsertSkinVars ($icon_template);
	    if ((($VAR["no"] % $SKIN_VALUE["posticons_per_line"]) == 0) and ($VAR["no"] != 0)) {
		$result .= InsertSkinVars (GetSkinTemplate ("posticon_newline"));
	    }
	    $icon_template = GetSkinTemplate ("posticon_item");
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
        $query = sprintf ("SELECT id FROM curusers WHERE accountid='%s'", $accountid, $ipaddress);
        $res = db_query ($query);

        // any results?
        if (db_nof_results ($res) == 0) {
            // no. insert our record
            $query = sprintf ("INSERT INTO curusers VALUES (NULL,'%s',now(),'%s','%s')",$accountid,$ipaddress,$GLOBALS["login_flags"]);
        } else {
	    // update our record
	    list ($tmp) = db_fetch_results ($res);
	    $query = sprintf ("UPDATE curusers SET timestamp=now() WHERE id='%s'", $tmp);
        }
        db_query ($query);

        // delete all inactive members
        $query = sprintf("DELETE FROM curusers WHERE now()-timestamp>%s", $CONFIG["online_timeout"]);
        db_query ($query);
	
	// get the number of online members
	$query = sprintf ("SELECT COUNT(id) FROM curusers");
	list ($numonline) = db_fetch_results (db_query ($query));
	if ($numonline > $CONFIG["max_online"]) {
	    // new record. update it
	    $query = sprintf ("UPDATE config SET content='%s' WHERE name='max_online'", $numonline);
	    db_query ($query);

	    // grab today's date
	    $CONFIG["max_online_timestamp"] = date ("l j M Y h:i A");
	    $query = sprintf ("UPDATE config SET content='%s' WHERE name='max_online_timestamp'", $CONFIG["max_online_timestamp"]);
	    db_query ($query);
	    $CONFIG["max_online"] = $numonline;
	}
    }

    //
    // HandleLogin ($username, $password)
    //
    // This will handle the setting of a cookie and current online status.
    //
    function
    HandleLogin ($username, $password) {
	global $ipaddress;

	// yup. verify the username and password
	VerifyPassword ($username, $password, 1);

        // it was correct. the user is nice, give him a cookie :)
        $newid = $username . ":" . $password;

	// do we need a new cookie?
	if ($newid != $_COOKIE["authid"]) {
	    // yes, use it
            SetCookie ("authid", $newid, time() + 3600);
	}

	// get rid of the guest login
	$query = sprintf ("DELETE FROM curusers WHERE accountid=0 AND ipaddr='%s'", $ipaddress);
	db_query ($query);

	// refresh the login list
	RefreshLogins();
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
	$query = sprintf ("SELECT userid,flags FROM notify WHERE forumid=%s", $forumid);
	$res = db_query ($query);
	if (db_nof_results ($res) == 0) {
	    // no. just get out of here
	    return;
	}

	// build the list of users to notify
	while (list ($userid, $flags) = db_fetch_results ($res)) {
	   // is this a group?
	   if (($flags & FLAG_USERLIST_GROUP) != 0) {
		// yes. add all members
		$query = sprintf ("SELECT userid FROM groupmembers WHERE groupid=%s", $userid);
		$res2 = db_query ($query);
		while (list ($userid) = db_fetch_results ($res2)) {
		    // add this user
		    $notifylist[$userid] = "!";
		}
	   } else {
		// no. just add this user
		$notifylist[$userid] = "!";
           }
	}

	// grab the thread title
	$query = sprintf ("SELECT title FROM threads WHERE id=%s", $threadid);
	list ($threadtitle) = db_fetch_results (db_query ($query));

	// carve up the email
	if ($messageid == 0) {
	    // grab the template
	    list ($subject, $body_templ) = GetSkinFields ("email_notifynewthread", "title,content");
	} else {
	    list ($subject, $body_templ) = GetSkinFields ("email_notifynewmessage", "title,content");
	}

	// grab this user's 
	$url = $CONFIG["forum_url"]; $forumtitle = $CONFIG["forumtitle"];
	$postusername = $GLOBALS["username"]; $postuserid = $GLOBALS["userid"];

	// now, handle all users
	while (list ($id) = @each ($notifylist)) {
	    // is this the user himself who posted a reply?
	    if ($id != $GLOBALS["userid"]) {
	        // no. grab the username and email address
	        $query = sprintf ("SELECT accountname,email FROM accounts WHERE id=%s",$id);
	        $res2 = db_query ($query);

	        // did this work?
	        if (db_nof_results ($res2) > 0) {
	   	    // yes. finish the email and send it
		    list ($VAR["username"], $VAR["email"]) = db_fetch_results ($res2);
		    $body = InsertSkinVars ($body_templ);

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
	global $VAR;

	// are we logged in?
	if ($GLOBALS["logged_in"] == 0) {
	    // no. get out of here
	    return "";
	}

	// grab all unread private messages for this account
	$query = sprintf ("SELECT COUNT(id) FROM privatemessages WHERE userid=%s AND NOT (flags&%s)", $GLOBALS["userid"], FLAG_PM_READ);
	list ($VAR["nofunreadmessages"]) = db_fetch_results (db_query ($query));

	// did this return any results?
	if ($VAR["nofunreadmessages"] > 0) {
	    // yes. we have new messages! build the template
	    return InsertSkinVars (GetSkinTemplate ("pm_newlist"));
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
	if (($flags & FLAG_USERLIST_GROUP) != 0) {
	    // yes. get the very first userid
            $query = sprintf ("SELECT userid FROM groupmembers WHERE groupid=%s ORDER BY id ASC LIMIT 1", $userid);
	    $res = db_query ($query);

            // did this yield any results?
            if (db_nof_results ($res) == 0) {
		// no. this group has no members
		return 0;
            }

            // return the member id
            list ($tmp) = db_fetch_results ($res);
	    return $tmp;
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
	$query = sprintf ("SELECT userid,flags FROM mods WHERE forumid=%s ORDER BY id ASC LIMIT 1", $forumid);
	$res = db_query ($query);

        // did this give any results?
        if (db_nof_results ($res) == 0) {
            // no. perhaps the category mod then?
            $query = sprintf ("SELECT catno FROM forums WHERE id=%s", $forumid);
            list ($catid) = db_fetch_results (db_query ($res));

            // grab the first category mod
            $query = sprintf ("SELECT userid,flags FROM catmods WHERE forumid=%s ORDER BY id ASC LIMIT 1",$catid);
	    list ($userid, $flags) = db_fetch_results (db_query ($query));
        } else {
            // yeppee, we've got the mod
	    list ($userid, $flags) = db_fetch_results ($res);
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
	$query = sprintf ("SELECT COUNT(id) FROM privatemessages WHERE userid=%s AND NOT (flags&%s) AND senderid=%s", $destuserid, FLAG_PM_READ, $GLOBALS["userid"]);
	list ($nofmsgs) = db_fetch_results (db_query ($query));
	$nofmsgs += 0;
	if ($nofmsgs >= $CONFIG["pm_per_user"]) {
	    // yes. complain
	    return 1;
	}

	// okay, all looks OK. send the message!
	$query = sprintf ("INSERT INTO privatemessages VALUES (NULL,%s,%s,'%s','%s',now(),0,'')",$destuserid,$GLOBALS["userid"],addslashes ($subject),addslashes ($body));
	db_query ($query);

	// all went ok
	return 0;
    }

    //
    // IsDoublePost ($body)
    //
    // This will check whether a post with body $body is a double post or not.
    // It will return the thread ID of the thread in which the original post
    // was if it is, or zero if not.
    //
    function
    IsDoublePost ($body) {
	// grab the last post we ever made
	$query = sprintf ("SELECT lastmessage FROM accounts WHERE id=%s", $GLOBALS["userid"]);
	list ($lastmsg) = db_fetch_results (db_query ($query));

	if (($lastmsg != "") and ($lastmsg != 0)) {
	    // we have made a last post. grab it
	    $query = sprintf ("SELECT post,threadid FROM posts WHERE id=%s", $lastmsg);
	    $res = db_query ($query); list ($post, $threadid) = db_fetch_results ($res);
	    if (db_nof_results ($res) > 0) {
		// the message really exists. is it equal to the one we're
		// about to post?
		if ($post == $body) {
		    // yes. return the thread id
		    return $threadid;
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
	$query = sprintf ("SELECT forumid FROM %s WHERE userid=%s AND NOT (flags&%s)", $modtable, $accountid, FLAG_USERLIST_GROUP);
	$res = db_query ($query);

	// add the forum to the list
	while ($result = db_fetch_results ($res)) {
	    $mod[$result[0]] = "!";
	}

	// figure out which forums this user moderates by use of groups
	$query = sprintf ("SELECT forumid FROM %s INNER JOIN groupmembers ON %s.userid=groupmembers.groupid AND groupmembers.userid=%s WHERE %s.flags&%s", $modtable, $modtable, $accountid, $modtable, FLAG_USERLIST_GROUP);
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
	    $query = sprintf ("SELECT id FROM forums WHERE catno='%s'", $catid);
	    $res = db_query ($query);
	    while ($tmp = db_fetch_results ($res)) {
		// add the forum
		$mods[$tmp[0]] = "!";
	    }
	}

	// return the array
	return $mods;
    }

    //
    // HandleRestrictedForum ($forumid)
    //
    // This will handle access to restricted forum $forumid.
    //
    function
    HandleRestrictedForum ($forumid) {
	// fetch the accountname and password
	$the_accountname = $_REQUEST["the_accountname"];
	$the_password = $_REQUEST["the_password"];

	// got an username and password?
	if (($the_accountname != "") and ($the_password != "")) {
	    // yes. do it
	    HandleLogin ($the_accountname, $the_password);
	}

	// does this forum even exist?
	$query = sprintf ("SELECT id FROM forums WHERE id='%s'", $forumid);
	if (db_nof_results (db_query ($query)) == 0) {
	    // no. complain
	    FatalError ("error_nosuchforum");
	}

	// is this forum restricted?
	$query = sprintf ("SELECT id FROM restricted WHERE forumid='%s' LIMIT 1",$forumid);
	if (db_nof_results (db_query ($query)) > 0) {
	    // yes. are we logged in?
	    if ($GLOBALS["logged_in"] == 0) {
		// no. request the user to log in
		FatalError ("page_restrictedlogin");
	    }

	    // we are logged in. should we be allowed access?
	    if (CanVisitRestrictedForum ($forumid) == 0) {
		// access is denied. complain
		FatalError ("error_restrictedenied");
	    }
	}
    }

    //
    // GetThreadForumID ($threadid)
    //
    // This will fetch the forum ID from thread $threadid and return it. If
    // the thread does not exist, an error will be shown.
    //
    function
    GetThreadForumID ($threadid) {
	// fetch the forum's id
	$query = sprintf ("SELECT forumid FROM threads WHERE id='%s'", $threadid);
	$res = db_query ($query); list ($forumid) = db_fetch_results ($res);

	// did this give any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	   FatalError ("error_nosuchthread");
	}

	// return the forum id
	return $forumid;
    }

    //
    // ShowForumPage ($template)
    //
    // This will show forum page $template, along with the header and footer.
    //
    function
    ShowForumPage ($template) {
	ShowHeader ($template);
	echo InsertSkinVars (GetSkinTemplate ($template));
	ShowFooter();
    }

    //
    // FatalError ($template)
    //
    // This will show fatal error template $template and quit.
    //
    function
    FatalError ($template) {
	// it's the same code for now
	ShowForumPage ($template);
	exit;
    }

    //
    // ApplySmilies ($text)
    //
    // This will apply the smilies to $text.
    //
    function
    ApplySmilies ($text) {
	global $SMILIES, $SKIN_VALUE;

	// have the smilies been loaded?
	if (!isset ($SMILIES)) {
	    // no. load them
	    $query = sprintf ("SELECT smilie,image FROM smilies");
	    $res = db_query ($query);
	    while (list ($smilie, $image) = db_fetch_results ($res)) {
		// is this smilie blank?
		if (($smilie != "") and ($image != "")) {
		    // no. add it to the list
		    $SMILIES[$smilie] = $image;
		}
	    }
	}

	// apply them all
	@reset ($SMILIES);
	while (list ($smilie, $image) = @each ($SMILIES)) {
	    $text = str_replace ($smilie, "<img src=\"" . $SKIN_VALUE["images_url"] . "/" . $image . "\">", $text);
	}

	return $text;
    }

    //
    // CensorText ($text)
    //
    // This will censor text $text and return the censored text.
    //
    function
    CensorText ($text) {
	global $CONFIG;

	// is censoring disabled for our account?
	if (($GLOBALS["flags"] & FLAG_DONTCENSOR) != 0) {
	    // yes. just return
	    return $text;
	}

	// get the censored words
	$words = explode (" ", $CONFIG["censored_words"]);

	// handle all words
	while (list (, $word) = each ($words)) {
	    // is this word blank?
	    $word = trim ($word);
	    if ($word != "") {
		// no. build the replacement
		$repl = "";
		for ($i = 0; $i < strlen ($word); $i++) { $repl .= "*"; }

	        // is this word between {}'s ?
	        if (preg_match ("/\{(.*)\}/", $word)) {
		    // yes. we need to match the whole word
	            $word = preg_replace ("/(^\{)|(\}$)/", "", $word);

		    // build the replacement
		    $repl = "";
		    for ($i = 0; $i < strlen ($word); $i++) { $repl .= "*"; }

		    $text = preg_replace ("/(\W)(" . $word . ")(\W)/i", "\\1" . $repl . "\\3", $text);
	        } else {
		    // no. build the replacement
		    $repl = "";
		    for ($i = 0; $i < strlen ($word); $i++) { $repl .= "*"; }

		    // match anything
		    $text = preg_replace ("/" . $word . "/i", $repl, $text);
		}
	    }
	}

	// return the censored text
	return $text;
    }

    //
    // BuildHopto() 
    //
    // This will build the Hop To list.
    //
    function
    BuildHopto() {
        global $CONFIG, $VAR;

	// we must ensure $VAR does now change, so restore it
	$VARSAVE = $VAR;

        // grab the categories
        $query = sprintf ("SELECT id,name FROM categories ORDER BY orderno ASC");
        $res = db_query ($query);
        while (list ($VAR["catid"], $VAR["catname"]) = db_fetch_results ($res)) {
	    // build the template
	    $hopto_list .= InsertSkinVars (GetSkinTemplate ("hopto_cat_sel"));

	    // grab the forums
	    $query = sprintf ("SELECT id,name FROM forums WHERE catno='%s' ORDER BY orderno ASC",$VAR["catid"]);
	    $res2 = db_query ($query);
	    while (list ($VAR["forumid"], $VAR["forumname"]) = db_fetch_results ($res2)) { 
		// build the template
		$hopto_list .= InsertSkinVars (GetSkinTemplate ("hopto_forum_sel"));
	    }
	}

	// restore $VAR
	$VAR = $VARSAVE;

	return $hopto_list;
    }

    //
    // BuildForumRestrictions ($forumid)
    //
    // This will build $htmlok, $maxok, $imgok, $pollok and $unregok according
    // the the forum settings of forum $forumid.
    //
    function
    BuildForumRestrictions ($forumid) {
	global $VAR;

	// grab the forum flags
	$query = sprintf ("SELECT flags FROM forums WHERE id='%s'", $forumid);
	list ($flags) = db_fetch_results (db_query ($query));

	// initially, deny it all
	$VAR["htmlok"] = "no"; $VAR["maxok"] = "no"; $VAR["imgok"] = "yes";
	$VAR["pollok"] = "no"; $VAR["unregok"] = "no";

	// now, turn things on according to the settings
	if (($flags & FLAG_FORUM_ALLOWHTML) != 0) { $VAR["htmlok"] = "yes"; };
	if (($flags & FLAG_FORUM_ALLOWMAX) != 0) { $VAR["maxok"] = "yes"; };
	if (($flags & FLAG_FORUM_NOIMAGES) != 0) { $VAR["imgok"] = "no"; };
	if (($flags & FLAG_FORUM_ALLOWPOLLS) != 0) { $VAR["pollok"] = "yes"; };
	if (($flags & FLAG_FORUM_UNREGPOST) != 0) { $VAR["unregok"] = "yes"; };
    }

    //
    // IsIPBanned()
    //
    // This will return zero if our IP is not banned, or non-zero if it is.
    //
    function
    IsIPBanned() {
	global $CONFIG, $ipaddress;

	// check for banned IP addresses
	$ips = explode (" ", $CONFIG["banned_ip"]);

	// check them all
	while (list (, $ip) = each ($ips)) {
	    // is this ip blank?
	    $ip = trim ($ip);
	    if ($ip != "") {
		// no. is it an exact match?
		list ($a, $b, $c, $d, $e) = explode (".", $ip);

		// valid one?
		if ($e == "") {
		    // yes. exact match?
		    if ($d != "") {
			// yes. does it match?
			if ($ipaddress == $ip) {
			    // yes. we're banned
			    return 1;
			}
		    } else {
			// no. partial match?
			if (preg_match ("/($ip)/", $ipaddress)) {
			    // yes. we're banned
			    return 1;
			}
		    }
		}
	    }
	}

	// we're not banned
	return 0;
    }

    //
    // IsEmailBanned ($address)
    //
    // This will return zero if email address $address is not banned, or
    // non-zero if it is.
    //
    function
    IsEmailBanned($address) {
	global $CONFIG;

	// check for banned email addresses
	$emails = explode (" ", $CONFIG["banned_email"]);

	// check them all
	while (list (, $email) = each ($emails)) {
	    // is this address blank?
	    $email = trim ($email);
	    if ($email != "") {
		// no. check the address
		list ($a, $b, $c) = explode ("@", $email);

		// valid one?
		if ($c == "") {
		    // yes. exact match?
		    if (($a != "") and ($b != "")) {
			// yes. does it match?
		        if (preg_match ("/^($name)$/i", $accountname)) {
			    // yes. we're banned
			    return 1;
			}
		    } else {
			// no. partial match?
			if (preg_match ("/($email)$/i", $address)) {
			    // yes. we're banned
			    return 1;
			}
	  	    }
		}
	    }
	}

	// we're not banned
	return 0;
    }

    //
    // IsAccountNameBanned ($accountname)
    //
    // This will return zero if account name $accountname is not banned, or
    // non-zero if it is.
    //
    function
    IsAccountNameBanned($accountname) {
	global $CONFIG;

	// check for banned account names
	$names = explode (" ", $CONFIG["banned_accountname"]);

	// check them all
	while (list (, $name) = each ($names)) {
	    // is this address blank?
	    $name = trim ($name);
	    if ($name != "") {
	        // is this word between {}'s ?
	        if (preg_match ("/\{(.*)\}/", $name)) {
		    // yes. we need to match the whole name
	            $name = preg_replace ("/(^\{)|(\}$)/", "", $name);

		    // match?
		    if (preg_match ("/^($name)$/i", $accountname)) {
			// yes. we are banned
			return 1;
		    }
		} else {
		    // partial match?
		    if (preg_match ("/($name)/i", $accountname)) {
			// yes. we are banned
			return 1;
		    }
		}
	    }
	}

	// we are not banned
	return 0;
    }

    //
    // ShowBaseForumPage ($template, $threadid, $forumid=0)
    //
    // This will show a page in which you take some action (eg. locking, sticky
    // etc). It will show template $template. It will retrieve the needed
    // information for thread $threadid or forum $forumid. If $threadid is zero,
    // $forumid will be used instead.
    //
    function
    ShowBaseForumPage ($template, $threadid, $forumid=0) {
	global $VAR;

	// is a valid thread id supplied?	
	if ($threadid != 0) {
	    // yes. grab the thread information
	    $query = sprintf ("SELECT forumid,title FROM threads WHERE id='%s'", $threadid);
	    $res = db_query ($query);
	    list ($VAR["forumid"], $VAR["threadtitle"]) = db_fetch_results ($res);
	    $VAR["threadtitle"] = CensorText ($VAR["threadtitle"]);

	    // did this give any results?
	    if (db_nof_results ($res) == 0) {
		// no. complain
		FatalError ("error_nosuchthread");
	    }
	} else {
	    // no. use the forum id, then
	    $VAR["forumid"] = $forumid;
	}

        // is a valid forum ID given?
	if ($VAR["forumid"] != 0) {
	    // yes. check the forum access
	    HandleRestrictedForum ($VAR["forumid"]);

	    // grab the forum name, image and category
	    $query = sprintf ("SELECT name,image,catno FROM forums WHERE id='%s'", $VAR["forumid"]);
	    $res = db_query ($query);
	    list ($VAR["forumname"], $VAR["forum_image"], $VAR["catid"]) = db_fetch_results ($res);

	    // did this give any results?
	    if (db_nof_results ($res) == 0) {
		// no. complain
		FatalError ("error_nosuchforum");
	    }

	    // do we have a category id?
	    if ($VAR["catid"] != 0) {
		// yes. grab the category name
		$query = sprintf ("SELECT name FROM categories WHERE id='%s'", $VAR["catid"]);
		list ($VAR["cat_title"]) = db_fetch_results (db_query ($query));
	     }
	}

	// get the authentication information as needed
	$VAR["the_accountname"] = $GLOBALS["username"];
	$VAR["the_password"] = $GLOBALS["password"];

	// show the page
	ShowForumPage($template);
    }

    // default to not logged in
    $GLOBALS["logged_in"] = 0;

    // are we logged in?
    if ($_COOKIE["authid"] != "") {
	// yes. verify the identity
	$idcookie = explode (":", $_COOKIE["authid"]);

	if (VerifyPassword ($idcookie[0], $idcookie[1], 0) != 0) {
	    // yes. we are logged in!
	    $GLOBALS["logged_in"] = 1;
	} else {
	    // crumble the cookie
	    SetCookie ("authid", "", 0);
	}
    }

    // are we logged in?
    if ($GLOBALS["logged_in"] != 1) {
        // no. load the skin (would already be done)
        LoadSkin();
    }

    // grab all configuration values and dump them into $CONFIG[].
    global $CONFIG;
    $query = sprintf ("SELECT name,content FROM config");
    $res = db_query ($query);

    // now, copy them all to $CONFIG[]
    while (list ($name, $content) = db_fetch_results ($res)) {
	// set the value
	$CONFIG[$name] = $content;
    }

    // is the ForuMAX database version correct?
    if (($CONFIG["forumax_version"] != FORUMAX_VERSION) and ($GLOBALS["disable_vcheck"] == "")) {
	// no. complain
	die ("Sorry, but your database version does not match your ForuMAX version.");
    }

    // grab the ip address
    $ipaddress = getenv ("REMOTE_ADDR");

    // refresh the current login list
    RefreshLogins();

    // get the time
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $GLOBALS["startime"] = $mtime[1] + $mtime[0];

    // are the boards closed?
    if (($CONFIG["bb_closed"] != 0) and ($GLOBALS["in_control_panel"] == "")) {
	// yes. grab the reason
	$VAR["reason"] = $CONFIG["bb_close_reason"];

	// show the message
	FatalError ("error_boardclosed");
    }

    // are we in the control panel?
    if ($GLOBALS["in_control_panel"] == "") {
	// no. is our IP address banned?
	if (IsIPBanned() != 0) {
	    // yes. complain
	    FatalError ("error_ipbanned");
	}
    }
 ?>
