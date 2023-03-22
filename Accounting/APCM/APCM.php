<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "APV.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>"> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">  
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F1
	    e.preventDefault();
		window.location = "APCM_new.php";
	  }
	});

function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){
	
	$("#typ").val(x);
	$("#aptyp").val(aptyp);
	$("#modzx").val(num);


		$("#AlertMsg").html("");
							
		$("#AlertMsg").html("Are you sure you want to "+x+" APV No.: "+num);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');
	

}

$(function(){
	var x = "";
	var num = "";
	
	$(".btnmodz").on("click", function (){
		if($('#AlertModal').hasClass('in')==true){
			var idz = $(this).attr('id');
			
			if(idz=="OK"){
				var x = $("#typ").val();
				var num = $("#modzx").val();
				
				if(x=="POST"){
					var msg = "POSTED";				
				}
				else if(x=="CANCEL"){
					var msg = "CANCELLED";
				}

					$.ajax ({
						url: "APCM_Tran.php",
						data: { x: num , typ:x},
						async: false,
						dataType: "json",
						beforeSend: function(){
							$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
							$("#alertbtnOK").hide();
							$("#OK").hide();
							$("#Cancel").hide();
							$("#AlertModal").modal('show');
						},
						success: function( data ) {
							console.log(data);
							$.each(data,function(index,item){
								
								itmstat = item.stat;
								
								if(itmstat!="False"){
									$("#msg"+num).html(item.stat);
									
										$("#AlertMsg").html("");
										
										$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
										$("#alertbtnOK").show();
										$("#OK").hide();
										$("#Cancel").hide();
										$("#AlertModal").modal('show');
				
								}
								else{
									$("#AlertMsg").html("");
									
									$("#AlertMsg").html(item.ms);
									$("#alertbtnOK").show();
									$("#OK").hide();
									$("#Cancel").hide();
									$("#AlertModal").modal('show');
				
								}
							});
						}
					});


			}
			else if(idz=="Cancel"){
				
				$("#AlertMsg").html("");
				$("#AlertModal").modal('hide');
				
			}




		}
	});
	
});

</script>
</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>AP Credit Memo</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" name="btnNewT" id="btnNewT"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>CM No</th>
            <th>Supplier</th>
            <th>Remarks</th>
						<!--<th>Payee</th>-->
						<!--<th>Trans Date</th>-->
            <th>CM Date</th>
						<th>Amount</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql = "select a.*,b.cname from apcm a left join suppliers b on a.ccode=b.ccode order by a.ddate DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?>>
						<td><a <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?> href="javascript:;" onClick="editgrp('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
 						<td><?php echo $row['ccode'];?> - <?php echo $row['cname'];?> </td>
                       	<td><?php echo $row['cremarks'];?></td>
                       <!-- <td><?php// echo $row['ddate'];?></td>-->
                        <td><?php echo $row['dcutdate'];?></td>
						<td><?php echo $row['ngross'];?></td>
						<td align="center">
                        <div id="msg<?php echo $row['ctranno'];?>">
                        	<?php 
							if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>')">CANCEL</a>
							<?php
                            }
							else{
								if(intval($row['lcancelled'])==intval(1)){
									echo "<b>Cancelled</b>";
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
				
				mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>			

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
                        <input type="hidden" id="aptyp" name="aptyp" value = "">
                        <input type="hidden" id="modzx" name="modzx" value = "">
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

	
	<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Add New Type</b></h5>        
      </div>

	<form name="frmapcm" id="frmapcm" method="post" action="APCM_newsavehdr.php">
		<input type="hidden" id="hdtsavetyp" name="hdtsavetyp" value="">
	  <div class="modal-body" style="height: 35vh">
    	
         <div class="col-xs-12">
            <div class="cgroup col-xs-2 nopadwtop" id="cGroup5">
                <b>Supplier</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                 <div class="col-xs-12 nopadding">
					<div class="col-xs-3 nopadding">
						<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" required>
						<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
					</div>

					<div class="col-xs-8 nopadwleft">
						<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" required>
					</div> 
				  </div>
            </div>
        </div>   

        <div class="col-xs-12">
            <div class="cgroup col-xs-2 nopadwtop" id="cGroup5">
                <b>Remarks</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                 <div class="col-xs-12 nopadding">
					<div class="col-xs-11 nopadding">
						<input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks"  placeholder="Enter Remarks..">
					 </div>	
				</div>
			</div>
                
        </div>   
 
		<div class="col-xs-12">
            <div class="cgroup col-xs-2 nopadwtop" id="cGroup5">
                <b>AP CM Date</b>
            </div>
            
            <div class="col-xs-3 nopadwtop">
                <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" />
            </div>
        </div> 

			 
		<div class="col-xs-12">
            <div class="cgroup col-xs-2 nopadwtop" id="cGroup5">
                <b>Reference</b>
            </div>
            
            <div class="col-xs-3 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtcrefno" name="txtcrefno"  placeholder="PR Trans. No..." readonly autocomplete="off">
            </div>
			
			<div class="col-xs-3 nopadwleft">
                <div class="checkbox">
				  <label><input type="checkbox" value="YES" name="chkwithref" id="chkwithref">With Reference</label>
				</div>
            </div>
			
        </div> 
		  

		<div class="col-xs-12">
            <div class="cgroup col-xs-2 nopadwtop" id="cGroup5">
                <b>Total Amount</b>
            </div>
            
            <div class="col-xs-3 nopadwtop">
                <input type="text" class="numeric form-control input-sm text-right font-weight-bold" id="txtnamt" name="txtnamt"  value="0.00" required>
            </div>
        </div> 		  
        <div class="alert alert-danger nopadding" id="add_err"></div>         

	
	</div>
    
 	<div class="modal-footer">
                <button type="Submit" id="btnSave" name="Save" class="btn btn-primary btn-sm">Save Transaction</button>
                <button type="Submit" id="btnUpdate" name="Update" class="btn btn-success btn-sm">Update Detail</button>
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
	</div>
    </form>
    </div>
  </div>
</div>
<!-- Modal -->	
	
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
		
	$(function(){
		$('form input:not([type="submit"])').keydown(function(e) {
			    if (e.keyCode == 13) {
					e.preventDefault();
					return false;
				}
		});
		
		$('#myModal').on('shown.bs.modal', function () {
			$('#txtcust').focus();
		})
		
		$("#add_err").hide();
		
									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
		
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				 //minDate: new Date(),
        });

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
		
		$('#example').DataTable({bSort:false});
		
		$("#btnNewT").on("click", function() {
		 var x = chkAccess('TYPE_New.php');
		 
		 if(x.trim()=="True"){
			$("#btnSave").show();
			$("#btnUpdate").hide();

			$("#txtccode").attr('readonly',false);
						
			$("#txtcust").val("");
			$("#txtcustid").val("");
			$("#txtremarks").val("");		
			$("#txtcrefno").val("");
			$("#txtnamt").val("0.00")
			$("#hdtsavetyp").val("new")
			 
			$("#txtcust").focus();
			$("#txtcrefno").attr("required", false);
			$("#txtcrefno").attr("readonly", true);	
			$("#txtnamt").attr("readonly", false);	
			 
			$('#myModalLabel').html("<b>Add New AP Credit Memo</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }
		});

		$("#txtcustid").keyup(function(event){
		if(event.keyCode == 13){
		
		var dInput = this.value;
		
		$.ajax({
        type:'post',
        url:'../get_supplierid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){
			//alert(value);
			if(value!=""){
				var data = value.split(":");
				$('#txtcust').val(data[0]);
							
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
								
			}
			else{
				$('#txtcustid').val("");
				$('#txtcust').val("");
				
				$('#hdnvalid').val("NO");
			}
		},
		error: function(){
			$('#txtcustid').val("");
			$('#txtcust').val("");
			
			$('#hdnvalid').val("NO");
		}
		});

		}
		
	});

	$('#txtcust, #txtcustid').on("blur", function(){
		if($('#hdnvalid').val()=="NO"){
		  $('#txtcust').attr("placeholder", "ENTER A VALID SUPPLIER FIRST...");
		  
		 // $('#txtprodnme').attr("disabled", true);
		 // $('#txtprodid').attr("disabled", true);
		}else{
			
		 // $('#txtprodnme').attr("disabled", false);
		 // $('#txtprodid').attr("disabled", false);
		  
		  $('#txtremarks').focus();
	
		}
	});
	//Search Cust name
	$('#txtcust').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_supplier.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();			
			
		}
	
	});
		
		$('#txtcrefno').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "get_refPR.php?id="+$('#txtcustid').val(),
					dataType: "json",
					data: {
						query: $("#txtcrefno").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.ddate + ' - ' + item.value + '</small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					

				$('#txtcrefno').val(item.id).change(); 	
				$('#txtnamt').val(item.value);
				$("#txtnamt").attr("readonly", true);

			}
		});
		
		$("#frmapcm").submit(function (ev) {

			if($("#txtnamt").val() != 0) {
				
			
			var formData = $("#frmapcm").serialize();
				
			//alert("APCM_newsavehdr.php?"+formData)
			$.ajax ({
				url: "APCM_newsavehdr.php",
				data: formData,
				async: false,
				beforeSend: function(){
					//$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW CREDIT MEMO: </b> Please wait a moment...");
					//$("#alertbtnOK").hide();
					//$("#AlertModal").modal('show');
				},
				success: function( data ) {
					if(data.trim()!="False"){
						alert("Record Successfully Saved!");
						$('#myModal').modal('hide');
						window.location = "APCM.php";
					}
				}
			});
			
			}else{
				alert("Zero amount is not accepted!");
				$('#txtnamt').focus();
			}
			
		ev.preventDefault();
			
		});
		
		$("#chkwithref").click(function(){ 
			  if($(this).prop("checked") == true) {
				  if($("#txtcustid").val()=="" || $("#txtcust").val()=="" ){
					 alert("Select a valid supplier!");
					 $(this).prop("checked", false);
				   }else{
				  	$("#txtcrefno").attr("required", true);
				    $("#txtcrefno").attr("readonly", false);
			  	   }
              }
              else if($(this).prop("checked") == false) {
					$("#txtcrefno").val("");
				  	$("#txtcrefno").attr("required", false);
				    $("#txtcrefno").attr("readonly", true);
              }
		});
		
		

});
		
	function editgrp(code){
		 var x = chkAccess('TYPE_Edit.php');

		 if(x.trim()=="True"){
			$("#btnSave").hide();
			$("#btnUpdate").show();
			$("#hdtsavetyp").val(code)
			 
			//get details
			 $.ajax({
                    url: 'th_apcmdet.php',
					data: { code:code },
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {

                       console.log(data);
                       $.each(data,function(index,item){
						  
						   $("#txtcustid").val(item.ccode);
						   $("#txtcust").val(item.cname);
						   $("#txtremarks").val(item.crem);
						   $("#txtcrefno").val(item.crefno);
						   $("#date_delivery").val(item.ddate);
						   if(item.cwithref==1){
							   	$("#chkwithref").prop("checked",true);
				  				$("#txtcrefno").attr("required", true);
				    			$("#txtcrefno").attr("readonly", false);
							   
							    $("#txtnamt").attr("readonly", true);
						   }else{
				  				$("#txtcrefno").attr("required", false);
				    			$("#txtcrefno").attr("readonly", true);	
							    
							    $("#txtnamt").attr("readonly", false);
						   }
						   $("#txtnamt").val(item.ngross);
						   
						   if(item.lapproved==1 || item.lcancelled==1){
							  $("#btnUpdate").attr("disabled",true);
						   }
						   
					   });
						
					}
			 });			
			
			$('#myModalLabel').html("<b>Update Detail ("+code+")</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');
		 }

	}
		
		
	 function chkAccess(id){
			var result;
			
			$.ajax ({
				url: "../../MasterFiles/Items/chkAccess.php",
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