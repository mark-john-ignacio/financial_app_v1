<?php
    if(!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['pageid'] = "BIRQAP";

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

    $month_text = $_POST['months'];
    $month = date("m", strtotime($_POST['months']));
    $year = date("Y", strtotime($_POST['years']));

    $quartersAndMonths = getQuartersAndMonths($year);
    $apv = array();
    foreach ($quartersAndMonths as $quarter => $month) {

        $QUARTERDATA = dataquarterly($month);

        if ($QUARTERDATA['valid']) {
            foreach($QUARTERDATA['quarter'] as $row) {
                $list = $row['data'];
                $code = $list['cewtcode'];
                $credit = $list['ncredit'];
                $gross = $list['ngross'];
                $ewt = getEWT($code);

                if (ValidateEWT($code) && $credit != 0 && $ewt['valid']) {
                    $json = array(
                        'name' => $list['cname'],
                        'tin' => $list['ctin'],
                        'credit' => $credit,
                        'ewt' => $ewt['code'],
                        'rate' => $ewt['rate'],
                        'date' => $list['dapvdate'],
                        'tranno' => $list['ctranno'],
                        'address' => $list['chouseno'] . " " . $list['ccity'],
                        'gross' => $list['ngross']
                    );
    
                    $apv[] = $json;
                }
               
            }
        }
        
    }

   // print_r($apv);

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
                <td><h4 style="margin: 0">QUARTERLY ALPHABETICAL LIST OF PAYEES SUBJECTED TO EXPANDED WITHHOLDING TAX & PAYEES WHOSE INCOME PAYMENTS ARE EXEMPT</h4></td>
            </tr>
            <tr>
                <td><h4  style="margin: 0">FOR THE QUARTER ENDING <?=$_POST['months']?>, <?=$_POST['years']?></h4></td>
            </tr>

            <tr>
                <td style="padding-top: 10px"><h4  style="margin: 0">TIN: <?=$company_detail['tin']?> </h4></td>
            </tr>
            <tr>
                <td style="padding-bottom: 20px"><h4  style="margin: 0">WITHHOLDING AGENT'S NAME: <?=$company_detail['name']?></h4></td>
            </tr>
        </table>

        <table class="table table-sm" id="QAPList" style="font-size: 11px !important">
            <thead>
                <tr>
                    <th>TRANSACTION DATE</th>
                    <th style="padding-left: 10px">CV REFERENCE NO.</th>
                    <th style="padding-left: 10px">VENDOR TIN</th>
                    <th style="padding-left: 10px">VENDOR NAME</th>
                    <th style="padding-left: 10px">VENDOR ADDRESS</th>
                    <th style="padding-left: 10px">W/TAX CODE</th>
                    <th style="padding-left: 10px">W/TAX RATE</th>
                    <th style="padding-left: 10px">W/TAX BASE AMOUNT</th>
                    <th style="padding-left: 10px">W/TAX AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($apv as $rs2){
                ?>
                <tr>
                    <td><?=$rs2['date']?></th>
                    <td style="padding-left: 10px"><?=$rs2['tranno']?></th>
                    <td style="padding-left: 10px"><?=$rs2['tin']?></th>
                    <td style="padding-left: 10px"><?=$rs2['name']?></th>
                    <td style="padding-left: 10px"><?=$rs2['address']?></th>
                    <td style="padding-left: 10px"><?=$rs2['ewt']?></th>
                    <td style="padding-left: 10px"><?=(floatval($rs2['rate']) / 100) . "%"?></th>
                    <td style="padding-left: 10px"><?=number_format($rs2['gross'],2)?></th>
                    <td style="padding-left: 10px"><?=number_format($rs2['credit'],2)?></th>
                </tr>
                <?php
                    }
                ?>

            </tbody>
        </table>

    </div>
</body>
</html>
