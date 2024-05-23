<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Groupings";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');
?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css"> 

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Items Master List > Groupings</u></font>	
            </div>
            
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<!--<th>Item Code</th>-->
						<th width="80">Group No</th>
						<th width="100">Code</th>
                        <th>Description</th>
						<th width="80">Status</th>
						<th width="80">Delete</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
					$sql = "select * from items_groups where compcode='$company' order by cgroupno";
				
					$result=mysqli_query($con,$sql);
					
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
				?>
 					<tr>

						<td width="100"><?php echo $row['cgroupno'];?></td>
						<td width="100">
                        <a href="javascript:;" onClick="editgrp('<?php echo $row['cgroupno'];?>','<?php echo $row['ccode'];?>','<?php echo $row['cgroupdesc'];?>')">
							<?php echo $row['ccode'];?>
                        </a>
                        </td>
                        <td >
                        <?php echo $row['cgroupdesc'];?>
                        <div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['ccode'];?>" style="display: inline";></div>
                        </td>
						<td>
                        <div id="itmstat<?php echo $row['ccode'];?>">
						<?php 
						if($row['cstatus']=="ACTIVE"){
						 	echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ccode'] ."','INACTIVE','".$row['cgroupno']."')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						}
						else{
							echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ccode'] ."','ACTIVE','".$row['cgroupno']."')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}
						?>
                        </div>
                        </td>
                        <td><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $row['ccode'];?>_delete' value='delete' onClick="deleteRow('<?php echo $row['ccode'];?>','<?php echo $row['cgroupno'];?>');"/></td>
					</tr>
                <?php 
				}
				
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>		


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Add New Group Detail</b></h5>        
      </div>

	  <div class="modal-body" style="height: 20vh">
    
                
        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop" id="cGroup5">
                <b>Group</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
    
            <select id="selgrpno" name="selgrpno" class="form-control input-sm selectpicker">
                <?php
                    $sql = "select * from `parameters` where ccode like 'cGroup%' order by cvalue";
					
                    $result=mysqli_query($con,$sql);
            			if(mysqli_num_rows($result)!=0){
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                ?>   
                	<option value="<?php echo $row['ccode'];?>"><?php echo $row['cvalue']?></option>
                <?php
                            }
						}
                ?>     
            </select>
    
            </div>
        </div>            
    
        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop" id="cGroup5">
                <b>Code</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtccode" name="txtccode" placeholder="Enter Code.." required>
            </div>
        </div>            
    
        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop" id="cGroup5">
                <b>Description</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc"  placeholder="Enter Description.." required>
            </div>
        </div>   
        
        <div class="alert alert-danger nopadding" id="add_err"></div>         

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
		$('#example').DataTable();
		$("#add_err").hide();
		$(".itmalert").hide();

		// Adding new user
		$("#btnadd").on("click", function() {
		 var x = chkAccess('Groupings_New');
		 
		 if(x.trim()=="True"){
			$("#btnSave").show();
			$("#btnUpdate").hide();

			$("#txtccode").attr('readonly',false);
						
			$("#txtccode").val("");
			$("#txtcdesc").val("");			
			
			$('#myModalLabel').html("<b>Add New Group Detail</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }
		 
		});
		
		$("#txtccode").on("keyup", function() {
			// Check if Code exist
			$.ajax ({
				url: "th_chkgrpcode.php",
				data: { code: $(this).val(), grp: $("#selgrpno").val() },
				async: false,
				success: function( data ) {
					if(data.trim()!="False"){
						$("#add_err").html("<b>ERROR: </b>"+data);
						$("#add_err").show();
					}
					else{
						$("#add_err").html("");
						$("#add_err").hide();
					}
				}
			
			});
		});

		$("#txtccode").on("blur", function() {
			// Check if Code exist
			$.ajax ({
				url: "th_chkgrpcode.php",
				data: { code: $(this).val(), grp: $("#selgrpno").val() },
				async: false,
				success: function( data ) {
					if(data.trim()!="False"){
						$("#txtccode").val("").change();
						$("#txtccode").focus();
					}
					else{
						$("#add_err").html("");
						$("#add_err").hide();
					}
				}
			
			});
		});
		
		$("#btnSave, #btnUpdate").on("click", function() {
			var vargrp = $('#selgrpno').val();
			var varcode = $('#txtccode').val();
			var vardesc = $('#txtcdesc').val();
			
			if(varcode=="" || vardesc==""){
				
			}else{
				$.ajax ({
					url: "th_savegrp.php",
					data: { grp: vargrp,  code: varcode, desc: vardesc },
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
			}

		})
		
	});

	$(document).keydown(function(e) {	
		 
		 if(e.keyCode == 112) { //F1
			 e.preventDefault();
			 $("#btnadd").click();
		 }
	 });
	
	function editgrp(grp,code,desc){
		var x = chkAccess('Groupings_Edit');
		 
		 if(x.trim()=="True"){
		$("#btnSave").hide();
			$("#btnUpdate").show();

			$("#txtccode").attr('readonly',true);
				
			$("#selgrpno").val(grp);		
			$("#txtccode").val(code);
			$("#txtcdesc").val(desc);			
			
			$('#myModalLabel').html("<b>Update Group Detail</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }

	}


		function setStat(code, stat, grpno){
			var x = chkAccess('Groupings_Edit');
		 
			if(x.trim()=="True"){
				
				$.ajax ({
					url: "th_grpsetstat.php",
					data: { code: code,  stat: stat, typz: grpno },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							$("#itm"+code).html("<b>Error: </b>"+ data);
							$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
							$("#itm"+code).show();
						}
						else{
						if(stat=="ACTIVE"){
							$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE','"+grpno+"')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
						}else{
							$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE','"+grpno+"')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
						}
							
							$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
							$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
							$("#itm"+code).show();

						}
					}
				
				});
			} else {
				$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				$("#AlertModal").modal('show');

			}
		}
		
		function chkAccess(id){
			var result;
			
			$.ajax ({
				url: "chkAccess.php",
				data: { id: id },
				async: false,
				success: function( data ) {
					 result = data;
				}
			});
			
			return result;
		}
		
		/*function deleteRow(xid,grp){
			$.ajax ({
				url: "../th_delete.php",
				data: { code: xid,  id: "itemGRP", grp: grp },
				async: false,
				dataType: "text",
				success: function( data ) {
					//alert(jQuery.type(data));
					if(data.trim() != "True"){
						$("#itm"+xid).html("<b>Error: </b>"+ data);
						$("#itm"+xid).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+xid).css('display', 'inline');
					}
					else{
					  	location.reload();
					}
				}
			
			});
		}*/

	</script>
