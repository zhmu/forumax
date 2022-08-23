<?php
    //
    // logout.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will logging out from the forum.
    //

    // we need our library
    require "lib.php";

    // delete the cookie
    SetCookie ("auth_cookie", "", 0);

    // redirect back to the index page
    Header ("Location: index.php");
 ?>
