<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Journal_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

	<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../js/bootstrap3-typeahead.min.js"></script>
	<script src="../include/autoNumeric.js"></script>
	<!--
		<script src="../Bootstrap/js/jquery.numeric.js"></script>
		<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->

	<script src="../Bootstrap/js/bootstrap.js"></script>
	<script src="../Bootstrap/js/moment.js"></script>
	<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px" onLoad="document.getElementById('txtctranno').focus();">
<form action="Journal_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return chkform();">
	<fieldset>
    	<legend>Record Journal Entry </legend>	
        <table width="100%" border="0">
  <tr>
    <tH>JOURNAL No.:</tH>
    <td colspan="2" style="padding:2px;">
      <div class="col-xs-5 nopadding">
        <input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter Journal No..." required autocomplete="off">
        
        </div>
        
         <div id="statmsgz" style="color:#F00"></div>
    </td>    
    <td style="padding:2px;" align="left">
      <!--<input type="checkbox" name="lTaxInc" id="lTaxInc" value="YES">-->
    </td>
  </tr>
  <tr>
    <tH><span style="padding:2px">DATE:</span></tH>
    <td width="500" style="padding:2px;"><div class="col-xs-5 nopadding">
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" />
    </div>
    <tH><span style="padding:2px">Total Debit:</span></tH>
    <td style="padding:2px;">
    <div class="col-xs-5 nopadding">
    <input type='text' class='form-control input-sm' name='txtnDebit' id='txtnDebit' value="0.00" style="text-align:right" readonly>
    </div>
    </td>
  </tr>
  <tr>
    <tH width="100" rowspan="3" valign="top">MEMO:</tH>
    <td rowspan="3" style="padding:2px;" valign="top"><div class="col-xs-10 nopadding">
      <textarea class="form-control" rows="3" id="txtremarks" name="txtremarks"></textarea>
    </div>
    <tH><span style="padding:2px">Total Credit:</span></tH>
    <td style="padding:2px;">
    <div class="col-xs-5 nopadding">
    <input type='text' class='form-control input-sm' name='txtnCredit' id='txtnCredit' value="0.00" style="text-align:right" readonly>
    </div>
    </td>
  </tr>
  <tr>
    <tH width="150" style="padding:2px">&nbsp;</tH>
    <td style="padding:2px">
    <!--
    <div class="col-xs-5 nopadding">
      <input type='text' class='form-control input-sm' name='txtnTax' id='txtnTax' value="0.00" style="text-align:right" readonly>
    </div>
    -->
    </td>
  </tr>
  <tr>
    <tH style="padding:2px">Out of Balance:</tH>
    <td style="padding:2px"><div class="col-xs-5 nopadding">
      <input type='text' class='form-control input-sm' name='txtnOutBal' id='txtnOutBal' value="0.00" style="text-align:right" readonly>
    </div></td>
  </tr>
      </table>
<br>
    
<small><i>*Press tab after remarks field (last row) to add new line..</i></small>

             <div class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 250px;
                        text-align: left;
                        overflow: auto">
        
               <table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">
    
                        <tr>
                            <th style="border-bottom:1px solid #999">Acct#</th>
                            <th style="border-bottom:1px solid #999">Account Title</th>
                            <th style="border-bottom:1px solid #999">Debit</th>
                            <th style="border-bottom:1px solid #999">Credit</th>
                            <th style="border-bottom:1px solid #999">Subsidiary</th>
                            <th style="border-bottom:1px solid #999">Remarks</th>
                            <th style="border-bottom:1px solid #999">&nbsp;</th>
                        </tr>
               <tbody class="tbody">
                 <tr>
               	   <td width="100px" style="padding:1px"><input type="text" class="typeahead1 form-control input-xs" name="txtcAcctNo1" id="txtcAcctNo1"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();"></td>
                   <td><input type="text" class="form-control input-xs" name="txtcAcctDesc1" id="txtcAcctDesc1"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();"></td>
                   <td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit1" id="txtnDebit1" value="0.00" autocomplete="off"></td>
                   <td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit1" id="txtnCredit1" value="0.00" autocomplete="off"></td>
                   <td width="100px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtnSub1" id="txtnSub1" placeholder="Subsidiary..." autocomplete="off" onFocus="this.select();"></td>
                   <td width="200px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtcRem1" id="txtcRem1" placeholder="Remarks..." autocomplete="off" onFocus="this.select();"></td>
                   <td width="40px" align="right">&nbsp;</td>
                  </tr>
                  
                  <script>
										$(function(){

											$("#txtcAcctNo1").typeahead({
												autoSelect: true,
												source: function(request, response) {
													$.ajax({
														url: "th_accounts.php",
														dataType: "json",
														data: {
															query: $("#txtcAcctNo1").val()
														},
														success: function (data) {
															response(data);
														}
													});
												},
												displayText: function (item) {
													return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
												},
												highlighter: Object,
												afterSelect: function(item) { 					
																
													$('#txtcAcctNo1').val(item.id).change(); 
													$('#txtcAcctDesc1').val(item.name); 
													$('#txtnDebit1').focus();
													
												}
											});


											$("#txtcAcctDesc1").typeahead({
												autoSelect: true,
												source: function(request, response) {
													$.ajax({
														url: "th_accounts.php",
														dataType: "json",
														data: {
															query: $("#txtcAcctDesc1").val()
														},
														success: function (data) {
															response(data);
														}
													});
												},
												displayText: function (item) {
													return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
												},
												highlighter: Object,
												afterSelect: function(item) { 					
																
													$('#txtcAcctDesc1').val(item.name).change(); 
													$('#txtcAcctNo1').val(item.id); 
													$('#txtnDebit1').focus();
													
												}
											});

										});
									</script>
                 </tbody>
                        
                </table>
            <input type="hidden" name="hdnACCCnt" id="hdnACCCnt">
			</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
     <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Journal.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

    
       <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>

</td>
    <td align="right">&nbsp;</td>
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
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("Journal.php");

	  }
	});


	$(function(){
		$('#date_delivery').datetimepicker({
			format: 'MM/DD/YYYY',
		});
		
		
				$("input.numeric").autoNumeric('init',{mDec:2,wEmpty: 'zero'});
				//$("input.numeric").numeric();
				$("input.numeric").on("focus", function () {
					$(this).select();
				});
										
				$("input.numeric").on("keyup", function () {
					GoToComp($(this).attr('name'));
				});

		$("#txtctranno").keydown(function(e) {
			var x = $(this).val();
			
			if(x != "") {
				$.ajax({
				type:'post',
					url:'th_chkJENo.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						$("#statmsgz").html("&nbsp;&nbsp;" + msg); // your message will come here.  
						//if(msg!=""){
						//	$("#statmsgz").show();
						//}
						//else{
						//	$("#statmsgz").hide();
						//}
					}
				});
			}

		});

		$("#txtctranno").on("blur", function () {
			
			var x = $(this).val();
			
			if(x != "") {
				$.ajax({
				type:'post',
					url:'th_chkJENo.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						if(msg.trim()!=""){
							$("#statmsgz").html(""); // your message will come here. 
							$("#txtctranno").val("").change();
							$("#txtctranno").focus(); 
						}
					}
				});
			}
		});


		$('#MyTable :input').keydown(function(e) {

					
			var cnt = $('#MyTable tr').length;
			var inFocus = $(this).attr('id');
			var thisName = inFocus.replace(/\d+/g, '');
			var thisindex = inFocus.replace(/\D/g,'');
			
			var lstrow = parseInt(cnt)-1;
			
			if(thisName=="txtcRem"){
				if(e.keyCode==9){
					e.preventDefault();
				}
				if(parseInt(thisindex)==lstrow){
					InsertRows(e.keyCode,thisName,cnt);
				}
			}
			
			
			//TABLE NAVIGATION
			tblnavigate(e.keyCode,inFocus);	   
			
		});



		
	});

	function tblnavigate(x,txtinput){
		
					var inputCNT = txtinput.replace(/\D/g,'');
					var inputNME = txtinput.replace(/\d+/g, '');
					
					switch(x){
						case 39: // <Left>
							if(inputNME=="txtcAcctNo"){
								$("#txtcAcctDesc"+inputCNT).focus();
							}
							else if(inputNME=="txtcAcctDesc"){
								$("#txtnDebit"+inputCNT).focus();
							}
							else if(inputNME=="txtnDebit"){
								$("#txtnCredit"+inputCNT).focus();
							}
							else if(inputNME=="txtnCredit"){
								$("#txtnSub"+inputCNT).focus();
							}
							else if(inputNME=="txtnSub"){
								$("#txtcRem"+inputCNT).focus();
							}
							else if(inputNME=="txtcRem"){
								var idx =  parseInt(inputCNT) + 1;
								$("#txtcAcctNo"+idx).focus();
							}
							
							break;
						case 38: // <Up>  
							var idx =  parseInt(inputCNT) - 1;
											$("#"+inputNME+idx).focus();
							break;
						case 37: // <Right>
							if(inputNME=="txtcAcctNo"){
								var idx =  parseInt(inputCNT) - 1;
								$("#txtcRem"+idx).focus();
							}
							else if(inputNME=="txtcAcctDesc"){
								$("#txtcAcctNo"+inputCNT).focus();
							}
							else if(inputNME=="txtnDebit"){
								$("#txtcAcctDesc"+inputCNT).focus();
							}
							else if(inputNME=="txtnCredit"){
								$("#txtnDebit"+inputCNT).focus();
							}
							else if(inputNME=="txtnSub"){
								$("#txtnCredit"+inputCNT).focus();
							}
							else if(inputNME=="txtcRem"){
								$("#txtnSub"+inputCNT).focus();
							}

							break;
						case 40: // <Down>
							var idx =  parseInt(inputCNT) + 1;
											$("#"+inputNME+idx).focus();
							break;
					}       


	}


	function InsertRows(thisKey,thisNme,rowCount){

			if(thisKey==9){
				$('#MyTable > tbody:last-child').append(
					'<tr>'
						+'<td width="100px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtcAcctNo'+rowCount+'" id="txtcAcctNo'+rowCount+'"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();"></td>'
						+'<td><input type="text" class="form-control input-xs" name="txtcAcctDesc'+rowCount+'" id="txtcAcctDesc'+rowCount+'"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();"></td>'
						+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit'+rowCount+'" id="txtnDebit'+rowCount+'" value="0.00" autocomplete="off"></td>'
						+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit'+rowCount+'" id="txtnCredit'+rowCount+'" value="0.00" autocomplete="off"></td>'
						+'<td width="100px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtnSub'+rowCount+'" id="txtnSub'+rowCount+'" placeholder="Subsidiary..." autocomplete="off" onFocus="this.select();"></td>'
						+'<td width="200px" style="padding:1px"><input type="text" class="form-control input-xs" name="txtcRem'+rowCount+'" id="txtcRem'+rowCount+'" placeholder="Remarks..." autocomplete="off" onFocus="this.select();"></td>'
						+'<td width="40px" align="right"><input class="btn btn-danger btn-xs" type="button" id="row_'+rowCount+'_delete" value="delete" onClick="deleteRow(this);"/></td>'
					+'</tr>');
							
							$("#txtcAcctNo"+rowCount).typeahead({
								autoSelect: true,
								source: function(request, response) {
									$.ajax({
										url: "th_accounts.php",
										dataType: "json",
										data: {
											query: $("#txtcAcctNo"+rowCount).val()
										},
										success: function (data) {
											response(data);
										}
									});
								},
								displayText: function (item) {
									return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
								},
								highlighter: Object,
								afterSelect: function(item) { 					
												
									$('#txtcAcctNo'+rowCount).val(item.id).change(); 
									$('#txtcAcctDesc'+rowCount).val(item.name); 
									$('#txtnDebit'+rowCount).focus();
									
								}
							});


							$("#txtcAcctDesc"+rowCount).typeahead({
								autoSelect: true,
								source: function(request, response) {
									$.ajax({
										url: "th_accounts.php",
										dataType: "json",
										data: {
											query: $("#txtcAcctDesc"+rowCount).val()
										},
										success: function (data) {
											response(data);
										}
									});
								},
								displayText: function (item) {
									return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
								},
								highlighter: Object,
								afterSelect: function(item) { 					
												
									$('#txtcAcctDesc'+rowCount).val(item.name).change(); 
									$('#txtcAcctNo'+rowCount).val(item.id); 
									$('#txtnDebit'+rowCount).focus();
									
								}
							});

							$('#MyTable :input').keydown(function(e) {
								var cnt = $('#MyTable tr').length;
								var inFocus = $(this).attr('id');
								var thisName = inFocus.replace(/\d+/g, '')
								var thisindex = inFocus.replace(/\D/g,'');
								
								var lstrow = parseInt(cnt)-1;
								
								if(thisName=="txtcRem"){
									if(e.keyCode==9){
									e.preventDefault();
									}
									if(parseInt(thisindex)==lstrow){
									InsertRows(e.keyCode,thisName,cnt);
									}
								}
						
								tblnavigate(e.keyCode,inFocus);
								
							});
				
		
							$("input.numeric").autoNumeric('init',{mDec:2,wEmpty: 'zero'});
							$("input.numeric").on("focus", function () {
								$(this).select();
							});
										
							$("input.numeric").on("keyup", function () {
								GoToComp($(this).attr('name'));
							});
			
							$("#txtcAcctNo"+rowCount).focus();

			}

	}

	function deleteRow(r) {
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;
		var i=r.parentNode.parentNode.rowIndex;
		document.getElementById('MyTable').deleteRow(i);

		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;
		
			for (z=i+1; z<=lastRow; z++){ 
				var tempcAcctNo = document.getElementById('txtcAcctNo' + z);
				var tempcAcctDesc = document.getElementById('txtcAcctDesc' + z);
				var tempnDebit = document.getElementById('txtnDebit' + z);
				var tempnCredit= document.getElementById('txtnCredit' + z);
				var tempnSub= document.getElementById('txtnSub' + z);
				var tempcRem= document.getElementById('txtcRem' + z);
				var tempdel = document.getElementById('row_'+z+'_delete');
				
				var x = z-1;
				tempcAcctNo.id = "txtcAcctNo" + x;
				tempcAcctNo.name = "txtcAcctNo" + x;
				tempcAcctDesc.id = "txtcAcctDesc" + x;
				tempcAcctDesc.name = "txtcAcctDesc" + x;
				tempnDebit.id = "txtnDebit" + x;
				tempnDebit.name = "txtnDebit" + x;
				tempnCredit.id = "txtnCredit" + x;
				tempnCredit.name = "txtnCredit" + x;
				tempnSub.id = "txtnSub" + x;
				tempnSub.name = "txtnSub" + x;
				tempcRem.id = "txtcRem" + x;
				tempcRem.name = "txtcRem" + x;
				tempdel.id = "row_"+x+"_delete";
				tempdel .name = "row_"+x+"_delete"; 
				//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

			}
			GoToComp("txtnDebit" + x);
			
			GoToComp("txtnCredit" + x);
	}


	function GoToComp(Nme){
		var thisname = Nme.replace(/\d+/g, '')
		var cnt = $('#MyTable tr').length;
		
		cnt = cnt - 1;

			var x = 0;
			
			for (i = 1; i <= cnt; i++) {
				x = x + parseFloat($("#"+thisname+i).val().replace(/,/g,''));
			}

		
		if(thisname=="txtnDebit"){
							
			$("#txtnDebit").val(x);
			$("#txtnDebit").autoNumeric('destroy');
			$("#txtnDebit").autoNumeric('init',{mDec:2,wEmpty:'zero'});
			
		}
		else if(thisname=="txtnCredit"){
			
			$("#txtnCredit").val(x);
			$("#txtnCredit").autoNumeric('destroy');
			$("#txtnCredit").autoNumeric('init',{mDec:2,wEmpty:'zero'});
			
		}
		
		//Compute out of balance
			if ($("#txtnDebit").val().replace(/,/g,'') >= $("#txtnCredit").val().replace(/,/g,'')){
			var xcrd = $("#txtnDebit").val().replace(/,/g,'');
			var xdeb = $("#txtnCredit").val().replace(/,/g,'');
			}
			else if($("#txtnCredit").val().replace(/,/g,'') >= $("#txtnDebit").val().replace(/,/g,'')){
			var xdeb = $("#txtnDebit").val().replace(/,/g,'');
			var xcrd = $("#txtnCredit").val().replace(/,/g,'');
			}
			else if((parseFloat($("#txtnCredit").val().replace(/,/g,'')) == 0 && parseFloat($("#txtnDebit").val().replace(/,/g,'')) == 0)){
				var xdeb = 0;
			var xcrd = 0;
			}
			
			
			txtnOutBal = Math.abs(xdeb - xcrd); 
			
			$("#txtnOutBal").val(txtnOutBal);
			$("#txtnOutBal").autoNumeric('destroy');
			$("#txtnOutBal").autoNumeric('init',{mDec:2,wEmpty:'zero'});
			

	}

	function chkform(){
		//Double Chk Journal Number
			var ISOK = "YES";
		
		if ($("#txtctranno").val()==""){

			$("#AlertMsg").html("");
								
			$("#AlertMsg").html("Journal No. Required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			$("#txtctranno").focus();
			return false;
			
			ISOK = "NO";
		}
		
		//Details Checking
		var cnt = $('#MyTable tr').length;
		cnt  = parseInt(cnt)-1;
		
		for (i = 1; i <= cnt; i++) {
			if($("#txtcAcctNo"+i).val().replace(/,/g,'') == "" || $("#txtcAcctDesc"+i).val().replace(/,/g,'') == ""){

				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Valid Account ID and Description is required!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				$("#txtcAcctNo"+i).focus();
				
				return false;
				
				
				
				ISOK = "NO";
			}
			
			if($("#txtnCredit"+i).val().replace(/,/g,'')==0 && $("#txtnDebit"+i).val().replace(/,/g,'')==0){

				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Input Debit or Credit amount for this row!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				$("#txtnDebit"+i).focus();
				
				return false;
				
				
				
				ISOK = "NO";
			}
			
		}
		
		if(parseFloat($("#txtnDebit").val().replace(/,/g,'')) != parseFloat($("#txtnCredit").val().replace(/,/g,''))){ 

				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Unbalanced details!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

			return false;
			
			ISOK = "NO";
		}
		
		if(ISOK=="YES"){
			$("#hdnACCCnt").val(cnt);
			document.getElementById("frmpos").submit();
		}
		else{
			return false;
		}
	}

</script>

</body>
</html>