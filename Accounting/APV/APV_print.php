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
	
	$cpono = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname,c.Fname,c.Lname,c.Minit,c.cusersign from apv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join users c on a.cpreparedby=c.Userid where a.compcode='$company' and a.ctranno = '$cpono'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cpaymentfor'];
		$Date = $row['dapvdate'];
		//$ChkNo = $row['cchkno'];

		$nGross = $row['ngross'];
		
		$PreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
		$cpreparedBySign = $row['cusersign'];


		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
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
					<td align="center">
							<font style="font-size: 24px;">ACCOUNTS PAYABLE VOUCHER </font>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 20px; text-align: right">
						<b>APV #: </b><?=$cpono;?>
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
						<b>Payee: </b> <?=$CustName?>
					</td>
					<td colspan="2" align="right">
						<b>Date: </b> <?=$Date?>
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
	</tr>
	<tr>
		<td colspan="4">
			<div style="padding:10px">
				<?php echo nl2br($Remarks);?>
			</div>
		</td>
	</tr>
	<tr>
        <td colspan="3"><b>Entry</b></td>
	</tr>
    <?php

		$xtotdebit = 0;
		$xtotcredit = 0;

		$sqlhead = mysqli_query($con,"SELECT a.*,b.cname FROM apv_t a LEFT JOIN customers b on a.compcode=b.compcode and a.csubsidiary=b.cempid WHERE a.compcode='$company' and a.ctranno = '$cpono' ORDER BY nidentity");
		
		if (mysqli_num_rows($sqlhead)!=0) {
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){     
	?>
	<tr>
		<td><?php echo $row['ctitle']?></td>
		<td align="right">
			<?php 
				if($row['ndebit']!=0){ 
					echo number_format($row['ndebit'],2);

					$xtotdebit = $xtotdebit + floatval($row['ndebit']);
				}
			?>
		</td>
		<td align="right">
			<?php 
				if($row['ncredit']!=0){ 
					echo number_format($row['ncredit'],2);
					$xtotcredit = $xtotcredit + floatval($row['ncredit']);
				}
			?>
		</td>
	</tr>
	<?php
		}
	}
	?>
	<tr>
        <td>&nbsp;</td>
		<td align="right"><b><?=number_format($xtotdebit,2);?></b></td>
		<td align="right"><b><?=number_format($xtotcredit,2);?></b></td>
	</tr>
</table>

<table border="0" width="100%" style="border-collapse:collapse; padding-top: 10px">
	<tr>
		<td width="33%" style="padding-top: 10px; vertical-align: top">

			<?php
				if($lPosted==1 && $cpreparedBySign != "" && $cpreparedBySign != null){
			?>
		
			<b>Prepared By:</b>
				<div><img src="<?php echo $cpreparedBySign; ?>" width="160px" height="88px"></div>
			<?php
				}else{
			?>

			<b>Prepared By:<br><br><br><br>&nbsp;&nbsp;&nbsp;</b>
				<div style="border-top: 1px solid">&nbsp;&nbsp;&nbsp;<?=$PreparedBy?>&nbsp;&nbsp;&nbsp;</div>
			<?php
				}
			?>

		</td>

		<td style="padding-top: 10px; vertical-align: top">
			<b>Checked By:<br><br><br><br>&nbsp;&nbsp;&nbsp;</b>
				<div style="border-top: 1px solid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
		</td>

		<td width="33%" style="padding-top: 10px; vertical-align: top">
			<b>Approved By:<br><br><br><br>&nbsp;&nbsp;&nbsp;</b>
				<div style="border-top: 1px solid">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
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
	values('$cpono','$preparedby',NOW(),'APV','PRINTED','$compname','Printed Record')");

?>

<script type="text/javascript">
	//window.opener.document.getElementById("hdnposted").value = 1;
	//window.opener.document.getElementById("salesstat").innerHTML = "POSTED";
	//window.opener.document.getElementById("salesstat").style.color = "#FF0000";
	//window.opener.document.getElementById("salesstat").style.fontWeight = "bold";

</script>

</body>
</html>