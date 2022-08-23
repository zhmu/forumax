<?php
    // we need our nice library
    require "cp_lib.php";

    // are we an admin?
    if (($GLOBALS["flags"] & FLAG_ADMIN) != 0) {
	// yes. redirect to the admin control panel
	Header ("Location: cp_admin.php");
    } else {
	// no. redirect to the moderator control panel
	Header ("Location: cp_mod.php");
    }
 ?>
