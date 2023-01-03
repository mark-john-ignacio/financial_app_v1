<?php
ini_set('max_execution_time', 1800); // 300 = 5min

if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');


$chkSales = mysqli_query($con2,"select * from adjustments where compcode='001' and dyear = '2018' and dmonth='05' Order By ctrancode desc");

	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		
		
			if (!mysqli_query($con, "INSERT INTO adjustments(`compcode`, `ctrancode`, `cremarks`, `dmonth`, `dyear`, `ddatetime`, `cpreparedby`, `cappcanby`, `dappcandate`,`lapproved`,`lcancelled`) values('".$row["compcode"]."', '".$row["ctrancode"]."', '".$row["cremarks"]."', '".$row["dmonth"]."', '".$row["dyear"]."', '".$row["ddatetime"]."', '".$row["cpreparedby"]."', '".$row["cappcanby"]."', '".$row["dappcandate"]."', '".$row["lapproved"]."', '".$row["lcancelled"]."')")) {
				echo $row["ctrancode"]."<br>";
				echo mysqli_error($con);
			} 
			else {
				
				$chkSalesT = mysqli_query($con2,"select a.* from adjustments_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='001' and a.ctrancode = '".$row["ctrancode"]."' Order By a.ctrancode, B.cclass, A.citemno desc");
				
				$cnt = 0;
				$ctrancode = "";
				while($rowT = mysqli_fetch_array($chkSalesT, MYSQLI_ASSOC)){
				
				if($ctrancode<>$row["ctrancode"]){
					$ctrancode=$row["ctrancode"];
					$cnt = 1;
					
				}else{
					$cnt = $cnt + 1;
				}
				
				$refcidenttran = $row["ctrancode"]."P".$cnt;
				
				
					if (!mysqli_query($con,"INSERT INTO adjustments_t(`compcode`, `cidentity`, `nidentity`, `ctrancode`, `citemno`, `cunit`, `nqty`, `nactual`, `nadj`) values('".$rowT["compcode"]."', '".$refcidenttran."', ".$cnt.",'".$row["ctrancode"]."', '".$rowT["citemno"]."', '".$rowT["cunit"]."', '".$rowT["nqty"]."', '".$rowT["nactual"]."', '".$rowT["nadj"]."') ")){
		//echo "False";
		
						echo "Errormessage: %s\n", mysqli_error($con);
					}
					else{
						//echo "True";
						echo $row["ctrancode"].$cnt." OK<br>";
					}
					
				}

				
				
			}

		
		
		
		
	}
	
?>