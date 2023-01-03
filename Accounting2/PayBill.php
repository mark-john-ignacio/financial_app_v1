<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill.php";

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

	<title>COOPERATIVE SYSTEM</title>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">    
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../js/bootstrap3-typeahead.min.js"></script>
    
    <script src="../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">


	$(document).keypress(function(e) {	 
	  if(e.keyCode == 112) { //F2
		window.location = "PayBill_new.php";
	  }
	});


function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){
	var r = confirm("Are you sure you want to "+x+" Sales Return No.: "+num);
	if(r==true){
	var page = 'Received_Tran.php?x='+num+'&typ='+x;
	var name = 'popwin';
	var w = 100;
	var h = 100;
	var myleft = (screen.width)?(screen.width-w)/2:100;
	var mytop = (screen.height)?(screen.height-h)/2:100;
	var setting = "width=" + w + ",height=" + h + ",top=" + mytop + ",left=" + myleft + ",scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no";
	myPopup = window.open(page, name, setting);
	}
}

function set(){
				var left = (screen.width/2)-(500/2);
				var top = (screen.height/2)-(400/2);
				var sFeatures="dialogHeight: 400px; dialogWidth: 500px; dialogTop: " + top + "px; dialogLeft: " + left + "px;";
				
				var url = "PayBill_set.php?"
				
				window.showModalDialog(url, "", sFeatures)

}
</script>
</head>

<body style="padding:5px; height:900px">
	<div>
		<section>

          <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Pay Bills</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" onClick="location.href='PayBill_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
             <button type="button" class="btn btn-warning btn-md" name="btnSet" id="btnSet"><span class="glyphicon glyphicon-cog"></span> Settings</button>

            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>CV No</th>
						<th>Check No.</th>
                        <th>Supplier</th>
						<th>Payee</th>
						<th>CV Date</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql = "select a.*,b.cname from paybill a left join suppliers b on a.ccode=b.ccode order by a.ddate DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
						<td><?php echo $row['cchkno'];?></td>
 						<td><?php echo $row['ccode'];?> - <?php echo $row['cname'];?> </td>
                       <td><?php echo $row['cpayee'];?></td>
                        <td><?php echo $row['dcvdate'];?></td>
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
				
				
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="PayBill_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		


<!--SETTINGS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CV SETUP</h3>
            </div>
            <div class="modal-body">

                <form method="post" name="frmSet" id="frmSet" action="PayBill_setsave.php">
                    <fieldset>
                        <legend>Account Settings</legend>
                    <table width="95%" border="0" cellpadding="0" align="right">
                      <tr>
                        <th scope="row" width="170">Default Debit Account</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='CVDEBIT'");
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
                        
                        <div class="col-xs-10"><input type="text" class="form-control input-xs" name="paydebit" id="paydebit" placeholder="Search Account Description..." required tabindex="1" value="<?php echo $nDebitDesc;?>"> <input type="hidden" name="paydebitid" id="paydebitid"  value="<?php echo $nDebitDef;?>"> </div></td>
                      </tr>
                      <tr>
                        <th scope="row">Credit Account (APV)</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='CVCREDIT'");
                        if (mysqli_num_rows($sqlchk)!=0) {
                            while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
                                $nCreditDef = $row['cvalue'];
                                $nCreditDesc = $row['cacctdesc'];
                            }
                        }else{
                            $nCreditDef = "";
                            $nCreditDesc = "";
                        }
                        ?>
                        
                        <div class="col-xs-10"><input type="text" class="form-control input-xs" name="paycredit" id="paycredit" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>"> <input type="hidden" name="paycreditid" id="paycreditid" value="<?php echo $nCreditDef;?>"> </div></td>
                      </tr>
                    </table>
                    
                    </fieldset>
                    <br>
                    <fieldset>
                        <legend>PrintOut Settings</legend>
                      <table width="95%" border="0" cellpadding="0"  align="right">
                          <tr>
                            <th scope="row"  width="170">Prepared By</th>
                            <td style="padding:2px">
                        <?php
                            $sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVPREP'");
                        if (mysqli_num_rows($sqlchk)!=0) {
                            while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
                                $cprepared = $row['cvalue'];
                            }
                        }else{
                            $cprepared = "";
                        }
                        ?>
                    
                            <div class="col-xs-10"><input type="text" class="form-control input-xs" name="cprepared" id="cprepared" placeholder="Enter Name or Initials..."  tabindex="3" value="<?php echo $cprepared;?>"></div></td>
                            <td style="padding:2px; widows:170px"><b>Verified By</b></td>
                            <td style="padding:2px"><?php
                            $sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVVERI'");
                        if (mysqli_num_rows($sqlchk)!=0) {
                            while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
                                $cverify = $row['cvalue'];
                            }
                        }else{
                            $cverify = "";
                        }
                        ?>
                              <div class="col-xs-10">
                                <input type="text" class="form-control input-xs" name="cverified" id="cverified" placeholder="Enter Name or Initials..." tabindex="5" value="<?php echo $cverify;?>">
                            </div></td>
                          </tr>
                          <tr>
                            <th scope="row">Reviewed By</th>
                            <td style="padding:2px">
                        <?php
                            $sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVREVW'");
                        if (mysqli_num_rows($sqlchk)!=0) {
                            while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
                                $creview = $row['cvalue'];
                            }
                        }else{
                            $creview = "";
                        }
                        ?>
                    
                            <div class="col-xs-10"><input type="text" class="form-control input-xs" name="creviewed" id="creviewed" placeholder="Enter Name or Initials..." tabindex="4" value="<?php echo $creview;?>"></div></td>
                            <td style="padding:2px"><b>Approved By</b></td>
                            <td style="padding:2px"><?php
                            $sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVAPPR'");
                        if (mysqli_num_rows($sqlchk)!=0) {
                            while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
                                $capprv = $row['cvalue'];
                            }
                        }else{
                            $capprv = "";
                        }
                        ?>
                              <div class="col-xs-10">
                                <input type="text" class="form-control input-xs" name="capproved" id="capproved" placeholder="Enter Name or Initials..." tabindex="6" value="<?php echo $capprv;?>">
                            </div></td>
                          </tr>
                      </table>
                    </fieldset>
                    </form>
                
                
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-success btn-sm" name="setSubmit" id="setSubmit"><span class="glyphicon glyphicon glyphicon-floppy-disk"></span> Save</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<?php
mysqli_close($con);

?>
</body>
</html>


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


	$('#paycredit').typeahead({
	
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
			  
				$('#paycredit').val(map[item].id);
				return item;
		}
	
	});


		$("#setSubmit").on('click', function(){
				// AJAX Code To Submit Form.
				$.ajax({
				type: "POST",
				url: "PayBill_setsave.php",
				data: $('#frmSet').serialize(),
				cache: false,
				success: function(result){
					alert(result);
					
					$('#SetModal').modal('hide');
				}
				});

			return false;
		});

	</script>
