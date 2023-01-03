<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Journal.php";

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

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cash Disbursement</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?php echo strtoupper($compname);  ?></h2>
<h3 class="nopadding">Cash Disbursement Journal</h3>
<h4 class="nopadding">For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h4>
</center>

<hr>
<table width="100%" border="0" align="center" cellpadding="2px">
  <tr>
    <th width="100">Acct Code</th>
    <th>Account Title</th>
    <th class="text-right" width="150">Debit</th>
    <th class="text-right" width="150">Credit</th>
  </tr>
  
<?php

	$sql = "Select b.ctranno, b.ccode, b.cpayee, b.ccheckno, a.acctno, a.ctitle, a.ndebit, a.ncredit, b.dcheckdate
	From glactivity a
	left join paybill b on a.compcode=b.compcode and a.ctranno=b.ctranno
	where a.compcode='$company' and a.cmodule='PV' and b.dcheckdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
	order by b.ccheckno, a.ndebit";

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//get 1st row data
			//$row1 = $result->fetch_assoc();
			$ctran = "";
			$ddate = "";
			$ccode = "";
			$cpayee = "";
			$cchecko = "";
	
	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($ctran!=$row['ctranno']){
			$cntr++;		
			$ctran = $row['ctranno'];
			$ddate = $row['dcheckdate'];
			$ccode = $row['ccode'];
			$cpayee = $row['cpayee'];
			$cchecko = $row['ccheckno'];
			
			//if($cntr>1){
				echo "<tr><td colspan='4'>&nbsp;</td></tr>";
			//}

		?>  
		  <tr>
			<td colspan="4">
			
			<div class="col-xs-12">
            
            	<div class="col-xs-2">
                	<b><?php echo $ctran;?></b>
                </div>
                <div class="col-xs-2">
                	<b><?php echo $ddate;?></b>
                </div>
                <div class="col-xs-5">
                	<b><?php echo $cpayee;?></b>
                </div>
                <div class="col-xs-3">
                	<b><?php echo "Check No.: ".$cchecko;?></b>
                </div>
                
            </div>
            
            
			</td>
		  </tr>
		<?php 

		}
		?>

    <tr>
    	<td><?php echo $row['acctno'];?></td>
    	<td><?php echo $row['ctitle'];?></td>
        <td align="right"><?php if($row['ndebit'] <> 0) 
		{ 
			echo number_format($row['ndebit'],2) ;
			
				$ntotdebit = $ntotdebit + $row['ndebit'] ;

		}
		
		?></td>
        <td align="right">
		<?php if($row['ncredit'] <> 0) 
		{ 
			echo number_format($row['ncredit'],2) ;
			
			$ntotcredit = $ntotcredit + $row['ncredit'];

		}
		
		?></td>
    </tr>
    
    <?php
    

	}
	
	?>
    <tr>
      <td colspan="2" align="right" ><b>TOTAL</b></td>
      <td align="right" style="border-top:5px double; border-bottom:8px double; padding-top:6px; padding-bottom:6px"><b>
      <?php if($ntotdebit <> 0) 
		{ 
			echo number_format($ntotdebit,2) ;
			
		}
		
		?></b>
      </td>
      <td align="right" style="border-top:5px double; border-bottom:8px double; padding:5px; padding-top:6px; padding-bottom:6px"><b>
      <?php if($ntotcredit <> 0) 
		{ 
			echo number_format($ntotcredit,2) ;
			
		}
		
	  ?></b>
      </td>
    </tr>

</table>

</body>
</html>