<?php
ini_set('max_execution_time', 1800); // 300 = 5min

if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


$chkSales = mysqli_query($con2,"select * from sales where compcode='001' and dcutdate between '2018-06-16 00:00:00' and '2018-06-30 23:59:59' Order By csalesno desc");

	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		
		
			if (!mysqli_query($con, "INSERT INTO sales(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `cacctcode`, `cvatcode`,`lapproved`,`lcancelled`,`lprintposted`) values('".$row["compcode"]."', '".$row["csalesno"]."', '".$row["ccode"]."', '".$row["cremarks"]."', '".$row["ddate"]."', '".$row["dcutdate"]."', '".$row["ngross"]."', '".$row["cpreparedby"]."', '".$row["ccustacctcode"]."', 'NV', '".$row["lapproved"]."', '".$row["lcancelled"]."', '".$row["lprintposted"]."')")) {
				echo $row["csalesno"]."<br>";
				echo mysqli_error($con);
			} 
			else {
				
				$chkSalesT = mysqli_query($con2,"select * from sales_t where compcode='001' and csalesno = '".$row["csalesno"]."' Order By csalesno desc");
				while($rowT = mysqli_fetch_array($chkSalesT, MYSQLI_ASSOC)){
				
				$refcidenttran = $row["csalesno"]."P".$rowT["nident"];
				
				
					if (!mysqli_query($con,"INSERT INTO sales_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `cmainunit`,`nfactor`,`cacctcode`,`ctaxcode`) values('".$rowT["compcode"]."', '".$refcidenttran."', '".$row["csalesno"]."', NULL, '".$rowT["nident"]."', '".$rowT["citemno"]."', '".$rowT["nqty"]."', '".$rowT["cunit"]."', '".$rowT["nprice"]."', '".$rowT["namount"]."', '".$rowT["cmainunit"]."', '".$rowT["nfactor"]."','40310','NT') ")){
		//echo "False";
		
						echo "Errormessage: %s\n", mysqli_error($con);
					}
					else{
						//echo "True";
						echo $row["csalesno"]."OK<br>";
					}
					
				}

				
				
			}

		
		
		
		
	}
	
?>