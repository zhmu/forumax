<?php
    // we need our nice library
    require "cp_lib.php";

    // NEWESTVERSION_URL is the URL we read to see what the newest version is
    define (NEWESTVERSION_URL, "http://tl/forumax/updates/curver.txt");
    // define (NEWESTVERSION_URL, "http://www.forumax.com/updates/curver.txt");

    // GRABFILE_URL is the URL we use to actually fetch ForuMAX files
    define (GRABFILE_URL, "http://tl/forumax/updates/getfile.php?username=%s&password=%s&file=%s");
    // define (GRABFILE_URL, "http://www.forumax.com/updates/getfile.php?username=%s&password=%s&file=%s");

    // do we have master privileges?
    if (($GLOBALS["flags"] & FLAG_MASTER) == 0) {
	// no. redirect back to the index.php file
	Header ("Location: index.php");
	exit;
    }

    //
    // Intro()
    //
    // This will show the introduction.
    //
    function
    Intro() {
	CPShowHeader();
 ?>Welcome to the <b>Master Control Panel</b>!<p>
The Master control panel will give you full administrative access to all forum options, as well as access to special options, such as ForuMAX upgrading. The master password is stored in an encoded form which cannot be reversed, and therefore it is very highly recommended that you keep this password secure. You can always change it at will if needed.<p>
<?php
	CPShowFooter();
    }

    //
    // Update()
    //
    // This will check for updates.
    //
    function
    Update() {
	global $todo;

	CPShowHeader();

	echo "<ul><li>Querying ForuMAX.com for updates...";

	// try to contact the server
	$fp = @fopen (NEWESTVERSION_URL, "rb") or die (" Failure</li></ul><p>Unable to query for updates. Please try again later");
	while (!feof ($fp)) { $curver = fread ($fp, 1024); };
	fclose ($fp);

	echo " Done</li></ul><p>";

	// now, figure out the newest version
	preg_match ("/CURVER\#((.)*)\#/U", $curver, $tmp);
	$new_version = $tmp[1];
	preg_match ("/FEATURES\#((.)*)\#/U", $curver, $tmp);
	$new_features = $tmp[1];

	// figure out the filenames
	$tmp = explode ("\n", $curver);
	while (list (, $line) = each ($tmp)) {
	    // does this line start with FILE# ?
	    if (preg_match ("/FILE\#((.)*)\#/U", $line, $file)) {
		// yes. add this file to the list
		$new_files[] = $file[1];
	    }
	}

	// was another action given?
	if ($todo == "") {
	    // no. just show what there is
	    printf ("You are currently running ForuMAX <b>%s</b><br>", FORUMAX_VERSION);
	    print "The current ForuMAX version is <b>$new_version</b><p>";

	    // is this the same version?
	    if (FORUMAX_VERSION != $new_version) {
	        // no. yay, we can update. show the page
 ?><center><font size=6><u><b>Update Available!</b></u></font><p>The newest version of ForuMAX contains these new features:<p><?php echo $new_features; ?><p>
The following files are affected, and need to be updated or created:<p><ul>
<?php
		// list all files
		while (list (, $file) = each ($new_files)) {
		    print "<li>$file</li>";
		}
 ?></ul><p>
All existing files will be copied to a directory called <b>backup</b>, so you can always restore the forum should things go wrong. Skins will <b>not</b> we upgraded!<p>
<b>NOTICE: We take <i>NO</i> responsibility for failed upgrades!</b><p>
Should you wish to perform this update, please fill in your license information in the entry fields below:<p>
<form action="cp_admin.php" method="post">
<input type="hidden" name="action" value="update">
<input type="hidden" name="todo" value="doupdate">
<table width="100%" border=0>
 <tr>
  <td width="50%" align="right">License username</td>
  <td width="50%" align="left"><input type="text" name="lic_username"></td>
 </tr>
 <tr>
  <td width="50%" align="right">License password</td>
  <td width="50%" align="left"><input type="password" name="lic_password"></td>
 </tr>
</table><p>
<input type="submit" value="Perform the update!">
</form>
<?php
   	    } else {
	        // yes. no updates for us.
 ?><center><font size=6>No updates available</font><p>
<?php
  	    }
	}

	// do we need to actually handle the update?
	if ($todo == "doupdate") {
	    // yes. try to grab a dummy file
	    echo "<ul><li>Verifying license username/password...";
	    $tmp = FetchForumFile ("dummy.txt");
	    echo " Success</li></ul>";

	    // go to the actual forum root directory
	    chdir ("..");

	    // backup all current files
	    echo "<ul><li>Backing up current files...";
	    while (list (, $file) = each ($new_files)) {
		// do we currently have this file?
		if (file_exists ($file)) {
		    // copy the file
		    @copy ($file, "backup/" . $file) or die ("Failure, could not back up <b>" . $file . "</b>");

		    // give the file a good chmod
		    chmod ("backup/" . $file, fileperms ($file));
		}
	    }
	    echo " Success</li></ul>";

   	    // download and install all new files
	    echo "<ul><li>Downloading and installing new files...";
	    reset ($new_files);
	    while (list (, $file) = each ($new_files)) {
		// grab the new file contents
	        $data = FetchForumFile ($file);

		// now, write this information to the forum file
		$fp = fopen ($file, "wb") or die ("Failure, cannot overwrite file <b>$file</b>");
		fwrite ($fp, $data);
		fclose ($fp);
	    }

	    echo " Success</li></ul>";

	    // all has been done. show the 'yay' page
 ?>The forum has successfully been updated. You are now the proud owner of ForuMAX <b><?php echo $new_version; ?></b>, instead of <?php echo FORUMAX_VERSION ?>. We hope you feel much better now :)<p>
<b>IMPORTANT</b> You will now need to use the <a href="../update.php">update.php</a> script to fix up any internal structures and such. You will only need to do this once.
<?php
	}

	CPShowFooter();
    }

    //
    // Password()
    //
    // This will change the Master Password.
    //
    function
    Password() {
	global $newpwd1, $newpwd2, $oldpwd, $CONFIG;

	CPShowHeader();

	// need to actually do this ($newpwd1, $newpwd2 and $oldpwd shouldn't
	// be blank and $newpwd1 == $newpwd2)
	if (($oldpwd != "") and ($newpwd1 != "") and ($newpwd2 != "") and ($newpwd1 == $newpwd2)) {
	    // yes. is the current master password correct?
	    if ($CONFIG["master_password"] != md5 ($oldpwd)) {
		// no. complain
		print "Old master password is not correct";
	    } else {
	        // set the new password
	        $query = sprintf ("update config set content='%s' where name='master_password'", md5 ($newpwd1));
		db_query ($query);

		// it worked. inform the user
 ?>Thank you, the master password has successfully been changed.
<?php
	    }
	} else {
	    // no. show the page
 ?><center>Change Master Password</center><p>
<form action="cp_master.php" method="post">
<input type="hidden" name="action" value="password">
<table width="100%">
 <tr>
  <td width="48%" align="right">Old Master Password</td>
  <td width="2%" align="center">&nbsp;</td>
  <td width="48%" align="left"><input type="password" name="oldpwd"></td>
 </tr>
 <tr>
  <td align="right">New Master Password</td>
  <td align="center">&nbsp;</td>
  <td align="left"><input type="password" name="newpwd1"></td>
 </tr>
 <tr>
  <td align="right">Retype New Master Password</td>
  <td align="center">&nbsp;</td>
  <td align="left"><input type="password" name="newpwd2"></td>
 </tr>
</table><p><center><?php
    if ($newpwd1 != $newpwd2) { echo "<b>New passwords are not equal</b>"; };
 ?><p><input type="submit" value="Change the master password"></center>
</form>
<?php
	}

	CPShowFooter();
    }

    // need to show the intro?
    if ($action == "") {
	// yes. do it
	Intro();
	exit;
    }

    // need to update the forum?
    if ($action == "update") {
	/// yes. do it
	Update();
	exit;
    }

    // need to alter the master password?
    if ($action == "password") {
	// yes. do it
	Password();
	exit;
    }
 ?>
