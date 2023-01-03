<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Receive_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>


</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="Received_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Receiving</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="100">SUPPLIER:</tH>
    <td style="padding:2px">
    	<div class="col-xs-7">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." autocomplete="off">
        </div> 
        &nbsp;&nbsp;
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px" readonly>
            <input type="hidden" id="txtcustchkr" name="txtcustchkr">    
            
            <input type="hidden" id="txtrefSI" name="txtrefSI">        
    </td>
    <tH width="150">DATE:</tH>
    <td style="padding:2px;">
     <div class="col-xs-8">
		<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" readonly/>
     </div>
	</div>
    </td>
  </tr>
  <tr>
    <tH width="100">REMARKS:</tH>
    <td style="padding:2px"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
    <tH width="150" style="padding:2px">RECEIVED DATE:</tH>
    <td style="padding:2px">
    <div class="col-xs-8">

    <input type='text' class="datepick form-control input-sm" id="rec_delivery" name="rec_delivery" value="<?php echo date("m/d/Y", strtotime($RecDate)); ?>" />

    </div>
    </td>
  </tr>
<!--
  <tr>
    <tH>&nbsp;</tH>
    <td style="padding:2px">&nbsp;</td>
    <tH style="padding:2px">RECEIVED TYPE:</tH>
    <td style="padding:2px"><div class="col-xs-5">
       <!-- <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="3">
          <option value="Grocery">Grocery</option>
          <option value="Cripples">Cripples</option>
        </select>
   </div></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  -->
<tr>
    <td colspan="2">
        <div class="col-xs-8 nopadwdown">
          <input type="text" id="txtsinum" name="txtsinum" class="form-control input-sm	" placeholder="Search Purchase Order No..." size="80" tabindex="5">
        </div>
        
		<input type="hidden" name="hdnident" id="hdnident">
        <input type="hidden" name="hdnprodid" id="hdnprodid">
        <input type="hidden" name="hdnprodnme" id="hdnprodnme">
        <input type="hidden" name="hdnprice" id="hdnprice">
        <input type="hidden" name="hdnunit" id="hdnunit">
        <input type="hidden" name="hdnmainunit" id="hdnmainunit">
        <input type="hidden" name="hdnfactor" id="hdnfactor">
        <input type="hidden" name="hdncost" id="hdncost">
        <input type="hidden" name="hdnqty" id="hdnqty"> 
        <input type="hidden" name="hdnamount" id="hdnamount">

    </td>
    <td>&nbsp;<b>TOTAL AMOUNT : </b></td>
    <td><input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>

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
	
            <table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">

					<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description (Convertion)</th>
                        <th style="border-bottom:1px solid #999">UOM</th>
						<th style="border-bottom:1px solid #999">Qty</th>
						<th style="border-bottom:1px solid #999">Price</th>
						<th style="border-bottom:1px solid #999">Amount</th>
                        <th style="border-bottom:1px solid #999">Conv. Factor</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
					<tbody class="tbody">
                    </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
 
     <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnqo" name="btnqo">PO<br> (Insert)</button>
    
     <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (F2)</button>
   
</td>
    <td>&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>


<!-- DETAILS ONLY -->
<div class="modal fade" id="mySIModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="DRListHeader">PO Details</h3>
            </div>
            
            <div class="modal-body pre-scrollable">
            
                          <table name='MyDRDetList' id='MyDRDetList' class="table table-small">
                           <thead>
                            <tr>
                              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                              <th>Item No</th>
                              <th>Description</th>
                              <th>UOM</th>
                              <th>Qty</th>
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


<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">PO List</h3>
            </div>
            
            <div class="modal-body">
            
       <div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-4 nopadding pre-scrollable">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
                           <thead>
                            <tr>
                              <th>Quote No</th>
                              <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                    </div>

                    <div class="col-xs-8 nopadwleft pre-scrollable">
                          <table name='MyInvDetList' id='MyInvDetList' class="table table-small">
                           <thead>
                            <tr>
                              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                              <th>Item No</th>
                              <th>Description</th>
                              <th>UOM</th>
                              <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
                    </div>
               </div>

        </div>
         	            
			</div>
			
            <div class="modal-footer">
                <button type="button" id="btnSave" onClick="InsertSI()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->

</body>
</html>

<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 113) { //F2
		  return chkform();
	  }
	  else if(e.keyCode == 45) { //Insert
		openinv();
	  }
	});


$(document).ready(function() {
	
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });
	
	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_supplier.php",
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
			 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
		}
	});
	
	//reference PO  searching	
	$('#txtsinum').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "th_sinum.php",
				dataType: "json",
				data: {
					query: $("#txtsinum").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.cpono+'</span><br><small><span class="dropdown-item-extra">' + item.dcutdate + '</span> ' + item.ngross + '</small></div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					
			 $("#txtsinum").val(item.cpono).change();
			 
			 if($("#txtcustid").val()==""){			
				$('#txtcust').val(item.cname); 
				$("#txtcustid").val(item.ccode);
				
				 $("#txtrefSI").val(item.cpono);
				 putSIdetail(item.cpono);
			 }else{
				 if($("#txtcustid").val()!=item.ccode){
					  alert("Invoice's Customer didn't match.");
					  $("#txtsinum").val("").change();
				 }
				 else{
					// alert(item.cpono);
					 $("#txtrefSI").val(item.cpono);
					 putSIdetail(item.cpono);
				 }
			 }
			
		}
	
	});


$('#MyInvTbl').on('mouseover', "td[name='tditem']", function() {
	 $(this).css('cursor','pointer');
});	
	
	
$('#MyInvTbl').on('click', "td[name='tditem']", function() {
	
	var drno = $(this).text();

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("PO List: " + $('#txtcust').val() + " | PO Details: " + drno + "<div id='loadimg'><center><img src='../images/cusload.gif' style='show:none;'> </center> </div>");
	
	$('#MyInvDetList tbody').empty();
	$('#MyDRDetList tbody').empty();
		
	$('#loadimg').show();
	
			var salesnos = "";
			var cnt = 0;
			var rc = $('#MyTable tr').length;
			for(y=1;y<=rc-1;y++){ 
			
			 if($('#txtcreference'+y).val()==drno){
			  cnt = cnt + 1;
			  if(cnt>1){
				  salesnos = salesnos + ",";
			  }
			  			  
				salesnos = salesnos + $('#txtitemcode'+y).val();
			 }
			}
					//alert('th_sinumdet.php?x='+drno+"&y="+salesnos);
					$.ajax({
                    url: 'th_sinumdet.php',
					data: 'x='+drno+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                      console.log(data);
					  $.each(data,function(index,item){
						  if(item.citemno==""){
							  alert("NO more items to add!")
						  }
						  else{
						  
							if (item.nqty>=1){
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.citemno+"' name='chkSales[]'>"),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty)
								).appendTo("#MyInvDetList tbody");
							}
					 	 }
					 });
                    },
					complete: function(){
						$('#loadimg').hide();
					},
                    error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
                    }
                });

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


$('#MyTable :input').keydown(function(e) {
	
				var txtinput = $(this).attr('name');
				tblnav(e.keyCode,txtinput)

});
	
});

function tblnav(xcode,txtinput){
	alert(xcode);
				var inputCNT = txtinput.replace(/\D/g,'');
				var inputNME = txtinput.replace(/\d+/g, '');
				 
				switch(xcode){
					case 39: // <Right>
						if(inputNME=="txtnqty"){
							$("#txtnprice"+inputCNT).focus();
						}
						else if(inputNME=="txtnprice"){
							$("#txtnfactor"+inputCNT).focus();
						}
						 
						break;
					case 38: // <Up>  
					 	var idx =  parseInt(inputCNT) - 1;
               			$("#"+inputNME+idx).focus();
						break;
					case 37: // <Left>
						if(inputNME=="txtnfactor"){
							$("#txtnprice"+inputCNT).focus();
						}
						else if(inputNME=="txtnprice"){
							$("#txtnqty"+inputCNT).focus();
						}

						break;
					case 40: // <Down>
					 	var idx =  parseInt(inputCNT) + 1;
               			$("#"+inputNME+idx).focus();
						break;
				}       

}

function putSIdetail(val){

	$('#mySIModal').modal('show');

	$('#DRListHeader').html("PO List: " + val + "<div id='loadimg'><center><img src='../images/cusload.gif' style='show:none;'> </center> </div>");
	$('#MyDRDetList tbody').empty();
	$('#MyInvDetList tbody').empty();
		
	$('#loadimg').show();
	
			var salesnos = "";
			var cnt = 0;
			var rc = $('#MyTable tr').length;
			for(y=1;y<=rc-1;y++){ 
			
			 if($('#txtcreference'+y).val()==val){
			  cnt = cnt + 1;
			  if(cnt>1){
				  salesnos = salesnos + ",";
			  }
			  			  
				salesnos = salesnos + $('#txtitemcode'+y).val();
			 }
			}

					$.ajax({
                    url: 'th_sinumdet.php',
					data: 'x='+val+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					   	   console.log(data);
						   $.each(data,function(index,item){
							if (item.nqty>=1){
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.citemno+"' name='chkSales[]'>"),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty)
								).appendTo("#MyDRDetList tbody");
							}
	
						   });
                    },
					complete: function(){
						$('#loadimg').hide();
					},
                    error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
						
						 $("#txtsinum").val("").change();
						 
						 $('#mySIModal').modal('hide');

					}
                });


}

function InsertSI(){	
	
   $("input[name='chkSales[]']:checked").each( function () {
	   
	
				var tranno = $("#txtrefSI").val();
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_sinumdetails.php?id=" + tranno + "&itm=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);
                       $.each(data,function(index,item){
						
						$('#hdnident').val(item.nident);
						$('#hdnprodnme').val(item.citemdesc); 
						$('#hdnprodid').val(item.citemno); 
						$("#hdnunit").val(item.cunit);
						$("#hdnmainunit").val(item.cmainunit);
						$("#hdnfactor").val(item.nfactor);
						$("#hdncost").val(item.ncost);
						$("#hdnqty").val(item.nqty);
						$("#hdnamount").val(item.namount); 
						$("#hdnprice").val(item.nprice); 
						
						addItemName();
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

   });
   //alert($("#hdnQuoteNo").val());
   
   $('#mySIModal').modal('hide');
   $('#mySIRef').modal('hide');
   
   $("#txtsinum").val("").change();
   						$('#hdnident').val("");
						$('#hdnprodnme').val(""); 
						$('#hdnprodid').val(""); 
						$("#hdnunit").val("");
						$("#hdnmainunit").val("");
						$("#hdnfactor").val("");
						$("#hdncost").val("");
						$("#hdnqty").val("");
						$("#hdnamount").val(""); 
						$("#hdnprice").val(""); 


}


function addItemName(){

	var itmident = document.getElementById("hdnident").value;
	var itmcode = document.getElementById("hdnprodid").value; 
	var itmdesc = document.getElementById("hdnprodnme").value;
	var itmprice = document.getElementById("hdnprice").value;
	var itmunit = document.getElementById("hdnunit").value;
	var itmnqty = document.getElementById("hdnqty").value;
	var crefno = document.getElementById("txtrefSI").value;	
	var itmnamount = document.getElementById("hdnamount").value;
	var itmmunit = document.getElementById("hdnmainunit").value;
	var itmfactor = document.getElementById("hdnfactor").value;
	var itmcost = document.getElementById("hdncost").value;

	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('MyTable').insertRow(-1);
	var u=a.insertCell(0);
	var v=a.insertCell(1);
	var v2=a.insertCell(2);
	var w=a.insertCell(3);
		w.style.width = "100px";
		w.style.padding = "1px";
	var x=a.insertCell(4);
		x.style.width = "100px";
		x.style.padding = "1px";
	var y=a.insertCell(5);
		y.style.width = "100px";
		y.style.padding = "1px";
	var w2=a.insertCell(6);
		w2.style.width = "100px";
		w2.style.padding = "1px";
		w2.style.paddingLeft = "10px";
	var z=a.insertCell(7);
		z.style.width = "80px";
		z.align = "right";
	
	u.innerHTML = "<input type='hidden' value='"+crefno+"' name='txtcreference"+lastRow+"' id='txtcreference"+lastRow+"'> <input type='hidden' value='"+itmident+"' name='txtnrefident"+lastRow+"' id='txtnrefident"+lastRow+"'> <input type='hidden' value='"+itmcode+"' name='txtitemcode"+lastRow+"' id='txtitemcode"+lastRow+"'>"+itmcode;
	v.innerHTML = "<input type='hidden' value='"+itmdesc+"' name='txtitemdesc"+lastRow+"' id='txtitemdesc"+lastRow+"'>"+itmdesc;
	v2.innerHTML = "<input type='hidden' value='"+itmunit+"' name='txtcunit"+lastRow+"' id='txtcunit"+lastRow+"'>"+itmunit;
	w.innerHTML = "<input type='text' value='"+itmnqty+"' class='numeric form-control input-xs' style='text-align:right' name='txtnqty"+lastRow+"' id='txtnqty"+lastRow+"' autocomplete='off' /> <input type='hidden' value='"+itmnqty+"' name='txtnqtyOrig"+lastRow+"' id='txtnqtyOrig"+lastRow+"'>";
	x.innerHTML = "<input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right' name='txtnprice"+lastRow+"' id='txtnprice"+lastRow+"'  autocomplete='off' />";
	y.innerHTML = "<input type='text' value='"+itmnamount+"' class='form-control input-xs' style='text-align:right' name='txtnamount"+lastRow+"' id='txtnamount"+lastRow+"' readonly>";
	w2.innerHTML = "<input type='text' value='"+itmfactor+"' class='numeric form-control input-xs' style='text-align:right' name='txtnfactor"+lastRow+"' id='txtnfactor"+lastRow+"'  autocomplete='off' />";
	z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='delete' onClick=\"deleteRow(this);\"/>";


									$("input.numeric").numeric();
									$("input.numeric").on("click, focus", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {
									   Chkval($(this).attr('name'));
									   GoToComp($(this).attr('name'));
									});
									
									$("input.numeric").on("keydown", function (e) {
										
										var txtinput = $(this).attr('name');
										tblnav(e.keyCode,txtinput)
									
									});
									
									ComputeGross();

}

function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTable').deleteRow(i);
	 document.getElementById('hdnrowcnt').value = lastRow - 2;
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempcreference = document.getElementById('txtcreference' + z);
			var tempnrefident = document.getElementById('txtnrefident' + z);
			var tempcitemno = document.getElementById('txtitemcode' + z);
			var tempcdesc = document.getElementById('txtitemdesc' + z);
			var tempnqty= document.getElementById('txtnqty' + z);
			var tempnqtyOrig= document.getElementById('txtnqtyOrig' + z);
			var tempcunit= document.getElementById('txtcunit' + z);
			var tempnprice = document.getElementById('txtnprice' + z);
			var tempnamount= document.getElementById('txtnamount' + z);
			var tempcfactor= document.getElementById('txtnfactor' + z);
			
			var x = z-1;
			tempcreference.id = "txtcreference" + x;
			tempcreference.name = "txtcreference" + x;
			tempnrefident.id = "txtnrefident" + x;
			tempnrefident.name = "txtnrefident" + x;			
			tempcitemno.id = "txtitemcode" + x;
			tempcitemno.name = "txtitemcode" + x;
			tempcdesc.id = "txtitemdesc" + x;
			tempcdesc.name = "txtitemdesc" + x;
			tempnqty.id = "txtnqty" + x;
			tempnqty.name = "txtnqty" + x;
			tempnqtyOrig.id = "txtnqtyOrig" + x;
			tempnqtyOrig.name = "txtnqtyOrig" + x;
			tempcunit.id = "txtcunit" + x;
			tempcunit.name = "txtcunit" + x;
			tempnprice.id = "txtnprice" + x;
			tempnprice.name = "txtnprice" + x;
			tempnamount.id = "txtnamount" + x;
			tempnamount.name = "txtnamount" + x;
			tempcfactor.id = "txtnfactor" + x;
			tempcfactor.name = "txtnfactor" + x;
			
			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

		}
ComputeGross();

if(lastRow==1){
	document.getElementById('txtcust').readOnly=false;
}

}

		function Chkval(nme){
		  
			var r = nme.replace( /^\D+/g, '');
			var x = nme.replace(/\d+/g, '');
			
			if(x=="txtnqty"){
				var nqty1 = $("#txtnqty"+r).val(); 
				var nqty2 = $("#txtnqtyOrig"+r).val(); 
				
				if(parseFloat(nqty1) > parseFloat(nqty2)){
					alert("Quantity cannot be greater than original quantity!");
					$("#txtnqty"+r).val(nqty2);
				}
			}
		}
		
		function GoToComp(nme){			

				 ComputeAmt(nme);
				 ComputeGross();

		}
		
	
		function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val();
			nqty = parseFloat(nqty)
			nnet = $("#txtnprice"+r).val();
			nnet = parseFloat(nnet);
			
			namt = nqty * nnet;
			namt = namt.toFixed(4);
						
			$("#txtnamount"+r).val(namt);

		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			var gross = 0;
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtnamount"+i).val();
					
					gross = gross + parseFloat(amt);
				}
			}
			
			gross = gross.toFixed(4);
			$("#txtnGross").val(gross);
			
		}

function addqty(){
	
	var itmcode = document.getElementById("txtprodid").value;
	//var itmdesc = document.getElementById("txtprodnme").value;
	//
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	var TotQty = 0;
	var TotAmt = 0;
	
	for (z=1; z<=lastRow; z++){
		if(document.getElementById("txtitemcode"+z).value==itmcode){
			var itmqty = document.getElementById("txtnqty"+z).value;
			var itmprice = document.getElementById("txtnprice"+z).value;
			
			TotQty = parseFloat(itmqty) + 1;
			document.getElementById("txtnqty"+z).value = TotQty;
			
			TotAmt = parseFloat(document.getElementById("txtnamount" + z).value) + parseFloat(itmprice);
			document.getElementById("txtnamount" + z).value = TotAmt.toFixed(2);
		}

	}
	
ComputeGross();

}

function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){
		alert("Supplier Required!");
		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(lastRow == 0){
		alert("No details found!");
		return false;
		ISOK = "NO";
	}
	else{
		var msgz = "";
		for (z=1; z<=lastRow; z++){
			if(document.getElementById("txtnqty"+z).value == 0 || document.getElementById("txtnqty"+z).value == ""){
				msgz = msgz + "\n Zero or blank qty is not allowed: row " + z;	
			}
		}
		
		if(msgz!=""){
			alert("Details Error: "+msgz);
			return false;
			ISOK = "NO";
		}
	}
	
	if(ISOK == "YES"){
		document.getElementById("hdnrowcnt").value = lastRow;
		document.getElementById("frmpos").submit();
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
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#InvListHdr').html("PO List: " + $('#txtcust').val())

			var xstat = "YES";
			
			$.ajax({
                    url: 'th_silist.php',
					data: 'x='+x,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                       console.log(data);
                       $.each(data,function(index,item){

								
						if(item.cpono=="NONE"){
							alert("NO Invoices Available!");
							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td name='tditem'>").text(item.cpono),
							$("<td>").text(item.ngross)
							).appendTo("#MyInvTbl tbody");
					   	}

                       });
					   

					   if(xstat=="YES"){
						   $('#mySIRef').modal('show');
					   }
                    },
                    error: function (req, status, err) {
						alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
					}
                });
			
			
			
		}

}

</script>
