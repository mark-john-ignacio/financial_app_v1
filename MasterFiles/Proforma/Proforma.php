<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Proforma";

	include('../../Connection/connection_string.php');
	include('../../include/accessinner.php');

	$company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];
	
	$posnew = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Proforma_New'");
	if(mysqli_num_rows($sql) == 0){
		$posnew = "False";
	}

	$posedit = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Proforma_Edit'");
	if(mysqli_num_rows($sql) == 0){
		$posedit = "False";
	}

	@$arrtaxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype = 'Purchase' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => $row['nrate']); 
		}
	}

	@$arrewtlist = array();
	$getewt = mysqli_query($con,"SELECT * FROM `wtaxcodes` WHERE compcode='$company'"); 
	if (mysqli_num_rows($getewt)!=0) {
		while($rows = mysqli_fetch_array($getewt, MYSQLI_ASSOC)){
			@$arrewtlist[] = array('ctaxcode' => $rows['ctaxcode'], 'nrate' => $rows['nrate']); 
		}
	}

?> 
<!DOCTYPE html>
	<html>
	<head>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">  
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

	<link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-datepicker/css/datepicker3.css"/>

	<script src="../../Bootstrap/js/moment.js"></script>
	<script type="text/javascript" src="../../global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="../../global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>

	<style>
		h4 {
			width: 100%; 
			text-align: left; 
			border-bottom: 1px solid #eee; 
			line-height: 0.1em;
			margin: 10px 0 20px; 
		} 

		h4 span { 
			background:#fff; 
			padding:0 10px; 
		}
	</style>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>

			<div>
				<div style="float:left; width:50%">
					<font size="+2"><u>A/P Proforma List</u></font>	
				</div>            
			</div>

			<br><br>

      		<button type="button" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            
       		<br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="20">&nbsp;</th>
						<th>Description</th>
						<th width="200">Last Modified</th>
						<th width="80">Status</th>
					</tr>
				</thead>

				<tbody>
					<?php
						$company = $_SESSION['companyid'];

						$sqlfreq = "Select * from proforma_ap_freq where compcode='$company'";
						$resfreq=mysqli_query($con,$sqlfreq);
						@$arrayallfreq = array();
						while($row = mysqli_fetch_array($resfreq, MYSQLI_ASSOC))
						{
							@$arrayallfreq[] = $row;
						}

						$sql = "select A.*, B.cacctdesc as CRDesc, C.cacctdesc as DRDesc from proforma_ap A left join accounts B on A.compcode=B.compcode and A.cacctcodecr=B.cacctid left join accounts C on A.compcode=C.compcode and A.cacctcodedr=C.cacctid where A.compcode='$company' order by A.ddatemodified DESC";	 		
						$result=mysqli_query($con,$sql);
								
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
						@$arrayall = array();
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
							@$arrayall[] = $row;
					?>
						<tr>
							<td align="center"> 
								<button id="popoverData2" onClick="editgrp('<?=$row['nidentity']?>')" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></button>					
							</td>
							<td> <?php echo $row['cdescription'];?> </td>
							<td> <?=date_format(date_create($row['ddatemodified']), "F d, Y H:i:s");?> </td>
							<td align="center">
								<div id="itmstat<?php echo $row['nidentity'];?>">
								<?php 
									if($row['cstatus']=="ACTIVE"){
										echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['nidentity'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
									}
									else{
										echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['nidentity'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
									}
								?>
								</div>
							</td>
						</tr>
					<?php 
						}				
					?>
										
				</tbody>
			</table>

			<input type="hidden" id="posnew" value="">
			<input type="hidden" id="posedit" value="<?=$posedit;?>">

		</section>
	</div>		


	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<form name="frmPro" id="frmPro" action="th_save.php">

					<div class="modal-header">
						<h5 class="modal-title" id="myModalLabel"><b>Add New Proforma</b></h5>        
					</div>

					<div class="modal-body" style="height: 60vh; overflow-y: auto">
					
						<div class="row">
							<div class="cgroup col-xs-2">
								<b>Description</b>
							</div>
							
							<div class="col-xs-10">
								<input type="hidden" id="txtid" name="txtid">
								<input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" placeholder="Description.." required tabindex="1">
							</div>
						</div> 

						<div class="row" style="margin-top: 3px !important">
							<div class="cgroup col-xs-2"> 
								<b>Expense Acct (Dr)</b>
							</div>

							<div class="col-xs-4">
								<input type="text" class="cacctdesc form-control input-sm" id="txtdracct" name="txtdracct" placeholder="Expense Acct.." required tabindex="2">

								<input type="hidden" id="txtdracctid" name="txtdracctid">
							</div>

							<div class="cgroup col-xs-2"> 
								<div style="padding-left: 10px"><b>Liability Acct (Cr)</b></div>
							</div>
							
							<div class="col-xs-4">
								<input type="text" class="cacctdesc form-control input-sm" id="txtcracct" name="txtcracct" placeholder="Liability Acct.." required tabindex="3">

								<input type="hidden" id="txtcracctid" name="txtcracctid">
							</div>							

						</div>
							
						<div class="row" style="margin-top: 3px !important">
							<div class="cgroup col-xs-2"> 
								<b>VAT Code (Dr)</b>
							</div>
							
							<div class="col-xs-4">
								<select id="txtvatcode" name="txtvatcode" class="form-control input-sm selectpicker" tabindex="4">
									<?php
										foreach(@$arrtaxlist as $rows){
											echo "<option value=\"".$rows['ctaxcode']."\" data-rate=\"".$rows['nrate']."\">".$rows['ctaxcode'].": ".number_format($rows['nrate'])."%</option>";
										}
									?>
										
								</select>
								<input type="hidden" id="txtvatcoderate" name="txtvatcoderate">
							</div>

							<div class="cgroup col-xs-2"> 
								<div style="padding-left: 10px"><b>EWT Acct (Cr)</b></div> 
							</div>

							<div class="col-xs-4">
								<select id="txtewtcode" name="txtewtcode[]" class="form-control input-sm selectpicker"  tabindex="3" multiple tabindex="5">
									<?php
										foreach(@$arrewtlist as $rows){
											echo "<option value=\"".$rows['ctaxcode']."\" data-rate=\"".$rows['nrate']."\">".$rows['ctaxcode'].": ".number_format($rows['nrate'])."%</option>";
										}
									?>
										
								</select>
								<input type="hidden" id="txtewtcoderate" name="txtewtcoderate"> 
							</div>

						</div>

						<div style="padding-top: 20px"> &nbsp;</div>
						<h4><span>Frequency</span></h4>

						<div class="row nopadwtop" style="margin-top: 3px !important">
							<div class="col-xs-2">
								<select id="selfreq" name="selfreq" class="form-control input-sm selectpicker" tabindex="6">

									<option value="Monthly">Monthly</option>
									<option value="Quarterly">Quarterly</option>
									<option value="Semi">Semi Annual</option>
									<option value="Annual">Annual</option> 
										
								</select>
							</div>

							<div class="col-xs-1 nopadwtop text-right">
								<b> Every: </b>			
							</div>

							<div class="evrydiv col-xs-2" id="tbMonthly"> 
								<select id="selmont" name="selmont" class="selopfreq form-control input-sm selectpicker" tabindex="7">
									<?php
										for($i=1; $i<=31; $i++){
											echo "<option value=\"".$i."\"> ".$i." </option>";
										}
									?>
								</select>
							</div>						

							<div class="evrydiv col-xs-5" id="tbQuarterly">
								<div class="row nopadwtop">
									<div class="col-xs-1 nopadwright text-right">
										<b> Q1: </b>			
									</div>	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq1" name="selfreqq1" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=3; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq1d" name="selfreqq1d" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>

								<div class="row nopadwtop">
									<div class="col-xs-1 nopadwright text-right">
										<b> Q2: </b>			
									</div>	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq2" name="selfreqq2" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=4; $i<=6; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq2d" name="selfreqq2d" class="form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>

								<div class="row nopadwtop">
									<div class="col-xs-1 nopadwright text-right">
										<b> Q3: </b>			
									</div>	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq3" name="selfreqq3" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=7; $i<=9; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq3d" name="selfreqq3d" class="form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>

								<div class="row nopadwtop">
									<div class="col-xs-1 nopadwright text-right">
										<b> Q4: </b>			
									</div>	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq4" name="selfreqq4" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=10; $i<=12; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqq4d" name="selfreqq4d" class="form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>
								
							</div>	

							<div class="evrydiv col-xs-5" id="tbSemi">
								<div class="row nopadwtop">
									<div class="col-xs-1 nopadwright text-right">
										<b> S1: </b>			
									</div>	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqs1" name="selfreqs1" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=6; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqs1d" name="selfreqs1d" class="form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>

								<div class="row nopadwtop">
									<div class="col-xs-1 nopadwright text-right">
										<b> S2: </b>			
									</div>	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqs2" name="selfreqs2" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=7; $i<=12; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqs2d" name="selfreqs2d" class="form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>
							</div>

							<div class="evrydiv col-xs-5" id="tbAnnual"> 
								<div class="row nopadwtop">	
									<div class="col-xs-10">
										<div class="row nopadwleft">
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqannm" name="selfreqannm" class="selopfreq form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=12; $i++){
															$monthNum  = $i;
															$dateObj   = DateTime::createFromFormat('!m', $monthNum);
															$monthName = $dateObj->format('F'); // March

															echo "<option value=\"".$i."\"> ".$monthName." </option>";
														}
													?>
												</select>			
											</div>
											<div class="col-xs-5 nopadwright text-right">
												<select id="selfreqannmd" name="selfreqannmd" class="form-control input-sm selectpicker" tabindex="7">
													<?php
														for($i=1; $i<=31; $i++){
															echo "<option value=\"".$i."\"> ".$i." </option>";
														}
													?>
												</select>			
											</div>
										</div>		
									</div>
								</div>
							</div>	

						</div>

						<div style="padding-top: 20px"> &nbsp;</div>
						<h4><span>Set Fix Amount</span></h4>
						<div class="col-xs-12">
							<div class="cgroup col-xs-2 nopadwtop2x">
								<b>Total Amount</b>
							</div>
							
							<div class="col-xs-3 nopadwtop">
								<input type="text" class="nnumeric form-control input-sm text-right" id="txtngross" name="txtngross" placeholder="0.00" value="0.00" tabindex="6">
							</div>
						</div>
						<div class="col-xs-12">
							<div class="cgroup col-xs-2 nopadwtop2x">
								<b>Net Gross</b>
							</div>
							
							<div class="col-xs-3 nopadwtop">
								<input type="text" class="nnumeric form-control input-sm text-right" id="txtnnet" name="txtnnet" placeholder="0.00" value="0.00" readonly tabindex="7">
							</div>
						</div>
						<div class="col-xs-12">
							<div class="cgroup col-xs-2 nopadwtop2x">
								<b>Total VAT</b>
							</div>
							
							<div class="col-xs-3 nopadwtop">
								<input type="text" class="nnumeric form-control input-sm text-right" id="txtnvat" name="txtnvat" placeholder="0.00" value="0.00" readonly tabindex="8">
							</div>
						</div>
						<div class="col-xs-12">
							<div class="cgroup col-xs-2 nopadwtop2x">
								<b>Total EWT</b>
							</div>
							
							<div class="col-xs-3 nopadwtop">
								<input type="text" class="nnumeric form-control input-sm text-right" id="txtnewt" name="txtnewt" placeholder="0.00" value="0.00" readonly tabindex="9">
							</div>
						</div>
													
						<div class="alert alert-danger" id="add_err"></div>         

					</div>
					
					<div class="modal-footer">
						<button type="submit" id="btnbtn" name="btnbtn" class="btn btn-primary btn-sm">Save</button>
						<button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Modal -->		

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-center">
				<div class="modal-content">
					<div class="alert-modal-danger">
						<p id="AlertMsg"></p>
						<p>
							<center>
								<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Ok</button>
							</center>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
mysqli_close($con);
?>
</body>
</html>

  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	
		$('body').on('focus',".cacctdesc", function(){
			var $input = $(':focus').attr("name");

			$("#"+$input).typeahead({
				items: 10,
				source: function(request, response) {
					$.ajax({
						url: "../th_accounts.php",
						dataType: "json",
						data: {
							query: $("#"+$input).val()
						},
						success: function (data) {
							console.log(data);
							response(data);
						}
					});
				},
				autoSelect: true,
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + '</small></div>';
				},
				highlighter: Object,
				afterSelect: function(item) { 

					$('#'+$input).val(item.name).change(); 
					$("#"+$input+"id").val(item.id);

				}
			});

		});

		$(function(){
			$("#add_err").hide();
			$(".itmalert").hide();

			$('#example').DataTable();

			$(".nnumeric").autoNumeric('init',{mDec:2});
			$(".nnumeric").on("focus", function(){
				this.select();
			});

			$(".nnumeric").on("keyup", function(){
				compvat();
			});

			
			$('#txtAnnualDte').datepicker({
				format: 'mm/dd/yyyy',
				autoclose: true
			});
			
			// Adding new user
			$("#btnadd").on("click", function() {
				var $xyz = chkAccess('Proforma_New');

				if($xyz.trim()=="True"){
					$("#btnbtn").html('Save');
								
					$("#txtid").val("new");    
					$("#txtcdesc").val("");	    
					$("#txtcracct").val("");
					$("#txtdracct").val("");

					$("#txtvatcode").val(null).trigger("change"); 
					$("#txtewtcode").val(null).trigger("change"); 

					$("#txtvatcoderate").val("");
					$("#txtewtcoderate").val("");

					$("#selfreq").val("Monthly").trigger("change");
					$(".selopfreq").attr("required", false);

					$("#selmont").attr("required", true);

					$("#txtngross").val(0.00);
					$("#txtnnet").val(0.00);
					$("#txtnvat").val(0.00);
					$("#txtnewt").val(0.00);
					
					$('#myModalLabel').html("<b>Add New A/P Proforma</b>");
					$('#myModal').modal('show');
				} else {
					$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
					$("#AlertModal").modal('show');

				}
			});
			
			$("#txtewtcode").select2({width: "100%"});

			$("#selfreq").on("change", function(){
				var x = $(this).val();

				$(".evrydiv").hide();
				$("#tb"+x).show();
				$(".selopfreq").attr("required", false);			
						
			});

			$("#txtewtcode, #txtvatcode").on("change", function(){   
				compvat();
			});

			$("#frmPro").on("submit", function(){ 
				var $arrvatrates = [];
				$("#txtvatcode > option:selected").each(function() {
					$arrvatrates.push($(this).data("rate"));
				});

				var $arrewtrates = [];
				$("#txtewtcode > option:selected").each(function() {
					$arrewtrates.push($(this).data("rate"));
				});

				$txtvatr = "";
				if($arrvatrates.length>=1){
					$txtvatr = $arrvatrates.join(",");
				}

				$txtewtr = "";
				if($arrewtrates.length>=1){
					$txtewtr = $arrewtrates.join(",");
				}

				$("#txtvatcoderate").val($txtvatr);
				$("#txtewtcoderate").val($txtewtr);


				$("#frmPro").submit();
			});
						
		});		

		$(document).keydown(function(e) {			 
			if(e.keyCode == 112) { //F1
				e.preventDefault();
				$("#btnadd").click();
			}
		});

	function editgrp(id){
		var x = chkAccess('Proforma_Edit');
		
		if(x.trim()=="True"){
			$("#btnbtn").html('Update');

			$xc = '<?=json_encode(@$arrayall);?>';
			$.each(jQuery.parseJSON($xc), function() { 
				if(this['nidentity']==id){
					$("#txtid").val(this['nidentity']);    
					$("#txtcdesc").val(this['cdescription']);	    
					$("#txtcracctid").val(this['cacctcodecr']);
					$("#txtdracctid").val(this['cacctcodedr']);

					$("#txtcracct").val(this['CRDesc']);
					$("#txtdracct").val(this['DRDesc']); 		 		

					$("#txtvatcode").val(null).trigger("change"); 
					$("#txtvatcode").val(this['cvatcode']).change();

					$("#txtewtcode").val(null).trigger("change"); 
					str = this['cewtcode'];
					const xarray2 = str.split(',');
					$("#txtewtcode").val(xarray2).change();  
					
					$("#txtvatcoderate").val(this['cvatrate']);
					$("#txtewtcoderate").val(this['cewtrate']);

					$("#txtngross").autoNumeric('set',this['ngross']);

					$("#txtnnet").val(this['nnetgross']);
					$("#txtnvat").val(this['nvatamt']);
					$("#txtnewt").val(this['newtamt']);

					$("#selfreq").val(this['cfrequency']).trigger("change");

					xcfresel = this['cfrequency'];
					$xcz = '<?=json_encode(@$arrayallfreq);?>';
					$.each(jQuery.parseJSON($xcz), function() { 
						if(this['proforma_ap_id']==id){

							if(xcfresel=="Monthly"){
								$("#selmont").val(this['nmonthly']); 
							}else if(xcfresel=="Quarterly"){
								str = this['cq1'];
								xq1del = str.split("/");

								str = this['cq2'];
								xq2del = str.split("/");

								str = this['cq3'];
								xq3del = str.split("/");

								str = this['cq4'];
								xq4del = str.split("/");

								$("#selfreqq1").val(xq1del[0]); 
								$("#selfreqq2").val(xq2del[0]); 
								$("#selfreqq3").val(xq3del[0]); 
								$("#selfreqq4").val(xq4del[0]); 

								$("#selfreqq1d").val(xq1del[1]); 
								$("#selfreqq2d").val(xq2del[1]); 
								$("#selfreqq3d").val(xq3del[1]); 
								$("#selfreqq4d").val(xq4del[1]);

							}else if(xcfresel=="Semi"){
								str = this['cs1'];
								xs1del = str.split("/");

								str = this['cs2'];
								xs2del = str.split("/");

								$("#selfreqs1").val(xs1del[0]); 
								$("#selfreqs2").val(xs2del[0]); 

								$("#selfreqs1d").val(xs1del[1]); 
								$("#selfreqs2d").val(xs2del[1]); 

							}else if(xcfresel=="Annual"){
								str = this['dannual'];
								xanndel = str.split("/");

								$("#selfreqannm").val(xanndel[0]); 
								$("#selfreqannmd").val(xanndel[1]); 
							}

						}

					});
				}
			});
			
			$("#add_err").html("");		

			$(".nnumeric").autoNumeric('destroy');
			$(".nnumeric").autoNumeric('init',{mDec:2});
			
			$('#myModalLabel').html("<b>Update A/P Proforma Details</b>");
			$('#myModal').modal('show');
		} else {
			$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			$("#AlertModal").modal('show');

		}

	}

	function setStat(code, stat){
		var x = chkAccess('Proforma_Edit');
		
		if(x.trim()=="True"){
			$.ajax ({
				url: "th_setstat.php",
				data: { code: code,  stat: stat },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						$("#itm"+code).html("<b>Error: </b>"+ data);
						$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+code).show();
					}
					else{
						if(stat=="ACTIVE"){
						$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
						}else{
							$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
						}
						
						$("#itm"+code).html("<br><b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).show();

					}
				}
			
			});
		} else {
			$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			$("#AlertModal").modal('show');

		}
	}

	function chkAccess(id){
		var result;
		
		$.ajax ({
			url: "../../MasterFiles/Items/chkAccess.php",
			data: { id: id },
			async: false,
			success: function( data ) {
					result = data;
			}
		});
		
		return result;
	}

	function compvat(){

		$nnet = 0;
		$nvats =0;
		$newt = 0;

		$tonrate = 0;
		$("#txtvatcode > option:selected").each(function() {
			$tonrate = parseFloat($(this).data("rate"));
		});

		if(parseFloat($tonrate) > 0){
			$nnet = parseFloat($("#txtngross").val().replace(/,/g,'')) / (1+($tonrate/100));
			$nvats = $nnet * ($tonrate/100);
		}else{
			$nnet = $("#txtngross").val().replace(/,/g,'');
		}

		$tonewtrate = 0;
		$("#txtewtcode > option:selected").each(function() {
			$tonewtrate = $tonewtrate + parseFloat($(this).data("rate"));
		});

		$newt = $nnet * ($tonewtrate/100);

		$("#txtnnet").val($nnet);
		$("#txtnnet").autoNumeric('destroy');
		$("#txtnnet").autoNumeric('init',{mDec:2});

		$("#txtnvat").val($nvats);
		$("#txtnvat").autoNumeric('destroy');
		$("#txtnvat").autoNumeric('init',{mDec:2}); 

		$("#txtnewt").val($newt);
		$("#txtnewt").autoNumeric('destroy');
		$("#txtnewt").autoNumeric('init',{mDec:2});
	}

	</script>
