<?php
  $hostname = "myxfin_v2_docker-mysql8";
  $dbanme = "prejane_myxfin";
  $usn = "root";
  $pwd = "tiger";

  $connect = new PDO("mysql:host=".$hostname.";dbname=".$dbanme."", "".$usn."", "".$pwd."");
  $con = mysqli_connect("".$hostname."","".$usn."","".$pwd."","".$dbanme."");  
  $con -> set_charset("utf8");

  $httpHost = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : 'localhost';
  $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? 'https' : 'http';
  $UrlBase = $protocol."://".($httpHost == "localhost" ? $httpHost."/st_myxfinancials" : $httpHost)."/";
  $AttachUrlBase = $UrlBase."Components/assets/";

  if (!function_exists('gen_token')) {
    function gen_token(){
      return bin2hex(random_bytes(35));
    }
  }
  
  if (!function_exists('check_credit_limit')) { //0 = Disable ; 1 = Enable
    function check_credit_limit($company){
      global $con;

      $rescrdlmt = mysqli_query($con,"select * from parameters where `compcode` = '$company' and `ccode` = 'CRDLIMIT'");
			$rowcrdlmt = mysqli_fetch_assoc($rescrdlmt);

      return $rowcrdlmt['cvalue'];
    }
  }

  if (!function_exists('check_nt')) { //0 = Disable ; 1 = Enable
    function check_nt($company){
      global $con;

      $rescrdlmt = mysqli_query($con,"select lallownontrade from company where `compcode` = '$company'");
			$rowcrdlmt = mysqli_fetch_assoc($rescrdlmt);

      return $rowcrdlmt['lallownontrade'];
    }
  }
