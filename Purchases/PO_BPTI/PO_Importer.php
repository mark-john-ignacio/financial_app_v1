<?php
ini_set('max_execution_time', 1800); // 300 = 5min

if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


$chkSales = mysqli_query($con2,"select * from purchase where compcode='001' and cpono in (select A.creference from receive_t A left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='001' and B.dreceived between '2018-05-01' and '2018-05-31' ORDER BY A.`ctranno` DESC ) Order By cpono desc");

	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		
		
			if (!mysqli_query($con, "INSERT INTO purchase(`compcode`, `cpono`, `ccode`, `cremarks`, `ddate`, `dneeded`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`) values('".$row["compcode"]."', '".$row["cpono"]."', '".$row["ccode"]."', '".$row["cremarks"]."', '".$row["ddate"]."', '".$row["dneeded"]."', '".$row["ngross"]."', '".$row["cpreparedby"]."', '".$row["lcancelled"]."', '".$row["lapproved"]."', '".$row["lprintposted"]."','".$row["ccustacctcode"]."')")) {
				
				echo $row["cpono"]."<br>";
				
				echo mysqli_error($con);
			} 
			else {
				
				$chkSalesT = mysqli_query($con2,"select * from purchase_t where compcode='001' and cpono = '".$row["cpono"]."'");
				while($rowT = mysqli_fetch_array($chkSalesT, MYSQLI_ASSOC)){
				
				$refcidenttran = $row["cpono"]."P".$rowT["nident"];
				
				
					if (!mysqli_query($con,"INSERT INTO purchase_t(`compcode`, `cidentity`, `cpono`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`, `ddateneeded`) values('".$rowT["compcode"]."', '".$refcidenttran."', '".$row["cpono"]."', '".$rowT["nident"]."', '".$rowT["citemno"]."', '".$rowT["nqty"]."', '".$rowT["cunit"]."', '".$rowT["nprice"]."', '".$rowT["namount"]."', '".$rowT["ncost"]."', '".$rowT["nfactor"]."', '".$rowT["cmainunit"]."', '".$rowT["cacctcode"]."','".$rowT["ddateneeded"]."') ")){
		//echo "False";
		
						echo "Errormessage: %s\n", mysqli_error($con);
					}
					else{
						//echo "True";
						echo $row["cpono"]."OK<br>";
					}
					
				}

				
				
			}

		
		
		
		
	}
	
?>