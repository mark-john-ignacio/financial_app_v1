<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
$company = $_SESSION['companyid'];

$ddeldate = date("m/d/Y");
$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));
		
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
  <link href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="PayBill_newsave.php" name="frmpos" id="frmpos" method="post" onsubmit="return chkform();">
	<fieldset>
   	  <legend>Bills Payment</legend>
   	  
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><span style="padding:2px"><b>Paid To:</b></span></td>
						<td>
						<div class="col-xs-12"  style="padding-left:2px">
							<div class="col-xs-6 nopadding">
									<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
							</div>
							<div class="col-xs-6 nopadwleft">
									<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
							</div>
						</div>
						</td>
						<td><span style="padding:2px"><b>Payee:</b></span></td>
						<td>
						<div class="col-xs-12"  style="padding-bottom:2px">
								<div class='col-xs-12 nopadding'>
										<input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" tabindex="5">
								</div>
						</div>
						</td>
					</tr>
				
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>


					<tr>
						<td width="150"><span style="padding:2px"><b>Payment Method</b></span></td>
						<td>
							<div class="col-xs-12" style="padding-left:2px">
								<div class="col-xs-6 nopadding">
									<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
										<option value="cheque">Cheque</option>
										<option value="cash">Cash</option>
										<option value="bank transfer">Bank Transfer</option>
										<option value="mobile payment">Mobile Payment</option>
										<option value="credit card">Credit Card</option>
										<option value="debit card">Debit Card</option>
									</select>
							</div>
						</td>
						<td width="120"><span style="padding:2px"><b>Payment Date:</b></span></td>
						<td>
						<div class='col-xs-12' style="padding-bottom:2px">
								<div class="col-xs-6 nopadding">
									<input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" tabindex="3"  />
								</div>
						</div>
						</td>
					</tr>
					<tr>

						<td><span style="padding:2px"><b>Payment Account: </b></span></td>
							<td>
							<div class="col-xs-12"  style="padding-left:2px">
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtcacctid" class="form-control input-sm" name="txtcacctid" value="" placeholder="Account Code" required>
								</div>
								<div class="col-xs-9 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="" autocomplete="off">
								</div>
								
							</div>
						</td>


						
						<td><span style="padding:2px" id="chkdate"><b>Check Date:</b></span></td>
						<td>
						<div class="col-xs-12"  style="padding-bottom:2px">
								<div class='col-xs-6 nopadding'>
										<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?php echo date("m/d/Y"); ?>" />
								</div>
						</div>
						</td>
					</tr>
					<tr>  
						<td width="150"><span style="padding:2px" id="paymntdesc"><b>Bank Name</b></span></td>
						<td>
							<div class="col-xs-12"  style="padding-left:2px" id="paymntdescdet">
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtBank" class="form-control input-sm" name="txtBank" value="" placeholder="Bank Code" readonly required>
								</div>
								<div class="col-xs-1 nopadwleft">
									<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchbank" id="btnsearchbank"><i class="fa fa-search"></i></button>
								</div>
								<div class="col-xs-8 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtBankName" name="txtBankName" width="20px" tabindex="1" placeholder="Bank Name..." required value="" autocomplete="off" readonly>
								</div>
								
							</div>

						</td>
						<td><span style="padding:2px"><b>Total Amount :</b></span></td>
						<td>
						<div class="col-xs-12"  style="padding-bottom:2px">
								<div class='col-xs-6 nopadding'>
									<input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm" value="0.0000" style="font-size:16px; font-weight:bold; text-align:right" readonly>
							</div>
						</div>
						</td>
					</tr>
					<tr>
						<td><span style="padding:2px" id="paymntrefr"><b>Check No.</b></span></td>
							<td>
							<div class="col-xs-12 "  style="padding-left:2px"  id="paymntrefrdet">
								<div class="col-xs-3 nopadding">
									<input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' readonly value="" required/>
								</div>
								<div class="col-xs-6 nopadwleft">
									<button type="button" class="btn btn-danger btn-sm" name="btnVoid" id="btnVoid">VOID CHECK NO. </button> 
								</div>
								
							</div>

							<div class="col-xs-12"  style="padding-left:2px; display: none" id="payrefothrsdet">
								<input type="text" id="txtPayRefrnce" class="form-control input-sm" name="txtPayRefrnce" value="" placeholder="Reference No.">
							</div>
						</td>
						<td><span style="padding:2px"><b>Total Applied :</b></span></td>
						<td>
						<div class="col-xs-12"  style="padding-bottom:2px">
								<div class='col-xs-6 nopadding'>
									<input type="text" id="txttotpaid" name="txttotpaid" class="numericchkamt form-control input-sm" value="0.0000" style="font-size:16px; font-weight:bold; text-align:right" readonly>
							</div>
						</div>
						</td>
					</tr>
				</table>
				<br>

	  		<div id="tableContainer" class="alt2" dir="ltr" style="
          margin: 0px;
          padding: 3px;
          border: 1px solid #919b9c;
          width: 100%;
          height: 250px;
          text-align: left;
          overflow: auto">
          <table width="100%" border="0" cellpadding="0" id="MyTable">
            <thead>
              <tr>
                <th scope="col">AP No</th>
                <th scope="col" width="150px">Date</th>
                <th scope="col" class="text-right" width="150px">Amount</th>
                <th scope="col" class="text-right" width="150px">Payed&nbsp;&nbsp;&nbsp;</th>
                <th scope="col" width="150px" class="text-right">Total Owed&nbsp;&nbsp;&nbsp;</th>
                <th scope="col" width="150px" class="text-center">Amount Applied</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
				</div>
                    
       <br>

      	<table width="100%" border="0" cellpadding="3">
       		<tr>
            <td width="50%"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
                        
              <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php';" id="btnMain" name="btnMain">
                Back to Main<br>(ESC)
							</button>

              <button type="button" class="btn btn-info btn-sm" tabindex="6" id="btnAPVIns" name="btnAPVIns">
                APV<br>(Insert)
              </button>
                                        
                       
              <button type="submit" class="btn btn-success btn-sm" tabindex="6">Save<br> (CTRL+S)</button>
                    
            </td>
            <td align="right">&nbsp;</td>
          </tr>
      	</table>

    </fieldset>

</form>


<!-- DETAILS ONLY -->
<div class="modal fade" id="myChkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="DRListHeader">Bank List</h3>
            </div>
            
            <div class="modal-body pre-scrollable">
            
                          <table name='MyDRDetList' id='MyDRDetList' class="table table-small table-hoverO" style="cursor:pointer">
                           <thead>
                            <tr>
                              <th>Bank Code</th>
                              <th>Bank Name</th>
                              <th>Bank Acct No</th>
                              <th>Checkbook No.</th>
                              <th>Check No.</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
            </div>         	
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<!-- DETAILS ONLY -->
<div class="modal fade" id="myAPModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="APListHeader">AP List</h3>
            </div>
            
            <div class="modal-body pre-scrollable">
            
                          <table name='MyAPVList' id='MyAPVList' class="table table-small table-hoverO" style="cursor:pointer">
                           <thead>
                            <tr>
                              <th><input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                              <th>AP No.</th>
                              <th>Date</th>
                              <th>Acct Code</th>
                              <th>Acct Desc</th>
                              <th>Payable Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
            </div> 
            
            <div class="modal-footer">
                	<button type="button" id="btnSave2" onClick="InsertSI()" class="btn btn-primary">Insert</button>
                	<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>        	
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


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

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("PayBill.php");

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#myChkModal').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			var custid = $("#txtcustid").val();
			showapvmod(custid)
		}
	  }
	});


$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY',
    });
	
});


$(function(){ 

	$('#txtcacct').typeahead({
	
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
			  
				$('#txtcacctid').val(map[item].id);
				$('#txtnbalance').val(map[item].balance);
				return item;
		}
	
	});
	
    $("#allbox").click(function () {
        if ($("#allbox").is(':checked')) {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", false);
            });
        }
    });

	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "../th_supplier.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val(), x: $("#selaptyp").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span>'+ item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			$("#txtpayee").val(item.value);
			
			showapvmod(item.id);

		}
	});
		
	$("#btnVoid").on("click", function(){
		var rems = prompt("Please enter your reason...", "");
		if (rems == null || rems == "") {
			alert("No remarks entered!\nCheque cannot be void!");
		}
		else{
			//alert( "id="+ $("#txtBankName").val()+"&chkno="+ $("#txtCheckNo").val()+"&rem="+ rems);
					$.ajax ({
					url: "PayBill_voidchkno.php",
					data: { id: $("#txtBank").val(), chkno: $("#txtCheckNo").val(), rem: rems },
					async: false,
					success: function( data ) {
						if(data.trim()!="False"){
							$("#txtCheckNo").val(data.trim());
							$("#btnVoid").attr("disabled", false);
						}
					}
					});

		}
	});


	$("#btnsearchbank").on("click", function() {
		
		$('#MyDRDetList tbody').empty();
		
			$.ajax({
        url: 'th_banklist.php',
        dataType: 'json',
				async:false,
        method: 'post',
        success: function (data) {
        // var classRoomsTable = $('#mytable tbody');
          console.log(data);
          $.each(data,function(index,item){

						$("<tr id=\"bank"+index+"\">").append(
							$("<td>").text(item.ccode),
							$("<td>").text(item.cname),
							$("<td>").text(item.cbankacctno),
							$("<td>").text(item.ccheckno),
							$("<td>").text(item.ccurrentcheck)
						).appendTo("#MyDRDetList tbody");
								
						$("#bank"+index).on("click", function() {
							$("#txtBank").val(item.ccode);
							$("#txtBankName").val(item.cname);
							$("#txtcacctid").val(item.cacctno);
							$("#txtcacct").val(item.cacctdesc);

							if($("#selpayment").val()=="cheque"){
								$("#txtCheckNo").val(item.ccurrentcheck);
							}
									
							$("#myChkModal").modal("hide");
						});

          });

        },
        error: function (req, status, err) {
					alert('Something went wrong\nStatus: '+status +"\nError: "+err);
					console.log('Something went wrong', status, err);
				}
      });

		
		$("#myChkModal").modal("show");
	});
	
	
	$("#btnAPVIns").on("click", function() {
		var custid = $("#txtcustid").val();
		showapvmod(custid)
	});


	$("#selpayment").on("change", function(){  
		if($(this).val()=="cash"){       //paymntdesc paymntdescdet
			$("#paymntdesc").html(" ");
			$("#paymntrefr").html(" ");		
			
			$("#paymntdescdet").hide();
			$("#paymntrefrdet").hide();
			$("#payrefothrsdet").hide(); 

			$("#chkdate").html("<b>Check Date</b>");
			$("#txtChekDate").attr("disabled", true);    

			$("#txtBank").prop("required", false);
			$("#txtBankName").prop("required", false); 
			$("#txtCheckNo").prop("required", false); 
			$("#txtPayRefrnce").prop("required", false); 

		}else if($(this).val()=="cheque"){	
			$("#paymntdesc").html("<b>Bank Name</b>");	
			$("#paymntrefr").html("<b>Check No.</b>");

			$("#paymntdescdet").show();
			$("#paymntrefrdet").show();

			$("#paymntothrsdet").hide();
			$("#payrefothrsdet").hide();
			$("#chkdate").html("<b>Transfer Date</b>"); 
			$("#txtChekDate").attr("disabled", false); 

			$("#txtBank").prop("required", true);
			$("#txtBankName").prop("required", true); 
			$("#txtCheckNo").prop("required", true); 
			$("#txtPayRefrnce").prop("required", false);

		}else if($(this).val()=="bank transfer"){
			$("#paymntdesc").html("<b>Bank Name</b>");
			$("#paymntrefr").html("<b>Reference No.</b>");

			$("#paymntdescdet").show();
			$("#paymntrefrdet").hide();

			$("#paymntothrsdet").hide();
			$("#payrefothrsdet").show();
			$("#chkdate").html("<b>Transfer Date</b>"); 
			$("#txtChekDate").attr("disabled", false); 

			$("#txtBank").prop("required", true);
			$("#txtBankName").prop("required", true); 
			$("#txtCheckNo").prop("required", false); 
			$("#txtPayRefrnce").prop("required", true);
		}else{
			$("#paymntdesc").html("<b>Bank Name</b>");
			$("#paymntrefr").html("<b>Reference No.</b>");

			$("#paymntdescdet").show();
			$("#paymntrefrdet").hide();

			$("#paymntothrsdet").show();
			$("#payrefothrsdet").show();

			$("#chkdate").html("<b>Transfer Date</b>"); 
			$("#txtChekDate").attr("disabled", false); 

			$("#txtBank").prop("required", false);
			$("#txtBankName").prop("required", false); 
			$("#txtCheckNo").prop("required", false); 
			$("#txtPayRefrnce").prop("required", true);
		}
	});

	


});
		
function showapvmod(custid){
					$('#MyAPVList tbody').empty();
		      //alert('th_APVlist.php?code='+custid);
					$.ajax({
                    url: 'th_APVlist.php',
					data: 'code='+custid,
                    dataType: 'json',
					async:false,
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                       console.log(data);
                       $.each(data,function(index,item){
						
						if(item.ctranno=="NO"){
							alert("No Available APV.");
							
								$('#txtcust').val("").change(); 
								$("#txtcustid").val("");

						}
						else{
	
							$("<tr id=\"APV"+index+"\">").append(
							$("<td>").html("<input type='checkbox' value='"+index+"' name='chkSales[]'>"),
							$("<td>").html(item.ctranno+"<input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'>"),
							$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
							$("<td>").html(item.cacctno+"<input type='hidden' id='APVacctno"+index+"' name='APVacctno"+index+"' value='"+item.cacctno+"'>"),
							$("<td>").text(item.cacctdesc),
							$("<td>").html(item.namount+"<input type='hidden' id='APVamt"+index+"' name='APVamt"+index+"' value='"+item.namount+"'> <input type='hidden' id='APVpayed"+index+"' name='APVpayed"+index+"' value='"+item.napplied+"'>")
							).appendTo("#MyAPVList tbody");
							
							$("#myAPModal").modal("show");
						
						}

                       });

                    },
                    error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
					}
                });

}

function InsertSI(){	
	 var totGross = 0;
	 var modnme = "";
	 	  
   $("input[name='chkSales[]']:checked").each( function () {
	   var xyz = $(this).val();
	   
	 
		  var a = $("#APVtxtno"+xyz).val();
		  var b = $("#APVdte"+xyz).val();
		  var c = $("#APVacctno"+xyz).val();
		  var d = $("#APVamt"+xyz).val();
		  var e = $("#APVpayed"+xyz).val();
		
		 var owed = parseFloat(d) - parseFloat(e);

		 addrrdet(a,b,d,e,owed,c);
		 
		 totGross = parseFloat(totGross) + parseFloat(owed) ;

   });


	$('#myAPModal').modal('hide');
	$('#myAPModal').on('hidden.bs.modal', function (e) {

  		$("#txtnGross").val(totGross);
  
	});
	

}

function addrrdet(ctranno,ddate,namount,npayed,ntotowed,cacctno){
	
	if(document.getElementById("txtcustid").value!=""){
		
	$('#txtcust').attr('readonly', true);
		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	
	var u = "<td>"+ctranno+"<input type=\"hidden\" name=\"cTranNo"+lastRow+"\" id=\"cTranNo"+lastRow+"\" value=\""+ctranno+"\" /> <input type=\"hidden\" name=\"cacctno"+lastRow+"\" id=\"cacctno"+lastRow+"\" value=\""+cacctno+"\" /> </td>";
	
	var v = "<td>"+ddate+"<input type=\"hidden\" name=\"dApvDate"+lastRow+"\" id=\"dApvDate"+lastRow+"\" value=\""+ddate+"\" /></td>";
	
	var w = "<td align='right'>"+namount+"<input type=\"hidden\" name=\"nAmount"+lastRow+"\" id=\"nAmount"+lastRow+"\" value=\""+namount+"\" /></td>";
	
	var x = "<td align='right'>"+npayed+"<input type=\"hidden\" name=\"cTotPayed"+lastRow+"\" id=\"cTotPayed"+lastRow+"\"  value=\""+npayed+"\" style=\"text-align:right\" readonly=\"readonly\">&nbsp;&nbsp;&nbsp;</td>";
	
	var y = "<td style=\"padding:2px\" align=\"right\">"+ntotowed+"<input type=\"hidden\" name=\"cTotOwed"+lastRow+"\" id=\"cTotOwed"+lastRow+"\"  value=\""+ntotowed+"\">&nbsp;&nbsp;&nbsp;</td>";
		
	var z = "<td style=\"padding:2px\" align=\"center\"><input type=\"text\" class=\"numeric form-control input-sm\" name=\"nApplied"+lastRow+"\" id=\"nApplied"+lastRow+"\"  value=\"0.0000\" style=\"text-align:right\" /></td>";
	
	//alert('<tr>'+u + v + w + x + y + '</tr>');		
	
	$('#MyTable > tbody:last-child').append('<tr>'+u + v + w + x + y + z + '</tr>');
	
		
								$("input.numeric").numeric({decimalPlaces: 4});
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
								
								$("input.numeric").on("keyup", function (e) {
										setPosi($(this).attr('name'),e.keyCode);
										GoToComp();
								});
								
					
	}
	else{
		alert("Paid To Required!");
	}
}

function setPosi(nme,keyCode){
		var r = nme.replace(/\D/g,'');
		var namez = nme.replace(/[0-9]/g, '');
		
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		

		if(namez=="nApplied"){
			//alert(keyCode);
			if(keyCode==38 && r!=1){//Up
				var z = parseInt(r) - parseInt(1);
				document.getElementById("nApplied"+z).focus();
			}
			
			if((keyCode==40 || keyCode==13) && r!=lastRow){//Down or ENTER
				var z = parseInt(r) + parseInt(1);
				document.getElementById("nApplied"+z).focus();
			}
			
		}

}

function chkform(){
	var isOK = "True";
	//alert(isOK);
	
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(document.getElementById("txttotpaid").value == 0){
			$("#AlertMsg").html("<b>ERROR: </b>Enter total paid!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			isOK="False";
			return false;
		}

			var npaid = document.getElementById("txttotpaid").value;
			var napplied = document.getElementById("txtnGross").value;
			
			var oob = parseFloat(npaid) - parseFloat(napplied);
			oob = oob.toFixed(4);
		
		if(parseFloat(oob)  > 1){
			
			
			$("#AlertMsg").html("<b>ERROR: </b>Unbalanced amount!<br>Out of Balance: "+ Math.abs(oob));
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			isOK="False";
			return false;
		}
		
		
		if(isOK == "True"){
			document.getElementById("hdnrowcnt").value = lastRow;
		//	$("#frmpos").submit();

		return true;
		}

}

function GoToComp(){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		var z;
		var gross = 0;
		
		for (z=1; z<=lastRow; z++){
			gross = parseFloat(gross) + parseFloat(document.getElementById("nApplied"+z).value);
		}
		
		//document.getElementById("txtnGross").value = gross.toFixed(2);
		document.getElementById("txttotpaid").value = gross.toFixed(2);

}
</script>
