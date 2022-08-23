<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is private messaging enabled?
    if ($CONFIG["allow_pm"] == 0) {
	// yes. complain
        ShowHeader("error_pmdisabled");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_pmdisabled")) . "\");");
	ShowFooter();
	exit;
    }

    // are we logged in?
    if ($GLOBALS["logged_in"] == 0) {
	// yes. complain
        ShowHeader("error_notloggedin");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_notloggedin")) . "\");");
	ShowFooter();
	exit;
    }

    // is a specific action given?
    if ($action == "") {
        // no. just list the messages
        ShowHeader("pm_overview");

	// grab all private messages
	$query = sprintf ("select id,subject,senderid,flags,timestamp from privatemessages where userid=%s order by id desc", $GLOBALS["userid"]);
	$res = db_query ($query); $nofmessages = db_nof_results ($res);

	// grab the generic read and unread templates
	$template_read = GetSkinTemplate ("pm_read");
	$template_unread = GetSkinTemplate ("pm_unread");

	// now, add all entries
	$pmlist = "";
	while ($result = db_fetch_results ($res)) {
	    // build the entry
	    $messageid = $result[0]; $messagetitle = $result[1];
	    $senderid = $result[2]; $sendername = GetMemberName ($senderid);
	    $flags = $result[3]; $messagetime = $result[4];

	    // is this message read?
	    if (($flags & FLAG_PM_READ) != 0) {
		// yes. mark the thingy as read
	 	$readunread = $template_read;
	    } else {
		// no. mark the thingy as unread
	 	$readunread = $template_unread;
	    }
	    eval ("\$pmlist .= stripslashes (\"" . addslashes (GetSkinTemplate ("pm_entry")) . "\");");
	}

	// build the page
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("pm_overview")) . "\");");
	print $tmp;

        ShowFooter();
    }

    // need to read a message
    if ($action == "readmessage") {
	// yes. grab the message
	$query = sprintf ("select senderid,subject,message,timestamp from privatemessages where id=%s and userid=%s", $messageid, $userid);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// did we have any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
            ShowHeader("error_nosuchmessage");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_nosuchmessage")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}

	// mark the message as read
	$query = sprintf ("update privatemessages set flags = flags or %s where id=%s", FLAG_PM_READ, $messageid);
	db_query ($query);

	// build the page
	ShowHeader ("pm_readmessage");

	// build the variables
	$senderid = $result[0]; $sendername = GetMemberName ($senderid);
	$accountid = $GLOBALS["userid"]; $accountname = GetMemberName ($accountid);
	$subject = $result[1]; $message = nl2br ($result[2]);
	$timestamp = $result[3];

	// construct the page
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("pm_readmessage")) . "\");");
	print $tmp;

	// end the page
	ShowFooter();
	exit;
    }

    // need to compose a new message?
    if ($action == "compose") {
	// yes. build the page
	ShowHeader("pm_compose");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("pm_compose")) . "\");");
	ShowFooter();
	exit;
    }

    // need to actually send the message?
    if ($action == "docompose") {
	// yes. destroy any HTML from the fields
	$subject = trim (strip_tags ($subject));
	$message = trim (strip_tags ($message));
	$to = trim (strip_tags ($to));

	// are all fields logged in?
	if (($to == "") or ($subject == "") or ($message == "")) {
	    // no. complain
	    ShowHeader("error_emptyfields");
	    eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_emptyfields")) . "\");");
	    ShowFooter();
	    exit;
	}

	// look up the destination user id
	$destuserid = GetMemberID ($to);

	// did this yield any results?
	if ($destuserid == "") {
	    // no. complain
	    ShowHeader("error_nosuchuser");
	    eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_nosuchuser")) . "\");");
	    ShowFooter();
	    exit;
	}

	// send the message
        $result = SendPM ($destuserid, $subject, $message);

	// did we have too much unread messages?
	if ($result == 1) {
	    // yes. complain
	    ShowHeader("error_mailboxfull");
	    eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("error_mailboxfull")) . "\");");
	    ShowFooter();
	    exit;
	}

	// the message has successfully been sent
	ShowHeader("pm_composeok");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("pm_composeok")) . "\");");
	ShowFooter();
	exit;
    }

    // need to delete a message?
    if ($action == "delete") {
	// yes. do it
	$query = sprintf ("delete from privatemessages where id=%s and userid=%s", $messageid, $GLOBALS["userid"]);
	db_query ($query);

	// all went ok. show the 'yay' page
	ShowHeader("pm_deleteok");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("pm_deleteok")) . "\");");
	ShowFooter();
	exit;
    }

    // need to delete multiple messages?
    if ($action == "deletemulti") {
	// yes. browse them all
	while (list ($id) = @each ($delete)) {
	    // get rid of this message
	    $query = sprintf ("delete from privatemessages where id=%s and userid=%s", $id, $GLOBALS["userid"]);
	    db_query ($query);
	}

	// all went ok. show the 'yay' page
	ShowHeader("pm_deleteok");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("pm_deleteok")) . "\");");
	ShowFooter();
	exit;
    }

    // need to reply to a message?
    if ($action == "reply") {
	// yes. grab the message information
	$query = sprintf ("select senderid,subject,message from privatemessages where id=%s and userid=%s", $messageid, $userid);
	$res = db_query ($query); $result = db_fetch_results ($res);
	$to = GetMemberName ($result[0]); $subject = $result[1];

	// build the reply message
	$tmp = explode ("\n", $result[2]); $message = "";
	while (list (, $line) = each ($tmp)) {
	    $message .= GetSkinTemplate ("pm_reply_line") . $line . "\n";
	}

	// build the page
	ShowHeader("pm_reply");
	eval ("echo stripslashes (\"" . addslashes (GetSkinTemplate ("pm_reply")) . "\");");
	ShowFooter();
	exit;
    }
 ?>
