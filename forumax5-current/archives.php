<?php
    //
    // archives.php
    //
    // This will display the archives.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab an overview of all archives we have
    $query = sprintf ("SELECT id,name,nofthreads,nofposts,description FROM archives");
    $res = db_query ($query);
    while (list ($VAR["archiveid"], $VAR["archivename"], $VAR["nofthreads"], $VAR["nofposts"], $VAR["description"]) = db_fetch_results ($res)) {
	// construct the archive list
	$VAR["archivelist"] .= InsertSkinVars (GetSkinTemplate ("archive_list"));
    }

    // build the hopto list
    $VAR["hopto_list"] = BuildHopto();

    // show the page
    ShowForumPage("archive_overview");
 ?>
