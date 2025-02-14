<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Bank";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];  

	$posedit = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Bank_Edit'");
	if(mysqli_num_rows($sql) == 0){
		$posedit = "False";
	}
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>



</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Bank Masterlist</u></font>	
            </div>
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-2 nopadding">
					<button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"  onClick="location.href='Bank_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
				</div>
                <div class="col-xs-5" style="padding-top: 6px !important;">
					<span id="itmerr" style="padding: 5px !important;"> </span>
				</div>
			</div>
           			
			<br><br>			
			<table class="table table-hover" role="grid" id="MyTable">
				<thead>
					<tr>
						<th>Bank Code</th>
						<th>Bank Name</th>
						
						<th>Bank Acct No</th>
                        <th>Bank Acct Name</th>
                        <th>cStatus</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				
					$company = $_SESSION['companyid'];
					
					$sql = "select A.* From bank A Where A.compcode='$company' order by A.cname";

				
				//echo $sql;
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
				
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					
				?>
 					<tr>
						<td>
                        	<a href="javascript:;" onClick="editfrm('<?php echo $row['ccode'];?>');"><?php echo $row['ccode'];?></a>
						</td>
                        <td><?php echo $row['cname'];?></td>
						
						<td><?php echo $row['cbankacctno'];?></td>
                        <td><?php echo $row['caccountname'];?></td>
                        <td align="right">
                        <div id="itmstat<?php echo $row['ccode'];?>">
						<?php 
						if($row['cstatus']=="ACTIVE"){
						 	echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ccode'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						}
						else{
							echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ccode'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}
						?>
                        </div>
                        </td>
					</tr>
                <?php 
				}
				?>
               
				</tbody>
			</table>

		</section>
	</div>		

	<form name="frmedit" id="frmedit" method="get" action="Bank_edit.php">
		<input type="hidden" name="txtcitemno" id="txtcitemno" />
	</form>		

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-top">
				<div class="modal-content">
				<div class="alert-modal-danger">
					<p id="AlertMsg"></p>
					<p>
						<center>							
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
						</center>
					</p>
				</div> 
				</div>
			</div>
		</div>
	</div>

</body>
</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

	<script>
		$(function() { 
			$(".itmalert").hide();
			$('#MyTable').DataTable();
		});

		$(document).keydown(function(e) {
			if(e.keyCode == 112){//F1
				if(document.getElementById("btnNew").className=="btn btn-primary btn-md"){
					e.preventDefault();
					window.location.href='Bank_new.php';
				}
			}
		});

		function setStat(code, stat){
			var x = "<?=$posedit;?>";
			
			if(x.trim()=="True"){
				
				$.ajax ({
					url: "th_supsetstat.php",
					data: { code: code,  stat: stat },
					async: false,
					dataType: "text",
					success: function( data ) {
						//alert(jQuery.type(data));
						if(data != "True"){
							$("#itmerr").html("<b>Error: </b>"+ data);
							$("#itmerr").attr("class", "itmalert alert alert-success");
							//$("#itmerr").css({'display':'inline', 'padding':'8px'});

						}
						else{
							if(stat=="ACTIVE"){
								$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
							}else{
								$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
							}
							
							$("#itmerr").html("<b>SUCCESS: </b> "+code+" Status changed to <b><u>"+stat+"</u></b>");
							$("#itmerr").attr("class", "itmalert alert alert-success");
							//$("#itmerr").css({'display':'inline', 'padding':'8px'});
						}
					}
				
				});

			}else {
				$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				$("#AlertModal").modal('show');
			}
		}

		function editfrm(x){
			document.getElementById("txtcitemno").value = x;
			document.getElementById("frmedit").submit();
		}
	</script>


<?php
	mysqli_close($con);
?>