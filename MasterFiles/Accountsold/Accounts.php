<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Accounts.php";

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

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
<link href="../../Bootstrap/css/jquery.bootstrap.treeselect.css" rel="stylesheet">


<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>



</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Chart of Accounts</u></font>	
            </div>
        </div>
			<br><br>
           			 <button type="button" data-toggle="modal" class="btn btn-primary btn-md" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
			<br><br>			
			<table class="table table-hover" role="grid" id="MyTable">
				<thead>
					<tr>
						<th>Acct No</th>
						<th>Description</th>
						<th>Category</th>
						<th>Type</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
        <h5 class="modal-title" id="myModalLabel"><b>New Account</b></h5>
        
      </div>
      <div class="modal-body">
         <div class="err" id="add_err"></div>
      
      	<form method="post" name="frmAdd" id="frmAdd" enctype="multipart/form-data" action = "">
        
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100"><b>Account No: </b></td>
            <td style="padding:2px">
            <div class="col-xs-7">
            	<input type="text" class="form-control input-sm" placeholder="Account No..." name="acctid" id="acctid" required autofocus>
            </div>
            </td>
          </tr>
          <tr>
            <td><b>Decription: </b></td>
            <td style="padding:2px">
             <div class="col-xs-12">
            	<input type="text" class="form-control input-sm" placeholder="Decsription..." name="cdesc" id="cdesc" required>
             </div>
            </td>
          </tr>

          <tr>
            <td><b>Accnt Group: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7">
                <select name="cfingrp" id="cfingrp" class="form-control input-sm">
					<option value="Income Statement">P&amp;L Statement Account</option>
                    <option value="Balance Sheet">Balance Sheet Account</option>
                </select>
             </div>

            </td>
          </tr>

          <tr>
            <td><b>Type: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7">
                <select name="seltyp" id="seltyp" class="form-control input-sm">
					<option value="General">General</option>
                    <option value="Details">Details</option>
                </select>
             </div>

            </td>
          </tr>

          <tr>
            <td><b>Category: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7">
                <select name="selcat" id="selcat" class="form-control input-sm">
                <option value="">Select Category</option>
					<?php
                    	$sql0 = "Select distinct ccategory from accounts where compcode='".$_SESSION['companyid']."'";

						$result0=mysqli_query($con,$sql0);
						
							if (!mysqli_query($con, $sql0)) {
								printf("Errormessage: %s\n", mysqli_error($con));
							} 

						while($row0 = mysqli_fetch_array($result0, MYSQLI_ASSOC))

						{

					?>
                    	<option value="<?php echo $row0["ccategory"];?>"><?php echo $row0["ccategory"];?></option>
                    <?php
						}
					?>
                </select>
             </div>

            </td>
          </tr>

         <tr>
            <td><b>Main Account: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7" id="divmainacc">
             </div>
                         
            </td>
          </tr>

        </table>
      

        </form>
        
      </div>
      
      <div class="modal-footer">
                <button type="button" id="btnSave" name="Save" class="btn btn-primary">Add New Account</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

      </div>
    </div>
  </div>
</div>
<!-- Modal -->		



<!-- Update Details  -->
<div class="modal fade" id="myUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
        <h5 class="modal-title" id="myModalLabel"><b>Update Account</b></h5>
        
      </div>
      <div class="modal-body" style="height:35vh">
         <div class="err" id="add_err2"></div>
      
      	<form method="post" name="frmUpdate" id="frmUpdate" enctype="multipart/form-data" action = "">
        
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="150"><b>Account No: </b></td>
            <td style="padding:2px">
            <div class="col-xs-7">

							<input type="hidden" name="acctidcode" id="acctidcode" value="">
            	<input type="text" class="form-control input-sm" placeholder="Account No..." name="acctid2" id="acctid2" autofocus>
            </div>
            </td>
          </tr>
          <tr>
            <td><b>Decription: </b></td>
            <td style="padding:2px">
             <div class="col-xs-12">
            	<input type="text" class="form-control input-sm" placeholder="Decsription..." name="cdesc2" id="cdesc2" required>
             </div>
            </td>
          </tr>

          <tr>
            <td><b>Accnt Group: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7">
                <select name="cfingrp2" id="cfingrp2" class="form-control input-sm">
					<option value="Income Statement">P&amp;L Statement Account</option>
                    <option value="Balance Sheet">Balance Sheet Account</option>
                </select>
             </div>

            </td>
          </tr>

          <tr>
            <td><b>Type: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7">
                <select name="seltyp2" id="seltyp2" class="form-control input-sm">
					<option value="General">General</option>
                    <option value="Details">Details</option>
                </select>
             </div>

            </td>
          </tr>

          <tr>
            <td><b>Category: </b></td>
            <td style="padding:2px">
             
             <div class="col-xs-7">
                <select name="selcat2" id="selcat2" class="form-control input-sm">
                <option value="">Select Category</option>
					<?php
                    	$sql0 = "Select distinct ccategory from accounts where compcode='".$_SESSION['companyid']."'";

						$result0=mysqli_query($con,$sql0);
						
							if (!mysqli_query($con, $sql0)) {
								printf("Errormessage: %s\n", mysqli_error($con));
							} 

						while($row0 = mysqli_fetch_array($result0, MYSQLI_ASSOC))

						{

					?>
                    	<option value="<?php echo $row0["ccategory"];?>"><?php echo $row0["ccategory"];?></option>
                    <?php
						}
					?>
                </select>
             </div>

            </td>
          </tr>
					<!--
         <tr>
            <td><b>Main Account: </b></td>
            <td style="padding:2px">
             <div class="col-xs-7" id="divmainacc2">
             </div>

            </td>
          </tr>
					-->
         <tr>
            <td colspan="2" height="35px">&nbsp;</td>
          </tr>

		  <tr>
            <td>
            	<label><input type="checkbox" value="YES" name="chkcontra2" id="chkcontra2">&nbsp;&nbsp;Contra A/c</label>
            </td>
            <td style="padding:2px" colspan="2">
             <div class="col-xs-7" id="divcontraacc2">
             </div>
                         
            </td>
          </tr>

        </table>
      

        </form>
        
      </div>
      
      <div class="modal-footer">
                <button type="button" id="btnUpdate" name="Update" class="btn btn-primary">UpdateAccount</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

      </div>
    </div>
  </div>
</div>
<!-- Modal -->		



<?php

mysqli_close($con);
?>
</body>
</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(document).ready(function() {
		var GENxyz
		var GENxyz0
		var table = $('#MyTable').DataTable( {
			"searching": true,
        	"paging": true,
			"columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
							
							if(full[4]==null){
								return full[9];
							}else{
								return "<a href=\"javascript:;\" onClick=\"editacct('"+full[9]+"','"+full[0]+"','"+full[1]+"','"+full[3]+"','"+full[2]+"','"+full[4]+"','"+full[6]+"','"+full[7]+"','"+full[8]+"')\">"+full[9]+"</a>";
							}
					}
						
				},
				{ "data": null,
					"render": function (data, type, full, row) {
						var GENxyz = parseInt(full[5]);
						
						var GENxyz0 = 0;
						if(GENxyz>1){
							GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
						}
						
						return "<div style='text-indent:"+GENxyz0+"px'> "+full[1]+"</div>";
					}
				},
				{ "data": 2 },
				{ "data": 3 }
				
        	],
			"order": [[ 0, "asc" ]],
			"serverSide": true,
			"ajax": {
				url: "serverside.php",
				type: "POST",
			},
			"columnDefs": [
				{ "targets": 3, "className": "text-center" } 
			],
		} );
			
	
	} );
	
	$(function(){
				
		// Adding new account
		$("#btnadd").on("click", function() {
			$("#divmainacc").html("");
			$('#myModal').modal('show');
		});
		
		$("#acctid").on("keyup", function (){
			var x = $(this).val();
			
			if(x != "") {
				$.ajax({
				type:'post',
					url:'Accounts_chkID.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						if(msg.trim() !=""){
							$("#add_err").css('display', 'inline', 'important');
							$("#add_err").html("<div class='alert alert-danger' role='alert'>&nbsp;&nbsp;" + msg + "</div>");
						}
						else{
							$("#add_err").css('display', 'none', 'important');
						}
					}
				});
			}
			
		});


		//Check new user id
		$("#acctid").on("blur", function () {
			
			var x = $(this).val();
			
			if(x != "") {
				$.ajax({
				type:'post',
					url:'Accounts_chkID.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						if(msg.trim()!=""){
							$("#add_err").css('display', 'none', 'important'); // your message will come here. 
							$("#acctid").val("").change();
							$("#acctid").focus(); 
						}
					}
				});
			}
		});

		
		$("#selcat").on("change", function() {
			
			var xy = $(this).val();
				
				$.ajax({
					type: "POST",
					url: "Accounts_getgeneral.php",
					data: { 'Id': xy  },
					success: function(data){
						$("#divmainacc").html(data);
						
						$('.btnsel').click(function(){
							if ($(this).is(':checked'))
							{
								
							  $("#btnlogo").text($(this).val());
							}
						  });					
					}
				});
				
			//}
		});


		$("#btnSave").on("click", function(){
			
			var numz = 0;
			$('form#frmAdd input[type=text]').each(function(){
			   if (this.value == "") {
				   numz = numz + 1;
					$("#add_err").css('display', 'inline', 'important');
					$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Complete the form</div>");
			   } 
			})
			
			
		if(parseInt(numz)==0){
			
				var fd = document.getElementById("frmAdd");
				var formData = new FormData(fd);
				
				
					$.ajax({
						type: 'post',
						url: 'Accounts_add.php',
						data: formData,
   					    contentType: false,
						processData: false,
						async:false,
						success: function (data) {
						  alert(data);
						  
						  $('#myModal').modal('hide');
						  location.reload();
						}
					});

		}


		});



	$("#btnUpdate").on("click", function(){
			
			var numz = 0;
			$('form#frmUpdate input[type=text]').each(function(){
			   if (this.value == "") {
				   numz = numz + 1;
					$("#add_err2").css('display', 'inline', 'important');
					$("#add_err2").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Complete the form</div>");
			   } 
			})
			
			
		if(parseInt(numz)==0){
			
				var fd = document.getElementById("frmUpdate");
				var formData = new FormData(fd);
				
				
					$.ajax({
						type: 'post',
						url: 'Accounts_update.php',
						data: formData,
   					    contentType: false,
						processData: false,
						async:false,
						success: function (data) {
						  alert(data);
						  
						  $('#myModal').modal('hide');
						  location.reload();
						}
					});

		}


		});
		
		$("#chkcontra2").change(function() {
			if(this.checked) {
			
			  var wx = $("#acctid2").val();
			  var xy = $("#selcat2").val();			  
			  var yz = $('input[name=selmain2]:checked').val();
			  
			  	xyz = yz.split(":");
				
				$.ajax({
					type: "POST",
					url: "Accounts_getcontra.php",
					data: { Cat: xy, Main:xyz[0], Id:wx, sel:""  },
					success: function(data){
						$("#divcontraacc2").html(data);
						
						$('.btncontra2').click(function(){
							if ($(this).is(':checked'))
							{
								
							  $("#btnClogo").text($(this).val());
							}
						  });					
					}
				});

			}
			else{
				alert("NO");
			}
		});
		


	});
	
	
	function editacct(id,codeno,name,typ,cat,mId,fngrp,lcon,conid){
			$("#divmainacc2").html("");
			
			$("#acctidcode").val(codeno);
			$("#acctid2").val(id);
			$("#cdesc2").val(name);
			$("#seltyp2").val(typ);
			$("#selcat2").val(cat);
			$("#cfingrp2").val(fngrp);
			
				if(parseInt(lcon)==1){
					$('#chkcontra2').prop('checked', true);
					
					$.ajax({
					type: "POST",
					url: "Accounts_getcontra.php",
					data: { Cat: cat, Main:mId, Id:id, sel:conid, codeno:codeno  },
					success: function(data){
						$("#divcontraacc2").html(data);
						
						$('.btncontra2').click(function(){
							if ($(this).is(':checked'))
							{
								
							  $("#btnClogo").text($(this).val());
							}
						  });					
					}
				});

				}

				/*
				var xy = cat;				
				$.ajax({
					type: "POST",
					url: "Accounts_getgeneral2.php",
					data: { 'Id': xy, 'mid': mId  },
					success: function(data){
						$("#divmainacc2").html(data);
						
						$('.btnsel').click(function(){
							if ($(this).is(':checked'))
							{
								
							  $("#btnlogo").text($(this).val());
							}
						  });					
					}
				});

				*/

		//	$("#acctid2").attr("readonly", true);
			
			$('#myUpdate').modal('show');

	}

	</script>
