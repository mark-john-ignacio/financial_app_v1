<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');


	$column = array('a.ctranno', 'a.crefsi', 'a.crefrr', 'CONCAT(a.ccode,"-",b.cname)', 'a.nbasegross', 'a.dreceived', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE CASE WHEN a.lsent=0 THEN "For Sending" ELSE "For Approval" END END','');

	$query = "select a.*,b.cname from suppinv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (LOWER(b.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ctranno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.crefsi) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.crefrr) like LOWER('%".$_POST['searchByName']."%'))";
	}

	if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')
	{
		if($_POST['searchBystat']=="post"){
			$query .= " and (a.lapproved=1 and a.lvoid=0)";
		}

		if($_POST['searchBystat']=="void"){
			$query .= " and a.lvoid=1";
		}

		if($_POST['searchBystat']=="cancel"){
			$query .= " and a.lcancelled=1";
		}

		if($_POST['searchBystat']=="pending"){
			$query .= " and (a.lapproved=0 and a.lcancelled=0)";
		}
				
	}

	if(isset($_POST['order']))
	{
		$query .= ' ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= ' ORDER BY a.ddate DESC ';
	}

	$query1 = '';

	//echo $query;

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
		$xcurr = " ".$row['ccurrencycode'];

		$sub_array = array();
		$sub_array[] = $row['ctranno'];
		$sub_array[] = $row['crefsi'];
		$sub_array[] = $row['crefrr'];
		$sub_array[] = $row['ccode'];
		$sub_array[] = $row['cname'];
		$sub_array[] = number_format($row['nbasegross'],2).$xcurr;
		$sub_array[] = date_format(date_create($row['dreceived']), "m/d/Y");
		$sub_array[] = $row['lapproved']; //7
		$sub_array[] = $row['lcancelled']; //8
		$sub_array[] = $row['lvoid']; //9
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM suppinv where compcode='".$_SESSION['companyid']."'";
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