<?php
    //
    // db_mysql.php
    //
    // (c) 2002 The Internet Factory, http://www.internet-factory.nl
    //
    // This contains functions to interact with a MySQL database.
    //

    //
    // db_error ($action)
    //
    // This will show an error for database action $action and exit.
    //
    function
    db_error ($action) {
 ?>We're sorry, but the following database error has occoured:<p>
<code><b><?php echo mysql_error(); ?></b></code><p>
The action which triggered this error was:<p>
<code><b><?php echo $action; ?></b></code><p>
Please consult the webmaster to report this error.
<?php
	exit;
    }

    //
    // db_query ($query)
    //
    // This will feed query $query in the MySQL database and return a result
    // handle. Any error will cause sudden death.
    //
    function
    db_query ($query) {
	global $logit;

	if ($logit == "yes") {
	// log all queries (eek!)
	$now = getdate();
	$fd = fopen ("/tmp/queries.fm5lite", "a+");
	fwrite ($fd, $now[hours] . ":" . $now[minutes] . ":" . $now[seconds] . " -- " . $query . "\n");
	fclose ($fd);
	}

	// feed the query into the database and execute it
	$res = @mysql_db_query ($GLOBALS["db_dbname"], $query);


	// query the database
	$res = @mysql_query ($query);
	if (!$res) {
	    // this failed. complain
	    db_error ("performing query <u>" . $query . "</u>");
	}

 	// return the result handle
	return $res;
    }

    //
    // db_fetch_result ($res)
    //
    // This will return an array with the results of result handle $res.
    //
    function
    db_fetch_result ($res) {
	return @mysql_fetch_array ($res);
    }

    //
    // db_nof_results ($res)
    //
    // This will return the number of affected rows in the previous action.
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
	$res = db_query ($query); list ($id) = db_fetch_result ($res);

	// return the value
	return $id;
    }

    // connect to the MySQL server
    if (!@mysql_connect (DB_HOSTNAME, DB_USERNAME, DB_PASSWORD)) {
	// this failed. complain
	db_error ("Connect to the MySQL database");
    }

    // select the appropriate database
    if (!@mysql_select_db (DB_DBNAME)) {
	// this failed. complain
	db_error ("Select appropriate MySQL database");
    }
 ?>
