<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Journal.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" href="../../Bootstrap/css/bootstrap-select.css?t=<?php echo time();?>">
    
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script src="../../Bootstrap/js/seljs/bootstrap-select.js?t=<?php echo time();?>" defer></script>

<title>Transactions Journal</title>
</head>

<body style="padding:10px">
<h3 class="nopadding">Transaction Journal</h3>
<p class="nopadding">&nbsp;</p>

    <ul class="nav nav-tabs" id="RptTabs" role="tablist">
      <li class="active"><a data-toggle="tab" href="#Acctg" onClick="clrifrme();">Accounting</a></li>
      <li><a data-toggle="tab" href="#Sales" onClick="clrifrme();">Sales &amp; Delivery</a></li>
      <li><a data-toggle="tab" href="#Purchs" onClick="clrifrme();">Purchases</a></li>
    </ul>
    
    <div class="tab-content">
    
      <div class="tab-pane in fade active" id="Acctg" >
      
      		<div class="col-sm-12 nopadwtop2x">
             	<div class="col-sm-1 nopadwleft">
                	<b><button class="btn btn-sm btn-success" id="btnAcctg" name="btnAcctg">View Report</button></b>
                </div>
           	    <div class="col-sm-1 nopadwleft">
                	<b>Date Range: </b>
                </div>
                <div class="col-sm-8 nopadwleft">
                		<div class="col-sm-12 nopadding">
                         <div class="col-sm-2 nopadding">
                        	<input type='text' class="datepick form-control input-sm" id="dateacctgfr" name="dateacctgfr" value="<?php echo date("m/d/Y");?>"/>
                         </div>
                         
                         <div class="col-sm-1 nopadding" style="text-align:center">
                         	<b>TO</b>
                         </div>
                         
                         <div class="col-sm-2 nopadding">
                         	<input type='text' class="datepick form-control input-sm" id="dateacctgto" name="dateacctgto" value="<?php echo date("m/d/Y");?>" />
                         </div>
                       </div>
                </div>
            </div>

      		<div class="col-sm-12 nopadwtop2x">
             	<div class="col-sm-1 nopadwleft">
                	&nbsp;
                </div>
           	    <div class="col-sm-1 nopadwleft">
                	<b>Module: </b>
                </div>
                <div class="col-sm-3 nopadwleft">
					<select id="seltypacctg" name="seltypacctg" class="selectpicker form-control" multiple data-done-button="true">
                        <option value="JE">Journal Entry</option>
                        <option value="APV">AP Voucher</option>
                        <option value="PV">Pay Bills</option>
                        <option value="CM">Credit Memo</option>
                        <option value="DM">Debit Memo</option>
                        <option value="OR">Receive Payment</option>
                        <option value="BD">Bank Deposit</option>
                    </select>
                    
                    <input type="hidden" name="hdntypacctg" id="hdntypacctg" value="JE,APV,PV,CM,DM,OR,BD">
                </div>
            </div>
           
      </div>
      
      <div class="tab-pane fade" id="Sales">
            
            <div class="col-sm-12 nopadwtop2x">
             	<div class="col-sm-1 nopadwleft">
                	<b><button class="btn btn-sm btn-success" id="btnSales" name="btnSales">View Report</button></b>
                </div>
           	    <div class="col-sm-1 nopadwleft">
                	<b>Date Range: </b>
                </div>
                <div class="col-sm-8 nopadwleft">
                		<div class="col-sm-12 nopadding">
                         <div class="col-sm-2 nopadding">
                        	<input type='text' class="datepick form-control input-sm" id="datesalesgfr" name="datesalesgfr" value="<?php echo date("m/d/Y");?>"/>
                         </div>
                         
                         <div class="col-sm-1 nopadding" style="text-align:center">
                         	<b>TO</b>
                         </div>
                         
                         <div class="col-sm-2 nopadding">
                         	<input type='text' class="datepick form-control input-sm" id="datesalesgto" name="datesalesgto" value="<?php echo date("m/d/Y");?>" />
                         </div>
                       </div>
                </div>
            </div>

      		<div class="col-sm-12 nopadwtop2x">
             	<div class="col-sm-1 nopadwleft">
                	&nbsp;
                </div>
           	    <div class="col-sm-1 nopadwleft">
                	<b>Module: </b>
                </div>
                <div class="col-sm-3 nopadwleft">
					<select id="seltypsales" name="seltypsales" class="selectpicker form-control" multiple data-done-button="true">
                        <option value="DR">Delivery Receipt</option>
                        <option value="SI">Sales Invoice / POS</option>
                        <option value="SR">Sales Return</option>
                    </select>
                    
                    <input type="hidden" name="hdntypsales" id="hdntypsales" value="DR,SI,SR">
                </div>
            </div>

      </div>
      <div class="tab-pane fade" id="Purchs">
      
              <div class="col-sm-12 nopadwtop2x">
             	<div class="col-sm-1 nopadwleft">
                	<b><button class="btn btn-sm btn-success" id="btnPurch" name="btnPurch">View Report</button></b>
                </div>
           	    <div class="col-sm-1 nopadwleft">
                	<b>Date Range: </b>
                </div>
                <div class="col-sm-8 nopadwleft">
                		<div class="col-sm-12 nopadding">
                         <div class="col-sm-2 nopadding">
                        	<input type='text' class="datepick form-control input-sm" id="datepurchgfr" name="datepurchgfr" value="<?php echo date("m/d/Y");?>"/>
                         </div>
                         
                         <div class="col-sm-1 nopadding" style="text-align:center">
                         	<b>TO</b>
                         </div>
                         
                         <div class="col-sm-2 nopadding">
                         	<input type='text' class="datepick form-control input-sm" id="datepurchgto" name="datepurchgto" value="<?php echo date("m/d/Y");?>" />
                         </div>
                       </div>
                </div>
            </div>

      		<div class="col-sm-12 nopadwtop2x">
             	<div class="col-sm-1 nopadwleft">
                	&nbsp;
                </div>
           	    <div class="col-sm-1 nopadwleft">
                	<b>Module: </b>
                </div>
                <div class="col-sm-3 nopadwleft">
					<select id="seltypurch" name="seltypurch" class="selectpicker form-control" multiple data-done-button="true">
                        <option value="RR">Receiving Report</option>
                        <option value="PR">Purchase Return</option>
                    </select>
                    
                    <input type="hidden" name="hdntyppurch" id="hdntyppurch" value="RR,PR">
                </div>
            </div>

      </div>
          
    </div>
        	
    </div>

<iframe name="ifrmbody" id="ifrmbody" src="" style="width:100%; border:0"></iframe>    
</body>
</html>

<script>
$(document).ready(function() {
    var iframeWin = parent.document.getElementById("ifrmbody");
    iframeWin.height = document.body.scrollHeight;
});

$(function(){
	    $('.datepick').datetimepicker({
                 format: 'MM/DD/YYYY',
        });
		
		$("#btnAcctg").on("click", function(){
			var xy = $("#seltypacctg").val();
			var dte1 = $("#dateacctgfr").val();
			var dte2 = $("#dateacctgto").val();
			var fxmodz = "";
			var cnt = 0;
			
			$('#seltypacctg > option:selected').each(function() {
				// this should loop through all the selected elements
				cnt = cnt + 1;
				
				if(cnt>1){
					fxmodz = fxmodz + ",";
				}
				
				fxmodz = fxmodz + $(this).val();
			});
			
			
			if(fxmodz==""){
				fxmodz = $("#hdntypacctg").val(); 
			}
			
			$("#ifrmbody").attr("src", "TransJournal.php?dtefr="+dte1+"&dteto="+dte2+"&typs="+fxmodz);
			
		});

		$("#btnSales").on("click", function(){
			var xy = $("#seltypsales").val();
			var dte1 = $("#datesalesgfr").val();
			var dte2 = $("#datesalesgto").val();
			var fxmodz = "";
			var cnt = 0;
			
			$('#seltypacctg > option:selected').each(function() {
				// this should loop through all the selected elements
				cnt = cnt + 1;
				
				if(cnt>1){
					fxmodz = fxmodz + ",";
				}
				
				fxmodz = fxmodz + $(this).val();
			});
			
			
			if(fxmodz==""){
				fxmodz = $("#hdntypsales").val(); 
			}
			
			$("#ifrmbody").attr("src", "TransJournal.php?dtefr="+dte1+"&dteto="+dte2+"&typs="+fxmodz);
			
		});


		$("#btnPurch").on("click", function(){
			var xy = $("#seltypurch").val();
			var dte1 = $("#datepurchgfr").val();
			var dte2 = $("#datepurchgto").val();
			var fxmodz = "";
			var cnt = 0;
			
			$('#seltypurch > option:selected').each(function() {
				// this should loop through all the selected elements
				cnt = cnt + 1;
				
				if(cnt>1){
					fxmodz = fxmodz + ",";
				}
				
				fxmodz = fxmodz + $(this).val();
			});
			
			
			if(fxmodz==""){
				fxmodz = $("#hdntyppurch").val(); 
			}
			
			$("#ifrmbody").attr("src", "TransJournal.php?dtefr="+dte1+"&dteto="+dte2+"&typs="+fxmodz);
			
		});
			
});

function clrifrme(){
	$("#ifrmbody").attr("src", "");
}
</script>