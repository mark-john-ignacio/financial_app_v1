<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$companyid = $rowcomp['compcode'];
			$companyname = $rowcomp['compname'];
			$companydesc = $rowcomp['compdesc'];
			$companyadd = $rowcomp['compadd'];
			$logosrc = $rowcomp['clogoname'];
		}

	}
	
	$corno = $_REQUEST['tranno'];
	$sqlchk = mysqli_query($con,"Select DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, a.namount, a.napplied, a.cremarks,c.Fname,c.Lname,c.Minit, b.cname From receipt a left join users c on a.cpreparedby=c.Userid left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.ctranno='$corno'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){

			$CustName = $row['cname'];
			
			$dDate = $row['dcutdate'];
			$nAmount = $row['namount'];
			$nApplied = $row['napplied'];
			
			$cRemarks = $row['cremarks'];

			$PreparedBy = $row['Lname'].", ".$row['Fname']." ".$row['Minit'];

		}
	}
?>

<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	html,
	body {
		margin:0;
		padding:0;
		height:100%;
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	#container {
		min-height:100%;
		position:relative;
	}
	#header {
		padding:10px;
	}
	#body {
		padding:10px;
		padding-bottom:60px;	/* Height of the footer */
	}
	#footer {
		position:absolute;
		bottom:5px;
		width:100%;
	}
	/* other non-essential CSS */
	#header p,
	#header h1 {
		margin:0;
		padding:10px 0 0 10px;
	}
	#footer p {
		margin:0;
		padding:10px;
	}
	
	@page { size:8.5in 11in; margin: 0.5cm }
</style>

</head>

<body onLoad="window.print();">
<div id="container">
<div id="header">
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
	<tr>
		<td align="center"> 

			<table border="0" width="100%">
					<tr align="center">
						<td><img src="<?php echo "../".$logosrc; ?>" height="68px"></td>  
					</tr>
					<tr align="center">
						<td><font style="font-size: 18px;"><?php echo $companyname; ?></font></td>
					</tr>
					<tr align="center">
						<td style="padding-bottom: 20px"><font><?php echo $companyadd; ?></font></td>
					</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td align="center"> 
			<table border="0" width="100%" style="border-collapse:collapse">
				<tr>
					<td align="center" colspan="2">
						<font style="font-size: 24px;">RECEIPT VOUCHER </font>
					</td>
					<tr>
					<td style="padding-bottom: 20px; text-align: right">
						<b>Receipt #: </b><?=$corno;?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center"> 
			<table border="0" width="100%" style="border-collapse:collapse">
				<tr>
					<td colspan="2" width="50%">
						<b>Payor: </b> <?=$CustName?>
					</td>
					<td colspan="2" align="right">
						<b>Date: </b> <?=$dDate?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table width="100%" border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
	<tr>
		<th>Particulars</th>
		<th width="150px">Debit</th>
		<th width="150px">Credit</th>
		<th width="150px">Total Amount</th>
	</tr>
	<tr>
		<td colspan="4">
			<div style="padding:10px">
				<?php echo nl2br($cRemarks);?>
			</div>
		</td>
	</tr>
	<tr>
        <td colspan="5"><b>Entry</b></td>
	</tr>
    <?php
	  
	  	$sqlbody = mysqli_query($con,"select a.* from glactivity a where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");		
		if (mysqli_num_rows($sqlbody)!=0) {
			while($row = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){     
	?>
	<tr>
		<td><?php echo $row['ctitle']?></td>
		<td align="right">
			<?php 
				if($row['ndebit']!=0){ 
					echo number_format($row['ndebit'],2);
				}
			?>
		</td>
		<td align="right">
			<?php 
				if($row['ncredit']!=0){ 
					echo number_format($row['ncredit'],2);
				}
			?>
		</td>
		<td>&nbsp;</td>
	</tr>
	<?php
		}
	}
	?>
	<tr>
        <td align="center"><b>Total Amount Received</b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right"><b><?=number_format($nAmount,2)?></b></td>
	</tr>
</table>

<table border="0" width="100%" style="border-collapse:collapse; padding-top: 10px">
	<tr>
		<td width="33%" style="padding-top: 10px">
			<b>Prepared By:<br><br><br>&nbsp;&nbsp;&nbsp;</b><?php echo $PreparedBy;?>
		</td>

		<td style="padding-top: 10px">
			<b>Checked By:<br><br><br>&nbsp;&nbsp;&nbsp;</b>____________________
		</td>

		<td width="33%" style="padding-top: 10px">
			<b>Approved By:<br><br><br>&nbsp;&nbsp;&nbsp;</b>____________________
		</td>
	</tr>
</table>


<?php
//POST RECORD
//if($lPrintPosted==0){
	//mysqli_query($con,"Update apv set lprintposted=1, lapproved=1 where compcode='$company' and ctranno='$cpono'");
//}

$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');
mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `module`, `cevent`, `cmachine`, `cremarks`) 
	values('$corno','$preparedby',NOW(),'RECEIVE PAYMENT','PRINTED','$compname','Printed Record')");

?>

<script type="text/javascript">
	//window.opener.document.getElementById("hdnposted").value = 1;
	//window.opener.document.getElementById("salesstat").innerHTML = "POSTED";
	//window.opener.document.getElementById("salesstat").style.color = "#FF0000";
	//window.opener.document.getElementById("salesstat").style.fontWeight = "bold";
</script>

</body>
</html>