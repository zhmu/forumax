<?php
    // we need our nice library
    require "cp_lib.php";

    // are we a master?
    if (($GLOBALS["flags"] & FLAG_MASTER) != 0) {
	// yes. redirect to the master control panel
	Header ("Location: cp_master.php");
    } elseif (($GLOBALS["flags"] & FLAG_ADMIN) != 0) {
	// no, but we're admin. redirect to the admin control panel
	Header ("Location: cp_admin.php");
    } else {
	// no. redirect to the moderator control panel
	Header ("Location: cp_mod.php");
    }

