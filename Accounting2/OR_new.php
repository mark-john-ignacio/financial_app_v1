<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR_new.php";

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

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../include/jquery-maskmoney.js" type="text/javascript"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px; height:700px" onLoad="document.getElementById('txtcust').focus();">

<form action="OR_newsave.php" name="frmOR" id="frmOR" method="post">
	<fieldset>
    	<legend>Receive Payment</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="210">
    	<select id="selDepAcct" name="selDepAcct" class="form-control input-sm selectpicker" tabindex="3">
        <option value="Deposit">Deposit To Account</option>
          <option value="Group">Group w/ Undeposited Funds</option>
        </select>
    
    </tH>
    <td style="padding:2px;" width="500">
    <?php
    $sqlchk = mysqli_query($con,"Select a.cvalue, b.cacctdesc, IFNULL(b.nbalance,0) as nbalance From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='ORDEBIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
			$nDebitDesc = $row['cacctdesc'];
			$nBalance = $row['nbalance'];
		}
	}else{
		$nDebitDef = "";
		$nDebitDesc =  "";
		$nBalance = 0.000;
	}
	?>
  <div class="col-xs-12">
    <div class="col-xs-8">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
    </div> 

        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
   </div>     
    </td>
    <tH width="150">Balance:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8">
    <input type="text" id="txtacctbal" name="txtacctbal" class="form-control input-sm" readonly value="<?php echo $nBalance;?>">
    </div>
    </td>
  </tr>
  <tr>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH width="100" valign="top">PAYOR:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-12">
        <div class="col-xs-8">
        	<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Customer Name..." required autocomplete="off">
		</div> 
		<div class="col-xs-4">
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
        </div>
    </div>        
    </td>
    <tH width="150" style="padding:2px">Payment Type:</tH>
    <td style="padding:2px">
    <div class="col-xs-8">

     <select id="selpaytype" name="selpaytype" class="form-control input-sm selectpicker">
       <option value="None">No Reference</option>
       <option value="Sales">Sales</option>
       <option value="Shares">Shares</option>
       <option value="Savings">Savings</option>
       <option value="Loan">Coop Loan</option>
       <option value="Fee">Membership Fee</option>
     </select>

    </div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">PAYMENT METHOD:</tH>
    <td valign="top" style="padding:2px">
    
    
    <div class="col-xs-12">
     <div class="col-xs-6">
      <select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
          <option value="Cash">Cash</option>
          <option value="Cheque">Cheque</option>
        </select>
     </div>
     
     <div class="col-xs-4">
       <button type="button" class="btn btn-primary btn-sm" tabindex="6" style="width:100%" name="btnDet" id="btnDet">Details</button>
   </div>
    </div>
    
    
    </td>
    <tH style="padding:2px">OR NO.:</tH>
    <td style="padding:2px">
	<?php
	$ornostat = "";
    	$sqlchk = mysqli_query($con,"select * from receipt where compcode='$company' Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cORNOm = $row['cornumber'];
			$ornostat = "readonly";
			
			$cORNOm = $cORNOm + 1;
		}
	}else{
			$cORNOm = "";
			$ornostat = "";
	}
	?>
      <div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtORNo" name="txtORNo" width="20px" required value="<?php echo $cORNOm;?>" <?php echo $ornostat; ?>>
    </div></td>
  </tr>
  <tr>
    <tH width="100" rowspan="2" valign="top">MEMO:</tH>
    <td rowspan="2" valign="top" style="padding:2px"><div class="col-xs-12">
      <div class="col-xs-10">
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"></textarea>
      </div>
    </div></td>
    <th valign="top" style="padding:2px">DATE:</th>
    <td valign="top" style="padding:2px"><div class="col-xs-8"> <!--<a href="javascript:NewCal('date_delivery','mmddyyyy')">-->
    
    <?php
	//get last date
	$ornostat = "";
    	$sqlchk = mysqli_query($con,"select * from receipt where compcode='$company' Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$dORLastDate = date("m/d/Y", strtotime($row['dcutdate']));
		}
	}else{
			$dORLastDate = date("m/d/Y");
	}
	?>


      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dORLastDate; ?>"/>
    <!--</a>--></div></td>
    </tr>
  <tr>
    <th valign="top" style="padding:2px">AMOUNT RECEIVED:</th>
    <td valign="top" style="padding:2px"><div class="col-xs-8">
      <input type="text" id="txtnGross" name="txtnGross" class="form-control" value="0.00">
    </div></td>
  </tr>
      </table>
<br>

<div id='divSales' style="display:none">
<button type="button" class="btn btn-xs btn-info" onClick="getInvs();">

	<!--<button type="button" class="btn btn-xs btn-primary" onClick="popup('add_asset.asp?types=asset');" name="openBtn" id="openBtn">-->
  	<table border="0">
    <tr>
      <td valign="top"><img src="../images/Find.png" border="0" height="20" width="20" />&nbsp;</td>
      <td>Find Invoice</td>
    </tr>
  	</table>
    </button>

    <br><br>
	  <div id="tableContainer" class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 200px;
                        text-align: left;
                        overflow: auto">
<table width="100%" border="0" cellpadding="3" id="MyTable">
<thead>
  <tr>
    <th scope="col" width="15%">Invoice No</th>
    <!--<th scope="col">Status</th>-->
    <th scope="col">Date</th>
    <th scope="col" >Amount</th>
    <th scope="col" width="15%">Discount</th>
    <th scope="col" width="15%">Total Due</th>
    <th scope="col" width="15%">Amount Applied</th>
     <th scope="col">&nbsp;</th>
  </tr>
</thead>
<tbody>

</tbody>
</table>
<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
</div>

</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
    
   <button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">Save<br> (F2)</button>
    
</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>



<!-- Bootstrap modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">Invoice List</h3>
            </div>
            
            <div class="modal-body">
            
            	
                  <table name='MyORTbl' id='MyORTbl' class="table table-scroll table-striped">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                      <th>Invoice No</th>
                      <th>Sales Date</th>
                      <th>Gross</th>
                      <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>
                
            
			</div>
			
            <div class="modal-footer">
                
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<!--CASH DETAILS DENOMINATIONS -->
<div class="modal fade" id="CashModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CASH DENOMINATION</h3>
            </div>
            <div class="modal-body">
            
                  <table width="100%" border="0" class="table table-scroll table-condensed">
                  <thead>
                      <tr>
                        <td align="center"><b>Denomination</b></td>
                        <td align="center"><b>Pieces</b></td>
                        <td align="center"><b>Amount</b></td>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td align="center">1000</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom1000' id='txtDenom1000' /></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1000' id='txtAmt1000' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">500</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom500' id='txtDenom500'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt500' id='txtAmt500' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">200</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom200' id='txtDenom200'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt200' id='txtAmt200' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">100</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom100' id='txtDenom100'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt100' id='txtAmt100' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">50</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom50' id='txtDenom50'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt50' id='txtAmt50' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">20</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom20' id='txtDenom20'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt20' id='txtAmt20' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">10</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom10' id='txtDenom10'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt10' id='txtAmt10' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">5</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom5' id='txtDenom5'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt5' id='txtAmt5' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">1</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom1' id='txtDenom1'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1' id='txtAmt1' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.25</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom025' id='txtDenom025'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt025' id='txtAmt025' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.10</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom010' id='txtDenom010'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt010' id='txtAmt010' readonly/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.05</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom005' id='txtDenom005'/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt005' id='txtAmt005' readonly/></div></td>
                      </tr>
                    </tbody>
                    </table>
            
            </div>
            <div class="modal-footer">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<div class="modal fade" id="ChequeModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CHEQUE DETAILS</h3>
            </div>
            <div class="modal-body">
            
                  <table width="100%" border="0" class="table table-condensed">
                      <tr>
                        <td><b>Bank Name</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtBankName' id='txtBankName' placeholder="Input Bank Name"/></div></td>
                      </tr>
                      <tr>
                        <td><b>Cheque Date</b></td>
                        <td>
                        <div class='col-sm-12'>
                            <input type='text' class="form-control input-sm" id='datetimepicker4' placeholder="Pick a Date" name="txtChekDate" id="txtChekDate"/>

                        </div>
                        </td>
                      </tr>
                      <tr>
                        <td><b>Cheque Number</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' placeholder="Input Cheque Number" /></div></td>
                      </tr>
                       <tr>
                        <td><b>Cheque Amount</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckAmt' id='txtCheckAmt' placeholder="Input Cheque Amount" /></div></td>
                      </tr>
                   </table>
            
            </div>
            <div class="modal-footer">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


</form>


<script type="text/javascript">
$(function() {              
           // Bootstrap DateTimePicker v4
           $('#datetimepicker4').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
	
	        $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
	   
		   $('#txtCheckAmt').maskMoney({prefix:'\u20B1 '});
});
		
$('.allownumericwithoutdecimal').keyup(function (e) {
        if (e.which == 39) { // right arrow
          $(this).closest('td').next().find('input').focus();
 
        } else if (e.which == 37) { // left arrow
          $(this).closest('td').prev().find('input').focus();
 
        } else if (e.which == 40) { // down arrow
          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
 
        } else if (e.which == 38) { // up arrow
          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
        }
		else{
			var str = $(this).attr('name');
			var res = str.substring(0, 8);
			var valz = str.substring(8);
			
			if(valz=="025"){
				var val2=0.25;
			}
			else if(valz=="010"){
				var val2=0.10;
			}
			else if(valz=="005"){
				var val2=0.05;
			}
			else{
				var val2 = valz;
			}
			
			var value = $(this).val();
			if(res=="txtDenom"){
				
				var x = parseFloat(val2) * parseFloat(value);	
				//alert("#txtAmt"+valz+" = "+x);	
				if(value!=""){		
					$("#txtAmt"+valz).val(x.toFixed(2));
				}
				else{
					$("#txtAmt"+valz).val("");
				}
				
			}

		}
});
	
$('#txtcacct').typeahead({

    source: function (query, process) {
        return $.getJSON(
            'th_accounts.php',
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
			$('#txtacctbal').val(map[item].balance);
			return item;
	}

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
        return '<span class="dropdown-item-extra">' + item.typ + " " + item.id + '</span><br>' + item.value;
    },
	highlighter: Object,
	afterSelect: function(item) { 
	 	$('#txtcust').val(item.value).change(); 
		$("#txtcustid").val(item.id);
	}
});

$("#txtcust").on('blur', function() {
	if($('#txtcustid').val() != "" && $('#txtcustid').val() != ""){
		$('#txtcust').attr('readonly', true);
	}
}); 


$("#selpayment").on('change', function() {
	$('#txtnGross').val('0.00');
	
	 if ($(this).val() == "Cheque"){
		$('#txtnGross').attr('readonly', true);
	 }
	 else{
		$('#txtnGross').attr('readonly', false);
	 }
});


$("#selpaytype").on('change', function() {
	
	 if ($(this).val() == "Sales"){
		$('#txtnGross').val('0.00');
		$('#txtnGross').attr('readonly', true);
	 }
	 else{
		$('#txtnGross').attr('readonly', false);
	 }
});

 
$("#txtCheckAmt").on('keyup', function() {
	var x = $('#txtCheckAmt').maskMoney('unmasked')[0];
		$('#txtnGross').val(x);
});



$("#selpaytype").change(function() {
var rc = $('#MyTable tr').length;


  if ($(this).val() != "Sales"){
	 if(rc > 1) {
		 var j = confirm("Changing Payment Type will clear the Sales Details.\n Are you sure you want to change type?");
		 if(j==true){
			 $('#MyTable tbody').empty();
		 }
	 }

	  $("#divSales").hide();

  }
  else{
  	  $("#divSales").show();
  }
 // alert($(this).val());
});


$("#btnDet").on('click', function() {
	if($('#selpayment').val() == "Cash"){
		$('#CashModal').modal('show');
	}
	if($('#selpayment').val() == "Cheque"){
		$('#ChequeModal').modal('show');
	}
});

$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
     $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
});

 $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
           $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which >= 48 && event.which <= 57) || event.which == 8 || event.which == 46) {
				return true;
			}
			else {
				event.preventDefault();
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


function computeAmt(str,valz){

	var numberPattern = /\d+/g;
	var r = str.match(numberPattern);
		
	var rwcnt = parseInt(r);
	
	var amtz = $("#txtAmt"+rwcnt).val();

									
	if(valz!=""){							
		var thisvalz = parseFloat(valz);
											
		var Totdicnt = (thisvalz/100) * amtz;
		var TotDue = amtz - Totdicnt;
											
		$("#txtDue"+rwcnt).val(TotDue.toFixed(4));
	}
	else{
		$("#txtDue"+rwcnt).val(amtz);
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
			var tempsalesno = document.getElementById('txtcSalesNo' + z);
			var tempamt = document.getElementById('txtAmt' + z);
			var tempdisc= document.getElementById('txtDiscount' + z);
			var tempdue= document.getElementById('txtDue' + z);
			var tempapplies = document.getElementById('txtApplied' + z);
			
			var x = z-1;
			tempsalesno.id = "txtcSalesNo" + x;
			tempsalesno.name = "txtcSalesNo" + x;
			tempamt.id = "txtAmt" + x;
			tempamt.name = "txtAmt" + x;
			tempdisc.id = "txtDiscount" + x;
			tempdisc.name = "txtDiscount" + x;
			tempdue.id = "txtDue" + x;
			tempdue.name = "txtDue" + x;
			tempapplies.id = "txtApplied" + x;
			tempapplies.name = "txtApplied" + x;
			
			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

		}

computeGross();

}

function computeGross(){
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var x = 0;
	var tot = 0;
	for (z=1; z<=lastRow-1; z++){
		x = document.getElementById('txtApplied' + z).value;
		if(x!=0 && x!=""){
		var tot = parseFloat(x) + parseFloat(tot);	
		}
	}
	
	document.getElementById('txtnGross').value = tot.toFixed(2);

}

function getInvs(){
	
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
		}
		else{
			
			//clear table body if may laman
			

			$('#MyORTbl tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";
			var rc = $('#MyTable tr').length;
			for(y=1;y<=rc-1;y++){ 
			  if(y>1){
				  salesnos = salesnos + ",";
			  }
				salesnos = salesnos + $('#txtcSalesNo'+y).val();
			}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#invheader').html("Invoice List: " + $('#txtcust').val())
			
			$.ajax({
                    url: 'th_orlist.php',
					data: 'x='+x+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                        console.log(data);
                       $.each(data,function(index,item){
                        $("<tr>").append(
						$("<td>").html("<input type='checkbox' value='"+item.csalesno+"' name='chkSales[]'>"),
                        $("<td>").text(item.csalesno),
                        $("<td>").text(item.dcutdate),
						$("<td>").text(item.ngross)
                        ).appendTo("#MyORTbl tbody");

                       });
                    },
                    error: function (err) {
                        alert(err);
                    }
                });
			
			$('#myModal').modal('show');
			
		}


}

function save(){

	var i = 0;
	var rowCount = $('#MyTable tr').length;
   $("input[name='chkSales[]']:checked").each( function () {
	   i += 1;
      // alert( $(this).val() );
	  			
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_getsalesdetails.php?id=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{				
					
					   console.log(data);
                       $.each(data,function(index,item){
						   $("<tr>").append(
							$("<td>").html("<div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtcSalesNo"+rowCount+"' id='txtcSalesNo"+rowCount+"' value='"+item.csalesno+"' readonly /></div>"),
							//$("<td>").text(""),
							$("<td align='center'>").text(item.dcutdate),
							$("<td align='right'>").html("<div class='col-xs-12'><div class='col-xs-6'><input type='hidden' name='txtAmt"+rowCount+"' id='txtAmt"+rowCount+"' value='"+item.ngross+"' />"+item.ngross+"</div></div>"),
							$("<td style='padding: 1px;'>").html("<div class='col-xs-12'><input type='text' class='InvDet form-control input-sm' name='txtDiscount"+rowCount+"' id='txtDiscount"+rowCount+"' placeholder='value in %' onKeyup='computeAmt(this.name,this.value);' /></div>"),
							$("<td style='padding: 1px;'>").html("<div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtDue"+rowCount+"' id='txtDue"+rowCount+"' readonly value='"+item.ngross+"' /></div>"),
							$("<td style='padding: 1px;'>").html("<div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtApplied"+rowCount+"' id='txtApplied"+rowCount+"' onKeyup='computeGross();' /></div>"),
							$("<td align='center'>").html("<input class='btn btn-danger btn-xs' type='button' id='row_"+rowCount+"_delete' value='delete' onClick='deleteRow(this);' />")
						   ).appendTo("#MyTable tbody");
						   
						   
						   $("#txtDiscount"+rowCount).on("keypress keyup blur",function (event) {    
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });
							 
							 
							 
							 $("#txtDue"+rowCount).on("keypress keyup blur",function (event) {    
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });

							$("#txtApplied"+rowCount).on("keypress keyup blur",function (event) { 
  
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });
							 
							 $("#txtApplied"+rowCount).maskMoney({precision:4});
							 $("#txtDiscount"+rowCount).maskMoney({precision:0,thousands:'',allowEmpty:true});
					   
						   
						});
					rowCount = rowCount + 1;

					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

	   
	   
	   
   });
   
   if(i==0){
	   alert("No Invoice is selected!")
   }
   
   $('#myModal').modal('hide');
   
}


$('#frmOR').submit(function() {
	var subz = "YES";

  	if($('#txtcustid').val() == "" || $('#txtcustid').val() == ""){
		alert("You Need a Valid Customer.");
		subz = "NO";
	}


  	if($('#txtnGross').val() == "" || $('#txtnGross').val() == 0){
		alert("Zero or Blank AMOUNT RECEIVED is not allowed!");
		subz = "NO";
	}

  	if($('#txtORNo').val() == ""){
		alert("Please input your OR NUMBER!");
		subz = "NO";
	}
	    
	if($('#selpayment').val() == "Cheque"){
		if($('#txtBankName').val() == "" || $('#txtChekDate').val() == "" || $('#txtCheckNo').val() == "" || $('#txtCheckAmt').val() == ""){
			alert("Please complete your cheque details!");
			subz = "NO";
		}
	}
	
	if($('#selpaytype').val() == "Sales"){
		
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			
			if(lastRow==0){
				alert("Sales Details Required!");
				subz = "NO";
			}
			else{
					var tot = 0;
					
					for (z=1; z<=lastRow; z++){
						x = document.getElementById('txtApplied' + z).value;
						if(x!=0 && x!=""){
							var tot = tot + 1;	
						}
					}
					
					if(tot == 0){
						alert("Your details has no amount");
						subz = "NO";
					}
					else if(tot < lastRow){
						alert("Note: Only details with applied amount will be saved.");
					}
					
					$("#hdnrowcnt").val(lastRow);

			}

	}
	
	if(subz=="NO"){
		return false;
	}
	else{
		if($('#selpayment').val() == "Cheque"){
			$('#txtCheckAmt').val($('#txtCheckAmt').maskMoney('unmasked')[0]);
		}
		
		
		$("#frmOR").submit();
	}

});

</script>


</body>
</html>
