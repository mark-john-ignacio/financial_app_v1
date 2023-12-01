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
				$("#divname").html("<font size=\"+2\"><u>GL & BIR Reports</u></font>");
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
<title>Myx Financials</title>
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

            <div style="padding-left:10px; padding-top:3px">
					    <ul class="ver-inline-menu tabbable margin-bottom-25">  

								<li>
          				<a href="" onClick="setI('A','SalesOrders.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Sales Orders </a>
                </li>

                <li>
          				<a href="" onClick="setI('A','SalesPerItem.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Sales Per Item </a>
                </li>

          			<li>
          				<a href="" onClick="setI('A','SalesPerCust.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Sales Per Customer </a>
                </li>
                
    						<li>
    							<a href="" onClick="setI('A','SalesSummary.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Sales Summary </a>
    						</li>
    						<li>
    							<a href="" onClick="setI('A','SalesDetailed.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Sales Detailed </a>
    						</li>
                <li>
    							<a href="" onClick="setI('A','SalesDisc.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> SO vs DR vs SI </a>
    						</li>
                <li>
    							<a href="" onClick="setI('A','SODRDisc.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Discrepancy Report - SO vs DR </a>
    						</li>
								<li>
									<a href="" onClick="setI('A','ARAgeing.php')" data-toggle="tab">
									<i class="fa fa-book"></i> AR Ageing </a>
								</li>
								<li>
									<a href="" onClick="setI('A','ARMonitoring.php')" data-toggle="tab">
									<i class="fa fa-book"></i> AR Monitoring </a>
								</li>
              </ul>
					  </div>

        </div>
        
        
        <div id="purch" class="divhid">

            <div style="padding-left:10px; padding-top:3px">
					    <ul class="ver-inline-menu tabbable margin-bottom-25">  
                <li>
          				<a href="" onClick="setI('A','PurchPerItem.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Purchases Per Item </a>
                </li>

          			<li>
          				<a href="" onClick="setI('A','PurchPerSupp.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Purchases Per Supplier </a>
                </li>
                
    						<li>
    							<a href="" onClick="setI('A','PurchSummary.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Purchases Summary </a>
    						</li>
    						<li>
    							<a href="" onClick="setI('A','PurchDetailed.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Purchases Detailed </a>
    						</li>
                <li>
    							<a href="" onClick="setI('A','PurchBalances.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> PO Balances </a>
    						</li>
                <li>
    							<a href="" onClick="setI('A','PurchMonitoring.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> PO Price Monitoring </a>
    						</li>
								<li>
									<a href="" onClick="setI('A','APAgeing.php')" data-toggle="tab">
									<i class="fa fa-book"></i> AP Ageing Report </a>
								</li>
              </ul>
					  </div>

        
        </div>
        
        <div id="acc" class="divhid">
        
					<h4 class="nopadding">GL Reports</h4>
					<hr class="alert-danger nopadding">
          			<div style="padding-left:10px; padding-top:3px">
						<ul class="ver-inline-menu tabbable margin-bottom-25">
							<li>
								<a href="" onClick="setI('A','SalesReg.php')" data-toggle="tab">
								<i class="fa fa-book"></i> Sales Register </a>
							</li>
							<li>
								<a href="" onClick="setI('A','CashBook.php')" data-toggle="tab">
								<i class="fa fa-book"></i> Cash Receipts Book</a>
							</li>
							<!--<li>
									<a href="" onClick="setI('A','PurchJourn.php')" data-toggle="tab">
								<i class="fa fa-book"></i> Purchase Journal </a>
							</li>-->
							<li>
								<a href="" onClick="setI('A','APJ.php')" data-toggle="tab">
								<i class="fa fa-book"></i> Accounts Payable Ledger </a>
							</li>
							<li>
								<a href="" onClick="setI('A','CDJ.php')" data-toggle="tab">
								<i class="fa fa-book"></i> Cash Disbursement Book </a>
							</li>
							<li>
								<a href="" onClick="setI('A','CashPosition.php')" data-toggle="tab">
								<i class="fa fa-book"></i> Cash Position </a>
							</li>  
							<li>
								<a href="" onClick="setI('A','GJournal.php')" data-toggle="tab">
								<i class="fa fa-book"></i> General Journal </a>
							</li>
							<li>
								<a href="" onClick="setI('A','GLedger.php')" data-toggle="tab">
								<i class="fa fa-book"></i> General Ledger </a>
							</li>
          			<li>
          				<a href="" onClick="setI('A','TBal.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Trial Balance </a>
                </li>
                
    						<li>
    							<a href="" onClick="setI('A','BalSheet.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Balance Sheet </a>
    						</li> 

								<li>
    							<a href="" onClick="setI('A','IStatement.php')" data-toggle="tab">
    							<i class="fa fa-book"></i> Income Statement </a>
    						</li> 
						</ul>
					</div>

							<h4 class="nopadwtop2x">BIR Reports</h4>
							<hr class="alert-danger nopadding">
								<div style="padding-left:10px; padding-top:3px">
									<ul class="ver-inline-menu tabbable margin-bottom-25"> 
										<li>
											<a href="" onclick="setI('A', 'SalesDat.php')"  data-toggle="tab">
											<i class="fa fa-book"></i>BIR Sales Relief</a>
										</li>
										<li>
											<a href="" onclick="setI('A', 'PurchaseDat.php')" data-toggle="tab">
												<i class="fa fa-book"></i>BIR Purchase Relief</a>
										</li>         
										<!-- <li>
											<a href="" onClick="setI('A','MonthlyVAT.php')" data-toggle="tab">
											<i class="fa fa-book"></i> Monthly Output VAT</a>
										</li>
										<li>
											<a href="" onClick="setI('A','Monthly_IVat.php')" data-toggle="tab">
											<i class="fa fa-book"></i> Monthly Input VAT and W/Tax </a>
										</li> -->
										<li>
											<a href="" onClick="setI('A', 'BIR_2307.php')" data-toggle="tab">
											<i class="fa fa-book"></i>BIR FORM 2307</a>
										</li>
										<li>
											<a href="" onClick="setI('A', 'BIR_2306.php')" data-toggle="tab">
											<i class="fa fa-book"></i>BIR FORM 2306</a>
										</li>
										<!--
										<li>
											<a href="" onClick="setI('A', 'BIR_Quartely.php')" data-toggle="tab">
											<i class="fa fa-book"></i>2550Q Form</a>
										</li>
										-->
									</ul>
								</div>
        
        </div>
        
        <div id="inv" class="divhid">
        
            <div style="padding-left:10px; padding-top:3px">
					    <ul class="ver-inline-menu tabbable margin-bottom-25">  
                <li>
          				<a href="" onClick="setI('A','InvSum.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> FG Inventory Report </a>
                </li>
								<!--
                <li>
          				<a href="" onClick="setI('A','InvTrans_Reg.php')" data-toggle="tab">
                  <i class="fa fa-book"></i> Inventory Transfer - Register </a>
                </li>
								-->
              </ul>
            </div>

        </div>
  

        </div>
    	<div class="col-sm-9 nopadwleft">

                <iframe src="" frameborder="0" scrolling="no" id="myreport" width="100%" height="600px"> </iframe>

        </div> 
 </div>

</body>
</html>