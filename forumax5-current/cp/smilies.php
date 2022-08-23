<?php 
    //
    // smilies.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will handle the editing of extra fields.
    //

    // we need our library, too
    require "lib.php";

    //
    // Overview()
    //
    // This will show the smilie overview.
    //
    function
    Overview() {
	global $SKIN_VALUE;

	// get all smilies
	$query = sprintf ("SELECT id,smilie,image FROM smilies");
	$res = db_query ($query);

	// build the page
	cpShowHeader("Smilie Maintenance", "Overview");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="submit">
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
<tr class="tab3">
 <td width="10%" align="center"><b>Delete?</b></td>
 <td width="30%" align="center"><b>Smilie Text</b></td>
 <td width="30%" align="center"><b>Image</b></td>
 <td width="30%" align="center"><b>Image preview</b></td>
</tr>
<?php
	// list them all
	while (list ($id, $smilie, $img) = db_fetch_results ($res)) {
	    // list the smilie
	    printf ("<tr class=\"tab2\"><td align=\"center\"><input type=\"checkbox\" name=\"delete[%s]\"></td><td align=\"center\"><input type=\"text\" name=\"smilie[%s]\" value=\"%s\"></td><td align=\"center\"><input type=\"text\" name=\"img[%s]\" value=\"%s\"></td><td align=\"center\"><img src=\"%s\" alt=\"[Smilie\"></td></tr>", $id, $id, $smilie, $id, $img, "../" . $SKIN_VALUE["images_url"] . "/" . $img);
	}
 ?></table><p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
 <tr class="tab3">
  <td width="10%" align="center"><b>Add?</b></td>
  <td width="90%"><b>Number of smilies to add</b></td>
 </tr>
 <tr class="tab2">
  <td align="center"><input type="checkbox" name="add"></td>
  <td><input type="text" name="nofsmilies"></td>
 </tr>
</table><p>
<center><input type="submit" value="Submit Changes"></center>
</form>
</table>
<?php
	cpShowFooter();
    }

    //
    // Submit()
    //
    // This will actually edit the smilies.
    //
    function
    Submit() {
	global $add, $delete, $smilie, $img, $nofsmilies, $PHP_SELF;

	// get all id's
	$query = sprintf ("SELECT id FROM smilies");
	$res = db_query ($query);
	while (list ($id) = db_fetch_results ($res)) {
	    // we have a smilie id. does it need to be deleted?
	    if ($_REQUEST["delete"][$id] != "") {
		// yes. get rid of it
		$query = sprintf ("DELETE FROM smilies WHERE id='%s'", $id);
		db_query ($query);
	    } else {
		// no. update it
		$query = sprintf ("UPDATE smilies SET smilie='%s',image='%s' WHERE id=%s", $_REQUEST["smilie"][$id], $_REQUEST["img"][$id], $id);
		db_query ($query);
	    }
	}

	// need to add some smilies?
	if ($_REQUEST["add"] != "") {
	    // yes. do it
	    for ($i = 0; $i < preg_replace ("/\D/", "", $_REQUEST["nofsmilies"]); $i++) {
		// add a smilie
		$query = sprintf ("INSERT INTO smilies VALUES (NULL,'','')");
		db_query ($query);
	    }
	}

	// it worked. inform the user
	CPShowHeader("Smilie Maintenance", "Submission results");
 ?>The smilie settings have successfully been activated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Return to smilie overview">
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_SMILIES);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // is an action given?
    if (($action == "") or ($action == "overview")) {
	// no. show the overview
	Overview();
    } elseif ($action == "submit") {
	// we need to submit the smilie changes. do it
	Submit();
    }
 ?>
