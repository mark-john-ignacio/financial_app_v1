<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('a.ctranno', 'a.cornumber', 'd.cref', 'CONCAT(a.ccode,"-",b.cname)', 'a.namount', 'a.dcutdate', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE "" END');

	$query = "select a.*,b.cname, d.cref from receipt a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid LEFT JOIN (Select x.ctranno, GROUP_CONCAT(DISTINCT x.csalesno) as cref from receipt_sales_t x where x.compcode='".$_SESSION['companyid']."' group by x.ctranno) d on a.ctranno=d.ctranno where a.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (LOWER(b.ctradename) like LOWER('%".$_POST['searchByName']."%') OR LOWER(b.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ctranno) like LOWER('%".$_POST['searchByName']."%'))";
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
		$sub_array[] = $row['cornumber'];
		$sub_array[] = str_replace(",","<br>",$row['cref']);
		$sub_array[] = $row['ccode'];
		$sub_array[] = $row['cname'];
		$sub_array[] = date_format(date_create($row['dcutdate']), "m/d/Y");
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$sub_array[] = number_format($row['namount'],2);
		$sub_array[] = $row['lvoid'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT * FROM receipt where compcode='".$_SESSION['companyid']."'";
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