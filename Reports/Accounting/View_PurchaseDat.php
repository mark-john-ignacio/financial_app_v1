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
    $apventry = array();
    $sql = "SELECT A.cmodule, A.ctranno, A.ctaxcode, B.nrate, A.ndebit, A.ncredit FROM glactivity A left join vatcode B on A.compcode=B.compcode and A.ctaxcode=B.cvatcode WHERE A.compcode = '$company_code' AND A.acctno = '$vat_code' and MONTH(A.ddate)=$monthcut and YEAR(A.ddate)=$yearcut";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $allapvno[] = $row['ctranno'];   
            $apventry[$row['ctranno']] = $row;    
        }
    }

    $suppliersdet = array();
    $sql = "SELECT * FROM suppliers WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $suppliersdet[$row['ccode']] = $row;
        }
    }

    //getall apv with payment
  /*  $allapvpaid = array();
    $sql = "SELECT A.ctranno, A.capvno FROM paybill_t A left join paybill B on A.compcode=B.compcode and A.ctranno=B.ctranno WHERE A.compcode = '$company_code' AND A.capvno in ('".implode("','",$allapvno)."') AND (B.lapproved = 1 AND B.lvoid = 0)";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            $allapvpaid[] = $row['capvno'];
        }
    } */


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
                    /*$sql = "SELECT A.ccode, A.chouseno, A.ccity, A.cstate, A.ccountry, A.ctin, A.cname 
                    From
                    (
                    SELECT A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.dapvdate as ddate
                    FROM apv A 
                    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
                    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
                    
                    UNION ALL
                    
                    SELECT A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.djdate as ddate 
                    FROM journal A 
                    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
                    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
                    ) A
                    Order by A.ddate, A.cname";*/

                    $sql = " SELECT A.ctranno, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.dapvdate as ddate
                    FROM apv A 
                    left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
                    WHERE A.compcode = '$company_code' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0)";

                    $query = mysqli_query($con, $sql);
                    if(mysqli_num_rows($query) != 0){
                        
                        while($row = $query -> fetch_assoc()){

                            $fullAddress = str_replace(",", "", $row['chouseno']);
                            if(trim($row['ccity']) != ""){
                                $fullAddress .= " ". str_replace(",", "", $row['ccity']);
                            }
                            if(trim($row['cstate']) != ""){
                                $fullAddress .= " ". str_replace(",", "", $row['cstate']);
                            }
                            if(trim($row['ccountry']) != ""){
                                $fullAddress .= " ". str_replace(",", "", $row['ccountry']);
                            }
                        
                            $xcnet = 0;
                            $xcvat = 0;
                            $xczerotot = 0;
                            $xcexmpt = 0;
                            $xservc = 0;
                            $xsgoods = 0;
                            $xsgoodsother = 0;

                            if($apventry[$row['ctranno']]['nrate']>0){
                                $xcnet = floatval($apventry[$row['ctranno']]['ndebit']) / (floatval($apventry[$row['ctranno']]['nrate'])/100);
                                $xcvat = $apventry[$row['ctranno']]['ndebit'];

                                if($apventry[$row['ctranno']]['ctaxcode']=="VTSDOM" || $apventry[$row['ctranno']]['ctaxcode'] == "VTSNR"){
                                    $xservc = $xcnet;
                                }

                                if($apventry[$row['ctranno']]['ctaxcode']=="VTGE1M" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGNE1M"){
                                    $xsgoods = $xcnet;
                                }

                                if($apventry[$row['ctranno']]['ctaxcode']=="VTGIMOCG" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGOCG"){
                                    $xsgoodsother = $xcnet;
                                }
                            }

                            if($apventry[$row['ctranno']]['ctaxcode']=="VTZERO"){
                                $xczerotot = floatval($row['ngross']);
                            }

                            if($apventry[$row['ctranno']]['ctaxcode']=="VTNOTQ"){
                                $xcexmpt = floatval($row['ngross']);
                            }

                            $TOTAL_GROSS += floatval($row['ngross']);
                            $TOTAL_NET += floatval($xcnet);
                            $TOTAL_VAT += floatval($xcvat);
                            $TOTAL_EXEMPT += floatval($xcexmpt);
                            $TOTAL_ZERO_RATED += floatval($xczerotot);
                            $TOTAL_GOODS += floatval($xsgoods);
                            $TOTAL_SERVICE += floatval($xservc);
                            $TOTAL_CAPITAL += floatval($xsgoodsother);
                            $TOTAL_TAX_GROSS += floatval($xcnet) + floatval($xcvat);
                ?>
                    <tr>
                        <td width='100px'><?= $row['dapvdate'] ?></td>
                        <td><?= substr($row['ctin'],0,11) ?></td>
                        <td><?= $row['cname'] ?></td>
                        <td>&nbsp;</td>
                        <td><?= $fullAddress ?></td>
                        <td align='right'><?= number_format($row['ngross'], 2) ?></td>
                        <td align='right'><?= number_format($xcexmpt,2) ?></td>
                        <td align='right'><?= number_format($xczerotot, 2) ?></td>
                        <td align='right'><?= number_format($xcnet, 2) ?></td>

                        <td align='right'><?= number_format($xservc, 2) ?></td>
                        <td align='right'><?= number_format($xsgoods,2) ?></td>
                        <td align='right'><?= number_format($xsgoodsother, 2) ?></td>

                        <td align='right'><?= number_format($xcvat, 2) ?></td>
                        <td align='right'><?= number_format((floatval($xcnet) + floatval($xcvat)),2) ?></td>
                    </tr>
                <?php 
                        }
                    }
                ?>
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

        </div>
    </body>
    </html>
