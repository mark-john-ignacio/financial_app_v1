<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'A.module', 'A.cevent', 'B.Fname', 'A.cmachine', 'A.ddate');

	$query = "SELECT A.ctranno, A.module, A.cevent, A.cmachine, A.ddate, B.Fname, B.Lname FROM `logfile` A LEFT JOIN `users` B ON A.`cuser` = B.`Userid` where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['order']))
	{
		$query .= ' ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= ' ORDER BY A.ddate DESC ';
	}

	$query1 = '';

	if(isset($_POST["length"])){
		if($_POST["length"] != -1)
		{
			$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
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
		$sub_array[] = $row['ctranno'];
		$sub_array[] = $row['module'];
		$sub_array[] = $row['cevent'];
		$sub_array[] = $row['cmachine'];
		$sub_array[] = $row['ddate'];
		$sub_array[] = $row['Fname'];
		$sub_array[] = $row['Lname'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM logfile where compcode='".$_SESSION['companyid']."'";
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