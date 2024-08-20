<?php

include('../../Connection/connection_string.php');

$tranno = mysqli_real_escape_string($con, $_REQUEST['tranno']);
$transaction_type = mysqli_real_escape_string($con, $_REQUEST['transaction_type']);

if($transaction_type == "Hold"){
    $date = mysqli_real_escape_string($con, $_REQUEST['date']);
    $sql = "SELECT 
                pt.tranno,
                pt.transaction_type,
                pt.items,
                pt.quantity,
                pt.order_adding,
                it.citemdesc,
                tpos.table,
                tpos.ordertype
            FROM pendingorder_status pt
            JOIN items it ON pt.items = it.cpartno
            JOIN pos_hold tpos ON pt.tranno = tpos.transaction
            WHERE pt.tranno = '$tranno' AND pt.transaction_type = '$transaction_type' AND pt.order_adding = '$date'";

    $query = mysqli_query($con, $sql);
}

else if($transaction_type == "Payment"){
    $sql = "SELECT 
                pt.tranno,
                pt.transaction_type,
                pt.items,
                pt.quantity,
                pt.order_adding,
                it.citemdesc,
                tpos.table,
                tpos.orderType AS ordertype,
                tpos.preparedby
            FROM pendingorder_status pt
            JOIN items it ON pt.items = it.cpartno
            JOIN pos tpos ON pt.tranno = tpos.tranno
            WHERE pt.tranno = '$tranno' AND pt.transaction_type = '$transaction_type'";

    $query = mysqli_query($con, $sql);
}
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
            margin: 0 !important;
        }
        * {
            font-size: 8px; 
            font-family: 'Helvetica';
        }

        td, th, tr, table {
            border-collapse: collapse;
        }

        td.description,
        th.description {
            width: 60%; 
            max-width: 60%;
        }

        .receipt {
            border-bottom:1px dashed black; 
            margin-left: 5px; 
            margin-right: 5px; 
            margin-top: 5px; 
        }

        td.quantity,
        th.quantity {
            width: 20%; 
            max-width: 20%;
            word-break: break-all;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: 2.19in;
            max-width: 2.19in;
        }

        img {
            max-width: inherit;
            width: inherit;
        }

        @media print {
            @page {
                size: 58mm auto;
                margin: 5mm;
            }
            body {
                width: 58mm;
                margin: 0;
            }
            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }
    </style>
    <title>Kitchen Receipt</title>
</head>
<body onload='window.print()'>
<div class="ticket">
    <?php
    if ($query && $query->num_rows > 0) {
        $first_row = $query->fetch_assoc();
        
        $transaction_number = $first_row['tranno'];
        $transaction_type = $first_row['transaction_type'];
        $name = "System";
        $order_adding = $first_row['order_adding'];
        $table = $first_row['table'];
        if($transaction_type == "Payment"){
            $preparedby = $first_row['preparedby'];
        }
        $ordertype = $first_row['ordertype'];

        $items_list = [];
        do {
            $items_list[] = [
                'items' => $first_row['citemdesc'],
                'quantity' => $first_row['quantity']
            ];
        } while ($first_row = $query->fetch_assoc());
        ?>
        <center>
            <p style="font-size:10px; font-weight:bold;">#<?php echo htmlspecialchars($transaction_number); ?></p> 
            <p style="font-size:8px; font-weight:bold; margin-top:-5px;"><?php echo htmlspecialchars($table); ?></p> 
        </center>
        <p style="margin-left:5px;"><?php echo htmlspecialchars($order_adding); ?></p>
        <?php if($transaction_type == "Hold"): ?>
            <p style="font-size:8px; font-weight:bold; margin-top:-5px; margin-left:5px;"><?php echo htmlspecialchars($name); ?></p>
        <?php elseif($transaction_type == "Payment"): ?>
            <p style="font-size:8px; font-weight:bold; margin-top:-5px; margin-left:5px;"><?php echo htmlspecialchars($preparedby); ?></p>
        <?php endif; ?>
        <center>
            <p style="font-size:10px; font-weight:bold; margin-left: 5px; margin-right: 5px; padding:5px; border-bottom:1px dashed black; border-top:1px dashed black;"><?php echo htmlspecialchars($ordertype); ?></p>
        </center>
        <table style="margin-left: 5px; margin-right: 5px;">
            <tbody>
            <?php foreach ($items_list as $item): ?>
                <tr>
                    <td class="quantity"><?php echo htmlspecialchars($item['quantity']); ?> &nbsp; <span>x</span></td>
                    <td class="description"><?php echo htmlspecialchars($item['items']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="receipt"></div>
    <?php } else { ?>
        <p>No data available</p>
    <?php } ?>
</div>
</body>
</html>
