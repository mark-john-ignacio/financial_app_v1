<?php
if(!isset($_SESSION)){
	session_start();
}

require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$id = $_REQUEST['id'];

	//get reset value
	$getcut = mysqli_query ($con, "select cvalue from parameters WHERE compcode='$company' and ccode = 'POSCLMT'"); 
	$rescut =  mysqli_fetch_array($getcut, MYSQLI_ASSOC);
	$varCutDate = $rescut['cvalue'];
	
	//Get Invoices per reset date..
	if($varCutDate=="Daily"){
		$qry = " and DATE(dcutdate) = CURDATE()";
	}
	elseif($varCutDate=="Weekly"){
		$qry = " and YEARWEEK(dcutdate) = YEARWEEK(CURDATE())";
	}
	elseif($varCutDate=="Semi"){
		require_once "th_semicutoff.php";	
		
		$qry = " and dcutdate between '".$datefrom."' and '".$dateto."'";	
				
	}
	elseif($varCutDate=="Monthly"){
		$qry = " and MONTH(dcutdate) = MONTH(CURDATE())";
	}
	elseif($varCutDate=="Yearly"){
		$qry = " and YEAR(dcutdate) = YEAR(CURDATE())";
	}
	elseif($varCutDate=="Never"){
		$qry = "";
	}

	$sqlinvs = "Select SUM(A.ngross) as ngross From ( Select ifnull(sum(ngross),0) as ngross from so where compcode='$company' and lcancelled=0 and lapproved=0 and ccode='$id'".$qry . " UNION ALL Select ifnull(sum(ngross),0) as ngross from dr where compcode='$company' and lcancelled=0 and lapproved=0 and ccode='$id'".$qry . " UNION ALL Select ifnull(sum(ngross),0) as ngross from sales where compcode='$company' and lcancelled=0 and ccode='$id'".$qry.") A";
	
	//echo $sqlinvs;
	
	$getinvs = mysqli_query ($con, $sqlinvs);
	$resinvs =  mysqli_fetch_array($getinvs, MYSQLI_ASSOC);

	$varinvsgross = $resinvs['ngross'];

	//Get Payments: ibabawas sa nakuha pra macompute ang remaining balance
	$sqlors = "Select ifnull(sum(namount),0) as ngross from receipt where compcode='$company' and lapproved=1 and ccode='$id'".$qry;
	$getors = mysqli_query ($con, $sqlors);
	$resors =  mysqli_fetch_array($getors, MYSQLI_ASSOC);
	
	//echo $sqlors;
	
	$varorsgross = $resors['ngross'];
	
		 $json['invs'] = $varinvsgross;
		 $json['ors'] = $varorsgross;
		 $json2[] = $json;
		 
	echo json_encode($json2);



?>
