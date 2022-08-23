<?php
    //
    // smilies.php
    //
    // This will show an overview of all known smilies.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the smilie row template
    $smilierow_template = addslashes (GetSkinTemplate ("smilie_row"));

    // grab all smilies from the database
    $query = sprintf ("SELECT smilie,image FROM smilies");
    $res = db_query ($query);

    // build a list of them all
    while (list ($VAR["smilie"], $VAR["smilie_img"]) = db_fetch_results ($res)) {
	$VAR["smilies"] .= InsertSkinVars (GetSkinTemplate ("smilie_row"));
    }

    // just show the page (no header and footer here)
    echo InsertSkinVars (GetSkinTemplate ("explain_smilies"));
 ?>
