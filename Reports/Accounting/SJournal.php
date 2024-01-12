<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once  "../../vendor2/autoload.php";
    include ("../../Connection/connection_string.php");
    require_once("../../Model/helper.php");
    $company_code = $_SESSION['companyid'];

    $date1 = $_REQUEST["date1"];
    $date2 = $_REQUEST['date2'];
    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);
    
    $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry, b.cvattype 
    FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company_code' 
    AND a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')  
    AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0
    AND a.ctranno in (
        SELECT b.csalesno FROM receipt a 
        left join receipt_sales_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                    WHERE a.compcode = '$company_code' 
                    AND a.lapproved = 1 
                    AND a.lvoid = 0 
                    AND a.lcancelled = 0
    )";

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
        $TOTAL_TAX_GROSS = 0;
        $TOTAL_DISCS = 0;
?>
            <table class='table table-condensed' width="100%" border="0" align="center" cellpadding = "1">
                <tr>
                    <th>Date</th>
                    <th>Customer's TIN</th>
                    <th>Customer's Name</th>
                    <th>Address</th>
                    <th>Description</th>
                    <th>Sales Invoice No.</th>
                    <th>Amount</th>
                    <th>Discount</th>
                    <th>VAT Amount</th>
                    <th>Net Sales</th>
                </tr>
                <?php 
                    foreach($sales as $list):
                        $compute = ComputeRST($list);
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
                        $TOTAL_NET += (floatval($compute['vat'])==0) ? floatval($compute['gross']) : floatval($compute['net']);
                        $TOTAL_VAT += floatval($compute['vat']);
                        $TOTAL_DISCS += floatval($compute['total_discount']);
                ?>
                    <tr>
                        <td width='100px'><?= $list['dcutdate'] ?></td>
                        <td><?= substr($list['ctin'],0,11) ?></td>
                        <td><?= $list['ctradename'] ?></td>
                        <td><?= $fullAddress ?></td>
                        <td><?= $list['cremarks'] ?></td>
                        <td><?= $list['csiprintno'] ?></td>
                        <td align='right'><?= number_format($compute['gross'], 2) ?></td>
                        <td align='right'><?= number_format($compute['total_discount'], 2) ?></td>                      
                        <td align='right'><?= number_format($compute['vat'], 2) ?></td>
                        <td align='right'><?=(floatval($compute['vat'])==0) ? number_format($compute['gross'], 2) : number_format($compute['net'], 2)  ?></td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan='6' style='font-weight: bold'>GRAND TOTAL</td>
                    <td align='right'><?= number_format($TOTAL_GROSS,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_DISCS,2) ?></td>               
                    <td align='right'><?= number_format($TOTAL_VAT,2) ?></td>
                    <td align='right'><?= number_format($TOTAL_NET,2) ?></td>
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
        <link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">

        <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
        <script src="../../Bootstrap/js/bootstrap.js"></script>

        <title>MyxFinancials</title>
    </head>
    <body style="padding:10px">


            <h3><b>Company: <?=strtoupper($company['compname']);  ?></b></h3>
            <h3><b>Company Address: <?php echo strtoupper($company['compadd']);  ?></b></h3>
            <h3><b>Vat Registered Tin: <?php echo $company['comptin'];  ?></b></h3>
            <h3><b>Kind of Book: SALES JOURNAL BOOK</b></h3>
            <h3><b>For the Period <?php echo date_format(date_create($date1),"F d, Y");?> to <?php echo date_format(date_create($date2),"F d, Y");?></b></h3>

        <div style='padding: 10px; padding-top: 20px;'>
            <?= isEmpty($sales);?>
        </div>
    </body>
    </html>
