<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['txtctranno'];

	mysqli_query($con,"DELETE from paybill_acct where compcode='$company' and ctranno='$tran'");
	
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

	//Net Gross
	$varlnoapvref = 0;
	$resulthdr = mysqli_query ($con, "Select lnoapvref from paybill where compcode='$company' and ctranno = '$tran'"); 
	if(mysqli_num_rows($resulthdr)!=0){
		while($rowhdr = mysqli_fetch_array($resulthdr, MYSQLI_ASSOC)){
			$varlnoapvref = $rowhdr['lnoapvref'];
		}
	}

	if($varlnoapvref==0){

		$cnt=0;
		$sqlchk = mysqli_query($con,"Select A.*, C.dcheckdate, B.cacctdesc From paybill_t A left join paybill C on A.compcode=C.compcode and A.ctranno=C.ctranno left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran' Order By A.nident");
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cnt++;
			$xcident = $tran."P".$cnt;
			$xscdesc = $row['cacctdesc'];

			mysqli_query($con,"INSERT INTO `paybill_acct`(`compcode`, `cidentity`, `nidentity`,  `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Values('$company', '".$xcident."', '".$cnt."', '$tran', '".$row['cacctno']."', '".str_replace("'", "\\'",$xscdesc)."', '".$row['napplied']."', 0)");


		}

		$cnt++;
		$xcident = $tran."P".$cnt;
		mysqli_query($con,"INSERT INTO `paybill_acct`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`)
		Select '$company', '$xcident', '$cnt', A.ctranno, A.cacctno, B.cacctdesc, 0, A.npaid From paybill A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran' ");

	}else{

		$cnt=0;

		$sqlchk = mysqli_query($con,"Select A.*, C.dcheckdate, B.cacctdesc From paybill_t A left join paybill C on A.compcode=C.compcode and A.ctranno=C.ctranno left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran' Order By A.nident");
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cnt++;

			$xscdesc = $row['cacctdesc'];

			$xcident = $tran."P".$cnt;
			if($row['entrytyp']=="Debit"){
				if (!mysqli_query($con,"INSERT INTO `paybill_acct`(`compcode`, `cidentity`, `nidentity`,  `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Values('$company', '".$xcident."', '".$cnt."', '$tran', '".$row['cacctno']."', '".str_replace("'", "\\'",$xscdesc)."', '".$row['napplied']."', 0) ")){
					$witherr = 1;

				}
			}else{
				if (!mysqli_query($con,"INSERT INTO `paybill_acct`(`compcode`, `cidentity`, `nidentity`,  `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Values('$company', '".$xcident."', '".$cnt."', '$tran', '".$row['cacctno']."', '".str_replace("'", "\\'",$xscdesc)."', 0, '".$row['napplied']."') ")){
					$witherr = 1;
				}
			}

		}

		$cnt++;
		$xcident = $tran."P".$cnt;
		//Credit Account BASE
		if (!mysqli_query($con,"INSERT INTO `paybill_acct`(`compcode`, `cidentity`, `nidentity`,  `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Select '$company', '".$xcident."', '".$cnt."', '$tran', A.cacctno, B.cacctdesc, 0, A.npaid From paybill A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
			$witherr = 1;
		}

	}

?>

<form action="Paybill_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?=$tran;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>
