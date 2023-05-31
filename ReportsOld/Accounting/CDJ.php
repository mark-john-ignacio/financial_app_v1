<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "CDJ.php";

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
			Where A.compcode='$company' and A.cmodule='PV' and A.ddate between STR_TO_DATE('".$_REQUEST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_REQUEST['date2']."', '%m/%d/%Y') Order By A.dpostdate, A.ctranno, A.ndebit desc, A.ncredit desc";

	$result = mysqli_query($con, $sql);
		
	$arrdebits = array();
	$arrcredits = array();
	$arrallqry = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if(floatval($row['ndebit'])!==0 && floatval($row['ncredit'])==0){
				$arrdebits[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
			}

			if(floatval($row['ncredit'])!==0 && floatval($row['ndebit'])==0){
				$arrcredits[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
			}

			$arrallqry[] = $row;
		}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cash Disbursement Journal</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?=strtoupper($compname);  ?></h2>
<h2 class="nopadding">Cash Disbursement Journal</h2>
<h3 class="nopadding">For the Period <?=date_format(date_create($_POST["date1"]),"F d, Y");?> to <?=date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="80%" border="1" align="center" cellpadding = "3">
  <tr>
    <th width="100" style="vertical-align:middle">Date</th>
    <th width="100" style="vertical-align:middle">Trans No.</th>
    <th style="vertical-align:middle">Account Credited</th>
    <th style="vertical-align:middle">Description</th>
      
   <?php
		$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );
   	foreach($arrundrs as $rsdr) {
			$arrallaccts[$rsdr['cacctno']] = 0;
			$arrtotaccts[$rsdr['cacctno']] = 0;
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
			$arrallaccts[$rscr['cacctno']] = 0;
			$arrtotaccts[$rscr['cacctno']] = 0;
   ?>
   	<th align="center" style="vertical-align:bottom; text-align: center !important" width="150">
    	<?=$rscr['cacctno'];?><br><?=$rscr['cacctdesc'];?><br>Cr.      
    </th>
   <?php
		}
   ?>

  </tr>
  
	<?php
	if(count($arrallqry) > 0){

		$ctranno = $arrallqry[0]['ctranno'];
		$cddate = $arrallqry[0]['ddate'];
		$cname = $arrallqry[0]['cname'];
		$crmrks = $arrallqry[0]['cremarks'];
		foreach($arrallqry as $rsallqry){
			if($ctranno==$rsallqry['ctranno']){

				foreach($arrundrs as $rsdr) {
					if($rsdr['cacctno']==$rsallqry['acctno']){
						$arrallaccts[$rsdr['cacctno']] = $rsallqry['ndebit'];
					}
				}

				foreach($arruncrs as $rscr) {
					if($rscr['cacctno']==$rsallqry['acctno']){
						$arrallaccts[$rscr['cacctno']] = $rsallqry['ncredit'];
					}
				}

			}else{
	?>

	<tr>
    <td><?=$cddate?></td>
    <td><?=$ctranno?></td>
    <td><?=$cname?></td>
    <td><?=$crmrks?></td>
	
		<?php
			$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );
			foreach($arrundrs as $rsdr) {

				if(isset($arrallaccts[$rsdr['cacctno']])){
					$arrtotaccts[$rsdr['cacctno']] = $arrtotaccts[$rsdr['cacctno']] + floatval($arrallaccts[$rsdr['cacctno']]);
		?>
			<td align="right">
				<?=number_format($arrallaccts[$rsdr['cacctno']],2);?>     
			</td>
		<?php
				}
			}
		?>

		<?php
			$arruncrs = array_intersect_key( $arrcredits , array_unique( array_map('serialize' , $arrcredits ) ) );
			foreach($arruncrs as $rscr) {
				if(isset($arrallaccts[$rscr['cacctno']])){
					$arrtotaccts[$rscr['cacctno']] = $arrtotaccts[$rscr['cacctno']] + floatval($arrallaccts[$rscr['cacctno']]);
		?>
			<td align="right">
				<?=number_format($arrallaccts[$rscr['cacctno']],2);?>  
			</td>
		<?php
				}
			}
		?>


	<tr>
	<?php
				$ctranno = $rsallqry['ctranno'];
				$cddate = $rsallqry['ddate'];
				$cname = $rsallqry['cname'];
				$crmrks = $rsallqry['cremarks'];
			}
		}
	?>

	<tr>
    <td><?=$cddate?></td>
    <td><?=$ctranno?></td>
    <td><?=$cname?></td>
    <td><?=$crmrks?></td>
	
		<?php
			$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );
			foreach($arrundrs as $rsdr) {

				if(isset($arrallaccts[$rsdr['cacctno']])){
					$arrtotaccts[$rsdr['cacctno']] = $arrtotaccts[$rsdr['cacctno']] + floatval($arrallaccts[$rsdr['cacctno']]);
		?>
			<td align="right">
				<?=number_format($arrallaccts[$rsdr['cacctno']],2);?>     
			</td>
		<?php
				}
			}
		?>

		<?php
			$arruncrs = array_intersect_key( $arrcredits , array_unique( array_map('serialize' , $arrcredits ) ) );
			foreach($arruncrs as $rscr) {
				if(isset($arrallaccts[$rscr['cacctno']])){
					$arrtotaccts[$rscr['cacctno']] = $arrtotaccts[$rscr['cacctno']] + floatval($arrallaccts[$rscr['cacctno']]);
		?>
			<td align="right">
				<?=number_format($arrallaccts[$rscr['cacctno']],2);?>  
			</td>
		<?php
				}
			}
		?>


	<tr>

	<!-- TOTALS -->
	<tr>
    <td colspan="4" align="right"><b>Total: </b></td>
		<?php
			$arrundrs = array_intersect_key( $arrdebits , array_unique( array_map('serialize' , $arrdebits ) ) );
			foreach($arrundrs as $rsdr) {

				if(isset($arrallaccts[$rsdr['cacctno']])){
		?>
			<td align="right">
				<b><?=number_format($arrtotaccts[$rsdr['cacctno']],2);?></b>  
			</td>
		<?php
				}
			}
		?>

		<?php
			$arruncrs = array_intersect_key( $arrcredits , array_unique( array_map('serialize' , $arrcredits ) ) );
			foreach($arruncrs as $rscr) {
				if(isset($arrallaccts[$rscr['cacctno']])){
		?>
			<td align="right">
			<b><?=number_format($arrtotaccts[$rscr['cacctno']],2);?> </b> 
			</td>
		<?php
				}
			}
		?>
	</tr>
	<?php
	}
	?>
</table>

</body>
</html>