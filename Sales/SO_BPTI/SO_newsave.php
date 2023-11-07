<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include('../../Connection/connection_string.php');
	include('../../include/denied.php');
    require_once('../../Model/helper.php');

    $dmonth = date('m');
    $dyear = date('y');

    $company = $_SESSION['companyid'];
    $cSINo = "RP".$dmonth.$dyear."00003";

    if(count($_FILES) != 0){
		$directory = "../../Components/assets/SO/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}
    
    //echo var_dump($_FILES['file']);
?>
