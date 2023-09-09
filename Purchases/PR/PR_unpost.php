<?php

	if(!isset($_SESSION)){
		session_start();
	}
	
	$_SESSION['pageid'] = "PR_unpost.php";
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
	<form action="PR_unpost_tran.php" name="frmunpost" id="frmunpost" method="POST">
	
		<div>
			<section>
					<div>
						<div style="float:left; width:50%">
							<font size="+2"><u>Purchase Request List</u></font>	
						</div>
					</div>
				<br><br>

				<button type="button" class="btn btn-warning" id="btnsubmit" name="btnsubmit"><span class="fa fa-refresh"></span>&nbsp;Un-Post Transaction</button>

				<br><br>

				<table id="example" class="table table-hover " cellspacing="1" width="100%">
					<thead>
						<tr>
							<td align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></td>
							<th class="text-center">PR No</th>
							<th class="text-center">Requested By</th>
							<th class="text-center">Section</th>
							<th class="text-center">Trans Date</th>
							<th class="text-center">Date Needed</th>
						</tr>
					</thead>

					<tbody>
					<?php
					//select * purchase reference in WRR
					$alrr = mysqli_query($con,"Select a.creference from purchase_t a left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono where a.compcode='$company' and b.lcancelled=0");
					$refpos[] = "";
					while($rowxcv=mysqli_fetch_array($alrr, MYSQLI_ASSOC)){
						$refpos[] = $rowxcv['creference'];
					}

					//select * approved or rejected po_trans_approval
					$alrr = mysqli_query($con,"Select cprno from purchrequest_trans_approvals where compcode='$company' and (lreject=1 or lapproved=1) and cprno not in ('".implode("','",$refpos)."')");
					$allPOFor[] = "";
					while($rowxcv=mysqli_fetch_array($alrr, MYSQLI_ASSOC)){
						$allPOFor[] = $rowxcv['cprno'];
					}
					
					$result=mysqli_query($con,"select a.*,b.cdesc, c.Minit, c.Fname, c.Lname from purchrequest a left join locations b on a.compcode=b.compcode and a.locations_id=b.nid left join users c on a.cpreparedby=c.Userid where a.compcode='$company' and (a.ctranno in ('".implode("','",$allPOFor)."') OR a.lcancelled=1) order by a.ddate desc");
					
						if (!$result) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						$mi = ($row['Minit']!="") ? " ".$row['Minit'] : "";
    				$cpreparedName =  $row['Lname'] . ", ". $row['Fname'] . $mi;
					?>
						<tr>
							<td align="center"> <input name="allbox[]" id="chk<?php echo $row['ctranno'];?>" type="checkbox" value="<?php echo $row['ctranno'];?>" /></td>
							<td><a href="javascript:;" onClick="printchk('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
							<td><?=$cpreparedName;?> </td>
							<td><?=$row['cdesc'];?> </td>
							<td align="right"><?php echo $row['ddate'];?></td>
							<td align="right"><?php echo $row['dneeded'];?></td>
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
	});

	function printchk(x){
		$("#myprintframe").attr("src","PrintPR.php?hdntransid="+x);
		$("#PrintModal").modal('show');
	}

</script>