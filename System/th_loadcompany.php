<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];

    $result = mysqli_query($con,"SELECT * FROM `company` where compcode='$company'"); 
            
    if (mysqli_num_rows($result)!=0) {
       $comprow = mysqli_fetch_array($result, MYSQLI_ASSOC);
                 
        $json['cname'] = $comprow['compname']; 
        $json['cdesc'] = $comprow['compdesc']; 
        $json['cadd']= $comprow['compadd']; 
        $json['czip']= $comprow['compzip']; 
        $json['ctin'] = $comprow['comptin']; 
		$json['lvat'] = $comprow['compvat'];
        $json['rdoc'] = $comprow['comprdo']; 
        $json['compbustype'] = $comprow['compbustype'];
        $json['txtheader'] = $comprow['txtheader'];
        $json['clogoname'] = $comprow['clogoname'];
        $json['ccpnum'] = $comprow['cpnum'];
        $json['emailadd'] = $comprow['email'];
        $json['ptucode'] =$comprow['ptucode'];
        $json['ptudate'] = $comprow['ptudate'];
		$json2[] = $json;
                    
     }
     else{
                     
        $json['cname'] = ""; 
        $json['cdesc'] = ""; 
        $json['cadd']= ""; 
        $json['ctin'] = ""; 
		$json['lvat'] = "";
        $json['txtheader'] = "";
        $json2[] = $json;
                     
     }

	echo json_encode($json2);




?>
