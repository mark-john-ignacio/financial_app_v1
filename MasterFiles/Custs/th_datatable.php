<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('cempid', 'cname', 'ctin', 'cterms', 'cstatus');

	$query = "SELECT * FROM customers WHERE compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (cempid like '%".$_POST['searchByName']."%' OR cname like '%".$_POST['searchByName']."%') ";
	}

	if(isset($_POST['order']))
	{
		$query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY cname ASC ';
	}

	$query1 = '';

	if(isset($_POST['length'])){
		if($_POST["length"] != -1)
		{
			$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
	}

	$statement = $connect->prepare($query);

	$statement->execute();

	$number_filter_row = $statement->rowCount();

	$statement = $connect->prepare($query . $query1);

	//$statement->execute();

	if(!$statement->execute()) echo $statement->error;

	$result = $statement->fetchAll();



	$data = array();

	foreach($result as $row)
	{
		$sub_array = array();
		$sub_array[] = $row['cempid'];
		$sub_array[] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $row['cname']);
		$sub_array[] = $row['ctin'];
		$sub_array[] = $row['cterms'];
		$sub_array[] = $row['cstatus'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM customers WHERE compcode='".$_SESSION['companyid']."'";
		$statement = $connect->prepare($query);
		$statement->execute();
		return $statement->rowCount();
	}

	$output = array(
		"draw" => intval($_POST["draw"]),
		"recordsTotal" => count_all_data($connect),
		"recordsFiltered" => $number_filter_row,
		"data" => $data
	);

	echo json_encode($output, JSON_UNESCAPED_UNICODE);

?>