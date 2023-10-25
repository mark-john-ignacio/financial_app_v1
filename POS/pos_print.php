<?php

use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

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

    $sql = "SELECT a.quantity, a.gross, b.gross as total, b.net, b.vat, b.preparedby, c.citemdesc FROM pos_t a
        LEFT JOIN pos b on a.compcode = b.compcode AND a.tranno = b.tranno
        LEFT JOIN items c on a.compcode = c.compcode AND a.item = c.cpartno
        WHERE a.compcode = '$company' and a.tranno = '$tranno'";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($items, $row);
        $total = $row['total'];
        $vat = $row['vat'];
        $net = $row['net'];
        $prepared = $row['preparedby'];
    }
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
                border-top: 1px solid black;
                border-collapse: collapse;
            }

            td.description,
            th.description {
                width: 75px;
                max-width: 75px;
            }

            td.quantity,
            th.quantity {
                width: 40px;
                max-width: 40px;
                word-break: break-all;
            }

            td.price,
            th.price {
                width: 40px;
                max-width: 40px;
                word-break: break-all;
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
            <img src="<?= $detail['clogoname'] ?>" alt="Logo">
            <p class="centered"><?= $detail['compname']?>
                <br><?= $detail['compadd'] ?>

            **************************

            <table>
                <thead>
                    <tr>
                        <th class="quantity">Q.</th>
                        <th class="description">Description</th>
                        <th class="price">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $list):  ?>

                        <tr>
                            <td class="quantity"><?= $list['quantity'] ?></td>
                            <td class="description"><?= $list['citemdesc'] ?></td>
                            <td class="price"><?= number_format($list['gross'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td class="quantity"></td>
                        <td class="description" style='font-weight: bold'>NET</td>
                        <td class="price" style='font-weight: bold'><?= number_format($net, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="quantity"></td>
                        <td class="description" style='font-weight: bold'>VAT</td>
                        <td class="price" style='font-weight: bold'><?= number_format($vat, 2) ?></td>
                    </tr>
                    <tr>
                        <td class="quantity"></td>
                        <td class="description" style='font-weight: bold'>TOTAL</td>
                        <td class="price" style='font-weight: bold'><?= number_format($total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <br>**************************
            <br>Prepared By: <?= $prepared ?>
            <br>Email: <?= $detail['email'] ?>
            <br>
            <p class="centered">Thanks for your purchase!
                </p>
        </div>
    </body>
</html>