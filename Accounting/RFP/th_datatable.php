<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];

	$chkapprovals = array();
	$sqlappx = mysqli_query($con,"Select A.* FROM rfp_trans_approvals A left join (Select crfpno, MIN(nlevel) as nlevel from rfp_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 Group By crfpno Order By crfpno, nlevel) B on A.crfpno=B.crfpno where A.compcode='$company' and A.lapproved=0 and A.lreject=0 and A.nlevel=B.nlevel");
	if (mysqli_num_rows($sqlappx)!=0) {
		while($rows = mysqli_fetch_array($sqlappx, MYSQLI_ASSOC)){
			@$chkapprovals[] = $rows; 
		}
	}


	$column = array('a.ctranno', 'c.cref', 'CONCAT(a.ccode,"-",b.cname)', 'a.ngross', 'a.dtransdate', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE CASE WHEN a.lsent=0 THEN "For Sending" ELSE "For Approval" END END','');

	$query = "select a.ctranno, a.lsent, a.lapproved, a.lcancelled, a.lvoid, a.ccode, a.ngross, b.cname, a.cremarks, a.dtransdate, IFNULL(c.cref,'') as capvno
	from rfp a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join (Select ctranno, GROUP_CONCAT(capvno) as cref from rfp_t where compcode='".$_SESSION['companyid']."' Group By ctranno) c on a.ctranno=c.ctranno where a.compcode='".$_SESSION['companyid']."' ";

	$filters = "";
	
	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$filters .= "and (LOWER(b.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ctranno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(c.cref) like LOWER('%".$_POST['searchByName']."%'))";
	}

	if(isset($_POST['searchBystat']) && $_POST['searchBystat'] != '')
	{
		if($_POST['searchBystat']=="post"){
			$filters .= " and (a.lapproved=1 and a.lvoid=0)";
		}

		if($_POST['searchBystat']=="void"){
			$filters .= " and a.lvoid=1";
		}

		if($_POST['searchBystat']=="cancel"){
			$filters .= " and a.lcancelled=1";
		}

		if($_POST['searchBystat']=="pending" || $_POST['searchBystat']=="approve"){
			$filters .= " and (a.lapproved=0 and a.lcancelled=0)";
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
		$sub_array = array();
		$sub_array[] = $row['ctranno'];
		$sub_array[] = str_replace(",","<br>",$row['capvno']);
		$sub_array[] = $row['ccode'];
		$sub_array[] = $row['cname'];
		$sub_array[] = $row['cremarks'];
		$sub_array[] = number_format($row['ngross'],2);
		$sub_array[] = date_format(date_create($row['dtransdate']), "m/d/Y");
		$sub_array[] = $row['lapproved']; //7
		$sub_array[] = $row['lcancelled']; //8
		$sub_array[] = $row['lsent']; //9
		$sub_array[] = $row['lvoid']; //10
		
		$xcstat = "False";
		foreach($chkapprovals as $rocx){
			if($rocx['crfpno']==$row['ctranno'] && $rocx['userid']==$employeeid){
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
		$query = "SELECT * FROM rfp where compcode='".$_SESSION['companyid']."'";
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