<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'A.crefsr', 'A.crefsi', 'A.ctype', 'A.dcutdate', 'CONCAT(a.ccode,"-",b.cname)', 'a.ngross', 'CASE WHEN A.lapproved=1 THEN "Posted" WHEN A.lcancelled=1 THEN "Cancelled" ELSE "" END');

	$query = "select A.*, B.cname from aradjustment A left join customers B on  A.compcode=B.compcode and A.ccode=B.cempid where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (LOWER(b.ctradename) like LOWER('%".$_POST['searchByName']."%') OR LOWER(b.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ctranno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(A.crefsr) like LOWER('%".$_POST['searchByName']."%') OR LOWER(A.crefsi) like LOWER('%".$_POST['searchByName']."%'))";
	}

	if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')
	{
		if($_POST['searchBystat']=="post"){
			$query .= " and A.lapproved=1";
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
		$sub_array[] = $row['crefsr'];
		$sub_array[] = $row['crefsi'];
		$sub_array[] = $row['ctype'];
		$sub_array[] = $row['ccode'];
		$sub_array[] = $row['cname'];
		$sub_array[] = date_format(date_create($row['dcutdate']), "m/d/Y");
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$sub_array[] = number_format($row['ngross'],2);
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM aradjustment where compcode='".$_SESSION['companyid']."'";
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