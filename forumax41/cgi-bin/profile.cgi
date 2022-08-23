#!/usr/bin/perl
#
# ForuMAX Version 4.1 - profile.cgi
#
# This will handle the profile modifications and registering new accounts
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# use our library files
require "forum_options.pl";
require "forum_lib.pl";

# we need the POSIX layer
use POSIX;

#
# LoginToProfile()
#
# This will show the 'login to profile' page to modify your profile.
#
sub
LoginToProfile() {
    # is an ID already given?
    if ((($field{"id"} . $cookie{"id"}) ne "") and ($field{"parent"} eq "")) {
	# yup. chain to &EditProfile();
	&EditProfile();
	exit;
    }

    # show the page
    &InitPage("");

    printf "<font face=\"$FORUM_FONT\">";

    # parent?
    if ($field{"parent"} eq "yes") {
	# yup. alter the message
	printf "Parents, please log in using your own parental password so we can verify your identity<p>";
    } else {
	printf "Please log in so we can verify your identity<p>";
    }

    printf "<form action=\"profile.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"editprofile\"><table>";

    &UsernamePasswordForm("1");

    printf "</table>";

    # do we have COPPA enabled?
    if ($COPPA_ENABLED eq "YES") {
	# yup. show the 'parent login' thing
	# did we get a 'parent edit' thing?
	if ($field{"parent"} eq "yes") {
	    printf "<input type=\"hidden\" name=\"parent\" value=\"YES\">";
	} else {
	    # no, just show a checkbox
	    printf "<br><input type=\"checkbox\" name=\"parent\">Check this if the user is below 13 and you wish to log in as a parent</input><p>";
	}
    }

    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form></font>";

    &NormalPageEnd();
}

#
# EditProfile()
#
# This will actually show the profile editing dialogs.
#
sub
EditProfile() {
    my $parent_login=0;

    # is COPPA enabled?
    if ($COPPA_ENABLED eq "YES") {
	# yes. is this a parent login?
	if ($field{"parent"} ne "") {
	    # yup. set the flag
	    $parent_login = 1;
	}
    }

    # do we have to login as a parent?
    if ($parent_login eq 0) {
        # no. are username/password fields filled in?
	if ($field{"id"} ne "") { $idstring = $field{"id"}; }
	if ($cookie{"id"} ne "") { $idstring = $cookie{"id"}; }
        if ($idstring eq "") {
	    # yup. generate a password hash
	    $field{"id"}=&HashID ($field{"username"},$field{"password"});
	    $idstring=$field{"id"};
        }

        # validate the id
        &VerifyHash ($idstring);
    } else {
        # yes. do we have an username/password pair?
	if (($field{"username"} . $field{"password"}) eq "") {
	    # no. chain to the Login() procedure
	    &HTMLHeader();
	    &LoginToProfile();
	    exit;
	}

	# verify the password
	&VerifyParentalPassword ($field{"username"},$field{"password"});
    }

    # get the user line
    my $userline=&GetUserRecord($field{"username"});
    my ($tmp,$passw,$the_flags,$nofposts,$fullname,$email,$sig,$extra,$parentinfo)=split(/:/,$userline);
    my ($parent_email,$parent_pwd)=split(/\|\^\|/, $parentinfo);

    # show the page
    &InitPage("");

    printf "<font face=\"$FORUM_FONT\">Editing profile of user <b>%s</b> (<i>%s</i>):<p>",$tmp,&GetMemberStatus ($tmp,$userline,$the_flags);

    printf "<form action=\"profile.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"doeditprofile\">";
    printf "<input type=\"hidden\" name=\"parent_login\" value=\"%s\">",$parent_login;
    # do we have a hash?
    if ($field{"id"} ne "") {
	# yes. show the hash of the username/password
        printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"}; 
    } else {
	# no. add the username and password
        printf "<input type=\"hidden\" name=\"username\" value=\"%s\">",$field{"username"};
        printf "<input type=\"hidden\" name=\"password\" value=\"%s\">",$field{"password"};
    }

    printf "<table><tr><td>Full name</td><td><input type=\"text\" name=\"fullname\" value=\"%s\"></td></tr>",$fullname;
    printf "<tr><td>Email address</td><td><input type=\"text\" name=\"email\" value=\"%s\"></td></tr>",$email;
    printf "<tr><td>Password</td><td><input type=\"password\" name=\"passwd1\" value=\"%s\"></td></tr>",$passw;
    printf "<tr><td>Password again</td><td><input type=\"password\" name=\"passwd2\" value=\"%s\"></td></tr>",$passw;

    # are signatures allowed?
    if ($SIG_ALLOWED eq "YES") {
	# yes. dump in the field
	$sig=&UnHTMLize (&RestoreSpecialChars ($sig));
	$sig=~ s/<br>/\n/gi;
        printf "<tr><td>Signature<br><font size=1>%s<br>Note that the forum restrictions<br>override these!</font></td><td><textarea rows=5 cols=25 name=\"sig\">%s</textarea></td></tr>",&GetSigRestrictions(),$sig;
    }

    # do we have a parental login?
    if ($parent_login ne 0) {
	# yup. show the 'parental password' and 'parent email' fields
	printf "<tr><td>Parent email address</td><td><input type=\"text\" name=\"parent_email\" value=\"%s\"></td></tr>", $parent_email;
	printf "<tr><td>Parent password</td><td><input type=\"password\" name=\"parent_pwd\" value=\"%s\"></td></tr>", $parent_pwd;
	printf "<tr><td>Parent password again</td><td><input type=\"password\" name=\"parent_pwd2\" value=\"%s\"></td></tr>", $parent_pwd;
    }

    my @prof_field = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @prof_type = split(/\|/, $EXTRA_PROFILE_TYPES);
    my @prof_hidden = split(/\|/, $EXTRA_PROFILE_HIDDEN);
    my @prof_perms = split(/\|/, $EXTRA_PROFILE_PERMS);
    my $totalfields = @prof_field;

    my @val = split(/\|\^\|/, $extra);

    for (my $i = 0; $i < $totalfields; $i++) {
	# need to show this field?
	if ($prof_hidden[$i] ne "YES") {
	    # yup. do we have rights to show this field?
	    if ($prof_perms[$i] eq "0") {
		# yup. get the old value
		my $the_val = &RestoreSpecialChars ($val[$i]);
		my $type = $prof_type[$i];
                printf "<tr><td>%s</td><td>", $prof_field[$i];

		# is this a text field?
		if (($type eq "0") or ($type eq "1") or ($type eq "2") or ($type eq "3") or ($type eq "4") or ($type eq "6")) {
		    # yup. just use a text input box
		    printf "<input type=\"text\" name=\"extra$i\" value=\"%s\">",$the_val;
		} else {
		    # is this a gender box?
		    if ($type eq "5") {
			# yup. show it
                        printf "<select name=\"extra$i\">";
			printf "<option value=\"m\"";
			if ($the_val eq "m") { printf " selected" };
                        printf ">Male</option>";
			printf "<option value=\"f\"";
			if ($the_val eq "f") { printf " selected" };
                        printf ">Female</option>";
			printf "<option value=\"?\"";
			if (($the_val eq "?") or ($the_val eq "")) { printf " selected" };
                        printf ">Unspecified</option>";
			printf "</select>";
		    }
		}

                printf "</td></tr>", $prof_field[$i], $the_val;
	    }
	}
    }

    printf "</table><p>";

    # are signatures allowed?
    if ($SIG_ALLOWED eq "YES") {
	# yes. do we refuse to show them for this user?
	if (&check_flag($flags,$FLAG_DENYSIG) ne "0") {
	    # yup. print that before they flood our helpdesk with emails! :)
	    printf "<b>The signature for this user has been disabled. It will never be shown.</b><p>";
	}

        printf "<input type=\"checkbox\" name=\"f_dontchecksig\"";
	# does the user want to never show his/hers sig?
        if (&check_flag($flags,$FLAG_DONTCHECKSIG) ne "0") {
	   # yup. check it.
           printf " checked";
        }
        printf ">Don't check 'show signature' box by default</input><p>";
    }

    # do we have email functionality and do we need a correct email address?
    if (($EMAIL_METHOD ne "0") and ($REQUIRE_VALID_EMAIL eq "YES")) {
	# yes. add it
	printf "<b>Note</b>: because we need a valid email address, a new password will be made up for you and emailed if you change your email address<p>";
    }
    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form></font>";

    &NormalPageEnd();
}

#
# DoEditProfile()
#
# This will actually edit the profile
#
sub
DoEditProfile() {
    # are we logged in as the parent?
    if ($field{"parent_login"} eq 0) {
        # no. validate the user's id
	if ($field{"id"} ne "") { $idstring = $field{"id"}; }
	if ($cookie{"id"} ne "") { $idstring = $cookie{"id"}; }

	&VerifyHash ($idstring);
    } else {
	# yup. validate the parent's id
	&VerifyParentalPassword ($field{"username"},$field{"password"});
    }

    # zap all trailing spaces
    $field{"passwd1"} = &ZapTrailingSpaces ($field{"passwd1"});
    $field{"passwd2"} = &ZapTrailingSpaces ($field{"passwd2"});
    $field{"email"} = &ZapTrailingSpaces ($field{"email"});
    $field{"parent_email"} = &ZapTrailingSpaces ($field{"parent_email"});
    $field{"fullname"} = &ZapTrailingSpaces ($field{"fullname"});
    $field{"sig"} = &FixSpecialChars (&HTMLize (&ZapTrailingSpaces ($field{"sig"})));
    $field{"parent_pwd"} = &ZapTrailingSpaces ($field{"parent_pwd"});
    $field{"parent_pwd2"} = &ZapTrailingSpaces ($field{"parent_pwd2"});

    # check if they are valid
    if (&HasInternalChars ($field{"passwd1"}) ne "0") { &error("Password field contains internal chars"); }
    if (&HasInternalChars ($field{"parent_pwd"}) ne "0") { &error("Parental password field contains internal chars"); }
    if (&HasInternalChars ($field{"email"}) ne "0") { &error("Email field contains internal chars"); }
    if (&HasInternalChars ($field{"parent_email"}) ne "0") { &error("Parental email field contains internal chars"); }
    if (&HasInternalChars ($field{"fullname"}) ne "0") { &error("Full name field contains internal chars"); }
    if (&HasInternalChars ($field{"sig"}) ne "0") { &error("Signature field contains internal chars"); }

    # check whether the passwords are equal
    if ($field{"passwd1"} ne $field{"passwd2"}) {
	# they aren't. die
	&error("Passwords are not equal");
    }

    # check whether the parent passwords are equal
    if ($field{"parent_pwd"} ne $field{"parent_pwd2"}) {
	# they aren't. die
	&error("Parent passwords are not equal");
    }

    # figure out the extra fields
    my @prof_field = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @prof_type = split(/\|/, $EXTRA_PROFILE_TYPES);
    my @prof_hidden = split(/\|/, $EXTRA_PROFILE_HIDDEN);
    my @prof_perms = split(/\|/, $EXTRA_PROFILE_PERMS);
    my $totalfields = @prof_field;

    # get the user line
    my $userline=&GetUserRecord($field{"username"});
    my ($tmp,$passw,$the_flags,$nofposts,$fullname,$email,$sig,$oldextra,$parent_field)=split(/:/,$userline);

    my @prevextra = split (/\|\^\|/, $oldextra);

    my $x = 0; my $new_extra = "";
    foreach $hidden (@prof_hidden) {
	# fix the field
	$field{"extra$x"} = &FixSpecialChars (&ZapTrailingSpaces ($field{"extra$x"}));
	if (&HasInternalChars ($field{"extra$x"}) ne "0") {
	    &cp_error("Extra field contains illegal chars");
	}
	$tmp = $field{"extra$x"}; $tmp=~ s/\|\^\|//g;
	if ($tmp ne $field{"extra$x"}) {
	    &cp_error("Extra field contains illegal chars");
        }

	# is this item hidden, or admin-only?
	if (($hidden ne "NO") or ($prof_perms[$x] eq "1")) {
	    # yup. copy the old value instead
	    $new_extra .= $prevextra[$x];
	} else {
	    # no. copy the new value
	    $new_extra .= $field{"extra$x"}
	}
	$new_extra .= "|^|";

	$x++;
    }

    # zap any HTML code
    $new_extra=~ s/\</&lt;/gi;
    $new_extra=~ s/\>/&gt;/gi;

    # did the email address change?
    my $emailpw="0";
    if ($email ne $field{"email"}) {
	# yup. do we require email validation?
        if (($EMAIL_METHOD ne "0") and ($REQUIRE_VALID_EMAIL eq "YES")) {
            # yes. generate a new password and set the flag
	    $field{"passwd1"}=&GeneratePassword();
	    $emailpw="1";

	    # send an email
            my $subject = "$FORUM_TITLE: Your email address has been changed";
            my $date = &GetTimeDate();
            $date=~ s/\|/ /;
            my $body = qq~
Hello $field{"fullname"},<p>

At $date, someone (probably you) has changed the email address of an account at our forums. Because we need to make certain the email address is valid, we have generated a new password for you, and we sent this email to the new email address. Your information, as we have it, is:<p>

        Your username is '<code>$field{"username"}</code>'<br>
        Your password is '<code>$field{"passwd1"}</code>'<br>
        (The 'es are to improve readability, they should not be entered)<p>

Please change your password as soon as you can, because more people than you *might* be reading this message as well.<p>

We hope you will have a very good time at our forums!<p>

Thank you.<br>
The forum administrator<br>
<a href="$WEBSITE_URI">$WEBSITE_URI</a>
~;

            # send it
            &SendEmail($field{"email"},$subject,$body);
        }
    }

    # zap the DONTCHECKSIG flag and re-add if necesary
    $the_flags=~ s/($FLAG_DONTCHECKSIG)//gi;
    if ($field{"f_dontchecksig"} ne "") { $the_flags .= $FLAG_DONTCHECKSIG; }

    # need to update the parental field?
    if ($field{"parent_login"} ne 0) {
	# yup. do it
	$parent_field = $field{"parent_email"} . "|^|" . $field{"parent_pwd"};
    }


    my $newline=$field{"username"} . ":" . $field{"passwd1"} . ":" . $the_flags . ":" . $nofposts . ":" . $field{"fullname"} . ":" . $field{"email"} . ":" . $field{"sig"} . ":" . $new_extra . ":" . $parent_field;

    # set the new line
    &SetUserRecord($field{"username"},$newline);

    # are we doing parent profile editing?
    if ($field{"parent_login"} eq "") {
	# no. build the identificiation
        if ($USE_COOKIES ne "YES") {
            # geneate a new hash, just in case the user changed his password
            $field{"id"} = &HashID ($field{"username"}, $field{"passwd1"});
        } else {
    	    # build a new cookie
            &SetCookie ("id", &HashID ($field{"username"}, $field{"passwd1"}), 60 * 60 * 31);
	    $field{"id"} = "";
	}
    } else {
	# we do. do we use cookies?
	if ($USE_COOKIES eq "YES") {
	    # yup. kill the cookie
            &SetCookie ("id", "", 0);
	}
	$field{"id"} = "";
    }

    # show the page
    &HTMLHeader();
    &InitPage("");

    printf "<font face=\"$FORUM_FONT\">Profile successfully updated, %s. ",$field{"fullname"};
    if ($emailpw ne "0") {
	printf "<p>A new password has been generated and emailed to the new email address you provided. ";
	print "As a result of this, we've automatically logged you out <p>";
	$field{"id"} = "";
    }

    printf "Click <a href=\"forum.cgi?id=%s\">here</a> to return to the forum</font>", $field{"id"};

    &NormalPageEnd();
}

#
# Register()
#
# This will show the page to register a new account.
#
sub
Register() {
    # do we allow registration?
    if ($ALLOW_REGISTRATION ne "YES") {
	# no. inform user
	&error("Registration of new accounts is disabled");
    }

    # is the coppa stuff enabled?
    if ($COPPA_ENABLED eq "YES") {
	# yeah. query the age
	&GetRegType();
	exit;
    }

    # chain to the normal register procedure
    &NormalRegister();
}

#
# GetRegType()
#
# This will prompt the user between a normal registration or a COPPA one.
#
sub
GetRegType() {
    # do we already have an 'agecheck' cookie?
    if (($cookie{"agecheck"} ne "") and ($cookie{"agecheck"} ne ":::")) {
	# chain to the date verification routine (it will use the cookie)
	&CheckRegType();
	exit;
    }

    # build the page
    &InitPage("");

    # build the page
    printf "<center>Please fill in the fields below:</center><p>";
    printf "<form action=\"profile.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"checkregtype\">";
    printf "<table border=1>";
    printf "<tr><td>Your date of birth</td><td>";
    printf "<select name=\"day\" size=1>";
    printf "<option value=\"1\" selected>1</option>";
    for ($i = 2; $i < 32; $i++) {
        printf "<option value=\"$i\">$i</option>";
    }
    printf "</select>&nbsp;";
    printf "<select name=\"month\" size=1>";
    for ($i = 0; $i < 12; $i++) {
	printf "<option value=\"%s\">%s</option>", $i, $month_name[$i];
    }
    printf "</select>&nbsp;";
    printf "<select name=\"year\" size=1>";
    for ($i = 0; $i < 100; $i++) {
	printf "<option value=\"$i\">%s</option>", $i + 1900;
    }
    printf "</select>";
    printf "</td></tr>";
    printf "<tr><td>Your location</td><td><select name=\"country\">";
    printf qq~ <OPTION VALUE="Afghanistan">Afghanistan</option>
</OPTION><OPTION VALUE="Albania">Albania
</OPTION><OPTION VALUE="Algeria">Algeria
</OPTION><OPTION VALUE="American Samoa">American Samoa
</OPTION><OPTION VALUE="Andorra">Andorra
</OPTION><OPTION VALUE="Angola">Angola
</OPTION><OPTION VALUE="Anguilla">Anguilla
</OPTION><OPTION VALUE="Antarctica">Antarctica
</OPTION><OPTION VALUE="Antigua and Barbuda">Antigua and Barbuda
</OPTION><OPTION VALUE="Argentina">Argentina
</OPTION><OPTION VALUE="Armenia">Armenia
</OPTION><OPTION VALUE="Aruba">Aruba
</OPTION><OPTION VALUE="Australia">Australia
</OPTION><OPTION VALUE="Austria">Austria
</OPTION><OPTION VALUE="Azerbaijan">Azerbaijan
</OPTION><OPTION VALUE="Bahamas">Bahamas
</OPTION><OPTION VALUE="Bahrain">Bahrain
</OPTION><OPTION VALUE="Bangladesh">Bangladesh
</OPTION><OPTION VALUE="Barbados">Barbados
</OPTION><OPTION VALUE="Belarus">Belarus
</OPTION><OPTION VALUE="Belgium">Belgium
</OPTION><OPTION VALUE="Belize">Belize
</OPTION><OPTION VALUE="Bermuda">Bermuda
</OPTION><OPTION VALUE="Bhutan">Bhutan
</OPTION><OPTION VALUE="Bolivia">Bolivia
</OPTION><OPTION VALUE="Bosnia and Herzegovina">Bosnia and Herzegovina
</OPTION><OPTION VALUE="Botswana">Botswana
</OPTION><OPTION VALUE="Bouvet Island">Bouvet Island
</OPTION><OPTION VALUE="Brazil">Brazil
</OPTION><OPTION VALUE="British Indian Ocean Territory">British Indian Ocean Territory
</OPTION><OPTION VALUE="Brunei Darussalam">Brunei Darussalam
</OPTION><OPTION VALUE="Bulgaria">Bulgaria
</OPTION><OPTION VALUE="Burkina Faso">Burkina Faso
</OPTION><OPTION VALUE="Burundi">Burundi
</OPTION><OPTION VALUE="Cambodia">Cambodia
</OPTION><OPTION VALUE="Cameroon">Cameroon
</OPTION><OPTION VALUE="Canada">Canada
</OPTION><OPTION VALUE="Cape Verde">Cape Verde
</OPTION><OPTION VALUE="Cayman Islands">Cayman Islands
</OPTION><OPTION VALUE="Central African Republic">Central African Republic
</OPTION><OPTION VALUE="Chad">Chad
</OPTION><OPTION VALUE="Chile">Chile
</OPTION><OPTION VALUE="China">China
</OPTION><OPTION VALUE="Christmas Island">Christmas Island
</OPTION><OPTION VALUE="Cocos (Keeling Islands)">Cocos (Keeling Islands)
</OPTION><OPTION VALUE="Colombia">Colombia
</OPTION><OPTION VALUE="Comoros">Comoros
</OPTION><OPTION VALUE="Congo">Congo
</OPTION><OPTION VALUE="Cook Islands">Cook Islands
</OPTION><OPTION VALUE="Costa Rica">Costa Rica
</OPTION><OPTION VALUE="Cote D'Ivoire (Ivory Coast)">Cote D'Ivoire (Ivory Coast)
</OPTION><OPTION VALUE="Croatia (Hrvatska)">Croatia (Hrvatska)
</OPTION><OPTION VALUE="Cuba">Cuba
</OPTION><OPTION VALUE="Cyprus">Cyprus
</OPTION><OPTION VALUE="Czech Republic">Czech Republic
</OPTION><OPTION VALUE="Denmark">Denmark
</OPTION><OPTION VALUE="Djibouti">Djibouti
</OPTION><OPTION VALUE="Dominican Republic">Dominican Republic
</OPTION><OPTION VALUE="Dominica">Dominica
</OPTION><OPTION VALUE="East Timor">East Timor
</OPTION><OPTION VALUE="Ecuador">Ecuador
</OPTION><OPTION VALUE="Egypt">Egypt
</OPTION><OPTION VALUE="El Salvador">El Salvador
</OPTION><OPTION VALUE="Equatorial Guinea">Equatorial Guinea
</OPTION><OPTION VALUE="Eritrea">Eritrea
</OPTION><OPTION VALUE="Estonia">Estonia
</OPTION><OPTION VALUE="Ethiopia">Ethiopia
</OPTION><OPTION VALUE="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)
</OPTION><OPTION VALUE="Faroe Islands">Faroe Islands
</OPTION><OPTION VALUE="Fiji">Fiji
</OPTION><OPTION VALUE="Finland">Finland
</OPTION><OPTION VALUE="France, Metropolitan">France, Metropolitan
</OPTION><OPTION VALUE="France">France
</OPTION><OPTION VALUE="French Guiana">French Guiana
</OPTION><OPTION VALUE="French Polynesia">French Polynesia
</OPTION><OPTION VALUE="French Southern Territories">French Southern Territories
</OPTION><OPTION VALUE="Gabon">Gabon
</OPTION><OPTION VALUE="Gambia">Gambia
</OPTION><OPTION VALUE="Georgia">Georgia
</OPTION><OPTION VALUE="Germany">Germany
</OPTION><OPTION VALUE="Ghana">Ghana
</OPTION><OPTION VALUE="Gibraltar">Gibraltar
</OPTION><OPTION VALUE="Greece">Greece
</OPTION><OPTION VALUE="Greenland">Greenland
</OPTION><OPTION VALUE="Grenada">Grenada
</OPTION><OPTION VALUE="Guadeloupe">Guadeloupe
</OPTION><OPTION VALUE="Guam">Guam
</OPTION><OPTION VALUE="Guatemala">Guatemala
</OPTION><OPTION VALUE="Guinea-Bissau">Guinea-Bissau
</OPTION><OPTION VALUE="Guinea">Guinea
</OPTION><OPTION VALUE="Guyana">Guyana
</OPTION><OPTION VALUE="Haiti">Haiti
</OPTION><OPTION VALUE="Heard and McDonald Islands">Heard and McDonald Islands
</OPTION><OPTION VALUE="Honduras">Honduras
</OPTION><OPTION VALUE="Hong Kong">Hong Kong
</OPTION><OPTION VALUE="Hungary">Hungary
</OPTION><OPTION VALUE="Iceland">Iceland
</OPTION><OPTION VALUE="India">India
</OPTION><OPTION VALUE="Indonesia">Indonesia
</OPTION><OPTION VALUE="Iran">Iran
</OPTION><OPTION VALUE="Iraq">Iraq
</OPTION><OPTION VALUE="Ireland">Ireland
</OPTION><OPTION VALUE="Israel">Israel
</OPTION><OPTION VALUE="Italy">Italy
</OPTION><OPTION VALUE="Jamaica">Jamaica
</OPTION><OPTION VALUE="Japan">Japan
</OPTION><OPTION VALUE="Jordan">Jordan
</OPTION><OPTION VALUE="Kazakhstan">Kazakhstan
</OPTION><OPTION VALUE="Kenya">Kenya
</OPTION><OPTION VALUE="Kiribati">Kiribati
</OPTION><OPTION VALUE="Korea (North)">Korea (North)
</OPTION><OPTION VALUE="Korea (South)">Korea (South)
</OPTION><OPTION VALUE="Kuwait">Kuwait
</OPTION><OPTION VALUE="Kyrgyzstan">Kyrgyzstan
</OPTION><OPTION VALUE="Laos">Laos
</OPTION><OPTION VALUE="Latvia">Latvia
</OPTION><OPTION VALUE="Lebanon">Lebanon
</OPTION><OPTION VALUE="Lesotho">Lesotho
</OPTION><OPTION VALUE="Liberia">Liberia
</OPTION><OPTION VALUE="Libya">Libya
</OPTION><OPTION VALUE="Liechtenstein">Liechtenstein
</OPTION><OPTION VALUE="Lithuania">Lithuania
</OPTION><OPTION VALUE="Luxembourg">Luxembourg
</OPTION><OPTION VALUE="Macau">Macau
</OPTION><OPTION VALUE="Macedonia">Macedonia
</OPTION><OPTION VALUE="Madagascar">Madagascar
</OPTION><OPTION VALUE="Malawi">Malawi
</OPTION><OPTION VALUE="Malaysia">Malaysia
</OPTION><OPTION VALUE="Maldives">Maldives
</OPTION><OPTION VALUE="Mali">Mali
</OPTION><OPTION VALUE="Malta">Malta
</OPTION><OPTION VALUE="Marshall Islands">Marshall Islands
</OPTION><OPTION VALUE="Martinique">Martinique
</OPTION><OPTION VALUE="Mauritania">Mauritania
</OPTION><OPTION VALUE="Mauritius">Mauritius
</OPTION><OPTION VALUE="Mayotte">Mayotte
</OPTION><OPTION VALUE="Mexico">Mexico
</OPTION><OPTION VALUE="Micronesia">Micronesia
</OPTION><OPTION VALUE="Moldova">Moldova
</OPTION><OPTION VALUE="Monaco">Monaco
</OPTION><OPTION VALUE="Mongolia">Mongolia
</OPTION><OPTION VALUE="Montserrat">Montserrat
</OPTION><OPTION VALUE="Morocco">Morocco
</OPTION><OPTION VALUE="Mozambique">Mozambique
</OPTION><OPTION VALUE="Myanmar">Myanmar
</OPTION><OPTION VALUE="Namibia">Namibia
</OPTION><OPTION VALUE="Nauru">Nauru
</OPTION><OPTION VALUE="Nepal">Nepal
</OPTION><OPTION VALUE="Netherlands Antilles">Netherlands Antilles
</OPTION><OPTION VALUE="Netherlands">Netherlands
</OPTION><OPTION VALUE="New Caledonia">New Caledonia
</OPTION><OPTION VALUE="New Zealand">New Zealand
</OPTION><OPTION VALUE="Nicaragua">Nicaragua
</OPTION><OPTION VALUE="Nigeria">Nigeria
</OPTION><OPTION VALUE="Niger">Niger
</OPTION><OPTION VALUE="Niue">Niue
</OPTION><OPTION VALUE="Norfolk Island">Norfolk Island
</OPTION><OPTION VALUE="Northern Mariana Islands">Northern Mariana Islands
</OPTION><OPTION VALUE="Norway">Norway
</OPTION><OPTION VALUE="Oman">Oman
</OPTION><OPTION VALUE="Pakistan">Pakistan
</OPTION><OPTION VALUE="Palau">Palau
</OPTION><OPTION VALUE="Panama">Panama
</OPTION><OPTION VALUE="Papua New Guinea">Papua New Guinea
</OPTION><OPTION VALUE="Paraguay">Paraguay
</OPTION><OPTION VALUE="Peru">Peru
</OPTION><OPTION VALUE="Philippines">Philippines
</OPTION><OPTION VALUE="Pitcairn">Pitcairn
</OPTION><OPTION VALUE="Poland">Poland
</OPTION><OPTION VALUE="Portugal">Portugal
</OPTION><OPTION VALUE="Puerto Rico">Puerto Rico
</OPTION><OPTION VALUE="Qatar">Qatar
</OPTION><OPTION VALUE="Reunion">Reunion
</OPTION><OPTION VALUE="Romania">Romania
</OPTION><OPTION VALUE="Russian Federation">Russian Federation
</OPTION><OPTION VALUE="Rwanda">Rwanda
</OPTION><OPTION VALUE="S. Georgia and S. Sandwich Isls.">S. Georgia and S. Sandwich Isls.
</OPTION><OPTION VALUE="Saint Kitts and Nevis">Saint Kitts and Nevis
</OPTION><OPTION VALUE="Saint Lucia">Saint Lucia
</OPTION><OPTION VALUE="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines
</OPTION><OPTION VALUE="Samoa">Samoa
</OPTION><OPTION VALUE="San Marino">San Marino
</OPTION><OPTION VALUE="Sao Tome and Principe">Sao Tome and Principe
</OPTION><OPTION VALUE="Saudi Arabia">Saudi Arabia
</OPTION><OPTION VALUE="Senegal">Senegal
</OPTION><OPTION VALUE="Seychelles">Seychelles
</OPTION><OPTION VALUE="Sierra Leone">Sierra Leone
</OPTION><OPTION VALUE="Singapore">Singapore
</OPTION><OPTION VALUE="Slovak Republic">Slovak Republic
</OPTION><OPTION VALUE="Slovenia">Slovenia
</OPTION><OPTION VALUE="Solomon Islands">Solomon Islands
</OPTION><OPTION VALUE="Somalia">Somalia
</OPTION><OPTION VALUE="South Africa">South Africa
</OPTION><OPTION VALUE="Spain">Spain
</OPTION><OPTION VALUE="Sri Lanka">Sri Lanka
</OPTION><OPTION VALUE="St. Helena">St. Helena
</OPTION><OPTION VALUE="St. Pierre and Miquelon">St. Pierre and Miquelon
</OPTION><OPTION VALUE="Sudan">Sudan
</OPTION><OPTION VALUE="Suriname">Suriname
</OPTION><OPTION VALUE="Svalbard and Jan Mayen Islands">Svalbard and Jan Mayen Islands
</OPTION><OPTION VALUE="Swaziland">Swaziland
</OPTION><OPTION VALUE="Sweden">Sweden
</OPTION><OPTION VALUE="Switzerland">Switzerland
</OPTION><OPTION VALUE="Syria">Syria
</OPTION><OPTION VALUE="Taiwan">Taiwan
</OPTION><OPTION VALUE="Tajikistan">Tajikistan
</OPTION><OPTION VALUE="Tanzania">Tanzania
</OPTION><OPTION VALUE="Thailand">Thailand
</OPTION><OPTION VALUE="Togo">Togo
</OPTION><OPTION VALUE="Tokelau">Tokelau
</OPTION><OPTION VALUE="Tonga">Tonga
</OPTION><OPTION VALUE="Trinidad and Tobago">Trinidad and Tobago
</OPTION><OPTION VALUE="Tunisia">Tunisia
</OPTION><OPTION VALUE="Turkey">Turkey
</OPTION><OPTION VALUE="Turkmenistan">Turkmenistan
</OPTION><OPTION VALUE="Turks and Caicos Islands">Turks and Caicos Islands
</OPTION><OPTION VALUE="Tuvalu">Tuvalu
</OPTION><OPTION VALUE="US Minor Outlying Islands">US Minor Outlying Islands
</OPTION><OPTION VALUE="Uganda">Uganda
</OPTION><OPTION VALUE="Ukraine">Ukraine
</OPTION><OPTION VALUE="United Arab Emirates"> United Arab Emirates
</OPTION><OPTION VALUE="United Kingdom">United Kingdom
</OPTION><OPTION VALUE="United States" SELECTED>United States
</OPTION><OPTION VALUE="Uruguay">Uruguay
</OPTION><OPTION VALUE="Uzbekistan">Uzbekistan
</OPTION><OPTION VALUE="Vanuatu">Vanuatu
</OPTION><OPTION VALUE="Vatican City State">Vatican City State
</OPTION><OPTION VALUE="Venezuela">Venezuela
</OPTION><OPTION VALUE="Vietnam">Vietnam
</OPTION><OPTION VALUE="Virgin Islands (British)">Virgin Islands (British)
</OPTION><OPTION VALUE="Virgin Islands (US)">Virgin Islands (US)
</OPTION><OPTION VALUE="Wallis and Futuna Islands">Wallis and Futuna Islands
</OPTION><OPTION VALUE="Western Sahara">Western Sahara
</OPTION><OPTION VALUE="Yemen">Yemen
</OPTION><OPTION VALUE="Yugoslavia">Yugoslavia
</OPTION><OPTION VALUE="Zaire">Zaire
</OPTION><OPTION VALUE="Zambia">Zambia
</OPTION><OPTION VALUE="Zimbabwe">Zimbabwe
~;
    printf "</select></td></tr>";
    printf "</table>";

    printf "<p><input type=\"submit\" value=\"OK\">";
    printf "</form>";

    # end it
    &NormalPageEnd();
}

#
# NormalRegister()
#
# This will carry out the normal registering procedure.
#
sub
NormalRegister() {
    # show the page
    &InitPage("");

    printf "Please submit the following information in order to gain an account to this forum. <b>NOTE: BY REGISTERING, YOU AGREE WITH OUR POLICIES BELOW!</b><p>";

    printf "<hr>";
    printf &UnHTMLize($FORUM_POLICIES);
    printf "<hr>";

    printf "<form action=\"profile.cgi\" method=\"post\">";

    printf "<input type=\"hidden\" name=\"action\" value=\"doregister\">";

    printf "<table><tr><td>User name</td><td><input type=\"text\" name=\"username\"></td></tr>";
    printf "<tr><td>Full name</td><td><input type=\"text\" name=\"fullname\"></td></tr>";

    printf "<tr><td>Email address</td><td><input type=\"text\" name=\"email\"></td></tr>";

    # do we have email functionality and do we need a correct email address?
    if (($EMAIL_METHOD ne "0") and ($REQUIRE_VALID_EMAIL eq "YES")) {
	# yes. show it
        printf "<tr><td>Password</td><td><i>Your password will be emailed to you</i></td></tr>";
    } else {
	# no. just prompt for it
        printf "<tr><td>Password</td><td><input type=\"password\" name=\"passwd1\"></td></tr>";
        printf "<tr><td>Password again</td><td><input type=\"password\" name=\"passwd2\"></td></tr>";
    }

    printf "</table><p>";

    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form>";

    &NormalPageEnd();
}

#
# COPPARegister()
#
# This will do the COPPA registration procedure.
#
sub
COPPARegister() {
    # show the page
    &InitPage("");

    printf "<center><table width=\"80%\" border=1>";
    printf "<tr><td>";
    printf "<form action=\"profile.cgi\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"docopparegister\">";

    $tmp=&UnHTMLize ($COPPA_KID_INSTR);
    $tmp=~ s/\|PARENT_EMAIL\|/<input type=\"text\" name=\"parent_email\"><p><input type=\"submit\" value=\"Click here to send the required information to the parent or guardian\">/gi;
    print $tmp;

    printf "</form></tr></td></table></center>";
}

#
# DoCOPPARegister();
#
# This will actually register a person.
#
sub
DoCOPPARegister() {
    # do we allow registration?
    if ($ALLOW_REGISTRATION ne "YES") {
	# no. inform user
	&error("Registration of new accounts is disabled");
    }

    # do we have the ability to send emails?
    if ($EMAIL_METHOD eq "0") {
	# no. complain
	&error("Sorry, but the forum hasn't been configured for email sending. This is required for COPPA registrations, however. Please contact the site's administrator");
    }

    $subject = "Account registration of your child";
    $body = $COPPA_PARENT_INSTR;

    # send it
    &SendEmail($field{"parent_email"},$subject,$body);

    # show the page
    &InitPage("");
    printf "Thank you! We have emailed your parents the correct procedure that is needed in order to allow you to legally access thsi site.<p>We hope to see you here soon!";

    &NormalPageEnd();
}

#
# DoRegister();
#
# This will actually register a person.
#
sub
DoRegister() {
    # do we allow registration?
    if ($ALLOW_REGISTRATION ne "YES") {
	# no. inform user
	&error("Registration of new accounts is disabled");
    }

    my @nofextras = split(/\|/, $EXTRA_PROFILE_FIELDS);

    # zap all spaces
    $field{"username"}=&ZapTrailingSpaces($field{"username"});
    $field{"passwd1"}=&ZapTrailingSpaces($field{"passwd1"});
    $field{"passwd2"}=&ZapTrailingSpaces($field{"passwd2"});
    $field{"email"}=&ZapTrailingSpaces($field{"email"});
    $field{"fullname"}=&ZapTrailingSpaces($field{"fullname"});

    # save the fields
    $old_username=$field{"username"};
    $old_passwd1=$field{"passwd1"};
    $old_email=$field{"email"};
    $old_fullname=$field{"fullname"};

    # zap all inappropiate chars
    $field{"username"} = &ZapIllegalChars($field{"username"});
    $field{"passwd1"} = &ZapIllegalChars($field{"passwd1"});
    $field{"email"} = &ZapIllegalChars($field{"email"});
    $field{"fullname"} = &ZapIllegalChars($field{"fullname"});

    # if they were changed, complain
    if (($old_username ne $field{"username"}) or ($old_passwd1 ne $field{"passwd1"}) or ($old_email ne $field{"email"})) {
	# they changed, and thus must have contained illegal chars!
	&error("You have used illegal charachters in one of the fields");
    }

    # check whether the passwords are equal
    if ($field{"passwd1"} ne $field{"passwd2"}) {
	# they aren't. die
	&error("Passwords are not equal (bear in mind that newlines, tabs and colons are not allowed)");
    }

    # do we have email functionality and do we need a correct email address?
    if (($EMAIL_METHOD ne "0") and ($REQUIRE_VALID_EMAIL eq "YES")) {
	# yup. generate a random one
	$field{"passwd1"}=&GeneratePassword();
    }

    # check for empty fields
    if (($field{"username"} eq "") or ($field{"passwd1"} eq "") or ($field{"fullname"} eq "") or ($field{"email"} eq "")) {	
	&error("All fields must be filled in");
    }

    # get the user line
    my $userline=&GetUserRecord($field{"username"});
    # does the user already exists?
    if ($userline ne "") {
	# yup. die
	&error("We already have an account that goes by that name");
    }

    # does the user name contain a censored word?
    if (&CensorPost ($field{"username"}) ne $field{"username"}) {
	# yes. it does. complain
	&error("Sorry, but this user name contains a censored word");
    }

    # check the email address
    my ($e1,$e2)=split(/\@/,$field{"email"});
    if ($e2 eq "") {
	# it's invalid!
	&error("Email address is not valid");
    }

    # do we require unique email addresses?
    if ($REQUIRE_UNIQUE_EMAIL eq "YES") {
	# yes. get all accounts in an array
	my @accounts = &GetAllAccounts();

	# browse them one by one
	foreach $account (@accounts) {
	    # get the email
	    my ($tmp,$tmp2,$tmp3,$tmp4,$tmp5,$email)=split(/:/,$account);

	    # is it equal to ours?
	    if ($email eq $field{"email"}) {
		# yup. complain	
		&error("Sorry, but your email address is already registered");
	    }
	}
    }

    # is this email address banned?
    if (&IsEmailBanned ($field{"email"}) ne 0) {
	# yup. complain
	&error("Sorry, but this email address is banned. You may not register an account with it");
    }

    # set up the flags
    my $theflags = "";
    if ($field{"f_dontchecksig"} ne "") { $theflags .= $FLAG_DONTCHECKSIG; }

    # set up the extra field
    my $extra = "";
    my @prof_field = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @prof_type = split(/\|/, $EXTRA_PROFILE_TYPES);
    my @prof_hidden = split(/\|/, $EXTRA_PROFILE_HIDDEN);
    my @prof_perms = split(/\|/, $EXTRA_PROFILE_PERMS);
    my $totalfields = @prof_field;

    # trace all profile types, in search for the 'date joined' field
    foreach $type (@prof_type) {
	# date joined?
	if ($type eq 8) {
	    # yup. add the date
	    my $now = &GetTimeDate();
	    my ($date) = split (/\|/, $now);

	    # add this field
	    $extra .= $date . "|^|";
	} else {
	    # no. just blank the field out
	    $extra .= "|^|";
	}
    }

    # construct the record
    my $therecord=$field{"username"} . ":" . $field{"passwd1"} . ":" . $theflags . ":0:" . $field{"fullname"} . ":" . $field{"email"} . "::" . $extra . ":";

    # add the new line
    &SetUserRecord($field{"username"},$therecord);

    # do we have email functionality and do we need a correct email address?
    if (($EMAIL_METHOD ne "0") and ($REQUIRE_VALID_EMAIL eq "YES")) {
	# yup. email the user now
        my $subject = "$FORUM_TITLE: Your account";
        my $date = &GetTimeDate();
        $date=~ s/\|/ /;
        my $body = qq~
Hello $field{"fullname"},<p>

At $date, someone (probably you) has applied for a new account on our forums. Because we need to make certain the email address is valid, we send this email to whatever address you registered with. Your information, as we have it, is:<p>

        Your username is '<code>$field{"username"}</code>'<br>
        Your password is '<code>$field{"passwd1"}</code>'<br>
        (The 'es are to improve readability, they should not be entered)<p>

Please change your password as soon as you can, because more people than you *might* be reading this message as well.<p>

We hope you will have a very good time at our forums!<p>

Thank you.<br>
The forum administrator<br>
<a href="$WEBSITE_URI">$WEBSITE_URI</a>
~;

        # send it
        &SendEmail($field{"email"},$subject,$body);
    }

    # show the page
    &InitPage("");

    printf "Thank you, %s, your account has been successfully created. Click <a href=\"forum.cgi\">here</a> to return to the forum<p>",$field{"username"};
    printf "If you want to supply extra information about yourself, you can <a href=\"profile.cgi?action=profile\">edit your profile</a>. This is not required, but it is nice to show the fellow-members some more information about yourself. You can also change your password here.";

    &NormalPageEnd();
}

#
# LostPW()
#
# This will show the 'lost password' dialogs.
#
sub
LostPW() {
    # set up the page
    &InitPage();

    printf "If you have forgotten your password, please fill in your account name, and we will email it to you.<p>";

    printf "<form method=\"post\" action=\"profile.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dolostpw\">";

    printf "<table>";
    printf "<tr><td>User name</td><td><input type=\"text\" name=\"username\"></td></tr>";
    printf "</table>";

    printf "<p><input type=\"submit\" value=\"Email me my password\">";

    printf "</form>";

    # terminate the page
    &NormalPageEnd();
}

#
# DoLostPW()
#
# This will actually email the user the lost password.
#
sub
DoLostPW() {
    # get the user information
    my $record = &GetUserRecord($field{"username"});

    # does this user exists?
    if ($record eq "") {
	# no. die
        &error("This account doesn't exist");
    }

    # get the password and email addy
    my ($tmp1,$pwd,$tmp3,$tmp4,$tmp5,$email)=split(/:/,$record);

    # craft the email message
    my $subject = "$FORUM_TITLE: Lost forum password request";
    my $date = &GetTimeDate();
    $date=~ s/\|/ /;
    my $body = qq~
Hello!<p>

At $date, someone (probably you) has requested the password of account '$field{"username"}' in our forums. This email has been sent you because your mail address is registered with the account. Your password is:<p>

    "<code>$pwd</code>"<p>

    (The <code>"</code>'s are only to improve the readability, they should not be entered)<p>

We advise you to change this as soon as you can, since this email may have been read by other people.<p>

Thank you.<br>
The forum administrator<br>
<a href="$WEBSITE_URI">$WEBSITE_URI</a>
~;

    # send it
    &SendEmail($email,$subject,$body);

    # set up the page
    &InitPage();

    printf "The email has been successfully sent. Please check your email to get your password. Click <a href=\"forum.cgi\">here</a> to return to the forums";

    # terminate the page
    &NormalPageEnd();
}

if ($field{"action"} eq "profile") {
    # the user wants to edit the profile. do it
    &HTMLHeader();
    &LoginToProfile();

    # get outta here!
    exit;
}

if ($field{"action"} eq "editprofile" ) {
    # the user wants to verify his access to modify his profile. do it
    &HTMLHeader();
    &EditProfile();

    # get outta here!
    exit;
}

if ($field{"action"} eq "doeditprofile" ) {
    # the user wants to really modify his profile. do it
    &DoEditProfile();

    # get outta here!
    exit;
}

#
# CheckRegType()
#
# This will check whether the user needs a COPPA registration.
#
sub
CheckRegType() {
    # do we already have the cookie?
    if ($cookie{"agecheck"} eq "") {
       # no. create the cookie (it will expire in one month)
       &SetCookie ("agecheck", $field{"country"} . ":" . $field{"day"} . ":" . $field{"month"} . ":" . $field{"year"}, 60 * 60 * 31);
    } else {
	# make sure the cookie overrides our values ;)
	my ($country, $day, $month, $year) = split (/:/, $cookie{"agecheck"});
	$field{"country"} = $country;
	$field{"day"} = $day;
	$field{"month"} = $month;
	$field{"year"} = $year;
    }

    # send the HTML header
    &HTMLHeader();

    # does the user live in the USA?
    if ($field{"country"} eq "United States") {
	# yup. is he below 13?
	my $birthday = mktime (0, 0, 0, $field{"day"}, $field{"month"}, $field{"year"});

	# get today's time
        my $now = time();

	# grab the difference
	my $diff = difftime ($now, $birthday);

	# calculate it in years
	$diff = ($diff / (60 * 60 * 24 * 365));

	# is that below 13?
	if ($diff < 13) {
	    # yup. use COPPA
	    &COPPARegister();
	    return;
	}
    }

    # do a normal registration
    &NormalRegister();
}

if ($field{"action"} eq "register" ) {
    # the user wants to register a new account. do it
    &HTMLHeader();
    &Register();

    # get outta here!
    exit;
}

#
# Login()
#
# This will show the 'login to forum' page.
#
sub
Login() {
    # show the page
    &InitPage("");

    printf "<font face=\"$FORUM_FONT\">When you log in to the forum system, you won't be prompted anymore for your username or password. You don't need to logout, simply closing the browser window is enough.<br>";

    printf "<form action=\"profile.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dologin\">";

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("1");

    # do we use cookies?
    if ($USE_COOKIES eq "YES") {
	# yes. show the drop down box
	printf "<tr><td>Cookie duration</td><td>";
        printf "<select name=\"duration\">";
	printf "<option value=\"%s\">1 hour</option>", 60 * 60;
	printf "<option value=\"%s\">1 day</option>", 24 * 60 * 60;
	printf "<option value=\"%s\">1 week</option>", 7 * 24 * 60 * 60;
	printf "<option value=\"%s\">1 month</option>", 31 * 24 * 60 * 60;
	printf "<option value=\"%s\">1 year</option>", 365 * 24 * 60 * 60;
	printf "</option></td></tr>";
    }

    printf "</table>";
    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form></font>";

    &NormalPageEnd();
}

#
# DoLogin()
#
# This will perform the actual logging in to the forum.
#
sub
DoLogin() {
    # make a hash
    $field{"id"} = &HashID($field{"username"},$field{"password"});

    # verify it
    if (&VerifyHash($field{"id"}) eq 0) {
	# this failed. die
	&error($ERROR_ACCESSDENIED);
    }

    # if the account is disabled, deny access
    if (&check_flag($flags,$FLAG_DISABLED) ne 0) {
        &error ($ERROR_POSTDISABLED);
    }

    # do we need to build a cookie?
    if ($USE_COOKIES eq "YES") {
	# yup. do it
	&SetCookie ("id", $field{"id"}, $field{"duration"});
	$field{"id"} = "";
    }

    # now, we need to redirect to the forum itself
    &HTMLHeader();
    $url = "forum.cgi?id=" . $field{"id"};
    &InitPage("","",$url);
    printf "<font face=\"$FORUM_FONT\">You have successfully been logged in. Please wait 2 seconds or click <a href=\"$url\">here</a> to continue to the forums.</font>";
    &NormalPageEnd();
}

#
# Logout()
#
# This will log the currently logged-in user out
#
sub
Logout() {
    # do we use cookies?
    if ($USE_COOKIES eq "YES") {
	# yup. remove it
	&SetCookie ("id", "", 0);
    }

    # now, chain to the 'yay, we logged out' page
    &HTMLHeader();
    $url = "forum.cgi?id=";
    &InitPage("","",$url);
    printf "<font face=\"$FORUM_FONT\">You have successfully been logged out. Please wait 2 seconds or click <a href=\"$url\">here</a> to continue to the forums.</font>";
    &NormalPageEnd();
}


if ($field{"action"} eq "doregister" ) {
    # the user wants to actually register the account. do it
    &HTMLHeader();
    &DoRegister();

    # get outta here!
    exit;
}

if ($field{"action"} eq "normalregister") {
    # the user wants to have a normal registration. do it
    &HTMLHeader();
    &NormalRegister();

    # get outta here
    exit;
}

if ($field{"action"} eq "copparegister" ) {
    # the user wants to register a new account under 13. do it
    &HTMLHeader();
    &COPPARegister();

    # get outta here!
    exit;
}

if ($field{"action"} eq "docopparegister" ) {
    # the user wants to actually  register the account under 13. do it
    &HTMLHeader();
    &DoCOPPARegister();

    # get outta here!
    exit;
}

if ($field{"action"} eq "lostpw") {
    # the user wants to retrieve his password. do it
    &HTMLHeader();
    &LostPW();

    # get outta here
    exit;
}

if ($field{"action"} eq "dolostpw") {
    # the user wants to retrieve his password. do it
    &HTMLHeader();
    &DoLostPW();

    # get outta here
    exit;
}

if ($field{"action"} eq "checkregtype") {
    # the user wants to check the registration type. do it
    &CheckRegType();

    # get outta here
    exit;
}

if ($field{"action"} eq "login") {
    # the user wants ot login. do it
    &HTMLHeader();
    &Login();

    # get outta here
    exit;
}

if ($field{"action"} eq "dologin") {
    # the user wants ot login. do it
    &DoLogin();

    # get outta here
    exit;
}

if ($field{"action"} eq "logout") {
    # the user wants to log out. do it
    &Logout();

    # get outta here
    exit;
}

# show the 'unknown request' error
&error("You made a request we don't handle");
