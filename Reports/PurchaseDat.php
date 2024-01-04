<?php 
    if(!isset($_SESSION)){
        session_start();
    }
     $_SESSION['pageid'] = "PurchaseDat.php";
    include("../Connection/connection_string.php");
     include('../include/denied.php');
     include('../include/access.php');
    $company = $_SESSION['companyid'];
    
    $sql = "SELECT * FROM vatcode WHERE compcode = '$company' AND cstatus = 'ACTIVE' and ctype='PURCHASE'";
    $query = mysqli_query($con, $sql);

    $sql = "select * From company where compcode='$company'";
    $result=mysqli_query($con,$sql);
    
    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $comprdo = $row['comprdo'];
    }

    @$rdocodes = array();
    $sqlhead=mysqli_query($con,"Select * from rdocodes");
    if (mysqli_num_rows($sqlhead)!=0) {
        while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
            @$rdocodes[] = array("ccode" => $row['ccode'], "cdesc" => $row['cdesc']); 
        }
    }
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
            <font size="+1">Purchase Generate DAT</font>
        </div>
        <div class='container' style='padding-top: 50px'>
            <table>
                <tr>
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
                <tr>
                    <th><button class="btn btn-success btn-block" id="btnExcel"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;To Excel</button></th>
                    <th>Tax Type: </th>
                    <th>
                        <select name="vatcode" id="vatcode" class='form-control input-sm'>
                            <option value="">-ALL-</option>
                            <?php while($row = $query -> fetch_assoc()): ?>
                                <option value="<?= $row['cvatcode'] ?>"><?= $row['cvatdesc'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </th>
                    <th colspan='2'>&nbsp;</th>
                </tr>
                <tr>
                    <th><button class="btn btn-info btn-block" id="btnDat"><i class="fa fa-file"></i>&nbsp;&nbsp;To DAT</button></th>
                    <th>RDO Code: </th>
                    <th colspan='3'>

                        <select class="form-control input-sm" name="rdo" id="rdo">
                            <?php
                                $isslc = "";
                                foreach(@$rdocodes as $rx){
                                    if($comprdo==$rx['ccode']){
                                        $isslc = " selected ";
                                    }else{
                                        $isslc = "";
                                    }
                            ?>
                            <option value="<?=$rx['ccode']?>"<?=$isslc?>> <?=$rx['ccode'].": ".$rx['cdesc']?> </option>
                            <?php
                                }
                            ?>
                        </select>
                    </th>

                </tr>
            </table>
        </div>
    </div>

    <form action="Accounting/View_PurchaseDat.php" id='viewfrm' name='viewfrm' target="_blank" style='display: none'>
        <input type="text" id='viewVat' name='viewVat'>
        <input type="text" id='viewmonth' name='viewmonth'>
        <input type="text" id='viewyear' name='viewyear'>
    </form>
    <form action="Export/PurchaseDat.php" id='exportFrm' name='exportFrm' target="_blank" style='display: none'>
        <input type="text" id='exportRDO' name='exportRDO'>
        <input type="text" id='exportVat' name='exportVat'>
        <input type="text" id='exportmonth' name='exportmonth'>
        <input type="text" id='exportyear' name='exportyear'>
    </form>
    <form action="toExcel/PurchaseDat.php" id='xlsfrm' name='xlsfrm' target="_blank" style='display: none'>
        <input type="text" id='xlsVat' name='xlsVat'>
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
            let vatcode = $("#vatcode").val();

            $.ajax({
                url: "th_PurchaseDat.php",
                data: { month: month, year: year, vatcode: vatcode },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $("#viewmonth").val(month)
                        $("#viewyear").val(year)
                        $('#viewVat').val(vatcode)
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

        $("#btnExcel").click(function(){
            let month = $("#datemonth").val();
            let year = $("#dateyear").val();
            let vatcode = $("#vatcode").val();
            let rdo = $("#rdo").val();
            
            $.ajax({
                url: "th_PurchaseDat.php",
                data: { month: month, year: year, vatcode: vatcode},
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $("#xlsmonth").val(month)
                        $("#xlsyear").val(year)
                        $("#xlsVat").val(vatcode)
                        $("#xlsfrm").submit()
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
            let month = $("#datemonth").val();
            let year = $("#dateyear").val();
            let vatcode = $("#vatcode").val();
            let rdo = $("#rdo").val();

            $.ajax({
                url: "th_PurchaseDat.php",
                data: { month: month, year: year, vatcode: vatcode },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        if(rdo){
                            $("#exportmonth").val(month);
                            $("#exportyear").val(year)
                            $("#exportVat").val(vatcode)
                            $("#exportRDO").val(rdo)
                            $("#exportFrm").submit()
                        } else {
                            alert("RDO is required")
                        }
                        
                    } else {
                        alert(res.msg)
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })
        })
    })
</script>