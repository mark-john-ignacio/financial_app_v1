<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    
    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    $customer = mysqli_real_escape_string($con, $_POST['customer']);
    $tin = mysqli_real_escape_string($con, $_POST['tin']);
    $houseno = mysqli_real_escape_string($con, $_POST['houseno']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $zip = mysqli_real_escape_string($con, $_POST['zip']);

    $sql = "SELECT * FROM customers WHERE compcode = '$company' ORDER BY cempid DESC LIMIT 1";
    $query = mysqli_query($con, $sql);
    $customer_fetched = $query -> fetch_assoc();

    if (mysqli_num_rows($query)==0) {
        $code = "CUST000";
    } else {
        $last = $customer_fetched['cempid'];
        
        $baseno = intval(substr($last,4,7)) + 1;
        $zeros = 3 - strlen($baseno);
        $zeroadd = "";
        
        for($x = 1; $x <= $zeros; $x++){
            $zeroadd = $zeroadd."0";
        }
        
        $baseno = $zeroadd.$baseno;
        $code = "CUST".$baseno;
    }

    
    $sql = "SELECT b.* FROM parameters a
        LEFT JOIN customers b ON a.compcode = b.compcode AND a.cvalue = b.cempid
        WHERE a.compcode = '$company' AND a.ccode = 'BASE_CUSTOMER_POS' AND a.cstatus = 'ACTIVE'";
    $query = mysqli_query($con, $sql);
    $default_customer = $query -> fetch_assoc();

    $account_sales = $default_customer['cacctcodesales'];
    $account_type = $default_customer['cacctcodetype'];
    $customer_type = $default_customer['ccustomertype'];
    $price = $default_customer['cpricever'];
    $vat_type = $default_customer['cvattype'];
    $terms = $default_customer['cterms'];
    $limit = $default_customer['nlimit']; 
    $currency = $default_customer['cdefaultcurrency'];

    
    $sql = "INSERT INTO customers (compcode, cempid, cname, ctradename, cacctcodesales, cacctcodetype, ccustomertype, cpricever, cvattype, cterms, ctin, chouseno, ccity, cstate, ccountry, czip, nlimit, cstatus, cdefaultcurrency) 
            VALUES ('$company', '$code', '$customer', '$customer', '$account_sales', '$account_type', '$customer_type', '$price', '$vat_type', '$terms', '$tin', '$houseno', '$city', '$state', '$country', '$zip', $limit, 'ACTIVE', '$currency')";
    if($query = mysqli_query($con, $sql)) {

        $sql = "UPDATE parameters SET cvalue = '$code' WHERE ccode = 'BASE_CUSTOMER_POS' AND cstatus = 'ACTIVE'";
        $query = mysqli_query($con, $sql);

        echo json_encode([
            'valid' => true,
            'msg' => "Successfully Add Customer"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Error! Add Customer Invalid!"
        ]);
    }
    