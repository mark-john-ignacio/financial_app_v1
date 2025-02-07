<?php
include('../../../Connection/connection_string.php');

// Query for pending orders
$sql = "SELECT 
    ps.tranno,
    ps.payment_transaction,
    ps.items,
    ps.quantity,
    ps.waiting_time,
    ps.transaction_type,
    ps.pstatus,
    ps.order_adding as ddate,
    ps.receipt,
    i.citemdesc,
    p.customer,
    p.orderType,
    p.table,
    p.preparedby
FROM pendingorder_status ps
LEFT JOIN items i ON ps.items = i.cpartno
LEFT JOIN pos p ON ps.tranno = p.tranno
WHERE ps.receipt = 'No'
ORDER BY ps.order_adding DESC";

$result = mysqli_query($con, $sql);
$transactions = array();

// Group items by transaction
while($row = mysqli_fetch_assoc($result)) {
    $transactionKey = $row['tranno'];
    
    if (!isset($transactions[$transactionKey])) {
        $transactions[$transactionKey] = array(
            'tranno' => $row['tranno'],
            'payment_transaction' => $row['payment_transaction'],
            'transaction_type' => $row['transaction_type'],
            'ddate' => $row['ddate'],
            'receipt' => $row['receipt'],
            'waiting_time' => $row['waiting_time'],
            'customer' => $row['customer'],
            'orderType' => $row['orderType'],
            'table' => $row['table'],
            'preparedby' => $row['preparedby'],
            'items' => array()
        );
    }
    
    $transactions[$transactionKey]['items'][] = array(
        'item' => $row['items'],
        'quantity' => $row['quantity'],
        'citemdesc' => $row['citemdesc'],
        'status' => $row['pstatus']
    );
}

// Format response
$output = array('transactions' => array_values($transactions));

// Return JSON
header('Content-Type: application/json');
echo json_encode($output);
?>
