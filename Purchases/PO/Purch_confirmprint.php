<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_PO'");
	if(mysqli_num_rows($sqlauto) != 0){
		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
		{
			$autopost = $rowauto['cvalue'];
		}
	}

	
	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
		}

	}
	
	$cpono = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*, b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, b.cterms from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.cpono = '$cpono'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$CustAdd = $row['chouseno']." ".$row['ccity']." ".$row['cstate']." ".$row['ccountry'];
		$Terms = $row['cterms']; 
		$CurrCode = $row['ccurrencycode'];
		//$CustContactNo = $row['cphone'];
	//		if($CustContactNo<>""){
		//		$CustContactNo = $CustContactNo." / ".$row['cmobile'];
		//	}else{
		//		$CustContactNo = $row['cmobile'];
		//	}
		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded']; 
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<style type="text/css">
#tblMain {
	/* the image you want to 'watermark' */
	background-image: url(../../images/preview.png);
	background-position: center;
	background-size: contain;
	background-repeat: no-repeat;
}

@media print {
.noPrint {
    display:none;
}
}
#menu{
	position: fixed;
	padding-top:0px 0px 0px 0px;
	top: 0px;
	height:30px;
	width:98%;
	border-style:solid;
	background-color:#9FF;
  border:1px solid black;
  opacity:1.0;
}
html, body {
	top:0px;
} 


</style>
<head>
<script type="text/javascript">

function Print(x){

					$.ajax ({
						url: "Purch_Tran.php",
						data: { x: x, typ: "POST" },
						async: false,
						dataType: "json",
						success: function( data ) {
							console.log(data);
							$.each(data,function(index,item){
								
								itmstat = item.stat;
								
								if(itmstat!="False"){

									window.parent.document.getElementById("hdnposted").value = 1;
									window.parent.document.getElementById("salesstat").innerHTML = "POSTED";
									window.parent.document.getElementById("salesstat").style.color = "#FF0000";
									window.parent.document.getElementById("salesstat").style.fontWeight = "bold";
				
								}
							});
						}
					});
				
				location.href = "PrintPO.php?hdntransid="+x;
}


function PrintRed(x){
	location.href = "PrintPO.php?hdntransid="+x;
}
</script>
</head>

<body>
<br><br>
<table border="0" width="100%" cellpadding="1px"  id="tblMain">
	<tr>
		<td align="center"> 

				<table border="0" width="100%">
						<tr align="center">
							<td><img src="<?php echo "../".$logosrc; ?>" width="80px" height="68px"></td>
						</tr>
						<tr align="center">
							<td><font style="font-size: 18px;"><?php echo $logonamz; ?></font></td>
						</tr>
						<tr align="center">
							<td style="padding-bottom: 20px"><font><?php echo $logoaddrs; ?></font></td>
						</tr>
				</table>

		</td>
	</tr>
	<tr>
		<td style="height: 5in; vertical-align: top; padding-top: 10px">

			<table border="0" width="100%">
				<tr>
					<td colspan="4" align="center" style="padding-bottom: 20px">
							<font style="font-size: 24px;">PURCHASE ORDER </font>
					</td>
				</tr>
				<tr>
					<td width="100px">
							<b>Name: </b>
					</td>
					<td>
							<?=$CustName?>
					</td>
					<td width="100px">
							<b>PO#: </b>
					</td>
					<td>
						<?=$cpono?>    
					</td>
				</tr>

				<tr>
					<td width="100px">
							<b>Address: </b>
					</td>
					<td>
						<?=$CustAdd?>
					</td>
					<td width="100px">
							<b>PR#: </b>
					</td>
					<td>
		
					</td>
				</tr>

				<tr>
					<td width="100px">
							<b>Date: </b>
					</td>
					<td>
						<?=date_format(date_create($Date), "M d, Y H:i:s")?>
					</td>
					<td width="100px">
							<b>Our Ref: </b>
					</td>
					<td>
		
					</td>
				</tr>

				<tr>
					<td width="100px">
							<b>Del Date: </b>
					</td>
					<td>
						<?=date_format(date_create($DateNeeded), "M d, Y")?>
					</td>
					<td width="100px">
							<b>Terms: </b>
					</td>
					<td>
						<?=$Terms?>
					</td>
				</tr>
				
			</table>
			<br>
			<table border="0" border-collapse="collapse" align="center" width="95%">
	
				<tr>
					<th style="padding: 3px; border-bottom: 1px solid">Qty</th>
					<th style="padding: 3px; border-bottom: 1px solid">Unit</th>
					<th style="padding: 3px; border-bottom: 1px solid">Product Description/s</th>
					<td style="padding: 3px; border-bottom: 1px solid" align="right"><b>Unit Price</b></td>
					<td style="padding: 3px; border-bottom: 1px solid" align="right"><b>Amount</b></td>
				</tr>

				<?php 
				$sqlbody = mysqli_query($con,"select a.*,b.citemdesc, a.citemdesc as newdesc from purchase_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.cpono = '$cpono' Order by a.nident");

				if (mysqli_num_rows($sqlbody)!=0) {

					while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
				?>

				<tr>
					<td style="padding: 3px"><?php echo $rowdtls['nqty'];?></td>
					<td style="padding: 3px"><?php echo $rowdtls['cunit'];?></td>					
					<td style="padding: 3px"><?php echo $rowdtls['citemdesc'];?></td>
					<td style="padding: 3px" align="right"><?php echo number_format($rowdtls['nprice'],2);?></td>
					<td style="padding: 3px" align="right"><?php echo number_format($rowdtls['namount'],2) . " " . $CurrCode;?></td>
					
				</tr>

				<?php 
					} 

				}
				?>

				<tr>
					<td colspan="4" style="padding-top: 10px" align="right"><b>Total Amount</b></td>
					<td style="padding-top: 10px" align="right"><?php echo number_format($Gross,2) . " " . $CurrCode;?></td>
					
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td style="height: 2in; vertical-align: bottom;">
			
			
			<table border="0" width="100%">
				<tr>
					<td>
						<table border=0 width="80%" align="center">

								<tr>
									<td align="center" colspan="2"><b>Signature and Acceptance:</b></td>
								</tr>
								<tr>
									<td width="100px" align="right">Print Name:&nbsp;&nbsp;&nbsp;</td>
									<td style="border-bottom: 1px solid #000; height: 30px">&nbsp;</td>
								</tr>
								<tr>
									<td width="100px" align="right">Title:&nbsp;&nbsp;&nbsp;</td>
									<td style="border-bottom: 1px solid #000; height: 30px">&nbsp;</td>
								</tr>
								<tr>
									<td width="100px" align="right">Signature:&nbsp;&nbsp;&nbsp;</td>
									<td style="border-bottom: 1px solid #000; height: 30px">&nbsp;</td>
								</tr>
						</table>
					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>


<div align="center" id="menu" class="noPrint">
<div style="float:left;">&nbsp;&nbsp;<strong><font size="-1">PURCHASE ORDER</font></strong></div>
<div style="float:right;">
<?php 
$strqry = "";
$valsub = "";

//if($lPosted==0 && $autopost==1){
//	$strqry = "Print('".$cpono."')";
//	$valsub = "PRINT AND POST PO";
//}
//else{
	$strqry = "PrintRed('".$cpono."')";
	$valsub = "PRINT PO";
//}


?>

<input type="button" value="<?php echo $valsub;?>" onClick="<?php echo $strqry;?>;" class="noPrint"/>
</div>
</div>

</body>
</html>