<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // do we have a master password variable?
    $query = sprintf ("select content from config where name='master_password'");
    if (db_nof_results (db_query ($query)) > 0) {
	// yes. we already upgraded. die
	echo "You have already upgraded";
	exit;
    }

    // inform the user
    echo "- Adding smilies table... ";
    $query = sprintf ("create table smilies (id bigint not null primary key auto_increment,smilie varchar(32) not null,image varchar(128) not null)");
    db_query ($query);
    echo "DONE<p>";

    echo "- Adding master password and censored words... ";
    $query = sprintf ("insert into config values ('master_password',md5('forumax'))");
    db_query ($query);
    $query = sprintf ("insert into config values ('censored_words','')");
    db_query ($query);
    echo "DONE<p>";

    echo "Now, <a href=\"cp/index.php\">use the control panel</a> to import the default skin, set it as default and get rid of the old one and you should be set. <B>ENSURE</B> you delete this script!!!";
 ?>
