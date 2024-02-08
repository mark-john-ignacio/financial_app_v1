<?php
    if(!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['pageid'] = "BIRSAWT";

    include("../../../Connection/connection_string.php");
    include('../../../include/denied.php');
    include('../../../include/access.php');
    include('../../../Model/helper.php');

    $company = $_SESSION['companyid'];

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    $list = $query -> fetch_assoc();
    $company_detail = [
        'name' => $list['compname'],
        'trade' => $list['compdesc'],
        'address' => $list['compadd'],
        'tin' => TinValidation($list['comptin'])
    ];

    $month = date("m", strtotime($_POST['months']));
    $year = $_POST['years'];

    $sql = "SELECT a.cewtcode, a.ctranno, b.ngross, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc FROM sales_t a
        LEFT JOIN sales b on a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN customers c on a.compcode = c.compcode AND b.ccode = c.cempid
        LEFT JOIN groupings d on a.compcode = b.compcode AND c.ccustomertype = d.ccode
        WHERE a.compcode = '$company' AND MONTH(b.dcutdate) = '$month' AND YEAR(b.dcutdate) = '$year' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND d.ctype = 'CUSTYP'";

    $query = mysqli_query($con, $sql);

    $array = array();

    while($list = $query -> fetch_assoc()) {
        $code = $list['cewtcode'];
        $ewt = getEWT($code);

        if (ValidateEWT($code) && $ewt['valid']) {
            $json = array(
                'name' => $list['cname'],
                'address' => $list['chouseno'] . " " . $list['ccity'],
                'tin' => $list['ctin'],
                'tranno' => $list['ctranno'],
                'gross' => $list['ngross'],
                'credit' => floatval($list['ngross']) * (floatval($ewt['rate']) / 100),
                'date' => $list['dcutdate'],
                'ewt' => $ewt['code'],
                'rate' => $ewt['rate']
            );
            $array[] = $json;
        }
    }

   // print_r($array);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap-datetimepicker.css">


    <script src="../../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../../include/autoNumeric.js"></script>

    <script src="../../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../../Bootstrap/js/moment.js"></script>
    <script src="../../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>
    <div style="padding: 10px;">
        <table width="100%" border=0 cellpadding="3px">
            <tr>
                <td><h4 style="margin: 0">SUMMARY ALPHALIST OF WITHHOLDING TAXES (SAWT)</h4></td>
            </tr>
            <tr>
                <td><h4  style="margin: 0">FOR THE MONTH OF <?=$_POST['months']?>, <?=$_POST['years']?></h4></td>
            </tr>

            <tr>
                <td style="padding-top: 10px"><h4  style="margin: 0">TIN: <?=$company_detail['tin']?> </h4></td>
            </tr>
            <tr>
                <td style="padding-bottom: 20px"><h4  style="margin: 0">PAYEE'S NAME: <?=$company_detail['name']?></h4></td>
            </tr>
        </table>

        <table class="table table-sm" id="QAPList" style="font-size: 11px !important">
            <thead>
                <tr>
                    <th>TRANSACTION DATE</th>
                    <th>CV REFERENCE NO.</th>
                    <th>VENDOR TIN</th>
                    <th>VENDOR NAME</th>
                    <th>VENDOR ADDRESS</th>
                    <th>W/TAX CODE</th>
                    <th>W/TAX RATE</th>
                    <th>W/TAX BASE AMOUNT</th>
                    <th>W/TAX AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($array as $rs2){
                ?>
                    <tr>
                        <td> <?=$rs2['date']?> </td>
                        <td> <?=$rs2['tranno']?> </td>
                        <td> <?=$rs2['tin']?> </td>
                        <td> <?=$rs2['name']?> </td>
                        <td> <?=$rs2['address']?> </td>
                        <td> <?=$rs2['ewt']?> </td>
                        <td> <?=floatval($rs2['rate'] / 100) . "%"?> </td>
                        <td> <?=number_format($rs2['gross'],2)?> </td>
                        <td> <?=number_format($rs2['credit'],2)?> </td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<script>

    function fetchSAWT(){
        let month = $("#months").val();
        let year = $("#years").val();

        $.ajax({
            url: "../SAWT_LIST/",
            type: "post",
            data: {
                months: month,
                years: year,
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
        display()
    }

    function display () {
        $("#List tbody").empty();

        let TOTAL_CREDIT = 0;
        let TOTAL_GROSS = 0;
        sawt.map((item, index) => { 
            TOTAL_CREDIT += parseFloat(item.credit);
            TOTAL_GROSS += parseFloat(item.gross);

            $("<tr>").append(
                $("<td>").text(item.date),
                $("<td>").text(item.tranno),
                $("<td>").text(item.tin),
                $("<td>").text(item.name),
                $("<td>").text(item.address),
                $("<td>").text(item.ewt),
                $("<td>").text((item.rate / 100) + "%"),
                $("<td>").text(parseFloat(item.gross).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')),
                $("<td>").text(parseFloat(item.credit).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')),
            ).appendTo("#List tbody")
        })

    }
</script>