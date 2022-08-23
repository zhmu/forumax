<?php
    //
    // posthog.php
    //
    // This will display the top forum posters.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // TOP_COUNT is the number of accounts we'll show
    define (TOP_COUNT, 100);

    // get the total forum post count
    $query = sprintf ("SELECT COUNT(id) FROM posts");
    list ($totalposts) = db_fetch_results (db_query ($query));

    // get them all
    $query = sprintf ("SELECT id,accountname,nofposts FROM accounts ORDER BY nofposts DESC LIMIT %s", TOP_COUNT);
    $res = db_query ($query);

    // show them all
    $hogline = GetSkinTemplate ("posthog_row");
    $VAR["pos"] = 1; $no = 1;
    while (list ($VAR["accountid"], $VAR["accountname"], $VAR["nofposts"]) = db_fetch_results ($res)) {
	$VAR["post_pct"] = sprintf ("%.2f", ($VAR["nofposts"] / $totalposts) * 100);
	$VAR["width"] = $VAR["post_pct"] * 2;
	$VAR["posthog_bar"] = InsertSkinVars (GetSkinTemplate ("bar_" . $no));
	$VAR["posthog_list"] .= InsertSkinVars ($hogline);
	$VAR["pos"]++;
	$no++;
	if ($no > 10) { $no = 1; };
    }

    // show the page
    $VAR["topno"] = $VAR["pos"] - 1;
    ShowForumPage("posthog_page");
 ?>
