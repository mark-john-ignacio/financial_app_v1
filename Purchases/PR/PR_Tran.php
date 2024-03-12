<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	if($_REQUEST['typ']=="POST"){
		$_SESSION['pageid'] = "PR_post";
	}

	if($_REQUEST['typ']=="CANCEL"){
		$_SESSION['pageid'] = "PR_cancel";
	}

	require_once "../../include/denied.php";
	require_once "../../include/access.php";
	require_once "../../include/sendEmail.php";

	//POST RECORD
	$tranno = $_REQUEST['x'];
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$status = "True";

	//email notif parameters
	$isemail = 0;
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='PR_APP_EMAIL'"); 
														
	if (mysqli_num_rows($result)!=0) {
		$xrsx = mysqli_fetch_array($result, MYSQLI_ASSOC);											
		$isemail = $xrsx['cvalue']; 												
	}

	if($_REQUEST['typ']=="POST"){

		//query lahat ng approvals order by nlevel -> isave sa array pra isang query lang
		$postapprovers = mysqli_query($con,"SELECT a.cprno,a.userid,a.nlevel,a.lapproved,a.lreject,b.Fname,b.cemailadd FROM `purchrequest_trans_approvals` a left join users b on a.userid=b.Userid where a.compcode='$company' and a.cprno='$tranno' order by a.nlevel");

		while($rowxcv=mysqli_fetch_array($postapprovers, MYSQLI_ASSOC)){
			$rowPOresult[] = $rowxcv;
		}
		
		//pag may isa na reject.... stop approving na
		$Goreject = 0;
		$Gorejectname = "";

		foreach($rowPOresult as $rs){
			if(intval($rs['lreject'])==1){
				$Goreject = 1;
				$Gorejectname = $rs['Fname'];
				break 1;
			}
		}

		if($Goreject==1){

			$msgz = "<b>ERROR: </b>This transaction has been rejected by: " .$Gorejectname. "<br>Please click TRACK to view status!";
			$status = "False";

		}else{

			//	print_r($rowPOresult);
			//	echo "<br><br>";

			$cntfinalapp = 0; //loop to check kung last approver na ung mag aapprove
			$cntfinalall = 0;
			foreach($rowPOresult as $rs){
			//	if(intval($rs['lapproved'])==0  && intval($rs['lreject'])==0){
					$cntfinalall++;
			//	}

				if(intval($rs['lapproved'])==1  && intval($rs['lreject'])==0){
					$cntfinalapp++;
				}
			}

			//loop sa array kunin ung lowest level na nde pa approved..
			$xcdlowest = 1;
			foreach($rowPOresult as $rs){
				if(intval($rs['lapproved'])==0  && intval($rs['lreject'])==0){
					$xcdlowest = $rs['nlevel'];
					break 1;
				}
			}

			//	print_r($xcdlowest);
			//echo "<br><br>";

			//loop ulit check kung cnu cnu ung mga nsa level na un., isave sa array -> ung nde na nag approve or cancel
			$counter = 0;
			foreach($rowPOresult as $rs){
				if($rs['nlevel']==$xcdlowest && (intval($rs['lapproved'])==0 && intval($rs['lreject'])==0)){
					$counter++;
					@$arrapprovers[] = $rs['userid'];
				}
			}

				//echo $counter;

				//print_r(@$arrapprovers);
				//echo "<br><br>";
				//echo $preparedby;
				//echo "<br><br>";

				//echo intval($cntfinalall)." - ".intval($cntfinalapp);
			//check if ung nakalogin ay isa sa mga mag aapprove.
			if(in_array(trim($preparedby),@$arrapprovers)){

				if (!mysqli_query($con,"Update purchrequest_trans_approvals set lapproved=1,ddatetimeapp='".date('Y-m-d H:i:s')."' where compcode='$company' and cprno='$tranno' and userid='$preparedby'")){
					$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
					$status = "False";	
				}else{

					$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
					$status = "Posted";

					mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$tranno','$preparedby',NOW(),'POSTED','PURCHASE REQUEST','$compname','Post Record')");

					if((intval($cntfinalall) - intval($cntfinalapp)) == 1){ //pag 1 meaning last approver na sya.. set to posted na ang transaction

						mysqli_query($con,"Update purchrequest set lapproved=1 where compcode='$company' and ctranno='$tranno'");

					}else{ //pag nde pa send to next approver

						//Check if sending email is set to 1
						if($isemail==1){ //send emails to next 

							//pag 1 na counter meaning kaw nlng nde approved pwde na send sa next level
							@$nextapprovers[] = "";
							if($counter==1){
								$xcdnext = intval($xcdlowest)+1;
								foreach($rowPOresult as $rs){
									if($rs['nlevel']==$xcdnext && intval($rs['lapproved'])==0){
										$counter++;
										@$nextapprovers[] = $rs['userid'];
									}
								}
							}

							//loop sa next approvers
							if(count(@$nextapprovers)>=1){
								foreach($rowPOresult as $rs){
									if(in_array(trim($rs['userid']),@$nextapprovers)){


										$output='<p>Dear '.$rs['Fname'].',</p>';
										$output.='<p>This email is to notify that the PR# '.$tranno.' is waiting for your approval.</p>'; 
										$output.='<p>Thanks,</p>';
										$output.='<p>Myx Financials,</p>';

										$subject = $logonamz." - Purchase Request";
										$getcred = getEmailCred();

										sendEmail($rs['cemailadd'],$output,$subject,$logonamz,$getcred);

									}
								}
							}

						}

				}


				}

			}else{
				$msgz = "<b>ERROR: </b>You are not one of the next approver(s)<br>Please click TRACK to view approval status!";
				$status = "False";
			}
		
		}

	}

	if($_REQUEST['typ']=="CANCEL"){
		
		//query lahat ng approvals order by nlevel -> isave sa array pra isang query lang
		$postapprovers = mysqli_query($con,"SELECT a.cprno,a.userid,a.nlevel,a.lapproved,a.lreject,b.Fname,b.cemailadd FROM `purchrequest_trans_approvals` a left join users b on a.userid=b.Userid where a.compcode='$company' and a.cprno='$tranno' order by a.nlevel");

		while($rowxcv=mysqli_fetch_array($postapprovers, MYSQLI_ASSOC)){
			$rowPOresult[] = $rowxcv;
		}
		
		//pag may isa na reject.... stop rejecting na
		$Goreject = 0;
		$Gorejectname = "";
		foreach($rowPOresult as $rs){
			if(intval($rs['lreject'])==1){
				$Goreject = 1;
				$Gorejectname = $rs['Fname'];
				break 1;
			}
		}

		if($Goreject==1){

			$msgz = "<b>ERROR: </b>This transaction is already been rejected by: " .$Gorejectname. "<br>Please click TRACK to view status!";
			$status = "False";

		}else{

			//	print_r($rowPOresult);
			//	echo "<br><br>";

			//loop sa array kunin ung lowest level na nde pa approved..
			$xcdlowest = 1;
			foreach($rowPOresult as $rs){
				if(intval($rs['lapproved'])==0 && intval($rs['lreject'])==0){
					$xcdlowest = $rs['nlevel'];
					break 1;
				}
			}

			//	print_r($xcdlowest);
			//	echo "<br><br>";

			//loop ulit check kung cnu cnu ung mga nsa level na un., isave sa array -> ung nde na nag approve or cancel
			$counter = 0;
			foreach($rowPOresult as $rs){
				if($rs['nlevel']==$xcdlowest && (intval($rs['lapproved'])==0 && intval($rs['lreject'])==0)){
					$counter++;
					@$arrapprovers[] = $rs['userid'];
				}
			}

			//check if ung nakalogin ay isa sa mga mag aapprove.
			if(in_array(trim($preparedby),@$arrapprovers)){

				if (!mysqli_query($con,"Update purchrequest_trans_approvals set lreject=1,ddatetimereject='".date('Y-m-d H:i:s')."' where compcode='$company' and cprno='$tranno' and userid='$preparedby'")){
					$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
					$status = "False";	
				}else{

					mysqli_query($con,"Update purchrequest set lcancelled=1 where compcode='$company' and ctranno='$tranno'");

					$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
					$status = "Cancelled";

					mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$tranno','$preparedby',NOW(),'CANCELLED','PURCHASE REQUEST','$compname','Cancel Record')");

				}

			}else{
				$msgz = "<b>ERROR: </b>You are not one of the next approver(s)<br>Please click TRACK to view approval status!";
				$status = "False";
			}
		
		}

	}

	if($_REQUEST['typ']=="CANCEL1"){

		mysqli_query($con,"Update purchrequest set lcancelled=1 where compcode='$company' and ctranno='$tranno'");

		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";

		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$tranno','$preparedby',NOW(),'CANCELLED','PURCHASE REQUEST','$compname','Cancel Record')");

	}

	if($_REQUEST['typ']=="SEND"){

		//to be sure
		mysqli_query($con,"Update purchrequest_trans_approvals set compcode='".date("Ymd_His")."' where compcode='$company' and cprno='$tranno'");								

		$sqlhead = mysqli_query($con,"select a.locations_id from purchrequest a where a.compcode='$company' and a.ctranno = '$tranno'");

		$SecsList = "";
		if (mysqli_num_rows($sqlhead)!=0) {
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
				$SecsList = $row['locations_id'];
			}
		}

		//to be sure
		mysqli_query($con,"Update purchrequest_trans_approvals set compcode='".date("Ymd_His")."' where compcode='$company' and cprno='$tranno'");								

		$sqlhead = mysqli_query($con,"select a.locations_id from purchrequest a where a.compcode='$company' and a.ctranno = '$tranno'");

		$SecsList = "";
		if (mysqli_num_rows($sqlhead)!=0) {
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
				$SecsList = $row['locations_id'];
			}
		}

		//get approvers
		$resPOApps = mysqli_query($con,"SELECT * FROM `purchrequest_approvals_id` WHERE compcode='".$_SESSION['companyid']."'");

		if (mysqli_num_rows($resPOApps)!=0) {

			while($row = mysqli_fetch_array($resPOApps, MYSQLI_ASSOC)){

				//pag ALL criteria insert na for approval
				if(in_array("ALL", explode(",",$row['locations_id']))){

					$sql = "INSERT INTO purchrequest_trans_approvals (`compcode`,`cprno`,`nlevel`,`userid`) values ('$company','$tranno','".$row['pr_approval_id']."','".$row['userid']."')";

					if ($con->query($sql) !== TRUE) {
						$msgz = "<b>ERROR: </b>There's a problem sending your transaction!";
						$status = "False";
					}

				}else{

					$xsent="False";
						if(in_array($SecsList, explode(",",$row['locations_id']))){

							$sql = "INSERT INTO purchrequest_trans_approvals (`compcode`,`cprno`,`nlevel`,`userid`) values ('$company','$tranno','".$row['pr_approval_id']."','".$row['userid']."')";

							if ($con->query($sql) !== TRUE) {
								$msgz = "<b>ERROR: </b>There's a problem sending your transaction!";
								$status = "False";
							}else{
								$xsent="True";
							}

						}
				}

			}


		}

		if($status !== "False"){

			if (!mysqli_query($con,"Update purchrequest set lsent=1, ddatetimesent='".date('Y-m-d H:i:s')."', csentby='".$preparedby."' where compcode='$company' and ctranno='$tranno'")){
				$msgz = "<b>ERROR: </b>There's a problem sending your transaction!";
				$status = "False";
			}else{

				$msgz = "<b>SUCCESS: </b>Your transaction is successfully sent!";
				$status = "SENT";

				mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
				values('$tranno','$preparedby',NOW(),'SEND','PURCHASE REQUEST','$compname','Cancel Record')");

				if($isemail==1){ //send emails to level 1

					$resemailapps = mysqli_query($con,"SELECT a.cprno,b.Fname,b.cemailadd FROM `purcrequest_trans_approvals` a left join users b on a.userid=b.Userid where a.compcode='$company' and a.cprno='$tranno' and a.nlevel = (Select MIN(nlevel) from `purcrequest_trans_approvals` where compcode='$company' and cprno='$tranno')");

					if (mysqli_num_rows($resemailapps)!=0) {
						while($row = mysqli_fetch_array($resemailapps, MYSQLI_ASSOC)){

							sendEmail($row['cemailadd'],$row['Fname'],$tranno);

						}
					}

				}

			}

		}

	}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		
	echo json_encode($json2);


?>
