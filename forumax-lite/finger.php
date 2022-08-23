<?php
    //
    // finger.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This will handle the displayal of account information.
    //

    // we need our library
    require "lib.php";

    // is an accountid given?
    $accountid = trim ($accountid);
    if ($accountid == "") {
	// no. complain
	Error ("No Account ID supplied. Check your link and try again");
    }

    // get the account information
    $query = sprintf ("SELECT name,email,joindate,flags,lastpost,nofposts FROM accounts WHERE id=%s", $accountid);
    $res = db_query ($query);
    list ($name, $email, $joindate, $flags, $lastpost, $nofposts) = db_fetch_result ($res);

    // does the account really exist?
    if (db_nof_results ($res) == 0) {
	// no. complain
	Error ("No such account. Check your link and try again");
    }

    // all looks good. show the information
    ShowHeader ("Information on account " . $name);
 ?><table width="100%">
 <tr>
  <td width="100%" align="right"><a href="javascript:history.go(-1);"><b>[Go Back]</b></a></td>
 </tr>
</table>
<table width="100%" cellspacing="1" cellpadding="3" border="0" class="heading">
 <tr>
   <td class="fheading" align="center" colspan="2">User information</td>
 </tr>
 <tr class="content">
  <td class="fnormal" width="20%"><b>Account Name</b></td>
  <td class="fnormal" width="80%"><?php echo $name; ?></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Email address</b></td>
  <td class="fnormal" width="80%"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Number of posts</b></td>
  <td class="fnormal" width="80%"><?php echo $nofposts; ?></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Member status</b></td>
  <td class="fnormal"><?php echo GetAccountStatus ($accountid, $flags); ?></td>
 </tr>
 <tr class="content">
  <td class="fnormal"><b>Last post</b></td>
  <td class="fnormal"><?php echo $lastpost; ?></td>
 </tr>
</table>
<?php
    ShowFooter();
 ?>
