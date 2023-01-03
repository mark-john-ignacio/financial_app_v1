<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$id = $_REQUEST['id'];
	$typ = $_REQUEST['typ'];
	$ctranno = $_REQUEST['tno'];

	//get reset value
	$getcut = mysqli_query ($con, "select cvalue from parameters WHERE compcode='$company' and ccode = 'POSCLMT'"); 
	$rescut =  mysqli_fetch_array($getcut, MYSQLI_ASSOC);
	$varCutDate = $rescut['cvalue'];
	
	//Get Invoices per reset date..
	if($varCutDate=="Daily"){
		$qry = " and DATE(b.dcutdate) = CURDATE()";
	}
	elseif($varCutDate=="Weekly"){
		$qry = " and YEARWEEK(b.dcutdate) = YEARWEEK(CURDATE())";
	}
	elseif($varCutDate=="Semi"){
		require_once "th_semicutoff.php";	
		
		$qry = " and b.dcutdate between '".$datefrom."' and '".$dateto."'";	
				
	}
	elseif($varCutDate=="Monthly"){
		$qry = " and MONTH(b.dcutdate) = MONTH(CURDATE())";
	}
	elseif($varCutDate=="Yearly"){
		$qry = " and YEAR(b.dcutdate) = YEAR(CURDATE())";
	}
	elseif($varCutDate=="Never"){
		$qry = "";
	}

	$sqlinvs = "Select ifnull(sum(a.namount),0) as ngross from sales_t a left join sales b on a.ctranno=b.ctranno left join items c on a.citemno=c.cpartno where a.compcode='$company' and b.lcancelled=0 and b.ccode='$id' and c.ctype='$typ' and a.ctranno<>'$ctranno'".$qry;
	
	echo $sqlinvs;
	
	$getinvs = mysqli_query ($con, $sqlinvs);
	$resinvs =  mysqli_fetch_array($getinvs, MYSQLI_ASSOC);
	
	$varinvsgross = $resinvs['ngross'];

	if($typ=="GROCERY"){
	//Get Payments: ibabawas sa nakuha pra macompute ang remaining balance //for GROCERY ONLY
	$sqlors = "Select ifnull(sum(b.namount),0) as ngross from receipt b where b.compcode='$company' and b.lapproved=1 and b.ccode='$id'".$qry;
	//echo $sqlors;
	
	$getors = mysqli_query ($con, $sqlors);
	$resors =  mysqli_fetch_array($getors, MYSQLI_ASSOC);
	
	//echo $sqlors;
	
		$varorsgross = $resors['ngross'];
	}
	else{
		$varorsgross = 0;
	}
	
		 $json['invs'] = $varinvsgross;
		 $json['ors'] = $varorsgross;
		 $json2[] = $json;
		 
	echo json_encode($json2);



?>
