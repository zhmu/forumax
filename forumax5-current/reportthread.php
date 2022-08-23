<?php
    //
    // reportthread.php
    //
    // This will report a thread to the moderator of the forum.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is the thread id valid?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. complain
	FatalError ("error_badrequest");
    }

    // need to show the report page?
    if ($_REQUEST["action"] == "") {
	// yes. grab some generic values
	$VAR["the_accountname"] = $GLOBALS["username"];
	$VAR["the_password"] = $GLOBALS["password"];

	// build the page and show it
	ShowBaseForumPage ("reportthread_page", $threadid, $forumid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // grab the thread information
    $query = sprintf ("SELECT title,forumid,flags FROM threads WHERE id='%s'", $threadid);
    list ($VAR["threadtitle"], $forumid, $threadflags) = db_fetch_results (db_query ($query));
    $VAR["forumid"] = $forumid;

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // need to actually report the thread?
    if ($_REQUEST["action"] == "reportthread") {
	// yes. is it already reported or locked?
	$template = "";
        if (($threadflags & FLAG_THREAD_LOCKED) != 0) { $template = "error_reportlocked"; };
        if (($threadflags & FLAG_THREAD_REPORTED) != 0) { $template = "error_alreadyreported"; };
        if ($template != "") {
	    // yes. complain
	    FatalError($template);
            exit;
        }

        // grab the appropriate mod
        $modid = GetFirstMod ($forumid);
        if ($modid != "") {
	    // build the private message
	    list ($subject, $body) = GetSkinFields ("email_reportthread", "title,content");
	    $VAR["modusername"] = GetMemberNameSimple ($modid);
	    $VAR["url"] = $CONFIG["forum_url"];

	    // grab our account name
	    $VAR["destuserid"] = $GLOBALS["userid"];
	    $VAR["destusername"] = GetMemberNameSimple ($destuserid);

	    // grab the forum name
	    $query = sprintf ("SELECT name FROM forums WHERE id='%s'",$forumid);
	    list ($VAR["forumname"]) = db_fetch_results (db_query ($query));

	    // build the message
            $body = InsertSkinVars ($body);

	    // send it
	    $result = SendPM ($modid, $subject, $body);

	    // did all go well?
	    if ($result == 0) {
		// yes. it worked. flag the thread as being reported
		$query = sprintf ("UPDATE threads SET flags=flags|%s WHERE id='%s'", FLAG_THREAD_REPORTED, $threadid);
		db_query ($query);

		// show the 'yay' page
		ShowForumPage("reportthread_ok");
		exit;
	    }
        }

	// it did not work. complain
	FatalError("error_reportthread");
    }
 ?>
