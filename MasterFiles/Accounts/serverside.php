<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.cacctno', 'A.cacctid', 'A.cacctdesc', 'A.ctype', 'A.ccategory', 'A.mainacct', 'A.cFinGroup', 'A.lcontra', 'A.nlevel');

	$query = "SELECT (CASE WHEN A.mainacct='0' THEN A.cacctid ELSE A.mainacct END) as 'main', A.cacctno, A.cacctid, A.cacctdesc, A.ctype, A.ccategory, A.mainacct, A.cFinGroup, A.lcontra, A.nlevel FROM `accounts` A where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (A.cacctid like '%".$_POST['searchByName']."%' OR A.cacctdesc like '%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['searchByType']) && $_POST['searchByType'] != '')
	{
		$query .= "and A.ccategory = '".$_POST['searchByType']."'";
	}

 	$query .= 'ORDER BY \'main\' ';

	$query1 = '';

	if($_POST["length"] != -1)
	{
	$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}

	$statement = $connect->prepare($query);

	$statement->execute();

	$number_filter_row = $statement->rowCount();

	//echo $query . $query1;
	$statement = $connect->prepare($query . $query1);

	$statement->execute();

	$result = $statement->fetchAll();

	$data = array();

	foreach($result as $row)
	{
	$sub_array = array();
	$sub_array[] = $row['cacctno']; //0
	$sub_array[] = $row['cacctid']; //1
	$sub_array[] = $row['cacctdesc']; //2
	$sub_array[] = $row['ctype']; //3
	$sub_array[] = $row['ccategory']; //4
	$sub_array[] = $row['mainacct']; //5
	$sub_array[] = $row['cFinGroup']; //6
	$sub_array[] = $row['lcontra']; //7
	$sub_array[] = $row['nlevel']; //8
	$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
	$query = "SELECT * FROM accounts where compcode='".$_SESSION['companyid']."'";
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