<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['txtctranno'];

	mysqli_query($con,"DELETE from apv_t where compcode='$company' and ctranno='$tran'");
	
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

	$z=0;

	//Debit Side		
	//InputVat if VATABLE and Net Gross
	
	//Net Gross
	$captypes = "";
	$resulthdr = mysqli_query ($con, "Select captype from apv where compcode='$company' and ctranno = '$tran'"); 
	if(mysqli_num_rows($resulthdr)!=0){
		while($rowhdr = mysqli_fetch_array($resulthdr, MYSQLI_ASSOC)){
			$captypes = $rowhdr['captype'];
		}
	}

	if($captypes=="Purchases"){

		$sqlnet = "Select A.cacctcode, A.cacctdesc, Sum(A.nVat) as nNetVat
			From (
				Select D.cacctid as cacctcode, D.cacctdesc, A.nnetvat AS nVat
				From suppinv_t A
				left join items C on A.compcode=C.compcode and A.citemno=C.cpartno 
				left join accounts D on C.compcode=D.compcode and C.cacctcodewrr=D.cacctno
				where A.compcode='$company' and A.ctranno in (Select crefno from apv_d where ctranno='$tran')
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
	}elseif($captypes=="PurchAdv"){

		$sql = "Select Sum(A.nVat) as nNetVat
		From (
			Select A.nnet AS nVat
			From purchase A
			where A.compcode='$company' and A.cpono in (Select crefno from apv_d where compcode='$company' and ctranno='$tran')
		) A HAVING Sum(A.nVat) <> 0";

		$resultNET = mysqli_query ($con, $sql); 

		if(mysqli_num_rows($resultNET)!=0){
			while($rowNET = mysqli_fetch_array($resultNET, MYSQLI_ASSOC)){
			$z = $z+1;
			$refcidenttran = $tran."P".$z;
						

				$Sales_Vat = getSetAcct("PO_ADV_PAYMENT");

				$SID = $Sales_Vat["id"];
				$SNM = $Sales_Vat["name"];

			mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$SID."','".$SNM."','',".$rowNET['nNetVat'].",0,'Others')");

			//echo "'$company','$refcidenttran',$z,'$tran','','".$SID."','".$SNM."','',".$rowNET['nNetVat'].",0,'Others'";
	
			}
		}
	}
		
	//InputVat	
	if($captypes=="Purchases"){
		$sqlvat = "Select A.cvatcode, A.nrate as nvatrate, Sum(A.nVat) as nVat
		From (
			Select C.cacctcodewrr as cacctcode,D.cacctdesc, A.cvatcode, A.nlessvat AS nVat, A.nrate
			From suppinv_t A 
			left join items C on A.compcode=C.compcode and A.citemno=C.cpartno 
			left join accounts D on C.compcode=D.compcode and C.cacctcodewrr=D.cacctno				  
			where A.compcode='$company' and A.ctranno in (Select crefno from apv_d where compcode='$company' and ctranno='$tran')
		) A Group By  A.cvatcode, A.nrate HAVING Sum(A.nVat) <> 0";
	}elseif($captypes=="PurchAdv"){
		$sqlvat = "Select A.cvatcode, A.nrate as nvatrate, Sum(A.nVat) as nVat
		From (
			Select C.cacctcodewrr as cacctcode,D.cacctdesc, A.ctaxcode as cvatcode, 
			CASE WHEN A.nrate>0 THEN (A.nbaseamount / (1 + (A.nrate/100))) * (A.nrate/100) ELSE A.nbaseamount END nVat, A.nrate
			From purchase_t A 
			left join items C on A.compcode=C.compcode and A.citemno=C.cpartno 
			left join accounts D on C.compcode=D.compcode and C.cacctcodewrr=D.cacctno 					  
			where A.compcode='$company' and A.cpono in (Select crefno from apv_d where compcode='$company' and ctranno='$tran')
		) A Group By  A.cvatcode, A.nrate HAVING Sum(A.nVat) <> 0";
	}
	
	$resultTX = mysqli_query ($con, $sqlvat);  

	if(mysqli_num_rows($resultTX)!=0){
		while($rowTX = mysqli_fetch_array($resultTX, MYSQLI_ASSOC)){

			if(floatval($rowTX['nvatrate']) > 0){
		
				$z = $z+1;
				$refcidenttran = $tran."P".$z;
				
				$Sales_Vat = getSetAcct("PURCH_VAT");

				$SID = $Sales_Vat["id"];
				$SNM = $Sales_Vat["name"];
			
				$xy = getAcctDef($SID,"PAYABLES");
				$xyValx = "";
				if($xy=="None"){
					$xyValx = "Others";
				}else{
					$xyValx = "Payables";
				}
							
				mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`, `cewtcode`, `newtrate`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$SID."','".$SNM."','',".$rowTX['nVat'].",0,'$xyValx','".$rowTX['cvatcode']."','".$rowTX['nvatrate']."')");

			}
	
	
		}
	}


	//Credit Side
	//Payable
	
		$z = $z+1;
		$refcidenttran = $tran."P".$z;

		$result = mysqli_query ($con, "Select A.cacctno as cacctcode, D.cacctdesc, sum(A.napplied) as nappld, sum(A.napcm) as napcm From apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno left join suppliers C on B.compcode=C.compcode and B.ccode=C.ccode left join accounts D on C.compcode=D.compcode and A.cacctno=D.cacctid Where A.compcode='$company' and A.ctranno='$tran' Group By C.cacctcode, D.cacctdesc");

	
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
				$xy = getAcctDef($row['cacctcode'],"PAYABLES");
				$xyValx = "";
				if($xy=="None"){
					$xyValx = "Others";
				}else{
					$xyValx = "Payables";
				}

				$xcvn = floatval($row['nappld']) + floatval($row['napcm']);
			
				mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) VALUES ('$company','$refcidenttran',$z,'$tran','','".$row['cacctcode']."','".$row['cacctdesc']."','',0,".$xcvn.",'$xyValx')");
				
		
			}
		}
		
		//EWT		
		if($captypes=="Purchases"){
			$result =  mysqli_query ($con, "Select A.cewtcode, A.nrate, Sum(A.nVat) as nVat From (Select A.cewtcode, B.nrate, A.newt AS nVat From suppinv A left join wtaxcodes B on A.compcode=B.compcode and A.cewtcode=B.ctaxcode where A.compcode='$company' and A.ctranno in (Select crefno from apv_d where compcode='$company' and ctranno='$tran')) A HAVING Sum(A.nVat) <> 0");
		}elseif($captypes=="PurchAdv"){
			$result =  mysqli_query ($con, "Select A.cewtcode, A.nrate, Sum(A.nVat) as nVat From (Select A.cewtcode, B.nrate, A.newt AS nVat From purchase A left join wtaxcodes B on A.compcode=B.compcode and A.cewtcode=B.ctaxcode where A.compcode='$company' and A.cpono in (Select crefno from apv_d where compcode='$company' and ctranno='$tran')) A HAVING Sum(A.nVat) <> 0");
		}

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
				 				 
				 mysqli_query ($con, "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`, `cewtcode`, `newtrate`) VALUES ('$company','$refcidenttran',$z,'$tran','','$SID','$SNM','',0,".$row['nVat'].",'$xyValx','".$row['cewtcode']."','".$row['nrate']."')");
				 
		
			}
		}

	
?>

<form action="APV_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?=$tran;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>
