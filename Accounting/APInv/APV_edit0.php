<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "APV_edit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];
$ctranno = $_REQUEST['txtctranno'];

$sqlhead = mysqli_query($con,"select a.ctranno, a.ccode, a.cpaymentfor, a.cpayee, DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.ngross, a.cpreparedby, a.lcancelled, a.lapproved, a.lprintposted, b.cname from apv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode = '$company' and a.ctranno = '$ctranno'");

//echo "select a.ctranno, a.ccode, a.cpaymentfor, a.cpayee, DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.ngross, a.cpreparedby, a.lcancelled, a.lapproved, a.lprintposted, b.cname from apv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode = '$company' and a.ctranno = '$ctranno'";

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtctranno').focus(); disabled();">
<?php

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Payee = $row['cpayee'];
		$Remarks = $row['cpaymentfor'];
		$DateAPV = $row['dapvdate'];
		//$cChkNo = $row['cchkno'];
		$nGross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrinted = $row['lprintposted'];
	}

?>

<form action="APV_editsave.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
    	<legend>AP Invoice Details</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>APV No.:</tH>
    <td colspan="2" style="padding:2px;"><div class="col-xs-4 nopadding">
      <input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter APV No..." required  value="<?php echo $ctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
      </div>
      <input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ctranno;?>">
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
      
    </td>
    <td style="padding:2px;">
          <div id="salesstat">
        <?php
	if($lCancelled==1){
		echo "<font color='#FF0000'><b>CANCELLED</b></font>";
	}
	
	if($lPosted==1){
		echo "<font color='#FF0000'><b>POSTED</b></font>";
	}
	?>
        </div> 

    
    </td>
  </tr>
  <tr>
    <tH width="150">SUPPLIER:</tH>
    <td style="padding:2px;" width="500">
     <div class="col-xs-12 nopadding">
    	<div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required autocomplete="off" value="<?php echo $CustName;?>">
		</div> 
		<div class="col-xs-6 nopadwleft">
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $CustCode;?>">
		</div>
    </div>
                   
            <input type="hidden" id="txtcustchkr" name="txtcustchkr">
            <input type="hidden" id="seltype" name="seltype">
            
    </td>
    <tH width="150"><span style="padding:2px">PAYEE:</span></tH>
    <td style="padding:2px;"><div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" width="20px" tabindex="1" required value="<?php echo $Payee; ?>">
    </div></td>
  </tr>
  <tr>
    <tH width="150" rowspan="2" valign="top">PARTICULARS:</tH>
    <td rowspan="2" valign="top" style="padding:2px"><div class="col-xs-10 nopadding">
       <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $Remarks; ?></textarea>
    </div></td>
    <tH width="150" style="padding:2px">AP DATE:</tH>
    <td style="padding:2px"><div class="col-xs-5">
      <input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $DateAPV; ?>" />
   </div></td>
  </tr>
  <tr>
    <tH style="padding:2px">TOTAL AMOUNT :</tH>
    <td style="padding:2px">
    <div class="col-xs-8">
      <input type="text" class="form-control input-sm"id="txtnGross" name="txtnGross" tabindex="1" required value="<?php echo $nGross;?>" style="font-weight:bold; color:#F00; text-align:right" readonly>
    </div>
    
   </td>
  </tr>
      </table>
<br>

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
                            <th style="border-bottom:1px solid #999">Debit Acct</th>
                            <th style="border-bottom:1px solid #999">Amount</th>
                            <th style="border-bottom:1px solid #999">Remarks</th>
                            <th style="border-bottom:1px solid #999">&nbsp;</th>
                        </tr>
                        <tbody class="tbody">
 
                          <?php 
							$sqlbody = mysqli_query($con,"select a.crefno, a.crefinv, a.cdescription, a.namount, a.cremarks, a.cacctno, B.cacctdesc from apv_d a left join accounts B on a.compcode=B.compcode and a.cacctno=B.cacctno where a.compcode = '$company' and a.ctranno = '$ctranno' order by a.nidentity");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
						

						 ?>
                         	<tr>
                            	<td style="padding:1px" width="150px">
                                <input type='text' name="txtrefno<?php echo $cntr;?>" id="txtrefno<?php echo $cntr;?>" class="form-control input-sm" value='<?php echo $rowbody['crefno'];?>' style="text-transform:uppercase" required placeholder="Search RR No..." readonly>
                                </td>
                                <td style="padding:1px" width="150px">
                                <input type='text' name="txtsuppSI<?php echo $cntr;?>" id="txtsuppSI<?php echo $cntr;?>" class="form-control input-sm" value='<?php echo $rowbody['crefinv'];?>'>
                                </td>
                                <td style="padding:1px">
                                <input type='text' name="txtrrdesc<?php echo $cntr;?>" id="txtrrdesc<?php echo $cntr;?>" class="form-control input-sm" value='<?php echo $rowbody['cdescription'];?>'>
                                </td>
                                <td style="padding:1px">
                                <input type='hidden' name="txtdracctid<?php echo $cntr;?>" id="txtdracctid<?php echo $cntr;?>" value="<?php echo $rowbody['cacctno'];?>"> <input type='text' name="txtdracct<?php echo $cntr;?>" id="txtdracct<?php echo $cntr;?>" value="<?php echo $rowbody['cacctdesc'];?>" class="form-control input-sm" readonly>
                                </td>
                                <td style="padding:1px" width="150px">
                                <input type='text' name="txtnamount<?php echo $cntr;?>" id="txtnamount<?php echo $cntr;?>" class="numeric form-control input-sm" required value='<?php echo $rowbody['namount'];?>' style="text-align:right" autocomplete="off">
                                </td>
                                <td style="padding:1px">
                                <input type='text' name="txtremarks<?php echo $cntr;?>" id="txtremarks<?php echo $cntr;?>" class="form-control input-sm" value='<?php echo $rowbody['cremarks'];?>'>
                                </td>
                                <td style="padding:1px" width="50px">
                                <input class='delete btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick="deleteRow1(this);"/>
                                </td>
                            	
                            </tr>
                         <?php
							}
						}
                         
						 ?>

                  		</tbody>
                        
                </table>
    		<input type="hidden" name="hdnRRCnt" id="hdnRRCnt"> 
            </div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">


<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='APV.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='APV_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnqo">RR<br> (Insert)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
<!--
    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $ctranno;?>');" id="btnPrint" name="btnPrint">
Print<br>(CTRL+P)
    </button>
 -->   
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(CTRL+E)    </button>
    
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(CTRL+S)    </button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>

<?php
}
else{
?>
<form action="APV_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>AP Invoice</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">APV NO:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>AP No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>

<?php
}
?>


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
            

<script type="text/javascript">
	$(document).keydown(function(e) {	 

	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='APV_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
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
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?php echo $ctranno;?>');
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
			$("#btnMain").click();
		}
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
		e.preventDefault();
		$('#txtprodnme').focus();
      }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			openinv();
		}
	  }

	});
	
	$(document).ready(function(e) {
        						$("input.numeric").numeric({decimalPlaces: 4});
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
														
								$("input.numeric").on("keyup", function (e) {
									$("#txtnGross").val($(this).val());
								});

    });



$(function(){
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


function addrrdet(rrno,amt,acctno,ctitle){

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
	var dr=a.insertCell(3);
		dr.style.padding = "1px";
	var x=a.insertCell(4);
		x.style.width = "150px";
		x.style.padding = "1px";
	var y=a.insertCell(5);
		y.style.padding = "1px";
	var z=a.insertCell(6);
		z.style.width = "50px";
		z.style.padding = "1px";
		
	u.innerHTML = "<input type='text' name=\"txtrefno"+lastRow+"\" id=\"txtrefno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search RR No...\" style=\"text-transform:uppercase\" required value=\""+rrno+"\">";
	v.innerHTML = "<input type='text' name=\"txtsuppSI"+lastRow+"\" id=\"txtsuppSI"+lastRow+"\" class=\"form-control input-sm\">";
	w.innerHTML = "<input type='text' name=\"txtrrdesc"+lastRow+"\" id=\"txtrrdesc"+lastRow+"\" class=\"form-control input-sm\">";
	dr.innerHTML = "<input type='hidden' name=\"txtdracctid"+lastRow+"\" id=\"txtdracctid"+lastRow+"\" value=\""+acctno+"\"> <input type='text' name=\"txtdracct"+lastRow+"\" id=\"txtdracct"+lastRow+"\" value=\""+ctitle+"\" class=\"form-control input-sm\" readonly>";
	x.innerHTML = "<input type='text' name=\"txtnamount"+lastRow+"\" id=\"txtnamount"+lastRow+"\" class=\"numeric form-control input-sm\" value=\""+amt+"\" style=\"text-align:right\">";
	y.innerHTML = "<input type='text' name=\"txtremarks"+lastRow+"\" id=\"txtremarks"+lastRow+"\" class=\"form-control input-sm\">";
	z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' class='delete' value='delete' onClick=\"deleteRow1(this);\"/>";

								$("input.numeric").numeric({decimalPlaces: 4});
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
														
								$("input.numeric").on("keyup", function (e) {
									$("#txtnGross").val($(this).val());
								});
	
					
}
else{
	alert("Supplier Required!");
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
			

			//get salesno na selected na
			var y;
			var salesnos = "";
			var cnt = 0;
			var rc = $('#MyTable tr').length;
			for(y=1;y<=rc-1;y++){ 
			  cnt = cnt + 1;
			  if(cnt>1){
				  salesnos = salesnos + ",";
			  }
			  			  
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
								$("<td>").html("<input type='checkbox' value='"+item.crrno+":"+item.ngross+":"+item.cacctno+":"+item.ctitle+"' name='chkSales[]'>"),
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
		var acctno=valuesz[2];
		var titlez=valuesz[3];
		
		 addrrdet(rrno,amt,acctno,titlez);
		 
		 totGross = parseFloat(totGross) + parseFloat(amt) ;

   });


	$("#txtnGross").val(totGross.toFixed(2));

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
			var tempacctid = document.getElementById('txtdracctid' + z);
			var tempacctdsc = document.getElementById('txtdracct' + z);
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
			tempacctid.id = "txtdracctid" + x;
			tempacctid.name = "txtdracctid" + x;
			tempacctdsc.id = "txtdracct" + x;
			tempacctdsc.name = "txtdracct" + x;
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

	
}

function chkform(){
	var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRowRR = tbl1.length-1;

	if(lastRowRR==0 && lastRowACC==0){  
		alert("Transaction has NO Details!");
		return false;
	}
	else{
		if(document.getElementById("txtnGross").value==0 || document.getElementById("txtnGross").value==""){
			//alert();
			$("#AlertMsg").html("");
								
			$("#AlertMsg").html("No amount detected. Please check your details!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
		}
		else{
			document.getElementById("hdnRRCnt").value = lastRowRR;			
			$("#frmpos").submit();
			return true;
		  }
		}

}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{

		$("#frmpos :input").attr("disabled", false);
		
			
			$("#txtctranno").attr("readonly", true);
			$("#txtctranno").val($("#hdnorigNo").val());
			
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);		
	}

}


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "APV_edit.php";
		document.getElementById(frm).submit();
	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{
		  var url = "APV_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		$("#PrintModal").modal('show');
		

	}
}


</script>

</body>
</html>