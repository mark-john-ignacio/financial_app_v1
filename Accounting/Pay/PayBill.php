<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

		<title>Myx Financials</title>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>"> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
    
       
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    
    <script src="../../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">


	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F2
	    e.preventDefault();
		window.location = "PayBill_new.php";
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
							
		$("#AlertMsg").html("Are you sure you want to "+x+" Payment No.: "+num);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');
	

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
					<font size="+2"><u>Bills Payment</u></font>	
        </div>
      </div>
			
			<br><br>
			
			<button type="button" class="btn btn-primary" onClick="location.href='PayBill_new.php'">
				<span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)
			</button>
      <!--
			<button type="button" class="btn btn-warning btn-md" name="btnSet" id="btnSet">
				<span class="glyphicon glyphicon-cog"></span> Settings
			</button>
			-->

      <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Payment No</th>
            <th>Paid To</th>
            <th>Bank Acct</th>
            <th>Cheque/Ref No.</th>
						<th>Payment Date</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
          <?php
						$sql = "select a.*, a.ccheckno, b.cname, e.cname as bankname
						from paybill a 
						left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode 
						left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
						where a.compcode='$company' order by a.dtrandate DESC";
						$result=mysqli_query($con,$sql);
				
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
					
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
					?>
						<tr <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?>>
							<td><a <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?> href="javascript:;" onClick="editfrm('<?=$row['ctranno'];?>');"><?=$row['ctranno'];?></a></td>
							<td><?=$row['ccode'];?> - <?=$row['cname']?> </td>
							<td><?=$row['bankname'];?></td>
							<td><?=($row['cpaymethod']=="cheque") ? $row['ccheckno'] : $row['cpayrefno'];?></td>
							<td><?=$row['dcheckdate'];?></td>
							<td align="center">
								<div id="msg<?=$row['ctranno'];?>">
									<?php 
										if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
									?>
										<a href="javascript:;" onClick="trans('POST','<?=$row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?=$row['ctranno'];?>')">CANCEL</a>
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
					?>
               
				</tbody>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="PayBill_edit.php">
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

<!--SETTINGS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CV SETUP</h3>
            </div>
            <div class="modal-body" style="height: 20vh">

                <form method="post" name="frmSet" id="frmSet" action="PayBill_setsave.php">
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
                    
                            <div class="col-xs-10"><input type="text" class="form-control input-xs" name="cprepared" id="cprepared" placeholder="Enter Name or Initials..."  tabindex="3" value="<?=$cprepared;?>"></div></td>
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
                                <input type="text" class="form-control input-xs" name="cverified" id="cverified" placeholder="Enter Name or Initials..." tabindex="5" value="<?=$cverify;?>">
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
                    
                            <div class="col-xs-10"><input type="text" class="form-control input-xs" name="creviewed" id="creviewed" placeholder="Enter Name or Initials..." tabindex="4" value="<?=$creview;?>"></div></td>
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
                                <input type="text" class="form-control input-xs" name="capproved" id="capproved" placeholder="Enter Name or Initials..." tabindex="6" value="<?=$capprv;?>">
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


<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
<script>
$(function(){
	
	$('#example').DataTable({bSort:false});
	
	$("#btnSet").on('click', function() {
		$('#SetModal').modal('show');
	});


	$('#paycreditchk').typeahead({
	
		source: function (query, process) {
			return $.getJSON(
				'../th_accounts.php',
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
			  
				$('#paycreditchkid').val(map[item].id);
				return item;
		}
	
	});


	$('#paycredit').typeahead({
	
		source: function (query, process) {
			return $.getJSON(
				'../th_accounts.php',
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
			  
				$('#paycreditid').val(map[item].id);
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
						url: "../../include/th_toAcc.php",
						data: { tran: num, type: "PV" },
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
						url: "PayBill_Tran.php",
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
