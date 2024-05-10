<?php
if(!isset($_SESSION)){
session_start();


include('../../vendor/autoload.php');

$mpdf = new \Mpdf\Mpdf();
ob_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_QUOTE'");
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
		}

	}

	$sqlprint = mysqli_query($con,"select * from parameters where ccode in ('QUOTEHDR','QUOTEFTR')");

	if(mysqli_num_rows($sqlprint) != 0){

		while($rowprint = mysqli_fetch_array($sqlprint, MYSQLI_ASSOC))
		{
			if($rowprint['ccode']=="QUOTEHDR"){
				$printhdrsrc = $rowprint['cdesc'];
			}
			if($rowprint['ccode']=="QUOTEFTR"){
				$printftrsrc = $rowprint['cdesc'];
			}			
		}

	}
	
	$csalesno = $_REQUEST['hdntransid'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname, b.chouseno, b.ccity, b.cstate, C.cdesc as termdesc from quote a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join groupings C on A.cterms = C.ccode where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$CustAddress= "";

		if($row['chouseno']<>""){
			$CustAddress = $row['chouseno'];
		}

		if($row['ccity']<>""){
			if($CustAddress<>""){
				$CustAddress = $CustAddress.", ".$row['ccity'];
			}else{
				$CustAddress = $row['ccity'];
			}			
		}
		
		if($row['cstate']<>""){
			if($CustAddress<>""){
				$CustAddress = $CustAddress.", ".$row['cstate'];
			}else{
				$CustAddress = $row['cstate'];
			}			
		}

		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$Gross = $row['ngross'];
		$cCurrCode = $row['ccurrencycode'];

		$ccontname = $row['ccontactname'];
		$ccontdesg = $row['ccontactdesig'];
		$ccontdept = $row['ccontactdept'];
		$ccontemai = $row['ccontactemail'];
		$ccontsalt = $row['ccontactsalut'];
		$cvattyp = $row['cvattype'];
		$cterms = $row['cterms'];
		$cdelinfo = $row['cdelinfo'];
		$cservinfo = $row['cservinfo'];

		$ctermsdesc = $row['termdesc']." upon delivery";
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
}


$sqldtlss = mysqli_query($con,"select A.*, B.citemdesc, B.cuserpic From quote_t A left join items B on A.citemno=B.cpartno where A.compcode='$company' and A.ctranno = '$csalesno'");

?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

<style>
#child{
    position:absolute;
    bottom:0px;
    /*right:0px;
    left:0px;
    overflow-y:auto;*/
    /* this is the key */
    max-height:100%;
    width: 100%;
}
</style>
<head>
</head>

<body>
<div class='wrapper nopadding' style="width: 8in; height: 10.2in;">

	<div class="row nopadding">
		<div class="col-xs-3" style="text-align: right;"><img src="<?php echo "../".$logosrc; ?>" width="136px" height="70px"></div>
		<div class="col-xs-9 nopadwtop2x" style="text-align: center; font-size: 10px; font-family: Verdana, sans-serif;"><?php echo $printhdrsrc; ?></div>
	</div>
	<hr class="hr2">
	<div class="row nopadding" style="padding-bottom: 40px !important">
		<div class="col-xs-12"><b><?php echo date("F d, Y"); ?></b></div>
	</div>

	<div class="row nopadding" style="padding-bottom: 20px !important">
		<div class="col-xs-12">
			<b>
				<?php 

					echo $ccontname."<br>".$ccontdesg."<br>".$ccontdept."<br>".$CustName;
					if($CustAddress<>""){
						echo "<br>".$CustAddress;
					}
					echo "<br>".$ccontemai; ?>
			</b>
		</div>
	</div>

	<div class="row nopadding" style="padding-bottom: 20px !important">
		<div class="col-xs-12">
			<b>
				<?php echo $ccontsalt; ?>
			</b>
		</div>
	</div>
	<div class="row nopadding" style="padding-bottom: 20px !important; padding-left: 20px !important; ">
		<div class="col-xs-12">
				This is our proposal for your requirement which includes the following:
		</div>
	</div>

	<table border="1" border-collapse="collapse" align="center" width="95%">
	
		<tr>
			<th class="text-center" style="padding: 3px">Qty</th>
			<th class="text-center" style="padding: 3px">Product Description/s</th>
			<th class="text-center" style="padding: 3px">Unit Price</th>
		</tr>

		<?php 
			while($rowdtls = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){ 
		?>

		<tr>
			<td class="text-center" style="padding: 3px"><?php echo $rowdtls['nqty']. " " . $rowdtls['cunit'];?></td>
			<td class="text-center" style="padding: 3px"><?php echo $rowdtls['citemdesc'];?></td>
			<td class="text-center" style="padding: 3px">
				<?php

						if($rowdtls['cuserpic']!=""){
							echo "<img src='".$rowdtls['cuserpic']."' height='82' width='80'><br>";
						}
					?>
					<?php echo $cCurrCode." ".$rowdtls['nprice'];?>

			</td>
		</tr>

		<?php 
			} 
		?>

	</table>	


	<div class="row nopadding" style="padding-top: 20px !important;">
		<div class="col-xs-3">
			<b>PRICE </b>
		</div>
		<div class="col-xs-9">
			:&nbsp;
			<?php 
				if($cvattyp=="VatEx"){
					echo "VAT EXCLUSIVE";
				}else{
					echo "VAT INCLUSIVE";
				}
			?>
		</div>
	</div>
	<div class="row nopadding">
		<div class="col-xs-3">
			<b>PAYMENT </b>
		</div>
		<div class="col-xs-9">
			:&nbsp;&nbsp;<?php echo $ctermsdesc; ?>
		</div>
	</div>
	<div class="row nopadding">
		<div class="col-xs-3">
			<b>DELIVERY </b>
		</div>
		<div class="col-xs-9">
			:&nbsp;&nbsp;<?php echo $cdelinfo; ?>
		</div>
	</div>
	<div class="row nopadding">
		<div class="col-xs-3">
			<b>SERVICE </b>
		</div>
		<div class="col-xs-9">
			:&nbsp;&nbsp;<?php echo $cservinfo; ?>
		</div>
	</div>
	<div class="row nopadding">
		<div class="col-xs-3">
			<b>PRICE VALIDTY </b>
		</div>
		<div class="col-xs-9">
			:&nbsp;&nbsp;<?php echo date("F d, Y", strtotime($Date)); ?>
		</div>
	</div>

	<div class="row nopadding" style="padding-top: 20px !important;">
		<div class="col-xs-12" style="height: 50px">
			<?php echo $Remarks; ?>
		</div>
	</div>

	<div class="row nopadding" id="child">
		<div class="col-xs-5">
			<?php echo $printftrsrc; ?>
		</div>
		<div class="col-xs-7">

				<div class="row nopadding" style="padding-bottom: 20px !important">
					<div class="col-xs-12 text-center">
						<b>Signature and Acceptance:</b>
					</div>
				</div>	
				<div class="row nopadding" style="padding-bottom: 10px !important">
					<div class="col-xs-4 text-right">
						Print Name:
					</div>
					<div class="col-xs-6 border-bottom border-dark">
						&nbsp;
					</div>
				</div>				
				<div class="row nopadding" style="padding-bottom: 10px !important">
					<div class="col-xs-4 text-right">
						Title:
					</div>
					<div class="col-xs-6 border-bottom border-dark">
						&nbsp;
					</div>
				</div>
				<div class="row nopadding" style="padding-bottom: 10px !important">
					<div class="col-xs-4 text-right">
						Signature:
					</div>
					<div class="col-xs-6 border-bottom border-dark">
						&nbsp;
					</div>
				</div>
		</div>
	</div>

</div>


</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();

// send the captured HTML from the output buffer to the mPDF class for processing
$mpdf->WriteHTML($html);
$mpdf->Output();


?>