<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'A.csiprintno', 'B.ctradename', 'A.ddate', 'A.dcutdate', 'A.lapproved', 'A.lcancelled', 'A.ccode', 'B.nlimit', 'A.ngross');

	$query = "SELECT A.ctranno, B.Lname, B.Fname, C.cdesc, A.dneeded, A.ddate, A.lapproved, A.lcancelled FROM `purchrequest` A LEFT JOIN `users` B ON A.`cpreparedby` = B.`Userid` LEFT JOIN `locations` C ON A.`locations_id` = C.`nid` where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['searchBySec']) && $_POST['searchBySec'] != '')
	{
		$query .= "and A.locations_id = ".$_POST['searchByName']."";
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
		$sub_array[] = $row['ctranno'];
		$sub_array[] = $row['Lname'].", ".$row['Fname'];
		$sub_array[] = $row['cdesc'];
		$sub_array[] = $row['dneeded'];
		$sub_array[] = $row['ddate'];
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM purchrequest where compcode='".$_SESSION['companyid']."'";
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