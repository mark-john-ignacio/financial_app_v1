<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "POS.php";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	@$arritem = array();
	$getitems = mysqli_query($con,"SELECT * FROM `items` where compcode='$company'"); 
	if (mysqli_num_rows($getitems)!=0) {
		while($row = mysqli_fetch_array($getitems, MYSQLI_ASSOC)){
			@$arritem[] = array('cpartno' => $row['cpartno'], 'citemdesc' => $row['citemdesc']); 
		}
	}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
						<font size="+2"><u>Sales Uploaded Data</u></font>	
          </div>
        </div>

			<br><br>

			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Transaction No.</th>
						<th>Receipt No.</th>
            <th>Item</th>
						<th>Qty</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>

				<?php
					$witherr = 0;

					$sqlmainupload = mysqli_query($con,"select * from sales_upload where compcode='$company ' and cuserid = '$employeeid'");
					while($row = mysqli_fetch_array($sqlmainupload, MYSQLI_ASSOC)){

						$iswth = 0;
						foreach(@$arritem as $rxc){
							if(strtolower(trim($rxc['citemdesc']))==strtolower(trim($row['citem']))){
								$iswth = 1;
							}
						}

						if($iswth==0){
							$witherr++;
				?>
					<tr>
						<td><?=$row['ctranno']?></td>
						<td><?=$row['creceiptno']?></td>
            <td><?=$row['citem']?></td>
						<td><?=$row['nqty']?></td>
						<td><?="ITEM NOT FOUND";?>

						</td>
					</tr>
				<?php
						}
					}
				?>

				<input type="hidden" id="hdnstat" value="<?=$witherr?>">


				</tbody>
			</table>

			<div class="text-center mt-5 mb-5 text-primary" style="display: none" id="msgup"><h1>NO ERROR!<br> PLEASE WAIT WHILE SYSTEM IS PROCESSING</h1></div>
			<p class="countdown mt-5 text-center text-danger"></p>

		</section>
	</div>		


  <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	
</body>
</html>

<script type="text/javascript">

	$(document).ready(function(e) {
		if($("#hdnstat").val()==0){
			
			$("#msgup").show();

			var count = 5;
			var countdown = setInterval(function(){
				$("p.countdown").html("<h1>" + count + " seconds remaining!</h1>");
				if (count == 0) {
					clearInterval(countdown);
					location.href = 'POS_Del.php';
				}
				count--;
			}, 1000);



		}

		if($("#hdnstat").val()>=1){
			$("#msgup").hide();
			alert("With Error!");
		}
	});

</script>