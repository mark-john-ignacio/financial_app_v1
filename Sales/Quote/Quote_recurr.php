<?php
if(!isset($_SESSION)){
	session_start();
}
$_SESSION['pageid'] = "Quote_new";
include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css"> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px">
	<form action="Quote_recurr_tran.php" name="frmunpost" id="frmunpost" method="POST" class="form form-inline">
	
		<div>
			<section>
				<div>
					<div style="float:left; width:50%">
						<font size="+2"><u>Quotations List</u></font>	
					</div>
				</div>
				<br><br>

				<div class="col-xs-12 nopadwdown">
					<div class="col-xs-1 nopadding">
						<button type="button" class="btn btn-danger btn-sm" id="btnsubmit" name="btnsubmit"><span class="fa fa-copy"></span>&nbsp;Generate</button>
					</div>

					<div class="col-xs-3 nopadwleft">
						<div class="form-group">
							<label for="pwd">Target Due Date:</label>
							<input type='text' class="form-control input-sm" id="date_trans" name="date_trans" readonly value="<?=$_POST['dtargetbill']; ?>" />
						</div>

						
					</div>
				</div>

				

				<br><br>

				<table id="example" class="table table-hover " cellspacing="1" width="100%">
					<thead>
						<tr>
							<td align="center"> <input id="allbox" type="checkbox" value="Check All" /></td>
							<th>Quote No</th>
							<th>Recurr Type</th>
							<th class="text-center">Bill Date</th>
							<th class="text-center">Customer</th>							
							<th class="text-center">Gross</th>
							<th class="text-center">Terms</th>
							<th class="text-center" width="50px">Due Date</th>
						</tr>
					</thead>

					<tbody>
					<?php

						$time = strtotime($_POST['dtargetbill']);
						$monthdate = date("Y-m-d", strtotime("-1 month", $time));
						$yeardate = date("Y-m-d", strtotime("-1 year", $time));
						$qrtrdate = date("Y-m-d", strtotime("-3 month", $time));
						$semidate = date("Y-m-d", strtotime("-6 month", $time));

					$result=mysqli_query($con,"select a.*,IFNULL(b.ctradename,b.cname) as cname, b.cterms, c.nintval from quote a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join groupings c on b.compcode=c.compcode and b.cterms=c.ccode and c.ctype='TERMS' where a.compcode='$company' and (a.lapproved=1 and a.lvoid=0) and quotetype='billing' and crecurrtype<>'one' and IFNULL(crecurrtype,'')<>'' and a.lgen=0 order by a.ddate desc");

					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						$cont = "False";
						if($row['crecurrtype']=="monthly" && $monthdate==$row['dtrandate']){
							$cont = "True";
						}elseif($row['crecurrtype']=="yearly" && $yeardate==$row['dtrandate']){
							$cont = "True";
						}elseif($row['crecurrtype']=="semi_annual" && $semidate==$row['dtrandate']){
							$cont = "True";
						}elseif($row['crecurrtype']=="quartertly" && $qrtrdate==$row['dtrandate']){
							$cont = "True";
						}

						if($cont=="True"){

							$xdue = date("Y-m-d", strtotime("+".$row['nintval']." days", $time));
					?>
						<tr>
							<td align="center"> <input name="allbox[]" id="chk<?php echo $row['ctranno'];?>" type="checkbox" value="<?php echo $row['ctranno'];?>" /></td>
							<td><?php echo $row['ctranno'];?></td>
							<td><?php echo ucfirst($row['crecurrtype']);?></td>
							<td><?php echo $row['dtrandate'];?></td>

							<td><?php echo $row['ccode'];?> - <?php echo $row['cname'];?> </td>
							
							<td align="right"><?php echo number_format($row['ngross'],2);?></td>
							<td align="center"><?php echo $row['cterms'];?></td>
							<td><div style="position: relative"> <input type='text' class="form-control input-sm datepic" id="dt<?php echo $row['ctranno'];?>" name="dt<?php echo $row['ctranno'];?>" value="<?=date_format(date_create($xdue), "m/d/Y"); ?>" /> </div></td>
						</tr>
					<?php 
						}
					}				
					mysqli_close($con);				
					?>
								
					</tbody>
				</table>

			</section>
		</div>		
	</form>  

	<!-- PRINT OUT MODAL-->
	<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-contnorad">   
				<div class="modal-bodylong">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
					
					<iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
							
				</div>
			</div>
		</div>
	</div>
	<!-- End Bootstrap modal -->



</body>
</html>

<script type="text/javascript">

	$(document).ready(function () {
	
		$('.datepic').datetimepicker({
			format: 'MM/DD/YYYY'
		});

		$('#btnsubmit').click(function() {
			checked = $("input[type=checkbox]:checked").length;

			if(!checked) {
				alert("You must check at least one checkbox.");
				return false;
			}else{
				$("#frmunpost").submit();
			}

		});

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
	});

	function printchk(x){
		$("#myprintframe").attr("src","SI_confirmprint.php?x="+x);
		$("#PrintModal").modal('show');
	}

</script>