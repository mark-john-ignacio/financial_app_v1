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

    if($code == 'VT'){
        $sql = "SELECT a.*, b.* FROM paybill a
        LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
        WHERE a.compcode = '$company_code'
        AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
        AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
        AND b.cvattype = '$code'
        AND ctranno in (
            SELECT a.ctranno FROM paybill_t a 
            LEFT JOIN apv_t b on a.compcode = b.compcode AND a.capvno = b.ctranno
            WHERE a.compcode = '$company_code' AND b.cacctno = '$vat_code'
        )
        AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    } else {
        $sql = "SELECT a.*, b.* FROM paybill a
        LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
        WHERE a.compcode = '$company_code'
        AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
        AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
        AND b.cvattype = '$code'
        AND ctranno in (
            SELECT a.ctranno FROM paybill_t a 
            WHERE a.compcode = '$company_code' 
        )
        AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    }
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
                    <th>AMOUNT of Exempt Purchase</th>
                    <th>AMOUNT of Zero Rated Purchase</th>
                    <th>AMOUNT of Taxable Purchase</th>
                    <th>AMOUNT of PURCHASE SERVICES</th>
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
                    <td><a href="javascript:;" onclick="show_transaction(<?php echo htmlspecialchars(json_encode($list), ENT_QUOTES, 'UTF-8'); ?>)"><?= $list['ctranno'] ?></a></td>
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

        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class='modal-header'>
                        <button type="button" class="close" data-dismiss="modal">

                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h5><b><i><span id='modalTitle'></span></i></b></h5>
                    </div>
                    <div class="modal-body">
                        <table class="table" id='compute'>
                            <thead style="background-color: #2d5f8b">
                                <tr>
                                    <th>Amount of Gross</th>
                                    <th>Amount of Exempt</th>
                                    <th>Amount of Zero Rated</th>
                                    <th>Amount of Taxable</th>
                                    <th>Amount of Service</th>
                                    <th>Amount of Capital Goods</th>
                                    <th>Amount of Goods Other Than Capital Goods</th>
                                    <th>Amount of Input Tax</th>
                                    <th>Amount of Gross Taxable</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>

    <script>
        $(document).ready(function(){
            
        })

        function show_transaction(data){
            $("compute tbody").empty()
            let PROCUREMENT = data['procurement'];
            let business = data['cvattype'];
            let TOTAL_GROSS = 0;
            let TOTAL_EXEMPT = 0;
            let TOTAL_ZERO_RATED = 0;
            let TOTAL_NET = 0;
            let TOTAL_VAT = 0;
            let TOTAL_TAX_GROSS = 0;
            let TOTAL_GOODS = 0;
            let TOTAL_SERVICE = 0;
            let TOTAL_CAPITAL = 0;

        amount = data['npaid'];
        gross = data['ngross'];

        net = parseFloat(amount) / 1.12;
        vat = parseFloat(net) * 0.12;

        TOTAL_GROSS += parseFloat(amount);
        // $TOTAL_NET += $net;
        // $TOTAL_VAT += $vat;
        // $TOTAL_TAX_GROSS += floatval($amount);

        switch(business){
            case "VT":
                TOTAL_NET += parseFloat(net);
                TOTAL_VAT += parseFloat(vat);
                TOTAL_TAX_GROSS += parseFloat(amount);

                break;
            case "NV":
                TOTAL_NET += parseFloat(net);
                TOTAL_VAT += parseFloat(vat);
                TOTAL_TAX_GROSS += parseFloat(amount);
                break;
            case "VE":
                TOTAL_EXEMPT += parseFloat(amount);
                break;
            case "ZR":
                TOTAL_ZERO_RATED += parseFloat(amount);
                break;
            default:
                break;
        }


        switch(PROCUREMENT){
            case "Goods":
                TOTAL_GOODS += TOTAL_NET;
                break;
            case "Services":
                TOTAL_SERVICE += TOTAL_NET;
                break;
            case "Capital":
                TOTAL_CAPITAL += TOTAL_NET;
                break;
            default: 
            break;
        }
        
        
            $("<tr>").append(
                $("<td>").text(parseFloat(TOTAL_GROSS).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_EXEMPT).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_ZERO_RATED).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_NET).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_SERVICE).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_CAPITAL).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_GOODS).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_VAT).toFixed(2)),
                $("<td>").text(parseFloat(TOTAL_TAX_GROSS).toFixed(2))
            ).appendTo("#compute tbody")
            $("#detailModal").modal("show")
        }
    </script>