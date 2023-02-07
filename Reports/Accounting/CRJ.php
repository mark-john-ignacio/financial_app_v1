<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "CashBook.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];
				$sql = "select * From company where compcode='$company'";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$compname =  $row['compname'];
				}


$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$qry = "";
$varmsg = "";

//ACCOUNTS HEADER
	$cntrCredz = 0;
	
	$sql = "Select DISTINCT A.ctyp, A.cacctno, B.cacctdesc
			From 
			(
			Select 2 as ctyp, A.ctranno, A.cacctno
			FROM `receipt_sales_t` A
			left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			Where A.compcode='$company' and B.lcancelled=0 and B.dcutdate  between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
			
			UNION ALL
			
			Select 2 as ctyp, A.ctranno, A.cacctno
			FROM `receipt_others_t` A
			left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			Where A.compcode='$company' and B.lcancelled=0 and B.dcutdate  between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and A.ncredit <> 0
                
            UNION ALL
                
            Select 1 as ctyp, A.ctranno, A.cacctno
			FROM `receipt_others_t` A
			left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			Where A.compcode='$company' and B.lcancelled=0 and B.dcutdate  between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and A.ndebit <> 0
                
            UNION ALL
                
            Select 0 as ctyp, A.ctranno, A.cacctcode as cacctno
			FROM `receipt` A
			Where A.compcode='$company' and A.lcancelled=0 and A.dcutdate  between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
                
                
			) A 
	left JOIN accounts B on A.cacctno=B.cacctid
	where A.cacctno IS NOT NULL
	Order By A.ctyp, A.cacctno";
	
	//echo $sql;
	
	$result = mysqli_query($con, $sql);

				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}
	else{
		
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$cntrCredz = $cntrCredz + 1;
			//echo $cntrCredz.":".$row['cacctno']."<br>";
			$Credztyp[$cntrCredz] = $row['ctyp'];
			$Credz[$cntrCredz] = $row['cacctno'];
			$CredzDesc[$cntrCredz] = $row['cacctdesc'];
		}
		
		
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cash Receipts Journal</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?php echo strtoupper($compname);  ?></h2>
<h3 class="nopadding">Cash Receipts Journal</h3>
<h4 class="nopadding">For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h4>
</center>

<hr>
<table border="1" align="center" class="table table-condensed">
  <tr>
    <th width="100" style="vertical-align:bottom">Date</th>
    <th width="100" style="vertical-align:bottom">Trans No.</th>
    <th style="vertical-align:bottom">Account Credited</th>
    <th style="vertical-align:bottom">Description</th>
      
   <?php
   	for ($x = 1; $x <= $cntrCredz; $x++) {
   ?>
   	<th class="text-center" style="vertical-align:bottom" width="150">
    	<?php echo $Credz[$x];?><br><?php echo $CredzDesc[$x];?><br>
        
        <?php
        	if($Credztyp[$x]==0 || $Credztyp[$x]==1){
				echo "Dr.";
			}else{
				echo "Cr.";
			}
		?>
        
    
    </th>
   <?php
	}
   ?>

  </tr>
  
  
  
  <!-- DETAILS -->
  
  <?php
  
  $sqlDet = "Select A.ctranno, A.cacctno, B.cacctdesc, C.dcutdate, C.ccode, C.cremarks, Sum(A.namount) as namount
			From 
			(
			Select 2 as ctyp, A.ctranno, A.cacctno, A.namount as namount
			FROM `receipt_sales_t` A
			left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			Where A.compcode='$company' and B.lcancelled=0 and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
			
			UNION ALL
			
			Select 2 as ctyp, A.ctranno, A.cacctno, A.ncredit as namount
			FROM `receipt_others_t` A
			left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			Where A.compcode='$company' and B.lcancelled=0 and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and A.ncredit <> 0
                
            UNION ALL
                
            Select 1 as ctyp, A.ctranno, A.cacctno, A.ndebit as namount
			FROM `receipt_others_t` A
			left join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			Where A.compcode='$company' and B.lcancelled=0 and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and A.ndebit <> 0
                
            UNION ALL
                
            Select 0 as ctyp, A.ctranno, A.cacctcode as cacctno, A.namount
			FROM `receipt` A
			Where A.compcode='$company' and A.lcancelled=0 and A.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
                
                
			) A 
		left JOIN accounts B on A.cacctno=B.cacctno
		left JOIN receipt C on A.ctranno=C.ctranno
		where A.cacctno IS NOT NULL
		Group By A.ctranno, A.cacctno, B.cacctdesc, C.dcutdate, C.ccode, C.cremarks
		Order By A.ctranno, A.ctyp, A.cacctno";
	
	//echo $sqlDet;
	$resDet = mysqli_query($con, $sqlDet);

				
	if (!mysqli_query($con, $sqlDet)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}
	else{
		
	$row1 = mysqli_fetch_array($resDet, MYSQLI_ASSOC);
	$tranno = $row1['ctranno'];
	$ccode = $row1['ccode'];
	$cremarks = $row1['cremarks'];
	
	  $time1 = strtotime($row1['dcutdate']);
	$dcutdate = date('M d',$time1);
	$cntrdetails = 0;
	
					for ($x = 1; $x <= $cntrCredz; $x++) {
						$RowvalArr[$x] = 0;
						$RowvalArrTOTAL[$x] = 0;
					}

				for ($x = 1; $x <= $cntrCredz; $x++) {
					$cntrdetails++;
					
					if($Credz[$x]==$row1['cacctno']){
						//echo " ID: ".$Credz[$x]."==".$row1['namount']."<br>";
						$RowvalArr[$x] = $row1['namount'];
						//$RowvalArrTOTAL[$x] = $RowvalArrTOTAL[$x] + $row1['namount'];
					}
				}
	//echo $dcutdate;
	
	
		while($rowDet = mysqli_fetch_array($resDet, MYSQLI_ASSOC))
		{
			//echo $tranno."==".$rowDet['ctranno'];
			
			if($tranno==$rowDet['ctranno'])	{
				$cntrdetails = 0;
				
				for ($x = 1; $x <= $cntrCredz; $x++) {
					$cntrdetails++;
					
					if($Credz[$x]==$rowDet['cacctno']){
						//echo " ID: ".$Credz[$x]."==".$rowDet['namount']."<br>";
						$RowvalArr[$x] = $rowDet['namount'];
					}
				}
								
			}
			else{
				
				


	?>
			
            <tr>
                <td width="100"><?php echo $dcutdate;?></td>
                <td><?php echo $tranno;?></td>
                <td><?php echo $ccode;?></td>
                <td><?php echo $cremarks;?></td>
               
               <?php
                for ($y = 1; $y <= $cntrCredz; $y++) {
               ?>
                <td class="text-right">
                    <?php 
					if($RowvalArr[$y]!=0){
						echo number_format($RowvalArr[$y],2);
						$RowvalArrTOTAL[$y] = $RowvalArrTOTAL[$y] + $RowvalArr[$y];
						
					}
					?>
                </td>
               <?php
                }
               ?>
    
          </tr>
    
	<?php

			$tranno = $rowDet['ctranno'];
			$ccode = $rowDet['ccode'];
			$cremarks = $rowDet['cremarks'];
			
			  $time1 = strtotime($rowDet['dcutdate']);
			$dcutdate = date('M d',$time1);
			
					for ($x = 1; $x <= $cntrCredz; $x++) {
							$RowvalArr[$x] = 0;
					}
					
					
					for ($x = 1; $x <= $cntrCredz; $x++) {
						if($Credz[$x]==$rowDet['cacctno']){
							//echo " ID: ".$Credz[$x]."==".$rowDet['namount']."<br>";
							$RowvalArr[$x] = $rowDet['namount'];
						}
					}
			
			}
			
	
		
		}
	}


  ?>
  
              <tr>
                <td width="100"><?php echo $dcutdate;?></td>
                <td><?php echo $tranno;?></td>
                <td><?php echo $ccode;?></td>
                <td><?php echo $cremarks;?></td>
               
               <?php
                for ($y = 1; $y <= $cntrCredz; $y++) {
               ?>
                <td class="text-right">
                    <?php 
					if($RowvalArr[$y]!=0){
						echo number_format($RowvalArr[$y],2);
						$RowvalArrTOTAL[$y] = $RowvalArrTOTAL[$y] + $RowvalArr[$y];
					}
					?>
                </td>
               <?php
                }
               ?>
    
          </tr>
          
          <tr>
          <td width="100"><?php echo $dcutdate;?></td>
          <td colspan="3"><b>TOTAL</b></td>
           <?php
                for ($y = 1; $y <= $cntrCredz; $y++) {
               ?>
                <td class="text-right">
                    <?php 
					if($RowvalArrTOTAL[$y]!=0){
						echo "<b>".number_format($RowvalArrTOTAL[$y],2)."</b>";
					}
					?>
                </td>
               <?php
                }
               ?>
          </tr>

  
</table>

</body>
</html>