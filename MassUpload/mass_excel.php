<?php

require_once("../Model/helper.php");

$excel_data = ExcelRead($_FILES);

if(count($excel_data) != 0){
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
