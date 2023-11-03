<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "TYPE.php";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');
?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
function editfrm(x){
	document.getElementById("txtcitemno").value = x;
	document.getElementById("frmedit").submit();
}
</script>
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

	<title>MYX Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Items Master List > Types</u></font>	
            </div>
            
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Type Code</th>
						<th>Type Description</th>
                        <th width="80">Status</th>
                        <th width="80">Delete</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
					$sql = "select * from groupings where compcode='$company' and ctype='ITEMTYP' order by cdesc";
				
					$result=mysqli_query($con,$sql);
					
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
				?>
 					<tr>

						<td width="100">
                        <a href="javascript:;" onClick="editgrp('<?php echo $row['ccode'];?>','<?php echo $row['cdesc'];?>')">
							<?php echo $row['ccode'];?>
                        </a>
                        </td>
                        <td >
                        <?php echo $row['cdesc'];?>
                        <div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['ccode'];?>" style="display: inline";></div>
                        </td>
						<td>
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
                        <td><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $row['ccode'];?>_delete' value='delete' onClick="deleteRow('<?php echo $row['ccode'];?>');"/></td>
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
        <h5 class="modal-title" id="myModalLabel"><b>Add New Type</b></h5>        
      </div>

	  <div class="modal-body" style="height: 20vh">
    
         <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop" id="cGroup5">
                <b>Type Code</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtccode" name="txtccode"  placeholder="Enter Code.." required>
            </div>
        </div>   

        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop" id="cGroup5">
                <b>Type Description</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc"  placeholder="Enter Description.." required>
            </div>
        </div>   

        <div class="col-xs-12">
                <b>
                <label class="checkbox-inline">
                    <input type="checkbox" value="1" name="chkSIAllow" id="chkSIAllow">Allow Open Invoice
                </label>
                </b>
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
		 var x = chkAccess('TYPE_New.php');
		 
		 if(x.trim()=="True"){
			$("#btnSave").show();
			$("#btnUpdate").hide();

			$("#txtccode").attr('readonly',false);
						
			$("#txtccode").val("");
			$("#txtcdesc").val("");			
			
			$('#myModalLabel').html("<b>Add New Item Type</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }
		});
		
		$("#txtccode").on("keyup", function() {
			// Check if Code exist
			var valz = $(this).val();
			var typz = "ITEMTYP";
			
			//$('#txtcdesc').val(valz);
			
			$.ajax ({
				url: "th_chkuomcode.php",
				data: { code: valz, typ: typz },
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
			var valz = $(this).val();
			var typz = "ITEMTYP";
			
			//$('#txtcdesc').val(valz);
			
			$.ajax ({
				url: "th_chkuomcode.php",
				data: { code: valz, typ: typz },
				async: false,
				success: function( data ) {
					if(data.trim()!="False"){
						$("#txtccode").val("").change();
						$("#txtccode").focus();
						
						//$('#txtcdesc').val("");
					}
					else{						
						$("#add_err").html("");
						$("#add_err").hide();
					}
				}
			
			});
		});
		
		$("#btnSave, #btnUpdate").on("click", function() {
			var varcode = $('#txtccode').val();
			var vardesc = $('#txtcdesc').val();

			if ($('#chkSIAllow').is(':checked')) {
				var varSIAllow = 1;
			}else{
				var varSIAllow = 0;
			}

			var ctype = "ITEMTYP";

			if(varcode=="" || vardesc==""){
				
			}else{
				$.ajax ({
				url: "th_saveuom.php",
				data: { desc: vardesc, code: varcode, typ: ctype, chkSI: varSIAllow },
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
	
	function editgrp(code,desc){
		 var x = chkAccess('TYPE_Edit.php');
		 
		 if(x.trim()=="True"){
			$("#btnSave").hide();
			$("#btnUpdate").show();
			
			$("#txtccode").attr('readonly',true);
			
			$("#txtccode").val(code);
			$("#txtcdesc").val(desc);			
			
			$('#myModalLabel').html("<b>Update Type Detail</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }

	}
	
		function setStat(code, stat){
			$.ajax ({
				url: "th_itmsetstat.php",
				data: { code: code,  stat: stat, typz: 'ITEMTYP' },
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
						
						$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).show();

					}
				}
			
			});

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

		function deleteRow(xid){
			$.ajax ({
				url: "../th_delete.php",
				data: { code: xid,  id: "itemTYP" },
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
		}
	</script>
