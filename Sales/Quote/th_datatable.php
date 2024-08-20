<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');
	$company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];

	$chkapprovals = array();
	$sqlappx = mysqli_query($con,"Select A.* FROM quote_trans_approvals A left join (Select ctranno, MIN(nlevel) as nlevel from quote_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 Group By ctranno Order By ctranno, nlevel) B on A.ctranno=B.ctranno where A.compcode='$company' and A.lapproved=0 and A.lreject=0 and A.nlevel=B.nlevel");
	if (mysqli_num_rows($sqlappx)!=0) {
		while($rows = mysqli_fetch_array($sqlappx, MYSQLI_ASSOC)){
			@$chkapprovals[] = $rows; 
		}
	}

	$column = array('a.ddate', 'a.quotetype', 'CONCAT(a.ccode,"-",COALESCE(b.ctradename, b.cname))', 'a.ddate', 'a.dtrandate', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE "" END', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE "" END');

	$query = "select a.*,b.cname from quote a left join customers b on a.`compcode` = b.`compcode` and a.ccode=b.cempid and a.compcode=b.compcode where a.compcode='$company'";

	$filters = "";

	if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
	{
		$filters .= "and (ctranno like '%".$_POST['searchByName']."%' OR ccode like '%".$_POST['searchByName']."%' OR cname like '%".$_POST['searchByName']."%')";
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

	if(isset($_POST['searchByselstypes']) && $_POST['searchByselstypes'] != '')
	{
		$filters .= " and a.quotetype='".$_POST['searchByselstypes']."'";		
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
		$query .= ' ORDER BY ddate DESC ';
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

		$queryChkex = "select * from so_t where compcode='".$company."' and creference = '".$row['ctranno']."'";
		$statEx = $connect->prepare($queryChkex);
		$statEx->execute();
		$chexst = $statEx->rowCount();


		$sub_array = array();
		$sub_array[] = $row['ctranno']; 
		$sub_array[] = $row['ccode'];
		$sub_array[] = $row['cname'];
		$sub_array[] = $row['ddate'];
		$sub_array[] = $row['ngross'];
		$sub_array[] = $row['lapproved'];
		$sub_array[] = $row['lcancelled'];
		$sub_array[] = $chexst;
		$sub_array[] = ucfirst($row['quotetype']);
		$sub_array[] = $row['lsent'];
		$sub_array[] = $row['quotetype'];
		$sub_array[] = $row['lvoid'];
		if($row['dtrandate']!=null){
			$sub_array[] = date_format(date_create($row['dtrandate']), "M d, Y");
		}else{
			$sub_array[] = "";
		}
		$sub_array[] = date_format(date_create($row['dcutdate']), "M d, Y");

		$xcstat = "False";
		foreach($chkapprovals as $rocx){
			if($rocx['ctranno']==$row['ctranno'] && $rocx['userid']==$employeeid){
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
		$company = $_SESSION['companyid'];
		$query = "select a.*,b.cname from quote a left join customers b on a.ccode=b.cempid and a.compcode=b.compcode where a.compcode='$company'";
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