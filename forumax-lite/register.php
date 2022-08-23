<?php
    //
    // register.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the registration of new accounts.
    //

    // we need our library
    require "lib.php";

    // is an action supplied?
    if ($action == "") {
	// no. show the page
	ShowHeader ("Register account");
 ?>
<form action="register.php" method="post">
<input type="hidden" name="action" value="register">
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr class="content">
   <td class="fheading" align="center" colspan="2">Register an account</td>
 </tr>
 <tr class="content">
  <td class="fnormal" width="20%"><b>Account Name</b></td>
  <td width="80%"><input type="text" name="accountname"></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Password</b></td>
  <td><input type="password" name="password1"></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Password <small>(confirmation)</small></b></td>
  <td><input type="password" name="password2"></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Email address</b></td>
  <td><input type="text" name="email"></td>
 </tr>
 <tr class="content">
   <td align="center" colspan="2"><br><input type="submit" value="Register this account"><p>
   </td>
 </tr>
</table>
</form>
<?php
	ShowFooter();
	exit;
    }

    // are all fields filled in?
    $accountname = trim ($accountname); $password1 = trim ($password1);
    $password2 = trim ($password2); $email = trim ($email);
    if (($accountname == "") or ($password1 == "") or ($password2 == "") or ($email == "")) {
	// no. complain
	Error ("All fields must be filled in");
    }

    // are the passwords equal?
    if ($password1 != $password2) {
	// no. complain
	Error ("Passwords are not equal");
    }

    // account name already in use?
    $query = sprintf ("SELECT id FROM accounts WHERE name='%s'", $accountname);
    if (db_nof_results (db_query ($query)) > 0) {
	// yes. complain
	Error ("Account name already exists, please pick another one");
    }

    // email address already used?
    $query = sprintf ("SELECT id FROM accounts WHERE email='%s'", $email);
    if (db_nof_results (db_query ($query)) > 0) {
	// yes. complain
	Error ("Email address already used. You may not register multiple accounts with the same email address");
    }

    // all looks okay. create the account
    $query = sprintf ("INSERT INTO accounts VALUES (NULL,'%s','%s','%s',now(),0,'',0)", $accountname, $password1, $email);
    db_query ($query);

    // inform the user
    ShowHeader ("Account successfully created");
?>The account has successfully been created.<p>
<form action="index.php" method="post">
<center><input type="submit" value="Return to forum overview">
</form>
<?php
    ShowFooter();
 ?>
