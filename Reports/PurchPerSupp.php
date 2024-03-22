<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "PurchPerSupp.php";
    include('../Connection/connection_string.php');
    include('../include/denied.php');
    include('../include/access.php');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>

    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:50px;">
    <center><font size="+1"><b><u>Purchases Per Supplier</u></b></font></center>
    <br>

    <form action="Purchases/PurchPerSupp.php" method="post" name="frmrep" id="frmrep" target="_blank">
        <table width="100%" border="0" cellpadding="2">
            <tr>
                <td valign="top" width="50" style="padding:2px">
                    <button type="button" class="btn btn-danger" id="btnView">
                        <span class="glyphicon glyphicon-search"></span> View Report
                    </button>
                </td>
                <td width="150" style="padding-left:10px"><b>Supplier Name: </b></td>
                <td style="padding:2px">
                    <div class="col-xs-12 nopadding">
                    <div class="col-xs-8 nopadding">
                        <input type="text" class="form-control" name="txtCust" id="txtCust" placeholder="Search Supplier Name..." required autocomplete="off">
                    </div>
                    <div class="col-xs-3 nopadwleft">
                        <input type="text" class="form-control" name="txtCustID" id="txtCustID" readonly>
                    </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td valign="top" width="50" style="padding:2px">
                    <button type="button" class="btn btn-success btn-block" id="btnexcel">
                        <i class="fa fa-file-excel-o"></i> To Excel
                    </button>
                </td>
                <td style="padding-left:10px"><b>Transaction Type: </b></td>
                <td style="padding:2px">
                    <div class="col-xs-12 nopadding">
                        <div class="col-xs-4 nopadding">
                            <select id="sleposted" name="sleposted" class="form-control input-sm selectpicker" tabindex="4">
                                <option value="">All Transactions</option>   
                                <option value="1">Posted</option>      
                                <option value="0">UnPosted</option>           
                            </select>                           
                        </div>  
                    </div> 
                </td>
            </tr>
            <tr>
                <td valign="top" width="50" style="padding:2px">&nbsp;</td>
                <td style="padding-left:10px"><b>Date Range: </b></td>
                <td style="padding:2px">
                    <div class="col-xs-12 nopadding" id="datezpick">

                        <div class="form-group nopadding">
                            <div class="col-xs-8 nopadding">
                            <div class="input-group input-large date-picker input-daterange">
                                <input type="text" class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>">
                                <span class="input-group-addon">to </span>
                                <input type="text" class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>">
                            </div>
                            </div>	
                        </div>

                    </div>   
                </td>
            </tr>
        </table>
    </form>
</body>
</html>

<script type="text/javascript">
    $(function(){

        $('.datepick').datetimepicker({
            format: 'MM/DD/YYYY'
        });

        $('#btnView').on("click", function(){
            $('#frmrep').attr("action", "Purchases/PurchPerSupp.php");
            $('#frmrep').submit();
        });

        $('#btnexcel').on("click", function(){
            $('#frmrep').attr("action", "Purchases/PurchPerSupp_xls.php");
            $('#frmrep').submit();
        });

	    //proddesc searching	
        $('#txtCust').typeahead({
            autoSelect: true,
            source: function(request, response) {
                $.ajax({
                    url: "th_supplier.php",
                    dataType: "json",
                    data: {
                        query: $("#txtCust").val()
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            displayText: function (item) {
                return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
            },
            highlighter: Object,
            afterSelect: function(item) { 					
                            
                $('#txtCust').val(item.value).change(); 
                $("#txtCustID").val(item.id);
                
            }

        });

    });


</script>
