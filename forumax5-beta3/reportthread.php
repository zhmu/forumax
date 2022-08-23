<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the thread information
    $query = sprintf ("select title,forumid,flags from threads where id=%s", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $threadtitle = CensorText ($result[0]); $forumid = $result[1];
    $threadflags = $result[2];

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // need to show the report page?
    if ($action == "") {
	// yes. grab the forum name
	$query = sprintf ("select name,catno from forums where id=%s", $forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$forumname = $result[0]; $catid = $result[1];

        // do we have a category id?
        if ($catid != 0) {
            // yes. grab the category name
            $query = sprintf ("select name from categories where id=%s", $catid);
            $res = db_query ($query); $tmp = db_fetch_results ($res);
            $cat_title = $tmp[0];
        }

	// grab some generic values
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// build the page and show it
        ShowHeader ("reportthread_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("reportthread_page")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // need to actually report the thread?
    if ($action == "reportthread") {
	// yes. is it already reported or locked?
	$template = "";
        if (($threadflags & FLAG_THREAD_LOCKED) != 0) { $template = "error_reportlocked"; };
        if (($threadflags & FLAG_THREAD_REPORTED) != 0) { $template = "error_alreadyreported"; };
        if ($template != "") {
	    // yes. complain
            ShowHeader ($template);
	    echo GetSkinTemplate ($template);
            ShowFooter();
            exit;
        }

        // grab the appropriate mod
        $modid = GetFirstMod ($forumid);
        if ($modid != "") {
	    // build the private message
	    $tmp = GetSkinFields ("template_reportthread", "title,content");
	    $subject = $tmp[0]; $modusername = GetMemberNameSimple ($modid);
	    $url = $CONFIG["forum_url"];

	    // grab our account name
	    $destuserid = $GLOBALS["userid"];
	    $destusername = GetMemberNameSimple ($destuserid);

	    // grab the forum name
	    $query = sprintf ("select name from forums where id=%s", $forumid);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    $forumname = $result[0];

	    // build the message
            eval ("\$body = stripslashes (\"" . addslashes ($tmp[1]) . "\");");

	    // send it
	    $result = SendPM ($modid, $subject, $body);

	    // did all go well?
	    if ($result == 0) {
		// yes. it worked. flag the thread as being reported
		$query = sprintf ("update threads set flags=flags|%s where id=%s", FLAG_THREAD_REPORTED, $threadid);
		db_query ($query);

		// show the 'yay' page
                ShowHeader("reportthread_ok");
		eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("reportthread_ok")) . "\");");
		print $tmp;
		ShowFooter();
		exit;
	    }
        }

	// it did not work. complain
        ShowHeader("error_reportthread");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_reportthread")) . "\");");
	print $tmp;
	ShowFooter();
    }
 ?>
