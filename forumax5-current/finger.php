<?php
    //
    // finger.php
    //
    // This will display information about a certain account or group.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is no account id or group id given?
    $accountid = trim (preg_replace ("/\D/", "", $_REQUEST["accountid"]));
    $groupid = trim (preg_replace ("/\D/", "", $_REQUEST["groupid"]));
    if (($accountid . $groupid) == "") {
	// no. quit
	FatalError ("error_badrequest");
    }

    // is an account id given?
    if ($accountid != "") {
        // yes. account id of zero?
	if ($accountid == 0) {
	    // yes. complain
	    FatalError("error_unreguser");
	}

	// grab all custom fields visible
        $query = sprintf ("SELECT id,name,type FROM customfields WHERE visible!=0");
	$res = db_query ($query);
	while (list ($id, $name, $type) = db_fetch_results ($res)) {
  	    // build the extra fields array	
	    $extra_name[$id] = $name;
	    $extra_type[$id] = $type;
	    $extra_query .= ",extra" . $id;
        }

        // grab the user's information
	$timezone = $GLOBALS["timediff"] + $CONFIG["timezone"] + 0;
        $query = sprintf ("SELECT accountname,flags,nofposts,email,DATE_FORMAT(joindate,'%s'),DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(lastpost)+%s),'%s'),lastmessage,DATE_FORMAT(birthday,'%s'),UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(birthday)%s FROM accounts WHERE id=%s", $CONFIG["joindate_timestamp_format"],$timezone,$CONFIG["post_timestamp_format"],$CONFIG["birthdate_timestamp_format"],$extra_query,$accountid);
        $res = db_query ($query);
	$account_result = db_fetch_results ($res);

        // build the variables
	list ($VAR["accountname"], $account_flags, $VAR["nofposts"], $VAR["email"], $VAR["joindate"], $VAR["timestamp"], $lastpost, $VAR["birthdate"]) = $account_result;
	$VAR["status"] = GetMemberStatus ($accountid);
	$VAR["age"] = (int)($account_result[8] / (365 * 3600 * 24));

        // is the user open about their email address?
        if (($account_flags & FLAG_HIDEMAIL) != 0) {
	    // no. don't show it
	    $VAR["email"] = GetSkinTemplate ("finger_emailhidden");
        } else {
	    // yes. build it
	    $VAR["email"] = $account_result[3];
	    $VAR["email"] = InsertSkinVars (GetSkinTemplate ("finger_email"));
        }

        // does this last post still exist?
        $query = sprintf ("SELECT threadid FROM posts WHERE id='%s'", $lastpost);
        $res = db_query ($query); list ($VAR["threadid"]) = db_fetch_results ($res);
        if (db_nof_results ($res) == 0) {
	    // no. it's unknown, or there never was one
	    $VAR["lastpost"] = GetSkinTemplate ("finger_nolastpost");
        } else {
	    // yes. grab the thread title
	    $query = sprintf ("SELECT title FROM threads WHERE id='%s'",$VAR["threadid"]);
	    list ($VAR["threadname"]) = db_fetch_results (db_query ($query));
	    $VAR["threadname"] = CensorText ($VAR["threadname"]);
	    $VAR["lastpost"] = InsertSkinVars (GetSkinTemplate ("finger_lastpost"));
        }

        // build the custom fields
        $i = 0;
        while (list ($id, $VAR["fieldname"]) = @each ($extra_name)) {
	    $VAR["fieldvalue"] = $account_result[$i + 9];
	    $VAR["customfields"] .= InsertSkinVars (GetSkinTemplate ("finger_viewcustom" . $extra_type[$id]));
	    $i++;
        }

	// figure out which forums this user moderates	
	$mod = GetModPositions ("mods", $accountid);

	// list all moderated forums
	while (list ($VAR["forumid"]) = @each ($mod)) {
	    // get the forum id
	    $query = sprintf ("SELECT name FROM forums WHERE id='%s'", $VAR["forumid"]);
	    $res2 = db_query ($query);
	    list ($VAR["forumname"]) = db_fetch_results ($res2);

	    // got any results?
	    if (db_nof_results ($res2) > 0) {
		$VAR["forumsmodded"] = InsertSkinVars (GetSkinTemplate ("finger_forum_mod"));
	    }
	}

	// do we actually mod something?
	if ($VAR["forumsmodded"] == "") {
	    // no. show the 'nothing' template
	    $VAR["forumsmodded"] = InsertSkinVars (GetSkinTemplate ("finger_nomod"));
	}

	// get all category moderator positions
	$catmod = GetModPositions ("catmods", $accountid);

	// list all moderated categories
	while (list ($VAR["catid"]) = @each ($catmod)) {
	    // get the forum id
	    $query = sprintf ("select name from categories where id='%s'",$VAR["catid"]);
	    $res2 = db_query ($query);
	    list ($VAR["forumname"]) = db_fetch_results ($res2);

	    // got any results?
	    if (db_nof_results ($res2) > 0) {
		// yes. list the info
	        $VAR["catsmodded"] .= InsertSkinVars (GetSkinTemplate ("finger_cat_mod"));
	    }
	}

	// do we actually mod something?
	if ($VAR["catsmodded"] == "") {
	    // no. show the 'nothing' template
	    $VAR["catsmodded"] = InsertSkinVars (GetSkinTemplate ("finger_nomod"));
	}

	// get the total forum post count
	$query = sprintf ("SELECT COUNT(id) FROM posts");
	list ($totalposts) = db_fetch_results (db_query ($query));
	$VAR["post_pct"] = sprintf ("%.2f", ($VAR["nofposts"] / $totalposts) * 100);

        // show the page
	ShowForumPage("fingerpage_account");
	exit;
    }

    // grab the group information
    $query = sprintf ("SELECT name,description FROM groups WHERE id='%s'", $groupid);
    $res = db_query ($query);
    list ($VAR["groupname"], $VAR["groupdesc"]) = db_fetch_results ($res);

    // get all members and add them
    $query = sprintf ("SELECT accounts.id,accounts.accountname FROM accounts INNER JOIN groupmembers ON accounts.id=groupmembers.userid AND groupmembers.groupid='%s' ORDER BY accounts.accountname ASC", $groupid);
    $res = db_query ($query);
    while (list ($VAR["accountid"], $VAR["accountname"]) = db_fetch_results ($res)) {
	$VAR["groupmembers"] .= InsertSkinVars (GetSkinTemplate ("finger_group_member"));
    }

    // were there any actual group members?
    if ($VAR["groupmembers"] == "") {
	// no. switch the template
	$VAR["groupmembers"] = GetSkinTemplate ("finger_group_nomembers");
    }

    // show the page
    ShowForumPage("fingerpage_group");
 ?>
