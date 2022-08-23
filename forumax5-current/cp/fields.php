<?php 
    //
    // fields.php
    //
    // (c) 2000-2002 Next-Future, www.next-future.nl
    //
    // This will handle the editing of extra fields.
    //

    // we need our library, too
    require "lib.php";

    // $exfield_type[] are the extra field types we know of
    $exfield_type[0] = "(delete this field)";
    $exfield_type[1] = "Text";
    $exfield_type[2] = "URL";
    $exfield_type[3] = "AIM";
    $exfield_type[4] = "Yahoo! ID";
    $exfield_type[5] = "Gender";
    $exfield_type[6] = "Homepage URL";
    $exfield_type[7] = "Custom Status";
    $exfield_type[8] = "MSN";
    $exfield_type[9] = "ICQ";
    $exfield_type[10] = "Country";
    $exfield_type[11] = "Image";

    // $exfield_perm[] are the extra field permissions
    $exfield_perm[0] = "User and admins can modify";
    $exfield_perm[1] = "Only admins can modify";

    //
    // BuildFieldTypes ($curtype)
    //
    // This will build a dropdown list of all field types known, with $curtype
    // selected.
    //
    function
    BuildFieldType ($curtype) {
	global $exfield_type;

	// build the list
	reset ($exfield_type); $tmp = "";
	while (list ($no, $desc) = each ($exfield_type)) {
	    $tmp .= "<option value=\"" . $no . "\"";
	    if ($curtype == $no) { $tmp .= " selected"; }
	    $tmp .= ">" . $desc . "</option>";
	}

	// return the list
	return $tmp;
    }

    //
    // BuildFieldPerm ($curperm)
    //
    // This will build a dropdown list of all permissions, with $curperm
    // selected.
    //
    function
    BuildFieldPerm ($curperm) {
	global $exfield_perm;

	// build the list
	reset ($exfield_perm); $tmp = "";
	while (list ($no, $desc) = each ($exfield_perm)) {
	    $tmp .= "<option value=\"" . $no . "\"";
	    if ($curperm == $no) { $tmp .= " selected"; }
	    $tmp .= ">" . $desc . "</option>";
	}

	// return the list
	return $tmp;
    }

    //
    // Overview()
    //
    // This will show the page for extra field editing.
    //
    function
    Overview() {
	// build the page
	cpShowHeader("Extra Field Maintenance", "Overview");
 ?><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="action" value="submit">
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
<tr class="tab3">
  <td width="5%" align="center"><b>ID</b></td>
  <td width="10%" align="center"><b>Visible?</b></td>
  <td width="35%">&nbsp;<b>Name</b></td>
  <td width="20%">&nbsp;<b>Type</b></td>
  <td width="30%">&nbsp;<b>Permissions</b></td>
</tr>
<?php
	// grab all custom fields
	$query = sprintf ("SELECT id,name,type,visible,perms FROM customfields");
	$res = db_query ($query);

	// browse them all
	while (list ($id, $name, $type, $visible, $perms) = db_fetch_results ($res)) {
	    // add the field
	    printf ("<tr class=\"tab2\"><td align=\"center\">%s</td>", $id);
	    printf ("<td align=\"center\"><input type=\"checkbox\" name=\"visible[%s]\"", $id);
	    if ($visible != 0) { echo " checked"; }
	    printf ("></td><td>&nbsp;<input type=\"text\" name=\"fieldname[%s]\" value=\"%s\"></td>", $id, $name);
	    printf ("<td>&nbsp;<select name=\"type[%s]\">%s</select></td>", $id, BuildFieldType ($type));
	    printf ("<td>&nbsp;<select name=\"perm[%s]\">%s</select></td></tr>", $id, BuildFieldPerm ($perms));
	}

 ?></table><p>
If you want to add more extra fields, please check the checkbox below and type the number of fields you would like to add in the field next to it.<p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
<tr class="tab3">
  <td width="10%"><center><b>Add?</b></center></td>
  <td width="70%"><b>Number of fields to add</b></td>
</tr>
<tr class="tab2">
  <td align="center"><input type="checkbox" name="add"></td>
  <td><input type="text" name="numadd"></td>
</tr>
</table><p>
<center><input type="submit" value="Submit Changes"></center></form>
<?php
	cpShowFooter();
    }

    //
    // Submit()
    //
    // This will actually take care of the extra fields.
    //
    function
    Submit() {
	// grab all fields
	$type = $_REQUEST["type"];
	$fieldname = $_REQUEST["fieldname"];
	$type = $_REQUEST["type"];
	$perm = $_REQUEST["perm"];
	$add = $_REQUEST["add"];
	$visible = $_REQUEST["visible"];
	$numadd = $_REQUEST["numadd"];

	// do we have any types?
	if (is_array ($type) != 0) {
	    // yes. modify them
	    while (list ($no, $value) = each ($type)) {
	        // do we need to get rid of this field?
	        if ($value == 0) {
	   	    // yes. do it
		    $query = sprintf ("ALTER TABLE accounts DROP COLUMN extra%s", $no);
		    db_query ($query);
		    $query = sprintf ("DELETE FROM customfields WHERE id='%s'", $no);
		    db_query ($query);
	        } else {
		    // no. just modify them
	            $vis = ($visible[$no] != "") ? 1 : 0;
		    $query = sprintf ("UPDATE customfields SET visible='%s',name='%s',type='%s',perms='%s' WHERE id='%s'", $vis, $fieldname[$no], $type[$no], $perm[$no], $no);
		    db_query ($query);
	        }
	    }
	}

	// need to add fields?
	if ($add != "") {
	    // yes. do it
	    for ($i = 0; $i < $numadd; $i++) {
		// add a new extra field
		$query = sprintf ("INSERT INTO customfields VALUES (NULL,'New field',1,0,0)");
		db_query ($query);

		// grab the new id
		$no = db_get_insert_id();

		// add the column for it
		$query = sprintf ("ALTER TABLE accounts ADD extra%s VARCHAR(128)", $no);
		db_query ($query);
	    }
	}

	// it worked. show the 'yay' page
	cpShowHeader("Extra Field Maintenance", "Commit Changes");
 ?>Thank you, the extra fields have successfully been updated.<p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="submit" value="Back to extra field overview">
</form>
<?php
	cpShowFooter();
    }

    // verify the rights
    cpVerifyAccess (CPOPTION_FIELDS);

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // is an action given?
    if (($action == "") or ($action == "overview")) {
	// no, just go to the overview
	Overview();
    } elseif ($action == "submit") {
	// we must submit the changes
	Submit();
    }
 ?>
