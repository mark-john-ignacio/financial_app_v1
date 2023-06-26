<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('a.ctranno', 'b.cname', 'e.cname', 'a.ccheckno', 'a.cpayrefno', 'a.dcheckdate', 'a.lapproved', 'a.lcancelled', 'a.lsent');

	$query = "select a.ctranno, b.cname, e.cname as bankname, CASE WHEN a.cpaymethod='cheque' THEN a.ccheckno ELSE a.cpayrefno END as cpayref, a.dcheckdate, a.lapproved, a.lcancelled, a.lsent from paybill a left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='".$_SESSION['companyid']."'";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= " and (LOWER(b.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ctranno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ccheckno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.cpayrefno) like LOWER('%".$_POST['searchByName']."%'))";
	}

	if(isset($_POST['order']))
	{
		$query .= ' ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= ' ORDER BY a.dtrandate DESC ';
	}

	$query1 = '';

	
	if($_POST["length"] != -1)
	{
		$query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}
	

	echo $query;

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
		$sub_array[] = $row['cname'];
		$sub_array[] = $row['bankname'];
		$sub_array[] = $row['cpayref'];
		$sub_array[] = $row['dcheckdate'];
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$sub_array[] = $row['lsent'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM paybill where compcode='".$_SESSION['companyid']."'";
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