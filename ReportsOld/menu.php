<html>
<head>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min34.css">
     <script type="text/javascript" src="../js/jquery-1.10.1.js"></script>

<style type="text/css">
    body{margin:0;padding:10px;}
div.divhid
{
    float:left;
    display:block;
    height:40px;
    width:20%;
    cursor:pointer;
    display:none;
    position:absolute;
    top:90px;
}
div.div1 {
    height: 400px;
	margin-top: -50px;
    margin-right: 10px;
    width: 200px;
	/*border:solid 1px;*/
}
div.div3 {
	
    height: 500px;
    overflow: hidden;
}
div.div2 {
    height: 15px;
    margin-top: -130px;
    margin-right: 10px;
	/*border:solid 1px;*/
}

#rcorners3 {
   /* border-radius: 25px;
    border: 2px solid #73AD21;
    padding: 20px; */
    width: 70%;
	float:right;
	display:none;
}
#tbl {
	width: 100%;
	padding:10px;
	border:none;
}

.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
  background-color: #BFDEFF;
  color:#FFF;
}

br {clear:both;}
  </style>

<script type="text/javascript">//<![CDATA[
$(window).load(function(){

$("#btnsales").on('click', function() {
   $("#sales,#rcorners3").fadeIn();
   $("#purch,#acc,#inv,#maindiv").fadeOut();
});
$("#btnpurch").on('click', function() {
   $("#purch,#rcorners3").fadeIn();
   $("#sales,#inv,#acc,#maindiv").fadeOut();
});
$("#btnacctg").on('click', function() {
   $("#acc,#rcorners3").fadeIn();
   $("#sales,#inv,#purch,#maindiv").fadeOut();
});
$("#btninv").on('click', function() {
   $("#inv,#rcorners3").fadeIn();
   $("#sales,#acc,#purch,#maindiv").fadeOut();
});
});//]]> 


function setI(x){
	document.getElementById("myreport").src = x;
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

<body style="padding:10px; height:450px">

<nav class="navbar navbar-default">
 <a class="navbar-brand" href="#">Reports Menu</a>
 
    <button type="button" class="btn btn-warning navbar-btn" id="btnsales" >
    <span class="glyphicon glyphicon-shopping-cart"></span> Sales
    </button>
    <button type="button" class="btn btn-Info navbar-btn" id="btnpurch">
    <span class="glyphicon glyphicon-download-alt"></span> Purchases
    </button>
    <button type="button" class="btn btn-success navbar-btn" id="btnacctg">
    <span class="glyphicon glyphicon-list-alt"></span> Accounting
    </button>
    <button type="button" class="btn btn-primary navbar-btn" id="btninv">
    <span class="glyphicon glyphicon-barcode"></span> Inventory
    </button>

</nav>

        <div id="sales" class="divhid">
        
        <table width="100%" border="0" class="table-hover">
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesReg.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Register </a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesPerItem.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Per Item</a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesPerCust.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Per Customer</a></td>
          </tr>
          
          <!--<tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesPerSupp.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Per Suppliers</a></td>
          </tr>-->

             <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesSummary.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Summary</a></td>
          </tr>
              <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesDetailed.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Sales Detailed</a></td>
          </tr>
      </table>

        </div>
        
        
        <div id="purch" class="divhid">
        
        <table width="100%" border="0" class="table-hover">
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('PurchReg.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchase Journal </a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('PurchPerItem.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchases Per Item</a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('PurchPerSupp.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Purchases Per Supplier</a></td>
          </tr>
             <tr>
             <!--
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('SalesSummary.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;PO Monitoring</a></td>-->
          </tr>
       </table>

        
        </div>
        
        <div id="acc" class="divhid">ACCOUNTING</div>
        
        <div id="inv" class="divhid">
        
         <table width="100%" border="0" class="table-hover">
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('InvSum.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Inventory Summary </a></td>
          </tr>
          <tr>
            <td height="50px" style="border-bottom:solid #999 1px"><a href="javascript:;" onClick="setI('StockLedger.php')"><span class="glyphicon glyphicon-chevron-right">
                            </span>&nbsp;&nbsp;Stock Ledger </a></td>
          </tr>
      </table>

        </div>
  
        <div id="pie"></div>
                
    <div id="rcorners3">
    	<iframe src="SalesReg.htm" frameborder="0" scrolling="no" onload="resizeIframe(this);" id="myreport" width="100%"> </iframe>
    </div>

<div id="maindiv">

      <div style="float:left;">
        <div class="div1"><img width="600" height="400" src="../Chart/charts/Top10Prod.php" /></div>
        <div class="div2"> <img width="600" height="400" src="../Chart/charts/Top10Purch.php" /></div>
      </div>

    <div class="div3">
        <img width="500" height="450" src="../Chart/charts/Top10Cust.php" />
    </div>


</div>

</body>
</html>