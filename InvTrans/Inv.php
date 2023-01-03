<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvTrans.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px; height:750px">
	<div>
		<section>
         <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Inventory Transfer</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" onClick="javascript:;" id="btnSet"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
<br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Transaction Code</th>
						<th>Remarks</th>
                        <th>Prepared By</th>
                        <th>Date Prepared</th>
                        <th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql = "SELECT ctranno, cremarks, ddatetime, lcancelled, lapproved , cpreparedby FROM invtransfer order by ddatetime DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{

	
				?>
 					<tr>
                        <td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>','InvCnt_Edit.php');"><?php echo $row['ctranno'];?></a></td>
                       <td><?php echo $row['cremarks'];?></td>
                        <td><?php echo $row['cpreparedby'];?></td>
                        <td><?php echo $row['ddatetime'];?></td>
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
     
<form name="frmNew" id="frmNew" method="post" action="InvCnt_New.php">
	<input type="hidden" name="month" id="month" />
    <input type="hidden" name="year" id="year" />
</form>		

<form name="frmEdit" id="frmEdit" method="post" action="InvCnt_Edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
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
	$(document).ready(function(e) {	
	
		$("#divimgload").hide();
	
	});

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F2
	    e.preventDefault();
		window.location = "InvAdj.php";
	  }
	});

	
	$(function(){
		
	$('#example').DataTable({bSort:false});		
	
	var x = "";
	var num = "";
	
	$("#btnSet").on('click', function() {
		$("#frmNew").submit();
	}); 
		
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
						url: "InvCnt_Tran.php",
						data: { x: num, typ: x },
						async: false,
						success: function( data ) {
							//alert(data.trim());
							if(data.trim()!="False"){
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

										$("#AlertMsg").html("");
										
										$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
										$("#alertbtnOK").show();
										$("#OK").hide();
										$("#Cancel").hide();
										$("#AlertModal").modal('show');


				}

			}
			else if(idz=="Cancel"){
				
				$("#AlertMsg").html("");
				$("#AlertModal").modal('hide');
				
			}




		}
	});
	
	});

function editfrm(x,url){
	document.getElementById("txtctranno").value = x;
	document.frmEdit.action = url;
	document.getElementById("frmEdit").submit();
}

function trans(x,num){
	
	$("#typ").val(x);
	$("#modzx").val(num);


		$("#AlertMsg").html("");
							
		$("#AlertMsg").html("Are you sure you want to "+x+" Inv Count No.: "+num);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');
	

}


	</script>

</body>
</html>