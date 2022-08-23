<?php
    //
    // maxcode.php
    //
    // This will display the MaX code help page.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // just show the page (no header and footer here)
    echo InsertSkinVars (GetSkinTemplate ("explain_maxcode"));
 ?>
