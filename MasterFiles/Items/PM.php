<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PM.php";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');
?>
<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>

</head>

<body style="padding:5px">

        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Sales Pricelist</u></font>	
            </div>
            
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
            <button type="button" class="btn btn-warning btn-sm" id="btnver" name="btnver"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;PM Versions</button>
            
<br><br>    
                
                <table id="example" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th width="100">PM Batch No.</th>
                            <th width="150">Effectivity Date</th>
                            <th>Versions</th>
                            <th>Remarks</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
    
                    <tbody>
                    <?php
                    $company = $_SESSION['companyid'];
                    	$sql = "SELECT cbatchno as ctranno, deffectdate, GROUP_CONCAT(cversion SEPARATOR ', ') as cversions, cremarks, lapproved, lcancelled FROM `items_pm` WHERE compcode='$company' Group By cbatchno, deffectdate, cremarks, lapproved, lcancelled order by deffectdate desc";
                    
                        $result=mysqli_query($con,$sql);
                        
                            if (!mysqli_query($con, $sql)) {
                                printf("Errormessage: %s\n", mysqli_error($con));
                            } 
                            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                    ?>
                        <tr>
    
                            <td>
                            <a href="javascript:;" onClick="editgrp('<?php echo $row['ctranno'];?>')">
                                <?php echo $row['ctranno'];?>
                            </a>
                            </td>
                            <td>
                            <?php echo date_format(date_create($row['deffectdate']), "F d, Y");?>
                            </td>
                            <td>
                                <?php echo $row['cversions'];?>
                            </td>
                             <td>
                                <?php echo $row['cremarks'];?>
                            </td>
                           <td>
                            <div id="msg<?php echo $row['ctranno'];?>">
                        	<?php 
							if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('post','<?php echo $row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('cancel','<?php echo $row['ctranno'];?>')">CANCEL</a>
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


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Add New Version</b></h5>        
      </div>

	  <div class="modal-body" style="height: 20vh">
    
         <div class="col-xs-12 nopadding">
			<div class="alert alert-danger nopadding" id="add_err"></div>        
         </div>   

			
        <div class="col-xs-12 nopadwtop">  
            
                    <div class="col-xs-3 nopadding">
                       <b>Version Code</b> 
                    </div>
                    <div class="col-xs-5 nopadwleft">
                      <b>Version Description</b>  
                    </div>
                    			
        </div>   
        
            <!-- BODY -->
                <div style="height:15vh; display:inline" class="col-lg-12 nopadding pre-scrollable" id="TblItemver">
                </div> 
                 

	</div>
    
 	<div class="modal-footer">
    			<button type="button" id="btnaddver" name="btnaddver" class="btn btn-success btn-sm">Add New</button>
                <button type="button" id="btnSave" name="Save" class="btn btn-primary btn-sm">Save Details</button>
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Close</button>
	</div>
    
    </div>
  </div>
</div>
<!-- Modal -->		

<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btnmodz btn btn-primary btn-sm" id="OK">Ok</button>
                        <button type="button" class="btnmodz btn btn-danger btn-sm" id="Cancel">Cancel</button>
                        
                        
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                        
                        <input type="hidden" id="typ" name="typ" value = "">
                        <input type="hidden" id="modzx" name="modzx" value = "">
                    </center>
                </p>
               </div> 
            </div>
        </div>
    </div>
</div>


<!-- Modal PICK VERSIONS -->
<div class="modal fade" id="myPickMod" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Pick PM Version</b></h5>        
      </div>

	  <div class="modal-body" style="height: 20vh">
    
         <div class="col-xs-12 nopadding">
			<div class="alert alert-danger nopadding" id="add_errpick"></div>        
         </div>   

			
        <div class="col-xs-12 nopadwtop">  
 
                    <div class="col-xs-1 nopadding">
                       &nbsp; 
                    </div>           
                    <div class="col-xs-3 nopadding">
                       <b>Version Code</b> 
                    </div>
                    <div class="col-xs-5 nopadwleft">
                      <b>Version Description</b>  
                    </div>
                    			
        </div>   
        
            <!-- BODY -->
                <div style="height:15vh; display:inline" class="col-lg-12 nopadding pre-scrollable" id="TblPickver">
                </div> 
                 

	</div>
    
 	<div class="modal-footer">
    			<button type="button" id="btnproceed" name="btnproceed" class="btn btn-success btn-sm">Proceed</button>
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
	</div>
    
    </div>
  </div>
</div>
<!-- Modal -->		


<?php
mysqli_close($con);
?>

<form method="post" name="frmnew" id="frmnew" action="PM_new.php">
	<input type="hidden" name="hdnvers" id="hdnvers" value="">
</form>

<form method="post" name="frmedit" id="frmedit" action="PM_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

</body>
</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

	
	<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F1
	  	e.preventDefault();
		$("#btnadd").click();
		
	  }
	});

	$(function(){
		$('#example').DataTable({
			"order": [[ 2, "desc" ]]
		});
		$("#add_err").hide();
		$("#add_errpick").hide();

		$("#btnadd").on("click", function() {
			
		 var x = chkAccess('PM_New.php');

		 if(x.trim()=="True"){
			$("#TblItemver").empty();
			$("#add_err").hide();
			
			loadversionspick();
			$('#myPickMod').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				$("#alertbtnOK").show();
				$("#OK").hide();
				$("#Cancel").hide();
			 $("#AlertModal").modal('show');

		 }
		
			//location.href = "PM_new.php";
		});
		
		$("#btnproceed").on("click", function() {
			var anyBoxesChecked = 0;
			var vlz = "";
						
			$("input[name='chkpricever[]']").each( function () {
				if ($(this).is(":checked")) {
					anyBoxesChecked = anyBoxesChecked + 1;
					
					if(anyBoxesChecked>1){
						vlz=vlz+";";
					}
					
					vlz=vlz+$(this).val();
				}			
			});
			
			if(anyBoxesChecked==0 || vlz==""){
				$("#add_errpick").html("<b>ERROR: </b> Please select atleast 1 before you proceed.");
				$("#add_errpick").show();
			}else{
				
				$("#hdnvers").val(vlz);
				$("#frmnew").submit();
			}

		});
		
		
		// Adding new user
		$("#btnver").on("click", function() {
		 var x = chkAccess('PM_Edit.php');

		 if(x.trim()=="True"){
			$("#TblItemver").empty();
			$("#add_err").hide();
			
			loadversions();
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				$("#alertbtnOK").show();
				$("#OK").hide();
				$("#Cancel").hide();
			 $("#AlertModal").modal('show');

		 }
		});
				
		$("#btnSave").on("click", function() {
			var errmsg = "";
			var NOLAMN = "NO";
			//check for blank values
			$(".itmverdet").each(function(index, element) {
				var id = $(this).find('.txtcode').val();
				var name = $(this).find('.txtdesc').val();
				
				if(id=="" || name==""){
					NOLAMN = "YES";
					//alert("YES");
				}
			});
			
			if(NOLAMN=="NO"){
				
			
				$(".itmverdet").each(function(index, element) {
				  //alert(index +": " + $(this).find('.txtcode').val());
					//alert(index +": " + $(this).find('.txtdesc').val());
					 var id = $(this).find('.txtcode').val();
					 var name = $(this).find('.txtdesc').val();

						$.ajax ({
							url: "th_saveuom.php",
							data: { code: id, desc: name, typ: 'ITMPMVER' },
							async: false,
							success: function( data ) {
								if(data.trim()!="True"){
									errmsg = data.trim();
								}
							}
						});


				});
				
			}
			
			if(errmsg=="" && NOLAMN=="NO"){
				$("#add_err").attr("class","alert alert-success nopadding");
				$("#add_err").html("<b>SUCCESS: </b>PM versions successfully saved!");
				$("#add_err").show();
				
				$("#TblItemver").empty();
				
				loadversions();
			}
			else if(errmsg=="" && NOLAMN=="YES"){
				
				$("#add_err").attr("class","alert alert-danger nopadding");
				$("#add_err").html("<b>ERROR: </b>Blank Values Detected!");
				$("#add_err").show();
				
			}
			else{
				$("#add_err").attr("class","alert alert-danger nopadding");
				$("#add_err").html("<b>ERROR: </b>There's a problem saving your data!<br><br>"+errmsg);
				$("#add_err").show();

			}
						

		})
		
		
		$("#btnaddver").on("click", function() {

			
						  var divhead = "<div class=\"itmverdet col-xs-12 nopadwtop\">";
						  var divcode = "<div class=\"col-xs-3 nopadding\"> <input type=\"text\" class=\"txtcode form-control input-xs\" placeholder=\"Enter code...\" required ></div>";
						  var divdet = "<div class=\"col-xs-5 nopadwleft\"> <input type=\"text\" class=\"txtdesc form-control input-xs\" placeholder=\"Enter description...\" required> </div>";
						  var divdel = "";
						  var divend = "</div>";
						  
						 // alert(divhead + divcode + divdet + divend);
						  
						  $("#TblItemver").append(divhead + divcode + divdet + divdel + divend);
						  
						  
						  		$(".txtcode").on("keyup", function() {
									// Check if Code exist
									var valz = $(this).val();
									var typz = "ITMPMVER";
									
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
						
								$(".txtcode").on("blur", function() {
									// Check if Code exist
									var valz = $(this).val();
									var typz = "ITMPMVER";
									
									//$('#txtcdesc').val(valz);
									
									$.ajax ({
										url: "th_chkuomcode.php",
										data: { code: valz, typ: typz },
										async: false,
										success: function( data ) {

											if(data.trim()!="False"){
												
												$(".txtcode").val("").change();
												$(".txtcode").focus();
												
											}
											else{						
												$("#add_err").html("");
												$("#add_err").hide();
											}
										}
									
									});
								});


		});
		
		
		
		var itmstat = "";
		var typ = "";
		var tran = "";
	
		$(".btnmodz").on("click", function (){
			if($('#AlertModal').hasClass('in')==true){
			var idz = $(this).attr('id');
			
			if(idz=="OK"){
				var typ = $("#typ").val();
				var tran = $("#modzx").val();
				
				if(x=="POST"){
					var msg = "POSTED";
				}
				else if(x=="CANCEL"){
					var msg = "CANCELLED";
				}
			
			
				 var x = chkAccess('PM_'+typ);
		
				 if(x.trim()=="True"){
					$.ajax ({
						url: "PM_Trans.php",
						data: { typ: typ, code:tran },
						async: false,
						beforeSend: function(){
							if(typ=='post'){
								$("#AlertMsg").html("<b>&nbsp;&nbsp;POSTING ("+tran+"): </b> Please wait a moment...");
							}
							else if(typ=='cancel'){
								$("#AlertMsg").html("<b>&nbsp;&nbsp;CANCELLING ("+tran+"): </b> Please wait a moment...");
							}
		
							$("#alertbtnOK").hide();
							$("#OK").hide();
							$("#Cancel").hide();
							$("#AlertModal").modal('show');
						},
						success: function( data ) {
							 $("#msg"+tran).html(data.trim());
							 
							 $("#AlertModal").modal('hide');
						}
					});
				 } else {
					 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
							$("#alertbtnOK").show();
							$("#OK").hide();
							$("#Cancel").hide();
					 $("#AlertModal").modal('show');
		
				 }
				 
			}
			else{
				$("#AlertModal").modal('hide');
			}
				
			}
		});
	


});

	function loadversions(){
			$.ajax ({
				url: "th_loadpmver.php",
				async: false,
				dataType: 'json',
				success: function( data ) {
                      console.log(data);
					  $.each(data,function(index,item){
						  
						  var divhead = "<div class=\"itmverdet col-xs-12 nopadwtop\">";
						  var divcode = "<div class=\"col-xs-3 nopadding\"> <input type=\"text\" class=\"txtcode form-control input-xs\" value=\""+item.id+"\" readonly> </div>";
						  var divdet = "<div class=\"col-xs-5 nopadwleft\"> <input type=\"text\" class=\"txtdesc form-control input-xs\" placeholder=\"Enter description...\" value=\""+item.name+"\"> </div>";
						   var divdel = "<div class=\"col-xs-3 nopadwleft\"> <input class=\"btn btn-danger btn-xs\" type=\"button\" id=\"row"+item.id+"\" value=\"delete\" onClick=\"deleteRow('1:"+item.id+"');\"/> </div>";
						  var divend = "</div>";
						  
						  $("#TblItemver").append(divhead + divcode + divdet + divdel + divend);
					  });
				}
			
			});
		
	}
	
	function deleteRow(xid){
		var xy = xid.split(":");

		if(xy[0]=="1"){

			$.ajax ({
				url: "../th_delete.php",
				data: { code: xy[1],  id: "PMVer" },
				async: false,
				dataType: "text",
				success: function( data ) {
					//alert(jQuery.type(data));
					if(data.trim() != "True"){
						$("#add_err").html("<b>Error: </b>"+ data);
						$("#add_err").attr("class", "itmalert alert alert-danger nopadding")
						$("#add_err").show();
					}
					else{
					  	$("#TblItemver").empty();
				
						loadversions();
					}
				}
			
			});
			
			
			
		}else if(xy[0]=="0"){
			$(this).closest("div").remove();
			//$(this).parent().parent().remove();
		}	
	}
		
	function loadversionspick(){
			$.ajax ({
				url: "th_loadpmver.php",
				async: false,
				dataType: 'json',
				success: function( data ) {
                      console.log(data);
					  $.each(data,function(index,item){
						  
						  var divhead = "<div class=\"itmverdet col-xs-12 nopadwtop\">";
						  var divcohkbox = "<div class=\"col-xs-1 nopadding\"> <div class\"checkbox\"> <input type=\"checkbox\" name=\"chkpricever[]\" name=\"id[]\" value=\""+item.id+ "\"> </div> </div>";
						  var divcode = "<div class=\"col-xs-3 nopadding\"> "+item.id+ "</div>";
						  var divdet = "<div class=\"col-xs-5 nopadwleft\"> "+item.name+ "</div>";
						 
						  var divend = "</div>";
						  
						  $("#TblPickver").append(divhead + divcohkbox + divcode + divdet + divend);
					  });
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
		
		function editgrp(val){
			$("#txtctranno").val(val);
			$("#frmedit").submit();
		}

		function trans(typ,tran){

			$("#typ").val(typ);
			$("#modzx").val(tran);
		
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Are you sure you want to "+typ+" Pricelist No.: "+tran);
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');

		}
	</script>
