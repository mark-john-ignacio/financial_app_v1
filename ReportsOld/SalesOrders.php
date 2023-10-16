<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesOrders.php";
include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
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
    <center><font size="+1"><b><u>Sales Orders</u></b></font></center>
    <br>

    <form action="Sales/SalesPerCustomer.php" method="post" name="frmrep" id="frmrep" target="_blank">
        <table width="100%" border="0" cellpadding="2">
            <tr>
                <td valign="top" width="50" style="padding:2px">
                    <button type="button" class="btn btn-danger btn-block" id="btnView">
                        <span class="glyphicon glyphicon-search"></span> View Report
                    </button>
                </td>
                <td width="150" style="padding-left:10px"><b>Billed To: </b></td>
                <td style="padding:2px">
                    <div class="col-xs-12 nopadding">
                    <div class="col-xs-8 nopadding">
                        <input type="text" class="form-control" name="txtCust" id="txtCust" placeholder="Search Customer Name..." required autocomplete="off">
                    </div>
                    <div class="col-xs-3 nopadwleft">
                        <input type="text" class="form-control" name="txtCustID" id="txtCustID" readonly>
                    </div>
                    </div>
                </td>
            </tr>
    
            <tr>
                <td rowspan="6" valign="top" width="50" style="padding:2px">
                    <button type="button" class="btn btn-success btn-block" id="btnexcel">
                        <i class="fa fa-file-excel-o"></i> To Excel
                    </button>
                </td>
                <td width="150" style="padding-left:10px"><b>Item Type:</b></td>
                <td style="padding:2px">
                    <div class="col-xs-8 nopadding">
                        <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
                            <option value="">All Items</option>
                            <?php
                                $sql = "select * from groupings where ctype='ITEMTYP' order by cdesc";
                                $result=mysqli_query($con,$sql);
                                if (!mysqli_query($con, $sql)) {
                                    printf("Errormessage: %s\n", mysqli_error($con));
                                }			
                        
                                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                    {
                            ?>   
                                <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                            <?php
                                }                        
                            ?>     
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <td width="150" style="padding-left:10px"><b>Item Classification:</b></td>
                <td style="padding:2px">
                    <div class="col-xs-8 nopadding">
                        <select id="seliclass" name="seliclass" class="form-control input-sm selectpicker"  tabindex="4">
                            <option value="">All Items</option>
                            <?php
                                $sql = "select * from groupings where ctype='ITEMCLS' order by cdesc";
                                $result=mysqli_query($con,$sql);
                                if (!mysqli_query($con, $sql)) {
                                    printf("Errormessage: %s\n", mysqli_error($con));
                                }			
                        
                                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                    {
                            ?>   
                                <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                            <?php
                                }                        
                            ?>     
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <td width="150" style="padding-left:10px"><b>Customer Type:</b></td>
                <td style="padding:2px">
                    <div class="col-xs-8 nopadding">
                        <select id="selcustype" name="selcustype" class="form-control input-sm selectpicker"  tabindex="4">
                            <option value="">All Items</option>
                            <?php
                                $sql = "select * from groupings where ctype='CUSTYP' order by cdesc";
                                $result=mysqli_query($con,$sql);
                                if (!mysqli_query($con, $sql)) {
                                    printf("Errormessage: %s\n", mysqli_error($con));
                                }			
                        
                                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                    {
                            ?>   
                                <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                            <?php
                                }                        
                            ?>     
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <td style="padding-left:10px"><b>Transaction Type: </b></td>
                <td style="padding:2px">
                    <div class="col-xs-4 nopadding">
                        <select id="seltrantype" name="seltrantype" class="form-control input-sm selectpicker"  tabindex="4">
                            <option value="">All Transactions</option>   
                            <option value="Trade">Trade</option>      
                            <option value="Non-Trade">Non-Trade</option>           
                        </select>               
                    </div>
                    <div class="col-xs-4 nopadwleft">
                        <select id="sleposted" name="sleposted" class="form-control input-sm selectpicker"  tabindex="4">
                            <option value="">All Transactions</option>   
                            <option value="1">Posted</option>      
                            <option value="0">UnPosted</option>           
                        </select>
                            
                    </div>
                </td>
            </tr>

            <tr>
                <td style="padding-left:10px"><b>Report Type: </b></td>
                <td style="padding:2px">
                    <div class="col-xs-8 nopadding">
                        <select id="selreporttyp" name="selreporttyp" class="form-control input-sm selectpicker"  tabindex="4">
                            <option value="SOSumCust">Summary per Customer</option>      
                            <option value="SOSumItem">Summary per Item</option>
                            <option value="SOSumCustItem">Summary per Customer/Item</option>          
                        </select>               
                    </div>
                </td>
            </tr>
        
            <tr>
                <td style="padding-left:10px"><b>Date Range: </b></td>
                <td style="padding:2px">
                <div class="col-xs-12 nopadding">
                    <div class="col-xs-3 nopadding">

                    <input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />

                    </div>
                    
                    <div class="col-xs-2 nopadding" style="vertical-align:bottom;" align="center">
                        <label style="padding:1px;">TO</label>
                    </div>
            
                    <div class="col-xs-3 nopadding">

                    <input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" />

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

        //proddesc searching	
        
        $('#txtCust').typeahead({
            autoSelect: true,
            source: function(request, response) {
                $.ajax({
                    url: "th_customer.php",
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

        $('#btnView').on("click", function(){
            $dval = $("#selreporttyp").val();
            $('#frmrep').attr("action", "Sales/"+$dval+".php");
            $('#frmrep').submit();
        });

        $('#btnexcel').on("click", function(){
            $dval = $("#selreporttyp").val();
            $('#frmrep').attr("action", "Sales/"+$dval+"_xls.php");
            $('#frmrep').submit();
        });

    });
</script>
