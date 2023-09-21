<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('cpartno', 'citemdesc', 'cunit', 'cstatus', 'cstatus');

	$query = "SELECT * FROM items WHERE compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
	$query .= "and (cpartno like '%".$_POST['searchByName']."%' OR citemdesc like '%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['order']))
	{
	$query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
	$query .= 'ORDER BY citemdesc DESC ';
	}

	$query1 = '';

 if($_POST["length"] != -1)
	{
	$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
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
	$sub_array[] = $row['cpartno'];
	$sub_array[] = $row['citemdesc'];
	$sub_array[] = $row['cunit'];
	$sub_array[] = $row['cstatus'];
	$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
	$query = "SELECT * FROM items";
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