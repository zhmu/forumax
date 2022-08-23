<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // need to show the 'new topic' page?
    if ($action == "") {
        // yup. grab the thread information
	$query = sprintf ("select name,catno from forums where id=%s", $forumid);
	$res = db_query ($query); $result = db_fetch_results ($res);

	// fill in the fields
	$forumname = $result[0]; $catid = $result[1];
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];
	$iconlist = BuildIconList();

	// do we have a category id?
	if ($catid != 0) {
	    // yes. grab the category name
	    $query = sprintf ("select name from categories where id=%s", $catid);
 	    $res = db_query ($query); $tmp = db_fetch_results ($res);
 	    $cat_title = $tmp[0];
        }

	// build the page and show it
        ShowHeader("newtopicpage");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("newtopicpage")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // need to actually post the new topic?
    if ($action == "newtopic") {
	// yes. render any HTML from the thread title useless
	$the_subject = preg_replace ("/\</", "&lt;", $the_subject);
	$the_subject = preg_replace ("/\>/", "&gt;", $the_subject);

	// is this a double post (attempt to post the same message again) ?
	if (IsDoublePost ($the_message) != 0) {
	    // yes. lie and say we posted it (it's a double post)
	    ShowHeader("newtopicokpage");

	    // build the page and show it
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("newtopicokpage")) . "\");");
	    print $tmp;

	    ShowFooter();
	    exit;
	}

	// insert the new thread
	$query = sprintf ("insert into threads values (NULL,%s,'%s',%s,0,now(),%s,0,%s,'',0,0)",$forumid,$the_subject,$icon_no,$GLOBALS["userid"],$GLOBALS["userid"]);
	db_query ($query);
	$threadid = db_get_insert_id();

	// build the flags
	$flags = 0;
	if ($f_sig != "") { $flags = $flags | FLAG_POST_SIG; };

	// create the post
	$query = sprintf ("insert into posts values (NULL,%s,%s,%s,now(),'%s','',0,%s,'%s',%s)",$GLOBALS["userid"],$forumid,$threadid,$the_message,$icon_no,$ipaddress, $flags);
	db_query ($query);
	$messageid = db_get_insert_id();

	// increment the forum post count
	$query = sprintf ("update forums set nofthreads=nofthreads+1,nofposts=nofposts+1,lastpost=now(),lastposterid=%s where id=%s",$GLOBALS["userid"],$forumid);
	db_query ($query);

	// increment the number of posts and lasting posting date of this user
	$query = sprintf ("update accounts set nofposts=nofposts+1,lastpost=now(),lastmessage=%s where id='%s'", $messageid, $GLOBALS["userid"]);
	db_query ($query);

	// send out notifications as required
	NotifyUsers ($forumid, $threadid, 0);

	// it worked. show the 'yay' page
        ShowHeader("newtopicokpage");

	// build the page and show it
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("newtopicokpage")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }
 ?>
