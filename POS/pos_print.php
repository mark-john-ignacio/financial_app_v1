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

    $sql = "SELECT * FROM company WHERE compcode = '$company' LIMIT 1";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_assoc();
    $detail =$row;

    $phone = explode(";",$detail['cpnum']);


    $sql = "SELECT a.quantity, a.gross, a.uom, b.ddate, b.orderType, b.customer, b.exchange, b.tendered, b.coupon, b.gross as total, b.net, b.vat, b.preparedby, b.subtotal, b.serviceFee, b.discount, c.citemdesc FROM pos_t a
        LEFT JOIN pos b on a.compcode = b.compcode AND a.tranno = b.tranno
        LEFT JOIN items c on a.compcode = c.compcode AND a.item = c.cpartno
        WHERE a.compcode = '$company' and a.tranno = '$tranno'";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($items, $row);
        $exchange = $row['exchange'];
        $coupon = floatval($row['coupon']);
        $tender = floatval($row['tendered']);

        $prepared = $row['preparedby'];
        $customer = $row['customer'];
        $ordertype=$row['orderType'];
        $date = $row['ddate'];

        $discount = $row['discount'];
        $serviceFee = $row['serviceFee'];
        $vat = $row['vat'];
        $net = $row['net'];
        $subtotal = $row['subtotal'];
        $total = $row['total'];
        
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
            * {
                font-size: 12px;
                font-family: 'Times New Roman';
            }

            td,th, tr,table {
                border-collapse: collapse;
            }

            td.description,
            th.description {
                width: 100%;
                max-width: 100%;
                font-weight: bold;
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
                width: 155px;
                max-width: 155px;
            }

            img {
                max-width: inherit;
                width: inherit;
            }

            @media print {
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
                <?php foreach($phone as $list ): ?>
                    <br><?= $list?>
                <?php endforeach; ?>
                <br>MIN: 
                <br>S/N: 
                <br><b>VAT REGISTERED TIN</b>
                <br><?= $detail['comptin'] ?>

            __________________________
            <br>
            <b><br>Official Receipt No. 
            <br>----- <?= $tranno ?> -----
            <br><?= $ordertype != null ? "----- ". $ordertype ." -----" : null ?></b>
            <div style='display: flex; width: 100%'>
                    <div><?= date("h:i:s A", strtotime($date)) ?></div>&nbsp;
                    <div><?= date("D d M Y", strtotime($date)) ?></div> 
                    
            </div>
            <div>Customer: <?= $customer ?></div>
            <div>Prepared By: <?= $prepared ?></div>
            
            
            
            <br><div style='text-align: center; font-weight: bold'>OFFICIAL RECEIPT</div>
            <br>
            <table >
                <tbody>
                    <?php foreach($items as $list):  ?>
                        <tr>
                            <td colspan="2" class="description"><?= $list['citemdesc'] ?></td>
                        </tr>
                        <tr>
                            <td class="quantity"><center>@<?php echo $list['quantity'] . " " . $list['uom'] ?></center></td>
                            <td class="price"><?= number_format($list['gross'], 2) ?> </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan='2'>__________________________</td>
                    </tr>
                    <tr>
                        <td class="description" style='font-weight: bold'>DISCOUNT:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($discount, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="description" style='font-weight: bold'>SERVICE FEE:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($serviceFee, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan='2'>__________________________</td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold'>NET:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($net, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold'>VAT:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($vat, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="description" style='font-weight: bold'>SUB-TOTAL:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="description" style='font-weight: bold'>TOTAL:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($total, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan='2'>__________________________</td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold;'>Cash:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($cash, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="quantity" style='font-weight: bold'>Change:</td>
                        <td class="price" style='font-weight: bold'><?= number_format($exchange, 2) ?></td>
                    </tr>

                </tbody>
            </table>
            <br>__________________________
            <center>
                <br><div style='font-weight: bold'>THIS SERVES AS AN OFFICIAL RECEIPT</div>
                <br>
                <br>Powered By MyxFinancials
                <br>Sert Technology Inc. | HRWeb
                <br>Blk 2 Lot 15 Tierra Grande Mangahan General Trias
                <br>VAT REG TIN: 
                <br>Accred No.: 
                <br>Date Issued: 
                <br>PTU No.: 
                <br>Date Issued: 
            </center>
            <br>__________________________
            <p class="centered">Thanks for your purchase!</p>
                </p>
        </div>
    </body>
</html>