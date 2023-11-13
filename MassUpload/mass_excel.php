<?php

require_once("../Model/helper.php");

$excel_data = ExcelRead($_FILES);

if(!empty($excel_data)){
    echo json_encode([
        'valid' => true,
        'data' => $excel_data
    ]);
} else {
    echo json_encode([
        'valid' => false,
        'msg' => "No Data Has been Found in Excel!"
    ]);
}
