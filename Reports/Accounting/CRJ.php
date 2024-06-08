<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "CashBook";

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

?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	
	<title>Cash Receipts Book</title>
</head>

<body style="padding:10px">
<h3><b>Company: <?=strtoupper($compname);  ?></b></h3>
<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
<h3><b>Kind of Book: CASH RECEIPTS BOOK</b></h3>
<h3><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h3>


<br>
<table class='table table-condensed' id='crjTable' border="0" align="center">
  <tr>
		<!--<th style=''>module</th>-->
    <th width="100" style="vertical-align:middle">Date</th>
    <th width="100" style="vertical-align:middle">Trans No.</th>
	<th width="100" style="vertical-align:middle">Receipt No.</th>
    <th style="vertical-align:middle">Account Credited</th>
    <th style="vertical-align:middle">Account No.</th>
    <th style="vertical-align:middle">Account Title</th>
    <!--<th style="vertical-align:middle">Description</th>-->
   	<th align="center" style="vertical-align:bottom; text-align: center !important" width="150"> Debit </th>
		<th align="center" style="vertical-align:bottom; text-align: center !important" width="150"> Credit </th>
  </tr>
	
  <tbody>
  </tbody>

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
	
	var totalCredit = 0.00;
	var totalDebit = 0.00;

	$(document).ready(function(){
		$.fn.digits = function(){ 
			return this.each(function(){ 
					$(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); 
			})
		}
		$.ajax({
			url: 'Controller/th_CDR_List.php',
			type: 'post',
			method: 'post',
			data: {
				dateto: '<?= $date1 ?>',
				datefrom: '<?= $date2 ?>'
			},
			dataType: 'json',
			async: false,
			success: function(res){

				if(res.valid){
					res['data'].map((item, key)=>{

						$dxkey = item.ndebit != 0.00 ? parseFloat(item.ndebit).toLocaleString(undefined, { maximumFractionDigits: 2, minimumFractionDigits: 2 }) : '' ;
						$cxkey = item.ncredit != 0.00 ? parseFloat(item.ncredit).toLocaleString(undefined, { maximumFractionDigits: 2, minimumFractionDigits: 2 }) : '' ;

						$("<tr id='tableContent' name='tableContent'>").append(
							//$("<td display:none; nowrap>").text(item.cmodule),
							$("<td nowrap>").text(item.ddate),
							$("<td nowrap>").text(item.ctranno),
							$("<td nowrap>").text(item.cornumber),
							$("<td nowrap>").text(item.cname),
							$("<td nowrap>").text(item.acctno),
							$("<td nowrap>").text(item.cacctdesc),
						//	$("<td nowrap>").text(),
						//	$("<td nowrap>").text(item.cdesc),
							$("<td style='text-align: right; !important' nowrap>").text($dxkey),
							$("<td style='text-align: right; !important' nowrap>").text($cxkey),
						).appendTo('#crjTable');

						totalCredit += parseFloat(item.ncredit);
						totalDebit += parseFloat(item.ndebit);
					})

					$("<tr id='tableContent' name='tableContent'>").append(
						$("<td colspan='5' align='right' nowrap>").html(" <b>TOTAL</b> "),
						$("<td style='text-align: right; border-top: 2px solid; font-weight: bold; !important' nowrap>").text(parseFloat(totalDebit).toLocaleString(undefined, { maximumFractionDigits: 2, minimumFractionDigits: 2 })),
						$("<td style='text-align: right; border-top: 2px solid; font-weight: bold; !important' nowrap>").text(parseFloat(totalCredit).toLocaleString(undefined, { maximumFractionDigits: 2, minimumFractionDigits: 2 })),
					).appendTo('#crjTable')


				} else {
					console.log('No Reference')
				}


			}
		})

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
							case 'OR':
								$('#modalTitle').text('Accounts Receivable Payment')
								console.log(item)
								ShowOR(index, item)
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
				$('<tH>').text('Tin:'),
				$('<tH>').text('Remarks:'),

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.cacctdesc),
				$('<td>').text(data.ddate),
				$('<td>').text(data.ctin),
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

	
</script>