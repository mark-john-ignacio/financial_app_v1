<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'A.crefSO', 'B.cname', 'A.dtargetdate', 'A.cpriority', 'CASE WHEN A.lapproved=1 THEN "Posted" WHEN A.lcancelled=0 THEN "Cancelled" ELSE "" END');

	$query = "SELECT * FROM `mrp_jo` A LEFT JOIN `customers` B ON A.`compcode` = B.`compcode` and A.`ccode` = B.`cempid` where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (LOWER(B.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(A.crefSO) like LOWER('%".$_POST['searchByName']."%') OR LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%'))";
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
		$sub_array[] = $row['crefSO'];
		$sub_array[] = $row['cname'];
		$sub_array[] = $row['dtargetdate'];
		$sub_array[] = $row['cpriority'];
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM mrp_jo where compcode='".$_SESSION['companyid']."'";
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