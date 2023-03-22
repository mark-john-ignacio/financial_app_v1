<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');


$company = $_SESSION['companyid'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">   
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css"> 
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
$(document).keydown(function(e) {	
	e.preventDefault();
	 
	  if(e.keyCode == 112) { //F2
		window.location = "ARAdj_new.php";
	  }
});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num,msg,id,xcred){
var itmstat = "";

	$.ajax ({
		url: "ARAdj_Tran.php",
		data: { x: num, typ: x },
		async: false,
		dataType: "json",
		beforeSend: function(){
			$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
			$("#alertbtnOK").hide();
			$("#AlertModal").modal('show');
		},
		success: function( data ) {
			
			console.log(data);
			$.each(data,function(index,item){
				
				itmstat = item.stat;
				
				if(itmstat!="False"){
					$("#msg"+num).html(item.stat);
					
						$("#AlertMsg").html("");
						
						$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+item.stat+"...");
						$("#alertbtnOK").show();
						//$("#AlertModal").modal('show');

				}
				else{
					$("#AlertMsg").html("");
					
					$("#AlertMsg").html(item.ms);
					$("#alertbtnOK").show();
					//$("#AlertModal").modal('show');

				}
			});
		}
	});


}

function chktyp(){
	location.href='ARAdj_new.php'
}
</script>
</head>

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus();">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Credit Memo List</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary btn-md" onClick="location.href='CM_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>CM No</th>
						<th>Customer</th>
                        <th>CM Date</th>
						<th>Type</th>
						<th>Gross</th>
                        <th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql =$sql = "select a.*,b.cname from aradj a left join customers b on a.ccode=b.cempid Where a.compcode='$company' and a.ctype='Credit' order by a.ddate desc";;
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
						<td><?php echo $row['ccode'];?> - <?php echo utf8_encode($row['cname']);?> </td>
                        <td><?php echo $row['dcutdate'];?></td>
                        <td><?php echo $row['ctype'];?></td>
						<td align="right"><?php echo $row['ngross'];?></td>
                        <td align="center">
                        <div id="msg<?php echo $row['ctranno'];?>">
                        	<?php 
							if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>','Posted')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>','Cancelled')">CANCEL</a>
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
    
<form name="frmedit" id="frmedit" method="post" action="CM_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		


<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>


    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$('#example').DataTable({bSort:false});
	</script>

</body>
</html>