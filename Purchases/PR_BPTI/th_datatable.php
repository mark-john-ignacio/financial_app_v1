<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'CONCAT(B.Lname,", ",B.Fname)', 'C.cdesc', 'A.dneeded', 'A.ddate', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE CASE WHEN a.lsent=0 THEN "For Sending" ELSE "For Approval" END END');

	$query = "SELECT A.ctranno, B.Lname, B.Fname, C.cdesc, A.dneeded, A.ddate, A.lapproved, A.lcancelled, A.lsent, A.lvoid, D.cdesc as cReqName FROM `purchrequest` A LEFT JOIN `users` B ON A.`cpreparedby` = B.`Userid` LEFT JOIN `locations` C ON A.`locations_id` = C.`nid` left join `mrp_operators` D on A.compcode=D.compcode and A.crequestedby=D.nid where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['searchBySec']) && $_POST['searchBySec'] != '')
	{
		$query .= "and A.locations_id = ".$_POST['searchBySec']."";
	}

	if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')
	{
		if($_POST['searchBystat']=="post"){
			$query .= " and (A.lapproved=1 and A.lvoid=0)";
		}

		if($_POST['searchBystat']=="void"){
			$query .= " and A.lvoid=1";
		}

		if($_POST['searchBystat']=="cancel"){
			$query .= " and A.lcancelled=1";
		}

		if($_POST['searchBystat']=="pending"){
			$query .= " and (A.lapproved=0 and A.lcancelled=0)";
		}
				
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
		$sub_array[] = $row['lsent'];
		$sub_array[] = $row['lvoid']; 
		$sub_array[] = $row['cReqName'];
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