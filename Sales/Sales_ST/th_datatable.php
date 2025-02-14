<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.ctranno', 'A.csiprintno', 'D.cref', 'CONCAT(a.ccode,"-",B.cname)', 'A.ngross', 'A.dcutdate', 'CASE WHEN A.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN A.lcancelled=1 THEN "Cancelled" ELSE "" END');

	$query = "SELECT A.*, B.cname as cname, IFNULL(D.cref,'') as cref, B.nlimit FROM `sales` A LEFT JOIN `customers` B ON A.`compcode` = B.`compcode` and A.`ccode` = B.`cempid` LEFT JOIN (Select x.ctranno, GROUP_CONCAT(DISTINCT x.creference) as cref from `sales_t` x where x.compcode='".$_SESSION['companyid']."' group by x.ctranno) D on A.ctranno=D.ctranno where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (LOWER(B.ctradename) like LOWER('%".$_POST['searchByName']."%') OR LOWER(B.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(D.cref) like LOWER('%".$_POST['searchByName']."%') OR LOWER(A.csiprintno) like LOWER('%".$_POST['searchByName']."%'))";
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
		$xcurr = " ".$row['ccurrencycode'];

		$sub_array = array();
		$sub_array[] = $row['ctranno'];
		$sub_array[] = $row['csiprintno'];
		$sub_array[] = $row['cname'];
		$sub_array[] = $row['ddate'];
		$sub_array[] = date_format(date_create($row['dcutdate']), "m/d/Y");
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$sub_array[] = $row['ccode'];
		$sub_array[] = $row['nlimit'];
		$sub_array[] = number_format($row['nbasegross'],2).$xcurr;
		$sub_array[] = str_replace(",","<br>",$row['cref']);
		$sub_array[] = $row['lvoid'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM sales where compcode='".$_SESSION['companyid']."'";
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