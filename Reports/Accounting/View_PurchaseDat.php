<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once  "../../vendor2/autoload.php";
    include ("../../Connection/connection_string.php");
    require_once("../../Model/helper.php");
    $company_code = $_SESSION['companyid'];
    $monthcut = $_REQUEST["viewmonth"];
    $yearcut = $_REQUEST['viewyear'];
    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

    // $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry FROM apv a 
    // LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
    // LEFT JOIN (
    //     SELECT DISTINCT(a.ctranno), a.cvatcode, a.compcode from apv_d a
    //         LEFT JOIN apv b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    //         WHERE a.compcode ='$company_code' 
    //         AND MONTH(STR_TO_DATE(b.dapvdate, '%Y-%m-%d')) = $monthcut 
    //         AND YEAR(STR_TO_DATE(b.dapvdate, '%Y-%m-%d')) = $yearcut 
    //         AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled =0 
    //         AND a.ctranno in (
    //             SELECT capvno FROM paybill a 
    //             LEFT JOIN paybill_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    //         )
    //     ) c on a.compcode = c.compcode AND a.ctranno = c.ctranno
    // WHERE a.compcode ='$company_code' 
    // AND MONTH(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $monthcut 
    // AND YEAR(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $yearcut 
    // AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled =0 
    // -- AND c.cvatcode <> 'NT'
    // AND a.ctranno in (
    //     SELECT capvno FROM paybill a 
    //     LEFT JOIN paybill_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    // )";

    $sql = "SELECT a.*, b.* FROM paybill a
            LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
            WHERE a.compcode = '$company_code'
            AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
            AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            array_push($sales, $row);
        }
    }
    function isEmpty($sales){
        if(empty($sales)){
            return ;
        }
?>
            <table class='table'>
                <tr class='btn-primary ' style='text-align: center'>
                    <th>Tax Payer <br>Month</th>
                    <th>Tax Payer <br>Indentification Number</th>
                    <th>Registered Name</th>
                    <th>Name of Customer <br>(Last Name, First Name, Middle Name)</th>
                    <th>Customer Address</th>
                    <th>AMOUNT of Gross Purchase</th>
                    <th>AMOUNT of Excempt Purchase</th>
                    <th>AMOUNT of Zero Rated Purchase</th>
                    <th>AMOUNT of Taxable Purchase</th>
                    <th>AMOUNT of PURCHASE SERIVICES</th>
                    <th>AMOUNT OF PURCHASE CAPITAL GOODS</th>
                    <th>AMOUNT OF PURCHASE GOODS OTHER THAN CAPITAL GOODS</th>
                    <th>AMOUNT of Input Tax</th>
                    <th>AMOUNT of Gross Taxable Purchase</th>
                </tr>
                <?php 
                    foreach($sales as $list):
                        $compute = ComputePaybills($list);
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
                        <td width='100px'><?= $list['dcheckdate'] ?></td>
                        <td><?= $list['ctin'] ?></td>
                        <td><?= $list['ctradename'] ?></td>
                        <td>&nbsp;</td>
                        <td><?= $fullAddress ?></td>
                        <td align='right'><?= number_format($compute['gross'], 2) ?></td>
                        <td align='right'><?= number_format($compute['exempt'],2) ?></td>
                        <td align='right'><?= number_format($compute['zero'], 2) ?></td>
                        <td align='right'><?= number_format($compute['net'], 2) ?></td>

                        <td align='right'><?= number_format($compute['service'], 2) ?></td>
                        <td align='right'><?= number_format($compute['capital'],2) ?></td>
                        <td align='right'><?= number_format($compute['goods'], 2) ?></td>

                        <td align='right'><?= number_format($compute['vat'], 2) ?></td>
                        <td align='right'><?= number_format($compute['gross_vat'],2) ?></td>
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
        <title>MyxFinancials</title>
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
