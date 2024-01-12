<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');

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

	$findr = array();
	$acctslist = array();
	$resDR=mysqli_query($con,"Select A.ccode, A.cname, A.cacctno, B.cacctdesc From bank A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.cstatus='ACTIVE'");
	$findr = array();
	while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
		$findr[] = $row;
		$acctslist[] = $row['cacctno'];
	}

	//echo "<pre>";
	//print_r($findr);
	//echo "</pre>";

	$AmountTotBalance = 0;

	//for begginning balance
	$begbalaz = array();
	
	$resBeg=mysqli_query($con,"Select acctno, sum(ndebit) as ndebit, sum(ncredit) as ncredit from glactivity where compcode='$company' and ddate < STR_TO_DATE('$date1', '%m/%d/%Y')  and acctno in ('".implode("','",$acctslist)."') group by acctno order by acctno");
	while($row = mysqli_fetch_array($resBeg, MYSQLI_ASSOC)){
		$begbalaz[$row['acctno']] = $row;
	}

	//echo "<pre>";
	//print_r($begbalaz);
	//echo "</pre>";

	//for transactions
	$transbalaz = array();
	$resBeg=mysqli_query($con,"Select cmodule, ctranno, ddate, acctno, ndebit, ncredit from glactivity where compcode='$company' and ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and acctno in ('".implode("','",$acctslist)."') order by ddate, acctno");

	while($row = mysqli_fetch_array($resBeg, MYSQLI_ASSOC)){
		$transbalaz[] = $row;
	}

	//echo "<pre>";
	//print_r($begbalaz);
	//echo "</pre>";
	
	function get_particulars($xmodule,$xtranno){
		global $con;
		global $company;

		$sql = "";

		switch($xmodule){
			case "JE":
				$sql = "Select cmemo as cparticulars from journal where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
			case "OR":
				$sql = "Select cremarks as cparticulars from receive where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
			case "APV":
				$sql = "Select cpaymentfor as cparticulars from apv where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
			case "PV":
				$sql = "Select cparticulars from paybill where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
		}
		
		$cparticulars = "";
		if($sql!=""){
			$res=mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
				$cparticulars = $row['cparticulars'];
			}
		}

		return $cparticulars;
	}
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

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cash Position</title>
</head>

<body style="padding:10px">
<h3><b>Company: <?=strtoupper($compname);  ?></b></h3>
<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
<h3><b>Kind of Book: CASH POSITION</b></h3>
<h3><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h3>

<br>

<table width="100%" border="1" align="center" cellpadding="5px" class="table table-small table-hover">
	<thead>
		<tr>
			<th nowrap rowspan="2" style="vertical-align: middle">Date</th>
			<th nowrap rowspan="2" style="vertical-align: middle">Reference</th>
			<th nowrap rowspan="2" style="vertical-align: middle">Particulars</th>
			<?php
				$xdbalance = array();
				foreach($findr as $rocut){
					$xdbalance[$rocut['cacctno']] = 0;
			?>
				<td nowrap align="center" colspan="3"><b><?=$rocut['cname']?></b><br><small><?=$rocut['cacctno']?> - <?=$rocut['cacctdesc']?></small></td>
			<?php
				}
			?>
			<th nowrap rowspan="2" style="vertical-align: middle">Total Balance</th>
		</tr>

		<tr>
			<?php
				foreach($findr as $rocut){
			?>
				<td nowrap align="center"><b>Debit</b></td>
				<td nowrap align="center"><b>Credit</b></td>
				<td nowrap align="center"><b>Balance</b></td>
			<?php
				}
			?>
		</tr>
	</thead>
	<tbody>
		<!-- for balance Row -->
		<tr>
			<td nowrap>&nbsp;</td>
   			<td nowrap>&nbsp;</td>
    		<td nowrap><b>Beginning Balance</b></td>
			<?php
				foreach($findr as $rocut){
			?>
				<td nowrap align="right">&nbsp;</td>
				<td nowrap align="right">&nbsp;</td>
				<td nowrap align="right" style="font-weight: bold">
					<?php
						if(isset($begbalaz[$rocut['cacctno']])){
							$xtot = floatval($begbalaz[$rocut['cacctno']]['ndebit']) - floatval($begbalaz[$rocut['cacctno']]['ncredit']);
							echo number_format($xtot,2);

							$xdbalance[$rocut['cacctno']] = $xtot;
							$AmountTotBalance = $AmountTotBalance + $xtot;
						}else{
							echo "0.00";
							$xdbalance[$rocut['cacctno']] = 0;
						}
					?>
				</td>
			<?php
				}
			?>
			<td nowrap align="right" style="font-weight: bold"><?=number_format($AmountTotBalance,2)?></td>
		</tr>
<?php
	$valdebit = 0;
	$valcredit = 0;
	$AmountTotBalance = 0;
	foreach($transbalaz as $row)
	{
	
?>  
  <tr id="tableContent" name="tableContent" style="cursor: pointer">
    <td nowrap align="center"><?=date_format(date_create($row['ddate']),"m/d/Y");?></td>
    <td nowrap data-mod="<?=$row['cmodule']?>"><?=$row['ctranno'];?></td>
    <td nowrap><?=get_particulars($row['cmodule'],$row['ctranno']);?></td>
	<?php
		$xcdebit = 0;
		$xccredit = 0;
		foreach($findr as $rocut){
			
			if($row['acctno']==$rocut['cacctno']){
				$valdebit = number_format($row['ncredit'],2);
				$valcredit = number_format($row['ndebit'],2);
				$xdbalance[$rocut['cacctno']] = $xdbalance[$rocut['cacctno']] + floatval($row['ndebit']) - floatval($row['ncredit']);
			}else{
				$valdebit = "";
				$valcredit = "";
			}
	?>
		<td nowrap align="right"><?=($valdebit==0) ? "" : $valdebit?></td>
		<td nowrap align="right"><?=($valcredit==0) ? "" : $valcredit?></td>
		<td nowrap align="right">
			<?php
				echo number_format(floatval($xdbalance[$rocut['cacctno']]),2);
				$AmountTotBalance = $AmountTotBalance + floatval($xdbalance[$rocut['cacctno']]);
			?>
		</td>
	<?php
		}
	?>
    <td nowrap align="right" style="font-weight: bold"><?=number_format($AmountTotBalance,2)?></td>
		
  </tr>
<?php 
		$AmountTotBalance = 0;
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
			let modules = $(this).closest('#tableContent').find('td:eq(1)').attr("data-mod");
			let ctranno = $(this).closest('#tableContent').find('td:eq(1)').text();

			clearTable("#HeadDetail")
			clearTable('#detailTable')
			clearTable('#subdetailTable')

			if(modules=="SI"){
				modules = ctranno.substring(0,2);
			}
			
			$.ajax({
				url: 'Controller/TBal_Controller.php',
				type: 'post',
				dataType: 'json',
				data: {
					module: modules,
					ctranno: ctranno
				},
				success: function(res){

					console.log(res);
					$('#detailModal').modal('show')

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
								//console.log(item)
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
								$('<td>').text(parseFloat(item.ndebit).toFixed(2)),
								$('<td>').text(parseFloat(item.ncredit).toFixed(2)),
						).appendTo('#detailTable > thead')
					})
				}
			})
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