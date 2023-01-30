<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

	$avail = $_REQUEST['itmbal'];

	if($_REQUEST['y']!=""){
		$qry = "and A.nident not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	/*
		$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc, d.nqty as navail
		from quote_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From so_t x
			 left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		left join 
			(
			 select COALESCE((Sum(nqtyin*nfactor)-sum(nqtyout*nfactor)),0) as nqty, X.cunit, X.citemno, X.nfactor
			 From tblinventory X
			 where X.compcode='$company' and X.dcutdate <= '$date1'
			 Group by X.cunit, X.citemno
			) d on a.citemno=d.citemno

		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry;

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	//$json2 = array();
	//$json = [];
// echo	mysqli_num_rows($result);
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$nqty1 = $row['nqty'];
			$nqty2 = $row['nqty2']; 
		
			 $json['citemno'] = $row['citemno'];
			 $json['cdesc'] = $row['citemdesc'];
			 $json['cunit'] = $row['cunit'];
			 $json['nqty'] = $nqty1 - $nqty2;
			 $json['navail'] = $row['navail'];
			 $json2[] = $json;
	
		}

	}
	else{
			$json['citemno'] = "";
			$json['cdesc'] = "";
			$json['cunit'] = "";
			$json['nqty'] = "";
			$json['navail'] = "";
			$json2[] = $json;
	}
	
	echo json_encode($json2);
*/




@$arritmdesc = array();
	$itmlst = mysqli_query ($con, "Select * from items where compcode='$company'");	
	if (mysqli_num_rows($itmlst)!=0){
		while($row = mysqli_fetch_array($itmlst, MYSQLI_ASSOC)){
			@$arritmdesc[$row['cpartno']]=$row['citemdesc'];
		}
	}

	//get all quotation
	$resq = mysqli_query ($con, "Select A.*, B.cvattype From quote_t A left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno = '".$_REQUEST['x']."'".$qry);
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing SO
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, nrefident, citemno, sum(nqty) as nqty From so_t a left join so b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 group by creference, nrefident,citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	if($avail==0){
		//get items inventory
		@$arrinventiry = array();
		$resinv = mysqli_query ($con, "select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit, X.citemno, X.nfactor From tblinventory X where X.compcode='$company' and X.dcutdate <= '$date1' Group by X.cmainunit, X.citemno");
		if (mysqli_num_rows($resinv)!=0){
			while($rowinv = mysqli_fetch_array($resinv, MYSQLI_ASSOC)){
				@$arrinventiry[]=$rowinv;
			}
		}
	}

	$json2 = array();
	foreach($arrresq as $row){

		//Serach item if existing in SO
		$inarray = "No";
		$nqty2 = 0;
		foreach(@$arrinv as $rsibnv){
			if($row['ctranno']==$rsibnv['creference']){
				if($row['citemno']==$rsibnv['citemno'] && $row['nident']==$rsibnv['nrefident']){
					$nqty2 = $rsibnv['nqty']; 
				}
			}
		}

		//if for inventory cheking, search sa inventory if exist
		$availinvtory = 1;
		if($avail==0){
			foreach(@$arrinventiry as $rxinv){
				if($row['citemno']==$rxinv['citemno']){
					$availinvtory = $rxinv['nqty']; 
				}
			}
		}

			$nqty1 = $row['nqty'];
			$xremain = $nqty1 - $nqty2;
			if($xremain>0){
		
				$json['id'] = $row['nident'];
				$json['citemno'] = $row['citemno'];
				$json['cdesc'] = @$arritmdesc[$row['citemno']];
				$json['cunit'] = $row['cunit'];
				$json['nqty'] = $nqty1 - $nqty2;
				$json['navail'] = $availinvtory;
				$json2[] = $json;

			}

	//}
	
	}


	echo json_encode($json2);

?>
