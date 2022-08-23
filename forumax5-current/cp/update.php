<?php 
    //
    // update.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the forum updating.
    //

    // we need our library, too
    require "lib.php";

    //
    // DieFooter ($error)
    //
    // This will  show error $error, along with the forum footer. It will
    // then exit the script.
    //
    function
    DieFooter ($error) {	
	echo $error;
	cpShowFooter();
	exit;
    }

    //
    // Overview()
    //
    // This will check for updates.
    //
    function
    Overview() {
	// show the nice header
	cpShowHeader("ForuMAX Update", "Update Forum");

	// XXX: disabled on purpose! ForuMAX.com is hijacked!
	DieFooter ("ForuMAX.com is hijacked, updates disabled!");

	// progress
	echo "<ul><li>Querying ForuMAX.com for updates...";

	// try to contact the server
	$fp = @fopen (NEWESTVERSION_URL, "rb") or DieFooter (" Failure</li></ul><p>Unable to query for updates. Please try again later");
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
All existing files will be copied to a directory called <b>backup</b>, so you can always restore the forum should things go wrong. Skins will <b>not</b> be upgraded!<p>
<b>NOTICE: We take <i>NO</i> responsibility for failed upgrades!</b><p>
Should you wish to perform this update, please fill in your license information in the entry fields below:<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
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
	if ($_REQUEST["todo"] == "doupdate") {
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

	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_UPDATE);

    // only one action here, so just go for it
    Overview();
 ?>
