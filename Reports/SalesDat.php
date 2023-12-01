<?php 
    if(!isset($_SESSION)){
        session_start();
    }
     $_SESSION['pageid'] = "SalesDat.php";
    include("../Connection/connection_string.php");
    include('../include/denied.php');
    include('../include/access.php');
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
                    <th><button class='btn btn-danger btn-block' id="btnView"><i class='fa fa-search'></i>&nbsp;&nbsp;View Report</button></th>
                    <th width='100px'>Month of:</th>
                    <th>
                        <div class="col-xs-8 nopadding">
                            <input type="text" id='datemonth' name='datemonth' class='form-control input-sm' value="<?= date("m"); ?>">
                        </div>
                    </th>
                    <th>Year:</th>
                    <th>
                        <div class="col-xs-8 nopadding">
                            <input type="text" id='dateyear' name='dateyear' class='form-control input-sm' value="<?= date("Y"); ?>">
                        </div>
                    </th>
                </tr>
                <tr valign="top">
                    <th><button class="btn btn-success btn-block" id="btnExcel"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;To Excel</button></th>
                    <th>RDO Type: </th>
                    <th><input type="text" id='rdo' class='form-control input-sm' placeholder="RDO TYPE...." required></th>
                    <th colspan='4'>&nbsp;</th>
                </tr>
                <tr>
                    <th><button class="btn btn-info btn-block" id="btnDat"><i class="fa fa-file"></i>&nbsp;&nbsp;To DAT</button></th>
                    <th colspan='4'>&nbsp;</th>
                </tr>
            </table>
        </div>
    </div>

    <form action="Accounting/View_SalesDat.php" id='viewfrm' name='viewfrm' target="_blank" style='display: none'>
        <input type="text" id='viewmonth' name='viewmonth'>
        <input type="text" id='viewyear' name='viewyear'>
    </form>
    <form action="Export/salesDat.php" id='exportFrm' name='exportFrm' target="_blank" style='display: none'>
        <input type="text" id='exportRDO' name='exportRDO'>
        <input type="text" id='exportmonth' name='exportmonth'>
        <input type="text" id='exportyear' name='exportyear'>
    </form>
    <form action="toExcel/salesDat.php" id='xlsfrm' name='xlsfrm' target="_blank" style='display: none'>
        <input type="text" id='xlsmonth' name='xlsmonth'>
        <input type="text" id='xlsyear' name='xlsyear'>
    </form>
</body>
</html>
<script>

    $(document).ready(function(){

        $("#datemonth").datetimepicker({
            format: "MM",
        });
        $("#dateyear").datetimepicker({
            format: "YYYY",
        });
        $("#btnView").click(function(){
            let month = $("#datemonth").val();
            let year = $("#dateyear").val();

            $.ajax({
                url: "th_salesDat.php",
                data: { month: month, year: year},
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $("#viewmonth").val(month)
                        $("#viewyear").val(year)
                        $("#viewfrm").submit()
                    } else {
                        alert(res.msg)
                    }
                    console.log(res)
                },
                error: function(res){
                    console.log(res)
                }
            })
        })

        $("#btnDat").click(function(){
            let month = $("#datemonth").val();
            let year = $("#dateyear").val();
            let rdo = $("#rdo").val();
            if(rdo){
                $("#exportmonth").val(month).change();
                $("#exportyear").val(year).change();
                $("#exportRDO").val(rdo).change();
                $("#exportFrm").submit();
            } else {
                alert("RDO is Required")
            }
            
        })

        $("#btnExcel").click(function(){
            let month = $("#datemonth").val();
            let year = $("#dateyear").val();
            
            $.ajax({
                url: "th_salesDat.php",
                data: { month: month, year: year},
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $("#xlsmonth").val(month)
                        $("#xlsyear").val(year)
                        $("#xlsfrm").submit()
                    } else {
                        alert(res.msg)
                    }
                    console.log(res)
                },
                error: function(res){
                    console.log(res)
                }
            })
        })
        
    })

    
    
</script>