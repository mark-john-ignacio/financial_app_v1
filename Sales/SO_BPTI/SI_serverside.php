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
$table = 'dr';

// Table's primary key
$primaryKey = 'ctranno';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`ctranno`', 'dt' => 0, 'field' => 'ctranno' ),
	array( 'db' => '`ud`.`cname`',  'dt' => 1, 'field' => 'cname' ),
	array( 'db' => '`u`.`ddate`', 'dt' => 2, 'field' => 'ddate'),
	array( 'db' => '`u`.`dcutdate`', 'dt' => 3, 'field' => 'dcutdate', 'formatter' => function( $d, $row ) {
																	return date( 'Y-m-d', strtotime($d));
																}),
	array('db'  => '`u`.`ngross`', 'dt' => 4, 'field' => 'ngross', 'formatter' => function( $d, $row ) {
																return number_format($d, 2);
															}),
	array('db'  => '`u`.`lapproved`', 'dt' => 5, 'field' => 'lapproved'),
	array('db'  => '`u`.`lcancelled`', 'dt' => 6, 'field' => 'lcancelled'),
	array('db'  => '`u`.`ccode`', 'dt' => 7, 'field' => 'ccode'),
	array('db'  => '`ud`.`nlimit`', 'dt' => 8, 'field' => 'nlimit'),
	array( 'db' => '`ud`.`ctradename`',  'dt' => 9, 'field' => 'ctradename' ),
	array( 'db' => '`u`.`cpono`',  'dt' => 10, 'field' => 'cpono' ),
);

// SQL server connection information
require('../../Connection/config.php');

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('../Sales/ssp.customized.class.php' );

$joinQuery = "FROM `so` AS `u` LEFT JOIN `customers` AS `ud` ON (`u`.`ccode` = `ud`.`cempid`)";
$extraWhere = "`u`.`compcode` = '001'";
//$groupBy = "";
//$having = "";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
