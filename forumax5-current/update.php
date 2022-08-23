<?php
    //
    // update.php
    //
    // This will update an old version of the forum to the newest version.
    //
    // This must be updated to use the $_REQUEST[] stuff.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // disable the version check
    $GLOBALS["disable_vcheck"] = 1;

    // we need the generic library too
    require "lib.php";

    //
    // AddSkin ($fname, $name, $desc)
    //
    // This will add skin $fname as name $name and description $desc and return
    // the ID of it.
    //
    function
    AddSkin ($fname, $name, $desc) {
	// read the skin file
	$fd = @fopen ($fname, "rt");
	if (!$fd) {
 ?>We could not open the skin file (<code><?php echo $skinsource; ?></code>). This may be a problem with your web server, please consult your system administrator.
<?php
	    cpShowFooter();
	    exit;
	}

	// read everything
	$skindata = "";
	while (!feof ($fd)) {
	    $skindata .= fread ($fd, 65536);
	}

	// close the file
	fclose ($fd);

	// evaluate the skin
	eval ($skindata);

	// okay, do we have a skin version now?
	if ($SKINVERSION == "") {
	    // no. complain
 ?>This does not appear to be a valid ForuMAX skin file.
<?php
	    cpShowFooter();
	    exit;
	}

	// is the version correct?
	if ($SKINVERSION != FORUMAX_VERSION) {
	    // no. complain
 ?>We're sorry, but this skin is designed for <b>ForuMAX <?php echo $SKINVERSION; ?></b>, which is not the same as your <b>ForuMAX <?php echo FORUMAX_VERSION; ?></b>.
<?php
	    cpShowFooter();
	    exit;
	}

	// add the skin to the list of available skins
	$query = sprintf ("INSERT INTO skins VALUES (NULL,'%s','%s',0)", $name, $desc);
	$res = db_query ($query);
	$id = db_get_insert_id ($res);

	// build the skin database
	$query = sprintf ("CREATE TABLE skin_%s (name VARCHAR(64) NOT NULL PRIMARY KEY,content TEXT NOT NULL,title VARCHAR(64) NOT NULL,refresh_url VARCHAR(64) NOT NULL,description VARCHAR(128) NOT NULL)", $id);
	db_query ($query);

	// browse all templates
	$tmp = "";
	while (list ($name, $content) = each ($SKIN)) {
	    // feed them into the database
	    $name = addslashes ($name); $content = addslashes ($content);
	    $refresh = addslashes ($SKINREFRESH[$name]);
	    $title = addslashes ($SKINTITLE[$name]);
	    $desc = addslashes ($SKINDESC[$name]);

	    // fix up the content
	    $content = str_replace ("\\[1]", "\\\\1", $content);
	    $content = str_replace ("\\[2]", "\\\\2", $content);

	    // insert them into the database
	    $query = sprintf ("INSERT INTO skin_%s VALUES ('%s','%s','%s','%s','%s')", $id, $name, $content, $title, $refresh, $desc);
	    db_query ($query);
	}

	// also add the variables
	$query = sprintf ("CREATE TABLE skinvars_%s (name VARCHAR(64) NOT NULL PRIMARY KEY,content VARCHAR(128) NOT NULL,description VARCHAR(128) NOT NULL)", $id);
	db_query ($query);

	// browse all variables
	while (list ($name, $content) = each ($SKINVAR)) {
	    // grab the description, too
	    $desc = $SKINVAR_DESC[$name];

	    // feed them into the database
	    $query = sprintf ("INSERT INTO skinvars_%s VALUES ('%s','%s','%s')", $id, $name, $content, $desc);
	    db_query ($query);
	}

	// fix up the values
	$content = preg_replace ("/\{((\S)*)\}/e", '$SKINVAR["\\1"]', $SKIN["stylesheet"]);

	// put them into a file
	$fd = @fopen ("styles/skin" . $id . ".css", "w");
	if (!$fd) {
	    // this failed. complain
 ?>Sorry, but we could not create the style file. This is most likely due to a file permission problem.
<?php
	    cpShowFooter();
	}

	// put the data into it
	fputs ($fd, "/* This file is automatically generated, do not edit */\n");
	fputs ($fd, $content);

	// close the file
	fclose ($fd);

	// paranoia (but it can't hurt :)
	@chmod ("styles/skin" . $id . ".css", 0600);

	return $id;
    }

    // is the forum already upgraded?
    $query = sprintf ("SELECT content FROM config WHERE name='forumax_version'");
    list ($curver) = db_fetch_results (db_query ($query));
    if (FORUMAX_VERSION == $curver) {
	// yes. complain
	die ("Your forum database has already been upgraded");
    }

    // master password supplied?
    if ($master_pwd == "") {
	// no. complain
 ?>Before you can upgrade the forum database, we'd like you to enter the master password in the textbox below.<p>
<form action="update.php" method="post">
<input type="password" name="master_pwd"><p>
<input type="submit" value="Upgrade forum">
</form>
<?php
	exit;
    }

    // correct master password?
    $query = sprintf ("SELECT content FROM config WHERE name='master_password'");
    list ($mpwd) = db_fetch_results (db_query ($query));
    if (md5 ($master_pwd) != $mpwd) {
	// no. complain
	die ("Sorry, but your master password is not correct");
    }

    // fix up the database
    print "- Fixing up the database tables...";

    $query = sprintf ("alter table accounts add sig_option tinyint not null after timediff");
    db_query ($query);
    $query = sprintf ("alter table accounts add avatar bigint not null after sig_option");
    db_query ($query);
    $query = sprintf ("insert into config values ('avatar_option','0')");
    db_query ($query);
    $query = sprintf ("insert into config values ('forumax_version','%s')", FORUMAX_VERSION);
    db_query ($query);
    $query = sprintf ("update config set name='master_password' where name='master_pwd'");
    db_query ($query);
    $query = sprintf ("create table avatar (id bigint not null primary key auto_increment,flags bigint not null,userid bigint not null,index(userid))");
    db_query ($query);
    $query = sprintf ("create table cp_access (id bigint not null primary key auto_increment,cp_option bigint not null,access bigint not null)");
    db_query ($query);
   $query = sprintf ("create table polls (id bigint not null primary key auto_increment,threadid bigint not null,question varchar(128) not null,flags bigint not null,totalvotes bigint not null,index(threadid))");
    db_query ($query);
    $query = sprintf ("create table poll_options (id bigint not null primary key auto_increment,pollid bigint not null,optiontext varchar(128) not null,nofvotes bigint not null,index(pollid))");
    db_query ($query);
    $query = sprintf ("create table poll_votes (id bigint not null primary key auto_increment,pollid bigint not null,accountid bigint not null,index(pollid))");
    db_query ($query);
    $query = sprintf ("alter table threads add rating float not null");
    db_query ($query);
    $query = sprintf ("create table threadsrated (id bigint not null primary key auto_increment,threadid bigint not null,accountid bigint not null,rating float not null,index(threadid),index(accountid))");
    db_query ($query); 
    $query = sprintf ("insert into config values ('birthdate_timestamp_format','%%e %%b %%Y')");
    db_query ($query);
    $query = sprintf ("insert into config values ('max_online','0')");
    db_query ($query);
    $query = sprintf ("insert into config values ('forum_pagesize','30')");
    db_query ($query);
    $query = sprintf ("alter table accounts add activatekey varchar(32) not null after avatar");
    db_query ($query);
    $query = sprintf ("insert into config values ('reply_maxbacklog','20')");
    db_query ($query);
    $query = sprintf ("alter table accounts add reply_backlog tinyint not null after activatekey");
    db_query ($query);
    $query = sprintf ("insert into config values ('coppa_fax_no','[FILL THIS IN]')");
    db_query ($query);
    $query = sprintf ("insert into config values ('bb_closed',0)");
    db_query ($query);
    $query = sprintf ("insert into config values ('bb_close_reason','Forums are closed due to maintenance')");
    db_query ($query);
    $query = sprintf ("insert into config values ('banned_accountname','')");
    db_query ($query);
    $query = sprintf ("insert into config values ('banned_email','')");
    db_query ($query);
    $query = sprintf ("insert into config values ('banned_ip','')");
    db_query ($query);
    $query = sprintf ("alter table categories add description varchar(128) not null after name");
    db_query ($query);
    $query = sprintf ("alter table posts add authorname varchar(64) not null after authorid");
    db_query ($query);
    $query = sprintf ("alter table threads add authorname varchar(64) not null after authorid");
    db_query ($query);
    $query = sprintf ("alter table threads add lastpostername varchar(64) not null after lastposterid");
    db_query ($query);
    $query = sprintf ("alter table forums add lastpostername varchar(64) not null after lastposterid");
    db_query ($query);

    // grab all skins
    $query = sprintf ("SELECT id FROM skins");
    $res = db_query ($query);

    // fix 'm all
    while (list ($skinid) = db_fetch_results ($res)) {
        $query = sprintf ("alter table skin_%s add description varchar(128) not null after refresh_url", $skinid);
    db_query ($query);
        $query = sprintf ("alter table skinvars_%s add description varchar(128) not null after content", $skinid);
    db_query ($query);
    }

    echo " DONE<br>- Installing new skin...";
    $id = AddSkin ("cp/defaultskin.php", "Default ForuMAX " . FORUMAX_VERSION . " skin", "New skin created by the update utility");
    echo " DONE<br>- Activating new skin as default...";

    // make everything not default
    $query = sprintf ("UPDATE skins SET flags=0");
    db_query ($query);
    $query = sprintf ("UPDATE skins SET flags=%s WHERE id=%s", FLAG_SKIN_DEFAULT, $id);
    db_query ($query);
    
    echo " DONE<br>- Updating version number...";
    $query = sprintf ("update config set content='%s' where name='forumax_version'", FORUMAX_VERSION);
    db_query ($query);

    echo " DONE<br>- Setting up control panel access...";
 
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,1,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,2,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,3,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,4,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,5,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,6,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,7,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,8,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,9,1)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,10,0)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,11,0)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,12,7)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,13,7)");
    db_query ($query);
    $query = sprintf ("INSERT INTO cp_access VALUES (NULL,14,0)");
    db_query ($query);

    echo " DONE<br>- Fixing up table indexes...";

    $query = sprintf("alter table privatemessages add index(userid)");
    db_query ($query);
    $query = sprintf("alter table groupmembers add index(groupid)");
    db_query ($query);
    $query = sprintf("alter table groupmembers add index(userid)");
    db_query ($query);

    echo " DONE<p>ForuMAX " . FORUMAX_VERSION . " is now set up and ready!";
 ?>
