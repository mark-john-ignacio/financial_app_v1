<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];

	$chkapprovals = array();
	$sqlappx = mysqli_query($con,"Select A.* FROM purchrequest_trans_approvals A left join (Select cprno, MIN(nlevel) as nlevel from purchrequest_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 Group By cprno Order By cprno, nlevel) B on A.cprno=B.cprno where A.compcode='$company' and A.lapproved=0 and A.lreject=0 and A.nlevel=B.nlevel");
	if (mysqli_num_rows($sqlappx)!=0) {
		while($rows = mysqli_fetch_array($sqlappx, MYSQLI_ASSOC)){
			@$chkapprovals[] = $rows; 
		}
	}

	$column = array('A.ddate', 'CONCAT(B.Lname,", ",B.Fname)', 'C.cdesc', 'A.dneeded', 'A.ddate', 'CASE WHEN A.lapproved=1 THEN CASE WHEN A.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN A.lcancelled=1 THEN "Cancelled" ELSE CASE WHEN A.lsent=0 THEN "For Sending" ELSE "For Approval" END END');

	$query = "SELECT A.ctranno, B.Lname, B.Fname, C.cdesc, A.dneeded, A.ddate, A.lapproved, A.lcancelled, A.lsent, A.lvoid FROM `purchrequest` A LEFT JOIN `users` B ON A.`cpreparedby` = B.`Userid` LEFT JOIN `locations` C ON A.`locations_id` = C.`nid` where A.compcode='".$_SESSION['companyid']."' ";

	$filters = "";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{

		if($_POST['searchByType']=="1"){
			$filters .= " and ctranno in (Select ctranno from purchrequest_t where compcode='".$_SESSION['companyid']."' and citemno='".$_POST['searchByName']."')";
		}else{
			$filters .= " and LOWER(A.ctranno) like LOWER('%".$_POST['searchByName']."%')";
		}
		
	}

	if(isset($_POST['searchBySec']) && $_POST['searchBySec'] != '')
	{
		$filters .= " and A.locations_id = ".$_POST['searchBySec']."";
	}

	if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')
	{
		if($_POST['searchBystat']=="post"){
			$filters .= " and (A.lapproved=1 and A.lvoid=0)";
		}

		if($_POST['searchBystat']=="void"){
			$filters .= " and A.lvoid=1";
		}

		if($_POST['searchBystat']=="cancel"){
			$filters .= " and A.lcancelled=1";
		}

		if($_POST['searchBystat']=="pending" || $_POST['searchBystat']=="approve"){
			$filters .= " and (A.lapproved=0 and A.lcancelled=0)";
		}
				
	}

	if(isset($_POST['searchBydtfr']) && $_POST['searchBydtfr'] != '' && isset($_POST['searchBydtto']) && $_POST['searchBydtto'] != '')
	{
		$filters .= " and DATE(".$_POST['searchBydtfil'].") >= '".$_POST['searchBydtfr']."' AND DATE(".$_POST['searchBydtfil'].") <=  '".$_POST['searchBydtto']."'";
	}

	$query .= $filters;

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
		if($filters != ''){
			$query1 = 'LIMIT 0, ' . $_POST['length'];
		}else{
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
		$sub_array[] = $row['Lname'].", ".$row['Fname'];
		$sub_array[] = $row['cdesc'];
		$sub_array[] = $row['dneeded'];
		$sub_array[] = $row['ddate'];
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$sub_array[] = $row['lsent'];
		$sub_array[] = $row['lvoid'];
		
		$xcstat = "False";
		foreach($chkapprovals as $rocx){
			if($rocx['cprno']==$row['ctranno'] && $rocx['userid']==$employeeid){
				$xcstat = "True";
			}
		}

		$sub_array[] = $xcstat;

		if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')

			if($_POST['searchBystat']=="pending"){
				if($xcstat == "False"){
					$data[] = $sub_array;
				}				
			}elseif($_POST['searchBystat']=="approve"){
				if($xcstat == "True"){
					$data[] = $sub_array;
				}
			}else{
				$data[] = $sub_array;
			}
		else{
			$data[] = $sub_array;
		}
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