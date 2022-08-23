#!/usr/bin/perl
#
# ForuMAX Version 4.1 - forum_options.pl
#
# This contains the forum options. You can set them via the control panel.
#
# ForuMAX is (c) 1999, 2000 Rink Springer. See http://www.forumax.com for
# license information.
#

# $DIRS_SETUP will indiciate whether the directories have been set up. It can
# be YES or NO. The control panel will allow you to alter them if this is NO.
$DIRS_SETUP=qq~NO~;

# $TMPCONF_FILE is the name of the temponary configuration file. It must reside
# in a directory where whatever user that runs the webserver can write.
$TMPCONF_FILE=qq~/www/cgi-bin/forum/forum_options.TMP.pl~;

# $CONF_FILE is the name of the real configuration file. It must be readable
# and writeable by whoever runs the web server
$CONF_FILE=qq~/www/cgi-bin/forum/forum_options.pl~;

# $FORUM_EXT is the extension of the forum files. It must include the dot
$FORUM_EXT=qq~.forum~;

# $FORUM_EXT_LOCK is the extension of the forum lock files. It must include
# the dot
$FORUM_EXT_LOCK=qq~.forum.LOCK~;

# $FORUM_COLOR_xxx are the forum colors. They must be in the form #xxxxxx
$TEXT_COLOR=qq~#CCCCFF~;
$FORUM_COLOR_TEXT=qq~#000000~;
$FORUM_COLOR_MEMBERLINK=qq~#ffff00~;
$FORUM_COLOR_MEMBERLINK_HOVER=qq~#FFFFFF~;
$FORUM_COLOR_LIST_CELLBACK=qq~#a0a0a0~;
$FORUM_COLOR_LIST_CONTENTS_CELLBACK=qq~#1010A0~;
$FORUM_COLOR_LIST_INFO=qq~#ffffff~;
$FORUM_COLOR_LIST_FORUMNAME=qq~#ffff00~;
$FORUM_COLOR_MEMBERLINK=qq~#ffff00~;
$FORUM_COLOR_MEMBERLINK_HOVER=qq~#FFFFFF~;
$FORUM_COLOR_LIST_TEXT=qq~#000000~;
$FORUM_COLOR_LIST_FORUMNAME_HOVER=qq~#ff0000~;
$FORUM_COLOR_THREAD_CONTENTS_CELLBACK=qq~#000080~;
$FORUM_COLOR_LIST_INFO=qq~#ffffff~;
$FORUM_COLOR_LIST_FORUMNAME=qq~#ffff00~;
$FORUM_COLOR_SUBJECTLINK=qq~#ffffff~;
$FORUM_COLOR_SUBJECTLINK_HOVER=qq~#ffffff~;
$FORUM_COLOR_LIST_TEXT=qq~#000000~;
$FORUM_COLOR_LIST_FORUMNAME_HOVER=qq~#ff0000~;
$FORUM_COLOR_THREAD_CELLBACK=qq~#000000~;
$FORUM_COLOR_THREAD_CONTENTS_CELLBACK=qq~#000080~;
$FORUM_COLOR_THREAD_TEXT=qq~#ffff00~;
$FORUM_COLOR_THREAD_DATECOLOR1=qq~#ffff00~;
$FORUM_COLOR_THREAD_DATECOLOR2=qq~#ffff00~;
$FORUM_COLOR_POST_CELLBACK=qq~#A0A0A0~;
$FORUM_COLOR_POST_1_INFO_CELLBACK=qq~#004000~;
$FORUM_COLOR_POST_1_POST_CELLBACK=qq~#000060~;
$FORUM_COLOR_POST_1_TEXT=qq~#FFFFFF~;
$FORUM_COLOR_POST_2_INFO_CELLBACK=qq~#000060~;
$FORUM_COLOR_POST_2_POST_CELLBACK=qq~#004000~;
$FORUM_COLOR_POST_2_TEXT=qq~#FFFFFF~;
$FORUM_COLOR_POST_INFOTEXT=qq~#000000~;
$FORUM_COLOR_POST1=qq~#FFFFFF~;
$FORUM_COLOR_POST2=qq~#ffffff~;
$FORUM_COLOR_BACKGROUND=qq~#ffffff~;

$FORUM_COLOR_LASTPOSTER_LINK=qq~#ffff00~;

$FORUM_COLOR_LASTPOSTER_HOVER=qq~#ff00ff~;

# $FORUM_POSTS_AT_A_SCREEN will say how much posts there can be at a screen
# at a single time
$FORUM_POSTS_AT_A_SCREEN=qq~25~;

# $FORUM_THREADS_AT_A_SCREEN will indicate how much threads there will be
# listed at a single screen.
$FORUM_THREADS_AT_A_SCREEN=qq~20~;

# $FORUM_DIR indicates where the forum directory is. All forum datafiles will
# be kept there. It should include the slash.
$FORUM_DIR=qq~/www/db/forum/~;

# $FORUM_DATAFILE indicates where the main forum datafile is. It should be
# a complete directory and filename pair, and unaccessible to the outside
# world.
$FORUM_DATAFILE=qq~/www/db/forum/forumdata~;

# $FORUM_LOCKFILE is the lockfile of the forum. It should be
# a complete directory and filename pair, and unaccessible to the outside
# world.
$FORUM_LOCKFILE=qq~/www/db/forum/forumdata.LOCK~;

# $ADMIN_EMAIL should be the email address of the forum administrator
$ADMIN_EMAIL=qq~webmaster\@yoursite.com~;

# $FORUM_TITLE is the title that will be given to every page
$FORUM_TITLE=qq~Your Forums~;

# $IMAGES_URI is the URI where the images are located.
$IMAGES_URI=qq~http;//www.yoursite.com/forum_images~;

# $USERDB_FILE is the file where the user database resides. This file should
# NEVER be accessable by the public.
$USERDB_FILE=qq~/www/db/forum/accounts~;

# $USERDB_LOCKFILE is the lockfile of the user database. This file should
# NEVER be accessable by the public.
$USERDB_LOCKFILE=qq~/www/db/forum/accounts.LOCK~;

# $FORUM_FILE_PERMS is the permission the new forum files will get.
$FORUM_FILE_PERMS=qq~438~;

# $FORUM_DIR_PERMS is the permission the new forum directories will get.
$FORUM_DIR_PERMS=qq~511~;

# $FORUM_OPTION_SHOWINFO will indicate whether information about the poster
# will be printer under the username in every thread.
$FORUM_OPTION_SHOWINFO=qq~YES~;

# $TITLE_ADMIN will indicate the title of an administrator account
$TITLE_ADMIN=qq~Administrator~;

# $TITLE_MEGAMOD will indicate the title of a mega moderator account
$TITLE_MEGAMOD=qq~Mega Moderator~;

# $TITLE_SUPERMOD will indicate the title of a super moderator account
$TITLE_SUPERMOD=qq~Super Moderator~;

# $TITLE_MOD will indicate the title of a moderator account
$TITLE_MOD=qq~Moderator~;

# $TITLE_MEMBER will indicate the title of a normal account
$TITLE_MEMBER=qq~Member~;

# $TITLE_NOMEMBER will indicate the title of a unknown accounts
$TITLE_NOMEMBER=qq~Banned Member~;

# $TIMEZONE should be the server timezone.
$TIMEZONE=qq~UTC~;

# $WEBSITE_URI should be the URI of the website of which this forum belongs to.
$WEBSITE_URI=qq~http://www.yoursite.com~;

# $WEBSITE_LINK should be the text of the link to the website
$WEBSITE_LINK=qq~Your Site~;

# $NOF_ICONS should be the number of icons you want to include, or NO if you
# don't want any icons.
$NOF_ICONS=qq~12~;

# $DEFAULT_SUBJECT is the default subject name, if nothing was given
$DEFAULT_SUBJECT=qq~(no subject)~;

# $FORUM_DISABLED is a flag that will entirely disable the forum when it is
# YES. It should be NO if you don't want this.
$FORUM_DISABLED=qq~YES~;

# $FORUM_POLICIES are the forum policies
$FORUM_POLICIES=qq~[new members policy]~;

# $ALLOW_REGISTRATION is the flag whether we should allow registration of new
# accounts. It should be YES or NO.
$ALLOW_REGISTRATION=qq~YES~;

# $SHOW_DESCRIPTIONS is the flag whether we should descriptions or not. It
# should be YES or NO.
$SHOW_DESCRIPTIONS=qq~YES~;

# $SHOW_CATS is the flag whether we should start with the category display or
# not. It should be YES or NO.
$SHOW_CATS=qq~YES~;

# $CATS_DATAFILE is the file where the category database resides. This file
# should NEVER be accessable by the public.
$CATS_DATAFILE=qq~/www/db/forum/cats~;

# $CATS_LOCKFILE is the lockfile of category database resides. This file should
# NEVER be accessable by the public.
$CATS_LOCKFILE=qq~/www/db/forum/cats.LOCK~;

# $EMAIL_METHOD is the way we send emails. 0 means disable, 1 is
# sendmail and 2 is SMTP
$EMAIL_METHOD=qq~1~;

# $SENDMAIL_LOCATION specifies where sendmail(1) is located
$SENDMAIL_LOCATION=qq~/usr/sbin/sendmail~;

# $SMTP_BOX is the hostname or IP address of the box that does SMTP for us
$SMTP_BOX=qq~smtp.yoursite.com~;

# $SMTP_PORT is the port $SMTP_BOX is listening on
$SMTP_PORT=qq~25~;

# $START_PAGE_TEXT is the text that will be appended to the top of every forum
# page.
$START_PAGE_TEXT=qq~~;

# $END_PAGE_TEXT is the text that will be appended to the bottom of every forum
# page.
$END_PAGE_TEXT=qq~~;

# $FORUM_THREAD_TABLE_TAGS are the tags will be be added to the table tag
# in the forum thread list.
$FORUM_THREAD_TABLE_TAGS=qq~border=1 cellspacing=0 cellpadding=0~;

# $FORUM_POST_TABLE_TAGS are the tags will be be added to the table tag
# in the forum post list.
$FORUM_POST_TABLE_TAGS=qq~border=1 cellspacing=0 cellpadding=0~;

# $FORUM_LIST_TABLE_TAGS are the tags will be be added to the table tag
# in the forum list.
$FORUM_LIST_TABLE_TAGS=qq~border=1 cellspacing=0 cellpadding=0~;

# $SHOW_EDIT will indiciate whether an 'edited by ...' will be appended
# when it has been edited. It should be YES or NO
$SHOW_EDIT=qq~YES~;

# $ALTER_LOCKED will indicate whether a locked thread can be altered
# (eg deleting/editing of messages)
$ALTER_LOCKED=qq~YES~;

# $RECOVER_PASSWORD will indicate whether an user can recover his
# password via email. Only works if you also have a SMTP server
$RECOVER_PASSWORD=qq~YES~;

# $ALLOW_EDIT_DELETE will indicate whether editing/deleting/both
# of posts is allowed. 0 means no to both, 1 means yes to editing
# but no to deleting and anything else is ok to both
$ALLOW_EDIT_DELETE=qq~2~;

# $ALLOW_LOCK_DELETE will indicate whether locking/deleting/both
# of threads is allowed. 0 means no to both, 1 means yes to editing
# but no to locking and anything else is ok to both
$ALLOW_LOCK_DELETE=qq~2~;

# $SHOW_HOPTO indiciates where we will show the 'hop to' list. It
# should be YES or NO
$SHOW_HOPTO=qq~YES~;

# $REQUIRE_VALID_EMAIL indicates whether we should email a random
# password to an user upon registering
$REQUIRE_VALID_EMAIL=qq~YES~;

# $REQUIRE_UNIQUE_EMAIL indicates whether we should check for
# unique emails upon registering
$REQUIRE_UNIQUE_EMAIL=qq~YES~;

# $REQUIRE_LOGIN indicates whether users must login before they can use
# the forums
$REQUIRE_LOGIN=qq~NO~;

# $SHOW_NOF_MEMBERS indicates whether we will show how much members
# there are on the site
$SHOW_NOF_MEMBERS=qq~YES~;

# $ALLOW_UNLOCK indicates whether unlocking threads is allowed. It
# can be YES or NO
$ALLOW_UNLOCK=qq~YES~;

# $SIG_ALLOWED indicates whether signatures (text that can be appened
# to posts) is allowed. It can be YES or NO
$SIG_ALLOWED=qq~NO~;

# $SIG_SHOWDEFAULT indicates whether we will initially check the
# 'show signature' checkbox. It can be YES or NO
$SIG_SHOWDEFAULT=qq~NO~;

# $SIG_ALLOW_HTML indicates whether we will allow HTML code in
# signatures. It can be YES or NO
$SIG_ALLOW_HTML=qq~YES~;

# $SIG_ALLOW_MAX indicates whether we will allow MaX code in
# signatures. It can be YES or NO
$SIG_ALLOW_MAX=qq~YES~;

# $SIG_DENY_EVIL_HTML indicates whether JavaScript and the likes
# must be removed from the sig. It can be YES or NO
$SIG_DENY_EVIL_HTML=qq~YES~;

# $SIG_ALLOW_IMGS indicates whether images are allowed in
# signatures. It can be YES or NO
$SIG_ALLOW_IMGS=qq~NO~;

# $CENSOR_POSTS indicates wether posts will be censored. It can be YES
# or NO
$CENSOR_POSTS=qq~NO~;

# $CENSORED_WORDS indicates the actual words that will be censored
$CENSORED_WORDS=qq~~;

# $BANNED_EMAIL are the email addresses that are banned
$BANNED_EMAIL=qq~~;

# $BANNED_IP are the IP addresses that are banned
$BANNED_IP=qq~~;

# $COPPA_ENABLED indicates whether COPPA compliance is enabled.
# It can be YES or NO
$COPPA_ENABLED=qq~NO~;

# $COPPA_KID_INSTR are the instructions for the kid
$COPPA_KID_INSTR=qq~coppa_kid_form~;

# $COPPA_PARENT_INSTR are the instructions for the kid
$COPPA_PARENT_INSTR=qq~coppa_parent_form~;

# $FORCE_LOGIN will force the user to login if the login
# information supplied is correct. It can be YES or NO
$FORCE_LOGIN=qq~YES~;

# $IP_LOG_DISPLAY indicates whether IP addresses will be
# logged, and who can view them if they are. 0 means that
# IP's are not logged and cannot be displayed, 1 means
# that they are logged but can only be viewed by admins,
# 2 means they are logged and viewable by admins and mods
# 3 means that they are logged and anyone can view them
$IP_LOG_DISPLAY=qq~1~;

# $EXTRA_STYLE is extra style sheet information. It will be added
# to the <style type="text/css"> HTML tag
$EXTRA_STYLE=qq~~;

# $REQUIRE_ICON will indicate whether the user is required to
# select an icon for a new thread. It can be YES or NO
$REQUIRE_ICON=qq~YES~;

# $FORUM_FONT is the font that will be used in the forums
$FORUM_FONT=qq~Verdana,Arial~;

# $SMILIES are the forum smilies
$SMILIES=qq~;a=icon1.gif|:check:=icon2.gif|;c=icon3.gif|;d=icon4.gif|:stop:=icon5.gif|:arrow:=icon6.gif|:)=icon7.gif|;(=icon8.gif|:flame:=icon9.gif|;)=icon10.gif|;k=icon11.gif|;l=icon12.gif~;

# $SHOW_LOCKER will indicate whether the forum will show who
# locked a thread or not. It can be YES or NO.
$SHOW_LOCKER=qq~YES~;

# $EXTRA_PROFILE_FIELDS are the names of the extra profile fields
# They are separated by |'s
$EXTRA_PROFILE_FIELDS=qq~Custom Status|New field~;

# $EXTRA_PROFILE_TYPES are the types of the extra profile fields
# They are separated by |'s
$EXTRA_PROFILE_TYPES=qq~7|0~;

# $EXTRA_PROFILE_HIDDEN are the hidden flags of the extra profile fields
# They are separated by |'s, and can be YES or NO
$EXTRA_PROFILE_HIDDEN=qq~YES|NO~;

# $EXTRA_PROFILE_PERMS are the permissions of the extra profile fields
# They are separated by |'s
$EXTRA_PROFILE_PERMS=qq~1|1~;

# $GROUPDB_FILE is the file where all group information will be stored. This
# file should NEVER be accessible by the public.
$GROUPDB_FILE=qq~/www/db/forum/groups~;

# $GROUPDB_FILE is the lockfile of the groups. This file should NEVER be
# accessible by the public.
$GROUPDB_LOCKFILE=qq~/www/db/forum/groups.LOCK~;

# $SHOW_LAST_POSTER indicates whether the forum shows who last posted to
# each thread
$SHOW_LAST_POSTER=qq~YES~;

# $HEADERFOOTER_FILE will indicate whether the header and footer fields
# are filenames or not. If it is YES, the header and footer fields will
# be used as the filename of the header and footer. If it is NO, they
# will just be printed
$HEADERFOOTER_FILE=qq~NO~;

# $USE_COOKIES will indicate whether the forum will use cookies
# for identification rather than the classic id= string
$USE_COOKIES=qq~YES~;

# $REVIEW_POST indicates whether the reply page will show the
# the replies of the thread replying to
$REVIEW_POST=qq~YES~;

1;
