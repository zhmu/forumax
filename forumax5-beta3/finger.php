<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is an account id given?
    if ($accountid != "") {
        // yes. grab all custom fields visible
        $query = sprintf ("select id,name,type from customfields where visible!=0");
	$res = db_query ($query);
	while ($result = db_fetch_results ($res)) {
  	    // build the extra fields array	
	    $extra_name[$result[0]] = $result[1];
	    $extra_type[$result[0]] = $result[2];
	    $extra_query .= ",extra" . $result[0];
        }

        // grab the user's information
	$timezone = $GLOBALS["timediff"] + $CONFIG["timezone"];
	if ($timezone == "") { $timezone = 0; };
        $query = sprintf ("select accountname,flags,nofposts,email,date_format(joindate,'%s'),date_format(from_unixtime(unix_timestamp(lastpost)+%s),'%s'),lastmessage%s from accounts where id=%s", $CONFIG["joindate_timestamp_format"],$timezone,$CONFIG["post_timestamp_format"],$extra_query,$accountid);
        $res = db_query ($query); $account_result = db_fetch_results ($res);

        // build the variables
        $accountname = $account_result[0]; $nofposts = $account_result[2];
        $status = GetMemberStatus ($accountid);
        $joindate = $account_result[4]; $lastpost = $account_result[6];
        $timestamp = $account_result[5];

        // is the user open about their email address?
        if ($account_result[1] & FLAG_HIDEMAIL) {
	    // no. don't show it
	    $email = GetSkinTemplate ("finger_emailhidden");
        } else {
	    // yes. build it
	    $email = $account_result[3];
	    eval ("\$email = stripslashes (\"" . addslashes (GetSkinTemplate ("finger_email")) . "\");");
        }

        // does this last post still exist?
        $query = sprintf ("select threadid from posts where id='%s'", $lastpost);
        $res = db_query ($query); $tmp = db_fetch_results ($res);
        if (db_nof_results ($res) == 0) {
	    // no. it's unknown, or there never was one
	    $lastpost = GetSkinTemplate ("finger_nolastpost");
        } else {
	    // yes. grab the thread title
	    $threadid = $tmp[0];
	    $query = sprintf ("select title from threads where id=%s", $tmp[0]);
	    $res = db_query ($query); $tmp = db_fetch_results ($res);
	    $threadname = $tmp[0];
	    eval ("\$lastpost = stripslashes (\"" . addslashes (GetSkinTemplate ("finger_lastpost")) . "\");");
        }

        // build the custom fields
        $i = 0;
        while (list ($id, $name) = @each ($extra_name)) {
	    $fieldname = $name; $fieldvalue = $account_result[$i + 7];
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("finger_viewcustom" . $extra_type[$id])) . "\");");
	    $customfields .= $tmp;
	    $i++;
        }

	// figure out which forums this user moderates	
	$mod = GetModPositions ("mods", $accountid);

	// list all moderated forums
	while (list ($forumid) = @each ($mod)) {
	    // get the forum id
	    $query = sprintf ("select name from forums where id=%s", $forumid);
	    $res2 = db_query ($query); $tmp = db_fetch_results ($res2);

	    // got any results?
	    if (db_nof_results ($res2) > 0) {
		// yes. list the info
		$forumname = $tmp[0];
	        eval ("\$forumsmodded .= stripslashes (\"" . addslashes (GetSkinTemplate ("finger_forum_mod")) . "\");");
	    }
	}

	// do we actually mod something?
	if ($forumsmodded == "") {
	    // no. show the 'nothing' template
	    eval ("\$forumsmodded = stripslashes (\"" . addslashes (GetSkinTemplate ("finger_nomod")) . "\");");
	}

	// get all category moderator positions
	$catmod = GetModPositions ("catmods", $accountid);

	// list all moderated categories
	while (list ($catid) = @each ($catmod)) {
	    // get the forum id
	    $query = sprintf ("select name from categories where id=%s",$catid);
	    $res2 = db_query ($query); $tmp = db_fetch_results ($res2);

	    // got any results?
	    if (db_nof_results ($res2) > 0) {
		// yes. list the info
		$forumname = $tmp[0];
	        eval ("\$catsmodded .= stripslashes (\"" . addslashes (GetSkinTemplate ("finger_cat_mod")) . "\");");
	    }
	}

	// do we actually mod something?
	if ($catsmodded == "") {
	    // no. show the 'nothing' template
	    eval ("\$catsmodded = stripslashes (\"" . addslashes (GetSkinTemplate ("finger_nomod")) . "\");");
	}

        // show the page
        ShowHeader("fingerpage_account");
        eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("fingerpage_account")) . "\");");
        print $tmp;
        ShowFooter();
	exit;
    }

    // grab the group information
    $query = sprintf ("select name,description from groups where id=%s", $groupid);
    $res = db_query ($query); $result = db_fetch_results ($res);

    // okay, build the page
    $groupname = $result[0]; $groupdesc = $result[1];

    // get all members and add them
    $query = sprintf ("select accounts.id,accountname from accounts inner join groupmembers on accounts.id=groupmembers.userid and groupmembers.groupid=%s order by accounts.accountname asc", $groupid);
    $res = db_query ($query);
    while ($result = db_fetch_results ($res)) {
	$accountid = $result[0]; $accountname = $result[1];
        eval ("\$groupmembers .= stripslashes (\"" . addslashes (GetSkinTemplate ("finger_group_member")) . "\");");
    }

    // were there any actual group members?
    if ($groupmembers == "") {
	// no. switch the template
	$groupmembers = GetSkinTemplate ("finger_group_nomembers");
    }

    // show the page
    ShowHeader("fingerpage_group");
    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("fingerpage_group")) . "\");");
    print $tmp;
    ShowFooter();
 ?>
