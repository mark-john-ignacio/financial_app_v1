<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $tranno = mysqli_real_escape_string($con, $_REQUEST['tranno']);

    $sql = "SELECT pos_t.quantity, pos_t.uom, pos.ddate, pos.orderType, 
            pos.table, items.citemdesc, pos.preparedby
            FROM pos_t 
            LEFT JOIN pos ON pos_t.tranno = pos.tranno 
            LEFT JOIN items ON pos_t.item = items.cpartno
            WHERE pos_t.compcode = '$company' AND pos_t.tranno = '$tranno'";
            
    $query = mysqli_query($con, $sql);
    $items = [];
    while($row = $query->fetch_assoc()) {
        $items[] = $row;
        $orderType = $row['orderType'];
        $table = $row['table'];
        $date = $row['ddate'];
        $preparedby = $row['preparedby'];
    }
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: monospace;
            font-size: 12px;
            width: 80mm;
        }
        .centered { text-align: center; }
        .ticket { width: 80mm; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; }
        .large-text { font-size: 14px; font-weight: bold; }
    </style>
    <title>Kitchen Order Slip</title>
</head>
<body onload="window.print()">
    <div class="ticket">
        <h1 class="centered">KITCHEN ORDER SLIP</h1>
        <p>
            <b>Order #:</b> <?= $tranno ?><br>
            <b>Date:</b> <?= date("Y-m-d H:i:s", strtotime($date)) ?><br>
            <b>Type:</b> <?= $orderType ?><br>
            <?php if($table): ?>
            <b>Table:</b> <?= $table ?><br>
            <?php endif; ?>
            <b>Cashier:</b> <?= $preparedby ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th class="large-text">Qty</th>
                    <th class="large-text">Item</th>
                    <th class="large-text">Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td class="centered large-text"><?= $item['quantity'] ?></td>
                    <td class="large-text"><?= $item['citemdesc'] ?></td>
                    <td class="centered"><?= $item['uom'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="centered">*** KITCHEN COPY ***</p>
    </div>
</body>
</html>