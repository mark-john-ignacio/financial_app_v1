<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
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
<script src="../../js/bootstrap3-typeahead.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F2
	    e.preventDefault();
		window.location = "App_new.php";
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
							
		$("#AlertMsg").html("Are you sure you want to "+x+" Loan No.: "+num);
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
				<font size="+2"><u>Loan Application</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" onClick="location.href='App_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
              <button type="button" class="btn btn-warning btn-md" name="btnSet" id="btnSet"><span class="glyphicon glyphicon-cog"></span> Settings</button>
       
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Loan App No</th>
                        <th>Loanee</th>
						<th>Loan Type</th>
                        <th>Amount</th>
						<th>Date</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
				
				$sql = "select a.*,b.cname from loans a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' order by a.ddate DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
						$ccustname = $row['cname'];
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
 						<td><?php echo $row['ccode'];?> - <?php echo $ccustname;?> </td>
                        <td><?php echo $row['cloantype'];?></td>
                        <td align="right"><?php echo number_format($row['nloaned'],4);?></td>
                        <td><?php echo $row['ddate'];?></td>
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
    
<form name="frmedit" id="frmedit" method="post" action="App_edit.php">
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

<!-- Setting Modal -->

<!--SETTINGS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">LOAN APPLICATION SETUP</h3>
            </div>
            <div class="modal-body">

                <form method="post" name="frmSet" id="frmSet" action="App_setsave.php">
                <fieldset>
                        <legend>Credit Account Settings</legend>
                                            <table width="95%" border="0" cellpadding="0" align="right">
                      <tr>
                        <th scope="row" width="200">AP Account</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='LOANCRACCT'");
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
                        
                        <div class="col-xs-12 nopadding">
                            <div class="col-xs-5 nopadding">
                            <input type="text" class="form-control input-xs" name="loancr" id="loancr" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>" autocomplete="off"> 
                            </div>
                            <div class="col-xs-3 nopadwleft">
                            <input type="text" class="form-control input-xs" name="loancrid" id="loancrid" value="<?php echo $nCreditDef;?>" readonly>
                            </div>
                        </div>
                        
                        </td>
                      </tr>
					</table>
                </fieldset>
                    <fieldset>
                        <legend>Debit Account Settings</legend>
                    <table width="95%" border="0" cellpadding="0" align="right">
                      <tr>
                        <th scope="row" width="200">Loan Account</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='LOANAPACCT'");
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
                        
                        <div class="col-xs-12 nopadding">
                            <div class="col-xs-5 nopadding">
                            <input type="text" class="form-control input-xs" name="loanap" id="loanap" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>" autocomplete="off"> 
                            </div>
                            <div class="col-xs-3 nopadwleft">
                            <input type="text" class="form-control input-xs" name="loanapid" id="loanapid" value="<?php echo $nCreditDef;?>" readonly>
                            </div>
                        </div>
                        
                        </td>
                      </tr>
 
                       <tr>
                        <th scope="row" width="200">Interest Account</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='LOANINTRST'");
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
                        
                        <div class="col-xs-12 nopadding">
                            <div class="col-xs-5 nopadding">
                            <input type="text" class="form-control input-xs" name="loanint" id="loanint" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>" autocomplete="off"> 
                            </div>
                            <div class="col-xs-3 nopadwleft">
                            <input type="text" class="form-control input-xs" name="loanintid" id="loanintid" value="<?php echo $nCreditDef;?>" readonly>
                            </div>
                        </div>
                        
                        </td>
                      </tr>

                      <tr>
                        <th scope="row" width="200">Capital Account</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='LOANCAPTL'");
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
                        
                        <div class="col-xs-12 nopadding">
                            <div class="col-xs-5 nopadding">
                            <input type="text" class="form-control input-xs" name="loancap" id="loancap" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>" autocomplete="off"> 
                            </div>
                            <div class="col-xs-3 nopadwleft">
                            <input type="text" class="form-control input-xs" name="loancapid" id="loancapid" value="<?php echo $nCreditDef;?>" readonly>
                            </div>
                        </div>
                        
                        </td>
                      </tr>

                      <tr>
                        <th scope="row" width="200">Service Fee</th>
                        <td style="padding:2px">
                        
                        <?php
                            $sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='LOANSRVFEE'");
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
                        
                        <div class="col-xs-12 nopadding">
                            <div class="col-xs-5 nopadding">
                            <input type="text" class="form-control input-xs" name="loansrv" id="loansrv" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>" autocomplete="off"> 
                            </div>
                            <div class="col-xs-3 nopadwleft">
                            <input type="text" class="form-control input-xs" name="loansrvid" id="loansrvid" value="<?php echo $nCreditDef;?>" readonly>
                            </div>
                        </div>
                        
                        </td>
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
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(function(){
		
	$('#example').DataTable({bSort:false});
	
	$("#btnSet").on('click', function() {
		$('#SetModal').modal('show');
	});
		
	$('#loanap, #loanint, #loancap, #loansrv, #loancr').typeahead({
	
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
			  
			  var id = $(document.activeElement).attr('id');
			
				$('#'+id+'id').val(map[item].id);
				return item;
		}
	
	});
		
	$("#setSubmit").on('click', function(){
				// AJAX Code To Submit Form.
		$.ajax({
			type: "POST",
			url: "App_setsave.php",
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
						data: { tran: num, type: "LOAN" },
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
						url: "App_Tran.php",
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