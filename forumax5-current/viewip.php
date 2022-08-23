<?php
    //
    // viewip.php
    //
    // This will display the IP address of a certain post.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // is a post id given?
    $postid = trim (preg_replace ("/\D/", "", $_REQUEST["postid"]));
    if ($postid == "") {
	// no. quit
	FatalError ("error_badrequest");
    }

    // get the thread and forum id
    $query = sprintf ("SELECT threadid,forumid FROM posts WHERE id='%s'",$postid);
    $res = db_query ($query); list ($threadid, $forumid) = db_fetch_results ($res);
    $VAR["threadid"] = $threadid;
    if (db_nof_results ($res) == 0) {
	// this failed. complain
	FatalError("error_nosuchpost");
    }

    // need to show the 'edit title' page?
    if ($_REQUEST["action"] == "") {
	// yes. show the page
	$VAR["postid"] = $postid;
        ShowBaseForumPage ("viewip_page", $threadid, $forumid);
	exit;
    }

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // handle forum restrictions, if any
    HandleRestrictedForum ($forumid);

    // do we need to be an admin or mod?
    if ($CONFIG["ip_log"] != 3) {
	// yes. do we need to be an admin?
	if ($CONFIG["ip_log"] == 1) {
	    // yes. are we one?
	    if (($GLOBALS["flags"] & FLAG_ADMIN) == 0) {
		// no. complain
		FatalError("error_accessdenied");
	    }
	} else {
            // no. we need to be a forum mod. are we one?
            if (IsForumMod ($forumid) == 0) {
		// no. complain
		FatalError("error_accessdenied");
	    }
	}
    }

    // need to actually edit the title?
    if ($_REQUEST["action"] == "viewip") {
	// yes. build the query
	$query = sprintf ("SELECT ipaddr FROM posts WHERE id=%s",$postid);
	$res = db_query ($query);
	list ($VAR["dest_ipaddress"]) = db_fetch_results ($res);

	// resolve the ip address
	$VAR["dest_hostname"] = @gethostbyaddr ($dest_ipaddress);

	// it worked. show the 'yay' page
        ShowBaseForumPage ("viewipresult_page", $threadid, $forumid);
    }
 ?>
