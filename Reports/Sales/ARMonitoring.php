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
	<title>AR Monitoring</title>
</head>

<body style="padding:10px">
	<input type="hidden" value='<?=json_encode(@$allqinfo)?>' id="hdnqinfos"> 

<center>
<h3 class="nopadding"><?php echo strtoupper($compname);  ?></h3>
<h3 class="nopadding">AR Monitoring</h3>
<h4 class="nopadding">For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h4><br>
</center>

<br><br>
<table border="0" align="center" cellpadding="5px" id="MyTable" class="table table-sm table-hover">
  <tr>
    <th nowrap>Type</th>
    <th nowrap>Transaction No.</th>
    <th nowrap>Reference</th>
    <th nowrap>Date</th>
    <th nowrap colspan="2">Customer</th>
    <th nowrap align="right">Vatable Sales</th>
	<th nowrap align="right">VAT%</th>
	<th nowrap align="right">VAT Amount</th>
	<th nowrap align="right">Sales Amount</th>
	<th nowrap style="text-align: center">EWT</th>
	<th nowrap align="right">EWT Amount</th>
	<th nowrap align="right">AR Balance<br>Net of TAX</th>
	<th nowrap align="right">Amount Collected</th>
	<th nowrap align="right">Balance</th>
  </tr>
  
<?php

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$trantype = "";
	if(isset($_POST['seltrantype'])){
		$trantype=$_POST['seltrantype'];
	}else{
		$trantype="Trade";
	}

	$postedtran = $_POST["selrpt"];

	$mainqry = "";
	$finarray = array();

	$qryposted = "";

	if($postedtran==1 || $postedtran==0){
		$qryposted = " and B.lcancelled=0 and B.lvoid=0 and B.lapproved=".$postedtran."";
	}elseif($postedtran==2){
		$qryposted = " and (B.lcancelled=1 or B.lvoid=1)";
	}
		

		$transrefDR = array();
		$result=mysqli_query($con,"Select ctranno, GROUP_CONCAT(DISTINCT creference) as cref from sales_t where compcode='$company' group by ctranno");
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$transrefDR[$row['ctranno']] = $row['cref'];
		}

		@$arrpaymnts = array();
		$sqlpay = "select X.* from receipt_sales_t X left join receipt B on X.compcode=B.compcode and X.ctranno=B.ctranno where X.compcode='$company' and B.lcancelled = 0 and B.lvoid=0 order By X.csalesno, B.ddate";
		$respay = mysqli_query ($con, $sqlpay);
		while($rowardj = mysqli_fetch_array($respay, MYSQLI_ASSOC)){
			@$arrpaymnts[] = $rowardj;
		}

		$transctions = array();
		$sqlx = "Select A.type, A.ctranno, A.ccode, A.cname, A.cacctid, A.cacctdesc, IFNULL(A.ctaxcode,'') as ctaxcode, A.nrate, IFNULL(A.cewtcode,'') as cewtcode, A.newtrate, A.dcutdate, SUM(ROUND(A.namountfull,2)) as ngross, SUM(ROUND(A.namount,2)) as cm, SUM(nvatgross) as nvatgross, (SUM(ROUND(A.namountfull,2)) - SUM(ROUND(A.namount,2)) - SUM(nvatgross)) as vatamt, A.lcancelled, A.lvoid, A.lapproved
		From (
			Select 'SI' as type, A.ctranno, B.ccode, COALESCE(C.ctradename, C.cname) as cname, A.citemno, ((A.nqtyreturned) * (A.nprice-A.ndiscount)) as namount, (A.nqty * (A.nprice-A.ndiscount)) as namountfull, B.dcutdate, D.cacctid, D.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, IFNULL(A.newtrate,0) as newtrate, CASE WHEN IFNULL(A.nrate,0) <> 0 THEN ROUND(((A.nqty-A.nqtyreturned)*(A.nprice-A.ndiscount))/(1 + (A.nrate/100)),2) ELSE A.namount END as nvatgross, B.lcancelled, B.lvoid, B.lapproved
		From sales_t A 
		left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
		left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
		left join accounts D on C.compcode=D.compcode and C.cacctcodesales=D.cacctno 
		left join wtaxcodes E on A.compcode=E.compcode and A.cewtcode=E.ctaxcode 
		where A.compcode='$company' and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$qryposted."
		
		UNION ALL

		Select 'BS' as type, A.ctranno, B.ccode, COALESCE(C.ctradename, C.cname) as cname, '' as citemno, 0 as namount, A.namount as namountfull, B.dcutdate, '' as cacctid, '' as cacctdesc, CASE WHEN B.cvattype='VatIn' THEN F.ctaxcode ELSE '' END as ctaxcode, CASE WHEN B.cvattype='VatIn' THEN F.nrate ELSE '' END as nrate, '' as cewtcode, 0 as newtrate, 	
						CASE 
							WHEN B.cvattype='VatIn'
							THEN 
								ROUND((A.nqty*A.nprice)/(1 + (F.nrate/100)),2)
							ELSE 
								A.namount 
							END as nvatgross, B.lcancelled, B.lvoid, B.lapproved
		From quote_t A
		left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno
		left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
		left join items E on A.compcode=E.compcode and A.citemno=E.cpartno 
		left join taxcode F on E.compcode=F.compcode and E.ctaxcode=F.ctaxcode
		left join (
			Select Y.creference From sales_t Y left join sales X on Y.compcode=X.compcode and Y.ctranno=X.ctranno 
			where Y.compcode='$company' and X.lcancelled=0 and X.lvoid=0
		) G on A.ctranno=G.creference
		where A.compcode='$company' and B.quotetype='billing' and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$qryposted." and IFNULL(G.creference,'') = ''

		) A
		Group By A.ctranno, A.ccode, A.cname, A.cacctid, A.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, A.newtrate, A.dcutdate, A.lcancelled, A.lvoid, A.lapproved
		order by A.dcutdate, A.ctranno";

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

	$ARBal = 0;
	$CollBal = 0;
	$BalBal = 0;
	foreach($finarray as $row)
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
			$invval = 
			$remarks = 
			$ccode =
			$xtypx =  
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";

			$ewtcode = 0;
			$vx = explode(";",$row['newtrate']);
			foreach($vx as $vx2){
				if($vx2!=""){
					$ewtcode = $ewtcode + floatval($vx2);
				}
			}

			if($row['lcancelled']==1 || $row['lvoid']==1){
				$xycolor = "BlanchedAlmond";
			}else{
				if($row['lapproved']==1){
					$xycolor = "White";
				}else{
					$xycolor = "LightCyan";
				}
			}
			
		
?>  
	<tr style="cursor: pointer; background-color:<?=$xycolor?> !important">
		<td nowrap><?=$row['type'];?></td>
		<td nowrap><a href="javascript:;" onclick="viewDets('<?=$row['type'];?>','<?=$row['ctranno'];?>')"><?=$row['ctranno'];?></a></td>
		<td nowrap>
			<?php
				if($row['type']=="SI") {
			?>
				<a href="javascript:;" onclick="viewDets('BS','<?=$transrefDR[$row['ctranno']];?>')"><?=$transrefDR[$row['ctranno']];?></a>
			<?php
				}
			?>
		</td>
		<td nowrap><?=$dateval;?></td>
		<td nowrap><?= $row['ccode'];?></td>
		<td nowrap><?=$row['cname'];?></td>   
		<td nowrap style="text-align: right"><?=(floatval($row['nvatgross'])!=0) ? number_format($row['nvatgross'],2) : ""?></td> 
		<td nowrap style="text-align: center"><?=(intval($row['nrate'])!=0 && intval($row['nrate'])!="") ? number_format($row['nrate'])."%" : ""?></td>
		<td nowrap style="text-align: right">
			<?php
				if(intval($row['nrate'])!=0 && intval($row['nrate'])!=""){
					if(floatval($row['vatamt'])!=0) {
						echo number_format($row['vatamt'],2);
					}
				}
			?>
		</td>
		<td nowrap style="text-align: right"><?=(floatval($row['ngross'])!=0) ? number_format($row['ngross'],2) : ""?></td>		
		<td nowrap style="text-align: center"><?=(intval($ewtcode)!=0 && intval($ewtcode)!="") ? number_format($ewtcode)."%" : ""?></td>
		<td nowrap style="text-align: right">
			<?php
				$phpewtamt = 0;

				if(intval($ewtcode)!=0 && intval($ewtcode)!=""){
					$phpewtamt = floatval($row['nvatgross']) * (floatval($ewtcode)/100);
				}

				echo (floatval($phpewtamt)!=0) ? number_format($phpewtamt,2) : "";
			?>
		</td>
		<td nowrap style="text-align: right">
			<?php
				$netvatamt = floatval($row['ngross']) - floatval($phpewtamt);
				echo number_format($netvatamt,2);

				$ARBal += floatval($netvatamt);
			?>
		</td>
		<td nowrap style="text-align: right">
			<?php
				$npay = 0;
				$cntofist = 0;
				foreach(@$arrpaymnts as $rxpymnts){
					if($row['ctranno']==$rxpymnts['csalesno'] && $row['ctaxcode']==$rxpymnts['ctaxcodeorig'] && $row['cewtcode']==$rxpymnts['cewtcodeorig']){
						$cntofist++;
						
						if($cntofist==1){
							$ntotal = floatval($rxpymnts['ndue']) - floatval($rxpymnts['napplied']);
						}

						$npay = $npay + floatval($rxpymnts['napplied']);
					}
				}

				echo (floatval($npay)!=0) ? number_format($npay,2) : "";
				$CollBal += floatval($npay);
			?>
		</td>
		<td nowrap style="text-align: right">
			<?php
				$nbalace = floatval($netvatamt) - floatval($npay);

				echo (floatval($nbalace)!=0) ? number_format($nbalace,2) : "";

				$BalBal += floatval($nbalace);
			?>
		</td>
	</tr>
<?php 		
		
	}
?>

    <tr class='rptGrand'>
    	<td colspan="12" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?=(floatval($ARBal)!=0) ? number_format($ARBal,2) : ""?></b></td>
		<td align="right"><b><?=(floatval($CollBal)!=0) ? number_format($CollBal,2) : ""?></b></td>
		<td align="right"><b><?=(floatval($BalBal)!=0) ? number_format($BalBal,2) : ""?></b></td>
    </tr>
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
						case 'BS':
							$('#modalTitle').text('Quotation - Billing Statement')
							ShowBS(index, item)
							//console.log(item)
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

	function ShowBS(index, data){
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