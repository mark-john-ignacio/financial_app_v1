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
		$compadd = $row['compadd'];
		$comptin = $row['comptin'];
	}


	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$qry = "";
	$varmsg = "";

	$cntrCredz = 0;
	
	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit, D.cname, D.ctin , C.cremarks
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			left join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno
			left join customers D on C.compcode=D.compcode and C.ccode=D.cempid
			Where A.compcode='$company' and A.cmodule='OR' and A.ddate between STR_TO_DATE('".$_REQUEST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_REQUEST['date2']."', '%m/%d/%Y') Order By A.ddate, A.ctranno, A.ndebit desc, A.ncredit desc";

			//echo $sql;

	$result = mysqli_query($con, $sql);
		
	$arrdebits = array();
	$arrcredits = array();
	$arrallqry = array();
	$arralltrans = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if(floatval($row['ndebit'])!=0){

				//echo "Debs: ".$row['acctno']." - ".floatval($row['ndebit'])."<br>";

				$arrdebits[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
			}

			if(floatval($row['ncredit'])!=0){
				$arrcredits[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
			}

			$arralltrans[] = array('cmodule' => $row['cmodule'], 'ctranno' => $row['ctranno'], 'ddate' => $row['ddate'], 'ctin' => $row['ctin'], 'cname' => $row['cname'], 'cremarks' => $row['cremarks']);

			$arrallqry[$row['ctranno']][] = $row;
		}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<title>Cash Receipts Book</title>
</head>

<body style="padding:20px">
<center>
	
<h1 class="nopadding">Cash Receipts Book</h1>
<h2 class="nopadding">Company: <?=strtoupper($compname);  ?></h2>
<h3>Company Address: <?php echo strtoupper($compadd);  ?></h3>
<h3>Vat Registered Tin: <?php echo $comptin;  ?></h3>
<h3 class="nopadding">For the Period <?=date_format(date_create($_POST["date1"]),"F d, Y");?> to <?=date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table class='table-bordered' border="1" align="center" cellpadding = "5" width='100%'>
  <tr>
	<th style='display: none;'>module</th>
    <th width="100" style="vertical-align:middle">Date</th>
    <th width="100" style="vertical-align:middle">Trans No.</th>
    <th style="vertical-align:middle">Account Credited</th>
    <th style="vertical-align:middle">Tin</th>
    <!-- <th style="vertical-align:middle">Account Title</th> -->
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

	<tr id='tableContent' name='tableContent'>
    <td style='display: none;' nowrap><?=$rsnoxc['cmodule']?></td>
    <td nowrap><?=$rsnoxc['ddate']?></td>
    <td nowrap><?=$rsnoxc['ctranno']?></td>
    <td nowrap><?=$rsnoxc['cname']?></td>
	<td nowrap align="right"><?=$rsnoxc['ctin']?></td>
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
    <td colspan="5" style="text-align: right"><b>TOTAL: </b></td>

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
				<b><?=($sumtot[$rsdr['cacctno']]!=0) ? number_format($sumtot[$rsdr['cacctno']],2) : "";?></b>  
			</td>
		<?php
			}
		?>


</table>
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">

					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h5><b><i><span id='modalTitle'></span></i></b></h5>
					
				</div>
				<div class="modal-body" style="height: 100%; overflow: auto">

					<table class='table ' id="HeadDetail" border="1" bordercolor="#CCCCCC" width="100%" style="overflow: auto;">
						<thead></thead>
						<tbody></tbody>
					</table>
					<br><br>
					<table class='table' id="detailTable" border="1" bordercolor="#CCCCCC" width="100%" style="text-align: right; min-width: 30%; overflow: auto;">
						<thead></thead>
						<tbody></tbody>
					</table>
					<table class='table' id="subdetailTable" border="1" bordercolor="#CCCCCC" width="100%" style="text-align: right; min-width: 30%; overflow: auto;">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script type="text/javascript">
	$(document).ready(function(){


		$(document).on('click', '#tableContent', function(){
			let modules = $(this).closest('#tableContent').find('td:eq(0)').text();
			let ctranno = $(this).closest('#tableContent').find('td:eq(2)').text();
			console.log(modules)

			clearTable("#HeadDetail")
			clearTable('#detailTable')
			clearTable('#subdetailTable')
			
			$.ajax({
				url: 'Controller/TBal_Controller.php',
				type: 'post',
				dataType: 'json',
				data: {
					module: modules,
					ctranno: ctranno
				},
				success: function(res){
					$('#detailModal').modal('show')
					console.log(modules)
					var sample = res.data;
					sample.map((item, index) => {
						switch(modules){
							case 'DR':
								$('#modalTitle').text('Delivery Receipt')
								ShowDR(index, item)
								console.log(item);
								break;
							case 'SI':
								$('#modalTitle').text('Sales Invoice')
								ShowSI(index, item)
								console.log(item)
								break;
							case 'IN': 
								$('#modalTitle').text('Non-Trade Sales Invoice')
								ShowIN(index, item)
								console.log(item)
								break;
							case 'APV':
								$('#modalTitle').text('Accounts Payment Voucher')
								ShowAPV(index, item)
								console.log(item)
								break;
							case 'JE':
								$('#modalTitle').text('Journal Entry')
								ShowJE(index, item)
								console.log(item)
								break;
							case 'OR':
								$('#modalTitle').text('Accounts Receivable Payment')
								console.log(item)
								ShowOR(index, item)
								break;
							case 'ARADJ':
								$('#modalTitle').text('Accounts Receivable Adjustment')
								ShowARADJ(index, item)
								console.log(item)
								break;
							case 'PV':
								$('#modalTitle').text('Bills Payment')
								ShowPV(index, item)
								console.log(item)
								break;
							case 'BD':
								$('#modalTitle').text('Bank Deposit')
								ShowBD(index, item)
								console.log(item)
								break;
							case 'APADJ':
								$('#modalTitle').text('Accounts Payment Adjustment')
								ShowAPADJ(index, item)
								console.log(item)
								break;
							default: 
								break;
						}
						
					})
					
				}
			})
		})
	})

	function clearTable(table){
			$(table +' thead').empty();
			$(table + ' tbody').empty();
	}

	// function ShowOR({cacctdesc, cacctno, cewtcode, cewtcodeorig, 
	// 	cidentity, compcode, csalesno, ctaxcode, ctaxcodeorig, ctranno, 
	// 	dcutdate, namount, napplied, ncm, ndiscount, ndm, ndue, newtamt, 
	// 	newtgiven, newtrate, nidentity, nnet, npayment, ntaxrate, nvat}){
	function ShowOR(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Supplier: '),
				$('<tH>').text('Credit Account'),
				$('<tH>').text('Date:'),
				$('<tH>').text('Remarks:'),

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.cacctdesc),
				$('<td>').text(data.ddate),
				$('<td>').text((data.cremarks != null ? data.cremarks : '-')),
				
			).appendTo('#HeadDetail > tbody')


			$('<tr>').append(
				$('<tH>').text('Account No. '),
				$('<tH>').text('Account Title '),
				$('<tH>').text('Debit: '),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')

			$('<tr>').append(
				$('<tH>').text('Transaction No.'),
				$('<tH>').text('UOM'),
				$('<tH>').text('Amount'),
				$('<tH>').text('Discount'),
				$('<tH>').text('CM'),
				$('<tH>').text('Payment'),
				$('<tH>').text('Vat Code'),
				$('<tH>').text('Vat'),
				$('<tH>').text('Net Vat'),
				$('<tH>').text('EWT Code'),
				$('<tH>').text('EWT AMT/Rate'),
				$('<tH>').text('Total EWT'),
				$('<tH>').text('Total Due'),
				$('<tH>').text('Amount Applied'),
			).appendTo('#subdetailTable > thead')

			$.ajax({
				url: 'Controller/th_GLactivity_List.php',
				type: 'post',
				dataType: 'json',
				data: {ctranno: data.ctranno},
				async: false,
				success: function(res){
					res['data'].map((item, res) =>{
						$('<tr>').append(
							$("<td style='text-align: left'>").text(item.acctno),
							$("<td style='text-align: left'>").text(item.ctitle),
							$('<td>').text(parseFloat(item.ndebit).toFixed(2)),
							$('<td>').text(parseFloat(item.ncredit).toFixed(2)),
						).appendTo('#detailTable > thead')
					})
				}
			})
		}

		$('<tr>').append(
			$('<td>').text( (data.csalesno != null ? data.csalesno : '-') ),
			$('<td>').text( (data.cdesc != null ? data.cdesc : '-') ),
			$('<td>').text( (data.namount != null ? parseFloat(data.namount).toFixed(2) : '-') ),
			$('<td>').text( (data.ndiscount != null ? parseFloat(data.ndiscount).toFixed(2) : '-') ),
			$('<td>').text( (data.ncm != null ? parseFloat(data.ncm).toFixed(2) : '-') ),
			$('<td>').text( (data.npayment != null ? parseFloat(data.npayment).toFixed(2) : '-') ),
			$('<td>').text( (data.ctaxcode != null ? data.ctaxcode : '-') ),
			$('<td>').text( (data.nvat != null ? parseFloat(data.nvat).toFixed(2) : '-') ),
			$('<td>').text( (data.nnet != null ? parseFloat(data.nnet).toFixed(2) : '-') ),
			$('<td>').text( (data.cewtcode != "" ? data.cewtcode : '-') ),
			$('<td>').text( (data.newtgiven != null ?  parseFloat(data.newtgiven).toFixed(2) : '-' ) ),
			$('<td>').text( (data.newtamt != null ?  parseFloat(data.newtamt).toFixed(2) : '-' ) ),
			$('<td>').text( (data.ndue != null ?  parseFloat(data.ndue).toFixed(2) : '-' ) ),
			$('<td>').text( (data.napplied != null ?  parseFloat(data.napplied).toFixed(2) : '-' ) ),
		).appendTo('#subdetailTable > tbody')

	}

	function ShowSI(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No.: '),
				$('<tH>').text('Customer: '),
				$('<tH>').text('Type'),
				$('<tH>').text('Date:'),
				$('<tH>').text('Remarks'),

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.csalestype),
				$('<td>').text(data.ddate),
				$('<td>').text((data.cremarks != null ? data.cremarks : '-')),
			).appendTo('#HeadDetail > tbody')

			$('<tr>').append(
				$('<tH>').text('Account No. '),
				$('<tH>').text('Account Title '),
				$('<tH>').text('Debit: '),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')

			$('<tr>').append(
				$('<tH>').text('Item'),
				$('<tH>').text('EWT Code'),
				$('<tH>').text('Vat Code'),
				$('<tH>').text('UOM'),
				$('<tH>').text('Quantity'),
				$('<tH>').text('Price'),
				$('<tH>').text('Discount'),
				$('<tH>').text('Amount'),
				$('<tH>').text('Total Amount in PHP')
			).appendTo('#subdetailTable > thead')
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.citemdesc != null ? data.citemdesc : '-') ),
			$('<td>').text( (data.cewtcode != "" ? data.cewtcode : '-') ),
			$('<td>').text( (data.ctaxcode != null ? data.ctaxcode : '-') ),
			$('<td>').text( (data.cmainunit != null ? data.cmainunit : '-') ),
			$('<td>').text( (data.nqty != null ? parseFloat(data.nqty).toFixed(2) : '-') ),
			$('<td>').text( (data.nprice != null ? parseFloat(data.nprice).toFixed(2) : '-') ),
			$('<td>').text( (data.ndiscount != null ? parseFloat(data.ndiscount).toFixed(2) : '-') ),
			$('<td>').text( (data.nbaseamount != null ? parseFloat(data.nbaseamount).toFixed(2) : '-') ),
			$('<td>').text( (data.namount != null ? parseFloat(data.namount).toFixed(2) : '-') ),
		).appendTo('#subdetailTable > tbody')

		$.ajax({
			url: 'Controller/th_GLactivity_List.php',
			type: 'post',
			dataType: 'json',
			data: {ctranno: data.ctranno},
			async: false,
			success: function(res){
				res['data'].map((item, res) =>{
					$('<tr>').append(
						$("<td style='text-align: left'>").text(item.acctno),
						$("<td style='text-align: left'>").text(item.ctitle),
							$('<td>').text(parseFloat(item.ndebit).toFixed(2)),
							$('<td>').text(parseFloat(item.ncredit).toFixed(2)),
					).appendTo('#detailTable > thead')
				})
			}
		})
	}

	function ShowIN(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No.'),
				$('<tH>').text('Customer: '),
				$('<tH>').text('Type'),
				$('<tH>').text('Date:')

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.csalestype),
				$('<td>').text(data.ddate)
			).appendTo('#HeadDetail > tbody')

			$('<tr>').append(
				$('<tH>').text('Account No. '),
				$('<tH>').text('Account Title '),
				$('<tH>').text('Debit: '),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')

			$('<tr>').append(
				$('<tH>').text('Item'),
				$('<tH>').text('UOM'),
				$('<tH>').text('Quantity'),
				$('<tH>').text('Price'),
				$('<tH>').text('Discount'),
				$('<tH>').text('Amount'),
				$('<tH>').text('Total Amount in PHP')
			).appendTo('#subdetailTable > thead')
		}
		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.citemdesc != null ? data.citemdesc : '-') ),
			$('<td>').text( (data.cunit != null ? data.cunit : '-') ),
			$('<td>').text( (data.nqty != null ? parseFloat(data.nqty).toFixed(2) : '-') ),
			$('<td>').text( (data.nprice != null ? parseFloat(data.nprice).toFixed(2) : '-') ),
			$('<td>').text( (data.ndiscount != null ? parseFloat(data.ndiscount).toFixed(2) : '-') ),
			$('<td>').text( (data.nbaseamount != null ? parseFloat(data.nbaseamount).toFixed(2) : '-') ),
			$('<td>').text( (data.namount != null ? parseFloat(data.namount).toFixed(2) : '-') ),
		).appendTo('#subdetailTable > tbody')

		$.ajax({
			url: 'Controller/th_GLactivity_List.php',
			type: 'post',
			dataType: 'json',
			data: {ctranno: data.ctranno},
			async: false,
			success: function(res){
				res['data'].map((item, res) =>{
					$('<tr>').append(
						$("<td style='text-align: left'>").text(item.acctno),
						$("<td style='text-align: left'>").text(item.ctitle),
							$('<td>').text(parseFloat(item.ndebit).toFixed(2)),
							$('<td>').text(parseFloat(item.ncredit).toFixed(2)),
					).appendTo('#detailTable > thead')
				})
			}
		})
	}


	function ShowAPV(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No.: '),
				$('<tH>').text('Supplier: '),
				$('<tH>').text('Date:'),
				$('<tH>').text('Remarks')

			).appendTo('#HeadDetail > thead')
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cpayee),
				$('<td>').text(data.ddate),
				$('<td>').text(data.cremarks)
			).appendTo('#HeadDetail > tbody')


			$('<tr>').append(
				$('<tH>').text('Referrence No.'),
				$('<tH>').text('Base Amount'),
				$('<tH>').text('Total CM'),
				$('<tH>').text('Total Discount'),
				$('<tH>').text('Vat Code'),
				$('<tH>').text('Vat Rate'),
				$('<tH>').text('Vat Amount'),
				$('<tH>').text('Net of Vat'),
				$('<tH>').text('EWT Code'),
				$('<tH>').text('EWT RATE'),
				$('<tH>').text('EWT AMOUNT'),
				$('<tH>').text('Total Due'),
			).appendTo('#subdetailTable > thead')

			

			$('<tr>').append(
				$('<tH>').text('Referrence No.'),
				$('<tH>').text('Account No.'),
				$('<tH>').text('Title'),
				$('<tH>').text('Debit'),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')
		}
			$('<tr>').append(
				$("<td style='text-align: left'>").text( (data.crefno != null ? data.crefno : '-') ),
				$('<td>').text( (data.namount != null ? parseFloat(data.namount).toFixed(2) : '-') ),
				$('<td>').text( (data.napcm != null ? parseFloat(data.napcm).toFixed(2) : '-') ),
				$('<td>').text( (data.napdisc != null ? parseFloat(data.napdisc).toFixed(2) : '-') ),
				$('<td>').text( (data.cvatcode != null ? data.cvatcode : '-') ),
				$('<td>').text( (data.nvatrate != null ? parseFloat(data.nvatrate).toFixed(2) : '-') ),
				$('<td>').text( (data.nvatamt != null ? parseFloat(data.nvatamt).toFixed(2) : '-') ),
				$('<td>').text( (data.nnet != null ? parseFloat(data.nnet).toFixed(2) : '-') ),
				$('<td>').text( (data.cewtcode != null ? data.cewtcode : '-') ),
				$('<td>').text( (data.newtrate != null ? parseFloat(data.newtrate).toFixed(2) : '-') ),
				$('<td>').text( (data.newtamt != null ? parseFloat(data.newtamt).toFixed(2) : '-') ),
				$('<td>').text( (data.ngross != null ? parseFloat(data.ngross).toFixed(2) : '-') ),
			).appendTo('#subdetailTable > tbody')
		
			
		$.ajax({
			url: 'Controller/th_APVD_LIST.php',
			data: {ctranno : data.ctranno},
			type: 'post',
			dataType: 'json',
			async: false,
			success: function(res){
				
				console.log(res.data)
				if(res.valid){
					var sample = res.data;
					var debit = 0;
					var credit = 0;
					sample.map((item, key) =>{
						debit += parseFloat(item['ndebit']);
						credit += parseFloat(item['ncredit']);
						console.log(sample.length-3 + ' ' + key )
						if(key >= sample.length-3 ){
							$('<tr>').append(
								$("<td style='text-align: left'>").text(data.crefno),
								$("<td style='text-align: left'>").text( item.cacctno),	
								$("<td style='text-align: left'>").text( (item.ctitle != null ? item.ctitle: '-') ),
								$('<td>').text( (item.ndebit != null ? parseFloat(item.ndebit).toFixed(2) : '-') ),
								$('<td>').text( (item.ncredit != null ? parseFloat(item.ncredit).toFixed(2) : '-') ),
							).appendTo('#detailTable > tbody')
						}
					})
					
				} else {
					console.log('no reference')
				}
				
			}
		})
	}

	function ShowJE(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Journal Entry Of: '),
				$('<tH>').text('Date:'),
				$('<tH>').text('Memo: '),

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.ddate),
				$('<tH>').text((data.cmemo != '' ? data.cmemo : '-')),
			).appendTo('#HeadDetail > tbody')


			$('<tr>').append(
				$('<tH>').text('Account No.'),
				$('<tH>').text('Account Title'),
				$('<tH>').text('Debit'),
				$('<tH>').text('Credit'),
				$('<tH>').text('Subsidiary'),
				$('<tH>').text('Remarks'),
			).appendTo('#detailTable > thead')
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.cacctno != null ? data.cacctno : '-') ),
			$("<td style='text-align: left'>").text( (data.ctitle != null ? data.ctitle : '-') ),
			$('<td>').text( (data.ndebit != null ? data.ndebit : '-') ),
			$('<td>').text( (data.ncredit != null ? data.ncredit : '-') ),
			$("<td style='text-align: left'>").text( (data.csub != null ? data.csub : '-') ),
			$("<td style='text-align: left'>").text( (data.cremarks != null ? data.cremarks : '-') ),
		).appendTo('#detailTable > tbody')
	}

	function ShowARADJ(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Customer '),
				$('<tH>').text('SR Referrence '),
				$('<tH>').text('SI Referrence '),
				$('<tH>').text('Date:')

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.crefsr),
				$('<td>').text(data.crefsi),
				$('<td>').text(data.ddate)
			).appendTo('#HeadDetail > tbody')


			$('<tr>').append(
				$('<tH>').text('Account No.'),
				$('<tH>').text('Account Title'),
				$('<tH>').text('Debit'),
				$('<tH>').text('Credit'),
				$('<tH>').text('Subsidiary'),
				$('<tH>').text('Remarks'),
			).appendTo('#detailTable > thead')
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.cacctno != null ? data.cacctno : '-') ),
			$("<td style='text-align: left'>").text( (data.ctitle != null ? data.ctitle : '-') ),
			$('<td>').text( (data.ndebit != null ? parseFloat(item.ndebit).toFixed(2) : '-') ),
			$('<td>').text( (data.ncredit != null ? parseFloat(item.ncredit).toFixed(2) : '-') ),
			$("<td style='text-align: left'>").text( (data.csub != null ? data.csub : '-') ),
			$("<td style='text-align: left'>").text( (data.cremarks != null ? data.cremarks : '-') ),
		).appendTo('#detailTable > tbody')
	}

	function ShowPV(index, data) {
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Paid to '),
				$('<tH>').text('Referrence No.:'),
				$('<tH>').text('Bank:'),
				$('<tH>').text('Date:')

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.cpayrefno),
				$('<td>').text(data.bankname),
				$('<td>').text(data.ddate)
			).appendTo('#HeadDetail > tbody')

			$('<tr>').append(
				$('<tH>').text('Account No. '),
				$('<tH>').text('Account Title '),
				$('<tH>').text('Debit: '),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')


			$('<tr>').append(
				$('<tH>').text('APV No.'),
				$('<tH>').text('Referrence No.'),
				$('<tH>').text('Date'),
				$('<tH>').text('Amount'),
				$('<tH>').text('Payed'),
				$('<tH>').text('Total Owed'),
				$('<tH>').text('Amount Applied'),
				$('<tH>').text('DR Account'),
				$('<tH>').text('Account No.'),
			).appendTo('#subdetailTable > thead')
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.capvno != null ? data.capvno : '-') ),
			$("<td style='text-align: left'>").text( (data.crefrr != null ? data.crefrr : '-') ),
			$('<td>').text( (data.dapvdate != null ? data.dapvdate : '-') ),
			$('<td>').text( (data.namount != null ? parseFloat(data.namount).toFixed(2) : '-') ),
			$('<td>').text( (data.npayed != null ? parseFloat(data.npayed).toFixed(2) : '-') ),
			$('<td>').text( (data.nowed != null ? parseFloat(data.nowed).toFixed(2) : '-') ),
			$('<td>').text( (data.napplied != null ? parseFloat(data.napplied).toFixed(2) : '-') ),
			$("<td style='text-align: left'>").text( (data.cacctdesc != null ? data.cacctdesc : '-') ),
			$("<td style='text-align: left'>").text( (data.cacctno != null ? data.cacctno : '-') ),
		).appendTo('#subdetailTable > tbody')

		$.ajax({
			url: 'Controller/th_GLactivity_List.php',
			type: 'post',
			dataType: 'json',
			data: {ctranno: data.ctranno},
			async: false,
			success: function(res){
				res['data'].map((item, res) =>{
					$('<tr>').append(
						$("<td style='text-align: left'>").text(item.acctno),
						$("<td style='text-align: left'>").text(item.ctitle),
						$('<td>').text(parseFloat(item.ndebit).toFixed(2)),
						$('<td>').text(parseFloat(item.ncredit).toFixed(2)),
					).appendTo('#detailTable > thead')
				})
			}
		})
	
	}

	function ShowBD(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Deposit Account '),
				$('<tH>').text('Remarks:'),
				$('<tH>').text('Date:'),

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cacctdesc),
				$('<td>').text((data.cremarks != null ? data.cremarks : '-')),
				$('<td>').text(data.ddate),
			).appendTo('#HeadDetail > tbody')

			$('<tr>').append(
				$('<tH>').text('Account No. '),
				$('<tH>').text('Account Title '),
				$('<tH>').text('Debit: '),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')


			$('<tr>').append(
				$('<tH>').text('OR No.'),
				$('<tH>').text('Date'),
				$('<tH>').text('Payment Method'),
				$('<tH>').text('Amount'),
				$('<tH>').text('Remarks'),
				
			).appendTo('#subdetailTable > thead')
		}

		$('<tr>').append(
			$('<td>').text( (data.corno != null ? data.corno : '-') ),
			$('<td>').text( (data.dcutdate != null ? data.dcutdate : '-') ),
			$('<td>').text( (data.cpaymethod != null ? data.cpaymethod : '-') ),
			$('<td>').text( (data.namount != null ? parseFloat(data.namount).toFixed(2) : '-') ),
			$('<td>').text( (data.remark_t != null ? data.remark_t : '-') ),
		).appendTo('#subdetailTable > tbody')

		$.ajax({
			url: 'Controller/th_GLactivity_List.php',
			type: 'post',
			dataType: 'json',
			data: {ctranno: data.ctranno},
			async: false,
			success: function(res){
				res['data'].map((item, res) =>{
					$('<tr>').append(
						$("<td style='text-align: left'>").text(item.acctno),
						$("<td style='text-align: left'>").text(item.ctitle),
						$('<td>').text(parseFloat(item.ndebit).toFixed(2)),
						$('<td>').text(parseFloat(item.ncredit).toFixed(2)),
					).appendTo('#detailTable > thead')
				})
			}
		})
	}

	function ShowDR(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Customer '),
				$('<tH>').text('Salesman '),
				$('<tH>').text('Date:'),
				$('<tH>').text('Remarks'),
			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.csalesman),
				$('<td>').text(data.ddate),
				$('<td>').text(data.cremarks)
			).appendTo('#HeadDetail > tbody')


			$('<tr>').append(
				$('<tH>').text('Item'),
				$('<tH>').text('UOM'),
				$('<tH>').text('Factor'),
				$('<tH>').text('QTY'),
				
			).appendTo('#detailTable > thead')
		}

		$('<tr>').append(
			$('<td>').text( (data.citemdesc != null ? data.citemdesc : '-') ),
			$('<td>').text( (data.cunit != null ? data.cunit : '-') ),
			$('<td>').text( (data.nfactor != null ? parseFloat(data.nfactor).toFixed(0) : '-') ),
			$('<td>').text( (data.nqty != null ? parseFloat(data.nqty).toFixed(2) : '-') ),
		).appendTo('#detailTable > tbody')
	}
	

	function ShowAPADJ(index, data){
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Supplier '),
				$('<tH>').text('Date '),
				$('<tH>').text('Remarks:')

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.ddate),
				$('<td>').text(data.cremarks)
			).appendTo('#HeadDetail > tbody')


			$('<tr>').append(
				$('<tH>').text('Account No.'),
				$('<tH>').text('Account Title'),
				$('<tH>').text('Debit'),
				$('<tH>').text('Credit'),
				$('<tH>').text('Remarks'),
			).appendTo('#detailTable > thead')
		}

		$('<tr>').append(
				$("<td style='text-align: left'>").text((data.cacctno != null ? data.cacctno : '-')),
				$("<td style='text-align: left'>").text((data.ctitle != null ? data.ctitle : '-')),
				$('<tH>').text((data.ndebit != null ? parseFloat(data.ndebit).toFixed(2) : '-')),
				$('<tH>').text((data.ncredit != null ? parseFloat(data.ncredit).toFixed(2) : '-')),
				$("<td style='text-align: left'>").text((data.remark_t != null ? data.remark_t : '-')),
			).appendTo('#detailTable > tbody')
	}
</script>