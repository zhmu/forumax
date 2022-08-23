#!/usr/bin/perl
#
# ForuMAX Version 4.1 - forum_lib.pl
#
# This contains all kinds of library functions.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# $FORUM_VERSION is the forum version. Don't mess with it.
$FORUM_VERSION="4.1";

# include the options file
require "forum_options.pl";

# include the user database functions
require "user_db.pl";

# we use socket stuff as well
use Socket;

# $ERROR_xxx are error messages
$ERROR_FORUMLOCKED="Forum is currently locked. Try again later";
$ERROR_NOMSG="Forum message does not exists";
$ERROR_POSTDISABLED="Posting is disabled for this account";
$ERROR_FILEOPENERR="Unable to open file. Check your setup";
$ERROR_FILECREATERR="Unable to create file. Check your setup";
$ERROR_FILEDELETERR="Unable to delete file. Check your setup";
$ERROR_ACCESSDENIED="Access denied";
$ERROR_DBLOCKED="User database is locked";
$ERROR_RENAMERR="Unable to rename file. Check your setup";
$ERROR_CHMODERR="Unable to set file permissions. Check your setup";

# $FLAG_xxx are the account flags
$FLAG_ADMIN="A";
$FLAG_DISABLED="D";
$FLAG_DENYSIG="S";
$FLAG_DONTCHECKSIG="C";
$FLAG_MEGAMOD="M";
$FLAG_UNDER13="U";
$FLAG_MOD="m";
$FLAG_SUPERMOD="s";

# $FLAG_FORUM_xxx are the forum flags
$FLAG_FORUM_LOCKED="L";
$FLAG_FORUM_NOEDIT="e";
$FLAG_FORUM_NODELETE="d";
$FLAG_FORUM_DISABLED="D";
$FLAG_FORUM_HTMLOK="H";
$FLAG_FORUM_MAXOK="M";
$FLAG_FORUM_NOIMG="i";
$FLAG_FORUM_NOSCRIPTEMBED="s";
$FLAG_FORUM_HIDDEN="h";
$FLAG_FORUM_MOVED="m";
$FLAG_FORUM_NOTIFY="N";


# nonfatal_error($message)
#
# This will show error $message. It will not show a footer.
#
sub
nonfatal_error() {
    # get the arguments
    my ($message) = @_;

    # make sure the header is sent
    &HTMLHeader();

    # need to ignore errors?
    if ($ignorerror ne "0") {
	# yup. do it
	return;
    }

    # set up the page
    &InitPage();

    printf "<font face=\"$FORUM_FONT\">We're sorry, but the following error has occoured:<p>";
    printf "<b>$message</b><p>";
    printf "If you have any questions or comments regarding this error, feel free to email the administrator at <a href=\"mailto:$ADMIN_EMAIL\">$ADMIN_EMAIL</a>.</font>";
}

#
# error($message)
#
# This will show error $message and exit.
#
sub
error() {
    # get the arguments
    my ($message) = @_;

    # show the error
    &nonfatal_error($message);

    # end the page
    &NormalPageEnd();

    # leave
    exit 0;
}

#
# deleterror($file,$error)
#
# This will behave excactly like error(), but this one will nuke $file before
# showing the error message.
#
sub
deleterror() {
    # get the arguments
    my ($file,$error)=@_;

    # nuke the file
    unlink($file);

    # show the error
    &error($error);
}

#
# ValidateIdentity($the_username,$the_password)
#
# This will validate whether the username $the_username combined with password
# $the_password is a correct pair. It will return zero if not and non-zero if it
# is.
#
sub
ValidateIdentity() {
    # get the arguments
    my ($the_username,$the_password) = @_;

    # get the database line
    my $line=&GetUserRecord($the_username);

    # was there data?
    if ($line eq "") {
        # nope. die
        return 0;
    }

    # get the password
    my ($tmp,$passw)=split(/:/,$line);

    # does the password match?
    if ($passw ne $the_password) {
        # nope. die
        return 0;
    }

    # set global variables
    $username = $the_username;
    ($tmp,$passw,$flags,$nofposts,$fullname,$email) = split(/:/,$line);

    # it's valid!
    return 1;
}

#
# VerifyHash($hash)
#
# This will verify hash $hash, and return an error if it's not correct.
#
sub
VerifyHash() {
    # get the arguments
    my ($hash)=@_;

    # split it in an username/password pair
    my ($username,$pwd)=split(/:/,$hash);

    # transform this field back
    $username=~tr/\+/ /;

    # get the user information line
    my $line=&GetUserRecord($username);

    # was there an user?
    if ($line eq "") {
	# no. die
        &error($ERROR_ACCESSDENIED);
    }

    # split the line
    ($username,$the_password,$flags,$nofposts,$fullname,$email) = split(/:/,$line);

    # get the date
    my ($date) = split(/\|/,&GetTimeDate());

    # do we use cookies?
    if ($USE_COOKIES eq "YES") {
	# yes. just verify the password
	if ($the_password ne $pwd) {
	    # say it's bad
            &error($ERROR_ACCESSDENIED);
	}
    } else {
	# no. append the date to the password.
        $the_password = $the_password . $date;
        # check the password
        if ((crypt ($the_password, $pwd) ne $pwd) or ($pwd eq "")) {
            # say it's bad
            &error($ERROR_ACCESSDENIED);
        }
    }

    # copy our username to the fields just in case
    $field{"username"}=$username;
}

#
# check_flag($inflags,$flag_to_check)
#
# This will check whether $flag_to_check is present in $inflags. It will return
# zero if not and non-zero if it is.
#
sub
check_flag() {
    # get the arguments
    my ($inflags,$flag_to_check)=@_;

    # copy $inflags to $tmp
    my $tmp=$inflags;

    # change all $flag_to_check chars to #'s.
    $tmp=~s/($flag_to_check)/#/;

    # are they equal?
    if ($tmp eq $inflags) {
        # yeah. flag was not set
	return 0;
    }

    # the flag was set!
    return 1;
}

#
# resolveaccountflags($the_flags)
#
# This will resolve account flags $the_flags to a human-readble string. The
# flags will also include html <FONT COLOR=#xxx> stuff to show them nicer.
#
sub
resolveaccountflags() {
    # get the arguments
    my ($the_flags) = @_;

    # start with nothing
    my $result="";

    # is the administrator flag set?
    if(&check_flag($the_flags,$FLAG_ADMIN) ne 0) {
        # yes! we are an admin!
	$result="<font color=\"#0000ff\">Administrator</font>";
    }

    # is the administrator flag set?
    if(&check_flag($the_flags,$FLAG_MEGAMOD) ne 0) {
        # yes! we are an admin!
	$result="<font color=\"#000080\">Mega Moderator</font>";
    }

    # is the disabled flag set?
    if(&check_flag($the_flags,$FLAG_DISABLED) ne 0) {
        # yes! the account is disabled
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#ff0000\">Disabled</font>";
    }

    # do we deny signatures for this user?
    if(&check_flag($the_flags,$FLAG_DENYSIG) ne 0) {
        # yes! the signatures are disabled
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#ff0000\">Signature disabled</font>";
    }

    # does the account say it's < 13?
    if(&check_flag($the_flags,$FLAG_UNDER13) ne 0) {
	# yes. change that in the status
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#000088\">Under 13</font>";
    }

    # return the flags
    return $result;
}

#
# CopyFilePreserve($source,$dest)
#
# This will copy file $source to $dest.
#
sub
CopyFilePreserve() {
    # get the arguments
    my ($source,$dest)=@_;

    # open the files
    open(SOURCE,$source)||&error($ERROR_FILEOPENERR);
    open(DEST,"+>" . $dest)||&error($ERROR_FILECREATERR);
    
    # copy the entire file
    while ( <SOURCE> ) {
	print DEST $_;
    }

    # close the dest file
    close(DEST);

    # close the source file
    close(SOURCE);
}

#
# CopyFile($source,$dest)
#
# This will copy file $source to $dest. The $source file will be always nuked.
#
sub
CopyFile() {
    # get the arguments
    my ($source,$dest)=@_;

    # open the files
    open(SOURCE,$source)||&deleterror($source,$ERROR_FILEOPENERR);
    open(DEST,"+>" . $dest)||&deleterror($source,$ERROR_FILECREATERR);
    
    # copy the entire file
    while ( <SOURCE> ) {
	print DEST $_;
    }

    # close the dest file
    close(DEST);

    # close the source file
    close(SOURCE);

    # nuke the source file
    unlink($source);
}

#
# TransformForBrowser($string)
#
# This will transform $string so it'll work in the browser. It'll return the
# new string.
#
sub
TransformForBrowser() {
    # get the arguments
    my ($string)=@_;

    # transform it
    $string=~tr/ /+/;

    # return it
    return $string;
}

#
# GetForumInfo($forumname)
#
# This will return the general forum info line of forum $forumname. It will
# return a blank line if the forum is not found.
#
sub
GetForumInfo() {
    # get the arguments
    my ($forumname)=@_;

    # open the forum file
    open(FORUMINFO,$FORUM_DATAFILE)||&error($ERROR_FILEOPENERR);

    # scan though the file line by line
    while (<FORUMINFO>) {
	# get the line
	my $line=$_;
	chop $line;

	# split it
	my ($theforumname)=split(/:/,$line);

	# is this the forum we are looking for?
	if ($theforumname eq $forumname) {
	    # yup. close the file and return the line
            close(FORUMINFO);
	    return $line;
	}
    }

    # close the forum fle
    close(FORUMINFO);

    # return a blank line
    return "";
}

#
# SetForumInfo($theforum,$theinfo)
#
# This will change the forum info of forum $theforum to $theinfo. If the
# forum is not there, it will be created.
#
sub
SetForumInfo() {
    # get the arguments
    my ($theforum,$theinfo)=@_;

    # does the lockfile exists?
    if ( -f $FORUM_LOCKFILE) {
        # yup. the accounts database file is locked
        &error($ERROR_FORUMLOCKED);
    }

    # create the lock file
    open(LOCKFILE,"+>" . $FORUM_LOCKFILE)||&error($ERROR_FILECREATERR);

    # open the database file
    open(FORUMFILE,$FORUM_DATAFILE)||&error($ERROR_FILEOPENERR);

    my $changed="0";

    # scan the forum file line by line
    while (<FORUMFILE>) {
        # get the line
	my $line=$_;
	chop $line;

	# is this the line?
	my ($tmpforum) = split(/:/,$line);
	if ($tmpforum eq $theforum) {
	    # yup. set it
	    if ($theinfo ne "") { print LOCKFILE $theinfo . "\n"; }
	    $changed="1";
	} else {
	    # otherwise just add the line
	    print LOCKFILE $line . "\n";
	}
    }
    # was something changed?
    if ($changed eq "0") {
	# nope. add the line
	if ($theinfo ne "") { print LOCKFILE $theinfo . "\n"; }
    }

    # close the forum datafile
    close(FORUMFILE);

    # close the lockfile
    close(LOCKFILE);

    # copy the old fle over the new file.
    &CopyFile($FORUM_LOCKFILE,$FORUM_DATAFILE);
}

#
# GetTimeDate()
#
# This will return the actual date/time in the form Month-Day-Year|5:12PM
#
sub
GetTimeDate() {
    my $timestr;

    # get the time
    my ($sec,$min,$hour,$day,$mon,$year) = localtime();

    # make sure each value has 2 digits
    $mon = $mon + 1;
    if ($min < "10") { $min = "0" . $min; }
    if ($day < "10") { $day = "0" . $day; }
    if ($mon < "10") { $mon = "0" . $mon; }

    # do the day-month-year
    $year = 1900 + $year; 

    $timestr = $mon . "-" . $day . "-" . $year;

    # and add the time
    $AMPM = "AM";
    if ($hour > "12") {
        # do the 12 hour notatation for our American friends <g>
        $hour = $hour - "12";
        $AMPM = "PM";
    }
    $timestr = $timestr . "|" . $hour . ":" . $min . " " . $AMPM;

    # return this
    return $timestr;
}

#
# TruncateFile($file)
#
# This will truncate file $file to zero bytes. If the file doesn't exists, it
# will create it. It will die on any error.
#
sub
TruncateFile() {
    # get the arguments
    my ($file)=@_;

    # create the file, enforcing overwrite
    open(TMPFILE,"+>" . $file)||&error($ERROR_FILECREATERR);

    # close it
    close(TMP);
}

#
# VerifyID()
#
# This will check your authorization. It will expect your user name to be in
# $field{"username"} and the password in $field{"password"}. It will also
# set $flags to your flags. It will show an error if anything goes wrong.
#
sub
VerifyID() {
    # if the user gave us an id value, check that
    if ($field{"id"} ne "") {
        &VerifyHash($field{"id"});
        return;
    }

    if ($cookie{"id"} ne "") {
        &VerifyHash($cookie{"id"});
        return;
    }

    # check the access
    if (&ValidateIdentity($field{"username"},$field{"password"}) eq "0") {
        # this is not correct. show error
        &error($ERROR_ACCESSDENIED);
    }

    # get the record of this account
    my $userline=&GetUserRecord($field{"username"});

    # split the info
    ($tmp,$passwd,$flags,$nofposts,$fullname,$email,$sig,$extra)=split(/:/,$userline);

    $username=$field{"username"};

    # disabled account?
    if (&check_flag($flags,$FLAG_DISABLED) ne 0) {
	# yup. deny access
        &error($ERROR_POSTDISABLED);
    }

    # do we need to force login?
    if ($FORCE_LOGIN eq "YES") {
	# yup. do we need to use cookies for this?
        if ($USE_COOKIES eq "YES") {
	    # yes. set a login cookie
            &SetCookie ("id", &HashID ($field{"username"}, $field{"password"}), 60 * 60 * 31);
	} else {
	    # no. build a has
            $field{"id"} = &HashID($field{"username"},$field{"password"});
	}
    }
}

#
# IsUserInGroup($username,$group)
#
# This will return zero if $username is not within comma-separated group $group
# otherwise non-zero.
#
sub
IsUserInGroup() {
    # get the arguments
    my ($username,$group) = @_;

    # passing this directly to &IsInArray()
    return &IsInArray ($username, split (/,/, $group));
}

#
# IsInArray ($string,@array)
#
# This will browse all indices of $array. If $string is found, it'll return
# non-zero, otherwise zero.
#
sub
IsInArray() {
    # get the arguments
    my ($string,@xarray) = @_;

    # browse them all
    my $item;
    foreach $item (@xarray) {
	# match?
	if ($item eq $string) {
	    # yup. say we got it
	    return 1;
	}
    }

    # no matches found.
    return 0;
}

#
# IsSuperModerator($username)
#
# This will return zero if user $username is no super moderator, otherwise
# non-zero.
#
sub
IsSuperModerator() {
    # get the arguments
    my ($username)=@_;

    # get the category data
    my @cats=&GetCats();

    # check them all
    foreach $cat (@cats) {
	# get the category info
	my ($name,$catno,$supermods)=split(/:/,$cat);

        # are we in the supermod list?
        if (&IsUserInGroup($username,$supermods) ne 0) {
	    # yes. say so
	    return 1;
        }
    }
    # we aren't a supermod.
    return 0;
}

#
# IsModerator($username)
#
# This will return zero if user $username is not a moderator, otherwise
# non-zero.
#
sub
IsModerator() {
    # get the arguments
    my ($username)=@_;

    # grab all forums
    my @forums = &GetForums();

    # scan 'em all
    foreach $forumline (@forums) {
	# split the line
	my ($forumname) = split(/:/, $forumline);

	# is the user a mod here?
	if (&IsInArray ($username, &GetForumMods ($forumname)) ne 0) {
	    # yup. say he's a mod
	    return 1;
	}
    }

    # this user doesn't moderate anything
    return 0;
}

#
# ApplyMaXCodes($text,$forum_flags)
#
# This will apply the MaX codes to $text and return the new stuff. It expects
# $forum_flags to contain the forum flags.
#
sub
ApplyMaXCodes() {
    # get the arguments
    my ($text,$forum_flags)=@_;

    # do we allow images?
    if (&check_flag($forum_flags,$FLAG_FORUM_NOIMG) eq 0) {
        # yup. do them
        $text =~ s/\[img\]((.)+)\[\/img\]/\<img src=\"$1\" alt=\"[Image]\" border=0>/gi;
    }

    # bold, italic and underline
    $text =~ s/(\[b\])((.)+?)(\[\/b\])/<b>$2<\/b>/isg;
    $text =~ s/(\[i\])((.)+?)(\[\/i\])/<i>$2<\/i>/isg;
    $text =~ s/(\[u\])((.)+?)(\[\/u\])/<u>$2<\/u>/isg;

    # links
    $text =~ s/\[url\]((.)+?)\[\/url\]/\<a href=\"$1\" target=\"_blank\"\>$1\<\/a\>/gi;
    $text =~ s/\[url\=((.)+?)\]((.)+)\[\/url\]/\<a href=\"$1\" target=\"_blank\"\>$3\<\/a\>/gi;
    $text =~ s/\[email\]((.)+?)\[\/email\]/\<a href=\"mailto:$1\"\>$1\<\/a\>/gi;

    # other
    $text =~ s/\[code\]((.)+?)\[\/code\]/\<blockquote\>\<font size=1\>code:\<\/font\>\<br\>\<hr\>\<code\>$1\<\/code\>\<\hr\>\<\/blockquote\>/gis;
    $text =~ s/\[quote\]((.)+?)\[\/quote\]/\<blockquote\>\<font size=1\>quote:\<\/font\>\<br\>\<hr\>$1\<\hr\>\<\/blockquote\>/gis;

    # return this
    return $text;
}

#
# ApplySmilies ($text)
#
# This will apply the smilies on text $text
#
sub
ApplySmilies() {
    # get the parameter
    my ($text) = @_;

    @smilies = split(/\|/, $SMILIES);

    foreach $happy (@smilies) {
	my @part = split (/=/, $happy);

	# is there actually a smilie?
	if ($part[0] ne "") {
	    # yup. apply it
	    # first, add a backslash before ( and ) to make sure the search and
	    # replace doesn't mess up
            $part[0]=~ s/\(/\\\(/g;
            $part[0]=~ s/\)/\\\)/g;
            $part[0]=~ s/\!/\\\!/g;
            $part[0]=~ s/\?/\\\?/g;
            $text=~ s/($part[0])/\<img src=\"$IMAGES_URI\/$part[1]\" alt=\"[Smilie]\"\>/gi;
	}
    }

    return $text;
}

#
# EditForumText($text,$forum_flags)
#
# This will edit the forum text, and return the edited thingy. It expects the
# forum flags to be in $forum_flags.
#
sub
EditForumText() {
    # get the parameters
    my ($text,$forum_flags)=@_;

    # list of all disallowed stuff
    my @scriptstuff = ( "onabort", "onafterupdate", "onbeforecopy",
                        "onbeforecut", "onbeforeeditfocus", "onbeforepaste",
                        "onbeforeupdate", "onblur", "oncellchange", "onclick",
                        "oncontextmenu", "oncopy", "onclick", "oncontextmenu",
                        "oncopy", "oncut", "ondataavailable",
                        "ondatasetchanged", "ondatasetcomplete", "ondblclick",
                        "ondrag", "ondragend", "ondragenter", "ondragleave",
                        "ondragover", "ondragstart", "ondrop", "onerror",
                        "onerrorupdate", "onfilterchange", "onfocus",
                        "onhelp", "onkeydown", "onkeypress", "onkeyup",
                        "onload", "onlosecapture", "onmousedown", "onmouseup",
                        "onmousemove", "onmouseout", "onmouseup", "onmouseover",
                        "onpaste", "onpropertychange", "onreadystatechange",
                        "onresize", "onrowenter", "onrowexit", "onrowsdelete",
                        "onrowsinserted", "onscroll", "onselectstart",
                        "javascript:" );
  
    # disallowed < stuff
    my @htmlstuff = ( "script", "embed", "object", "applet", "iframe",
                      "server", "table", "tr", "td", "/tr", "/td", "/table",
                      "meta", "style", "!--", "--", "link" );

    # if no images are allowed, nuke the tags
    if(&check_flag($forum_flags,$FLAG_FORUM_NOIMG) ne 0) {
        $text =~ s/\<img/&lt;img/gi;
    }

    # if no scripts are allowed, nuke the tags
    if(&check_flag($forum_flags,$FLAG_FORUM_NOSCRIPTEMBED) ne 0) {
        for $it (@scriptstuff) {
	    $text =~s /$it/not$it/gi;
        }
        for $it (@htmlstuff) {
	    my $badtag = "<" . $it;
            my $newtag = "&lt;" . $it;
	    $text =~s /$badtag/$newtag/gi;
        }
        $text =~s /\-->/--&gt;/gi;
    }

    # apply the smilies
    $text = &ApplySmilies($text);

    # return the text
    return $text;
}

#
# GetForumFlags($destforum)
#
# This will return the flag list of forum $destforum.
#
sub
GetForumFlags() {
    # get the arguments
    my ($destforum)=@_;

    # get the forum data
    my $line=&GetForumInfo($destforum);
    my ($the_forum,$nofposts,$mods,$restricted,$date1,$date2,$forum_flags) = split(/:/,$line);

    # return the flags
    return $forum_flags;
}

#
# InitPage($body,$header)
#
# This will initialize our page so it has the style stuff we want etc. It will
# add $body to the <BODY> tag. If $header is not a blank string, it will be
# shown instead of the general header. If $refresh is not a blank string, you
# will be redirected to that in 2 sedonds.
#
sub
InitPage() {
    # get the parameter
    my ($body,$header,$refresh) = @_;

    # dump all HTML tags...
    printf "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
    printf "<html><head><title>$FORUM_TITLE";
    # if we haven't got a forum name, or if we should browse to the intro page,
    # don't show that in the title
    if (($field{"forum"} ne "") and ($field{"forum"} ne "intro_page")) {
        printf " - %s",&RestoreSpecialChars ($field{"forum"});
    }
    printf "</title><meta http-equiv=\"pragma\" content=\"no-cache\">";
    printf "<style type=\"text/css\">\n";
    printf ".subjectlink { font: 13px $FORUM_FONT; text-decoration: none; color: $FORUM_COLOR_SUBJECTLINK; }\n";
    printf ".subjectlink:hover { font: 13px $FORUM_FONT; text-decoration: none; color: $FORUM_COLOR_SUBJECTLINK_HOVER }\n";
    printf ".memberlink { font: 13px $FORUM_FONT; color: $FORUM_COLOR_MEMBERLINK; }\n";
    printf ".memberlink:hover { font: 13px $FORUM_FONT; color: $FORUM_COLOR_MEMBERLINK_HOVER; }\n";
    printf ".forumlink { font: 13px $FORUM_FONT; text-decoration: none; color: %s; }\n",$FORUM_COLOR_LIST_FORUMNAME;
    printf ".forumlink:hover { font: 13px $FORUM_FONT; text-decoration: none; color: %s;}\n",$FORUM_COLOR_LIST_FORUMNAME_HOVER;
    printf ".lastpost:link { font 13px $FORUM_FONT; text-decoration: none; color: %s; }\n", $FORUM_COLOR_LASTPOSTER_LINK;
    printf ".lastpost:visited { font 13px $FORUM_FONT; text-decoration: none; color: %s; }\n", $FORUM_COLOR_LASTPOSTER_LINK;
    printf ".lastpost:hover { font 13px $FORUM_FONT; text-decoration: none; color: %s; }\n", $FORUM_COLOR_LASTPOSTER_HOVER;
    printf ".leftlink:link { color: #ffff00 }\n";
    printf $EXTRA_STYLE;
    printf "</style>";
    # disable caching of this page
    printf "<meta http-equiv=\"pragma\" content=\"no-cache\">";
    if ($refresh ne "") {
	# dump in the refresh thingy
        printf "<meta http-equiv=\"Refresh\" content=\"2; url=$refresh\">";
    }

    printf "</head><body text=\"$FORUM_COLOR_TEXT\" bgcolor=\"$FORUM_COLOR_BACKGROUND\" %s><font color=\"$FORUM_COLOR_TEXT\">",$body;

    # now, add the page text
    if ($header eq "") {
        $header = $START_PAGE_TEXT;
    }

    # fix it
    printf &FixHeaderFooter ($header);
}

#
# NormalPageEnd($footer)
#
# This will show a normal page end, eg. the powered by messages and the link
# to your site.
#
sub
NormalPageEnd() {
    # get the parameter
    my ($footer) = @_;

    # shut down the error routines
    $ignorerror = 1;

    # if we need to show the 'hop to' list, do it
    if ($SHOW_HOPTO eq "YES") {
	# get the forum data
        my @forums = &GetForums();

        # add a 'hop to' list of all forums
        printf "<form action=\"forumview.cgi\" method=\"post\">";
        printf "<input type=\"hidden\" name=\"action\" value=\"showforum\">";

        # make a table to align this stuff to the right
        printf "<table width=\"100%\"><tr><td align=\"right\"><font face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_TEXT\">Hop to</font>&nbsp;<select name=\"forum\">";

        # add the entry for the intro page
        printf "<option ";
        # if we have no forum, select this one
        if ($field{"forum"} eq "") {
            printf "selected ";
        }
        printf " value=\"intro_page\">Intro page</option>";

	# only need to show the forums?
	if ($SHOW_CATS ne "YES") {
	    # yup. add them
	    foreach $forumline (@forums) {
                # get the forum info
                my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags,$desc)=split(/:/,$forumline);

                # is this forum enabled and not hidden?
                if ((&check_flag($forum_flags,$FLAG_FORUM_DISABLED) eq "0") and (&check_flag($forum_flags,$FLAG_FORUM_HIDDEN) eq "0")) {
    	            # yup. add it to the list
                    printf "<option ";

		    # is this forum currently active?
	            if ($field{"forum"} eq $forum_name) {
                        # yup. select this one
	                printf "selected ";
	            }

                    printf "value=\"%s\">%s</option>",$forum_name,&RestoreSpecialChars($forum_name);
	        }	
	    }
	} else {
	    my @cats = &GetCats();

	    # add a blank line to improve the readability
	    printf "<option value=\"intro_page\"></option>";

	    # we do have categories! add them one by one
	    my $count = "0";
	    foreach $cat (@cats) {
		# split the line
	        my ($name,$no)=split(/:/,$cat);
		my $nicename = &RestoreSpecialChars ($name);

		# add the name to the list
	        printf "<option value=\"x:$name\"";
	        # are we currently browsing this category?
		if ((($field{"action"} eq "showforum") or ($field{"action"} eq "showcat")) and ($field{"cat"} eq $name)) {	
		    # yup. select it
		    printf " selected";
		}

                printf ">Category: %s</option>",$nicename;

		# now, add all forums that belong in this category
		foreach $forum_line (@forums) {
		    # grab the line
                    my ($forum_name,$tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$forum_flags,$tmp7,$catno)=split(/:/,$forum_line);

		    # is this forum enabled, and does it have the category id?
                    if ((&check_flag($forum_flags,$FLAG_FORUM_DISABLED) eq "0") and ($catno eq $no)) {

		        # yes. add it to the list
                        printf "<option ";

		        # is this forum currently active?
	                if ($field{"forum"} eq $forum_name) {
                            # yup. select this one
	                    printf "selected ";
			}
                        printf "value=\"%s\">%s</option>",$forum_name,&RestoreSpecialChars($forum_name);
		    }
		}

		# increment the number of categories done
		$count++;

	        # add a blank line, if this isn't the last one
		if ($count < @cats) {
	            printf "<option value=\"intro_page\"></option>";
		}
	    }
	}

        # close the file
        close(FORUMDATA);
    
        # end it
        printf "</select>&nbsp;<input type=\"hidden\" name=\"id\" value=\"%s\"><input type=\"submit\" value=\"Go\"></td></tr></table></form>",$field{"id"};
    }

    # add the link to the website
    printf "<p><center><font face=\"$FORUM_FONT\"><a href=\"$WEBSITE_URI\">$WEBSITE_LINK</a></font><br><br>";

    # add useless information
    printf "<font size=2 face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_TEXT\">Powered by <a href=\"http://www.forumax.com\">ForuMAX</a> Version $FORUM_VERSION<br>&copy; 1999, 2000 Rink Springer</font></center>";

    # now, add the page text
    if ($footer eq "") {
        $footer = $END_PAGE_TEXT;
    }

    # Print it
    printf &FixHeaderFooter ($footer);
}

#
# GetMemberStatus($username,$userline,$flags)
#
# This will return the membership status of the account $username, with user
# line $userline. It expects the flags to be in $flags.
#
sub
GetMemberStatus() {
    # get the arguments
    my ($username,$userline,$flags)=@_;

    # was there actually info available?
    if ($userline eq "") {
        # no. the account doesn't exists anymore
        return $TITLE_NOMEMBER;
    }
    
    # split the user line
    my ($tmp,$tmp2,$tmp3,$tmp4,$tmp5,$tmp6,$tmp7,$extra)=split(/:/,$userline);

    # do we have a custom status extra field?
    my @extra_type = split (/\|/, $EXTRA_PROFILE_TYPES);
    my @extra_val = split(/\|\^\|/, $extra);
    my $custom = ""; my $i = 0;
    foreach $type (@extra_type) {
	# is this a custom title field?
	if ($type eq 7) {
	    # yup. dump the value in $custom
	    $custom=$extra_val[$i];
	}
	$i++;
    }

    # do we have a custom title?
    if ($custom ne "") {
	# yup. return it instead
	return $custom;
    }

    # if he is an admin, return that
    if (&check_flag($flags,$FLAG_ADMIN) ne "0") {
        return $TITLE_ADMIN;
    } else {
	# if he is a mega moderator, return that
	if (&check_flag($flags,$FLAG_MEGAMOD) ne 0) {
	    return $TITLE_MEGAMOD;
	}
        # if he is a supermod, return that
	if (&check_flag($flags,$FLAG_SUPERMOD) ne 0) {
	    return $TITLE_SUPERMOD;
	} else {
            # if he is a mod, return that
            if (&check_flag ($flags, $FLAG_MOD) ne 0) {
                return $TITLE_MOD;
	    }
       }
   }
   # it's just an ordinary member
   return $TITLE_MEMBER;
}

#
# encrypt_pwd($pwd)
#
# This will encrypt string $pwd as a password and return the encrypted stuff.
#
sub
encrypt_pwd() {
    my ($pwd) = @_;
    my @saltchars = ("a".."z", "A".."Z", 0..9);

    # set $pepper to a random salt <g>
    srand(time || $$);
    my $pepper = $saltchars[rand ($#saltchars)] . $saltchars[rand ($#saltchars)];

    # encrypt the password
    my $new_pwd = crypt($pwd,$pepper);

    # return the result
    return $new_pwd;
}

#
# HashID($username,$password)
#
# This will generate a hashed ID, that can be used to verify the user.
#
sub
HashID() {
    # get the arguments
    my ($username,$password)=@_;

    # do we use cookies?
    if ($USE_COOKIES eq "YES") {
	# yes. just return it in a username:password form
	return $username . ":" . $password;
    }

    # get the date
    my ($date) = split(/\|/,&GetTimeDate());
    $password = $password . $date;

    # encrypt the password and return it
    return &TransformForBrowser ($username) . ":" . &encrypt_pwd($password);
}

#
# ZapIllegalChars($string)
#
# This will zap all illegal chars from $string and return the result.
#
sub
ZapIllegalChars() {
    # get the arguments
    my ($string)=@_;

    # nuke them
    $string =~ tr/[A-Z][a-z][0-9]\~\`\!\@\#\$\%\^\&\*\(\)\-\+\_\=\'\{\}\. //cd;

    # return the string
    return $string;
}

#
# GetCatNumber($catname)
#
# This will return the number of category $catname, or zero if it was not found.
#
sub
GetCatNumber() {
    # get the arguments
    my ($catname)=@_;

    # open the category file
    open(CATINFO,$CATS_DATAFILE)||&error($ERROR_FILEOPENERR);

    # scan though the file line by line
    while (<CATINFO>) {
	# get the line
	my $line=$_;

	# split it
	my ($name,$no)=split(/:/,$line);

	# is this the one?
	if ($catname eq $name) {
	    # yup. return the number
	    return $no;
	}
    }

    # close the category file
    close(CATINFO);

    # it was not found. return zero
    return 0;
}

#
# GetForums()
#
# This will return a list of all forums.
#
sub
GetForums() {
    my @foruminfo;

    # open the forum datafile
    open(FORUMFILE,$FORUM_DATAFILE)||&error($ERROR_FILEOPENERR);

    # read it all to $foruminfo{}
    while (<FORUMFILE>) {
	my $line=$_;
	chop $line;

	# put it in
	push(@foruminfo,$line);
    }

    # close the forum datafile
    close(FORUMFILE);

    return @foruminfo;
}

#
# GetItemFromList($indexname,@list)
#
# This will return the record of $indexname. It will browse list @list for
# this. It will return a blank string if nothing was found.
#
sub
GetItemFromList() {
    # get the parameters
    my ($indexname,@list)=@_;

    # browse the list
    foreach $it (@list) {
	my ($name) = split(/:/,$it);
	# is this the one?
	if ($indexname eq $name) {
	    # yup. return it
	    return $it;
	}
    }

    # nothing was found. return a blank string
    return "";
}

#
# GetCatInfo($catname)
#
# This will return the category info line of category $catname. It will
# return a blank line if the category is not found.
#
sub
GetCatInfo() {
    # get the arguments
    my ($catname)=@_;

    # open the category file
    open(CATINFO,$CATS_DATAFILE)||&error($ERROR_FILEOPENERR);

    # scan though the file line by line
    while (<CATINFO>) {
	# get the line
	my $line=$_;

	# split it
	my ($name)=split(/:/,$line);

	# is this the forum we are looking for?
	if ($catname eq $name) {
	    # yup. close the file and return the line
            close(CATINFO);
	    return $line;
	}
    }

    # close the category file
    close (CATINFO);

    # return a blank lne
    return "";
}

#
# SetCatInfo($thecat,$theinfo)
#
# This will change the category info of category $thecat to $theinfo. If the
# category is not there, it will be created.
#
sub
SetCatInfo() {
    # get the arguments
    my ($thecat,$theinfo)=@_;

    # does the lockfile exists?
    if ( -f $CATS_LOCKFILE) {
        # yup. the category database file is locked
        &error("Category datafile locked. Try again later");
    }

    # create the lock file
    open(LOCKFILE,"+>" . $CATS_LOCKFILE)||&error($ERROR_FILECREATERR);

    # open the database file
    open(CATSFILE,$CATS_DATAFILE)||&error($ERROR_FILEOPENERR);

    my $changed="0";

    # scan the categories file line by line
    while (<CATSFILE>) {
        # get the line
	my $line=$_;

	# is this the line?
	my ($tmpname) = split(/:/,$line);
	if ($tmpname eq $thecat) {
	    # yup. set it
	    if ($theinfo ne "") { print LOCKFILE $theinfo . "\n"; }
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
	if ($theinfo ne "") { print LOCKFILE $theinfo . "\n" }
    }

    # close the category datafile
    close(CATFILE);

    # close the lockfile
    close(LOCKFILE);

    # copy the old fle over the new file.
    &CopyFile($CATS_LOCKFILE,$CATS_DATAFILE);
}

#
# GetCats()
#
# This will return a list of all categories.
#
sub
GetCats() {
    my @catinfo;

    # open the forum datafile
    open(CATSFILE,$CATS_DATAFILE)||&error($ERROR_FILEOPENERR);

    # read it all to $catinfo{}
    while (<CATSFILE>) {
	my $line=$_;
	chop $line;

	# put it in
	push(@catinfo,$line);
    }

    # close the forum datafile
    close(FORUMFILE);

    return @catinfo;
}

#
# GetCatByNumber($catno)
#
# This will return the name of category numbered $catno, or a blanks string if
# it was not found.
#
sub
GetCatByNumber() {
    # get the arguments
    my ($catno)=@_;

    # open the category file
    open(CATINFO,$CATS_DATAFILE)||&error($ERROR_FILEOPENERR);

    # scan though the file line by line
    while (<CATINFO>) {
	# get the line
	my $line=$_;

	# split it
	my ($name,$no)=split(/:/,$line);

	# is this the one?
	if ($catno eq $no) {
	    # yup. return the name
	    return $name;
	}
    }

    # close the category file
    close (CATINFO);

    # return a blank line
    return "";
}

#
# SendEmail_Sendmail($to,$subject,$body)
#
# This will attempt to send an email via Sendmail from address $ADMIN_EMAIL to
# $to. The subject will be set to $subject, and the email body will be $body.
#
sub
SendEmail_Sendmail() {
    # get the arguments
    my ($to,$subject,$body)=@_;

    # open a pipe to sendmail
    my $mail_prog = $SENDMAIL_LOCATION . " -t";
    open(MAIL,"|$mail_prog")||&error("Unable to open a pipe to sendmail");

    # write the 'From: user@box' line
    printf MAIL "From: %s\n",$ADMIN_EMAIL;

    # write the 'To: user@box' line
    printf MAIL "To: %s\n",$to;

    # tell the box this is HTML mail
    printf MAIL "Content-Type: text/html\n";

    # write the 'Subject: blah' line
    printf MAIL "Subject: %s\n",$subject;
     
    # write the message itself, terminated with a \n.\n pair.
    printf MAIL "%s\n.\n",$body;

    # close the pipe
    close(MAIL);
}

#
# SendEmail_SMTP($to,$subject,$body)
#
# This will send an email via SMTP from address $ADMIN_EMAIL to $to. The
# subject will be set to $subject, and the email body will be $body.
#
# I followed RFC821, which can be found at
# http://www.rfc-editor.org/rfc/rfc821.txt
#
sub
SendEmail_SMTP() {
    # get the arguments
    my ($to,$subject,$body)=@_;
    my $result;

    # get the ip and socket no
    my $port = $SMTP_PORT;

    # if the port is not numeric, resolve it
    if ($port =~ /\D/) { $port = getservbyname($port, "tcp"); }
    $port || &error("SMTP server port not set");

    # make a connection to the SMTP box
    socket(SOCK, PF_INET, SOCK_STREAM, getprotobyname ("tcp")) || &error("Unable to create socket");
    connect (SOCK,sockaddr_in($port,inet_aton($SMTP_BOX))) || &error("Couldn't connect to SMTP server");
    # make sure we've got no buffering, are we will crash
    select (SOCK); $| = 1; select (STDOUT);

    # first of all, get the 'hello' string
    $result = <SOCK>; chop $result;
    if (! $result =~ /^2/) { &error("SMTP: Invalid welcome message"); }

    # tell the box who mailed this
    printf SOCK "MAIL FROM: $ADMIN_EMAIL\n";
    $result = <SOCK>; chop $result;
    if (! $result =~ /^2/) { &error("SMTP: Unable to specify source email"); }

    # tell the box who should get the mail
    printf SOCK "RCPT TO: $to\n";
    $result = <SOCK>; chop $result;
    if (! $result =~ /^2/) { &error("Unable to specify destination email"); }

    # tell the box we will send the data now
    printf SOCK "DATA\n";
    $result = <SOCK>; chop $result;
    if (! $result =~ /^3/) { &error("Unable to send email data"); } 
    printf SOCK "Subject: $subject\n\n$body\n.\n";

    $result = <SOCK>; chop $result;
    if (! $result =~ /^2/) { &error("Unable to send email data"); }

    # say the box we are done with everything
    printf SOCK "QUIT\n";
    $result = <SOCK>; chop $result;
    if (! $result =~ /^2/) { &error("Unable to specify end of email transaction"); }

    close (SOCK);
}

#
# SendEmail($to,$subject,$body)
#
# This will attempt to send an email from address $ADMIN_EMAIL to $to. The
# subject will be set to $subject, and the email body will be $body.
#
sub
SendEmail() {
    # get the arguments
    my ($to,$subject,$body)=@_;

    # is email disabled?
    if ($EMAIL_METHOD eq "0") {
	# yup. complain
	&error("Attempt to send mail without a mail method");
    }

    # do we use Sendmail?
    if ($EMAIL_METHOD eq "1") {
	# yup. send it via Sendmail
	&SendEmail_Sendmail($to, $subject, $body);
        return;
    }

    # do we use SMTP?
    if ($EMAIL_METHOD eq "2") {
	# yup. send it via SMTP
	&SendEmail_SMTP($to, $subject, $body);
        return;
    }

    # unknown email method... complain
    &error("Attempt to send mail via an unknown email method");
}

#
# TransformForConf($string)
#
# This will transfor $string so it can be stored using a qq~text~ expression.
#
sub
TransformForConf() {
    # get the arguments
    my ($string)=@_;

    # change all ~'s to \~'s
    $string=~s/\~/\\\~/gi;
    $string=~s/\@/\\\@/gi;

    # return the new string
    return $string;
}

#
# GeneratePassword()
#
# This will generate a random forum password for you.
#
sub
GeneratePassword() {
    my @pwd_chars = ("a".."z", "A".."Z", 0 .. 9);

    # generate a password for the user
    my $the_pwd = "";
    my $i;
    for ($i = 0; $i < 7; $i++) {
	$the_pwd .= $pwd_chars[rand($#pwd_chars)];
    }

    # give the user the password
    return $the_pwd;
}

# 
# HTMLilize($string)
#
# This will HTMLize $string and return the HTMLized string. It will also
# take care of colons, those will be changed to HTML ASCII values.
#
sub
HTMLize() {
    # get the arguments
    my ($string)=@_;

    # HTMLize it!
    $string=~ s/\n/\|C\|/gi;
    $string=~ s/\r/\|R\|/gi;

    # return the new string
    return $string;
}

# 
# UnHTMLilize($string)
#
# This will UnHTMLize $string and return the UnHTMLized string. It will also
# take care of colons, those will be changed to HTML ASCII values.
#
sub
UnHTMLize() {
    # get the arguments
    my ($string)=@_;

    $string=~ s/\|C\|/\n/g;
    $string=~ s/\|R\|/\r/g;

    # return the new string
    return $string;
}

#
# GetNiceDate()
#
# This will return the date in the format Monday, 1 February 2000
#
sub
GetNiceDate() {
    # get the current date
    my ($sec,$min,$hour,$day,$mon,$year,$wday) = localtime();
    $year = 1900 + $year; 

    my $dow = $day_name[$wday];
    my $moname = $month_name[$mon];

    # return the date
    return $dow . ", " . $day . " " . $moname . " " . $year;
}

#
# FixHeaderFooter($text)
#
# This will change the special |foo| thingies in the header and footer.
#
sub
FixHeaderFooter() {
    # get the arguments
    my ($text) = @_;

    # get the date
    my $nicedate = &GetNiceDate();

    # change all we need
    $text=~ s/\|date\|/$nicedate/gi;
    $text=~ s/\|ip\|/$ENV{"REMOTE_ADDR"}/gi;
    $text=~ s/\|id\|/$field{"id"}/gi;
    $text=~ s/\|username\|/$username/gi;

    # return the modified text
    return &UnHTMLize ($text);
}

#
# GetHeaderFooter($forum)
#
# This will set $header to the header of forum $forum, and $footer to the footer
# of the forum.
#
sub
GetHeaderFooter() {
    # get the arguments
    my ($forum) = @_;

    # get the forum info and split it
    my $forum_info=&GetForumInfo($forum);
    ($tmp1,$tmp3,$tmp4,$tmp5,$tmp6,$tmp7,$tmp8,$tmp9,$catno,$f_header,$f_footer)=split(/:/,$forum_info);

    # default to the forum info now
    $header = $f_header; $footer = $f_footer;

    # if the forum don't have a header, a footer or both, use the category
    # values
    if (($header eq "") or ($footer eq "")) {
        # figure out the category name
        my $catname = &GetCatByNumber ($catno);

        # did it exists?
        if ($catname ne "") {
            # yes. split the list
	    my $catline = &GetCatInfo ($catname);
            my ($tmp1,$tmp2,$tmp3,$tmp4,$c_header,$c_footer)=split(/:/,$catline);

            # change what is needed
            if ($c_header ne "") { $header = $c_header; }
            if ($c_footer ne "") { $footer = $c_footer; }
        }
    }

    # get the date
    my $nicedate = &GetNiceDate();

    # replace |header| by the general header
    $START_PAGE_TEXT=~ s/\|header\|//gi;
    $header=~ s/\|header\|/$START_PAGE_TEXT/gi;

    # and |footer| by the general footer
    $END_PAGE_TEXT=~ s/\|footer\|//gi;
    $footer=~ s/\|footer\|/$END_PAGE_TEXT/gi;

    # fix them
    $header=&RestoreSpecialChars($header);
    $footer=&RestoreSpecialChars($footer);
}

#
# ZapTrailingSpaces($string)
#
# This will zap all trailing spaces from $string, and return the output
#
sub
ZapTrailingSpaces() {
    # get the arguments
    my ($string) = @_;

    # nuke the spaces
    $string=~ s/^\s+//;
    $string=~ s/\s+$//;

    # return the new string
    return $string;
}

#
# FixSpecialChars($string)
#
# This will fix any special chars in $string.
#
sub
FixSpecialChars() {
    # get the arguments
    my ($string) = @_;

    # fix 'em
    $string=~ s/\&/\|AMP\|/gi;
    $string=~ s/\:/\|CLN\|/gi;

    # return the new string
    return $string;
}

#
# RestoreSpecialChars($string)
#
# This will restore any special chars in $string.
#
sub
RestoreSpecialChars() {
    # get the arguments
    my ($string) = @_;

    # fix 'em
    $string=~ s/\|AMP\|/\&/gi;
    $string=~ s/\|CLN\|/\:/gi;

    # return the new string
    return $string;
}

#
# ForumNameOK ($name)
#
# This will return zero if forum name $name is not OK, otherwise non-zero.
#
sub
ForumNameOK() {
    # get the arguments
    my ($name) = @_;
    my $tmp = $name;

    # zap all illegal chars
    $name=~ tr/[A-Za-z0-9\!\,\.\(\)\;\'\"\&\: ]//cd;

    # did it change?
    if ($tmp ne $name) {
	# yes. it was not valid
	return 0;
    }

    # no. it was valid
    return 1;
}

# 
# HasInternalChars ($text)
#
# This will return zero if text $text doesn't use special internal chars,
# otherwise non-zero.
#
sub
HasInternalChars() {
    # get the arguments
    my ($text) = @_;
    my $tmp = $text;

    # zap all colons and newlines
    $tmp=~ s/://gi;
    $tmp=~ s/\n//gi;

    # did the text change?
    if ($text ne $tmp) {
	# yes. it has internal chars
	return 1;
    }

    # no. it doesn't have internal chars
    return 0;
}

# 
# FormatSignature()
#
# This will correctly format and return the signature of user $username. It will
# be formated for use in forum $field{"forum"}.
#
sub
FormatSignature() {
    # get the arguments
    my ($username) = @_;

    # get the user profile
    my $userinfo = &GetUserRecord($username);

    # does this use still exists?
    if ($userinfo eq "") {
	# no. return an empty string
	return "";
    }

    # format the line
    my ($tmp,$passwd,$flags,$nofposts,$fullname,$email,$sig,$extra)=split(/:/,$userinfo);
    $sig = &UnHTMLize (&RestoreSpecialChars ($sig));

    # do we have to nuke HTML?
    if ($SIG_ALLOW_HTML ne "YES") {
        # yup. do it
        $sig =~ s/</&lt;/g;
        $sig =~ s/>/&gt;/g;
    }

    # build fake forum flags for ApplyMaXCodes() and EditForumText()
    my $forum_flags = "";
    # need to deny images?
    if ($SIG_ALLOW_IMGS ne "YES") {
	# yup. do it
        $forum_flags .= $FLAG_FORUM_NOIMG;

	# zap all [img] tags if we don't allow them
	$sig=~ s/\[img\]/\|IMGOPEN\|/gi;
	$sig=~ s/\[\/img\]/\|IMGCLOSED\|/gi;
    }
    # need to zap evil html?
    if ($SIG_DENY_EVIL_HTML eq "YES") {
	# yup. do it
        $forum_flags .= $FLAG_FORUM_NOSCRIPTEMBED;
    }

    # apply the forum text
    $sig = &EditForumText($sig,$forum_flags);

    # do we allow MaX codes?
    if ($SIG_ALLOW_MAX eq "YES") {
        # yup. apply them
        $sig = &ApplyMaXCodes($sig,$forum_flags);
    }

    # return the signature
    return $sig;
}

#
# GetSigRestrictions()
#
# This will return the signature restrictions in human-readable text.
#
sub
GetSigRestrictions() {
    my $text;

    $text="HTML is ";
    if ($SIG_ALLOW_HTML ne "YES") {
	$text .= "<b>not</b> ";
    }
    $text .= "allowed<br><a href=\"forum.cgi?action=maxcodes\" target=\"_blank\">MaX</a> codes are ";
    if ($SIG_ALLOW_MAX ne "YES") {
	$text .= "<b>not</b> ";
    }
    $text .= "allowed<br>Images are ";
    if ($SIG_ALLOW_IMGS ne "YES") {
	$text .= "<b>not</b> ";
    }
    $text .= "allowed";

    # return the restrictions
    return $text;
}

#
# DumpExtraProfileEditFields($in)
#
# This will show the extra profile fields in a table for editing. The
# actual values will be copied from $in.
#
sub
DumpExtraProfileEditFields() {
    # get the arguments
    my ($in) = @_;

    # add all fields
    my @thefields = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @vals = split(/\|\^\|/, $in);
    my $x = "0";
    foreach $field (@thefields) {
	my $val = $vals[$x];
	$val = &RestoreSpecialChars ($val);
	$val=~ s/&lt;/\</gi;
	$val=~ s/&gt;/\>/gi;
        printf "<tr><td>$field</td><td><input type=\"text\" name=\"extra$x\" value=\"%s\"></td></tr>",$val;
	$x++;
    }
}

#
# GetForumCatName ($forum)
#
# This will return the name of the category in which forum $forum is.
#
sub
GetForumCatName() {
    # get the arguments
    my ($forum) = @_;

    # get the cats
    my @cats = &GetCats();

    # get the forum info
    my $forum_info=&GetForumInfo($forum);

    # is there forum info?
    if ($forum_info eq "") {
	# nope. just return an empty string
	return "";
    }

    # split the forum info
    my ($tmp1,$tmp3,$tmp4,$tmp5,$tmp6,$tmp7,$tmp8,$tmp9,$forum_catno)=split(/:/,$forum_info);

    # browse all categories
    foreach $cat (@cats) {
	# split the line
        my ($name,$catno,$supermods)=split(/:/,$cat);

        # is this the one?
	if ($catno eq $forum_catno) {
	    # yup. return the category name
	    return $name;
	}
    }

    # the category wasn't found. return an empty string
    return "";
}

#
# CensorPost($message)
#
# This will censor post $message and return the result.
#
sub
CensorPost() {
    # get the arguments
    my ($message) = @_;

    # do we need to censor posts?
    if ($CENSOR_POSTS ne "YES") {
	# no. just return the message
	return $message;
    }

    # yes. create an array of all bad words
    my @badlist = split(/ /,$CENSORED_WORDS);

    # browse all words
    foreach $baddie (@badlist) {
	my $baddie_replace = "*" x length ($baddie);
	$message=~ s/$baddie/$baddie_replace/isg;
    }

    # return the censored message
    return $message;
}

#
# IsEmailBanned ($address)
#
# This will return non-zero if email address $address is banned, otherwise zero.
#
sub
IsEmailBanned() {
    # get the arguments
    my ($address) = @_;
    my $tmp;

    # zap trailing spaces, just to be sure
    $address = &ZapTrailingSpaces ($address);

    # create an array from the banned email addresses
    my @banned_email = split(/ /,$BANNED_EMAIL);

    # browse all of them
    foreach $banned (@banned_email) {
	# in the shape (user@host.com) ?
        my ($a, $b) = split(/\@/, $banned);
	if ($b eq "") {
	    # no. it's an entire domain ban
	    $tmp=$address;
	    $tmp=~ s/($banned)$//gi;
	    if ($tmp ne $address) {
		# it changed, so the email address must be banned.
		return 1;
	    }
	}
	# is the email address equal to be banned email address?
	if ($banned eq $address) {
	    # yup. the email address is banned
	    return 1;
	}
    }

    # the address is not banned.
    return 0;
}

#
# IsIPBanned()
#
# This will check if the current IP address is banned. It will return zero if
# it's not banned and non-zero if it is.
#
sub
IsIPBanned() {
    # get the IP
    my $the_ip = $ENV{"REMOTE_ADDR"};

    # create an array of all IP addresses
    my @banned_ips = split(/ /,$BANNED_IP);
    my $tmp;

    # browse the ip list
    foreach $badip (@banned_ips) {
	# is it something in the order 1.2.3.4?
	my ($a,$b,$c,$d) = split(/\./,$badip);
	if ($d ne "") {
	    # yup. check only this host
	    $tmp=$the_ip;
	    $tmp=~ s/^($badip)//gi; 
	    if (length ($tmp) eq 0) {
	        # it's banned. say it is.
		return 1;
	    }
            # NOTE: the length() eq 0 check is to make sure a rule like
	    # '1.2.3.4' will not ban '1.2.3.44' etc
	} else {
	    # it is a (sub)domain ban. handle it
	    $tmp=$the_ip;
	    $tmp=~ s/^($badip)//gi;
	    if ($tmp ne $the_ip) {
		# it's banned. say it is.
	        return 1;
	    }
	}
    }
    # this host is not banned. say it's ok.
    return 0;
}

#
# UsernamePasswordForm($enforce_display)
#
# This will show the username/password pair form, if needed. It will also add
# the 'forgot your password?' link, if that is possible. If $enforce_display
# is non-zero, the username/password forum will also be shown, regardless of
# you being logged in or not.
#
sub
UsernamePasswordForm() {
    # get the arguments
    my ($enforce_display)=@_;

    # if we are logged in, and $enforce_display is zero, just return
    if (($field{"id"} . $cookie{"id"} ne "") and ($enforce_display eq "0")) { return; }

    # show it
    printf "<tr><td>User name</td><td><input type=\"text\" name=\"username\"></td></tr>";
    printf "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>";

    # if we can recover passwords, show that
    if (($RECOVER_PASSWORD eq "YES") and ($EMAIL_METHOD ne "0")) {
        # we can! show the link
        printf "<tr><td></td><td><font size=1><a href=\"profile.cgi?action=lostpw\">Forgot your password?</a></font></td></tr>";
    }
    # if we can register an account, show that
    if ($ALLOW_REGISTRATION eq "YES") {
        # we can! show the link
        printf "<tr><td></td><td><font size=1><a href=\"profile.cgi?action=register\">Register an account</a></font></td></tr>";
    }
}

#
# SetCookie($name,$value,$expire)
#
# This will set cookie $name to $value, and expires in $expire seconds.
#
sub
SetCookie() {
    # grab the arguments
    my ($name, $value, $expire) = @_;

    # grab the local time
    my ($sec,$min,$hour,$mday,$mon,$year,$wday) = gmtime(time() + $expire);

    # pad everything to two digits
    if ($mday < 10) { $mday = "0" . $mday; }
    if ($hour < 10) { $hour = "0" . $hour; }
    if ($min < 10) { $min = "0" . $min; }
    if ($sec < 10) { $sec = "0" . $sec; }

    # make sure the year is in 4 digits
    $year = 1900 + $year;

    # calculate the expire date
    my $exp_date = sprintf ("%s, %s-%s-%s %s:%s:%s GMT", $cookie_day[$wday], $mday, $cookie_month[$mon], $year, $hour, $min, $sec);

    # print the HTTP cookie header
    printf "Set-Cookie: %s=%s; expires=%s\n", $name, $value, $exp_date;
}

#
# HTMLHeader()
#
# This will print the HTML response header. Do this *after* you've set all
# cookies.
#
sub
HTMLHeader() {
    # have we already sent the header?
    if ($header_sent eq "0") {
        # no. tell the web server we output HTML
        print "Content-type: text/html\n\n";

	# set flag we've done it
	$header_sent = "1";
    }
}

#
# SetAccountFlags ($username,$addsub,$flag)
#
# This will alter the flags of account $username. If the $addsub is zero, it
# will remove flag $flag, otherwise, it will add it.
#
sub
SetAccountFlags() {
    # get the arguments
    my ($username,$addsub,$flag)=@_;

    # grab the account
    my $userline=&GetUserRecord ($username);

    # no such account?
    if ($userline eq "") {
	# no. get out of here
	return 0;
    }

    # split the line
    my ($uname,$passw,$flags,$nofposts,$fullname,$email,$sig,$extra,$parentinfo)=split(/:/,$userline);
    
    # kill the flag to prevent it from being added twice

    $flags=~ s/($flag)//g;
    # need to add the flag?
    if ($addsub ne 0) {
	# yup. do it
	$flags .= $flag;
    }

    # re-create the record and save it
    my $newrecord = $uname . ":" . $passw . ":" . $flags . ":" . $nofposts . ":" . $fullname . ":" . $email . ":" . $sig . ":" . $extra . ":" . $parentinfo;
    &SetUserRecord ($username, $newrecord);
}

# we haven't sent the header yet
$header_sent = "0";

# don't ignore errors!
$ignorerror = "0";

# Was it a get thingy?
if ($ENV{"REQUEST_METHOD"} eq "GET") { 
    # yup. handle it as a get thingy
    $query_line=$ENV{"QUERY_STRING"};
} else {
    # nope, it must have been a post thingy...
    read(STDIN,$query_line,$ENV{"CONTENT_LENGTH"});
}

# first, grab the cookies
@cookies=split(/;/,$ENV{"HTTP_COOKIE"});
foreach $item (@cookies) {
    ($key,$content)=split(/=/,$item);
    $content=~ s/\+/ /g;
    $content=~ s/%(..)/pack("c",hex($1))/ge;
    $content=~ s/^\s+//;
    $content=~ s/\s+$//;
    $cookie{$key}=$content;
}

# split the arguments
@pairs=split(/&/,$query_line);
foreach $item (@pairs) {
    ($key,$content)=split(/=/,$item);
    $content=~ s/\+/ /g;
    $content=~ s/%00//g;
    $content=~ s/%(..)/pack("c",hex($1))/ge;
    $content=~ s/^\s+//;
    $content=~ s/\s+$//;
    $field{$key}=$content;
}

# are we banned?
if (&IsIPBanned() ne 0) {
    # yes. do we have a valid username/password combination or an id string?
    if (($field{"username"} eq "") and ($field{"password"} eq "") and ($field{"id"} eq "")) {
        # yup. complain
        my $ip = $ENV{"REMOTE_ADDR"};
        &nonfatal_error("The IP address you are using ($ip) is banned. Please contact the site administrator if you think this is not appropiate");

        printf "<p>If you are a forum administrator, you can override IP banning. In such a case, please fill in your username and password below:<p>";

        # grab this script's name
        $script_name=$0;
        $script_name=~ s/((.)+)\///gi;

        printf "<form action=\"%s\" method=\"post\">", $script_name;
        printf "<table>";
        &UsernamePasswordForm(1);
        printf "</table>";

	printf "<input type=\"submit\" value=\"OK\">";
	printf "</form>";

        &NormalPageEnd();

        exit;
    } else {
	# do we have a hash?
	if ($field{"id"} eq "") {
	    # no. build one
	    $field{"id"}=&HashID($field{"username"},$field{"password"});
	}

	# verify the hash
	&VerifyHash($field{"id"});

	# are we an enabled admin?
	if ((&check_flag($flags,$FLAG_DISABLED) ne 0) or (&check_flag($flags,$FLAG_ADMIN) eq 0)) {
	    # no. complain
	    &error($ERROR_ACCESSDENIED);
	}
    }
}

#
# FormatUserGroup (@list)
#
# This will format the $list moderator list. It'll add some nice formating
# to the users, and replace all @group by 'The group group'.
#
sub
FormatUserGroup() {
    # grab the arguments
    my (@list) = @_;
    my $result;
    my $count = @list;
    my $x = 1;

    foreach $item (@list) {
	# does it begin with a @?
	if ($item=~/^\@/) {
	    # yup. remove it
	    $item=~ s/^\@//;
	    $result .= "The $item group";
	} else {
	    # no. just add it
	    $result .= $item;
	}
	if ($x < $count) { $result .= ", "; }

	$x++;
    }

    return $result;
}

#
# BuildUserGroupList (@list)
#
# This will take a user/group list and replace all @group by the actual group
# members.
#
sub
BuildUserGroupList() {
    # get the argments
    my (@list)=@_;
    my @user_list;

    foreach $usergroup (@list) {
	# does this line begin with a @?
	if ($usergroup=~/^\@/) {
	    # yup. first, get rid of it
	    $usergroup=~ s/^\@//;

	    # now, grab the group line
	    my $groupinfo=&GetGroupRecord ($usergroup);

	    # does such a group exists?
	    if ($groupinfo ne "") {
		# yup. split the record
                my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$groupinfo);

		# add all members to the moderators list
		foreach $member (split(/,/, $groupmembers)) {
		    push (@user_list, $member);
		}
	    }
	} else {
	    # no, just an ordinary user. add him to the list
	    push (@user_list, $usergroup);
	}
    }

    # return this list
    return @user_list;
}

#
# ZapDuplicates (@list)
#
# This will remove all duplicates from @list and return the result.
#
sub
ZapDuplicates() {
    # get the arguments
    my (@list)=@_;
    my @newlist;
    my $ok;

    # browse the all
    foreach $item (@list) {
	# is this one new?
	$ok = 1;
	foreach $itemx (@newlist) {
	    if ($item eq $itemx) { $ok = 0; }
	}
	# is this ok?
	if ($ok ne 0) {
	    # yup. add it
	    push (@newlist, $item);
	}
    }

    # return the new list
    return @newlist;
}

#
# GetForumModList($forum)
#
# This will return the moderators of forum $forum. It will include super
# moderators, but groups will be @group.
#
sub
GetForumModList() {
    # get the arguments
    my ($forum)=@_;

    # get the forum line
    my $forumline = &GetForumInfo($forum);

    # did this retrieve anything?
    if ($forumline eq "") {
	# no. if there's no forum, there are also no mods
	return "";
    }

    # split the forum line
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags,$tmp,$catno)=split(/:/,$forumline);

    # resolve all mods
    my @mod_list = split (/,/, $mods);
    my @forum_mods;

    # now, is this forum in a category?
    my $catname = &GetCatByNumber($catno);

    if ($catname ne "") {
	# yup. grab the category line
	my $catline = &GetCatInfo ($catname);

	# split it
	my ($name,$catno,$supermods)=split(/:/,$catline);

	# grab all super mods and add them to the list
	foreach $supermod (split(/,/, $supermods)) {
	    push (@mod_list, $supermod);
	}
    }

    return &ZapDuplicates (@mod_list);
}

#
# GetForumMods($forum)
#
# This will return the list of valid moderators that have superuser rights over
# forum $forum. It will also check the super moderators and resolve groups as
# needed.
#
sub
GetForumMods() {
    # get the arguments
    my ($forum)=@_;

    # grab the moderator list that includes super mods
    return &BuildUserGroupList (&GetForumModList($forum));
}

#
# GetForumModsText($forum)
#
# This will return the list of valid moderators that have superuser rights over
# forum $forum, but in a comma-separated list. It will also check the super
# moderators and resolve groups as needed.
#
sub
GetForumModsText() {
    # get the arguments
    my ($forum) = @_;

    # grab the forum mods
    my @mods = &GetForumMods ($forum);

    # return 'em
    return join (",", @mods);
}

#
# IsForumMod ($forum)
#
# This will verify whether $username is a moderator of forum $forum. It will
# return zero if not and non-zero if true. It expects the user's flags to be
# in $flags, and the username is $field{"username"}.
#
sub
IsForumMod() {
    # get the arguments
    my ($forum)=@_;

    # are we an admin?
    if (&check_flag($flags,$FLAG_ADMIN) ne 0) {
        # yeah. we moderate anything.
        return 1;
    }

    # are we a mega moderator?
    if (&check_flag($flags,$FLAG_MEGAMOD) ne 0) {
        # yeah. we moderate anyting.
        return 1;
    }

    # grab the forum mods
    my @the_mods = &GetForumMods($forum);

    # check whether we are in this list
    foreach $mod (@the_mods) {
	if ($field{"username"} eq $mod) {
	    # yup, we are in the list. we moderate this forum
	    return 1;
	}
    }

    # sorry, no luck
    return 0;
}

#
# GetForumRestrictedUsers ($forum)
#
# This will return the users which can access forum $forum.
#
sub
GetForumRestrictedUsers() {
    # get the arguments
    my ($forum)=@_;

    # get the forum line
    my $forumline = &GetForumInfo($forum);

    # did this retrieve anything?
    if ($forumline eq "") {
	# no. if there's no forum, there are also no restrictions
	return "";
    }

    # split the forum line
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags,$tmp,$catno)=split(/:/,$forumline);

    # return the users
    return &BuildUserGroupList (split (/,/, $restricted));
}

#
# UpdateForumData($forumname,$addsub,$changedate,$no,$addsubt)
#
# This will update the forum data file. If $addsub is zero, it will not change
# the number of posts that have been made. If $addsub is one, it will add $no
# to the number of posts, and when $addsub is two, it will subtract $no of
# the number of posts. if $changedate is not zero, the date will be updated
# too. If $addsubt is 1, one will be added to the number of threads. If it is
# 2, one will be subtractred from the number of threads. All changes will be
# made to forum $forumname.
#
sub
UpdateForumData() {
    # split the arguments
    my ($forumname,$addsub,$changedate,$no,$addsubt)=@_;

    # get the forum info
    my $line = &GetForumInfo($forumname);

    my ($the_forum,$nofposts,$mods,$restricted,$date1,$date2,$forum_flags,$desc,$cat_no,$header,$footer,$nofthreads,$newtopic,$newreply) = split(/:/,$line);

    my $date = $date1 . ":" . $date2;

    # need to update the number of posts?
    if ($addsub ne "0") {
        # yeah. need to add?
        if ($addsub eq "1") {
            # yeah. do it
            $nofposts = $nofposts + $no;
        } else {
            # nope. subtrace one
            $nofposts = $nofposts - $no;
	}
    }
    # need to update the date?
    if ($changedate ne "0") {
        # yeah. get the new date
        $date = &GetTimeDate();
    }
    # need to update the number of posts?
    if ($addsubt ne "0") {
        # yeah. need to add?
        if ($addsubt eq "1") {
            # yeah. do it
            $nofthreads++;
        } else {
            # nope. subtrace one
            $nofthreads--;
        }
    }

    # construct the new line
    my $newline = $the_forum . ":" . $nofposts . ":" . $mods . ":" . $restricted . ":" . $date . ":" . $forum_flags . ":" . $desc . ":" . $cat_no . ":" . $header . ":" . $footer . ":" . $nofthreads . ":" . $newtopic . ":" . $newreply;

    # set the category info
    &SetForumInfo ($forumname, $newline);
}

#
# GetForumLine($forum,$id)
#
# This will return the database line of post number $id from forum $forum. It
# will show an error if the post does not exists.
#
sub
GetForumLine() {
    # get my parameters
    my ($forum,$id) = @_;

    # open the forum datafile
    open(GFLFORUMDATA,$FORUM_DIR . $forum . $FORUM_EXT)||&error($ERROR_FILEOPENERR);

    # scan through until we found our id
    while ( <GFLFORUMDATA> ) {
        # store the line
        my $line = $_; $line=~ s/\n//g;

        # split the line
        my ($forum_id,$subject,$nofposts,$date1,$date2,$owner)=split(/:/,$line);

        # is this our id?
        if ($forum_id eq $id) {
            # yeah. close the data file and return the subject
            close(GFLFORUMDATA);
            return $line;
        }
    }

    # close the forum data file
    close(GFLFORUMDATA);

    # show error
    &error("Forum message does not exists");
}

#
# IncrementForumPosts($forumname,$threadid,$addsub,$lastposter,$no)
#
# This will increment the number of posts at forum $forumname, thread id
# $threadid. If $addsub is zero, $no will be added to the number of
# replies, otherwise $no will be subtracted. If $addsub is zero, the post will
# be put at the top of the page. If $addsub is zero, it will also clear
# the names of the users which have viewed this post, leaving only the current
# user's name there. If $lastposter is not blank, it will be used as the one
# who last posted a message here
#
sub
IncrementForumPosts() {
    # get the arguments
    my ($forumname,$threadid,$addsub,$lastposter,$no) = @_;

    # first get the old data
    my $data = &GetForumLine ($forumname,$threadid);
    chop $data;

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $forumname . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yes. show error
        &error($ERROR_FORUMLOCKED);
    }

    # open the forum file
    my $forumfile=$FORUM_DIR . $forumname . $FORUM_EXT;
    open(IFPPOSTFILE,$forumfile)||&error($ERROR_FILEOPENERR);

    # create the lock file
    open(IFPLOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERR);

    # if $addsub is zero, put the new entry there now
    if ($addsub eq "0") {
        my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$oldlastposter,$locker)=split(/:/,$data);

        # yeah, increment it
        $nofposts = $nofposts + $no;

	# need to update last poster?
	if ($lastposter ne "") {
	    # yup. do it
	    $oldlastposter = $lastposter;
	}

        # write the new string
        my $timestr = &GetTimeDate();
        print IFPLOCKFILE $forum_id . ":" . $subject . ":" . $nofposts . ":".  &GetTimeDate() . ":" . $owner . ":" . $icon . ":" . $movedforum . ":" . $forum_flags . ":" . $oldlastposter . ":" . $locker . "\n";
    }

    # trace through the complete forum file
    while ( <IFPPOSTFILE> ) {
        # get the line
        my $line = $_;
	chop $line;

        # split the line
        my ($forum_id)=split(/:/,$line);

        # is this our id?
        if ($forum_id ne $threadid) {
            # no. copy it to the file
            print IFPLOCKFILE $line . "\n";
        } else {
            # update it
            my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$movedforum,$forum_flags,$oldlastposter,$locker)=split(/:/,$line);
            # need to decrement?
            if ($addsub ne "0") {
                # yeah. are there more than zero posts?
                if ($nofposts => $no) {
                    # yeah, decrement it
                    $nofposts = $nofposts - $no;
                }
		# need to update last poster?
		if ($lastposter ne "") {
	    	    # yup. do it
	            $oldlastposter = $lastposter;
		}
                # write the new string
                print IFPLOCKFILE $forum_id . ":" . $subject . ":" . $nofposts . ":".  &GetTimeDate() . ":" . $owner . ":" . $icon . ":" . $movedforum . ":" . $forum_flags . ":" . $oldlastposter . ":" . $locker . "\n";
            }
        }
    }

    # close the forum file
    close(IFPPOSTFILE);

    # close the lock file
    close(IFPLOCKFILE);

    # copy the file
    &CopyFile($lockfile,$forumfile);

    # delete the lock file
    unlink($lockfile);
}

#
# DestroyThread ($forumname,$threadid)
#
# This will destroy thread $threadid in forum $forumname.
#
sub
DestroyThread() {
    # get the arguments
    my ($forumname,$threadid)=@_;

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $forumname . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yes. show error
        &error($ERROR_FORUMLOCKED);
    }

    # open the forum file
    my $forumfile=$FORUM_DIR . $forumname . $FORUM_EXT;
    open(DTPOSTFILE,$forumfile)||&error($ERROR_FILEOPENERR);

    # create the lock file
    open(DTLOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERR);

    # trace through the complete forum file
    my $the_nofposts=0;
    while ( <DTPOSTFILE> ) {
        # get the line
        my $line = $_;

        # split the line
        my ($forum_id,$subject,$nofposts,$date1,$date2,$owner)=split(/:/,$line);

        # is this our id?
        if ($forum_id ne $threadid) {
            # no. copy it to the file
            print DTLOCKFILE $line;
        } else {
	    # yup. store the number of posts
	    $the_nofposts = $nofposts;
	}
    }

    # close the forum file
    close(DTPOSTFILE);

    # close the lock file
    close(DTLOCKFILE);

    # copy the file
    &CopyFile($lockfile,$forumfile);

    # delete the actual thread contents
    unlink($FORUM_DIR . $forumname . "/" . $threadid)||&error($ERROR_FILEDELETERR);

    # decrement the number of forum posts
    &UpdateForumData($forumname,"2","0",$the_nofposts + 1,2);
}

#
# DestroyPost ($forumname, $threadid, $postid, $zapusername)
#
# This will destroy post $postid, in forum $threadid of forum $forumname. If
# $zapusername is not blank, all posts made by that user will be deleted. It
# will return the number of posts destroyed.
#
sub
DestroyPost() {
    # get the arguments
    my ($forumname, $threadid, $postid, $zapusername)=@_;

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $forumname . "/" . $threadid . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yes. show error
        &error($ERROR_FORUMLOCKED);
    }

    # $msgno is the current message number, and $nuking is the nuking flag 
    # $lastposter is the person who last replied to this thread
    my $msgno="0";
    my $nuking="0";
    my $lastposter = "";
    my $no=0;

    # open the forum file
    my $forumfile=$FORUM_DIR . $forumname . "/" . $threadid;
    open(DPPOSTFILE,"+<" . $forumfile)||&error($ERROR_FILEOPENERR);

    # create the lock file
    open(DPLOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERR);

    # trace through the complete forum file
    my $lastposter="";
    while ( <DPPOSTFILE> ) {
        # get the line
        my $line = $_;

        # is this a line starthing with a dot?
        my ($dot,$author,$d1,$d2) = split(/:/, $line);
        if ($dot eq ".") {
	    # yeah. is this the message we should nuke?
            if (($msgno eq $postid) or ($zapusername eq $author)) {
                # yeah. set the flag
                $nuking="1";
		$no++;
            } else {
                # no. clear the flag
                $nuking="0";

	        # store the last poster
	        $lastposter = $author;
            }

            # increment the message number
            $msgno++;
        }

        # if not nuking, print the line to the lock file
        if ($nuking eq "0") {
            print DPLOCKFILE $line;
        }
    }

    # close the forum file
    close(DPPOSTFILE);

    # close the lock file
    close(DPLOCKFILE);

    # copy the file
    &CopyFile($lockfile,$forumfile);

    # decrement the number of posts made
    &IncrementForumPosts($forumname,$threadid,"1",$lastposter,"1");
    &UpdateForumData($forumname,"2","1","1");

    # return the number of posts deleted
    return $no;
}

#
# VerifyParentalPassword ($accountname, $password)
#
# This will verify parental password $password for account $accountname. If it's
# not correct, it'll show an error.
#
sub
VerifyParentalPassword() {
    # get the arguments
    my ($accountname,$password)=@_;

    # grab the user's profile
    my $userline=&GetUserRecord($accountname);

    # grab the parental and flag fields
    my ($tmp,$passw,$flags,$nofposts,$fullname,$email,$sig,$extra,$parentinfo)=split(/:/,$userline);
    my ($parent_email,$parent_pwd)=split(/\|\^\|/, $parentinfo);

    # is this account below 13?
    if (&check_flag($flags,$FLAG_UNDER13) eq 0) {
	# no. say the password is false
	&error($ERROR_ACCESSDENIED);
    }

    # is the password correct?
    if ($password ne $parent_pwd) {
	# no. complain
	&error($ERROR_ACCESSDENIED);
    }
}

# build the $month_name[] array
$month_name["0"]="January";
$month_name["1"]="February";
$month_name["2"]="March";
$month_name["3"]="April";
$month_name["4"]="May";
$month_name["5"]="June";
$month_name["6"]="July";
$month_name["7"]="August";
$month_name["8"]="September";
$month_name["9"]="October";
$month_name["10"]="November";
$month_name["11"]="December";

# build the $day_name[] array
$day_name["0"]="Monday";
$day_name["1"]="Tuesday";
$day_name["2"]="Wednesday";
$day_name["3"]="Thursday";
$day_name["4"]="Friday";
$day_name["5"]="Saturday";
$day_name["6"]="Sunday";

# build the cookie days of week
$cookie_day["0"]="Sun";
$cookie_day["1"]="Mon";
$cookie_day["2"]="Tue";
$cookie_day["3"]="Wed";
$cookie_day["4"]="Thu";
$cookie_day["5"]="Fri";
$cookie_day["6"]="Sat";

# build the cookie month names
$cookie_month["0"] = "Jan";
$cookie_month["1"] = "Feb";
$cookie_month["2"] = "Mar";
$cookie_month["3"] = "Apr";
$cookie_month["4"] = "May";
$cookie_month["5"] = "Jun";
$cookie_month["6"] = "Jul";
$cookie_month["7"] = "Aug";
$cookie_month["8"] = "Sep";
$cookie_month["8"] = "Oct";
$cookie_month["10"] = "Nov";
$cookie_month["11"] = "Dec";

# make sure the $field{"id"} thing always has plusses instead of spaces!
$field{"id"}=~tr/ /\+/;

# are we inside the CP?
if ($in_cp ne "YES") {
    # no. are the header and footer thingies files?
    if ($HEADERFOOTER_FILE eq "YES") {
        # yup. first, destroy all slashes in the paths
        $START_PAGE_TEXT=~ s/\///g;
        $START_PAGE_TEXT=~ s/\\//g;
        $END_PAGE_TEXT=~ s/\///g;
        $END_PAGE_TEXT=~ s/\\//g;

        # now, try to read them
        my $err = 0;
        $err=1 unless open(HEADERFILE,$FORUM_DIR . $START_PAGE_TEXT);

        # error? 
        if ($err eq 0) {
	    # no. read the files and use this as header
	    $START_PAGE_TEXT = "";
	    while (<HEADERFILE>) { $START_PAGE_TEXT .= $_; }
	    close (HEADERFILE);
        } else {
	    $START_PAGE_TEXT = "<center><font size=2><i>Unable to read header file</i></font></center>";
        }

        $err = 0;
        $err=1 unless open(FOOTERFILE,$FORUM_DIR . $END_PAGE_TEXT);

        # error? 
        if ($err eq 0) {
	    # no. read the files and use this as header
	    $END_PAGE_TEXT = "";
	    while (<FOOTERFILE>) { $END_PAGE_TEXT .= $_; }
	    close (FOOTERFILE);
        } else {
	    $END_PAGE_TEXT = "<p><center><font size=2><i>Unable to read footer file</i></font></center>";
       }
    }

    # always add a proper <p> to the $START_PAGE_TEXT and $END_PAGE_TEXT fields
    $START_PAGE_TEXT .= "<p>";
    $END_PAGE_TEXT = "<p>" . $END_PAGE_TEXT;
}

#
# CanPostNewTopic($newtopic_posters)
#
# This will return zero if the user cannot post a new topic in the current
# forum, otherwise non-zero.
#
sub
CanPostNewTopic() {
    # grab the arguments
    my ($newtopic_posters)=@_;

    # are we a superuser?
    if (&check_flag($flags,$FLAG_ADMIN) ne "0") {
        # yeah. we can post!
        return 1;
    }

    # do we have the posting denied flag?    
    if (&check_flag($flags,$FLAG_DISABLED) ne "0")  {
        # yeah. we cannot post
        return 0;
    }

    # do we have a list of good posters?
    if ($newtopic_posters ne "") {
	# yeah. if we're on this list, we're in
        return &IsInArray ($field{"username"}, split (/,/, $newtopic_posters));
    }

    # we can post here
    return 1;
}

#
# ConstructOption()
#
# This will build the options list (like Login | Profile | Register | Control
# Panel) that will
# appear in every forum. It'll return this string.
#
sub
ConstructOptions() {
    my $result,$id;

    $result = "<font face=\"$FORUM_FONT\">";

    # grab the ID field
    $id = $field{"id"};
    if ($id eq "") { $id = $cookie{"id"}; }

    # are we currently logged in?
    if ($id ne "") {
	# yes. add 'Logged in as <someone> to the text
	my ($tmp) = split (/\:/, $id);
	$tmp=~ s/\+/ /g;
        $result .= "Logged in as <b>" . $tmp . "</b> | <a href=\"profile.cgi?action=logout\">Logout</a> ";
    } else {
	# no. add the 'Login' link
        $result .= "<a href=\"profile.cgi?action=login\">Login</a>";
    }

    # can we register accounts?
    if ($ALLOW_REGISTRATION eq "YES") {
       # yes. add the 'Register' link
       $result .= " | <a href=\"profile.cgi?action=register\">Register</a>";
    }
    
    # add the 'edit profile' link
    $result .= " | <a href=\"profile.cgi?action=profile&id=" . $field{"id"} . "\">Edit profile</a>";

    # do we have the COPPA stuff on?
    if ($COPPA_ENABLED eq "YES") {
    $result .= " (<a href=\"profile.cgi?action=profile&parent=yes\">Parents</a>)";
    }

    # are we an admin?
    if (&check_flag($flags,$FLAG_ADMIN) ne 0) {
	# yes. add the 'Control Panel' link
	if ($USE_COOKIES eq "YES") { $id = ""; }
	$result .= " | <a href=\"cp.cgi?action=main&id=" . $id . "\">Control Panel</a>";
    }

    $result .= "</font>";

    return $result;
}

#
# CanThreadBeDeleted($author,$ismod)
#
# This will return non-zero if a thread with author $author can be deleted by
# the current user, otherwise zero. $ismod is assumed to be the moderator
# status.
#
sub
CanThreadBeDeleted() {
    # get the arguments
    my ($author,$ismod) = @_;

    # does the forum like this?
    if (($ALLOW_LOCK_DELETE eq "0") or ($ALLOW_LOCK_DELETE eq "1")) {
	# no. say no
	return 0;
    }

    # are we the owner of this message?
    if ($field{"username"} eq $author)  {
        # yeah. the message owner can also edit their own messages, so say yes
        return 1;
    }

    # it's all up to the moderatorship of this forum now
    return $ismod;
}

#
# CanBeUnlocked($author,$ismod)
#
# This will return non-zero if a thread owned by author $author can be locked
# by the current user, otherwise zero. $ismod is assumed to be the moderator
# status.
#
sub
CanBeUnlocked() {
    # get the parameters
    my ($author,$ismod) = @_;

    # do we allow unlocking?
    if ($ALLOW_UNLOCK ne "YES") {
	# no. say no
	return 0;
    }

    # it all depends upon moderatorship now
    return $ismod;
}

#
# CanViewRestricted()
#
# This will return zero if user $username cannot view forum $field{"forum"}
#
sub
CanViewRestricted() {
    # are we an administrator?
    if (&check_flag($flags,$FLAG_ADMIN) ne 0) {
	# yes! we have access
	return 1;
    }

    # do we have an array of restricted users?
    my @restricted = &GetForumRestrictedUsers ($field{"forum"});
    my $nofrest = @restricted;
    if ($nofrest eq 0) {
	# no. we have access
	return 1;
    }
    # it now depends whether we are in the list or not
    return &IsInArray ($field{"username"}, @restricted);
}

#
# CanPostReply($forum_flags,$newposters)
#
# This will return zero if the current user cannot post a reply to forum
# $field{"forum"}, otherwise non-zero.
#
#
sub
CanPostReply() {
    # get the parameters
    my ($forum_flags,$newposters) = @_;

    # is this thread locked?
    if (&check_flag ($forum_flags, $FLAG_FORUM_LOCKED) ne "0") {
	# yup. no replies allowed
	return 0;
    }

    # are we a superuser?
    if (&check_flag($flags,$FLAG_ADMIN) ne "0") {
        # yeah. we can post!
        return 1;
    }

    # do we have the posting denied flag?    
    if (&check_flag($flags,$FLAG_DISABLED) ne "0")  {
        # yeah. we cannot post
        return 0;
    }

    # do we have a list of good posters?
    if ($newposters ne "") {
	# yeah. if we're on this list, we're in
        return &IsInArray ($field{"username"}, split (/,/, $newposters));
    }

    # we can post replies!
    return 1;
}

#
# CanBeDeleted($author,$ismod)
#
# This will return non-zero if a message with author $author can be deleted by
# the current user, otherwise zero. $ismod is assumed to be the moderator
# status.
#
sub
CanBeDeleted() {
    # get the arguments
    my ($author,$ismod) = @_;

    # does the forum like this?
    if (($ALLOW_EDIT_DELETE eq "0") or ($ALLOW_EDIT_DELETE eq "1")) {
	# no. say no
	return 0;
    }

    # let the CanBeEdited function handle it
    return &CanBeEdited($author,$ismod);
}

#
# CanBeEdited($author,$ismod)
#
# This will return non-zero if a message with author $author can be edited by
# the current user, otherwise zero. $ismod is assumed to be the moderator
# status.
#
sub
CanBeEdited() {
    # get my parameters
    my ($author,$ismod) = @_;

    # does the forum like editing messages?
    if ($ALLOW_EDIT_DELETE eq "0") {
	# no. say no
	return 0;
    }

    # are we the owner of this message?
    if ($field{"username"} eq $author)  {
        # yeah. the message owner can also edit their own messages, so say yes
        return 1;
    }

    # now, it's all up to the moderator position
    return $ismod;
}

#
# CanViewIP()
#
# This will return zero if the current logged-in user cannot view the IP
# address of forum $field{"forum"}, otherwise non-zero. $ismod is assumed to
# indicate the user's moderator status.
#
sub
CanViewIP() {
    # get the argument
    my ($ismod)=@_;

    # is IP address viewing disabled?
    if ($IP_LOG_DISPLAY eq "0") {
	# yup. viewing is disabled
	return 0;
    }

    # can anyone view it?
    if ($IP_LOG_DISPLAY eq "2") {
	# yup. say it's ok
	return 1;
    }

    # is the user an admin?
    if (&check_flag($flags,$FLAG_ADMIN) ne 0) {
	# yup. he can view it
	return 1;
    }

    # can only admins view it?
    if ($IP_LOG_DISPLAY eq "1") {
	# yup. the user cannot view it
	return 0;
    }

    # if we reach this, only moderators and up can view an IP.
    return $ismod;
}

#
# CanBeLocked($author,$ismod)
#
# This will return non-zero if a thread owned by author $author can be locked
# by the current user, otherwise zero. $ismod is assumed to be the moderator
# status.
#
sub
CanBeLocked() {
    # get the parameters
    my ($author,$ismod) = @_;

    # does the forum like this?
    if ($ALLOW_LOCK_DELETE eq "0") {
	# no. say no
	return 0;
    }

    # is the user an admin?
    if (&check_flag($flags,$FLAG_ADMIN) ne 0) {
	# yup. he can view it
	return 1;
    }

    # only moderators and up can lock threads now.
    return $ismod;
}

#
# CanMoveThread($forum)
#
# This will return zero if the current logged-in user cannot move a thread from
# forum $forum, otherwise non-zero. $ismod is assumed to be the moderator
# status.
#
sub
CanMoveThread() {
    # get the arguments
    my ($forum,$ismod) = @_;

    # is the user an admin?
    if (&check_flag($flags,$FLAG_ADMIN) ne 0) {
	# yup. he can move it
	return 1;
    }

    # is the user a megamod?
    if (&check_flag($flags,$FLAG_MEGAMOD) ne 0) {
	# yup. he can move it
	return 1;
    }

    # only moderators and up can lock threads now.
    return $ismod;
}

#
# ShowNofMembers()
#
# This will insert 'Number of registered members: xxx' into the forum.
#
sub
ShowNofMembers() {
    # get the number of members
    my $totalmembers = &GetNofAccounts();

    # show them!	
    printf "<font size=2 face=\"$FORUM_FONT\">Number of registered members: %s</font>",$totalmembers;
}

# always disable buffering so browsers won't time out
select (STDOUT); $| = 1;
