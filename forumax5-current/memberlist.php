<?php
    //
    // memberlist.php
    //
    // This will display a list of all forum members.
    //
    // $_REQUEST[] stuff needs work here.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // do we need to actually list the memberlist?
    if ($_REQUEST["action"] == "") {
	// yep. grab the accounts
	$query = sprintf ("SELECT COUNT(id) FROM accounts");
	list ($VAR["totalaccounts"]) = db_fetch_results (db_query ($query));

	// was an amount of accounts per screen given?
	$accounts = trim (preg_replace ("/\D/", "", $_REQUEST["accounts"]));
	if ($accounts == "") {
	    // no. give it the default one.
	    $accounts = $CONFIG["default_memberlist_size"];
	}

	// calculate the number of pages
	$nofpages = floor ($VAR["totalaccounts"] / $accounts);
	if (($nofpages * $accounts) != $VAR["totalaccounts"]) { $nofpages++; };
	$VAR["nofpages"] = $nofpages;

	// was a page given?
	$page = preg_replace ("/\D/", "", $_REQUEST["page"]);
	if ($page == "") {
	    // no. default to the last page
	    $page = "1";
	}
	$VAR["page"] = $page;

	$joindatetimestamp = "%m-%d-%Y";
	$lastposttimestamp = "%m-%d-%Y %I:%i:%s";

	$order = "accountname";

	$query = sprintf ("SELECT id,accountname,flags,email,DATE_FORMAT(lastpost,'%s'),lastmessage,DATE_FORMAT(joindate,'%s'),nofposts FROM accounts ORDER BY %s ASC LIMIT %s,%s", $lastposttimestamp, $joindatetimestamp, $order, ($page - 1) * $accounts, $accounts);

        // grab all pages
        $res = db_query ($query);

	// is this subject multiple pages long?
	if ($nofpages > 1) {
	    // yes. build the list of pages
	    $pageslist = "";

	    // do we have pages before this one?
	    if (($page - $CONFIG["page_display_range"] + 1) > 0) {
		// yes. show the 'first link' and the dots
		$VAR["pageslist"]  = InsertSkinVars (GetSkinTemplate ("memberlist_page_firstpage"));
		$VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ("memberlist_page_range_separator"));
	   }

	   // now, figure out the page range
	   $page_from = $page - floor ($CONFIG["page_display_range"] / 2);
	   if ($page_from == 0) { $page_from = 1; };
	   $page_to = $page_from + $CONFIG["page_display_range"] - 1;
	   if ($page_to > $nofpages) { $page_to = $nofpages; };

	   // list them
	   $curpage = $page;
	   for ($VAR["page"] = $page_from; $VAR["page"] <= $page_to; $VAR["page"]++) {
		// is this thing selected?
		if ($VAR["page"] == $curpage) {
		    // yes. use the selected template
		    $template = "memberlist_page_sel";
		} else {
		    // no. use the unselected template
		    $template = "memberlist_page_unsel";
		}

		$VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ($template));

		// not the last page?
		if ($VAR["page"] != $page_to) {
		    // yes, add the separator
		    $VAR["pageslist"] .= InsertSkinVars (GetSkinTemplate ("memberlist_page_separator"));
		}
	    }

	    // do we have more pages?
	    if ($page_to < $nofpages) {
		// yes. show the last page link, too
		$VAR["pageslist"] .= InsertSkinVars (GetSkinTemplates ("memberlist_page_separator"));
		$VAR["pageslist"] .= InsertSkinVars (GetSkinTemplates ("memberlist_page_lastpage"));
	   }
       }

       // list them all
       while (list ($VAR["accountid"], $VAR["username"], $flags, $VAR["email"], $VAR["lastpost"], $lastmessage, $VAR["joindate"], $VAR["nofposts"]) = db_fetch_results ($res)) {
	    // is there a last message?
	    if ($VAR["lastpost"] == "") {
                // no. set it to none
                $VAR["lastpost"] = GetSkinTemplate ("lastpost_none");
		$VAR["lasthread"] = "";
	    } else {
		// yes. figure out the thread id
		$query = sprintf ("SELECT threadid FROM posts WHERE id='%s'", $lastmessage);
		list ($VAR["lasthread"]) = db_fetch_results (db_query ($query));
	    }

	    // is the user open about their email address?
	    if ($flags & FLAG_HIDEMAIL) {
	        // no. don't show it
	        $VAR["email"] = GetSkinTemplate ("memberlist_emailhidden");
	    } else {
	        // yes. build it
	        $VAR["email"] = InsertSkinVars (GetSkinTemplate ("memberlist_email"));
	    }

	    // show the account
	    $VAR["memberlist"] .= InsertSkinVars (GetSkinTemplate ("memberlist_list"));
       }
        
        // list them
	ShowForumPage("memberlist_page");
	exit;
    }

    // do we need to perform a earch?
    if ($_REQUEST["action"] == "search") {
	// yep.  show the page
	ShowForumPage("memberlist_search_page");
    }
?>
