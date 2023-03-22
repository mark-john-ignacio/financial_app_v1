<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	function getDefAcct($id){
		global $company;
		global $con;
	
		$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='$id'");
		if (mysqli_num_rows($sqldefacc)!=0) {
			$rowdefacc = mysqli_fetch_assoc($sqldefacc);
			
			$array["id"] = $rowdefacc["cacctno"];
			$array["name"] = $rowdefacc["cacctdesc"];
			
			return $array;
		}
	
	}


	$SRtran = $_REQUEST['srno'];
	$SItran = $_REQUEST['invno'];

	if($_REQUEST['styp']=="trade"){
		$tblhdr = "sales";
		$tbldtl = "sales_t";

		$tblhdrsr = "salesreturn";
		$tbldtlsr = "salesreturn_t";
	}else{
		$tblhdr = "ntsales";
		$tbldtl = "ntsales_t";

		$tblhdrsr = "ntsalesreturn";
		$tbldtlsr = "ntsalesreturn_t";
	}

	$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$xvatcode = $row["compvat"];
		$xcomp = $row["lcompute"];
	}

	$sqlhead = mysqli_query($con,"Select *, B.ctype from ".$tbldtlsr." A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$SRtran."'");
	$arrreturn = array();
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$arrreturn[] = $row;
	}

	$sqlhead = mysqli_query($con,"Select * from ".$tbldtl." where compcode='$company' and ctranno='".$SItran."'");
	$arrinvoices = array();
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$arrinvoices[] = $row;
	}


	$sqlhead = mysqli_query($con,"Select B.cacctcodetype from ".$tblhdr." A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid where A.compcode='$company' and A.ctranno='$SItran'");
	
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cSIsalescodetype = $row["cacctcodetype"];
	}

	//CUSTOMERS ENTRY - RECEIVABLES CREDIT
	if($cSIsalescodetype=="multiple"){

		$qrySI = "Select B.cacctno,D.cacctdesc,ROUND(C.ngross,2) as ngross
				From ".$tblhdr." A
				left join customers_accts B on A.compcode=B.compcode and A.ccode=B.ccode
				right join (
					Select B.ctype, sum(A.nprice * C.nqty) as ngross
					From ".$tbldtl." A
					left join items B on A.compcode=B.compcode and A.citemno=B.cpartno
					left join salesreturn_t C on A.compcode=C.compcode and A.citemno=C.citemno and A.nident=C.nrefident
					where A.compcode='$company' and A.ctranno='$SItran'
					Group By B.ctype
				) C on B.citemtype=C.ctype
				left join accounts D on B.compcode=D.compcode and B.cacctno=D.cacctid 
				where A.compcode='$company' and A.ctranno='$SItran'";

				$result = mysqli_query ($con,$qrySI);
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$json['cacctid'] = $row['cacctno'];
					$json['cacctdesc'] = $row['cacctdesc'];
					$json['ndebit'] = 0;
					$json['ncredit'] = $row['ngross'];
					$json2[] = $json;
				}


	}else{

		if($_REQUEST['styp']=="trade"){

			$totgross = 0;
			$totewtamt = 0;
			foreach($arrreturn as $rssr){
				foreach($arrinvoices as $rssi){
					if($rssr['creference']==$rssi['ctranno'] && $rssr['citemno']==$rssi['citemno'] && $rssr['nrefident']==$rssi['nident']){
						$amt = floatval($rssr['nqty']) * floatval($rssi['nprice']);
						
						if(!is_null($rssi['newtrate']) && $rssi['newtrate']<> 0){
						
							if($rssi['cbase']='NET'){
								$newamt = $amt - ROUND((ROUND($amt/(1 + ($rssi['nrate']/100)),2) * ($rssi['newtrate']/100)),2);
								$totewtamt = $totewtamt + (ROUND((ROUND($amt/(1 + ($rssi['nrate']/100)),2) * ($rssi['newtrate']/100)),2));
							}else{
								$newamt = $amt - ROUND(($amt * ($rssi['newtrate']/100)),2);
								$totewtamt = $totewtamt + (ROUND(($amt * ($rssi['newtrate']/100)),2));
							}

							$totgross = $totgross + $newamt; 

						}else{
							$totgross = $totgross + $amt; 
						}											
					}
				}
			}

		} else{
			
			$totgross = 0;
			foreach($arrreturn as $rssr){
				foreach($arrinvoices as $rssi){
					if($rssr['creference']==$rssi['ctranno'] && $rssr['citemno']==$rssi['citemno'] && $rssr['nrefident']==$rssi['nident']){
						$amt = floatval($rssr['nqty']) * floatval($rssi['nprice']);
						$totgross = $totgross +$amt; 
					}
				}
			}
		}

		$qrySI = "Select B.cacctid,B.cacctdesc From ".$tblhdr." A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$SItran'";

		$result = mysqli_query ($con,$qrySI);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$json['cacctid'] = $row['cacctid'];
			$json['cacctdesc'] = $row['cacctdesc'];
			$json['ndebit'] = 0;
			$json['ncredit'] = $totgross;
			$json2[] = $json;
		}

		$Sales_Ewt = getDefAcct("EWTREC");

		$SID = $Sales_Ewt["id"];
		$SNM = $Sales_Ewt["name"];

		$json['cacctid'] = $SID;
		$json['cacctdesc'] = $SNM;
		$json['ndebit'] = 0;
		$json['ncredit'] = $totewtamt;
		$json2[] = $json;


	}


	//ITEMS ENTRY - SALES AND VATS - DEBIT
	if($xcomp==1){ // Pag ung mismo may ari system ay Vatable

			$sql0 = "Select A.cacctcode,A.cacctdesc, ROUND(sum(A.ngross),2) as ngross
			From (Select B.dcutdate, A.citemno, C.cacctid as cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(F.nqty*A.nprice)/(1 + (A.nrate/100)) ,2) Else SUM(F.nqty*A.nprice) END as ngross
			From ".$tbldtl." A 
			left join ".$tblhdr." B on A.compcode=B.compcode and A.ctranno=B.ctranno 
			left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
			left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
			left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
			left join ".$tbldtlsr." F on A.compcode=F.compcode and A.citemno=F.citemno and A.nident=F.nrefident and F.ctranno='$SRtran'
			where A.compcode='$company' and A.ctranno='$SItran' 
			group by B.dcutdate,C.cacctid,C.cacctdesc,A.citemno) A Where IFNULL(A.ngross,0) <> 0";

			$result = mysqli_query ($con,$sql0);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$json['cacctid'] = $row['cacctcode'];
				$json['cacctdesc'] = $row['cacctdesc'];
				$json['ndebit'] = $row['ngross'];
				$json['ncredit'] = 0;
				$json2[] = $json;
			}

			//VAT Entry

			if($_REQUEST['styp']=="trade"){
				//get Default SALES_VAT Code
				$Sales_Vat = getDefAcct("SALES_VAT");

				$SID = $Sales_Vat["id"];
				$SNM = $Sales_Vat["name"];
				
				$sqlvat = "Select Sum(A.nVat) as nVat
				From (
					Select B.dcutdate, A.citemno, ROUND((SUM(F.nqty*A.nprice)/(1 + (D.nrate/100))) * ((D.nrate/100)), 2) AS nVat
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode
					left join salesreturn_t F on A.compcode=F.compcode and A.citemno=F.citemno and A.nident=F.nrefident and F.ctranno='$SRtran'
					where A.compcode='$company' and A.ctranno='$SItran'
					group by B.dcutdate, A.citemno
				) A Where IFNULL(A.nVat,0) <> 0";
				
				$resvat = mysqli_query($con,$sqlvat);
				$isok = "True";
				if (mysqli_num_rows($resvat)!=0) {
					while($rowvat = mysqli_fetch_array($resvat, MYSQLI_ASSOC)){

						$json['cacctid'] = $SID;
						$json['cacctdesc'] = $SNM;
						$json['ndebit'] = $rowvat['nVat'];
						$json['ncredit'] = 0;
						$json2[] = $json;
					
					}

				}
			}


	}else{ // pag nde vatable no VAT dapat

		$sql0 = "Select A.cacctcode,A.cacctdesc, sum(A.ngross) as ngross
		From (Select C.cacctid as cacctcode,C.cacctdesc,SUM(F.nqty*A.nprice) as ngross
		From  ".$tbldtl." A 
		left join ".$tblhdr." B on A.compcode=B.compcode and A.ctranno=B.ctranno 
		left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
		left join salesreturn_t F on A.compcode=F.compcode and A.citemno=F.citemno and A.nident=F.nrefident and F.ctranno='$SRtran'
		where A.compcode='$company' and A.ctranno='$SItran' group by B.dcutdate,C.cacctid,C.cacctdesc) A Where IFNULL(A.ngross,0) <> 0";

		$result = mysqli_query ($con,$sql0);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$json['cacctid'] = $row['cacctcode'];
			$json['cacctdesc'] = $row['cacctdesc'];
			$json['ndebit'] = $row['ngross'];
			$json['ncredit'] = 0;
			$json2[] = $json;
		}


	}

		
	echo json_encode($json2);

	


?>
