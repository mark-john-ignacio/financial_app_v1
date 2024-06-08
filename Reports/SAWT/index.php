<?php 
    if(!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['pageid'] = "BIRSAWT";

    include("../../Connection/connection_string.php");
    include('../../include/denied.php');
    include('../../include/access.php');

    $company = $_SESSION['companyid'];

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
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/autoNumeric.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <style>
        th, td {
            padding-top: 2px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 2px;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>
        <div style="text-align: center; font-weight: bold; text-decoration: underline;">
            <font size="+1">Summary Alphalist of Withholding Tax at Source</font>
        </div>
        <div class='container' style='padding-top: 50px'>
            <form action="" method="post" id="SAWTForm" enctype="multipart/form-data" target="_blank">
                <table>
                    <tr valign="top">
                        <th><button type="button" class='btn btn-danger btn-block' id="btnView" onclick="btnonclick.call(this)" value="VIEW"><i class='fa fa-search'></i>&nbsp;&nbsp;View Report</button></th>
                        <th width='100px' valign="middle">Quarter:</th>
                        <th>
                            <div class="col-xs-12 nopadding">
                                <?php
                                    $curMonth = date("m", time());
                                    $curQuarter = ceil($curMonth/3);
                                ?>
                                <select class="form-control input-sm" name="selqrtr" id="selqrtr">
                                    <option value="1"<?=($curQuarter==1) ? " selected": ""?>>1st Quarter</option>
                                    <option value="2"<?=($curQuarter==2) ? " selected": ""?>>2nd Quarter</option>
                                    <option value="3"<?=($curQuarter==3) ? " selected": ""?>>3rd Quarter</option>
                                    <option value="4"<?=($curQuarter==4) ? " selected": ""?>>4th Quarter</option>
                                </select>
                            </div>
                        </th>
                        <th valign="middle">Year:</th>
                        <th>
                            <div class="col-xs-10 nopadding">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id='years' name='years' class='yearpicker form-control input-sm' value="<?= date("Y"); ?>">
                                </div>
                                
                            </div>
                        </th>
                    </tr>
                    <tr valign="top">
                        <th><button type="button" class="btn btn-success btn-block" id="btnExcel" onclick="btnonclick.call(this)" value="CSV"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;To Excel</button></th>
                        <th valign="middle">RDO Type: </th>
                        <th>
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
                    <tr>
                        <th><button type="button" class="btn btn-info btn-block" id="btnDat" onclick="btnonclick.call(this)" value="DAT"><i class="fa fa-file"></i>&nbsp;&nbsp;To DAT</button></th>
                        <th colspan='4'>&nbsp;</th>
                    </tr>
                </table>
            </form>
        </div>
</body>
</html>

<script type="text/javascript">
    var sawt = [];
    $(document).ready(function(){
        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        })

        $(".monthpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'MMMM'
        })

        // FetchAPV();
    })

    function btnonclick() {
        let type = $(this).val();
        let rdo = $("#rdo").val();
        var form = document.getElementById('SAWTForm');
        var formData = new FormData(form);

        if(sawt.length != 0) {
            return alert("No Reference found");
        }
        
        if(rdo == ""){ 
            return alert("No RDO found please! Fill this detail!");
        }

        switch (type) {
            case "CSV":
                newAction = "./TO_CSV/";
                break;
            case "DAT":
                newAction = "./TO_DAT/";
                break;
            case "VIEW":
                newAction = "./TO_VIEW/";
                break;
        }
        form.action = newAction;
        // console.log(form)
        form.submit();
    }

    function fetchSAWT() {
        let month = $("#months").val();
        let year = $("#years").val();

        $.ajax({
            url: "./SAWT_LIST/",
            type: "post",
            data: {
                month: month,
                year: year
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    sawt = res.data;
                } else {
                    sawt.length = 0;
                    sawt = [];
                    console.log(res.msg)
                }
                $("#trade").text(res.company.trade);
                $("#company").text(res.company.name);
                $("#tin").text(res.company.tin);
                $("#address").text(res.company.address);
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }
</script>