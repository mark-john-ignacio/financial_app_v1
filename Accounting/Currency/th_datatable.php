<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('symbol', 'country', 'unit', 'rate', 'cstatus');

	$query = "SELECT `id`, `symbol`, `country`, `unit`, `rate`, `cstatus` FROM `currency_rate` WHERE `compcode`='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (LOWER(country) like LOWER('%".$_POST['searchByName']."%') OR LOWER(unit) like LOWER('%".$_POST['searchByName']."%') OR LOWER(symbol) like LOWER('%".$_POST['searchByName']."%') OR LOWER(rate) like LOWER('%".$_POST['searchByName']."%'))";
	}

	if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')
	{

		$query .= " and cstatus = '".$_POST['searchBystat']."'";

	}

	if(isset($_POST['order']))
	{
		$query .= " ORDER BY ".$column[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir']." ";
	}
	else
	{
		$query .= " ORDER BY `country` ASC";
	}

	$query1 = '';

	
	if(isset($_POST['length']) && $_POST["length"] != -1)
	{
		$query1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}
	

	$statement = $connect->prepare($query);

	$statement->execute();

	$number_filter_row = $statement->rowCount();

	$statement = $connect->prepare($query . $query1);

	$statement->execute();

	$result = $statement->fetchAll();

	$data = array();

	foreach($result as $row)
	{
		$sub_array = array();
		$sub_array[] = $row['id'];
		$sub_array[] = $row['symbol'];
		$sub_array[] = $row['country'];
		$sub_array[] = $row['unit'];
		$sub_array[] = $row['rate'];
		$sub_array[] = $row['cstatus'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM currency_rate where compcode='".$_SESSION['companyid']."'";
		$statement = $connect->prepare($query);
		$statement->execute();
		return $statement->rowCount();
	}

	$output = array(
		"draw"       =>  intval($_POST["draw"]),
		"recordsTotal"   =>  count_all_data($connect),
		"recordsFiltered"  =>  $number_filter_row,
		"data"       =>  $data
	);

	echo json_encode($output);

?>