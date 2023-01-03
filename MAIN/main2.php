    <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">
.glyphicon { margin-right:10px; }
.panel-body { padding:0px; }
.panel-body table tr td { padding-left: 15px }
.panel-body .table {margin-bottom: 0px; }
.sidebar { color:#036; font-size:12px};
    </style>
    <script src="../include/jquery-1.10.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script type="text/javascript">
        window.alert = function(){};
        var defaultCSS = document.getElementById('bootstrap-css');
        function changeCSS(css){
            if(css) $('head > link').filter(':first').replaceWith('<link rel="stylesheet" href="'+ css +'" type="text/css" />'); 
            else $('head > link').filter(':first').replaceWith(defaultCSS); 
        }
        $( document ).ready(function() {
          var iframe_height = parseInt($('html').height()); 
          window.parent.postMessage( iframe_height, 'http://bootsnipp.com');
        });
		
		
function setframe(x) {

	document.getElementById("myframe").src=x;
	document.getElementById("myframe").focus();
}

    </script>


            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           <span class="glyphicon glyphicon-home">
                           </span>Home
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse">
                        <div class="panel-body">
							&nbsp;
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span class="glyphicon glyphicon-cog">
                            </span>Maintenance</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                       <a href="../Maintenance/Accounts.php?f="  target="myframe" class="sidebar"> &nbsp; > Chart of Accounts </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="../Maintenance/Items.php?f="  target="myframe" class="sidebar"> &nbsp; > Items Master List</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="../Maintenance/Customers.php?f="  target="myframe" class="sidebar"> &nbsp; > Customers Master List</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="../Maintenance/Suppliers.php?f="  target="myframe" class="sidebar"> &nbsp; > Suppliers Master List</a>
                                    </td>
                                </tr>
                               <tr>
                                    <td>
                                        <a href="../Maintenance/users.php?f=" target="myframe" class="sidebar"> &nbsp; > Users List</a>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>
                                        <a href="../System/" target="myframe" class="sidebar"> &nbsp; > System Settings</a>
                                    </td>
                                </tr>
                          </table>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><span class="glyphicon glyphicon-shopping-cart">
                            </span>Sales</a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table">
                                 <tr>
                                    <td>
                                        <a href="javascript:;" target="myframe" class="sidebar" onclick="setframe('../Sales/Quote.php')"> &nbsp; > Sales Quotation</a>
                                    </td>
                                </tr>
                               <tr>
                                    <td>
                                        <a href="javascript:;" target="myframe" class="sidebar" onclick="setframe('../Sales/POS.php')"> &nbsp; > POS Retail</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:;" target="myframe" class="sidebar" onclick="setframe('../Sales/SalesRet.php')"> &nbsp; > Sales Return</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"><span class="glyphicon glyphicon-download-alt">
                            </span>Purchases</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <a href="../Purchases/Purch.php" target="myframe" class="sidebar"> &nbsp; > Purchase Order</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                         <a href="../Purchases/Received.php" target="myframe" class="sidebar"> &nbsp; > Receiving</a>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>
                                         <a href="../Purchases/PurchRet.php" target="myframe" class="sidebar"> &nbsp; > Purchase Return</a>
                                    </td>
                                </tr>
                           </table>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive"><span class="glyphicon  glyphicon-list-alt">
                            </span>Accounting</a>
                        </h4>
                    </div>
                    <div id="collapseFive" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <a href="../Accounting/APV.php" target="myframe" class="sidebar"> &nbsp; > AP Voucher</a>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>
                                        <a href="../Accounting/PayBill.php" target="myframe" class="sidebar"> &nbsp; > Pay Bills</a>
                                    </td>
                                </tr>
                                   <tr>
                                    <td>
                                        <a href="../Accounting/OR.php" target="myframe" class="sidebar"> &nbsp; > Receive Money</a>
                                    </td>
                                </tr>
                                   <tr>
                                    <td>
                                        <a href="../Accounting/Deposit.php" target="myframe" class="sidebar"> &nbsp; > Prepare Bank Deposit</a>
                                    </td>
                                </tr>
                           </table>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="../Reports/menu.php" target="myframe">
                            <span class="glyphicon glyphicon-book">
                            </span>Reports
                            </a>
                        </h4>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                            <span class="glyphicon glyphicon-barcode">
                            </span>Inventory</a>
                        </h4>
                    </div>
                    <div id="collapseSix" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <a href="../RECOM/" target="myframe" class="sidebar"> &nbsp; > Recompute</a>
                                    </td>
                                </tr>
                           </table>
                        </div>
                    </div>
                </div>
            </div>


           <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse7">
                            <span class="glyphicon glyphicon-barcode">
                            </span>Loan Manager</a>
                        </h4>
                    </div>
                    <div id="collapse7" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                        <a href="../RECOM/" target="myframe" class="sidebar"> &nbsp; > Recompute</a>
                                    </td>
                                </tr>
                           </table>
                        </div>
                    </div>
                </div>
            </div>
