<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "CashBook.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);

	$arrallaccts = array();
	$arrtotaccts = array();
					
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
						
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}


	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$qry = "";
	$varmsg = "";

	$cntrCredz = 0;
	
	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit, D.cname, C.cremarks
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			left join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno
			left join customers D on C.compcode=D.compcode and C.ccode=D.cempid
			Where A.compcode='$company' and A.cmodule='OR' and A.ddate between STR_TO_DATE('".$_REQUEST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_REQUEST['date2']."', '%m/%d/%Y') Order By A.ddate, A.ctranno, A.ndebit desc, A.ncredit desc";

			//echo $sql;

	$result = mysqli_query($con, $sql);
		
	$arrdebits = array();
	$arrcredits = array();
	$arrallqry = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if(floatval($row['ndebit'])!==0){

				$arrdebits[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
			}

			if(floatval($row['ncredit'])!==0){
				$arrcredits[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
			}

			$arralltrans[] = array('ctranno' => $row['ctranno'], 'ddate' => $row['ddate'], 'cname' => $row['cname'], 'cremarks' => $row['cremarks']);

			$arrallqry[$row['ctranno']][] = $row;
		}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cash Receipts Journal</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?=strtoupper($compname);  ?></h2>
<h2 class="nopadding">Cash Receipts Journal</h2>
<h3 class="nopadding">For the Period <?=date_format(date_create($_POST["date1"]),"F d, Y");?> to <?=date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table border="1" align="center" cellpadding = "5">
  <tr>
    <th width="100" style="vertical-align:middle">Date</th>
    <th width="100" style="vertical-align:middle">Trans No.</th>
    <th style="vertical-align:middle">Account Credited</th>
    <th style="vertical-align:middle">Description</th>
      
   <?php

		$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );

   	foreach($arrundrs as $rsdr) {
			$sumtot[$rsdr['cacctno']] = 0;
   ?>
   	<th style="vertical-align:bottom; text-align: center !important" width="150">
    	<?=$rsdr['cacctno'];?><br><?=$rsdr['cacctdesc'];?><br>Dr.      
    </th>
   <?php
		}
   ?>

	<?php
		$arruncrs = array_intersect_key( $arrcredits , array_unique( array_map('serialize' , $arrcredits ) ) );
   	foreach($arruncrs as $rscr) {
			$sumtot[$rscr['cacctno']] = 0;
   ?>
   	<th align="center" style="vertical-align:bottom; text-align: center !important" width="150">
    	<?=$rscr['cacctno'];?><br><?=$rscr['cacctdesc'];?><br>Cr.      
    </th>
   <?php
		}
   ?>

  </tr>
  
	<?php

	if(count($arralltrans) > 0){

		$arruntransno = array_intersect_key( $arralltrans , array_unique( array_map('serialize' , $arralltrans ) ) );
		foreach($arruntransno as $rsnoxc){

	?>

	<tr>
    <td nowrap><?=$rsnoxc['ddate']?></td>
    <td nowrap><?=$rsnoxc['ctranno']?></td>
    <td nowrap><?=$rsnoxc['cname']?></td>
    <td nowrap><?=$rsnoxc['cremarks']?></td>
	
		<?php
			$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );
			foreach($arrundrs as $rsdr) {				
				$drval = 0;
				foreach($arrallqry[$rsnoxc['ctranno']] as $rx2lo){
					if($rx2lo['acctno']==$rsdr['cacctno']){
						$drval = $rx2lo['ndebit'];
						break;
					}
				}
		?>
			<td style="text-align: right !important">
			<?=($drval!=0) ? number_format($drval,2) : "";?>     
			</td>
		<?php
			$sumtot[$rsdr['cacctno']] = $sumtot[$rsdr['cacctno']] + floatval($drval);
			$drval = 0;
			}
		?>

		<?php
			$arrundrs = array_intersect_key( $arrcredits , array_unique( array_map('serialize' , $arrcredits ) ) );
			foreach($arrundrs as $rsdr) {				
				$drval = 0;
				foreach($arrallqry[$rsnoxc['ctranno']] as $rx2lo){
					if($rx2lo['acctno']==$rsdr['cacctno']){
						$drval = $rx2lo['ncredit'];
						break;
					}
				}
		?>
			<td style="text-align: right !important">
				<?=($drval!=0) ? number_format($drval,2) : "";?>      
			</td>
		<?php
			$sumtot[$rsdr['cacctno']] = $sumtot[$rsdr['cacctno']] + floatval($drval);
			$drval = 0;
			}
		?>



	<?php
	}

}
	?>

	<!-- TOTAL -->
	<tr>
    <td colspan="4" style="text-align: right"><b>TOTAL: </b></td>

		<?php
			$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );
			foreach($arrundrs as $rsdr) {				
		?>
			<td style="text-align: right !important">
				<b><?=($sumtot[$rsdr['cacctno']]!=0) ? number_format($sumtot[$rsdr['cacctno']],2) : "";?></b>
			</td>
		<?php
			}
		?>

		<?php
			$arrundrs = array_intersect_key( $arrcredits , array_unique( array_map('serialize' , $arrcredits ) ) );
			foreach($arrundrs as $rsdr) {				
		?>
			<td style="text-align: right !important">
				<b><?=($sumtot[$rsdr['cacctno']]!=0) ? number_format($sumtot[$rsdr['cacctno']]) : "";?></b>  
			</td>
		<?php
			}
		?>


</table>

</body>
</html>