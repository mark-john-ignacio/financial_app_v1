<?php
    if(!isset($_SESSION)){
        session_start();
    }

    require_once "../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];

    $result = mysqli_query ($con, "select 'SUPPLIER' as typ, ccode, cname from suppliers WHERE compcode='$company' and cname like '%".$_GET['query']."%'  Order By cname");
    
    
    @$arr = array();
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $arr['supplier'] = array(
            'type' => $row['typ'],
            'code' => $row['ccode'],
            'name' => utf8_decode($row['cname']),
        );
        $json2[] = $arr['supplier'];
    }
    echo json_encode($json2);

    // $sql = "select a.*, a.ccheckno, b.cname, e.cname as bankname
    //         from paybill a 
    //         left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode 
    //         left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
    //         where a.compcode='$company' and b.cname='".$arr['supplier']['name']."' order by a.dtrandate DESC";
    // $result = mysqli_query($con, $sql);

    // while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    //     $json['ctranno'] = $row['ctranno'];
    //     $json['name'] = $row['cname'];
    //     $json['ddate'] = $row['ddate'];
    //     $json['status'] = $row['Iapproved'];

    //     $json2[] = $json;
    // }

    // echo json_encode($json2);
    ?>