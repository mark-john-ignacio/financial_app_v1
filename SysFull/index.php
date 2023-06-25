<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

$company = $_SESSION['companyid'];

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ABOVECL'"); 

	if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		$nallowval = $all_course_data["cvalue"];
	}		


	$result1 = mysqli_query($con,"SELECT dcutdate FROM `sales` where lcancelled=0 order By ctranno desc Limit 1"); 

	if (mysqli_num_rows($result1)!=0) {
	 $all_course_data1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
	 
		$ndcutdate = $all_course_data1["dcutdate"];
	}		

?>

        <?php
		$sql = "select * from groupings where ctype='ITEMCLS' LIMIT 1";
		$result=mysqli_query($con,$sql);
		$rowcnt = mysqli_num_rows($result);
		
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$cclascoddef = $row["ccode"];
		
		}
		?>	

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
   	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
    
	

	<style type='text/css'>
		.deleterow{cursor:pointer}
		.column-left{ float: left; width: 50%;}
		.column-right{ float: right; width: 25%; padding-left:5px}
		.column-center{ display: inline-block; width: 25%; padding-left:5px }   
		.black_overlay{
			display: none;
			position: absolute;
			top: 0%;
			left: 0%;
			width: 100%;
			height: 100%;
			background-color: black;
			z-index:1001;
			-moz-opacity: 0.5;
			opacity:.50;
			filter: alpha(opacity=50);
		}
		.white_content {
			display: none;
			position: absolute;
			top: 50%;
			left: 50%;
			/* bring your own prefixes */
			transform: translate(-50%, -50%);
			width: 80%;
			height: 80%;
			padding: 5px;
			border: 5px solid SteelBlue ;
			background-color: white;
			z-index:1002;
			overflow: auto;
		}

		.hdr {
			border: 2px solid #06F;
			width: 100%;
			height: 50px;
			border-radius: 5px;
			padding-left:10px;
			background: blue; /* For browsers that do not support gradients */
			background: -webkit-linear-gradient(top left, #06C, #0CF); /* For Safari 5.1 to 6.0 */
			background: -o-linear-gradient(right bottom, #06C, #0CF); /* For Opera 11.1 to 12.0 */
			background: -moz-linear-gradient(right bottom, #06C, #0CF); /* For Firefox 3.6 to 15 */
			background: linear-gradient(top right bottom, #06C, #0CF); /* Standard syntax */
		}

	</style>

</head>

<body style="padding:10px; background-color:#006" onLoad="document.getElementById('txtcustid').focus();">
	<form action="newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
		<input type="hidden" name="txtsubmit" id="txtsubmit" value="1">
		<input type='hidden' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" readonly/>
		
		<input type="hidden" value="Goods" name="seltype" id="seltype">

		<div class="column-left">
			<table width="100%" border="0">
				<tr>
					<td colspan="3">
						<font style="font:normal 12pt Arial; color:#FFF"><b>
							<?php
								$ccomp = $_SESSION['companyid'];
								$result = mysqli_query($con,"SELECT * FROM `company` WHERE compcode='$ccomp'"); 

								if (mysqli_num_rows($result)!=0) {
								$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
								
									$cnme = $all_course_data['compname']; 
									
								}
								else{
									
									$cnme = ""; 
									
								}
									echo $cnme;
							?>
						
						&nbsp;|&nbsp;
						<?php echo date("D, F d, Y");?>
						&nbsp;|&nbsp;
						User Name: <?php echo $_SESSION['employeename']; ?>     </b>
						</font>
					</td>
				</tr>
				<tr>
					<tH width="100" rowspan="3"><span style="padding:2px"><img src="../images/blueX.png" width="100" height="100" style="border:solid 1px  #06F;" name="imgemp" id="imgemp"></span></tH>
					<tH width="100"><font color="#FFFFFF">&nbsp;CUSTOMER</font></tH>
					<td colspan="3" style="padding:2px">
						<div class="col-xs-4 nopadding">
							<input type="text" class="form-control" id="txtcustid" name="txtcustid" width="20px" tabindex="1" placeholder="ID..." required style="font-size:28px; height:38px;">
							
						</div>
						<div class="col-xs-8 nopadwleft" style="padding-left:10px">
							<input type="text" class="form-control" id="txtcustname" name="txtcustname" tabindex="1" placeholder="Name..." style="font-size:28px; height:38px;" autocomplete="off">
							
						</div> 
						<input type="hidden" id="hdnabvecl" name="hdnabvecl" value="<?php echo $nallowval;?>">

					</td>
				</tr>
					<tr>
						<tH width="100"><font color="#FFFFFF">&nbsp;CREDIT</font></tH>
						<td style="padding:2px">

						<input type="text" id="txtncredit" name="txtncredit"  readonly style="border:none; background:none; font-size:18px; color:#FFF; font-weight:bold; text-align:left">    
						
						</td>
					</tr>
					<tr>
						<th><font color="#FFFFFF">&nbsp;BALANCE</font></th>
						<td style="padding:2px">
							<input type="text" id="txtncreditbal" name="txtncreditbal"  readonly  style="border:none; background:none; font-size:18px; color:#FFF; font-weight:bold; text-align:left">
						</td>
					</tr>
					<tr>
						<tH>&nbsp;</tH>
						<th><font color="#FFFFFF">&nbsp;AMT DUE</font></th>
						<td style="padding:2px">   <input type="text" id="txtnDue" name="txtnDue" readonly value="0" style="text-align:left; font-size:18px; font-weight:bold; color:#F00; border:none; background:none"> 
					</td>
  				</tr>
				<tr>
					<tH>&nbsp;</tH>
					<th><font color="#FFFFFF">&nbsp;TOTAL PAYED</font></th>
					<td style="padding:2px">
						<?php
					
							if($nallowval=="Deny"){

						?>
					
							<div class="col-xs-9">
							<input type="hidden" id="txtnPayed" name="txtnPayed" value="0">
							<font color="#FFFFFF"><?php echo "Payment Not Allowed!" ?></font>
							</div>

						<?php

							}
							elseif($nallowval=="Allow"){

						?>
					
						<input type="text" id="txtnPayed" name="txtnPayed" value="0" style="text-align:left; font-size:18px; font-weight:bold; color:#F00;" onFocus="this.select();" class="input-sm" size="20">

						<?php
						}
						?>

					</td>
				</tr>
				<tr>
					<td colspan="3" height="50">
						<div class="col-xs-4 nopadding">          
							<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Bar Code..." width="25" tabindex="3">
						</div>
						<div class="col-xs-7 nopadwleft"  style="padding-left:10px">           
							<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="Search Product Name..." size="80" tabindex="4">
						</div>
						<input type="hidden" name="hdnprice" id="hdnprice">
						<input type="hidden" name="hdnunit" id="hdnunit"> 
						<input type="hidden" name="hdndiscount" id="hdndiscount">
					</td>
				</tr>
				<tr>
    				<td colspan="3" height="50">
						<div class="alt2" dir="ltr" style="
							margin: 0px;
							padding: 5px;
							border: 2px solid #06F;
							width: 100%;
							height: 50vh;
							text-align: left;
							overflow: auto; border-radius: 10px; background: #fff">
	
							<table id="MyTable" class="MyTable" cellpadding"1px" width="100%" border="0">

								<tr bgcolor="#0CF">
									<!--<th style="border-bottom:1px solid #999">&nbsp;&nbsp;&nbsp;Code</th>-->
									<th style="border-bottom:1px solid #999">Description</th>
									<th style="border-bottom:1px solid #999">UOM</th>
									<th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">Price</th>
									<th style="border-bottom:1px solid #999">Discount</th>
									<th style="border-bottom:1px solid #999">Amount</th>
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
								<tbody class="tbody">
								</tbody>
                    
    						</table>
						</div>

					</td>
				</tr>

  				<tr>
    				<td colspan="3" height="50">
    
						<table width="100%" border="0" cellpadding="3">   
							<tr>
								<td colspan="3" style="padding-top:10px"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
									<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
										<table align="center">
											<tr>
											<td width="20" height="35" style="padding-right:5px;"><b><u>Save</u></b> (F2)</td>
											</tr>
										</table>
									</button>
									<button type="button" class="btn btn-success btn-sm" tabindex="6" id="btnopen" name="btnopen">
										<table align="center">
											<tr>
											<td width="20" height="35" style="padding-right:5px;"><b><u>View Sales</u></b> (F4)</td>
											</tr>
										</table>
									</button>
									<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="window.location.href='../logout.php'" id="btnout" name="btnout">
										<table align="center">
											<tr>
											<td width="20" height="35" style="padding-right:5px;"><b><u>Exit</u></b> (ESC)</td>
											</tr>
										</table>
									</button>
								
								
									<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="location.reload();" id="btnout" name="btnout">
										<table align="center">
											<tr>
											<td width="20" height="35" style="padding-right:5px;"><b><u>Reset</u></b> (F1)</td>
											</tr>
										</table>
									</button>
								
								</td>
								<td><font color="#FFFFFF"><b>TOTAL AMOUNT : </b></font></td>
								<td>
									<div class="col-xs-10">
									<input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right;  font-size:20px; font-weight:bold; color:#F00;" class="form-control input-sm" size="10">
									</div>
								</td>
							</tr>
						</table>

					</td>
				</tr>

			</table>

		</div>


		<div  class="column-center">

			<div id="itmlist" style="
								margin: 0px;
								padding: 10px;
								border: 2px solid  #06F;
								width: 100%;
								height: 90vh;
								text-align: left;
								overflow: auto; border-radius: 10px; background: #fff">



			</div>
			<div style="
								margin: 0px;
								padding-top: 2px;
								border: 0px;
								width: 100%;
								height: 70px;
								text-align: left;
								overflow: auto;" >


				<table width="100%" border="0" cellpadding="3">
					<tr>
						<td style="padding-top:5px;" nowrap>
							<?php
							$sql = "select * from groupings where ctype='ITEMCLS' order by cdesc";
							$result=mysqli_query($con,$sql);
							$rowcnt = mysqli_num_rows($result);
							
								if (!mysqli_query($con, $sql)) {
									printf("Errormessage: %s\n", mysqli_error($con));
								}			

							while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
							?>	
						
							<button type="button" class="btndcust btn btn-primary btn-xs" id="btn<?php echo $row["ccode"]?>" name="btn<?php echo $row["ccode"]?>" data-id="<?php echo $row["ccode"]?>">
								<table align="center">
									<tr>
									<td width="20" height="35" style="padding-right:1px;"><?php echo $row["cdesc"]?></td>
									</tr>
								</table>
							</button>
							<?php	
							}
							?>   	
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="column-right">
			<div id="itmlist2" style="
					margin: 0px;
					padding: 10px;
					border: 2px solid  #06F;
					width: 100%;
					height: 90vh;
					text-align: left;
					overflow: auto; border-radius: 10px; background: #fff">

				<fieldset>
					<legend>Current Transactions <?php echo date("m/d/Y");?></legend>
				</fieldset>

        		<div id="tranlist">
					<table width="100%" class="table table-striped">
						<?php
									$sql = "select a.*,b.cname from sales a left join customers b on a.ccode=b.cempid Where dcutdate=CURDATE() order by ctranno DESC LIMIT 4";
									$result=mysqli_query($con,$sql);
									
										if (!mysqli_query($con, $sql)) {
											printf("Errormessage: %s\n", mysqli_error($con));
										} 
										
									while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
										
						if(!file_exists("../imgcust/".$row["ccode"] .".jpg")){
							$imgsrc = "../images/emp.jpg";
						}
						else{
							$imgsrc = "../imgcust/".$row["ccode"] .".jpg";
						}
						
						if($row["lcancelled"]==1){
							$cstate = " <b>(Cancelled)</b>";
						}
						else{
							$cstate = "";
						}


						?>
						<tr>
							<td rowspan="2"><img src="<?php echo $imgsrc;?>" width="100" height="100" align="absmiddle"></td>
							<td><?php echo $row["ctranno"].$cstate;?><br><?php echo $row["ngross"]?></td>
						</tr>
						<tr>
							<td><font size="4"><?php echo $row["cname"];?></font></td>
						</tr>
					
						<?php 
							}
									
							mysqli_close($con);
									
						?>

					</table>

       			</div>

			</div>

			<div style="
					margin: 0px;
					padding-top: 7px;
					border: 0px;
					width: 100%;
					height: 70px;
					text-align: left;
					overflow: auto; text-align:right" >


				<button type="button" class="btn btn-danger btn-xs" tabindex="6" id="btnupload" name="btnupload"  data-title="UPLOAD SALES">
					<table align="center">
						<tr>
						<td width="20" height="35" style="padding-right:5px;"><b><u>UPLOAD SALES</u></b> (F9)</td>
						</tr>
					</table>
				</button>
					&nbsp;
				<button type="button" class="btn btn-danger btn-xs" tabindex="6" id="btnreport" name="btnreport"  data-title="SALES REPORT">
					<table align="center">
						<tr>
						<td width="20" height="35" style="padding-right:5px;"><b><u>Sales Report</u></b> (F8)</td>
						</tr>
					</table>
				</button>
					&nbsp;
				<button type="button" class="btn btn-xs" tabindex="6" id="btnbackup" name="btnbackup"  data-title="BACKUP DATA">
					<table align="center">
						<tr>
						<td width="20" height="35" style="padding-right:5px;"><img src="../images/backup.png" width="30" height="30"></td>
						</tr>
					</table>
				</button>

			</div>

		</div>



		<div id="light" class="white_content">
			<input type="hidden" name="hdnrowcnt2" id="hdnrowcnt2">
			<div align="right"><a href = "javascript:void(0)" onclick = "chkCloseInfo();">Close</a></div>
			<table id="MyTable2" class="MyTable2" cellpadding"3px" width="100%" border="0">
				<tr>
					<th style="border-bottom:1px solid #999">Code</th>
					<th style="border-bottom:1px solid #999">Description</th>
					<th style="border-bottom:1px solid #999">Field Name</th>
					<th style="border-bottom:1px solid #999">Value</th>
					<th style="border-bottom:1px solid #999">&nbsp;</th>
				</tr>
			</table>
		
		</div>
    
    	<div id="fade" class="black_overlay"></div>

	</form>

	<div id="dialog" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" style="height: 750px !important;">

				<div class="modal-body">
					<iframe frameborder="0" scrolling="no" width="100%" height="700" id="myIframe"></iframe>
				</div>

			</div>

		</div>
	</div>

	<div id="dialog2" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" style="height: 250px !important;">

				<div class="modal-body">
					<iframe frameborder="0" scrolling="no" width="100%" height="200" id="myIframe2"></iframe>
				</div>

			</div>

		</div>
	</div>

	<div id="dialog3" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" style="height: 250px !important;">

				<div class="modal-body">
					<iframe frameborder="0" scrolling="no" width="100%" height="200" id="myIframe3"></iframe>
				</div>

			</div>

		</div>
	</div>

	<div id="dialog4" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" style="height: 250px !important;">

				<div class="modal-body">
					<iframe frameborder="0" scrolling="no" width="100%" height="200" id="myIframe4"></iframe>
				</div>

			</div>

		</div>
	</div>

	<div id="diapayed" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" style="height: 200px !important;">

				<div class="modal-body">
					<center>
					<span id="payedtxt"></span>
					<input name="hdnPayedamt" id="hdnPayedamt" type="text" class="form-control input-lg" >

					<span id="changetxt" style="padding-top:10px">Change: </span>
					</center>
				</div>

			</div>

		</div>
	</div>

</body>
</html>

	<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

	<script src="../Bootstrap/js/bootstrap.js"></script>
	<script src="../Bootstrap/js/moment.js"></script>
	<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<script>
	$(function(){
		var globalTitle = ''; // Your global variable

		$('#hdnPayedamt').keyup(function(event){
			//alert(event.keyCode);
				if(event.keyCode === 13){
					//alert(document.getElementById("hdnPayedamt").value);
					document.getElementById("txtnPayed").value=document.getElementById("hdnPayedamt").value;
					return chkform();
					//$( "#diapayed" ).dialog( "close" );
					
				}
				else{
						var payedamt = document.getElementById("hdnPayedamt").value;
						var dueamt = document.getElementById("txtnDue").value;
						
						if(parseFloat(payedamt) > parseFloat(dueamt)){
							var Tot = parseFloat(payedamt) - parseFloat(dueamt);
								if (Tot.toFixed(4) < 0){
									var Tot = Math.abs(Tot);
								}
						}
						else{
							//var Tot = parseFloat(CBal) - parseFloat(NGross);
							var Tot = 0;
						}
						
						document.getElementById("changetxt").innerHTML = "<font color='#F00'>Change: "+Tot.toFixed(4)+"</font>";
					
				}
		});

		$( "#btnopen" ).click(function() {
			$('#myIframe').attr('src', 'list.php');
			//$( "#dialog" ).dialog( "open" );
			$( "#dialog" ).modal("show");
		});

		


		$( "#btnreport" ).click(function() {
			$('#myIframe2').attr('src', '../Reports/TotalSales.php');
			//$( "#dialog2" ).dialog( "open" );
			$( "#dialog2" ).modal("show");

		});

		

		$( "#btnupload" ).click(function() {
			$('#myIframe3').attr('src', '../SalesUp/upload.php');
			//$( "#dialog3" ).dialog( "open" );
			$( "#dialog3" ).modal("show");

		});

		

		$( "#btnbackup" ).click(function() {
			$('#myIframe4').attr('src', '../SalesBackUp/index.php');
			//$( "#dialog4" ).dialog( "open" );
			$( "#dialog4" ).modal("show");

		});


		//$("#dialog").dialog({
		//	autoOpen: false,
		//	title: "TRANSACTIONS LIST",
		//	draggable: false,
		///	width : 800,
		//	height : 400, 
		//	resizable : false,
		//	modal : true,
		//});

		//$("#dialog2").dialog({
		//	autoOpen: false,
		//	title: "SALES REPORT",
		//	draggable: false,
		//	width : 900,
		//	height : 300, 
		//	resizable : false,
		//	modal : true,
		//});

		//$("#dialog3").dialog({
		//	autoOpen: false,
		//	title: "UPLOAD SALES",
		//	draggable: false,
		//	width : 900,
		//	height : 300, 
		//	resizable : false,
		//	modal : true,
		//});

		//$("#dialog4").dialog({
		//	autoOpen: false,
		//	title: "BACKUP DATA",
		//	draggable: false,
		//	width : 900,
		//	height : 300, 
		//	resizable : false,
		//	modal : true,
		//});


		//$("#diapayed").dialog({
		//	autoOpen: false,
		//	title: "Press Enter After Input...",
		//	draggable: false,
		//	width : 500,
		//	height : 300, 
		//	resizable : false,
		//	modal : true,
		//}).css("font-size", "36px");

		
		$('.btndcust').click(function() {
			var xyz = $(this).data("id");	
				$.ajax({ 
					url: 'getItms.php?',
					data: "id="+xyz,
					type: 'post',
					success: function(html)
					{
						$("#itmlist").html(html);
					}
				});
		});

		$("#txtprodid").keyup(function(event){
			//alert(event.keyCode + " - " + $(this).val());
			if(event.keyCode != 13){
				
				//alert("get_productid.php?c_id="+ $(this).val());
			$.ajax({
			type:'post',
			url:'get_productid.php',
			data: 'c_id='+ $(this).val() + '&custid=' + $('#txtcustid').val(),                 
			success: function(value){
				var data = value.split(",");
				$('#txtprodid').val(data[0]);
				$('#txtprodnme').val(data[1]);
				$('#hdnprice').val(data[2]);
				$('#hdnunit').val(data[3]);
				$("#hdndiscount").val(data[4]);
			

			if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
				var rowCount = $('#MyTable tr').length;
				var isItem = "NO";
				var itemindex = 1;
			
				if(rowCount > 1){
				var cntr = rowCount-1;
				
				for (var counter = 1; counter <= cntr; counter++) {
					// alert(counter);
					if($("#txtprodid").val()==$("#txtitemcode"+counter).val()){
						isItem = "YES";
						itemindex = counter;
						//alert($("#txtitemcode"+counter).val());
						//alert(isItem);
					//if prd id exist
					}
				//for loop
				}
			//if rowcount >1
				}
			//if value is not blank
			}
			
			if(isItem=="NO"){		

				myFunctionadd();
				computeGross();	
				
			}
			else{
				
				addqty();
			}
			
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnprice").val("");
			$("#hdnunit").val("");
			$("#hdndiscount").val("");
	
			//closing for success: function(value){
			}
			}); 
			
			}
		});

		$('#txtcustid').keyup(function(event){
			if(event.keyCode === 13){
			//alert("Enter");
			var dInput = this.value;
			$.ajax({
				type:'post',
				url:'get_customer.php',
				data: 'c_id='+ $(this).val(),                 
				success: function(value){
				//alert(value);
				if(value!=""){
					//alert(value);
					var data = value.split(":");
					$('#txtcustname').val(data[0]);
					$('#txtncredit').val(data[1]);
					$('#imgemp').attr("src",data[2]);
					
					chkbalance(dInput,data[1]);
					loaditm();
					
					
					
					document.getElementById("txtcustid").disabled = true;
					document.getElementById("txtcustname").disabled = true;
					
					document.getElementById("txtprodid").focus();
					
				}
				else{
					$('#txtcustid').val("");
					$('#txtcustname').val("");
					$('#txtncredit').val("");
					$('#txtncreditbal').val("");
					$('#imgemp').attr("src","../images/blueX.png");
				}
			},
				error: function(){
					$('#txtcustid').val("");
					$('#txtcustname').val("");
					$('#txtncredit').val("");
					$('#txtncreditbal').val("");
					$('#imgemp').attr("src","../images/blueX.png");
				}
			});
			}
		});

		//custname searching
		$('#txtcustname').typeahead({
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "get_custname.php",
					dataType: "json",
					data: {
						query: $("#txtcustname").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.cempid + '</span><br><small>' + item.cname + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$("#txtcustname").val(item.cname).change();
				$("#txtcustid").val(item.cempid);
				$("#txtncredit").val(item.nlimit);
				$('#imgemp').attr("src",item.imgsrc);

				chkbalance(item.cempid,item.nlimit);
			
				if($('#txtcustid').val()!=""){
						loaditm();
											
						document.getElementById("txtcustid").disabled = true;
						document.getElementById("txtcustname").disabled = true;

						document.getElementById("txtprodid").focus();
						
				}
			}

		});

		$('form').submit(function() {
			if(typeof jQuery.data(this, "disabledOnSubmit") == 'undefined') {
				jQuery.data(this, "disabledOnSubmit", { submited: true });
				$('input[type=submit], input[type=button]', this).each(function() {
					$(this).attr("disabled", "disabled");
				});
				return true;
			}
			else
			{
				return false;
			}
		});

	});


	$(document).keydown(function(e) {	 
	  	if(e.keyCode == 113) { //F2
			return chkform();
	  	}
	  	else if(e.keyCode == 112){ //ESC
		  	e.preventDefault();
		  	location.reload();
	  	}
	  	else if(e.keyCode == 27){ //ESC
		  	e.preventDefault();
		  	window.location.replace("../logout.php");  
	 	 }
	  	else if(e.keyCode == 115){ //F4
	   		$('#myIframe').attr('src', 'list.php');
			//$( "#dialog" ).dialog( "open" );
			$( "#dialog" ).modal( "show" );
			
			//$("#dialog").dialog({
			//	autoOpen: false,
			//	position: 'center' ,
			//	title: 'TRANSACTIONS LIST',
			//	draggable: false,
			//	width : 800,
			//	height : 400, 
			//	resizable : false,
			//	modal : true,
			//});
		}
	  	else if(e.keyCode == 119){ //F8
	   		$('#myIframe2').attr('src', '../Reports/TotalSales.php');
			//$( "#dialog2" ).dialog( "open" );
			$( "#dialog2" ).modal( "show" );
			
			//$("#dialog2").dialog({
			//	autoOpen: false,
			//	position: 'center' ,
			//	title: 'SALES REPORT',
			//	draggable: false,
			//	width : 900,
			//	height : 300, 
			//	resizable : false,
			//	modal : true,
			//});
	  	}
	  	else if(e.keyCode == 120){ //F8
	   		$('#myIframe3').attr('src', '../SalesUp/upload.php');
		//	$( "#dialog3" ).dialog( "open" );
			$( "#dialog3" ).diamodallog( "show" );
			
			//$("#dialog3").dialog({
			//	autoOpen: false,
			//	position: 'center' ,
			//	title: 'UPLOAD SALES',
			//	draggable: false,
			//	width : 900,
			//	height : 300, 
			//	resizable : false,
			//	modal : true,
			//});
	  	}


	});


	function chkbalance(id,xcred){
		//Check Credit Limit BALNCE here

		var xBalance = 0;
		var xinvs = 0;
		var xors = 0;
		var dte = $("#date_delivery").val();
		
			$.ajax ({
				url: "get_creditbal.php",
				data: { id: id },
				async: false,
				dataType: "json",
				success: function( data ) {
												
					console.log(data);
					$.each(data,function(index,item){
						if(item.invs!=null){
							xinvs = item.invs;
						}
						
						if(item.ors!=null){
							xors = item.ors;
						}
						
					});
				}
			});
		
		//alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
			
		xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);
		//$("#txtncreditbal").val(xBalance);
		
		if(xBalance > 0){
			//xBalance = Number(xBalance).toLocaleString('en', { minimumFractionDigits: 4 });
			$("#txtncreditbal").val(xBalance.toFixed(4));
		}
		else{

			$("#txtncreditbal").val(0);
			
		}
	}

	function myFunctionadd(){
		if(document.getElementById("txtcustid").value=="" || document.getElementById("txtcustname").value==""){
			alert("Valid Customer Required!");
		}
		else{
			if(document.getElementById("txtprodnme").value != "") {
				var itmcode = document.getElementById("txtprodid").value;
				var itmdesc = document.getElementById("txtprodnme").value;
				var itmprice = document.getElementById("hdnprice").value;
				var itmdiscount = document.getElementById("hdndiscount").value;
				var itmunit = document.getElementById("hdnunit").value;  

				//alert(itmcode);
						if(itmcode!="OTHERS"){
							var qry="readonly";
							
						}
						else{
							var qry="onKeyup=\"comamtprice(this.value,this.name,event.keyCode);\" onkeydown=\"return isNumber(event.keyCode)\" onBlur=\"chkdecimal(this.value,"+lastRow+");\" onfocus=\"this.select();\"";

							var itmprice = prompt("Please Input PRICE!", 1);

						}

						if(parseInt(itmdiscount)!=0){
							var discnt = (parseFloat(itmdiscount)/100) * parseFloat(itmprice);
							var finprice = parseFloat(itmprice) - parseFloat(discnt);
							
							var Tot = parseFloat(finprice);
						}
						else{
							var Tot = parseFloat(itmprice)
						}


				
				var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
				var lastRow = tbl.length;

				var a=document.getElementById('MyTable').insertRow(-1);
				//var u=a.insertCell(0);
				var v=a.insertCell(0);
					v.style.font = "normal 8pt Arial";
				var v2=a.insertCell(1);
					v2.style.width = "50px";
					v2.style.font = "normal 8pt Arial";
				var w=a.insertCell(2);
					w.style.width = "80px";
					w.style.padding = "1px";
				var x=a.insertCell(3);
					x.style.width = "100px";
					x.style.padding = "1px";
				var x2=a.insertCell(4);
					x2.style.width = "100px";
					x2.style.padding = "1px";
				var y=a.insertCell(5);
					y.style.width = "100px";
					y.style.padding = "1px";
				var z=a.insertCell(6);
					z.style.width = "80px";
					z.align = "right";
				//u.innerHTML = ""+itmcode;
				v.innerHTML = "<input type='hidden' value='"+itmcode+"' name='txtitemcode"+lastRow+"' id='txtitemcode"+lastRow+"'><input type='hidden' value='"+itmdesc+"' name='txtitemdesc"+lastRow+"' id='txtitemdesc"+lastRow+"'>"+itmdesc+"";
				v2.innerHTML = "<input type='hidden' value='"+itmunit+"' name='txtcunit"+lastRow+"' id='txtcunit"+lastRow+"'>"+itmunit;
				w.innerHTML = "<input type='text' value='1' class='form-control input-xs' style='text-align:right' name='txtnqty"+lastRow+"' id='txtnqty"+lastRow+"' onKeyup=\"computeamt(this.value,this.name,event.keyCode);\" onkeydown=\"return isNumber(event.keyCode)\" onBlur=\"chkdecimal(this.value,"+lastRow+");\" onfocus=\"this.select();\" tabindex=\"4\"><input type='hidden' value='"+itmunit+"' name='hdnmainuom"+lastRow+"' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='1' name='hdnfactor"+lastRow+"' id='hdnfactor"+lastRow+"'>";
				x.innerHTML = "<input type='text' value='"+itmprice+"' class='form-control input-xs' style='text-align:right' name='txtnprice"+lastRow+"' id='txtnprice"+lastRow+"' "+qry+">";
				x2.innerHTML = "<input type='text' value='"+itmdiscount+"' class='form-control input-xs' style='text-align:right' name='txtndisc"+lastRow+"' id='txtndisc"+lastRow+"' readonly>";
				y.innerHTML = "<input type='text' value='"+Tot+"' class='form-control input-xs' style='text-align:right' name='txtnamount"+lastRow+"' id='txtnamount"+lastRow+"' readonly>";
				z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='delete' onClick=\"deleteRow(this);\"/>  &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/>";
			}
		
		}
	
	}

	function deleteRow(r) {
		//alert(r);
			if(isNaN(r)==true){
				var i=r.parentNode.parentNode.rowIndex;
			}
			else{
				var i = r;
			}

				var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
				var lastRow = tbl.length;
				var itmcode = document.getElementById('txtitemcode' + i).value;
				
				document.getElementById('MyTable').deleteRow(i);
				document.getElementById('hdnrowcnt').value = lastRow - 2;
				var lastRow = tbl.length;
				var z; //for loop counter changing textboxes ID;
				
					for (z=i+1; z<=lastRow; z++){
						var tempcitemno = document.getElementById('txtitemcode' + z);
						var tempcdesc = document.getElementById('txtitemdesc' + z);
						var tempnqty= document.getElementById('txtnqty' + z);
						var tempcunit= document.getElementById('txtcunit' + z);
						var tempnprice = document.getElementById('txtnprice' + z);
						var tempnamount= document.getElementById('txtnamount' + z);
						var tempmainuom = document.getElementById('hdnmainuom' + z);
						var tempfactor= document.getElementById('hdnfactor' + z);
						
						var x = z-1;
						tempcitemno.id = "txtitemcode" + x;
						tempcitemno.name = "txtitemcode" + x;
						tempcdesc.id = "txtitemdesc" + x;
						tempcdesc.name = "txtitemdesc" + x;
						tempnqty.id = "txtnqty" + x;
						tempnqty.name = "txtnqty" + x;
						tempcunit.id = "txtcunit" + x;
						tempcunit.name = "txtcunit" + x;
						tempnprice.id = "txtnprice" + x;
						tempnprice.name = "txtnprice" + x;
						tempnamount.id = "txtnamount" + x;
						tempnamount.name = "txtnamount" + x;
						tempmainuom.id = "hdnmainuom" + x;
						tempmainuom.name = "hdnmainuom" + x;
						tempfactor.id = "hdnfactor" + x;
						tempfactor.name = "hdnfactor" + x;
						
						//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

					}
			
				//alert(itmcode);
				
				computeGross();
				delInfoCode(itmcode);
				document.getElementById("txtprodnme").focus();
	}

	function computeamt(valz,str,keyCode){
		//var r = parseInt(str.slice(-1));
		var numberPattern = /\d+/g;
		var r = parseInt(str.match(numberPattern));

		
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(keyCode==38 || keyCode==40 || keyCode==45 || keyCode==46 || keyCode==39){
			
			if(keyCode==38 && r!=1){
				//alert(r + ":"+ keyCode +"taas");
				var z = r - 1;
				document.getElementById("txtnqty"+z).focus();
			}
			
			if(keyCode==40 && r!=parseInt(lastRow)){
				//alert(r + ":"+ keyCode+"baba");
				var z = r + 1;
				document.getElementById("txtnqty"+z).focus();
			}

			if(keyCode==39 && document.getElementById("txtitemcode"+r).value=="OTHERS"){
				//alert(r + ":"+ keyCode+"kanan" DAPAT OTHERS);
				document.getElementById("txtnprice"+r).focus();
			}
		
			if(keyCode==45){
				var itmcode = document.getElementById("txtitemcode"+r).value;
				var itmdesc = document.getElementById("txtitemdesc"+r).value;
				viewhidden(itmcode,itmdesc);
			}

			if(keyCode==46){
				//var z = r + 1;
				//alert(z);
				deleteRow(r);		
			}
			
		}
		else{
			var txtprice = document.getElementById("txtnprice" + r).value;
		
			var pattern = /^-?[0-9]+(.[0-9]{1,4})?$/; 
			var text = valz;
		
			if (text.match(pattern)==null) 
			{
				//alert("Invalid Quantity");
				//with (document.forms[0].elements[nqtyname])
				//{
				//value = 1
				//}
				document.getElementById("txtnamount"+r).value = "0";
				//document.getElementById("txtnqty"+r).value = "0";
		
			}
			else {
				var Tot = parseFloat(txtprice) * parseFloat(valz);
				document.getElementById("txtnamount" + r).value = Tot.toFixed(4);
				
				computeGross();
				
			}
		}

		
	}

	function comamtprice(valz,str,keyCode){
		//var r = parseInt(str.slice(-1));
		var numberPattern = /\d+/g;
		var r = parseInt(str.match(numberPattern));

		
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(keyCode==38 || keyCode==40 || keyCode==45 || keyCode==46 || keyCode==37){
			
			if(keyCode==38 && r!=1){
				//alert(r + ":"+ keyCode +"taas");
				var z = r - 1;
				document.getElementById("txtnprice"+z).focus();
			}
			
			if(keyCode==40 && r!=parseInt(lastRow)){
				//alert(r + ":"+ keyCode+"baba");
				var z = r + 1;
				document.getElementById("txtnprice"+z).focus();
			}

			if(keyCode==37 && document.getElementById("txtitemcode"+r).value=="OTHERS"){
				//alert(r + ":"+ keyCode+"kaliwa" DAPAT OTHERS);
				document.getElementById("txtnqty"+r).focus();
			}
			
			if(keyCode==45){
				var itmcode = document.getElementById("txtitemcode"+r).value;
				var itmdesc = document.getElementById("txtitemdesc"+r).value;
				viewhidden(itmcode,itmdesc);
			}

			if(keyCode==46){
				//var z = r + 1;
				//alert(z);
				deleteRow(r);		
			}
			
		}
		else{
			var txtnqty = document.getElementById("txtnqty" + r).value;
		
			var pattern = /^-?[0-9]+(.[0-9]{1,4})?$/; 
			var text = valz;
		
			if (text.match(pattern)==null) 
			{
				//alert("Invalid Quantity");
				//with (document.forms[0].elements[nqtyname])
				//{
				//value = 1
				//}
				document.getElementById("txtnamount"+r).value = "0";
				//document.getElementById("txtnqty"+r).value = "0";
		
			}
			else {
				var Tot = parseFloat(txtnqty) * parseFloat(valz);
				document.getElementById("txtnamount" + r).value = Tot.toFixed(4);
				
				computeGross();
				
			}
		}

		
	}


	function computeGross(){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;

		var TotAmt = 0;
		for (z=1; z<=lastRow; z++){
			TotAmt += +document.getElementById("txtnamount" + z).value;
		}
		
		document.getElementById("txtnGross").value = TotAmt.toFixed(4);

		computeDue();
	}

	function computeDue(){
		var CBal = document.getElementById("txtncreditbal").value;
		var NGross = document.getElementById("txtnGross").value;
		
		if(parseFloat(NGross) > parseFloat(CBal)){
			var Tot = parseFloat(NGross) - parseFloat(CBal);
				if (Tot.toFixed(4) < 0){
					var Tot = Math.abs(Tot);
				}
		}
		else{
			//var Tot = parseFloat(CBal) - parseFloat(NGross);
			var Tot = 0;
		}
		
		document.getElementById("txtnDue").value = Tot.toFixed(4);
	}

	function isNumber(keyCode) {
		return ((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode == 189 || keyCode == 37 || keyCode == 110 || keyCode == 190 || keyCode == 39 || (keyCode >= 96 && keyCode <= 105 || keyCode == 9))
	}

	function chkdecimal(valz,r){
			var nqtyname = "txtnqty" + r
		
			var pattern = /^-?[0-9]+(.[0-9]{1,2})?$/; 
			var text = valz;
		
			if (text.match(pattern)==null) 
			{
				alert("Invalid Quantity.\nNote: 2 decimal places only");
				with (document.forms[0].elements[nqtyname])
				{
					value = 1;
					computeamt(1,r,0);
				}

			}
	}

	function addqty(){
		
		var itmcode = document.getElementById("txtprodid").value;
		//var itmdesc = document.getElementById("txtprodnme").value;
		//
		
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;

		var TotQty = 0;
		var TotAmt = 0;
		
		for (z=1; z<=lastRow; z++){
			if(document.getElementById("txtitemcode"+z).value==itmcode){
				var itmqty = document.getElementById("txtnqty"+z).value;
				var itmprice = document.getElementById("txtnprice"+z).value;
				
				TotQty = parseFloat(itmqty) + 1;
				document.getElementById("txtnqty"+z).value = TotQty;
				
				TotAmt = parseFloat(document.getElementById("txtnamount" + z).value) + parseFloat(itmprice);
				document.getElementById("txtnamount" + z).value = TotAmt.toFixed(4);
			}

		}
		
		computeGross();

	}

	function chkform(){
		var ISOK = "YES";
		document.getElementById("txtcustid").disabled = false;
		document.getElementById("txtcustname").disabled = false;
		
		if(document.getElementById("txtcustname").value=="" && document.getElementById("txtcustid").value==""){
			alert("Customer Required!");
			document.getElementById("txtcustid").focus();
			return false;
			
			ISOK = "NO";
		}
		// ACTIVATE MUNA LAHAT NG INFO
		
		var tbl2 = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow2 = tbl2.length-1;
		var z2;
		//alert(lastRow2);
		
		if(lastRow2>=1){
			for (z2=1; z2<=lastRow2; z2++){
					document.getElementById("txtinfofld"+z2).disabled = "";
					document.getElementById("txtinfoval"+z2).disabled = "";
					document.getElementById("info" + z2 + "_delete").className = "btn btn-danger btn-xs";
			}
		}

			
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		var NPayed = parseFloat(document.getElementById("txtnPayed").value);
		var NDue = parseFloat(document.getElementById("txtnDue").value);
		var CBal = parseFloat(document.getElementById("txtncreditbal").value); 
		var cAllowz = document.getElementById("hdnabvecl").value;
		
		//alert(document.getElementById("hdnabvecl").value);
		if(lastRow == 0){
			alert("No details found!");
			return false;
			ISOK = "NO";
		}
		else{
			var msgz = "";
			for (z=1; z<=lastRow; z++){
				if(document.getElementById("txtnqty"+z).value == 0 || document.getElementById("txtnqty"+z).value == ""){
					msgz = msgz + "\n Zero or blank qty is not allowed: row " + z;	
				}
			}
			
			if(msgz!=""){
				alert("Details Error: "+msgz);
				return false;
				ISOK = "NO";
			}
		}
		
		//alert(cAllowz);

		if(cAllowz=="Allow"){
			if( NPayed > NDue || NPayed == NDue ){
				ISOK == "YES"
			}
			else{
				
						//var str = "Please pay total amount due!\nCredit Bal: "+CBal.toFixed(4)+"\nGross: "+NDue.toFixed(4)+"\nPayed: "+NPayed.toFixed(4);
						
						var str = "AMOUNT DUE: "+NDue.toFixed(4);
						//alert(str.fontsize(3));
						//var payedamt = prompt(str);
						document.getElementById("payedtxt").innerHTML  = "Amount Due: " + NDue.toFixed(4);
						
						//$( "#diapayed" ).dialog( "open" );

						$("#diapayed").modal("show");
						
						
						
						//if (payedamt != null) {
							//document.getElementById("txtnPayed").value = payedamt;
							//"Hello " + person + "! How are you today?";
							
							
						//}
						
						//  }
			
					//alert("Please pay total amount due!\nCredit Bal: "+CBal.toFixed(4)+"\nGross: "+NDue.toFixed(4)+"\nPayed: "+NPayed.toFixed(4));
				
					return false;
					ISOK = "NO";
					
					//document.getElementById("txtnPayed").focus();
			}
		}
		else{
			//alert(NDue);
			if( NDue==0 ){
				ISOK == "YES"
			}
			else{
				alert("Over Credit Limit for the cutoff period!");
				return false;
				ISOK = "NO";
			}
		}
		
		if(ISOK == "YES"){ 
			document.getElementById("btnSave").className = "btn btn-success btn-sm disabled"
			document.getElementById("hdnrowcnt").value = lastRow;
			document.getElementById("hdnrowcnt2").value = lastRow2;
			document.getElementById("frmpos").submit();
		}

	}

	function viewhidden(itmcde,itmnme){
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block'

		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow2 = tbl.length-1;
		var lastRownum = tbl.length;
		var z;
		//alert(lastRow2);
		
		if(lastRow2>=1){
			for (z=1; z<=lastRow2; z++){
				//alert(document.getElementById("txtinfocode"+z).value);
				if(document.getElementById("txtinfocode"+z).value!=itmcde){
					document.getElementById("txtinfofld"+z).disabled = "disabled";
					document.getElementById("txtinfoval"+z).disabled = "disabled";
					document.getElementById("info" + z + "_delete").className = "btn btn-danger btn-xs disabled";
				}
				else{
					document.getElementById("txtinfofld"+z).disabled = "";
					document.getElementById("txtinfoval"+z).disabled = "";
					document.getElementById("info" + z + "_delete").className = "btn btn-danger btn-xs";
				}
			}
		}
		
		addinfo(itmcde,itmnme);
		
		document.getElementById("txtinfofld"+lastRownum).focus();
		
		
	}

	function addinfo(itmcde,itmnme){
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length;

		var a=document.getElementById('MyTable2').insertRow(-1);
		var u=a.insertCell(0);
		var v=a.insertCell(1);
		var w=a.insertCell(2);
			w.style.padding = "1px";
		var x=a.insertCell(3);
			x.style.padding = "1px";
		var y=a.insertCell(4);
		u.innerHTML = "<input type='hidden' value='"+itmcde+"' name='txtinfocode"+lastRow+"' id='txtinfocode"+lastRow+"'>"+itmcde;
		v.innerHTML = "<input type='hidden' value='"+itmnme+"' name='txtinfodesc"+lastRow+"' id='txtinfodesc"+lastRow+"'>"+itmnme;
		w.innerHTML = "<input type='text' name='txtinfofld"+lastRow+"' id='txtinfofld"+lastRow+"' class='form-control input-xs' onKeyup=\"setInfofoc(event.keyCode);\">";
		x.innerHTML = "<input type='text' name='txtinfoval"+lastRow+"' id='txtinfoval"+lastRow+"' class='form-control input-xs' onKeyup=\"setInfofoc(event.keyCode);\">";
		y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='info" + lastRow + "_delete' class='delete' value='delete' onClick=\"delInfo(this);\"/>";


	}


	function delInfo(r) {
		
			if(isNaN(r)==true){
				var i=r.parentNode.parentNode.rowIndex;
			}
			else{
				var i = r;
			}

		//alert(i);

		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length;
		//var i=r.parentNode.parentNode.rowIndex;
		document.getElementById('MyTable2').deleteRow(i);
		document.getElementById('hdnrowcnt2').value = lastRow - 2;
		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;

			for (z=i+1; z<=lastRow; z++){
				var infocode = document.getElementById('txtinfocode' + z);
				var infodesc = document.getElementById('txtinfodesc' + z);
				var infofld= document.getElementById('txtinfofld' + z);
				var infoval= document.getElementById('txtinfoval' + z);
				var infodel = document.getElementById('info' + z + '_delete');
				
				var x = z-1;
				infocode.id = "txtinfocode" + x;
				infocode.name = "txtinfocode" + x;
				infodesc.id = "txtinfodesc" + x;
				infodesc.name = "txtinfodesc" + x;
				infofld.id = "txtinfofld" + x;
				infofld.name = "txtinfofld" + x;
				infoval.id = "txtinfoval" + x;
				infoval.name = "txtinfoval" + x;
				infodel.id = "info" + x + '_delete';
				infodel.name = "info" + x + '_delete';
			}
	}

	function chkCloseInfo(){
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		var z; //for loop counter changing textboxes ID;
		var isInfo = "TRUE";

		for (z=1; z<=lastRow; z++){
			if(document.getElementById("txtinfofld"+z).value=="" || document.getElementById("txtinfoval"+z).value==""){
				isInfo = "FALSE";
			}
		}
		
		if(isInfo == "TRUE"){
			document.getElementById("light").style.display='none';
			document.getElementById("fade").style.display='none';
			
			document.getElementById("txtprodnme").focus();
		}
		else{
			alert("Incomplete info values!");
		}
	}

	function setInfofoc(keyCode){
			if(keyCode==35){
				chkCloseInfo();		
			}

	}

	function delInfoCode(itm){
		var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		for (z=1; z<=lastRow; z++){
			//alert(document.getElementById('txtinfocode' + z).value +":"+ itm);
			if(document.getElementById('txtinfocode' + z).value==itm){
				delInfo(z);
			}
		}

	}

	function loaditm(){

			$.ajax({ 
					url: 'getItms.php?',
					data: "id=<?php echo $cclascoddef;?>",
					type: 'post',
					success: function(html)
					{
						$("#itmlist").html(html);
					}
				});
	}
	
</script>