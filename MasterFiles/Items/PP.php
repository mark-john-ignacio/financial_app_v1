<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PP.php";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');
?>
<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
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
				<font size="+2"><u>Purchase Pricelist</u></font>	
            </div>
            
        </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
			<button type="button" class="btn btn-warning btn-sm" id="btnmass" name="btnmass"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Mass Upload</button>
<br><br>    
                
                <table id="example" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th width="100">PM Batch No.</th>
                            <th width="150">Effectivity Date</th>
                            <th>Supplier</th>
                            <th>Remarks</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
    
                    <tbody>
                    <?php
                    $company = $_SESSION['companyid'];
                    	$sql = "SELECT A.ctranno, A.deffectdate, A.ccode, A.cremarks, A.lapproved, A.lcancelled, B.cname FROM `items_purch_cost` A left Join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode WHERE A.compcode='$company' order by deffectdate desc";
                    
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
                                <?php echo $row['ccode']." - ".$row['cname'];?>
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



<?php
mysqli_close($con);
?>


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


<form method="post" name="frmedit" id="frmedit" action="PP_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

	
	<script type="text/javascript">
	
	$(function(){
		$('#example').DataTable({
			"order": [[ 2, "desc" ]]
		});
		$("#add_err").hide();
		$("#add_errpick").hide();

		$('#btnmass').click(function(){
			window.location="../../MassUpload/PurchasePrice.php"
		})

		$("#btnadd").on("click", function() {
			 var x = chkAccess('PP_New.php');
	
			 if(x.trim()=="True"){
				location.href = "PP_new.php";
			 }
			 else{
				 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
					$("#alertbtnOK").show();
					$("#OK").hide();
					$("#Cancel").hide();
				 $("#AlertModal").modal('show');
	
			 }
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

				 var x = chkAccess('PP_'+typ);
		
				 if(x.trim()=="True"){
					$.ajax ({
						url: "PP_Trans.php",
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
			 var x = chkAccess('PP_Edit.php');
	
			 if(x.trim()=="True"){
				$("#txtctranno").val(val);
				$("#frmedit").submit();
			 }
			 else{
				 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
				 $("#alertbtnOK").show();
				 $("#OK").hide();
				 $("#Cancel").hide();							
				 $("#AlertModal").modal('show');
			 }

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
