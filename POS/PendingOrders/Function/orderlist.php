<?php
include('../../../Connection/connection_string.php');

// Queries for transactions and holds
$sql_transactions = "SELECT 'transaction' AS type, pt.tranno, pt.payment_transaction, pt.items, pt.quantity, pt.waiting_time, pt.transaction_type, pt.pstatus, pt.order_adding, pt.receipt, it.citemdesc, tpos.customer, tpos.orderType, tpos.table, tpos.preparedby
    FROM pendingorder_status pt
    JOIN items it ON pt.items = it.cpartno
    JOIN pos tpos ON pt.tranno = tpos.tranno
    WHERE pt.transaction_type = 'Payment'

    UNION ALL

    SELECT 'hold' AS type, ph.tranno, ph.payment_transaction, ph.items, ph.quantity, ph.waiting_time, ph.transaction_type, ph.pstatus, ph.order_adding, ph.receipt, it.citemdesc, 'Hold' AS customer, tpos.ordertype, tpos.table, 'System' AS preparedby
    FROM pendingorder_status ph
    JOIN items it ON ph.items = it.cpartno
    JOIN pos_hold tpos ON ph.tranno = tpos.transaction
    WHERE ph.transaction_type = 'Hold'

    ORDER BY order_adding";

// Arrays to hold transaction data separately
$transactions = array();

// Fetch regular transactions and add status for each item
$result_transactions = $con->query($sql_transactions);
if ($result_transactions->num_rows > 0) {
    while ($row = $result_transactions->fetch_assoc()) {
        $transactionKey = "{$row['type']}-{$row['tranno']}-{$row['order_adding']}-{$row['customer']}-{$row['preparedby']}";

        if (!isset($transactions[$transactionKey])) {
            $transactions[$transactionKey] = array(
                'tranno' => $row['tranno'],
                'payment_transaction' => $row['payment_transaction'],
                'ddate' => $row['order_adding'],
                'transaction_type' => $row['transaction_type'],
                'waiting_time' => $row['waiting_time'],
                'receipt' => $row['receipt'],
                'customer' => $row['customer'],
                'orderType' => $row['orderType'],
                'table' => $row['table'],
                'preparedby' => $row['preparedby'],
                'items' => array()
            );
        }

        // Check if the item already exists in the current transaction's items array
        $itemFound = false;
        foreach ($transactions[$transactionKey]['items'] as &$existingItem) {
            if ($existingItem['item'] === $row['items']) {
                $existingItem['quantity'] += $row['quantity'];
                $itemFound = true;
                break;
            }
        }

        // If the item does not exist, add it to the array
        if (!$itemFound) {
            $transactions[$transactionKey]['items'][] = array(
                'item' => $row['items'],
                'quantity' => $row['quantity'],
                'citemdesc' => $row['citemdesc'],
                'status' => $row['pstatus'],
            );
        }
    }
}

// Convert items arrays to values arrays for each transaction
foreach ($transactions as $key => $transaction) {
    $transactions[$key]['items'] = array_values($transaction['items']);
}

// Output both types of transactions with item status
echo json_encode(array(
    'transactions' => array_values($transactions)
));

$con->close();
?>
