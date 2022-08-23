#!/usr/bin/perl
#
# ForuMAX Version 4.0 - conv_db.cgi
#
# This will convert an accounts database to something else.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# $PURPOSE_CONV is the purpose of our conversation
$PURPOSE_CONV="Convert the current ForuMAX database format to MySQL and alter ForuMAX to use the MySQL database";

# use our library files
require "forum_options.pl";
require "forum_lib.pl";
require "user_db.pl";

# we need DBI
use DBI;

#
# StartPage()
#
# This will build a page and display it.
#
sub
StartPage() {
    printf qq~
<html><head><title>ForuMAX accounts migration utility</title></head><style type="text/css">
body { margin: 0px 0px 0px 0px }
</style><body>
<table width="100%" cellspacing=0 cellpadding=1>
<tr bgcolor="#0000fff"><td width="12%">&nbsp;</td><td></td><td><font size=6 color="#ffff00">ForuMAX account migration utility</font></td></tr>
<tr height="400"><td align="center" bgcolor="#0000fff" width="12%"><font color="#ffff00"><br>
~;

    # show all steps
    if ($field{"action"} eq "") {
	printf "<b>>Introduction<</b><p>";
    } else {
	printf "Introduction<p>";
    }

    if ($field{"action"} eq "login") {
	printf "<b>>Confirm identity<</b><p>";
    } else {
	printf "Confirm identity<p>";
    }

    if ($field{"action"} eq "queryinfo") {
	printf "<b>>Acquire needed information<</b><p>";
    } else {
	printf "Acquire needed information<p>";
    }

    if ($field{"action"} eq "modifyconf") {
	printf "<b>>Modify forum configuration<</b><p>";
    } else {
	printf "Modify forum configuration<p>";
    }

printf qq~</font></td><td width=5></td><td valign="top">
~;
}

#
# EndPage()
#
# This will end a page and display it.
#
sub
EndPage() {
    printf qq~</td></tr>
</table>
</body></html>
~;
}

#
# Intro()
#
# This will show a generic introduction
#
sub
Intro() {
    &StartPage();

    printf qq~
Welcome to the ForuMAX accounts migration utility! This utility will assist you in converting an account file to the ForuMAX account database format. This version of the utility will perform the following conversation:<p>
<b>$PURPOSE_CONV</b><p>
Should you wish to perform this modification, you can continue. If not, feel free to close the browser window. No actual changes will be made until the <i>Modify Forum Configuration</i> step is reached.<p>
<form action="conv_db.cgi" method="post">
<input type="hidden" name="action" value="login">
<input type="submit" value="Next >>">
</form>
~;

    &EndPage();
}

#
# Login()
#
# This will show the 'Login' page.
#
sub
Login() {
    &StartPage();
    printf qq~
In order to verify your authority in modifying the accounts database, we will need to ensure you are a forum administrator. Please fill in a valid username and password of an administrator of this forum in the boxes below.<p>
<form action="conv_db.cgi" method="post">
<input type="hidden" name="action" value="queryinfo">
<table>
<tr><td>User name</td><td><input type="text" name="username"></td></tr>
<tr><td>Password</td><td><input type="password" name="password"></td></tr>
</table><p>
<input type="submit" value="Next >>">
</form>
~;
    &EndPage();
}

#
# QueryInfo()
#
# This will query for the needed information.
#
sub
QueryInfo() {
    &StartPage();
    printf qq~
In order to be able to store all the current accounts in the MySQL database, you will need to supply us the username and password of the MySQL user that will be used to connect. The hostname or IP address of the destination MySQL computer must be supplied as well.<p>
We will also need the database name of the database in which we have to create the table, as well as the table name. The table name <b>must</b> be unique, for it will be created. If it's not unique, an error will be triggered.<p>
<form action="conv_db.cgi" method="post">
<input type="hidden" name="action" value="modifyconf">
<input type="hidden" name="id" value="~; printf $field{"id"};
printf qq~">
<table>
<tr><td>MySQL host name/IP address</td><td><input type="text" name="hostname"></td></tr>
<tr><td>MySQL user name</td><td><input type="text" name="dbusername"></td></tr>
<tr><td>MySQL password</td><td><input type="password" name="dbpassword"></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>MySQL database name</td><td><input type="text" name="dbname"></td></tr>
<tr><td>MySQL user table name</td><td><input type="text" name="user_tablename"></td></tr>
<tr><td>MySQL group table name</td><td><input type="text" name="group_tablename"></td></tr>
</table><p>
<b>NOTICE</b> When you now click the <i>Next >></i> link, all changes will be applied! You will <i>not</i> be able to cancel the progress!<p>
<input type="submit" value="Next >>">
</form>
~;
    &EndPage();
}

#
# ConvError($error,$dbstat)
#
# This will show error $error and end the page. If $dbstat is non-zero, it will
# also show a database error message.
#
sub
ConvError() {
    # get the arguments
    my ($error,$dbstat) = @_;

    printf qq~<dl><dd><font color=\"#ff0000\">FAILURE</font></dd></dl><p></dl>~;
    printf "We're sorry, but we $error. Please inform your system administrator about this error and try again.<p>";

    if ($dbstat > 0) {
        printf "The error was:<p>";
        printf "<code><b>" . DBI->errstr . "</b></code>";
    }

    &EndPage();
    exit;
}

#
# CopyFileNoDel ($source,$dest)
#
# This will copy file $source to $dest, *without* deleting any files.
#
sub
CopyFileNoDel() {
    # get the arguments
    my ($source,$dest)=@_;

    open(IN,"<" . $source)||&ConvError("couldn't open <code>$source</code>", 0);
    open(OUT,"+>" . $dest)||&ConvError("couldn't create <code>$dest</code>", 0);
    while (<IN>) {
	print OUT $_;
    }
    close(OUT);
    close(IN);
}

#
# ModifyConf()
#
# This will actually change the forum configuration
#
sub
ModifyConf() {
    # disable output buffering
    $| = 1;

    # build the layout
    &StartPage();

    printf "<dl><b>Migration status</b>";
    printf "<dd>Retrieving users from current ForuMAX database</dd>";
    my @accounts = &GetAllAccounts();
    my $nofaccounts = @accounts;
    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Retrieving groups from current ForuMAX database</dd>";
    my @groups = &GetAllGroups();
    my $nofgroups = @groups;
    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Logging in to MySQL database</dd>";

    # connect to the MySQL database
    $db_string = sprintf ("DBI:mysql:database=%s;host=%s", $field{"dbname"}, $field{"hostname"});

    $dbh = DBI->connect($db_string, $field{"dbusername"}, $field{"dbpassword"});

    # did this work?	
    &ConvError ("couldn't login to the database", 1) unless $dbh;

    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Creating table <code>%s</code></dd>", $field{"user_tablename"};
    my $query = sprintf ("create table %s (accountname varchar(64) primary key not null,password varchar(64) not null,flags varchar(32) not null,nofposts bigint not null,fullname varchar(64) not null,email varchar(64) not null,signature text not null,extra text not null,parent_email varchar(64) not null)", $field{"user_tablename"});

    $dbh->do ($query) || &ConvError ("couldn't execute the SQL query", 1);

    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Migrating " . $nofaccounts . " account";
    if ($nofaccounts != 1) { printf "s"; }
    printf " to the database</dd>";

    foreach $the_account (@accounts) {
	# split the record
        my ($accountname, $password, $flags, $nofposts, $fullname, $email, $signature, $extra, $parent_email) = split (/:/, $the_account);

	# build the query
        my $query = sprintf ("insert into %s values (?, ?, ?, ?, ?, ?, ?, ?, ?)", $field{"user_tablename"});
        my $sth = $dbh->prepare ($query) || &ConvError ("couldn't prepare the SQL query", 1);
        $sth->execute ($accountname, $password, $flags, $nofposts, $fullname, $email, $signature, $extra, $parent_email) || &ConvError ("couldn't execute the SQL query", 1);
        $sth->finish();
    }
    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Creating table <code>%s</code></dd>", $field{"group_tablename"};
    my $query = sprintf ("create table %s (groupname varchar(64) primary key not null,groupid bigint not null,description varchar(128) not null,members text not null)", $field{"group_tablename"});

    $dbh->do ($query) || &ConvError ("couldn't execute the SQL query", 1);

    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Migrating " . $nofgroups . " group";
    if ($nofgroups != 1) { printf "s"; }
    printf " to the database</dd>";

    foreach $the_group (@groups) {
	# split the record
        my ($groupname, $groupid, $desc, $members) = split (/:/, $the_group);

	# build the query
        my $query = sprintf ("insert into %s values (?, ?, ?, ?)", $field{"group_tablename"});
        my $sth = $dbh->prepare ($query) || &ConvError ("couldn't prepare the SQL query", 1);
        $sth->execute ($groupname, $groupid, $desc, $members) || &ConvError ("couldn't execute the SQL query", 1);
        $sth->finish();
    }
    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Writing configuration file for the new user module</dd>";
    open(CONF,"+>userdb_conf_mysql.pl")||&ConvError("couldn't create <code>userdb_conf_mysql.pl</code> configuration file", 0);
    printf CONF "#!/usr/bin/perl\n";
    printf CONF "#\n";
    printf CONF "# userdb_conf_mysql.pl\n";
    printf CONF "#\n";
    printf CONF "# This is the configuration file of the user's database, in this case, of the\n";
    printf CONF "# MySQL module.\n";
    printf CONF "#\n\n";
    printf CONF "# \$MYSQL_USERNAME is the name of whoever we use to access the database\n";
    printf CONF "\$MYSQL_USERNAME=qq~%s~;\n\n",$field{"dbusername"};

    printf CONF "# \$MYSQL_HOST is the name or IP of whatever host we send our MySQL\n";
    printf CONF "# queries to\n";
    printf CONF "\$MYSQL_HOST=qq~%s~;\n\n",$field{"hostname"};

    printf CONF "# \$MYSQL_PASSWORD is the password of whoever we use to connect\n";
    printf CONF "# to the MySQL database\n";
    printf CONF "\$MYSQL_PASSWORD=qq~%s~;\n\n",$field{"dbpassword"};

    printf CONF "# \$MYSQL_USERTABLENAME is the name of whatever table we use to store\n";
    printf CONF "# our users in\n";
    printf CONF "\$MYSQL_USERTABLENAME=qq~%s~;\n\n",$field{"user_tablename"};

    printf CONF "# \$MYSQL_GROUPTABLENAME is the name of whatever table we use to store\n";
    printf CONF "# our groups in\n";
    printf CONF "\$MYSQL_GROUPTABLENAME=qq~%s~;\n\n",$field{"group_tablename"};

    printf CONF "# \$MYSQL_DBNAME is the name of whatever database we use to store\n";
    printf CONF "# our users in\n";

    printf CONF "# \$MYSQL_DBNAME is the name of whatever database we use to store\n";
    printf CONF "# our users in\n";
    printf CONF "\$MYSQL_DBNAME=qq~%s~;\n\n1;",$field{"dbname"};
    close (CONF);

    # hide it from prying eyes
    chmod(0700,"userdb_conf_mysql.pl");

    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Backing up old user module</dd>";

    # make the backup
    &CopyFileNoDel ("user_db.pl", "user_db.pl.OLD");

    # make the backup read-only, and only accessible by the current user
    chmod(0400, "user_db.pl.OLD");

    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";

    printf "<dd>Activating new user module</dd>";
    &CopyFileNoDel ("user_db_mysql.pl", "user_db.pl");
    printf "<dl><dd><font color=\"#008000\">SUCCESS</font></dd></dl>";
    printf "</dd></dl><p>";

    printf qq~
Congratulations! You have successfully migrated your current user database to a MySQL version. ForuMAX will use the MySQL database from now on. It is suggested you now delete the old accounts file (<code>~; printf $USERDB_FILE; printf qq~</code>) and the old group file (<code>~; printf $GROUPDB_FILE; printf qq~</code>) from the sytem. They will not be used anymore.<p>
~;
}

HTMLHeader();

# do we have a valid action?
if ($field{"action"} eq "") {
    # no. show the intro
    &Intro();
    exit;
}

# need to log in?
if ($field{"action"} eq "login") {
    # yup. do it
    &Login();
    exit;
}

# if we reach this step, we need to be logged in. verify the username/password
# pair.

# do we have a login hash?
if ($field{"id"} eq "") {
    # no. generate one
    $field{"id"} = &HashID ($field{"username"}, $field{"password"});
}

# is the hash valid?
&VerifyHash($field{"id"});

# do we have to acquire the needed information?
if ($field{"action"} eq "queryinfo") {
    # yup. query for it
    &QueryInfo();
    exit;
}

# can we now finally modify the actual configuration? :)
if ($field{"action"} eq "modifyconf") {
    # yup. do it
    &ModifyConf();
    exit;
}
