<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Deposit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];


$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.compcode=b.compcode and a.cvalue=b.cacctno where a.compcode='$company' and a.ccode='DEPDEBIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
			$nDebitDesc = $row['cacctdesc'];
		}
	}else{
		$nDebitDef = "";
		$nDebitDesc =  "";
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx   Financials</title>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F2
	    e.preventDefault();
		window.location = "Deposit_new.php";
	  }
	});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){
	
	$("#typ").val(x);
	$("#modzx").val(num);


		$("#AlertMsg").html("");
							
		$("#AlertMsg").html("Are you sure you want to "+x+" Deposit No.: "+num);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');
	

}
</script>
</head>

<body style="padding:5px; height:750px">
		<div>
			<section>
        <div>
        	<div style="float:left; width:50%">
						<font size="+2"><u>Bank Deposit</u></font>	
          </div>
        </div>
			<br><br>

			<button type="button" class="btn btn-primary" onClick="location.href='Deposit_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

			<!--
    	<button type="button" class="btn btn-warning btn-md" id="btnSet" name="btnSet"><span class="glyphicon glyphicon-cog"></span> Settings</button>
-->
                 
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Trans No.</th>
            <th>Deposit Acct</th>
						<th>Amount</th>
						<th>Date</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql = "select a.*,b.cacctdesc from deposit a left join accounts b on a.compcode=b.compcode and a.cacctcode=b.cacctno where a.compcode='$company' order by a.ddate DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
 						<td><?php echo $row['cacctcode'];?> - <?php echo $row['cacctdesc'];?> </td>
                       <td><?php echo $row['namount'];?></td>
                        <td><?php echo $row['dcutdate'];?></td>
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
				
				mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="Deposit_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		



<!--CASH DETAILS DENOMINATIONS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">DEPOSIT SETUP</h3>
            </div>
            <div class="modal-body">

                <form method="post" name="frmSet" id="frmSet" action="Deposit_setsave.php">
                	<fieldset>
                    <legend>Account Settings</legend>
										<table width="100%" border="0" cellpadding="0" align="right">
											<tr>
												<th scope="row" width="200">Default Deposit to Account</th>
												<td style="padding:2px">
												
												
												<div class="col-xs-10"><input type="text" class="form-control input-xs" name="paydebit" id="paydebit" placeholder="Search Account Description..." required tabindex="1" value="<?php echo $nDebitDesc; ?>"> <input type="hidden" name="paydebitid" id="paydebitid"  value="<?php echo $nDebitDef; ?>"> </div></td>
											</tr>

											<tr>
												<th scope="row" width="200">On Hand Account</th>
												<td style="padding:2px">
												
												
												<div class="col-xs-10"><input type="text" class="form-control input-xs" name="payonhand" id="payonhand" placeholder="Search Account Description..." required tabindex="1" value="<?php echo $nDebitDesc; ?>"> <input type="hidden" name="paydebitid" id="paydebitid"  value="<?php echo $nDebitDef; ?>"> </div></td>
											</tr>

										</table>                
               		</fieldset>
									<br><br>
									<center>
										<button type="button" class="btn btn-success btn-sm" name="setSubmit" id="setSubmit"><span class="glyphicon glyphicon glyphicon-floppy-disk"></span> Save</button>
									</center>
                </form>
                
            </div>
            <div class="modal-footer">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

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


    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(function(){
		
		$('#example').DataTable({bSort:false});
		
		$("#btnSet").on('click', function() {
			$('#SetModal').modal('show');
		});

		$('#paydebit').typeahead({
	
		source: function (query, process) {
			return $.getJSON(
				'th_accounts.php',
				{ query: query },
				function (data) {
					newData = [];
					map = {};
					
					$.each(data, function(i, object) {
						map[object.name] = object;
						newData.push(object.name);
					});
					
					process(newData);
				});
		},
		updater: function (item) {	
			  
				$('#paydebitid').val(map[item].id);
				return item;
		}
	
		}); 

	
		$("#setSubmit").on('click', function(){
			var id = $("#paydebitid").val();
			var desc = $("#paydebit").val();
			// Returns successful data submission message when the entered information is stored in database.
			var dataString = 'id='+ id + '&desc='+ desc;
			if(id==''|| desc=='')
			{
				alert("Please Select a valid account!");
			}
			else
			{
				// AJAX Code To Submit Form.
				$.ajax({
				type: "POST",
				url: "Deposit_setsave.php",
				data: dataString,
				cache: false,
				success: function(result){
					alert(result);
					
					$('#SetModal').modal('hide');
				}
				});
			}
			return false;
		});
		
	var x = "";
	var num = "";
	
	$(".btnmodz").on("click", function (){
	var itmstat = "";	
		
		if($('#AlertModal').hasClass('in')==true){
			var idz = $(this).attr('id');
			
			if(idz=="OK"){
				var x = $("#typ").val();
				var num = $("#modzx").val();
				
				if(x=="POST"){
					var msg = "POSTED";
					
					//generate GL ENtry muna
					$.ajax ({
						dataType: "text",
						url: "../include/th_toAcc.php",
						data: { tran: num, type: "BD" },
						async: false,
						success: function( data ) {
							//alert(data.trim());
							if(data.trim()=="True"){
								itmstat = "OK";								
							}
							else{
								itmstat = data.trim();	
							}
						}
					});
					
				}
				else if(x=="CANCEL"){
					var msg = "CANCELLED";
					itmstat = "OK";
				}


				if(itmstat=="OK"){
					$.ajax ({
						url: "Deposit_Tran.php",
						data: { x: num, typ: x },
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


			}
			else if(idz=="Cancel"){
				
				$("#AlertMsg").html("");
				$("#AlertModal").modal('hide');
				
			}




		}
	});
	
	});



	</script>

</body>
</html>