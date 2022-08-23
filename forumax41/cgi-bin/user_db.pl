#!/usr/bin/perl
#
# ForuMAX Version 4.1 - user_db_text.pl
#
# This contains all functions to interact with the user database
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# include the options file
require "forum_options.pl";

# $USERDB_DESC is the description of this user database module
$USERDB_DESC="ForuMAX textfile-driven user database";

# $USERDB_VER is the version of this user database module
$USERDB_VER="4.0";

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

    # open the user database
    open(ACCOUNTFILE,$USERDB_FILE)||&error($ERROR_FILEOPENERR);
    
    # scan it line by line
    while(<ACCOUNTFILE>) {
        # store the line
        my $line = $_;
	chop $line;

        # dump the user name in $thename
        my ($thename) = split(/:/,$line);

        # is this the chosen one?
        if ($thename eq $username) {
	    # yup. close the file and return the line
            close(ACCOUNTFILE);
	    return $line;
        }
    }

    # close the file
    close(ACCOUNTFILE);

    # return a blank line
    return "";
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

    # does the lockfile exists?
    if ( -f $USERDB_LOCKFILE) {
        # yup. the accounts database file is locked
        &error($ERROR_DBLOCKED);
    }

    # create the lock file
    open(LOCKFILE,"+>" . $USERDB_LOCKFILE)||&error($ERROR_FILECREATERR);

    # open the database file
    open(ACCOUNTFILE,$USERDB_FILE)||&error($ERROR_FILEOPENERR);

    my $changed="0";

    # scan the database file line by line
    while (<ACCOUNTFILE>) {
        # get the line
	my $line=$_;

	# is this the line?
	my ($tmpuser) = split(/:/,$line);
	if ($tmpuser eq $theuser) {
	    # yup. set it
	    if ($therecord ne "") { print LOCKFILE $therecord . "\n"; }
	    $changed="1";
	} else {
	    # otherwise just add the line
	    $/ = "\n";
	    chomp $line;
	    print LOCKFILE $line . "\n";
	}
    }
    # was something changed?
    if ($changed eq "0") {
	# nope. add the line
	if ($therecord ne "") { print LOCKFILE $therecord . "\n" }
    }

    # close the database file
    close(ACCOUNTFILE);

    # close the lockfile
    close(LOCKFILE);

    # copy the old fle over the new file.
    &CopyFile($USERDB_LOCKFILE,$USERDB_FILE);
}

#
# GetAllAccounts()
#
# This will return an array of all accounts, with the information lines.
#
sub
GetAllAccounts() {
    # open the accounts file
    open(ACCOUNTSFILE,$USERDB_FILE)||&error($ERROR_FILEOPENERR);

    # read every line
    my @result;
    while (<ACCOUNTSFILE>) {
	# get the line
	my $line = $_;
	chop $line;

	# add it to the results
	push(@result,$line);
    }

    # close the accounts file
    close(ACCOUNTSFILE);

    # return them
    return @result;
}

#
# GetNofAccounts()
#
# This will return the number of account we have registered in our database.
#
sub
GetNofAccounts() {
    # open the accounts file
    open(ACCOUNTSFILE,$USERDB_FILE)||&error($ERROR_FILEOPENERR);

    # read every line
    my $count = 0;
    while (<ACCOUNTSFILE>) {
	# increment the counter
	$count++;
    }

    # close the accounts file
    close(ACCOUNTSFILE);

    # return the count
    return $count;
}

#
# GetGroupRecord($groupname)
#
# This will retrieve a group record line from the group file. It will return
# an empty string if group $groupname could not be found.
#
sub
GetGroupRecord() {
    # get the parameters
    my ($groupname) = @_;

    # open the user database
    open(GROUPFILE,$GROUPDB_FILE)||&error($ERROR_FILEOPENERR);
    
    # scan it line by line
    while(<GROUPFILE>) {
        # store the line
        my $line = $_;
	chop $line;

        # dump the group name in $thename
        my ($thename) = split(/:/,$line);

        # is this the chosen one?
        if ($thename eq $groupname) {
	    # yup. close the file and return the line
            close(GROUPFILE);
	    return $line;
        }
    }

    # close the file
    close(GROUPFILE);

    # return a blank line
    return "";
}

#
# SetGroupRecord($thegroup,$therecord)
#
# This will change group database entry $groupname to $record. It will add
# $thegroup if it is not there. If $therecord is an empty string, it'll delete
# the group.
#
sub
SetGroupRecord() {
    # get the arguments
    my ($thegroup,$therecord)=@_;

    # does the lockfile exists?
    if ( -f $GROUPDB_LOCKFILE) {
        # yup. the accounts database file is locked
        &error($ERROR_DBLOCKED);
    }

    # create the lock file
    open(LOCKFILE,"+>" . $GROUPDB_LOCKFILE)||&error($ERROR_FILECREATERR);

    # open the database file
    open(GROUPFILE,$GROUPDB_FILE)||&error($ERROR_FILEOPENERR);

    my $changed="0";

    # scan the database file line by line
    while (<GROUPFILE>) {
        # get the line
	my $line=$_;

	# is this the line?
	my ($tmpgroup) = split(/:/,$line);
	if ($tmpgroup eq $thegroup) {
	    # yup. set it
	    if ($therecord ne "") { print LOCKFILE $therecord . "\n"; }
	    $changed="1";
	} else {
	    # otherwise just add the line
	    $/ = "\n";
	    chomp $line;
	    print LOCKFILE $line . "\n";
	}
    }

    # was something changed?
    if ($changed eq "0") {
	# nope. add the line
	if ($therecord ne "") { print LOCKFILE $therecord . "\n" }
    }

    # close the database file
    close(GROUPFILE);

    # close the lockfile
    close(LOCKFILE);

    # copy the old fle over the new file.
    &CopyFile($GROUPDB_LOCKFILE,$GROUPDB_FILE);
}

#
# GetAllGroups()
#
# This will return an array of all groups, with the information lines.
#
sub
GetAllGroups() {
    # open the accounts file
    open(GROUPFILE,$GROUPDB_FILE)||&error($ERROR_FILEOPENERR);

    # read every line
    my @result;
    while (<GROUPFILE>) {
	# get the line
	my $line = $_;
	chop $line;

	# add it to the results
	push(@result,$line);
    }

    # close the accounts file
    close(GROUPFILE);

    # return them
    return @result;
}

#
# GetNofGroups()
#
# This will return the number of groups we have registered in our database.
#
sub
GetNofGroups() {
    # open the accounts file
    open(GROUPFILE,$GROUPDB_FILE)||&error($ERROR_FILEOPENERR);

    # read every line
    my $count = 0;
    while (<GROUPFILE>) {
	# increment the counter
	$count++;
    }

    # close the accounts file
    close(GROUPFILE);

    # return the count
    return $count;
}

1;
