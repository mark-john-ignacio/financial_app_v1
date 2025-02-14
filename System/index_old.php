<?php

if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "System_Set";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


function listcurrencies(){ //API for currency list
	$apikey = $_SESSION['currapikey'];
  
	$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
	//$obj = json_decode($json, true);
  
	return $json;
}

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
   	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
   	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/DigiClock.css"> 
</head>

<body style="padding:5px">
	<fieldset>
    	<legend>System Setup</legend>

        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Company Details</a></li>
          <li><a data-toggle="tab" href="#param">Parameters</a></li>
          <li><a data-toggle="tab" href="#sales">Sales &amp; Delivery</a></li>
          <li><a data-toggle="tab" href="#purch">Purchases</a></li>
          <li><a data-toggle="tab" href="#acct">Accounting</a></li>
          <!--<li><a data-toggle="tab" href="#loan">Loan Mgr</a></li>-->
          <li><a data-toggle="tab" href="#rpts">Reports</a></li>
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
												<td width="180" rowspan="4" align="center">
													<?php 
														$imgsrc = "../images/COMPLOGO.png";
													?>
													<img src="<?php echo $imgsrc;?>" width="145" height="145" name="previewing" id="previewing">                       
												</td>
												<td width="200"><b>Registered Name:</b></td>
												<td style="padding:2px" colspan="3"><div class="col-xs-10"><input type="text" name="txtcompanycom" id="txtcompanycom" class="form-control input-sm" placeholder="Company Name..." maxlength="90"></div></td>
											</tr>											
											<tr>
												<td><b>Business/Trade Name:</b></td>
												<td style="padding:2px" colspan="3">
													<div class="col-xs-10">
														<input type="text" name="txtcompanydesc" id="txtcompanydesc" class="form-control input-sm" placeholder="Company Description..." maxlength="90" >
													</div>
												</td>
											</tr>
											<tr>
												<td><b>Address:</b></td>
												<td style="padding:2px" colspan="3">
													<div class="col-xs-10">
														<input type="text" name="txtcompanyadd" id="txtcompanyadd" class="form-control input-sm" placeholder="Address..." maxlength="90">
													</div>
												</td>
											</tr>
											<tr>                        
												<td><b>Tin No.:</b></td>
												<td style="padding:2px" colspan="3">
												<div class="col-xs-10">
													<input type="text" name="txtcompanytin" id="txtcompanytin" class="form-control input-sm" placeholder="TIN No..." maxlength="50">
												</div></td>
											</tr>
											<tr>
												<td align="center">
													<label class="btn btn-warning btn-xs">
														Browse Image&hellip; <input type="file" name="file" id="filecpmnid" style="display: none;">
													</label>
												</td>
												<td><b>Business Type:</b></td>
												<td style="padding:2px"  width="200px">
													<div class="col-xs-12">
														<select class="form-control input-xs" name="selcompanyvat" id="selcompanyvat">
														</select>
													</div>
												</td>
												<td  width="100px"><b>Email:</b></td>
												<td style="padding:2px">
													<div class="col-xs-8">
														<input type="text" name="txtemail" id="txtemail" class="form-control input-sm" placeholder="Email Address..." maxlength="50">
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

					<!-- SALES SETUP -->
						<div id="sales" class="tab-pane fade in">  

							<div class="col-xs-12">
								<div class="col-xs-2 nopadwtop">
									<b>Inventory Checking</b>
									<div id="divInvChecking" style="display:inline; padding-left:5px">
									</div>
								</div>                    
								<div class="col-xs-3 nopadwtop">
									<?php
										$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='INVPOST'"); 
						
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
							<div class="col-xs-12">
								<div class="col-xs-2 nopadwtop">
									<b>Customer's Credit Limit</b>
									<div id="divcCustLimit" style="display:inline; padding-left:5px">
									</div>
								</div>                    
								<div class="col-xs-3 nopadwtop">
									<?php
										$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='CRDLIMIT'"); 									
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
										$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='POSCLMT'"); 
							
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

									<div class="col-xs-12">
										<b>Default PrintOut Header</b>
										<div id="divQuotePrintHdr" style="display:inline; padding-left:5px"></div>
									</div>
									<div class="col-xs-12">
										<textarea rows="5" class="form-control input-sm" name="txtQuotePrintHdr" id="txtQuotePrintHdr">													
										</textarea>
									</div>
									<div class="col-xs-12" style="padding-top: 2px">
										<b>Default PrintOut Footer</b>
										<div id="divQuotePrintFtr" style="display:inline; padding-left:5px"></div>
									</div>
									<div class="col-xs-12">
										<textarea rows="5" class="form-control input-sm" name="txtQuotePrintFtr" id="txtQuotePrintFtr">
														
										</textarea>
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
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_SO'"); 
											
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
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='CRDLIMWAR'"); 
											
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
								
									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop2x">
											<b>Auto post upon printing</b>
											<div id="divPostDRprint" style="display:inline; padding-left:5px"></div>
										</div>
											
										<div class="col-xs-3 nopadwtop2x">
											<?php
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_DR'"); 
											
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
									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop2x">
											<b>Auto post upon printing</b>
											<div id="divcPostPOSprint" style="display:inline; padding-left:5px">
											</div>
										</div>                    
										<div class="col-xs-3 nopadwtop2x">
											<?php
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_POS'"); 
											
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
															
									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop2x">
											<b>Output Tax Acct Code</b>
											<div id="divcOutputTax" style="display:inline; padding-left:5px">
											</div>
										</div>                   
										<div class="col-xs-3 nopadwtop2x">
											<?php
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
											?>
											<div class="col-xs-3 nopadding">
												<input type="text" name="txtSales_vatid" id="txtSales_vatid" class="form-control input-sm" placeholder="Select Acct Code..." value="<?php echo $nvalueid;?>" readonly>
											</div>
											<div class="col-xs-9 nopadwleft">
											<input type="text" name="txtSales_vat" id="txtSales_vat" class="txtacctsel form-control input-sm" placeholder="Select Acct Code..." value="<?php echo $nvalue;?>">
											</div>	
										</div>
											
										<div class="col-xs-1 nopadwtop2x" id="msgsales_vat">
										</div>                    
									</div>              
								</div>
							
						</div>
					<!-- SALES SETUP END -->

					<!-- PURCHASES SETUP -->
						<div id="purch" class="tab-pane fade in">
							<p data-toggle="collapse" data-target="#itmpo"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Purchase Order</b></u></p>
							
								<div class="collapse" id="itmpo">  

									<div class="col-xs-12">
										<div class="col-xs-2 nopadwtop2x">
											<b>Auto post upon printing</b>
											<div id="divPostPOprint" style="display:inline; padding-left:5px"></div>
										</div>                    
										<div class="col-xs-3 nopadwtop2x">
										<?php
											$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_PO'"); 
										
											if (mysqli_num_rows($result)!=0) {
												$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
												$nvalue = $all_course_data['cvalue']; 												
											}
											else{
												$nvalue = "";
											}
										?>
										<select class="form-control input-sm selectpicker" name="selpoautopost" id="selpoautopost" onChange="setparamval('AUTO_POST_PO',this.value,'popostmsg')">
											<option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
											<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
										</select>
									</div>                   
									<div class="col-xs-1 nopadwtop2x" id="popostmsg">
									</div>
											
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
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_RR'"); 
											
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
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ALLOW_REF_RR'"); 										
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
												$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='AUTO_POST_PR'"); 										
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

					<!-- PARAMETERS SETUP --> 
						<div id="param" class="tab-pane fade in">
							<div class="col-xs-12">
								<div class="col-xs-2 nopadwtop">
									<b>Base Currency</b>
								</div>                    
								<div class="col-xs-3 nopadwtop">
									<select class="form-control input-sm" name="selbasecurr" id="selbasecurr" onChange="setparamval('DEF_CURRENCY',this.value,'basecurrchkmsg')">
								
										<?php
											$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_CURRENCY'"); 
									
											if (mysqli_num_rows($result)!=0) {
												$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);									
												$nvalue = $all_course_data['cvalue']; 										
											}
											else{
												$nvalue = "";
											}

											$objcurrs = listcurrencies();
											$objrows = json_decode($objcurrs, true);
											
											foreach($objrows['results'] as $rows){
										?>
										<option value="<?=$rows['id']?>" <?php if ($nvalue==$rows['id']) { echo "selected='true'"; } ?>><?=$rows['currencyName']?></option>
										<?php
											}
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
								
							<p data-toggle="collapse" data-target="#vatcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Customers Business Type</b></u></p>
									
								<div class="collapse" id="vatcodecollapse">
									<div class="col-xs-12 nopadwdown">   
										<div style="display:inline" class="col-xs-3">
											<button class="btn btn-xs btn-primary" name="btnaddvat" id="btnaddvat"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
											<button class="btn btn-xs btn-success" name="btnvat" id="btnvat"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Vat Exempt Codes</button>
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

									<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblVAT">
												
									</div>
									
								</div>
												
							<p data-toggle="collapse" data-target="#taxcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Items VAT Codes</b></u></p>

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
							
						</div> 
					<!-- PARAMETERS SETUP END -->
								
					<!-- ACCOUNTING SETUP -->
					<div id="acct" class="tab-pane fade in">
					
						<p data-toggle="collapse" data-target="#accdefcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Account Defaults</b></u> <i></i></p>
										
							<div class="collapse" id="accdefcollapse">   

								<div class="col-xs-12">                        
									<div style="display:inline" class="col-xs-3">
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
										
							<div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable">                     
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
                            
        </div>
  
          <!--<div id="loan" class="tab-pane fade in">
              
                <div class="col-xs-12">
                  <div class="col-xs-2 nopadwtop">
                    <b>Deduction Method</b>
                    <div id="divInvChecking" style="display:inline; padding-left:5px"></div>
                  </div>
                    
                  <div class="col-xs-3 nopadwtop">
                    <?php
        						// $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='LOANDED'"); 
        					
        						//  if (mysqli_num_rows($result)!=0) {
        						// $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        						 
        						//	 $nvalue = $all_course_data['cvalue']; 
        							
        						// }
        						// else{
        					//		 $nvalue = "";
        					//	 }

        					  ?>

                        <select class="form-control input-sm selectpicker" name="selloanded" id="selloanded" onChange="setparamval('LOANDED',this.value,'loandedchkmsg')">
                            <option value="Monthly" <?php// if ($nvalue=="Monthly") { echo "selected"; } ?>> Monthly </option>
                            <option value="Semi" <?php //if ($nvalue=="Semi") { echo "selected"; } ?>> Semi Monthly </option>
                        </select>

                  </div>
                    
                  <div class="col-xs-1 nopadwtop" id="loandedchkmsg">
                  </div>                   
				        </div>

              
              
                <p data-toggle="collapse" data-target="#loantypecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Loan Types</b></u></p>
                <div class="collapse" id="loantypecollapse">
                    <div class="col-xs-12 nopadwdown">   
                      <div style="display:inline" class="col-xs-3">
                        <button class="btn btn-xs btn-primary" name="btnaddtloan" id="btnaddtloan"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
                        <button class="btn btn-xs btn-success" name="btnloans" id="btnloans"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Loan Types</button>
   						        </div>
                        
   						        <div style="display:inline" class="col-xs-5"> 
                        <div class="alert alert-danger nopadding" id="LoanTypeAlertMsg">
                              
                        </div>
                        <div class="alert alert-success nopadding" id="LoanTypeAlertDone">
                              
                        </div>
                      </div>                 
 					          </div>
                	  <div class="col-xs-12 nopadding">
                        <div class="col-xs-2">
                           <b>Loan Type Code</b> 
                        </div>
                        
                        <div class="col-xs-4">
                          <b>Description</b>  
                        </div>
                          
                        <div class="col-xs-3">
                          <b>Status</b> 
                        </div>                      
                    </div>

                    <div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="Tblloantyp">
                      
                    </div>
                </div>     
                    
                <p data-toggle="collapse" data-target="#loantermcollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Loan Terms</b></u></p>
                <div class="collapse" id="loantermcollapse">
                  <div class="col-xs-12 nopadwdown">   
                    <div style="display:inline" class="col-xs-3">
                      <button class="btn btn-xs btn-primary" name="btnaddloanterm" id="btnaddloanterm"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
                      <button class="btn btn-xs btn-success" name="btnloantrm" id="btnloantrm"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Loan Term</button>
     						    </div>
                          
     						    <div style="display:inline" class="col-xs-5"> 
                      <div class="alert alert-danger nopadding" id="LoanTermAlertMsg">                              
                      </div>
                      <div class="alert alert-success nopadding" id="LoanTermAlertDone">
                      </div>
                    </div>                 
   				       	</div>

                  <div class="col-xs-12 nopadding">
                    <div class="col-xs-2">
                      <b>Loan Term Code</b> 
                    </div>                        
                    <div class="col-xs-4">
                      <b>Description</b>  
                    </div>                          
                    <div class="col-xs-3">
                      <b>Status</b> 
                    </div>                      
                  </div>

                  <div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="Tblloantrm">                     
                  </div>
                </div>     


                <p data-toggle="collapse" data-target="#intratecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Interest Rates</b></u></p>
                <div class="collapse" id="intratecollapse">
                    <div class="col-xs-12 nopadwdown">   
                      <div style="display:inline" class="col-xs-3">
                        <button class="btn btn-xs btn-primary" name="btnaddint" id="btnaddint"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
                        <button class="btn btn-xs btn-success" name="btnintrate" id="btnintrate"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Loan Term</button>
     						      </div>
                          
     						      <div style="display:inline" class="col-xs-5"> 
                        <div class="alert alert-danger nopadding" id="IntRateAlertMsg">
                                
                        </div>
                        <div class="alert alert-success nopadding" id="IntRateAlertDone">
                                
                        </div>
                      </div>                 
   					        </div>
                  	<div class="col-xs-12 nopadding">
                          <div class="col-xs-2">
                             <b>Rate Code</b> 
                          </div>
                          
                          <div class="col-xs-4">
                            <b>Description</b>  
                          </div>
                            
                          <div class="col-xs-3">
                            <b>Status</b> 
                          </div>                       
                    </div>

                    <div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="Tblintrate">
                        
                    </div>
                 </div>   

              </div>-->  
            
              <div id="rpts" class="tab-pane fade in">
                rpts
              </div> 
              
            </div>
            
     </fieldset>
</body>
</html>

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>


<script type="text/javascript">
$(document).ready(function(e) {
	loadcompany();
	
	loadgroups();
	
	loadewt();
	
	loadtax();
	
	loadvat();
	
	loadsemi();
	
	loadterms();
	
	loadloantyp();
	
	loadloantrm();
	
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

});

$(function() {              
      // Bootstrap DateTimePicker v4
	  	$('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
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
		var xy = 1;
			$.ajax ({
				url: "th_getlastewt.php",
				async: false,
				success: function( data ) {
					if(data!="False"){
						xy = parseInt(data) + 1;
						
					}
				}
			
			});
			
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
		var xy = 1;
			$.ajax ({
				url: "th_getlasttax.php",
				async: false,
				success: function( data ) {
					if(data!="False"){
						xy = parseInt(data) + 1;
						
					}
				}
			
			});
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
	
	
	$("#btntax").on("click", function() {
		var isOk = "YES";
		
		$('.taxdetail').each(function(i, obj) {
			
			divid = $(this).attr("id");
			varcode = $(this).find('input[name="txtctaxcode[]"]').val();
			vardesc = $(this).find('input[name="txtctaxdesc[]"]').val();
			varrate = $(this).find('input[name="txtctaxrate[]"]').val();
			

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
	
	
	$("#btnaddvat").on("click", function(){
		var xy = 1;
			$.ajax ({
				url: "th_getlastvat.php",
				async: false,
				success: function( data ) {
					if(data!="False"){
						xy = parseInt(data) + 1;
						
					}
				}
			
			});
						var divhead = "<div class=\"vatdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
						var divcode = "<div class=\"col-xs-1\"><input type=\"text\" name=\"txtcvatcode[]\" id=\"txtcvatcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Code...\" /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcvatdesc[]\" id=\"txtcvatdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" /></div>";
						var divrem = "<div class=\"col-xs-3\"><input type=\"text\" name=\"txtcvatrem[]\" id=\"txtcvatrem"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Remarks...\" /></div>";                                                 
						var divcomp = "<div class=\"col-xs-1\"><select class=\"form-control input-xs\" name=\"selcomp[]\" id=\"selcomp"+xy+"\" ><option value=\"1\">YES</option><option value=\"0\">NO</option></select></div>";                                                 
						var divstat = "<div class=\"col-xs-2\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
						var divend = "</div>";

						$("#TblVAT").append(divhead + divcode + divdesc + divrem + divcomp + divstat + divend);

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
		var tin = $("#txtcompanytin").val();
		var vatz = $("#selcompanyvat").val();
		var texthdr = $("#texthdr").val();
		
			$.ajax ({
				url: "th_savecompany.php",
				data: { nme: nme,  desc: desc, add: add, tin: tin, vatz: vatz, txthdr: texthdr },
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
	/*
	$("#btnloans").on("click", function() {  
		var isOk = "YES";

		$('.loanstypdetail').each(function(i, obj) {
			//alert("una");
			divid = $(this).attr("id");
			varcode = $(this).find('input[name="txtcloantypcode[]"]').val();
			//alert(varcode);
			vardesc = $(this).find('input[name="txtcloantypdesc[]"]').val();
			//alert(vardesc);

			$.ajax ({
				url: "th_saveterms.php",
				data: { code: varcode,  desc: vardesc, id:"LOANTYP", msg:"LOAN TYPE" },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						isOk = data;
					}
				}
			
			});
						
			
		});	
		
			if(isOk == "YES"){
				$("#Tblloantyp").html("");
				loadloantyp();
				
				$("#LoanTypeAlertDone").html("<b>SUCCESS: </b> Loan Type table successfully saved!");
				$("#LoanTypeAlertDone").show();

						$("#LoanTypeAlertMsg").html("");
						$("#LoanTypeAlertMsg").hide();
				
			}
			else{
				$("#LoanTypeAlertMsg").html("<b>Error Saving:</b>"+isOk);
				$("#LoanTypeAlertMsg").show();

						$("#LoanTypeAlertDone").html("");
						$("#LoanTypeAlertDone").hide();

			}
		
	});	
	
	
	$("#btnaddloanterm").on("click", function(){
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
						var divhead = "<div class=\"loanstrmdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
						var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtcloantrmcode[]\" id=\"txtcloantrmcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Code...\" maxlength=\"10\" /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcloantrmdesc[]\" id=\"txtcloantrmdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" maxlength=\"25\" /></div>";
						var divstat = "<div class=\"col-xs-3\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
						var divend = "</div>";

						$("#Tblloantrm").append(divhead + divcode + divdesc + divstat + divend);

							$("#txtcloantrmcode"+xy).on("keyup", function() {
								var valz = $(this).val();

								$.post('th_chktermcode.php',{q:valz,id:"LOANTRM" },function( data ){ //send value to post request in ajax to the php page
									if(data.trim()!="True"){ 
										$("#LoanTermAlertMsg").html("<b>Error: </b>"+ data);
										$("#LoanTermAlertMsg").show();

										$("#LoanTermAlertDone").html("");
										$("#LoanTermAlertDone").hide();
									}
									else {
										$("#LoanTermAlertMsg").html("");
										$("#LoanTermAlertMsg").hide();

										$("#LoanTermAlertDone").html("");
										$("#LoanTermAlertDone").hide();
									}
								});
							});
							$("#txtcloantrmcode"+xy).on("blur", function() {
								 if ($("#LoanTermAlertMsg").text().length > 0) {
									 
									 $("#txtcloantrmcode"+xy).val("").change();
									 $("#txtcloantrmcode"+xy).focus();
									 
								 }
							});
		
	});
	
	$("#btnloantrm").on("click", function() {  
		var isOk = "YES";

		$('.loanstrmdetail').each(function(i, obj) {
			//alert("una");
			divid = $(this).attr("id");
			varcode = $(this).find('input[name="txtcloantrmcode[]"]').val();
			//alert(varcode);
			vardesc = $(this).find('input[name="txtcloantrmdesc[]"]').val();
			//alert(vardesc);

			$.ajax ({
				url: "th_saveterms.php",
				data: { code: varcode,  desc: vardesc, id:"LOANTRM", msg:"LOAN TERMS" },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						isOk = data;
					}
				}
			
			});
						
			
		});	
		
			if(isOk == "YES"){
				$("#Tblloantrm").html("");
				loadloantrm();
				
				$("#LoanTermAlertDone").html("<b>SUCCESS: </b> Loan Term table successfully saved!");
				$("#LoanTermAlertDone").show();

						$("#LoanTermAlertMsg").html("");
						$("#LoanTermAlertMsg").hide();
				
			}
			else{
				$("#LoanTermAlertMsg").html("<b>Error Saving:</b>"+isOk);
				$("#LoanTermAlertMsg").show();

						$("#LoanTermAlertDone").html("");
						$("#LoanTermAlertDone").hide();

			}
		
	});	
	*/

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
					$("#txtcompanytin").val(item.ctin);

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
			
						var divhead = "<div class=\"vatdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
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
						var divcode = "<div class=\"col-xs-1\"><input type=\"text\" name=\"txtcvatcode[]\" id=\"txtcvatcode"+item.nident+"\" value=\""+item.cvatcode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcvatdesc[]\" id=\"txtcvatdesc"+item.nident+"\" value=\""+item.cvatdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
						var divrem = "<div class=\"col-xs-3\"><input type=\"text\" name=\"txtcvatrem[]\" id=\"txtcvatrem"+item.nident+"\" value=\""+item.nrem+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Remarks...\" /></div>"; 
						var divcomp = "<div class=\"col-xs-1\"><select class=\"form-control input-xs\" name=\"selcomp[]\" id=\"selcomp"+item.nident+"\"><option value=\"1\" "+isYes+">YES</option><option value=\"0\" "+isNo+">NO</option></select></div>";                                                 
						var divstat = "<div class=\"col-xs-2\">&nbsp;"+spanstat+"</div>";                                               
						var divend = "</div>";
						
							
						$("#TblVAT").append(divhead + divcode + divdesc + divrem + divcomp + divstat + divend);
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
						v.innerHTML = "<input type=\"text\" name=\"txtcdesc[]\" id=\"txtcdesc"+lastRow+"\" value=\""+item.cdesc+"\" data-citmno=\""+item.nidentity+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" />";
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

//preview of image
  function imageIsLoaded(e) {
    $("#file").css("color","green");
    $('#image_preview').css("display", "block");
    $('#previewing').attr('src', e.target.result);
    $('#previewing').attr('width', '145px');
    $('#previewing').attr('height', '145px');
  };

</script>