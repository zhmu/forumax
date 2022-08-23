#!/usr/bin/perl
#
# ForuMAX Version 4.1 - finger.cgi
#
# This will handle information lookups.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# use our library files
require "forum_options.pl";
require "forum_lib.pl";

# $ERROR_xxx are error messages
$ERROR_NOSUCHUSER="User not found";

# add the HTTP header
&HTMLHeader();

# was an user name given?
if ($field{"accountname"} ne "") {
    # yup. get the account line
    my $account_info=&GetUserRecord($field{"accountname"});

    my @prof_fields = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @prof_type = split(/\|/, $EXTRA_PROFILE_TYPES);
    my @prof_hidden = split(/\|/, $EXTRA_PROFILE_HIDDEN);
    my @prof_perms = split(/\|/, $EXTRA_PROFILE_PERMS);

    # was there info?
    if ($account_info eq "") {
        # yup. say that's bad
        &error($ERROR_NOSUCHUSER);
    }

    # split it
    my ($username,$passwd,$flags,$nofposts,$fullname,$email,$sig,$extra)=split(/:/,$account_info);

    # init the page
    &InitPage("");

    printf "<h1><font face=\"$FORUM_FONT\">Information on user %s</h1>",$username;

    printf "<table width=\"100%\">";
    printf "<tr><td width=\"25%\">User name</td><td width=\"75%\">%s</td></tr>",$username;
    printf "<tr><td>Status</td><td>%s</td></tr>",&GetMemberStatus($username,$account_info,$flags);
    printf "<tr><td>Full name</td><td>%s</td></tr>",$fullname;
    printf "<tr><td>Email address</td><td><a href=\"mailto:%s\">%s</a></td></tr>",$email,$email;
    printf "<tr><td>Number of posts</td><td>%s</td></tr>",$nofposts;

    # add all fields
    my @vals = split(/\|\^\|/, $extra);
    my $x = "0";
    foreach $type (@prof_type) {
        # is this one hidden?
        if ($prof_hidden[$x] ne "YES") {
	    # no. show it
            printf "<tr><td>%s</td><td>", $prof_fields[$x];

	    my $val = &RestoreSpecialChars ($vals[$x]);

	    # is this text, ICQ, AIM, Yahoo! ID or joining date?
	    if (($prof_type[$x] eq 0) or ($prof_type[$x] eq 2) or ($prof_type[$x] eq 3) or ($prof_type[$x] eq 4) or ($prof_type[$x] eq 8)) {
	        # yup. show it
	        printf $val;
	    }

	    # is it an (homepage) URL?
	    if (($prof_type[$x] eq 1) or ($prof_type[$x] eq 6)) {
	       # yup. show it
	       printf "<a href=\"$val\">$val</a>";
	    }

	    # is it a gender?
	    if ($prof_type[$x] eq 5) {
	        if ($val eq "m") {
	            printf "Male";
	        } else {
		    if ($val eq "f") {
		        printf "Female";
		    } else {
		        printf "Unspecified";
		    }
	        }
	    }

            printf "</td></tr>";
        }
        $x++;
    }
    printf "<tr valign=\"top\"><td>Moderator of</td><td><ul>";

    # grab all category information
    my @cats = &GetCats();

    # handle them all
    my $ismod = 0;
    foreach $cat (@cats) {
	# get the category name and super moderators
	my ($name,$catno,$supermods)=split(/:/,$cat);

        # are we in the supermod list?
        if (&IsUserInGroup($username,$supermods) ne 0) {
	    # yup. show the information
	    printf "<li>Category: <a href=\"forum.cgi?action=showcat&id=%s&cat=%s\">%s</a></li>", $field{"id"}, &TransformForBrowser ($name), $name;
	}
    }

    # grab all forum information
    my @forums = &GetForums();

    # if we are a mod at one of them, add it
    foreach $forum (@forums) {
	# get the forum name
	my ($forumname) = split (/:/, $forum);

	# are we a moderator here?
	if (&IsInArray ($username, &GetForumMods ($forumname)) ne 0) {
	    # yes. add it to the list
	    printf "<li>Forum: <a href=\"forumview.cgi?action=showforum&id=%s&forum=%s\">%s</a></li>", $field{"id"}, &TransformForBrowser ($forumname), $forumname;
	    $ismod = 1;
	}
    }

    # are we a mod?
    if ($ismod eq 0) {
	# no. show the 'nothing' one
        printf "<li><i>Nothing</i></li>";
    }

    # now, do the group memberships
    printf "</ul></td></tr><tr valign=\"top\"><td>Group memberships</td><td><ul>";

    # grab all groups
    my $ingroup = 0; 
    my @groups = &GetAllGroups();

    # handle all groups
    foreach $group (@groups) {
        # split the group info
        my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$group);

	# are we a member of this group?
	if (&IsUserInGroup($username,$groupmembers) ne 0) {
	    # yup, add this to the list.
	    printf "<li><a href=\"finger.cgi?groupname=%s\">%s</a></li>", &TransformForBrowser ($groupname), $groupname;
	    $ingroup = 1;
	}
    }

    # are we in a group?
    if ($ingroup eq 0) {
	# no. show the 'none' one
	printf "<li><i>None</i></li>";
    }
    
    printf "</ul></td></tr></table></font>";
} else {
    # no. get group information
    my $group_info=&GetGroupRecord($field{"groupname"});

    # got any information?
    if ($group_info eq "") {
	# no. complain
	&error("Group <b>" . $field{"groupname"} . "</b> doesn't exist");
    }

    # split the group info
    my ($groupname,$groupid,$groupdesc,$groupmembers)=split(/:/,$group_info);

    # init the page
    &InitPage("");

    printf "<h1><font face=\"$FORUM_FONT\">Information on group %s</h1>",$groupname;

    printf "<table width=\"100%\">";
    printf "<tr><td width=\"25%\">Group name</td><td width=\"75%\">%s</td></tr>",$groupname;
    printf "<tr><td>Group description</td><td>%s</td></tr>",$groupdesc;
    printf "<tr><td valign=\"top\">Members</td><td><ul>";
    my @group_members=split(/,/, $groupmembers);
    foreach $member (@group_members) {
	printf "<li><a href=\"finger.cgi?accountname=%s\">%s</a></li>", &TransformForBrowser ($member), $member;
    }
    printf "</ul></td></tr>";
}

# End it
&NormalPageEnd();

# leave
exit;
