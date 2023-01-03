<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvCnt_new.php";

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

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
    
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:20px; padding-right:20px; padding-top:10px">


<div class="col-xs-12">
	    
    <div class="col-xs-5 nopadding">
    	<b><font size="+2">
        <div class="col-xs-6 nopadwright2x">
        	<b>No. of Product: </b>
        </div>
        <div class="col-xs-6 nopadding" id="divLblNo">
        	
        </div>
        
        </font></b>
    </div>

    <div class="col-xs-4 nopadding">
    	<b><font size="+2">
        <div class="col-xs-6">
        	Total Qty:
        </div>
        <div class="col-xs-6 nopadding" id="divLblQTy">
        	
        </div>

        </font></b>
        
    </div>

</div>

<div class="col-xs-12 nopadwtop2x" id="divStatMsg">
	
</div>

<div class="col-xs-12 nopadwtop2x">
	
    <input type="text" class="form-control input-lg" name="txtscan" id="txtscan" value="" placeholder="Search Item Name...">

</div>


<form id="frmCount" name="frmCount" method="post" action="">
	<!--<input type="hidden" name="hdnmonth" id="hdnmonth" value="<?php// echo $_REQUEST["month"];?>">
    <input type="hidden" name="hdnyear" id="hdnyear" value="<?php// echo $_REQUEST["year"];?>">-->
 
                       
                  <table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
                   <thead>
                    <tr>
                      <th width="50">&nbsp;</th>
                      <th width="150">Item Code</th>
                      <th>Item Description</th>
                      <th width="70">Unit</th>
                      <th width="100">Qty</th>
						<th width="100">Unit Cost</th>
                      <th width="200">Barcode</th>
                      <th width="200">Serial</th>
                      <th width="180">Loc</th>
                      <th width="100">Exprd</th>
                      <th width="50">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>


<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
Upload<br>(XLS)</button>

    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button></td>

    </tr>
</table>

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


<form name="frmEdit" id="frmEdit" method="post" action="InvCnt_Edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		

</body>

</html>

<script type="text/javascript">

$("#txtscan").focus();

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){
			e.preventDefault();
			return chkform();
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
		$(".datepick").datetimepicker({
        	format: 'MM/DD/YYYY',
			useCurrent: false,
			minDate: moment(),
			defaultDate: moment(),
		});
	
	$('#txtscan').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "th_product.php",
				dataType: "json",
				data: { query: $("#txtscan").val(), styp: "Goods" },
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 900px"><span >'+item.desc+'</span</div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 	
		
			$('.datepick').each(function(){
				$(this).data('DateTimePicker').destroy();
			});
					
			xy = item.id;
			exist = "NO";		
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
					
					vartot = 1 + parseFloat(varx);
					
					$(this).find('input[name="txtnqty"]').val(vartot.toFixed(4));
					
					$("#divStatMsg").html("<font size=\"4px\"><b>Item No. "+(index+1)+": "+myscanDesc+" ( Qty: "+varx+" + 1 )</b></font>");
					
					
				}
	
			});
			varlocs = "";
			if(exist == "NO"){
					//get locations
				
				$.ajax({
					url : "th_locations.php",
					async: false,
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);

						 $.each(data,function(index,item){
							 varlocs = varlocs + "<option value=\""+item.nid+"\">"+item.cdesc+"</option>";
						 });
			 
					}
				});
				//alert(varlocs);
					dneeded = moment(new Date()).format("MM/DD/YYYY"); 
					
					var trRow0 = "<td><div id=\"rowItmNum\"></div></td>";
					var trRow1 = "<td><input type='hidden' value='"+item.id+"' name='txtcpartno'> <input type='hidden' value='"+item.cscan+"' name='txtcscancode'>"+item.cscan+"</td>";
					var trRow2 = "<td><input type='hidden' value='"+item.desc+"' name='txtcdesc'>"+item.desc+"</td>";
					var trRow3 = "<td><input type='hidden' value='"+item.cunit+"' name='txtcunit'>"+item.cunit+"</td>";
					var trRow4 = "<td><input type='text' value='1' name='txtnqty' class=\"numeric form-control input-xs text-right\" autocomplete=\"false\">"+"</td>";
					var trRow4b = "<td><input type='text' name='txtucost' class=\"numeric form-control input-xs text-right\" autocomplete=\"false\" value='0.0000' >"+"</td>";
					var trRow5 = "<td><input type='text' value='' name='txtbcode' class=\"form-control input-xs\" style=\"align: right\" autocomplete=\"false\">"+"</td>";
					var trRow6 = "<td><input type='text' value='' name='txtserial' class=\"form-control input-xs\" style=\"align: right\" autocomplete=\"false\">"+"</td>";
					var trRow7 = "<td><select name='sellocs' class=\"form-control input-xs\">"+varlocs+"</select></td>";
					var trRow8 = "<td><div style=\"position: relative\"><input type=\"text\" name=\"txtexpdte\" class=\"datepick form-control input-xs\"></div></td>";
					var trRow9= "<td><input class='btn btn-danger btn-xs' type='button' id='"+item.cscan+"_delete' value='delete' onClick='deleteRow(this);' /></td>";

					$("<tr>"+trRow0+trRow1+trRow2+trRow3+trRow4+trRow4b+trRow5+trRow6+trRow7+trRow8+trRow9+"</tr>").prependTo("#MyTbl > tbody");		
			
					$("input.numeric").numeric(
						{negative: false}
					);

					$("input.numeric").on("click", function () {
						$(this).select();
					});
					
			}
			
			updateStat();		
			$('#txtscan').val("");
			
			$('.datepick').each(function(){
				$(this).datetimepicker({
					format: 'MM/DD/YYYY',
					useCurrent: false,
					minDate: moment(),
					defaultDate: moment(),
				});	
			});
					
		}
	
	});
});
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
		
			var cmonth = $("#hdnmonth").val();
			var cyear = $("#hdnyear").val();
	
		//Saving Header
					$.ajax ({
					url: "InvCnt_SaveHdr.php",
					data: { mo: cmonth, yr: cyear },
					async: false,
					beforeSend: function(){
						$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW COUNT: </b> Please wait a moment...");
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

				var ucost = $(this).find('input[name="txtucost"]').val(); 
				var nqty = $(this).find('input[name="txtnqty"]').val(); 
				//var cscan = $(this).find('input[type="hidden"][name="txtcscancode"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtcpartno"]').val();
				var cunit = $(this).find('input[type="hidden"][name="txtcunit"]').val();    
				var bcode = $(this).find('input[type="text"][name="txtbcode"]').val();
				var cserial = $(this).find('input[type="text"][name="txtserial"]').val();
				var selloc = $(this).find('select[name="sellocs"]').val();
				var dexpdte = $(this).find('input[type="text"][name="txtexpdte"]').val();

				alert("InvCnt_SaveDet.php?trancode="+trancode+"&indx="+index+"&citmno="+citmno+"&cunit="+cunit+"&nqty="+nqty+"&ucost="+ucost+"&bcode="+bcode+"& cserial="+cserial+"&selloc="+selloc+"&dexpdte="+dexpdte);
				
				$.ajax ({
					url: "InvCnt_SaveDet.php",
					data: { trancode: trancode, indx: index, citmno: citmno, cunit: cunit, nqty:nqty, ucost:ucost, bcode:bcode, cserial:cserial, selloc:selloc, dexpdte:dexpdte },
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
				$("#AlertMsg").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
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

</script>
