<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['tran'];
	$typ = $_REQUEST['type'];


	$arruomlist = array();
	$sqluom = mysqli_query($con,"Select * from items_factor where compcode='$company'");
	if (mysqli_num_rows($sqluom)!=0) {
		while($row = mysqli_fetch_array($sqluom, MYSQLI_ASSOC)){
			$arruomlist[]  = array('cpartno' => $row['cpartno'],'cunit' => $row['cunit'],'nfactor' => $row['nfactor'],'crule' => $row['crule']);
		}
	}


function getcostfromin($getcitmno, $getnqty){
	global $company;
	global $con;

	$sql = "Select A.ctranno, A.ncost, A.ntotqty, IFNULL(B.ntotout,0) as ntotout from tblinvin A left join (select crefIn, sum(ntotqty) as ntotout from tblinvout where compcode='$company' and citemno='$getcitmno' Group by crefIn) B on A.ctranno=B.crefIn where A.compcode='$company' and A.citemno='$getcitmno' and A.ntotqty - IFNULL(B.ntotout,0) >= 1 order by A.dexpired";
	
	//echo $sql;
	
	$sqlcostinvin = mysqli_query($con,$sql);
	$array = array();
	
	if (mysqli_num_rows($sqlcostinvin)!=0) {
		$rowcostinvin = mysqli_fetch_assoc($sqlcostinvin);
		
		$qtyIN = $rowcostinvin["ntotqty"];
		$qtyOUT = $rowcostinvin["ntotout"];
		
		$qtyleft = (float)$qtyIN - (float)$qtyOUT;
		
		$array["id"] = $rowcostinvin["ctranno"];
		$array["cost"] = $rowcostinvin["ncost"];
		$array["qty"] = $qtyleft;
	}
	return $array;
}

	
	//Delete muna existing if meron pra iwas double;
	mysqli_query($con,"DELETE FROM `tblinventory` where `ctranno` = '$tran'");
	mysqli_query($con,"DELETE FROM `tblinvin` where `ctranno` = '$tran'");
	mysqli_query($con,"DELETE FROM `tblinvout` where `ctranno` = '$tran'");
	 

	if($typ=="INVTRANS"){

		$witherr = 0;
		$dsql = mysqli_query($con,"Select A.*, B.csection1, B.csection2, B.dcutdate, C.cunit as cmainunit, B.ctrantype From invtransfer_t A left join invtransfer b on A.compcode=B.compcode and A.ctranno=B.ctranno left join items C on A.compcode=C.compcode and A.citemno=C.cpartno where A.compcode='$company' and A.ctranno='$tran'");
		while($rowinv = mysqli_fetch_array($dsql, MYSQLI_ASSOC)){

				$nqtytotal = $rowinv['nqty2'];
				$nfactor = 1;
				foreach($arruomlist as $xrowx){
					if($rowinv['cunit']==$xrowx['cunit'] && $rowinv['citemno']==$xrowx['cpartno']){
						$nfactor = $xrowx['nfactor'];

						if($xrowx['crule']=="div"){
							$nqtytotal = floatval($rowinv['nqty2']) / floatval($xrowx['nfactor']);
							break;
						}elseif($xrowx['crule']=="mul"){
							$nqtytotal = floatval($rowinv['nqty2']) * floatval($xrowx['nfactor']);
							break;
						}
					}
				}

				if($rowinv['ctrantype'] == "request"){
					$csecin = $rowinv['csection1'];
					$csecout = $rowinv['csection2'];
				}elseif($rowinv['ctrantype'] == "transfer"){
					$csecin = $rowinv['csection2'];
					$csecout = $rowinv['csection1'];
				}elseif($rowinv['ctrantype'] == "fg_transfer"){
					$csecin = $rowinv['csection2'];
					$csecout = $rowinv['csection1'];
				}	


				$amtcost = 0;
				$amtretial = 0;

			//section IN
			if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`,  `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) VALUES ('$company','".$rowinv['ctranno']."', NOW(), '".$rowinv['dcutdate']."', '$typ', '".$rowinv['citemno']."', '".$rowinv['cunit']."', ".$rowinv['nqty2'].", '".$rowinv['cmainunit']."', ".$nfactor.", ".$csecin.", ".$nqtytotal.", ".$amtcost.", ".$amtretial.", 0, 0, 0 )")){
				$witherr = 1;
			}

			//section OUT
			if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`,  `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) VALUES ('$company','".$rowinv['ctranno']."', NOW(), '".$rowinv['dcutdate']."', '$typ', '".$rowinv['citemno']."', '".$rowinv['cunit']."', ".$rowinv['nqty2'].", '".$rowinv['cmainunit']."', ".$nfactor.", ".$csecout.", 0, 0, 0 , ".$nqtytotal.", ".$amtcost.", ".$amtretial.")")){
				$witherr = 1;
			}
		}

		if ($witherr==1){
			echo "False";
		}else{
			echo "True";
		}
		
	}

	if($typ=="INVCNT"){

		$witherr = 0;

		$amtcost = 0;
		$amtretial = 0;

		//section IN
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`,  `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', A.ctranno, NOW(), B.dcutdate, '$typ', A.citemno, A.cunit, A.nqty, C.cunit, 1, B.section_nid, A.nqty, ".$amtcost.", ".$amtretial.",0,0,0 From invcount_t A left join invcount B on A.compcode=B.compcode and A.ctranno=B.ctranno left join items C on A.compcode=C.compcode and A.citemno=C.cpartno where A.compcode='$company' and A.ctranno='$tran'")){
			$witherr = 1;
		}

		if ($witherr==1){
			echo "False";
		}else{
			echo "True";
		}
	}

	if($typ=="RR"){
		$witherr = 0;

		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_WHIN' and compcode='$company'"); 				
		$csecin = 0;
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$csecin = $all_course_data['cvalue']; 							
		}

		$dsql = mysqli_query($con,"Select A.*,B.dreceived From receive_t A left join receive b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'");
		while($rowinv = mysqli_fetch_array($dsql, MYSQLI_ASSOC)){

			$nqtytotal = $rowinv['nqty'];
				$nfactor = 1;
				foreach($arruomlist as $xrowx){
					if($rowinv['cunit']==$xrowx['cunit'] && $rowinv['citemno']==$xrowx['cpartno']){
						$nfactor = $rowinv['nfactor'];

						if($xrowx['crule']=="div"){ //mas maliit ung UOM sa main UOM
							$nqtytotal = floatval($rowinv['nqty']) / floatval($nfactor);
							break;
						}elseif($xrowx['crule']=="mul"){ //mas malaki ung UOM sa main UOM
							$nqtytotal = floatval($rowinv['nqty']) * floatval($nfactor);
							break;
						}
					}
				}

				$amtcost = 0;
				$amtretial = 0;

			if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`,  `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) VALUES ('$company','".$rowinv['ctranno']."', NOW(), '".$rowinv['dreceived']."', '$typ', '".$rowinv['citemno']."', '".$rowinv['cunit']."', ".$rowinv['nqty'].", '".$rowinv['cmainunit']."', ".$nfactor.", ".$csecin.", ".$nqtytotal.", ".$amtcost.", ".$amtretial.", 0, 0, 0 )")){
				$witherr = 1;
			}

		}


		if ($witherr == 1){
			echo "False";
		}
		else{
			echo "True";			
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `cserial`,`cbarcode`,`nlocation`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dexpired`) Select '$company', '$tran', A.citemno, A.cunit, A.cserial, A.cbarcode, A.nlocation, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, B.ncost, NOW(), A.dexpired From receive_t_serials A left join receive_t B on A.compcode=B.compcode and A.ctranno=B.ctranno and A.citemno=B.citemno and  A.nrefidentity=B.nident where A.ctranno='$tran'");	
		}
	} 

	if($typ=="PRet"){

		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_PROUT' and compcode='$company'"); 
				
		$csecout = 0;
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$csecout = $all_course_data['cvalue']; 							
		}

		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),B.dreturned,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, ".$csecout.", 0, 0, 0, A.nqty*A.nfactor, A.ncost, 0 From purchreturn_t A left join purchreturn b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `cserial`, `cbarcode`, `nlocation`, `ddate`, `dcutdate`,`crefin`) Select '$company', '$tran', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, B.ncost, A.cserial, A.cbarcode, A.nlocation, NOW(), A.dexpired, B.creference From purchreturn_t_serials A left join purchreturn_t B on A.compcode=B.compcode and A.ctranno=B.ctranno and A.citemno=B.citemno and  A.nrefidentity=B.nident where A.ctranno='$tran'");	
		}
	}
	
	if($typ=="DR" || $typ=="DRNT"){

		if($typ=="DR"){
			$tbl = "dr";
			$tblt = "dr_t";
		}else{	
			$tbl = "ntdr";
			$tblt = "ntdr_t";
		}

		$witherr = 0;
		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_WHOUT' and compcode='$company'"); 
				
		$csecout = 0;
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$csecout = $all_course_data['cvalue']; 							
		}


		$dcutdate = "";

		$dsql = mysqli_query($con,"Select A.*,B.dcutdate From dr_t A 
		left join dr b on A.compcode=B.compcode and A.ctranno=B.ctranno 
		where A.compcode='$company' and A.ctranno='$tran'");
		while($rowinv = mysqli_fetch_array($dsql, MYSQLI_ASSOC)){

			$nqtytotal = $rowinv['nqty'];
				$nfactor = 1;
				foreach($arruomlist as $xrowx){
					if($rowinv['cunit']==$xrowx['cunit'] && $rowinv['citemno']==$xrowx['cpartno']){
						$nfactor = $rowinv['nfactor'];

						if($xrowx['crule']=="div"){ //mas maliit ung UOM sa main UOM
							$nqtytotal = floatval($rowinv['nqty']) / floatval($nfactor);
							break;
						}elseif($xrowx['crule']=="mul"){ //mas malaki ung UOM sa main UOM
							$nqtytotal = floatval($rowinv['nqty']) * floatval($nfactor);
							break;
						}
					}
				}

			$amtcost = 0;
			$dcutdate = $rowinv['dcutdate'];

			if (!mysqli_query($con,"INSERT INTO `tblinventory`
			(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, 
			`nfactor`, `nsection_id`,  `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) 
			VALUES ('$company','".$rowinv['ctranno']."', NOW(), '".$rowinv['dcutdate']."', '$typ', '".$rowinv['citemno'].
			"', '".$rowinv['cunit']."', ".$rowinv['nqty'].", '".$rowinv['cmainunit']."', ".$nfactor.", ".$csecout."
			, 0, 0, 0, ".$nqtytotal.", ".$amtcost.", ".$rowinv['nprice'].")")){
				$witherr = 1;
			} 

		}


		if ($witherr == 1){
			echo "False";
		}
		else{
			echo "True";			

			//get cost and ref in
				
			$sqldrdet = "Select A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor as totqty From ".$tblt." A where A.compcode='$company' and A.ctranno='$tran'";
			
			$resdrdet= mysqli_query ($con, $sqldrdet); 
		
			while($row = mysqli_fetch_array($resdrdet, MYSQLI_ASSOC)){
				
				 $drtitmno = $row['citemno'];
				 //$drdsc  = $row['citemdesc'];
				 $drqty = $row['nqty'];
				 $drtotqty = $row['totqty'];
				 $drunit = $row['cunit'];
				// $drprice = $row['nprice'];
				// $dramt = $row['namount'];
				 $drmainuom = $row['cmainunit'];
				 $drfactor = $row['nfactor'];
				 
				 $citemgetqty = 0;
				 $totqty = 0;
				 $totqtyremain = $drtotqty;
				 $qtyinser = 0;
				 $citemgetid= "";
				
				 do {
					 $citemget = getcostfromin($drtitmno, $drtotqty);
					 
					 if(count($citemget) > 0){
						$citemgetid = $citemget["id"];
						$citemgetcost = $citemget["cost"];
						$citemgetqty = $citemget["qty"];
					 }else{
						$citemgetid = $drtitmno;
						$citemgetcost = 0;
						$citemgetqty = $drtotqty;
					 }
					 
					 
					 if($citemgetqty > $totqtyremain){
						//echo "SA A: ".$citemgetqty. " > " . $totqtyremain."<br>";
						$qtyinsert = $totqtyremain;
					 }
					 else{
					//	echo "SA B: ".$citemgetqty. " > " . $totqtyremain."<br>";
						$qtyinsert = $citemgetqty;
						$totqtyremain = $totqtyremain - $citemgetqty;
	
					 }
					 
					 $totqty = $totqty + $qtyinsert;				 
	
					 
					// echo "Tran No.: ".$citemgetid."<br>";
					// echo "Cost: ".$citemgetcost."<br>";
					// echo "Qty.: ".$qtyinsert."<br>";
					 
					// echo "TOTQty.: ".$citemgetqty."<br>";
					// echo "QtyRem.: ".$totqtyremain."<br>";
					
					 mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `cserial`, `cbarcode`, `nlocation`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`,`crefin`) values ('$company','$tran','$drtitmno','$drunit','$drqty','$drmainuom','','','','$drfactor','$qtyinsert','$citemgetcost', NOW(),'$dcutdate','$citemgetid') "); 
					 
				 }while($totqty < $drtotqty);
				 
			}
	
		}
		
	}

	if($typ=="SRet"){

		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_SRIN' and compcode='$company'"); 
				
		$csecin = 0;
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$csecin = $all_course_data['cvalue']; 							
		}

		$amtcost = 0;
		$amtretial = 0;

		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),B.dreceived,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, ".$csecin.", A.nqty*A.nfactor,".$amtcost.", 0, 0, 0, 0 From salesreturn_t A left join salesreturn b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `cserial`, `cbarcode`, `nlocation`, `ddate`, `dcutdate`) Select '$company', '$tran', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, ".$amtcost.", null, null, null, NOW(), null From salesreturn_t A where A.ctranno='$tran'");	
		}
	}

	if($typ=="NTSRet"){

		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_SRIN' and compcode='$company'"); 
				
		$csecin = 0;
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$csecin = $all_course_data['cvalue']; 							
		}

		$amtcost = 0;
		$amtretial = 0;

		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),B.dreceived,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, ".$csecin.", A.nqty*A.nfactor,".$amtcost.", 0, 0, 0, 0 From ntsalesreturn_t A left join ntsalesreturn b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `cserial`, `cbarcode`, `nlocation`, `ddate`, `dcutdate`) Select '$company', '$tran', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, ".$amtcost.", null, null, null, NOW(), null From salesreturn_t A where A.ctranno='$tran'");	
		}
	}


	if($typ == "POS"){
		$tbl = "pos";
		$tbl2 = "pos_t";

		$wither = 0;
		$section = 0;

		$employeeid = $_SESSION['employeeid'];
		
		function getSection($con, $company, $employeeid) {
			$sql = "SELECT cdepartment FROM users WHERE Userid='$employeeid'";
			$query = mysqli_query($con, $sql);
			if (mysqli_num_rows($query) != 0) {
				$data = $query->fetch_assoc();
				$cdepartment = $data['cdepartment'];
		
				$sql = "SELECT nid FROM locations WHERE ccode='$cdepartment'";
				$query = mysqli_query($con, $sql);
				if (mysqli_num_rows($query) != 0) {
					$data = $query->fetch_assoc();
					return $data['nid'];
				}
			}
			return getDefaultSection($con, $company);
		}
		
		function getDefaultSection($con, $company) {
			$sql = "SELECT * FROM `parameters` WHERE ccode='DEF_WHOUT' AND compcode='$company'";
			$query = mysqli_query($con, $sql);
			if (mysqli_num_rows($query) != 0) {
				$data = $query->fetch_assoc();
				return $data['cvalue'];
			}
			return null;
		}
		
		$section = getSection($con, $company, $_SESSION['employeeid']);


		$sql = "SELECT a.*, b.ddate FROM pos_t a
		left join pos b on a.compcode = b.compcode AND a.tranno = b.tranno
		WHERE a.compcode = '$company' AND a.tranno = '$tran'";
		$query = mysqli_query($con, $sql);

		while($row = $query -> fetch_assoc()){
			$totalQty = $row['quantity'];
			$factor = 1; 
			foreach($arruomlist as $list){
				$factor = $list['nfactor'];
				if($list['cunit']== $row['uom'] && $list['cpartno'] == $row['item'] ) {
					match($list['crule']){
						"div" => $totalQty = floatval($row['quantity']) / floatval($actor),
						"mul" => $totalQty = floatval($row['quanity']) * floatval($actor),
					};
					break;
				}
			}
			$cost = 0;
			$date = $row['ddate'];
			$item = $row['item'];
			$unit = $row['uom'];
			$quantity = $row['quantity'];
			$price = $row['amount'];

			$sql = "INSERT INTO `tblinventory` (`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nsection_id`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) VALUES ('$company', '$tran', NOW(), '$date', '$typ', '$item', '$unit', '$quantity', '$unit', '$factor', '$section', 0, 0, 0, '$totalQty', '$cost', '$price')";
			mysqli_query($con, $sql);	
		}

		if($wither == 1){
			echo "False";
		} else {
			echo "True";


			//Issue no Item found for inventory transfer

			$sql = "SELECT a.*, b.nfactor, a.quantity * b.nfactor as totalQty FROM pos_t a
			LEFT JOIN items_factor b on a.compcode = b.compcode AND a.item = b.cpartno	
			WHERE a.compcode = '$company' AND a.tranno = '$tran'";
			$query = mysqli_query($con, $sql);

			while($row = $query -> fetch_assoc()){
				$itemno = $row['item'];
				$quantity = $row['quantity'];
				$totalQty = $row['totalQty'];
				$unit = $row['uom'];
				$mainunit = $row['uom'];
				$factor = $row['nfactor'];

				$getItemQty = 0;
				$remainQty = $totalQty;
				$quantityTotal = 0;
				$insertQty = 0;
				$itemId = "";
				$getitem = getcostfromin($itemno, $totalQty);

				do{
					if(count($getitem) > 0){
						$itemId = $getItem['id'];
						$itemCost = $getItem['cost'];
						$getItemQty = $getItem['qty'];
					} else {
						$itemId = $itemno;
						$itemCost = 0;
						$getItemQty = $totalQty;
					}

					if($getItemQty > $remainQty){
						$insertQty = $remainQty;
					} else {
						$insertQty = $getItemQty;
						$remainQty = $remainQty - $getItemQty;
					}
					$quantityTotal = $quantityTotal + $insertQty;

					$sql = "INSERT INTO `tblinvout` (`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `cserial`, `cbarcode`, `nlocation`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`, `crefin`) VALUES ('$company', '$tran', '$itemno', '$unit', '$quantity', '$unit', '', '','', '$factor', '$insertQty', '$itemCost', NOW(), '$date', '$itemId')";
					mysqli_query($con, $sql);
				} while($quantityTotal < $totalQty);
			}
		}
	}

?>
