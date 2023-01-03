<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['tran'];
	
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


	//Credit Side
	//Payable
	$z=0;
	
	$z = $z+1;
	$refcidenttran = $tran."P".$z;
	
		$result = mysqli_query ($con, "Select D.cacctid as cacctcode, D.cacctdesc, sum(A.napplied) as nappld From apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno left join suppliers C on B.compcode=C.compcode and B.ccode=C.ccode left join accounts D on C.compcode=D.compcode and C.cacctcode=D.cacctno Where A.compcode='$company' and A.ctranno='$tran' Group By C.cacctcode, D.cacctdesc"); 
	
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					$xy = getAcctDef($row['cacctcode'],"PAYABLES");
					$xyValx = "";
					if($xy=="None"){
						$xyValx = "Others";
					}else{
						$xyValx = "Payables";
					}
				 
				 
				 mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$row['cacctcode']."','".$row['cacctdesc']."','',0,".$row['nappld'].",'$xyValx')");
				 
		
			}
		}
		
	//EWT
		
		$result = mysqli_query ($con, "Select D.cacctid as cacctcode, D.cacctdesc, sum(A.newtamt) as nappld From apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno left join suppliers C on B.compcode=C.compcode and B.ccode=C.ccode left join accounts D on C.compcode=D.compcode and C.cacctcode=D.cacctno Where A.compcode='$company' and A.ctranno='$tran' Group By C.cacctcode, D.cacctdesc Having sum(A.newtamt) > 0"); 
	
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$z = $z+1;
				$refcidenttran = $tran."P".$z;
		
					$Sales_EWT = getSetAcct("EWTPAY");
					$SID = $Sales_EWT["id"];
					$SNM = $Sales_EWT["name"];
					
					$xy = getAcctDef($SID,"PAYABLES");
					$xyValx = "";
					if($xy=="None"){
						$xyValx = "Others";
					}else{
						$xyValx = "Payables";
					}
				 				 
				 mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','$SID','$SNM','',0,".$row['nappld'].",'$xyValx')");
				 
		
			}
		}

	//credits and discunts
	$result = mysqli_query ($con, "Select D.cacctid as cacctno, D.cacctdesc, sum(A.namount) as namount From apv_deds A left join accounts D on A.compcode=D.compcode and A.cacctno=D.cacctno Where A.compcode='$company' and A.ctranno='$tran' Group By A.cacctno, D.cacctdesc Having sum(A.namount) > 0"); 
	
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$z = $z+1;
				$refcidenttran = $tran."P".$z;
				 				 
				 mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$row['cacctno']."','".$row['cacctdesc']."','',0,".$row['namount'].",'Others')");
				 
		
			}
		}	
		
	//Debit Side		
	//Input Tax if VATABLE and Net Gross
	
			//Net Gross
			$sqlnet = "Select A.cacctcode, A.cacctdesc, Sum(A.nVat) as nNetVat
				From (
					Select D.cacctid as cacctcode,D.cacctdesc, 
					CASE WHEN H.lcompute=1 THEN ROUND((SUM(A.namount)/(1 + (E.nrate/100))), 4) ELSE ROUND(SUM(A.namount),4) END AS nVat
					From receive_t A
					left join items C on A.compcode=C.compcode and A.citemno=C.cpartno 
					left join accounts D on C.compcode=D.compcode and C.cacctcodewrr=D.cacctno
					left join taxcode E on C.compcode=E.compcode and C.ctaxcode=E.ctaxcode
					left join receive F on A.compcode=F.compcode and A.ctranno=F.ctranno  
					left join suppliers G on F.compcode=G.compcode and F.ccode=G.ccode 
					left join vatcode H on G.compcode=H.compcode and G.cvattype=H.cvatcode   
					where A.compcode='$company' and A.ctranno in (Select crefno from apv_d where ctranno='$tran')
					group by C.cacctcodewrr,D.cacctdesc
				) A group by A.cacctcode,A.cacctdesc HAVING Sum(A.nVat) <> 0";
				
			
			$resultNET = mysqli_query ($con, $sqlnet); 

			if(mysqli_num_rows($resultNET)!=0){
				while($rowNET = mysqli_fetch_array($resultNET, MYSQLI_ASSOC)){
				$z = $z+1;
				$refcidenttran = $tran."P".$z;
							
					$xy = getAcctDef($rowNET['cacctcode'],"PAYABLES");
					$xyValx = "";
					if($xy=="None"){
						$xyValx = "Others";
					}else{
						$xyValx = "Payables";
					}
				 				 
				 mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$rowNET['cacctcode']."','".$rowNET['cacctdesc']."','',".$rowNET['nNetVat'].",0,'$xyValx')");
			
				}
			}
			
			//InputVat
			
			$sqlvat = "Select Sum(A.nVat) as nVat
				From (
					Select C.cacctcodewrr as cacctcode,D.cacctdesc, 
					CASE WHEN H.lcompute=1 THEN ROUND((SUM(A.namount)/(1 + (E.nrate/100))) * ((E.nrate/100)), 4) ELSE 0 END AS nVat
					From receive_t A 
					left join items C on A.compcode=C.compcode and A.citemno=C.cpartno 
					left join accounts D on C.compcode=D.compcode and C.cacctcodewrr=D.cacctno
					left join taxcode E on C.compcode=E.compcode and C.ctaxcode=E.ctaxcode
					left join receive F on A.compcode=F.compcode and A.ctranno=F.ctranno  
					left join suppliers G on F.compcode=G.compcode and F.ccode=G.ccode 
					left join vatcode H on G.compcode=H.compcode and G.cvattype=H.cvatcode 					  
					where A.compcode='$company' and A.ctranno in (Select crefno from apv_d where ctranno='$tran')
					group by C.cacctcodewrr,D.cacctdesc
				) A HAVING Sum(A.nVat) <> 0";
				
			
			$resultTX = mysqli_query ($con, $sqlvat); 

			if(mysqli_num_rows($resultTX)!=0){
				while($rowTX = mysqli_fetch_array($resultTX, MYSQLI_ASSOC)){
				
					$z = $z+1;
					$refcidenttran = $tran."P".$z;
					
					$Sales_Vat = getSetAcct("SALES_VAT");

					$SID = $Sales_Vat["id"];
					$SNM = $Sales_Vat["name"];
				
					$xy = getAcctDef($SID,"PAYABLES");
					$xyValx = "";
					if($xy=="None"){
						$xyValx = "Others";
					}else{
						$xyValx = "Payables";
					}
				 				 
				 mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$SID."','".$SNM."','',".$rowTX['nVat'].",0,'$xyValx')");
			
			
				}
			}

?>
