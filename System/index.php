<?php

if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "System_Set";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

$sqlhead=mysqli_query($con,"Select * from groupings where ctype='ITEMTYP' and cstatus='ACTIVE' and compcode='".$_SESSION['companyid']."'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		@$itmtype[] = array("ccode" => $row['ccode'], "cdesc" => $row['cdesc']);
	}
} 

$sqlhead=mysqli_query($con,"Select * from groupings where ctype='SUPTYP' and cstatus='ACTIVE' and compcode='".$_SESSION['companyid']."'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		@$suptype[] = array("ccode" => $row['ccode'], "cdesc" => $row['cdesc']); 
	}
} 

@$arsecs = array();
$sqlhead=mysqli_query($con,"Select * from locations where cstatus='ACTIVE' and compcode='".$_SESSION['companyid']."'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		@$arsecs[] = array("ccode" => $row['nid'], "cdesc" => $row['cdesc']); 
	}
}

$sqlhead=mysqli_query($con,"Select * From users where cstatus='Active' and Userid<>'Admin'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		@$ursnmse[] = array("userid" => $row['Userid'], "name" => $row['Fname'].(($row['Minit']!=="" && $row['Minit']!=="") ? " ": "").$row['Minit']." ".$row['Lname']);
	}
}

$isCheck = 0;
$service = "";
$sql = mysqli_query($con, "SELECT * FROM parameters WHERE compcode = '$company' AND ccode = 'SERVICE_FEE'");
if(mysqli_num_rows($sql) != 0){
	while($row = $sql -> fetch_assoc()){
		$isCheck = $row['nallow'];
		$service = $row['cvalue'];
	}
}

$account = "";
$accountDesc = "";
$sql = mysqli_query($con, "SELECT A.cvalue, B.cacctdesc FROM parameters A left join accounts B on A.compcode=B.compcode and A.cvalue=B.cacctid WHERE A.compcode = '$company' AND A.ccode = 'ACCOUNT_ENTRY'");
if(mysqli_num_rows($sql) != 0){
	while($row = $sql -> fetch_assoc()){
		$account = $row['cvalue'];
		$accountDesc = $row['cacctdesc'];
	}
}

////function listcurrencies(){ //API for currency list
//	global $con;
//	$apikey = $_SESSION['currapikey'];

//	try  
//	{  
//		$json = @file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}", true); //getting the file content
//		if($json==false)
//		{
//			return "False";
//		}
////	}  
//	catch (Exception $e)  
//	{  
		//echo $e->getMessage();  
//		return "False";
////	}
//}

@$qortype = array(array('ccode' => 'quote', 'cdesc' => 'Quotation'),array('ccode' => 'billing', 'cdesc' => 'Billing'));

?>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Myx Financials</title>
		<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>">
		<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
		<link rel="stylesheet" type="text/css" href="../Bootstrap/css/DigiClock.css"> 

		<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

		<link rel="stylesheet" type="text/css" href="../include/select2/select2.min.css?x=<?=time()?>"> 
		<script src="../include/select2/select2.full.min.js"></script>

		<style>
			.btn span.glyphicon {    			
				opacity: 0;				
			}
			.btn.active span.glyphicon {				
				opacity: 1;				
			}
		</style>
	</head>

	<body style="padding:5px">
		<input type='hidden' id='atitemtype' value='<?=json_encode(@$itmtype);?>'>
		<input type='hidden' id='atsupptype' value='<?=json_encode(@$suptype);?>'>
		<input type='hidden' id='atuserslst' value='<?=json_encode(@$ursnmse);?>'>
		<input type='hidden' id='atsections' value='<?=json_encode(@$arsecs);?>'>
		<input type='hidden' id='qotyplst' value='<?=json_encode(@$qortype);?>'> 

		<fieldset>
				<legend>System Setup</legend>

					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#home">Company Details</a></li>
						<li><a data-toggle="tab" href="#param">Parameters</a></li>
						<li><a data-toggle="tab" href="#sales">Sales &amp; Delivery</a></li>
						<li><a data-toggle="tab" href="#purch">Purchases</a></li>
						<li><a data-toggle="tab" href="#acct">Accounting</a></li>
						<li><a data-toggle="tab" href="#invntry">Inventory</a></li>
						<!--<li><a data-toggle="tab" href="#rpts">Reports</a></li>-->
						<li><a data-toggle="tab" href="#POS">Point of Sale</a></li>
					</ul>
							
					<div class="tab-content col-lg-12 nopadwtop2x">   

						<!-- COMPANY INFO -->
							<div id="home" class="tab-pane fade in active">
								<form name="frmcompinfo" id="frmcompinfo" method="post" enctype="multipart/form-data" action="">
									<div class="col-xs-12 nopadwdown">   
										<div style="display:inline" class="col-xs-3">
															
										</div>
														
										<div style="display:inline" class="col-xs-5"> 
											<div class="alert alert-danger nopadding" id="CompanyAlertMsg">
																	
											</div>
											<div class="alert alert-success nopadding" id="CompanyAlertDone">
																	
											</div>                
										</div>

										<div class="col-xs-12 nopadwtop">
											<table width="100%" border="0" cellpadding="0">
												<tr>
													<td width="180" rowspan="5" align="center">
														<?php 
															$imgsrc = "../images/COMPLOGO.png";
														?>
														<img src="<?php echo $imgsrc;?>" width="145" height="145" name="previewing" id="previewing">                       
													</td>
													<td width="200"><b>Registered Name:</b></td>
													<td style="padding:2px" colspan="3"><div class="col-xs-10 nopadding"><input type="text" name="txtcompanycom" id="txtcompanycom" class="form-control input-sm" placeholder="Company Name..." maxlength="90"></div></td>
												</tr>											
												<tr>
													<td><b>Business/Trade Name:</b></td>
													<td style="padding:2px" colspan="3">
														<div class="col-xs-10 nopadding">
															<input type="text" name="txtcompanydesc" id="txtcompanydesc" class="form-control input-sm" placeholder="Company Description..." maxlength="90" >
														</div>
													</td>
												</tr>
												<tr>
													<td><b>Address / ZIP Code:</b></td>
													<td style="padding:2px" colspan="3">
														<div class="col-xs-8 nopadwright">
															<input type="text" name="txtcompanyadd" id="txtcompanyadd" class="form-control input-sm" placeholder="Address..." maxlength="500">
														</div>
														<div class="col-xs-2 nopadding">
															<input type="text" name="txtcompanyzip" id="txtcompanyzip" class="form-control input-sm" placeholder="ZIP Code..." maxlength="25">
														</div>
													</td>
												</tr>
												<tr>                        
													<td><b>Email / Contact No.:</b></td>
													<td style="padding:2px" colspan="3">

														<div class="col-xs-4 nopadwright">
															<input type="text" name="txtcompanyemail" id="txtcompanyemail" class="form-control input-sm" placeholder="Email Address..." maxlength="255">
														</div>
														<div class="col-xs-6 nopadding">
														<input type="text" name="txtcompanycpnum" id="txtcompanycpnum" class="form-control input-sm" placeholder="Contact Nos..." maxlength="255">
														</div>

													</td>
												</tr>
												<tr>
													
													<td><b>Business Type / TIN:</b></td>
													<td style="padding:2px" colspan="3">

														<div class="col-xs-5 nopadwright">
															<select class="form-control input-sm" name="selcompanyvat" id="selcompanyvat">
															</select>
														</div>
														<div class="col-xs-5 nopadding">
															<input type="text" name="txtcompanytin" id="txtcompanytin" class="form-control input-sm" placeholder="TIN..." maxlength="50">
														</div>

													</td>
												</tr>
												<tr>
													<td align="center">
														<label class="btn btn-warning btn-xs">
															Browse Image&hellip; <input type="file" name="file" id="filecpmnid" style="display: none;">
														</label>
													</td>
													<td><b>Permit To Use Details:</b></td>
													<td style="padding:2px" colspan="3">

														<div class="col-xs-5 nopadwright">
														<input type="text" name="ptucode" id="ptucode" class="form-control input-sm" placeholder="PTU Code..." maxlength="100">
														</div>
														<div class="col-xs-5 nopadding">
															<input type="text" name="ptudate" id="ptudate" class="form-control input-sm" placeholder="PTU Date YYYY/MM/DD" maxlength="">
														</div>

													</td>
												</tr>
												<tr>
													<td colspan="5" align="center" style="height: 50px; vertical-align: bottom;"> <button class="btn btn-sm btn-success" name="btncompsave" id="btncompsave"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Company Details</button> </td>
												</tr>                     
											</table>
										</div>
									</div> 
								</form>
							</div>
						<!-- COMPANY INFO END -->

						<!-- PARAMETERS SETUP --> 
							<div id="param" class="tab-pane fade in">
								<div class="col-xs-12">
									<div class="col-xs-2 nopadwtop">
										<b>Base Currency</b>
									</div>                    
									<div class="col-xs-3 nopadwtop">
										<select class="form-control input-sm" name="selbasecurr" id="selbasecurr" onChange="setparamval('DEF_CURRENCY',this.value,'basecurrchkmsg')">
									
											<?php
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='DEF_CURRENCY'"); 
										
												if (mysqli_num_rows($result)!=0) {
													$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);									
													$nvalue = $all_course_data['cvalue']; 										
												}
												else{
													$nvalue = "";
												}

												//$objcurrs = listcurrencies();

											//	if($objcurrs=="False"){
													$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName from currency_rate");
													if (mysqli_num_rows($sqlhead)!=0) {
														while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
											?>
															<option value="<?=$row['id']?>" <?php if ($nvalue==$row['id']) { echo "selected='true'"; } ?>><?=$row['currencyName']?></option>
											<?php											
														}
													}
											//	}else{

												
											//		$objrows = json_decode($objcurrs, true);

											//		foreach($objrows['results'] as $rows){
											?>
												<!--		<option value="< ?//=$rows['id']?>" < ?php// if ($nvalue==$rows['id']) { echo "selected='true'"; } ?>>< ?//=$rows['currencyName']?></option>-->
											<?php
											//		}
											//	}
											?>
										</select>
									</div>

									<div class="col-xs-1 nopadwtop" id="basecurrchkmsg">
									</div>
								</div>
							
								<br><br>

								<p data-toggle="collapse" data-target="#itmgrpcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Items Groupings</b></u> <i>**Note: Press ENTER after you enter your description to save...</i></p>
									
									<div class="collapse in" id="itmgrpcollapse">
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 1</b>
												<div id="divcGroup1" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup1" name="txtGroup1" tabindex="11" placeholder="Enter Description..." data-content="cGroup1">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>

											<div class="col-xs-2 nopadwtop">
												<b>Group 6</b>
												<div id="divcGroup6" style="display:inline; padding-left:5px"></div>
											</div>
													
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup6" name="txtGroup6" tabindex="11" placeholder="Enter Description..." data-content="cGroup6">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 2</b>
												<div id="divcGroup2" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup2" name="txtGroup2" tabindex="11" placeholder="Enter Description..." data-content="cGroup2">
											</div>
								
								
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 7</b>
												<div id="divcGroup7" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup7" name="txtGroup7" tabindex="11" placeholder="Enter Description..." data-content="cGroup7">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 3</b>
												<div id="divcGroup3" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup3" name="txtGroup3" tabindex="11" placeholder="Enter Description..." data-content="cGroup3">
											</div>
														
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 8</b>
												<div id="divcGroup8" style="display:inline; padding-left:5px"></div>
											</div>
								
								
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup8" name="txtGroup8" tabindex="11" placeholder="Enter Description..." data-content="cGroup8">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
														<b>Group 4</b>
														<div id="divcGroup4" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup4" name="txtGroup4" tabindex="11" placeholder="Enter Description..." data-content="cGroup4">
											</div>             
								
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 9</b>
												<div id="divcGroup9" style="display:inline; padding-left:5px"></div>
											</div>
								
								
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup9" name="txtGroup9" tabindex="11" placeholder="Enter Description..."data-content="cGroup9">
											</div>           
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
														<b>Group 5</b>
														<div id="divcGroup5" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
														<input type="text" class="cgroup form-control input-sm" id="txtGroup5" name="txtGroup5" tabindex="11" placeholder="Enter Description..." data-content="cGroup5">
											</div>
														
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 10</b>
												<div id="divcGroup10" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="cgroup form-control input-sm" id="txtGroup10" name="txtGroup10" tabindex="11" placeholder="Enter Description..." data-content="cGroup10">
											</div>            
										</div>
									</div>
								
								<p data-toggle="collapse" data-target="#cusgrpcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Customers Groupings</b></u> <i>**Note: Press ENTER after you enter your description to save...</i></p>
									
									<div class="collapse" id="cusgrpcollapse">
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 1</b>
												<div id="divCustGroup1" style="display:inline; padding-left:5px">
												</div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup1" name="txtCustGroup1" placeholder="Enter Description..." data-content="CustGroup1">
											</div>
														
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 6</b>
												<div id="divCustGroup6" style="display:inline; padding-left:5px">
												</div>
											</div>
													
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup6" name="txtCustGroup6" tabindex="11" placeholder="Enter Description..." data-content="CustGroup6">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 2</b>
												<div id="divCustGroup2" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup2" name="txtCustGroup2" tabindex="11" placeholder="Enter Description..." data-content="CustGroup2">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 7</b>
												<div id="divCustGroup7" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup7" name="txtCustGroup7" tabindex="11" placeholder="Enter Description..." data-content="CustGroup7">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
														<b>Group 3</b>
														<div id="divCustGroup3" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
														<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup3" name="txtCustGroup3" tabindex="11" placeholder="Enter Description..." data-content="CustGroup3">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 8</b>
												<div id="divCustGroup8" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup8" name="txtCustGroup8" tabindex="11" placeholder="Enter Description..." data-content="CustGroup8">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 4</b>
												<div id="divCustGroup4" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup4" name="txtCustGroup4" tabindex="11" placeholder="Enter Description..." data-content="CustGroup4">
											</div>
														
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 9</b>
												<div id="divCustGroup9" style="display:inline; padding-left:5px"></div>
											</div>
													
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup9" name="txtCustGroup9" tabindex="11" placeholder="Enter Description..."data-content="CustGroup9">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 5</b>
												<div id="divCustGroup5" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup5" name="txtCustGroup5" tabindex="11" placeholder="Enter Description..." data-content="CustGroup5">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 10</b>
												<div id="divCustGroup10" style="display:inline; padding-left:5px"></div>
											</div>
													
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="ccustgroup form-control input-sm" id="txtCustGroup10" name="txtCustGroup10" tabindex="11" placeholder="Enter Description..." data-content="CustGroup10">
											</div>            
										</div>
									</div>
																
								<p data-toggle="collapse" data-target="#suppgrpcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Suppliers Groupings</b></u> <i>**Note: Press ENTER after you enter your description to save...</i></p>
									
									<div class="collapse" id="suppgrpcollapse">

										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 1</b>
												<div id="divSuppGroup1" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup1" name="txtSuppGroup1" placeholder="Enter Description..." data-content="SuppGroup1">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 6</b>
												<div id="divSuppGroup6" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup6" name="txtSuppGroup6" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup6">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
														<b>Group 2</b>
														<div id="divSuppGroup2" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
														<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup2" name="txtSuppGroup2" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup2">
											</div>
														
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 7</b>
												<div id="divSuppGroup7" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup7" name="txtSuppGroup7" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup7">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 3</b>
												<div id="divSuppGroup3" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup3" name="txtSuppGroup3" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup3">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 8</b>
												<div id="divSuppGroup8" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup8" name="txtSuppGroup8" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup8">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 4</b>
												<div id="divSuppGroup4" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup4" name="txtSuppGroup4" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup4">
											</div>            
								
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 9</b>
												<div id="divSuppGroup9" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup9" name="txtSuppGroup9" tabindex="11" placeholder="Enter Description..."data-content="SuppGroup9">
											</div>            
										</div>
								
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Group 5</b>
												<div id="divSuppGroup5" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup5" name="txtSuppGroup5" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup5">
											</div>
															
											<div class="col-xs-1 nopadwtop">
												&nbsp;
											</div>
												
											<div class="col-xs-2 nopadwtop">
												<b>Group 10</b>
												<div id="divSuppGroup10" style="display:inline; padding-left:5px"></div>
											</div>
													
											<div class="col-xs-3 nopadwtop">
												<input type="text" class="csuppgroup form-control input-sm" id="txtSuppGroup10" name="txtSuppGroup10" tabindex="11" placeholder="Enter Description..." data-content="SuppGroup10">
											</div>            
										</div>

									</div>	

								<p data-toggle="collapse" data-target="#custermscollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Customers Terms</b></u></p>
									
									<div class="collapse" id="custermscollapse">

										<div class="col-xs-12 nopadwdown">   
											<div style="display:inline" class="col-xs-3">
												<button class="btn btn-xs btn-primary" name="btnaddterms" id="btnaddterms"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
												<button class="btn btn-xs btn-success" name="btnterms" id="btnterms"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Terms</button>
											</div>
														
											<div style="display:inline" class="col-xs-5"> 
												<div class="alert alert-danger nopadding" id="TERMSAlertMsg">                             
												</div>
												<div class="alert alert-success nopadding" id="TERMSAlertDone">                             
												</div>
											</div>                 
										</div>

										<div class="col-xs-12 nopadding">
											<div class="col-xs-2">
												<b>Terms Code</b> 
											</div>                       
											<div class="col-xs-4">
												<b>Description</b>  
											</div>                         
											<div class="col-xs-3">
												<b>Status</b> 
											</div>                      
										</div>

										<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblTerms">
													
										</div>
												
									</div>	

								<!--
								<p data-toggle="collapse" data-target="#taxcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>VAT Codes</b></u></p>

									<div class="collapse" id="taxcodecollapse">
										<div class="col-xs-12 nopadwdown">   
											<div style="display:inline" class="col-xs-3">
												<button class="btn btn-xs btn-primary" name="btnaddtax" id="btnaddtax"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
												<button class="btn btn-xs btn-success" name="btntax" id="btntax"><i class="fa fa-save"></i>&nbsp; &nbsp;Save VAT Codes</button>
											</div>
														
											<div style="display:inline" class="col-xs-5"> 
												<div class="alert alert-danger nopadding" id="TAXAlertMsg">                              
												</div>
												<div class="alert alert-success nopadding" id="TAXAlertDone">                              
												</div>
											</div>                 
										</div>

										<div class="col-xs-12 nopadding">
														<div class="col-xs-2">
															<b>Tax Code</b> 
														</div>
														
														<div class="col-xs-4">
															<b>Description</b>  
														</div>
														
														<div class="col-xs-2">
															<b>Rate %</b> 
														</div>
			
														<div class="col-xs-3">
															<b>Status</b> 
														</div>                      
										</div>

										<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblTax">
													
										</div>
												
									</div>
								-->		

								<p data-toggle="collapse" data-target="#vatcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>VAT Codes</b></u></p>
										
									<div class="collapse" id="vatcodecollapse">
										<div class="col-xs-12 nopadwdown">   
											<div style="display:inline" class="col-xs-3">
												<button class="btn btn-xs btn-primary" name="btnaddvat" id="btnaddvat"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
												<button class="btn btn-xs btn-success" name="btnvat" id="btnvat"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Vat Codes</button>
											</div>
														
											<div style="display:inline" class="col-xs-5"> 
												<div class="alert alert-danger nopadding" id="VATAlertMsg">                              
												</div>
												<div class="alert alert-success nopadding" id="VATAlertDone">                              
												</div>
											</div>                 
										</div>

										<div class="col-xs-12 nopadding">
											<div class="col-xs-1">
												<b>Code</b> 
											</div>

											<div class="col-xs-1">
												<b>Rate %</b> 
											</div>
														
											<div class="col-xs-4">
												<b>Description</b>  
											</div>
														
											<div class="col-xs-3">
												<b>Remarks</b> 
											</div>
		
											<div class="col-xs-1">
												<b>Compute</b> 
											</div>
														
											<div class="col-xs-2">
												<b>Status</b> 
											</div>                      
										</div>

										<div style="height:20vh; border:1px solid #CCC;" class="col-lg-12 nopadwleft pre-scrollable" id="TblVAT">
													
										</div>
										
									</div>
								
								
								<p data-toggle="collapse" data-target="#ewtcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>EWT Codes</b></u></p>
									
									<div class="collapse" id="ewtcodecollapse">
										<div class="col-xs-12 nopadwdown">   
											<div style="display:inline" class="col-xs-3">
												<button class="btn btn-xs btn-primary" name="btnaddewt" id="btnaddewt"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
												<button class="btn btn-xs btn-success" name="btnewt" id="btnewt"><i class="fa fa-save"></i>&nbsp; &nbsp;Save EWT Codes</button>
											</div>
														
											<div style="display:inline" class="col-xs-5"> 
												<div class="alert alert-danger nopadding" id="EWTAlertMsg">                              
												</div>
												<div class="alert alert-success nopadding" id="EWTAlertDone">                              
												</div>
											</div>                 
										</div>

										<div class="col-xs-12 nopadding">
											<div class="col-xs-1 nopadwleft">
												<b>EWT Code</b> 
											</div>

											<div class="col-xs-1 nopadwleft">
												<b>Rate</b> 
											</div>

											<div class="col-xs-1 nopadwleft">
												<b>Rate Divisor</b> 
											</div>

											<div class="col-xs-1 nopadwleft">
												<b>Base</b> 
											</div>
																																																		
											<div class="col-xs-4 nopadwleft">
											<b>Description</b>  
											</div>
														
											<div class="col-xs-1 nopadwleft">
												<b>Acct Code</b> 
											</div>
			
											<div class="col-xs-2 nopadwleft">
												<b>Status</b> 
											</div>                     
										</div>

										<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblEWT">
													
										</div>
												
									</div>    
								
								<p data-toggle="collapse" data-target="#disccodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Discount Codes</b></u></p>
									
									<div class="collapse" id="disccodecollapse">
										<div class="col-xs-12 nopadwdown">   
											<div style="display:inline" class="col-xs-3">
												<button class="btn btn-xs btn-primary" name="btnadddisc" id="btnadddisc"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
												<button class="btn btn-xs btn-success" name="btdiscnt" id="btdiscnt"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Discount Codes</button>
											</div>
														
											<div style="display:inline" class="col-xs-5"> 
												<div class="alert alert-danger nopadding" id="DiscAlertMsg">                         
												</div>
												<div class="alert alert-success nopadding" id="DiscAlertDone">                              
												</div>
											</div>                 
										</div>

										<div class="col-xs-12 nopadding"> 
											<div class="col-xs-2 nopadwleft">
												<b>Discount Code</b> 
											</div>

											<div class="col-xs-4 nopadwleft">
												<b>Description</b> 
											</div>

											<div class="col-xs-1 nopadwleft">
												<b>Acct Code</b> 
											</div>

											<div class="col-xs-3 nopadwleft">
												<b>Acct Desc</b> 
											</div>
			
											<div class="col-xs-1 nopadwleft">
												<b>Status</b> 
											</div>                     
										</div>

										<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblDISC">
													
										</div>
												
									</div> 
								
									<p data-toggle="collapse" data-target="#contypescollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Contacts Details</b></u></p>
									
									<div class="collapse" id="contypescollapse">
										<div class="col-xs-12 nopadwdown">   
											<div style="display:inline" class="col-xs-3">
												<button class="btn btn-xs btn-primary" name="btnaddcondet" id="btnaddcondet"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
												<button class="btn btn-xs btn-success" name="btcntctdets" id="btcntctdets"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Contacts Details</button>
											</div>
														
											<div style="display:inline" class="col-xs-5"> 
												<div class="alert alert-danger nopadding" id="ConDetAlertMsg">                         
												</div>
												<div class="alert alert-success nopadding" id="ConDetAlertDone">                              
												</div>
											</div>                 
										</div>

										<div class="col-xs-12 nopadding"> 
											<div class="col-xs-4 nopadwleft">
												<b>Description</b> 
											</div>
			
											<div class="col-xs-1 nopadwleft">
												<b>Status</b> 
											</div>                     
										</div>

										<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblCONTDET">
													
										</div>
												
									</div>
							
								
							</div> 
						<!-- PARAMETERS SETUP END -->

						<!-- SALES SETUP -->
							<div id="sales" class="tab-pane fade in">  
              
								<div class="col-xs-12">
									<div class="col-xs-2 nopadwtop">
										<b>Customer's Credit Limit</b>
										<div id="divcCustLimit" style="display:inline; padding-left:5px">
										</div>
									</div>                    
									<div class="col-xs-3 nopadwtop">
										<?php
											$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='CRDLIMIT'"); 									
											if (mysqli_num_rows($result)!=0) {
												$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);								
												$nvalue = $all_course_data['cvalue']; 									
											}
											else{
												$nvalue = "";
											}
										?>
										<select class="form-control input-sm selectpicker" name="selcrdlmt" id	="selcrdlmt" onChange="setparamval('CRDLIMIT',this.value,'invchklimit')">
											<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> Enable credit limit checking </option>
											<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> Disable credit limit checking</option>
										</select>
									</div>                   
									<div class="col-xs-1 nopadwtop" id="invchklimit">
									</div>                    
								</div>
								<div class="col-xs-12">
									<div class="col-xs-2 nopadwtop">
										<b>Credit Limit Reset</b>
										<div id="divcLimitReset" style="display:inline; padding-left:5px">
										</div>
									</div>                    
									<div class="col-xs-3 nopadwtop">
										<?php
											$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='POSCLMT'"); 
								
											if (mysqli_num_rows($result)!=0) {
												$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);								
												$nvalue = $all_course_data['cvalue']; 								
											}
											else{
												$nvalue = "";
											}

										?>
										<select class="form-control input-sm" name="selcut" id	="selcut" onChange="setparamval('POSCLMT',this.value,'invcrdreset')">
											<option value="Daily" <?php if ($nvalue=="Daily") { echo "selected"; } ?>> Daily </option>
											<option value="Weekly" <?php if ($nvalue=="Weekly") { echo "selected"; } ?>> Weekly </option>
											<option value="Semi" <?php if ($nvalue=="Semi") { echo "selected"; } ?>> Semi Monthly </option>
											<option value="Monthly" <?php if ($nvalue=="Monthly") { echo "selected"; } ?>> Monthly </option>
											<option value="Yearly" <?php if ($nvalue=="Yearly") { echo "selected"; } ?>> Yearly </option>
											<option value="Never" <?php if ($nvalue=="Never") { echo "selected"; } ?>> Never </option>
										</select>
									</div>
													
									<div class="col-xs-1 nopadwtop" id="invcrdreset">
									</div>                   
								</div>

								<div class="col-xs-12" id="semidiv">
									<div class="col-xs-2 nopadwtop">
										<b>Semi Monthly </b>
										<div id="divcSemiMonthly" style="display:inline; padding-left:5px"></div>
									</div>
												
									<div class="col-xs-3 nopadwtop">
										<div class="col-xs-4 nopadding">
											<b>1st CutOff</b>
										</div>
										<div class="col-xs-3 nopadding">
											<select class="semicut form-control input-sm" id="semidayfr1">
											</select>
										</div>
										<div class="col-xs-2 nopadding">
											<b>&nbsp;&nbsp;TO&nbsp;&nbsp;</b>
										</div>
										<div class="col-xs-3 nopadding">
											<select class="semicut form-control input-sm" id="semidayto1">
											</select>                       
										</div>

										<br><br>

										<div class="col-xs-4 nopadding">
											<b>2nd CutOff</b>
										</div>
										<div class="col-xs-3 nopadding">
											<select class="semicut form-control input-sm" id="semidayfr2">
											</select>
										</div>
										<div class="col-xs-2 nopadding">
											<b>&nbsp;&nbsp;TO&nbsp;&nbsp;</b>
										</div>
										<div class="col-xs-3 nopadding">
											<select class="semicut form-control input-sm" id="semidayto2">
											</select>                       
										</div>
									</div>
												
									<div class="col-xs-1 nopadwtop" id="invcrdreset">
									</div>                   
								</div>

								<div class="col-xs-12  nopadwtop">&nbsp;</div>
									
								<p data-toggle="collapse" data-target="#itmqtes"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Quotation</b></u></p>
									
									<div class="collapse" id="itmqtes">

									<!-- for approvals -->

										<div class="col-xs-12" style="margin-bottom: 15px !important; margin-left: 15px !important">

											<div class="col-xs-3 nopadwtop2x">
												<b>Send Approval Email Notif.</b>
												<div id="divPOEmailprint" style="display:inline; padding-left:5px"></div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='QO_APP_EMAIL'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selpoautopost" id="selpoautopost" onChange="setparamval('QO_APP_EMAIL',this.value,'qoemailmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>                   
											<div class="col-xs-1 nopadwtop2x" id="qoemailmsg">
											</div>												
										</div>

											<?php
												$resQOApps = mysqli_query($con,"SELECT * FROM `quote_approvals_id` WHERE compcode='".$_SESSION['companyid']."'"); 
											?>


										<form action="th_saveqolevels.php" method="POST" name="frmPOLvls" id="frmPOLvls" onSubmit="return chkqolvlform();" target="_self">

											<input type="hidden" name="tbLQL1count" id="tbLQL1count" value="0">
											<input type="hidden" name="tbLQL2count" id="tbLQL2count" value="0">
											<input type="hidden" name="tbLQL3count" id="tbLQL3count" value="0"> 

											<div class="col-xs-12" style="padding-bottom: 5px !important">
												<div class="col-xs-2">
													<b>Approval Levels</b>
												</div>                    
												<div class="col-xs-3">
													<button type="submit" class="btn btn-xs btn-success" name="btnsaveQOApp" id="btnsaveQOApp"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Approvals</button>
												</div>
											</div>


											<div class="col-xs-12" style="margin-top: 5px !important; margin-left: 15px !important">

												<ul class="nav nav-tabs">
													<li class="active"><a data-toggle="tab" href="#qolevel1">Level 1</a></li>
													<li><a data-toggle="tab" href="#qolevel2">Level 2</a></li>
													<li><a data-toggle="tab" href="#qolevel3">Level 3</a></li>
												</ul>


												<div class="tab-content col-lg-12 nopadwtop2x">   

													<!-- LEVEL 1 -->
														<div id="qolevel1" class="tab-pane fade in active">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddqolvl1" id="btnaddqolvl1" onClick="addqolevel(1,'QOAPP1');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button> 															
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																	<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="QOAPP1">
																		<thead>
																			<tr>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">User ID</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Item Type</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Customer Type</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Quote Type</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																			if (mysqli_num_rows($resQOApps)!=0) {
																				$cntr = 0;

																				while($rowxcv=mysqli_fetch_array($resQOApps, MYSQLI_ASSOC)){
																					$rowQOresult[] = $rowxcv;
																				}

																				foreach ($rowQOresult as $row){
																					if($row['qo_approval_id']==1){
																						$cntr++;
																			?>	
																				<tr>
																					<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select class="form-control" name="selqosuser<?=$row['qo_approval_id'].$cntr?>" id="selqosuser<?=$row['qo_approval_id'].$cntr?>" >

																							<?php
																								foreach(@$ursnmse as $rsusr){
																									if($rsusr['userid']==$row['userid']){
																										$xscd = "selected";
																									}else{
																										$xscd = "";
																									}
																									echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																						<select required multiple class="form-control" name="selqoitmtyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqoitmtyp<?=$row['qo_approval_id'].$cntr?>" >

																						<option value='ALL' <?=($row['items']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$itmtype as $rsitm){

																									$xsc = "";
																									if($row['items']!=="" && $row['items']!==null){
																										if(in_array($rsitm['ccode'], explode(",",$row['items']))){
																											$xsc = "selected";
																										}
																									}
																									
																									echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																								}
																							?> 
																						</select>  
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select required multiple class="form-control" name="selqosutyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqosutyp<?=$row['qo_approval_id'].$cntr?>" >

																						<option value='ALL' <?=($row['suppliers']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$suptype as $rssup){
																									
																									$xsc = "";
																									if($row['suppliers']!=="" && $row['suppliers']!==null){
																										if(in_array($rssup['ccode'], explode(",",$row['suppliers']))){
																											$xsc = "selected";
																										}
																									}

																									echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select required multiple class="form-control" name="selqotrtyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqotrtyp<?=$row['qo_approval_id'].$cntr?>" >

																							<option value='ALL' <?=($row['qotype']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$qortype as $rssup){
																									
																									$xsc = "";
																									if($row['qotype']!=="" && $row['qotype']!==null){
																										if(in_array($rssup['ccode'], explode(",",$row['qotype']))){
																											$xsc = "selected";
																										}
																									}

																									echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<button class="btn btn-danger btn-sm" type="button" onclick="qotransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																					</td>
																				</tr>

																				<script>
																					$(document).ready(function(e) {
																						$('#selqosuser<?=$row['qo_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																						$('#selqoitmtyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																						$('#selqosutyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																						$('#selqotrtyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																					});
																				</script>
																			<?php
																					}
																				}
																			}
																			?>
																		</tbody>
																	</table> 

															</div>

														</div>

													<!-- LEVEL 2 -->
														<div id="qolevel2" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddqolvl2" id="btnaddqolvl2" onClick="addqolevel(2,'QOAPP2');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px"  id="QOAPP2">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">User ID</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Item Type</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Customer Type</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Quote Type</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resQOApps)!=0) {
																			$cntr = 0;
																			foreach ($rowQOresult as $row){
																				if(intval($row['qo_approval_id'])==2){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selqosuser<?=$row['qo_approval_id'].$cntr?>" id="selqosuser<?=$row['qo_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}

																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																					<select required multiple class="form-control" name="selqoitmtyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqoitmtyp<?=$row['qo_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['items']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$itmtype as $rsitm){

																								$xsc = "";
																								if($row['items']!==""){
																									if(in_array($rsitm['ccode'], explode(",",$row['items']))){
																										$xsc = "selected";
																									}
																								}
																								
																								echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																							}
																						?> 
																					</select>  
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select required multiple class="form-control" name="selqosutyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqosutyp<?=$row['qo_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['suppliers']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$suptype as $rssup){
																								
																								$xsc = "";
																								if($row['suppliers']!==""){
																									if(in_array($rssup['ccode'], explode(",",$row['suppliers']))){
																										$xsc = "selected";
																									}
																								}

																								echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select required multiple class="form-control" name="selqotrtyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqotrtyp<?=$row['qo_approval_id'].$cntr?>" >

																							<option value='ALL' <?=($row['qotype']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$qortype as $rssup){
																									
																									$xsc = "";
																									if($row['qotype']!=="" && $row['qotype']!==null){
																										if(in_array($rssup['ccode'], explode(",",$row['qotype']))){
																											$xsc = "selected";
																										}
																									}

																									echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="qotransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selqosuser<?=$row['qo_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																					$('#selqoitmtyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																					$('#selqosutyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																					$('#selqotrtyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

													<!-- LEVEL 3 -->
														<div id="qolevel3" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="lvlamtcls btn btn-xs btn-primary" name="btnaddqolvl3" id="btnaddqolvl3" onClick="addqolevel(3,'QOAPP3');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="QOAPP3">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">User ID</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Item Type</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Customer Type</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Quote Type</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resQOApps)!=0) {
																			$cntr = 0;
																			foreach ($rowQOresult as $row){
																				if($row['qo_approval_id']==3){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selqosuser<?=$row['qo_approval_id'].$cntr?>" id="selqosuser<?=$row['qo_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}

																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																					<select required multiple class="form-control" name="selqoitmtyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqoitmtyp<?=$row['qo_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['items']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$itmtype as $rsitm){

																								$xsc = "";
																								if($row['items']!==""){
																									if(in_array($rsitm['ccode'], explode(",",$row['items']))){
																										$xsc = "selected";
																									}
																								}
																								
																								echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																							}
																						?> 
																					</select>  
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select required multiple class="form-control" name="selqosutyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqosutyp<?=$row['qo_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['suppliers']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$suptype as $rssup){
																								
																								$xsc = "";
																								if($row['suppliers']!==""){
																									if(in_array($rssup['ccode'], explode(",",$row['suppliers']))){
																										$xsc = "selected";
																									}
																								}

																								echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select required multiple class="form-control" name="selqotrtyp<?=$row['qo_approval_id'].$cntr?>[]" id="selqotrtyp<?=$row['qo_approval_id'].$cntr?>" >

																							<option value='ALL' <?=($row['qotype']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$qortype as $rssup){
																									
																									$xsc = "";
																									if($row['qotype']!=="" && $row['qotype']!==null){
																										if(in_array($rssup['ccode'], explode(",",$row['qotype']))){
																											$xsc = "selected";
																										}
																									}

																									echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="qotransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selqosuser<?=$row['qo_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																					$('#selqoitmtyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																					$('#selqosutyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																					$('#selqotrtyp<?=$row['qo_approval_id'].$cntr?>').select2({width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

												</div>

											</div>


										</form>

										<div class="col-xs-12 nopadwtop2x" style="margin-left: 30px !important">
											<b>Default PrintOut Header</b>
											<div id="divQuotePrintHdr" style="display:inline; padding-left:5px"></div>
										</div>
										<div class="col-xs-12" style="margin-left: 15px !important">
											<textarea rows="5" class="form-control input-sm" name="txtQuotePrintHdr" id="txtQuotePrintHdr">													
											</textarea>
										</div>
										<div class="col-xs-12 nopadwtop2x" style="margin-left: 30px !important">
											<b>Default PrintOut Footer</b>
											<div id="divQuotePrintFtr" style="display:inline; padding-left:5px"></div>
										</div>
										<div class="col-xs-12" style="margin-left: 15px !important">
											<textarea rows="5" class="form-control input-sm" name="txtQuotePrintFtr" id="txtQuotePrintFtr">
															
											</textarea>
										</div>
										<div class="col-xs-12 nopadwtop2x" style="margin-left: 30px !important">
											<b>Default Remarks</b>
											<div id="QuoteRemarksChk" style="display: inline; padding-left:5px"></div>
										</div>
										<div class="col-xs-12" style="margin-left: 15px !important">
											<textarea name="QuoteRemarks" id="QuoteRemarks" class="form-control input-sm" rows="5"></textarea>
										</div>
									</div>
												
								<div class="col-xs-12  nopadwtop">&nbsp;</div>
								<p data-toggle="collapse" data-target="#itmso"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Sales Order</b></u></p>
											
									<div class="collapse" id="itmso">
											
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Auto post upon printing</b>
												<div id="divcPostSOprint" style="display:inline; padding-left:5px"></div>
											</div>
														
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='AUTO_POST_SO'"); 
												
														if (mysqli_num_rows($result)!=0) {
													$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
													
														$nvalue = $all_course_data['cvalue']; 
														
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selsoautopost" id	="selsoautopost" onChange="setparamval('AUTO_POST_SO',this.value,'sopostmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>
														
											<div class="col-xs-1 nopadwtop2x" id="sopostmsg">
											</div>												
										</div>

										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Credit Limit Management</b> <i>(if checking is enabled)</i>
												<div id="divcLimitMgt" style="display:inline; padding-left:5px">
												</div>
											</div>
												
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='CRDLIMWAR'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selcrdlmtwarn" id	="selcrdlmtwarn" onChange="setparamval('CRDLIMWAR',this.value,'crdwarnmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> Accept Order / Warning Only </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> Accept Order / Block Delivery </option>
													<option value="2" <?php if ($nvalue==2) { echo "selected"; } ?>> Refuse Orders </option>
												</select>
											</div>                   
											<div class="col-xs-1 nopadwtop2x" id="crdwarnmsg">
											</div>                    
										</div>

									</div>
									
								<div class="col-xs-12  nopadwtop">&nbsp;</div>
								
								<p data-toggle="collapse" data-target="#itmdr"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Delivery Receipt</b></u></p>
									
									<div class="collapse" id="itmdr">
										<div class='col-xs-12'>
											<div class='col-xs-2 nopadwtop2x'>
												<b>Print Version</b>
												<div id="divversprint" style="display:inline; padding-left:5px">
												</div>
											</div>
											<div class='col-xs-3 nopadwtop2x'>
													<?php 
														$result = mysqli_query($con, "SELECT * FROM `parameters` WHERE compcode='$company' and ccode = 'PRINT_VERSION_DR'");
														if(mysqli_num_rows($result) != 0){
															$verrow = mysqli_fetch_array($result, MYSQLI_ASSOC);
															$version = $verrow['cvalue'];
														} else {
															$version ='';
														}
													?>
													<select class='form-control input-sm selectpicker' id='printverDR' name='printverDR' onChange="setparamval('PRINT_VERSION_DR',this.value,'verdronmsg')">
														<option value='0' <?php if($version == 0 ) { echo "selected"; } ?>> Default </option>
														<option value='1' <?php if($version == 1) { echo "selected"; } ?>> Customize </option>
													</select>
											</div>
											<div class="col-xs-1 nopadwtop2x" id="verdronmsg">
											</div>    
										</div>

										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Auto post upon printing</b>
												<div id="divPostDRprint" style="display:inline; padding-left:5px"></div>
											</div>
												
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='AUTO_POST_DR'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}

												?>
												<select class="form-control input-sm selectpicker" name="seldrautopost" id	="seldrautopost" onChange="setparamval('AUTO_POST_DR',this.value,'drpostmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="drpostmsg">
											</div>                    
										</div>

									</div>


								<div class="col-xs-12  nopadwtop">&nbsp;</div>
								<p data-toggle="collapse" data-target="#itmsi"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Sales Invoice</b></u></p>
									
									<div class="collapse" id="itmsi">  
										<div class='col-xs-12'>
											<div class='col-xs-2 nopadwtop2x'>
												<b>Print Version</b>
												<div id="divversprint" style="display:inline; padding-left:5px">
												</div>
											</div>
											<div class='col-xs-3 nopadwtop2x'>
													<?php 
														$result = mysqli_query($con, "SELECT * FROM `parameters` WHERE compcode='$company' and ccode = 'PRINT_VERSION_SI'");
														if(mysqli_num_rows($result) != 0){
															$verrow = mysqli_fetch_array($result, MYSQLI_ASSOC);
															$version = $verrow['cvalue'];
														} else {
															$version ='';
														}
													?>
													<select class='form-control input-sm selectpicker' id='printverSI' name='printverSI' onChange="setparamval('PRINT_VERSION_SI',this.value,'versionmsg')">
														<option value='0' <?php if($version == 0 ) { echo "selected"; } ?>> Default </option>
														<option value='1' <?php if($version == 1) { echo "selected"; } ?>> Customize </option>
													</select>
											</div>
											<div class="col-xs-1 nopadwtop2x" id="versionmsg">
											</div>    
										</div>
									

										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Auto post upon printing</b>
												<div id="divcPostPOSprint" style="display:inline; padding-left:5px">
												</div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='AUTO_POST_POS'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
													
												?>

												<select class="form-control input-sm selectpicker" name="seldrautopost" id="seldrautopost" onChange="setparamval('AUTO_POST_POS',this.value,'sipostmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="sipostmsg">
											</div>                   
										</div>
										
										<!--
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Output Tax Acct Code</b>
												<div id="divcOutputTax" style="display:inline; padding-left:5px">
												</div>
											</div>                   
											<div class="col-xs-3 nopadwtop2x">
												<?php
												/*
													$result = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctno WHERE ccode='SALES_VAT'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cacctdesc']; 
														$nvalueid = $all_course_data['cacctno']; 												
													}
													else{
														$nvalue = "";
														$nvalueid = ""; 
													}
													*/
												?>
												<div class="col-xs-3 nopadding">
													<input type="text" name="txtSales_vatid" id="txtSales_vatid" class="form-control input-sm" placeholder="Select Acct Code..." value="<?//php echo $nvalueid;?>" readonly>
												</div>
												<div class="col-xs-9 nopadwleft">
												<input type="text" name="txtSales_vat" id="txtSales_vat" class="txtacctsel form-control input-sm" placeholder="Select Acct Code..." value="<?//php echo $nvalue;?>">
												</div>	
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="msgsales_vat">
											</div>                    
										</div>      
										
										-->

									</div>
								
							</div>
						<!-- SALES SETUP END -->

						<!-- PURCHASES SETUP -->
							<div id="purch" class="tab-pane fade in">

								<p data-toggle="collapse" data-target="#itmpurchreq"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Purchase Request</b></u></p>

								<div class="collapse" id="itmpurchreq">  

											<div class="col-xs-12" style="margin-bottom: 15px !important; margin-left: 15px !important">
												<div class="col-xs-3 nopadwtop2x">
													<b>Send Approval Email Notif.</b>
													<div id="divPOEmailprint" style="display:inline; padding-left:5px"></div>
												</div>                    
												<div class="col-xs-3 nopadwtop2x">
													<?php
														$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='PR_APP_EMAIL'"); 
													
														if (mysqli_num_rows($result)!=0) {
															$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
															$nvalue = $all_course_data['cvalue']; 												
														}
														else{
															$nvalue = "";
														}
													?>
													<select class="form-control input-sm selectpicker" name="selpoautopost" id="selpoautopost" onChange="setparamval('PR_APP_EMAIL',this.value,'premailmsg')">
														<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
														<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
													</select>
												</div>                   
												<div class="col-xs-1 nopadwtop2x" id="premailmsg">
												</div>												
											</div>

												<?php
													$resPRApps = mysqli_query($con,"SELECT * FROM `purchrequest_approvals_id` WHERE compcode='".$_SESSION['companyid']."'"); 
												?>


											<form action="th_saveprlevels.php" method="POST" name="frmPRLvls" id="frmPRLvls" onSubmit="return chkprlvlform();" target="_self">

												<!--<input type="hidden" name="tbPRLVL1count" id="tbLVL1count" value="0">-->
												<input type="hidden" name="tblPRLVL2count" id="tblPRLVL2count" value="0">
												<input type="hidden" name="tblPRLVL3count" id="tblPRLVL3count" value="0">

												<div class="col-xs-12" style="padding-bottom: 5px !important">
													<div class="col-xs-2">
														<b>Approval Levels</b>
													</div>                    
													<div class="col-xs-3">
														<button type="submit" class="btn btn-xs btn-success" name="btnsavePRApp" id="btnsavePRApp"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Approvals</button>
													</div>
												</div>


												<div class="col-xs-12" style="margin-top: 5px !important; margin-left: 15px !important; margin-bottom: 15px !important">

													<ul class="nav nav-tabs">
														<li class="active"><a data-toggle="tab" href="#prlevel1">Level 1</a></li>
														<li><a data-toggle="tab" href="#prlevel2">Level 2</a></li>
														<li><a data-toggle="tab" href="#prlevel3">Level 3</a></li>
													</ul>


													<div class="tab-content col-lg-12 nopadwtop2x">   

														<!-- LEVEL 1 -->
															<div id="prlevel1" class="tab-pane fade in active">
																<!--<input type="hidden" data-id="2" id = "lvlamt1" name = "lvlamt1" value="0">-->
																<div class="col-xs-12 nopadding">
																	<div style="padding: 20px">
																		<h5><i>* Set Level 1 approval by giving Post and Cancel access in User's Access module and selecting the Sections in Inventory tab in the same module.</i></h5>
																	</div>

																</div>

															</div>

														<!-- LEVEL 2 -->
															<div id="prlevel2" class="tab-pane fade in">

																<div class="col-xs-12 nopadding">
												
																	<div class="col-xs-2 nopadding"> 
																		<button type="button" class="btn btn-xs btn-primary" onClick="addprlevel(2,'PRAPP2');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>		
																		
																		<input type="hidden" data-id="2" id = "lvlamt2" name = "lvlamt2" value="0">
																	</div>

																	<!--<div class="col-xs-2 nopadwleft"> 
																		<b>Minimum Amount</b>
																	</div>

																	<div class="col-xs-2 nopadwleft"> 
																		<input type="hidden" data-id="2" id = "lvlamt2" name = "lvlamt2" value="0">
																		
																	</div>-->

																	<div class="col-xs-3 nopadwleft" id="divlevel2amounts"> 
																		
																	</div>

																</div>

																<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																	<table cellpadding="3px" width="100%" border="0" style="font-size: 14px"  id="PRAPP2">
																		<thead>
																			<tr>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">User ID</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Sections</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																			</tr>
																		</thead>

																		<tbody>
																			<?php
																			$rowPRresult = array();
																			if (mysqli_num_rows($resPRApps)!=0) {
																				$cntr = 0;

																				while($rowxcv=mysqli_fetch_array($resPRApps, MYSQLI_ASSOC)){
																					$rowPRresult[] = $rowxcv;
																				}

																				foreach ($rowPRresult as $row){
																					if(intval($row['pr_approval_id'])==2){
																						$cntr++;
																			?>	
																				<tr>
																					<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select class="form-control" name="selprsuser<?=$row['pr_approval_id'].$cntr?>" id="selprsuser<?=$row['pr_approval_id'].$cntr?>" >
																							<?php
																								foreach(@$ursnmse as $rsusr){
																									if($rsusr['userid']==$row['userid']){
																										$xscd = "selected";
																									}else{
																										$xscd = "";
																									}

																									echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																						<select required multiple class="form-control" name="selprsecs<?=$row['pr_approval_id'].$cntr?>[]" id="selprsecs<?=$row['pr_approval_id'].$cntr?>" >

																						<option value='ALL' <?=($row['locations_id']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$arsecs as $rsitm){

																									$xsc = "";
																									if($row['locations_id']!==""){
																										if(in_array($rsitm['ccode'], explode(",",$row['locations_id']))){
																											$xsc = "selected";
																										}
																									}
																									
																									echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																								}
																							?> 
																						</select>  
																					</td>
																				
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<button class="btn btn-danger btn-sm" type="button" onclick="prtransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																					</td>
																				</tr>

																				<script>
																					$(document).ready(function(e) {
																						$('#selprsuser<?=$row['pr_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																						$('#selprsecs<?=$row['pr_approval_id'].$cntr?>').select2({width: '100%'});
																					});
																				</script>
																			<?php
																					}
																				}
																			}
																			?>
																		</tbody>
																	</table> 

																</div>

															</div>

														<!-- LEVEL 3 -->
															<div id="prlevel3" class="tab-pane fade in">

																<div class="col-xs-12 nopadding">
												
																	<div class="col-xs-2 nopadding"> 
																		<button type="button" class="lvlamtcls btn btn-xs btn-primary" onClick="addprlevel(3,'PRAPP3');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>		
																		
																		<input type="hidden" data-id="3" id="lvlamt3" name="lvlamt3" value="0">
																	</div>

																	<!--<div class="col-xs-2 nopadwleft"> 
																		<b>Minimum Amount</b>
																	</div>

																	<div class="col-xs-2 nopadwleft"> 
																		<input type="hidden" data-id="3" id="lvlamt3" name="lvlamt3" value="0">
																	</div>-->

																	<div class="col-xs-3 nopadwleft" id="divlevel3amounts"> 
																	</div>

																</div>

																<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																	<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="PRAPP3">
																		<thead>
																			<tr>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">User ID</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Sections</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																			</tr>
																		</thead>

																		<tbody>
																			<?php
																			if (mysqli_num_rows($resPRApps)!=0) {
																				$cntr = 0;
																				foreach ($rowPRresult as $row){
																					if($row['pr_approval_id']==3){
																						$cntr++;
																			?>	
																				<tr>
																					<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select class="form-control" name="selprsuser<?=$row['pr_approval_id'].$cntr?>" id="selprsuser<?=$row['pr_approval_id'].$cntr?>" >
																							<?php
																								foreach(@$ursnmse as $rsusr){
																									if($rsusr['userid']==$row['userid']){
																										$xscd = "selected";
																									}else{
																										$xscd = "";
																									}
																									echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																						<select required multiple class="form-control" name="selprsecs<?=$row['pr_approval_id'].$cntr?>[]" id="selprsecs<?=$row['pr_approval_id'].$cntr?>" >

																						<option value='ALL' <?=($row['locations_id']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$arsecs as $rsitm){

																									$xsc = "";
																									if($row['locations_id']!==""){
																										if(in_array($rsitm['ccode'], explode(",",$row['locations_id']))){
																											$xsc = "selected";
																										}
																									}
																									
																									echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																								}
																							?> 
																						</select>  
																					</td>																	
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<button class="btn btn-danger btn-sm" type="button" onclick="prtransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																					</td>
																				</tr>

																				<script>
																					$(document).ready(function(e) {
																						$('#selprsuser<?=$row['pr_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																						$('#selprsecs<?=$row['pr_approval_id'].$cntr?>').select2({width: '100%'});
																					});
																				</script>
																			<?php
																					}
																				}
																			}
																			?>
																		</tbody>
																	</table> 

																</div>

															</div>

													</div>

												</div>


											</form>
									
								</div>


								<p data-toggle="collapse" data-target="#itmpo"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Purchase Order</b></u></p>

									<div class="collapse" id="itmpo">  

										<!--
											<div class="col-xs-12">
												<div class="col-xs-2 nopadwtop2x">
													<b>Auto post upon printing</b>
													<div id="divPostPOprint" style="display:inline; padding-left:5px"></div>
												</div>                    
												<div class="col-xs-3 nopadwtop2x">
													<?php
														//$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_PO'"); 
													
													//	if (mysqli_num_rows($result)!=0) {
													//		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
													//		$nvalue = $all_course_data['cvalue']; 												
													//	}
													//	else{
													//		$nvalue = "";
													//	}
													?>
													<select class="form-control input-sm selectpicker" name="selpoautopost" id="selpoautopost" onChange="setparamval('AUTO_POST_PO',this.value,'popostmsg')">
														<option value="0" <?//php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
														<option value="1" <?//php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
													</select>
												</div>                   
												<div class="col-xs-1 nopadwtop2x" id="popostmsg">
												</div>												
											</div>
										-->

										<div class="col-xs-12" style="margin-left: 15px !important">
											<div class="col-xs-3 nopadwtop2x">
												<b>Reference PR</b>
												<div id="divPostRRprint" style="display:inline; padding-left:5px"></div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_PR'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selporefpr" id="selporefpr" onChange="setparamval('ALLOW_REF_PR',this.value,'porefprmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> Allow No Reference </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> With Reference </option>
												</select>
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="porefprmsg">
											</div>                    
										</div>

										<div class="col-xs-12" style="margin-bottom: 15px !important; margin-left: 15px !important">
											<div class="col-xs-3 nopadwtop2x">
												<b>Send Approval Email Notif.</b>
												<div id="divPOEmailprint" style="display:inline; padding-left:5px"></div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='PO_APP_EMAIL'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selpoautopost" id="selpoautopost" onChange="setparamval('PO_APP_EMAIL',this.value,'poemailmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>                   
											<div class="col-xs-1 nopadwtop2x" id="poemailmsg">
											</div>												
										</div>

											<?php
												$resPOAppsHDR = mysqli_query($con,"SELECT * FROM `purchase_approvals` WHERE compcode='".$_SESSION['companyid']."'"); 
												while($rowpoaph=mysqli_fetch_array($resPOAppsHDR, MYSQLI_ASSOC)){
													$i = $rowpoaph['nlevel'];
													$rwpoapphdramt[$i] = $rowpoaph['namount'];
												}

												$resPOApps = mysqli_query($con,"SELECT * FROM `purchase_approvals_id` WHERE compcode='".$_SESSION['companyid']."'"); 
											?>


										<form action="th_savepolevels.php" method="POST" name="frmPOLvls" id="frmPOLvls" onSubmit="return chkpolvlform();" target="_self">

											<input type="hidden" name="tbLVL1count" id="tbLVL1count" value="0">
											<input type="hidden" name="tbLVL2count" id="tbLVL2count" value="0">
											<input type="hidden" name="tbLVL3count" id="tbLVL3count" value="0">

											<div class="col-xs-12" style="padding-bottom: 5px !important">
												<div class="col-xs-2">
													<b>Approval Levels</b>
												</div>                    
												<div class="col-xs-3">
													<button type="submit" class="btn btn-xs btn-success" name="btnsavePOApp" id="btnsavePOApp"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Approvals</button>
												</div>
											</div>


											<div class="col-xs-12" style="margin-top: 5px !important; margin-left: 15px !important">

												<ul class="nav nav-tabs">
													<li class="active"><a data-toggle="tab" href="#level1">Level 1</a></li>
													<li><a data-toggle="tab" href="#level2">Level 2</a></li>
													<li><a data-toggle="tab" href="#level3">Level 3</a></li>
												</ul>


												<div class="tab-content col-lg-12 nopadwtop2x">   

													<!-- LEVEL 1 -->
														<div id="level1" class="tab-pane fade in active">
															<input type="hidden" data-id="2" id = "lvlamt1" name = "lvlamt1" value="0">
															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddapplvl1" id="btnaddapplvl1" onClick="addpolevel(1,'POAPP1');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button> 															
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																	<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="POAPP1">
																		<thead>
																			<tr>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">User ID</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Item Type</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px">Supplier Type</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																			if (mysqli_num_rows($resPOApps)!=0) {
																				$cntr = 0;

																				while($rowxcv=mysqli_fetch_array($resPOApps, MYSQLI_ASSOC)){
																					$rowPOresult[] = $rowxcv;
																				}

																				foreach ($rowPOresult as $row){
																					if($row['po_approval_id']==1){
																						$cntr++;
																			?>	
																				<tr>
																					<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select class="form-control" name="selposuser<?=$row['po_approval_id'].$cntr?>" id="selposuser<?=$row['po_approval_id'].$cntr?>" >

																							<?php
																								foreach(@$ursnmse as $rsusr){
																									if($rsusr['userid']==$row['userid']){
																										$xscd = "selected";
																									}else{
																										$xscd = "";
																									}
																									echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																								}
																							?> 
																						</select>
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																						<select required multiple class="form-control" name="selpoitmtyp<?=$row['po_approval_id'].$cntr?>[]" id="selpoitmtyp<?=$row['po_approval_id'].$cntr?>" >

																						<option value='ALL' <?=($row['items']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$itmtype as $rsitm){

																									$xsc = "";
																									if($row['items']!=="" && $row['items']!==null){
																										if(in_array($rsitm['ccode'], explode(",",$row['items']))){
																											$xsc = "selected";
																										}
																									}
																									
																									echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																								}
																							?> 
																						</select>  
																					</td>
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select required multiple class="form-control" name="selposutyp<?=$row['po_approval_id'].$cntr?>[]" id="selposutyp<?=$row['po_approval_id'].$cntr?>" >

																						<option value='ALL' <?=($row['suppliers']=="ALL") ? "selected" : ""?>> ALL</option>

																							<?php
																								foreach(@$suptype as $rssup){
																									
																									$xsc = "";
																									if($row['suppliers']!=="" && $row['suppliers']!==null){
																										if(in_array($rssup['ccode'], explode(",",$row['suppliers']))){
																											$xsc = "selected";
																										}
																									}

																									echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																								}
																							?> 
																						</select>
																					</td>																					
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<button class="btn btn-danger btn-sm" type="button" onclick="potransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																					</td>
																				</tr>

																				<script>
																					$(document).ready(function(e) {
																						$('#selposuser<?=$row['po_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																						$('#selpoitmtyp<?=$row['po_approval_id'].$cntr?>').select2({width: '100%'});
																						$('#selposutyp<?=$row['po_approval_id'].$cntr?>').select2({width: '100%'});
																					});
																				</script>
																			<?php
																					}
																				}
																			}
																			?>
																		</tbody>
																	</table> 

															</div>

														</div>

													<!-- LEVEL 2 -->
														<div id="level2" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddapplvl2" id="btnaddapplvl2" onClick="addpolevel(2,'POAPP2');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<b>Minimum Amount</b>
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<input type="text" class="lvlamtcls form-control input-xs" data-id="2" id = "lvlamt2" name = "lvlamt2" value="<?=$rwpoapphdramt[2]?>">
																	
																</div>

																<div class="col-xs-3 nopadwleft" id="divlevel2amounts"> 
																	
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px"  id="POAPP2">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px">User ID</td>
																			<td style="padding-top: 5px">Item Type</td>
																			<td style="padding-top: 5px">Supplier Type</td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resPOApps)!=0) {
																			$cntr = 0;
																			foreach ($rowPOresult as $row){
																				if(intval($row['po_approval_id'])==2){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selposuser<?=$row['po_approval_id'].$cntr?>" id="selposuser<?=$row['po_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}

																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																					<select required multiple class="form-control" name="selpoitmtyp<?=$row['po_approval_id'].$cntr?>[]" id="selpoitmtyp<?=$row['po_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['items']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$itmtype as $rsitm){

																								$xsc = "";
																								if($row['items']!==""){
																									if(in_array($rsitm['ccode'], explode(",",$row['items']))){
																										$xsc = "selected";
																									}
																								}
																								
																								echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																							}
																						?> 
																					</select>  
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select required multiple class="form-control" name="selposutyp<?=$row['po_approval_id'].$cntr?>[]" id="selposutyp<?=$row['po_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['suppliers']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$suptype as $rssup){
																								
																								$xsc = "";
																								if($row['suppliers']!==""){
																									if(in_array($rssup['ccode'], explode(",",$row['suppliers']))){
																										$xsc = "selected";
																									}
																								}

																								echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="potransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selposuser<?=$row['po_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																					$('#selpoitmtyp<?=$row['po_approval_id'].$cntr?>').select2({width: '100%'});
																					$('#selposutyp<?=$row['po_approval_id'].$cntr?>').select2({width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

													<!-- LEVEL 3 -->
														<div id="level3" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="lvlamtcls btn btn-xs btn-primary" name="btnaddapplvl3" id="btnaddapplvl3" onClick="addpolevel(3,'POAPP3');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<b>Minimum Amount</b>
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<input type="text" class="form-control input-xs" data-id="3" id="lvlamt3" name="lvlamt3" value="<?=$rwpoapphdramt[3]?>">
																</div>

																<div class="col-xs-3 nopadwleft" id="divlevel3amounts"> 
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="POAPP3">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px">User ID</td>
																			<td style="padding-top: 5px">Item Type</td>
																			<td style="padding-top: 5px">Supplier Type</td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resPOApps)!=0) {
																			$cntr = 0;
																			foreach ($rowPOresult as $row){
																				if($row['po_approval_id']==3){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selposuser<?=$row['po_approval_id'].$cntr?>" id="selposuser<?=$row['po_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}
																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">																
																					<select required multiple class="form-control" name="selpoitmtyp<?=$row['po_approval_id'].$cntr?>[]" id="selpoitmtyp<?=$row['po_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['items']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$itmtype as $rsitm){

																								$xsc = "";
																								if($row['items']!==""){
																									if(in_array($rsitm['ccode'], explode(",",$row['items']))){
																										$xsc = "selected";
																									}
																								}
																								
																								echo "<option value='".$rsitm['ccode']."' ".$xsc."> ".$rsitm['cdesc']." </option>";
																							}
																						?> 
																					</select>  
																				</td>
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select required multiple class="form-control" name="selposutyp<?=$row['po_approval_id'].$cntr?>[]" id="selposutyp<?=$row['po_approval_id'].$cntr?>" >

																					<option value='ALL' <?=($row['suppliers']=="ALL") ? "selected" : ""?>> ALL</option>

																						<?php
																							foreach(@$suptype as $rssup){
																								
																								$xsc = "";
																								if($row['suppliers']!==""){
																									if(in_array($rssup['ccode'], explode(",",$row['suppliers']))){
																										$xsc = "selected";
																									}
																								}

																								echo "<option value='".$rssup['ccode']."' ".$xsc."> ".$rssup['cdesc']." </option>";
																							}
																						?> 
																					</select>
																				</td>																				
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="potransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selposuser<?=$row['po_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																					$('#selpoitmtyp<?=$row['po_approval_id'].$cntr?>').select2({width: '100%'});
																					$('#selposutyp<?=$row['po_approval_id'].$cntr?>').select2({width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

												</div>

											</div>


										</form>



													<div class="col-xs-12" style="margin-top: 10px !important; margin-left: 15px !important; padding-top: 10px !important">
														<b>Default Email Body</b>
														<div id="divPOBodyEmail" style="display:inline; padding-left:5px"></div>
													</div>
													<div class="col-xs-12" style="margin-left: 15px !important; margin-bottom: 15px !important;">
														<textarea rows="5" class="form-control input-sm" name="txtPOBodyEmail" id="txtPOBodyEmail">													
														</textarea>
													</div>

										
									</div>

								<p data-toggle="collapse" data-target="#itmwrr"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Receiving</b></u></p>
								
									<div class="collapse" id="itmwrr">             	
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Auto post upon printing</b>
												<div id="divPostRRprint" style="display:inline; padding-left:5px"></div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='AUTO_POST_RR'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selrrautopost" id="selrrautopost" onChange="setparamval('AUTO_POST_RR',this.value,'rrpostmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="rrpostmsg">
											</div>                    
										</div>
										
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Reference PO</b>
												<div id="divcRefPORR" style="display:inline; padding-left:5px"></div>
											</div>                   
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_RR'"); 										
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selrrallowref" id="selrrallowref" onChange="setparamval('ALLOW_REF_RR',this.value,'rrallowmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> Allow No Reference </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> W/ Reference (Check Qty) </option>
													<option value="2" <?php if ($nvalue==2) { echo "selected"; } ?>> W/ Reference (Open Qty) </option>
												</select>
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="rrallowmsg">
											</div>                    
										</div>               
									</div>
									
									
								<p data-toggle="collapse" data-target="#itmpret"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Purchase Return</b></u></p>
								
									<div class="collapse" id="itmpret">              	
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop2x">
												<b>Auto post upon printing</b>
												<div id="divcPostPRprint" style="display:inline; padding-left:5px">
												</div>
											</div>
												
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='AUTO_POST_PR'"); 										
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);													
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selpretautopost" id="selpretautopost" onChange="setparamval('AUTO_POST_PR',this.value,'pretpostmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>
												
											<div class="col-xs-1 nopadwtop2x" id="pretpostmsg">
											</div>                    
										</div>              	               
									</div>             
							</div>
						<!-- PURCHASES SETUP END -->
									
						<!-- ACCOUNTING SETUP -->
							<div id="acct" class="tab-pane fade in">
							
								<p data-toggle="collapse" data-target="#accdefcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Account Defaults</b></u> <i></i></p>
												
								<div class="collapse" id="accdefcollapse">  

									<div class="col-xs-12 nopadding" style="margin-bottom: 10px !important">                        
										<div style="display:inline" class="col-xs-3 nopadding">
											<button class="btn btn-xs btn-primary" name="btnaddacctdef" id="btnaddacctdef"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
											<button class="btn btn-xs btn-success" name="btnacctdef" id="btnacctdef"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Account Codes</button>
										</div>
															
										<div style="display:inline" class="col-xs-5"> 
											<div class="alert alert-danger nopadding" id="acctdefAlertMsg">                              
											</div>
											<div class="alert alert-success nopadding" id="acctdefAlertDone">																
											</div>
										</div>   
																					
									</div>
												
									<div style="height:50vh; " class="col-lg-12 pre-scrollable">                     
										<table id="TblAcctDef" cellpadding="3px" width="100%" border="0">
											<thead>
												<tr>
													<th style="border-bottom:1px solid #999">Acct ID</th>
													<th style="border-bottom:1px solid #999">Description</th>
													<th style="border-bottom:1px solid #999">Acct Code</th>
													<th style="border-bottom:1px solid #999">Account Title</th>
												</tr>
											</thead>
											<tbody>
																
											</tbody>
										</table>                    
									</div>				
								</div>
								<!-- RECEIVE PAYMENT SETUP -->
								<p data-toggle="collapse" data-target="#ARPcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Receive Payment</b></u> <i></i></p>

								<div class='collapse' id='ARPcollapse'>
									<div class='col-xs-12'>
											<div class='col-xs-2 nopadwtop2x'>
												<b>Print Version</b>
												<div id="divversprint" style="display:inline; padding-left:5px">
												</div>
											</div>
											<div class='col-xs-3 nopadwtop2x'>
													<?php 
														$result = mysqli_query($con, "SELECT * FROM `parameters` WHERE compcode='$company' and ccode = 'PRINT_VERSION_RP'");
														if(mysqli_num_rows($result) != 0){
															$verrow = mysqli_fetch_array($result, MYSQLI_ASSOC);
															$version = $verrow['cvalue'];
														} else {
															$version ='';
														}
													?>
													<select class='form-control input-sm selectpicker' id='printverSI' name='printverSI' onChange="setparamval('PRINT_VERSION_RP',this.value,'verrponmsg')">
														<option value='0' <?php if($version == 0 ) { echo "selected"; } ?>> Default </option>
														<option value='1' <?php if($version == 1) { echo "selected"; } ?>> Customize </option>
													</select>
											</div>
											<div class="col-xs-1 nopadwtop2x" id="verrponmsg">
											</div>    
										</div>				
								</div>			
								<!-- RECEIVE PAYMENT SETUP END -->

								<p data-toggle="collapse" data-target="#rfpcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Request For Payment</b></u> <i></i></p>
												
								<div class="collapse" id="rfpcollapse">

										<div class="col-xs-12" style="margin-bottom: 1px !important; margin-left: 15px !important">
											<div class="col-xs-3 nopadwtop">
												<b>Request for Payment</b>
												<!--<div id="divInvChecking" style="display:inline; padding-left:5px">
												</div>-->
											</div>                    
											<div class="col-xs-3 nopadwtop">

												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='RFPMODULE'"); 
											
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
														$nvalue = $all_course_data['cvalue']; 							
													}
													else{
														$nvalue = "";
													}
												?>

													<select class="form-control input-sm selectpicker" name="selchkinvsys" id="selchkinvsys" onChange="setparamval('RFPMODULE',this.value,'rfpmodchkmsg')">
														<option value="1" <?php if ($nvalue=='1') { echo "selected"; } ?>> Enabled </option>
														<option value="0" <?php if ($nvalue=='0') { echo "selected"; } ?>> Disabled </option>
													</select>
											</div>                    
											<div class="col-xs-1 nopadwtop" id="rfpmodchkmsg">
											</div>                    
										</div>

										<div class="col-xs-12" style="margin-bottom: 15px !important; margin-left: 15px !important">
											<div class="col-xs-3 nopadwtop2x">
												<b>Send Approval Email Notif.</b>
												<div id="divRFPEmailprint" style="display:inline; padding-left:5px"></div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='RFP_APP_EMAIL'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selrfpemailnoti" id="selrfpemailnoti" onChange="setparamval('RFP_APP_EMAIL',this.value,'rfpemailmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>                   
											<div class="col-xs-1 nopadwtop2x" id="rfpemailmsg">
											</div>												
										</div>									

											<?php
												$resRFPAppsHDR = mysqli_query($con,"SELECT * FROM `rfp_approvals` WHERE compcode='".$_SESSION['companyid']."'"); 
												while($rowrfpaph=mysqli_fetch_array($resRFPAppsHDR, MYSQLI_ASSOC)){
													$i = $rowrfpaph['nlevel'];
													$rwrfpapphdramt[$i] = $rowrfpaph['namount'];
												}

												$resRFPApps = mysqli_query($con,"SELECT * FROM `rfp_approvals_id` WHERE compcode='".$_SESSION['companyid']."'"); 
											?>

										<form action="th_saverfplevels.php" method="POST" name="frmRFPLvls" id="frmRFPLvls" onSubmit="return chkrfplvlform();" target="_self">

											<input type="hidden" name="tbLRFPL1count" id="tbLRFPL1count" value="0">
											<input type="hidden" name="tbLRFPL2count" id="tbLRFPL2count" value="0">
											<input type="hidden" name="tbLRFPL3count" id="tbLRFPL3count" value="0">

											<div class="col-xs-12" style="padding-bottom: 5px !important">
												<div class="col-xs-2">
													<b>Approval Levels</b>
												</div>                    
												<div class="col-xs-3">
													<button type="submit" class="btn btn-xs btn-success" name="btnsaveRFPApp" id="btnsaveRFPApp"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Approvals</button>
												</div>
											</div>


											<div class="col-xs-12" style="margin-top: 5px !important; margin-left: 15px !important">

												<ul class="nav nav-tabs">
													<li class="active"><a data-toggle="tab" href="#rfplevel1">Level 1</a></li>
													<li><a data-toggle="tab" href="#rfplevel2">Level 2</a></li>
													<li><a data-toggle="tab" href="#rfplevel3">Level 3</a></li>
												</ul>


												<div class="tab-content col-lg-12 nopadwtop2x">   

													<!-- LEVEL 1 -->
														<div id="rfplevel1" class="tab-pane fade in active">
															<input type="hidden" data-id="2" id = "lvlamt1" name = "lvlamt1" value="0">
															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddrfpapplvl1" id="btnaddrfpapplvl1" onClick="addrfplevel(1,'RFPAPP1');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button> 															
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																	<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="RFPAPP1">
																		<thead>
																			<tr>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px" width="60%">User ID</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																			if (mysqli_num_rows($resRFPApps)!=0) {
																				$cntr = 0;

																				while($rowxcv=mysqli_fetch_array($resRFPApps, MYSQLI_ASSOC)){
																					$rowRFPresult[] = $rowxcv;
																				}

																				foreach ($rowRFPresult as $row){
																					if($row['rfp_approval_id']==1){
																						$cntr++;
																			?>	
																				<tr>
																					<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select class="form-control" name="selrfpsuser<?=$row['rfp_approval_id'].$cntr?>" id="selrfpsuser<?=$row['rfp_approval_id'].$cntr?>" >

																							<?php
																								foreach(@$ursnmse as $rsusr){
																									if($rsusr['userid']==$row['userid']){
																										$xscd = "selected";
																									}else{
																										$xscd = "";
																									}
																									echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																								}
																							?> 
																						</select>
																					</td>																			
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<button class="btn btn-danger btn-sm" type="button" onclick="rfptransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																					</td>
																				</tr>

																				<script>
																					$(document).ready(function(e) {
																						$('#selrfpsuser<?=$row['rfp_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																					});
																				</script>
																			<?php
																					}
																				}
																			}
																			?>
																		</tbody>
																	</table> 

															</div>

														</div>

													<!-- LEVEL 2 -->
														<div id="rfplevel2" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddrfpapplvl2" id="btnaddrfpapplvl2" onClick="addrfplevel(2,'RFPAPP2');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<b>Minimum Amount</b>
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<input type="text" class="lvlamtcls form-control input-xs" data-id="2" id = "lvlamt2" name = "lvlamt2" value="<?=$rwrfpapphdramt[2]?>">
																	
																</div>

																<div class="col-xs-3 nopadwleft" id="divlevel2amounts"> 
																	
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px"  id="RFPAPP2">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px" width="60%">User ID</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resRFPApps)!=0) { 
																			$cntr = 0;
																			foreach ($rowRFPresult as $row){
																				if(intval($row['rfp_approval_id'])==2){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selrfpsuser<?=$row['rfp_approval_id'].$cntr?>" id="selrfpsuser<?=$row['rfp_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}

																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>

																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="rfptransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selrfpsuser<?=$row['rfp_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

													<!-- LEVEL 3 -->
														<div id="rfplevel3" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="lvlamtcls btn btn-xs btn-primary" name="btnaddrfpapplvl3" id="btnaddrfpapplvl3" onClick="addrfplevel(3,'RFPAPP3');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<b>Minimum Amount</b>
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<input type="text" class="form-control input-xs" data-id="3" id="lvlamt3" name="lvlamt3" value="<?=$rwrfpapphdramt[3]?>">
																</div>

																<div class="col-xs-3 nopadwleft" id="divlevel3amounts"> 
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="RFPAPP3">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px" width="60%">User ID</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resRFPApps)!=0) {  
																			$cntr = 0;
																			foreach ($rowRFPresult as $row){
																				if($row['rfp_approval_id']==3){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selrfpsuser<?=$row['rfp_approval_id'].$cntr?>" id="selrfpsuser<?=$row['rfp_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}
																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																			
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="rfptransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#rfp_approval_id<?=$row['rfp_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

												</div>

											</div>


										</form>

								</div>

								<p data-toggle="collapse" data-target="#blpcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Bills Payment</b></u> <i></i></p>
												
								<div class="collapse" id="blpcollapse">

											<div class="col-xs-12" class="border" style="margin-bottom: 1px !important; margin-left: 15px !important">
												<div class="col-xs-3 nopadwtop2x">
													<b>Reference APV</b>
													<div id="divPaybillRef" style="display:inline; padding-left:5px"></div>
												</div>                   
												<div class="col-xs-3 nopadwtop2x">
													<?php
														$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_APV'"); 										
														if (mysqli_num_rows($result)!=0) {
															$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
															$nvalue = $all_course_data['cvalue']; 												
														}
														else{
															$nvalue = "";
														}
													?>
													<select class="form-control input-sm selectpicker" name="selrrallowref" id="selrrallowref" onChange="setparamval('ALLOW_REF_APV',this.value,'apvallowmsg')">
														<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> Allow No Reference </option>
														<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> Required Reference </option>
													</select>
												</div>
												<div class="col-xs-1 nopadwtop2x" id="apvallowmsg">
												</div>
											</div>

										<div class="col-xs-12" class="border" style="margin-bottom: 15px !important; margin-left: 15px !important">
											<div class="col-xs-3 nopadwtop">
												<b>Send Approval Email Notif.</b>
												<div id="divRFPEmailprint" style="display:inline; padding-left:5px"></div>
											</div>                    
											<div class="col-xs-3 nopadwtop2x">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='BIP_APP_EMAIL'"); 
												
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
														$nvalue = $all_course_data['cvalue']; 												
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selrfpemailnoti" id="selrfpemailnoti" onChange="setparamval('BIP_APP_EMAIL',this.value,'bilpemailmsg')">
													<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
													<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
												</select>
											</div>                   
											<div class="col-xs-1 nopadwtop2x" id="bilpemailmsg">
											</div>												
										</div>									

											<?php
												$resPAYAppsHDR = mysqli_query($con,"SELECT * FROM `paybill_approvals` WHERE compcode='".$_SESSION['companyid']."'"); 
												while($rowrfpaph=mysqli_fetch_array($resPAYAppsHDR, MYSQLI_ASSOC)){
													$i = $rowrfpaph['nlevel'];
													$rwrfpapphdramt[$i] = $rowrfpaph['namount'];
												}

												$resPAYApps = mysqli_query($con,"SELECT * FROM `paybill_approvals_id` WHERE compcode='".$_SESSION['companyid']."'"); 
											?>

										<form action="th_savepaylevels.php" method="POST" name="frmPAYLvls" id="frmPAYLvls" onSubmit="return chkpaylvlform();" target="_self">

											<input type="hidden" name="tbLPAYL1count" id="tbLPAYL1count" value="0">
											<input type="hidden" name="tbLPAYL2count" id="tbLPAYL2count" value="0">
											<input type="hidden" name="tbLPAYL3count" id="tbLPAYL3count" value="0">

											<div class="col-xs-12" style="padding-bottom: 5px !important">
												<div class="col-xs-2">
													<b>Approval Levels</b>
												</div>                    
												<div class="col-xs-3">
													<button type="submit" class="btn btn-xs btn-success" name="btnsavePAYApp" id="btnsavePAYApp"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Approvals</button>
												</div>
											</div>


											<div class="col-xs-12" style="margin-top: 5px !important; margin-left: 15px !important">

												<ul class="nav nav-tabs">
													<li class="active"><a data-toggle="tab" href="#paylevel1">Level 1</a></li>
													<li><a data-toggle="tab" href="#paylevel2">Level 2</a></li>
													<li><a data-toggle="tab" href="#paylevel3">Level 3</a></li>
												</ul>


												<div class="tab-content col-lg-12 nopadwtop2x">   

													<!-- LEVEL 1 -->
														<div id="paylevel1" class="tab-pane fade in active">
															<input type="hidden" data-id="2" id = "lvlamt1" name = "lvlamt1" value="0">
															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddpayapplvl1" id="btnaddpayapplvl1" onClick="addpaylevel(1,'PAYAPP1');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button> 															
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																	<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="PAYAPP1">
																		<thead>
																			<tr>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px" width="60%">User ID</td>
																				<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																			if (mysqli_num_rows($resPAYApps)!=0) {
																				$cntr = 0;

																				while($rowxcv=mysqli_fetch_array($resPAYApps, MYSQLI_ASSOC)){
																					$rowPAYresult[] = $rowxcv;
																				}

																				foreach ($rowPAYresult as $row){

																					if($row['pay_approval_id']==1){
																						$cntr++;
																			?>	
																				<tr>
																					<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<select class="form-control" name="selpaysuser<?=$row['pay_approval_id'].$cntr?>" id="selpaysuser<?=$row['pay_approval_id'].$cntr?>" >

																							<?php
																								foreach(@$ursnmse as $rsusr){
																									if($rsusr['userid']==$row['userid']){
																										$xscd = "selected";
																									}else{
																										$xscd = "";
																									}
																									echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																								}
																							?> 
																						</select>
																					</td>																			
																					<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																						<button class="btn btn-danger btn-sm" type="button" onclick="paytransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																					</td>
																				</tr>

																				<script>
																					$(document).ready(function(e) {
																						$('#selpaysuser<?=$row['pay_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																					});
																				</script>
																			<?php
																					}
																				}
																			}
																			?>
																		</tbody>
																	</table> 

															</div>

														</div>

													<!-- LEVEL 2 -->
														<div id="paylevel2" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="btn btn-xs btn-primary" name="btnaddpayapplvl2" id="btnaddpayapplvl2" onClick="addpaylevel(2,'PAYAPP2');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<b>Minimum Amount</b>
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<input type="text" class="lvlamtcls form-control input-xs" data-id="2" id = "lvlamt2" name = "lvlamt2" value="<?=$rwrfpapphdramt[2]?>">
																	
																</div>

																<div class="col-xs-3 nopadwleft" id="divlevel2amounts"> 
																	
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px"  id="PAYAPP2">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px" width="60%">User ID</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resPAYApps)!=0) { 
																			$cntr = 0;
																			foreach ($rowPAYresult as $row){
																				if(intval($row['pay_approval_id'])==2){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selpaysuser<?=$row['pay_approval_id'].$cntr?>" id="selpaysuser<?=$row['pay_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}

																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>

																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="paytransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selpaysuser<?=$row['pay_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

													<!-- LEVEL 3 -->
														<div id="paylevel3" class="tab-pane fade in">

															<div class="col-xs-12 nopadding">
											
																<div class="col-xs-2 nopadding"> 
																	<button type="button" class="lvlamtcls btn btn-xs btn-primary" name="btnaddpayapplvl3" id="btnaddrfpapplvl3" onClick="addpaylevel(3,'PAYAPP3');"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add Approver</button>															
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<b>Minimum Amount</b>
																</div>

																<div class="col-xs-2 nopadwleft"> 
																	<input type="text" class="form-control input-xs" data-id="3" id="lvlamt3" name="lvlamt3" value="<?=$rwrfpapphdramt[3]?>">
																</div>

																<div class="col-xs-3 nopadwleft" id="divlevel3amounts"> 
																</div>

															</div>

															<div class="col-xs-12 border pre-scrollable" style="height: 150px; margin-top: 5px !important">

																<table cellpadding="3px" width="100%" border="0" style="font-size: 14px" id="PAYAPP3">
																	<thead>
																		<tr>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px" width="60%">User ID</td>
																			<td style="padding-top: 5px; border-bottom: 1px solid; padding-bottom: 5px"><small>Delete</small></td>
																		</tr>
																	</thead>

																	<tbody>
																		<?php
																		if (mysqli_num_rows($resPAYApps)!=0) {  
																			$cntr = 0;
																			foreach ($rowPAYresult as $row){
																				if($row['pay_approval_id']==3){
																					$cntr++;
																		?>	
																			<tr>
																				<td width="200px" style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<select class="form-control" name="selpaysuser<?=$row['pay_approval_id'].$cntr?>" id="selpaysuser<?=$row['pay_approval_id'].$cntr?>" >
																						<?php
																							foreach(@$ursnmse as $rsusr){
																								if($rsusr['userid']==$row['userid']){
																									$xscd = "selected";
																								}else{
																									$xscd = "";
																								}
																								echo "<option value='".$rsusr['userid']."' ".$xscd."> ".$rsusr['name']." </option>";
																							}
																						?> 
																					</select>
																				</td>
																			
																				<td style="padding-top: 2px; padding-left: 1px; padding-right: 1px">
																					<button class="btn btn-danger btn-sm" type="button" onclick="paytransset('delete',<?=$row['id']?>)"> <i class="fa fa-trash-o" aria-hidden="true"></i></button>
																				</td>
																			</tr>

																			<script>
																				$(document).ready(function(e) {
																					$('#selpaysuser<?=$row['pay_approval_id'].$cntr?>').select2({minimumResultsForSearch: Infinity,width: '100%'});
																				});
																			</script>
																		<?php
																				}
																			}
																		}
																		?>
																	</tbody>
																</table> 

															</div>

														</div>

												</div>

											</div>


										</form>

								</div>
								

								<div class="col-xs-12">
									<div class="col-xs-2 nopadwtop">
										<b>Income Account</b>
										<!--<div id="divInvChecking" style="display:inline; padding-left:5px">
										</div>-->
									</div>                    
									<div class="col-xs-3 nopadwtop">
										<?php
											$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='INCOME_ACCOUNT'"); 
									
											if (mysqli_num_rows($result)!=0) {
												$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
												$nvalue = $all_course_data['cvalue']; 							
											}
											else{
												$nvalue = "";
											}
										?>
											<select class="form-control input-sm selectpicker" name="selchkinvsys" id="selchkinvsys" onChange="setparamval('INCOME_ACCOUNT',this.value,'acctgincomeaccount')">
												<option value="item" <?php if ($nvalue=='item') { echo "selected"; } ?>> Sales Per Item</option>
												<option value="customer" <?php if ($nvalue=='customer') { echo "selected"; } ?>> Sales Per Customer </option>
												<option value="si" <?php if ($nvalue=='si') { echo "selected"; } ?>> Sales Per SI Type </option>
											</select>
									</div>                    
									<div class="col-xs-1 nopadwtop" id="acctgincomeaccount">
									</div>                    
								</div> 
													
							</div>
						<!-- ACCOUNTING SETUP END -->

						<!-- INVENTORY SETUP -->
							<div id="invntry" class="tab-pane fade in">
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Inventory Type</b>
												<div id="divInvChecking" style="display:inline; padding-left:5px">
												</div>
											</div>                    
											<div class="col-xs-3 nopadwtop">
												<?php
													$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='INVSYSTEM'"); 
									
													if (mysqli_num_rows($result)!=0) {
														$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
														$nvalue = $all_course_data['cvalue']; 							
													}
													else{
														$nvalue = "";
													}
												?>
												<select class="form-control input-sm selectpicker" name="selchkinvsys" id="selchkinvsys" onChange="setparamval('INVSYSTEM',this.value,'invsyschkmsg')">
													<option value="periodic" <?php if ($nvalue=='periodic') { echo "selected"; } ?>> Periodic Inventory</option>
													<option value="perpetual" <?php if ($nvalue=='perpetual') { echo "selected"; } ?>> Perpetual Inventory </option>
												</select>
											</div>                    
											<div class="col-xs-1 nopadwtop" id="invsyschkmsg">
											</div>                    
										</div> 

									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop">
											<b>Inventory Checking</b>
											<div id="divInvChecking" style="display:inline; padding-left:5px">
											</div>
										</div>                    
										<div class="col-xs-3 nopadwtop">
											<?php
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='".$_SESSION['companyid']."' and ccode='INVPOST'"); 
								
												if (mysqli_num_rows($result)!=0) {
													$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
													$nvalue = $all_course_data['cvalue']; 							
												}
												else{
													$nvalue = "";
												}
											?>
											<select class="form-control input-sm selectpicker" name="selchkinv" id="selchkinv" onChange="setparamval('INVPOST',this.value,'invchkmsg')">
												<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> Don't Check Available Inventory </option>
												<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> Always Check Available Inventory </option>
											</select>
										</div>                    
										<div class="col-xs-1 nopadwtop" id="invchkmsg">
										</div>                    
									</div> 

									<?php
										$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");
										$arrseclist[] = 0;
										$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
										foreach($rowdetloc as $row0){
											$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);
										}

										$sqlparams = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='".$_SESSION['companyid']."' and ccode in ('DEF_WHOUT','DEF_WHIN','DEF_PROUT','DEF_SRIN','MES_REQ_FROM','MES_REQ_TO')");
										$rowdetprms = $sqlparams->fetch_all(MYSQLI_ASSOC);

										$def_whout = "";
										$def_whin = "";
										$def_prout = "";
										$def_srin = "";

										$def_matreqfrm = "";
										$def_matreqtop = "";
										foreach($rowdetprms as $rowx){

											if($rowx['ccode']=="DEF_WHOUT"){
												$def_whout = $rowx['cvalue'];
											}
											
											if($rowx['ccode']=="DEF_WHIN"){
												$def_whin = $rowx['cvalue'];
											}

											if($rowx['ccode']=="DEF_PROUT"){
												$def_prout = $rowx['cvalue'];
											}

											if($rowx['ccode']=="DEF_SRIN"){
												$def_srin = $rowx['cvalue'];
											}

											if($rowx['ccode']=="MES_REQ_FROM"){
												$def_matreqfrm = $rowx['cvalue'];
											}

											if($rowx['ccode']=="MES_REQ_TO"){
												$def_matreqtop = $rowx['cvalue'];
											}
										
										}
									?>
									
									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop">
											<b>Finished Goods (Out)</b>
											<div id="divInvChecking" style="display:inline; padding-left:5px">
											</div>
										</div> 
										<div class="col-xs-3 nopadwtop">
											<select class="form-control input-sm" name="selfgout" id="selfgout" onChange="setparamval('DEF_WHOUT',this.value,'invdefwhout')">
												<?php
														foreach($arrallsec as $localocs){
													?>
														<option value="<?php echo $localocs['nid'];?>" <?=($def_whout==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
													<?php	
														}						
													?>
											</select>
										</div>
										<div class="col-xs-1 nopadwtop" id="invdefwhout">
										</div> 
									</div>

									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop">
											<b>Main Warehouse (In)</b>
											<div id="divInvChecking" style="display:inline; padding-left:5px">
											</div>
										</div>
										<div class="col-xs-3 nopadwtop">
											<select class="form-control input-sm" name="selwhiin" id="selwhiin" onChange="setparamval('DEF_WHIN',this.value,'invdefwhin')">
												<?php
													$issel = 0;
														foreach($arrallsec as $localocs){
															$issel++;
													?>
														<option value="<?php echo $localocs['nid'];?>" <?=($def_whin==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
													<?php	
														}						
													?>
											</select>
										</div>
										<div class="col-xs-1 nopadwtop" id="invdefwhin">
										</div>
									</div>

									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop">
											<b>Purchase Return (Out)</b>
											<div id="divInvChecking" style="display:inline; padding-left:5px">
											</div>
										</div> 
										<div class="col-xs-3 nopadwtop">
											<select class="form-control input-sm" name="selprout" id="selprout" onChange="setparamval('DEF_PROUT',this.value,'invdefprout')">
												<?php
													$issel = 0;
														foreach($arrallsec as $localocs){
															$issel++;
													?>
														<option value="<?php echo $localocs['nid'];?>" <?=($def_prout==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
													<?php	
														}						
													?>
											</select>
										</div>
										<div class="col-xs-1 nopadwtop" id="invdefprout">
										</div>
									</div>

									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop">
											<b>Sales Return (In)</b>
											<div id="divInvChecking" style="display:inline; padding-left:5px">
											</div>
										</div>
										<div class="col-xs-3 nopadwtop">
											<select class="form-control input-sm" name="selsrin" id="selsrin" onChange="setparamval('DEF_SRIN',this.value,'invdefsrin')">
												<?php
													$issel = 0;
														foreach($arrallsec as $localocs){
															$issel++;
													?>
														<option value="<?php echo $localocs['nid'];?>" <?=($def_srin==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
													<?php	
														}						
													?>
											</select>
										</div>
										<div class="col-xs-1 nopadwtop" id="invdefsrin">
										</div>
									</div>
									

									<p data-toggle="collapse" data-target="#mescollapse" style="margin-top:10px"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>MES Settings</b></u> <i></i></p>

									<div class="collapse" id="mescollapse">
											
										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Material Request (From)</b>												
											</div> 
											<div class="col-xs-3 nopadwtop">
												<select class="form-control input-sm" name="selfgout" id="selfgout" onChange="setparamval('MES_REQ_FROM',this.value,'matreqfrom')">
													<?php
															foreach($arrallsec as $localocs){
														?>
															<option value="<?php echo $localocs['nid'];?>" <?=($def_matreqfrm==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
														<?php	
															}						
														?>
												</select>
											</div>
											<div class="col-xs-1 nopadwtop" id="matreqfrom">
											</div> 
										</div>

										<div class="col-xs-12">
											<div class="col-xs-2 nopadwtop">
												<b>Material Request (To)</b>												
											</div>
											<div class="col-xs-3 nopadwtop">
												<select class="form-control input-sm" name="selwhiin" id="selwhiin" onChange="setparamval('MES_REQ_TO',this.value,'matreqto')">
													<?php
														$issel = 0;
															foreach($arrallsec as $localocs){
																$issel++;
														?>
															<option value="<?php echo $localocs['nid'];?>" <?=($def_matreqtop==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
														<?php	
															}						
														?>
												</select>
											</div>
											<div class="col-xs-1 nopadwtop" id="matreqto">
											</div>
										</div>

									</div>

							</div>						
						<!-- INVENTORY SETUP END 


						<div id="rpts" class="tab-pane fade in">
							<p data-toggle="collapse" data-target="#rpt_sofp"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Statement of Financial Position Template</b></u></p>

								<div class="collapse" id="rpt_sofp">

											<div class="col-xs-12">
												<div class="col-xs-4">
													ASSETS
												</div>
												<div class="col-xs-8 text-right">
													<button type="button" class="btn btn-warning btn-sm"><i class="fa fa-files-o" aria-hidden="true"></i></button>
													<button type="button" class="btn btn-success btn-sm"><i class="fa fa-file-o" aria-hidden="true"></i></button>
												</div>
											</div>
											
											<div class="col-xs-12">
												<div class="table-responsive">
													<table class="table table-condensed" style="margin-top: 10px; font-size: 11px">

														<?php
															//$qry = mysqli_query ($con, "SELECT * FROM template_balsheet WHERE compcode='".$_SESSION['companyid']."' and accttype='ASSETS' order by sortno");
														//	while($row = mysqli_fetch_array($qry, MYSQLI_ASSOC)){
														?>
															<tr>
																<td><?//=$row['sortno']?></td>
																<td><?//=$row['cacctid']?></td>
																<td><?//=$row['cacctdesc']?></td>
															</tr>
														<?php
														//	}
														?>
													</table>
												</div>
											</div>

											<div class="col-xs-12">
												<div class="col-xs-4">
													LIABILITIES
												</div>
												<div class="col-xs-8 text-right">
													<button type="button" class="btn btn-warning btn-sm"><i class="fa fa-files-o" aria-hidden="true"></i></button>
													<button type="button" class="btn btn-success btn-sm"><i class="fa fa-file-o" aria-hidden="true"></i></button>
												</div>
											</div>
											
											<div class="col-xs-12">
												<div class="table-responsive">
													<table class="table table-condensed" style="margin-top: 10px; font-size: 11px">

														<?php
														//	$qry = mysqli_query ($con, "SELECT * FROM template_balsheet WHERE compcode='".$_SESSION['companyid']."' and accttype='ASSETS' order by sortno");
														//	while($row = mysqli_fetch_array($qry, MYSQLI_ASSOC)){
														?>
															<tr>
																<td><?//=$row['sortno']?></td>
																<td><?//=$row['cacctid']?></td>
																<td><?//=$row['cacctdesc']?></td>
															</tr>
														<?php
														// 	}
														?>
													</table>
												</div>
											</div>


									</div>	
								</div>
						</div> 
						-->	
						<div id="POS" class="tab-pane fade in">
							<div class='col-sm-12'>
								<div class='col-xs-2 nopadwtop'>
									<b>Base Customer: </b>
								</div>
								<div class='col-xs-5 nopadwtop'>
									<input type='text' class='form-control input-sm' name="basecustomer" id="basecustomer" autocomplete="false" />
								</div>		
								<div class='col-xs-1 nopadwtop'>
									<div class='input-sm' id="basecustmsg"> </div>
								</div>					
							</div>		

							<p data-toggle="collapse" data-target="#service_table"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Service Fee</b></u></p>
							<div class="collapse" id='service_table' style='padding-bottom: 20px'>

								<div class='col-sm-12 '>
									<div class='nopadwtop' >
										<div class='col-xs-2 nopadwtop'><b>Enable Service Fee: </b></div>
										<span>
											<input type="checkbox" class='form-check-input' id='serviceCheck' <?= $isCheck != 0 ? "Checked": null ?> style='padding-left: 10px'><span>
										</span>
									</div>
									<div class='col-sm-12 nopadwtop'>
										<div class='col-xs-2 nopadwtop'><b>Service Fee: </b></div>
										<span>
												<input type="number" class='input-sm' name='servicefee' id='servicefee' placeholder='Service Fee Percentage...' value='<?= $service ? $service : 0 ?>' autocomplete='false'>
												<span style="padding-left:10px">%</span>
										</span> 
									</div>
									<div class='col-sm-12 nopadwtop'>
										<div class='col-xs-2 nopadwtop'><b>Account Entry: </b></div>
										<span> 
												<input type="text" class='input-sm' name='AccountEntry' id='AccountEntry' placeholder='Enter Account Entry...' value="<?=$accountDesc?>" autocomplete='false' data-val='<?=$account?>'>
										</span> 
									</div>
									<div class='col-sm-6' style='text-align: center; padding-top: 10px'>
										<button class='btn btn-sm btn-success' id='serviceSave' style='margin-left: 0px;'>Save</button>
									</div>
									<div class='col-xs-1 nopadwtop' id='servicefeemsg'></div>
								</div>
							</div>
															
							<p data-toggle="collapse" data-target="#pos_table"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Table Seats</b></u></p>
							<div class="collapse" id='pos_table' style='padding-bottom: 20px'>
								<div class="col-sm-12">
									<div class="col-lg-2 nopadwtop">
										<b><i>/* Insert a Table if restaurant based business */</i></b>
										<div id="divInvChecking" style="display:inline; padding-left:5px">
										</div>
									</div>
								</div>
								<div class="col-xs-12 nopadwtop" >
									<div class='col-sm-12' style=' padding-bottom: 10px;'><button type='button' class='btn btn-xs btn-primary' id='addTable' onclick="insert_table()"><span><i class='fa fa-plus'></i></span>&nbsp; Add Table</button></div>
									<form action="th_setTable.php" method='post' id='tableform' name='tableform' onsubmit='return false' enctype="multipart/form-data">
											<div class='col-sm-12' style='padding-bottom: 10px;'><button type='submit' id='tableSave' name='tableSave' onclick="table_save()" class='btn btn-xs btn-success' >Save </button><span id="save_table"></span></div> 
											<div class='col-sm-6 nopadwtop' style='border: 1px solid grey; height: 2in;overflow: auto; '>
												<table class='table' id='dataTable'>
													<thead>
														<tr>
															<th>Tables</th>
															<th>Remarks</th>
															<th>&nbsp;</th>
														</tr>
													</thead>
													<tbody style='overflow: auto;'>
														<?php
															$sql = "SELECT * FROM pos_grouping WHERE `compcode` = '$company' and `type` = 'TABLE'";
															$query = mysqli_query($con, $sql);
															while($row = $query -> fetch_assoc()):
														?>
															<tr>
																<td class='input-sm' style='display: none'><input type='text' id='tableID' name='tableID[]' placeholder='Name of Table' class='input-sm' value="<?= $row['id'] ?>"/></td>
																<td class='input-sm'><input type='text' id='tableName' name='tableName[]' placeholder='Name of Table' class='input-sm' value="<?= $row['code'] ?>"/></td>
																<td class='input-sm'><input type='text' id='tableRemarks' name='tableRemarks[]' placeholder='Remarks' class='input-sm' value="<?= $row['remarks'] ?>" /></td>
																<td class='input-sm'><button type='button' id='delTbl' name='delTbl' class='btn btn-xs btn-danger' value='<?= $row['id'] ?>'><i class='fa fa-trash'></i>&nbsp; delete</button></td>
															</tr>
														<?php endwhile; ?>
													</tbody>
												</table>
											</div>
									</form>
								</div>
							</div>


							<p data-toggle="collapse" data-target="#pos_order" ><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Order Type</b></u></p>
							<div class="collapse" id='pos_order'>
								<div class="col-lg-12">
									<div class="col-lg-2 nopadwtop">
										<b><i>/* Insert a Order Type if restaurant based business */</i></b>
										<div id="divInvChecking" style="display:inline; padding-left:5px">
										</div>
									</div>     
								</div>
								<div class="col-xs-12 nopadwtop" >
									<div class='col-sm-12' style=' padding-bottom: 10px;'><button type='button' class='btn btn-xs btn-primary' id='addTable' onclick="order_table()"><span><i class='fa fa-plus'></i></span>&nbsp; Add Order Type</button></div>

									<form action="" method="post" id="orderfrm" name="orderfrm" onsubmit="return false;" enctype="multipart/form-data">
											<div class='col-sm-12' style='padding-bottom: 10px;'><button type='submit' id='tableSave' name='tableSave' onclick="save_order()" class='btn btn-xs btn-success'>Save</button> <span id="save_order"></span></div>
											<div class='col-sm-6 nopadwtop' style='border: 1px solid grey; height: 2in; overflow: auto;'>
												<table class='table' id='ordertable' >
													<thead>
														<tr>
															<th>Order Type</th>
															<th>Remarks</th>
															<th>&nbsp;</th>
														</tr>
													</thead>
													<tbody >
														<?php 
															$sql = "SELECT * FROM pos_grouping WHERE `compcode` = '$company' and `type` = 'ORDER'";
															$query = mysqli_query($con, $sql);
															if(mysqli_num_rows($query) != 0):
																while($row = $query -> fetch_assoc()):
														?>
															<tr>	
																<td class='input-sm' style='display: none'><input type='text' id='orderID' name='orderID[]' placeholder='Name of Table' class='input-sm' value="<?= $row['id'] ?>"/></td>
																<td class='input-sm'><input type='text' id='orderName' name='orderName[]' placeholder='Name of Order' class='input-sm' value="<?= $row['code'] ?>"/></td>
																<td class='input-sm'><input type='text' id='orderRemarks' name='orderRemarks[]' placeholder='Remarks' class='input-sm' value="<?= $row['remarks'] ?>" /></td>
																<td class='input-sm'><button type='button' id='delTbl' name='delTbl' value='<?= $row['id'] ?>' class='btn btn-xs btn-danger'><i class='fa fa-trash'></i>&nbsp; delete</button></td>
															</tr>
														<?php endwhile; endif;?>
													</tbody>
												</table>
											</div>
									</form>
								</div>
							</div>
						</div>
					</div>
							
			</fieldset>




				<!-- 1) Alert Modal -->
				<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
						<div class="vertical-alignment-helper">
								<div class="modal-dialog vertical-align-top">
										<div class="modal-content">
											<div class="alert-modal-danger">
												<p id="AlertMsg"></p>
												<p>
													<center>
														<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
													</center>
												</p>
											</div>
										</div>
								</div>
						</div>
				</div>

				<form id="frmPRTrans" name="frmPRTrans" action="th_saveprtrans.php" method="POST">
					<input type='hidden' id='PRTransID' name='PRTransID' value=''>
					<input type='hidden' id='PRTransTP' name='PRTransTP' value=''>
				</form>

				<form id="frmPOTrans" name="frmPOTrans" action="th_savepotrans.php" method="POST">
					<input type='hidden' id='POTransID' name='POTransID' value=''>
					<input type='hidden' id='POTransTP' name='POTransTP' value=''>
				</form>

				<form id="frmQOTrans" name="frmQOTrans" action="th_saveqotrans.php" method="POST">
					<input type='hidden' id='QOTransID' name='QOTransID' value=''>
					<input type='hidden' id='QOTransTP' name='QOTransTP' value=''>
				</form>

				<form id="frmRFPTrans" name="frmRFPTrans" action="th_saverfptrans.php" method="POST">
					<input type='hidden' id='RFPTransID' name='RFPTransID' value=''>
					<input type='hidden' id='RFPTransTP' name='RFPTransTP' value=''>
				</form>

				<form id="frmPAYTrans" name="frmPAYTrans" action="th_savepaytrans.php" method="POST">
					<input type='hidden' id='PAYTransID' name='PAYTransID' value=''>
					<input type='hidden' id='PAYTransTP' name='PAYTransTP' value=''>
				</form>
	</body>

</html>


<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>


<script type="text/javascript">
	var isCheck = 0;
	$(document).ready(function(e) {
		loadcompany();
		
		loadgroups();
		
		loadewt();

		loaddiscs();

		loadbasecustomer();

		loadConDets();
		
		loadtax();
		
		loadvat();
		
		loadsemi();
		
		loadterms();
		loadAccountEntry();
		
		//loadloantyp();
		
		//loadloantrm();
		
		loadintrate();
		
		loadcusgrps();
		
		loadacctdefaults();

		loadquotesprint();

		loadsuppgrps();
		
		if($("#selcut").val()!="Semi"){
			$("#semidiv").hide();
		}
		
		if($("#selcrdlmt").val()==0){
			$("#selcrdlmtwarn").attr("disabled", true);
		}

		$('#serviceCheck').on('change', function () {
			if ($(this).prop("checked")) {
				isCheck = 1;
			} else {
				isCheck = 0;
			}
		});

		$("#serviceSave").click(function(){
			let servicefee = $("#servicefee").val()
			let accountEntry = $("#AccountEntry").attr("data-val")
			$.ajax({
				url: "th_servicefee.php",
				data: {
					service: servicefee,
					account: accountEntry,
					isCheck: isCheck
				},
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						console.log(res.msg)
						alert(res.msg)
					} else {
						console.log(res.msg)
						alert(res.msg)
					}
					location.reload()
				},
				error: function(res){
					console.log(res)
				}
			})
		})

		$("#basecustomer").typeahead({
			autoSelect: true,
			source: function(request, response){
				$.ajax({
					url: "../POS/Function/th_customer.php",
					data: { query: $("#basecustomer").val() },
					dataType: 'json',
					async: false,
					success: function(res){
						if(res.valid){
							console.log(res.data)
							response(res.data);
						} else {
							console.log(res)
						}
						
					},
					error: function(res){
						console.log(res)
					}
				})
			},
			displayText: function (item) {
                return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
            },
            highlighter: Object,
            afterSelect: function(items) { 
				
				$.ajax({
					url: "../POS/Function/th_setdefaultcust.php",
					data: { customer: items.id },
					dataType: 'json',
					async: false,
					success: function(res){
						if(res.valid){
							console.log(res.msg)
							$('#basecustmsg').html("&nbsp;&nbsp;<i class=\"fa fa-check\" style=\"color:green;\"></i>");
							$('#basecustomer').val(items.value)
						}
					},
					error: function(res){
						console.log(res)
					}
				})
			}
		})

	});

	$(function() {              
				// Bootstrap DateTimePicker v4
				$('.datepick').datetimepicker({
					format: 'MM/DD/YYYY'
				});

				$('#ptuissue').datetimepicker({
					format: 'YYYY/MM/DD'
				});

				$('#popoverData1, #popoverData2').popover({ trigger: "hover" });
				$("#EWTAlertMsg").hide();
				$("#EWTAlertDone").hide();			
				$("#TAXAlertMsg").hide();
				$("#TAXAlertDone").hide();
				$("#VATAlertMsg").hide();
				$("#VATAlertDone").hide();
				$("#CompanyAlertMsg").hide();
				$("#CompanyAlertDone").hide();
				$("#TERMSAlertMsg").hide();
				$("#TERMSAlertDone").hide();
				$("#LoanTypeAlertMsg").hide();
				$("#LoanTypeAlertDone").hide(); 
				$("#LoanTermAlertMsg").hide();
				$("#LoanTermAlertDone").hide(); 
				$("#IntRateAlertMsg").hide();
				$("#IntRateAlertDone").hide();  
				$("#acctdefAlertMsg").hide(); 
				$("#acctdefAlertDone").hide(); 
				$("#DiscAlertMsg").hide(); 
				$("#DiscAlertDone").hide(); 				
				$("#ConDetAlertDone").hide();
				$("#ConDetAlertMsg").hide();
				
						var $input = $(".txtacctsel");
						
						$input.typeahead({						 
							autoSelect: true,
							source: function(request, response) {							
								$.ajax({
									url: "../Maintenance/th_accounts.php",
									dataType: "json",
									data: { query: request },
									success: function (data) {
										response(data);
									}
								});
							},
							displayText: function (item) {
								return item.id + " : " + item.name;
							},
							highlighter: Object,
							afterSelect: function(item) { 					
										
								var id = $(document.activeElement).attr('id');
								//alert(id);	
								
								$('#'+id).val(item.name).change(); 
								$('#'+id+'id').val(item.id); 
								
								updateacctsdef('SALES_VAT',item.id,'msgsales_vat');
								
							}
						});

						$("#txtsalesacct").typeahead({
							autoSelect: true,
							source: function(request, response) {
								$.ajax({
									url: "th_accounts.php",
									dataType: "json",
									data: {
										query: $("#txtsalesacct").val()
									},
									success: function (data) {
										response(data);
									}
								});
							},
							displayText: function (item) {
								return item.id + " : " + item.name;
							},
							highlighter: Object,
							afterSelect: function(item) { 					
											
								$('#txtsalesacct'+rowCount).val(item.name).change(); 
								$('#txtsalesacctD'+rowCount).val(item.id); 
								
							}
						});


		$(".cgroup").on("keyup", function(e) {
			if(e.keyCode==13){
				var x = $(this).val();
				var y = $(this).attr("data-content") 
				var nme = $(this).attr("name");
				var r = nme.replace( /^\D+/g, '');
				
			//  alert(x+":"+y+":"+nme+":"+r);
				
				
				if(r<=10){
					r = parseInt(r) + 1;
				}
				
				$.ajax ({
							url: "th_updategroup.php",
							data: { val: x,  nme: y},
							success: function( result ) {
					
						if(result.trim()=="True"){
							$("#div"+y).html("<i class=\"fa fa-check\" style=\"color:green;\"></i>");
							
							$("#txtGroup"+r).focus();
						}
						else{
							alert(result);
						}
							}
					});

			}
		});

		$(".ccustgroup").on("keyup", function(e) {
			if(e.keyCode==13){
				var x = $(this).val();
				var y = $(this).attr("data-content") 
				var nme = $(this).attr("name");
				var r = nme.replace( /^\D+/g, '');
				
				if(r<=10){
					r = parseInt(r) + 1;
				}
				
				$.ajax ({
							url: "th_updategroup.php",
							data: { val: x,  nme: y},
							success: function( result ) {
					
						if(result.trim()=="True"){
							$("#div"+y).html("<i class=\"fa fa-check\" style=\"color:green;\"></i>");
							
							$("#txtCustGroup"+r).focus();
						}
						else{
							alert(result);
						}
							}
					});

			}
		});

		$(".csuppgroup").on("keyup", function(e) {
			if(e.keyCode==13){
				var x = $(this).val();
				var y = $(this).attr("data-content") 
				var nme = $(this).attr("name");
				var r = nme.replace( /^\D+/g, '');
				
				if(r<=10){
					r = parseInt(r) + 1;
				}
				
				$.ajax ({
							url: "th_updategroup.php",
							data: { val: x,  nme: y},
							success: function( result ) {
					
						if(result.trim()=="True"){
							$("#div"+y).html("<i class=\"fa fa-check\" style=\"color:green;\"></i>");
							
							$("#txtSuppGroup"+r).focus();
						}
						else{
							alert(result);
						}
							}
					});

			}
		});
		
		$("#btnaddewt").on("click", function(){
			var xy = parseInt($('div.ewtcodedetail:last').attr("id")) + 1;

			/*
				$.ajax ({
					url: "th_getlastewt.php",
					async: false,
					success: function( data ) {
						if(data!="False"){
							xy = parseInt(data) + 1;
							
						}
					}
				
				});
			*/	
				var divhead = "<div class=\"ewtcodedetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
				var divcode = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtcewtcode[]\" id=\"txtcewtcode"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" /></div>";

				var divrate = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtcewtrate[]\" id=\"txtcewtrate"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" /></div>";

				var divratediv = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtcewtratedivs[]\" id=\"txtcewtratedivs"+xy+"\" value=\"0.00\" data-citmno=\"xy\" class=\"form-control input-xs\" /></div>";

				var divbasec = "<div class=\"col-xs-1 nopadwleft\"><select class=\"form-control input-xs\" name=\"selbasecd[]\" id=\"selbasecd"+xy+"\"><option value=\"GROSS\">GROSS</option><option value=\"NET\">NET</option></select></div>";
																							
				var divdesc = "<div class=\"col-xs-4 nopadwleft\"><input type=\"text\" name=\"txtcewtdesc[]\" id=\"txtcewtdesc"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";
													
				var divacct = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtewtacct[]\" id=\"txtewtacct"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\"  placeholder=\"Enter Acct Code...\" /></div>"; 
																															
				var divstat = "<div class=\"col-xs-2 nopadwleft\">&nbsp;<span class='label label-success'>Active</span></div>";                                               
				var divend = "</div>";
							//alert(divhead + divcode + divrate + divratediv + divbasec + divdesc + divacct + divstat + divend);
								
				$("#TblEWT").append(divhead + divcode + divrate + divratediv + divbasec + divdesc + divacct + divstat + divend);
				
				
				$("#txtewtacct"+xy).on('keyup', function(event) {
							
					if(event.keyCode == 13){
								
						var dInput = this.value;
							
						$.ajax({
							type:'post',
							url:'../Accounting/getaccountid.php',
							data: 'c_id='+ $(this).val(),                 
							success: function(value){
											//alert(value);
								if(value.trim()!=""){
									//$("#txtewtacct"+lastRow).val(value.trim());
								}
								else{
									alert("Invalid Account Code");
									$('#txtewtacct'+xy).val("").change(); 
								}
							}
						});
									
					}
				});
		});
		
		$("#btnaddtax").on("click", function(){
			var xy = parseInt($('div.taxdetail:last').attr("id")) + 1;
				//$.ajax ({
				//	url: "th_getlasttax.php",
				//	async: false,
				//	success: function( data ) {
				//		if(data!="False"){
				//			xy = parseInt(data) + 1;
							
				//		}
				//	}
				
			//	});

							var divhead = "<div class=\"taxdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
							var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxcode[]\" id=\"txtctaxcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Code...\" /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtctaxdesc[]\" id=\"txtctaxdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" /></div>";
							var divrate = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxrate[]\" id=\"txtctaxrate"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Rate...\" /></div>";                                                 
							var divstat = "<div class=\"col-xs-3\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
							var divend = "</div>";

							var iswith = "True";
							$("#TblTax").append(divhead + divcode + divdesc + divrate + divstat + divend);

								$("#txtctaxcode"+xy).on("keyup", function() {
									var valz = $(this).val();

									$.post('th_chktaxcode.php',{'q':valz },function( data ){ //send value to post request in ajax to the php page
										if(data.Trim()!="True"){ 
											$("#TAXAlertMsg").html("<b>Error: </b>"+ data);
											$("#TAXAlertMsg").show();

											$("#TAXAlertDone").html("");
											$("#TAXAlertDone").hide();

											iswith = "True";
										}
										else {
											$("#TAXAlertMsg").html("");
											$("#TAXAlertMsg").hide();

											$("#TAXAlertDone").html("");
											$("#TAXAlertDone").hide();

											iswith = "False";
										}
									});
								});
								$("#txtctaxcode"+xy).on("blur", function() {
									
									if ($("#TAXAlertMsg").text().length > 0) {
									//if (iswith=="True") {
										$("#txtctaxcode"+xy).val("").change();
										$("#txtctaxcode"+xy).focus();
										
									}
								});
							
							
			
		});
		
		/*
		$("#btntax").on("click", function() {
			var isOk = "YES";
			
			$('.taxdetail').each(function(i, obj) {
				
				divid = $(this).attr("id");
				varcode = $(this).find('input[name="txtctaxcode[]"]').val();
				vardesc = $(this).find('input[name="txtctaxdesc[]"]').val();
				varrate = $(this).find('input[name="txtctaxrate[]"]').val();
				
				alert("th_savetax.php?code="+varcode+"&desc="+vardesc+"&rate="+varrate);
				$.ajax ({
					url: "th_savetax.php",
					data: { code: varcode,  desc: vardesc, rate: varrate },
					async: false,
					success: function( data ) {
						if(data!="True"){
							isOk = data;
						}
					}
				
				});
							
				
			});	
			
				if(isOk == "YES"){
					$("#TblTax").html("");
					loadtax();
					
					$("#TAXAlertDone").html("<b>SUCCESS: </b> Tax table successfully saved!");
					$("#TAXAlertDone").show();

							$("#TAXAlertMsg").html("");
							$("#TAXAlertMsg").hide();
					
				}
				else{
					$("#TAXAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#TAXAlertMsg").show();

							$("#TAXAlertDone").html("");
							$("#TAXAlertDone").hide();

				}
			
		});	
		*/
		
		$("#btnaddvat").on("click", function(){

			var xy = parseInt($('div.vatdetail:last').attr("id")) + 1;

			var xy = 1;
			/*
				$.ajax ({
					url: "th_getlastvat.php",
					async: false,
					success: function( data ) {
						if(data!="False"){
							xy = parseInt(data) + 1;
							
						}
					}
				
				});
				*/
							var divhead = "<div class=\"vatdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
							var divcode = "<div class=\"col-xs-1 nopadwright\"><input type=\"text\" name=\"txtcvatcode[]\" id=\"txtcvatcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Code...\" /></div>";
							var divrates = "<div class=\"col-xs-1 nopadwright\"><input type=\"text\" name=\"txtnrates[]\" id=\"txtnrates"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Rate...\" /></div>";
							var divdesc = "<div class=\"col-xs-4 nopadwright\"><input type=\"text\" name=\"txtcvatdesc[]\" id=\"txtcvatdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" /></div>";
							var divrem = "<div class=\"col-xs-3 nopadwright\"><input type=\"text\" name=\"txtcvatrem[]\" id=\"txtcvatrem"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Remarks...\" /></div>";                                                 
							var divcomp = "<div class=\"col-xs-1 nopadwright\"><select class=\"form-control input-xs\" name=\"selcomp[]\" id=\"selcomp"+xy+"\" ><option value=\"1\">YES</option><option value=\"0\">NO</option></select></div>";                                                 
							var divstat = "<div class=\"col-xs-2 nopadwright\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
							var divend = "</div>";

							$("#TblVAT").append(divhead + divcode + divrates + divdesc + divrem + divcomp + divstat + divend);

								$("#txtcvatcode"+xy).on("keyup", function() {
									var valz = $(this).val();

									$.post('th_chkvatcode.php',{'q':valz },function( data ){ //send value to post request in ajax to the php page
										if(data.trim()!="True"){ 
											$("#VATAlertMsg").html("<b>Error: </b>"+ data.trim());
											$("#VATAlertMsg").show();

											$("#VATAlertDone").html("");
											$("#VATAlertDone").hide();
										}
										else {
											$("#VATAlertMsg").html("");
											$("#VATAlertMsg").hide();

											$("#VATAlertDone").html("");
											$("#VATAlertDone").hide();
										}
									});
								});
								$("#txtcvatcode"+xy).on("blur", function() {
									if ($("#VATAlertMsg").text().length > 0) {
										
										$("#txtcvatcode"+xy).val("").change();
										$("#txtcvatcode"+xy).focus();
										
									}
								});
							
							
			
		});
		
		
		$("#btnvat").on("click", function() {
			var isOk = "YES";

			$('.vatdetail').each(function(i, obj) {
				//alert("una");
				divid = $(this).attr("id");
				varcode = $(this).find('input[name="txtcvatcode[]"]').val();
				//alert(varcode);
				vardesc = $(this).find('input[name="txtcvatdesc[]"]').val();
				//alert(vardesc);
				varrem = $(this).find('input[name="txtcvatrem[]"]').val();
				//alert(varrem);
				varcomp = $(this).find('select[name="selcomp[]"]').val();
				//alert(varcomp);

				$.ajax ({
					url: "th_savevat.php",
					data: { code: varcode,  desc: vardesc, rem: varrem, lcomp: varcomp },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							isOk = data;
						}
					}
				
				});
							
				
			});	
			
				if(isOk == "YES"){
					$("#TblVAT").html("");
					loadvat();
					
					$("#VATAlertDone").html("<b>SUCCESS: </b> VAT Exempt table successfully saved!");
					$("#VATAlertDone").show();

							$("#VATAlertMsg").html("");
							$("#VATAlertMsg").hide();
					
				}
				else{
					$("#VATAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#VATAlertMsg").show();

							$("#VATAlertDone").html("");
							$("#VATAlertDone").hide();

				}
			
		});	
		
		$("#btncompsave").on("click", function() {
			var nme = $("#txtcompanycom").val();
			var desc = $("#txtcompanydesc").val();
			var add = $("#txtcompanyadd").val();
			var zip = $("#txtcompanyzip").val();
			var tin = $("#txtcompanytin").val();
			var vatz = $("#selcompanyvat").val();
			var email = $("#txtcompanyemail").val();
			var cpnum = $("#txtcompanycpnum").val();
			var ptucode = $('#ptucode').val();
			var ptudate = $('#ptudate').val();
			//var texthdr = $("#texthdr").val();
			
				$.ajax ({
					url: "th_savecompany.php",
					data: { nme: nme,  desc: desc, add: add, tin: tin, vatz: vatz, zip: zip, email: email, cpnum: cpnum ,ptucode: ptucode, ptudate: ptudate},
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							$("#CompanyAlertMsg").html("<b>Error Saving:</b>"+data.trim());
							$("#CompanyAlertMsg").show();
			
									$("#CompanyAlertDone").html("");
									$("#CompanyAlertDone").hide();
						}
						else{
							$("#CompanyAlertMsg").html("");
							$("#CompanyAlertMsg").hide();
			
									$("#CompanyAlertDone").html("<b>SUCCESS: </b> Company details successfully updated!");
									$("#CompanyAlertDone").show();
						}
					},
					error: function (req, status, err) {
							console.log('Something went wrong', status, err)
							alert('Something went wrong\n'+status+"\n"+err);	
							$("#CompanyAlertMsg").html("<b>Something went wrong: </b>"+status+ " " + err);
							$("#CompanyAlertMsg").show();
					}
				
				});

		});
		
		
		$("#btnaddterms").on("click", function(){
			var xy = 1;
				$.ajax ({
					url: "th_getlasterm.php",
					async: false,
					success: function( data ) {
						if(data!="False"){
							xy = parseInt(data) + 1;
							
						}
					}
				
				});
							var divhead = "<div class=\"termdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
							var divcode = "<div class=\"col-xs-1\"><input type=\"text\" name=\"txtctermcode[]\" id=\"txtctermcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Code...\" maxlength=\"10\" /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtctermdesc[]\" id=\"txtctermdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" maxlength=\"25\" /></div>";
							var divstat = "<div class=\"col-xs-2\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
							var divend = "</div>";

							$("#TblTerms").append(divhead + divcode + divdesc + divstat + divend);

								$("#txtctermcode"+xy).on("keyup", function() {
									var valz = $(this).val();
									//alert(valz);
									$.post('th_chktermcode.php',{q:valz,id:"TERMS" },function( data ){ //send value to post request in ajax to the php page
										if(data.trim()!="True"){ 
											$("#TERMSAlertMsg").html("<b>Error: </b>"+ data);
											$("#TERMSAlertMsg").show();

											$("#TERMSAlertDone").html("");
											$("#TERMSAlertDone").hide();
										}
										else {
											$("#TERMSAlertMsg").html("");
											$("#TERMSAlertMsg").hide();

											$("#TERMSAlertDone").html("");
											$("#TERMSAlertDone").hide();
										}
									});
								});
								$("#txtctermcode"+xy).on("blur", function() {
									if ($("#TERMSAlertMsg").text().length > 0) {
										
										$("#txtctermcode"+xy).val("").change();
										$("#txtctermcode"+xy).focus();
										
									}
								});
							
							
			
		});

		$("#btnterms").on("click", function() {
			var isOk = "YES";

			$('.termdetail').each(function(i, obj) {
				//alert("una");
				divid = $(this).attr("id");
				varcode = $(this).find('input[name="txtctermcode[]"]').val();
				//alert(varcode);
				vardesc = $(this).find('input[name="txtctermdesc[]"]').val();
				//alert(vardesc);

				$.ajax ({
					url: "th_saveterms.php",
					data: { code: varcode,  desc: vardesc, id:"TERMS", msg:"CUST TERMS" },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							isOk = data;
						}
					}
				
				});
							
				
			});	
			
				if(isOk == "YES"){
					$("#TblTerms").html("");
					loadterms();
					
					$("#TERMSAlertDone").html("<b>SUCCESS: </b> Customer Terms table successfully saved!");
					$("#TERMSAlertDone").show();

							$("#TERMSAlertMsg").html("");
							$("#TERMSAlertMsg").hide();
					
				}
				else{
					$("#TERMSAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#TERMSAlertMsg").show();

							$("#TERMSAlertDone").html("");
							$("#TERMSAlertDone").hide();

				}
			
		});	

		
		$("#btnaddtloan").on("click", function(){
			var xy = 1;
				$.ajax ({
					url: "th_getlasterm.php",
					async: false,
					success: function( data ) {
						if(data!="False"){
							xy = parseInt(data) + 1;
							
						}
					}
				
				});
							var divhead = "<div class=\"loanstypdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
							var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtcloantypcode[]\" id=\"txtcloantypcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Code...\" maxlength=\"10\" /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcloantypdesc[]\" id=\"txtcloantypdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" maxlength=\"25\" /></div>";
							var divstat = "<div class=\"col-xs-3\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
							var divend = "</div>";

							$("#Tblloantyp").append(divhead + divcode + divdesc + divstat + divend);

								$("#txtcloantypcode"+xy).on("keyup", function() {
									var valz = $(this).val();

									$.post('th_chktermcode.php',{q:valz,id:"LOANTYP" },function( data ){ //send value to post request in ajax to the php page
										if(data.trim()!="True"){ 
											$("#LoanTypeAlertMsg").html("<b>Error: </b>"+ data);
											$("#LoanTypeAlertMsg").show();

											$("#LoanTypeAlertDone").html("");
											$("#LoanTypeAlertDone").hide();
										}
										else {
											$("#LoanTypeAlertMsg").html("");
											$("#LoanTypeAlertMsg").hide();

											$("#LoanTypeAlertDone").html("");
											$("#LoanTypeAlertDone").hide();
										}
									});
								});
								$("#txtcloantypcode"+xy).on("blur", function() {
									if ($("#LoanTypeAlertMsg").text().length > 0) {
										
										$("#txtcloantypcode"+xy).val("").change();
										$("#txtcloantypcode"+xy).focus();
										
									}
								});
			
		});
		
		$("#btnaddint").on("click", function(){
			var xy = 1;
				$.ajax ({
					url: "th_getlasterm.php",
					async: false,
					success: function( data ) {
						if(data!="False"){
							xy = parseInt(data) + 1;
							
						}
					}
				
				});
							var divhead = "<div class=\"intratesdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
							var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtintratecode[]\" id=\"txtintratecode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Code...\" maxlength=\"10\" /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtintratedesc[]\" id=\"txtintratedesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" maxlength=\"25\" /></div>";
							var divstat = "<div class=\"col-xs-3\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
							var divend = "</div>";

							$("#Tblintrate").append(divhead + divcode + divdesc + divstat + divend);

								$("#txtcloantrmcode"+xy).on("keyup", function() {
									var valz = $(this).val();

									$.post('th_chktermcode.php',{q:valz,id:"LOANTRM" },function( data ){ //send value to post request in ajax to the php page
										if(data.trim()!="True"){ 
											$("#IntRateAlertMsg").html("<b>Error: </b>"+ data);
											$("#IntRateAlertMsg").show();

											$("#IntRateAlertDone").html("");
											$("#IntRateAlertDone").hide();
										}
										else {
											$("#IntRateAlertMsg").html("");
											$("#IntRateAlertMsg").hide();

											$("#IntRateAlertDone").html("");
											$("#IntRateAlertDone").hide();
										}
									});
								});
								$("#txtcloantrmcode"+xy).on("blur", function() {
									if ($("#IntRateAlertMsg").text().length > 0) {
										
										$("#txtintratecode"+xy).val("").change();
										$("#txtintratecode"+xy).focus();
										
									}
								});
			
		});
		
		$("#btnintrate").on("click", function() {  
			var isOk = "YES";

			$('.intratesdetail').each(function(i, obj) {
				//alert("una");
				divid = $(this).attr("id");
				varcode = $(this).find('input[name="txtintratecode[]"]').val();
				//alert(varcode);
				vardesc = $(this).find('input[name="txtintratedesc[]"]').val();
				//alert(vardesc);

				$.ajax ({
					url: "th_saveterms.php",
					data: { code: varcode,  desc: vardesc, id:"LOANINT", msg:"INT RATES" },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							isOk = data;
						}
					}
				
				});
							
				
			});	
			
				if(isOk == "YES"){
					$("#Tblintrate").html("");
					loadintrate();
					
					$("#IntRateAlertDone").html("<b>SUCCESS: </b> Interest rates table successfully saved!");
					$("#IntRateAlertDone").show();

							$("#IntRateAlertMsg").html("");
							$("#IntRateAlertMsg").hide();
					
				}
				else{
					$("#IntRateAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#IntRateAlertMsg").show();

							$("#IntRateAlertDone").html("");
							$("#IntRateAlertDone").hide();

				}
			
		});	
		
		$("#btnaddacctdef").on("click", function(){
							
							var tbl = document.getElementById('TblAcctDef').getElementsByTagName('tr');
							var lastRow = tbl.length;

							var tblz = document.getElementById('TblAcctDef').getElementsByTagName('tbody')[0];
							var a=tblz.insertRow(tblz.rows.length);
							
							var u=a.insertCell(0);
								u.style.padding = "1px";
								u.style.width = "100px";
							var v=a.insertCell(1);
								v.style.padding = "1px";
								v.style.width = "400px";
							var w=a.insertCell(2);
								w.style.padding = "1px";
								w.style.width = "100px";
							var x=a.insertCell(3);
								x.style.padding = "1px";
								x.style.width = "400px";
								
							u.innerHTML = "<input type=\"hidden\" name=\"txtnident[]\" id=\"txtnident"+lastRow+"\" value=\""+lastRow+"\" data-citmno=\""+lastRow+"\" /><input type=\"text\" name=\"txtccode[]\" id=\"txtccode"+lastRow+"\" value=\"\" data-citmno=\""+lastRow+"\" class=\"form-control input-xs\" />";
							v.innerHTML = "<input type=\"text\" name=\"txtcdesc[]\" id=\"txtcdesc"+lastRow+"\" value=\"\" data-citmno=\""+lastRow+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" />";
							w.innerHTML = "<input type=\"text\" name=\"txtcacctid[]\" id=\"txtcacctid"+lastRow+"\" value=\"\" data-citmno=\""+lastRow+"\" class=\"form-control input-xs\"  placeholder=\"Enter Account Code...\" />";
							x.innerHTML = "<input type=\"text\" name=\"txtcacctdesc[]\" id=\"txtcacctdesc"+lastRow+"\" value=\"\" data-citmno=\""+lastRow+"\" class=\"form-control input-xs\"  placeholder=\"Enter Account Tile...\" />";
														
							$("#txtcacctid"+lastRow).on('keyup', function(event) {
							
								if(event.keyCode == 13){
								
									var dInput = this.value;
							
										$.ajax({
										type:'post',
										url:'../Accounting/getaccountid.php',
										data: 'c_id='+ $(this).val(),                 
										success: function(value){
											//alert(value);
											if(value.trim()!=""){
												$("#txtcacctdesc"+lastRow).val(value.trim());
											}
											else{
												alert("Invalid Account Code");
												$("#txtcacctdesc"+lastRow).val("");
												$('#txtcacctid'+lastRow).val("").change(); 
											}
										}
										});
									
								}
							});
							
							$("#txtcacctdesc"+lastRow).typeahead({						 
								autoSelect: true,
								source: function(request, response) {							
									$.ajax({
										url: "../Maintenance/th_accounts.php",
										dataType: "json",
										data: { query: request },
										success: function (data) {
											response(data);
										}
									});
								},
								displayText: function (item) {
									return item.id + " : " + item.name;
								},
								highlighter: Object,
								afterSelect: function(item) { 					
											
									$('#txtcacctdesc'+lastRow).val(item.name).change(); 
									$('#txtcacctid'+lastRow).val(item.id); 
																	
								}
						});
						
		});
		
		$("#btnacctdef").on("click", function(){
			var isOk = "True";
			
				$("#TblAcctDef > tbody > tr").each(function(index) {	
				
					var ccode = $(this).find('input[name="txtccode[]"]').val();
					var cdesc = $(this).find('input[name="txtcdesc[]"]').val();
					var cacctid = $(this).find('input[name="txtcacctid[]"]').val();
					var nident = $(this).find('input[type="hidden"][name="txtnident[]"]').val();

					$.ajax ({
						url: "th_saveacctdefaults.php",
						data: { trancode: nident, ccode: ccode, cdesc: cdesc, cacctid: cacctid },
						async: false,
						success: function( data ) {
							if(data.trim()!="True"){
								isOk = data.trim();
							}
						}
					});
					
				});
			
			//alert(isOk);

				if(isOk == "True"){
					$('#TblAcctDef tbody').empty();
					loadacctdefaults();
					
					$("#acctdefAlertDone").html("<b>SUCCESS: </b> Accounts Defaults table successfully saved!");
					$("#acctdefAlertDone").show();

							$("#acctdefAlertMsg").html("");
							$("#acctdefAlertMsg").hide();
					
				}
				else{
					$("#IntRateAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#IntRateAlertMsg").show();

							$("#IntRateAlertDone").html("");
							$("#IntRateAlertDone").hide();

				}
		});

		$("[id='delTbl']").click(function(){
			console.log($(this).val())
			let id = $(this).val();
			let row = $(this).closest("tr");

			$.ajax({
				url: "th_deletegroup.php", 
				data: {
					id: id
				},
				type: 'post',
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						console.log(res.msg)
						row.remove();
					} else {
						console.log(res.msg)
						row.remove();
					}
				},
				error: function(res){
					console.log(res)
				}
			})
		})

		$("#btnadddisc").on("click", function(){

			if($('div.dsccodedetail').length>=1){
				var xy = parseInt($('div.dsccodedetail:last').attr("id")) + 1;
			}else{
				var xy = parseInt(1);
			}

			
			/*
				$.ajax ({
					url: "th_getlastdisc.php",
					async: false,
					success: function( data ) {
						if(data!="False"){
							xy = parseInt(data) + 1;							
						}else{
							xy = 1;
						}
					}				
				});
			*/

				var divhead = "<div class=\"dsccodedetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
				var divcode = "<div class=\"col-xs-2 nopadwleft\"><input type=\"text\" name=\"txtcdsccode[]\" id=\"txtcdsccode"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" /></div>";
																							
				var divdesc = "<div class=\"col-xs-4 nopadwleft\"><input type=\"text\" name=\"txtcdscdesc[]\" id=\"txtcdscdesc"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";
													
				var divacct = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtdscacct[]\" id=\"txtdscacct"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\"  placeholder=\"Acct Code...\" /></div>"; 

				var divacctdsc = "<div class=\"col-xs-3 nopadwleft\"><input type=\"text\" name=\"txtdscacctdsc[]\" id=\"txtdscacctdsc"+xy+"\" value=\"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\"  placeholder=\"Acct Code Desc...\" /></div>"; 
																															
				var divstat = "<div class=\"col-xs-1 nopadwleft\">&nbsp;<span class='label label-success'>Active</span></div>";                                               
				var divend = "</div>";
							//alert(divhead + divcode + divrate + divratediv + divbasec + divdesc + divacct + divstat + divend); 
								
				$("#TblDISC").append(divhead + divcode + divdesc + divacct + divacctdsc+ divstat + divend);
				
				
				$("#txtdscacct"+xy).on('keyup', function(event) {
							
					if(event.keyCode == 13){
								
						var dInput = this.value;
							
						$.ajax({
							type:'post',
							url:'../Accounting/getaccountid.php',
							data: 'c_id='+ $(this).val(),                 
							success: function(value){
											//alert(value);
								if(value.trim()!=""){
									$("#txtdscacctdsc"+xy).val(value.trim());
								}
								else{
									alert("Invalid Account Code");
									$('#txtdscacct'+xy).val("").change(); 
									$('#txtdscacctdsc'+xy).val("").change(); 
								}
							}
						});
									
					}
				});

				$("#txtdscacctdsc"+xy).typeahead({
							autoSelect: true,
							source: function(request, response) {
								$.ajax({
									url: "th_accounts.php",
									dataType: "json",
									data: {
										query: $("#txtdscacctdsc"+xy).val()
									},
									success: function (data) {
										response(data);
									}
								});
							},
							displayText: function (item) {
								return item.id + " : " + item.name;
							},
							highlighter: Object,
							afterSelect: function(item) { 					
											
								$('#txtdscacctdsc'+xy).val(item.name).change(); 
								$('#txtdscacct'+xy).val(item.id); 
								
							}
						});

		});

		$("#btdiscnt").on("click", function(){

			var isOk = "True";

			$('.dsccodedetail').each(function(i, obj) {

				divid = $(this).attr("id");
				vardsccode = $(this).find('input[name="txtcdsccode[]"]').val();
				vardscdesc = $(this).find('input[name="txtcdscdesc[]"]').val();
				vardscacctcode = $(this).find('input[name="txtdscacct[]"]').val();

				if(vardsccode!=="" && vardsccode!==undefined){
					//alert("th_savdsccodes.php?code="+vardsccode+"&desc="+vardscdesc+"&acctid="+vardscacctcode);
					$.ajax ({
						url: "th_savdsccodes.php",
						data: { code: vardsccode,  desc: vardscdesc, acctid: vardscacctcode },
						async: false,
						success: function( data ) {
							if(data.trim()!="True"){
								isOk = data;
							}
						}
					
					});
				}
									
			});	


			if(isOk == "True"){
				$('#TblDISC').html("");
				loaddiscs();
				
				$("#DiscAlertDone").html("<b>SUCCESS: </b> Discounts Codes table successfully saved!");        
				$("#DiscAlertDone").show(); 

						$("#DiscAlertMsg").html("");
						$("#DiscAlertMsg").hide();
				
			}
			else{
				$("#DiscAlertMsg").html("<b>Error Saving:</b>"+isOk);
				$("#DiscAlertMsg").show();

						$("#DiscAlertDone").html("");
						$("#DiscAlertDone").hide();

			}

		});

		
		$(".semicut").on("change", function(){
			var x = $(this).attr("id");
			var yval = $(this).val();
			alert(yval);

			if(x=="semidayfr1"){
				var ycol = "dayfrom1";
			}
			else if(x=="semidayfr2"){
				var ycol = "dayfrom2";
			}
			else if(x=="semidayto1"){
				var ycol = "dayto1";
			}
			else if(x=="semidayto2"){
				var ycol = "dayto2";
			}
			alert("th_setsemi.php?col="+ ycol +"&val=" + yval);
									$.ajax ({
									url: "th_setsemi.php",
									data: { col: ycol, val: yval },
									dataType: 'json',
									async:false,
									success: function( data ) {
										if(data.trim()=="False"){
											alert("There's a problem updating your cutoff values!");
										}
									}
									});
		
		});


		//Checking of uploaded file.. must be image
			$("#filecpmnid").change(function() {
				$("#add_err").empty(); // To remove the previous error message
				var file = this.files[0];
				var imagefile = file.type;
				var match= ["image/jpeg","image/png","image/jpg"];
				if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
				{
					$('#previewing').attr('src','../../imgusers/preview.jpg');
					
					$("#add_err").css('display', 'inline', 'important');
					$("#add_err").html("<div class='alert alert-danger nopadwleft' role='alert'>Please Select A valid Image File. <b>Note: </b>Only jpeg, jpg and png Images type allowed</div>");
					return false;
				}
				else
				{

							var fd = new FormData();
							var files = $('#filecpmnid')[0].files;
							var isupz = "YES";

							if(files.length > 0 ){
									fd.append('file',files[0]);

									$.ajax({
										url: 'uploadcomlogo.php',
										type: 'post',
										data: fd,
										contentType: false,
										processData: false,
										success: function(response){

											if(response == 0){

													isupz = "NO";
													alert('file not uploaded');

											}
										},
									});
							}

							if(isupz=="YES"){
								var reader = new FileReader();
								reader.onload = imageIsLoaded;
								reader.readAsDataURL(this.files[0]);              
							}

				}
			});

			$("#txtPOBodyEmail").on("blur", function(){
					$.ajax({
						url: 'updtequote.php',
						data: { val: $(this).val(), nme:"POEMAILBODY" },
						dataType: "text",
							success: function(response){

								if(response.trim() == "True"){

									$("#divPOBodyEmail").html("Header Saved!");

								}
							},
					});
			});

			$("#txtQuotePrintHdr").on("blur", function(){
					$.ajax({
						url: 'updtequote.php',
						data: { val: $(this).val(), nme:"QUOTEHDR" },
						dataType: "text",
							success: function(response){

								if(response.trim() == "True"){

									$("#divQuotePrintHdr").html("Header Saved!");

								}
							},
					});
			});

			$("#txtQuotePrintFtr").on("blur", function(){

					$.ajax({
						url: 'updtequote.php',
						data: { val: $(this).val(), nme:"QUOTEFTR" },
						dataType: "text",
						success: function(response){

							if(response.trim() == "True"){

								$("#divQuotePrintFtr").html("Footer Saved!");

							}
						},
					});
			});

			$("#QuoteRemarks").on("blur", function() {
				let remarks = $(this).val();
				$.ajax({
					url: "updatequote.php",
					type: "POST",
					data: { 
						description: remarks,
						code: "QUOTE_RMKS" 
					},
					dataType: "json",
					async: false,
					success: function(res) {
						if(res.valid) {
							$("#QuoteRemarksChk").css("color", "green");
							$("#QuoteRemarksChk").text(res.msg);
						} 
					},
					error: function(res) {
						console.log(res)
					}
				})
			})

			$(".lvlamtcls").on("blur", function(){

				var dlvl =  $(this).data("id");
					$.ajax({
						url: 'updtepoamt.php',
						data: { val: $(this).val(), id: dlvl},
						dataType: "text",
						success: function(response){

							if(response.trim() == "True"){
								//alert("divlevel"+dlvl+"amounts");

								$("#divlevel"+dlvl+"amounts").html("Amount Saved!");

							}
						},
					});
			});

			$("#btnewt").on("click", function(){

				var isOk = "True";

				$('.ewtcodedetail').each(function(i, obj) {
				
					divid = $(this).attr("id");
					varewtcode = $(this).find('input[name="txtcewtcode[]"]').val();
					varewtdesc = $(this).find('input[name="txtcewtdesc[]"]').val();
					varewtrate = $(this).find('input[name="txtcewtrate[]"]').val();
					varewtratedivs = $(this).find('input[name="txtcewtratedivs[]"]').val(); 
					varewtselbase = $(this).find('select[name="selbasecd[]"]').val();

				//	alert("th_savewtcodes.php?code="+varewtcode+"&desc="+varewtdesc+"&rate="+varewtrate+"&divs="+varewtratedivs+"&vbase="+varewtselbase);
					$.ajax ({
						url: "th_savewtcodes.php",
						data: { code: varewtcode,  desc: varewtdesc, rate: varewtrate, divs: varewtratedivs, vbase: varewtselbase },
						async: false,
						success: function( data ) {
							if(data.trim()!="True"){
								isOk = data;
							}
						}
					
					});
										
				});	
			
			
				if(isOk == "True"){
					$('#TblAcctDef tbody').empty();
					loadacctdefaults();
					
					$("#EWTAlertDone").html("<b>SUCCESS: </b> EWT Codes table successfully saved!");
					$("#EWTAlertDone").show();

							$("#EWTAlertMsg").html("");
							$("#EWTAlertMsg").hide();
					
				}
				else{
					$("#EWTAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#EWTAlertMsg").show();

							$("#EWTAlertDone").html("");
							$("#EWTAlertDone").hide();

				}

			});


			$("#btnaddcondet").on("click", function(){

				if($('div.cntctdetdetail').length>=1){
					var xy = parseInt($('div.cntctdetdetail:last').attr("id")) + 1;
				}else{
					var xy = parseInt(1);
				}

				var divhead = "<div class=\"cntctdetdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";

				var divdesc = "<div class=\"col-xs-4 nopadwleft\"><input type=\"text\" name=\"txtcntctdetdesc[]\" id=\"txtcntctdetdesc"+xy+"\" value=\"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /> <input type=\"hidden\" name=\"txtcntctdetid[]\" id=\"txtcntctdetid"+xy+"\" value=\"new\" /></div>";
																															
				var divstat = "<div class=\"col-xs-1 nopadwleft\">&nbsp;<span class='label label-success'>Active</span></div>";                                               
				var divend = "</div>";
								
				$("#TblCONTDET").append(divhead + divdesc + divstat + divend);

			});

			$("#btcntctdets").on("click", function(){

				var isOk = "True";

				$('.cntctdetdetail').each(function(i, obj) {

					divid = $(this).attr("id");
					varcntctid = $(this).find('input[type=hidden][name="txtcntctdetid[]"]').val();
					varcntct = $(this).find('input[name="txtcntctdetdesc[]"]').val();

					$.ajax ({
						url: "th_savcntctsdet.php",
						data: { desc: varcntct, code: varcntctid},
						async: false,
						success: function( data ) {
							if(data.trim()!="True"){
								isOk = data;
							}
						}
					
					});
										
				});	


				if(isOk == "True"){
					$('#TblCONTDET').html("");
					loadConDets();
					
					$("#ConDetAlertDone").html("<b>SUCCESS: </b> Contacts Details successfully saved!");        
					$("#ConDetAlertDone").show(); 

							$("#ConDetAlertMsg").html("");
							$("#ConDetAlertMsg").hide();
					
				}
				else{
					$("#ConDetAlertMsg").html("<b>Error Saving:</b>"+isOk);
					$("#ConDetAlertMsg").show();

							$("#ConDetAlertDone").html("");
							$("#ConDetAlertDone").hide();

				}

			});

			
			
	});

	function loadquotesprint(){

			$.ajax ({
				url: "th_loadQuotesPrint.php",
				dataType: 'json',
				async:false,
				success: function( result ) {
					console.log(result);
					$.each(result,function(index,item){   

							if(item.ccode=='QUOTEHDR'){
									$("#txtQuotePrintHdr").val(item.cdesc);
							}

							if(item.ccode=='QUOTEFTR'){
									$("#txtQuotePrintFtr").val(item.cdesc);
							}

							if(item.ccode=='POEMAILBODY'){
									$("#txtPOBodyEmail").val(item.cdesc);
							}

							if(item.ccode == 'QUOTE_RMKS') {
								$("#QuoteRemarks").val(item.cdesc);
							}

					});     
				}
			});


	}

	function loadcompany(){
			$.ajax ({
				url: "th_loadcompany.php",
				dataType: 'json',
				async:false,
					success: function( result ) {

					console.log(result);
					$.each(result,function(index,item){
						$("#txtcompanycom").val(item.cname);
						$("#txtcompanydesc").val(item.cdesc);
						$("#txtcompanyadd").val(item.cadd); 
						$("#txtcompanyzip").val(item.czip);
						$("#txtcompanytin").val(item.ctin);
						$("#txtcompanyemail").val(item.emailadd);
						$("#txtcompanycpnum").val(item.ccpnum);
						$('#ptucode').val(item.ptucode);
						$('#ptudate').val(item.ptudate);

						$("#previewing").attr('src',item.clogoname);

						var vatcompcode = item.lvat;
						
									$.ajax ({
									url: "th_loadvat.php",
									dataType: 'json',
									async:false,
									success: function( result ) {
										var isselctd = "";
										
										console.log(result);
										$.each(result,function(index,item){
											
											if(item.cvatcode==vatcompcode){
											isselctd = "selected";
											}else{
												isselctd = "";
											}
											$("#selcompanyvat").append("<option value=\""+item.cvatcode+"\" "+isselctd+">"+item.cvatdesc+"</option>");
										});
									}
									});

						
					});
				}
			});
				
	}

	function loadgroups(){
		$('.cgroup').each(function(i, obj) {
				var y = $(this).attr("data-content"); 
				var nme = $(this).attr("name");
				var r = nme.replace( /^\D+/g, '');
							
			
					$.ajax ({
							url: "th_loadgroup.php",
							data: { nme: y},
							success: function( result ) {
					
						if(result.trim()!="False"){						
							$("#txtGroup"+r).val(result);
						}
						else{
							$("#txtGroup"+r).val("");
						}
							}
					});

		});
	}

	function loadtax(){
		
					$.ajax ({
							url: "th_loadtax.php",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
								$.each(result,function(index,item){

									if(item.ctaxcode!=""){	
										if(item.cstat == "ACTIVE"){ 
											var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setTaxStat('"+item.ctaxcode+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
										} else{
											var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setTaxStat('"+item.ctaxcode+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
										}
														
										var divhead = "<div class=\"taxdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
										var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxcode[]\" id=\"txtctaxcode"+item.nident+"\" value=\""+item.ctaxcode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
										var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtctaxdesc[]\" id=\"txtctaxdesc"+item.nident+"\" value=\""+item.ctaxdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" /></div>";						
										var divrate = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxrate[]\" id=\"txtctaxrate"+item.nident+"\" value=\""+item.nrate+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" /></div>"; 
										var divstat = "<div class=\"col-xs-3\">&nbsp;"+spanstat+"</div>";                                               
										var divend = "</div>";
										
											
										$("#TblTax").append(divhead + divcode + divdesc + divrate + divstat + divend);
										//$("#TblTax").html("Hello String");

									}
						
								});
							}
					});
		
		
	}

	function loadewt(){
		
					$.ajax ({
							url: "th_loadewt.php",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
					$.each(result,function(index,item){

						if(item.ctaxcode!=""){	
							if(item.cstat == "ACTIVE"){ 
								var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setEWTStat('"+item.ctaxcode+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
							} else{
								var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setEWTStat('"+item.ctaxcode+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
							}
							
							if(item.cbase == "NET"){ 
								var isNet = "selected";
								var isGrs = "";
							} else{
								var isNet = "";
								var isGrs = "selected";
							}
				
							var divhead = "<div class=\"ewtcodedetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
							var divcode = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtcewtcode[]\" id=\"txtcewtcode"+item.nident+"\" value=\""+item.ctaxcode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";

							var divrate = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtcewtrate[]\" id=\"txtcewtrate"+item.nident+"\" value=\""+item.nrate+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" /></div>";

							var divratediv = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtcewtratedivs[]\" id=\"txtcewtratedivs"+item.nident+"\" value=\""+item.nratedivisor+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" /></div>";

							var divbasec = "<div class=\"col-xs-1 nopadwleft\"><select class=\"form-control input-xs\" name=\"selbasecd[]\" id=\"selbasecd"+item.nident+"\"><option value=\"GROSS\" "+isGrs+">GROSS</option><option value=\"NET\" "+isNet+">NET</option></select></div>";
																							
							var divdesc = "<div class=\"col-xs-4 nopadwleft\"><input type=\"text\" name=\"txtcewtdesc[]\" id=\"txtcewtdesc"+item.nident+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";
													
							var divacct = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtewtacct[]\" id=\"txtewtacct"+item.nident+"\" value=\""+item.cacctno+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Acct Code...\" /></div>"; 
																															
							var divstat = "<div class=\"col-xs-2 nopadwleft\">&nbsp;"+spanstat+"</div>";                                               
							var divend = "</div>";
							//alert(divhead + divcode + divrate + divratediv + divbasec + divdesc + divacct + divstat + divend);
								
							$("#TblEWT").append(divhead + divcode + divrate + divratediv + divbasec + divdesc + divacct + divstat + divend);

						}
						
					});
							}
					});
		
		
	}

	function loadvat(){
		
				$.ajax ({
					url: "th_loadvat.php",
					dataType: 'json',
					async:false,
						success: function( result ) {

							console.log(result);
							$.each(result,function(index,item){

								if(item.cvatcode!=""){	
									if(item.cstat == "ACTIVE"){ 
										var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setVATStat('"+item.cvatcode+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
									} else{
										var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setVATStat('"+item.cvatcode+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
									}

									if(item.lcomp == 1){ 
										var isYes = "selected";
										var isNo = "";
									} else{
										var isNo = "selected";
										var isYes = "";
									}
													
									var divhead = "<div class=\"vatdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
									var divcode = "<div class=\"col-xs-1 nopadwright\"><input type=\"text\" name=\"txtcvatcode[]\" id=\"txtcvatcode"+item.nident+"\" value=\""+item.cvatcode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
									var divrates = "<div class=\"col-xs-1 nopadwright\"><input type=\"text\" name=\"txtnrates[]\" id=\"txtnrates"+item.nident+"\" value=\""+item.nrate+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs text-right\" placeholder=\"Rate...\" /></div>";
									var divdesc = "<div class=\"col-xs-4 nopadwright\"><input type=\"text\" name=\"txtcvatdesc[]\" id=\"txtcvatdesc"+item.nident+"\" value=\""+item.cvatdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
									var divrem = "<div class=\"col-xs-3 nopadwright\"><input type=\"text\" name=\"txtcvatrem[]\" id=\"txtcvatrem"+item.nident+"\" value=\""+item.nrem+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Remarks...\" /></div>"; 
									var divcomp = "<div class=\"col-xs-1 nopadwright text-cebter\"><select class=\"form-control input-xs\" name=\"selcomp[]\" id=\"selcomp"+item.nident+"\"><option value=\"1\" "+isYes+">YES</option><option value=\"0\" "+isNo+">NO</option></select></div>";                                                 
									var divstat = "<div class=\"col-xs-2 nopadwright\">&nbsp;"+spanstat+"</div>";                                               
									var divend = "</div>";
									
										
									$("#TblVAT").append(divhead + divcode + divrates + divdesc + divrem + divcomp + divstat + divend);
									//$("#TblTax").html("Hello String");

								}
								
							});
						}
				});
		
		
	}

	function loadterms(){

					$.ajax ({
							url: "th_loadterms.php?id=TERMS",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
					$.each(result,function(index,item){

						if(item.ccode!=""){	
							if(item.cstat == "ACTIVE"){ 
								var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','INACTIVE','CUST TERMS','TERMS','TblTerms','TERMS','terms')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
							} else{ 
								var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','ACTIVE','CUST TERMS','TERMS','TblTerms','TERMS','terms');\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
							}

											
							var divhead = "<div class=\"termdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
							var divcode = "<div class=\"col-xs-1\"><input type=\"text\" name=\"txtctermcode[]\" id=\"txtctermcode"+item.nident+"\" value=\""+item.ccode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtctermdesc[]\" id=\"txtctermdesc"+item.nident+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
							var divstat = "<div class=\"col-xs-2\">&nbsp;"+spanstat+"</div>";                                               
							var divend = "</div>";
							
								
							$("#TblTerms").append(divhead + divcode + divdesc + divstat + divend);
							//$("#TblTax").html("Hello String");

						}
						
					});
							}
					});


	}

	/*
	function loadloantyp(){

					$.ajax ({
							url: "th_loadterms.php?id=LOANTYP",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
					$.each(result,function(index,item){

						if(item.ccode!=""){	
							if(item.cstat == "ACTIVE"){ 
								var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','INACTIVE','LOAN TYPE','LOANTYP','Tblloantyp','LoanType','loantyp')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
							} else{ 
								var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','ACTIVE','LOAN TYPE','LOANTYP','Tblloantyp','LoanType','loantyp')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
							}

											
							var divhead = "<div class=\"loanstypdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
							var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtcloantypcode[]\" id=\"txtcloantypcode"+item.nident+"\" value=\""+item.ccode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcloantypdesc[]\" id=\"txtcloantypdesc"+item.nident+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
							var divstat = "<div class=\"col-xs-3\">&nbsp;"+spanstat+"</div>";                                               
							var divend = "</div>";
							
								
							$("#Tblloantyp").append(divhead + divcode + divdesc + divstat + divend);
							//$("#TblTax").html("Hello String");

						}
						
					});
							}
					});


	}

	function loadloantrm(){

					$.ajax ({
							url: "th_loadterms.php?id=LOANTRM",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
					$.each(result,function(index,item){

						if(item.ccode!=""){	
							if(item.cstat == "ACTIVE"){ 
								var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','INACTIVE','LOAN TERMS','LOANTRM','Tblloantrm','LoanTerm','loantrm')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
							} else{ 
								var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','ACTIVE','LOAN TERMS','LOANTRM','Tblloantrm','LoanTerm','loantrm')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
							}

											
							var divhead = "<div class=\"loanstrmdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
							var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtcloantrmcode[]\" id=\"txtcloantrmcode"+item.nident+"\" value=\""+item.ccode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcloantrmdesc[]\" id=\"txtcloantrmdesc"+item.nident+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
							var divstat = "<div class=\"col-xs-3\">&nbsp;"+spanstat+"</div>";                                               
							var divend = "</div>";
							
								
							$("#Tblloantrm").append(divhead + divcode + divdesc + divstat + divend);
							//$("#TblTax").html("Hello String");

						}
						
					});
							}
					});


	}

	*/

	function loadintrate(){

					$.ajax ({
							url: "th_loadterms.php?id=LOANINT",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
					$.each(result,function(index,item){

						if(item.ccode!=""){	
							if(item.cstat == "ACTIVE"){ 
								var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','INACTIVE','INT RATES','LOANINT','Tblintrate','IntRate','intrate')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
							} else{ 
								var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setgrouping('"+item.ccode+"','ACTIVE','INT RATES','LOANINT','Tblintrate','IntRate','intrate')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
							}

											
							var divhead = "<div class=\"intratesdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
							var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtintratecode[]\" id=\"txtintratecode"+item.nident+"\" value=\""+item.ccode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
							var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtintratedesc[]\" id=\"txtintratedesc"+item.nident+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
							var divstat = "<div class=\"col-xs-3\">&nbsp;"+spanstat+"</div>";                                               
							var divend = "</div>";
							
								
							$("#Tblintrate").append(divhead + divcode + divdesc + divstat + divend);
							//$("#TblTax").html("Hello String");

						}
						
					});
							}
					});


	}


	function loadsemi(){
		var dayfr1 = "";
		var dayto1 = "";
		var dayfr2 = "";
		var dayto2 = "";

		$.ajax ({
			url: "th_loadsemi.php",
			dataType: 'json',
			async:false,
			success: function( result ) {
										
				console.log(result);
				$.each(result,function(index,item){
						
					dayfr1 = item.dyfr1;
					dayto1 = item.dyto1;
					dayfr2 = item.dyfr2;
					dayto2 = item.dyto2;
								
				});
			}
		});
		
		//day from 1
		for(i=1; i<=30; i++){
			if(i==dayfr1){
				isselected = "selected";
			}
			else{
				isselected = "";
			}
			
			if(i==30){
				$("#semidayfr1").append("<option value=\""+i+"\" "+isselected+">"+i+"/31</option>");
			}else{
				$("#semidayfr1").append("<option value=\""+i+"\" "+isselected+">"+i+"</option>");
			}
		}

		//day to 1
		for(i=1; i<=30; i++){
			if(i==dayto1){
				isselected = "selected";
			}
			else{
				isselected = "";
			}
			
			if(i==30){
				$("#semidayto1").append("<option value=\""+i+"\" "+isselected+">"+i+"/31</option>");
			}else{
				$("#semidayto1").append("<option value=\""+i+"\" "+isselected+">"+i+"</option>");
			}
		}

		//day from 2
		for(i=1; i<=30; i++){
			if(i==dayfr2){
				isselected = "selected";
			}
			else{
				isselected = "";
			}

			if(i==30){
				$("#semidayfr2").append("<option value=\""+i+"\" "+isselected+">"+i+"/31</option>");
			}else{		
				$("#semidayfr2").append("<option value=\""+i+"\" "+isselected+">"+i+"</option>");
			}
		}

		//day to 2
		for(i=1; i<=30; i++){
			if(i==dayto2){
				isselected = "selected";
			}
			else{
				isselected = "";
			}

			if(i==30){
				$("#semidayto2").append("<option value=\""+i+"\" "+isselected+">"+i+"/31</option>");
			}else{				
				$("#semidayto2").append("<option value=\""+i+"\" "+isselected+">"+i+"</option>");
			}
		}

	}

	function setTaxStat(code,stat){
				$.ajax ({
					url: "th_settaxstat.php",
					data: { code: code,  stat: stat },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							$("#TAXAlertMsg").html("<b>Error: </b>"+ data);
							$("#TAXAlertMsg").show();
							
							$("#TAXAlertDone").html("");
							$("#TAXAlertDone").hide();

						}
						else{
							$("#TblTax").html("");
							loadtax();
							
							$("#TAXAlertDone").html("<b>SUCCESS: </b> "+code+" status changed to "+ stat);
							$("#TAXAlertDone").show();

							$("#TAXAlertMsg").html("");
							$("#TAXAlertMsg").hide();

						}
					}
				
				});

	}

	function setDISCSStat(code,stat){
				$.ajax ({
					url: "th_setdiscsstat.php",
					data: { code: code,  stat: stat },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							$("#DiscAlertMsg").html("<b>Error: </b>"+ data);
							$("#DiscAlertMsg").show();
							
							$("#DiscAlertDone").html("");
							$("#DiscAlertDone").hide();

						}
						else{
							$('#TblDISC').html("");
							loaddiscs();
							
							$("#DiscAlertDone").html("<b>SUCCESS: </b> "+code+" status changed to "+ stat);  
							$("#DiscAlertDone").show();

							$("#DiscAlertMsg").html("");
							$("#DiscAlertMsg").hide();

						}
					}
				
				});

	}


	function setVATStat(code,stat){
				$.ajax ({
					url: "th_setvatstat.php",
					data: { code: code,  stat: stat },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							$("#VATAlertMsg").html("<b>Error: </b>"+ data);
							$("#VATAlertMsg").show();
							
							$("#VATAlertDone").html("");
							$("#VATAlertDone").hide();

						}
						else{
							$("#TblVAT").html("");
							loadvat();
							
							$("#VATAlertDone").html("<b>SUCCESS: </b> "+code+" status changed to "+ stat);
							$("#VATAlertDone").show();

							$("#VATAlertMsg").html("");
							$("#VATAlertMsg").hide();

						}
					}
				
				});

	}

	function setgrouping(code,stat,msg,id,tbl,alrt,ld){
				$.ajax ({
					url: "th_setgrpstat.php",
					data: { code: code,  stat: stat, msg:msg, id:id },
					async: false,
					success: function( data ) {
						if(data.trim()!="True"){
							$("#"+alrt+"AlertMsg").html("<b>Error: </b>"+ data);
							$("#"+alrt+"AlertMsg").show();
							
							$("#"+alrt+"AlertDone").html("");
							$("#"+alrt+"AlertDone").hide();

						}
						else{
							$("#"+tbl+"").html("");
							if(ld=="terms"){
								loadterms();
							}else if(ld=="loantyp"){
								loadloantyp();
							}else if (ld=="loantrm"){
								loadloantrm();
							}else if (ld=="intrate"){
								loadintrate();
							}
							
							$("#"+alrt+"AlertDone").html("<b>SUCCESS: </b> "+code+" status changed to "+ stat);
							$("#"+alrt+"AlertDone").show();

							$("#"+alrt+"AlertMsg").html("");
							$("#V"+alrt+"AlertMsg").hide();

						}
					}
				
				});

	}


	function setparamval (code, valz, msgid){
				$.ajax ({
					url: "th_setinvcheck.php",
					data: { code: code,  id: valz },
					async: false,
					success: function( data ) {
						if(data.trim()=="True"){
							$("#"+msgid).html("&nbsp;&nbsp;<i class=\"fa fa-check\" style=\"color:green;\"></i>");
						}
					}
				
				});
				
				if(code=='POSCLMT' && valz=='Semi'){
					$("#semidiv").show();
				}
				else if(code=='POSCLMT' && valz!='Semi'){
					$("#semidiv").hide();
				}
				
				if(code=='CRDLIMIT' && valz==1){
					$("#selcrdlmtwarn").attr("disabled", false);
				}
				else if(code=='CRDLIMIT' && valz==0){
					$("#selcrdlmtwarn").attr("disabled", true);
				}

	}

	function updateacctsdef(idz,valz,msgid){
		//alert(valz);
				$.ajax ({
					url: "th_setacctsdef.php",
					data: { code: idz,  id: valz },
					async: false,
					success: function( data ) {
						if(data.trim()=="True"){
							$("#"+msgid).html("&nbsp;&nbsp;<i class=\"fa fa-check\" style=\"color:green;\"></i>");
						}
					}
				
				});
		
	}

	function showalert(id, msg){
		$('#' +id).html("&nbsp;&nbsp;<i class=\"fa fa-check\" style=\"color:green;\">"+msg+"</i>")
	}

	function loadcusgrps(){
		$('.ccustgroup').each(function(i, obj) {
				var y = $(this).attr("data-content"); 
				var nme = $(this).attr("name");
				var r = nme.replace( /^\D+/g, '');
							
			
					$.ajax ({
							url: "th_loadgroup.php",
							data: { nme: y},
							success: function( result ) {
					
						if(result.trim()!="False"){						
							$("#txtCustGroup"+r).val(result);
						}
						else{
							$("#txtCustGroup"+r).val("");
						}
							}
					});

		});
	}

	function loadsuppgrps(){
		$('.csuppgroup').each(function(i, obj) {
				var y = $(this).attr("data-content"); 
				var nme = $(this).attr("name");
				var r = nme.replace( /^\D+/g, '');
							
			
					$.ajax ({
							url: "th_loadgroup.php",
							data: { nme: y},
							success: function( result ) {
					
						if(result.trim()!="False"){           
							$("#txtSuppGroup"+r).val(result);
						}
						else{
							$("#txtSuppGroup"+r).val("");
						}
							}
					});

		});
	}

	function loadacctdefaults(){


		$.ajax ({
							url: "th_loadacctdefs.php",
				dataType: 'json',
				async:false,
							success: function( result ) {

								console.log(result);
					$.each(result,function(index,item){
						
						if(item.ccode!=""){	
							var tbl = document.getElementById('TblAcctDef').getElementsByTagName('tr');
							var lastRow = tbl.length;

							var tblz = document.getElementById('TblAcctDef').getElementsByTagName('tbody')[0];
							var a=tblz.insertRow(tblz.rows.length);
							
							var u=a.insertCell(0);
								u.style.padding = "1px";
								u.style.width = "100px";
							var v=a.insertCell(1);
								v.style.padding = "1px";
								v.style.width = "400px";
							var w=a.insertCell(2);
								w.style.padding = "1px";
								w.style.width = "100px";
							var x=a.insertCell(3);
								x.style.padding = "1px";
								x.style.width = "400px";
								
							u.innerHTML = "<input type=\"hidden\" name=\"txtnident[]\" id=\"txtnident"+lastRow+"\" value=\""+lastRow+"\" data-citmno=\""+lastRow+"\" /><input type=\"text\" name=\"txtccode[]\" id=\"txtccode"+lastRow+"\" value=\""+item.ccode+"\" data-citmno=\""+item.nidentity+"\" class=\"form-control input-xs\" readonly />";
							v.innerHTML = "<input type=\"text\" name=\"txtcdesc[]\" id=\"txtcdesc"+lastRow+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nidentity+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" readonly/>";
							w.innerHTML = "<input type=\"text\" name=\"txtcacctid[]\" id=\"txtcacctid"+lastRow+"\" value=\""+item.cacctcode+"\" data-citmno=\""+item.nidentity+"\" class=\"form-control input-xs\"  placeholder=\"Enter Account Code...\" />";
							x.innerHTML = "<input type=\"text\" name=\"txtcacctdesc[]\" id=\"txtcacctdesc"+lastRow+"\" value=\""+item.ctitle+"\" data-citmno=\""+item.nidentity+"\" class=\"form-control input-xs\"  placeholder=\"Enter Account Tile...\" />";
								
							$("#txtcacctid"+lastRow).on('keyup', function(event) {
							
								if(event.keyCode == 13){
								
									var dInput = this.value;
							
										$.ajax({
										type:'post',
										url:'../Accounting/getaccountid.php',
										data: 'c_id='+ $(this).val(),                 
										success: function(value){
											//alert(value);
											if(value.trim()!=""){
												$("#txtcacctdesc"+lastRow).val(value.trim());
											}
											else{
												alert("Invalid Account Code");
												$("#txtcacctdesc"+lastRow).val("");
												$('#txtcacctid'+lastRow).val("").change(); 
											}
										}
										});
									
								}
							});
							
							$("#txtcacctdesc"+lastRow).typeahead({						 
								autoSelect: true,
								source: function(request, response) {							
									$.ajax({
										url: "../Maintenance/th_accounts.php",
										dataType: "json",
										data: { query: request },
										success: function (data) {
											response(data);
										}
									});
								},
								displayText: function (item) {
									return item.id + " : " + item.name;
								},
								highlighter: Object,
								afterSelect: function(item) { 					
											
									$('#txtcacctdesc'+lastRow).val(item.name).change(); 
									$('#txtcacctid'+lastRow).val(item.id); 
																	
								}
						});
						}
						
					});
							}
			});
		
	}

	function loadbasecustomer(){
		$.ajax({
			url: 'th_loadbasecustomer.php',
			dataType: 'json',
			async: false,
			success: function(res){
				if(res.valid){
					$('#basecustomer').val(res.data)
				} else {
					console.log(res.msg)
				}
			},
			error: function(res){
				console.log(res)
			}
		})
	}

	function loaddiscs(){
		$.ajax ({
			url: "th_loaddiscs.php",
			dataType: 'json',
			async:false,
			success: function( result ) {

				console.log(result);
				$.each(result,function(index,item){

					if(item.ccode!=""){	
						if(item.cstat == "ACTIVE"){ 
							var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setDISCSStat('"+item.ccode+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						} else{
							var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setDISCSStat('"+item.ccode+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}

						var divhead = "<div class=\"dsccodedetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
						var divcode = "<div class=\"col-xs-2 nopadwleft\"><input type=\"text\" name=\"txtcdsccode[]\" id=\"txtcdsccode"+item.nident+"\" value=\""+item.ccode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly/></div>";
																									
						var divdesc = "<div class=\"col-xs-4 nopadwleft\"><input type=\"text\" name=\"txtcdscdesc[]\" id=\"txtcdscdesc"+item.nident+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";
															
						var divacct = "<div class=\"col-xs-1 nopadwleft\"><input type=\"text\" name=\"txtdscacct[]\" id=\"txtdscacct"+item.nident+"\" value=\""+item.cacctno+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Acct Code...\" /></div>"; 

						var divacctdsc = "<div class=\"col-xs-3 nopadwleft\"><input type=\"text\" name=\"txtdscacctdsc[]\" id=\"txtdscacctdsc"+item.nident+"\" value=\""+item.cacctdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Acct Code Desc...\" /></div>"; 
																															
						var divstat = "<div class=\"col-xs-2 nopadwleft\">&nbsp;"+spanstat+"</div>";                                               
						var divend = "</div>";
								
						$("#TblDISC").append(divhead + divcode + divdesc + divacct + divacctdsc+ divstat + divend);
				
				
						$("#txtdscacct"+item.nident).on('keyup', function(event) {
									
							if(event.keyCode == 13){
										
								var dInput = this.value;
									
								$.ajax({
									type:'post',
									url:'../Accounting/getaccountid.php',
									data: 'c_id='+ $(this).val(),                 
									success: function(value){
													//alert(value);
										if(value.trim()!=""){
											$("#txtdscacctdsc"+item.nident).val(value.trim());
										}
										else{
											alert("Invalid Account Code");
											$('#txtdscacct'+item.nident).val("").change(); 
											$('#txtdscacctdsc'+item.nident).val("").change(); 
										}
									}
								});
											
							}
						});

						$("#txtdscacctdsc"+item.nident).typeahead({
									autoSelect: true,
									source: function(request, response) {
										$.ajax({
											url: "th_accounts.php",
											dataType: "json",
											data: {
												query: $("#txtdscacctdsc"+item.nident).val()
											},
											success: function (data) {
												response(data);
											}
										});
									},
									displayText: function (item) {
										return item.id + " : " + item.name;
									},
									highlighter: Object,
									afterSelect: function(item) { 					
													
										$('#txtdscacctdsc'+item.nident).val(item.name).change(); 
										$('#txtdscacct'+item.nident).val(item.id); 
										
									}
								});


							}
								
						});
			}
		});
	}

	function insert_table(){
		// tableform
		$('<tr>').append(
			$("<td>").html("<input type='text' id='tableName' name='tableName' placeholder='Name of Table' class='input-sm'>"),
			$("<td>").html("<input type='text' id='tableRemarks' name='tableRemarks' placeholder='Remarks' class='input-sm' />"),
			$('<td>').html("<button type='button' id='delTbl' name='delTbl' onclick='removeRow.call(this)' class='btn btn-sm btn-danger'><i class='fa fa-trash'></i>&nbsp;delete</button>")
		).appendTo("#dataTable > tbody")
	}

	function removeRow(){
		$(this).parent().closest('tr').remove();
	}

	function table_save(){
		let forms = new FormData($("#tableform")[0]);
		const remarkList = []
		const tableList = []
		const idlist = []

		document.querySelectorAll('[id="tableName"]').forEach(element => {
			tableList.push(element.value)
		})

		document.querySelectorAll('[id="tableRemarks"]').forEach(element => {
			remarkList.push(element.value)
		})

		document.querySelectorAll('[id="tableID"]').forEach(element => {
			idlist.push(element.value)
		})

		forms.append('tables', JSON.stringify(tableList))
		forms.append('remarks', JSON.stringify(remarkList))
		forms.append('id', JSON.stringify(idlist))
		
		$.ajax({
			url: "th_setTable.php",
			data: forms,
			dataType: 'json',
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
			async: false,
			success: function(res){
				if(res.valid){
					console.log(res.msg)
					showalert('save_table', res.msg)
				} else {
					console.log(res.msg)
				}
				
			},
			error: function(res){
				console.log(res)
			}
		})
		console.log(forms)
	}

	function order_table(){
		$("<tr>").append(
			$("<td>").html("<input type='text' id='orderName' name='orderName[]' placeholder='Name of Order' class='input-sm' />"),
			$("<td>").html("<input type='text' id='orderRemarks' name='orderRemarks[]' placeholder='Remarks' class='input-sm' />"),
			$("<td>").html("<button type='button' onclick='removeRow.call(this)' class='btn btn-xs btn-danger'><i class='fa fa-trash'></i>&nbsp; delete</button>")
		).appendTo("#ordertable > tbody")
	}

	function save_order(){
		let forms = new FormData($("#orderfrm")[0]);
		const remarkList = []
		const orderList = []
		const orderIDs =[]

		document.querySelectorAll('[id="orderName"]').forEach(element => {
			orderList.push(element.value)
		})

		document.querySelectorAll('[id="orderRemarks"]').forEach(element => {
			remarkList.push(element.value)
		})

		document.querySelectorAll('[id="orderID"]').forEach(element => {
			orderIDs.push(element.value)
		})

		forms.append('order', JSON.stringify(orderList))
		forms.append('remarks', JSON.stringify(remarkList))
		forms.append('id', JSON.stringify(orderIDs))
		
		$.ajax({
			url: "th_setOrderType.php",
			data: forms,
			dataType: 'json',
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
			async: false,
			success: function(res){
				if(res.valid){
					console.log(res.msg)
					showalert('save_order', res.msg)
				} else {
					console.log(res.msg)
				}
			}
		})
	}

	//preview of image
  function imageIsLoaded(e) {
    $("#file").css("color","green");
    $('#image_preview').css("display", "block");
    $('#previewing').attr('src', e.target.result);
    $('#previewing').attr('width', '145px');
    $('#previewing').attr('height', '145px');
  };

	//add pr app level
	function addprlevel($lvl, $tbl){

		var xz = $("#atsections").val();
		var htmlITM = "<option value='ALL'> ALL </option>";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlITM = htmlITM + '<option value="' +this['ccode'] + '">' + this['cdesc'] + '</option>';
		});

		var xz = $("#atuserslst").val();
		var htmlUSERS = "";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlUSERS = htmlUSERS + '<option value="' +this['userid'] + '">' + this['name'] + '</option>';
		});

		var tbl = document.getElementById($tbl).getElementsByTagName('tr');
		var lastRow = tbl.length;

		var tblz = document.getElementById($tbl).getElementsByTagName('tbody')[0];
		var a=tblz.insertRow(tblz.rows.length);
							
		var u=a.insertCell(0);
			u.style.paddingTop = "2px";
			u.style.paddingLeft = "1px";
			u.style.paddingRight = "1px";
			u.style.width = "200px";
		var x=a.insertCell(1);
			x.style.paddingTop = "2px";
			x.style.paddingLeft = "1px";
			x.style.paddingRight = "1px";
		var za=a.insertCell(2);
			za.style.paddingTop = "2px";
			za.style.paddingLeft = "1px";
			za.style.paddingRight = "1px";
									
		u.innerHTML = "<select class=\"form-control input-xs\" name=\"selprsuser"+$lvl+""+lastRow+"\" id=\"selprsuser"+$lvl+""+lastRow+"\" > "+htmlUSERS+" </select>";
		x.innerHTML = "<select required multiple class=\"form-control input-xs\" name=\"selprsecs"+$lvl+""+lastRow+"[]\" id=\"selprsecs"+$lvl+""+lastRow+"\" >"+htmlITM+"</select>";
		za.innerHTML = "";

		$('#selprsuser'+$lvl+""+lastRow).select2({minimumResultsForSearch: Infinity,width: '100%'});
		$('#selprsecs'+$lvl+""+lastRow).select2({width: '100%'});

	}

	function chkprlvlform(){

		var lastRow2 = $("#PRAPP2 > tbody > tr").length;
		var lastRow3 = $("#PRAPP3 > tbody > tr").length;
  
		$("#tblPRLVL2count").val(lastRow2); 
		$("#tblPRLVL3count").val(lastRow3); 

		return true;

	}

	function prtransset(typ,id){

		$("#PRTransID").val(id);
		$("#PRTransTP").val(typ);

		$("#frmPRTrans").submit();
	}


	//add po app level
	function addpolevel($lvl, $tbl){

			var xz = $("#atitemtype").val();
			var htmlITM = "<option value='ALL'> ALL </option>";

			$.each(jQuery.parseJSON(xz), function() {  
				htmlITM = htmlITM + '<option value="' +this['ccode'] + '">' + this['cdesc'] + '</option>';
			});

			var xz = $("#atsupptype").val();
			var htmlSUPP = "<option value='ALL'> ALL</option>";

			$.each(jQuery.parseJSON(xz), function() {  
				htmlSUPP = htmlSUPP + '<option value="' +this['ccode'] + '">' + this['cdesc'] + '</option>';
			});

			var xz = $("#atuserslst").val();
			var htmlUSERS = "";

			$.each(jQuery.parseJSON(xz), function() {  
				htmlUSERS = htmlUSERS + '<option value="' +this['userid'] + '">' + this['name'] + '</option>';
			});

		var tbl = document.getElementById($tbl).getElementsByTagName('tr');
		var lastRow = tbl.length;

		var tblz = document.getElementById($tbl).getElementsByTagName('tbody')[0];
		var a=tblz.insertRow(tblz.rows.length);
								
		var u=a.insertCell(0);
			u.style.paddingTop = "2px";
			u.style.paddingLeft = "1px";
			u.style.paddingRight = "1px";
			u.style.width = "200px";
		var x=a.insertCell(1);
			x.style.paddingTop = "2px";
			x.style.paddingLeft = "1px";
			x.style.paddingRight = "1px";
		var y=a.insertCell(2);
			y.style.paddingTop = "2px";
			y.style.paddingLeft = "1px";
			y.style.paddingRight = "1px";
		var za=a.insertCell(3);
			za.style.paddingTop = "2px";
			za.style.paddingLeft = "1px";
			za.style.paddingRight = "1px";
									
		u.innerHTML = "<select class=\"form-control input-xs\" name=\"selposuser"+$lvl+""+lastRow+"\" id=\"selposuser"+$lvl+""+lastRow+"\" > "+htmlUSERS+" </select>";
		x.innerHTML = "<select required multiple class=\"form-control input-xs\" name=\"selpoitmtyp"+$lvl+""+lastRow+"[]\" id=\"selpoitmtyp"+$lvl+""+lastRow+"\" >"+htmlITM+"</select>";
		y.innerHTML = "<select required multiple class=\"form-control input-xs\" name=\"selposutyp"+$lvl+""+lastRow+"[]\" id=\"selposutyp"+$lvl+""+lastRow+"\" >"+htmlSUPP+"</select>";
		za.innerHTML = "";

		$('#selposuser'+$lvl+""+lastRow).select2({minimumResultsForSearch: Infinity,width: '100%'});
		$('#selpoitmtyp'+$lvl+""+lastRow).select2({width: '100%'});
		$('#selposutyp'+$lvl+""+lastRow).select2({width: '100%'});

	}

	function chkpolvlform(){
		var lastRow = $("#POAPP1 > tbody > tr").length;
		var lastRow2 = $("#POAPP2 > tbody > tr").length;
		var lastRow3 = $("#POAPP3 > tbody > tr").length;

		if(lastRow==0){

				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("<br><center>Atleast 1 approver is required in Level 1!</center><br>");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');


			return false;
		}else{
			$("#tbLVL1count").val(lastRow);  
			$("#tbLVL2count").val(lastRow2); 
			$("#tbLVL3count").val(lastRow3); 

			return true;

		}

	}

	function potransset(typ,id){
			
		$("#POTransID").val(id);
		$("#POTransTP").val(typ);

		$("#frmPOTrans").submit();
	}

	//add qo app level
	function addqolevel($lvl, $tbl){

		var xz = $("#atitemtype").val();
		var htmlITM = "<option value='ALL' selected> ALL </option>";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlITM = htmlITM + '<option value="' +this['ccode'] + '">' + this['cdesc'] + '</option>';
		});

		var xz = $("#atsupptype").val();
		var htmlSUPP = "<option value='ALL' selected> ALL</option>";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlSUPP = htmlSUPP + '<option value="' +this['ccode'] + '">' + this['cdesc'] + '</option>';
		});

		var xz = $("#atuserslst").val();
		var htmlUSERS = "";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlUSERS = htmlUSERS + '<option value="' +this['userid'] + '">' + this['name'] + '</option>';
		});

		var xz = $("#qotyplst").val();
		var htmlQRTYP = "<option value='ALL' selected> ALL</option>";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlQRTYP = htmlQRTYP + '<option value="' +this['ccode'] + '">' + this['cdesc'] + '</option>';
		});


		var tbl = document.getElementById($tbl).getElementsByTagName('tr');
		var lastRow = tbl.length;

		var tblz = document.getElementById($tbl).getElementsByTagName('tbody')[0];
		var a=tblz.insertRow(tblz.rows.length);
							
		var u=a.insertCell(0);
		u.style.paddingTop = "2px";
		u.style.paddingLeft = "1px";
		u.style.paddingRight = "1px";
		u.style.width = "200px";
		var x=a.insertCell(1);
		x.style.paddingTop = "2px";
		x.style.paddingLeft = "1px";
		x.style.paddingRight = "1px";
		var y=a.insertCell(2);
		y.style.paddingTop = "2px";
		y.style.paddingLeft = "1px";
		y.style.paddingRight = "1px";
		var z=a.insertCell(3);
		z.style.paddingTop = "2px";
		z.style.paddingLeft = "1px";
		z.style.paddingRight = "1px";
		var za=a.insertCell(4);
		za.style.paddingTop = "2px";
		za.style.paddingLeft = "1px";
		za.style.paddingRight = "1px";
								
		u.innerHTML = "<select class=\"form-control input-xs\" name=\"selqosuser"+$lvl+""+lastRow+"\" id=\"selqosuser"+$lvl+""+lastRow+"\" > "+htmlUSERS+" </select>";
		x.innerHTML = "<select required multiple class=\"form-control input-xs\" name=\"selqoitmtyp"+$lvl+""+lastRow+"[]\" id=\"selqoitmtyp"+$lvl+""+lastRow+"\" >"+htmlITM+"</select>";
		y.innerHTML = "<select required multiple class=\"form-control input-xs\" name=\"selqosutyp"+$lvl+""+lastRow+"[]\" id=\"selqosutyp"+$lvl+""+lastRow+"\" >"+htmlSUPP+"</select>";
		z.innerHTML = "<select required multiple class=\"form-control\" name=\"selqotrtyp"+$lvl+""+lastRow+"[]\" id=\"selqotrtyp"+$lvl+""+lastRow+"\" >"+htmlQRTYP+"</select>";
		za.innerHTML = "";

		$('#selqosuser'+$lvl+""+lastRow).select2({minimumResultsForSearch: Infinity,width: '100%'});
		$('#selqoitmtyp'+$lvl+""+lastRow).select2({width: '100%'});
		$('#selqosutyp'+$lvl+""+lastRow).select2({width: '100%'});
		$('#selqotrtyp'+$lvl+""+lastRow).select2({width: '100%'});

	}

	function chkqolvlform(){
		var lastRow = $("#QOAPP1 > tbody > tr").length;
		var lastRow2 = $("#QOAPP2 > tbody > tr").length;
		var lastRow3 = $("#QOAPP3 > tbody > tr").length;

		if(lastRow==0){

				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("<br><center>Atleast 1 approver is required in Level 1!</center><br>");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');


			return false;
		}else{
			$("#tbLQL1count").val(lastRow);     
			$("#tbLQL2count").val(lastRow2); 
			$("#tbLQL3count").val(lastRow3); 

			return true;

		}

	}

	function qotransset(typ,id){
			
		$("#QOTransID").val(id);
		$("#QOTransTP").val(typ);

		$("#frmQOTrans").submit();
	}

	//addrfp app level
	function addrfplevel($lvl, $tbl){

		var xz = $("#atuserslst").val();
		var htmlUSERS = "";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlUSERS = htmlUSERS + '<option value="' +this['userid'] + '">' + this['name'] + '</option>';
		});

		var tbl = document.getElementById($tbl).getElementsByTagName('tr');
		var lastRow = tbl.length;

		var tblz = document.getElementById($tbl).getElementsByTagName('tbody')[0];
		var a=tblz.insertRow(tblz.rows.length);
							
		var u=a.insertCell(0);
		u.style.paddingTop = "2px";
		u.style.paddingLeft = "1px";
		u.style.paddingRight = "1px";
		u.style.width = "200px";
		var za=a.insertCell(1);
		za.style.paddingTop = "2px";
		za.style.paddingLeft = "1px";
		za.style.paddingRight = "1px";
								
		u.innerHTML = "<select class=\"form-control input-xs\" name=\"selrfpsuser"+$lvl+""+lastRow+"\" id=\"selrfpsuser"+$lvl+""+lastRow+"\" > "+htmlUSERS+" </select>";
		za.innerHTML = "";

		$('#selrfpsuser'+$lvl+""+lastRow).select2({minimumResultsForSearch: Infinity,width: '100%'});
	} 

	function addpaylevel($lvl, $tbl){

		var xz = $("#atuserslst").val();
		var htmlUSERS = "";

		$.each(jQuery.parseJSON(xz), function() {  
			htmlUSERS = htmlUSERS + '<option value="' +this['userid'] + '">' + this['name'] + '</option>';
		});

		var tbl = document.getElementById($tbl).getElementsByTagName('tr');
		var lastRow = tbl.length;

		var tblz = document.getElementById($tbl).getElementsByTagName('tbody')[0];
		var a=tblz.insertRow(tblz.rows.length);
							
		var u=a.insertCell(0);
		u.style.paddingTop = "2px";
		u.style.paddingLeft = "1px";
		u.style.paddingRight = "1px";
		u.style.width = "200px";
		var za=a.insertCell(1);
		za.style.paddingTop = "2px";
		za.style.paddingLeft = "1px";
		za.style.paddingRight = "1px";
								
		u.innerHTML = "<select class=\"form-control input-xs\" name=\"selpaysuser"+$lvl+""+lastRow+"\" id=\"selpaysuser"+$lvl+""+lastRow+"\" > "+htmlUSERS+" </select>";
		za.innerHTML = "";

		$('#selpaysuser'+$lvl+""+lastRow).select2({minimumResultsForSearch: Infinity,width: '100%'});
	}

	function chkrfplvlform(){
		var lastRow = $("#RFPAPP1 > tbody > tr").length;
		var lastRow2 = $("#RFPAPP2 > tbody > tr").length;
		var lastRow3 = $("#RFPAPP3 > tbody > tr").length;

		if(lastRow==0){

				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("<br><center>Atleast 1 approver is required in Level 1!</center><br>");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');


			return false;
		}else{
			$("#tbLRFPL1count").val(lastRow);  
			$("#tbLRFPL2count").val(lastRow2); 
			$("#tbLRFPL3count").val(lastRow3); 

			return true;

		}

	}

	function chkpaylvlform(){
		var lastRow = $("#PAYAPP1 > tbody > tr").length;
		var lastRow2 = $("#PAYAPP2 > tbody > tr").length;
		var lastRow3 = $("#PAYAPP3 > tbody > tr").length;

		if(lastRow==0){

				$("#AlertMsg").html("");
				
				$("#AlertMsg").html("<br><center>Atleast 1 approver is required in Level 1!</center><br>");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');


			return false;
		}else{
			$("#tbLPAYL1count").val(lastRow);  
			$("#tbLPAYL2count").val(lastRow2); 
			$("#tbLPAYL3count").val(lastRow3); 

			return true;

		}

	}
	
	function rfptransset(typ,id){
	  
		$("#RFPTransID").val(id);
		$("#RFPTransTP").val(typ);
	
		$("#frmRFPTrans").submit();
	}
	
	function paytransset(typ,id){
	  
		$("#PAYTransID").val(id);
		$("#PAYTransTP").val(typ);
	
		$("#frmPAYTrans").submit();
	}


	function loadConDets(){
		$.ajax ({
			url: "th_loadcontctdet.php",
			dataType: 'json',
			async:false,
			success: function( result ) {

				console.log(result);
				$.each(result,function(index,item){

					if(item.cid!=""){
						if(item.cstat == "ACTIVE"){ 
							var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setCNTCTDETStat('"+item.cid+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						} else{
							var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setCNTCTDETStat('"+item.cid+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}

						var divhead = "<div class=\"cntctdetdetail col-xs-12 nopadwtop\" id=\""+item.cid+"\">";
																									
						var divdesc = "<div class=\"col-xs-4 nopadwleft\"><input type=\"text\" name=\"txtcntctdetdesc[]\" id=\"txtcntctdetdesc"+item.cid+"\" value=\""+item.cdesc+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /> <input type=\"hidden\" name=\"txtcntctdetid[]\" id=\"txtcntctdetid"+item.cid+"\" value=\""+item.cid+"\" /> </div>";												
																															
						var divstat = "<div class=\"col-xs-2 nopadwleft\">&nbsp;"+spanstat+"</div>";                                               
						var divend = "</div>";
								
						$("#TblCONTDET").append(divhead + divdesc + divstat + divend);
					}

				});

			}
		});
	}

	function loadAccountEntry(){
		$("#AccountEntry").typeahead({
			autoSelect: true,
			source: function(request, response){
				$.ajax({
					url: "th_AccountEntry.php",
					dataType: 'json',
					async: false,
					data: { account: $("#AccountEntry").val() },
					success: function(res){
						if(res.valid){
							response(res.data)
						} else {
							alert(res.msg)
						}
						
					},
					error: function(res){
						console.log(res)
					}
				})
			},
			displayText: function (item) {
                return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.cacctid + '</span><br><small>' + item.cacctdesc + "</small></div>";
            },
            highlighter: Object,
            afterSelect: function(items) { 
				$("#AccountEntry").val(items.cacctdesc).change()
				$("#AccountEntry").attr('data-val', items.cacctid).change()
				
			}
		})
	}
</script>