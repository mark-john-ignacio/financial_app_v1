<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "AR_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

//echo $_SESSION['chkitmbal']."<br>";
//echo $_SESSION['chkcompvat'];

$ddeldate = date("m/d/Y");
$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

//echo $ddeldate;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
   <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="ARAdj_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>AR Adjustment</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="100" rowspan="3">
    <span style="padding:2px"><img src="../../images/blueX.png" width="100" height="100" style="border:solid 1px  #06F;" name="imgemp" id="imgemp"></span>
    </tH>
    <tH width="100">&nbsp;Customer:</tH>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding">
        <div class="col-xs-3 nopadding">
        	<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
            <input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">

        </div>

    	<div class="col-xs-8 nopadwleft">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
        </div> 
      </div>
    </td>
    <tH width="150">AR Date:</tH>
    <td style="padding:2px;">
     <div class="col-xs-11 nopadding">
		<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $ddeldate; ?>" />
     </div>
    </td>
  </tr>
  <tr>
    <tH width="100">&nbsp;Remarks:</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
    <tH width="150">AR Type:</tH>
    <td style="padding:2px" align="right">
      <div class="col-xs-11 nopadding">
    	<select name="seltype" id="seltype" class="form-control input-sm">
            <option value="Credit">Credit Memo</option>
            <option value="Debit">Debit Memo</option>
        </select>
      </div>
    </td>
  </tr>
  <tr>
  	<tH width="100">&nbsp;<!--RETURN NO.:-->Reference:</tH>
    <td>       
    <div class="col-xs-12 nopadding">
        	<div class="col-xs-3 nopadding"><input type="text" class="form-control input-sm" id="txtSIRef" name="txtSIRef" width="20px" tabindex="2" readonly placeholder="Search Sales Return No...">
            </div>
  
            <div class="col-xs-1 nopadwleft">
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnSISearch" onClick="InsertDet('REF');"><i class="fa fa-search"></i></button>
            </div>
            
             <div class="col-xs-5 nopadwleft">
                <label class="checkbox-inline"><input type="checkbox" name="chkWOR" id="chkWOR" value="WOR">Without Reference</label>
             </div>
     
   </div>

</td>
    <tH width="150">&nbsp;</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding">
		&nbsp;
    </div></td>
    <td style="padding:2px"  align="right">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td style="padding:2px">&nbsp;</td>
    <td style="padding:2px"  align="right">&nbsp;</td>
  </tr>

<tr>
    <td colspan="5">
      <input type="hidden" id="txtprodid" name="txtprodid">
      <input type="hidden" id="txtprodnme" name="txtprodnme">
      <input type="hidden" name="hdnqty" id="hdnqty">
      <input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
      <input type="hidden" name="hdnunit" id="hdnunit">

    </td>
    </tr>
</table>
         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable table table-condensed" width="100%">

					<tr>
						<th style="border-bottom:1px solid #999">Invoice No.</th>
						<th style="border-bottom:1px solid #999">Account No.</th>
                        <th style="border-bottom:1px solid #999">Account Title</th>
                        <th style="border-bottom:1px solid #999">Description</th>
						<th style="border-bottom:1px solid #999">Debit</th>
                        <th style="border-bottom:1px solid #999">Credit</th>
                        <th style="border-bottom:1px solid #999">Remarks</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
                    
					<tbody class="tbody">
                    </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='ARAdj.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="InsertDet('WOR');" id="btnIns" name="btnIns" disabled>
Detail<br>(Insert)</button>

    
    <input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (F2)</button></td>
    <td align="right" valign="top">
    <b>TOTAL AMOUNT </b>
    &nbsp;&nbsp;
    <input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
      </td>
    </tr>
</table>

    </fieldset>
    
    
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

<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">Sales Return List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
                    <div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-condensed">
                           <thead>
                            <tr>
                              <th>SR No.</th>
                              <th>Date</th>
                              <th>Gross</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                    </div>
         	            
			</div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->


<form method="post" name="frmedit" id="frmedit" action="ARAdj_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	
	
	  if(e.keyCode == 113) { //F2
	  	  e.preventDefault();
		 if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		  return chkform();
		 }
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		 window.location.replace("ARAdj.php");
	    }

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			var selwref = $('input[name="chkWOR"]:checked').length > 0;
			if(selwref==1){
				InsertDet('WOR');
			}else{
				InsertDet('REF');
			}
		}
	  }

	
	});

$(function(){
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				 minDate: new Date(),
        });

		$("#txtcustid").keyup(function(event){
		if(event.keyCode == 13){
		
		var dInput = this.value;
		
		$.ajax({
        type:'post',
        url:'../get_customerid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){
			//alert(value);
			if(value!=""){
				var data = value.split(":");
				$('#txtcust').val(data[0]);
				$('#imgemp').attr("src",data[3]);
								
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
								
			}
			else{
				$('#txtcustid').val("");
				$('#txtcust').val("");
				$('#imgemp').attr("src","../../images/blueX.png");
				
				$('#hdnvalid').val("NO");
			}
		},
		error: function(){
			$('#txtcustid').val("");
			$('#txtcust').val("");
			$('#imgemp').attr("src","../../images/blueX.png");
			
			$('#hdnvalid').val("NO");
		}
		});

		}
		
	});

	$('#txtcust, #txtcustid').on("blur", function(){
		if($('#hdnvalid').val()=="NO"){
		  $('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
		  
		 // $('#txtprodnme').attr("disabled", true);
		 // $('#txtprodid').attr("disabled", true);
		}else{
			
		 // $('#txtprodnme').attr("disabled", false);
		 // $('#txtprodid').attr("disabled", false);
		  
		  $('#txtremarks').focus();
	
		}
	});
	//Search Cust name
	$('#txtcust').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			$("#imgemp").attr("src",item.imgsrc);
			
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();			
			
		}
	
	});
	
	var sltypprev_val;
			
	$('#seltype').on('focus', function () {
    	sltypprev_val = $(this).val();
	}).change(function() {
		//alert(sltypprev_val);
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(lastRow != 0){
	
			$("#AlertMsg").html("");
								
			$("#AlertMsg").html("<b>Note: </b>Changing selection will remove all your details!");
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');
			
				$('.btnmodz').on('click', function(){
					var x = $(this).attr('id');
					
					if(x=="OK"){
						removeALL();
					}
					else{
						
						$('#seltype option[value="'+sltypprev_val+'"]').prop('selected', true);
					}
					
					$("#AlertModal").modal('hide');
				});

		}
		else{
			removeALL();
		}
				
	});
	
	
	$("#chkWOR").on("change", function(e){
	  var selwref = $('input[name="chkWOR"]:checked').length;
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(lastRow != 0){

			$("#AlertMsg").html("");
								
			$("#AlertMsg").html("<b>Note: </b>Changing reference will remove all your details!");
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');
			
				$('.btnmodz').on('click', function(){
					var x = $(this).attr('id');
					
					if(x=="OK"){
						removeALL();

						  if(selwref==1){
							$("#btnIns").prop("disabled", false); 
							$("#btnSISearch").prop("disabled", true);
							$("#txtSIRef").val("");
						  } else {
							$("#btnIns").prop("disabled", true);
							$("#btnSISearch").prop("disabled", false);
						  }

					}
					else{
						if(selwref==1){
							$('#chkWOR').prop('checked', false);
						}else{
							$('#chkWOR').prop('checked', true);
						}
					}
					
					$("#AlertModal").modal('hide');
				});
		}
		else{
			if(selwref==1){
				$("#btnIns").prop("disabled", false); 
				$("#btnSISearch").prop("disabled", true);
				$("#txtSIRef").val("");
			} else {
				$("#btnIns").prop("disabled", true);
				$("#btnSISearch").prop("disabled", false);
			}

		}
		
	  
	});
						

});

function InsertDet(typ){
 if($("#txtcust").val()!="" || $("#txtcustid").val()!=""){
	 
	 if(typ=="WOR"){
		//var selwref = $('input[name="chkWOR"]:checked').length > 0;
		 InsertWOR();
	 }
	 else if(typ=="REF"){
		 PopReturns();
	 }

 }
  else{
	  	$("#AlertMsg").html("&nbsp;&nbsp;Enter a valid customer first...");
			$("#alertbtnOK").show();
			$("#OK").hide();
			$("#Cancel").hide();
		$("#AlertModal").modal('show');
		
		 $('#txtcust').focus();

  }
}

function InsertWOR(){
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	
	var tdinv= "<td><input type='hidden' name='txtcsrno' id='txtcsrno"+lastRow+"' value=''><input type='text' class='form-control input-xs' name='txtcinvno' id='txtcinvno"+lastRow+"' value='WOR' readonly></td>";
	var tdaact = "<td><input type='text' class='cacctno form-control input-xs' name='txtcacctno' id='txtcacctno"+lastRow+"' placeholder=\"Enter Acct Code...\"></td>";
	var tdtitle = "<td width='230'><input type='text' class='acctdesc form-control input-xs' name='txtctitle' id='txtctitle"+lastRow+"' placeholder=\"Enter Acct Title...\"></td>";
	var tddesc = "<td width='200'><input type='text' class='form-control input-xs' name='txtcdesc' id='txtcdesc"+lastRow+"' placeholder=\"Enter Description...\"></td>";
	var tddebit = "<td><input type='text' class='numeric form-control input-xs' name='txtndebit' id='txtndebit"+lastRow+"' style=\"text-align:right\" value=\"0.0000\"></td>";
	var tdcredit = "<td><input type='text' class='numeric form-control input-xs' name='txtncredit' id='txtncredit"+lastRow+"' style=\"text-align:right\" value=\"0.0000\"></td>";
	var tdremarks = "<td><input type='text' class='form-control input-xs' name='txtcremarks' id='txtcremarks"+lastRow+"' placeholder=\"Enter Remarks...\"></td>";
	var tddel = "<td><input class='btndel btn btn-danger btn-xs' type='button' name='del' id='del"+lastRow+"' value='delete' /></td>";

	//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
	
	$('#MyTable > tbody:last-child').append('<tr>'+tdinv + tdaact + tdtitle + tddesc + tddebit + tdcredit + tdremarks + tddel + '</tr>');
	

	$('#txtctitle'+lastRow).focus();		


	$("#del"+lastRow).on('click', function() {
		//alert($(this).closest('td').parent()[0].sectionRowIndex);
		$(this).closest('tr').remove();
		renametdids();
		
	});
	
	
					$("#txtctitle"+lastRow).typeahead({						 
						autoSelect: true,
						source: function(request, response) {							
							$.ajax({
								url: "../th_accounts.php",
								dataType: "json",
								data: { query: request },
								success: function (data) {
									response(data);
								}
							});
						},
						displayText: function (item) {
							return item.id + " : " + item.name;
						},
						highlighter: Object,
						afterSelect: function(item) { 					

							var id = $(document.activeElement).attr('id');
							var r = id.replace( /^\D+/g, '');
															
							$("#txtctitle"+r).val(item.name).change(); 
							$("#txtcacctno"+r).val(item.id); 
							
							$('#txtcdesc'+r).focus();
							
						}
					});
					
					$(".cacctno").on("keydown", function(e) {
						
						var id = $(document.activeElement).attr('id');
						var r = id.replace( /^\D+/g, '');
						
						if((e.type=="blur" || e.keyCode==13) && $(this).val()!=""){
							//alert($(this).val());
							$.ajax({
								url: "th_chkacctno.php",
								dataType: 'json',
								async: false,
								data: { id: $(this).val() },
								success: function (data) {
									
									console.log(data);
									$.each(data,function(index,item){
										//alert(item.cstat);
										if(item.cstat!="True"){
											$("#txtcacctno"+r).val("").change();
											
											$("#AlertMsg").html("&nbsp;&nbsp;"+item.cstat);
												$("#alertbtnOK").show();
												$("#OK").hide();
												$("#Cancel").hide();
											$("#AlertModal").modal('show');
											
											 setTimeout(function() { $("#txtcacctno"+r).focus(); }, 50);
										}
										else{
											$("#txtctitle"+r).val(item.name); 
											$("#txtcacctno"+r).val(item.id).change(); 
											
											$('#txtcdesc'+r).focus();
										}
										
									});
								
								},
								error: function (req, status, err) {
									//alert();
									console.log('Something went wrong', status, err);
									$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
												$("#alertbtnOK").show();
												$("#OK").hide();
												$("#Cancel").hide();
									$("#AlertModal").modal('show');
								}

							});
						}

					});


					$("input.numeric").numeric();
					$("input.numeric").on("click", function () {
						$(this).select();
					});
													
					$("input.numeric").on("keyup", function () {
						ComputeGross();
					});
																						

}


function setinvref(invref){
	$("#txtSIRef").val(invref); 
	
				$.ajax({
                    url: 'th_sientry.php',
					data: 'x='+invref,
                    dataType: 'json',
                    method: 'post',
					async: false,
                    success: function (data) {

                       console.log(data);
                       $.each(data,function(index,item){
						   
							var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
							var lastRow = tbl.length;
							
							var tdinv= "<td><input type='hidden' name='txtcsrno' id='txtcsrno"+lastRow+"' value='"+invref+"'><input type='text' class='form-control input-xs' name='txtcinvno' id='txtcinvno"+lastRow+"' value='"+item.csino+"' readonly></td>";
							var tdaact = "<td><input type='text' class='cacctno form-control input-xs' name='txtcacctno' id='txtcacctno"+lastRow+"' readonly value='"+item.cacctno+"'></td>";
							var tdtitle = "<td width='230'><input type='text' class='acctdesc form-control input-xs' name='txtctitle' id='txtctitle"+lastRow+"' readonly value='"+item.cacctdesc+"'></td>";
							var tddesc = "<td width='200'><input type='text' class='form-control input-xs' name='txtcdesc' id='txtcdesc"+lastRow+"' placeholder=\"Enter Description...\"></td>";
							var tddebit = "<td><input type='text' class='numeric form-control input-xs' name='txtndebit' id='txtndebit"+lastRow+"' style=\"text-align:right\" readonly value='"+item.ndebit+"'></td>";
							var tdcredit = "<td><input type='text' class='numeric form-control input-xs' name='txtncredit' id='txtncredit"+lastRow+"' style=\"text-align:right\" readonly value='"+item.ncredit+"'></td>";
							var tdremarks = "<td><input type='text' class='form-control input-xs' name='txtcremarks' id='txtcremarks"+lastRow+"' placeholder=\"Enter Remarks...\"></td>";
							var tddel = "<td>&nbsp;</td>";
						
							//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
							
							$('#MyTable > tbody:last-child').append('<tr>'+tdinv + tdaact + tdtitle + tddesc + tddebit + tdcredit + tdremarks + tddel + '</tr>');
							
						   
					   });
					   
					}
				});
}

function PopReturns(){

	$('#MyInvTbl tbody').empty();

	$('#InvListHdr').html("Invoice List: " + $('#txtcust').val());
	
			var ccode = $("#txtcustid").val();						
			var xstat = "YES";
					
			$.ajax({
                    url: 'th_qolist.php',
					data: 'x='+ccode,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {

                       console.log(data);
                       $.each(data,function(index,item){

								
						if(item.cpono=="NONE"){
						$("#AlertMsg").html("No Sales Return Available");
							$("#alertbtnOK").show();
							$("#OK").hide();
							$("#Cancel").hide();
						$("#AlertModal").modal('show');

							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td id='td"+item.cpono+"'>").html("<a href=\"javascript:;\" data-dismiss=\"modal\" onclick=\"setinvref('"+item.cpono+"')\">"+item.cpono+"</a>"),
							$("<td>").text(item.ngross),
							$("<td>").text(item.dcutdate)
							).appendTo("#MyInvTbl tbody");
														
							$("#td"+item.cpono).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
					   	}

                       });
					   

					   if(xstat=="YES"){
						   $('#mySIRef').modal("show");
					   }
                    },
                    error: function (req, status, err) {
						//alert();
						console.log('Something went wrong', status, err);
						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
												$("#alertbtnOK").show();
												$("#OK").hide();
												$("#Cancel").hide();
						$("#AlertModal").modal('show');
					}
                });
}

function removeALL(){
	$("#MyTable > tbody > tr").each(function(index) {
		if(index!=0){
			$(this).remove();
		}
	});
}

function renametdids(){
	$("#MyTable > tbody > tr").each(function(index) {
		if(index>=1){
			
			$(this).find('input[type="hidden"][name="txtcsrno"]').attr('id', 'txtcsrno'+index); 
			$(this).find('input[name="txtcinvno"]').attr('id', 'txtcinvno'+index);
			$(this).find('input[name="txtcacctno"]').attr('id', 'txtcacctno'+index);
			$(this).find('input[name="txtctitle"]').attr('id', 'txtctitle'+index);
			$(this).find('input[name="txtcdesc"]').attr('id', 'txtcdesc'+index);
			$(this).find('input[name="txtndebit"]').attr('id', 'txtndebit'+index);
			$(this).find('input[name="txtncredit"]').attr('id', 'txtncredit'+index);
			$(this).find('input[name="txtcremarks"]').attr('id', 'txtcremarks'+index);
			$(this).find('input[type="button"][name="del"]').attr('id', 'del'+index);
		}
	});
}

</script>