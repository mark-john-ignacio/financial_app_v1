<?php
function getCompanyDetails($con, $company) {
    $query = mysqli_query($con, "SELECT * FROM company WHERE compcode='$company'");
    if (mysqli_num_rows($query) !== 0) {
        return $query->fetch_assoc();
    }
    return null;
}

function getCategories($con, $company) {
    $categories = [];
    $sql = "SELECT * FROM groupings WHERE ctype='ITEMCLS' AND ccode IN (SELECT cclass FROM items WHERE compcode='$company' AND cstatus='ACTIVE' AND ctradetype='Trade') ORDER BY cdesc";
    $query = mysqli_query($con, $sql);
    while ($row = $query->fetch_assoc()) {
        array_push($categories, $row);
    }
    return $categories;
}

function getItems($con, $company, $date) {
    $items = [];
    $sql = "SELECT a.cpartno, a.cpartno AS cscancode, a.citemdesc, 0 AS nretailcost, 0 AS npurchcost, a.cunit, a.cstatus, 0 AS ltaxinc, a.cclass, 1 AS nqty, a.cuserpic, c.nqty AS quantity, linventoriable AS isInvetory
            FROM items a 
            LEFT JOIN (
                SELECT a.citemno, COALESCE((SUM(nqtyin) - SUM(nqtyout)), 0) AS nqty
                FROM tblinventory a
                RIGHT JOIN items d ON a.citemno=d.cpartno AND a.compcode=d.compcode
                WHERE a.compcode='$company' AND a.dcutdate <= '$date' AND d.cstatus='ACTIVE'
                GROUP BY a.citemno
            ) c ON a.cpartno=c.citemno
            WHERE a.compcode='$company' AND a.cstatus='ACTIVE' AND a.ctradetype='Trade' ORDER BY a.cclass, a.citemdesc";
    $query = mysqli_query($con, $sql);
    while ($row = $query->fetch_assoc()) {
        array_push($items, $row);
    }
    return $items;
}

function getTables($con, $company) {
    $tables = [];
    $sql = "SELECT * FROM pos_grouping WHERE compcode='$company' AND type='TABLE'";
    $query = mysqli_query($con, $sql);
    while ($row = $query->fetch_assoc()) {
        array_push($tables, $row);
    }
    return $tables;
}

function getOrders($con, $company) {
    $orders = [];
    $sql = "SELECT * FROM pos_grouping WHERE compcode='$company' AND type='ORDER'";
    $query = mysqli_query($con, $sql);
    while ($row = $query->fetch_assoc()) {
        array_push($orders, $row);
    }
    return $orders;
}

function getDiscounts($con, $company) {
    $discounts = [];
    $sql = "SELECT * FROM discounts WHERE compcode='$company' AND lapproved='1'";
    $query = mysqli_query($con, $sql);
    while ($row = $query->fetch_assoc()) {
        array_push($discounts, $row);
    }
    return $discounts;
}

function getServiceFee($con, $company) {
    $sql = "SELECT * FROM parameters WHERE compcode='$company' AND ccode='SERVICE_FEE'";
    $query = mysqli_query($con, $sql);
    $serviceFee = 0;
    $isCheck = 0;
    if (mysqli_num_rows($query) != 0) {
        while ($row = $query->fetch_assoc()) {
            $serviceFee = $row['cvalue'];
            $isCheck = $row['nallow'];
        }
    }
    return [$serviceFee, $isCheck];
}

function getWaitingTime($con, $company) {
    $sql = "SELECT * FROM parameters WHERE compcode='$company' AND ccode='WAITING_TIME'";
    $query = mysqli_query($con, $sql);
    $isCheckWaitingTime = 0;
    if (mysqli_num_rows($query) != 0) {
        while ($row = $query->fetch_assoc()) {
            $isCheckWaitingTime = $row['nallow'];
        }
    }
    return $isCheckWaitingTime;
}

function getManualReceipt($con, $company) {
    $sql = "SELECT * FROM parameters WHERE compcode='$company' AND ccode='MANUAL_RECEIPT'";
    $query = mysqli_query($con, $sql);
    $isCheckManualReceipt = 0;
    if (mysqli_num_rows($query) != 0) {
        while ($row = $query->fetch_assoc()) {
            $isCheckManualReceipt = $row['nallow'];
        }
    }
    return $isCheckManualReceipt;
}
?>