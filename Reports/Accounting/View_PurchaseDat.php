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
    $code = $_REQUEST['viewVat'];
    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);

    $sql = "SELECT a.cacctno FROM accounts_default a WHERE a.compcode = '$company_code' AND a.ccode = 'PURCH_VAT' ORDER BY a.cacctno DESC LIMIT 1";
    $query = mysqli_query($con, $sql);
    $account = $query -> fetch_array(MYSQLI_ASSOC);
    $vat_code = $account['cacctno'];

    //getallapv with input tax
    $allapvno = array();
    $sql = "SELECT ctranno FROM glactivity WHERE compcode = '$company_code' AND acctno = '$vat_code' and MONTH(ddate)=$monthcut and YEAR(ddate)=$yearcut";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $allapvno[] = $row['ctranno'];
        }
    }

    //getall paybill No. based from apvs
    $allpaybill = array();
    $sql = "SELECT ctranno FROM paybill_t WHERE compcode = '$company_code' AND capvno in ('".implode("','",$allapvno)."')";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $allpaybill[] = $row['ctranno'];
        }
    }

    $sql = "SELECT a.*, b.* FROM paybill a
    LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
    WHERE a.compcode = '$company_code' AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1) and a.ctranno in ('".implode("','",$allpaybill)."')";

    echo $sql;

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
        $TOTAL_GROSS = 0;
        $TOTAL_NET = 0;
        $TOTAL_VAT = 0;
        $TOTAL_EXEMPT = 0;
        $TOTAL_ZERO_RATED = 0;
        $TOTAL_GOODS = 0;
        $TOTAL_SERVICE = 0;
        $TOTAL_CAPITAL = 0;
        $TOTAL_TAX_GROSS = 0;
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
                        
                        $TOTAL_GROSS += floatval($compute['gross']);
                        $TOTAL_NET += floatval($compute['net']);
                        $TOTAL_VAT += floatval($compute['vat']);
                        $TOTAL_EXEMPT += floatval($compute['exempt']);
                        $TOTAL_ZERO_RATED += floatval($compute['zero']);
                        $TOTAL_GOODS += floatval($compute['goods']);
                        $TOTAL_SERVICE += floatval($compute['service']);
                        $TOTAL_CAPITAL += floatval($compute['capital']);
                        $TOTAL_TAX_GROSS += floatval($compute['gross_vat']);
                ?>
                    <tr>
                        <td width='100px'><?= $list['dcheckdate'] ?></td>
                        <td><?= substr($list['ctin'],0,11) ?></td>
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
                <tr>
                    <td colspan='5' style='font-weight: bold'>GRAND TOTAL</td>
                    <td align='right'><?= number_format($TOTAL_GROSS,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_EXEMPT,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_ZERO_RATED,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_NET,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_SERVICE,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_CAPITAL,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_GOODS,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_VAT,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_TAX_GROSS,2) ?></td>
                </tr>
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
            <h5>TIN: <?= substr($company['comptin'],0,11)?></h5>
            <h5>OWNER'S Name: <?= $company['compname'] ?></h5>
            <h5>OWNER'S TRADE NAME: <?= $company['compdesc'] ?></h5>
            <h5>OWNER'S ADDRESS: <?= $company['compadd'] ?></h5>
        </div>
        <div style='padding: 10px; padding-top: 20px;'>
            <?= isEmpty($sales);?>
        </div>
    </body>
    </html>
