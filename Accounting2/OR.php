<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
                    <?php
					$company = $_SESSION['companyid'];
					
					$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.compcode=b.compcode and a.cvalue=b.cacctno where a.compcode='$company' and a.ccode='ORDEBIT'");
					
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

	<title>COOPERATIVE SYSTEM</title>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 112) { //F2
		window.location = "OR_new2.php";
	  }
	});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){
	var r = confirm("Are you sure you want to "+x+" Receipt No.: "+num);
	if(r==true){
	var page = 'OR_Tran.php?x='+num+'&typ='+x;
	var name = 'popwin';
	var w = 100;
	var h = 100;
	var myleft = (screen.width)?(screen.width-w)/2:100;
	var mytop = (screen.height)?(screen.height-h)/2:100;
	var setting = "width=" + w + ",height=" + h + ",top=" + mytop + ",left=" + myleft + ",scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no";
	myPopup = window.open(page, name, setting);
	}
}
</script>
</head>

<body style="padding:5px; height:750px">
	<div>
		<section>
 
          <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Receive Payment</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" onClick="location.href='OR_new2.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

              <button type="button" class="btn btn-warning btn-md" id="btnSet" name="btnSet"><span class="glyphicon glyphicon-cog"></span> Settings</button>

        
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>OR No</th>
						<th>Trans No.</th>
                        <th>Payor</th>
						<th>Payment Method</th>
						<th>Date</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql = "select a.*,b.cname, c.cname as suppname from receipt a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join suppliers c on a.ccode=c.ccode where a.compcode='$company' order by a.ddate DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
						$ccustname = $row['cname'];
						if($ccustname==""){
							$ccustname = $row['suppname'];
						}
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
						<td><?php echo $row['cornumber'];?></td>
 						<td><?php echo $row['ccode'];?> - <?php echo $ccustname;?> </td>
                        <td align="right"><?php echo $row['namount'];?></td>
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
    
<form name="frmedit" id="frmedit" method="post" action="OR_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		



<!--CASH DETAILS DENOMINATIONS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">OR SETUP</h3>
            </div>
            <div class="modal-body">

                <form method="post" name="frmSet" id="frmSet" action="OR_setsave.php">
                <fieldset>
                    <legend>Account Settings</legend>
                <table width="95%" border="0" cellpadding="0" align="right">
                  <tr>
                    <th scope="row" width="170">Deposit to Account</th>
                    <td style="padding:2px">
                    
                    
                    <div class="col-xs-10"><input type="text" class="form-control input-xs" name="paydebit" id="paydebit" placeholder="Search Account Description..." required tabindex="1" value="<?php echo $nDebitDesc; ?>"> <input type="hidden" name="paydebitid" id="paydebitid"  value="<?php echo $nDebitDef; ?>"> </div></td>
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


    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
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
				url: "OR_setsave.php",
				data: dataString,
				cache: false,
				success: function(result){
					alert(result);
				}
				});
			}
			return false;
		});


	</script>

</body>
</html>