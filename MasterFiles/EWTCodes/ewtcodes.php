<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "EWTCodes.php";

	include('../../Connection/connection_string.php');
	include('../../include/accessinner.php');

	$employeeid = $_SESSION['employeeid'];
	
	$posnew = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'EWTCodes_New.php'");
	if(mysqli_num_rows($sql) == 0){
		$posnew = "False";
	}

	$posedit = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'EWTCodes_Edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$posedit = "False";
	}

?>
	<!DOCTYPE html>
	<html>
	<head>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

	<style>
		a.info{
			position:relative; /*this is the key*/
			z-index:24; 
			color:#000;
			text-decoration:none}

		a.info:hover{z-index:25; background-color:#0099ff}

		a.info span{display: none}

		a.info:hover span{ /*the span will display just on :hover state*/
				display:block;
				position:absolute;
				top:-10em; left:10em; width:30em;
				border:1px solid #0cf;
				background-color:#fff; color:#000;
			padding:5px;
		}
	</style>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>

      <div>
        <div style="float:left; width:50%">
					<font size="+2"><u>EWT Codes List</u></font>	
        </div>            
      </div>

		<br><br>

      <button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
       <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">EWT Code</th>
						<th>Description</th>
						<th width="50">Rate</th>
						<th width="80">Status</th>
					</tr>
				</thead>

				<tbody>
					<?php
						$company = $_SESSION['companyid'];
							
						$sql = "select * from wtaxcodes where compcode='$company' order by ctaxcode";							
						$result=mysqli_query($con,$sql);
								
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
								
						$arrdesc = array();
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
							$arrdesc[] = $row['ctaxcode'];
					?>
						<tr>
							<td>
								<a href="javascript:;" onClick="editgrp('<?php echo $row['nident'];?>','<?php echo $row['ctaxcode'];?>','<?php echo $row['cdesc'];?>','<?php echo $row['nrate'];?>')">
									<?php echo $row['ctaxcode'];?>
								</a>								
							</td>
							<td> <?php echo $row['cdesc'];?> <div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['nident'];?>" style="display: inline";></div></td>
							<td> <?php echo number_format($row['nrate'])."%";?> </td>
							<td align="center">
								<div id="itmstat<?php echo $row['nident'];?>">
								<?php 
									if($row['cstatus']=="ACTIVE"){
										echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['nident'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
									}
									else{
										echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['nident'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
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

			<input type="hidden" id="posnew" value="<?=$posnew;?>">
			<input type="hidden" id="posedit" value="<?=$posedit;?>">

		</section>
	</div>		


	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="myModalLabel"><b>Add New EWT Code</b></h5>        
				</div>

				<div class="modal-body" style="height: 30vh">
				
						<div class="col-xs-12">
							<div class="cgroup col-xs-3 nopadwtop">
								<b>EWT Code</b>
							</div>
							
							<div class="col-xs-3 nopadwtop">
								<input type="text" class="form-control input-sm" id="txttaxcode" name="txttaxcode"  placeholder="Tax Code.." required>
								<input type="hidden" id="hdnid" name="hdnid" value="">
							</div>
						</div>
						<div class="col-xs-12">
							<div class="cgroup col-xs-3 nopadwtop">
								<b>Description</b>
							</div>
							
							<div class="col-xs-9 nopadwtop">
								<input type="text" class="form-control input-sm" id="txttaxdesc" name="txttaxdesc"  placeholder="Description.." required>
							</div>
						</div> 
						
						<div class="col-xs-12">
							<div class="cgroup col-xs-3 nopadwtop">
								<b>Rate</b>
							</div>
							
							<div class="col-xs-3 nopadwtop">
								<input type="text" class="form-control input-sm" id="txttaxrate" name="txttaxrate"  placeholder="0%" required>
							</div>
						</div> 
						
						
						<div class="alert alert-danger" id="add_err"></div>         

				</div>
				
				<div class="modal-footer">
					<button type="button" id="btnSave" name="Save" class="btn btn-primary btn-sm">Add Detail</button>
					<button type="button" id="btnUpdate" name="Update" class="btn btn-success btn-sm">Update Detail</button>
					<button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
				</div>
			
			</div>
		</div>
	</div>
	<!-- Modal -->		

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-center">
				<div class="modal-content">
					<div class="alert-modal-danger">
						<p id="AlertMsg"></p>
						<p>
							<center>
								<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Ok</button>
							</center>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
mysqli_close($con);
?>
</body>
</html>

  <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	
		$(function(){
			$("#add_err").hide();
			$(".itmalert").hide();

			$('#example').DataTable();

			// Adding new user
			$("#btnadd").on("click", function() {
				var x = $("#posnew").val();
				
				if(x.trim()=="True"){
					$("#btnSave").show();
					$("#btnUpdate").hide();
								
					$("#hdnid").val("new");
					$("#txttaxcode").val("");	    
					$("#txttaxdesc").val("");
					$("#txttaxrate").val(0);
					$("#add_err").html("");		
					
					$('#myModalLabel').html("<b>Add New EWT Code</b>");
					$('#myModal').modal('show');
				} else {
					$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
					$("#AlertModal").modal('show');

				}
			});
			
			$("#btnSave, #btnUpdate").on("click", function() {
				var taxcode = $("#txttaxcode").val();	    
				var taxdesc = $("#txttaxdesc").val();
				var taxrate = $("#txttaxrate").val();

				var varcode = $('#hdnid').val();

				if(taxcode=="" || taxdesc==""){
					$("#add_err").html("<center><b>Code and Description required!</b></center>");
					$("#add_err").show();
				}else{

					$("#add_err").html("");
					$("#add_err").show();

					var xcstat = "False";

					if(varcode=="new"){

						response = '<?=json_encode($arrdesc);?>';

						var obj = jQuery.parseJSON(response);
						$.each(obj, function(key,value) {
							if(value.toLowerCase()==taxcode.toLowerCase()){
								xcstat = "True";
							}
						}); 
					}

					if(xcstat=="False"){
						$.ajax ({
							url: "th_save.php",
							data: { id:varcode, taxcode: taxcode, taxdesc: taxdesc, taxrate: taxrate },
							async: false,
							success: function( data ) {
								if(data.trim()=="True"){
									
									$('#myModal').modal('hide');
									location.reload();
							
								}
								else {
									$("#add_err").html("<b>ERROR: </b>"+data);
									$("#add_err").show();
								}
							}
					
						});
					}else{
						$("#add_err").html("<center><b>EWT Code Already Exist!</b></center>");
						$("#add_err").show();
					}
					
				}
				

			})
			
		});

		$(document).keydown(function(e) {	
		 
		 if(e.keyCode == 112) { //F1
			 e.preventDefault();
			 $("#btnadd").click();
		 }
	 });
	
		function editgrp(id,code,desc,rate){
			var x = $("#posedit").val();
			
			if(x.trim()=="True"){
				$("#btnSave").hide();
				$("#btnUpdate").show();
				
				$("#hdnid").val(id);

				$("#txttaxcode").val(code);	    
				$("#txttaxdesc").val(desc);
				$("#txttaxrate").val(rate);

				$("#add_err").html("");		
				
				$('#myModalLabel').html("<b>Update EWT Code Details</b>");
				$('#myModal').modal('show');
			} else {
				$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				$("#AlertModal").modal('show');

			}

		}
	
		function setStat(code, stat){
			$.ajax ({
				url: "th_setstat.php",
				data: { code: code,  stat: stat },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						$("#itm"+code).html("<b>Error: </b>"+ data);
						$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+code).show();
					}
					else{
					  if(stat=="ACTIVE"){
						$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
					  }else{
						 $("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
					  }
						
						$("#itm"+code).html("<br><b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).show();

					}
				}
			
			});

		}

	</script>
