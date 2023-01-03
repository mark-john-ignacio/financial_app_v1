<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	$xvatcode = $row["compvat"];
	$xcomp = $row["lcompute"];
}

function getDefAcct($id){
	global $company;
	global $con;

	$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctno where A.compcode='$company' and A.ccode='$id'");
	if (mysqli_num_rows($sqldefacc)!=0) {
		$rowdefacc = mysqli_fetch_assoc($sqldefacc);
		
		$array["id"] = $rowdefacc["cacctno"];
		$array["name"] = $rowdefacc["cacctdesc"];
		
		return $array;
	}

}


		//get Customer Entry
		$result = mysqli_query($con,"Select A.creference,D.cacctno,D.cacctdesc,Sum(A.nAmount) as ngross From salesreturn_t A left join salesreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno left join customers C on B.compcode=C.compcode and B.ccode=C.cempid left join accounts D on C.compcode=D.compcode and C.cacctcodesales=D.cacctno where A.compcode='$company' and A.ctranno='".$_REQUEST['x']."'");
		
		while($rowhdr = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$json['csino'] = $rowhdr["creference"];
			$json['cacctno'] = $rowhdr["cacctno"];
			$json['cacctdesc'] = $rowhdr["cacctdesc"];
			$json['ndebit'] = 0;
			$json['ncredit'] = $rowhdr["ngross"];
			$json2[] = $json;
		
		
			$sql0 = "select * from salesreturn_t WHERE compcode='$company' and ctranno = '".$_REQUEST['x']."' and creference='".$rowhdr["creference"]."'";
			$result0 = mysqli_query ($con, $sql0); 
			while($row0 = mysqli_fetch_array($result0, MYSQLI_ASSOC)){
				
			//echo "<br>".$row0["citemno"]." : ".$row0["namount"];
			
				 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
					
					$result1 = mysqli_query($con,"Select A.ctranno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(".$row0["namount"].")/(1 + (D.nrate/100)) ,2) Else ".$row0["namount"]." END as ngross
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
					where A.compcode='$company' and A.ctranno='".$row0["creference"]."' and A.citemno = '".$row0["citemno"]."' and A.nident='".$row0["nrefident"]."'");
										
					
					$rowhdr1 = mysqli_fetch_array($result1,MYSQLI_ASSOC);
							$json['csino'] = $rowhdr1["ctranno"];
							$json['cacctno'] = $rowhdr1["cacctcode"];
							$json['cacctdesc'] = $rowhdr1["cacctdesc"];
							$json['ndebit'] = $rowhdr1["ngross"];
							$json['ncredit'] = 0;
							$json2[] = $json;
					

					//VAT Entry
					//get Default SALES_VAT Code
					$Sales_Vat = getDefAcct("SALES_VAT");
		
					$SID = $Sales_Vat["id"];
					$SNM = $Sales_Vat["name"];
					
					$sqlvat = "Select A.ctranno,A.dcutdate, Sum(A.nVat) as nVat
						From (
							Select A.ctranno, B.dcutdate, A.citemno, ROUND((SUM(".$row0["namount"].")/(1 + (D.nrate/100))) * ((D.nrate/100)), 2) AS nVat
							From sales_t A 
							left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
							left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
							left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
							left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
							where A.compcode='$company' and A.ctranno='".$row0["creference"]."' and A.citemno = '".$row0["citemno"]."' and A.nident='".$row0["nrefident"]."'
							group by B.dcutdate, A.citemno
						) A HAVING Sum(A.nVat) <> 0";
						//echo $sqlvat;
						
					$resvat = mysqli_query($con,$sqlvat);
					if (mysqli_num_rows($resvat)!=0) {
						while($rowhdr2 = mysqli_fetch_array($resvat, MYSQLI_ASSOC)){
							
							$json['csino'] = $rowhdr2["ctranno"];
							$json['cacctno'] = $SID;
							$json['cacctdesc'] = $SNM;
							$json['ndebit'] = $rowhdr2["nVat"];
							$json['ncredit'] = 0;
							$json2[] = $json;
						
						}
					}
											
					
				 }		
					
				 else{ // pag nde vatable no VAT dapat
					
					$sqldet1 = mysqli_query($con,"Select A.ctranno,A.cacctcode,C.cacctdesc, ROUND(SUM(".$row0["namount"].",2)) as ngross From sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='".$row0["creference"]."' and A.citemno = '".$row0["citemno"]."' 	and A.nident='".$row0["nrefident"]."' group by B.dcutdate,A.cacctcode,C.cacctdesc");
						$result1 = mysqli_query ($con, $sqldet1);
						$rowhdr1 = mysqli_fetch_array($result1,MYSQLI_ASSOC);
						
							$json['csino'] = $rowhdr1["ctranno"];
							$json['cacctno'] = $rowhdr1["cacctcode"];
							$json['cacctdesc'] = $rowhdr1["cacctdesc"];
							$json['ndebit'] = $rowhdr1["ngross"];
							$json['ncredit'] = 0;
							$json2[] = $json;
		
				
				 }
				
			
			}
		}

echo json_encode($json2);

?>
