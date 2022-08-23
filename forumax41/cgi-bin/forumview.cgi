#!/usr/bin/perl
#
# ForuMAX Version 4.1 - forumview.cgi
#
# This will handle displayal of forum threads.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# use our library files
require "forum_options.pl";
require "forum_lib.pl";
require "user_db.pl";

#
# ShowForum()
#
# This will show the contents of a forum. It will show forum $field{"forum"}
#
sub
ShowForum() {
    # is there a 'x:<category>' pair in $field{"forum"}?
    my ($x,$y)=split(/:/,$field{"forum"});
    if ($x eq "x") {
	# yup. browse to that category
	$field{"cat"} = $y;
        &ShowCat();	
	exit;
    }

    # if it's the intro page, chain to the correct page
    if ($field{"forum"} eq "intro_page") {
	# visit the intro page

        # do we have to start with the categories?
        if ($SHOW_CATS eq "YES") {
	    # yup, list the categories
	    &ListCats();
	} else {
	    # nope, list the forums
	    &ListForums();
	}
	exit;
    }

    # check whether this is a restricted forum
    my $forum_info=&GetForumInfo($field{"forum"});
    my ($tmp1,$tmp3,$tmp4,$restricted,$tmp4,$tmp5,$forum_flags,$tmp6,$catno,$f_header,$f_footer,$nofthreads,$newtopic_posters,$newreply_posters)=split(/:/,$forum_info);

    # is this forum disabled?
    if (&check_flag($forum_flags,$FLAG_FORUM_DISABLED) ne 0) {
        # yup. show error
	&error("This forum is disabled. You cannot view it or post in it");
    }

    # check whether this is a restricted forum
    if ($restricted ne "") {
	# it *is* restricted! was an ID given?
        if (($field{"id"} . $cookie{"id"}) eq "") {
	    # no. ask for it
	    &VisitRestricted();
            exit;
	}
        # we have an ID, which is verified. does it give us access?
	if (&CanViewRestricted() eq 0) {
	    # no. show error
	    &error("Access to forum denied");
	}
    }

    # get the header and footer
    &GetHeaderFooter ($field{"forum"});

    # check whether we are a forum mod here
    my $ismod = &IsForumMod ($field{"forum"});

    # Try to open the forum data file
    open(FORUMDATA,$FORUM_DIR . $field{"forum"} . $FORUM_EXT)||&error($ERROR_FILEOPENERR);

    # initialize the page
    &InitPage("",$header);

    # show some info
    #printf "<CENTER><TABLE WIDTH=\"700\" BORDER=\"0\" CELLPADDING=\"5\"><TR><TD WIDTH=\"109\">&nbsp;</TD><TD WIDTH=\"4\"><IMG SRC=\"$IMAGES_URI/forumh.jpg\" alt=\"[Logo]\"></TD><TD WIDTH=\"97\"><B><I><FONT FACE=\"%s\" SIZE=\"2\" COLOR=\"%s\">%s</FONT></I></B><BR>",$FORUM_FONT,$FORUM_TEXT_COLOR,&RestoreSpecialChars ($field{"forum"});

    printf "<CENTER><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLPADDING=\"5\"><TR><TD WIDTH=\"50%\" ALIGN=\"RIGHT\"><B><I><FONT FACE=\"%s\" SIZE=\"2\" COLOR=\"%s\">%s</FONT></I></B><BR>",$FORUM_FONT,$FORUM_TEXT_COLOR,&RestoreSpecialChars ($field{"forum"});

    # are we allowed to post?
    if (&CanPostNewTopic($newtopic_posters) ne "0") {
        printf "<a href=\"forum.cgi?action=newtopic&id=%s&forum=%s\"><img src=\"$IMAGES_URI/newtop.jpg\" alt=\"[Post New Topic]\" ALIGN=\"MIDDLE\" border=0></a>",$field{"id"},&TransformForBrowser($field{"forum"});
    } else {
        print "<font color=\"$FORUM_COLOR_TEXT\"><i>New posts disabled</i></font>";
    }

    printf "</td><td valign=\"top\" width=\"50%\"><font size=2>";

    # show some forum info
    printf "<font face=\"%s\">Forums:<br><a href=\"forum.cgi?id=%s\">$FORUM_TITLE</a><br>",$FORUM_FONT,$field{"id"};

    # do we have a category?
    if (($catno ne "") and ($SHOW_CATS eq "YES")) {
	# yup. get the category name
	my $catname = &GetForumCatName ($field{"forum"});

	printf "<font face=\"$FORUM_FONT\">Category:</font><br><a href=\"forum.cgi?action=showcat&id=%s&cat=%s\">%s</a>", $field{"id"}, &TransformForBrowser ($catname), &RestoreSpecialChars ($catname);
    }

    # close the tables at the top
    printf "</font></td></TD></TR></TABLE></CENTER>\n";

    # show the 'moderated' by stuff
    my @mod_list = &GetForumModList ($field{"forum"});

    # show the logo and some other options
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"50%\">"; 

    # are there mods?
    if (join ("", @mod_list) ne "") {
        # yup. show the list
        printf "<font face=\"%s\">Moderated by ",$FORUM_FONT;

        my $first = "1";

        # dump them all in
        foreach $mod (@mod_list) { 
	    # does it begin with a @?
	    if ($mod=~/^\@/) {
	        # yup. strip off the @
		$mod=~ s/^\@//;
		$record = $mod;

	        # if we have a record, add the group
                if ($record ne "") {
                    if ($first ne "1") {
                        printf ", ";
                    }

                    printf "the <a href=\"finger.cgi?groupname=%s\">%s</a> group",&TransformForBrowser ($mod),$mod;

                    $first="0";
	        }
	    } else {
                # get the info about them
                my $record = &GetUserRecord($mod);

	        # if we have a record, add the user
                if ($record ne "") {
                    if ($first ne "1") {
                        printf ", ";
                    }

                    printf "<a href=\"finger.cgi?accountname=%s\">%s</a>",&TransformForBrowser ($mod),$mod;

                    $first="0";
	        }
	    }
        }

	printf "</font>";
    }

    printf "</td><td width=\"50%\" align=\"right\">%s</td></tr></table><p>",&ConstructOptions();

    # got a valid page number?
    if ($field{"page"} eq "") {
	# no. default to page 1
	$field{"page"} = 1;
    }

    # calculate the number of pages
    my $nofpages = ($nofthreads / $FORUM_THREADS_AT_A_SCREEN);
    if (($nofthreads * $FORUM_THREADS_AT_A_SCREEN) != $nofpages) { $nofpages++; }

    # do we have more than one page?
    my $pagetext = "";
    if ($nofpages >= 1) {
	# yup. build the page text
	$pagetext = "Page ";
	for ($i = 1; $i < $nofpages; $i++) {
	    if ($field{"page"} eq $i) {
		$pagetext .= "[<b>$i</b>] ";
	    } else {
		$pagetext .= "[<a href=\"forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"forum"}) . "&page=$i\">$i</a>] "; 
	    }
	}
	if ($field{"page"} ne "all") {
	    $pagetext .= "[<a href=\"forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"forum"}) . "&page=all\">All</a>] "; 
	} else {
	    $pagetext .= "[<b>All</b>]";
	}
    }

    # add the go button and close the form
    printf $pagetext . "<p>";

    # set up some vars
    my $thread_start;
    my $thread_end;
    my $thread_no;

    $thread_no=0;

    # got a valid page number?
    if ($field{"page"} eq "") {
	# no. default to page 1
	$field{"page"} = 1;
    }

    # need to show only a few?
    if (($field{"page"} ne "") and ($field{"page"} ne "all")) {
        # yup, set the parameters
        $thread_start = $field{"page"} - 1;
        $thread_start = $thread_start * $FORUM_THREADS_AT_A_SCREEN;
        $thread_end = $thread_start + $FORUM_THREADS_AT_A_SCREEN;
    } else {
        # show 'em all!
        $thread_start = 0;
        $thread_end = 9999999999;
    }

    # show the number of registered accounts, if needed
    if ($SHOW_NOF_MEMBERS eq "YES") {
        # show them
        &ShowNofMembers();
    }

    # does this forum limit the new topic creators?
    printf "<p><font size=2 face=\"$FORUM_FONT\">";
    if ($newtopic_posters eq "") {
	printf "All registered users can create a new topic<br>";
    } else {
	printf "Only <b>authorized</b> users can create a new topic<br>";
    }
    if ($newreply_posters eq "") {
	printf "All registered users post a new reply<br>";
    } else {
	printf "Only <b>authorized</b> users can post a new reply<br>";
    }
    printf "</font>";

    # do the table
    printf "<table width=\"100%\" %s>",$FORUM_THREAD_TABLE_TAGS;

    # do the first column that says
    # Subject (69%)  | Author (10%) | Posts (0%) | Newest post Date (21%)
    printf "<tr bgcolor=\"%s\"><td width=\"69%%\"><font face=\"%s\" size=2 color=\"$FORUM_COLOR_THREAD_TEXT\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Subject</font></td><td bgcolor=\"%s\" width=\"10%%\"><font face=\"%s\" color=\"%s\" size=2>Author</td><td bgcolor=\"%s\" width=\"0%%\"><font face=\"%s\" size=\"2\" color=\"$FORUM_COLOR_THREAD_TEXT\">Replies</font></td><td bgcolor=\"%s\" width=\"21%\" align=\"center\"><font face=\"$FORUM_FONT\" size=\"2\" color=\"$FORUM_COLOR_THREAD_TEXT\">Newest Post</font></td></tr></font>",$FORUM_COLOR_THREAD_CELLBACK,$FORUM_FONT,$FORUM_COLOR_THREAD_CELLBACK,$FORUM_FONT,$FORUM_COLOR_THREAD_TEXT,$FORUM_COLOR_THREAD_CELLBACK,$FORUM_FONT,$FORUM_COLOR_THREAD_CELLBACK;

    # read the complete forum info file
    while ( <FORUMDATA> ) {
        # and dump the lines into $line
        my $line = $_;
        chop $line;

        # split the line
        my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter,$locker)=split(/:/,$line);

        $date = $date1 . ":" . $date2;
        my ($datea,$dateb)=split(/\|/,$date);

        # do we need to show this?
        if (($thread_no >= $thread_start) and ($thread_no <= $thread_end)) {
            # yeah, let's rock!

            # add the table entry
            printf "<tr bgcolor=\"%s\"><td width=\"21%\">&nbsp;&nbsp;<font color=\"%s\">",$FORUM_COLOR_THREAD_CONTENTS_CELLBACK,$FORUM_COLOR_SUBJECT;

            # is the forum locked?
            if (&check_flag($forum_flags,$FLAG_FORUM_LOCKED)) {
                # yeah. show the locked icon

                # can we unlock it?
                if ((&CanBeUnlocked($owner,$ismod) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
		    # add the link
                    printf "<a href=\"forum.cgi?action=unlockthread&id=%s&postid=%s&forum=%s\">",$field{"id"},$forum_id,&TransformForBrowser($field{"forum"});
	        }
                printf "<img border=0 src=\"$IMAGES_URI/lock.gif\" alt=\"[Thread locked]\">";
                # can we unlock it?
                if ((&CanBeUnlocked($owner,$ismod) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
		    # yes. close the link
	            printf "</a>";
	        }
            } else {
		# do we have the privileges to lock this?
		if ((&CanBeLocked ($owner,$ismod) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
                    # yes. show the key icon
                    printf "<a href=\"forum.cgi?action=lockthread&id=%s&postid=%s&forum=%s\"><img src=\"$IMAGES_URI/key.gif\" alt=\"[Lock thread]\" border=0></a>",$field{"id"},$forum_id,&TransformForBrowser($field{"forum"});
                }
	    }

            # do we have the privileges to nuke this?
            if ((&CanThreadBeDeleted ($owner,$ismod) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
                # dump the nuke icon in
                printf "<a href=\"forum.cgi?action=deletethread&id=%s&postid=%s&forum=%s\"><img src=\"$IMAGES_URI/del.gif\" alt=\"[Delete thread]\" border=0></a>",$field{"id"},$forum_id,&TransformForBrowser($field{"forum"});
	    }

            # calculate the correct page
            my $j = (($nofposts - ($nofposts % $FORUM_POSTS_AT_A_SCREEN)) / $FORUM_POSTS_AT_A_SCREEN) + 1;
	    # add the line
            printf "<a href=\"forumview.cgi?action=showthread&id=%s&postid=%s&forum=%s&page=%s\" class=subjectlink>",$field{"id"},$forum_id,&TransformForBrowser($field{"forum"}),$j;

	    # do we require icons where there isn't one?
	    if (($NOF_ICONS ne "YES") and ($icon eq "") and ($REQUIRE_ICON eq "YES")) {
		# enforce icon to be the first one
		$icon="1";
	    }

            # is there a cute icon?
            if (($icon ne "") and ($NOF_ICONS ne "NO")) {
                # yeah. put it in
                printf "<img src=\"$IMAGES_URI/icon%s.gif\" border=0>&nbsp;",$icon;
            }

            # set up the subject (owner) cell
            printf "%s</a>&nbsp;<font color=\"$FORUM_COLOR_SUBJECTLINK\">",&CensorPost ($subject);

            # if we have more posts than there would fit on a screen, add some
            # page buttons
            if ($nofposts >= $FORUM_POSTS_AT_A_SCREEN) {
                # dump in the links
                printf " <font size=1>page";
                my $i;my $j;
                $i=1; $j=$nofposts;
                while ($j >= $FORUM_POSTS_AT_A_SCREEN) {
                    printf " <a href=\"forumview.cgi?action=showthread&id=%s&postid=%s&page=%s&forum=%s\">%s</a>",$field{"id"},$forum_id,$i,$field{"forum"},$i;
                    $j = $j - $FORUM_POSTS_AT_A_SCREEN;
                    $i = $i + 1;
                }
                printf " <a href=\"forumview.cgi?action=showthread&id=%s&postid=%s&page=%s&forum=%s\">%s</a></font>",$field{"id"},$forum_id,$i,$field{"forum"},$i;
            }

            # end the cel
            printf "</td>";

            printf "<td><a class=\"memberlink\" href=\"finger.cgi?accountname=%s\">%s</a></td>", &TransformForBrowser ($owner), $owner;

            # set up the number of replies cell
            printf "<td bgcolor=\"%s\"><font color=\"%s\">%s</font></td>",$color2,$FORUM_COLOR_SUBJECTLINK,$nofposts;

            # set up the newest post date cell
            printf "<td align=\"center\" bgcolor=\"%s\"><font size=2 color=\"$FORUM_COLOR_THREAD_DATECOLOR1\" face=\"$FORUM_FONT\">%s <font size=1 color=\"$FORUM_COLOR_THREAD_DATECOLOR2\">%s</font></font>",$color2,$datea,$dateb;

	    # need to show who replied to here the last time?
	    if ($SHOW_LAST_POSTER eq "YES") {
	        printf "<br><font color=\"%s\" face=\"%s\" size=1>by <a href=\"finger.cgi?accountname=%s\" class=\"lastpost\">%s</a></font>",$FORUM_COLOR_THREAD_DATECOLOR2,$FORUM_FONT,&TransformForBrowser ($lastposter), $lastposter;
	    }

	    print "</td>";

            # close the row
            printf "</tr>";
        }

       # increment thread count
       $thread_no = $thread_no + 1;
    }

    # close the forum file
    close(FORUMDATA);
   
    # end the table 
    printf "</table>";

    # add time zone
    printf "<p><center><i><font face=\"$FORUM_FONT\" color=\"$FORUM_TEXT_COLOR\">Times and dates are in $TIMEZONE</font></i></center>";

    # end the page
    &NormalPageEnd($f_footer);

    # close the forum file
    close(FORUMDATA);
}

#
# ShowThread()
#
# This will show the contents of a posted message. It will show the message
# from forum $field{"forum"}, message number $field{"postid"}
#
sub
ShowThread() {
    # if it's a restricted forum, say access denied if we may not access it
    if (&CanViewRestricted() eq 0) {
	# we may not access it
	&error($ERROR_ACCESSDENIED);
    }

    # open the post file
    open(POSTFILE,$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"})||&error($ERROR_FILEOPENERR);

    # get the forum flag
    my $mainforum_line=&GetForumInfo($field{"forum"});

    # get the flags
    my ($tmp,$tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$mainforum_flags,$tmp6,$tmp7,$tmp8,$tmp9,$tmp10,$newtopic_posters,$reply_posters) = split(/:/,$mainforum_line);

    # get the forum data line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter,$locker)=split(/:/,$line);

    # get the header and footer
    &GetHeaderFooter ($field{"forum"});

    # check whether we are a forum mod here
    my $ismod = &IsForumMod ($field{"forum"});

    &InitPage($field{"forum"},$header);

    # Create the page layout stuff
    printf "<CENTER><TABLE WIDTH=\"100%\" BORDER=\"0\" CELLPADDING=\"5\"><TR><TD WIDTH=\"33%\">&nbsp;</td><TD WIDTH=\"99\">\n";

    # is the forum locked? 
    my $locktext;
    if (&check_flag ($forum_flags, $FLAG_FORUM_LOCKED) eq 0) {
        # If thread is NOT locked, can we post a reply?
        if (&CanPostReply($forum_flags,$reply_posters) ne 0) {
            printf "<p><a href=\"forum.cgi?action=postreply&id=%s&postid=%s&forum=%s&page=%s\"><img src=\"$IMAGES_URI/postre.jpg\" alt=\"Post reply\" border=0></a>",$field{"id"},$field{"postid"},$field{"forum"},$field{"page"};
	} else {
            printf "<font face=\"%s\" color=\"$FORUM_COLOR_TEXT\"><I>Replies Disabled</I></font>",$FORUM_FONT;
	}
    } else {
        # yes. show a message indicating it is
        printf "<font face=\"%s\" color=\"$FORUM_COLOR_TEXT\"><I>Replies Disabled</I></font>",$FORUM_FONT;

	# was this thread moved?
	if (&check_flag ($forum_flags, $FLAG_FORUM_MOVED) ne 0) {
	    # yeah. show this as status
	    $locktext = "Thread moved to forum <a href=\"forumview.cgi?id=" . $field{"id"} . "&action=showforum&forum=" . &TransformForBrowser ($newforum) . "\">" . $newforum . "</a>";
	} else {
	    # no, it was only locked
	    $locktext = "Thread locked";
	}

        if (($locker ne "") and ($SHOW_LOCKER eq "YES")) {
	    $locktext .= " by <a href=\"finger.cgi?accountname=" . &TransformForBrowser ($locker) . "\">" . $locker . "</a>";
	}
    }
    $subject =~ s/ /&nbsp;/g;
    $subject = &CensorPost ($subject);

    printf "</TD><TD WIDTH=\"128\"><I><FONT FACE=\"$FORUM_FONT\" SIZE=\"1\" COLOR=\"%s\">%s<br></FONT></I><B><I><FONT FACE=\"$FORUM_FONT\" SIZE=\"4\" COLOR=\"%s\">%s</FONT></I></B></TD><TD WIDTH=\"97\">\n",$FORUM_TEXT_COLOR,&RestoreSpecialChars($field{"forum"}),$FORUM_TEXT_COLOR,$subject;

    # print post new topic button 
    if (&CanPostNewTopic($newtopic_posters) ne "0") {
        printf "<a href=\"forum.cgi?action=newtopic&id=%s&forum=%s\"><img src=\"$IMAGES_URI/newtop.jpg\" alt=\"[New Topic]\" ALIGN=\"MIDDLE\" border=0></a>",$field{"id"},&TransformForBrowser($field{"forum"});
    } else {
        printf "<font face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_TEXT\"><I>New topics disabled</I></font>";
    }

    # show some forum info
    printf "<td><font face=\"$FORUM_FONT\" size=2>Forum:<br><a href=\"forumview.cgi?action=showforum&id=%s&forum=%s\">%s</a><br>",$field{"id"},&TransformForBrowser ($field{"forum"}),&RestoreSpecialChars ($field{"forum"});

    # do we have a category?
    my $catname = &GetForumCatName ($field{"forum"});
    if ($catname ne "") {
	# yup. show it
	printf "Category:<br><a href=\"forum.cgi?action=showcat&id=%s&cat=%s\">%s</a>", $field{"id"}, &TransformForBrowser ($catname), &RestoreSpecialChars ($catname);
    }

    printf "</font></TD></TD></TR></TABLE></CENTER>";

    printf "<table width=\"100%\"><tr><td><font face=\"$FORUM_FONT\"><i>%s</i></font></td><td align=\"right\">%s</td></tr></table><p>",$locktext,&ConstructOptions();

    # calc. some stuff
    my $messageno = 0;
    my $msgstart = $field{"page"} - 1;
    my $msgstart = $msgstart * $FORUM_POSTS_AT_A_SCREEN;
    my $msgend = $msgstart + $FORUM_POSTS_AT_A_SCREEN;

    # calculate the number of pages
    my $nofpages = (($nofposts - ($nofposts % $FORUM_POSTS_AT_A_SCREEN)) / $FORUM_POSTS_AT_A_SCREEN) + 1;

    # if there's more than one page, print it
    my $pagetext = "";
    if ($nofpages > 1) {
        $pagetext = "<font face=\"$FORUM_FONT\" size=1>Show page";
	my $i=1;
	while ($i <= $nofpages) {
	    if ($field{"page"} eq $i) {
	        # if this is the thread, don't hyperlink it
		$pagetext .= " <b>$i</b> " ;
	    } else {
		# otherwise do hyperlink it
		$pagetext .= " <a href=\"forumview.cgi?id=" . $field{"id"} . "&action=showthread&postid=" . $field{"postid"} . "&page=$i&forum=" . &TransformForBrowser ($field{"forum"}) . "\">$i</a> ";
	    }	
	    $i++;
	}
	$pagetext .= "</font>";
    }
    print $pagetext;

    # do the table
    printf "<table width=\"100%\" %s>",$FORUM_POST_TABLE_TAGS;

    # create the author (18%) | post fields (82%)
    printf "<tr><td width=\"18%\" bgcolor=\"%s\"><FONT COLOR=\"%s\" face=\"$FORUM_FONT\">Author</FONT></td><td width=\"82%\" bgcolor=\"%s\"><FONT COLOR=\"%s\" FACE=\"$FORUM_FONT\">Post</FONT></td></tr>",$FORUM_COLOR_POST_CELLBACK,$FORUM_COLOR_POST_INFOTEXT,$FORUM_COLOR_POST_CELLBACK,$FORUM_COLOR_POST_INFOTEXT;

    # grab the extra fields
    my @prof_fields = split(/\|/, $EXTRA_PROFILE_FIELDS);
    my @prof_type = split(/\|/, $EXTRA_PROFILE_TYPES);
    my @prof_hidden = split(/\|/, $EXTRA_PROFILE_HIDDEN);
    my @prof_perms = split(/\|/, $EXTRA_PROFILE_PERMS);

    # walk through it completely
    my $color1=""; my $color2=""; my $color3="";
    my $intable="0"; my $postcolor=""; my $curmsg="";

    while ( <POSTFILE> ) {
        # get the line in $line
        my $line = $_;

        # is the first line starting with a dot?
        my ($dot) = split(/:/, $line);
        if ($dot eq "." ) {
	    # yeah. parse the header
            my ($dot,$author,$d1,$d2) = split(/:/, $line);

	    # need to show the current message?
	    if ($curmsg ne "") {
	        # yup. do it
                $curmsg=&EditForumText($curmsg,$mainforum_flags);
                # do we allow MaX codes?
                if (&check_flag ($mainforum_flags,$FLAG_FORUM_MAXOK) ne "0") {
                    # yup. apply them
                    $curmsg = &ApplyMaXCodes($curmsg,$mainforum_flags);
                }
	        print &CensorPost ($curmsg);
	    }

	    # need to close the current table?
	    if ($intable ne "0" ) {
                # yeah. do it
                if (($messageno < $msgstart) or ($messageno > $msgend)) {
                    printf "</td></tr>\n";
                }
		# make sure we don't do this twice
                $intable="0";
            }
            # increment message number
	    $messageno = $messageno + 1;

	    # no current message now
	    $curmsg = "";
	
	    # toggle colors
            if ($color1 eq $FORUM_COLOR_POST_1_INFO_CELLBACK) {
                $color1=$FORUM_COLOR_POST_2_INFO_CELLBACK;
                $color2=$FORUM_COLOR_POST_2_POST_CELLBACK;
	        $color3=$FORUM_COLOR_POST_2_TEXT;
		$postcolor=$FORUM_COLOR_POST2;
	    } else {
                $color1=$FORUM_COLOR_POST_1_INFO_CELLBACK;
                $color2=$FORUM_COLOR_POST_1_POST_CELLBACK;
	        $color3=$FORUM_COLOR_POST_1_TEXT;
		$postcolor=$FORUM_COLOR_POST1;
            }

            # if we need to print it, do that
            if (($messageno >= $msgstart) and ($messageno <= $msgend)) {
                # set up the table
                printf "<tr valign=\"top\"><td width=\"18%\" bgcolor=\"%s\"><a href=\"finger.cgi?accountname=%s\" class=\"memberlink\">%s</a><br>",$color1,&TransformForBrowser ($author),$author;

                # do we have enough access to delete this post?
                if ((&CanBeDeleted($author,$ismod) ne 0) or (($field{"id"} . $cookie{"id"}) eq "")) {
	            # yup. show the 'delete' icon. is this the first post?
		    if ($messageno ne 1) {
			# no. just make a 'delete' link
                        printf "<a href=\"forum.cgi?action=deletepost&id=%s&postid=%s&forum=%s&messageno=%s&page=%s\"><img src=\"$IMAGES_URI/del.gif\" alt=\"[Delete this message\" border=0></a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"}),$messageno-1,$field{"page"};
		    } else {
        		printf "<a href=\"forum.cgi?action=deletethread&id=%s&postid=%s&forum=%s\"><img src=\"$IMAGES_URI/del.gif\" alt=\"[Delete thread]\" border=0></a>",$field{"id"},$field{"postid"},TransformForBrowser($field{"forum"});
		    }
		}

                # do we have enough access to edit this post?
                if ((&CanBeEdited($author,$ismod) ne 0) or (($field{"id"} . $cookie{"id"}) eq "")) {
                    # yes. show in the 'edit' icon
                    printf "<a href=\"forum.cgi?action=editpost&id=%s&postid=%s&forum=%s&messageno=%s&page=%s\"><img src=\"$IMAGES_URI/edit.gif\" alt=\"Edit this message\" border=0></a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"}),$messageno-1,$field{"page"};
		}

		# can we reply to this?
    		if ((&CanPostReply($forum_flags,$reply_posters) ne 0) or (($field{"id"} . $cookie{"id"}) eq "")) {
		    # yes. show the 'post reply' icon
        	    printf "<a href=\"forum.cgi?action=postreply&id=%s&postid=%s&forum=%s&page=%s&quotefrom=%s\"><img src=\"$IMAGES_URI/quote.gif\" alt=\"Reply with quote\" border=0></a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"}),$field{"page"},$messageno-1
		}
                # do we have enough access to view the IP?
		if ((&CanViewIP($ismod) ne 0) or (($field{"id"} . $cookie{"id"}) eq "")) {
                    # yes. show the 'ip' icon
                    printf "<a href=\"forum.cgi?action=showip&id=%s&postid=%s&forum=%s&messageno=%s&page=%s\"><img src=\"$IMAGES_URI/ip.gif\" alt=\"Show IP\" border=0></a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"}),$messageno-1,$field{"page"};
		}

                # if we need to show info, do it
                if ($FORUM_OPTION_SHOWINFO eq "YES") {
                    # get the user record
                    my $userline=&GetUserRecord($author);

                    # split the info
                    my ($tmp,$passw,$the_flags,$nofposts,$fullname,$email,$sig,$extra)=split(/:/,$userline);
		    $extra=&RestoreSpecialChars ($extra);
		    my @extrafield=split(/\|\^\|/,$extra);

		    # now, handle the extra fields
		    my $fieldno = 0;
                    foreach $type (@prof_type) {
		        # is it visible and have content?
		        if (($prof_hidden[$fieldno] ne "YES") and ($extrafield[$fieldno] ne "")) {
			    # yes. is it an ICQ thing?
			    if ($type eq "2") {
				# yup! make a link to the flower
		                printf "<a href=\"http://wwp.icq.com/scripts/contact.dll?msgto=%s\"><img src=\"http://online.mirabilis.com/scripts/online.dll?icq=%s&img=5\" width=18 height=18 alt=\"[Send ICQ message]\" border=0></a>", $extrafield[$fieldno],$extrafield[$fieldno];
			    }

			    # maybe an AIM thing?
			    if ($type eq "3") {
				# yup! make the link
		                printf "<a href=\"aim:goim?screenname=%s&message=Hi!+Are+You+There?\"><img src=\"$IMAGES_URI/aim.gif\" alt=\"[Send AIM message]\" border=0></a>",$extrafield[$fieldno];
			    }

			    # maybe the Yahoo thing?
			    if ($type eq "4") {
				# yes! make the link to the Y!
		    		printf "<a href=\"http://edit.yahoo.com/config/send_webmsg?.target=%s&.src=pg\" target=\"_blank\"><img src=\"http://opi.yahoo.com/online?u=%s&m=g&t=1\" alt=\"[Send YID message]\" border=0></a>", $extrafield[$fieldno],$extrafield[$fieldno];
		            }

			    # maybe an homepage URL?
			    if ($type eq "6") {
				# yes! make a link to the home!
		    	        printf "<a href=\"%s\" target=\"_blank\"><img src=\"$IMAGES_URI/house.gif\" alt=\"[Visit homepage]\" border=0></a>",$extrafield[$fieldno];
			    }
			}
			$fieldno++;
		    }

	            printf "<br><font face=\"$FORUM_FONT\" color=\"%s\" size=2><i>",$color3;
                    print &GetMemberStatus($author,$userline,$the_flags);

                    # if there are number of posts, print them
		    my $posts;
                    if ($nofposts ne "") {
                        $posts = $nofposts;
                    } else {
                        $posts = "N/A";
                    }

                    printf "</i><br><b>Number of posts</b>: %s<br>",$posts;

		    # now, handle the extra fields
		    $fieldno = 0;
                    foreach $type (@prof_type) {
		        # is it visible and have content?
		        if (($prof_hidden[$fieldno] ne "YES") and ($extrafield[$fieldno] ne "")) {
			    # yes. is it a text or join date field?
			    if (($type eq "0") or ($type eq "8")) {
				# yup! show it
		                printf "<b>%s</b>: %s<br>", $prof_fields[$fieldno], $extrafield[$fieldno];
			    }

			    # is it a gender field?
			    if ($type eq "5") {
				# yup. show it
                                printf "<b>%s</b>: ", $prof_fields[$fieldno];
                                if ($extrafield[$fieldno] eq "m") {
				    printf "Male";
				} else {
                                    if ($extrafield[$fieldno] eq "f") {
				        printf "Female";
				    } else {
				        printf "Unspecified";
				    }
				}
				printf "<br>";
			    }
			}
		        $fieldno++;
		    }
		    printf "<p></font>";
                }

                # now do the message
                printf "</td><td width=\"82%\" bgcolor=\"%s\"><font face=\"$FORUM_FONT\" color=\"%s\">",$color2,$postcolor;
		$the_date = $d1 . ":" . $d2;
		$the_date=~ s/\|/ /;
                printf "&nbsp;<font size=1 face=\"$FORUM_FONT\"><img src=\"$IMAGES_URI/icon1.gif\"> posted at %s %s</font><hr>",$the_date;

                # set the forum printing flag
                $intable="1";
            }
        } else {
            if (($messageno >= $msgstart) and ($messageno <= $msgend)) {
                # are we currently printing forum lines?
                if ($intable ne "0" ) {
                    # edit the text
                    $line=&EditForumText($line,$mainforum_flags);

                    # do we allow MaX codes?
                    if (&check_flag($mainforum_flags,$FLAG_FORUM_MAXOK) ne "0") {
                        # yup. apply them
                        $line = &ApplyMaXCodes($line,$mainforum_flags);
                    }
		    # fix the |IMGOPEN| and |IMGCLOSE| things
		    $line=~ s/\|IMGOPEN\|/[img]/gi;
		    $line=~ s/\|IMGCLOSED\|/[\/img]/gi;
		    $curmsg .= $line . "<br>";
                }
            }
        }
    }

    # need to show the current message?
    if ($curmsg ne "") {
        # yup. do it
        $curmsg=&EditForumText($curmsg,$mainforum_flags);
        # do we allow MaX codes?
        if (&check_flag ($mainforum_flags,$FLAG_FORUM_MAXOK) ne "0") {
            # yup. apply them
            $curmsg = &ApplyMaXCodes($curmsg,$mainforum_flags);
        }
        print &CensorPost ($curmsg);
	print "</td></tr>";
    }

    # end the table
    printf "</table>";

    # close the post file
    close(POSTFILE);

    # add the other 'show page' stuff
    print $pagetext;

    # set up the table
    printf "<CENTER><TABLE WIDTH=\"362\" BORDER=\"0\" CELLPADDING=\"5\"><TR><TD WIDTH=\"99\">\n";

    # is the forum locked?
    # can we post replies?
    if (&CanPostReply($forum_flags,$reply_posters) ne 0) {
        #  yeah. dump in the correct stuff
        printf "<p><a href=\"forum.cgi?action=postreply&id=%s&postid=%s&forum=%s&page=%s\"><img src=\"$IMAGES_URI/postre.jpg\" alt=\"Post reply\" border=0></a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"}),$field{"page"};

        printf "</TD><TD WIDTH=\"128\" ALIGN=\"CENTER\"></TD><TD WIDTH=\"97\">",$FORUM_TEXT_COLOR;
        if (&CanPostNewTopic($newtopic_posters) ne "0") {
            printf "<a href=\"forum.cgi?action=newtopic&id=%s&forum=%s\"><img src=\"$IMAGES_URI/newtop.jpg\" alt=\"[New Topic]\" ALIGN=\"MIDDLE\" border=0></a>",$field{"id"},&TransformForBrowser ($field{"forum"});
        } else {
            printf "<font face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_TEXT\"><I>New topics disabled</I></font>";
	}
        printf "</a></TD></TR></TABLE></CENTER>\n";
    } else {
        # yes. show a message indicating it is
        printf "</TD></TR></TABLE></CENTER>\n";
        printf "<CENTER><br><font face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_TEXT\"><i>Thread replies disabled. No futher posting allowed</i></font></CENTER>";
    }

    # show the administrative options
    printf "<br><center><font size=2 face=\"$FORUM_FONT\">";

    # is this thread locked?
    my $couldunlock="0";
    if (&CanPostReply($forum_flags,$reply_posters) ne 0) {
        # no. show the 'lock thread' link

	# do we have rights to lock it?
        if ((&CanBeLocked($owner) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
	    # yup. show the link
            printf "<a href=\"forum.cgi?action=lockthread&id=%s&postid=%s&forum=%s\">Lock thread</a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"});
	    $couldunlock=1;
        }
    } else {
        if ((&CanBeUnlocked($owner) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
            printf "<a href=\"forum.cgi?action=unlockthread&id=%s&postid=%s&forum=%s\">Unlock thread</a>",$field{"id"},$field{"postid"},&TransformForBrowser($field{"forum"});
	    $couldunlock=1;
        }
    }

    # can we delete this thread?
    my $couldelete = 0;
    if ((&CanThreadBeDeleted ($owner,$ismod) ne "0") or (($field{"id"} . $cookie{"id"}) eq "")) {
	# yup. show the link
        if ($couldunlock ne 0) { printf " | "; }
        printf "<a href=\"forum.cgi?action=deletethread&id=%s&postid=%s&forum=%s\">Delete thread</a>",$field{"id"},$forum_id,&TransformForBrowser($field{"forum"});
	$couldelete = 1;
    }

    # can we move this thread?
    if ((&CanMoveThread($field{"forum"},$ismod) ne 0) or (($field{"id"} . $cookie{"id"}) eq "")) {
	# yup. show the link
	if ($couldelete ne 0) { printf " | "; }
        printf "<a href=\"forum.cgi?action=movethread&id=%s&threadid=%s&forum=%s\">Move thread</a>",$field{"id"},$forum_id,&TransformForBrowser ($field{"forum"});
    }

    printf "</font></center>";

    # end the page
    &NormalPageEnd($f_footer);
}

#
# VisitRestricted()
#
# This will show the 'about to visit restricted forum' page for forum
# $field{"forum"}
#
sub
VisitRestricted() {
    # get the info
    my $forum_info=&GetForumInfo($field{"forum"});
    my ($tmp1,$tmp2,$tmp3,$restricted)=split(/:/,$forum_info);

    # show the page
    &InitPage("");

    printf "The forum <b>%s</b> is a restricted forum, and can only be visited by the following users and groups:<ul>",&RestoreSpecialChars ($field{"forum"});

    my @okusers = split(/,/,$restricted);
    for $ok (@okusers) {
	# does it begin with a @?
	if ($ok=~/^\@/) {
	    # yup. remove it
	    $ok=~ s/^\@//;

	    # and show it
	    printf "<li>%s group</li>\n",$ok;
	} else {
	    # it's just an ordinary user now
	    printf "<li>%s</li>\n",$ok;
	}
    }
    printf "</ul>and all administrator users. You'll need to prove your authication in order to be able to visit it:";
   
    printf "<form action=\"forumview.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dovisitrestricted\">";
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "</table>";
    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form>";

    &NormalPageEnd();
}

#
# DoVisitRestricted()
#
# This will attempt to visit restricted forum $field{"forum"} by using user
# name $field{"username"}, along with password $field{"password"}
#
sub
DoVisitRestricted() {
    # make a hash
    $field{"id"} = &HashID($field{"username"},$field{"password"});

    # verify it
    if (&VerifyHash($field{"id"}) eq 0) {
	# this failed. die
	&error($ERROR_ACCESSDENIED);
    }

    # chain through to the ShowForum() procedure
    &ShowForum();
}

# show the HTML header
&HTMLHeader();

if ($field{"id"} ne "") { $idstring = $field{"id"}; }
if ($cookie{"id"} ne "") { $idstring = $cookie{"id"}; }

# did the user give us an id?
if ($idstring ne "") {
    # yup. check it
    if (&VerifyHash($idstring) eq 0) {
	# the id was not valid. die
	&error($ERROR_ACCESSDENIED);
    }
} else {
    # do we need the user to be logged in?
    if ($REQUIRE_LOGIN eq "YES") {
	# yes. ask for it
        &RequestLogin();
        exit;
    }
}

if ($field{"action"} eq "showforum") {
    # the user wants to show a forum. do it
    &ShowForum();

    # get outta here!
    exit;
}

if ($field{"action"} eq "showthread" ) {
    # the user wants to view a thread. do it
    &ShowThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "dovisitrestricted") {
    # the user wants to actually visit a restricted forum. do it
    &DoVisitRestricted();
  
    # get outta here!
    exit;
}
