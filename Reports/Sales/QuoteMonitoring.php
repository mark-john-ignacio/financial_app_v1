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
	}


	@$allqinfo = array();
	$sql = "select * From quote_t_info where compcode='$company'";
	$result=mysqli_query($con,$sql);
	if (mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			@$allqinfo[] =  $row;
		}
	}

	@$allrefx = array();
	$sql = "Select 'SI' as typ, x.creference as ctranno, GROUP_CONCAT(DISTINCT x.ctranno) as cref from sales_t x left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno where x.compcode='$company' and y.lcancelled=0 and y.lvoid=0 and IFNULL(x.creference,'') <> '' group by x.creference UNION ALL Select 'SO' as typ, x.creference as ctranno, GROUP_CONCAT(DISTINCT x.ctranno) as cref from so_t x left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno where x.compcode='001' and y.lcancelled=0 and y.lvoid=0 and IFNULL(x.creference,'') <> '' group by x.creference";
	$result=mysqli_query($con,$sql);
	if (mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			@$allrefx[$row['ctranno']] =  array('typ' => $row['typ'], 'ref' => $row['cref']);
		}
	}

	//echo "<pre>";
	//print_r(@$allrefx);
	//echo "</pre>";

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css?x=<?=time()?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link href="../../Bootstrap/css/NFont.css" rel="stylesheet">
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Quotation Monitoring</title>
</head>

<body style="padding:10px">
	<input type="hidden" value='<?=json_encode(@$allqinfo)?>' id="hdnqinfos"> 

<center>
<h3 class="nopadding"><?php echo strtoupper($compname);  ?></h3>
<h3 class="nopadding">Quotation Monitoring</h3>
<h4 class="nopadding">For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h4><br>
</center>

<br><br>
<table border="0" align="center" cellpadding="5px" id="BillTable" class="table table-sm table-hover">
	<tr>
		<td colspan="9"><b>BILLING</b></td>
	</tr>
  <tr>
    <th nowrap>Transaction No.</th>
	<th nowrap>Reference</th>
	<th nowrap>Prepared Date</th>
    <th nowrap>Due Date</th>
    <th nowrap colspan="2">Customer</th>
    <th nowrap>Recurr</th>
	<th nowrap>Sales Type</th>
	<th nowrap>VAT Type</th>
	<th nowrap style="text-align: right">Gross</th>
  </tr>
  
<?php

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$datefil = $_POST["seldtetp"];

	$postedtran = $_POST["selrpt"];

	$mainqry = "";
	$finarray = array();

	$qryposted = "";
	$qryposted2 = "";
	if($postedtran!==""){
		$qryposted = " and B.lapproved=".$postedtran."";
		$qryposted2 = " and A.lapproved=".$postedtran."";
	}


	$transctions = array();
	$sqlx = "Select B.*, C.cname
	From quote B
	left join customers C on B.compcode=C.compcode and B.ccode=C.cempid  
	where B.compcode='$company' and date(B.".$datefil.") between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.lcancelled=0 and B.lvoid=0 ".$qryposted." Order by B.dcutdate, B.ctranno";

	//echo $sqlx;

	$result=mysqli_query($con,$sqlx);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
		$transctions[] = $row['ctranno'];
	}

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;
	foreach($finarray as $row)
	{
		if($row['quotetype']=="billing"){
		
?>  
	<tr style="cursor: pointer">
		<td nowrap><a href="javascript:;" onclick="viewDets('BS','<?=$row['ctranno'];?>')"><?=$row['ctranno'];?></a></td>
		<td nowrap>

			<a href="javascript:;" onclick="viewDets('<?=@$allrefx[$row['ctranno']]['typ'];?>','<?=@$allrefx[$row['ctranno']]['ref'];?>')"><?=@$allrefx[$row['ctranno']]['ref'];?></a>

		</td>
		<td nowrap><?=date_format(date_create($row['ddate']),"m/d/Y");?></td>
		<td nowrap><?=date_format(date_create($row['dcutdate']),"m/d/Y");?></td>
		<td nowrap><?= $row['ccode'];?></td>
		<td nowrap><?=$row['cname'];?></td>   
		<td nowrap><?=strtoupper($row['crecurrtype']);?></td> 
		<td nowrap><?=$row['csalestype'];?></td>
		<td nowrap><?=$row['cvattype'];?></td>
		<td nowrap style="text-align: right"><?=number_format($row['ngross'],2)." ".$row['ccurrencycode']?>
		</td>
		
	</tr>
<?php 
		}
	}
?>

</table>

<br>

<table border="0" align="center" cellpadding="5px" id="BillTable" class="table table-sm table-hover">
	<tr>
		<td colspan="8"><b>QUOTATIONS</b></td>
	</tr>
  <tr>
    <th nowrap>Transaction No.</th>
	<th nowrap>Reference</th>
	<th nowrap>Prepared Date</th>
    <th nowrap>Effectivity Date</th>
    <th nowrap colspan="2">Customer</th>
	<th nowrap>Sales Type</th>
	<th nowrap>VAT Type</th>
	<th nowrap style="text-align: right">Gross</th>
  </tr>
  
<?php

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;
	foreach($finarray as $row)
	{
		if($row['quotetype']=="quote"){
		
?>  
	<tr style="cursor: pointer">
		<td nowrap><a href="javascript:;" onclick="viewDets('BS','<?=$row['ctranno'];?>')"><?=$row['ctranno'];?></a></td>
		<td nowrap>
			<a href="javascript:;" onclick="viewDets('<?=@$allrefx[$row['ctranno']]['typ'];?>','<?=@$allrefx[$row['ctranno']]['ref'];?>')"><?=@$allrefx[$row['ctranno']]['ref'];?></a>
		</td>
		<td nowrap><?=date_format(date_create($row['ddate']),"m/d/Y");?></td>
		<td nowrap><?=$row['dcutdate'];?></td>
		<td nowrap><?=$row['ccode'];?></td>
		<td nowrap><?=$row['cname'];?></td>   
		<td nowrap><?=$row['csalestype'];?></td>
		<td nowrap><?=$row['cvattype'];?></td>
		<td nowrap style="text-align: right"><?=number_format($row['ngross'],2)." ".$row['ccurrencycode']?>
		</td>
		
	</tr>
<?php 
		}
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
			let ctranno = $(this).closest('#tableContent').find('td:eq(1)').text();

			

		});
	});

	function clearTable(table){
		$(table +' thead').empty();
		$(table + ' tbody').empty();
	}

	function viewDets(modules,ctranno){
		clearTable("#HeadDetail")
		clearTable('#detailTable')
		clearTable('#subdetailTable')

		$.ajax({
			url: '../Accounting/Controller/TBal_Controller.php',
			type: 'post',
			dataType: 'json',
			data: {
				module: modules,
				ctranno: ctranno,
				captypex: ""
			},
			success: function(res){

				//console.log(res);
				$('#detailModal').modal('show')

				var sample = res.data;

				sample.map((item, index) => {
					switch(modules){						
						case 'SI':
							$('#modalTitle').text('Sales Invoice')
							ShowSI(index, item)
							//console.log(item)
							break;	
						case 'SO':
							$('#modalTitle').text('Sales Order')
							ShowSO(index, item)
							//console.log(item)
							break;													
						case 'BS':
							$('#modalTitle').text('Quotation Details')
							ShowBS(index, item)
							console.log(item)
							break;
						default: 
							break;
					}
					
				})
				
			}
		})
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
				url: '../Accounting/Controller/th_GLactivity_List.php',
				type: 'post',
				dataType: 'json',
				data: {ctranno: data.ctranno},
				async: false,
				success: function(res){
					//console.log(res);
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

	function ShowSO(index, data){
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
				url: '../Accounting/Controller/th_GLactivity_List.php',
				type: 'post',
				dataType: 'json',
				data: {ctranno: data.ctranno},
				async: false,
				success: function(res){
					//console.log(res);
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

	function ShowBS(index, data){
		console.log(index);
		if(index <= 0){

			$('<tr>').append(
				$('<tH>').text('Transaction No.: '),
				$('<tH>').text('Customer: '),
				$('<tH>').text('Type'),
				$('<tH>').text('Date:'),
				$('<tH>').text('Recurr Type'),

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.csalestype),
				$('<td>').text(data.ddate),
				$('<td>').text((data.crecurrtype != null ? data.crecurrtype : '-')),
			).appendTo('#HeadDetail > tbody')

			$('<tr>').append(
				$('<tH>').text('Bill Period'),
				$('<tH>').text('Description'),
				$('<tH>').text('VAT SALES'),
				$('<tH>').text('VAT AMOUNT'),
				$('<tH>').text('TOTAL AMOUNT'),
			).appendTo('#subdetailTable > thead')
		}

		

		var billperd = "";
		var billdesc = "";
		$zxc = $("#hdnqinfos").val();		
		$.each(jQuery.parseJSON($zxc), function() {
			
			if(this['ctranno']==data.ctranno ){
				//console.log(this['ctranno']+"=="+data.ctranno +"&&"+ this['nrefident']+"=="+data.nident);
				if(this['nrefident']==data.nident){
					if(billperd!=""){
						billperd + "\n";
					}
					if(billdesc!=""){
						billdesc + "\n";
					}
					billperd = billperd +  this['cfldnme'];
					billdesc = billdesc +  this['cvalue'];
				}
			}				
		});

		if(data.cvattyp=="VatEx"){							
			var $nvatamt = data.namount;
			var $nvat=0;
			var $ntotamt = data.namount;
		}else{
			var $ntotamt = data.namount;
			var $nvatamt = parseFloat(data.namount) / (1 + (parseFloat(data.nrate)/100));
			var $nvat = $ntotamt - $nvatamt;											
		}

		$('<tr>').append(
			$("<td style='text-align: center'>").html( ((billperd != null && billperd !="") ? billperd : '') ),
			$("<td style='text-align: center'>").html( (data.citemdesc != null ? data.citemdesc : '-') + ((billdesc != null && billdesc !="") ? "<br>" + billdesc : '') ),
			$('<td>').text( ($nvatamt != null ? settodecl($nvatamt) : '-') ),
			$('<td>').text( ($nvat != null ? settodecl($nvat) : '-') ),
			$('<td>').text( ($ntotamt != null ? settodecl($ntotamt) : '-') ),
		).appendTo('#subdetailTable > tbody')

	}

	function settodecl(xyz){
		xyz = parseFloat(xyz);
		return xyz.toLocaleString('en-US', {minimumFractionDigits: 2,maximumFractionDigits: 2});
	}
</script>