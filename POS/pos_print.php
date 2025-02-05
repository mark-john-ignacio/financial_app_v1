<?php

// use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

    if(!isset($_SESSION)){
        session_start();
    }
    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $tranno = mysqli_real_escape_string($con, $_REQUEST['tranno']);

    $detail= [];
    $items = [];
    
    $sql = "SELECT * FROM parameters WHERE compcode = '$company' AND ccode = 'BASE_CUSTOMER_POS'";
    $query = mysqli_query($con, $sql);
    $fetch = $query -> fetch_assoc();
    $default_customer = $fetch['cvalue'];

    $sql = "SELECT * FROM company WHERE compcode = '$company' LIMIT 1";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_assoc();
    $detail =$row;

    $sql = "SELECT * FROM pos_system WHERE compcode = '$company' LIMIT 1";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_assoc();
    $posys =$row;

    $phone = explode(";",$detail['cpnum']);


    $sql = "SELECT a.quantity, a.gross, a.uom, b.ddate, b.orderType, d.cname, b.exchange, b.tendered, b.coupon, a.amount, b.gross as total, b.net, b.vat, b.preparedby, b.subtotal, b.serviceFee, b.discount, c.citemdesc, d.chouseno, d.ccity, d.ctin, d.cempid, b.payment_method FROM pos_t a
        LEFT JOIN pos b on a.compcode = b.compcode AND a.tranno = b.tranno
        LEFT JOIN items c on a.compcode = c.compcode AND a.item = c.cpartno
        LEFT JOIN customers d on a.compcode  = d.compcode AND b.customer = d.cempid
        WHERE a.compcode = '$company' and a.tranno = '$tranno'";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($items, $row);
        $exchange = $row['exchange'];
        $coupon = floatval($row['coupon']);
        $tender = floatval($row['tendered']);

        $prepared = $row['preparedby'];
        $customer_id = $row['cempid'];
        $customer =  $customer_id != $default_customer ? $row['cname'] : "";
        $customer_tin = $customer_id != $default_customer ? $row['ctin'] : "";
        $customer_address = $customer_id != $default_customer ? $row['chouseno'] . " " . $row['ccity'] : "";
        $ordertype=$row['orderType'];
        $date = $row['ddate'];

        $discount = $row['discount'];
        $serviceFee = $row['serviceFee'];
        $vat = $row['vat'];
        $net = $row['net'];
        $subtotal = $row['subtotal'];
        $total = $row['total'];
        $pay_meth = ($row['payment_method']=="DEBIT" || $row['payment_method']=="CREDIT") ? $row['payment_method']." CARD" : $row['payment_method'];
        
    }
    $cash = $tender + $coupon;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
 
        <style>
            body{
                padding: 0 !important;
                margin: 0 !important
            }
            * {
                font-size: 12px;
                font-family: 'Helvetica';
            }

            td,th, tr,table {
                border-collapse: collapse;
            }

            td.description,
            th.description {
                width: 70%;
                max-width: 70%;
            }

            #receipt {
                border-bottom: 1px solid;
            }

            td.quantity,
            th.quantity {
                width: 50%;
                max-width: 50%;
                word-break: break-all;
            }

            td.price,
            th.price {
                width: 50%;
                max-width: 50%;
                word-break: break-all;
                text-align: right;
            }

            .centered {
                text-align: center;
                align-content: center;
            }

            .ticket {
                width: 2.8in;
                max-width: 2.8in;
            }

            img {
                max-width: inherit;
                width: inherit;
            }

            @media print {
                @page {
                size: 80mm auto;
                margin: 5mm;
                }
                body {
                    width: 80mm;
                    margin: 0;
                }
                .hidden-print,
                .hidden-print * {
                    display: none !important;
                }
            }
        </style>
        <title>POS Receipt</title>
    </head>
    <body onload='window.print()'>
        <div class="ticket">
            <!-- <img src="< ?= $detail['clogoname'] ?>" alt="Logo"> -->
            <p class="centered"><?= $detail['compname']?>
                <br><?= $detail['compadd'] ?>        
                <br>
                    <?php
                        if($detail['compvat']=="VAT_REG"){
                            echo "VAT REG TIN: ".$detail['comptin'];
                        }else if($detail['compvat']=="NON-VAT_REG"){
                            echo "NON-VAT REG TIN: ".$detail['comptin'];
                        }
                    ?>
            <br>S/N: <?=$posys['cserialno']?>
            <br>MIN: <?=$posys['cmachine']?> 
            <br><?= $ordertype != null ? "----- ". $ordertype ." -----" : null ?></b>
            <div style='display: flex; width: 100%'>
                    <div>OR No.: <?= $tranno ?><br>Date:<?= date("d/m/Y H:i:s A", strtotime($date)) ?></div> 
                    
            </div>
            <div style="padding-bottom: 2px; border-bottom: 1px dashed #000">Cashier: <?= $prepared ?></div>
            
            <table >
                <tbody>
                    <?php 
                        $subtot = 0;
                        foreach($items as $list):  
                            
                            $subtot = $subtot + floatval($list['gross']);
                    ?>
                        <tr>
                            <td width="80%"><?= $list['citemdesc'] ?></td>
                            <td align="right"><?=number_format($list['gross'], 2) ?></td>
                        </tr>
                        <?php
                            if(floatval($list['quantity'])>1){
                        ?>
                        <tr>
                            <td colspan="2"><center><?php echo $list['quantity'] ?>&nbsp;@&nbsp;<?=number_format($list['amount'], 2) ?></center></td>
                        </tr>
                        <?php
                         }
                        ?>
                    <?php endforeach; ?>

                    <tr>
                        <td class="description" style='font-weight: bold;'>SUB TOTAL:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($subtot, 2) ?></td>
                    </tr>

                    <?php
                        if(floatval($serviceFee)>0){
                    ?>
                    <tr>
                        <td class="description" style='font-weight: bold; padding-left: 5px'>add Service Fee:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($serviceFee, 2) ?></td>
                    </tr>
                    <?php
                        }
                        if(floatval($discount)>0){
                    ?>
                    <tr>
                        <td class="description" style='font-weight: bold; padding-left: 5px'>less discounts:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($discount, 2) ?></td>
                    </tr>   
                    <?php
                        }
                        if(floatval($coupon)>0){
                    ?>                
                    <tr>
                        <td class="description" style='font-weight: bold; padding-left: 5px'>less coupon:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($coupon, 2) ?></td>
                    </tr>
                    <?php
                        }
                    ?>

                    <tr>
                        <td style="padding-TOP: 5px;"><b>TOTAL: </b></td>
                        <td style="padding-TOP: 5px;" align="right"><b><?= number_format($total, 2) ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>AMOUNT TENDERED</b></td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold; padding-left: 5px'><?=$pay_meth?></td>
                        <td class="price" style='font-weight: bold'><?= number_format($cash, 2) ?></td>
                    </tr>
                    <?php
                        if(floatval($exchange)>0){
                    ?>
                    <tr>
                        <td class="quantity" style='font-weight: bold'>Change:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($exchange, 2) ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                    
                    <tr>
                        <td colspan='2' align="center">Tax Summary</td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold'>Vatable Sales:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($net, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold'>VAT Amount:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($vat, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="description" style='font-weight: bold'>Zero Rated Sales:</td>
                        <td class="price" style='font-weight: bold'>0.00</td>
                    </tr>
                    <tr>
                        <td class="description" style='font-weight: bold'>Vat Exempt Sales:</td>
                        <td class="price" style='font-weight: bold'>0.00</td>
                    </tr>
                    <tr>
                        <td colspan='2'>&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan='2'><div style="float: left; width: 10%;">Name:</div><div style="float:right; border-bottom: 1px solid #000; width: 80%">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan='2'><div style="float: left; width: 15%;">Address:</div><div style="float:right; border-bottom: 1px solid #000; width: 75%">&nbsp;</div></td>
                    </tr>
                    <tr>
                        <td colspan='2'><div style="float: left; width: 10%;">TIN:</div><div style="float:right; border-bottom: 1px solid #000; width: 85%">&nbsp;</div></td>
                    </tr>
                   

                </tbody>
            </table>

            <center>
                <br>Thank You, Come Again!
                <br>
                <br>Powered By MyxFinancials
                <br><?=$posys['cpoweredname']?>
                <br><?=$posys['cpoweredadd']?>
                <br>VAT REG TIN: <?=$posys['cpoweredtin']?>
                <br>Accred No.: <?=$posys['caccredno']?>
                <br>Date Issued: <?=$posys['ddateissued']?>
                <br>Effectivity Date: <?=$posys['deffectdate']?>
                <br>PTU No.: <?=$posys['cptunum']?>
                <br>Date Issued: <?=$posys['dptuissued']?>
            </center>
            
        </div>
    </body>
</html>