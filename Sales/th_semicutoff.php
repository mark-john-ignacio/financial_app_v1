<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

		$company = $_SESSION['companyid'];

		//last date ng INVOICE
		$result1 = mysqli_query($con,"SELECT dcutdate FROM `so` order By ddate desc Limit 1"); 

		if (mysqli_num_rows($result1)!=0) {
		 $all_course_data1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
		 
			$curdate = $all_course_data1["dcutdate"];
		}	
		else{
			$curdate = date("Y-m-d");
		}
		
		
		
		//get ung semicutdays ; Getting current cutoff
		$isBetween= "";
		
		$getsemi = mysqli_query ($con, "select * from pos_cutoff WHERE compcode='$company'"); 
		$ressemi =  mysqli_fetch_array($getsemi, MYSQLI_ASSOC);
		$vardayfr1 = sprintf("%02d", $ressemi['dayfrom1']);
		$vardayto1 = sprintf("%02d", $ressemi['dayto1']);
		$vardayfr2 = sprintf("%02d", $ressemi['dayfrom2']);
		$vardayto2 = sprintf("%02d", $ressemi['dayto2']);
		
		$month1 = date('m');
		$year1 =  date('Y');

		$month2 = date('m');
		$year2 =  date('Y');
		
		//$ = date("Y-m-d");
		//$curdate = "2017-12-16";
		
		$datefrom = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate)) . "-" . $vardayfr1;
		$dateto = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate)) . "-" . $vardayto1;

	//-echo $curdate."<br>";		
	//-echo $datefrom;
	//-echo " : ";
	//-echo $dateto."<br>";
	
	if ((strtotime($curdate) >= strtotime($datefrom)) && (strtotime($curdate) <= strtotime($dateto)))
    {
      $isBetween = "YES";
	 //- echo "Date 1 Success";
    }
    else
    {
      $isBetween = "NO";
	 //- echo "Date 1 Error";
    }


	//GO TO OPTION 2
	if($isBetween == "NO"){
			if($vardayfr2>$vardayto2){
				$datefrom = date("Y", strtotime($curdate)) ."-". date("m", strtotime($curdate)) . "-" . $vardayfr2;
				
				if(date("m", strtotime($curdate))=="12"){
					$dateto = date("Y", strtotime($curdate. " +1 years"))."-".date("m", strtotime($curdate. " +1 months")) . "-" . $vardayto2;
				}
				else{
					$dateto = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate. " +1 months")) . "-" . $vardayto2;
				}
			}
			else{
				$datefrom = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate)) . "-" . $vardayfr2;
				$dateto = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate)) . "-" . $vardayto2;
			}
	
	//-echo "<br><br>".$datefrom;
	//-echo " : ";
	//-echo $dateto."<br>";
	
	if ((strtotime($curdate) >= strtotime($datefrom)) && (strtotime($curdate) <= strtotime($dateto)))
    {
      $isBetween = "YES";
	 //- echo "Date 2 Success";
    }
    else
    {
      $isBetween = "NO";
	 //- echo "Date 2 Error";
    }

	}
	

	//GO TO OPTION 3
	if($isBetween == "NO"){
			if($vardayfr2>$vardayto2){
				
				
				if(date("m", strtotime($curdate))=="12"){
					$datefrom = date("Y", strtotime($curdate. " -1 years"))."-".date("m", strtotime($curdate. " -1 months")) . "-" . $vardayfr2;
				}
				else{
					$datefrom = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate. " -1 months")) . "-" . $vardayfr2;
				}
				
				$dateto = date("Y", strtotime($curdate)) ."-". date("m", strtotime($curdate)) . "-" . $vardayto2;
			}
			else{
				$datefrom = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate)) . "-" . $vardayfr2;
				$dateto = date("Y", strtotime($curdate))."-".date("m", strtotime($curdate)) . "-" . $vardayto2;
			}
	
	//-echo "<br><br>".$datefrom;
	//-echo " : ";
	//-echo $dateto."<br>";
	
	if ((strtotime($curdate) >= strtotime($datefrom)) && (strtotime($curdate) <= strtotime($dateto)))
    {
      $isBetween2 = "YES";
	  //-echo "Date 3 Success";
    }
    else
    {
      $isBetween2 = "NO";
	//-  echo "Date 3 Error";
    }
	}
	

?>
