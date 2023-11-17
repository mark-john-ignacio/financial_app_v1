<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

	$avail = $_REQUEST['itmbal'];

	if($_REQUEST['y']!=""){
		$qry = "and a.citemno not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	/*	$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc, d.nqty as navail
		from so_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join
			(
			 Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From dr_t x
			 left join dr y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			) c on a.ctranno=c.creference and a.citemno=c.citemno
		left join 
			(
			 select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit as cunit, X.citemno, X.nfactor
			 From tblinventory X
			 where X.compcode='$company' and X.dcutdate <= '$date1'
			 Group by X.cmainunit, X.citemno
			) d on a.citemno=d.citemno

		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry;

	
	$result = mysqli_query ($con, $sql); 

	$json2 = array();
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$nqty1 = $row['nqty'];
			$nqty2 = $row['nqty2']; 
		
			$json['nident'] = $row['nident'];
			$json['citemno'] = $row['citemno'];
			$json['cdesc'] = $row['citemdesc'];
			$json['cunit'] = $row['cunit'];
			$json['nqty'] = $nqty1 - $nqty2;
			$json['navail'] = $row['navail'];
			$json2[] = $json;
	
		}

	}
	*/


	@$arritmdesc = array();
	$itmlst = mysqli_query ($con, "Select * from items where compcode='$company'");	
	if (mysqli_num_rows($itmlst)!=0){
		while($row = mysqli_fetch_array($itmlst, MYSQLI_ASSOC)){
			@$arritmdesc[$row['cpartno']]=$row['citemdesc'];
		}
	}

	//get all so
	$resq = mysqli_query ($con, "Select A.*, B.cvattype From so_t A left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno = '".$_REQUEST['x']."'".$qry);
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing dr
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, crefident, citemno, sum(nqty) as nqty From dr_t a left join dr b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0 group by creference, crefident,citemno");
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

		//Serach item if existing in DR
		$inarray = "No";
		$nqty2 = 0;
		foreach(@$arrinv as $rsibnv){
			if($row['ctranno']==$rsibnv['creference']){
				if($row['citemno']==$rsibnv['citemno'] && $row['nident']==$rsibnv['crefident']){
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
