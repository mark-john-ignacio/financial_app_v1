<?php

			//$dte1 = date("m/d/Y");
			$dte1 = date_format(date_create("01/30/2018"), "m/d/Y");
			$effectiveDate = date('m/d/Y', strtotime("+3 months", strtotime($dte1)));
			
			//if($dedtyp=="Semi"){
			//	$effectiveDate = date('m/d/Y', strtotime("-15 days", strtotime($dte1)));
			//}
			//else{
				//$effectiveDate = date('m/d/Y', strtotime("-1 months", strtotime($dte1)));
			//}
			//$effectiveDate = date_format(date_create($effectiveDate), "m/d/Y");
			
			echo $dte1." : ".$effectiveDate;

?>

