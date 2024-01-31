<?php
ini_set('max_execution_time', 1800); // 300 = 5min

if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


$chkSales = mysqli_query($con2,"select * from receive where compcode='001' and dreceived between '2018-06-01' and '2018-06-30' Order By ctranno desc");

	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		
		
			if (!mysqli_query($con, "INSERT INTO receive(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dreceived`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`) values('".$row["compcode"]."', '".$row["ctranno"]."', '".$row["ccode"]."', '".$row["cremarks"]."', '".$row["ddate"]."', '".$row["dreceived"]."', '".$row["ngross"]."', '".$row["cpreparedby"]."', '".$row["lcancelled"]."', '1', '".$row["lprintposted"]."','".$row["ccustacctcode"]."')")) {
				
				echo $row["ctranno"]."<br>";
				
				echo mysqli_error($con);
			} 
			else {
				
				$chkSalesT = mysqli_query($con2,"select * from receive_t where compcode='001' and ctranno = '".$row["ctranno"]."'");
				while($rowT = mysqli_fetch_array($chkSalesT, MYSQLI_ASSOC)){
				
				$refcidenttran = $row["ctranno"]."P".$rowT["nident"];
				
				
					if (!mysqli_query($con,"INSERT INTO receive_t(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`, `dexpired`) values('".$rowT["compcode"]."', '".$refcidenttran."', '".$row["ctranno"]."', '".$rowT["nident"]."', '".$rowT["creference"]."', '".$rowT["nrefidentity"]."', '".$rowT["citemno"]."', '".$rowT["nqty"]."', '".$rowT["nqtyorig"]."', '".$rowT["cunit"]."', '".$rowT["nprice"]."', '".$rowT["namount"]."', '".$rowT["ncost"]."', '".$rowT["nfactor"]."', '".$rowT["cmainunit"]."', '".$rowT["cacctcode"]."',NULL) ")){
		//echo "False";
		
						echo "Errormessage: %s\n", mysqli_error($con);
					}
					else{
						//echo "True";
						echo $row["ctranno"]."OK<br>";
					}
					
				}

				
				
			}

		
		
		
		
	}
	
?>