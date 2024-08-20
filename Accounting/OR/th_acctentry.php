<?php
	if(!isset($_SESSION)){
	session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['txtctranno'];

	mysqli_query($con,"DELETE from receipt_entry where compcode='$company' and ctranno='$tran'");

	$result = mysqli_query($con,"SELECT * FROM `receipt` WHERE compcode='$company' and ctranno='$tran'");
	if (mysqli_num_rows($result)!=0) {
		$all = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
		$varlnosiref= $all['lnosiref']; 							
	}

	if($varlnosiref==0){

		$z = 1;
		$refcidenttran = $tran."P".$z;

		//OR -> Deposit account -> Debit
		if (!mysqli_query($con,"INSERT INTO `receipt_entry`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Select '$company', '$refcidenttran', $z, '$tran', A.cacctcode, B.cacctdesc, A.namount, 0 From receipt A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
		}
		else{

			//OR -> Customer account -> Credit
			if (!mysqli_query($con,"INSERT INTO `receipt_entry`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Select '$company', CONCAT(A.ctranno,'P',A.nidentity+1), A.nidentity+1, '$tran', A.cacctno, B.cacctdesc, 0, sum(A.napplied) as namount From receipt_sales_t A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno where A.compcode='$company' and A.ctranno='$tran'  Group by C.dcutdate, A.cacctno, B.cacctdesc ")){
				echo "False";
			}
			else{
				echo "True";
			}
			
		}

	}else{
		$z = 1;
		$refcidenttran = $tran."P".$z;

		//BASE DEBIT
		if (!mysqli_query($con,"INSERT INTO `receipt_entry`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Select '$company', '$refcidenttran', $z, '$tran', A.cacctcode, B.cacctdesc, A.namount, 0 From receipt A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
		}
		else{

			$sqlchk = mysqli_query($con,"Select A.*, C.dcutdate, B.cacctdesc From receipt_others_t A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno where A.compcode='$company' and A.ctranno='$tran' Order By A.nidentity");
			while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){

				$z = $z+ 1;
				$refcidenttran = $tran."P".$z;


				if (!mysqli_query($con,"INSERT INTO `receipt_entry`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`) Values('$company', '$refcidenttran', $z, '$tran', '".$row['cacctno']."', '".$row['cacctdesc']."', '".$row['ndebit']."', '".$row['ncredit']."') ")){
					$witherr = 1;
				}


			}

		}


	}
		
?>
<form action="OR_edit2.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?=$tran;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>