<?php 
    //
    // index.php
    //
    // (c) 2000-2002 NextFuture, www.next-future.nl
    //
    // This will give an introduction page to the control panel.
    //

    // we need our library, too
    require "lib.php";

    // fetch the action
    $action = trim ($_REQUEST["action"]);

    // do we need to log the user out?
    if ($action == "logout") {
	// yes. zap the cookie
	SetCookie ("cp_authcookie", "", 0);

	// redirect back to the forums
	Header ("Location: ../index.php");
	exit;
    }

    // show the introduction
    cpShowHeader();
 ?>Welcome the the ForuMAX 5.0 Control Panel!<p>

This control panel will allow you to alter your forum behaviour.<p>
<center><b>ForuMAX Credits</b></center><p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
 <tr class="tab2">
  <td width="50%" align="center">Project Supervisor, Main Programmer</td>
  <td width="50%" align="center">Rink Springer</td>
 </tr>
 <tr class="tab2">
  <td align="center">Layout Supervisor, Graphics Designer</td>
  <td align="center">Emiel Roumen</td>
 </tr>
</table><p>
<center><b>The ForuMAX Team would like to thank the following people (in alphabetical order)</b></center><p>
<table width="100%" border="0" cellspacing="1" cellpadding="4" class="tab5">
 <tr class="tab2">
  <td width="33%" align="center">Shawn Corio</td>
  <td width="34%" align="center">Douglas Hazard</td>
  <td width="33%" align="center">Dion Jones</td>
 </td>
</table>
<?php
    cpShowFooter();
 ?>
