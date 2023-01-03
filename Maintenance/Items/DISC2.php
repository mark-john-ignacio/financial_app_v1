<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "DISC.php";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');
?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Discounts List</u></font>	
            </div>
            
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-md" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Discount Code</th>
						<th>Description</th>
                        <th width="80">Label</th>
                        <th width="80">Value</th>
                        <th width="80" colspan="2">Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
					$sql = "select * from discounts where compcode='$company' order by clabel";
				
					$result=mysqli_query($con,$sql);
					
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
				?>
 					<tr>

						<td width="100">
                        <a href="javascript:;" onClick="editgrp('<?php echo $row['ctranno'];?>')">
							<?php echo $row['ctranno'];?>
                        </a>
                        </td>
                        
                        <td>
                        <?php echo $row['cdescription'];?>
                        <div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['ctranno'];?>" style="display: inline";></div>
                        </td>
                        
                        <td><?php echo $row['clabel'];?></td>
                        
                        <td><?php echo $row['nvalue'];?></td>
                        
						<td>
                        <div id="itmstat<?php echo $row['ctranno'];?>">
						<?php 
						if($row['cstatus']=="ACTIVE"){
						 	echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ctranno'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						}
						else{
							echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['ctranno'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}
						?>
                        </div>
                        </td>
                        
                        <td>
                        <div id="msg<?php echo $row['ctranno'];?>">
                        	<?php 
							if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>')">CANCEL</a>
							<?php
                            }
							else{
								if(intval($row['lcancelled'])==intval(1)){
									echo "Cancelled";
								}
								if(intval($row['lapproved'])==intval(1)){
									echo "Posted";
								}
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


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Add New Discount</b></h5>        
      </div>

	  <div class="modal-body" style="height: 20vh">
    
         <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Description</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtdesc" name="txtdesc"  placeholder="Enter Description.." required>
            </div>
        </div>   

        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Label</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtlabel" name="txtlabel"  placeholder="Enter Label.." required>
            </div>
        </div>   
 
        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Value</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="numeric form-control input-sm" id="txtvalue" name="txtvalue"  placeholder="Enter Decimal Value.." required>
            </div>
        </div>   
        
        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Effectivity Date</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="effect_date" name="effect_date" value='<?php echo date("m/d/Y");?>'>
            </div>
        </div> 
         
      </div> 
      
      <div class="alert alert-danger nopadding" id="add_err"></div>         

	</div>
    
 	<div class="modal-footer">
    			<input type="hidden" id="txtcode" name="txtcode" value=''>
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

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	
	$(function(){
		
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		
		var yyyy = today.getFullYear();
		if(dd<10){
			dd='0'+dd;
		} 
		if(mm<10){
			mm='0'+mm;
		} 
		var today = mm+'/'+dd+'/'+yyyy;


		$('#example').DataTable();
		$("#add_err").hide();
		$(".itmalert").hide();

		// Adding new user
		$("#btnadd").on("click", function() {
			 var x = chkAccess('DISC_New');
			 
			 if(x.trim()=="True"){
				$("#btnSave").show();
				$("#btnUpdate").hide();
							
				$("#txtcode").val("");
				$('#txtdesc').val("");	
				$('#txtlabel').val("");
				$('#txtvalue').val("");
				$('#effect_date').val(today);
				
				$('#myModalLabel').html("<b>Add New Discount</b>");
				$('#myModal').modal('show');
			 } else {
				 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				 $("#AlertModal").modal('show');
	
			 }
		});
				
		$("#btnSave, #btnUpdate").on("click", function() {
			var varlabel = $('#txtlabel').val();
			var vardesc = $('#txtdesc').val();
			var varvalz = $('#txtvalue').val();
			var vareffect = $('#effect_date').val();
			var varcode = $('#txtcode').val();
						
			$.ajax ({
				url: "th_savedisc.php",
				data: { code:varcode, effdte: vareffect, desc: vardesc, lbl: varlabel, val: varvalz },
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

		});
		
	});
	
	function editgrp(code,desc,lbl,val,effdte){
		 var x = chkAccess('DISC_Edit');
		 
		 if(x.trim()=="True"){
			$("#btnSave").hide();
			$("#btnUpdate").show();
						
			$("#txtcode").val(code);
			$('#txtdesc').val(desc);	
			$('#txtlabel').val(lbl);
			$('#txtvalue').val(val);
			$('#effect_date').val(effdte);
								
			$('#myModalLabel').html("<b>Update Discounts Detail</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }

	}
	
	function setStat(code, stat){
			$.ajax ({
				url: "th_itmdiscstat.php",
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

	</script>
</body>
</html>
