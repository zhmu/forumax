<?php
    //
    // mod_mysql.php
    // (c) 2000 Rink Springer, www.forumax.com
    //
    // This is the MySQL database plugin for ForuMAX. It will take care of all
    // ForuMAX database transactions.
    //

    //
    // db_query ($query)
    //
    // This will feed $query to the database and execute it. It'll return
    // a result handler. Any errors will be instant death.
    //
    function
    db_query ($query) {
	// log all queries (eek!)
/*	$now = getdate();
	$fd = fopen ("/www/log/queries.fm5", "a+");
	fwrite ($fd, $now[hours] . ":" . $now[minutes] . ":" . $now[seconds] . " -- " . $query . "\n");
	fclose ($fd);*/

	// feed the query into the database and execute it
	$res = @mysql_db_query ($GLOBALS["db_dbname"], $query);

	// any failure?
	if (!$res) {
	    // yup. instant death
	    echo "Query was: " . $query . "<p>";
	    die ("<p><b><i>Database query error, contact the forum admin</i></b>" . mysql_error());
	}
    
	// return the identifier
	return $res;
    }

    //
    // db_fetch_result ($res)
    //
    // This will fetch a single row of results from the previous database
    // query and return them as an array. Result handle $res will be used. This
    // will return nothing if it there are no more results.
    //
    function
    db_fetch_results ($res) {
	return @mysql_fetch_row ($res);
    }

    //
    // db_nof_results ($res)
    //
    // This will return the number of results we have had on result handle $res.
    // This only works for SELECT queries.
    //
    function
    db_nof_results ($res) {
	return @mysql_num_rows ($res);
    }

    //
    // db_get_insert_id ()
    //
    // This will return the last ID automatically generated.
    //
    function
    db_get_insert_id () {
	// because we use a BIGINT for the threads, we have to use the internal
	// MySQL function LAST_INSERT_ID()
	$query = sprintf ("select last_insert_id()");
	$res = db_query ($query); $tmp = db_fetch_results ($res);

	// return the value
	return $tmp[0];
    }

    // try to open a connection with the MySQL database
    $dbh = @mysql_connect ($GLOBALS["db_hostname"], $GLOBALS["db_username"], $GLOBALS["db_password"]);

    // did this work?
    if (!$dbh) {
	// no, not really. die
	die ("<p><b><i>Unable to open the link to the database, contact the forum admin</i></b>");
    }
 ?>
