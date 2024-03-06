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
					$compadd = $row['compadd'];
					$comptin = $row['comptin'];
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
<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<title>Accounts Payable</title>

<style>
	@media print {
		.my-table {
    	width: 100% !important;
		}
	}
</style>
</head>

<body style="padding:10px">
<h3><b>Company: <?=strtoupper($compname);  ?></b></h3>
<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
<h3><b>Kind of Book: ACCOUNTS PAYABLE JOURNAL</b></h3>
<h3><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h3>


<hr>
<table width="100%" border="0" align="center" cellpadding="2px" class="my-table">
  <tr>
    <th width="80">Acct Code</th>
    <th>Account Title</th>
    <th class="text-right" width="100">Debit</th>
    <th class="text-right" width="100">Credit</th>
  </tr>
  
<?php

	$sql = "Select b.ctranno, b.ccode, b.cpayee, b.cpaymentfor, a.cacctno, a.ctitle, a.ndebit, a.ncredit, b.dapvdate, b.lapproved
	From apv_t a 
	left join apv b on a.compcode=b.compcode and a.ctranno=b.ctranno
	where a.compcode='$company' and b.dapvdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid = 0 and (a.ncredit<>0 or a.ndebit<>0)
	order by b.ctranno";

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
			$cpaymentfor = "";
			$lapproved = "";
	
	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($ctran!=$row['ctranno']){
			$cntr++;		
			$ctran = $row['ctranno'];
			$ddate = $row['dapvdate'];
			$ccode = $row['ccode'];
			$cpayee = $row['cpayee'];
			$cpaymentfor = $row['cpaymentfor'];
			$lapproved = $row['lapproved'];
			
			//if($cntr>1){
				echo "<tr><td colspan='4'>&nbsp;</td></tr>";
			//}

		?>  
		  <tr>
			<td colspan="4">
			
						<div class="col-xs-12">
            
            	<div class="col-xs-4">
                	<b><?php echo $ctran;?>
									<?php
										if(intval($lapproved)==0){
											echo "(Unposted)";
										}
									?>
									</b>
                </div>
                <div class="col-xs-4">
                	<b><?php echo $ddate;?></b>
                </div>
                <div class="col-xs-4">
                	<b><?php echo $cpayee;?></b>
                </div>
                
                
            </div>
            <div class="col-xs-12">
              <b><?php echo $cpaymentfor;?></b>
						</div>
            
			</td>
		  </tr>
		<?php 

		}
		?>

    <tr>
    	<td><?php echo $row['cacctno'];?></td>
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