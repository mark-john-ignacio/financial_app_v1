<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "GLedger";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	require_once "../../Model/helper.php";

	$company = $_SESSION['companyid'];
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
	
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

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css?x=<?=time()?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link href="../../Bootstrap/css/NFont.css" rel="stylesheet">
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	
	<title>General Ledger</title>
</head>

<body style="padding:10px">
<h3><b>Company: <?=strtoupper($compname);  ?></b></h3>
<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
<h3><b>Kind of Book: GENERAL LEDGER BOOK</b></h3>
<h3><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h3>

<br><br>

<?php

	$jqy = "";
	if($_POST['selbanks']!=""){
		$jqy = " and A.acctno = '".$_POST['selbanks']."' ";
	}

	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit, C.captype, A.crefno
	From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
	left join apv C on A.compcode=C.compcode and A.ctranno=C.ctranno
	Where A.compcode='$company' and A.ddate between STR_TO_DATE('".$_POST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_POST['date2']."', '%m/%d/%Y')".$jqy."
	Order By A.acctno, A.dpostdate, A.ctranno, CASE WHEN (A.ndebit <> 0) THEN 1 ELSE 0 END desc, A.acctno";

	$arracctnos = array();
	$arrallqry = array();
	$result=mysqli_query($con,$sql);			
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
	$arrallqry[] = $row;
	$arracctnos[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
	}

	$arrundrs = array_intersect_key( $arracctnos , array_unique( array_map('serialize' , $arracctnos ) ) );

	$cntr = 0;
	$dcurrentacct = "";
	foreach($arrundrs as $rowxz){
		$cntr++;
		if($cntr>1){
			echo "<br><br>";
		}

		$dcurrentacct = $rowxz['cacctno'];
?>

<table class='table' width="100%" border="0"  cellpadding = "3" class="tbl-serate">
	<tr>
		<th colspan="8">
			<table width="100%" border="0" align="center" cellpadding = "3">
				<tr>
					<td width="150px"><b>Acct ID:</b> <?=$rowxz['cacctno']?></td>
					<td><b>Description:</b> <?=$rowxz['cacctdesc']; ?></td>
				</tr>
			</table>
		</th>
	</tr>
  <tr>
  	<th style="text-align:left" width="100px">Date</th>
	<th style="text-align:left" width="100px">Reference</th>
	<th style="text-align:left">Description</th>
	<th style="text-align:left" width="150px">Customer/Supplier</th>
	<th style="text-align:left" width="100px">Account Code</th>
	<th style="text-align:left" width="150px">Account Title</th>
	<th style="text-align:right" width="150px">Debit</th>
	<th style="text-align:right" width="150px">Credit</th>
		<!-- <th>Reference</th>
		<th width="100px">Date</th>
    <th style="text-align:right" width="150px">Debit</th>
    <th style="text-align:right" width="150px">Credit</th> -->
  </tr>

 <?php
	$totdebit = 0;
	$totcredit = 0;
	$cntr = 0;
	$xv = 0;
	foreach($arrallqry as $drow)
	{
		if($drow['acctno']==$rowxz['cacctno']){
			$cntr++;

			$totdebit = $totdebit + floatval($drow['ndebit']);
			$totcredit = $totcredit + floatval($drow['ncredit']);
	?>
   <tr id="tableContent" name="tableContent">
   		<td style="display: none;"><?= $drow['cmodule'] ?></td>
		<td><?=date_format(date_create($drow['ddate']), "d-M-y")?></td>
		<td><a href='javascript:;'><?=$drow['ctranno']?></a></td>

		<?php 
			$ctranno = $drow['ctranno'];
			$controller = ReadDescription($drow['cmodule'], $ctranno, $company);
			$result = mysqli_query($con, $controller);
			$descrow = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
		
		<td><?=(@$descrow['typ'] != null ? @$descrow['typ'] : '-')?></td>


		<?php 
			$ctranno = $drow['ctranno'];
			$controller = CustomerNames($drow['cmodule'], $ctranno, $company, $drow['crefno']);
			$result = mysqli_query($con, $controller);
			$namerow = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>

		<td><?=(@$namerow['cname'] != null ? @$namerow['cname'] : '-')?></td>
		<td><?=(@$drow['acctno'] != null ? @$drow['acctno'] : '-')?></td>
		<td><?=(@$drow['cacctdesc'] != null ? @$drow['cacctdesc'] : '-')?></td>
  		<td style="text-align:right;"><?=(floatval($drow['ndebit'])<>0) ? number_format(floatval($drow['ndebit']), 2) : ""?></td>
    	<td style="text-align:right"><?=(floatval($drow['ncredit'])<>0) ? number_format(floatval($drow['ncredit']), 2) : ""?></td>
		<!--<td style="text-align:right">
			<?php
					//$xv = getbalance($cntr, $xv, $drow['ndebit'], $drow['ncredit']);
					//echo number_format(floatval($xv), 2);
			?>
		</td>-->

		<td style="display: none;"><?= $drow['captype'] ?></td>
  </tr>
	<?php
		}
	}
	?>

	<tr>
		<td style="text-align:right;" colspan="6"><b>Total <?=$dcurrentacct?></b></td>
  		<td style="text-align:right; border-bottom-style: double; border-top: 1px solid"><b><?=(floatval($totdebit)<>0) ? number_format(floatval($totdebit), 2) : ""?></b></td>
    	<td style="text-align:right; border-bottom-style: double; border-top: 1px solid"><b><?=(floatval($totcredit)<>0) ? number_format(floatval($totcredit), 2) : ""?></b></td>
		<!--<td>
			&nbsp;
		</td>-->
  </tr>

 
</table>

<?php
	}
?>
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">

					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h5><b><i><span id='modalTitle'></span></i></b></h5>
					
				</div>
				<div class="modal-body" style="height: 100%; overflow: auto">

					<table class='table' id="HeadDetail" border="1" bordercolor="#CCCCCC" width="100%" style="overflow: auto;">
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

			var captypex = $(this).closest('#tableContent').find('td:eq(8)').text();

			clearTable("#HeadDetail")
			clearTable('#detailTable')
			clearTable('#subdetailTable')
			console.log(modules)
			$.ajax({
				url: 'Controller/TBal_Controller.php',
				type: 'post',
				dataType: 'json',
				data: {
					module: modules,
					ctranno: ctranno,
					captypex: captypex
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
								if(captypex=="Others" || captypex=="PettyCash"){
									ShowAPV_Others(index, item)
								}else{
									ShowAPV(index, item)
								}
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
							$('<td>').text(settodecl(item.ndebit)),
							$('<td>').text(settodecl(item.ncredit)),
						).appendTo('#detailTable > thead')
					})
				}
			})
		} 

		$('<tr>').append(
			$('<td>').text( (data.csalesno != null ? data.csalesno : '-') ),
			$('<td>').text( (data.cdesc != null ? data.cdesc : '-') ),
			$('<td>').text( (data.namount != null ? settodecl(data.namount) : '-') ),
			$('<td>').text( (data.ndiscount != null ? settodecl(data.ndiscount) : '-') ),
			$('<td>').text( (data.ncm != null ? settodecl(data.ncm) : '-') ),
			$('<td>').text( (data.npayment != null ? settodecl(data.npayment) : '-') ),
			$('<td>').text( (data.ctaxcode != null ? data.ctaxcode : '-') ),
			$('<td>').text( (data.nvat != null ? settodecl(data.nvat) : '-') ),
			$('<td>').text( (data.nnet != null ? settodecl(data.nnet) : '-') ),
			$('<td>').text( (data.cewtcode != "" ? data.cewtcode : '-') ),
			$('<td>').text( (data.newtgiven != null ?  settodecl(data.newtgiven) : '-' ) ),
			$('<td>').text( (data.newtamt != null ?  settodecl(data.newtamt) : '-' ) ),
			$('<td>').text( (data.ndue != null ?  settodecl(data.ndue) : '-' ) ),
			$('<td>').text( (data.napplied != null ?  settodecl(data.napplied) : '-' ) ),
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
								$('<td>').text(settodecl(item.ndebit)),
								$('<td>').text(settodecl(item.ncredit)),
						).appendTo('#detailTable > thead')
					})
				}
			})
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.citemdesc != null ? data.citemdesc : '-') ),
			$('<td>').text( (data.cewtcode != "" ? data.cewtcode : '-') ),
			$('<td>').text( (data.ctaxcode != null ? data.ctaxcode : '-') ),
			$('<td>').text( (data.cmainunit != null ? data.cmainunit : '-') ),
			$('<td>').text( (data.nqty != null ? settodecl(data.nqty) : '-') ),
			$('<td>').text( (data.nprice != null ? settodecl(data.nprice) : '-') ),
			$('<td>').text( (data.ndiscount != null ? settodecl(data.ndiscount) : '-') ),
			$('<td>').text( (data.nbaseamount != null ? settodecl(data.nbaseamount) : '-') ),
			$('<td>').text( (data.namount != null ? settodecl(data.namount) : '-') ),
		).appendTo('#subdetailTable > tbody')
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
								$('<td>').text(settodecl(item.ndebit)),
								$('<td>').text(settodecl(item.ncredit)),
						).appendTo('#detailTable > thead')
					})
				}
			})
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( (data.citemdesc != null ? data.citemdesc : '-') ),
			$('<td>').text( (data.cunit != null ? data.cunit : '-') ),
			$('<td>').text( (data.nqty != null ? settodecl(data.nqty) : '-') ),
			$('<td>').text( (data.nprice != null ? settodecl(data.nprice) : '-') ),
			$('<td>').text( (data.ndiscount != null ? settodecl(data.ndiscount) : '-') ),
			$('<td>').text( (data.nbaseamount != null ? settodecl(data.nbaseamount) : '-') ),
			$('<td>').text( (data.namount != null ? settodecl(data.namount) : '-') ),
		).appendTo('#subdetailTable > tbody')

		
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
				$('<td>').text(data.cpaymentfor)
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
				$('<td>').text( (data.namount != null ? settodecl(data.namount) : '-') ),
				$('<td>').text( (data.napcm != null ? settodecl(data.napcm) : '-') ),
				$('<td>').text( (data.napdisc != null ? settodecl(data.napdisc) : '-') ),
				$('<td>').text( (data.cvatcode != null ? data.cvatcode : '-') ),
				$('<td>').text( (data.nvatrate != null ? settodecl(data.nvatrate) : '-') ),
				$('<td>').text( (data.nvatamt != null ? settodecl(data.nvatamt) : '-') ),
				$('<td>').text( (data.nnet != null ? settodecl(data.nnet) : '-') ),
				$('<td>').text( (data.cewtcode != null ? data.cewtcode : '-') ),
				$('<td>').text( (data.newtrate != null ? settodecl(data.newtrate) : '-') ),
				$('<td>').text( (data.newtamt != null ? settodecl(data.newtamt) : '-') ),
				$('<td>').text( (data.ngross != null ? settodecl(data.ngross) : '-') ),
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
								$('<td>').text( (item.ndebit != null ? settodecl(item.ndebit) : '-') ),
								$('<td>').text( (item.ncredit != null ? settodecl(item.ncredit) : '-') ),
							).appendTo('#detailTable > tbody')
						}
					})
					
				} else {
					console.log('no reference')
				}
				
			}
		})
	}

	function ShowAPV_Others(index, data){
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
				$('<td>').text(data.cpaymentfor)
			).appendTo('#HeadDetail > tbody')

			$('<tr>').append(
				$('<tH>').text('Account No.'),
				$('<tH>').text('Title'),
				$('<tH>').text('Debit'),
				$('<tH>').text('Credit'),
			).appendTo('#detailTable > thead')
		}

		$('<tr>').append(
			$("<td style='text-align: left'>").text( data.cacctno),	
			$("<td style='text-align: left'>").text( (data.ctitle != null ? data.ctitle: '-') ),
			$('<td>').text( (data.ndebit != null ? settodecl(data.ndebit) : '-') ),
			$('<td>').text( (data.ncredit != null ? settodecl(data.ncredit) : '-') ),
		).appendTo('#detailTable > tbody')
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
			$('<td>').text( (data.ndebit != null ? settodecl(data.ndebit) : '-') ),
			$('<td>').text( (data.ncredit != null ? settodecl(data.ncredit) : '-') ),
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
			$('<td>').text( (data.ndebit != null ? settodecl(item.ndebit) : '-') ),
			$('<td>').text( (data.ncredit != null ? settodecl(item.ncredit) : '-') ),
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
			$('<td>').text( (data.namount != null ? settodecl(data.namount) : '-') ),
			$('<td>').text( (data.npayed != null ? settodecl(data.npayed) : '-') ),
			$('<td>').text( (data.nowed != null ? settodecl(data.nowed) : '-') ),
			$('<td>').text( (data.napplied != null ? settodecl(data.napplied) : '-') ),
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
						$('<td>').text(settodecl(item.ndebit)),
						$('<td>').text(settodecl(item.ncredit)),
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
			$('<td>').text( (data.namount != null ? settodecl(data.namount) : '-') ),
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
						$('<td>').text(settodecl(item.ndebit)),
						$('<td>').text(settodecl(item.ncredit)),
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
			$('<td>').text( (data.nfactor != null ? settodecl(data.nfactor) : '-') ),
			$('<td>').text( (data.nqty != null ? settodecl(data.nqty) : '-') ),
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
				$('<tH>').text((data.ndebit != null ? settodecl(data.ndebit) : '-')),
				$('<tH>').text((data.ncredit != null ? settodecl(data.ncredit) : '-')),
				$("<td style='text-align: left'>").text((data.remark_t != null ? data.remark_t : '-')),
			).appendTo('#detailTable > tbody')
	}

	function settodecl(xyz){
		xyz = parseFloat(xyz);
		return xyz.toLocaleString('en-US', {minimumFractionDigits: 2,maximumFractionDigits: 2});
	}
</script>