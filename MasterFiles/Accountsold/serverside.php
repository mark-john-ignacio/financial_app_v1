<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'accounts';

// Table's primary key
$primaryKey = 'cacctno';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array('db' => 'cacctno', 'dt' => 0, 'field' => 'cacctno' ),
	array('db' => 'cacctdesc',  'dt' => 1, 'field' => 'cacctdesc' ),
	array('db' => 'ccategory', 'dt' => 2, 'field' => 'ccategory'),
	array('db' => 'ctype', 'dt' => 3, 'field' => 'ctype'),
	array('db'  => 'mainacct', 'dt' => 4, 'field' => 'mainacct'),
	array('db'  => 'nlevel', 'dt' => 5, 'field' => 'nlevel'),
	array('db'  => 'cFinGroup', 'dt' => 6, 'field' => 'cFinGroup'),
	array('db'  => 'lcontra', 'dt' => 7, 'field' => 'lcontra'),
	array('db'  => 'cconacct', 'dt' => 8, 'field' => 'cconacct'),
	array('db' => 'cacctid', 'dt' => 9, 'field' => 'cacctid' )
);

// SQL server connection information
require('../../Connection/config.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('../ssp.class.php' );

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns)
);
