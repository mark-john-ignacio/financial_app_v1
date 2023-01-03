<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvAdj_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

		if(isset($_REQUEST['txtctranno'])){
				$txtctranno = $_REQUEST['txtctranno'];
		}
		else{
				$txtctranno = $_REQUEST['hdnctranno'];
		}


$sqlhead = mysqli_query($con,"select a.* from invcount a where a.compcode='$company' and a.ctranno='$txtctranno'");


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
    
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>

</head>

<body style="padding-left:20px; padding-right:20px; padding-top:10px">
<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$cTranNo = $row['ctranno'];
		$cMonth = $row['dmonth'];
		$cYear = $row['dyear'];		
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
	
	$QtyCnt = 0;
	$sqldet = mysqli_query($con,"select a.* from invcount_t a where a.compcode='$company' and a.ctranno='$txtctranno'");
	$ProdCnt = mysqli_num_rows($sqldet);
	while($rowdet = mysqli_fetch_array($sqldet, MYSQLI_ASSOC)){
		$QtyCnt = $QtyCnt + floatval($rowdet['nqty']);
	}
?>

<div class="col-xs-12">
	<div class="col-xs-3">
    	<b><font size="+2">
        <?php 
        
        $monthNum  = floatval($cMonth);
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F'); // March
        
        echo $monthName . " " . $cYear;
        
        ?>
        </font></b>
    </div>
    
    <div class="col-xs-3 nopadding">
    	<b><font size="+2">
        <div class="col-xs-7 nopadwright2x">
        	<b>No. of Prod.: </b>
        </div>
        <div class="col-xs-5 nopadding" id="divLblNo">
        	<b><?php echo $ProdCnt; ?></b>
        </div>
        
        </font></b>
    </div>

    <div class="col-xs-3 nopadding">
    	<b><font size="+2">
        <div class="col-xs-7">
        	Tot Qty:
        </div>
       <div class="col-xs-5 nopadding" id="divLblQTy">
        	<b><?php echo $QtyCnt; ?></b>
        </div>

        </font></b>
        
    </div>
    
    <div class="col-xs-3 nopadding" style="text-align:right" id="statmsgz">
    	
    </div>

</div>

<div class="col-xs-12 nopadwtop2x" id="divStatMsg">
	
</div>


<div class="col-xs-12 nopadwtop2x">
	
    <input type="text" class="form-control input-lg" name="txtscan" id="txtscan" value="" placeholder="<Qty>*SCAN BARCODE">

</div>


<form id="frmCount" name="frmCount" method="post" action="">
	<input type="hidden" name="hdnmonth" id="hdnmonth" value="<?php echo $_REQUEST["month"];?>">
    <input type="hidden" name="hdnyear" id="hdnyear" value="<?php echo $_REQUEST["year"];?>">
    <input type="hidden" name="hdnctranno" id="hdnctranno" value="<?php echo $txtctranno;?>">
    
    <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
    <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">

                  <table name='MyTbl' id='MyTbl' class="table table-scroll table-striped">
                   <thead>
                    <tr>
		      <th width="10">&nbsp;</th>
                      <th width="150">Scan Code</th>
                      <th>Item Description</th>
                      <th width="70">Unit</th>
                      <th width="100">Qty</th>
                      <th width="50">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    	<?php
							$sqldet = mysqli_query($con,"select a.*, b.citemdesc from invcount_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno='$txtctranno' order by a.nidentity");

$cnt = 0;							
while($rowdet = mysqli_fetch_array($sqldet, MYSQLI_ASSOC)){
$cnt = $cnt + 1;
							
						?>
                        	<tr>
							<td><div id="rowItmNum"><?php echo $cnt; ?></div></td></td>
                              <td><input type='hidden' value='<?php echo $rowdet['citemno'];?>' name='txtcpartno'> <input type='hidden' value='<?php echo $rowdet['cscancode'];?>' name='txtcscancode'><?php echo $rowdet['cscancode'];?></td>
                              <td><input type='hidden' value='<?php echo $rowdet['citemdesc'];?>' name='txtcdesc'><?php echo $rowdet['citemdesc'];?></td>
                              <td><input type='hidden' value='<?php echo $rowdet['cunit'];?>' name='txtcunit'><?php echo $rowdet['cunit'];?></td>
                              <td><input type='text' value='<?php echo $rowdet['nqty'];?>' name='txtnqty' class="form-control input-sm" style="align: right"></td>
                              <th><input class='btn btn-danger btn-xs' type='button' id='del<?php echo $rowdet['cscancode'];?>' value='delete' /></td>
                            </tr>
                            <script>
											$("#del<?php echo $rowdet['cscancode'];?>").on("click", function() {
												//alert("HELLO");
												$(this).closest('tr').remove();
											});

							</script>
                    	<?php
							}
                        
						?>

                    </tbody>
				  </table>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $txtctranno;?>');" id="btnPrint" name="btnPrint">
Print<br>(CTRL+P)
    </button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>

    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(CTRL+E)</button>

    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button></td>

    </tr>
</table>

</form>

<?php
}
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
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>


<!--SETTINGS -->
<div class="modal fade" id="SetModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">Search Item Description</h3>
            </div>
            
            <div class="modal-body">
            	<div class="col-xs-12 nopadwtop2x">
	
                <input type="text" class="form-control input-md" name="txtcdesc" id="txtcdesc" value="" placeholder="SEARCH ITEM DESCRIPTION...">
                
                <input type="hidden" name="hdnscan" id="hdnscan" value="">
                <input type="hidden" name="hdnqty" id="hdnqty" value="">

				</div>

			</div>
            

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- PRINT OUT MODAL-->
<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-contnorad">   
            <div class="modal-bodylong">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
        
               <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
    
            	
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<form name="frmEdit" id="frmEdit" method="post" action="InvCnt_Edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		

<form name="frmPrint" id="frmPrint" method="post" action="InvCnt_Print.php" target="_blank">
	<input type="hidden" name="xid" id="xid" />
</form>		
</body>

</html>

<script type="text/javascript">

	$(document).ready(function(e) {
	  
		disabled();
		
    });

	$(document).keydown(function(e) {	 
	 if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?php echo $txtctranno;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			window.location.href='Inv.php';
		}
	  }

	});


$(function(){	
	
	$("#txtscan").keypress(function(event){
		if(event.keyCode == 13 && $(this).val()!=""){
			var exist = "NO";
			var xy = "";
			var finqty=0;
			
			
			var itm = $(this).val();

			if(itm.indexOf("*")==-1){
				var xy = itm;
				var finqty = 1;
			}else{
				var itmcode = itm.split("*", 2);
				var xy = itmcode[1];
				var finqty = itmcode[0];
			}

			//Check sa table if existing na.. update Qty lang
			$("#MyTbl > tbody > tr").each(function(index) {
				//divStatMsg
				myscan = $(this).find('input[name="txtcscancode"]').val(); 
				myscanDesc =$(this).find('input[name="txtcdesc"]').val();
				myid = $(this).find('input[name="txtcpartno"]').val(); 
				myunit = $(this).find('input[name="txtcunit"]').val();  

				if(myscan==xy){
					//Update Qty Lang
					exist = "YES";
					
					varx = $(this).find('input[name="txtnqty"]').val();
					
					vartot = parseFloat(finqty) + parseFloat(varx);
					
					$(this).find('input[name="txtnqty"]').val(vartot.toFixed(4));
					
					$("#divStatMsg").html("<font size=\"4px\"><b>Item No. "+(index+1)+": "+myscanDesc+" ( Qty: "+varx+" + "+finqty+" )</b></font>");
					
					
					insertTemp(myscan,myid,myunit,parseFloat(varx));
					
				}
	
			});
			
			//alert("This");
			
			//Pag wala search sa barcodes table
			if(exist == "NO"){
				$.ajax({
					url:'th_getProDet.php',
					data: 'id='+ xy, 
					dataType: "json",
					async: false,                
					success: function(data){
						
						console.log(data);
						$.each(data,function(index,item){
							exist = "YES";
							
							finqty = parseFloat(finqty);
							finqty = finqty.toFixed(4);
								var trRow0 = "<td><div id=\"rowItmNum\"></div></td>";
								var trRow1 = "<td><input type='hidden' value='"+item.id+"' name='txtcpartno'> <input type='hidden' value='"+item.cscan+"' name='txtcscancode'>"+item.cscan+"</td>";
								var trRow2 = "<td><input type='hidden' value='"+item.desc+"' name='txtcdesc'>"+item.desc+"</td>";
								var trRow3 = "<td><input type='hidden' value='"+item.cunit+"' name='txtcunit'>"+item.cunit+"</td>";
								var trRow4 = "<td><input type='text' value='"+finqty+"' name='txtnqty' readonly class=\"form-control input-sm\" style=\"align: right\">"+"</td>";
								var trRow5 = "<td><input class='btn btn-danger btn-xs' type='button' id='"+item.cscan+"_delete' value='delete' onClick='deleteRow(this);' /></td>";

								$("<tr>"+trRow0+trRow1+trRow2+trRow3+trRow4+trRow5+"</tr>").prependTo("#MyTbl > tbody");
								
								insertTemp(item.cscan,item.id,item.cunit,finqty);

							
						});	
						
					}
			
				});
			}
			
			//pag wla pa dn.... set ung barcode sa existing item
			if(exist == "NO"){
				
				$("#SetModal").modal("show");
				
				$("#hdnscan").val(xy);
				$("#hdnqty").val(finqty);
				
				

			}
			
			$('#txtscan').val('').change();
			updateStat();
		
		}
		
	});
	
	$('#SetModal').on('shown.bs.modal', function () {
    	$("#txtcdesc").focus();
	})
	
					$('#txtcdesc').typeahead({
						autoSelect: true,
						source: function(request, response) {
							$.ajax({
								url: "th_product.php",
								dataType: "json",
								data: { query: $("#txtcdesc").val() },
								success: function (data) {
									response(data);
								}
							});
						},
						displayText: function (item) {
							return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.desc+'</span</div>';
						},
						highlighter: Object,
						afterSelect: function(item) { 					
										
								var trRow0 = "<td><div id=\"rowItmNum\"></div></td>";		
								var trRow1 = "<td><input type='hidden' value='"+item.id+"' name='txtcpartno'> <input type='hidden' value='"+$("#hdnscan").val()+"' name='txtcscancode'>"+$("#hdnscan").val()+"</td>";
								var trRow2 = "<td><input type='hidden' value='"+item.desc+"' name='txtcdesc'>"+item.desc+"</td>";
								var trRow3 = "<td><input type='hidden' value='"+item.cunit+"' name='txtcunit'>"+item.cunit+"</td>";
								var trRow4 = "<td><input type='text' value='"+$("#hdnqty").val()+"' name='txtnqty' readonly class=\"form-control input-sm\" style=\"align: right\"></td>";
								var trRow5 = "<td><input class='btn btn-danger btn-xs' type='button' id='row_"+item.cscan+"_delete' value='delete' onClick='deleteRow2(this);' /></td>";

								$("<tr>"+trRow0+trRow1+trRow2+trRow3+trRow4+trRow5+"</tr>").prependTo("#MyTbl > tbody");

								
								$('#txtcdesc').val("").change(); 
								
								$("#SetModal").modal("hide");
								$("#txtscan").focus();
								
								updateStat();
								
								
								insertTemp($("#hdnscan").val(),item.id,item.cunit,parseFloat($("#hdnqty").val()));
							
						}
	
					});


});

function insertTemp(xscan,cid,xunit,xqty){
				$.ajax ({
					url: "InvCnt_SaveTemp.php",
					data: { citmno: cid, cunit: xunit, nqty:xqty, cscan: xscan },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							$("#AlertMsg").html("<b>Problem inserting data!</b>");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');
						}
					}
				});
	
}


function updateStat(){

			var cntr = 0;
			var nqty = 0;
				
			$("#MyTbl > tbody > tr").each(function(index) {
				
				varxqty = $(this).find('input[name="txtnqty"]').val(); 
				
				cntr = cntr + 1;
				
				$(this).find('div[id="rowItmNum"]').text(cntr);				
				
				
				nqty = parseFloat(nqty) + parseFloat(varxqty);
											
			});
			
			$("#divLblNo").html("<b>"+cntr+"</b>");
			$("#divLblQTy").html("<b>"+nqty+"</b>");

}

function chkform(){
		var ISOK = "YES";
		var trancode = "";
		var isDone = "True";
		var VARHDRSTAT = "";
		var VARHDRERR = "";
		
		var tbl1 = document.getElementById('MyTbl').getElementsByTagName('tr');
		var lastRow1 = tbl1.length-1;
				
		if(lastRow1!=0){
		
			var ctranno = $("#hdnctranno").val();
			alert(ctranno);
		//Saving Header
					$.ajax ({
					url: "InvCnt_SaveEdit.php",
					data: { id: ctranno },
					async: false,
					beforeSend: function(){
						$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING COUNT: </b> Please wait a moment...");
						$("#alertbtnOK").hide();
						$("#AlertModal").modal('show');
					},
					success: function( data ) {
						//alert(data.trim());
						if(data.trim()!="False"){
							trancode = data.trim();
						}
					},
					error: function (req, status, err) {
								//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
		
						VARHDRSTAT = status;
						VARHDRERR = err;
		
					}
					
				});

		// Saving Details
		if(trancode!=""){
			$("#MyTbl > tbody > tr").each(function(index) {

				var nqty = $(this).find('input[name="txtnqty"]').val();
				var cscan = $(this).find('input[type="hidden"][name="txtcscancode"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtcpartno"]').val();
				var cunit = $(this).find('input[type="hidden"][name="txtcunit"]').val();

				$.ajax ({
					url: "InvCnt_SaveDet.php",
					data: { trancode: trancode, indx: index, citmno: citmno, cunit: cunit, nqty:nqty, cscan: cscan, nqtyfin:nqty },
					async: false,
					success: function( data ) {
						if(data.trim()!="False"){
							//$("#AlertMsg").html("<b>"+data.trim()+"</b>");
							//$("#alertbtnOK").hide();
						}else{
							isDone = "False";
						}
					}
				});

				
			});
			
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY UPDATED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							$("#txtctranno").val(trancode);
							$("#frmEdit").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}

		}
		else{
			$("#AlertMsg").html("Something went wrong<br>Status: "+VARHDRSTAT +"<br>Error: "+VARHDRERR);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		}
	
							
		}
		else{
			alert("Cannot be saved without details!");
		}

}

function disabled(){

	$("#frmCount :input").attr("disabled", true);
	
	$("#btnMain").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	//if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+"!";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	//}
	//else{
		
		$("#frmCount :input").attr("disabled", false);
		
			$("#btnMain").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					

///	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		$("#xid").val(x);
		$("#frmPrint").submit();

	}
}



</script>
