#!/usr/bin/perl
#
# ForuMAX Version 4.0 - user_db_mysql.pl
#
# This contains all functions to interact with the user database, using MySQL
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# include the options file
require "forum_options.pl";

# include our configuration file
require "userdb_conf_mysql.pl";

# $USERDB_DESC is the description of this user database module
$USERDB_DESC="ForuMAX MySQL-driven user database";

# $USERDB_VER is the version of this user database module
$USERDB_VER="4.0";

# we need DBI
use DBI;

#
# GetUserRecord($username)
#
# This will retrieve an user record line from the account file. It will return
# an empty string if user $username could not be found.
#
sub
GetUserRecord() {
    # get the parameters
    my ($username) = @_;

    my $query = sprintf ("select * from %s where accountname = ?", $MYSQL_USERTABLENAME);
    my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

    $sth->execute ($username) || &error ("Cannot execute MySQL query");

    # get all fields and concate them
    my @data = $sth->fetchrow_array();
    my $result = "";
    foreach $it (@data) {
	$result .= $it . ":";
    }
    $sth->finish();

    return $result;
}

#
# SetUserRecord($theuser,$therecord)
#
# This will change database entry $username to $record. It will add $username
# if it is not there. If $therecord is an empty string, it'll delete the
# account.
#
sub
SetUserRecord() {
    # get the arguments
    my ($theuser,$therecord)=@_;

    # do we need to delete this old record?
    if ($therecord eq "") {
        # yup. kill the old record
        my $query = sprintf ("delete from %s where accountname = ?", $MYSQL_USERTABLENAME);
        my $sth = $dbh->prepare ($query);
        $sth->execute ($theuser);
        $sth->finish();
    } else {
        # no, just modify it.

        # get the record
        my ($accountname, $password, $flags, $nofposts, $fullname, $email, $signature, $extra, $parent_email) = split (/:/, $therecord);

	# build the query
        my $query = sprintf ("update %s set accountname=?,password=?,flags=?,nofposts=?,fullname=?,email=?,signature=?,extra=?,parent_email=? where accountname=?", $MYSQL_USERTABLENAME);
        my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

        $sth->execute ($accountname, $password, $flags, $nofposts, $fullname, $email, $signature, $extra, $parent_email, $theuser) || &error ("Cannot execute MySQL query");
        $sth->finish();

	# did this yield any results?
	if ($sth->rows eq 0) {
	    # no. insert the row
            my $query = sprintf ("insert into %s values (?,?,?,?,?,?,?,?,?)", $MYSQL_USERTABLENAME);

            my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

            $sth->execute ($accountname, $password, $flags, $nofposts, $fullname, $email, $signature, $extra, $parent_email) || &error ("Cannot execute MySQL query");
            $sth->finish();
	}
    }
}

#
# GetAllAccounts()
#
# This will return an array of all accounts, with the information lines.
#
sub
GetAllAccounts() {
    # get the arguments
    my ($theuser,$therecord)=@_;

    my $query = sprintf ("select * from %s", $MYSQL_USERTABLENAME);
    my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

    $sth->execute () || &error ("Cannot execute MySQL query");

    # get all fields and concate them
    my $result; my @the_list;
    while (@data = $sth->fetchrow_array()) {
	$result = "";
        foreach $it (@data) {
   	    $result .= $it . ":";
        }
        push (@the_list, $result);
    }
    $sth->finish();

    return @the_list;
}

#
# GetNofAccounts()
#
# This will return the number of accounts we have registered in our user
# database.
#
sub
GetNofAccounts() {
    my $query = sprintf ("select count(*) from %s", $MYSQL_USERTABLENAME);
    my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

    $sth->execute () || &error ("Cannot execute MySQL query");
    my @data = $sth->fetchrow_array();
    $sth->finish();

    return $data[0];
}

#
# GetGroupRecord($groupname)
#
# This will retrieve grpu record line from the group database file. It will
# return an empty string if group $groupname could not be found.
#
sub
GetGroupRecord() {
    # get the parameters
    my ($groupname) = @_;

    my $query = sprintf ("select * from %s where groupname=?", $MYSQL_GROUPTABLENAME);
    my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

    $sth->execute ($groupname) || &error ("Cannot execute MySQL query");

    # get all fields and concate them
    my @data = $sth->fetchrow_array();
    my $result = "";
    foreach $it (@data) {
	$result .= $it . ":";
    }
    $sth->finish();

    return $result;
}

#
# SetGroupRecord($thegroup,$therecord)
#
# This will change database entry $thegroup to $therecord. It will add $thegroup
# if it is not there. If $therecord is an empty string, it'll delete the
# entry.
#
sub
SetGroupRecord() {
    # get the arguments
    my ($thegroup,$therecord)=@_;

    # do we need to delete this old record?
    if ($therecord eq "") {
        # yup. kill the old record
        my $query = sprintf ("delete from %s where groupname=?", $MYSQL_GROUPTABLENAME);
        my $sth = $dbh->prepare ($query);
        $sth->execute ($thegroup);
        $sth->finish();
    } else {
        # no, just modify it.

        # get the record
        my ($groupname, $groupid, $groupdesc, $groupmembers) = split (/:/, $therecord);

	# build the query
        my $query = sprintf ("update %s set groupname=?,groupid=?,description=?,members=? where groupname=?", $MYSQL_GROUPTABLENAME);
        my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

        $sth->execute ($groupname, $groupid, $groupdesc, $groupmembers, $thegroup) || &error ("Cannot execute MySQL query");
        $sth->finish();

	# did this yield any resulsts?
	if ($sth->rows eq 0) {
	    # no. insert it instead.
	    # build the query
            my $query = sprintf ("insert into %s values (?,?,?,?)", $MYSQL_GROUPTABLENAME);
            my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

            $sth->execute ($groupname, $groupid, $groupdesc, $groupmembers) || &error ("Cannot execute MySQL query");
            $sth->finish();
	}
    }
}

#
# GetAllGroups()
#
# This will return an array of all groups, with the information lines.
#
sub
GetAllGroups() {
    # get the arguments
    my ($thegroup,$therecord)=@_;

    my $query = sprintf ("select * from %s", $MYSQL_GROUPTABLENAME);
    my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

    $sth->execute () || &error ("Cannot execute MySQL query");

    # get all fields and concate them
    my $result; my @the_list;
    while (@data = $sth->fetchrow_array()) {
	$result = "";
        foreach $it (@data) {
   	    $result .= $it . ":";
        }
        push (@the_list, $result);
    }
    $sth->finish();

    return @the_list;
}

#
# GetNofGroups()
#
# This will return the number of groups we have registered in our database.
#
sub
GetNofGroups() {
    my $query = sprintf ("select count(*) from %s", $MYSQL_GROUPTABLENAME);
    my $sth = $dbh->prepare ($query) || &error ("Cannot prepare MySQL query");

    $sth->execute () || &error ("Cannot execute MySQL query");
    my @data = $sth->fetchrow_array();
    $sth->finish();

    return @data[0];
}

# connect to the MySQL database
$db_string = sprintf ("DBI:mysql:database=%s;host=%s", $MYSQL_DBNAME, $MYSQL_HOST);

$dbh = DBI->connect($db_string, $MYSQL_USERNAME, $MYSQL_PASSWORD);

# did this work?
if (!$dbh) {
    # no. quit
    &error("Could not connect with the MySQL server. Inform the administrator!");
}

1;
