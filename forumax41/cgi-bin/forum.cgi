#!/usr/bin/perl
#
# ForuMAX Version 4.1 - forum.cgi
#
# This will handle the forum itself.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# use our library files
require "forum_options.pl";
require "forum_lib.pl";
require "user_db.pl";

# $ERROR_xxx are error messages
$ERROR_FORUMLOCKED="Forum is currently locked. Try again later";
$ERROR_NOMSG="Forum message does not exists";
$ERROR_POSTDISABLED="Posting is disabled for this account";

#
# GetForumSubjectID($forum,$id)

# This will return the subject of post number $id from forum $forum. It will
# show an error if the post does not exists.
#
sub
GetForumSubjectID() {
    # get my parameters
    my ($forum,$id) = @_;

    # get the forum line
    my $line = &GetForumLine ($forum,$id);

    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$newforum)=split(/:/,$line);

    # return the information
    return $subject;
}

#
# GetMessageHeader($forum,$id,$messageno)
#
# This will return the header of post number $id from forum $forum, message
# $messageno. It will show an error if the post does not exists.
#
sub
GetMessageHeader() {
    # get my parameters
    my ($forum,$id,$messageno) = @_;

    # open the forum file
    open(POSTFILE,$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"})||&error($ERROR_FILEOPENERR);

    # walk through it completely
    my $msgno="0";

    while ( <POSTFILE> ) {
        # get the line in $line
        my $line = $_;

        # is the first line starting with a dot?
        my ($dot) = split(/:/, $line);
        if ( $dot eq "." ) {
	    # yeah. parse the header
            my ($dot,$author,$d1,$d2) = split(/:/, $line);

            # is this our message?
            if ($messageno eq $msgno) {
                # yeah. return the header
                # close the post file
                close(POSTFILE);
                return $line;
            }

            # increment message number
            $msgno++;
        }
    }

    # close the post file
    close(POSTFILE);

    # show the error
    &error($ERROR_NOMSG);
}

#
# GetMessageData($forum,$id,$messageno)
#
# This will return the actual data of post number $id from forum $forum, message
# $messageno. It will show an error if the post does not exists.
#
sub
GetMessageData() {
    # get my parameters
    my ($forum,$id,$messageno) = @_;

    # open the forum file
    open(POSTFILE,$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"})||&error($ERROR_FILEOPENERR);

    # walk through it completely
    my $msgno="0"; my $data=""; my $active="0";

    while ( <POSTFILE> ) {
        # get the line in $line
        my $line = $_;

        # is the first line starting with a dot?
        my ($dot) = split(/:/, $line);
        if ( $dot eq "." ) {
	    # yeah. parse the header
            my ($dot,$author,$d1,$d2) = split(/:/, $line);
 
            # was the active flag set?
            if ($active ne "0") {
                # yeah. return this as the data
                # close the post file
                close(POSTFILE);
                # and return the data
                return $data;
            }

            # is this our message?
            if ($messageno eq $msgno) {
                # yeah. set the active flag
                $active="1";
            }

            # increment message number
            $msgno++;
        } else {
            # are we active?
            if ($active ne "0") {
                # yeah. add the line to the buffer
                $data = $data . $line;
            }
        }
    }

    # close the post file
    close(POSTFILE);

    # are we now active?
    if ($active ne "0") {
        # yeah. return the data
        return $data;
    }

    # show the error
    &error($ERROR_NOMSG);
}

#
# DeletePost()
#
# This will delete a post. It will delete message number $field{"messageno"}
# in forum $field{"forum"} in thread $field{"postid"}
#
sub
DeletePost() {
    # is it allowed?
    if (($ALLOW_EDIT_DELETE eq "0") or ($ALLOW_EDIT_DELETE eq "1")) {
	# no. complain	
	&error("Deleting of posts is disabled");
    }

    # get the forum description line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it into usuable fields
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter,$locker)=split(/:/,$line);

    # is the forum locked?
    if (&check_flag($forum_flags,$FLAG_FORUM_LOCKED)) {
        # yeah. do we have to complain about that?
        if ($ALTER_LOCKED ne "YES") {
	    # yup. do it
            &error("Messages in locked forums cannot be altered");
        }
    }

    # initialize the page
    &InitPage("");

    # write what we are going to do
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">Are you sure you want to delete this message?<br>";
    # and dump in the 'yes' and 'no' buttons
    printf "<form action=\"forum.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dodeletepost\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"messageno\" value=\"%s\">",$field{"messageno"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "</table>";

    printf "<input type=\"submit\" value=\"OK\">";

    printf "</form>";

    printf "<form action=\"forumview.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showthread\">";
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};
    printf "<input type=\"submit\" value=\"Cancel!\">";
    printf "</form>";

    # end the page
    &NormalPageEnd();
}

#
# DoDeletePost()
#
# This will actually delete the post. It will delete message number
# $field{"messageno"} in forum $field{"forum"} in thread $field{"postid"}
#
sub
DoDeletePost() {
    # is it allowed?
    if (($ALLOW_EDIT_DELETE eq "0") or ($ALLOW_EDIT_DELETE eq "1")) {
	# no. complain	
	&error("Deleting of posts is disabled");
    }

    # get the forum description line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it into usuable fields
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter)=split(/:/,$line);

    # is the forum locked?
    if (&check_flag($forum_flags,$FLAG_FORUM_LOCKED)) {
        # yeah. do we have to complain about that?
        if ($ALTER_LOCKED ne "YES") {
	    # yup. do it
            &error("Messages in locked forums cannot be altered");
        }
    }

    # verify our authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # if it's a restricted forum, say access denied if we may not access it
    if (&CanViewRestricted() eq 0) {
	# we may not access it
	&error($ERROR_ACCESSDENIED);
    }

    # get the message header
    my $hdr = &GetMessageHeader($field{"forum"},$field{"postid"},$field{"messageno"});

    # split the line
    my ($dot,$author,$d1,$d2) = split(/:/, $hdr);

    # grab the moderator status
    my $ismod = &IsForumMod ($field{"forum"});

    # does the user really have the privileges to do this?
    if (&CanBeDeleted($author,$ismod) eq 0) {
        # no. say access denied
        &HackError($ERROR_ACCESSDENIED);
    }

    # destroy the post
    &DestroyPost ($field{"forum"}, $field{"postid"}, $field{"messageno"},"");

    # initialize the html page
    my $URL = "forumview.cgi?action=showthread&id=" . $field{"id"} . "&postid=" . $field{"postid"} . "&forum=" . &TransformForBrowser ($field{"forum"}) . "&page=" . $field{"page"};
    &InitPage("","",$URL);

    # show the 'message deleted ok' message
    printf "Message deleted successfully. You will be redirected to the thread in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</body>";

    # end the page
    &NormalPageEnd();
}

#
# PostReply()
#
# This will show the 'post reply' dialog of a post. It will post a reply to
# forum $field{"forum"}, thread $field{"postid"}
#
sub
PostReply() {
    # does the user really have the privileges to do this?
    if (&check_flag($flags,$FLAG_DISABLED) ne "0") {
        # no. show an error
        &error($ERROR_POSTDISABLED);
    }

    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$newforum)=split(/:/,$line);

    # create the page layout stuff
    &InitPage("");

    # Make sure the text can be seen
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">";

    # set up the form
    printf "<form method=\"post\" action=\"forum.cgi\">";

    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"dopostreply\">";
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};

    # dump in the Forum: xxx and Topic: xxx stuff
    printf "<table>";
    printf "<tr><td><font color=\"$FORUM_COLOR_TEXT\">Forum</font></td><td><font size=5 color=\"$FORUM_COLOR_TEXT\"><a href=\"forumview.cgi?id=%s&action=showforum&forum=%s\"><i><b>%s</b></i></a></font></td</tr>",$field{"id"}, &TransformForBrowser ($field{"forum"}),&RestoreSpecialChars($field{"forum"});

    printf "<tr><td><font color=\"$FORUM_COLOR_TEXT\">Topic</font></td><td><font color=\"$FORUM_COLOR_TEXT\"><b>%s</b></font><td></tr>",&CensorPost ($subject);

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "<tr><td valign=\"top\"><font color=\"$FORUM_COLOR_TEXT\">Reply</font><br><font color=\"$FORUM_COLOR_TEXT\" size=1>%s</font></td><td>",&ResolveForumRestrictions($field{"forum"});
    printf "<textarea rows=10 cols=50 name=\"text\">";

    # need to quote?
    if ($field{"quotefrom"} ne "") {
        # yup. get the actual data
        my $data = &GetMessageData($field{"forum"},$field{"postid"},$field{"quotefrom"}); chop $data;

        # get the message author and data
        my $hdr = &GetMessageHeader($field{"forum"},$field{"postid"},$field{"quotefrom"});

        # split the line
        my ($dot,$author,$d1,$d2) = split(/:/, $hdr);

	# and print i
	printf "<font size=1>Originally posted by %s</font>\[quote\]\[b\]$data\[/b\]\[/quote\]", $author;
    }
    printf "</textarea>";
    printf "</td></tr></table>";

    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"dopostreply\">";
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};

    # do we allow signatures?
    if ($SIG_ALLOWED eq "YES") {
	# yes. show the 'show signature' checkbox
	printf "<input type=\"checkbox\" name=\"showsig\"";
        # should it default to on?
	if (($SIG_SHOWDEFAULT eq "YES") and (&check_flag($flags,$FLAG_DONTCHECKSIG) eq "0")) {
	    # yes. check the box
	    printf " checked";
	}
	printf ">Show signature</input><p>";
    }

    # and do the buttons
    printf "<input type=\"submit\" value=\"OK\">&nbsp;";
    printf "</form>";

    printf "<form method=\"post\" action=\"forumview.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showthread\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};

    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};
    printf "<input type=\"submit\" value=\"Cancel\"><br>";
    printf "</form>";

    # do we need to show a part of the reply?
    if ($REVIEW_POST eq "YES") {
	# yup. do the number of posts we have exceed the number of posts at a
	# screen?
	if ($nofposts >= $FORUM_POSTS_AT_A_SCREEN) {
	    # yup. just do a link
	    printf "<center><a target=\"_blank\" href=\"forumview.cgi?action=showthread&id=%s&postid=%s&forum=%s&page=1\">Click this to review the thread</a></center>",$field{"id"},$field{"postid"},&TransformForBrowser ($field{"forum"});
	} else {
	    # we can show the thread! open the post file
            open(POSTFILE,$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"})||&error($ERROR_FILEOPENERR);

	    # get the forum flag
    	    my $mainforum_line=&GetForumInfo($field{"forum"});

    	    # get the flags
   	    my ($tmp,$tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$mainforum_flags) = split(/:/,$mainforum_line);

	    printf "<center>Thread review</center><p>";

    	    # do the table
	    printf "<table width=\"100%\" %s>",$FORUM_POST_TABLE_TAGS;

            # create the author (18%) | post fields (82%)
	    printf "<tr><td width=\"18%\" bgcolor=\"%s\"><FONT COLOR=\"%s\" face=\"$FORUM_FONT\">Author</FONT></td><td width=\"82%\" bgcolor=\"%s\"><FONT COLOR=\"%s\" FACE=\"$FORUM_FONT\">Post</FONT></td></tr>",$FORUM_COLOR_POST_CELLBACK,$FORUM_COLOR_POST_INFOTEXT,$FORUM_COLOR_POST_CELLBACK,$FORUM_COLOR_POST_INFOTEXT;

            while ( <POSTFILE> ) {
                # get the line in $line
        	my $line = $_;

        	# is the first line starting with a dot?
        	my ($dot) = split(/:/, $line);
		if ($dot eq ".") {
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

                    # set up the table
                    printf "<tr valign=\"top\"><td width=\"18%\" bgcolor=\"%s\"><a href=\"finger.cgi?accountname=%s\" class=\"memberlink\">%s</a><br>",$color1,&TransformForBrowser ($author),$author;

                    printf "</td><td width=\"82%\" bgcolor=\"%s\"><font face=\"$FORUM_FONT\" color=\"%s\">",$color2,$postcolor;
		    $the_date = $d1 . ":" . $d2;
		    $the_date=~ s/\|/ /;

                    # set the forum printing flag
	   	    $in_table = 1;
	        } else {
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

	    printf "</table>";

	    # close the post file
	    close(POSTFILE);
	}
    }

    # end the page
    &NormalPageEnd();
}

#
# DoPostReply()
#
# This will actually post the reply. It will post a reply to forum
# $field{"forum"}, thread $field{"postid"}
#
sub
DoPostReply() {
    # verify authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # if it's a restricted forum, say access denied if we may not access it
    if (&CanViewRestricted() eq 0) {
	# we may not access it
	&error($ERROR_ACCESSDENIED);
    }

    # grab the forum flags
    my $forum_info=&GetForumInfo($field{"forum"});
    my ($tmp1,$tmp3,$tmp4,$restricted,$tmp4,$tmp5,$forum_flags,$tmp6,$catno,$f_header,$f_footer,$nofthreads,$newtopic_posters,$reply_posters)=split(/:/,$forum_info);
   
    # does the user really have the privileges to do this?
    if (&CanPostReply($forum_flags,$reply_posters) eq "0") {
        # no. show error
        &error ($ERROR_POSTINGDISABLED);
    }

    # is this forum disabled?
    if (&check_flag($forum_flags,$FLAG_FORUM_DISABLED) ne 0) {
        # yup. show error
	&error("This forum is disabled. You cannot view it or post in it");
    }

    # get the forum description line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it into usuable fields
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$thread_flags,$lastposter)=split(/:/,$line);

    # is the forum locked?
    if (&check_flag($forum_flags,$FLAG_FORUM_LOCKED)) {
        # yeah. show error
        &error("Forum locked");
    }

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"} . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yes. show error
        &error($ERROR_FORUMLOCKED);
    }

    # open the forum file
    my $forumfile=$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"};
    open(POSTFILE,"+<" . $forumfile)||&error($ERROR_FILEOPENERR);

    # create the lock file
    open(LOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERR);

    # need to log the IP address?
    my $theip;
    if ($IP_LOG_DISPLAY ne "0") {
        # yup. get the IP
        $theip = $ENV{"REMOTE_ADDR"};
    } else {
	# no. use 0.0.0.0 as the IP
	$theip = "0.0.0.0";
    }

    # construct the heading record
    my $timestr = &GetTimeDate();
    my $header = ".:" . $field{"username"} . ":" . &GetTimeDate() . ":" . $theip . "\n";

    # seek to the end of the forum file
    seek(POSTFILE, 0, 2);

    # Searches for .: in the field and replaces it with . :
    $field{"text"} =~ s/\.:/\. :/g;

    # get the forum flag
    my $forum_line=&GetForumInfo($field{"forum"});

    # get the flags
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags)=split(/:/,$forum_line);

    # do we have to nuke HTML?
    if (&check_flag($forum_flags,$FLAG_FORUM_HTMLOK) eq "0") {
	# yup. do it
       $field{"text"} =~ s/</&lt;/g;
       $field{"text"} =~ s/>/&gt;/g;
    }

    # add the header and the message to the file
    print POSTFILE $header;
    print POSTFILE $field{"text"} . "\n";

    # need to add the signature?
    if (($SIG_ALLOWED eq "YES") and ($field{"showsig"} ne "") and (&check_flag($flags,$FLAG_DENYSIG) eq "0")) {
	# yup. do it
	print POSTFILE "\n----------\n";
        print POSTFILE &FormatSignature($field{"username"}) . "\n";
    }

    # close the forum file
    close(POSTFILE);

    # close the lock file
    close(LOCKFILE);

    # delete the lock file
    unlink($lockfile);

    # increment the number of posts made
    &IncrementNofPosts();
    &IncrementForumPosts($field{"forum"},$field{"postid"},"0", $field{"username"},"1");
    &UpdateForumData($field{"forum"},"1","1","1","0");

    # do we need to notify the user?
    if ((&check_flag ($thread_flags,$FLAG_FORUM_NOTIFY) ne 0) and ($field{"username"} ne $owner)) {
	# yup. carve up the email
	my $mailsubject = "$FORUM_TITLE: Reply to your topic";
        my $date = &GetTimeDate();
        $date=~ s/\|/ /;
	my $post_url = $ENV{"SERVER_NAME"} . $ENV{"SCRIPT_NAME"};
	$post_url=~ s/forum.cgi/forumview.cgi/gi;
	$forum_url = "http://" . $post_url . "?action=showforum&id=&forum=" . &TransformForBrowser ($field{"forum"});
	$post_url .= "?action=showthread&id=&postid=" . $field{"postid"} . "&forum=" . &TransformForBrowser ($field{"forum"}) . "&page=1";
	$post_url = "http://" . $post_url;
	my $body = qq~Hello $owner,<p>

At $date, $field{"username"} has replied to your topic <a href="$post_url">$subject</a> in the <a href="$forum_url">$field{"forum"}</a>. Since you turned email notification on, we thought you might be interested :)<p>

Thank you<br>
The forum administrator<br>
<a href="$WEBSITE_URI">$WEBSITE_URI</a>\n
~;

	# grab the user info
	my $userline = &GetUserRecord ($owner);
        my ($tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$email)=split(/:/,$userline);

	# if we have a valid email address, send the notification
	if ($email ne "") {
            # send it
	    printf "[Sending email to $email]";
            &SendEmail($email,$mailsubject,$body);
	}
    }

    # initialize the html page
    my $URL = "forumview.cgi?action=showthread&id=" . $field{"id"} . "&postid=" . $field{"postid"} . "&forum=" . &TransformForBrowser ($field{"forum"}) . "&page=" . $field{"page"};
    &InitPage("","",$URL);

    # show the 'reply posted ok' message
    printf "<font face=\"$FORUM_FONT\">Reply posted successfully. You will be redirected to the thread in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";

    # end the page
    &NormalPageEnd();
}

#
# EditPost()
#
# This will show the 'edit post' dialog of a post. It will post a reply to
# forum $field{"forum"}, thread $field{"postid"}
#
sub
EditPost() {
    # does the user really have the privileges to do this?
    if (&check_flag($flags,$FLAG_DISABLED) ne "0") {
        # no. show an error
        &error($ERROR_POSTDISABLED);
    }

    # is it allowed?
    if ($ALLOW_EDIT_DELETE eq "0") {
	# no. complain	
	&error("Editing of posts is disabled");
    }

    # get the forum description line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it into usuable fields
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter)=split(/:/,$line);

    # is the forum locked?
    if (&check_flag($forum_flags,$FLAG_FORUM_LOCKED)) {
        # yeah. do we have to complain about that?
        if ($ALTER_LOCKED ne "YES") {
	    # yup. do it
            &error("Messages in locked forums cannot be altered");
        }
    }

    # get the message author and data
    my $hdr = &GetMessageHeader($field{"forum"},$field{"postid"},$field{"messageno"});

    # split the line
    my ($dot,$author,$d1,$d2) = split(/:/, $hdr);

    # get the actual data
    my $data = &GetMessageData($field{"forum"},$field{"postid"},$field{"messageno"});
    # and nuke the newline
    chop $data;

    # get the name of the subject
    my $name = &GetForumSubjectID($field{"forum"},$field{"postid"});

    # get the forum flag
    my $forum_line=&GetForumInfo($field{"forum"});

    # get the flags
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags)=split(/:/,$forum_line);

    # create the page layout stuff
    &InitPage("");

    # Make sure the text can be seen
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">";

    # set up the form
    printf "<form method=\"post\" action=\"forum.cgi\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"doeditpost\">";
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"messageno\" value=\"%s\">",$field{"messageno"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};

    # create a table for these thingys
    printf "<table>";

    printf "<tr><td><font color=\"$FORUM_COLOR_TEXT\">Forum</font></td><td><font size=5 color=\"$FORUM_COLOR_TEXT\"><i><b>%s</b></i></font></td></tr>",$field{"forum"};
    printf "<tr><td><font color=\"$FORUM_COLOR_TEXT\">Topic</font></td><td><font color=\"$FORUM_COLOR_TEXT\"><b>%s</b></font></td></tr>",&CensorPost ($name);

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    # dump in the reply box
    printf "<tr><td valign=\"top\"><font color=\"$FORUM_COLOR_TEXT\">Editing post</font><br><font color=\"$FORUM_COLOR_TEXT\" size=1>%s</font></td><td>",&ResolveForumRestrictions($field{"forum"});
    printf "<textarea rows=10 cols=50 name=\"text\">";
    # insert the old text
    $data=~ s/--\>/--&gt;/g;
    $data=~ s/\<--/&lt;--;/g;
    $data=~ s/\|IMGOPEN\|/[img]/gi;
    $data=~ s/\|IMGCLOSED\|/[\/img]/gi;
    printf $data;
    printf "</textarea></td></tr>";

    printf "</table>";

    # dump in the ok button
    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form>";

    # dump in the cancel button
    printf "<form action=\"forumview.cgi\" method=\"post\">";
# method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showthread\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};
    printf "<input type=\"submit\" value=\"Cancel!\">";
    printf "</form>";

    # end the page
    &NormalPageEnd();
}

#
# DoEditPost()
#
# This will actually edit the posted message. It will post a reply to forum
# $field{"forum"}, thread $field{"postid"}, message $field{"messageno"}.
#
sub
DoEditPost() {
    # verify authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # is it allowed?
    if ($ALLOW_EDIT_DELETE eq "0") {
	# no. complain	
	&error("Editing of posts is disabled");
    }

    # if it's a restricted forum, say access denied if we may not access it
    if (&CanViewRestricted() eq 0) {
	# we may not access it
	&error($ERROR_ACCESSDENIED);
    }

    # get the message header
    my $hdr = &GetMessageHeader($field{"forum"},$field{"postid"},$field{"messageno"});

    # split the line
    my ($dot,$author,$d1,$d2,$theip) = split(/:/, $hdr);

    # grab moderator status
    my $ismod = &IsForumMod ($field{"forum"});

    # do we have the rights to do this?
    if (&CanBeEdited($author,$ismod) eq "0") {
	# no. show the access denied thingy
	&error($ERROR_ACCESSDENIED);
    }

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"} . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yes. show error
        &error($ERROR_FORUMLOCKED);
    }

    # get the forum description line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it into usuable fields
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter)=split(/:/,$line);

    # is the forum locked?
    if (&check_flag($forum_flags,$FLAG_FORUM_LOCKED)) {
        # yeah. do we have to complain about that?
        if ($ALTER_LOCKED ne "YES") {
	    # yup. do it
            &error("Messages in locked forums cannot be altered");
        }
    }

    # get the forum flag
    my $forum_line=&GetForumInfo($field{"forum"});

    # get the flags
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags)=split(/:/,$forum_line);

    # open the forum file
    my $forumfile=$FORUM_DIR . $field{"forum"} . "/" . $field{"postid"};
    open(POSTFILE,$forumfile)||&error($ERROR_FILEOPENERR);

    # create the lock file
    open(LOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERR);

    # Searches for .: in the field and replaces it with . :
    $field{"text"} =~ s/\.:/\. :/g;

    # do we have to nuke HTML?
    if (&check_flag($forum_flags,$FLAG_FORUM_HTMLOK) eq "0") {
	# yup. do it
       $field{"text"} =~ s/</&lt;/g;
       $field{"text"} =~ s/>/&gt;/g;
    }

    # set the vars
    my $msgno="0";my $editing="0";

    # trace through the complete forum file
    while ( <POSTFILE> ) {
        # get the line
        my $line = $_;

        # is this a line starthing with a dot?
        my ($dot) = split(/:/, $line);
        if ( $dot eq "." ) {
	    # yeah. parse the header
            my ($dot,$author,$d1,$d2) = split(/:/, $line);

            # is this the message we should edit?
            if ($msgno eq $field{"messageno"}) {
                # yeah. set the flag
                $editing="1";
            } else {
                # nope. clear the flag
                $editing="0";
            }

            # increment the message number
            $msgno++;
        }

        # if not editing, print the line to the lock file
        if ($editing eq "0") {
            print LOCKFILE $line;
        } else {
            if ($editing ne "2" ) {
                # do copy the header
                print LOCKFILE $line;

                # and dump in the new data
                print LOCKFILE $field{"text"} . "\n";

                # do we need to add the 'edited by ...' message?
                if($SHOW_EDIT eq "YES") {
		    # yes. do it
		    my $date=&GetTimeDate();
		    $date=~tr/\|/ /;
		    printf LOCKFILE "\n[This message has been edited by %s at %s]\n",$field{"username"},$date;
		}

                # now skip the rest of the message
                $editing="2";
            }
        }
    }

    # close the forum file
    close(POSTFILE);

    # close the lock file
    close(LOCKFILE);

    # copy the file
    &CopyFile($lockfile,$forumfile);

    unlink($lockfile);

    # initialize the html page
    my $URL = "forumview.cgi?action=showthread&id=" . $field{"id"} . "&postid=" . $field{"postid"} . "&forum=" . &TransformForBrowser ($field{"forum"}) . "&page=" . $field{"page"};
    &InitPage("","",$URL);

    # show the 'message edited ok' message
    printf "<font face=\"$FORUM_FONT\">Message successfully edited. You will be redirected to the thread in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";

    # end the page
    &NormalPageEnd();
}

#
# ShowIP()
#
# This will show the IP address of the poster of post $field{"messageno"} in
# thread $field{"postid"} in forum $field{"forum"}. If the user is not
# logged in, it will prompt for a valid username/password pair, otherwise
# it will chain to &DoViewIP()
#
sub
ShowIP() {
    # are we logged in?
    if (($field{"id"} . $cookie{"id"}) ne "") {
	# yes. chain to DoShowIP()
	&DoShowIP();
	return;
    }

    # build the page
    &InitPage();

    printf "<font face=\"$FORUM_FONT\">In order to let you view the IP address, we'll need your authorization in order to prove you are allowed to do this. Please fill in the fields below:";
    
    # set up the form
    printf "<form method=\"post\" action=\"forum.cgi\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"action\" value=\"doshowip\">";
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"messageno\" value=\"%s\">",$field{"messageno"};
    printf "<input type=\"hidden\" name=\"page\" value=\"%s\">",$field{"page"};

    # create a table for these thingys
    printf "<table>";
    &UsernamePasswordForm("0");
    printf "</table>";

    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form></font>";

    &NormalPageEnd();
}


#
# DoShowIP()
#
# This will actually show the IP address of the poster of post
# $field{"messageno"} in thread $field{"postid"} in forum $field{"forum"}
#
sub
DoShowIP() {
    # verify authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # do we have access for this?
    if (&CanViewIP() eq 0) {
	# no. complain
	&error($ERROR_ACCESSDENIED);
    }

    # get the message author and data
    my $hdr = &GetMessageHeader($field{"forum"},$field{"postid"},$field{"messageno"});
    my $data = &GetMessageData($field{"forum"},$field{"postid"},$field{"messageno"});

    # get the forum flag
    my $mainforum_line=&GetForumInfo($field{"forum"});

    # get the flags
    my ($tmp,$tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$mainforum_flags) = split(/:/,$mainforum_line);

    # get the forum data line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});
    chop $line;

    # split the line
    my ($dot,$author,$d1,$d2,$ip) = split(/:/, $hdr);
    my $tmp=$ip;
    # is IP in the form a.b.c.d?
    $tmp=~ s/\.//gi;
    if ($tmp eq $ip) {
	# no. take the next field
        ($dot,$author,$d1,$d2,$img,$ip) = split(/:/, $hdr);
    }

    # if the IP is 0.0.0.0, logging was disabled
    if (&ZapTrailingSpaces ($ip) eq "0.0.0.0") {
	$ip = "not logged";
    }

    # initialize the page
    &InitPage();

    # edit the text
    $data=&EditForumText($data,$mainforum_flags);

    # do we allow MaX codes?
    if (&check_flag($mainforum_flags,$FLAG_FORUM_MAXOK) ne "0") {
        # yup. apply them
        $line = &ApplyMaXCodes($data,$mainforum_flags);
    }
    # fix the |IMGOPEN| and |IMGCLOSE| things
    $data=~ s/\|IMGOPEN\|/[img]/gi;
    $data=~ s/\|IMGCLOSED\|/[\/img]/gi;

    # fix the returns
    $data=~ s/\n/<br>/gi;

    # show the post
    printf "<font face=\"$FORUM_FONT\"><table width=\"100%\" border=1>";
    printf "<tr><td>Posted by <a href=\"finger.cgi?accountname=%s\">%s</a>, IP was %s</td></tr>",$author,$author,$ip;
    printf "<tr><td>%s</td></tr>", &CensorPost ($data);
    printf "</table>";

    printf "<p><a href=\"forumview.cgi?action=showthread&id=%s&postid=%s&forum=%s&page=%s\">Return to the thread</a></font>",$field{"id"},$field{"postid"},&TransformForBrowser ($field{"forum"}),$field{"page"};

    # end the page
    &NormalPageEnd();
}

# 
# ResolveForumRestrictions($the_forum)
#
# This will return the forum restrictions for forum $the_forum.
#
sub
ResolveForumRestrictions() {
    # get the arguments
    my ($the_forum)=@_;

    # get the forum flag
    my $forum_line=&GetForumInfo($the_forum);

    # get the flags
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags)=split(/:/,$forum_line);

    # check HTML allowance
    my $result="HTML is ";
    if (&check_flag($forum_flags,$FLAG_FORUM_HTMLOK) eq 0) { $result.=" <b>not</b> "; };
    $result.="allowed<br>";

    $result.="<a href=\"forum.cgi?action=maxcodes\" target=\"_blank\">MaX</a> codes are ";
    if (&check_flag($forum_flags,$FLAG_FORUM_MAXOK) eq 0) { $result.=" <b>not</b> "; };
    $result.="allowed<br>";

    $result.="Images are ";
    if (&check_flag($forum_flags,$FLAG_FORUM_NOIMG) ne 0) { $result.=" <b>not</b> "; };
    $result.="allowed<br>";
    $result.="<a href=\"forum.cgi?id=" . $field{"id"} . "&action=smilielegend\" target=\"_blank\">Smilie Legend</a>";

    return $result;
}

#
# NewTopic()
#
# This will show the 'new topic' dialog of a post. It will create a new topic
# at forum $field{"forum"}
#
sub
NewTopic() {
    # create the page layout stuff
    &InitPage("");

    # make sure the text can be seen
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">";

    # set up the form
    printf "<form method=\"post\" action=\"forum.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"donewtopic\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    # create a table for these thingys
    printf "<table>";

    # dump in the Forum: xxx stuff
    printf "<tr><td><font color=\"$FORUM_COLOR_TEXT\">Forum</font></td><td><font size=5 color=\"$FORUM_COLOR_TEXT\"><a href=\"forumview.cgi?id=%s&action=showforum&forum=%s\"><i><b>%s</b></i></a></font></td</tr>",$field{"id"}, &TransformForBrowser ($field{"forum"}),&RestoreSpecialChars($field{"forum"});

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "<tr><td><font color=\"$FORUM_COLOR_TEXT\">Topic</font></td><td><input type=\"text\" name=\"subjectname\"></td></tr>";
    # if icons are allowed, use them!
    if ($NOF_ICONS ne "NO") {
        printf "\n<tr valign=\"top\"><td color=\"$FORUM_COLOR_TEXT\">Icon</td><td>";

	printf "<table width=\"100%\">";
	my $j = 0;
        if ($REQUIRE_ICON ne "YES") {
            printf "<tr><td><input type=\"radio\" name=\"icon\" value=\"0\" checked>None</input>";
	    $j++;
        }
        for ($i = 1; $i <= $NOF_ICONS; $i++) {
	    if ($j eq 0) { printf "<tr>"; }
	    printf "<td width=\"10%\"><input type=\"radio\" name=\"icon\" value=\"$i\"";
	    if (($REQUIRE_ICON eq "YES") and ($i eq "1")) {
	        printf " checked";
	    }
	    printf "><img src=\"$IMAGES_URI/icon$i.gif\"></input></td>";
	    if ($j eq 9) { printf "</tr>"; }

	    $j++; if ($j eq 10) { $j = 0; }
	}
	printf "</table>";
	printf "</td></tr>";
    }

    printf "<tr valign=\"top\"><td><font color=\"$FORUM_COLOR_TEXT\">Message</font><br><font size=1 color=\"$FORUM_COLOR_TEXT\">%s</font></td><td><textarea cols=50 rows=15 name=\"text\"></textarea></tr></tr>",&ResolveForumRestrictions($field{"forum"});

    printf "</table><p>";

    # do we allow signatures?
    if ($SIG_ALLOWED eq "YES") {
	# yes. show the 'show signature' checkbox
	printf "<input type=\"checkbox\" name=\"showsig\"";
        # should it default to on?
	if (($SIG_SHOWDEFAULT eq "YES") and (&check_flag($flags,$FLAG_DONTCHECKSIG) eq "0")) {
	    # yes. check the box
	    printf " checked";
	}
	printf ">Show signature</input><br>";
    }

    # do we have email abilities?
    if ($EMAIL_METHOD ne "0") {
	# yes. show the 'notification' checkbox
	printf "<input type=\"checkbox\" name=\"notify\">Email Notification<br><font size=1>This will send you an email every time someone replies to this thread</font></input><p>";
    }

    # and do the buttons
    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form>";

    # set up the form
    printf "<form method=\"post\" action=\"forumview.cgi\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showforum\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"submit\" value=\"Cancel\"><br>";
    printf "</form>";

    # end the page
    &NormalPageEnd();
}

#
# DoNewTopic()
#
# This will actually post the new topic. It will post the topic to forum
# $field{"forum"}.
#
sub
DoNewTopic() {
    # validate the ID
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # get the forum information
    my $forum_info=&GetForumInfo($field{"forum"});
    my ($tmp1,$tmp3,$tmp4,$restricted,$tmp4,$tmp5,$forum_flags,$tmp6,$catno,$f_header,$f_footer,$nofthreads,$newtopic_posters,$reply_posters)=split(/:/,$forum_info);

    # if it's a restricted forum, say access denied if we may not access it
    if (&CanViewRestricted() eq 0) {
	# we may not access it
	&error($ERROR_ACCESSDENIED);
    }
   
    # is this forum disabled?
    if (&check_flag($forum_flags,$FLAG_FORUM_DISABLED) ne 0) {
        # yup. show error
	&error("This forum is disabled. You cannot view it or post in it");
    }

    # does the user really have the privileges to do this?
    if (&CanPostNewTopic($newtopic_posters) eq "0") {
        # no. complain
        &error("Sorry, but you don't have access to post a new topic here");
    }

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $field{"forum"} . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yeah. tell the user the forum is locked
        &error($ERROR_FORUMLOCKED);
    }

    # create the lock file
    open(LOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERROR);

    # Try to open the forum data file
    my $forumfile=$FORUM_DIR . $field{"forum"} . $FORUM_EXT;
    open(FORUMDATA,$forumfile)||&error($ERROR_FILEOPENERR);

    # need to log the IP address?
    my $theip;
    if ($IP_LOG_DISPLAY ne "0") {
        # yup. get the IP
        $theip = $ENV{"REMOTE_ADDR"};
    } else {
	# no. use 0.0.0.0 as the IP
	$theip = "0.0.0.0";
    }

    # generate a forum id
    my $forumid = time || $$;
    while (-e $FORUM_DIR . $field{"forum"} . "/" . $forumid) {
       # the file exists. redo it
       $forumid = time || $$;
    }

    # get the time and construct the header
    my $postime = &GetTimeDate();
    my $header = ".:" . $field{"username"} . ":" . $postime . ":0:" . $theip . "\n";

    # remove the : from the SUBJECT field and replace with '...'
    $field{"subjectname"} =~ s/:/\.\.\./g;

    # remove all HTML code in the subject string
    $field{"subjectname"} =~ s/</&lt;/g;
    $field{"subjectname"} =~ s/>/&gt;/g;

    # forcefully exterminate every newline in the subject
    $field{"subjectname"} =~ s/\n//g;

    # if there is no subject name, make it something default
    if ($field{"subjectname"} eq "") {
	$field{"subjectname"} = $DEFAULT_SUBJECT;
    }

    # is it only spaces?
    my $tmpsubject = $field{"subjectname"};
    $tmpsubject=~ s/ //g;
    if ($tmpsubject eq "") {
        # yup. make it default
	$field{"subjectname"} = $DEFAULT_SUBJECT;
    }

    # build the flags
    my $theflags = "";
    if ($field{"notify"} ne "") {
	# we need to add notification. do it
	$theflags .= $FLAG_FORUM_NOTIFY;
    }

    # update the main forum file
    $mainid = $forumid . ":" . $field{"subjectname"} . ":0:" . $postime . ":" . $field{"username"} . ":" . $field{"icon"} . "::" . $theflags . ":" . $field{"username"} . "\n";

    # dump this to the lock file
    printf LOCKFILE $mainid;

    # and append the rest of the forum data
    while ( <FORUMDATA> ) {
        # get a line
        my $line = $_;

        # append it to the lock file
        print LOCKFILE $line;
    }

    # close the forum file
    close(FORUMDATA);

    # now create the actual forum datafile
    open(POSTFILE,"+>" . $FORUM_DIR . $field{"forum"} . "/" . $forumid)||&error($ERROR_FILECREATERR);

    # change all .: to . : thingys
    $field{"text"} =~ s/\.:/\. :/g;

    # get the forum flag
    my $forum_line=&GetForumInfo($field{"forum"});

    # get the flags
    my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags)=split(/:/,$forum_line);

    # do we have to nuke HTML?
    if (&check_flag($forum_flags,$FLAG_FORUM_HTMLOK) eq "0") {
	# yup. do it
       $field{"text"} =~ s/</&lt;/g;
       $field{"text"} =~ s/>/&gt;/g;
    }

    # add the header and the message to the file
    print POSTFILE $header;
    print POSTFILE $field{"text"} . "\n";

    # need to add the signature?
    if (($SIG_ALLOWED eq "YES") and ($field{"showsig"} ne "") and (&check_flag($flags,$FLAG_DENYSIG) eq "0")) {
	# yup. do it
	print POSTFILE "\n----------\n";
        print POSTFILE &FormatSignature($field{"username"}) . "\n";
   }

    # close the forum and lock file
    close(POSTFILE);
    close(LOCKFILE);

    # copy the lock file
    &CopyFile($lockfile,$forumfile);

    # increment the number of posts field
    &IncrementNofPosts();
    &UpdateForumData($field{"forum"},"1","1","1","1");

    # initialize the html page
    my $URL = "forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"forum"});
    &InitPage("","",$URL);

    # show the 'new topic posted ok' message
    printf "<font face=\"$FORUM_FONT\">Topic successfully posted. You will be redirected to the forum in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";

    # end the page
    &NormalPageEnd();
}

#
# DeleteThread()
#
# This will delete a thread. It will delete thread number $field{"postid"}
# in forum $field{"forum"}
#
sub
DeleteThread() {
    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$name,$tmp1,$tmp2,$tmp3,$author)=split(/:/,$line);

    # get the moderator status
    my $ismod = &IsForumMod ($field{"forum"});

    # is this allowed?
    if ((&CanThreadBeDeleted($author,$ismod) eq "0") and (($field{"id"} . $cookie{"id"}) ne "")) {
	# no. complain
	&error("Deleting of threads is disabled");
    }

    # initialize the page
    &InitPage("");

    # write what we are going to do
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">Are you sure you want to delete thread <b><i>%s</i></b>?<br>",&CensorPost ($name);
    # and dump in the 'yes' and 'no' buttons
    printf "<form action=\"forum.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dodeletethread\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "</table><input type=\"submit\" value=\"Yes\">";
    printf "</form>";

    printf "<form action=\"forumview.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showforum\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"submit\" value=\"No\">";
    printf "</form>";

    # end the page
    &NormalPageEnd();
}

#
# DoDeleteThread()
#
# This will actually delete the thread. It will delete thread $field{"postid"}
# in forum $field{"forum"}
#
sub
DoDeleteThread() {
    # verify the authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # if it's a restricted forum, say access denied if we may not access it
    if (&CanViewRestricted() eq 0) {
	# we may not access it
	&error($ERROR_ACCESSDENIED);
    }

    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner)=split(/:/,$line);

    # get the moderator status
    my $ismod = &IsForumMod ($field{"forum"});

    # does the user really have the privileges to do this?
    if (&CanBeDeleted($owner,$ismod) eq 0) {
        # no. complain
        &error($ERROR_ACCESSDENIED);
    }

    # forcefully destroy the thread
    &DestroyThread ($field{"forum"},$field{"postid"});

    # initialize the html page
    my $URL = "forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"forum"});
    &InitPage("","",$URL);

    # show the 'topic deleted ok' message
    printf "<font face=\"$FORUM_FONT\">Topic successfully deleted. You will be redirected to the forum in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";

    # end the page
    &NormalPageEnd();
}

#
# IncrementNofPosts()
#
# This will add one to the number of posts this user has made
#
sub
IncrementNofPosts() {
    # get the record of this account
    my $userline=&GetUserRecord($field{"username"});

    # split the info
    my ($tmp,$passwd,$flags,$nofposts,$fullname,$email,$sig,$extra,$parentemail)=split(/:/,$userline);

    # increment the number of posts
    $nofposts++;

    # and add the new stuff
    my $newrecord = $tmp . ":" . $passwd . ":" . $flags . ":" . $nofposts . ":" . $fullname . ":" . $email . ":" . $sig . ":" . $extra . ":" . $parentemail;

    &SetUserRecord($field{"username"},$newrecord);
}

#
# SetThreadOptions($lockit,$lastreply,$movedest)
#
# This will change thread options. If $lockit is 1, the thread will be locked.
# If it's 2, it will be unlocked. If $lastreply is not a blank string, it will
# be filled in as the user who last replied to this post. If $movedest is
# not blank, it will be filled in as the destination forum, and the appropriate
# flag will be set.
#
sub
SetThreadOptions() {
    # get the arguments
    my ($lockit,$lastreply,$movedest) = @_;

    # first get the old data
    my $data = &GetForumLine ($field{"forum"}, $field{"postid"});
    chop $data;

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $field{"forum"} . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yes. show error
        &error($ERROR_FORUMLOCKED);
    }

    # open the forum file
    my $forumfile=$FORUM_DIR . $field{"forum"} . $FORUM_EXT;
    open(POSTFILE,$forumfile)||&error($ERROR_FILEOPENERR);

    # create the lock file
    open(LOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERR);

    # trace through the complete forum file
    while ( <POSTFILE> ) {
        # get the line
        my $line = $_;
	chop $line;

        # split the line
        my ($forum_id)=split(/:/,$line);

        # is this our id?
        if ($forum_id eq $field{"postid"} ) {
            # yeah, update it
            my ($forum_id,$subject,$nofposts,$postime,$postime2,$author,$icon,$movedforum,$forum_flags,$lastposter,$locker)=split(/:/,$line);
            # need to lock/unlock it?
            if ($lockit ne "0") {
                # yeah. need to lock?
                if ($lockit eq "1") {
                    # yeah. was it locked before?
                    if(&check_flag($forum_flags,$FLAG_FORUM_LOCKED) eq "0") {
                        # nope. add the locked flag
                        $forum_flags = $forum_flags . $FLAG_FORUM_LOCKED;
			$locker = $field{"username"};
                    }
	        } else {
		    # no. unlock it (also make sure the moved flag is gone)
		    $forum_flags=~ s/($FLAG_FORUM_LOCKED)//gi;
		    $forum_flags=~ s/($FLAG_FORUM_MOVED)//gi;
		}
            }
	    # need to fill in the last replier?
	    if ($lastreply ne "") {
		# yup. do it
		$lastposter = $lastreply;
	    }
	    # need to fill in the new forum?
	    if ($movedest ne "") {
		# yup. do it
		$movedforum = $movedest;

		# and set the moved flag
		$forum_flags .= $FLAG_FORUM_MOVED;
	    }
            # format the new line
            $line = $forum_id . ":" . $subject . ":" . $nofposts . ":" . $postime . ":" . $postime2 . ":" . $author . ":" . $icon . ":" . $movedforum . ":" . $forum_flags . ":" . $lastposter . ":" . $locker;
        }
        # write the line to the lockfile
        print LOCKFILE $line . "\n";
    }

    # close the forum file
    close(POSTFILE);

    # close the lock file
    close(LOCKFILE);

    # copy the file
    &CopyFile($lockfile,$forumfile);
}

#
# GetLastForumPoster($forum)
#
# This will return the name of the person who was the last poster of forum
# $forum.
#
sub
GetLastForumPoster() {
    # get the arguments
    my ($forum) = @_;

    # open the forum datafile
    my $forumfile=$FORUM_DIR . $forum . $FORUM_EXT;
    open(FFILE,$forumfile)||&error($ERROR_FILEOPENERR);

    # grab the first line
    my $line = <FFILE>; chop $line;
    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner,$icon,$movedforum,$forum_flags,$lastposter,$locker)=split(/:/,$line);

    # close the datafile
    close(FFILE);

    # and return the last poster
    return $lastposter;
}

#
# ListForums()
#
# This will list the forums
#
sub
ListForums() {
    # open the forum datafile
    open(FORUMDATA,$FORUM_DATAFILE)||&error($ERROR_FILEOPENERR);

    # initialize the page
    &InitPage("");

    # show the logo and some other options
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"33%\"><td><td width=\"33%\" align=\"center\"><font face=\"%s\" size=5>$FORUM_TITLE</font></td><td align=\"right\" width=\"33%\">%s</td></tr>",$FORUM_FONT,&ConstructOptions();
    printf "</table><p>";

    # show the number of registered accounts, if needed
    if ($SHOW_NOF_MEMBERS eq "YES") {
        # show them
        &ShowNofMembers();
    }

    # do the table
    printf "<table width=\"100%\" $FORUM_LIST_TABLE_TAGS>";

    # Section (40%)  | Posts (10%) | Threads (10%) | Newest post Date (20%)
    # Moderator (20%)
    printf "<tr bgcolor=\"%s\"><td width=\"40%\">&nbsp;<font face=\"$FORUM_FONT\" color=\"%s\">Section</font></td><td width=\"10%\"><font face=\"$FORUM_FONT\" color=\"%s\">Posts</font></td><td width=\"10%\"><font face=\"$FORUM_FONT\" color=\"%s\">Threads</font></td><td width=\"20%\"><font face=\"$FORUM_FONT\" color=\"%s\">Newest post date</font></td><td width=\"20%\"><font face=\"$FORUM_FONT\" color=\"%s\">Moderator</font></td></tr>",$FORUM_COLOR_LIST_CELLBACK,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT,&FormatUserGroup (split (/,/, $supermods)),$FORUM_COLOR_LIST_TEXT,,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT;

    # read the complete forum info file
    while ( <FORUMDATA> ) {
        # and dump the lines into $line
        my $line = $_;
        chop $line;

        # split the line
        my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags,$desc,$tmp1,$tmp2,$tmp3,$nofthreads)=split(/:/,$line);
        $date = $date1 . ":" . $date2;
	$date=~ s/\|/ /g;
        $desc = &RestoreSpecialChars ($desc);

        # is this forum enabled and not hidden?
        if ((&check_flag($forum_flags,$FLAG_FORUM_DISABLED) eq "0") and (&check_flag($forum_flags,$FLAG_FORUM_HIDDEN) eq "0")) {
	    # yup. show it

            # add the table line
            printf "<tr bgcolor=\"%s\"><td width=\"20%\"><font color=\"%s\">",$FORUM_COLOR_LIST_CONTENTS_CELLBACK,$FORUM_COLOR_LIST_FORUMNAME;

            printf "<a href=\"forumview.cgi?action=showforum&id=%s&forum=%s\" class=forumlink>%s</a>",$field{"id"},&TransformForBrowser($forum_name),&RestoreSpecialChars ($forum_name);

            # should we show the descriptions?
            if ($SHOW_DESCRIPTIONS eq "YES") {
	        # yup. do it
                printf "<br><font size=2 face=\"$FORUM_FONT\" color=\"$FORUM_COLOR_LIST_INFO\">%s</font>",$desc;
            }

            printf "</td>";
            printf "<td><font color=\"%s\" face=\"$FORUM_FONT\">%s</font></td>",$FORUM_COLOR_LIST_INFO,$nofreplies;
            printf "<td><font color=\"%s\" face=\"$FORUM_FONT\">%s</font></td>",$FORUM_COLOR_LIST_INFO,$nofthreads;
            printf "<td align=\"center\"><font color=\"%s\" face=\"$FORUM_FONT\">%s",$FORUM_COLOR_LIST_INFO, $date;
	    # need to show who was the last poster here?
	    if ($SHOW_LAST_POSTER eq "YES") {
		# yup. show it
		my $lastposter=&GetLastForumPoster ($forum_name);
		printf "<br><font size=1 face=\"$FORUM_FONT\">by <a href=\"finger.cgi?accountname=%s\" class=\"lastpost\">%s</a>",&TransformForBrowser ($lastposter),$lastposter;
	    }
	    printf "</td>";
            printf "<td><font color=\"%s\" face=\"$FORUM_FONT\">",$FORUM_COLOR_LIST_INFO;

	    # build a nice list of the mods
	    print &FormatUserGroup (split (/,/, $mods));
            printf "</td></tr>";
	}
    }

    # close the forum file
    close(FORUMDATA);
   
    # end the table 
    printf "</table>";

    # end the page
    &NormalPageEnd();
}

#
# LockThread()
#
# This will lock thread $field{"postid"} in forum $field{"forum"}
#
sub
LockThread() {
    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$name,$tmp1,$tmp2,$tmp3,$author)=split(/:/,$line);

    # is this allowed?
    if ($ALLOW_LOCK_DELETE eq "0") {
	# no. complain
	&error("Locking of threads is disabled");
    }

    # initialize the page
    &InitPage("");

    # write what we are going to do
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">Are you sure you want to lock thread <b><i>%s</i></b>? Only authorized moderators and administrators can lock a thread.<br>",&CensorPost ($name);

    # and dump in the 'yes' and 'no' buttons
    printf "<form action=\"forum.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dolockthread\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "</table><input type=\"submit\" value=\"Lock it\">";
    printf "</form>";

    printf "<form action=\"forumview.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showforum\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"submit\" value=\"Cancel\">";
    printf "</form>";

    # end the page
    &NormalPageEnd();
}

#
# DoLockThread()
#
# This will actually lock thread $field{"postid"} in forum $field{"forum"}
#
sub
DoLockThread() {
    # verify the authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner)=split(/:/,$line);

    # get the moderator status
    my $ismod = &IsForumMod ($field{"forum"});

    # does the user really have the privileges to do this?
    if (&CanBeLocked($owner,$ismod) eq 0) {
        # no. say no
        &error($ERROR_ACCESSDENIED);
    }

    # lock the thread
    &SetThreadOptions("1", "", "");

    # initialize the html page
    my $URL = "forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"forum"});
    &InitPage("","",$URL);

    # show the 'topic locked ok' message
    printf "<font face=\"$FORUM_FONT\">Topic successfully locked. You will be redirected to the forum in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";


    # end the page
    &NormalPageEnd();
}

#
# UnlockThread()
#
# This will unlock thread $field{"postid"} in forum $field{"forum"}
#
sub
UnlockThread() {
    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$name,$tmp1,$tmp2,$tmp3,$author)=split(/:/,$line);

    # is this allowed?
    if ($ALLOW_UNLOCK ne "YES") {
	# no. complain
	&error("Unlocking of threads is disabled");
    }

    # initialize the page
    &InitPage("");

    # write what we are going to do
    printf "<font color=\"$FORUM_COLOR_TEXT\" face=\"$FORUM_FONT\">Are you sure you want to unlock thread <b><i>%s</i></b>? Only authorized moderators and administrators can unlock a thread.<br>",&CensorPost ($name);

    # and dump in the 'yes' and 'no' buttons
    printf "<form action=\"forum.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dounlockthread\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"postid\" value=\"%s\">",$field{"postid"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "</table><input type=\"submit\" value=\"Unlock it\">";
    printf "</form>";

    printf "<form action=\"forumview.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"showforum\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"forum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"submit\" value=\"Cancel\">";
    printf "</form>";

    # end the page
    &NormalPageEnd();
}

#
# DoUnlockThread()
#
# This will actually unlock thread $field{"postid"} in forum $field{"forum"}
#
sub
DoUnlockThread() {
    # verify the authorization
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"postid"});

    # split it
    my ($forum_id,$subject,$nofposts,$date1,$date2,$owner)=split(/:/,$line);

    # get the moderator status
    my $ismod = &IsForumMod ($field{"forum"});

    # does the user really have the privileges to do this?
    if (&CanBeUnlocked($ismod) eq 0) {
        # no. say no
        &error($ERROR_ACCESSDENIED);
    }

    # unlock the thread
    &SetThreadOptions("2", "", "");

    # initialize the html page
    my $URL = "forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"forum"});
    &InitPage("","",$URL);

    # show the 'topic unlocked ok' message
    printf "<font face=\"$FORUM_FONT\">Topic successfully unlocked. You will be redirected to the forum in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";

    &NormalPageEnd();
}

#
# ListCats()
#
# This will list the forum categories.
#
sub
ListCats() {
    # initialize the page
    &InitPage("");

    # get the category data
    @cats=&GetCats();

    # get the forum data
    @forums=&GetForums();

    # show the logo and some other options
    printf "<table width=\"100%\">";
    printf "<tr><td width=\"33%\"><td><td width=\"33%\" align=\"center\"><font size=5>$FORUM_TITLE</font></td><td align=\"right\" width=\"33%\">%s</td></tr>",&ConstructOptions();
    printf "</table><p>";

    # show the number of registered accounts, if needed
    if ($SHOW_NOF_MEMBERS eq "YES") {
        # show them
        &ShowNofMembers();
    }

    # start the table (Category Name (40%) | Super Moderators (30%)
    # Number of forums (30%)
    printf "<table width=\"100%\" $FORUM_LIST_TABLE_TAGS>";

    printf "<tr bgcolor=\"%s\"><td width=\"40%\"><font face=\"$FORUM_FONT\" color=\"%s\">Category Name</font></td><td bgcolor=\"%s\" width=\"30%\"><font color=\"%s\" face=\"$FORUM_FONT\">Super Moderators</font></td><td bgcolor=\"%s\" width=\"30%\"><font color=\"%s\" face=\"$FORUM_FONT\">Number of forums</font></td></tr>",$FORUM_COLOR_LIST_CELLBACK,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_CELLBACK,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_CELLBACK,$FORUM_COLOR_LIST_TEXT;

    # do all categories
    foreach $cat (@cats) {
	# yup. show it
        my ($name,$catno,$supermods,$desc)=split(/:/,$cat);

        # add the table line
        printf "<tr bgcolor=\"%s\"><td width=\"20%\"><font color=\"%s\">",$FORUM_COLOR_LIST_CONTENTS_CELLBACK,$FORUM_COLOR_LIST_FORUMNAME;

        printf "<a href=\"forum.cgi?action=showcat&id=%s&cat=%s\" class=forumlink>%s</a>",$field{"id"},&FixSpecialChars (&TransformForBrowser($name)),&RestoreSpecialChars ($name);

        # should we show the descriptions?
        if ($SHOW_DESCRIPTIONS eq "YES") {
            # yup. do it
            printf "<br><font size=2 color=\"$FORUM_COLOR_LIST_INFO\">%s</font>",&RestoreSpecialChars ($desc);
        }

        printf "</td>";
        printf "<td><font face=\"$FORUM_FONT\" color=\"%s\">%s</font></td>",$FORUM_COLOR_LIST_INFO,&FormatUserGroup (split (/,/, $supermods));

        # count the number of forums with this category
        my $forumcount=0;
        foreach $forum (@forums) {
	    my ($forumname,$forum_posts,$forum_mods,$forum_restricted,$date1,$date2,$forum_flags,$descr,$forum_catno)=split(/:/,$forum);

	    if ($catno eq $forum_catno) { $forumcount++; };
	}
        printf "<td><font face=\"$FORUM_FONT\" color=\"%s\">%s</td></tr>",$FORUM_COLOR_LIST_INFO,$forumcount;
    }

    # close the forum file
    close(FORUMDATA);
   
    # end the table 
    printf "</table>";

    # end the page
    &NormalPageEnd();
}

#
# ShowCat()
#
# This will show all forums in a certain category.
#
sub
ShowCat() {
    # get the categories
    @cats=&GetCats();

    # figure out this category number
    my $catline=&GetItemFromList($field{"cat"},@cats);

    # did it exists?
    if ($catline eq "") {
	# no. show error
	my $name = &RestoreSpecialChars ($field{"cat"});
	&error("Category <b>$name</b> doesn't exists");
    }

    # split the list
    my ($name,$the_catno,$supermods,$tmp2,$header,$footer)=split(/:/,$catline);

    # replace |header| by the general header
    $START_PAGE_TEXT=~s /\|header\|//g;
    $header=~ s/\|header\|/$START_PAGE_TEXT/;
    $header=&RestoreSpecialChars($header);

    # and |footer| by the general footer
    $END_PAGE_TEXT=~s /\|footer\|//g;
    $footer=~ s/\|footer\|/$END_PAGE_TEXT/;
    $footer=&RestoreSpecialChars($footer);

    # open the forum datafile
    open(FORUMDATA,$FORUM_DATAFILE)||&error($ERROR_FILEOPENERR);

    # initialize the page
    &InitPage("",$header);

    # show the logo and some other options
    printf "<table width=\"100%\">";

    printf "<tr><td width=\"33%\"><td><td width=\"33%\" align=\"center\"><font face=\"$FORUM_FONT\" size=5><a href=\"forum.cgi?id=%s\">$FORUM_TITLE</a></font><br><font face=\"$FORUM_FONT\">Category: %s</font></td><td align=\"right\" width=\"33%\">%s</td></tr>",$field{"id"},&RestoreSpecialChars ($field{"cat"}),&ConstructOptions();
    printf "</table><p>";

    # show the number of registered accounts, if needed
    if ($SHOW_NOF_MEMBERS eq "YES") {
        # show them
        &ShowNofMembers();
    }

    # do the table
    printf "<table width=\"100%\" $FORUM_LIST_TABLE_TAGS>";

    # Section (40%)  | Posts (10%) | Threads (10%) | Newest post (20%)
    # Moderator (20%)
    printf "<tr bgcolor=\"%s\"><td width=\"40%\"><font face=\"$FORUM_FONT\" color=\"%s\">Section</font><br><font face=\"$FORUM_FONT\" size=2 color=\"%s\">Super moderators: %s</font></td><td width=\"10%\"><font face=\"$FORUM_FONT\" color=\"%s\">Posts</font></td><td width=\"10%\"><font face=\"$FORUM_FONT\" color=\"%s\">Threads</font></td><td width=\"20%\"><font face=\"$FORUM_FONT\" color=\"%s\">Newest post</font></td><td width=\"20%\"><font face=\"$FORUM_FONT\" color=\"%s\">Moderator</font></td></tr>",$FORUM_COLOR_LIST_CELLBACK,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT,&FormatUserGroup (split (/,/, $supermods)),$FORUM_COLOR_LIST_TEXT,,$FORUM_COLOR_LIST_TEXT,$FORUM_COLOR_LIST_TEXT;

    # read the complete forum info file
    while ( <FORUMDATA> ) {
        # and dump the lines into $line
        my $line = $_;
        chop $line;

        # split the line
        my ($forum_name,$nofreplies,$mods,$restricted,$date1,$date2,$forum_flags,$desc,$forum_catno,$tmp_header,$tmp_footer,$nofthreads)=split(/:/,$line);
        $date = $date1 . ":" . $date2;

	# change the pipe (|) to a space (it's being used as a marker for
	# different colors in the forum)
	$date=~tr/\|/ /;

        # is this forum enabled?
        if ((&check_flag($forum_flags,$FLAG_FORUM_DISABLED) eq "0") and (&check_flag($forum_flags,$FLAG_FORUM_HIDDEN) eq "0") and ($the_catno eq $forum_catno)) {
	    # yup. show it

            # add the table line
            printf "<tr bgcolor=\"%s\"><td width=\"20%\"><font color=\"%s\">",$FORUM_COLOR_LIST_CONTENTS_CELLBACK,$FORUM_COLOR_LIST_FORUMNAME;

            printf "<a href=\"forumview.cgi?action=showforum&id=%s&forum=%s\" class=forumlink>%s</a>",$field{"id"},&TransformForBrowser($forum_name),&RestoreSpecialChars ($forum_name);

            # should we show the descriptions?
            if ($SHOW_DESCRIPTIONS eq "YES") {
	        # yup. do it
                printf "<br><font face=\"$FORUM_FONT\" size=2 color=\"$FORUM_COLOR_LIST_INFO\">%s</font>",&RestoreSpecialChars ($desc);
            }

            printf "</td>";
            printf "<td><font face=\"$FORUM_FONT\" color=\"%s\">%s</font></td>",$FORUM_COLOR_LIST_INFO,$nofreplies;
            printf "<td><font font=\"$FORUM_FONT\" color=\"%s\">%s</font></td>",$FORUM_COLOR_LIST_INFO,$nofthreads;
            printf "<td align=\"center\"><font face=\"$FORUM_FONT\" color=\"%s\">%s",$FORUM_COLOR_LIST_INFO,$date;
	    if ($SHOW_LAST_POSTER eq "YES") {
		# yup. show it
		my $lastposter=&GetLastForumPoster ($forum_name);
		printf "<font size=1 face=\"$FORUM_FONT\"><br>by <a href=\"finger.cgi?accountname=%s\" class=\"lastpost\">%s</a></font>",&TransformForBrowser ($lastposter),$lastposter;
	    }
	    printf "</td>";

	    # any moderators?
	    if ($mods eq "") {
		# no. set it to a space, so the cell won't be messed up
		$mods="&nbsp";
	    }

            printf "<td><font face=\"$FORUM_FONT\" color=\"%s\">%s</td></tr>",$FORUM_COLOR_LIST_INFO,&FormatUserGroup (split (/,/, $mods));
	}
    }

    # close the forum file
    close(FORUMDATA);
   
    # end the table 
    printf "</table>";

    # end the page
    &NormalPageEnd($footer);
}

#
# RequestLogin()
#
# This will request the user to login in. This will only be called if this is
# an user who is not logged in trying to access us when we require logging in.
#
sub
RequestLogin() {
    # show the page
    &InitPage("");

    printf "<font face=\"$FORUM_FONT\">This forum system requires you to log in. Please fill in your username and password below, so we can grant you access to the forum<br>";

    printf "<form action=\"forum.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"dologin\">";

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("1");

    printf "</table>";
    printf "<input type=\"submit\" value=\"OK\">";
    printf "</form></font>";

    &NormalPageEnd();
}

#
# MaXHelp()
#
# This will show the ForuMAX MaX code help.
#
sub
MaXHelp() {
    # initialize and show the page
    &InitPage("");

    printf qq~
<center><font size=5 face="$FORUM_FONT"><b>MaX codes</b></font></center><hr>
<font face="$FORUM_FONT">MaX codes can be used to put special options in your message. For example, if the site administrator has disabled HTML but enabled MaX codes, you can always create a link using this code. All allowed MaX codes are described below:
<p>
<table width="100%" border=1>
  <tr>
    <td width="20%">[img]<i>name</i>[/img]</td>
    <td width="80%">This will put an image in your post. The source datafile used is <i>name</i>. <font color="#ff0000">Note: the site adminstrator can disable images!</font><p>
     For example: <code>[img]http://www.mysite.com/images/myimage.jpg[/img]</code>
    </td>
  </tr>
  <tr>
    <td>[b]<i>text</i>[/b]</td>
    <td>This will print <i>text</i> in bold<p>
    For example: <code>[b]Bold text[/b]</code> will result in <b>Bold text</b>
  </tr>
  <tr>
    <td>[b]<i>text</i>[/i]</td>
    <td>This will print <i>text</i> in italic<p>
    For example: <code>[i]Italic text[/i]</code> will result in <i>Italic text</i>
  </tr>
  <tr>
    <td>[u]<i>text</i>[/u]</td>
    <td>This will print <i>text</i> underlined<p>
    For example: <code>[u]Underlined text[/u]</code> will result in <u>Underlined text</u>
  </tr>
  <tr>
    <td>[url]<i>location</i>[/url]</td>
    <td>This will create a hyperlink to <i>location</i><p>
    For example: <code>[url]http://www.forumax.com[/url]</code> will result in <a href="http://www.forumax.com">http://www.forumax.com</a>
  </tr>
  <tr>
    <td>[url=<i>text</i>]<i>location</i>[/url]</td>
    <td>This will create a hyperlink to <i>location</i>, but with <i>text</i> as the link name<p>
    For example: <code>[url=Link to ForuMAX]http://www.forumax.com[/url]</code> will result in <a href="http://www.forumax.com">Link to ForuMAX</a>
  </tr>
  <tr>
    <td>[email]<i>location</i>[/email]</td>
    <td>This will create a hyperlink to email address <i>address</i><p>
    For example: <code>[email]webmaster\@forumax.com[/email]</code> will result in <a href="mailto:webmaster\@forumax.com">webmaster\@forumax.com</a>
  </tr>
  <tr>
    <td>[code]<i>code</i>[/code]</td>
    <td>This will create a piece of text that shows code<p>
    For example: <code>[code]echo "ForuMAX";[/code]</code> will result in:<p>
    <blockquote><code><font size=1>code:</font><br><hr><code>echo "ForuMAX";</code><hr></blockquote>
  </tr>
  <tr>
    <td>[quote]<i>quote</i>[/quote]</td>
    <td>This will create a piece of text that shows a quote<p>
    For example: <code>[quote]Errare humanum est[/quote]</code> will result in:<p>
    <blockquote><font size=1>quote:</font><br><hr>Errare humanum est<hr></blockquote>
  </tr>
</table></font>
~;

    # end the page
    &NormalPageEnd();
}

#
# SmilieLegend()
#
# This will show the smilie legend.
#
sub
SmilieLegend() {
    # initialize and show the page
    &InitPage("");

    printf qq~
<center><font size=5 face="$FORUM_FONT"><b>Smilie Legend</b></font></center><hr>
<font face="$FORUM_FONT">Smilies are cute icons that you can display in your posts by typing special words, which will get replaced by the cute smilie. Below are all smilies shown, which the special word to trigger them
<p>
<table width="100%" border=1>
  <tr>
    <td width="40%"><b>Word that triggers the smilie</b></td>
    <td width="60%"><b>Smilie that will be triggered</b></td>
  </tr>
~;
    my @splitsmilies = split (/\|/, $SMILIES);
    foreach $happyface (@splitsmilies) {
        my @part = split (/\=/, $happyface);

        printf "<tr><td><code>%s</code></td><td><img src=\"$IMAGES_URI/%s\" alt=\"[Smilie]\"></td></tr>", $part[0], $part[1];
    }

    printf "</table></font>";

    # end the page
    &NormalPageEnd();
}

# check whether the forum is disabled, if it is, disallow access
if ($FORUM_DISABLED eq "YES") {
    # it is disabled. deny all access
    # initialize the html page
    &HTMLHeader();
    &InitPage("");

    printf "<center>The forum is currently disabled. Try again later</center>";

    NormalPageEnd();
    exit;
}

#
# MoveThread()
#
# This will show the page to move a thread.
#
sub
MoveThread() {
    # get the forum line
    my $line = &GetForumLine ($field{"forum"},$field{"threadid"});

    # split it
    my ($forum_id,$name,$tmp1,$tmp2,$tmp3,$author)=split(/:/,$line);

    # show the page
    &InitPage("","");

    printf "<font face=\"$FORUM_FONT\">You have chosen to move thread <b>%s</b> to another forum. Please select the forum from the list below:<p>", $name;
    
    my @forums = &GetForums();

    printf "<form action=\"forum.cgi\" method=\"post\">";
    printf "<input type=\"hidden\" name=\"action\" value=\"domovethread\">";
    printf "<input type=\"hidden\" name=\"id\" value=\"%s\">",$field{"id"};
    printf "<input type=\"hidden\" name=\"sourceforum\" value=\"%s\">",$field{"forum"};
    printf "<input type=\"hidden\" name=\"threadid\" value=\"%s\">",$field{"threadid"};

    printf "Move thread to forum ";

    printf "<select name=\"destforum\">";

    foreach $theforum (@forums) {
	# get the name and flags
        my ($name,$tmp1,$tmp2,$tmp3,$tmp4,$tmp5,$forum_flags) = split(/:/,$theforum);

	# is this forum not hidden and enabled, and not the current forum?
	if ((&check_flag ($forum_flags,$FLAG_FORUM_HIDDEN) eq 0) and (&check_flag ($forum_flags,$FLAG_FORUM_DISABLED) eq 0) and ($name ne $field{"forum"})) {
	    # yup. add it to the list
	    printf "<option name=\"%s\">%s</option>",$name,$name;
	}
    }

    printf "</select><p>";

    printf "<table>";

    # if we are authorized, don't show the username/password stuff
    &UsernamePasswordForm("0");

    printf "</table><p><input type=\"submit\" value=\"Move thread\">";
    printf "</form>";

    &NormalPageEnd();
}

#
# DoMoveThread()
#
# This will actually move the thread to the other forum.
#
sub
DoMoveThread() {
    # first of all, verify the access
    &VerifyID();

    # build the HTML header
    &HTMLHeader();

    # grab the moderator status
    my $ismod = &IsForumMod ($field{"sourceforum"});
	
    # do we have rights to move this thread?
    if (&CanMoveThread($field{"sourceforum"},$ismod) eq 0) {
	# no. complain
	&error($ERROR_ACCESSDENIED);
    }

    # generate a random id for the other forum
    my $forumid = time || $$;
    while (-e $FORUM_DIR . $field{"destforum"} . "/" . $forumid) {
       # the file exists. redo it
       $forumid = time || $$;
    }

    # does the forum lock file exists?
    my $lockfile=$FORUM_DIR . $field{"destforum"} . $FORUM_EXT_LOCK;
    if ( -e $lockfile ) {
        # yeah. tell the user the forum is locked
        &error($ERROR_FORUMLOCKED);
    }

    # create the lock file
    open(XLOCKFILE,"+>" . $lockfile)||&error($ERROR_FILECREATERROR);

    # grab information about the source thread
    my $line = &GetForumLine ($field{"sourceforum"}, $field{"threadid"});
    my ($tmp,$subject,$nofposts,$date1,$date2,$owner,$icon,$newforum,$forum_flags,$lastposter)=split(/:/,$line);

    # copy this thread to the other forum
    &CopyFilePreserve ($FORUM_DIR . $field{"sourceforum"} . "/" . $field{"threadid"}, $FORUM_DIR . $field{"destforum"} . "/". $forumid);

    # lock the old one and mark it as moved
    $field{"forum"} = $field{"sourceforum"}; $field{"postid"} = $field{"threadid"};
    &SetThreadOptions("1", "", $field{"destforum"});

    # open the forum data file
    my $forumfile=$FORUM_DIR . $field{"destforum"} . $FORUM_EXT;
    open(FORUMDATA,$forumfile)||&error($ERROR_FILEOPENERR);

    # add our new entry
    print XLOCKFILE $forumid . ":" . $subject . ":" . $nofposts . ":" . $date1 . ":" . $date2 . ":" . $owner . ":" . $icon . "::" . $forum_flags . ":" . $lastposter . "\n";

    # now, add everything else
    while (<FORUMDATA>) {
	print XLOCKFILE $_;
    }

    # close the files 
    close (FORUMDATA);
    close (XLOCKFILE);

    # copy the lockfile over the original one
    &CopyFile ($lockfile, $forumfile);

    # and finally, increment the forum posts
    &UpdateForumData($field{"destforum"},"1", "0", $nofposts + 1,"2");

    # initialize the html page
    my $URL = "forumview.cgi?action=showforum&id=" . $field{"id"} . "&forum=" . &TransformForBrowser ($field{"sourceforum"});
    &InitPage("","",$URL);

    # show the 'topic moved ok' message
    printf "<font face=\"$FORUM_FONT\">Topic successfully moved. You will be redirected to the source forum in 2 seconds. Please click <a href=\"$URL\">here</a> if nothing happens</font>";

    # end the page
    &NormalPageEnd();
}

# do we use the id= stuff but have a cookie?
if (($USE_COOKIES ne "YES") and ($cookie{"id"} ne "")) {
    # yup. kill it
    &SetCookie ("id", "", 0);
    $cookie{"id"} = "";
}

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
	&HTMLHeader();
        &RequestLogin();
        exit;
    }
}

if ($field{"action"} eq "") {
    # no action was given. show the forums or categories, depending on the
    # settings
    # do we have to start with the categories?
    &HTMLHeader();
    if ($SHOW_CATS eq "YES") {
        # yup, list the categories
        &ListCats();
    } else {
        # nope, list the forums
        &ListForums();
    }

    # get outta here!
    exit;
}

if ($field{"action"} eq "listforums") {
    # the user wants to list the main forums. do it
    &HTMLHeader();
    &ListForums();

    # get outta here!
    exit;
}

if ($field{"action"} eq "deletepost" ) {
    # the user wants to delete a posted message. do it
    &HTMLHeader();
    &DeletePost();

    # get outta here!
    exit;
}

if ($field{"action"} eq "dodeletepost" ) {
    # the user wants to actually nuke the posted message. do it
    &DoDeletePost();

    # get outta here!
    exit;
}

if ($field{"action"} eq "postreply" ) {
    # the user wants to post a reply to a forum. do it
    &HTMLHeader();
    &PostReply();

    # get outta here!
    exit;
}

if ($field{"action"} eq "dopostreply" ) {
    # the user wants to actually post the reply. do it
    &DoPostReply();

    # get outta here!
    exit;
}

if ($field{"action"} eq "editpost" ) {
    # the user wants to edit a post. do it
    &HTMLHeader();
    &EditPost();

    # get outta here!
    exit;
}

if ($field{"action"} eq "doeditpost" ) {
    # the user wants to actually edit the post. do it
    &DoEditPost();

    # get outta here!
    exit;
}

if ($field{"action"} eq "newtopic" ) {
    # the user wants to add a topic to the list. do it
    &HTMLHeader();
    &NewTopic();

    # get outta here!
    exit;
}

if ($field{"action"} eq "donewtopic" ) {
    # the user wants to actually add the topic to the list. do it
    &DoNewTopic();

    # get outta here!
    exit;
}

if ($field{"action"} eq "deletethread" ) {
    # the user wants to delete a thread. do it
    &HTMLHeader();
    &DeleteThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "dodeletethread" ) {
    # the user wants to actually delete the thread. do it
    &DoDeleteThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "lockthread" ) {
    # the user wants to lock the thread. do it
    &HTMLHeader();
    &LockThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "dolockthread" ) {
    # the user wants actually lock the thread. do it
    &DoLockThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "unlockthread") {
    # the user wants to unlock the thread. do it
    &HTMLHeader();
    &UnlockThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "dounlockthread") {
    # the user wants to actually unlock the thread. do it
    &DoUnlockThread();

    # get outta here!
    exit;
}

if ($field{"action"} eq "listcats") {
    # the user wants to see the categories. do it
    &HTMLHeader();
    &ListCats();

    # get outta here
    exit;
}

if ($field{"action"} eq "showcat") {
    # the user wants to see a certain category. do it
    &HTMLHeader();
    &ShowCat();

    # get outta here
    exit;
}

if ($field{"action"} eq "maxcodes") {
    # the user wants to view the MaX code help. do it
    &HTMLHeader();
    &MaXHelp();

    # get outta here
    exit;
}

if ($field{"action"} eq "showip") {
    # the user wants to view an ip address. do it
    &HTMLHeader();
    &ShowIP();

    # get outta here
    exit;
}

if ($field{"action"} eq "doshowip") {
    # the user wants to actually view an ip address. do it
    &DoShowIP();

    # get outta here
    exit;
}

if ($field{"action"} eq "smilielegend") {
    # the user wants to see the smilie legend. show it
    &HTMLHeader();
    &SmilieLegend();

    # get outta here
    exit;
}

if ($field{"action"} eq "movethread") {
    # the user wants to move a thread. do it
    &HTMLHeader();
    &MoveThread();

    # get outta here
    exit;
}

if ($field{"action"} eq "domovethread") {
    # the user wants to actually move the thread. do it
    &DoMoveThread();

    # get outta here
    exit;
}

# show the 'unknown request' error
&error("You made a request we don't handle");
