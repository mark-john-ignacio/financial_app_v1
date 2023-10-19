<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Journal.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

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
$qry = "";
$varmsg = "";

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<title>Cash Disbursement Book</title>
</head>

<body style="padding:20px">
<h4><b>Company: <?=strtoupper($compname);  ?></b></h4>
<h4><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h4>
<h4><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h4>
<h4><b>Kind of Book: CASH DISBURSEMENT BOOK</b></h4>
<h4><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h4>
<br>
<table width="100%" class='table table-condensed' border="0" align="center" cellpadding="2px">
  <tr >
	<th style='display: none;'>module</th>
  <th width="100">Date</th>
  <!--<th width="100">Transaction No.</th>-->
	<th width="200">Referrence</th>
	<th width="100">Name</th>
	<th width='100'>Account No.</th>
	<th width="100">Account Title</th>
  <th class="text-right" width="120">Debit</th>
  <th class="text-right" width="120">Credit</th>
  </tr>
  
<?php

	$sql = "Select a.cmodule, b.ctranno, b.ccode, b.cpayee, b.ccheckno, a.acctno, a.ctitle, a.ndebit, a.ncredit, b.dcheckdate, b.cpayrefno, b.cpaymethod, c.ctin
	From glactivity a
	left join paybill b on a.compcode=b.compcode and a.ctranno=b.ctranno
	left join suppliers c on a.compcode=c.compcode and b.ccode = c.ccode
	where a.compcode='$company' and a.cmodule='PV' and b.dcheckdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
	order by b.ctranno, a.ndebit DESC";

	//echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//get 1st row data
			//$row1 = $result->fetch_assoc();
			$ctran = "";
			$ddate = "";
			$ccode = "";
			$cpayee = "";
			$cchecko = "";
	
	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		//if($ctran!=$row['ctranno']){
			$cntr++;		
			$ctran = $row['ctranno'];
			$ddate = $row['dcheckdate'];
			$ccode = $row['ccode'];
			$cpayee = $row['cpayee'];
			$cpaymeth = $row['cpaymethod'];
			$cchecko = ($cpaymeth=="cheque") ? $row['ccheckno'] : $row['cpayrefno'];
			$acctno = $row['acctno'];
			$cmodule = $row['cmodule'];
			
			//if($cntr>1){
				//echo "<tr><td colspan='4'>&nbsp;</td></tr>";
			//}

		?>  
<<<<<<< HEAD
<<<<<<< HEAD
		  <tr id='tableContent' name='tableContent' style="cursor: pointer";>
=======
		  <tr id='tableContent' name='tableContent' style="cursor: pointer">
>>>>>>> production
=======
		  <tr id='tableContent' name='tableContent' style="cursor: pointer";>
>>>>>>> production
			<!-- <td colspan="4">
			
			<div class="col-xs-12">
            
            	<div class="col-xs-2">
                	<b>< ?php echo $ctran;?></b>
                </div>
                <div class="col-xs-2">
                	<b>< ?php echo $ddate;?></b>
                </div>
                <div class="col-xs-4">
                	<b>< ?php echo $cpayee;?></b>
                </div>
                <div class="col-xs-4">
                	<b>< ?php echo "Reference: ".$cpaymeth." / ".$cchecko;?></b>
                </div>
                
            </div>
			</td> -->
			<td style="display: none;"><?php echo $cmodule;?></td>
			<td><?php echo $ddate;?></td>
<<<<<<< HEAD
			<td><?php echo $ctran;?></td>
			<td><?=$cchecko;?></td>
			<td nowrap><?php echo $cpayee;?></td>
			<td><?php echo $acctno;?></td>
=======
			<!--<td><b><?//php echo $ctran;?></b></td>-->
			<td><?=$cchecko;?></td>
			<td nowrap><?php echo $cpayee;?></td>
			<td nowrap><?php echo $acctno;?></td>
>>>>>>> production
			<td nowrap><?php echo $row['ctitle'];?></td>

			<td align="right"><?php if($row['ndebit'] <> 0) 
		{ 
			echo number_format($row['ndebit'],2) ;
				$ntotdebit = $ntotdebit + $row['ndebit'] ;
		}
		
		?></td>
        <td align="right"><?php if($row['ncredit'] <> 0) 
		{ 
			echo number_format($row['ncredit'],2) ;
			$ntotcredit = $ntotcredit + $row['ncredit'];
		}
		?></td>
		
		  </tr>
		<?php 

		}
		
		?>
    <?php
<<<<<<< HEAD
		//}
=======
	//	}
>>>>>>> production
	?>
    <tr>
      <td colspan="5" align="right" ><b>TOTAL</b></td>
      <td align="right" style='text-align:right; border-top: 2px solid !important'><b>
      <?php if($ntotdebit <> 0) 
		{ 
			echo number_format($ntotdebit,2) ;
			
		}
		
		?></b>
      </td>
      <td align="right" style='text-align:right; border-top: 2px solid !important'><b>
      <?php if($ntotcredit <> 0) 
		{ 
			echo number_format($ntotcredit,2) ;
			
		}
		
	  ?></b>
      </td>
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
			let ctranno = $(this).closest('#tableContent').find('td:eq(2)').text();
			console.log(modules)
			console.log(ctranno)

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
							case 'PV':
								$('#modalTitle').text('Bills Payment')
								ShowPV(index, item)
								console.log(item)
								break;
							default: 
								break;
						}
						
					})
				},
				error: function(data){
					console.log(data)
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
	

	function ShowPV(index, data) {
		if(index <= 0){
			$('<tr>').append(
				$('<tH>').text('Transaction No. '),
				$('<tH>').text('Paid to '),
				$('<tH>').text('Referrence No.:'),
				$('<tH>').text('Bank:'),
				$('<tH>').text('Tin'),
				$('<tH>').text('Date:')

			).appendTo('#HeadDetail > thead')
			// const fulldate = ddate.getMonth() + '-' + ddate.getDate() + '-' + ddate.getFullYear()
			$('<tr>').append(
				$('<td>').text(data.ctranno),
				$('<td>').text(data.cname),
				$('<td>').text(data.cpayrefno),
				$('<td>').text(data.bankname),
				$('<td>').text(data.ctin),
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
</script>