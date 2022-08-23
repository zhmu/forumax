<?php
    //
    // login.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will logging in to the forum.
    //

    // we need our library
    require "lib.php";

    // is an action given?
    if ($action == "") {
	// no. just show the login page
	ShowHeader ("Login");
 ?><form action="login.php" method="post">
<input type="hidden" name="action" value="login">
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
  <tr>
    <td width="100%" align="center" class="fheading" colspan=2>Please log in</td>
  </tr>
 <tr class="content">
   <td width="20%" align="left" class="fnormal"><b>Username</b></td> 
   <td width="80%" align="left"><input type="text" name="username"></td>
 </tr>
 <tr class="content">
   <td align="left" class="fnormal"><b>Password</b></td> 
   <td align="left"><input type="password" name="password"></td>
 </tr>
 <tr class="content">
   <td align="center" colspan=2><br><input type="submit" value="Login"><p></form></td>
 </tr>
</table>
<?php
	ShowFooter();
	exit;
    }

    // verify the username/password
    VerifyAccount ($username, $password);

    // create a cookie
    SetCookie ("auth_cookie", $username . "|^|" . $password, time() + 7200);

    // redirect back to index.php
    Header ("Location: index.php");
 ?>
