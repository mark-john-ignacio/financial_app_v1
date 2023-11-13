<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.cacctid', 'A.cacctdesc', 'A.ccategory', 'A.ctype', 'A.nbalance');

	$query = "SELECT (CASE WHEN A.mainacct='0' OR ctype='General' THEN A.cacctid ELSE A.mainacct END) as 'main', A.cacctno, A.cacctid, A.cacctdesc, A.ctype, A.ccategory, A.mainacct, A.cFinGroup, A.lcontra, A.nlevel, A.nbalance FROM `accounts` A where A.compcode='".$_SESSION['companyid']."' ";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$query .= "and (A.cacctid like '%".$_POST['searchByName']."%' OR A.cacctdesc like '%".$_POST['searchByName']."%')";
	}

	if(isset($_POST['searchByType']) && $_POST['searchByType'] != '')
	{
		$query .= "and A.ccategory = '".$_POST['searchByType']."'";
	}

	//$query .= 'ORDER BY ccategory, CASE WHEN A.mainacct=\'0\' OR ctype=\'General\' THEN A.cacctid ELSE A.mainacct END, nlevel, cacctid ';

	$query .= 'ORDER BY ccategory, nlevel, cacctid';

	$query1 = '';


	if($_POST["length"] != -1)
	{
		$query1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}

	$statement = $connect->prepare($query);

	$statement->execute();

	$number_filter_row = $statement->rowCount();

	//echo $query . $query1;

	$statement = $connect->prepare($query . $query1);

	$statement->execute();

	$result = $statement->fetchAll();

	$data = array();

	function getchild($acctcode, $nlevel){
		global $result;
		global $data;

		foreach($result as $rsz){
			if($rsz['mainacct']==$acctcode){
				$sub_array = array();
				$sub_array[] = $rsz['cacctno']; //0
				$sub_array[] = $rsz['cacctid']; //1
				$sub_array[] = $rsz['cacctdesc']; //2
				$sub_array[] = $rsz['ctype']; //3
				$sub_array[] = $rsz['ccategory']; //4
				$sub_array[] = $rsz['mainacct']; //5
				$sub_array[] = $rsz['cFinGroup']; //6
				$sub_array[] = $rsz['lcontra']; //7
				$sub_array[] = $rsz['nlevel']; //8
				$sub_array[] = number_format($rsz['nbalance'],4); //9
				$data[] = $sub_array;

				if($rsz['ctype']=="General"){
					getchild($rsz['cacctid'], $rsz['nlevel']);
				}
			}
		}
	}

	foreach($result as $row)
	{
		if(intval($row['nlevel'])==1){
			$sub_array = array();
			$sub_array[] = $row['cacctno']; //0
			$sub_array[] = $row['cacctid']; //1
			$sub_array[] = $row['cacctdesc']; //2
			$sub_array[] = $row['ctype']; //3
			$sub_array[] = $row['ccategory']; //4
			$sub_array[] = $row['mainacct']; //5
			$sub_array[] = $row['cFinGroup']; //6
			$sub_array[] = $row['lcontra']; //7
			$sub_array[] = $row['nlevel']; //8
			$sub_array[] = number_format($row['nbalance'],4); //9
			$data[] = $sub_array;

			if($row['ctype']=="General"){
				getchild($row['cacctid'], $row['nlevel']);
			}
		}
	}

	function count_all_data($connect)
	{
	$query = "SELECT * FROM accounts where compcode='".$_SESSION['companyid']."'";
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

	//echo "<pre>";
	//print_r($data);
//	echo "</pre>";

	echo json_encode($output);

?>