<?php
    // we need the database settings!
    require "dbconfig.php";

    // we need the database module as well
    require $GLOBALS["db_mod"];

    // we need the generic library too
    require "lib.php";

    // grab the thread information
    $query = sprintf ("select title,forumid,nofreplies from threads where id=%s", $threadid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $threadtitle = CensorText ($result[0]); $forumid = $result[1]; $nofreplies = $result[2];

    // handle the restrictions, if needed
    HandleRestrictedForum ($forumid);

    // is there a thread title?
    if (trim ($threadtitle) == "") {
	// no. revert to the default one
	$threadtitle = $CONFIG["default_topic"];
    }

    // grab the lastest thread poster
    $query = sprintf ("select name,catno,image from forums where id=%s", $forumid);
    $res = db_query ($query); $result = db_fetch_results ($res);
    $forumname = $result[0]; $catid = $result[1]; $forum_image = $result[2];

    // do we have a category id?
    if ($catid != 0) {
        // yes. grab the category name
        $query = sprintf ("select name from categories where id=%s", $catid);
        $res = db_query ($query); $tmp = db_fetch_results ($res);
        $cat_title = $tmp[0];
    }

    // need to show the 'are you sure' page?
    if ($action == "") {
	// yes. do it
	// grab some generic values
        $forums_title = $CONFIG["forumtitle"];
	$the_accountname = $GLOBALS["username"];
	$the_password = $GLOBALS["password"];

	// grab the template
	$forumlist_template = addslashes (GetSkinTemplate ("forumlist"));

	// build the forum list
	$query = sprintf ("select id,name from forums order by name");
	$res = db_query ($query);

	// handle all forums
	$tmp_forumid = $forumid; $tmp_forumname = $forumname;
	while ($result = db_fetch_results ($res)) {
	    // format the stuff
	    $forumid = $result[0]; $forumname = $result[1];

	    eval ("\$tmp = stripslashes (\"" . $forumlist_template . "\");");
	    $forumlist .= $tmp;
	}
	$forumid = $tmp_forumid; $forumname = $tmp_forumname;

	// build the page and show it
        ShowHeader("movethread_page");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("movethread_page")) . "\");");
	print $tmp;

	ShowFooter();
	exit;
    }

    // verify our username and password
    HandleLogin ($the_accountname, $the_password);

    // for the next steps, we have to be a moderator. are we one?
    if (IsForumMod ($forumid) == 0) {
	// no. complain
        ShowHeader("error_accessdenied");
	eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_accessdenied")) . "\");");
	print $tmp;
	ShowFooter();
	exit;
    }

    // need to actually move the thread?
    if ($action == "movethread") {
	// yes. do we need to move or copy this thread?
	if ($how == "lock") {
	    // we need to lock the original one. do it
	    $query = sprintf ("update threads set flags=flags or %s,lockerid=%s,destforum=%s where id=%s", FLAG_THREAD_LOCKED, $GLOBALS["userid"], $destforum, $threadid);
	    db_query ($query);

	    // grab all old thread values
	    $query = sprintf ("select title,icon,nofreplies,authorid,flags,lastposterid,lastdate,nofviews from threads where id=%s", $threadid);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    $title = $result[0]; $icon = $result[1]; $nofreplies = $result[2];
	    $authorid = $result[3]; $flags = $result[4];
	    $lastposterid = $result[5]; $lastdate = $result[6];
	    $nofviews = $result[7];

	    // did we get any results?
	    if (db_nof_results ($res) == 0) {
		// no. complain
                ShowHeader("error_nosuchthread");
	        eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("error_nosuchthread")) . "\");");
	        print $tmp;
	        ShowFooter();
	        exit;
	    }

	    // now, grab the thread and re-create it.
	    $flags = $flags & (!FLAG_THREAD_LOCKED);
	    $query = sprintf ("insert into threads values (NULL,%s,'%s',%s,%s,now(),%s,%s,'%s','','',0)",$destforum,addslashes($title),$icon,$nofreplies,$authorid,$flags,$lastposterid, $nofviews);
	    db_query ($query);
	    $new_threadid = db_get_insert_id();

	    // now, move all replies
	    $query = sprintf ("select authorid,timestamp,post,edittime,editid,icon,ipaddr,flags from posts where threadid=%s order by timestamp asc", $threadid);
	    $res = db_query ($query);

	    // handle all replies
	    while ($result = db_fetch_results ($res)) {
		// re-post it to the new forum
	 	$query = sprintf ("insert into posts values (NULL,%s,%s,%s,'%s','%s','%s',%s,%s,'%s',%s)",$result[0],$destforum,$new_threadid,$result[1],addslashes($result[2]),$result[3],$result[4],$result[5],$result[6],$result[7]);
	        db_query ($query);
	    }

	    // grab the last poster in this forum
	    $query = sprintf ("select authorid,timestamp from posts where forumid=%s order by timestamp desc limit 1", $destforum);
	    $res = db_query ($query); $result = db_fetch_results ($res);

	    // now, increment the forum's post count.
	    $query = sprintf ("update forums set nofthreads=nofthreads+1,nofposts=nofposts+%s,lastposterid=%s,lastpost='%s' where id=%s", $nofreplies+1,$result[0],$result[1],$destforum);
	    db_query ($query);

	    // it worked. show the wohoo page.
            ShowHeader("movethread_ok");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("movethread_ok")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	} else {
	    // we need to delete the old one. grab all info first
	    $query = sprintf ("select title,icon,nofreplies,authorid,flags,lastposterid,nofviews from threads where id=%s",$threadid);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    $title = $result[0]; $icon = $result[1]; $nofreplies = $result[2];
	    $authorid = $result[3]; $flags = $result[4]; $lastposterid = $result[5];
	    $nofviews = $result[6];

	    // delete the original thread
	    $query = sprintf ("delete from threads where id=%s", $threadid);
	    db_query ($query);

	    // create a new thread
	    $flags = $flags & (!FLAG_THREAD_LOCKED);
	    $query = sprintf ("insert into threads values (NULL,%s,'%s',%s,%s,now(),'%s',%s,'%s','',0,%s)",$destforum,$title,$icon,$nofreplies,$authorid,$flags,$lastposterid,$nofviews);
	    db_query ($query);
	    $new_threadid = db_get_insert_id();

	    // now, just alter all thread id's of the replies
	    $query = sprintf ("select id from posts where threadid=%s", $threadid);
	    $res = db_query ($query);

	    // handle all replies
	    while ($result = db_fetch_results ($res)) {
		// re-post it to the new forum
	 	$query = sprintf ("update posts set threadid=%s,forumid=%s where id=%s",$new_threadid,$destforum,$result[0]);
	        db_query ($query);
	    }

	    // grab the last poster in this forum
	    $query = sprintf ("select authorid,timestamp from posts where forumid=%s order by timestamp desc limit 1", $destforum);
	    $res = db_query ($query); $result = db_fetch_results ($res);

	    // increment the forum's post count.
	    $query = sprintf ("update forums set nofthreads=nofthreads+1,nofposts=nofposts+%s,lastposterid=%s,lastpost='%s' where id=%s", $nofreplies+1,$result[0],$result[1],$destforum);
	    db_query ($query);

	    // grab the last poster from the source forum
	    $query = sprintf ("select authorid,timestamp from posts where forumid=%s order by timestamp desc limit 1", $forumid);
	    $res = db_query ($query); $result = db_fetch_results ($res);
	    if ($result[0] == "") { $result[0] = 0; }

	    // decrement the original forum's post count.
	    $query = sprintf ("update forums set lastposterid=%s,lastpost='%s',nofthreads=nofthreads-1,nofposts=nofposts-%s where id=%s", $result[0],$result[1],$nofreplies+1,$forumid);
	    db_query ($query);

	    // it worked. show the wohoo page.
            ShowHeader("movethread_ok");
	    eval ("\$tmp = stripslashes (\"" . addslashes (GetSkinTemplate ("movethread_ok")) . "\");");
	    print $tmp;
	    ShowFooter();
	    exit;
	}
    }
 ?>
