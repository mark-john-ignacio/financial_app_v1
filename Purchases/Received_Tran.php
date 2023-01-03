<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../Accounting/InsertToGL.php');
include('../Inventory/InsertToInv.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Receive_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Receive_cancel";
}

include('../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

		$dcutdate = mysqli_query($con,"Select * from receive where compcode='$company' and ctranno='$sino'");
		
		while($dcutdatez = mysqli_fetch_array($dcutdate, MYSQLI_ASSOC)){
			
			$dates = $dcutdatez['dcutdate'];
					
		}


if($_REQUEST['typ']=="POST"){
	
mysqli_query($con,"Update receive set lapproved=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','RECEIVING','$compname','Post Record')");

$status = "Posted";

WRREntry($tranno);
ToInv($tranno,"WRR","IN",$dates);

//Update items table cost, retailcost and stockonhands
		$UpdateItem = mysqli_query($con,"Select A.citemno, B.citemdesc, A.nqty as nqtyin, A.nfactor as nfactorin, A.ncost, A.nretail, B.nqty, B.npurchcost, B.nretailcost From receive_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno Where A.compcode='$company' and A.ctranno='$tranno'");
		
		while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
			
			$itmpartno = $itmupdate['citemno'];
			$nstock = ($itmupdate['nqtyin'] * $itmupdate['nfactorin']) + $itmupdate['nqty'];
			$updatecost = $itmupdate['ncost'];
			$updateretail = $itmupdate['nretail'];
			
			mysqli_query($con,"Update items set nqty=$nstock, npurchcost=$updatecost, nretailcost=$updateretail where compcode='$company' and cpartno='$itmpartno'");
					
		}



}

if($_REQUEST['typ']=="CANCEL"){
	
mysqli_query($con,"Update receive set lcancelled=1 where compcode='$company' and ctranno='$tranno'");

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','RECEIVING','$compname','Cancel Record')");

$status = "Cancelled";

}
?>

<script type="text/javascript">
	window.opener.document.getElementById("msg<?php echo $tranno;?>").innerHTML = "<?php echo $status; ?>";
	window.close();
</script>
