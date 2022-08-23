#!/usr/bin/perl
#
# ForuMAX Version 4.1 - cp.cgi
#
# This will handle the entire control panel.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# tell the forum_lib.pl file we are in the CP, and that it shouldn't fix the
# header and footer now
$in_cp = "YES";

# include the files
require "forum_lib.pl";
require "forum_options.pl";
require "user_db.pl";

# $FORUMS_PER_CP_PAGE will indicate how much forums will be shown per CP
# page.
$FORUMS_PER_CP_PAGE="10";

#
# AboutToModify()
#
# This will be called just before the control panel will actually modify your
# configuration files. You can insert extra checks and such here. You can
# also log stuff here if you like.
#
sub
AboutToModify() {
}

#
# begin_page($bgcolor,$textcolor,$logo)
#
# This will set up the page. It will get background color $bgcolor, if it was
# given. If $textcolor is given, all default text will be in that color. If
# $logo is not an empty string, the name of it will be used as the header logo.
#
sub
begin_page() {
    # get the arguments and set the defaults if needed
    my ($bgcolor,$textcolor,$logo) = @_;

    # have we already shown the header?
    if ($shown_header ne 0) {
	# yup. leave
	return;
    }

    if ($bgcolor eq "") { $bgcolor="#c0c0c0"; }
    if ($textcolor eq "") { $textcolor="#000000"; }
    if ($logo eq "") { $logo="fcp.gif"; }
    $logo=$IMAGES_URI . "/$logo";

    # format the page
    printf "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
    printf "<style type=\"text/css\">\n";
    printf "body { margin: 0px 0px 0px 0px }\n";
    printf "a:link { color: #0000ff; }\n";
    printf "a:visited { color: #0000ff; }\n";
    printf "a:hover { text-decoration: none; }\n";
    printf ".subjectlink:visited { font: 13px $FORUM_FONT; text-decoration: none; color: $FORUM_COLOR_SUBJECTLINK; }\n";
    printf ".subjectlink:link { font: 13px $FORUM_FONT; text-decoration: none; color: $FORUM_COLOR_SUBJECTLINK; }\n";
    printf ".subjectlink:hover { font: 13px $FORUM_FONT; text-decoration: none; color: $FORUM_COLOR_SUBJECTLINK_HOVER }\n";
    printf ".memberlink:visited { font: 13px $FORUM_FONT; color: $FORUM_COLOR_MEMBERLINK; }\n";
    printf ".memberlink:link { font: 13px $FORUM_FONT; color: $FORUM_COLOR_MEMBERLINK; }\n";
    printf ".memberlink:hover { font: 13px $FORUM_FONT; color: $FORUM_COLOR_MEMBERLINK_HOVER; }\n";
    printf ".forumlink:visited { font: 13px $FORUM_FONT; text-decoration: none; color: %s; }\n",$FORUM_COLOR_LIST_FORUMNAME;
    printf ".forumlink:link { font: 13px $FORUM_FONT; text-decoration: none; color: %s; }\n",$FORUM_COLOR_LIST_FORUMNAME;
    printf ".forumlink:hover { font: 13px $FORUM_FONT; text-decoration: none; color: %s; }\n",$FORUM_COLOR_LIST_FORUMNAME_HOVER;
    printf ".lastpost:link { font 13px $FORUM_FONT; text-decoration: none; color: %s; }\n", $FORUM_COLOR_LASTPOSTER_LINK;
    printf ".lastpost:visited { font 13px $FORUM_FONT; text-decoration: none; color: %s; }\n", $FORUM_COLOR_LASTPOSTER_LINK;
    printf ".lastpost:hover { font 13px $FORUM_FONT; text-decoration: none; color: %s; }\n", $FORUM_COLOR_LASTPOSTER_HOVER;
    printf ".leftlink:link { color: #ffff00 }\n";
    printf ".leftlink:hover { text-decoration: none }\n";
    printf ".leftlink:visited { color: #ffff00 }\n";
    printf "</style>\n";
    printf "<meta http-equiv=\"pragma\" content=\"no-cache\">";
    printf "<title>ForuMAX Control Panel</title></head>\n<body bgcolor=\"$bgcolor\" text=\"$textcolor\">";

    # set up the page
    printf "<table width=\"100%\" cellspacing=0 cellpadding=0>";
    printf "<tr><td height=1000 width=150 valign=\"top\" bgcolor=\"#234567\" align=\"center\">";
    # set up the left piece of the pages
    printf "<br><a href=\"cp.cgi?id=%s\"><img border=0 src=\"$IMAGES_URI/fcpsmall.gif\" alt=\"[Forum Control Panel]\"></a>", $field{"id"};

    # add the links
    printf "<br><hr><a class=\"leftlink\" href=\"cp.cgi?action=accounts&id=%s\">Accounts</a><br><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=groups&id=%s\">Groups</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=forums&id=%s&page=1\">Forums</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=options&id=%s\">Options</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=extrafields&id=%s\">Extra fields</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=styles&id=%s\">Styles</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=cats&id=%s\">Categories</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=smilies&id=%s\" target=\"_top\">Smilies</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=prune&id=%s\" target=\"_top\">Prune posts</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"cp.cgi?action=emailaccounts&id=%s\" target=\"_top\">Email accounts</a><hr>",$field{"id"};
    printf "<a class=\"leftlink\" href=\"forum.cgi?id=%s\" target=\"_top\">Visit forums</a><hr>",$field{"id"};

    # set up the right piece
    printf "</td><td valign=\"top\"><br><br><center><img src=\"$logo\" alt=\"[Forum Control Panel]\"></center><br><table width=\"100%\" border=0><tr><td width=5></td><td>";

    # we're shown the header now
    $shown_header=1;
}

#
# end_page()
#
# This will end a page begun with begin_page().
#
sub
end_page() {
    # close all tags.
    printf "</td><td width=5></td></tr></table></td></tr></table></body></html>";
}

#
# cp_error($error)
#
# This will show error $error in the control panel layout.
#
sub
cp_error() {
    # get the arguments
    my ($error) = @_;

    # first, do the HTML stuff
    &begin_page();

    printf "We're terribly sorry, but the following error has occoured:<p>";
    printf "<b>$error</b><p>";
    printf "If your browser supports JavaScript, you can return to the previous page by clicking <a href=\"#\" onclick=\"javascript: history.go(-1);\">this</a> link.";

    # end the page
    &end_page();

    # get outta here!
    exit;
}

#
# ShowMain()
#
# This will show the main forum pages.
#
sub
ShowMain() {
    # first, do the HTML stuff
    &begin_page();

    # set up the rest of the page
    printf "Welcome to the Forum Control Panel! You will use this panel to set up all forum options, create new forums, delete/edit users, etc. All kinds of maintaince options can be done with this utility. Please select in the list to your left what you would like to modify.<p>";

    printf "You are running <b>ForuMAX %s</b> single-site edition<br>", $FORUM_VERSION;
    printf "Your current user account module is <b>%s</b>, version <b>%s</b>", $USERDB_DESC, $USERDB_VER;

    # end the page
    &end_page();
}

#
# AskIdentification()
#
# This will request for authication.
#
sub
AskIdentification() {
    # set up the page
    printf "<head><title>$FORUM_TITLE Control Panel</title></head><body bgcolor=\"#c0c0c0\" text=\"#000000\">";

    printf "<center><img src=\"$IMAGES_URI/fcp.gif\" alt=\"[Forum Control Panel]\"><p>";
    printf "You'll need to prove your identification as an administrator before you can continue setting up the forum. Please fill out the form below and click the OK button<p></center>";
    printf "<form method=\"post\" action=\"cp.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"main\">";
    printf "<table border=0>";
    printf "<tr><td>User name</td><td><input type=\"text\" name=\"username\"></td></tr>";
    printf "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>";
    printf "</table><input type=\"submit\" value=\"OK\"></form>";
    printf "</body>";

    # leave
    exit;
}

#
# Accounts()
#
# This will show the user the possibilities to edit the accounts
#
sub
Accounts() {
    # first, do the HTML stuff
    &begin_page();

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showaccounts\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<table width=\"100%\" border=1>";
    printf "<tr><td width=\"25%\" valign=\"top\">";
    printf "<input checked type=\"radio\" name=\"type\" value=\"s\">Show specific accounts</input>";
    printf "</td><td width=\"75%\">You can carefully select which accounts you want. Empty fields will be ignored<p>";
    printf "<table border=0>";
    printf "<tr><td>Account name</td><td><input type=\"text\" name=\"accountname\"></td></tr>";
    printf "<tr><td>Full name</td><td><input type=\"text\" name=\"fullname\"></td></tr>";
    printf "<tr><td>Email address</td><td><input type=\"text\" name=\"email\"></td></tr>";
    printf "<tr><td>Parental Email address</td><td><input type=\"text\" name=\"parent_email\"></td></tr>";
    printf "<tr><td>Flags</td><td>";
    printf "<input type=\"checkbox\" name=\"f_admin\">Administrator</input><br>";
    printf "<input type=\"checkbox\" name=\"f_disabled\">Disabled account</input><br>";
    printf "<input type=\"checkbox\" name=\"f_mod\">Moderator</input><br>";
    printf "<input type=\"checkbox\" name=\"f_supermod\">Super Moderator</input><br>";
    printf "<input type=\"checkbox\" name=\"f_megamod\">Mega Moderator</input><br>";
    printf "<input type=\"checkbox\" name=\"f_under13\">Under 13</input><br>";
    printf "</td></tr></table>";
    printf "</td></tr><tr><td width=\"25%\" valign=\"top\">";
    printf "<input type=\"radio\" name=\"type\" value=\"a\">Show <b><u>all</u></b> accounts</input>";
    printf "</td><td width=\"75%\">This will show all accounts known to this forum. Use with caution for large sites!</td></tr>";
    printf "</table>";

    printf "<p><input type=\"submit\" value=\"OK\">";
    printf "</form>";

    # add the 'add account' button
    printf "<form method=\"post\" action=\"cp.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"addaccount\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"submit\" value=\"Add account\">";
    printf "</form>";

    # end the page
    &end_page();
}

#
# ShowAccounts()
#
# This will show the accounts applicable for editing.
#
sub
ShowAccounts() {
    # first, do the HTML stuff
    &begin_page();

    # first, get all accounts
    my @accounts = &GetAllAccounts();

    # set up the table
    # Account name (20%) | Full Name (20%) | Email address (20%) | Flags (20%)
    # Number of Emails (20%)
    printf "<table border=1 width=\"100%\"><tr><td width=\"20%\"><b>Account name</b></td>";
    printf "<td width=\"20%\"><b>Full name</b></td>";
    printf "<td width=\"20%\"><b>Email address</b></td>";
    printf "<td width=\"20%\"><b>Flags</b></td>";
    printf "<td width=\"20%\"><b>Number of posts</b></td>";
    printf "</tr>";

    # scan through 'em one by one
    my $ok; my $tmp; my $count = 0;
    foreach $line (@accounts) {
	# split the line
	my ($name,$pwd,$flags,$nofposts,$fullname,$email,$sig,$extra,$parentemail)=split(/:/,$line);

	# need to show all accounts?
        $ok=1;
	if ($field{"type"} eq "s") {
	    # need to check for an account name match?
	    if (($field{"accountname"} ne "") and ($ok eq 1)) {
	        # yup. do it
		$tmp=$name;
	        $tmp=~ s/($field{"accountname"})//g;
	        # did something change?
		if ($tmp ne $name) {
		    # the name has matched!
		    $ok=1;
		} else {
		    # the name didn't match.
		    $ok=0;
		}
	    }
	    # need to check for the full name?
	    if (($field{"fullname"} ne "") and ($ok eq 1)) {
	        # yup. do it
		$tmp=$fullname;
	        $tmp=~ s/($field{"fullname"})//g;
	        # did something change?
		if ($tmp ne $fullname) {
		    # the name has matched!
		    $ok=1;
		} else {
		    # this is not a match
		    $ok=0;
		}
	    }
	    # need to check the email address
	    if (($field{"email"} ne "") and ($ok eq 1)) {
		# yup. do it
		$tmp=$email;
	        $tmp=~ s/($field{"email"})//g;
	        # did something change?
		if ($tmp ne $email) {
		    # the email address has matched!
		    $ok=1;
		} else {
		    # this is not a match
		    $ok=0;
		}
	    }
	    # need to check the parental email address
	    if (($field{"parent_email"} ne "") and ($ok eq 1)) {
		# yup. do it
		$tmp=$parentemail;
	        $tmp=~ s/($field{"parent_email"})//g;
	        # did something change?
		if ($tmp ne $parentemail) {
		    # the email address has matched!
		    $ok=1;
		} else {
		    # this is not a match
		    $ok=0;
		}
	    }

            # now, check the flags
	    if (($field{"f_admin"} ne "") and ($ok eq 1)) { $ok=&check_flag ($flags,$FLAG_ADMIN); }
	    if (($field{"f_disabled"} ne "") and ($ok eq 1)) { $ok=&check_flag ($flags,$FLAG_DISABLED); }
	    if (($field{"f_megamod"} ne "") and ($ok eq 1)) { $ok=&check_flag ($flags,$FLAG_MEGAMOD); }
	    if (($field{"f_mod"} ne "") and ($ok eq 1)) { $ok=&check_flag ($flags,$FLAG_MOD); }
	    if (($field{"f_supermod"} ne "") and ($ok eq 1)) { $ok=&check_flag ($flags,$FLAG_SUPERMOD); }
	    if (($field{"f_under13"} ne "") and ($ok eq 1)) { $ok=&check_flag ($flags,$FLAG_UNDER13); }
	}

        # do we need to add this account?
	if ($ok ne 0) {
  	    # yup. show it
	    printf "<tr><td><a href=\"cp.cgi?action=editaccount&id=%s&destaccount=%s\">%s</a></td><td>%s</td><td><a href=\"mailto:%s\">%s</a></td><td>%s</td><td>%s</td></tr>",$field{"id"},&TransformForBrowser($name),$name,$fullname,$email,$email,&resolveaccountflags($flags),$nofposts;

	    $count++;
	}
    }
    printf "</table>";
    printf "<p>Total of %s account", $count;
    if ($count != 1) { print "s"; }
    printf " matched your query";

    # end the page
    &end_page();
}

#
# EditAccount()
#
# This will edit account $field{"destaccount"}.
#
sub
EditAccount() {
    # first, do the HTML stuff
    &begin_page();

    # get the record of this user
    my $userline=&GetUserRecord($field{"destaccount"});

    # was information available?
    if ($userline eq "") {
	# nope. die
	&cp_error("This account doesn't exists anymore");
    }

    # split the info
    my ($tmp,$passw,$flags,$nofposts,$fullname,$email,$sig,$extra,$parentinfo)=split(/:/,$userline);
    my ($parent_email,$parent_pwd)=split(/\|\^\|/, $parentinfo);

    # build the form
    printf "<form method=\"post\" action=\"cp.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doeditaccount\">";
    printf "<input type=\"hidden\" name=\"destaccount\" value=\"%s\">",$field{"destaccount"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # show the info
    printf "<table width=\"100%\" border=0>";
    printf "<tr><td>Account name</td><td><input type=\"text\" name=\"destname\" value=\"%s\"></td></tr>",$field{"destaccount"};
    printf "<tr><td>Full name</td><td><input type=\"text\" name=\"fullname\" value=\"%s\"></td></tr>",$fullname;
    printf "<tr><td>Email address</td><td><input type=\"text\" name=\"email\" value=\"%s\"></td></tr>",$email;
    printf "<tr><td>Parental Email address</td><td><input type=\"text\" name=\"parent_email\" value=\"%s\"></td></tr>",$parent_email;
    printf "<tr><td>Parental Profile password</td><td><input type=\"text\" name=\"parent_pwd\" value=\"%s\"></td></tr>",$parent_pwd;
    printf "<tr><td>Number of posts</td><td><input type=\"text\" name=\"nofposts\" value=\"%s\"></td></tr>",$nofposts;
    printf "<tr><td>Password</td><td><input type=\"text\" name=\"passwd1\" value=\"%s\"></td></tr>",$passw;
    $sig=&UnHTMLize (&RestoreSpecialChars ($sig));
    $sig=~ s/<br>/\n/gi;
    printf "<tr valign=\"top\"><td>Signature</td><td><textarea rows=5 cols=25 name=\"sig\">%s</textarea></td></tr>",$sig;

    # if we have extra fields, dump them in
    if ($EXTRA_PROFILE_FIELDS ne "") {
        &DumpExtraProfileEditFields($extra);
    }

    printf "</table><p>";

    # add the checkboxes
    printf "<input type=\"checkbox\" name=\"f_admin\"";
    # is this an admin account?
    if (&check_flag($flags,$FLAG_ADMIN)) {
	# yup, check it
        printf " checked";
    }
    printf ">Administrator</input><br>";

    printf "<input type=\"checkbox\" name=\"f_disabled\"";
    # is this a disabled account?
    if (&check_flag($flags,$FLAG_DISABLED)) {
	# yup, check it
        printf " checked";
    }
    printf ">Disabled</input><br>";

    printf "<input type=\"checkbox\" name=\"f_u13\"";
    # is this a disabled account?
    if (&check_flag($flags,$FLAG_UNDER13)) {
	# yup, check it
        printf " checked";
    }
    printf ">Under 13</input><br>";

    printf "<input type=\"checkbox\" name=\"f_denysig\"";
    # does this account deny signatures?
    if (&check_flag($flags,$FLAG_DENYSIG)) {
	# yup, check it
        printf " checked";
    }
    printf ">Never show signature</input><br>";

    printf "<input type=\"checkbox\" name=\"f_dontchecksig\"";
    # does this account deny signatues?
    if (&check_flag($flags,$FLAG_DONTCHECKSIG) ne "0") {
	# yup, check it
        printf " checked";
    }
    printf ">Don't check signature box by default</input><br>";

    printf "<input type=\"checkbox\" name=\"f_megamod\"";
    # is this account a mega moderator?
    if (&check_flag($flags,$FLAG_MEGAMOD) ne "0") {
	# yup, check it
        printf " checked";
    }
    printf ">Mega Moderator (can moderate any forum)</input><br>";

    # add the OK button
    printf "<p><input type=\"submit\" value=\"OK\">";

    # close the form
    printf "</form>";

    # add the 'nuke' button
    printf "<form method=\"post\" action=\"cp.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"donukeaccount\">";
    printf "<input type=\"hidden\" name=\"destaccount\" value=\"%s\">",$field{"destaccount"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Delete account\">";

    printf "</form>";

    # end the HTML stuff
    &end_page();
}

#
# DoEditAccount()
#
# This will actually edit account $field{"user"}
#
sub
DoEditAccount() {
    # we're about to modify stuff!
    &AboutToModify();

    # grab the old flags
    my $userline=&GetUserRecord($field{"destaccount"});

    # was information available?
    if ($userline eq "") {
	# nope. die
	&cp_error("This account doesn't exists anymore");
    }
    my ($tmp,$tmp2,$oldflags)=split(/:/,$userline);
    
    my @nofextras = split(/\|/, $EXTRA_PROFILE_FIELDS);

    # zap all trailing spaces
    $field{"passwd1"} = &ZapTrailingSpaces ($field{"passwd1"});
    $field{"fullname"} = &ZapTrailingSpaces ($field{"fullname"});
    $field{"destname"} = &ZapTrailingSpaces ($field{"destname"});
    $field{"destaccount"} = &ZapTrailingSpaces ($field{"destaccount"});
    $field{"email"} = &ZapTrailingSpaces ($field{"email"});
    $field{"parent_email"} = &ZapTrailingSpaces ($field{"parent_email"});
    $field{"parent_pwd"} = &ZapTrailingSpaces ($field{"parent_pwd"});
    $field{"nofposts"} = &ZapTrailingSpaces ($field{"nofposts"});
    $field{"sig"} = &FixSpecialChars (&HTMLize (&ZapTrailingSpaces ($field{"sig"})));
    for ($x = 0; $x < @nofextras; $x++) {
	$field{"extra$x"} = &FixSpecialChars (&ZapTrailingSpaces ($field{"extra$x"}));
	if (&HasInternalChars ($field{"extra$x"}) ne "0") {
	    &cp_error("Extra field contains illegal chars");
	}
	$tmp = $field{"extra$x"}; $tmp=~ s/\|\^\|//g;
	if ($tmp ne $field{"extra$x"}) {
	    &cp_error("Extra field contains illegal chars");
        }
    }

    # check everything for illegal chars
    if (&HasInternalChars ($field{"passwd1"}) ne "0") { &cp_error("The password field contains illegal chars"); }
    if (&HasInternalChars ($field{"fullname"}) ne "0") { &cp_error("The full name field contains illegal chars"); }
    if (&HasInternalChars ($field{"destname"}) ne "0") { &cp_error("The account name field contains illegal chars"); }
    if (&HasInternalChars ($field{"destaccount"}) ne "0") { &cp_error("The destination account name field contains illegal chars"); }
    if (&HasInternalChars ($field{"email"}) ne "0") { &cp_error("The email address field contains illegal chars"); }
    if (&HasInternalChars ($field{"parent_email"}) ne "0") { &cp_error("The parental email address field contains illegal chars"); }
    if (&HasInternalChars ($field{"parent_pwd"}) ne "0") { &cp_error("The parental password field contains illegal chars"); }
    if (&HasInternalChars ($field{"nofposts"}) ne "0") { &cp_error("The number of posts field contains illegal chars"); }
    if (&HasInternalChars ($field{"sig"}) ne "0") { &cp_error("Signature field contains illegal chars"); }

    # figure out the new flags
    my $new_flags="";
    if ($field{"f_admin"} ne "") { $new_flags .= $FLAG_ADMIN; }
    if ($field{"f_disabled"} ne "") { $new_flags .= $FLAG_DISABLED; }
    if ($field{"f_u13"} ne "") { $new_flags .= $FLAG_UNDER13; }
    if ($field{"f_denysig"} ne "") { $new_flags .= $FLAG_DENYSIG; }
    if ($field{"f_dontchecksig"} ne "") { $new_flags .= $FLAG_DONTCHECKSIG; }
    if ($field{"f_megamod"} ne "") { $new_flags .= $FLAG_MEGAMOD; }

    # check moderator and supermoderator flags, and add them if needed
    if (&check_flag ($oldflags, $FLAG_MOD) ne 0) { $new_flags .= $FLAG_MOD; }
    if (&check_flag ($oldflags, $FLAG_SUPERMOD) ne 0) { $new_flags .= $FLAG_SUPERMOD; }

    # figure out the extra fields
    my $extra = "";
    for ($x = 0; $x < @nofextras; $x++) {
	$extra .= $field{"extra$x"} . "|^|";
    }

    # zap any HTML code
    $extra=~ s/\</&lt;/gi;
    $extra=~ s/\>/&gt;/gi;

    # construct the record
    my $newrecord = $field{"destname"} . ":" . $field{"passwd1"} . ":" . $new_flags . ":" . $field{"nofposts"} . ":" . $field{"fullname"} . ":" . $field{"email"} . ":" . $field{"sig"} . ":" . $extra . ":" . $field{"parent_email"} . "|^|" . $field{"parent_pwd"};

    &SetUserRecord($field{"destaccount"},$newrecord);

    # if this is the current account, also activate the changes here
    if ($field{"username"} eq $field{"destaccount"}) {
	# do we use cookies?
	if ($USE_COOKIES eq "YES") {
	    # yup. build a new one
	    &SetCookie ("id",&HashID($field{"destname"},$field{"passwd1"}),60 * 60 * 24);
	} else {
	    $field{"id"}=&HashID($field{"destname"},$field{"passwd1"});
	}
    }

    # show the HTML header
    &HTMLHeader();

    # first, do the HTML stuff
    &begin_page();

    # show the 'wohoo' message
    printf "Account information successfully changed.<p>";
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"accounts\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to account maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# DoNukeAccount()
#
# This will delete account $field{"destaccount"}.
#
sub
DoNukeAccount() {
    # we're about to modify stuff!
    &AboutToModify();

    # is the destination user myself?
    if ($field{"username"} eq $field{"destaccount"}) {
	# yes. show error
	&cp_error("You cannot delete your own account");
    }

    # nuke the account
    &SetUserRecord($field{"destaccount"},"");

    # do the HTML stuff
    &begin_page();

    # show the 'wohoo' message
    printf "Account <b>%s</b> successfully deleted.<p>",$field{"destaccount"};
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"accounts\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to account maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# AddAccount()
#
# This will show the 'add account' page.
#
sub
AddAccount() {
    # first, do the HTML stuff
    &begin_page();

    # build the form
    printf "<form method=\"post\" action=\"cp.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doaddaccount\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # show the options
    printf "<table width=\"100%\" border=0>";
    printf "<tr><td>Account name</td><td><input type=\"text\" name=\"destname\" ></td></tr>";
    printf "<tr><td>Full name</td><td><input type=\"text\" name=\"fullname\"></td></tr>";
    printf "<tr><td>Email address</td><td><input type=\"text\" name=\"email\"></td></tr>";
    printf "<tr><td>Parental Email address</td><td><input type=\"text\" name=\"parent_email\"></td></tr>";
    printf "<tr><td>Parental Password</td><td><input type=\"text\" name=\"parent_pwd\"></td></tr>";
    printf "<tr><td>Number of posts</td><td><input type=\"text\" name=\"nofposts\"</td></tr>";
    printf "<tr><td>Password</td><td><input type=\"text\" name=\"passwd1\"></td></tr>";
    printf "<tr valign=\"top\"><td>Signature</td><td><textarea rows=10 cols=20 name=\"sig\">$sig</textarea></td></tr>";

    # if we have extra fields, dump them in
    if ($EXTRA_PROFILE_FIELDS ne "") {
        &DumpExtraProfileEditFields($extra);
    }

    printf "</table>";

    # add the checkboxes
    printf "<input type=\"checkbox\" name=\"f_admin\">Administrator</input><br>";
    printf "<input type=\"checkbox\" name=\"f_disabled\">Disabled</input><br>";
    printf "<input type=\"checkbox\" name=\"f_u13\">Under 13</input><br>";
    printf "<input type=\"checkbox\" name=\"f_megamod\">Mega Moderator</input><br>";
    printf "<input type=\"checkbox\" name=\"f_denysig\">Never show signature</input><br>";
    printf "<input type=\"checkbox\" name=\"f_dontchecksig\">Don't check signature box by default</input><br>";

    # add the OK button
    printf "<p><input type=\"submit\" value=\"OK\">";

    # close the form
    printf "</form>";

    # end the page
    &end_page();
}

#
# DoAddAccount()
#
# This will actually add the account
#
sub
DoAddAccount() {
    # we're about to modify stuff!
    &AboutToModify();

    my @nofextras = split(/\|/, $EXTRA_PROFILE_FIELDS);

    # zap all trailing spaces
    $field{"passwd1"} = &ZapTrailingSpaces ($field{"passwd1"});
    $field{"fullname"} = &ZapTrailingSpaces ($field{"fullname"});
    $field{"destname"} = &ZapTrailingSpaces ($field{"destname"});
    $field{"email"} = &ZapTrailingSpaces ($field{"email"});
    $field{"parent_email"} = &ZapTrailingSpaces ($field{"parent_email"});
    $field{"parent_pwd"} = &ZapTrailingSpaces ($field{"parent_pwd"});
    $field{"nofposts"} = &ZapTrailingSpaces ($field{"nofposts"});
    $field{"sig"} = &FixSpecialChars (&HTMLize (&ZapTrailingSpaces ($field{"sig"})));
    for ($x = 0; $x < @nofextras; $x++) {
	$field{"extra$x"} = &FixSpecialChars (&ZapTrailingSpaces ($field{"extra$x"}));
	if (&HasInternalChars ($field{"extra$x"}) ne "0") {
	    &cp_error("Extra field contains illegal chars");
	}
	$tmp = $field{"extra$x"}; $tmp=~ s/\|\^\|//g;
	if ($tmp ne $field{"extra$x"}) {
	    &cp_error("Extra field contains illegal chars");
        }
    }

    # check everything for illegal chars
    if (&HasInternalChars ($field{"passwd1"}) ne "0") { &cp_error("The password field contains illegal chars"); }
    if (&HasInternalChars ($field{"fullname"}) ne "0") { &cp_error("The full name field contains illegal chars"); }
    if (&HasInternalChars ($field{"destname"}) ne "0") { &cp_error("The account name field contains illegal chars"); }
    if (&HasInternalChars ($field{"email"}) ne "0") { &cp_error("The email address field contains illegal chars"); }
    if (&HasInternalChars ($field{"parent_email"}) ne "0") { &cp_error("The parental email address field contains illegal chars"); }
    if (&HasInternalChars ($field{"parent_pwd"}) ne "0") { &cp_error("The parental password field contains illegal chars"); }
    if (&HasInternalChars ($field{"nofposts"}) ne "0") { &cp_error("The number of posts field contains illegal chars"); }
    if (&HasInternalChars ($field{"sig"}) ne "0") { &cp_error("Signature field contains illegal chars"); }

    # figure out the new flags
    my $new_flags="";
    if ($field{"f_admin"} ne "") { $new_flags .= $FLAG_ADMIN; }
    if ($field{"f_disabled"} ne "") { $new_flags .= $FLAG_DISABLED; }
    if ($field{"f_u13"} ne "") { $new_flags .= $FLAG_UNDER13; }
    if ($field{"f_denysig"} ne "") { $new_flags .= $FLAG_DENYSIG; }
    if ($field{"f_dontchecksig"} ne "") { $new_flags .= $FLAG_DONTCHECKSIG; }
    if ($field{"f_megamod"} ne "") { $new_flags .= $FLAG_MEGAMOD; }

    # figure out the extra fields
    my $extra = "";
    for ($x = 0; $x < @nofextras; $x++) {
	$extra .= $field{"extra$x"} . "|^|";
    }

    # zap any HTML code
    $extra=~ s/\</&lt;/gi;
    $extra=~ s/\>/&gt;/gi;

    # construct the record
    my $newrecord = $field{"destname"} . ":" . $field{"passwd1"} . ":" . $new_flags . ":" . $field{"nofposts"} . ":" . $field{"fullname"} . ":" . $field{"email"} . ":" . $field{"sig"} . ":" . $extra . ":" . $field{"parent_email"} . "|^|" . $field{"parent_pwd"};

    &SetUserRecord($field{"destname"},$newrecord);

    # first, do the HTML stuff
    &begin_page();

    # show the 'wohoo' message
    printf "Account <b>%s</b> successfully added.<p>",$field{"destname"};
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"accounts\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to account maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# ResolveForumFlags($the_flags)
#
# This will resolve forum flags $flags and return them as a human-readable
# string. They may include <FONT COLOR=#xxxxxx>...</FONT> HTML code.
#
sub
ResolveForumFlags() {
    # get the arguments
    my ($the_flags)=@_;

    # start with nothing
    my $result="";

    # is editing disabled?
    if(&check_flag($the_flags,$FLAG_FORUM_NOEDIT) ne 0) {
        # yup. show it
	$result="<font color=\"#ff0000\">Edit denied</font>";
    }

    # is deleting disabled?
    if(&check_flag($the_flags,$FLAG_FORUM_NODELETE) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#ff0000\">Deleting denied</font>";
    }

    # is the forum disabled?
    if(&check_flag($the_flags,$FLAG_FORUM_DISABLED) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#ff0000\">Disabled</font>";
    }

    # is HTML enabled?
    if(&check_flag($the_flags,$FLAG_FORUM_HTMLOK) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#008000\">HTML enabled</font>";
    }

    # are MaX codes enabled?
    if(&check_flag($the_flags,$FLAG_FORUM_MAXOK) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#008000\">MaX codes enabled</font>";
    }

    # are image codes disabled?
    if(&check_flag($the_flags,$FLAG_FORUM_NOIMG) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#ff0000\">Image codes disabled</font>";
    }

    if(&check_flag($the_flags,$FLAG_FORUM_NOSCRIPTEMBED) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#ff0000\">Evil JavaScript and HTML disabled</font>";
    }

    # is the forum hidden?
    if(&check_flag($the_flags,$FLAG_FORUM_HIDDEN) ne 0) {
        # yup. show it
        if ($result ne "") { $result .= ", "; };
	$result.="<font color=\"#c00000\">Hidden</font>";
    }

    # return the flags
    return $result;
}

#
# EditForums()
#
# This will show the forum editing page.
#
sub
EditForums() {
    # set up the page
    &begin_page();

    # get the category data
    my @cats=&GetCats();
    my $catcount="0";
    foreach (@cats) { $catcount++; }

    # get the forums
    my @forums=&GetForums();
    my $forumcount = @forums;

    # got a page number?
    if ($field{"page"} eq "") {
	# no. force page #1
	$field{"page"} = "1";	
    }

    # need to show all?
    if ($field{"page"} eq 0) {
	# yup. do it
	$start_no = 0;
	$end_no = $forumcount;
    } else {
        # no. calculate start and end values
        $start_no = ($field{"page"} - 1) * $FORUMS_PER_CP_PAGE;
        $end_no = $start_no + $FORUMS_PER_CP_PAGE - 1;
    }

    # need to split this in pages?
    if ($forumcount > $FORUMS_PER_CP_PAGE) {
	# yup. do it
	printf "Page: ";
	my $no = 1;
	for ($i = 0; $i < ($forumcount / $FORUMS_PER_CP_PAGE); $i++) {
	    # is this the current page?
	    if ($no eq $field{"page"}) {
		# yup. don't hyperlink it
	        printf "<b>%s</b> ", $no;
	    } else {
		# no. hyperlink it
	        printf "<a href=\"cp.cgi?action=forums&id=%s&page=%s\">%s</a> ",$field{"id"},$no,$no;
	    }
	    $no++;
	}

	if ($field{"page"} ne "0") {
	    printf "<a href=\"cp.cgi?action=forums&id=%s&page=0\">All</a> ",$field{"id"};
	    $all = 0;
	} else {
	    printf "<b>All</b>";
	    $all = 1;
	}
	printf "<p>";
    } else {
	# no. show them all
	$field{"page"} = 0;
	$start_no = 0;
	$end_no = $forumcount;
	$all = 1;
    }

    # begin the form for changing forum orders
    printf "<form action=\"cp.cgi\" method=\"post\">";

    # set up the table
    # Order (5%) | Name (25%) | Category (30%) | Flags (40%)
    printf "<table border=1 width=\"100%\"><tr>\n";
    if ($all eq "1") {
        printf "<td width=\"5%\"><b>Order</b></td>\n";
        printf "<td width=\"35%\"><b>Forum name</b></td>\n";
        printf "<td width=\"60%\"><b>Flags</b></td></tr>\n";
    } else {
        printf "<td width=\"30%\"><b>Forum name</b></td>\n";
        printf "<td width=\"70%\"><b>Flags</b></td></tr>\n";
    }

    # scan through all lines
    my $x=0;
    foreach $line (@forums) {
	# get the line
	$\ = "\n";
	chomp $line;

	# need to show this one?
	if (($x >= $start_no) and ($x <= $end_no)) {
            # yup. split the line
	    my ($forumname,$forum_posts,$forum_mods,$forum_restricted,$date1,$date2,$forum_flags,$descr,$catno)=split(/:/,$line);

            printf "<input type=\"hidden\" name=\"name%s\" value=\"$forumname\">",$x + 1;

    	    # show it
	    printf "<tr><td>";

	    # show all entries?
            if ($all eq "1") {
		# show the order too
                printf "<input type=\"text\" name=\"order%s\" size=3 maxlength=6 value=\"%s\"></td><td>", $x + 1, $x + 1;
	    }

            printf "<a href=\"cp.cgi?action=editforum&destforum=%s&id=%s\">%s</a></td>",&TransformForBrowser($forumname),$field{"id"},&RestoreSpecialChars ($forumname);

            printf "<td>%s</td></tr>\n",&ResolveForumFlags($forum_flags);
	}
	$x++;
    }

    # close the table
    printf "</table><p>";

    if ($all eq "1") {
        printf "<input type=\"hidden\" name=\"action\" value=\"doeditforums\">";
        printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
        printf "<input type=\"hidden\" name=\"page\" value=\"%s\">", $field{"page"};

        printf "<input type=\"submit\" value=\"Apply modifications\">";
    } else {
	printf "<b>Notice</b> If you want to alter the forum order, you will need to display all forums. Click <a href=\"cp.cgi?action=forums&id=%s&page=0\">here</a> to show them all<p>", $field{"id"};
    }

    printf "</form>";

    # add the 'add forum' button
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"addforum\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Add forum\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# DoEditForums()
#
# This will actually modify the forums.
#
sub
DoEditForums() {
    # we're about to modify stuff!
    &AboutToModify();

    @foruminfo=&GetForums();
    my $forumcount="0";
    foreach (@foruminfo) { $forumcount++; }

    # scan them all
    for ($x = 1; $x <= $forumcount; $x++) {
	# get the record line
	my $record=&GetItemFromList($field{"name$x"},@foruminfo);

	# was there a record?
	if ($record eq "") {
	    # no. the forum must have been deleted
	    my $name = $field{"name$x"};
	    &cp_error("Forum <b>$name</b> deleted while processing");
	}
        my ($forum_name,$nofposts,$mods,$restricted_accounts,$d1,$d2,$forum_flags,$desc,$catno,$h1,$h2,$nofthreads,$newtopic,$newpost)=split(/:/,$record);

        my $newline = $forum_name . ":" . $nofposts . ":" . $mods . ":" . $restricted_accounts . ":" . $d1 . ":" . $d2 . ":" . $forum_flags . ":" . $desc . ":" . $catno . ":" . $h1 . ":" . $h2 . ":" . $nofthreads . ":" . $newtopic . ":" . $newpost;

	my $tmp=$field{"order$x"} . ":" . $newline;
	push(@order,$tmp);
    }
    @order = sort { $a <=> $b } @order;

    # does the lockfile exists?
    if ( -f $FORUM_LOCKFILE) {
        # yup. the accounts database file is locked
        &cp_error($ERROR_FORUMLOCKED);
    }

    # create the lock file
    open(LOCKFILE,"+>" . $FORUM_LOCKFILE)||&cp_error($ERROR_FILECREATERR . " ($FORUM_LOCKFILE)");

    # now, add every record to it
    foreach $it (@order) {
        my ($tmp,$forum_name,$nofposts,$mods,$restricted_accounts,$d1,$d2,$forum_flags,$desc,$catno,$h1,$h2,$nofthreads,$newthread,$newpost)=split(/:/,$it);

        my $newline = $forum_name . ":" . $nofposts . ":" . $mods . ":" . $restricted_accounts . ":" . $d1 . ":" . $d2 . ":" . $forum_flags . ":" . $desc . ":" . $catno . ":" . $h1 . ":" . $h2 . ":" . $nofthreads . ":" . $newthread . ":" . $newpost;

	printf LOCKFILE $newline . "\n";
    }
    # close the lockfile
    close(LOCKFILE);

    # copy the old fle over the new file.
    &CopyFile($FORUM_LOCKFILE,$FORUM_DATAFILE);

    # set up the page
    &begin_page();

    # show the 'wohoo' message
    printf "Forum modifications successfully applied<br>";

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# EditTheForum()
#
# This will edit forum $field{"destforum"}
#
sub
EditTheForum() {
    # set up the page
    &begin_page();

    # grab the categories
    my @cats = &GetCats();

    # get the forum info line
    my $forum_line=&GetForumInfo($field{"destforum"});

    # did that work?
    if ($forum_line eq "") {
	# nope. show error
	my $name = $field{"destforum"};
	&error("Forum <b>$name</b> does not exists anymore");
    }

    # split the line
    my ($forum_name,$nofposts,$mods,$restricted_accounts,$d1,$d2,$forum_flags,$desc,$catno,$header,$footer,$nofthreads,$newtopic_posters,$reply_posters)=split(/:/,$forum_line);

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doeditforum\">";
    printf "<input type=\"hidden\" name=\"destforum\" value=\"%s\">",$field{"destforum"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # show the info
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"40%\">Direct forum link URL</td><td><a href=\"forum.cgi?action=showforum&id=&forum=%s\">forum.cgi?action=showforum&id=&forum=%s</a><br><font size=1>This is the link you should use to directly link to this forum</font></td></td></tr>",&TransformForBrowser ($field{"destforum"}),&TransformForBrowser ($field{"destforum"});
    printf "<tr><td width=\"40%\">Forum Name</td><td><input type=\"text\" name=\"forumname\" value=\"%s\"></td></tr>",&RestoreSpecialChars ($field{"destforum"});

    printf "<tr><td width=\"40%\">Category</td><td><select name=\"cat\">";

    printf "<option value=\"0\"";
    if ($catno eq "0") { printf " selected"; }
    printf ">No category</option>";

    # build the category list
    foreach $thecat (@cats) {
        my ($name,$thecatno)=split(/:/,$thecat);

	printf "<option value=\"%s\"",$thecatno;
	if ($thecatno eq $catno) { printf " selected"; }
	printf ">%s</option>",$name;
    }

    printf "</select></td></tr>";

    printf "<tr><td>Moderator<br><font size=1>List all moderators here, separated by commas. Add a @ before any group</font></td><td><input type=\"text\" name=\"mods\" value=\"%s\"></td></tr>",$mods;
    printf "<tr><td>Restrict to<br><font size=1>List all users you want to be able to visit this forum here, separated by commas. Add a @ before any group. If blank, anyone has access</font></td><td><input type=\"text\" name=\"restricted\" value=\"%s\"></td></tr>",$restricted_accounts;
    printf "<tr><td>Users capable of creating new topics<br><font size=1>List all users you want to be able to create new topics in this forum here, separated by commas. Add a @ before any group. If blank, anyone can create new threads</font></td><td><input type=\"text\" name=\"newtopic\" value=\"%s\"></td></tr>",$newtopic_posters;
    printf "<tr><td>Users capable of posting replies<br><font size=1>List all users you want to be able to post replies here, separated by commas. Add a @ before any group. If blank, anyone can post replies</font></td><td><input type=\"text\" name=\"postreply\" value=\"%s\"></td></tr>",$reply_posters;
    printf "<tr valign=\"top\"><td>Description</td><td><textarea rows=5 cols=30 name=\"desc\">%s</textarea></tr>",&RestoreSpecialChars (&UnHTMLize ($desc));
    printf "<tr valign=\"top\"><td>Header (overrides general header if not blank. Copy the general header by inserting <code>|header|</code>)</td><td><textarea rows=5 cols=30 name=\"header\">%s</textarea></tr>",&RestoreSpecialChars (&UnHTMLize($header));
    printf "<tr valign=\"top\"><td>Footer (overrides general footer if not blank. Copy the general footer by inserting <code>|footer|</code>)</td><td><textarea rows=5 cols=30 name=\"footer\">%s</textarea></tr>",&RestoreSpecialChars (&UnHTMLize($footer));
    printf "</table><p>";

    # add the option checkboxes
    printf "<input type=\"checkbox\" name=\"f_allowhtml\"";
    if (&check_flag($forum_flags,$FLAG_FORUM_HTMLOK)) { printf " checked"; }
    printf ">Allow HTML code in forum</input><br>";

    printf "<input type=\"checkbox\" name=\"f_allowmax\"";
    if (&check_flag($forum_flags,$FLAG_FORUM_MAXOK)) { printf " checked"; }
    printf ">Allow MaX code in forum</input><br>";

    printf "<input type=\"checkbox\" name=\"f_noimg\"";
    if (&check_flag($forum_flags,$FLAG_FORUM_NOIMG)) { printf " checked"; }
    printf ">Disallow any images in forum</input><br>";

    printf "<input type=\"checkbox\" name=\"f_noscript\"";
    if (&check_flag($forum_flags,$FLAG_FORUM_NOSCRIPTEMBED)) { printf " checked"; }
    printf ">Disallow any evil JavaScript and HTML code (recommended)</input><p>";

    printf "<input type=\"checkbox\" name=\"f_hidden\"";
    if (&check_flag($forum_flags,$FLAG_FORUM_HIDDEN)) { printf " checked"; }
    printf ">Hidden forum (can be accessed but will be hidden in the forum lists</input><br>";

    printf "<input type=\"checkbox\" name=\"f_disable\"";
    if (&check_flag($forum_flags,$FLAG_FORUM_DISABLED)) { printf " checked"; }
    printf ">Disabled forum (deny all access to it and hide it in the lists)</input><br>";

    printf "<p><input type=\"checkbox\" name=\"f_recount\">Check this to recount all topics and messages</input>";

    # add the OK button and close the form
    printf "<p><input type=\"submit\" value=\"OK\"></form>";

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dodeleteforum\">";
    printf "<input type=\"hidden\" name=\"destforum\" value=\"%s\">",$field{"destforum"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # add the 'delete form' button and close the form
    printf "<input type=\"submit\" value=\"Delete forum\"></form>";

    # add the 'delete messages' button
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"deletemessages\">";
    printf "<input type=\"hidden\" name=\"destforum\" value=\"%s\">",$field{"destforum"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # add the 'delete form' button and close the form
    printf "<input type=\"submit\" value=\"Delete all forum messages\"></form>";

    # end the page
    &end_page();
}

#
# DoEditTheForum()
#
# This will actually edit forum $field{"destforum"}
#
sub
DoEditTheForum() {
    # we're about to modify stuff!
    &AboutToModify();

    # zap all trailing spaces
    $field{"forumname"} = &ZapTrailingSpaces ($field{"forumname"});
    $field{"destforum"} = &ZapTrailingSpaces ($field{"destforum"});
    $field{"mods"} = &ZapTrailingSpaces ($field{"mods"});
    $field{"restricted"} = &ZapTrailingSpaces ($field{"restricted"});
    $field{"desc"} = &ZapTrailingSpaces ($field{"desc"});
    $field{"header"} = &ZapTrailingSpaces ($field{"header"});
    $field{"footer"} = &ZapTrailingSpaces ($field{"footer"});
    $field{"cat"} = &ZapTrailingSpaces ($field{"cat"});
    $field{"newtopic"} = &ZapTrailingSpaces ($field{"newtopic"});
    $field{"postreply"} = &ZapTrailingSpaces ($field{"postreply"});

    # is the name legal?
    if (&ForumNameOK ($field{"forumname"}) eq "0") {
	# no. complain
        &cp_error("Only A-Za-z0-9,.()!;'\"\& and spaces can be used in a forum name");
    }

    # destroy any whitespace near commas
    $field{"mods"}=~ s/((\s)*)\,/\,/g;
    $field{"mods"}=~ s/\,((\s)*)/\,/g;
    $field{"restricted"}=~ s/((\s)*)\,/\,/g;
    $field{"restricted"}=~ s/\,((\s)*)/\,/g;

    # make sure all moderators exist
    my @mods = &BuildUserGroupList (split (/,/, $field{"mods"}));
    foreach $themod (@mods) {
	# does it begin with a @ ?
	if ($themod=~/$\@/) {
	    # yup. is this a valid group?
	    $themod=~s/$\@//;
	    if (&GetGroupRecord ($themod) eq "") {
	        # no. complain
	        &cp_error("Group <i>$themod</i> doesn't seem to exist");
	    }
	} else {
	    # record available?
	    if (&GetUserRecord ($themod) eq "") {
	        # no. complain
	        &cp_error("Account <i>$themod</i> doesn't seem to exist");
	    }
	}
    }

    # fix the field names
    $field{"forumname"} = &FixSpecialChars ($field{"forumname"});
    $field{"destforum"} = &FixSpecialChars ($field{"destforum"});

    # first, get the old line
    my $oldline = &GetForumInfo($field{"destforum"});

    # is the name different?
    if ($field{"destforum"} ne $field{"forumname"}) {
	# yup. rename the directory and datafile
	my $from = $FORUM_DIR . $field{"destforum"};
	my $to = $FORUM_DIR . $field{"forumname"};
	rename($from,$to)||&cp_error($ERROR_FILERENAMERR, " (from $from to $to)");
	my $from = $FORUM_DIR . $field{"destforum"} . $FORUM_EXT;
	my $to = $FORUM_DIR . $field{"forumname"} . $FORUM_EXT;
	rename($from,$to)||&cp_error($ERROR_FILERENAMERR, " (from $from to $to)");
    }

    # construct the flags
    my $new_flags = "";
    if ($field{"f_allowhtml"} ne "") { $new_flags .= $FLAG_FORUM_HTMLOK; }
    if ($field{"f_allowmax"} ne "") { $new_flags .= $FLAG_FORUM_MAXOK; }
    if ($field{"f_noimg"} ne "") { $new_flags .= $FLAG_FORUM_NOIMG; }
    if ($field{"f_disable"} ne "") { $new_flags .= $FLAG_FORUM_DISABLED; }
    if ($field{"f_noscript"} ne "") { $new_flags .= $FLAG_FORUM_NOSCRIPTEMBED; }
    if ($field{"f_hidden"} ne "") { $new_flags .= $FLAG_FORUM_HIDDEN; }

    # build the description, header and footer lines
    my $desc = &HTMLize (&FixSpecialChars ($field{"desc"}));
    my $header = &HTMLize (&FixSpecialChars ($field{"header"}));
    my $footer = &HTMLize (&FixSpecialChars ($field{"footer"}));

    # split the line
    my ($tmp1,$nofposts,$curmods,$tmp2,$d1,$d2,$tmp7,$tmp8,$tmp11,$tmp9,$tmp10,$nofthreads)=split(/:/,$oldline);

    # complain on anything containing internal chars
    if (&HasInternalChars ($field{"forumname"}) ne "0") { &cp_error("Forum name field contains internal chars"); }
    if (&HasInternalChars ($field{"mods"}) ne "0") { &cp_error("Moderators field contains internal chars"); }
    if (&HasInternalChars ($field{"restricted"}) ne "0") { &cp_error("Restricted field contains internal chars"); }
    if (&HasInternalChars ($desc) ne "0") { &cp_error("Description field contains internal chars"); }
    if (&HasInternalChars ($field{"cat"}) ne "0") { &cp_error("Category field contains internal chars"); }
    if (&HasInternalChars ($header) ne "0") { &cp_error("Header field contains internal chars"); }
    if (&HasInternalChars ($footer) ne "0") { &cp_error("Footer field contains internal chars"); }

    # need to recount all stuff?
    if ($field{"f_recount"} ne "") {
	# yup. open the forum datafile
	$nofposts = 0; $nofthreads = 0;
        open(THREADATA,$FORUM_DIR . $field{"forumname"} . $FORUM_EXT)||&error($ERROR_FILEOPENERR);

	# add all stuff
	while (<THREADATA>) {
	    # get all information
	    my $line = $_; chop $line;

	    # split the line
            my ($tmp1,$tmp2,$thread_nofposts)=split(/:/,$line);

	    # increment the counters
	    $nofposts = $nofposts + $thread_nofposts + 1;
	    $nofthreads++;
	}

	close (THREADATA);
    }

    my $newline = $field{"forumname"} . ":" . $nofposts . ":" . $field{"mods"} .":" . $field{"restricted"} . ":" . $d1 . ":" . $d2 . ":" . $new_flags . ":" . $desc . ":" . $field{"cat"} . ":" . $header . ":" . $footer . ":" . $nofthreads . ":" . $field{"newtopic"} . ":" . $field{"postreply"};

    # add the line
    &SetForumInfo($field{"destforum"},$newline);

    # build the list of all previous mods of this forum now
    my @old_modlist = &BuildUserGroupList (split (/,/, $curmods));

    # make the list of differences
    foreach $oldmod (@old_modlist) {
	# is this user still a mod in any forum?
	if (&IsModerator ($oldmod) eq "0") {
	    # no. we need to remove moderator status.
	    push (@nomods, $oldmod);
	}
    }

    # now, zap mod status from all ex-mods
    foreach $exmod (@nomods) {
	&SetAccountFlags ($exmod, 0, $FLAG_MOD);
    }

    # now, get all new mods
    my @new_modlist = &BuildUserGroupList (split (/,/, $field{"mods"}));

    # boost 'em all up to mod
    foreach $newmod (@new_modlist) {
	&SetAccountFlags ($newmod, 1, $FLAG_MOD);
    }

    # show the page
    &begin_page();

    # show the 'wohohooh' message
    printf "Forum settings changed successfully.<p>";
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# DoDeleteForum()
#
# This will delete forum $field{"destforum"}
#
sub
DoDeleteForum() {
    # we're about to modify stuff!
    &AboutToModify();

    # does the forum exists?
    my $forum_line=&GetForumInfo($field{"destforum"});
    if ($forum_line eq "") {
	# no. complain
	my $name = &RestoreSpecialChars ($field{"destforum"});
        &cp_error("Forum <b>$name</b> does not exists anymore");
    }

    # split the line
    my ($forum_name,$nofposts,$mods,$restricted_accounts,$d1,$d2,$forum_flags,$desc,$catno,$header,$footer)=split(/:/,$forum_line);

    # nuke the old line
    &SetForumInfo($field{"destforum"},"");

    # build a list of all soon-to-be ex-mods
    my @exmodlist = &BuildUserGroupList (split (/,/, $mods));

    # handle them all
    foreach $exmod (@exmodlist) {
	# moderating an existing forum?
	if (&IsModerator ($exmod) eq 0) {
	    # no. destroy the user's moderator status
	    &SetAccountFlags ($exmod, 0, $FLAG_MOD);
	}
    }

    # show the page
    &begin_page();

    # open the directory
    my $name = $FORUM_DIR . $field{"destforum"};
    opendir(DIR,$FORUM_DIR . $field{"destforum"})||&cp_error($ERROR_FILEOPENERR . " ($name)");

    # get the contents
    @files = readdir(DIR);

    # close the directory
    closedir(DIR);

    # nuke all files in the forum directory
    foreach $item (@files) {
	# is it not '.' or '..'?
	if (($item ne ".") and ($item ne "..")) {
	    # yup. nuke the file
	    my $name = $FORUM_DIR . $field{"destforum"} . "/" . $item;
	    unlink($name)||&cp_error($ERROR_FILEDELETERR . " ($name)");
	}
    }

    # remove the directory
    my $name = $FORUM_DIR . $field{"destforum"};
    rmdir($name)||&cp_error($ERROR_FILEOPENERR . " ($name)");

    # nuke the forum info file
    unlink ($FORUM_DIR . $field{"destforum"} . $FORUM_EXT);

    # show the page
    &begin_page();

    # show the 'yay' page
    printf "Forum <b>%s</b> deleted successfully.<p>",&RestoreSpecialChars ($field{"destforum"});
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# AddForum()
#
# This will show the 'add forum' page.
#
sub
AddForum() {
    # begin the page
    &begin_page();

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doaddforum\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # show the info
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"40%\">Forum Name</td><td><input type=\"text\" name=\"forumname\"></td></tr>";
    printf "<tr><td>Moderator (separate with commas)</td><td><input type=\"text\" name=\"mods\" value=\"%s\"></td></tr>",$mods;
    printf "<tr><td>Restrict to (if empty anyone can view, separate with commas)</td><td><input type=\"text\" name=\"restricted\"></td></tr>";
    printf "<tr valign=\"top\"><td>Description</td><td><textarea rows=5 cols=30 name=\"desc\"></textarea></tr>";
    printf "<tr valign=\"top\"><td>Header (overrides general header if not blank. Copy the general header by inserting <code>|header|</code>)</td><td><textarea rows=5 cols=30 name=\"header\"></textarea></tr>";
    printf "<tr valign=\"top\"><td>Footer (overrides general footer if not blank. Copy the general footer by inserting <code>|footer|</code>)</td><td><textarea rows=5 cols=30 name=\"footer\"></textarea></tr>";
    printf "</table><p>";

    # add the option checkboxes
    printf "<input type=\"checkbox\" name=\"f_allowhtml\">Allow HTML code in forum</input><br>";
    printf "<input type=\"checkbox\" name=\"f_allowmax\">Allow MaX code in forum</input><br>";
    printf "<input type=\"checkbox\" name=\"f_noimg\">Disallow any images in forum</input><br>";
    printf "<input type=\"checkbox\" name=\"f_noscript\">Disallow any evil JavaScript and HTML code (recommended)</input><p>";
    printf "<input type=\"checkbox\" name=\"f_disable\">Disabled forum (deny all access to it and hide it in the lists)</input><br>";
    printf "<input type=\"checkbox\" name=\"f_hidden\">Hidden forum (can be accessed, but is hidden in the forum lists)</input><br>";

    # add the 'OK' button and close the form
    printf "<p><input type=\"submit\" value=\"Add forum\"></form>";

    # create the 'back to forum maintenance' link
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# DoAddForum()
#
# This will actually add forum $field{"destforum"}
#
sub
DoAddForum() {
    # we're about to modify stuff!
    &AboutToModify();

    # zap all trailing spaces
    $field{"forumname"} = &ZapTrailingSpaces ($field{"forumname"});
    $field{"mods"} = &ZapTrailingSpaces ($field{"mods"});
    $field{"restricted"} = &ZapTrailingSpaces ($field{"restricted"});
    $field{"desc"} = &ZapTrailingSpaces ($field{"desc"});
    $field{"header"} = &ZapTrailingSpaces ($field{"header"});
    $field{"footer"} = &ZapTrailingSpaces ($field{"footer"});

    # is the name legal?
    if (&ForumNameOK ($field{"forumname"}) eq "0") {
	# no. complain
        &cp_error("Only A-Za-z0-9,.()!;'\"\& and spaces can be used in a forum name");
    }

    # is there an actual name given?
    if ($field{"forumname"} eq "") {
	# no. complain
	&cp_error("Blank forum names are not allowed");
    }

    # fix the names
    $destname = &FixSpecialChars ($field{"forumname"});

    # does the forum already exists?
    if (&GetForumInfo($field{"forumname"}) ne "") {
	# yes. show error
	my $name = $field{"forumname"};
	&cp_error("Forum <b>$name</b> already exists");
    }

    # construct the moderator list
    my @mods = &BuildUserGroupList (split (/,/, $field{"mods"}));
    foreach $mod (@mods) {
	# boost 'em up to moderator
	&SetAccountFlags ($mod, 1, $FLAG_MOD);
    }

    # construct the flags
    my $new_flags = "";
    if ($field{"f_allowhtml"} ne "") { $new_flags .= $FLAG_FORUM_HTMLOK; }
    if ($field{"f_allowmax"} ne "") { $new_flags .= $FLAG_FORUM_MAXOK; }
    if ($field{"f_noimg"} ne "") { $new_flags .= $FLAG_FORUM_NOIMG; }
    if ($field{"f_disable"} ne "") { $new_flags .= $FLAG_FORUM_DISABLED; }
    if ($field{"f_noscript"} ne "") { $new_flags .= $FLAG_FORUM_NOSCRIPTEMBED; }
    if ($field{"f_hidden"} ne "") { $new_flags .= $FLAG_FORUM_HIDDEN; }

    # build the description, header and footer lines
    my $desc = &HTMLize (&FixSpecialChars ($field{"desc"}));
    my $header = &HTMLize (&FixSpecialChars ($field{"header"}));
    my $footer = &HTMLize (&FixSpecialChars ($field{"footer"}));
    my $catno = "";

    # complain on anything containing internal chars
    if (&HasInternalChars ($destname) ne "0") { &cp_error("Forum name field contains internal chars"); }
    if (&HasInternalChars ($field{"mods"}) ne "0") { &cp_error("Moderators field contains internal chars"); }
    if (&HasInternalChars ($field{"restricted"}) ne "0") { &cp_error("Restricted field contains internal chars"); }
    if (&HasInternalChars ($desc) ne "0") { &cp_error("Description field contains internal chars"); }
    if (&HasInternalChars ($header) ne "0") { &cp_error("Header field contains internal chars"); }
    if (&HasInternalChars ($footer) ne "0") { &cp_error("Footer field contains internal chars"); }

    my $newline = $destname . ":0:" . $field{"mods"} . ":" . $field{"restricted"} . ":" . &GetTimeDate() . ":" . $new_flags . ":" . $desc . ":" . $catno . ":" . $header . ":" . $footer . "0";

    # add the line
    &SetForumInfo($destname,$newline);

    # add the directory
    my $name = $FORUM_DIR . $destname;
    mkdir($name,$FORUM_DIR_PERMS)||&cp_error($ERROR_FILECREATERR . " ($name)");

    # create the forum datafile
    &TruncateFile ($FORUM_DIR . $destname . $FORUM_EXT);

    # show the page
    &begin_page();

    # show the 'wohohooh' message
    printf "Forum <b>%s</b> added successfully.<p>",$field{"forumname"};

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# DeleteMessages()
#
# This will delete all messages from forum $field{"destforum"}
#
sub
DeleteMessages() {
    # we're about to modify stuff!
    &AboutToModify();

    # does the exists?
    if (&GetForumInfo($field{"destforum"}) eq "") {
	# no. complain
	my $name = $field{"destforum"};
        &cp_error("Forum <b>$name</b> does not exists");
    }

    # open the directory
    my $name = $FORUM_DIR . $field{"destforum"};
    opendir(DIR,$name)||&cp_error($ERROR_FILEOPENERR . " ($name)");

    # get the contents
    @files = readdir(DIR);

    # close the directory
    closedir(DIR);

    # nuke all files in the forum directory
    foreach $item (@files) {
	# is it not '.' or '..'?
	if (($item ne ".") and ($item ne "..")) {
	    # yup. nuke the file
	    my $name = $FORUM_DIR . $field{"destforum"} . "/" . $item;
	    unlink($name)||&cp_error($ERROR_FILEDELETERR . " ($name)");
	}
    }

    # get all old info
    my $oldline = &GetForumInfo ($field{"destforum"});
 
    # split it
    my ($name,$nofposts,$mods,$restricted,$tmp1,$tmp2,$forum_flags,$descr,$catno,$h1,$h2,$nofthreads,$newthread,$newpost)=split(/:/,$oldline);

    # construct the new line (always have zero posts now)
    my $newline = $name . ":0:" . $mods . ":" . $restricted . ":" . &GetTimeDate() . ":" . $forum_flags  . ":" . $descr . ":" . $catno . ":" . $h1 . ":" . $h2 . ":0:" . $newthread . ":" . $newpost;

    # change the line
    &SetForumInfo($field{"destforum"},$newline);

    # truncate the old info line
    &TruncateFile ($FORUM_DIR . $field{"destforum"} . $FORUM_EXT);

    # show the page
    &begin_page();

    # show the 'yay' page
    printf "All forums messages from forum <b>%s</b> deleted successfully.<p>",$field{"destforum"};
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# ShowRadio($cond,$pos,$neg,$name)
#
# This will show a radio button called $name, with values $pos and $neg. If
# $cond is true, $pos will be checked, otherwise $neg.
#
sub
ShowRadio() {
    # grab the arguments
    my ($cond,$pos,$neg,$name)=@_;

    # start the thing
    printf "<input type=\"radio\" name=\"$name\" value=\"YES\"";
    if ($cond eq "YES") { printf "checked"; }
    printf ">$pos</input><br>";
    printf "<input type=\"radio\" name=\"$name\" value=\"NO\"";
    if ($cond ne "YES") { printf "checked"; }
    printf ">$neg</input><br>";
}


#
# Options()
#
# This will show the options page.
#
sub
Options() {
    # show the page
    &begin_page();

    # add the actions
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doptions\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>General options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect the entire site. They set all sorts of generic options</td></tr>";
    printf "<tr><td width=\"50%\"><b>Email method</b><br>If you want the forum to be able to send emails (some options require this), you'll have to set up which which email method ForuMAX will use. If you use UNIX, you'll probably want to use <a href=\"http://www.sendmail.org\">sendmail</a> or a sendmail wrapper to send email. If you are using Windows, you'll need to use a SMTP server. Ask your system administrator for more information</td><td>";

    printf "<input type=\"radio\" name=\"email_method\" ";
    if ($EMAIL_METHOD eq "0") { printf "checked "; }
    printf "value=\"0\">No emailing</input><br>";

    printf "<input type=\"radio\" name=\"email_method\" ";
    if ($EMAIL_METHOD eq "1") { printf "checked "; }
    printf "value=\"1\">Use sendmail to send mail (UNIX)</input><br>";

    printf "<input type=\"radio\" name=\"email_method\" ";
    if ($EMAIL_METHOD eq "2") { printf "checked "; }
    printf "value=\"2\">Use a SMTP server to send mail (Windows)</input></td></tr>";

    printf "<tr><td><b>Sendmail location</b><br>This is the location of the sendmail executable. It will only be used if you specify emailing using sendmail.</td><td><input type=\"text\" name=\"sendmail_loc\" value=\"%s\" size=40></td></tr>",$SENDMAIL_LOCATION;
    printf "<tr><td><b>SMTP server name</b><br>This is the name of the host you use to send email via SMTP. It will only be used if you specify emailing using SMTP</td><td><input type=\"text\" name=\"smtp_box\" value=\"%s\" size=40></td></tr>",$SMTP_BOX;
    printf "<tr><td><b>SMTP port</b><br>This is the port of the host you use to send email via STMP. it will only be used if you specify emailing using SMTP. This is normally 25. When in doubt, consult your system administrator</td><td><input type=\"text\" name=\"smtp_port\" value=\"%s\" size=40></td></tr>",$SMTP_PORT;
    printf "<tr><td><b>Administrator email address</b><br>This is the reply-to address for all emails sent by the forum. Any mail error messages will also be sent here. It will also be used in error messages.</td><td><input type=\"text\" name=\"email\" value=\"%s\" size=40></td></tr>",$ADMIN_EMAIL;
    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>Layout options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect the entire site. They generally will affect the forums looks (usually in generated pages)</td></tr>";
    printf "<tr><td width=\"50%\">Website address</td><td><input type=\"text\" name=\"websiteuri\" value=\"%s\" size=40></td></tr>",$WEBSITE_URI;
    printf "<tr><td>Website link text</td><td><input type=\"text\" name=\"websitelink\" value=\"%s\"></td></tr>",$WEBSITE_LINK;
    printf "<tr><td>Server Timezone</td><td><input type=\"text\" name=\"timezone\" value=\"%s\"></td></tr>",$TIMEZONE;
    printf "<tr><td>Title of the forum</td><td><input type=\"text\" name=\"forumtitle\" value=\"%s\"></td></tr>",$FORUM_TITLE;
    printf "<tr><td>Images URL</td><td><input type=\"text\" name=\"images_uri\" value=\"%s\"></td></tr>",$IMAGES_URI;

    printf "<tr><td valign=\"top\">Policies for new members</td><td><textarea rows=3 cols=20 name=\"policies\">%s</textarea></td></tr>",&UnHTMLize($FORUM_POLICIES);
    printf "<tr><td valign=\"top\">Header text<br>(will be added to every page, at the beginning)</td><td><textarea rows=3 cols=20 name=\"start_text\">%s</textarea></td></tr>",&UnHTMLize($START_PAGE_TEXT);

    printf "<tr><td valign=\"top\">Footer text<br>(will be added to every page, at the end)</td><td><textarea rows=3 cols=20 name=\"end_text\">%s</textarea></td></tr>",&UnHTMLize($END_PAGE_TEXT);

    printf "<tr><td valign=\"top\">Extra style sheet<br><font size=1>You can add extra style sheet settings here. They will be added to the &lt;style type=\"text/css\"&gt; tag</font></td><td><textarea rows=3 cols=20 name=\"extra_style\">%s</textarea></td></tr>",$EXTRA_STYLE;

    printf "<tr><td valign=\"top\"><b>Import header and footer from files</b><br>If this is enabled, the header and footer will be read from the file which name you type in the field. Notice: this file <i>must</i> reside in your internal forum data directory. Slashes will automatically be removed</td><td>";
    &ShowRadio($HEADERFOOTER_FILE,"Header and footer fields indicate files","Header and footer fields are just text","f_headerfooterfile");
     printf "</td></tr>";

    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>Forum options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect how the forum itself acts</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow registration</b><br>You enabling this, you allow users to be able to register an account. If you disable this, only administrators can create accounts by using the control panel</td><td>";

    &ShowRadio($ALLOW_REGISTRATION,"Allow registration of accounts","Deny registration of accounts","f_allowreg");
     printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Disable forum</b><br>If this is enabled, the forum will be inaccessible to anyone.</td><td>";

    &ShowRadio($FORUM_DISABLED,"Forum is disabled","Forum is enabled","f_disabled");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow emailing of lost password</b><br>If this is enabled, users can recover their password by having the forum email it back to them. Email services are required.</td><td>";

    &ShowRadio($RECOVER_PASSWORD,"Allow emailing of lost password","Deny emailing of lost passwords","f_recoverpassword");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Start with category display</b><br>If this is enabled, the forum will show the category disabled rather than the forums on start. If you have categories and want to list them, this is required</td><td>";
    &ShowRadio($SHOW_CATS,"Show categories","Hide categories", "f_showcats");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Show 'Hop To' list</b><br>If this is enabled, the forum will show a list box with all accessible forums and a Go button to hop to them. This makes navigating very easy. <font color=\"#ff0000\"><b><i>Recommended</i><b></font></td><td>";

    &ShowRadio($SHOW_HOPTO,"Show Hop To list","Hide Hop To List", "f_showhopto");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Require valid email address</b><br>If this is enabled, the forum will email a randomly-generated password to the email address of the new account. This ensures the email address is valid. It requires email services. <font color=\"#ff0000\"><b><i>Recommended</i><b></font></td><td>";
    &ShowRadio($REQUIRE_VALID_EMAIL,"Email random password upon registering","Let user set their password", "f_requirevalidemail");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Require unique email addresses</b><br>If this is enabled, the forum will verify whether an email address is already used during registering. If it is, it will give an error. This ensures no two people use the same email address. <font color=\"#ff0000\"><b><i>Recommended</i><b></font></td><td>";
    &ShowRadio($REQUIRE_UNIQUE_EMAIL,"Require a unique email address","Duplicate email addresses are allowed", "f_requireuniqueemail");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Require logging in to the forum</b><br>If this is enabled, the forum requires everyone to log in before it will do anything else.</font></td><td>";
    &ShowRadio($REQUIRE_LOGIN,"Require users to log in to the forum","Users don't need to log in, anyone can access the forums","f_requirelogin");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Show total number of members on forum pages</b><br>If this is enabled, the forum will show how much members there are currently registered.</font></td><td>";
    &ShowRadio($SHOW_NOF_MEMBERS,"Show number of registered members","Hide number of registered members", "f_shownofmembers");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Force automatic login when username/password entered</b><br>If this is enabled, the forum will automatically log in users when they have entered their username/password pair when making a post or editing/deleting/locking one. This saves them from having to login, as long as they don't use their browser's back button, that is. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($FORCE_LOGIN,"Force automatic login","Don't force automatic login","f_forcelogin");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Show username of the user which closes a thread</b><br>If this is enabled, the forum will display who has closed a thread. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($SHOW_LOCKER,"Show username of whoever closes a thread", "Don't show the username of whoever closes a thread","f_showlock");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Show the username of the last poster</b><br>If this is enabled, the forum will display who has last posted to every thread. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($SHOW_LAST_POSTER,"Show username of the last poster", "Don't show the username of the last poster","f_showlastposter");
    printf "</td></tr>";
  
    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>Censoring and Banning options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect the entire site. They control which IP addresses are not allowed to use the forum and which words will be censored out.</td></tr>";

    printf "<tr><td width=\"50%\"><b>Enable censoring</b><br>If this is enabled, the forum will browse all censored words, and censor them when they appears in posts or subjects. Users also will not be allowed to register a name that contains a censored word.</font></td><td>";
    &ShowRadio($CENSOR_POSTS,"Enable censoring", "Disable censoring", "f_censor_posts");
     printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Words to censor</b><br>You can specify all words here that you like to be censored. Each word must be separated by a space or newline. If a word is listed here and censoring is enabled, the word will be replaced by asteriks (eg. dog becomes ***)</td><td>";
     printf "<textarea name=\"censored_words\" rows=5 cols=40>%s</textarea></td></tr>", $CENSORED_WORDS;
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Banned email addresses</b><br>You can specify all email addresses here that you would like to be banned. Each email address must be separated by a space or newline. If an email address is listed here and an user tries to register using it, the request will be denied. You can ban email addresses specifically (eg. user\@host.com), but you can also ban an entire domain by typing the domain name (eg. host.com)</td><td>";
     printf "<textarea name=\"banned_email\" rows=5 cols=40>%s</textarea></td></tr>", $BANNED_EMAIL;

    printf "<tr><td width=\"50%\"><b>Banned IP addresses</b><br>You can specify all IP addresses here that you would like to be banned. Each IP address address must be separated by a space or newline. If an user tries to access the forums or control panel from a banned IP address, he will permanently be denied access. Administrators can override IP banning. You can ban IP addresses specifically (eg. 1.2.3.4), but you can also ban an entire class (eg. 1.2.3.). <b>NOTE the trailing dot when banning IP classes!</b></td><td>";
     printf "<textarea name=\"banned_ip\" rows=5 cols=40>%s</textarea></td></tr>", $BANNED_IP;

    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>Profile options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect the entire site. They control some user profile options</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow signatures</b><br>If this is enabled, the forum will allow users to add a signature to their post (which is text which can be edited in the profile)</td><td>";
    &ShowRadio($SIG_ALLOWED,"Allow signatures", "Do not allow signatures", "f_allow_sigs");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Check <i>Show signature</i> box by default</b><br>If this is enabled, the forum will check the <i>Show signature</i> checkbox when making new posts. Users can force it to be unchecked by editing their profile and checking the appropriate box there</td><td>";
    &ShowRadio($SIG_SHOWDEFAULT,"Check <i>Show Signature</i> by default", "Do not check <i>Show Signature</i> by default", "sig_checkdefault");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow HTML code in signature</b><br>If this is enabled, the forum will allow HTML code in the signature.</td><td>";
    &ShowRadio($SIG_ALLOW_HTML,"Allow HTML code in signatures", "Do not allow HTML code in signatures", "sig_allow_html");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow MaX code in signature</b><br>If this is enabled, the forum will allow MaX code in the signature.</td><td>";
    &ShowRadio($SIG_ALLOW_MAX,"Allow MaX code in signatures", "Do not allow MaX code in signatures", "sig_allow_max");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Delete evil JavaScript and HTML code from signature</b><br>If this is enabled, the forum will automatically delete any bad JavaScript and HTML code from signatures. This will cause some HTML tags to be removed (like <code>APPLET</code>, <code>FRAME</code> etc), and any JavaScript tags. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($SIG_DENY_EVIL_HTML,"Remove evil JavaScript and HTML from signatures", "Do not remove evil JavaScript and HTML from signatures", "sig_deny_evil_html");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow images in signatures</b><br>If this is enabled, the forum will allow inclusion of images in signatures. <font color=\"#ff0000\"><b><i>Not Recommended</i></b></font></td><td>";
    &ShowRadio($SIG_ALLOW_IMGS,"Allow images in signatures", "Do not allow images in signatures", "sig_allow_imgs");
    printf "</td></tr>";

    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>Even more forum options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect the entire site. They control generic forum issues.</td></tr>";

    printf "<tr><td width=\"50%\"><b>Posts shown per page</b><br>This controls how much posts the forum will show at a single page. If there are more, the thread will be divided in multiple pages.</font></td><td>";
    printf "<input type=\"text\" name=\"postsatascreen\" value=\"%s\" size=40></td></tr>",$FORUM_POSTS_AT_A_SCREEN;
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Threads shown per page</b><br>This controls how much threads the forum will show at a single page. If there are more, the thread display will be divided in multiple pages.</font></td><td>";
    printf "<input type=\"text\" name=\"threadsatascreen\" value=\"%s\" size=40></td></tr>",$FORUM_THREADS_AT_A_SCREEN;
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Title of an administrator</b><br>This is the text that will be shown as status below an account with Administrator access. Please note that a custom status overrides this!</font></td><td>";
    printf "<input type=\"text\" name=\"admin_name\" value=\"%s\" size=40></td></tr>",$TITLE_ADMIN;
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Title of a mega moderator</b><br>This is the text that will be shown as status below an account with Mega Moderator access. Please note that a custom status overrides this!</font></td><td>";
    printf "<input type=\"text\" name=\"megamod_name\" value=\"%s\" size=40></td></tr>",$TITLE_MEGAMOD;
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Title of a super moderator</b><br>This is the text that will be shown as status below an account with Super Moderator access. Please note that a custom status overrides this!</font></td><td>";
    printf "<input type=\"text\" name=\"supermod_name\" value=\"%s\" size=40></td></tr>",$TITLE_SUPERMOD;
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Title of a moderator</b><br>This is the text that will be shown as status below an account with Moderator access. Please note that a custom status overrides this!</font></td><td>";
    printf "<input type=\"text\" name=\"mod_name\" value=\"%s\" size=40></td></tr>",$TITLE_MOD;

    printf "<tr><td width=\"50%\"><b>Title of a normal member</b><br>This is the text that will be shown as status below an account with no extra access. Please note that a custom status overrides this!</font></td><td>";
    printf "<input type=\"text\" name=\"member_name\" value=\"%s\" size=40></td></tr>",$TITLE_MEMBER;

    printf "<tr><td width=\"50%\"><b>Title of a unknown member</b><br>This is the text that will be shown as status below an account that isn't listed in the accounts database.</font></td><td>";
    printf "<input type=\"text\" name=\"nomember_name\" value=\"%s\" size=40></td></tr>",$TITLE_NOMEMBER;

    printf "<tr><td width=\"50%\"><b>Display icons at a thread</b></input><br>If this is enabled, users will be able to select an icon that describes the mood of their post. The actual icon files that will be used are <code>icon1.gif</code> until the number you supply here. Only GIFs can be used. All files must be in the images directory.</font></td><td>";
    printf "<input type=\"radio\" name=\"show_icons\" value=\"YES\"";
    if ($NOF_ICONS ne "NO") { printf " checked"; }
    printf ">Display icons, use <code>icon0.gif</code> to <code>icon</code><input type=\"text\" name=\"noficons\" value=\"%s\" size=10><code>.gif</code></input><br>",$NOF_ICONS;
    printf "<input type=\"radio\" name=\"show_icons\" value=\"NO\"";
    if ($NOF_ICONS eq "NO") { printf " checked"; }
    printf ">Do not display any icons</input></td></tr>";

    printf "<tr><td width=\"50%\"><b>Require a mood icon to be selected</b><br>If this is enabled, there will always be shown a mood icon for all threads, whether it is enabled or not. While creating new posts, the <i>No icon</i> option will be hidden as well. Threads already created without an icon will get <code>icon0.gif</code>. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($REQUIRE_ICON,"Require a mood icon to be selected","Do not require a mood icon to be selected","f_require_icon");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Show information about accounts when viewing posts</b><br>If this is enabled, the forum will show information about the author of the message. Included information is the member status, number of posts and any extra, unhidden profile fields. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($FORUM_OPTION_SHOWINFO,"Show information about the author","Do not show information about the author","f_showinfo");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>After editing messages, add text stating message has been editing</b><br>If this is enabled, the forum will add <code>[Edited at <i>timestamp</i> by <i>username</i>]</code> to any message that has been edited. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($SHOW_EDIT,"Add text stating message has been edited","Do not add text stating message has been edited", "f_showedit");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Allow editing and deleting of message in locked threads</b><br>If this is enabled, the forum will allow administrators and moderators to edit and delete messages in locked threads.</td><td>";
    &ShowRadio($ALTER_LOCKED,"Allow editing/deleting in locked thread","Do not allow editing/deleting in locked threads","f_alterlocked");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Post editing/deleting options</b><br>This will alter the forum permissions to edit and delete messages. This applies to all moderators and administrators.</td><td>";
    printf "<input type=\"radio\" name=\"edit_delete\" ";
    if ($ALLOW_EDIT_DELETE eq "0") { printf "checked "; }
    printf "value=\"0\">No editing/deleting allowed</input><br>";
    printf "<input type=\"radio\" name=\"edit_delete\" ";
    if ($ALLOW_EDIT_DELETE eq "1") { printf "checked "; }
    printf "value=\"1\">Editing allowed, but not deleting</input><br>";
    printf "<input type=\"radio\" name=\"edit_delete\" ";
    if (($ALLOW_EDIT_DELETE ne "0") and ($ALLOW_EDIT_DELETE ne "1")) { printf "checked "; }
    printf "value=\"2\">Editing and deleting allowed</input></select></td></tr>";

    printf "<tr><td width=\"50%\"><b>Thread locking/deleting options</b><br>This will alter the forum permissions to lock and delete threads. This applies to all moderators and administrators.</td><td>";

    printf "<input type=\"radio\" name=\"lock_delete\" ";
    if ($ALLOW_LOCK_DELETE eq "0") { printf "checked "; }
    printf "value=\"0\">No locking/deleting allowed</input><br>";

    printf "<input type=\"radio\" name=\"lock_delete\" ";
    if ($ALLOW_LOCK_DELETE eq "1") { printf "checked "; }
    printf " value=\"1\">Locking allowed, but not deleting</input><br>";

    printf "<input type=\"radio\" name=\"lock_delete\" ";
    if ($ALLOW_LOCK_DELETE eq "2") { printf "checked "; }
    printf " value=\"2\">Locking and deleting allowed</input>";

    printf "<tr><td width=\"50%\"><b>Allow unlocking of locked threads</b><br>If this is enabled, moderators and administrators will be able to unlock previously-locked threads, making them available for replies.</td><td>";
    &ShowRadio($ALLOW_UNLOCK,"Allow unlocking of locked threads","Do not allow unlocking of locked threads","f_allowunlock");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>IP logging/display options</b><br>This will control the forums options on IP logging and displayal. You can strictly disable IP logging, meaning that IP addresses will not get stored anywhere. You can also log them, but limit access to them to a moderators. You can also allow anyone to view them. This only applies to new threads and posts, IP addresses of anything else will not be stored.</td><td>";
    
    printf "<input type=\"radio\" name=\"ip_log_display\" ";
    if ($IP_LOG_DISPLAY eq "0") { printf "checked "; }
    printf "value=\"0\">No IP logging/displaying</input><br>";

    printf "<input type=\"radio\" name=\"ip_log_display\" ";
    if ($IP_LOG_DISPLAY eq "1") { printf "checked "; }
    printf "value=\"1\">Log IP, only viewable by admins</input><br>";

    printf "<input type=\"radio\" name=\"ip_log_display\" ";
    if ($IP_LOG_DISPLAY eq "2") { printf "checked "; }
    printf "value=\"2\">Log IP, only viewable by admins and mods</input><br>";

    printf "<input type=\"radio\" name=\"ip_log_display\" ";
    if ($IP_LOG_DISPLAY eq "3") { printf "checked "; }
    printf "value=\"3\">Log IP, viewable by anyone</input></td></tr>";

    printf "<tr><td width=\"50%\"><b>Set cookies for identification</b><br>If this is enabled, the forum will use cookies rather than the classical id= method. <font color=\"#ff0000\"><b><i>Highly Recommended</i></b></font></td><td>";
    &ShowRadio($USE_COOKIES,"Use cookies for identification","Use old id= string for identification","f_usecookies");
    printf "</td></tr>";

    printf "<tr><td width=\"50%\"><b>Show previous post when replying</b><br>If this is enabled, the forum will show the replies to this thread in the new post page. <font color=\"#ff0000\"><b><i>Recommended</i></b></font></td><td>";
    &ShowRadio($REVIEW_POST,"Show replies on the reply page","Don't show replies on the reply page","f_reviewpost");
    printf "</td></tr>";

    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>COPPA options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options will affect the registration process. COPPA is an American law there requires kids below 13 to have parental permission when signing up to the site. ForuMAX 4.0 is fully COPPA-compliant, and will allow you to show the kid a special page. When someone signs up, they will first have to submit their birthdate and country. This information will <i>not</i> be stored on the server. A cookie will be set on the client's computer so they can't just surf back and submit a lower date of birth. COPPA is an American law, you may or may not need to comply with it.</td></tr>";

    printf "<tr><td width=\"50%\"><b>Enable COPPA compliance</b><br>If this is enabled, the forum will require everyone who signs up to submit his date of birth and country of origin.</font></td><td>";
    &ShowRadio($COPPA_ENABLED,"Enable COPPA compliance","Do not enable COPPA compliance","f_coppa_enabled");

    printf "</td></tr>";

    printf "<tr><td><b>COPPA kid instruction form</b><br>These are the instructions the kid will receive before he can sign up. <b>You will need to insert <code>|PARENT_EMAIL|</code> somewhere!</b> That will be replaced by an input box in which the kid can type the email address of the parent, which will receive the instructions. HTML is allowed here.</td><td><textarea name=\"coppa_kid_instr\" rows=5 cols=25>%s</textarea></td></tr>",$COPPA_KID_INSTR;

    printf "<tr><td><b>COPPA parent instruction form</b><br>These are the instructions the parent will receive. Make sure you provide clear instructions! HTML is allowed here.</td><td><textarea name=\"coppa_parent_instr\" rows=5 cols=25>%s</textarea></td></tr>",$COPPA_PARENT_INSTR;

    printf "</table><p>";

    printf "<table width=\"100%\" cellpadding=0 cellspacing=0 border=1>";
    printf "<tr bgcolor=\"#000000\"><td colspan=2 align=\"center\"><font color=\"#ffff00\"><b>Advanced options</b></font></td></tr>";
    printf "<tr bgcolor=\"#ffff00\"><td colspan=2>These options are only meant for people who know exactly what they're doing. You are recommended not to mess with there, unless you have a clear idea what you are doing or was asked to use them by ForuMAX support.</td></tr>";

    printf "<tr><td width=\"50%\"><b>Delete all lockfiles</b><br>This will cause the forum to delete all lockfiles. This should <i>only</i> be done if you cannot post to certain forum because of <i>Forum Locked</i> errors.</td><td><input type=\"checkbox\" name=\"kill_lockfiles\">Delete all lockfiles</input></td></tr>";
    printf "<tr><td width=\"50%\"><b>Rebuild moderator list</b><br>This will cause the forum to rebuild the moderator list. This should only be done to upgrade to 4.1 from any old version</td><td><input type=\"checkbox\" name=\"rebuild_mods\">Rebuild moderator list</input></td></tr>";
    printf "</table><p>";

    printf "<p><input type=\"submit\" value=\"OK\">";
    printf "</form>";

    # end the page
    &end_page();
}

#
# WriteConfig()
#
# This will write the forum configuration file.
#
sub
WriteConfig() {
    # if there is still the old configuration file, say it's locked
    if (-f $TMPCONF_FILE) {
	&cp_error("Configuration file locked. Try again later");
    }

    # open the old config file, and read the first line
    open(OLDCONF,$CONF_FILE)||&cp_error($ERROR_FILEOPENERR . " ($CONF_FILE)");

    # get the first line of the old file (UNIX wants it!)
    my $exe = <OLDCONF>;
    chop $exe;

    # close the old file
    close(OLDCONF);

    # open the config file, enforce overwrite
    open(CONFILE,"+>" . $TMPCONF_FILE)||&cp_error($ERROR_FILEOPENERR . " ($TMPCONF_FILE)");

    # hide the contents from prying eyes (XXX)
    chmod(0660,$TMPCONF_FILE);

    # first, dump in the first line from the old file and some info
    printf CONFILE $exe . "\n";
    printf CONFILE "#\n";
    printf CONFILE "# ForuMAX Version $FORUM_VERSION - forum_options.pl\n";
    printf CONFILE "#\n";
    printf CONFILE "# This contains the forum options. You can set them via the control panel.\n";
    printf CONFILE "#\n";
    printf CONFILE "# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for\n";
    printf CONFILE "# license information.\n";
    printf CONFILE "#\n\n";

    printf CONFILE "# \$DIRS_SETUP will indiciate whether the directories have been set up. It can\n";
    printf CONFILE "# be YES or NO. The control panel will allow you to alter them if this is NO.\n";
    printf CONFILE "\$DIRS_SETUP=qq~%s~;\n\n",&TransformForConf($DIRS_SETUP);

    printf CONFILE "# \$TMPCONF_FILE is the name of the temponary configuration file. It must reside\n";
    printf CONFILE "# in a directory where whatever user that runs the webserver can write.\n";
    printf CONFILE "\$TMPCONF_FILE=qq~%s~;\n\n",&TransformForConf($TMPCONF_FILE);

    printf CONFILE "# \$CONF_FILE is the name of the real configuration file. It must be readable\n";
    printf CONFILE "# and writeable by whoever runs the web server\n";
    printf CONFILE "\$CONF_FILE=qq~%s~;\n\n",&TransformForConf($CONF_FILE);

    printf CONFILE "# \$FORUM_EXT is the extension of the forum files. It must include the dot\n";
    printf CONFILE "\$FORUM_EXT=qq~%s~;\n\n",&TransformForConf($FORUM_EXT);

    printf CONFILE "# \$FORUM_EXT_LOCK is the extension of the forum lock files. It must include\n";
    printf CONFILE "# the dot\n";
    printf CONFILE "\$FORUM_EXT_LOCK=qq~%s~;\n\n",&TransformForConf($FORUM_EXT_LOCK);

    printf CONFILE "# \$FORUM_COLOR_xxx are the forum colors. They must be in the form #xxxxxx\n";
    printf CONFILE "\$TEXT_COLOR=qq~%s~;\n",&TransformForConf($TEXT_COLOR);
    printf CONFILE "\$FORUM_COLOR_TEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_TEXT);
    printf CONFILE "\$FORUM_COLOR_MEMBERLINK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_MEMBERLINK);
    printf CONFILE "\$FORUM_COLOR_MEMBERLINK_HOVER=qq~%s~;\n",&TransformForConf($FORUM_COLOR_MEMBERLINK_HOVER);
    printf CONFILE "\$FORUM_COLOR_LIST_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_LIST_CONTENTS_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_CONTENTS_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_LIST_INFO=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_INFO);
    printf CONFILE "\$FORUM_COLOR_LIST_FORUMNAME=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_FORUMNAME);
    printf CONFILE "\$FORUM_COLOR_MEMBERLINK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_MEMBERLINK);
    printf CONFILE "\$FORUM_COLOR_MEMBERLINK_HOVER=\qq~%s~;\n",&TransformForConf($FORUM_COLOR_MEMBERLINK_HOVER);
    printf CONFILE "\$FORUM_COLOR_LIST_TEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_TEXT);
    printf CONFILE "\$FORUM_COLOR_LIST_FORUMNAME_HOVER=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_FORUMNAME_HOVER);
    printf CONFILE "\$FORUM_COLOR_THREAD_CONTENTS_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_THREAD_CONTENTS_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_LIST_INFO=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_INFO);
    printf CONFILE "\$FORUM_COLOR_LIST_FORUMNAME=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_FORUMNAME);
    printf CONFILE "\$FORUM_COLOR_SUBJECTLINK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_SUBJECTLINK);
    printf CONFILE "\$FORUM_COLOR_SUBJECTLINK_HOVER=qq~%s~;\n",&TransformForConf($FORUM_COLOR_SUBJECTLINK_HOVER);
    printf CONFILE "\$FORUM_COLOR_LIST_TEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_TEXT);
    printf CONFILE "\$FORUM_COLOR_LIST_FORUMNAME_HOVER=qq~%s~;\n",&TransformForConf($FORUM_COLOR_LIST_FORUMNAME_HOVER);
    printf CONFILE "\$FORUM_COLOR_THREAD_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_THREAD_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_THREAD_CONTENTS_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_THREAD_CONTENTS_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_THREAD_TEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_THREAD_TEXT);
    printf CONFILE "\$FORUM_COLOR_THREAD_DATECOLOR1=qq~%s~;\n",&TransformForConf($FORUM_COLOR_THREAD_DATECOLOR1);
    printf CONFILE "\$FORUM_COLOR_THREAD_DATECOLOR2=qq~%s~;\n",&TransformForConf($FORUM_COLOR_THREAD_DATECOLOR2);
    printf CONFILE "\$FORUM_COLOR_POST_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_POST_1_INFO_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_1_INFO_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_POST_1_POST_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_1_POST_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_POST_1_TEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_1_TEXT);
    printf CONFILE "\$FORUM_COLOR_POST_2_INFO_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_2_INFO_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_POST_2_POST_CELLBACK=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_2_POST_CELLBACK);
    printf CONFILE "\$FORUM_COLOR_POST_2_TEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_2_TEXT);
    printf CONFILE "\$FORUM_COLOR_POST_INFOTEXT=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST_INFOTEXT);
    printf CONFILE "\$FORUM_COLOR_POST1=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST1);
    printf CONFILE "\$FORUM_COLOR_POST2=qq~%s~;\n",&TransformForConf($FORUM_COLOR_POST2);
    printf CONFILE "\$FORUM_COLOR_BACKGROUND=qq~%s~;\n\n",&TransformForConf($FORUM_COLOR_BACKGROUND);
    printf CONFILE "\$FORUM_COLOR_LASTPOSTER_LINK=qq~%s~;\n\n",&TransformForConf ($FORUM_COLOR_LASTPOSTER_LINK);
    printf CONFILE "\$FORUM_COLOR_LASTPOSTER_HOVER=qq~%s~;\n\n",&TransformForConf ($FORUM_COLOR_LASTPOSTER_HOVER);

    printf CONFILE "# \$FORUM_POSTS_AT_A_SCREEN will say how much posts there can be at a screen\n";
    printf CONFILE "# at a single time\n";
    printf CONFILE "\$FORUM_POSTS_AT_A_SCREEN=qq~%s~;\n\n",&TransformForConf($FORUM_POSTS_AT_A_SCREEN);

    printf CONFILE "# \$FORUM_THREADS_AT_A_SCREEN will indicate how much threads there will be\n";
    printf CONFILE "# listed at a single screen.\n";
    printf CONFILE "\$FORUM_THREADS_AT_A_SCREEN=qq~%s~;\n\n",&TransformForConf($FORUM_THREADS_AT_A_SCREEN);

    printf CONFILE "# \$FORUM_DIR indicates where the forum directory is. All forum datafiles will\n";
    printf CONFILE "# be kept there. It should include the slash.\n";
    printf CONFILE "\$FORUM_DIR=qq~%s~;\n\n",&TransformForConf($FORUM_DIR);

    printf CONFILE "# \$FORUM_DATAFILE indicates where the main forum datafile is. It should be\n";
    printf CONFILE "# a complete directory and filename pair, and unaccessible to the outside\n";
    printf CONFILE "# world.\n";
    printf CONFILE "\$FORUM_DATAFILE=qq~%s~;\n\n",&TransformForConf($FORUM_DATAFILE);

    printf CONFILE "# \$FORUM_LOCKFILE is the lockfile of the forum. It should be\n";
    printf CONFILE "# a complete directory and filename pair, and unaccessible to the outside\n";
    printf CONFILE "# world.\n";
    printf CONFILE "\$FORUM_LOCKFILE=qq~%s~;\n\n",&TransformForConf($FORUM_LOCKFILE);

    printf CONFILE "# \$ADMIN_EMAIL should be the email address of the forum administrator\n";
    printf CONFILE "\$ADMIN_EMAIL=qq~%s~;\n\n",&TransformForConf($ADMIN_EMAIL);

    printf CONFILE "# \$FORUM_TITLE is the title that will be given to every page\n";
    printf CONFILE "\$FORUM_TITLE=qq~%s~;\n\n",&TransformForConf($FORUM_TITLE);

    printf CONFILE "# \$IMAGES_URI is the URI where the images are located.\n";
    printf CONFILE "\$IMAGES_URI=qq~%s~;\n\n",&TransformForConf($IMAGES_URI);

    printf CONFILE "# \$USERDB_FILE is the file where the user database resides. This file should\n";
    printf CONFILE "# NEVER be accessable by the public.\n";
    printf CONFILE "\$USERDB_FILE=qq~%s~;\n\n",&TransformForConf($USERDB_FILE);

    printf CONFILE "# \$USERDB_LOCKFILE is the lockfile of the user database. This file should\n";
    printf CONFILE "# NEVER be accessable by the public.\n";
    printf CONFILE "\$USERDB_LOCKFILE=qq~%s~;\n\n",&TransformForConf($USERDB_LOCKFILE);

    printf CONFILE "# \$FORUM_FILE_PERMS is the permission the new forum files will get.\n";
    printf CONFILE "\$FORUM_FILE_PERMS=qq~%s~;\n\n",&TransformForConf($FORUM_FILE_PERMS);

    printf CONFILE "# \$FORUM_DIR_PERMS is the permission the new forum directories will get.\n";
    printf CONFILE "\$FORUM_DIR_PERMS=qq~%s~;\n\n",&TransformForConf($FORUM_DIR_PERMS);

    printf CONFILE "# \$FORUM_OPTION_SHOWINFO will indicate whether information about the poster\n";
    printf CONFILE "# will be printer under the username in every thread.\n";
    printf CONFILE "\$FORUM_OPTION_SHOWINFO=qq~%s~;\n\n",&TransformForConf($FORUM_OPTION_SHOWINFO);

    printf CONFILE "# \$TITLE_ADMIN will indicate the title of an administrator account\n";
    printf CONFILE "\$TITLE_ADMIN=qq~%s~;\n\n",&TransformForConf($TITLE_ADMIN);

    printf CONFILE "# \$TITLE_MEGAMOD will indicate the title of a mega moderator account\n";
    printf CONFILE "\$TITLE_MEGAMOD=qq~%s~;\n\n",&TransformForConf($TITLE_MEGAMOD);

    printf CONFILE "# \$TITLE_SUPERMOD will indicate the title of a super moderator account\n";
    printf CONFILE "\$TITLE_SUPERMOD=qq~%s~;\n\n",&TransformForConf($TITLE_SUPERMOD);

    printf CONFILE "# \$TITLE_MOD will indicate the title of a moderator account\n";
    printf CONFILE "\$TITLE_MOD=qq~%s~;\n\n",&TransformForConf($TITLE_MOD);

    printf CONFILE "# \$TITLE_MEMBER will indicate the title of a normal account\n";
    printf CONFILE "\$TITLE_MEMBER=qq~%s~;\n\n",&TransformForConf($TITLE_MEMBER);

    printf CONFILE "# \$TITLE_NOMEMBER will indicate the title of a unknown accounts\n";
    printf CONFILE "\$TITLE_NOMEMBER=qq~%s~;\n\n",&TransformForConf($TITLE_NOMEMBER);

    printf CONFILE "# \$TIMEZONE should be the server timezone.\n";
    printf CONFILE "\$TIMEZONE=qq~%s~;\n\n",&TransformForConf($TIMEZONE);

    printf CONFILE "# \$WEBSITE_URI should be the URI of the website of which this forum belongs to.\n";
    printf CONFILE "\$WEBSITE_URI=qq~%s~;\n\n",&TransformForConf($WEBSITE_URI);

    printf CONFILE "# \$WEBSITE_LINK should be the text of the link to the website\n";
    printf CONFILE "\$WEBSITE_LINK=qq~%s~;\n\n",&TransformForConf($WEBSITE_LINK);

    printf CONFILE "# \$NOF_ICONS should be the number of icons you want to include, or NO if you\n";
    printf CONFILE "# don't want any icons.\n";
    printf CONFILE "\$NOF_ICONS=qq~%s~;\n\n",&TransformForConf($NOF_ICONS);

    printf CONFILE "# \$DEFAULT_SUBJECT is the default subject name, if nothing was given\n";
    printf CONFILE "\$DEFAULT_SUBJECT=qq~%s~;\n\n",&TransformForConf($DEFAULT_SUBJECT);

    printf CONFILE "# \$FORUM_DISABLED is a flag that will entirely disable the forum when it is\n";
    printf CONFILE "# YES. It should be NO if you don't want this.\n";
    printf CONFILE "\$FORUM_DISABLED=qq~%s~;\n\n",&TransformForConf($FORUM_DISABLED);

    printf CONFILE "# \$FORUM_POLICIES are the forum policies\n";
    printf CONFILE "\$FORUM_POLICIES=qq~%s~;\n\n",&TransformForConf($FORUM_POLICIES);

    printf CONFILE "# \$ALLOW_REGISTRATION is the flag whether we should allow registration of new\n";
    printf CONFILE "# accounts. It should be YES or NO.\n";
    printf CONFILE "\$ALLOW_REGISTRATION=qq~%s~;\n\n",&TransformForConf($ALLOW_REGISTRATION);

    printf CONFILE "# \$SHOW_DESCRIPTIONS is the flag whether we should descriptions or not. It\n";
    printf CONFILE "# should be YES or NO.\n";
    printf CONFILE "\$SHOW_DESCRIPTIONS=qq~%s~;\n\n",&TransformForConf($SHOW_DESCRIPTIONS);

    printf CONFILE "# \$SHOW_CATS is the flag whether we should start with the category display or\n";
    printf CONFILE "# not. It should be YES or NO.\n";
    printf CONFILE "\$SHOW_CATS=qq~%s~;\n\n",&TransformForConf($SHOW_CATS);

    printf CONFILE "# \$CATS_DATAFILE is the file where the category database resides. This file\n";
    printf CONFILE "# should NEVER be accessable by the public.\n";
    printf CONFILE "\$CATS_DATAFILE=qq~%s~;\n\n",&TransformForConf($CATS_DATAFILE);

    printf CONFILE "# \$CATS_LOCKFILE is the lockfile of category database resides. This file should\n";
    printf CONFILE "# NEVER be accessable by the public.\n";
    printf CONFILE "\$CATS_LOCKFILE=qq~%s~;\n\n",&TransformForConf($CATS_LOCKFILE);

    printf CONFILE "# \$EMAIL_METHOD is the way we send emails. 0 means disable, 1 is\n";
    printf CONFILE "# sendmail and 2 is SMTP\n";
    printf CONFILE "\$EMAIL_METHOD=qq~%s~;\n\n",&TransformForConf($EMAIL_METHOD);

    printf CONFILE "# \$SENDMAIL_LOCATION specifies where sendmail(1) is located\n";
    printf CONFILE "\$SENDMAIL_LOCATION=qq~%s~;\n\n",&TransformForConf($SENDMAIL_LOCATION);

    printf CONFILE "# \$SMTP_BOX is the hostname or IP address of the box that does SMTP for us\n";
    printf CONFILE "\$SMTP_BOX=qq~%s~;\n\n",&TransformForConf($SMTP_BOX);

    printf CONFILE "# \$SMTP_PORT is the port \$SMTP_BOX is listening on\n";
    printf CONFILE "\$SMTP_PORT=qq~%s~;\n\n",&TransformForConf($SMTP_PORT);

    printf CONFILE "# \$START_PAGE_TEXT is the text that will be appended to the top of every forum\n";
    printf CONFILE "# page.\n";
    printf CONFILE "\$START_PAGE_TEXT=qq~%s~;\n\n",&TransformForConf($START_PAGE_TEXT);

    printf CONFILE "# \$END_PAGE_TEXT is the text that will be appended to the bottom of every forum\n";
    printf CONFILE "# page.\n";
    printf CONFILE "\$END_PAGE_TEXT=qq~%s~;\n\n",&TransformForConf($END_PAGE_TEXT);

    printf CONFILE "# \$FORUM_THREAD_TABLE_TAGS are the tags will be be added to the table tag\n";
    printf CONFILE "# in the forum thread list.\n";
    printf CONFILE "\$FORUM_THREAD_TABLE_TAGS=qq~%s~;\n\n",&TransformForConf($FORUM_THREAD_TABLE_TAGS);

    printf CONFILE "# \$FORUM_POST_TABLE_TAGS are the tags will be be added to the table tag\n";
    printf CONFILE "# in the forum post list.\n";
    printf CONFILE "\$FORUM_POST_TABLE_TAGS=qq~%s~;\n\n",&TransformForConf($FORUM_POST_TABLE_TAGS);

    printf CONFILE "# \$FORUM_LIST_TABLE_TAGS are the tags will be be added to the table tag\n";
    printf CONFILE "# in the forum list.\n";
    printf CONFILE "\$FORUM_LIST_TABLE_TAGS=qq~%s~;\n\n",&TransformForConf($FORUM_LIST_TABLE_TAGS);

    printf CONFILE "# \$SHOW_EDIT will indiciate whether an 'edited by ...' will be appended\n";
    printf CONFILE "# when it has been edited. It should be YES or NO\n";
    printf CONFILE "\$SHOW_EDIT=qq~%s~;\n\n",&TransformForConf($SHOW_EDIT);

    printf CONFILE "# \$ALTER_LOCKED will indicate whether a locked thread can be altered\n";
    printf CONFILE "# (eg deleting/editing of messages)\n";
    printf CONFILE "\$ALTER_LOCKED=qq~%s~;\n\n",&TransformForConf($ALTER_LOCKED);

    printf CONFILE "# \$RECOVER_PASSWORD will indicate whether an user can recover his\n";
    printf CONFILE "# password via email. Only works if you also have a SMTP server\n";
    printf CONFILE "\$RECOVER_PASSWORD=qq~%s~;\n\n",&TransformForConf($RECOVER_PASSWORD);

    printf CONFILE "# \$ALLOW_EDIT_DELETE will indicate whether editing/deleting/both\n";
    printf CONFILE "# of posts is allowed. 0 means no to both, 1 means yes to editing\n";
    printf CONFILE "# but no to deleting and anything else is ok to both\n";
    printf CONFILE "\$ALLOW_EDIT_DELETE=qq~%s~;\n\n",&TransformForConf($ALLOW_EDIT_DELETE);

    printf CONFILE "# \$ALLOW_LOCK_DELETE will indicate whether locking/deleting/both\n";
    printf CONFILE "# of threads is allowed. 0 means no to both, 1 means yes to editing\n";
    printf CONFILE "# but no to locking and anything else is ok to both\n";
    printf CONFILE "\$ALLOW_LOCK_DELETE=qq~%s~;\n\n",&TransformForConf($ALLOW_LOCK_DELETE);

    printf CONFILE "# \$SHOW_HOPTO indiciates where we will show the 'hop to' list. It\n";
    printf CONFILE "# should be YES or NO\n";
    printf CONFILE "\$SHOW_HOPTO=qq~%s~;\n\n",&TransformForConf($SHOW_HOPTO);

    printf CONFILE "# \$REQUIRE_VALID_EMAIL indicates whether we should email a random\n";
    printf CONFILE "# password to an user upon registering\n";
    printf CONFILE "\$REQUIRE_VALID_EMAIL=qq~%s~;\n\n",&TransformForConf($REQUIRE_VALID_EMAIL);

    printf CONFILE "# \$REQUIRE_UNIQUE_EMAIL indicates whether we should check for\n";
    printf CONFILE "# unique emails upon registering\n";
    printf CONFILE "\$REQUIRE_UNIQUE_EMAIL=qq~%s~;\n\n",&TransformForConf($REQUIRE_UNIQUE_EMAIL);

    printf CONFILE "# \$REQUIRE_LOGIN indicates whether users must login before they can use\n";
    printf CONFILE "# the forums\n";
    printf CONFILE "\$REQUIRE_LOGIN=qq~%s~;\n\n",&TransformForConf($REQUIRE_LOGIN);

    printf CONFILE "# \$SHOW_NOF_MEMBERS indicates whether we will show how much members\n";
    printf CONFILE "# there are on the site\n";
    printf CONFILE "\$SHOW_NOF_MEMBERS=qq~%s~;\n\n",&TransformForConf($SHOW_NOF_MEMBERS);

    printf CONFILE "# \$ALLOW_UNLOCK indicates whether unlocking threads is allowed. It\n";
    printf CONFILE "# can be YES or NO\n";
    printf CONFILE "\$ALLOW_UNLOCK=qq~%s~;\n\n",&TransformForConf($ALLOW_UNLOCK);

    printf CONFILE "# \$SIG_ALLOWED indicates whether signatures (text that can be appened\n";
    printf CONFILE "# to posts) is allowed. It can be YES or NO\n";
    printf CONFILE "\$SIG_ALLOWED=qq~%s~;\n\n",&TransformForConf ($SIG_ALLOWED);

    printf CONFILE "# \$SIG_SHOWDEFAULT indicates whether we will initially check the\n";
    printf CONFILE "# 'show signature' checkbox. It can be YES or NO\n";
    printf CONFILE "\$SIG_SHOWDEFAULT=qq~%s~;\n\n",&TransformForConf ($SIG_SHOWDEFAULT);

    printf CONFILE "# \$SIG_ALLOW_HTML indicates whether we will allow HTML code in\n";
    printf CONFILE "# signatures. It can be YES or NO\n";
    printf CONFILE "\$SIG_ALLOW_HTML=qq~%s~;\n\n",&TransformForConf ($SIG_ALLOW_HTML);

    printf CONFILE "# \$SIG_ALLOW_MAX indicates whether we will allow MaX code in\n";
    printf CONFILE "# signatures. It can be YES or NO\n";
    printf CONFILE "\$SIG_ALLOW_MAX=qq~%s~;\n\n",&TransformForConf ($SIG_ALLOW_MAX);

    printf CONFILE "# \$SIG_DENY_EVIL_HTML indicates whether JavaScript and the likes\n";
    printf CONFILE "# must be removed from the sig. It can be YES or NO\n";
    printf CONFILE "\$SIG_DENY_EVIL_HTML=qq~%s~;\n\n",&TransformForConf ($SIG_DENY_EVIL_HTML); 

    printf CONFILE "# \$SIG_ALLOW_IMGS indicates whether images are allowed in\n";
    printf CONFILE "# signatures. It can be YES or NO\n";
    printf CONFILE "\$SIG_ALLOW_IMGS=qq~%s~;\n\n",&TransformForConf ($SIG_ALLOW_IMGS);

    printf CONFILE "# \$CENSOR_POSTS indicates wether posts will be censored. It can be YES\n";
    printf CONFILE "# or NO\n";
    printf CONFILE "\$CENSOR_POSTS=qq~%s~;\n\n",&TransformForConf ($CENSOR_POSTS);
    printf CONFILE "# \$CENSORED_WORDS indicates the actual words that will be censored\n";
    printf CONFILE "\$CENSORED_WORDS=qq~%s~;\n\n",&TransformForConf ($CENSORED_WORDS);

    printf CONFILE "# \$BANNED_EMAIL are the email addresses that are banned\n";
    printf CONFILE "\$BANNED_EMAIL=qq~%s~;\n\n",&TransformForConf ($BANNED_EMAIL);

    printf CONFILE "# \$BANNED_IP are the IP addresses that are banned\n";
    printf CONFILE "\$BANNED_IP=qq~%s~;\n\n",&TransformForConf ($BANNED_IP);

    printf CONFILE "# \$COPPA_ENABLED indicates whether COPPA compliance is enabled.\n";
    printf CONFILE "# It can be YES or NO\n";
    printf CONFILE "\$COPPA_ENABLED=qq~%s~;\n\n",&TransformForConf ($COPPA_ENABLED);

    printf CONFILE "# \$COPPA_KID_INSTR are the instructions for the kid\n";
    printf CONFILE "\$COPPA_KID_INSTR=qq~%s~;\n\n", &TransformForConf (&HTMLize ($COPPA_KID_INSTR));

    printf CONFILE "# \$COPPA_PARENT_INSTR are the instructions for the kid\n";
    printf CONFILE "\$COPPA_PARENT_INSTR=qq~%s~;\n\n", &TransformForConf (&HTMLize ($COPPA_PARENT_INSTR));

    printf CONFILE "# \$FORCE_LOGIN will force the user to login if the login\n";
    printf CONFILE "# information supplied is correct. It can be YES or NO\n";
    printf CONFILE "\$FORCE_LOGIN=qq~%s~;\n\n",&TransformForConf ($FORCE_LOGIN);

    printf CONFILE "# \$IP_LOG_DISPLAY indicates whether IP addresses will be\n";
    printf CONFILE "# logged, and who can view them if they are. 0 means that\n";
    printf CONFILE "# IP's are not logged and cannot be displayed, 1 means\n";
    printf CONFILE "# that they are logged but can only be viewed by admins,\n";
    printf CONFILE "# 2 means they are logged and viewable by admins and mods\n";
    printf CONFILE "# 3 means that they are logged and anyone can view them\n";
    printf CONFILE "\$IP_LOG_DISPLAY=qq~%s~;\n\n",&TransformForConf ($IP_LOG_DISPLAY);

    printf CONFILE "# \$EXTRA_STYLE is extra style sheet information. It will be added\n";
    printf CONFILE "# to the <style type=\"text/css\"> HTML tag\n";
    printf CONFILE "\$EXTRA_STYLE=qq~%s~;\n\n",&TransformForConf ($EXTRA_STYLE);

    printf CONFILE "# \$REQUIRE_ICON will indicate whether the user is required to\n";
    printf CONFILE "# select an icon for a new thread. It can be YES or NO\n";
    printf CONFILE "\$REQUIRE_ICON=qq~%s~;\n\n",&TransformForConf ($REQUIRE_ICON);

    printf CONFILE "# \$FORUM_FONT is the font that will be used in the forums\n";
    printf CONFILE "\$FORUM_FONT=qq~%s~;\n\n",&TransformForConf ($FORUM_FONT);

    printf CONFILE "# \$SMILIES are the forum smilies\n";
    printf CONFILE "\$SMILIES=qq~%s~;\n\n", &TransformForConf ($SMILIES);

    printf CONFILE "# \$SHOW_LOCKER will indicate whether the forum will show who\n";
    printf CONFILE "# locked a thread or not. It can be YES or NO.\n";
    printf CONFILE "\$SHOW_LOCKER=qq~%s~;\n\n", &TransformForConf ($SHOW_LOCKER);

    printf CONFILE "# \$EXTRA_PROFILE_FIELDS are the names of the extra profile fields\n";
    printf CONFILE "# They are separated by |'s\n";
    printf CONFILE "\$EXTRA_PROFILE_FIELDS=qq~%s~;\n\n", &TransformForConf ($EXTRA_PROFILE_FIELDS);

    printf CONFILE "# \$EXTRA_PROFILE_TYPES are the types of the extra profile fields\n";
    printf CONFILE "# They are separated by |'s\n";
    printf CONFILE "\$EXTRA_PROFILE_TYPES=qq~%s~;\n\n", &TransformForConf ($EXTRA_PROFILE_TYPES);
   
    printf CONFILE "# \$EXTRA_PROFILE_HIDDEN are the hidden flags of the extra profile fields\n";
    printf CONFILE "# They are separated by |'s, and can be YES or NO\n";
    printf CONFILE "\$EXTRA_PROFILE_HIDDEN=qq~%s~;\n\n", &TransformForConf ($EXTRA_PROFILE_HIDDEN);

    printf CONFILE "# \$EXTRA_PROFILE_PERMS are the permissions of the extra profile fields\n";
    printf CONFILE "# They are separated by |'s\n";
    printf CONFILE "\$EXTRA_PROFILE_PERMS=qq~%s~;\n\n", &TransformForConf ($EXTRA_PROFILE_PERMS);

    printf CONFILE "# \$GROUPDB_FILE is the file where all group information will be stored. This\n";
    printf CONFILE "# file should NEVER be accessible by the public.\n";
    printf CONFILE "\$GROUPDB_FILE=qq~%s~;\n\n", &TransformForConf ($GROUPDB_FILE);

    printf CONFILE "# \$GROUPDB_FILE is the lockfile of the groups. This file should NEVER be\n";
    printf CONFILE "# accessible by the public.\n";
    printf CONFILE "\$GROUPDB_LOCKFILE=qq~%s~;\n\n", &TransformForConf ($GROUPDB_LOCKFILE);

    printf CONFILE "# \$SHOW_LAST_POSTER indicates whether the forum shows who last posted to\n";
    printf CONFILE "# each thread\n";
    printf CONFILE "\$SHOW_LAST_POSTER=qq~%s~;\n\n", &TransformForConf ($SHOW_LAST_POSTER);

    printf CONFILE "# \$HEADERFOOTER_FILE will indicate whether the header and footer fields\n";
    printf CONFILE "# are filenames or not. If it is YES, the header and footer fields will\n";
    printf CONFILE "# be used as the filename of the header and footer. If it is NO, they\n";
    printf CONFILE "# will just be printed\n";
    printf CONFILE "\$HEADERFOOTER_FILE=qq~%s~;\n\n",&TransformForConf($HEADERFOOTER_FILE);

    printf CONFILE "# \$USE_COOKIES will indicate whether the forum will use cookies\n";
    printf CONFILE "# for identification rather than the classic id= string\n";
    printf CONFILE "\$USE_COOKIES=qq~%s~;\n\n",&TransformForConf($USE_COOKIES);

    printf CONFILE "# \$REVIEW_POST indicates whether the reply page will show the\n";
    printf CONFILE "# the replies of the thread replying to\n";
    printf CONFILE "\$REVIEW_POST=qq~%s~;\n\n",&TransformForConf($REVIEW_POST);

    printf CONFILE "1;";

    close(CONFILE);

    # copy this file to the options file
    &CopyFile($TMPCONF_FILE,$CONF_FILE);
}

#
# KillLockFiles($dir)
#
# This will kill the lock files in directory $dir.
#
sub
KillLockFiles() {
    # get the arguments
    my ($dir)=@_;

    # open the post directory
    opendir(DIR,$dir)||&cp_error($ERROR_FILEOPENERR . " ($dir)");

    # get the contents
    my @files = readdir(DIR);

    # close the directory
    closedir(DIR);

    foreach $file (@files) {
        # does the file end with .LOCK?
        my $tmp = $file;
        $tmp=~ s/\.LOCK$//gi;
        if ($file ne $tmp) {
            # yes, it does. kill it
    	    unlink ($dir . "/" . $file)||printf "<b>Warning</b>: could not delete $dir/$file<br>";
	}
    }
}

#
# RebuildMods ($mods,$the_flag)
#
# This will rebuild mod list $mods. $the_flag is the flag the user will get.
#
sub
RebuildMods() {
    # get the arguments
    my ($mods,$the_flag) = @_;

    # make sure we include groups too
    my @the_mods = &BuildUserGroupList (split (/,/, $mods));

    # now, browse them one by one and boost the status up
    foreach $mod (@the_mods) {
	# boost the user up to mod
        &SetAccountFlags ($mod, 1, $the_flag);
    }
}

#
# DoOptions()
#
# This will actually apply the options
#
sub
DoOptions() {
    # we're about to modify stuff!
    &AboutToModify();

    # need to kill all lockfiles?
    if ($field{"kill_lockfiles"} ne "") {
	# yup. first, kill them in the main dir
	&KillLockFiles ($FORUM_DIR);

	# now, get a forum list
	my @forums = &GetForums();

	# browse all forums
	foreach $the_forum (@forums) {
	    # get the forum name
	    my ($name) = split (/:/, $the_forum);

	    # kill all lockfiles in the forum directory
	    &KillLockFiles ($FORUM_DIR . "/" . $name);
	}
    }

    # need to rebuild the mod list?
    if ($field{"rebuild_mods"} ne "") {
	# yes. get a forum and category list
	my @forums = &GetForums();
	my @cats = &GetCats();

	# browse all forums
	foreach $the_forum (@forums) {
	    # grab the forum mods
	    my ($tmp1,$tmp2,$mods) = split (/:/, $the_forum);

	    # rebuild the mods
	    &RebuildMods ($mods,$FLAG_MOD);
	}

	# rebuild the supermod list too
	foreach $the_cat (@cats) {
	    # grab the forum supermods
	    my ($tmp1,$tmp2,$supermods)=split(/:/,$the_cat);

	    # rebuild the supermods
	    &RebuildMods ($supermods,$FLAG_SUPERMOD);
	}
    }

    # copy the new options
    $EMAIL_METHOD=$field{"email_method"};
    $SENDMAIL_LOCATION=$field{"sendmail_loc"};
    $SMTP_BOX=$field{"smtp_box"};
    $SMTP_PORT=$field{"smtp_port"};
    $ADMIN_EMAIL=$field{"email"};
    $WEBSITE_URI=$field{"websiteuri"};
    $WEBSITE_LINK=$field{"websitelink"};
    $TIMEZONE=$field{"timezone"};
    $FORUM_TITLE=$field{"forumtitle"};
    $IMAGES_URI=$field{"images_uri"};
    $FORUM_POLICIES=&HTMLize($field{"policies"});
    $START_PAGE_TEXT=$field{"start_text"};
    $START_PAGE_TEXT=&HTMLize($START_PAGE_TEXT);
    $END_PAGE_TEXT=$field{"end_text"};
    $END_PAGE_TEXT=&HTMLize($END_PAGE_TEXT);
    $HEADERFOOTER_FILE=$field{"f_headerfooterfile"};
    $EXTRA_STYLE=$field{"extra_style"};
    $EXTRA_STYLE=~ s/\r//g;
    $ALLOW_REGISTRATION = $field{"f_allowreg"};
    $FORUM_DISABLED=$field{"f_disabled"};
    $FORCE_LOGIN=$field{"f_forcelogin"};
    $RECOVER_PASSWORD = $field{"f_recoverpassword"};
    $SHOW_CATS=$field{"f_showcats"};
    $SHOW_HOPTO = $field{"f_showhopto"};
    $REQUIRE_VALID_EMAIL = $field{"f_requirevalidemail"};
    $REQUIRE_UNIQUE_EMAIL = $field{"f_requireuniqueemail"};
    $REQUIRE_LOGIN = $field{"f_requirelogin"};
    $SHOW_NOF_MEMBERS = $field{"f_shownofmembers"};
    $SHOW_LOCKER = $field{"f_showlock"};
    $SHOW_LAST_POSTER=$field{"f_showlastposter"};
    $CENSOR_POSTS=$field{"f_censor_posts"};
    $CENSORED_WORDS=$field{"censored_words"};
    $CENSORED_WORDS=~ s/\n/ /g;
    $CENSORED_WORDS=~ s/\r//g;
    $CENSORED_WORDS=~ s/,/ /g;
    $CENSORED_WORDS=~ s/;/ /g;
    $BANNED_EMAIL=$field{"banned_email"};
    $BANNED_EMAIL=~ s/\n/ /g;
    $BANNED_EMAIL=~ s/\r//g;
    $BANNED_EMAIL=~ s/,/ /g;
    $BANNED_EMAIL=~ s/;/ /g;
    $BANNED_EMAIl=~ s/\@/\\\@/g;
    $BANNED_IP=$field{"banned_ip"};
    $BANNED_IP=~ s/\n/ /g;
    $BANNED_IP=~ s/\r//g;
    $BANNED_IP=~ s/,/ /g;
    $BANNED_IP=~ s/;/ /g;
    $BANNED_IP=~ s/\@/\\\@/g;
    $SIG_ALLOWED = $field{"f_allow_sigs"};
    $SIG_SHOWDEFAULT = $field{"sig_checkdefault"};
    $SIG_ALLOW_HTML = $field{"sig_allow_html"};
    $SIG_ALLOW_MAX = $field{"sig_allow_max"};
    $SIG_ALLOW_IMGS = $field{"sig_allow_imgs"};
    $FORUM_POSTS_AT_A_SCREEN=$field{"postsatascreen"};
    $FORUM_THREADS_AT_A_SCREEN=$field{"threadsatascreen"};
    $TITLE_ADMIN=$field{"admin_name"};
    $TITLE_MOD=$field{"mod_name"};
    $TITLE_MEGAMOD=$field{"megamod_name"};
    $TITLE_SUPERMOD=$field{"supermod_name"};
    $TITLE_MEMBER=$field{"member_name"};
    $TITLE_NOMEMBER=$field{"nomember_name"};
    if ($field{"show_icons"} ne "NO") {
	$NOF_ICONS=$field{"noficons"};
    } else {
	$NOF_ICONS="NO";
    }
    $REQUIRE_ICON=$field{"f_require_icon"};
    $FORUM_OPTION_SHOWINFO=$field{"f_showinfo"};
    $SHOW_EDIT=$field{"f_showedit"};
    $ALTER_LOCKED=$field{"f_alterlocked"};
    $ALLOW_EDIT_DELETE=$field{"edit_delete"};
    $ALLOW_LOCK_DELETE=$field{"lock_delete"};
    $ALLOW_UNLOCK=$field{"f_allowunlock"};
    $IP_LOG_DISPLAY=$field{"ip_log_display"};
    $COPPA_ENABLED=$field{"f_coppa_enabled"};
    $COPPA_KID_INSTR=$field{"coppa_kid_instr"};
    $COPPA_PARENT_INSTR=$field{"coppa_parent_instr"};
    $USE_COOKIES=$field{"f_usecookies"};
    $REVIEW_POST=$field{"f_reviewpost"};

    # apply the options
    &WriteConfig();

    # show the page
    &begin_page();

    # show the 'wohoo' message
    printf "Options successfully applied.<br>";

    # end the page
    &end_page();
}

#
# Styles()
#
# The user wants to edit the forum looks! Oh well...
#
sub
Styles() {
    # build the page
    &begin_page($FORUM_COLOR_BACKGROUND,$FORUM_COLOR_TEXT,"forumh.jpg");

    # show the forum list colors
    printf "<b>Forum list colors</b><br>";

    printf "<center><font size=\"5\" color=\"$FORUM_COLOR_TEXT\">(15) $FORUM_TITLE</font></center><p>";

    # do the table
    printf "<table width=\"95%\" $FORUM_LIST_TABLE_TAGS>";

    # Section (50%)  | Replies (10%) | Newest post Date (20%) | Moderator (20%)
    printf "<tr bgcolor=\"%s\"><td width=\"40%\"><font face=\"$FORUM_FONT\" color=\"%s\">Section (1)</font><br><font face=\"$FORUM_FONT\" size=2 color=\"%s\"></font></td><td width=\"10%\"><font face=\"$FORUM_FONT\" color=\"%s\">Posts (1)</font></td><td width=\"10%\"><font face=\"$FORUM_FONT\" color=\"%s\">Threads (1)</font></td><td width=\"20%\"><font face=\"$FORUM_FONT\" color=\"%s\">Newest post date (1)</font></td><td width=\"20%\"><font face=\"$FORUM_FONT\" color=\"%s\">Moderator (1)</font></td></tr>",$FORUM_COLOR_LIST_CELLBACK,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT,,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT;

    my $date = &GetTimeDate();
    my ($datea,$dateb)=split(/\|/,$date);
    $date=~ tr/|/ /;
    my ($username)=split(/:/,$field{"id"});

    # NEW ROW!!
    printf "<tr bgcolor=\"%s\"><td width=\"20%\"><font color=\"%s\">",$FORUM_COLOR_LIST_CONTENTS_CELLBACK,$FORUM_COLOR_LIST_FORUMNAME;
    printf "<a href=\"#\" class=\"forumlink\">Some forum name (2)</a>";
    printf "<br><font size=2 face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_LIST_INFO\">Description</font></td>";
    printf "<td><font color=\"%s\" face=\"$FORUM_FONT\">16 (3)</font></td>",$FORUM_COLOR_LIST_INFO;
    printf "<td><font color=\"%s\" face=\"$FORUM_FONT\">14 (3)</font></td>",$FORUM_COLOR_LIST_INFO;
    printf "<td align=\"center\"><font color=\"%s\" face=\"$FORUM_FONT\">%s (3)</font>",$FORUM_COLOR_LIST_INFO, $date;
    printf "<br><font size=1 color=\"%s\" face=\"$FORUM_FONT\">by <a href=\"#\" class=\"lastpost\">%s (4)</a></font>", $FORUM_COLOR_LIST_INFO,$username;
    printf "</td>";
    printf "<td><font color=\"%s\" face=\"$FORUM_FONT\">%s (3)</td></tr>",$FORUM_COLOR_LIST_INFO, $username;
    printf "</table>";

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"dostyles\">";

    printf "<table width=500>";
    printf "<tr><td>(*) Extra HTML tags to add to &lt;TABLE&gt; tag</td><td></td><td><input type=\"text\" name=\"list_tags\" value=\"%s\"></td></tr>",$FORUM_LIST_TABLE_TAGS;
    printf "<tr><td>Background color of all pages</td><td></td><td><input type=\"text\" name=\"page_back\" value=\"%s\"></td></tr>",$FORUM_COLOR_BACKGROUND;
    printf "<tr><td>Font name that will be used in all pages</td><td></td><td><input type=\"text\" name=\"page_font\" value=\"%s\"></td></tr>",$FORUM_FONT;
    printf "<tr><td>(15) Default text color, also used for reply text etc.</td><td></td><td><input type=\"text\" name=\"page_text\" value=\"%s\"></td></tr>",$FORUM_COLOR_TEXT;
    printf "<tr><td>(1) Information cells</td><td>Text color</td><td><input type=\"text\" name=\"c1_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LIST_TEXT;
    printf "<tr><td></td><td>Background color</td><td><input type=\"text\" name=\"c1_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LIST_CELLBACK;

    printf "<tr><td>(2) Forum link</td><td>Link color</td><td><input type=\"text\" name=\"c2_linkcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LIST_FORUMNAME;
    printf "<tr><td></td><td>Link hover color</td><td><input type=\"text\" name=\"c2_hovercol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LIST_FORUMNAME_HOVER;

    printf "<tr><td>(3) Information cells</td><td>Text color</td><td><input type=\"text\" name=\"c3_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LIST_INFO;
    printf "<tr><td></td><td>Background color</td><td><input type=\"text\" name=\"c3_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LIST_CONTENTS_CELLBACK;
    printf "<tr><td>(4) Last poster link</td><td>Link color</td><td><input type=\"text\" name=\"c4_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LASTPOSTER_LINK;
    printf "<tr><td></td><td>Link hover color</td><td><input type=\"text\" name=\"c4_hovercol\" value=\"%s\"></td></tr>",$FORUM_COLOR_LASTPOSTER_HOVER;
    printf "</table>";

    # show the forum thread list colors
    printf "<p><b>Forum thread list colors</b><br>";

    printf "<table $FORUM_THREAD_TABLE_TAGS>";

    # do the first column that says
    # Subject (79%)  | Replies (0%) | Newest post Date (21%)
    printf "<tr bgcolor=\"%s\"><td width=\"69%%\"><font face=\"%s\" size=2 color=\"$FORUM_COLOR_THREAD_TEXT\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Subject (5)</font></td><td bgcolor=\"%s\" width=\"10%%\"><font face=\"%s\" color=\"%s\" size=2>Author (5)</td><td bgcolor=\"%s\" width=\"0%%\"><font face=\"%s\" size=\"2\" color=\"$FORUM_COLOR_THREAD_TEXT\">Replies (5)</font></td><td bgcolor=\"%s\" width=\"21%\" align=\"center\"><font face=\"$FORUM_FONT\" size=\"2\" color=\"$FORUM_COLOR_THREAD_TEXT\">Newest Post (5)</font></td></tr></font>",$FORUM_COLOR_THREAD_CELLBACK,$FORUM_FONT,$FORUM_COLOR_THREAD_CELLBACK,$FORUM_FONT,$FORUM_COLOR_THREAD_TEXT,$FORUM_COLOR_THREAD_CELLBACK,$FORUM_FONT,$FORUM_COLOR_THREAD_CELLBACK;

    printf "<tr bgcolor=\"%s\"><td width=\"21%\">&nbsp;&nbsp;<font color=\"%s\">",$FORUM_COLOR_THREAD_CONTENTS_CELLBACK,$FORUM_COLOR_SUBJECT;
    printf "<img src=\"$IMAGES_URI/icon1.gif\" border=0>&nbsp;";
    printf "<a href=\"#\" class=\"subjectlink\">Subject Name (6)</a></td>";
    printf "<td><a class=\"memberlink\" href=\"#\">%s (7)</a></td>", $username;
    printf "<td bgcolor=\"%s\"><font color=\"%s\">17 (6)</font></td>",$color2, $FORUM_COLOR_SUBJECTLINK;
    printf "<td align=\"center\" bgcolor=\"%s\"><font size=2 color=\"$FORUM_COLOR_THREAD_DATECOLOR1\" face=\"$FORUM_FONT\">%s (8)</font> <font size=1 color=\"$FORUM_COLOR_THREAD_DATECOLOR2\">%s (9)</font>",$color2,$datea,$dateb;

    printf "</table><br>";

    printf "<table width=500>";
    printf "<tr><td>(*) Extra HTML tags to add to &lt;TABLE&gt; tag</td><td></td><td><input type=\"text\" name=\"thread_tags\" value=\"%s\"></td></tr>",$FORUM_THREAD_TABLE_TAGS;

    printf "<tr><td>(5) Information cells</td><td>Text color</td><td><input type=\"text\" name=\"c5_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_THREAD_TEXT;
    printf "<tr><td></td><td>Background color</td><td><input type=\"text\" name=\"c5_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_THREAD_CELLBACK;

    printf "<tr><td>(6) Thread link and replies</td><td>Link color</td><td><input type=\"text\" name=\"c6_linkcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_SUBJECTLINK;
    printf "<tr><td></td><td>Link hover color</td><td><input type=\"text\" name=\"c6_hovercol\" value=\"%s\"></td></tr>",$FORUM_COLOR_SUBJECTLINK_HOVER;

    printf "<tr><td>(7) Member link</td><td>Link color</td><td><input type=\"text\" name=\"c7_linkcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_MEMBERLINK;

    printf "<tr><td></td><td>Link hover color</td><td><input type=\"text\" name=\"c7_hovercol\" value=\"%s\"></td></tr>",$FORUM_COLOR_MEMBERLINK_HOVER;

    printf "<tr><td>(8) Date color #1</td><td>Color</td><td><input type=\"text\" name=\"c8_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_THREAD_DATECOLOR1;
    printf "<tr><td></td><td>Background color (entire row)</td><td><input type=\"text\" name=\"c8_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_THREAD_CONTENTS_CELLBACK;

    printf "<tr><td>(9) Date color #2</td><td>Color</td><td><input type=\"text\" name=\"c9_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_THREAD_DATECOLOR2;
    printf "</table>";

    # show the post colors
    printf "<p><b>Forum post colors</b><br>";

    # do the table
    printf "<table width=\"100%\" $FORUM_POST_TABLE_TAGS>";

    # create the author (18%) | post fields (82%)
    printf "<tr><td width=\"18%\" bgcolor=\"%s\"><FONT COLOR=\"%s\" face=\"$FORUM_FONT\">Author (10)</FONT></td><td width=\"82%\" bgcolor=\"%s\"><FONT COLOR=\"%s\" face=\"$FORUM_FONT\">Post (10)</FONT></td></tr>",$FORUM_COLOR_POST_CELLBACK,$FORUM_COLOR_POST_INFOTEXT,$FORUM_COLOR_POST_CELLBACK,$FORUM_COLOR_POST_INFOTEXT;

    printf "<tr valign=\"top\"><td width=\"18%\" bgcolor=\"%s\"><a href=\"#\" class=\"memberlink\">%s (11)</a><br>",$FORUM_COLOR_POST_1_INFO_CELLBACK,&TransformForBrowser ($username),$username;
    printf "</td><td width=\"82%\" bgcolor=\"%s\"><font face=\"$FORUM_FONT\" color=\"%s\">Post #1 contents (12)",$FORUM_COLOR_POST_1_POST_CELLBACK,$FORUM_COLOR_POST_1_TEXT;
    printf "</td></tr>";

    printf "<tr valign=\"top\"><td width=\"18%\" bgcolor=\"%s\"><a href=\"#\" class=\"memberlink\">%s (13)</a><br>",$FORUM_COLOR_POST_2_INFO_CELLBACK,&TransformForBrowser ($username),$username;
    printf "</td><td width=\"82%\" bgcolor=\"%s\"><font face=\"$FORUM_FONT\" color=\"%s\">Post #2 contents (14)",$FORUM_COLOR_POST_2_POST_CELLBACK,$FORUM_COLOR_POST_2_TEXT;
    printf "</td></tr>";
    printf "</table><p>";

    printf "<table width=500>";
    printf "<tr><td>(*) Extra HTML tags to add to &lt;TABLE&gt; tag</td><td></td><td><input type=\"text\" name=\"post_tags\" value=\"%s\"></td></tr>",$FORUM_POST_TABLE_TAGS;

    printf "<tr><td>(10) Information cells</td><td>Text color</td><td><input type=\"text\" name=\"c10_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST_INFOTEXT;
    printf "<tr><td></td><td>Background color</td><td><input type=\"text\" name=\"c10_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST_CELLBACK;

    printf "<tr><td>(11) Member link</td><td>Link color</td><td>See Member Link (7)</td></tr>";
    printf "<tr><td></td><td>Link hover color</td><td>See Member Link (7)</td></tr>";
    printf "<tr><td></td><td>Background cell color</td><td><input type=\"text\" name=\"c11_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST_1_INFO_CELLBACK;

    printf "<tr><td>(12) Contents</td><td>Text color</td><td><input type=\"text\" name=\"c12_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST1;
    printf "<tr><td></td><td>Background cell color</td><td><input type=\"text\" name=\"c12_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST_1_POST_CELLBACK;

    printf "<tr><td>(13) Member link</td><td>Link color</td><td>See Member Link (7)</td></tr>";
    printf "<tr><td></td><td>Link hover color</td><td>See Member Link (7)</td></tr>";
    printf "<tr><td></td><td>Background cell color</td><td><input type=\"text\" name=\"c13_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST_2_INFO_CELLBACK;

    printf "<tr><td>(14) Contents</td><td>Text color</td><td><input type=\"text\" name=\"c14_textcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST2;
    printf "<tr><td></td><td>Background cell color</td><td><input type=\"text\" name=\"c14_backcol\" value=\"%s\"></td></tr>",$FORUM_COLOR_POST_2_POST_CELLBACK;

    printf "</table>";

    # add the submit button
    printf "<input type=\"submit\" value=\"OK\">";

    # add the preview button
    printf "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"preview\" value=\"Preview\">";

    printf "</form>";

    # end the page
    &end_page();
}

#
# GetFormColors()
#
# This will get the colors from the forum page.
#
sub
GetFormColors() {
    # background and text color
    $FORUM_COLOR_BACKGROUND=$field{"page_back"};
    $FORUM_COLOR_TEXT=$field{"page_text"};
    $FORUM_FONT=$field{"page_font"};

    # forum list colors
    $FORUM_COLOR_LIST_TEXT=$field{"c1_textcol"};
    $FORUM_COLOR_LIST_CELLBACK=$field{"c1_backcol"};
    $FORUM_COLOR_LIST_FORUMNAME=$field{"c2_linkcol"};
    $FORUM_COLOR_LIST_FORUMNAME_HOVER=$field{"c2_hovercol"};
    $FORUM_COLOR_LIST_INFO=$field{"c3_textcol"};
    $FORUM_COLOR_LIST_CONTENTS_CELLBACK=$field{"c3_backcol"};
    $FORUM_COLOR_LASTPOSTER_LINK=$field{"c4_textcol"};
    $FORUM_COLOR_LASTPOSTER_HOVER=$field{"c4_hovercol"};

    # forum thread list colors
    $FORUM_COLOR_THREAD_TEXT=$field{"c5_textcol"};
    $FORUM_COLOR_THREAD_CELLBACK=$field{"c5_backcol"};
    $FORUM_COLOR_SUBJECTLINK=$field{"c6_linkcol"};
    $FORUM_COLOR_SUBJECTLINK_HOVER=$field{"c6_hovercol"};
    $FORUM_COLOR_MEMBERLINK=$field{"c7_linkcol"};
    $FORUM_COLOR_MEMBERLINK_HOVER=$field{"c7_hovercol"};
    $FORUM_COLOR_THREAD_DATECOLOR1=$field{"c8_textcol"};
    $FORUM_COLOR_THREAD_CONTENTS_CELLBACK=$field{"c8_backcol"};
    $FORUM_COLOR_THREAD_DATECOLOR2=$field{"c9_textcol"};

    # forum posts colors
    $FORUM_COLOR_POST_INFOTEXT=$field{"c10_textcol"};
    $FORUM_COLOR_POST_CELLBACK=$field{"c10_backcol"};
    $FORUM_COLOR_POST_1_INFO_CELLBACK=$field{"c11_backcol"};
    $FORUM_COLOR_POST1=$field{"c12_textcol"};
    $FORUM_COLOR_POST_1_POST_CELLBACK=$field{"c12_backcol"};
    $FORUM_COLOR_POST_2_INFO_CELLBACK=$field{"c13_backcol"};
    $FORUM_COLOR_POST2=$field{"c14_textcol"};
    $FORUM_COLOR_POST_2_POST_CELLBACK=$field{"c14_backcol"};

    # styles
    $FORUM_THREAD_TABLE_TAGS=$field{"thread_tags"};
    $FORUM_POST_TABLE_TAGS=$field{"post_tags"};
    $FORUM_LIST_TABLE_TAGS=$field{"list_tags"};
}

#
# PreviewColors()
#
# This will preview the selected colors.
#
sub
PreviewColors() {
    # get the color values
    &GetFormColors();

    # chain to the styles page
    &Styles();
}

#
# DoStyles()
#
# This will actually edit the forum styles
#
sub
DoStyles() {
    # we're about to modify stuff!
    &AboutToModify();

    # get the new color values
    &GetFormColors();

    # save them
    &WriteConfig();

    # show the page
    &begin_page();

    # show the 'wohoo' message
    printf "Styles successfully applied<br>";

    # end the page
    &end_page();
}

#
# Cats()
#
# This will show the category configuration.
#
sub
Cats() {
    # show the page
    &begin_page();

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"docats\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # set up the table
    # Order (5%) | Category Name (50%) | Super Moderator (45%)
    printf "<table border=1 width=\"100%\"><tr>\n";
    printf "<td width=\"5%\"><b>Order</b></td>\n";
    printf "<td width=\"50%\"><b>Category name</b></td>\n";
    printf "<td width=\"45%\"><b>Super Moderators</b></td></tr>\n";

    # open the categories file
    open(CATSFILE,$CATS_DATAFILE)||&cp_error($ERROR_FILEOPENERR . " ($CATS_DATAFILE)");

    # read it line by line
    my $x="1";
    while (<CATSFILE>) {
	# get a line
	my $line = $_;

	# split it
	my ($name,$no,$supermods)=split(/:/,$line);

        printf "<input type=\"hidden\" name=\"name$no\" value=\"$name\">";

	printf "<tr><td><input type=\"text\" name=\"order$no\" value=\"%s\" size=3 maxlength=6></td><td><a href=\"cp.cgi?id=%s&action=editcat&destcat=%s\">%s</a></td><td>%s</td></tr>",$x,$field{"id"},&FixSpecialChars (&TransformForBrowser($name)),&RestoreSpecialChars ($name),&FormatUserGroup (split (/,/, $supermods));
	$x++;
    }

    # close the categories file
    close(CATSFILE);

    # end the table
    printf "</table><p>";

    # show the apply button
    printf "<input type=\"submit\" value=\"Apply modifications\">";
    printf "</form>";

    # add the 'add category' button
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"addcat\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"submit\" value=\"Add category\"></form>";

    # end the page
    &end_page();
}

#
# EditCat()
#
# This will show the category editing page.
#
sub
EditCat() {
    # set up the page
    &begin_page();

    # get the category info line
    my $cat_line=&GetCatInfo($field{"destcat"});

    # did that work?
    if ($cat_line eq "") {
	# nope. show error
	my $name = $field{"destcat"};
	&error("Category <b>$name</b> does not exists");
    }

    # split the line
    my ($name,$cat_no,$supermods,$desc,$header,$footer)=split(/:/,$cat_line);

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doeditcat\">";
    printf "<input type=\"hidden\" name=\"destcat\" value=\"%s\">",$field{"destcat"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # show the info
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"40%\">Category Name</td><td><input type=\"text\" name=\"catname\" value=\"%s\"></td></tr>",&RestoreSpecialChars ($field{"destcat"});
    printf "<tr><td>Super Moderator<br><font size=1>Specify all accounts here you would like to grant moderator access to all forums within this category. Add a @ before any group</font></td><td><input type=\"text\" name=\"supermods\" value=\"%s\"></td></tr>",$supermods;
    printf "<tr valign=\"top\"><td>Description</td><td><textarea rows=5 cols=30 name=\"desc\">%s</textarea></tr>",&RestoreSpecialChars (&UnHTMLize ($desc));

    printf "<tr valign=\"top\"><td>Header (overrides general header if not blank. Copy the general header by inserting <code>|header|</code>)</td><td><textarea rows=5 cols=30 name=\"header\">%s</textarea></tr>",&RestoreSpecialChars (&UnHTMLize($header));
    printf "<tr valign=\"top\"><td>Footer (overrides general footer if not blank. Copy the general footer by inserting <code>|footer|</code>)</td><td><textarea rows=5 cols=30 name=\"footer\">%s</textarea></tr>",&RestoreSpecialChars (&UnHTMLize($footer));
    printf "</table><p>";

    # add the OK button and close the form
    printf "<p><input type=\"submit\" value=\"OK\"></form>";

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dodeletecat\">";
    printf "<input type=\"hidden\" name=\"destcat\" value=\"%s\">",$field{"destcat"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # add the 'delete category' button and close the form
    printf "<input type=\"submit\" value=\"Delete category\"></form>";

    # end the page
    &end_page();
}

#
# DoEditCat()
#
# This will really alter the specific category.
#
sub
DoEditCat() {
    # we're about to modify stuff!
    &AboutToModify();

    # zap all spaces
    $field{"catname"} = &ZapTrailingSpaces ($field{"catname"});
    $field{"supermods"} = &ZapTrailingSpaces ($field{"supermods"});
    $field{"desc"} = &ZapTrailingSpaces ($field{"desc"});
    $field{"header"} = &ZapTrailingSpaces ($field{"header"});
    $field{"footer"} = &ZapTrailingSpaces ($field{"footer"});

    # destroy all whitespace around commas
    $field{"supermods"}=~ s/((\s)*)\,/\,/g;
    $field{"supermods"}=~ s/\,((\s)*)/\,/g;

    # fix the cat name
    $field{"destcat"} = &FixSpecialChars ($field{"destcat"});

    # grab the old line
    my $oldline = &GetCatInfo($field{"destcat"});
    my ($oldname,$catno,$oldsupermods,$desc)=split(/:/,$oldline);

    # does it exists?
    if ($oldline eq "") {
	# no. complain
        my $name = &RestoreSpecialChars ($field{"destcat"});
	&cp_error("Category <b>$name</b> not found");
    }

    # build the description, header and footer lines
    $field{"catname"} = &FixSpecialChars ($field{"catname"});
    $field{"desc"} = &HTMLize (&FixSpecialChars ($field{"desc"}));
    $field{"header"} = &HTMLize (&FixSpecialChars ($field{"header"}));
    $field{"footer"} = &HTMLize (&FixSpecialChars ($field{"footer"}));

    # check for internal chars
    if (&HasInternalChars ($field{"catname"}) ne "0") { &cp_error("The category name field contains illegal chars"); }
    if (&HasInternalChars ($field{"supermods"}) ne "0") { &cp_error("The super moderators field contains illegal chars"); }
    if (&HasInternalChars ($field{"desc"}) ne "0") { &cp_error("The description field contains illegal chars"); }
    if (&HasInternalChars ($field{"header"}) ne "0") { &cp_error("The header field contains illegal chars"); }
    if (&HasInternalChars ($field{"footer"}) ne "0") { &cp_error("The footer field contains illegal chars"); }

    # construct the new line
    my $line = $field{"catname"} . ":" . $catno . ":" . $field{"supermods"} . ":" . $field{"desc"} . ":" . $field{"header"} . ":" . $field{"footer"};

    # set the new line
    &SetCatInfo($field{"destcat"},$line);

    # build the list of all previous mods of this forum now
    my @old_smodlist = &BuildUserGroupList (split (/,/, $oldsupermods));

    # make the list of differences
    foreach $oldsmod (@old_smodlist) {
	# is this user still a mod in any forum?
	if (&IsSuperModerator ($oldsmod) eq "0") {
	    # no. we need to remove moderator status.
	    push (@nosmods, $oldsmod);
	}
    }

    # now, zap mod status from all ex-mods
    foreach $exmod (@nosmods) {
	&SetAccountFlags ($exmod, 0, $FLAG_SUPERMOD);
    }

    # now, get all new mods
    my @new_smodlist = &BuildUserGroupList (split (/,/, $field{"supermods"}));

    # boost 'em all up to mod
    foreach $newmod (@new_smodlist) {
	&SetAccountFlags ($newmod, 1, $FLAG_SUPERMOD);
    }

    # show the page
    &begin_page();

    # show the 'wohohooh' message
    printf "Forum settings changed successfully.<p>";
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"forums\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    printf "<input type=\"submit\" value=\"Back to forum maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

# 
# DoCats()
#
# This will actually alter the category order.
#
sub
DoCats() {
    # we're about to modify stuff!
    &AboutToModify();

    @catinfo=&GetCats();
    my $catcount="0";
    foreach (@catinfo) { $catcount++; }

    # scan them all
    for ($x = 1; $x <= $catcount; $x++) {
	# get the record line
	my $record=&GetItemFromList($field{"name$x"},@catinfo);

	# was there a record?
	if ($record ne "") {
	    # yup. add the category
            my ($oldname,$catno,$supermods,$desc,$header,$footer)=split(/:/,$record);

            my $newline = $oldname . ":" . $catno . ":" . $supermods .":" . $desc . ":" . $header . ":" . $footer;

	    my $tmp=$field{"order$x"} . ":" . $newline;
	    push(@order,$tmp);
	}
    }
    @order = sort { $a <=> $b } @order;

    # does the lockfile exists?
    if ( -f $CATS_LOCKFILE) {
        # yup. the accounts database file is locked
        &cp_error("Category datafile locked. Try again later");
    }

    # create the lock file
    open(LOCKFILE,"+>" . $CATS_LOCKFILE)||&cp_error($ERROR_FILECREATERR . " ($CATS_LOCKFILE)");

    # now, add every record to it
    foreach $it (@order) {
        my ($tmp,$name,$catno,$supermods,$desc,$header,$footer)=split(/:/,$it);

	my $newline = $name . ":" . $catno . ":" . $supermods . ":" . $desc . ":" . $header . ":" . $footer;
	printf LOCKFILE $newline . "\n";
    }

    # close the lockfile
    close(LOCKFILE);

    # copy the old fle over the new file.
    &CopyFile($CATS_LOCKFILE,$CATS_DATAFILE);

    # set up the page
    &begin_page();

    # show the 'wohoo' message
    printf "Category modifications successfully applied<br>";

    # create the 'back to category maintenance' link
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"cats\">";
    printf "<input type=\"submit\" value=\"Back to category maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# DoDeleteCat()
#
# This will delete category $field{"destcat"}.
#
sub
DoDeleteCat() {
    # we're about to modify stuff!
    &AboutToModify();

    # grab the old info
    my $oldline = &GetCatInfo($field{"destcat"});
    my ($tmp,$tmp2,$oldsupermods)=split(/:/,$oldline);

    # zap the category
    &SetCatInfo($field{"destcat"},"");

    # build the list of smods to delete
    my @old_smodlist = &BuildUserGroupList (split (/,/, $oldsupermods));

    # make the list of differences
    foreach $oldsmod (@old_smodlist) {
	# is this user still a mod in any forum?
	if (&IsSuperModerator ($oldsmod) eq "0") {
	    # no. we need to remove moderator status.
	    &SetAccountFlags ($oldsmod, 0, $FLAG_SUPERMOD);
	}
    }

    # set up the page
    &begin_page();

    # show the 'wohoo' message
    printf "Category <b>%s</b> deleted successfully<br>",&RestoreSpecialChars ($field{"destcat"});

    # create the 'back to category maintenance' link
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"cats\">";
    printf "<input type=\"submit\" value=\"Back to category maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# AddCat()
#
# This will show the 'add category' page.
#
sub
AddCat() {
    # set up the page
    &begin_page();

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doaddcat\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};

    # show the info
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"40%\">Category Name</td><td><input type=\"text\" name=\"catname\"></td></tr>";
    printf "<tr><td>Super Moderator (separate with commas)</td><td><input type=\"text\" name=\"supermods\"></td></tr>";
    printf "<tr valign=\"top\"><td>Description</td><td><textarea rows=5 cols=30 name=\"desc\"></textarea></tr>";

    printf "<tr valign=\"top\"><td>Header (overrides general header if not blank. Copy the general header by inserting <code>|header|</code>)</td><td><textarea rows=5 cols=30 name=\"header\"></textarea></tr>";
    printf "<tr valign=\"top\"><td>Footer (overrides general footer if not blank. Copy the general footer by inserting <code>|footer|</code>)</td><td><textarea rows=5 cols=30 name=\"footer\"></textarea></tr>";
    printf "</table><p>";

    # add the 'Add Category' button and close the form
    printf "<p><input type=\"submit\" value=\"Add Category\"></form>";

    # end the old page
    &end_page();
}

#
# DoAddCat()
#
# This will show the 'add category' page.
#
sub
DoAddCat() {
    # we're about to modify stuff!
    &AboutToModify();

    # zap all trailing spaces
    $field{"catname"} = &ZapTrailingSpaces ($field{"catname"});
    $field{"supermods"} = &ZapTrailingSpaces ($field{"supermods"});
    $field{"desc"} = &ZapTrailingSpaces ($field{"desc"});
    $field{"header"} = &ZapTrailingSpaces ($field{"header"});
    $field{"footer"} = &ZapTrailingSpaces ($field{"footer"});

    # build the description, header and footer lines
    $field{"catname"} = &FixSpecialChars ($field{"catname"});
    $field{"desc"} = &HTMLize (&FixSpecialChars ($field{"desc"}));
    $field{"header"} = &HTMLize (&FixSpecialChars ($field{"header"}));
    $field{"footer"} = &HTMLize (&FixSpecialChars ($field{"footer"}));

    # check for internal chars
    if (&HasInternalChars ($field{"catname"}) ne "0") { &cp_error("The category name field contains illegal chars"); }
    if (&HasInternalChars ($field{"supermods"}) ne "0") { &cp_error("The super moderators field contains illegal chars"); }
    if (&HasInternalChars ($field{"desc"}) ne "0") { &cp_error("The description field contains illegal chars"); }
    if (&HasInternalChars ($field{"header"}) ne "0") { &cp_error("The header field contains illegal chars"); }
    if (&HasInternalChars ($field{"footer"}) ne "0") { &cp_error("The footer field contains illegal chars"); }

    # does the category already exists?
    if (&GetCatInfo($field{"catname"}) ne "") {
	# yup. generate error
	my $name = &RestoreSpecialChars ($field{"catname"});
	&error("Category <b>$name</b> already exists");
    }

    # get the number of forums
    @catinfo=&GetCats();
    my $catcount="0";
    foreach (@catinfo) { $catcount++; }
    $catcount++;

    my $line = $field{"catname"} . ":" . $catcount . ":" . $field{"supermods"} . ":" . $field{"desc"} . ":" . $field{"header"} . ":" . $field{"footer"};

    # add the line
    &SetCatInfo($field{"catname"},$line);

    # build the list of smods to add
    my @smodlist = &BuildUserGroupList (split (/,/, $field{"supermods"}));

    # boost everyone up to smod
    foreach $smod (@smodlist) {
	# add the status
	&SetAccountFlags ($smod, 1, $FLAG_SUPERMOD);
    }

    # set up the page
    &begin_page();

    # show the 'wohoo' message
    printf "Category <b>%s</b> successfully added<br>",$field{"catname"};

    # create the 'back to category maintenance' link
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"cats\">";
    printf "<input type=\"submit\" value=\"Back to category maintenance\">";
    printf "</form>";

    # end the old page
    &end_page();
}

#
# SetupDirs()
#
# This will ask the user to set up the directories.
#
sub
SetupDirs() {
    my $cgi_dir = $0;
    $cgi_dir=~ s/\/cp\.cgi//gi;
 
    printf "<head><title>$FORUM_TITLE Control Panel</title></head><body bgcolor=\"#c0c0c0\" text=\"#000000\">";

    printf "<center><font size=6>ForuMAX Control Panel</font></center><p>";
    printf "This forum's directories haven't been set up correctly yet. You will need to do this first before you can actually use it. Please fill in the fields below. Make sure there are no errors, because you cannot change them later.<p>";
    printf "<b>NOTE</b> <i>NEVER</i> add trailing slashes to paths, and <i>ALWAYS</i> use FORWARD slashes (/), even when using Windows!<p>";

    printf "<form method=\"post\" action=\"cp.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dosetupdirs\">";
    printf "<table border=0>";
    printf "<tr><td>CGI-BIN directory<br><font size=2>This directory should contain all CGI-BIN scripts of the forum<br>(eg. <b>forum.cgi</b>, <b>cp.cgi</b>, <b>forum_lib.pl</b> etc must reside there)</td><td><input type=\"text\" name=\"cgi-dir\" value=\"%s\"></td></tr>",$cgi_dir;
    printf "<tr><td>Data directory<br><font size=2>This directory should contain all forum data<br>(<b>forums</b>, <b>accounts</b> and <b>cats</b> must reside there)</td><td><input type=\"text\" name=\"db-dir\"></td></tr>";
    printf "<tr><td>Images <b>URL</b><br><font size=2>This should be the URL where the forum images can be found<br>(ie <b>edit.gif</b>, <b>del.gif</b> and <b>forumh.jpg</b> must reside there)</td><td><input type=\"text\" name=\"img-uri\" value=\"%s\"></td></tr>", "http://" . $ENV{"SERVER_NAME"} . "/forum-images";

    printf "<tr><td>User name</td><td><input type=\"text\" name=\"username\"></td></tr>";
    printf "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>";
    printf "</table><input type=\"submit\" value=\"OK\"></form>";
    printf "</body>";
}

#
# DoSetupDirs()
#
# This will actually setup the directories
#
sub
DoSetupDirs() {
    # we're about to modify stuff!
    &AboutToModify();

    # is the CGI directory valid?
    if (!-d $field{"cgi-dir"}) {
	# no, it's not a directory. complain
	&error("CGI directory <i>" . $field{"cgi-dir"} . "</i> is not a directory!");
    }

    # appropriate files there?
    if (!-f $field{"cgi-dir"} . "/cp.cgi") {
	# no, it's not there. complain
	&error("File <code>cp.cgi</code> doesn't exist in CGI directory <i>" . $field{"cgi-dir"} . "</i>");
    }
    if (!-f $field{"cgi-dir"} . "/forum.cgi") {
	# no, it's not there. complain
	&error("File <code>forum.cgi</code> doesn't exist in CGI directory <i>" . $field{"cgi-dir"} . "</i>");
    }
    if (!-f $field{"cgi-dir"} . "/forum_lib.pl") {
	# no, it's not there. complain
	&error("File <code>forum_lib.pl</code> doesn't exist in CGI directory <i>" . $field{"cgi-dir"} . "</i>");
    }

    # does the data directory exist?
    if (!-d $field{"db-dir"}) {
	# no, it's not a directory. complain
	&error("Forum data directory <i>" . $field{"db-dir"} . "</i> is not a directory!");
    }

    # appropriate files there?
    if (!-f $field{"db-dir"} . "/accounts") {
	# no, it's not there. complain
	&error("File <code>accounts</code> doesn't exist in data directory <i>" . $field{"db-dir"} . "</i>");
    }
    if (!-f $field{"db-dir"} . "/forumdata") {
	# no, it's not there. complain
	&error("File <code>forumdata</code> doesn't exist in data directory <i>" . $field{"db-dir"} . "</i>");
    }
    if (!-f $field{"db-dir"} . "/cats") {
	# no, it's not there. complain
	&error("File <code>cats</code> doesn't exist in data directory <i>" . $field{"db-dir"} . "</i>");
    }

    # does the images URL begin with http:// ?
    my $tmp = $field{"img-uri"};
    $tmp=~ s/^http\:\/\///gi;
    if ($field{"img-uri"} eq $tmp) {
	# no. complain
	&error("The images URL should begin with <code>http://</code>");
    }

    # all looks fine. verify the account info
    $USERDB_FILE=$field{"db-dir"} . "/accounts";

    # verify the account info
    $FORCE_LOGIN = "YES"; $field{"id"} = ""; $cookie{"id"} = "";
    &VerifyID();

    # it all worked out *fine*! Write the new values
    $FORUM_DIR=$field{"db-dir"} . "/";
    $IMAGES_URI=$field{"img-uri"};
    $TMPCONF_FILE=$field{"cgi-dir"} . "/forum_options.TMP.pl";
    $CONF_FILE=$field{"cgi-dir"} . "/forum_options.pl";
    $FORUM_DATAFILE=$field{"db-dir"} . "/forumdata";
    $FORUM_LOCKFILE=$field{"db-dir"} . "/forumdata.LOCK";
    $WEBSITE_URI="http://" . $ENV{"SERVER_NAME"};
    $CATS_DATAFILE=$field{"db-dir"} . "/cats";
    $CATS_LOCKFILE=$field{"db-dir"} . "/cats.LOCK";
    $USERDB_LOCKFILE=$field{"db-dir"} . "/accounts.LOCK";
    $GROUPDB_FILE=$field{"db-dir"} . "/groups";
    $GROUPDB_LOCKFILE=$field{"db-dir"} . "/groups.LOCK";
    $DIRS_SETUP="YES";

    # write them to the datafile
    &WriteConfig();

    # do the HTML stuff
    &HTMLHeader();
    &begin_page();

    printf "The directories have been setup successfully. You can now use the control panel to set the forum up.";

    # end the HTML stuff
    &end_page();
}

#
# Smilies()
#
# This will show the 'edit smilies' page
#
sub
Smilies() {
    # do the HTML stuff
    &begin_page();

    # split the smilies
    my @smilies = split(/\|/, $SMILIES);
    my $totalsmilies = @smilies;

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dosmilies\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"totalsmilies\" value=\"%s\">", $totalsmilies;
    printf "<table width=\"100%\" border=1>";

    printf "<tr><td width=\"20%\"><font size=2>Check this to delete the smilie</font></td><td width=\"20%\"><font size=2>Text to invoke smilie</a></td><td width=\"30%\"><font size=2>Icon file</font></td><td width=\"30%\"><font size=2>Preview</font></td></tr>";

    my $x = 0;
    foreach $smily (@smilies) {
	my @part = split(/\=/, $smily);
        printf "<tr><td align=\"center\"><input type=\"checkbox\" name=\"delete$x\"></td><td><input type=\"text\" name=\"text$x\" value=\"%s\"></td><td><input type=\"text\" name=\"img$x\" value=\"%s\"></td><td><img src=\"%s\" alt=\"[Icon Preview]\"></td></tr>", $part[0], $part[1], $IMAGES_URI . "/" . $part[1];
	
	$x++;
    }

    printf "</table>";

    printf "<p>If you want to add extra Smilies, check the checkbox below and fill in the number of Smilies you want to add<p>";

    printf "<table width=\"50%\" border=1>";
    printf "<tr><td align=\"center\">Add smilies?<br><input type=\"checkbox\" name=\"addsmilies\"></td><td align=\"center\">Number of smilies to add?<br><input type=\"text\" name=\"nofsmilies\"></td></tr>";
    printf "</table><p>";

    printf "<input type=\"submit\" value=\"OK\">";

    printf "</form>";

    # end the HTML stuff
    &end_page();
}

#
# DoSmilies()
#
# This will actually submit the smilies.
#
sub
DoSmilies() {
    # we're about to modify stuff!
    &AboutToModify();

    # re-create the list of smilies
    $SMILIES = "";
    for ($i = 0; $i < $field{"totalsmilies"}; $i++) {
        # marked as delete?
        if ($field{"delete$i"} eq "") {
	    # no. add this one
            $SMILIES .= $field{"text$i"} . "=" . $field{"img$i"} . "|";
	}
    }

    # add the smilies
    if ($field{"addsmilies"} ne "") {
        for ($i = 0; $i < $field{"nofsmilies"}; $i++) {
    	    $SMILIES .= "=|";
        }
    }
    chop $SMILIES;

    # save this
    &WriteConfig();

    # and chain to the 'edit smilies' procedure
    &Smilies();
}

#
# BuildFieldTypes ($no, $value)
#
# This will build the field type list for item $no with value $value
#
sub
BuildFieldTypes() {
    # get the arguments
    my ($no, $value) = @_;

    printf "<select name=\"type$no\">";
    printf "<option value=\"0\"";
    if (($value eq 0) or ($value eq "")) { printf " selected"; }
    printf ">Text</option>";
    printf "<option value=\"1\"";
    if ($value eq 1) { printf " selected"; }
    printf ">URL</option>";
    printf "<option value=\"2\"";
    if ($value eq 2) { printf " selected"; }
    printf ">ICQ</selected>";
    printf "<option value=\"3\"";
    if ($value eq 3) { printf " selected"; }
    printf ">AIM</option>";
    printf "<option value=\"4\"";
    if ($value eq 4) { printf " selected"; }
    printf ">Yahoo! ID</option>";
    printf "<option value=\"5\"";
    if ($value eq 5) { printf " selected"; }
    printf ">Gender</option>";
    printf "<option value=\"6\"";
    if ($value eq 6) { printf " selected"; }
    printf ">Homepage URL</option>";
    printf "<option value=\"7\"";
    if ($value eq 7) { printf " selected"; }
    printf ">Custom Status</option>";
    printf "<option value=\"8\"";
    if ($value eq 8) { printf " selected"; }
    printf ">Joining Date</option>";
    printf "</select>";
}

#
# BuildFieldPerms ($no, $value)
#
# This will build the field permissions field of field $no with value $value.
#
sub
BuildFieldPerms() {
    # get the arguments
    my ($no, $value) = @_;

    printf "<select name=\"perm$no\">";
    printf "<option value=\"0\"";
    if (($value eq 0) or ($value eq "")) { printf " selected"; }
    printf ">User himself and admins can modify this field</option>";
    printf "<option value=\"1\"";
    if ($value eq 1) { printf " selected"; }
    printf ">Only admins can modify this field</option>";
    printf "</select>";
}

#
# ExtraFields()
#
# This will show the extra fields.
#
sub
ExtraFields() {
    # do the HTML stuff
    &begin_page();

    my @prof_fields = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @prof_type = split(/\|/, $EXTRA_PROFILE_TYPES);
    my @prof_hidden = split(/\|/, $EXTRA_PROFILE_HIDDEN);
    my @prof_perms = split(/\|/, $EXTRA_PROFILE_PERMS);
    my $totalfields = @prof_fields;

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doextrafields\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"totalfields\" value=\"%s\">", $totalfields;
    printf "<table width=\"100%\" border=1>";
    printf "<tr><td width=\"15%\"><b>Field number</b></td><td width=\"15%\"><b>Field name</b></td><td width=\"25%\"><b>Type</b></td><td width=\"15%\"><b>Hide in profile?</td><td width=\"30%\"><b>Permissions</b></td></tr>";

    my $i = 0;
    foreach $name (@prof_fields) {
	printf "<tr><td>%s</td><td><input type=\"text\" name=\"name$i\" value=\"%s\"></td><td>", $i,$name;
	&BuildFieldTypes ($i, $prof_type[$i]);
	printf "</td><td><center><input type=\"checkbox\" name=\"hide$i\"";
	if ($prof_hidden[$i] eq "YES") {
	    printf " checked";
	}
	printf "></td></center><td>";
	&BuildFieldPerms ($i, $prof_perms[$i]);
        printf "</td></tr>";
	$i++;
    }

    printf "</table>";
    printf "<p>If you want to add extra fields, check the checkbox below and fill in the number of extra fields you want to add<p><i>Notice</i> You cannot delete extra fields. However, you can make them inaccessible to users by hiding it in the profile and setting the permissiosn to Administrator modification only<p>";

    printf "<table width=\"50%\" border=1>";
    printf "<tr><td align=\"center\">Add fields?<br><input type=\"checkbox\" name=\"addfields\"></td><td align=\"center\">Number of extra fields to add?<br><input type=\"text\" name=\"nofieldstoadd\"></td></tr>";
    printf "</table><p>";

    printf "<input type=\"submit\" value=\"OK\">";

    printf "</form>";

    # end the HTML stuff
    &end_page();
}

#
# DoExtraFields()
#
# This will actually modify the extra fields.
#
sub
DoExtraFields() {
    # we're about to modify stuff!
    &AboutToModify();

    # re-create the list of smilies
    $EXTRA_PROFILE_FIELDS = "";
    $EXTRA_PROFILE_TYPES = "";
    $EXTRA_PROFILE_HIDDEN = "";
    $EXTRA_PROFILE_PERMS = "";

    my $totalfields = @prof_fields;
    for ($i = 0; $i < $field{"totalfields"}; $i++) {
	# add the fields
	$EXTRA_PROFILE_FIELDS .= $field{"name$i"} . "|";
	$EXTRA_PROFILE_TYPES .= $field{"type$i"} . "|";
	if ($field{"hide$i"} ne "") {
	    $EXTRA_PROFILE_HIDDEN .= "YES|";
	} else {
	    $EXTRA_PROFILE_HIDDEN .= "NO|";
	}
	$EXTRA_PROFILE_PERMS .= $field{"perm$i"} . "|";
    }

    # add new ones
    if ($field{"addfields"} ne "") {
        for ($i = 0; $i < $field{"nofieldstoadd"}; $i++) {
	    $EXTRA_PROFILE_FIELDS .= "New field|";
	    $EXTRA_PROFILE_TYPES .= "|";
	    $EXTRA_PROFILE_HIDDEN .= "YES|";
	    $EXTRA_PROFILE_PERMS .= "1|";
	}
    }
    chop $EXTRA_PROFILE_FIELDS;
    chop $EXTRA_PROFILE_TYPES;
    chop $EXTRA_PROFILE_HIDDEN;
    chop $EXTRA_PROFILE_PERMS;

    # write this to the configuration file
    &WriteConfig();

    # chain to the extra fields page
    &ExtraFields();
}

#
# Groups()
#
# This will handle the user groups.
#
sub
Groups() {
    # set up the page
    &begin_page();

    # grab all groups
    my @groups = &GetAllGroups();

    # set up the table
    printf "<table width=\"100%\" border=1>";
    printf "<tr><td width=\"70%\"><b>Group Name</b></td><td width=\"30%\"><b>Number of members</b></td></tr>";

    # show information for every group
    foreach $groupinfo (@groups) {
	# figure out the information
        my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$groupinfo);
	my @group_members = split(/\,/, $groupmembers);	
	my $nofmembers = @group_members;

	printf "<tr><td><a href=\"cp.cgi?action=editgroup&id=%s&destgroup=%s\">%s</a></td><td>%s</td></tr>", $field{"id"}, &TransformForBrowser ($groupname), $groupname, $nofmembers;
    }

    # end the table
    printf "</table>";

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"addgroup\">";

    printf "<input type=\"submit\" value=\"Add group\">";
    printf "</form>";

    &end_page();
}

#
# EditGroup()
#
# This will show the page for editing group $field{"destgroup"}.
#
sub
EditGroup() {
    # grab the group's information
    my $groupinfo = &GetGroupRecord ($field{"destgroup"});

    # did this yield any results?
    if ($groupinfo eq "") {
	# no. complain
	&cp_error("Group <b>" . $field{"destgroup"} . "</b> could not be found");
    }

    # split the group info
    my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$groupinfo);

    # set up the page
    &begin_page();

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"doeditgroup\">";
    printf "<input type=\"hidden\" name=\"destgroup\" value=\"%s\">", $field{"destgroup"};

    # show the info
    printf "<table width=\"100%\" border=0>";
    printf "<tr><td>Group name</td><td><input type=\"text\" name=\"destname\" value=\"%s\"></td></tr>",$field{"destgroup"};
    printf "<tr><td>Group description</td><td><input type=\"text\" name=\"desc\" value=\"%s\"></td></tr>",$groupdesc;
    printf "<tr><td>Group members<br><font size=1>Group members must be separated by commas (<code>,</code>)</font></td><td><input type=\"text\" name=\"members\" value=\"%s\"></td></tr>",$groupmembers;

    printf "</table><p>";

    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form>";

    # add the 'delete group' button
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"deletegroup\">";
    printf "<input type=\"hidden\" name=\"destgroup\" value=\"%s\">", $field{"destgroup"};
    printf "<input type=\"submit\" value=\"Delete group\">";
    printf "</form>";

    # end the page
    &end_page();
}

#
# DoEditGroup()
#
# This will actually modify group $field{"destgroup"}
#
sub
DoEditGroup() {
    # we're about to modify stuff!
    &AboutToModify();

    # grab the group's information
    my $groupinfo = &GetGroupRecord ($field{"destgroup"});

    # did this yield any results?
    if ($groupinfo eq "") {
	# no. complain
	&cp_error("Group <b>" . $field{"destgroup"} . "</b> could not be found");
    }

    # split the group info
    my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$groupinfo);

    # does the new group name already exist and should it be renamed?
    if (($field{"destname"} ne $field{"destgroup"}) and (&GetGroupRecord ($field{"destname"}) ne "")) {
	# yup. complain
	&cp_error("Group <b>" . $field{"destname"} . "</b> already exists");
    }

    # destroy any whitespace near commas
    $field{"members"}=~ s/((\s)*)\,/\,/g;
    $field{"members"}=~ s/\,((\s)*)/\,/g;

    # do all members actually exist?
    foreach $member (split(/,/, $field{"members"})) {
	# try to grab the profile
	if (&GetUserRecord ($member) eq "") {
	    # it doesn't exist. complain
	    &cp_error ("User '<b>" . $member . "</b>' doesn't exists, please check the spelling");
	}
    }

    # build the new record
    $new_record = $field{"destname"} . ":" . $groupid . ":" . $field{"desc"} . ":" . $field{"members"};

    # alter the record
    &SetGroupRecord ($field{"destgroup"}, $new_record);

    # set up the page
    &begin_page();

    printf "The group has successfully been edited. Click <a href=\"cp.cgi?action=groups&id=%s\">here</a> to return to the group overview", $field{"id"};

    # end the page
    &end_page();
}

#
# AddGroup()
#
# This will show the page for adding groups.
#
sub
AddGroup() {
    # start the page
    &begin_page();

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"doaddgroup\">";

    # show the info
    printf "<table width=\"100%\" border=0>";
    printf "<tr><td>Group name</td><td><input type=\"text\" name=\"destname\"></td></tr>";
    printf "<tr><td>Group description</td><td><input type=\"text\" name=\"desc\"></td></tr>";
    printf "<tr><td>Group members</td><td><input type=\"text\" name=\"members\"></td></tr>";

    printf "</table><p>";

    printf "<input type=\"submit\" value=\"Add group\">";
    printf "</form>";

    # end the page
    &end_page();
}

#
# DoAddGroup()
#
# This will actually add group $field{"destname"}
#
sub
DoAddGroup() {
    # we're about to modify stuff!
    &AboutToModify();

    # does this group already exist?
    if (&GetGroupRecord ($field{"destname"}) ne "") {
	# no. complain
	&cp_error("Group <b>" . $field{"destname"} . "</b> already exists");
    }

    # count the number of groups
    $nofgroups = &GetNofGroups();
    my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$groupinfo);

    # create the group record
    $grouprecord = $field{"destname"} . ":" . $nofgroups . ":" . $field{"desc"} . ":" . $field{"members"};
    &SetGroupRecord ($field{"destname"}, $grouprecord);

    # show the 'wohoo' message
    &begin_page();

    printf "Group <b>" . $field{"destname"} . "</b> successfully added<p>";

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"groups\">";

    printf "<input type=\"submit\" value=\"Back to group management\">";
    printf "</form>";

    # end the page
    &end_page();
}

#
# DeleteGroup()
#
# This will delete group $field{"destgroup"}
#
sub
DeleteGroup() {
    # does this group already exist?
    if (&GetGroupRecord ($field{"destgroup"}) eq "") {
	# no. complain
	&cp_error("Group <b>" . $field{"destgroup"} . "</b> doesn't exist. Perhaps it was already deleted?");
    }

    # zap the record
    &SetGroupRecord ($field{"destgroup"}, "");

    # show the 'wohoo' message
    &begin_page();

    printf "Group <b>" . $field{"destgroup"} . "</b> successfully deleted<p>";

    # set up the form
    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"groups\">";

    printf "<input type=\"submit\" value=\"Back to group management\">";
    printf "</form>";

    &end_page();
}

#
# PrunePosts()
#
# This will show the page to prune posts.
#
sub
PrunePosts() {
    # grab all forum names
    my @forums = &GetForums();

    # build the page
    &begin_page();

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"dopruneposts\">";

    printf "<table>";
    printf "<tr><td>Prune all posts by username</td><td><input type=\"text\" name=\"destuser\"></td></tr>";
    printf "<tr><td>from forums</td><td><select name=\"destforum\">";

    # add the 'all forums' thing
    printf "<option value=\":all:\" selected>All forums</option>";

    # browse all forums
    foreach $theforum (@forums) {
        # get the name and flags
        my ($name,$tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$forum_flags) = split(/:/,$theforum);
        # is this forum not hidden and enabled, and not the current forum?
        if ((&check_flag ($forum_flags,$FLAG_FORUM_HIDDEN) eq 0) and (&check_flag ($forum_flags,$FLAG_FORUM_DISABLED) eq 0) and ($name ne $field{"forum"})) {
            # yup. add it to the list
            printf "<option value=\"%s\">%s</option>",$name,$name;
        }
    }
    printf "</select></td></tr>";
    printf "</table><p>";

    printf "<input type=\"submit\" value=\"Prune\">";
    printf "</form>";

    &end_page();
}

#
# DoPrunePosts()
#
# This will actually prune the posts.
#
sub
DoPrunePosts() {
    # we're about to modify stuff!
    &AboutToModify();

    # disable output buffering for this page
    $| = 1;

    # do we have a destination user name?
    if ($field{"destuser"} eq "") {
	# no. complain
	&cp_error("You must fill in a destination user name!");
    }

    # grab the forum list
    my @forums=&GetForums();

    # create the header
    &begin_page();

    # start all counts with zero
    my $threads_killed = 0;
    my $posts_killed = 0;

    # browse them all
    foreach $theforum (@forums) {
	# grab the name
	my ($forumname)=split(/:/,$theforum);

	# does this forum need to be pruned?
	if (($field{"destforum"} eq ":all:") or ($field{"destforum"} eq $forumname)) {
	    # yup. prune it
	    printf "<li>Pruning from forum <b>%s</b></li>", $forumname;

	    # open this forum datafile
	    open(PFORUMDATA,$FORUM_DIR . $forumname . $FORUM_EXT)||&cp_error("Cannot open forum datafile");

	    # browse it completely
	    while (<PFORUMDATA>) {
		# grab a line and split it
		my $line=$_; chop $line;
		my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter,$locker)=split(/:/,$line); 

		# did this user create the thread?
		if ($field{"destuser"} eq $owner) {
		    # yup. blow it into oblivion
		    printf "<dl><dd>Deleting thread <b>%s</b></dd></dl>", $subject;
		    &DestroyThread ($forumname, $forum_id);
		    $threads_killed++;
		} else {
		    # no. did this user reply to the thread?
		    $replied = 0;

		    # open the thread datafile
		    open(PTHREADDATA,$FORUM_DIR . $forumname . "/" . $forum_id)||&cp_error("Cannot open thread datafile");

		    while (<PTHREADDATA>) {
			# get the line
			my $line=$_; chop $line;

			# is this a special marker field?
			my ($dot,$author) = split(/:/, $_);
			if ($dot eq ".") {
			    # yup. did the user reply to this thread?
			    if ($author eq $field{"destuser"}) {
				# yup. he did
				$replied = 1;
			    }
			}
		    }

		    # need to zap replies?
		    if ($replied ne 0) {
			# yup. do it
		        printf "<dl><dd>Removing replies from thread <b>%s</b></dd></dl>", $subject;
			$posts_killed = $posts_killed + &DestroyPost ($forumname, $forum_id, "", $field{"destuser"});
		    }

		    # close the thread datafile
		    close (PTHREADDATA);
		}
	    }

	    # close the forum datafile
	    close(PFORUMDATA);
	}
    }

    printf "<hr>";
    printf "The forums have successfully been cleaned up. A total of <b>%s</b> threads and <b>%s</b> post were deleted, making an average of <b>%s</b> deleted items",$threads_killed,$posts_killed,$threads_killed+$posts_killed;

    # end the page
    &end_page();
}

#
# EmailAccounts()
#
# This will show the page for emailing a message to all members.
#
sub
EmailAccounts() {
    # create the header
    &begin_page();

    printf "<form action=\"cp.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doemailaccounts\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">", $field{"id"};

    printf "<table width=\"100%\" border=1>";
    printf "<tr><td><input type=\"radio\" name=\"emailwho\" value=\"all\" checked>Email all forum users</option><br>This will send your message to literally all users on this forum. <b>Use with caution on large sites!</b></td></tr>";
    printf "<tr><td><input type=\"radio\" name=\"emailwho\" value=\"specific\">You can also email only specific accounts (like all administrators, all moderators, all members under 13 etc)</input><p>Please check all accounts you want to email below:<p>";
    printf "<input type=\"checkbox\" name=\"f_admin\">Administrator accounts</input><br>";
    printf "<input type=\"checkbox\" name=\"f_disabled\">Disabled accounts</input><br>";
    printf "<input type=\"checkbox\" name=\"f_mod\">Moderators</input><br>";
    printf "<input type=\"checkbox\" name=\"f_smod\">Super Moderators</input><br>";
    printf "<input type=\"checkbox\" name=\"f_mmod\">Mega Moderators</input><br>";
    printf "<input type=\"checkbox\" name=\"f_u13\">Accounts below 13</input><br>";
    printf "<p><input type=\"radio\" name=\"matchmode\" value=\"any\" checked>Only one of these flags has to be set to match</input><br>";
    printf "<input type=\"radio\" name=\"matchmode\" value=\"all\">All these flags have to be set exactly as they are here to match</input><br>";
    printf "</td></tr>";
    printf "<tr><td>Message to send:<p>Subject: <input type=\"text\" name=\"subject\"><br>Message:<br><textarea rows=10 cols=30 name=\"body\"></textarea><p>HTML is allowed in emails. All newlines will automatically be converted to HTML linebreaks, for your convenience</td></tr>";
    printf "</table>";

    printf "<p><input type=\"checkbox\" name=\"dontemail\">Don't email the accounts, but show the email addresses to email instead</input><p><input type=\"submit\" value=\"Send emails\">";
    printf "</form>";

    # end the page
    &end_page();
}

#
# DoEmailAccounts()
#
# This will actually email all accounts.
#
sub
DoEmailAccounts() {
    # we're about to modify stuff!
    &AboutToModify();

    # grab all accounts
    my @accounts = &GetAllAccounts();

    # disable output buffering
    $| = 1;

    # replace newlines by HTML line breaks in the message
    $field{"body"}=~ s/\n/\<br\>/gi;

    # show the page
    &begin_page();

    # show some info
    printf "Building list of accounts to email...";

    # scan them one by one
    my $ok; my @emaillist; my $count = 0;
    foreach $theaccount (@accounts) {
	# default to not ok
	$ok = 0;

	# grab the email addy
	my ($accountname,$tmp2,$flags,$tmp3,$tmp4,$email)=split(/:/,$theaccount);

	# do we have to check for anything?
	if ($field{"emailwho"} eq "all") {
	    # no. just add this account
	    $ok = 1;
	} else {
	    # yes, we do. grab all basic info
	    $is_admin = &check_flag ($flags, $FLAG_ADMIN);
	    $is_mod = &check_flag ($flags, $FLAG_MOD);
	    $is_smod = &check_flag ($flags, $FLAG_SUPERMOD);
	    $is_mmod = &check_flag ($flags, $FLAG_MEGAMOD);
	    $is_disabled = &check_flag ($flags, $FLAG_DISABLED);
	    $is_u13 = &check_flag ($flags, $FLAG_UNDER13);
	
	    # does everything need to be true?
	    if ($field{"matchmode"} eq "any") {
		# no, any flag will do
		if (($field{"f_admin"} ne "") and ($is_admin ne 0)) { $ok = 1; }
		if (($field{"f_disabled"} ne "") and ($is_disabled ne 0)) { $ok = 1; }
		if (($field{"f_mod"} ne "") and ($is_mod ne 0)) { $ok = 1; }
		if (($field{"f_mmod"} ne "") and ($is_mmod ne 0)) { $ok = 1; }
		if (($field{"f_smod"} ne "") and ($is_smod ne 0)) { $ok = 1; }
		if (($field{"f_u13"} ne "") and ($is_u13 ne 0)) { $ok = 1; }
	    } else {
		# yes, all flags have to be set like the user told us
		$mustbe_admin = 0; $mustbe_mod = 0; $mustbe_u13 = 0;
		$mustbe_disabled = 0; $mustbe_smod = 0; $mustbe_mmod = 0;
		if ($field{"f_admin"} ne "") { $mustbe_admin = 1; }
		if ($field{"f_disabled"} ne "") { $mustbe_disabled = 1; }
		if ($field{"f_mod"} ne "") { $mustbe_mod = 1; }
		if ($field{"f_u13"} ne "") { $mustbe_u13 = 1; }
		if ($field{"f_mmod"} ne "") { $mustbe_mmod = 1; }
		if ($field{"f_smod"} ne "") { $mustbe_smod = 1; }

		# is this all true?
		if (($mustbe_admin eq $is_admin) and ($mustbe_disabled eq $is_disabled) and ($mustbe_mod eq $is_mod) and ($mustbe_u13 eq $is_u13) and ($mustbe_mmod eq $is_mmod) and ($mustbe_smod eq $is_smod)) {
		    # yup. add the user
		    $ok = 1;
		}
	    }
	}

	# need to add the user to the list?
	if ($ok ne 0) {
	    # no. just add this account
	    push (@emaillist, $email);
	    $count++;
	}
    }

    printf " done<p>";

    # do we actually email them?
    if ($field{"dontemail"} eq "") {
	# yup. show that
        printf "Emailing $count account";
    } else {
	printf "Listing $count account";
    }
    if ($count ne 1) { printf "s"; }
    if ($field{"dontemail"} eq "") {
	printf "...";
    } else {
	printf "<hr>";
    }

    # now, email all accounts or show all addresses
    my $no = 0;
    foreach $email (@emaillist) {
	# need to actually email?
	if ($field{"dontemail"} eq "") {
	    # yup. do it
            &SendEmail_Sendmail($email,$field{"subject"},$field{"body"});
	} else {
	    printf "$email<br>";
	}

	# print a dot every 100 accounts if emailing
	if (($no eq 100) and ($field{"dontemail"} eq "")) {
	    print ".";
	    $no = 0;
	}
	$no++;
    }

    if ($field{"dontemail"} eq "") {
        printf " done<p>All accounts have been emailed";
    } else {
        printf "<hr>All email addresses successfully printed";
    }
}

# no page header shown yet
$shown_header=0;

# are the paths set up?
if ($DIRS_SETUP eq "NO") {
    # no. is this the request to set them up?
    if ($field{"action"} eq "dosetupdirs") {
        # yup. set them up
	&DoSetupDirs();
    } else {
        # no. ask the user to set them up
	&HTMLHeader();
        &SetupDirs();
    }
    exit;
}

my $idstring = $field{"id"};
if ($idstring eq "") { $idstring = $cookie{"id"}; }

# is there's no id specified?
if (($idstring eq "") and ($field{"username"} eq "") and ($field{"password"} eq "")) {
    # yup. ask for identification
    &HTMLHeader();
    &AskIdentification();
    exit;
}

# if we don't have a hash. generate it from the username and password fields
if (($field{"id"} eq "") and ($USE_COOKIES ne "YES")) {
    $field{"id"} = &HashID ($field{"username"}, $field{"password"});
    $idstring = $field{"id"};
} else {
    # build the cookie
    if ($idstring eq "") {
        $idstring = &HashID ($field{"username"}, $field{"password"});
	&SetCookie ("id", $idstring, 30 * 24 * 60 * 60);
    }
}

# verify the hash
&VerifyHash($idstring);

# do we actually have admin rights?
if (&check_flag($flags,$FLAG_ADMIN) eq "0") {
    # nope. die
    &error("You must have administrator privileges in order to use this!");
}

# is our account disabled?
if (&check_flag($flags,$FLAG_DISABLED) ne "0") {
    # yes. die
    &error($ERROR_ACCOUNTDISABLED);
}

# do we need to preview the colors?
if ($field{"preview"} ne "") {
    # yup. do it
    &HTMLHeader();
    &PreviewColors();
    exit;
}

# let's see, do we just need to show the main page?
if (($field{"action"} eq "main") or ($field{"action"} eq "")) {
    # yup, do it
    &HTMLHeader();
    &ShowMain();
    exit;
}

# maybe it is the request to edit accounts?
if ($field{"action"} eq "accounts") {
    # yup, do it
    &HTMLHeader();
    &Accounts();
    exit;
}

# maybe it is the request to show all accounts applicable for editing?
if ($field{"action"} eq "showaccounts") {
    # yup, do it
    &HTMLHeader();
    &ShowAccounts();
    exit;
}

# maybe it is the request to edit the account?
if ($field{"action"} eq "editaccount") {
    # yup, do it
    &HTMLHeader();
    &EditAccount();
    exit;
}

# maybe it is the request to actually edit the account?
if ($field{"action"} eq "doeditaccount") {
    # yup, do it
    &DoEditAccount();
    exit;
}

# maybe it is the request to nuke the account?
if ($field{"action"} eq "donukeaccount") {
    # yup. do it
    &HTMLHeader();
    &DoNukeAccount();
    exit;
}

# maybe it is the request to add an account?
if ($field{"action"} eq "addaccount") {
    # yup. do it
    &HTMLHeader();
    &AddAccount();
    exit;
}

# maybe it is the request to actually add the account?
if ($field{"action"} eq "doaddaccount") {
    # yup. do it
    &HTMLHeader();
    &DoAddAccount();
    exit;
}

# maybe it is the request to edit the forums?
if ($field{"action"} eq "forums") {
    # yup. do it
    &HTMLHeader();
    &EditForums();
    exit;
}

# maybe it is the request to actually edit the forums?
if ($field{"action"} eq "doeditforums") {
    # yup. do it
    &HTMLHeader();
    &DoEditForums();
    exit;
}

# maybe it is the request to edit a specific forum?
if ($field{"action"} eq "editforum") {
    # yup, do it
    &HTMLHeader();
    &EditTheForum();
    exit;
}

# maybe it is the request to actually edit a specific forum?
if ($field{"action"} eq "doeditforum") {
    # yup, do it
    &HTMLHeader();
    &DoEditTheForum();
    exit;
}

# maybe it is the request to actually delete a specific forum?
if ($field{"action"} eq "dodeleteforum") {
    # yup, do it
    &HTMLHeader();
    &DoDeleteForum();
    exit;
}

# maybe it is the request to add a forum?
if ($field{"action"} eq "addforum") {
    # yup, do it
    &HTMLHeader();
    &AddForum();
    exit;
}

# maybe it is the request to actually add the forum?
if ($field{"action"} eq "doaddforum") {
    # yup, do it
    &HTMLHeader();
    &DoAddForum();
    exit;
}

# maybe it is the request to delete all forum messages?
if ($field{"action"} eq "deletemessages") {
    # yup, do it
    &HTMLHeader();
    &DeleteMessages();
    exit;
}

# maybe it is the request to edit the general options?
if ($field{"action"} eq "options") {
    # yup, do it
    &HTMLHeader();
    &Options();
    exit;
}

# maybe it is the request to actually change the options?
if ($field{"action"} eq "doptions") {
    # yup, do it
    &HTMLHeader();
    &DoOptions();
    exit;
}

# maybe the user wants to edit the styles?
if ($field{"action"} eq "styles") {
    # yup. do it
    &HTMLHeader();
    &Styles();
    exit;
}

# maybe the user wants to actually change the styles?
if ($field{"action"} eq "dostyles") {
    # yup. do it
    &HTMLHeader();
    &DoStyles();
    exit;
}

# maybe the user wants to edit the categories?
if ($field{"action"} eq "cats") {
    # yup. do it
    &HTMLHeader();
    &Cats();
    exit;
}

# maybe the user wants to edit a specific category?
if ($field{"action"} eq "editcat") {
    # yup. do it
    &HTMLHeader();
    &EditCat();
    exit;
}

# maybe the user wants to actually edit the specific category?
if ($field{"action"} eq "doeditcat") {
    # yup. do it
    &HTMLHeader();
    &DoEditCat();
    exit;
}

# maybe the user wants to modify the category order?
if ($field{"action"} eq "docats") {
    # yup. do it
    &HTMLHeader();
    &DoCats();
    exit;
}

# maybe the user wants to delete a category?
if ($field{"action"} eq "dodeletecat") {
    # yup. do it
    &HTMLHeader();
    &DoDeleteCat();
    exit;
}

# maybe the users wants to add a category?
if ($field{"action"} eq "addcat") {
    # yup. do it
    &HTMLHeader();
    &AddCat();
    exit;
}

# maybe the users wants to actually add the category?
if ($field{"action"} eq "doaddcat") {
    # yup. do it
    &HTMLHeader();
    &DoAddCat();
    exit;
}

# maybe the user wants to edit the smilies?
if ($field{"action"} eq "smilies") {
    # yup. do it
    &HTMLHeader();
    &Smilies();
    exit;
}

# maybe the user wants to submit the smilies?
if ($field{"action"} eq "dosmilies") {
    # yup. do it
    &HTMLHeader();
    &DoSmilies();
    exit;
}

# maybe the user wants to edit the extra fields?
if ($field{"action"} eq "extrafields") {
    # yup. do it
    &HTMLHeader();
    &ExtraFields();
    exit;
}

# maybe the user wants to submit the extra fields?
if ($field{"action"} eq "doextrafields") {
    # yup. do it
    &HTMLHeader();
    &DoExtraFields();
    exit;
}

# maybe the user wants to edit the groups?
if ($field{"action"} eq "groups") {
    # yup. do it
    &HTMLHeader();
    &Groups();
    exit;
}

# maybe the user wants to edit a specific group?
if ($field{"action"} eq "editgroup") {
    # yup. do it
    &HTMLHeader();
    &EditGroup();
    exit;
}

# maybe the user wants to actually edit a specific group?
if ($field{"action"} eq "doeditgroup") {
    # yup. do it
    &HTMLHeader();
    &DoEditGroup();
    exit;
}

# maybe the user wants to add a group?
if ($field{"action"} eq "addgroup") {
    # yup. do it
    &HTMLHeader();
    &AddGroup();
    exit;
}

# maybe the user wants to actually add the group?
if ($field{"action"} eq "doaddgroup") {
    # yup. do it
    &HTMLHeader();
    &DoAddGroup();
    exit;
}

# maybe the user wants to delete a group?
if ($field{"action"} eq "deletegroup") {
    # yup. do it
    &HTMLHeader();
    &DeleteGroup();
    exit;
}

# maybe to user wants to prune posts?
if ($field{"action"} eq "prune") {
    # yup. do it
    &HTMLHeader();
    &PrunePosts();
    exit;
}

# maybe the user wants to actually prune the posts?
if ($field{"action"} eq "dopruneposts") {
    # yup. do it
    &HTMLHeader();
    &DoPrunePosts();
    exit;
}

# maybe the user wants to email accounts?
if ($field{"action"} eq "emailaccounts") {
    # yup. do it
    &HTMLHeader();
    &EmailAccounts();
    exit;
}

# maybe the user wants to actually email the accounts?
if ($field{"action"} eq "doemailaccounts") {
    # yup. do iy
    &HTMLHeader();
    &DoEmailAccounts();
    exit;
}


# show the 'unknown request' error
&cp_error("You made a request we don't handle");
