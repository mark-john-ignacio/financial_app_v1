<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "ARAdj_unpost";
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
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>
</head>

<body style="padding:5px">
	<form action="ARAdj_void_tran.php" name="frmunpost" id="frmunpost" method="POST">
	
		<div>
			<section>
					<div>
						<div style="float:left; width:50%">
							<font size="+2"><u>AR Adjustment List</u></font>	
						</div>
					</div>
				<br><br>

				<button type="button" class="btn btn-danger btn-sm" id="btnsubmit" name="btnsubmit"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>

				<br><br>

				<table id="example" class="table table-hover " cellspacing="1" width="100%">
					<thead>
						<tr>
							<td align="center"> <input id="allbox" type="checkbox" value="Check All" /></td>
							<th class="text-center">Tran No</th>
							<th class="text-center">Type</th>
							<th class="text-center">Customer</th>
							<th class="text-center">Adj Date</th>
							<th class="text-center">Remarks</th>
							<th class="text-center">Amount</th>
						</tr>
					</thead>

					<tbody>
					<?php

					$alrr = mysqli_query($con,"Select a.aradjustment_ctranno from receipt_deds a left join receipt b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.lvoid=0");
					$refpos[] = "";
					while($rowxcv=mysqli_fetch_array($alrr, MYSQLI_ASSOC)){
						$refpos[] = $rowxcv['aradjustment_ctranno'];
					}
					
					$result=mysqli_query($con,"select a.*,IFNULL(b.ctradename,b.cname) as cname from aradjustment a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.ctranno not in ('".implode("','",$refpos)."') and (a.lapproved=1 and a.lvoid=0) order by a.ddate desc");
					
						if (!$result) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
					?>
					<!-- <a href="javascript:;" onClick="printchk('<?//php echo $row['ctranno'];?>');"> </a>-->
						<tr>
							<td align="center"> <input name="allbox[]" id="chk<?php echo $row['ctranno'];?>" type="checkbox" value="<?php echo $row['ctranno'];?>" /></td>
							<td><?php echo $row['ctranno'];?></td>
							<td><?php echo $row['ctype'];?></td>
							<td><?php echo $row['ccode'];?> - <?php echo $row['cname'];?> </td>
							<td><?php echo $row['dcutdate'];?></td>
							<td><?php echo $row['cremarks'];?></td>
							<td style="text-align: right"><?php echo number_format($row['ngross'],2);?></td>
						</tr>
					<?php 
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

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

<script type="text/javascript">

	$(document).ready(function () {
		$("#example").DataTable();
		
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