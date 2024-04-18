<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'CONCAT(B.Lname,", ",B.Fname)', 'D.cdesc', 'C.cdesc', 'A.dneeded', 'A.ddate', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE CASE WHEN a.lsent=0 THEN "For Sending" ELSE "For Approval" END END');


	$alrr = mysqli_query($con,"SELECT A.creference FROM purchase_t A LEFT JOIN purchase B on A.compcode=B.compcode and A.cpono=B.cpono WHERE A.compcode='".$_SESSION['companyid']."' and B.lcancelled=0 and B.lvoid=0");
	$refpos[] = "";
	while($rowxcv=mysqli_fetch_array($alrr, MYSQLI_ASSOC)){
		$refpos[] = $rowxcv['creference'];
	}

	$query = "SELECT A.*,B.cdesc, C.Minit, C.Fname, C.Lname FROM purchrequest A LEFT JOIN locations B on A.compcode=B.compcode and A.locations_id=B.nid LEFT JOIN users C on A.cpreparedby=C.Userid WHERE A.compcode='".$_SESSION['companyid']."' and A.ctranno not in ('".implode("','",$refpos)."') and (A.lapproved=1 and A.lvoid=0)";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['searchBySec']) && $_POST['searchBySec'] != '')
	{
		$query .= "and A.locations_id = ".$_POST['searchBySec']."";
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
		$mi = ($row['Minit']!="") ? " ".$row['Minit'] : "";
    	$cpreparedName =  $row['Lname'] . ", ". $row['Fname'] . $mi;

		$sub_array = array();
		$sub_array[] = $row['ctranno'];
		$sub_array[] = $cpreparedName;
		$sub_array[] = $row['cdesc'];
		$sub_array[] = $row['ddate'];
		$sub_array[] = $row['dneeded'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM purchrequest where compcode='".$_SESSION['companyid']."' and lcancelled=0 and lvoid=0";
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