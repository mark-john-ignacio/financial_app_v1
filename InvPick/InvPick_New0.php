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

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="InvPick_save.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Dispatch/Picking</legend>	
        <input type="hidden" value="<?php echo $nCHKREFvalue;?>" name="hdnCHECKREFval" id="hdnCHECKREFval">
        <table width="100%" border="0">
  <tr>
    <tH width="100">REF.SO:</tH>
    <td style="padding:2px">
    		<div class="col-xs-5">
        	<input type="text" class="form-control input-sm" id="txtrrref" name="txtrrref" width="20px" tabindex="1" placeholder="Search Reference RR" autocomplete="off">
        </div> 
    </td>
    <tH width="150">DATE:</tH>
    <td style="padding:2px;">
     <div class="col-xs-8">
		<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" readonly/>
	</div>
    </td>
  </tr>
  <tr>
    <tH width="100">REMARKS:</tH>
    <td style="padding:2px" width="60%"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
    <tH width="150" style="padding:2px">DATE PICKED:</tH>
    <td style="padding:2px">
    <div class="col-xs-8">

		<input type='text' class="datepick form-control input-sm" id="date_received" name="date_received" />

     </div>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <th style="padding:2px"><!--<span style="padding:2px">PURCHASE TYPE:</span>-->&nbsp;</th>
    <td>&nbsp;
    <!--
    <div class="col-xs-5">
        <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="3">
          <option value="Grocery">Grocery</option>
          <option value="Cripples">Cripples</option>
        </select>
   </div>
   -->
   </td>
    </tr>
</table>

<ul class="nav nav-tabs">
  <li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Items List</a></li>
  <li id="liacct"><a href="#2Acct" data-toggle="tab">Items Inventory</a></li>
</ul>

  <div class="tab-content nopadwtop2x">
    <div class="tab-pane active" id="1Det">
         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 300px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable" width="100%">

								<tr>
									<th style="border-bottom:1px solid #999">Code</th>
									<th style="border-bottom:1px solid #999">Description</th>
			            <th style="border-bottom:1px solid #999">UOM</th>
									<th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">Conv.</th>
									<th style="border-bottom:1px solid #999">Tot.Qty</th>
									<th style="border-bottom:1px solid #999">Qty</th>
			            <th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
								<tbody class="tbody">
			          </tbody>
			                    
						</table>
				</div>
		</div>

		<div class="tab-pane" id="2Acct">

             <div class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 250px;
                        text-align: left;
                        overflow: auto">
        
                <table id="MyTable2" cellpadding="3px" width="100%" border="0">
    							<thead>
                        <tr>
                        	
                            <th style="border-bottom:1px solid #999">Item Code</th>
                            <th style="border-bottom:1px solid #999">Serial No.</th>
                            <th style="border-bottom:1px solid #999">UOM</th>
                            <th style="border-bottom:1px solid #999">Qty</th>
                            <th style="border-bottom:1px solid #999">Location</th>
                            <th style="border-bottom:1px solid #999">Expiration Date</th>
                            <th style="border-bottom:1px solid #999">&nbsp;</th>
                        </tr>
                   </thead>
                   <tbody>
                   </tbody>
                        
                </table>
            <input type="hidden" name="hdnserialscnt" id="hdnserialscnt">
			</div>

	</div>

</div>
	
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 

    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

     <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
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

<div class="modal fade" id="SerialMod" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="InvSerDetHdr">Inventory Detail</h4>
								<input type="hidden" class="form-control input-sm" name="serdisitmcode" id="serdisitmcode"> 
								<input type="hidden" class="form-control input-sm" name="serdisrefident" id="serdisrefident">
            </div>
            
            <div class="modal-body" style="height:20vh">

								

						</div>

						<div class="modal-footer">
								<button class="btn btn-success btn-sm" name="btnInsSer" id="btnInsSer">Insert (Ctrl+Insert)</button>
								<button class="btn btn-danger btn-sm" name="btnClsSer" id="btnClsSer" data-dismiss="modal" >Close (Ctrl+X)</button>
						</div>
				</div>
		</div>
</div>


<form method="post" name="frmedit" id="frmedit" action="InvRec_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

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
		 window.location.replace("Inv.php");

	  }
		else if(e.keyCode == 88 && e.ctrlKey){ //CTRL X - Close Modal
			if($('#SerialMod').hasClass('in')==true){
		 		$("#btnClsSer").click();
			}

	  }
		else if(e.keyCode == 45 && e.ctrlKey){ //CTRL Insert - Insert Modal
			if($('#SerialMod').hasClass('in')==true){
		 		$("#btnInsSer").click();
			}

	  } 
	});


$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY',
			useCurrent: false,
			minDate: moment(),
			defaultDate: moment(),
    });
	
});
	
$(function(){	
	
	$('#txtrrref').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_rrlist.php",
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
			 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + ' - ' + item.ddate +'</span><br><small>' + item.cname + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtrrref').val(item.id).change(); 

			InsertSI(item.id);

			$('#txtrrref').attr("readonly", true);

		}
	});
	
});

function InsertSI(rrno){	

	   		$.ajax({
					url : "th_qolistput.php?id=" + rrno,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);
             $.each(data,function(index,item){

								myFunctionadd(item.id,item.cdesc,item.cunit,item.nqty,item.nfactor,item.cmainuom,item.xref,item.nident);
								FindSerials(item.id,item.cunit,item.nqty,item.nfactor,item.cmainuom,item.nident);
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

}

function myFunctionadd(prodid,prdnme,cunit,nqty,nfactor,cmainunit,xref,nident){

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
		
	var a=document.getElementById('MyTable').insertRow(-1);
	var s=a.insertCell(0);
		s.style.width = "120px";
		
	var t=a.insertCell(1);
		t.style.whiteSpace = "nowrap";
		t.style.textOverflow = "ellipsis";
		t.style.overflow = "hidden";
		t.style.maxWidth = "1px";
		t.style.paddingRight = "1px";
	var u=a.insertCell(2);
		u.style.width = "80px";
		u.style.padding = "1px";
	var v=a.insertCell(3);
		v.style.width = "80px";
		v.style.padding = "1px";
	var w=a.insertCell(4);
		w.style.width = "150px";
		w.style.padding = "1px";
	var x=a.insertCell(5);
		x.style.width = "100px";
		x.style.padding = "1px";
	var y=a.insertCell(6);
		y.style.width = "100px";
		y.style.padding = "1px";
	var z=a.insertCell(7);
		z.style.width = "110px";
		z.style.padding = "1px";    //prodid,prdnme,cunit,nqty,nfactor,cmainunit,xref,nident

	s.innerHTML = "<input type='hidden' value='"+prodid+"' name=\"txtitemcode\" id=\"txtitemcode\">"+prodid+"<input type='hidden' value='"+xref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+nident+"' name=\"txtnrefident\" id=\"txtnrefident\">";
	t.innerHTML = prdnme;
	u.innerHTML = "<input type='hidden' value='"+cunit+"' name=\"txtcunit\" id=\"txtcunit\">"+cunit;
	v.innerHTML = "<input type='hidden' value='"+nqty+"' name=\"txtnqtyrr\" id=\"txtnqtyrr\">"+nqty;
	w.innerHTML = "<input type='hidden' value='"+nfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'>"+nfactor+" "+cmainunit+"/"+cunit;
	x.innerHTML = "<input type='hidden' value='"+cmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'>"+parseFloat(nqty)*parseFloat(nfactor)+" "+cmainunit;
	y.innerHTML = "<input type='text' value='0' class='form-control input-xs' style='text-align:right' name=\"txtnqty\" id='txtnqty"+prodid+nident+"' readonly>";
	z.innerHTML = "<input class='btn btn-info btn-xs' type='button' id='ins" + prodid + "' value='insert' />";			


									$("#ins"+prodid).on('click', function() {
										 InsertDetSerial(prodid,prdnme,cmainunit,nident);
									});					
									
}

function FindSerials(itmcode,itmunit,itmqty,itmfctr,itmuntmain,mainident){
				$.ajax({
					url : "th_serialslist.php",
					data: { itm: itmcode, cuom: itmunit, qty: itmqty, factr: itmfctr, mainuom: itmuntmain },
					type: "POST",
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);
							var disqty = document.getElementById("txtnqty"+itmcode+mainident).value;
             $.each(data,function(index,item){

								InsertToSerials(item.citemno, item.cserial, item.cunit, item.nqty, item.nlocation, item.locadesc, item.dexpired, item.nrefidentity, item.ctranno, mainident);
								disqty = parseFloat(disqty) + parseFloat(item.nqty);
											   
					   });

						document.getElementById("txtnqty"+itmcode+mainident).value = disqty;
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});
}

function InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident,refe,mainident){

	$("<tr>").append(
		$("<td width=\"120px\" style=\"padding:1px\">").html("<input type='hidden' value='"+itmcode+"' name=\"sertabitmcode\" id=\"sertabitmcode\"><input type='hidden' value='"+mainident+"' name=\"sertabident\" id=\"sertabident\"><input type='hidden' value='"+nident+"' name=\"sertabreferid\" id=\"sertabreferid\"><input type='hidden' value='"+refe+"' name=\"sertabrefer\" id=\"sertabrefer\">"+itmcode),
		$("<td>").html("<input type='hidden' value='"+serials+"' name=\"sertabserial\" id=\"sertabserial\">"+serials), 
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+uoms+"' name=\"sertabuom\" id=\"sertabuom\">"+uoms),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+qtys+"' name=\"sertabqty\" id=\"sertabqty\">"+qtys),
		$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+locas+"' name=\"sertablocas\" id=\"sertablocas\">"+locasdesc),
		$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+expz+"' name=\"sertabesp\" id=\"sertabesp\">"+expz),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' />")
	).appendTo("#MyTable2 tbody");

		
}

function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtrrref").value==""){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Reference SO Required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtrrref").focus();
		return false;

		
		ISOK = "NO";
	}
	
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(lastRow == 0){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;NO Serials found!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		return false;
		ISOK = "NO";
	}
	
	if(ISOK == "YES"){
	var trancode = "";
	var isDone = "True";


		//Saving the header
		var rrno = $("#txtrrref").val();
		var crem = $("#txtremarks").val();
		var ddaterec = $("#date_received").val();
				
		$.ajax ({
			url: "InvPick_save.php",
			data: { rr: rrno, crem: crem, ddate: ddaterec },
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW TRANSACTION: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			}
		});
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
				var itmcd = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var qtycd = $(this).find('input[name="txtnqty"]').val();
				var indxcvb = $(this).find('input[type="hidden"][name="txtnrefident"]').val();

				if(index!=0){
						$.ajax ({
							url: "InvPick_SaveDetItems.php",
							data: { trancode: trancode, itmcd: itmcd, indx: indxcvb, qtycd: qtycd },
							async: false,
							success: function( data ) {
								if(data.trim()=="False"){
									isDone = "False";
								}
							}
						});
				}

			});

			$("#MyTable2 > tbody > tr").each(function(index) {	


				var xcref = $("#txtrrref").val();  
				var crefidnt = $(this).find('input[type="hidden"][name="sertabident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="sertabitmcode"]').val();
				var cuom = $(this).find('input[type="hidden"][name="sertabuom"]').val();
				var nqty = $(this).find('input[type="hidden"][name="sertabqty"]').val();
				var dneed = $(this).find('input[type="hidden"][name="sertabesp"]').val();
				var clocas = $(this).find('input[type="hidden"][name="sertablocas"]').val();
				var seiraln = $(this).find('input[type="hidden"][name="sertabserial"]').val();
				var putref = $(this).find('input[type="hidden"][name="sertabrefer"]').val();
				var putrefident = $(this).find('input[type="hidden"][name="sertabreferid"]').val();

				$.ajax ({
					url: "InvPick_SaveDet.php",
					data: { trancode: trancode, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, clocas:clocas, xcref:xcref, crefidnt:crefidnt, seiraln:seiraln, putref:putref, putrefident:putrefident },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});
			
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							$("#txtctranno").val(trancode);
							$("#frmedit").submit();
			
					}, 1000); // milliseconds = 1second

				
			}

		}
		else{
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem saving your transaction...<br><br>" + trancode);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}



	}

}
</script>
