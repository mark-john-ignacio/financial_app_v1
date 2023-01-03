<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Demo - Import Excel file data in mysql database using PHP, Upload Excel file data in database</title>
<meta name="description" content="This tutorial will learn how to import excel sheet data in mysql database using php. Here, first upload an excel sheet into your server and then click to import it into database. All column of excel sheet will store into your corrosponding database table."/>
<meta name="keywords" content="import excel file data in mysql, upload ecxel file in mysql, upload data, code to import excel data in mysql database, php, Mysql, Ajax, Jquery, Javascript, download, upload, upload excel file,mysql"/>
</head>
<body>

<?php
/************************ YOUR DATABASE CONNECTION START HERE   ****************************/
if(isset($_POST["submit"]))
	{
		
define ("DB_HOST", "localhost:9090"); // set database host
define ("DB_USER", "root"); // set database user
define ("DB_PASS",""); // set database password
define ("DB_NAME","coopsys"); // set database name

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("Couldn't make connection.");
$databasetable = "pagelist";

/************************ YOUR DATABASE CONNECTION END HERE  ****************************/


set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
include 'PHPExcel/IOFactory.php';

// This is the file path to be uploaded.
$inputFileName = 'pages.xlsx'; 

try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}


$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet


for($i=2;$i<=$arrayCount;$i++){
$identity = trim($allDataInSheet[$i]["A"]);
$sort = trim($allDataInSheet[$i]["B"]);
$main = trim($allDataInSheet[$i]["C"]);
$pageid = trim($allDataInSheet[$i]["D"]);
$pagedesc = trim($allDataInSheet[$i]["E"]);
$pageaccess = trim($allDataInSheet[$i]["F"]);
$pageurl = trim($allDataInSheet[$i]["G"]);
$pagetype = trim($allDataInSheet[$i]["H"]);


$query = "SELECT pagedesc FROM pagelist WHERE pageid = '".$pageid."'";
$sql = mysqli_query($link,$query);
$recResult = mysqli_fetch_array($sql);
$existName = $recResult["pagedesc"];
if($existName=="") {
$insertTable= mysqli_query($link,"insert into pagelist (identity, sort, main, pageid, pagedesc, pageaccess, pageurl, pagetype) values('".$identity."', '".$sort."','".$main."','".$pageid."', '".$pagedesc."','".$pageaccess."', '".$pageurl."','".$pagetype."');");

//echo "HELLO".$existName;
$msg = 'Record has been added. <div style="Padding:20px 0 0 0;"><a href="">Go Back to tutorial</a></div>';
} else {
//echo "HELLO".$existName;
$msg = 'Record already exist. '.$existName.' <div style="Padding:20px 0 0 0;"><a href="">Go Back to tutorial</a></div>';
}
}
echo "<div style='font: bold 18px arial,verdana;padding: 45px 0 0 500px;'>".$msg."</div>";
}

?>
<body>
<form name="import" method="post" enctype="multipart/form-data">
	Click submit store data in mysql
        <input type="submit" name="submit" value="Submit" style="margin-left:100px;"/>
    </form>
</html>