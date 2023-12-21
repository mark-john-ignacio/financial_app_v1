<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../../../Connection/connection_string.php";
    
    $company = $_SESSION['companyid'];
    $datefrom = date("Y-m-d", strtotime($_REQUEST['from']));
    $dateto = date("Y-m-d", strtotime($_REQUEST['to']));

    $sql = "SELECT a.ctranno, a.csalesno, a.namount, a.nnet, a.nvat, b.dcutdate, c.creference, d.cname, d.ctin, d.chouseno, d.ccity, a.ctaxcode FROM receipt_sales_t a
            LEFT JOIN receipt b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
            LEFT JOIN sales_t c ON a.compcode = c.compcode AND a.csalesno = c.ctranno
            LEFT JOIN customers d ON a.compcode = d.compcode AND b.ccode = d.cempid
            WHERE a.compcode = '$company' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND (STR_TO_DATE(b.dcutdate, '%Y-%m-%d') BETWEEN '$datefrom' AND '$dateto') ";

    if($query = mysqli_query($con, $sql)) {
        $vatable = [];
        $nonvat = [];
        $exempt = [];
        $zero = [];
        while($list = $query -> fetch_assoc()) :
            $json = [
                'transaction' => $list['ctranno'],
                'date' => date("F d, Y", strtotime($list['dcutdate'])),
                'sales' => $list['csalesno'],
                'reference' => $list['creference'],
                'partner' => $list['cname'],
                'tin' => $list['ctin'],
                'address' => $list['chouseno'] . " " . $list['ccity'],
                'gross' => round($list['namount'], 2),
                'net' => round($list['nnet'], 2),
                'tax' => round($list['nvat'], 2)
            ];
            
            switch($list['ctaxcode']) {
                case "VT":
                    if(!in_array($json, $vatable)) :
                        array_push($vatable, $json);
                    endif;
                    break;
                case "NV":
                    if(!in_array($nonvat, $vatable)) :
                        array_push($nonvat, $json);
                    endif;
                    break;
                case "ZR":
                    if(!in_array($json, $zero)) :
                        array_push($zero, $json);
                    endif;
                    break;
                case "VE":
                    if(!in_array($json, $exempt)) :
                        array_push($exempt, $json);
                    endif;
                    break;
            }
        endwhile;
        
        echo json_encode([
            'valid' => true,
            'vt' => $vatable,
            'nv' => $nonvat,
            'zr' => $zero,
            've' => $exempt
        ]);

    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }