<html>
<head>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">//<![CDATA[

$(document).ready(function() {
    $(".divhid").hide();
	
	$("#<?php echo $_GET["id"];?>").show();
	
	if($("#hdntyp").val()=="sales"){
		$("#divname").html("<font size=\"+2\"><u>Sales Reports</u></font>");
	}
	else if($("#hdntyp").val()=="purch"){
		$("#divname").html("<font size=\"+2\"><u>Purchase Reports</u></font>");
	}
	else if($("#hdntyp").val()=="acc"){
		$("#divname").html("<font size=\"+2\"><u>Finance Reports</u></font>");
	}
	else if($("#hdntyp").val()=="inv"){
		$("#divname").html("<font size=\"+2\"><u>Inventory Reports</u></font>");
	}

});


function setI(typ,x){
	if(typ=='A'){
		document.getElementById("myreport").src = x;
	}
	else if(typ=='B'){
		document.getElementById("myreport").src = "";
		document.getElementById("transnew").action = x;
		document.getElementById("transnew").submit();
	}
}

function resizeIframe(obj) {
            // here you can make the height, I delete it first, then I make it again
			var x = obj.contentWindow.document.body.scrollHeight + 30;
			obj.style.height = x + 'px';      
			}   
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
</head>

<body style="padding-left:10px; height:450px">
<form id="transnew" name="transnew" target="_blank" method="post">
</form>

<input type="hidden" name="hdntyp" id="hdntyp" value="<?php echo $_GET["id"];?>">

<div class="col-sm-12 nopadding">

	<div id="divname">
			
    </div>
    <hr>
</div>

  <div class="col-sm-12 nopadding">
        <div class="col-sm-3 nopadding">

      <div id="sales" class="divhid">

        <table width="100%" border="0" class="table-hover">
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','SalesPerItem.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Per Item</a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','SalesPerCust.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Per Customer</a></td>
          </tr>
          <!--
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','SalesPerSupp.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Per Suppliers</a></td>
          </tr>
    -->
             <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','SalesSummary.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Summary</a></td>
          </tr>
              <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','SalesDetailed.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Detailed</a></td>
          </tr>
      </table>

        </div>
        
        
        <div id="purch" class="divhid">
        
        <table width="100%" border="0" class="table-hover">
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','PurchPerItem.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchases Per Item</a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','PurchPerSupp.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchases Per Supplier</a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','PurchSummary.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchased Summary</a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','PurchDetailed.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchased Detailed</a></td>
          </tr>
             <tr>
             <!--
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','SalesSummary.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;PO Monitoring</a></td>-->
          </tr>
       </table>

        
        </div>
        
        <div id="acc" class="divhid">
        
				<h4 class="nopadding">Receivables</h4>
				<hr class="alert-danger nopadding">
          <div style="padding-left:10px; padding-top:3px">
					<ul class="ver-inline-menu tabbable margin-bottom-25">
						<li>
              <a href="" onClick="setI('A','SalesReg.php')" data-toggle="tab">
              <i class="fa fa-book"></i> Sales Register </a>
            </li>
            <li>
							<a href="" onClick="setI('A','CashBook.php')" data-toggle="tab">
							<i class="fa fa-book"></i> Cash Receipts Journal</a>
						</li>
						<!--<li>
							<a href="" onClick="setI('A','ARStat.php')" data-toggle="tab">
							<i class="fa fa-book"></i> Sales Book </a>
						</li>
            <li>
              <a href="" onClick="setI('A','ARAgeing.php')" data-toggle="tab">
              <i class="fa fa-book"></i> AR Ageing </a>-->
            </li>            
          </ul>
					</div>
                    
          <h4 class="nopadwtop2x">Payables</h4>
          <hr class="alert-danger nopadding">
            <div style="padding-left:10px; padding-top:3px">
  					<ul class="ver-inline-menu tabbable margin-bottom-25">          
            	<li>
            		<a href="" onClick="setI('A','PurchJourn.php')" data-toggle="tab">
                <i class="fa fa-book"></i> Purchase Journal </a>
              </li>
  						<li>
  							<a href="" onClick="setI('A','APJ.php')" data-toggle="tab">
  							<i class="fa fa-book"></i> Accounts Payable Ledger </a>
  						</li>
  						<li>
  							<a href="" onClick="setI('A','CDJ.php')" data-toggle="tab">
  							<i class="fa fa-book"></i> Cash Disbursement Journal </a>
  						</li>
            </ul>
  					</div>

            <h4 class="nopadwtop2x">Financial Reports</h4>
            <hr class="alert-danger nopadding">
              <div style="padding-left:10px; padding-top:3px">
					    <ul class="ver-inline-menu tabbable margin-bottom-25">       
                <li>
          				<a href="" onClick="setI('A','GJournal.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> General Journal </a>
                </li>  
          			<li>
          				<a href="" onClick="setI('A','TBal.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Trial Balance </a>
                </li>
                
    						<li>
    							<a href="" onClick="setI('A','SFP.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Statement of Financial Position </a>
    						</li>
                <!--
    						<li>
    							<a href="" onClick="setI('A','CDJ.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Cash Disbursement Journal </a>
    						</li>-->
              </ul>
					    </div>

        

        
        </div>
        
        <div id="inv" class="divhid">
        
         <table width="100%" border="0" class="table-hover">
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','InvSum.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Inventory Summary </a></td>
          </tr>
         <!-- <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('A','StockLedger.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Stock Ledger </a></td>
          </tr>-->
      </table>

        </div>
  

        </div>
    	<div class="col-sm-9 nopadwleft">

                <iframe src="" frameborder="0" scrolling="no" id="myreport" width="100%" height="450px"> </iframe>

        </div> 
 </div>

</body>
</html>