<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    // $_SESSION['pageid'] = "SalesDat.php";
    include("../Connection/connection_string.php");
    // include('../include/denied.php');
    // include('../include/access.php');
    $company = $_SESSION['companyid'];
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script> 
    <title>MyxFinancials</title>

    <style>
        th, td {
            padding-top: 2px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div style="text-align: center; font-weight: bold; text-decoration: underline;">
            <font size="+1">Sales Generate DAT</font>
        </div>
        <div class='container' style='padding-top: 50px'>
            <table>
                <tr valign="top">
                    <th><button class='btn btn-danger btn-block' id="btnView"><i class='fa fa-search'></i>View Report</button></th>
                    <th>Generate Date From:</th>
                    <th>
                        <div class="col-xs-8 nopadding">
                            <input type="text" id='datefrom' name='datefrom' class='datepicker form-control input-sm' value="<?= date("M/d/Y"); ?>">
                        </div>
                    </th>
                </tr>
                <tr valign="top">
                    <th><button class="btn btn-success btn-block" id="btnDat"><i class="fa fa-file-excel-o"></i>To Excel</button></th>
                    <th colspan='2'>&nbsp;</th>
                </tr>
            </table>
        </div>
    </div>

    <form action="Accounting/View_SalesDat.php" id='viewfrm' name='viewfrm' target="_blank" style='display: none'>
        <input type="text" id='daterange' name='daterange'>
    </form>
    <form action="Export/salesDat.php" id='exportFrm' name='exportFrm' target="_blank" style='display: none'>
        <input type="text" id='exportrange' name='exportrange'>
    </form>
</body>
</html>
<script>
    $(document).ready(function(){
        $(".datepicker").datetimepicker({
                 format: 'MMMM'
           });
        $("#btnView").click(function(){
            let range = $('#datefrom').val();
            $.ajax({
                url: "th_salesDat.php",
                data: { date: range},
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $("#daterange").val(range)
                        $("#viewfrm").submit()
                    } else {
                        alert(res.msg)
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })
        })

        $("#btnDat").click(function(){
            let range = $('#datefrom').val();
            $("#exportrange").val(range);
            $("#exportFrm").submit();
        })
    })

    
    
</script>