<?php
    //
    // movethread.php
    //
    // This will move a thread around forums.
    //
    // (c) 1999-2002 NextFuture (http://www.next-future.nl)
    //

    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // do we have a correct thread id parameter?
    $threadid = trim (preg_replace ("/\D/", "", $_REQUEST["threadid"]));
    if ($threadid == "") {
	// no. complain
	FatalError ("error_badrequest");
    }
    $VAR["threadid"] = $threadid;

    // need to show the 'are you sure' page?
    if ($_REQUEST["action"] == "") {
	// yes. build the forum list
	$query = sprintf ("SELECT id,name FROM forums ORDER BY name ASC");
	$res = db_query ($query);

	// handle all forums
	while (list ($VAR["forumid"], $VAR["forumname"]) = db_fetch_results ($res)) {
	    // build the line
	    $VAR["forumlist"] .= InsertSkinVars (GetSkinTemplate ("forumlist"));
	}

	// build the page and show it
	ShowBaseForumPage ("movethread_page", $threadid);
	exit;
    }

    // everything given?
    $destforum = trim (preg_replace ("/\D/", "", $_REQUEST["destforum"]));
    if ($destforum == "") {
	// no. quit
	FatalError ("error_badrequest");
    }

    // grab the forum in which the thread id and handle restrictions
    $forumid = GetThreadForumID ($threadid);
    $VAR["forumid"] = $forumid;
    HandleRestrictedForum ($forumid);

    // verify our username and password
    HandleLogin ($_REQUEST["the_accountname"], $_REQUEST["the_password"]);

    // for the next steps, we have to be a moderator. are we one?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
        FatalError("error_accessdenied");
    }

    // need to actually move the thread?
    if ($_REQUEST["action"] == "movethread") {
	// yes. do we need to move or copy this thread?
	if ($how == "lock") {
	    // we need to lock the original one. do it
	    $query = sprintf ("UPDATE threads SET flags=flags|%s,lockerid='%s',destforum='%s' WHERE id='%s'", FLAG_THREAD_LOCKED, $GLOBALS["userid"], $destforum, $threadid);
	    db_query ($query);

	    // grab all old thread values
	    $query = sprintf ("SELECT title,icon,nofreplies,authorid,flags,lastposterid,lastdate,nofviews,authorname,lastpostername,rating FROM threads WHERE id='%s'", $threadid);
	    list ($title, $icon, $nofreplies, $authorid, $flags, $lastposterid, $lastdate, $nofviews, $authorname, $lastpostername, $rating) = db_fetch_results (db_query ($query));

	    // now, re-create the thread
	    $flags = $flags & (~FLAG_THREAD_LOCKED);
	    $query = sprintf ("INSERT INTO threads VALUES (NULL,%s,'%s',%s,%s,NOW(),%s,'%s',%s,'%s','%s','','',%s,%s)",$destforum,addslashes($title),$icon,$nofreplies,$authorid,addslashes($authorname),$flags,$lastposterid,addslashes($lastpostername),$nofviews,$rating);
	    db_query ($query);
	    $new_threadid = db_get_insert_id();

	    // now, move all replies
	    $query = sprintf ("SELECT authorid,timestamp,post,edittime,editid,icon,ipaddr,flags,authorname FROM posts WHERE threadid='%s' ORDER BY timestamp ASC", $threadid);
	    $res = db_query ($query);

	    // handle all replies
	    while ($result = db_fetch_results ($res)) {
		// re-post it to the new forum
	 	$query = sprintf ("INSERT INTO posts VALUES (NULL,%s,'%s',%s,%s,'%s','%s','%s',%s,%s,'%s',%s)",$result[0],$result[8],$destforum,$new_threadid,$result[1],addslashes($result[2]),$result[3],$result[4],$result[5],$result[6],$result[7]);
	        db_query ($query);
	    }

	    // grab the last poster in this forum
	    $query = sprintf ("SELECT authorid,timestamp,authorname FROM posts WHERE forumid='%s' ORDER BY timestamp DESC LIMIT 1", $destforum);
	    $res = db_query ($query); $result = db_fetch_results ($res);

	    // now, increment the forum's post count.
	    $query = sprintf ("UPDATE forums SET nofthreads=nofthreads+1,nofposts=nofposts+%s,lastposterid='%s',lastpost='%s',lastpostername='%s' WHERE id='%s'", $nofreplies+1,$result[0],$result[1],$result[2],$destforum);
	    db_query ($query);

	    // it worked. show the wohoo page.
	    ShowForumPage("movethread_ok");
	    exit;
	} else {
	    // we need to delete the old one. grab all info first
	    $query = sprintf ("SELECT title,icon,nofreplies,authorid,flags,lastposterid,nofviews,rating,authorname,lastpostername FROM threads WHERE id='%s'",$threadid);
	    list ($title, $icon, $nofreplies, $authorid, $flags, $lastposterid, $nofviews, $rating, $authorname, $lastpostername) = db_fetch_results (db_query ($query));

	    // delete the original thread
	    $query = sprintf ("DELETE FROM threads WHERE id='%s'", $threadid);
	    db_query ($query);

	    // create a new thread
	    $flags = $flags & (~FLAG_THREAD_LOCKED);
	    $query = sprintf ("INSERT INTO threads VALUES (NULL,%s,'%s',%s,%s,now(),'%s','%s',%s,'%s','%s','',0,%s,%s)",$destforum,addslashes($title),$icon,$nofreplies,$authorid,$authorname,$flags,$lastposterid,$lastpostername,$nofviews,$rating);
	    db_query ($query);
	    $new_threadid = db_get_insert_id();

	    // now, alter all thread id's of the replies
	    $query = sprintf ("UPDATE posts SET threadid=%s,forumid=%s WHERE threadid='%s'",$new_threadid,$destforum,$threadid);
	    db_query ($query);

	    // grab the last poster in this forum
	    $query = sprintf ("SELECT authorid,timestamp,authorname FROM posts WHERE forumid='%s' ORDER BY timestamp DESC LIMIT 1", $destforum);
	    $res = db_query ($query); $result = db_fetch_results ($res);

	    // increment the forum's post count.
	    $query = sprintf ("UPDATE forums SET nofthreads=nofthreads+1,nofposts=nofposts+%s,lastposterid=%s,lastpost='%s',lastpostername='%s' WHERE id='%s'", $nofreplies+1,$result[0],$result[1],$result[2],$destforum);
	    db_query ($query);

	    // grab the last poster from the source forum
	    $query = sprintf ("SELECT authorid,timestamp,authorname FROM posts WHERE forumid='%s' ORDER BY timestamp DESC LIMIT 1", $forumid);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    if ($result[0] == "") { $result[0] = 0; }

	    // decrement the original forum's post count.
	    $query = sprintf ("UPDATE forums SET lastposterid='%s',lastpost='%s',nofthreads=nofthreads-1,nofposts=nofposts-'%s',lastpostername='%s' WHERE id='%s'", $result[0],$result[1],$nofreplies+1,$result[2],$forumid);
	    db_query ($query);

	    // move the poll over
	    $query = sprintf ("UPDATE polls SET threadid='%s' WHERE threadid='%s'", $new_threadid, $threadid);
	    db_query ($query);

	    // it worked. show the wohoo page.
	    ShowForumPage("movethread_ok");
	}
    }
 ?>
