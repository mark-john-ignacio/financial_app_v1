<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$column = array('A.cmainitemno', 'B.citemdesc', 'B.cunit');


	$query = "SELECT A.cpartno as cmainitemno, A.citemdesc, A.cunit FROM items A WHERE A.compcode='".$_SESSION['companyid']."' and A.cstatus='ACTIVE' and cpartno in (Select cmainitemno from mrp_bom where compcode='".$_SESSION['companyid']."')";


	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{

		if($_POST['searchByType']=="1"){
			$query .= " and cpartno in (Select cmainitemno from mrp_bom where compcode='".$_SESSION['companyid']."' and citemno='".$_POST['searchByName']."')";
		}else{
			$query .= " and A.cpartno = '".$_POST['searchByName']."'";
		}
		
	}

	if(isset($_POST['order']))
	{
		$query .= ' ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= ' ORDER BY A.citemdesc DESC ';
	}

	$query1 = '';

	if($_POST["length"] != -1)
	{
		$query1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
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
		$sub_array[] = $row['cmainitemno'];
		$sub_array[] = $row['citemdesc'];
		$sub_array[] = $row['cunit'];
		$data[] = $sub_array;
	}

	function count_all_data($connect)
	{
		$query = "SELECT DISTINCT cmainitemno FROM mrp_bom";
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