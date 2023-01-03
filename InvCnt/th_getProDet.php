<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$id = $_REQUEST['id'];
				
		//Pag wla sa barcode table search items table
		$sql = "select  A.cscanno, B.cpartno, B.citemdesc, B.cunit
			from items_barcode A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno 
			where A.compcode='$company' and A.cscanno = '".$id."'";
		//echo $sql;
		$rsd = mysqli_query($con,$sql);
		
		if(mysqli_num_rows($rsd)>=1){
			
				while($row2 = mysqli_fetch_array($rsd, MYSQLI_ASSOC)){

					$json['cscan'] = $row2['cscanno'];
					$json['id'] = $row2['cpartno'];
					$json['desc'] = $row2['citemdesc'];
					$json['cunit'] = strtoupper($row2['cunit']);
					
					$json2[] = $json;
			
				}

		
		}
		else{
			$sqlitm = "select A.cpartno, A.citemdesc, A.cunit
			from items A where A.compcode='$company' and A.cpartno = '".$id."'";
		
			$rsditm = mysqli_query($con,$sqlitm);
			if(mysqli_num_rows($rsditm)>=1){
				
					while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
	
						$json['cscan'] = $row['cpartno'];
						$json['id'] = $row['cpartno'];
						$json['desc'] = $row['citemdesc'];
						$json['cunit'] = strtoupper($row['cunit']);
						
						$json2[] = $json;
				
					}
	
			
			}
			else{
						$json['cscan'] = "FALSE";
						$json['id'] = "";
						$json['desc'] = "";
						$json['cunit'] = "";

			}

		}

	

	echo json_encode($json2);


?>
