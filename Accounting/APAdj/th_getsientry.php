<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['invno'];
	$prtran = $_REQUEST['srno'];

	$json2 = array();

	function getAcctDef($id,$typ){

		global $company;
		global $con;
		
		$sqldefacc = mysqli_query($con,"Select * from accounts_default where compcode='$company' and ccode='$typ' and cacctno='$id'");
		if (mysqli_num_rows($sqldefacc)!=0) {
			$rowdefacc = mysqli_fetch_assoc($sqldefacc);
			
			return $rowdefacc["cacctno"];

		}
		else{
			return "None";
		}	

	}

	function getSetAcct($id){
		global $company;
		global $con;
	
		$sqldefacc = mysqli_query($con,"Select B.cacctid, A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='$id'");
		if (mysqli_num_rows($sqldefacc)!=0) {
			$rowdefacc = mysqli_fetch_assoc($sqldefacc);
			
			$array["id"] = $rowdefacc["cacctid"];
			$array["name"] = $rowdefacc["cacctdesc"];
			
			return $array;
		}

	}

		$mdets = array();
		$sqldets = mysqli_query ($con, "Select A.citemno, A.nqty, B.nprice, B.cvatcode, C.cacctid, C.cacctdesc, D.nrate
		From purchreturn_t A 
		left join suppinv_t B on A.compcode=B.compcode and A.citemno=B.citemno and A.creference=B.creference and A.nrefidentity=B.nrefidentity
		left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno
		left join vatcode D on B.compcode=D.compcode and B.cvatcode=D.cvatcode
		Where A.compcode='$company' and A.ctranno='$prtran'");
		while($row = mysqli_fetch_array($sqldets, MYSQLI_ASSOC)){
			$mdets[] = $row;
		}

		$totgross = 0;
		foreach($mdets as $xrow){
			$totgross = $totgross + (floatval($xrow['nqty']) * floatval($xrow['nprice']));
		}

		//Debit Side -> Original Credit
		//Payable
		$result = mysqli_query ($con, "Select B.cacctid as cacctcode, B.cacctdesc From suppinv A 
		left join accounts B on A.compcode=B.compcode and A.ccustacctcode=B.cacctno Where A.compcode='$company' and A.ctranno='$tran'"); 
	
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
				$json['cacctid'] = $row['cacctcode'];
				$json['cacctdesc'] = $row['cacctdesc'];
				$json['ndebit'] = round($totgross,2);
				$json['ncredit'] = 0;
				$json2[] = $json;
				
			}
		}


		//Credit Side -> Original Debit	
		//Input Tax if VATABLE and Net Gross
	
			//Net Gross
			$sqlnet = "Select A.cacctid, A.cacctdesc, Sum(A.nNetVat) as nNetVat
			From (
				Select A.citemno, A.nqty, B.nprice, B.cvatcode, C.cacctid, C.cacctdesc, B.nrate, CASE WHEN B.nrate <> 0 THEN ((A.nqty*B.nprice) / (1 + (B.nrate/100))) ELSE (A.nqty*B.nprice) END as nNetVat
				From purchreturn_t A 
				left join suppinv_t B on A.compcode=B.compcode and A.citemno=B.citemno and A.creference=B.creference and A.nrefidentity=B.nrefidentity
				left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno
				Where A.compcode='$company' and A.ctranno='$prtran'
			) A Group By A.cacctid, A.cacctdesc HAVING Sum(A.nNetVat) <> 0";			
			
			$resultNET = mysqli_query ($con, $sqlnet); 

			if(mysqli_num_rows($resultNET)!=0){
				while($rowNET = mysqli_fetch_array($resultNET, MYSQLI_ASSOC)){

					$json['cacctid'] = $rowNET['cacctid'];
					$json['cacctdesc'] = $rowNET['cacctdesc'];
					$json['ndebit'] = 0;
					$json['ncredit'] = round($rowNET['nNetVat'],2);
					$json2[] = $json;
			
				}
			}
			
			//InputVat
			
			$sqlvat = "Select Sum(A.nVat) as nVat
			From (
				Select A.citemno, A.nqty, B.nprice, B.cvatcode, C.cacctid, C.cacctdesc, B.nrate, CASE WHEN B.nrate <> 0 THEN ((A.nqty*B.nprice) / (1 + (B.nrate/100))) * (B.nrate/100) ELSE 0 END as nVat
				From purchreturn_t A 
				left join suppinv_t B on A.compcode=B.compcode and A.citemno=B.citemno and A.creference=B.creference and A.nrefidentity=B.nrefidentity
				left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno
				Where A.compcode='$company' and A.ctranno='$prtran'
			) A HAVING Sum(A.nVat) <> 0";
				
			
			$resultTX = mysqli_query ($con, $sqlvat); 

			if(mysqli_num_rows($resultTX)!=0){
				while($rowTX = mysqli_fetch_array($resultTX, MYSQLI_ASSOC)){
					
					$Sales_Vat = getSetAcct("PURCH_VAT");

					$SID = $Sales_Vat["id"];
					$SNM = $Sales_Vat["name"];

					$json['cacctid'] = $SID;
					$json['cacctdesc'] = $SNM;
					$json['ndebit'] = 0;
					$json['ncredit'] = round($rowTX['nVat'],2);
					$json2[] = $json;			
			
				}

			}

	
		
	echo json_encode($json2);

	


?>
