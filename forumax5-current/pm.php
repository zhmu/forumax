<?php
    //
    // pm.php
    //
    // This will handle private messaging.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is private messaging enabled?
    if ($CONFIG["allow_pm"] == 0) {
	// yes. complain
	FatalError("error_pmdisabled");
    }

    // are we logged in?
    if ($GLOBALS["logged_in"] == 0) {
	// yes. complain
	FatalError("error_notloggedin");
    }

    // is a specific action given?
    if ($_REQUEST["action"] == "") {
        // no. grab all private messages
	$query = sprintf ("SELECT id,subject,senderid,flags,timestamp FROM privatemessages WHERE userid='%s' ORDER BY id DESC", $GLOBALS["userid"]);
	$res = db_query ($query); $VAR["nofmessages"] = db_nof_results ($res);

	// now, add all entries
	while (list ($VAR["messageid"], $VAR["messagetitle"], $VAR["senderid"], $flags, $VAR["messagetime"]) = db_fetch_results ($res)) {
	    // get the sender's username
	    $VAR["sendername"] = GetMemberName ($VAR["senderid"]);

	    // is this message read?
	    if (($flags & FLAG_PM_READ) != 0) {
		// yes. mark the thingy as read
	 	$VAR["readunread"] = InsertSkinVars (GetSkinTemplate ("pm_read"));
	    } else {
		// no. mark the thingy as unread
	 	$VAR["readunread"] = InsertSkinVars (GetSkinTemplate ("pm_unread"));
	    }

	    // build the list
	    $VAR["pmlist"] .= InsertSkinVars (GetSkinTemplate ("pm_entry"));
	}

	// build the page
	ShowForumPage("pm_overview");
	exit;
    }

    // need to read a message
    if ($_REQUEST["action"] == "readmessage") {
	// yes. is a valid message id given?
        $messageid = trim (preg_replace ("/\D/", "", $_REQUEST["messageid"]));
	if ($messageid == "") {
	    // no. complain
	    FatalError ("error_badrequest");
	}
	$VAR["messageid"] = $messageid;

	// grab the message
	$query = sprintf ("SELECT senderid,subject,message,timestamp FROM privatemessages WHERE id='%s' AND userid='%s'", $messageid, $userid);
	$res = db_query ($query);
	list ($VAR["senderid"], $VAR["subject"], $VAR["message"], $VAR["timestamp"]) = db_fetch_results ($res);

	// did we have any results?
	if (db_nof_results ($res) == 0) {
	    // no. complain
	    FatalError("error_nosuchmessage");
	}

	// mark the message as read
	$query = sprintf ("UPDATE privatemessages SET flags=flags|%s WHERE id='%s'", FLAG_PM_READ, $messageid);
	db_query ($query);

	// build the variables
	$VAR["sendername"] = GetMemberName ($VAR["senderid"]);
	$VAR["accountid"] = $GLOBALS["userid"];
	$VAR["accountname"] = GetMemberName ($GLOBALS["userid"]);
	$VAR["message"] = nl2br (ApplySmilies (CensorText ($VAR["message"])));

	// display the page
	ShowForumPage("pm_readmessage");
	exit;
    }

    // need to compose a new message?
    if ($_REQUEST["action"] == "compose") {
	// yes. is an user id given?
	if ($destid != "") {
	    // yes. get the member name
	    $VAR["destusername"] = GetMemberNameSimple ($destid);
	}

	// build the page
	ShowForumPage("pm_compose");
	exit;
    }

    // need to actually send the message?
    if ($_REQUEST["action"] == "docompose") {
	// yes. destroy any HTML from the fields
	$subject = trim (strip_tags ($_REQUEST["subject"]));
	$the_message = trim (strip_tags ($_REQUEST["the_message"]));
	$to = trim (strip_tags ($_REQUEST["to"]));

	// are all fields logged in?
	if (($to == "") or ($subject == "") or ($the_message == "")) {
	    // no. complain
	    FatalError ("error_emptyfields");
	}

	// send the messages to everyone on the list
	$userlist = explode (",", $to);
	while (list (, $to) = each ($userlist)) {
	    // look up the destination user id
	    $destuserid = GetMemberID (trim ($to));

	    // did this yield any results?
	    if ($destuserid == "") {
		// no. complain
		FatalError ("error_nosuchuser");
	    }

	    // send the message
	    $result = SendPM ($destuserid, $subject, $the_message);

	    // did we have too much unread messages?
	    if ($result == 1) {
		// yes. complain
		$VAR["destusername"] = $to;
		FatalError ("error_mailboxfull");
	    }
	}

	// the message has successfully been sent
	ShowForumPage("pm_composeok");
	exit;
    }

    // need to delete a message?
    if ($_REQUEST["action"] == "delete") {
	// yes. do it
	$messageid = preg_replace ("/\D/", "", $_REQUEST["messageid"]);
	$query = sprintf ("DELETE FROM privatemessages WHERE id='%s' AND userid='%s'", $messageid, $GLOBALS["userid"]);
	db_query ($query);

	// all went ok. show the 'yay' page
	ShowForumPage("pm_deleteok");
	exit;
    }

    // need to delete multiple messages?
    if ($_REQUEST["action"] == "deletemulti") {
	// yes. browse them all
	while (list ($id) = @each ($_REQUEST["delete"])) {
	    // get rid of this message
	    $id = preg_replace ("/\D/", "", $id);
	    $query = sprintf ("DELETE FROM privatemessages WHERE id='%s' AND userid='%s'", $id, $GLOBALS["userid"]);
	    db_query ($query);
	}

	// all went ok. show the 'yay' page
	ShowForumPage("pm_deleteok");
	exit;
    }

    // need to reply to a message?
    if ($_REQUEST["action"] == "reply") {
	// yes. is a valid message id given?
        $messageid = trim (preg_replace ("/\D/", "", $_REQUEST["messageid"]));
	if ($messageid == "") {
	    // no. complain
	    FatalError ("error_badrequest");
	}

	// grab the message information
	$query = sprintf ("SELECT senderid,subject,message FROM privatemessages WHERE id='%s' AND userid='%s'", $messageid, $userid);
	list ($senderid, $VAR["subject"], $msg) = db_fetch_results (db_query ($query));
	$VAR["to"] = GetMemberNameSimple ($senderid);

	// build the reply message
	$tmp = explode ("\n", $msg); $VAR["message"] = "";
	while (list (, $line) = each ($tmp)) {
	    $VAR["message"] .= GetSkinTemplate ("pm_reply_line") . htmlspecialchars ($line) . "\n";
	}

	// build the page
	ShowForumPage("pm_reply");
	exit;
    }
 ?>
