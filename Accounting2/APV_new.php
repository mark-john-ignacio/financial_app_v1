<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "APV_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="APV_newsave.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
    	<legend>Accounts Payable Voucher</legend>	
        <table width="100%" border="0">
<!--
  <tr>
    <tH>APV No.:</tH>
    <td style="padding:2px;"><div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter APV No..." required>
    </div></td>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  -->
  <tr>
    <tH width="100">SUPPLIER:</tH>
    <td style="padding:2px;" width="500">
    	<div class="col-xs-8">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required autocomplete="off">
</div> 

        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
            
                    
            <input type="hidden" id="txtcustchkr" name="txtcustchkr">
            <input type="hidden" id="seltype" name="seltype">
            
    </td>
    <tH width="150"><span style="padding:2px">PAYEE:</span></tH>
    <td style="padding:2px;"><div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" width="20px" tabindex="1" required>
    </div></td>
  </tr>
  <tr>
    <tH width="100" rowspan="2" valign="top">PARTICULARS:</tH>
    <td rowspan="2" valign="top" style="padding:2px"><div class="col-xs-10">
       <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"></textarea>
    </div></td>
    <tH width="150" style="padding:2px">VOUCHER DATE:</tH>
    <td style="padding:2px"><div class="col-xs-5">
      <input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" />
   </div></td>
  </tr>
  <tr>
    <tH style="padding:2px">TOTAL AMOUNT :</tH>
    <td style="padding:2px">
    <div class="col-xs-8">
      <input type="text" class="form-control input-sm"id="txtnGross" name="txtnGross" tabindex="1" required value="0.00" style="font-weight:bold; color:#F00; text-align:right" readonly>
    </div>
    
   </td>
  </tr>
      </table>
<br>

<ul class="nav nav-tabs">
  <li class="active"><a href="#1" data-toggle="tab">Details</a></li>
  <li><a href="#2" data-toggle="tab">Accounting</a></li>
</ul>
  
  <div class="tab-content nopadwtop2x">
    <div class="tab-pane active" id="1">  
				<table width="100%" border="0">
        				<tr>
                            <td colspan="6" height="25px" valign="top">
                             
                              <input type="button" id="btnaddrr" name="btnaddrr" class="btn btn-xs btn-warning" value="ADD LINE" onClick="addrrdet('','');">
       						 
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
        
                <table id="MyTable" cellpadding"3px" width="100%" border="0">
                        
                        <tr>
                            <th style="border-bottom:1px solid #999">Ref No.</th>
                            <th style="border-bottom:1px solid #999">Supplier SI</th>
                            <th style="border-bottom:1px solid #999">Description</th>
                            <th style="border-bottom:1px solid #999">Amount</th>
                            <th style="border-bottom:1px solid #999">Remarks</th>
                            <th style="border-bottom:1px solid #999">&nbsp;</th>
                        </tr>
                        <tbody class="tbody">
                  </tbody>
                        
                </table>
    		<input type="hidden" name="hdnRRCnt" id="hdnRRCnt"> 
            </div>
	</div>
	<div class="tab-pane" id="2">
				<table width="100%" border="0">
        				<tr>
                            <td colspan="6" height="25px" valign="top">
                             
                              <input type="button" id="btnaddacc" name="btnaddacc" class="btn btn-xs btn-primary" value="ADD LINE" onClick="addaccntdet();">
       						 
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
        
                <table id="MyTable2" cellpadding"3px" width="100%" border="0">
    
                        <tr>
                            <th style="border-bottom:1px solid #999">Acct#</th>
                            <th style="border-bottom:1px solid #999">Account Title</th>
                            <th style="border-bottom:1px solid #999">Debit</th>
                            <th style="border-bottom:1px solid #999">Credit</th>
                            <th style="border-bottom:1px solid #999">Subsidiary</th>
                            <th style="border-bottom:1px solid #999">Remarks</th>
                            <th style="border-bottom:1px solid #999">&nbsp;</th>
                        </tr>
                        
                </table>
            <input type="hidden" name="hdnACCCnt" id="hdnACCCnt">
			</div>

	</div>
    </div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">


     <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnqo">RR<br> (Insert)</button>
    
     <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (F2)</button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>


<!-- DETAILS ONLY -->
<div class="modal fade" id="mySIModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="DRListHeader">RR List</h3>
            </div>
            
            <div class="modal-body pre-scrollable">
            
                          <table name='MyDRDetList' id='MyDRDetList' class="table table-small">
                           <thead>
                            <tr>
                              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                              <th>RR No</th>
                              <th>Received Date</th>
                              <th>Gross</th>
                              <th>Remarks</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
            </div>         	
                
           			
            <div class="modal-footer">
                <button type="button" id="btnSave" onClick="InsertSI()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 113) { //F2
		  return chkform();
	  }
	  else if(e.keyCode == 45) { //F1
		openinv();
	  }
	});


$(function(){
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });

	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.typ + " "+ item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			$("#txtpayee").val(item.value);
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
	
});


function addrrdet(rrno,amt){

if(document.getElementById("txtcustid").value!=""){
	
	$('#txtcust').attr('readonly', true);
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('MyTable').insertRow(-1);

	var u=a.insertCell(0);
		u.style.padding = "1px";
		u.style.width = "150px";
	var v=a.insertCell(1);
		v.style.padding = "1px";
		v.style.width = "150px";
	var w=a.insertCell(2);
		w.style.padding = "1px";
	var x=a.insertCell(3);
		x.style.width = "150px";
		x.style.padding = "1px";
	var y=a.insertCell(4);
		y.style.padding = "1px";
	var z=a.insertCell(5);
		z.style.width = "50px";
		z.style.padding = "1px";
		
	u.innerHTML = "<input type='text' name=\"txtrefno"+lastRow+"\" id=\"txtrefno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search RR No...\" style=\"text-transform:uppercase\" required value=\""+rrno+"\" autocomplete=\"off\" >";
	v.innerHTML = "<input type='text' name=\"txtsuppSI"+lastRow+"\" id=\"txtsuppSI"+lastRow+"\" class=\"form-control input-sm\">";
	w.innerHTML = "<input type='text' name=\"txtrrdesc"+lastRow+"\" id=\"txtrrdesc"+lastRow+"\" class=\"form-control input-sm\">";
	x.innerHTML = "<input type='text' name=\"txtnamount"+lastRow+"\" id=\"txtnamount"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+amt+"\">";
	y.innerHTML = "<input type='text' name=\"txtremarks"+lastRow+"\" id=\"txtremarks"+lastRow+"\" class=\"form-control input-sm\">";
	z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' class='delete' value='delete' onClick=\"deleteRow1(this);\"/>";
	
								$("input.numeric").numeric();
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
														
								$("input.numeric").on("keyup", function () {
									CompSub($(this).attr("name"), $(this).val());
									GoToComp();
								});

	
	if(rrno==""){
		$('#txtrefno'+lastRow).typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_rrlist.php",
				dataType: "json",
				async:false,
				data: {
					query: $('#txtrefno'+lastRow).val(), code: $("#txtcustid").val()
				},
				success: function (data) {
					response(data);
				},
                error: function (req, status, err) {
					alert('Something went wrong\nStatus: '+status +"\nError: "+err);
					console.log('Something went wrong', status, err);
                }
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.id+'</span><br><small><span class="dropdown-item-extra">' + item.value + '</span> '+item.label+'</small></div>';
		},
		highlighter: Object,
		afterSelect: function(item) {
			if(item.id!="NO AVAILABLE WRR"){ 
				$('#txtrefno'+lastRow).val(item.id).change(); 
				$('#txtnamount'+lastRow).val(item.value);
				
				genAccts(item.id);
				GoToComp();
				
				$('#txtsuppSI'+lastRow).focus();
			}
			else{
				$('#txtrefno'+lastRow).val("").change();
			}
		}
	});
	
	}
	else{
		genAccts(rrno);
		GoToComp();
	}
			
}
else{
	alert("Supplier Required!");
}
}

function genAccts(rrno){
	var x = $("#MyTable2 tr").length;
	
				$.ajax({
                    url: 'th_putAccnt.php',
					data: 'x='+rrno,
                    dataType: 'json',
                    method: 'post',
					async:false,
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                       console.log(data);
                       $.each(data,function(index,item){

							$("<tr>").append(
							$("<td style='padding: 1px' width='100px'>").html("<input type='hidden' name=\"txtcrefrr"+x+"\" id=\"txtcrefrr"+x+"\" value=\""+rrno+"\"><input type='text' name=\"txtacctno"+x+"\" id=\"txtacctno"+x+"\" class=\"form-control input-sm\" value=\""+item.acctid+"\" style=\"text-transform:uppercase\" readOnly>"),
							$("<td style='padding: 1px'>").html("<input type='text' name=\"txtacctitle"+x+"\" id=\"txtacctitle"+x+"\" class=\"form-control input-sm\" readonly value=\""+item.accttitle+"\">"),
							$("<td style='padding: 1px' width='150px'>").html("<input type='text' name=\"txtdebit"+x+"\" id=\"txtdebit"+x+"\" class=\"numeric form-control input-sm\" value=\""+item.ndebit+"\" required>"),
							$("<td style='padding: 1px' width='150px'>").html("<input type='text' name=\"txtcredit"+x+"\" id=\"txtcredit"+x+"\" class=\"numeric form-control input-sm\" value=\""+item.ncredit+"\" required>"),
							$("<td style='padding: 1px' width='200px'>").html("<input type='text' name=\"txtsubs"+x+"\" id=\"txtsubs"+x+"\" class=\"form-control input-sm\" placeholder=\"Search Name...\" /> <input type='hidden' name=\"txtsubsid"+x+"\" id=\"txtsubsid"+x+"\">"),
							$("<td style='padding: 1px'>").html("<input type='text' name=\"txtacctrem"+x+"\" id=\"txtacctrem"+x+"\" class=\"form-control input-sm\" placeholder=\""+rrno+"\">"),
							$("<td style='padding: 1px' width='50px'>").html("<input class='btn btn-danger btn-xs' type='button' id='row2_"+x+"_delete' class='delete' value='delete' onClick=\"deleteRow2(this);\"/>")
							).appendTo("#MyTable2 tbody");
							
							x = x + 1;


                       });
					   
                    },
                    error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
					}
                });


}


function addaccntdet(){ ////Auto insert Accounts Entry
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('MyTable2').insertRow(-1);

	var u=a.insertCell(0);
		u.style.padding = "1px";
		u.style.width = "100px";
	var v=a.insertCell(1);
		v.style.padding = "1px";
	var w=a.insertCell(2);
		w.style.padding = "1px";
		w.style.width = "150px";
	var x=a.insertCell(3);
		x.style.width = "150px";
		x.style.padding = "1px";
	var y=a.insertCell(4);
		y.style.width = "200px";
		y.style.padding = "1px";
	var z=a.insertCell(5);
		z.style.padding = "1px";
	var b=a.insertCell(6);
		b.style.width = "50px";
		b.style.padding = "1px";


	u.innerHTML = "<input type='hidden' name=\"txtcrefrr"+lastRow+"\" id=\"txtcrefrr"+lastRow+"\" value=\"\"><input type='text' name=\"txtacctno"+lastRow+"\" id=\"txtacctno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Acct No...\" style=\"text-transform:uppercase\" required autocomplete='off'>";
	v.innerHTML = "<input type='text' name=\"txtacctitle"+lastRow+"\" id=\"txtacctitle"+lastRow+"\" class=\"form-control input-sm\" autocomplete='off' placeholder=\"Acct Title...\">";
	w.innerHTML = "<input type='text' name=\"txtdebit"+lastRow+"\" id=\"txtdebit"+lastRow+"\" class=\"numeric form-control input-sm\" value=\"0.00\" required>";
	x.innerHTML = "<input type='text' name=\"txtcredit"+lastRow+"\" id=\"txtcredit"+lastRow+"\" class=\"numeric form-control input-sm\" value=\"0.00\" required>";
	y.innerHTML = "<input type='text' name=\"txtsubs"+lastRow+"\" id=\"txtsubs"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search Name...\"> <input type='hidden' name=\"txtsubsid"+lastRow+"\" id=\"txtsubsid"+lastRow+"\">";
	z.innerHTML = "<input type='text' name=\"txtacctrem"+lastRow+"\" id=\"txtacctrem"+lastRow+"\" class=\"form-control input-sm\">";
	b.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row2_"+lastRow+"_delete' value='delete' onClick=\"deleteRow2(this);\"/>";

					
								$("input.numeric").numeric();
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
														
								$("input.numeric").on("keyup", function () {
									GoToComp();
								});
					

				  	$("#txtacctno"+lastRow).typeahead({
						autoSelect: true,
						source: function(request, response) {
							$.ajax({
								url: "th_accounts.php",
								dataType: "json",
								data: {
									query: $("#txtacctno"+lastRow).val()
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
										
							$("#txtacctno"+lastRow).val(item.id).change(); 
							$("#txtacctitle"+lastRow).val(item.name); 
							$("#txtdebit"+lastRow).focus();
							
						}
					});



				  	$("#txtacctitle"+lastRow).typeahead({
						autoSelect: true,
						source: function(request, response) {
							$.ajax({
								url: "th_accounts.php",
								dataType: "json",
								data: {
									query: $("#txtacctitle"+lastRow).val()
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
										
							$("#txtacctitle"+lastRow).val(item.name).change(); 
							$("#txtacctno"+lastRow).val(item.id); 
							$("#txtdebit"+lastRow).focus();
							
						}
					});


}

function CompSub(nme, valx){
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var cnt = tbl.length;
	
		var indx = nme.replace(/\D/g,'');
		var RRNo = $("#txtrefno"+indx).val();
		
		cnt = cnt - 1;

		for (i = 1; i <= cnt; i++) {
			if($("#txtcrefrr"+i).val() == RRNo){
				if(document.getElementById('txtdebit'+i).value != 0){
					document.getElementById('txtdebit'+i).value = valx;
				}
				if(document.getElementById('txtcredit'+i).value != 0){
					document.getElementById('txtcredit'+i).value = valx;
				}
			}
		}
		
}

function GoToComp(){

		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var cnt = tbl.length;
	
		cnt = cnt - 1;

		var xdeb = 0;
		var xcrd = 0;
		
		for (i = 1; i <= cnt; i++) {
			xdeb = xdeb + parseFloat(document.getElementById('txtdebit'+i).value);
			xcrd = xcrd + parseFloat(document.getElementById('txtcredit'+i).value);
		}

		var totdebit = xdeb.toFixed(2);
		var totcredit = xcrd.toFixed(2);
		
		if(totdebit==totcredit){
			$("#txtnGross").val(totdebit);
			//document.getElementById("grosmsg").innerHTML = "";
		}
		else{
			$("#txtnGross").val('(DR: '+totdebit+', CR: '+totcredit+')');
			//document.getElementById("txtnGross").value = 'UNBALANCED TRANSACTION';
			//document.getElementById("grosmsg").innerHTML = "UNBALANCED TRANSACTION";
		}
}


function openinv(){
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid supplier!");
		}
		else{
			
			$("#txtcustid").attr("readonly", true);
			$("#txtcust").attr("readonly", true);

			//clear table body if may laman
			$('#MyDRDetList tbody').empty(); 
			

			var y;
			var salesnos = "";
			var cnt = 0;
			var rc = $('#MyTable tr').length;

				for(y=1;y<=rc-1;y++){ 
				  cnt = cnt + 1;
				  if(cnt>1){
					  salesnos = salesnos + ",";
				  }
				 // alert("value: " + document.getElementById("txtrefno"+y).value);
					salesnos = salesnos + $('#txtrefno'+y).val();
				}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#DRListHeader').html("RR List: " + $('#txtcust').val())

			var xstat = "YES";
						
			$.ajax({
                    url: 'th_rrlistings.php',
					data: 'x='+x+'&y='+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                       console.log(data);
                       $.each(data,function(index,item){

								
						if(item.crrno=="NONE"){
							alert("NO RR Available!");
							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.crrno+":"+item.ngross+"' name='chkSales[]'>"),
								$("<td>").text(item.crrno),
								$("<td>").text(item.ddate),
								$("<td>").text(item.ngross),
								$("<td>").text(item.cremarks)
								).appendTo("#MyDRDetList tbody");
					   	}

                       });
					   

					   if(xstat=="YES"){
						   $('#mySIModal').modal('show');
					   }
                    },
                    error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
					}
                });
			
			
			
		}

}

function InsertSI(){	
	 var totGross = 0;
   $("input[name='chkSales[]']:checked").each( function () {
	   var xyz = $(this).val();
	   
	   var valuesz = xyz.split(":");
	   	var rrno=valuesz[0];
		var amt=valuesz[1];
		
		 addrrdet(rrno,amt);
		 
		 //totGross = parseFloat(totGross) + parseFloat(amt) ;

   });


	GoToComp();

	$('#mySIModal').modal('hide');

}

function deleteRow1(r){
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	var cRRNo = document.getElementById('txtrefno'+i).value;
	
	 document.getElementById('MyTable').deleteRow(i);

	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			
			var temprefno = document.getElementById('txtrefno' + z);
			var tempsuppSI = document.getElementById('txtsuppSI' + z);
			var temprrdesc = document.getElementById('txtrrdesc' + z);
			var tempamnt = document.getElementById('txtnamount' + z);
			var temprem= document.getElementById('txtremarks' + z);
			var tempbtn= document.getElementById('row_' + z + '_delete');
			
			var x = z-1;
			temprefno.id = "txtrefno" + x;
			temprefno.name = "txtrefno" + x;
			tempsuppSI.id = "txtsuppSI" + x;
			tempsuppSI.name = "txtsuppSI" + x;			
			temprrdesc.id = "txtrrdesc" + x;
			temprrdesc.name = "txtrrdesc" + x;
			tempamnt.id = "txtnamount" + x;
			tempamnt.name = "txtnamount" + x;
			temprem.id = "txtremarks" + x;
			temprem.name = "txtremarks" + x;
			tempbtn.id = "row_" + x + "_delete";
			tempbtn.name = "row_" + x + "_delete";
		
		}

if(lastRow==1){
	document.getElementById('txtcust').readOnly=false;
}

 	var accntnum=0;
				
 	var tbl2 = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow2 = tbl2.length-1;

	for(yz=1; yz<=lastRow2; yz++){
		//alert(document.getElementById("txtcrefrr"+yz).value);
		if(document.getElementById("txtcrefrr"+yz).value==cRRNo){
			accntnum = accntnum + 1;
		}
	}
	

 	var xz=0;
	var yz;


 if(accntnum>=1){
	do{

 	var tbl2 = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow2 = tbl2.length;

		for(yz=1; yz<=lastRow2; yz++){
			if(document.getElementById("txtcrefrr"+yz).value==cRRNo){
				var thsitm = document.getElementById("row2_" + yz + "_delete")
				
				deleteRow2(thsitm);
				xz = xz + 1;
				
				break;
			}
		}
		
	//	alert(xz+"!="+accntnum);
	}while(xz!=accntnum);
 }
 

	
}

function deleteRow2(r){

	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	
	document.getElementById('MyTable2').deleteRow(i);

	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			
			var temprefrr = document.getElementById('txtcrefrr' + z);
			var tempacctno = document.getElementById('txtacctno' + z);
			var tempaactdesc = document.getElementById('txtacctitle' + z);
			var tempdebit = document.getElementById('txtdebit' + z);
			var tempcredit= document.getElementById('txtcredit' + z);
			var tempsubs = document.getElementById('txtsubs' + z);
			var tempsubsid = document.getElementById('txtsubsid' + z);
			var tempacctrem= document.getElementById('txtacctrem' + z);
			var tempbtn= document.getElementById('row2_' + z + '_delete');
			
			var x = z-1;
			temprefrr.id = "txtcrefrr" + x;
			temprefrr.name = "txtcrefrr" + x;
			tempacctno.id = "txtacctno" + x;
			tempacctno.name = "txtacctno" + x;			
			tempaactdesc.id = "txtacctitle" + x;
			tempaactdesc.name = "txtacctitle" + x;
			tempdebit.id = "txtdebit" + x;
			tempdebit.name = "txtdebit" + x;
			tempcredit.id = "txtcredit" + x;
			tempcredit.name = "txtcredit" + x;
			tempsubs.id = "txtsubs" + x;
			tempsubs.name = "txtsubs" + x;
			tempsubsid.id = "txtsubsid" + x;
			tempsubsid.name = "txtsubsid" + x;
			tempacctrem.id = "txtacctrem" + x;
			tempacctrem.name = "txtacctrem" + x;
			tempbtn.id = "row2_" + x + "_delete";
			tempbtn.name = "row2_" + x + "_delete";
			
		}
		
		GoToComp();
}

function chkform(){
	var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRowRR = tbl1.length-1;

	var tbl2 = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRowACC = tbl2.length-1;
	
	if(lastRowRR==0 && lastRowACC==0){  
		alert("Transaction has NO Details!");
		return false;
	}
	else{
		if(document.getElementById("txtnGross").value==0 || document.getElementById("txtnGross").value==""){
			alert("No amount detected. Please check your details!");
			return false;
		}
		else{
		  if(isNaN(document.getElementById("txtnGross").value)){
			  alert("Unbalanced transaction!");
			  return false;
		  }
		  else{
			document.getElementById("hdnRRCnt").value = lastRowRR;
			document.getElementById("hdnACCCnt").value = lastRowACC;
			
			$("#frmpos").submit();
			return true;
		  }
		}
	}

}



</script>

</body>
</html>