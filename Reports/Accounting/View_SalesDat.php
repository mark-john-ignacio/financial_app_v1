<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once  "../../vendor2/autoload.php";
    include ("../../Connection/connection_string.php");
    $company_code = $_SESSION['companyid'];
    $monthcut = $_REQUEST["viewmonth"];
    $yearcut = $_REQUEST['viewyear'];
    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);
    
    $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company_code' AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $monthcut AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $yearcut  AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            array_push($sales, $row);
        }
    }

    function Computation($transaction){
        global $con;
        global $company_code;
        $taxcode='';

        $exempt = 0; $zero = 0;  $gross = 0; $net = 0; $less = 0; $amount = 0;
        $sql = "SELECT  b.cvatcode,b.nnet, nvat, b.ngross, c.nrate FROM sales b 
                LEFT JOIN taxcode c on b.compcode=c.compcode and b.cvatcode=c.ctaxcode 
                WHERE b.compcode = '$company_code' AND b.ctranno = '$transaction'  AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled =0";
        $query = mysqli_query($con, $sql);
        while($row = $query -> fetch_assoc()){
            $taxcode = $row['cvatcode'];
            $gross = floatval($row['ngross']);

            if(floatval($row['nrate']) != 0 ){
                $net += floatval($row['nnet']);
                $less += floatval($row['nvat']);
                $amount += floatval($row['ngross']); 
            } else {
                $exempt += floatval($row['ngross']);
            }

        }
        switch($taxcode){
            case "VT":
                $gross = floatval($gross);
                $exempt = 0;
                $zero = 0;

                break;
            case "VE":
                $exempt = floatval($gross);
                $zero = 0;
                $gross = 0;
                $net = 0;
                $less = 0;
                break;
            case "ZR":
                $zero = floatval($gross);
                $exempt = 0;
                $gross = 0;
                $net = 0;
                $less = 0;
                break;
            default: 
            break;
        }
        

        return [
            'gross' => $amount,
            'exempt' => $exempt,
            'zero' => $zero,
            'taxable' => $net,
            'output' => $less,
            'gross_vat' => $gross
        ];

    }

    function isEmpty($sales){
        if(empty($sales)){
            return ;
        }
?>
            <table class='table'>
                <tr class='btn-info ' style='text-align: center'>
                    <th>Tax Payer <br>Month</th>
                    <th>Tax Payer <br>Indentification Number</th>
                    <th>Registered Name</th>
                    <th>Name of Customer <br>(Last Name, First Name, Middle Name)</th>
                    <th>Customer Address</th>
                    <th>Amount of Gross Sales</th>
                    <th>Amount of Excempt Sales</th>
                    <th>Amount of Zero Rated Sales</th>
                    <th>Amount of Taxable Sales</th>
                    <th>Amount of Output Tax</th>
                    <th>Amount of Gross Taxable Sales</th>
                </tr>
                <?php 
                    foreach($sales as $list):
                        $compute = Computation($list['ctranno']);
                        $fullAddress = str_replace(",", "", $list['chouseno']);
                        if(trim($list['ccity']) != ""){
                            $fullAddress .= " ". str_replace(",", "", $list['ccity']);
                        }
                        if(trim($list['cstate']) != ""){
                            $fullAddress .= " ". str_replace(",", "", $list['cstate']);
                        }
                        if(trim($list['ccountry']) != ""){
                            $fullAddress .= " ". str_replace(",", "", $list['ccountry']);
                        }
                ?>
                    <tr>
                        <td width='100px'><?= $list['dcutdate'] ?></td>
                        <td><?= $list['ctin'] ?></td>
                        <td><?= $list['ctradename'] ?></td>
                        <td>&nbsp;</td>
                        <td><?= $fullAddress ?></td>
                        <td align='right'><?= number_format($compute['gross'], 2) ?></td>
                        <td align='right'><?= number_format($compute['exempt'],2) ?></td>
                        <td align='right'><?= number_format($compute['zero'], 2) ?></td>
                        <td align='right'><?= number_format($compute['taxable'], 2) ?></td>
                        <td align='right'><?= number_format($compute['output'], 2) ?></td>
                        <td align='right'><?= number_format($list['ngross'],2) ?></td>
                    </tr>
                <?php endforeach;?>
            </table>
        <?php
    }?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

        <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
        <script src="../../Bootstrap/js/bootstrap.js"></script>
        <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
        <script src="../../Bootstrap/js/moment.js"></script>
        <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script> 
        <title>Document</title>
    </head>
    <body>
        <div class='container-fluid'>
            <h5>SALES TRANSACTION</h5>
            <h5>RECONCILIATION OF LISTING FOR ENFORCEMENT</h5>
            <br><br>
            <h5>TIN: <?= $company['comptin']?></h5>
            <h5>OWNER'S Name: <?= $company['compname'] ?></h5>
            <h5>OWNER'S TRADE NAME: <?= $company['compdesc'] ?></h5>
            <h5>OWNER'S ADDRESS: <?= $company['compadd'] ?></h5>
        </div>
        <div style='padding: 10px; padding-top: 20px;'>
            <?= isEmpty($sales);?>
        </div>
    </body>
    </html>
