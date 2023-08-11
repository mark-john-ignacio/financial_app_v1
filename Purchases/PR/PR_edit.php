<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PR_edit.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	if(isset($_REQUEST['txtctranno'])){
		$cprno = $_REQUEST['txtctranno'];
	}
	else{
		$cprno = $_REQUEST['txtcprno'];
	}

	$id = $_SESSION['employeeid'];

	$arrseclist = array();
	$sqlempsec = mysqli_query($con,"select A.section_nid as nid, B.cdesc from users_sections A left join locations B on A.section_nid=B.nid where A.UserID='$employeeid' and B.cstatus='ACTIVE' Order By B.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrseclist[] = $row0['nid'];
	}

	if(isset($_REQUEST['cwh'])){
		$arrseclist = array();
		$arrseclist[0] = $_REQUEST['cwh'];
	}else{
		if(count($arrseclist)==0){
			$arrseclist[] = 0;
		}
	}

	$sqlhead = mysqli_query($con,"Select A.*, B.Minit, B.Fname, B.Lname from purchrequest A left join users B on A.cpreparedby=B.Userid Where A.compcode='$company' and A.ctranno='$cprno'");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$cpreparedBy = $row['cpreparedby'];

		$mi = ($row['Minit']!="") ? " ".$row['Minit'] : "";
    $cpreparedName =  $row['Lname'] . ", ". $row['Fname'] . $mi;

		$cSecID = $row['locations_id'];
		$cRemarks = $row['cremarks'];
		$dDueDate = date_format(date_create($row['dneeded']), "m/d/Y");

		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
?>
	<form action="PR_editsave.php" name="frmpos" id="frmpos" method="post">
		<fieldset>
    	<legend>Purchase Request</legend>	

        <table width="100%" border="0">
					<tr>
								<tH>PR No.:</tH>
								<td colspan="2" style="padding:2px">
									<div class="col-xs-3 nopadding">
										<input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cprno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
									</div>     
									<input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cpono;?>">
									<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
									<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
									&nbsp;&nbsp;
									<div id="statmsgz" style="display:inline"></div>
								</td>
								<td style="padding:2px" align="center">
									<div id="salesstat">
										<?php
											if($lCancelled==1){
												echo "<font color='#FF0000'><b>CANCELLED</b></font>";
											}
											
											if($lPosted==1){
												echo "<font color='#FF0000'><b>POSTED</b></font>";
											}
										?>
									</div>
								</td>
							</tr>

					<tr>
						<tH width="100">Requested By:</tH>
						<td style="padding:2px">
							<div class="col-xs-12 nopadding">
								<div class="col-xs-11 nopadding">
									<input type="hidden" id="txtcustid" name="txtcustid" value="<?=$cpreparedBy?>">
									<?=$cpreparedName?>
								</div>
							</div>
						</td>
						<tH width="150" style="padding:2px">Date Needed:</tH>
						<td style="padding:2px" width="200">
							<div class="col-xs-8 nopadding">
								<input type='text' class="form-control input-sm" id="date_needed" name="date_needed" value="<?=$dDueDate; ?>"/>
							</div>
						</td>
					</tr>
					<tr>
						<tH width="100">Section:</tH>
						<td style="padding:2px">
							<div class="col-xs-5 nopadding">
							<select class="form-control input-sm" name="selwhfrom" id="selwhfrom"> 
								<?php
										foreach($rowdetloc as $localocs){									
								?>
											<option value="<?php echo $localocs['nid'];?>" <?=($cSecID==$localocs['nid']) ? "selected" : "";?>><?php echo $localocs['cdesc'];?></option>										
								<?php	
										}						
								?>
							</select>
							</div>
						</td>
						<tH width="150">&nbsp;</tH>
						<td style="padding:2px;">
						&nbsp;
						</td>
					</tr>
					<tr>
						<tH width="100">Remarks:</tH>
						<td style="padding:2px">
							<div class="col-xs-11 nopadding">
								<textarea class="form-control input-sm" id="txtremarks" name="txtremarks"><?=$cRemarks?></textarea>
							</div>
						</td>
						<tH width="150">&nbsp;</tH>
						<td style="padding:2px;">
						&nbsp;
						</td>
					</tr>

					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
				</table>

				<div class="col-xs-12 nopadwdown">
					<input type="hidden" name="hdnunit" id="hdnunit">
							
					<div class="col-xs-3 nopadding">
						<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Item/SKU Code..." tabindex="4">
					</div>
					<div class="col-xs-5 nopadwleft">
						<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5">
					</div>
				</div>  

				<div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 300px;
					text-align: left;
					overflow: auto">
				
					<table id="MyTable" class="MyTable" width="100%">
						<thead>	
							<tr>
								<th style="border-bottom:1px solid #999">Code</th>
								<th style="border-bottom:1px solid #999">Description</th>
								<th style="border-bottom:1px solid #999">UOM</th>
								<th style="border-bottom:1px solid #999">Qty</th>
								<th style="border-bottom:1px solid #999">Remarks</th>
								<th style="border-bottom:1px solid #999">&nbsp;</th>
							</tr>
						</thead>
						<tbody class="tbody">
							<?php 
								$sqlbody = mysqli_query($con,"select a.*, B.citemdesc from purchrequest_t a left join items b on A.compcode=B.compcode and A.citemno=b.cpartno where a.compcode = '$company' and a.ctranno = '$cprno'");

								if (mysqli_num_rows($sqlbody)!=0) {
									$cntr = 0;
									while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
									$cntr = $cntr + 1;
							?>
								<tr>
									<td width='120px'><input type='hidden' value='<?=$rowbody['citemno']?>' name="txtitemcode" id="txtitemcode"><?=$rowbody['citemno']?></td>
									<td width='120px' nowrap style='padding:1px;overflow: hidden;text-overflow: ellipsis;'><?=$rowbody['citemdesc']?></td>
									<td width='80px' style='padding:1px'></td>
									<td width='100px' style='padding:1px'>
										<input type='text' value='<?=$rowbody['nqty']?>' class='numeric form-control input-xs' style='text-align:right' name="txtnqty" id="txtnqty<?=$cntr?>" autocomplete='off' onFocus='this.select();' /> 
										<input type='hidden' value='<?=$rowbody['cmainunit']?>' name='hdnmainuom' id='hdnmainuom<?=$cntr?>'> 
										<input type='hidden' value='<?=$rowbody['nfactor']?>' name='hdnfactor' id='hdnfactor<?=$cntr?>'>
									</td>
									<td width='200px' style='padding:1px'><input type='text' class='form-control input-xs' id='dremarks<?=$cntr?>' name='dremarks' placeholder='Enter remarks...'value="<?=$rowbody['cremarks']?>" /></td>
									<td width='80px' style='padding:1px'><input class='btn btn-danger btn-xs' type='button' id='del<?=$cntr?>' value='delete' /></td>
								</tr>
							<?php
									}
								}
							?>

						</tbody>			                    
					</table>

				</div>
					
				<br>
				<table width="100%" border="0" cellpadding="3">
					<tr>
						<td>
							<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 

							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PurchRet.php';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>
							
							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>

    </fieldset>

	</form>
<?php
}else{
?>
	<form action="PR_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Purchase Request</legend>	

			<table width="100%" border="0">
				<tr>
					<tH width="100">PR NO.:</tH>
					<td colspan="3" style="padding:2px" align="left"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcprno" name="txtcprno" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
				</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PR No. DID NOT EXIST!</b></font></tH>
				</tr>
			</table>
		</fieldset>
	</form>
<?php
}
?>

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

	<form method="post" name="frmedit" id="frmedit" action="PurchRet_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" value="">
	</form>

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
		e.preventDefault();
		$('#txtprodnme').focus();
      }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("RR.php");

	  }
	});

	$(document).ready(function() {

		$('#date_needed').datetimepicker({
			format: 'MM/DD/YYYY'
		});

		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric").on("click", function () {
			$(this).select();
		});

		$(".xseluom").on('change', function() {
			var fact = setfactor($(this).val(), itmcode);									
			$('#hdnfactor'+lastRow).val(fact.trim());										
		});

		$('#txtprodnme').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../th_product.php",
					dataType: "json",
					data: {
						query: $("#txtprodnme").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.cname+'</span><br><small><span class="dropdown-item-extra">' + item.cunit + '</span></small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					
			
					$('#txtprodnme').val(item.cname).change(); 
					$('#txtprodid').val(item.id); 
					$("#hdnunit").val(item.cunit);
					
					addItemName("");	
				
			}
		
		});


		$("#txtprodid").keydown(function(e){
			if(e.keyCode == 13){

			$.ajax({
					url:'../get_productid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
				
							var data = value.split(",");
							$('#txtprodid').val(data[0]);
							$('#txtprodnme').val(data[1]);
				$('#hdnunit').val(data[2]);
			

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
					ComputeGross();	
					
						
				}
				else{
				//alert("ITEM NOT IN THE MASTERLIST!");
				addqty();
			}
			
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
	
				//closing for success: function(value){
				}
					}); 

		
			
			//if ebter is clicked
			}
			
		});

	});

function addItemName(){
	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		myFunctionadd();		
				
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		
	 }

}

function myFunctionadd(){

	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;

	var uomoptions = "";
								
		$.ajax ({
			url: "../th_loaduomperitm.php",
			data: { id: itmcode },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					if(item.id==itmunit){
						isselctd = "selected";
					}
					else{
						isselctd = "";
					}
					
					uomoptions = uomoptions + '<option value='+item.id+' '+isselctd+'>'+item.name+'</option>';
				});
						
			}
		});
		
		uomoptions = "<select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select>";

		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;


		$('#MyTable > tbody:last-child').append(
			"<tr>"
			+"<td width='120px'><input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"</td>"
			+"<td width='120px' nowrap style='padding:1px;overflow: hidden;text-overflow: ellipsis;'>"+itmdesc+"</td>"
			+"<td width='80px' style='padding:1px'>"+uomoptions+"</td>"
			+"<td width='100px' style='padding:1px'><input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='1' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>"
			+"<td width='200px' style='padding:1px'><input type='text' class='form-control input-xs' id='dremarks"+lastRow+"' name='dremarks' placeholder='Enter remarks...' /></td>"
			+"<td width='80px' style='padding:1px'><input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' /></td>"
		);								

		$("#del"+itmcode).on('click', function() { 
			$(this).closest('tr').remove();
		});

		$("input.numeric").numeric();
		$("input.numeric").on("click", function () {
			$(this).select();
		});
											
		$(".xseluom").on('change', function() {
			var fact = setfactor($(this).val(), itmcode);									
			$('#hdnfactor'+lastRow).val(fact.trim());										
		});

}

function setfactor(itmunit, itmcode){
	var result;
			
	$.ajax ({
		url: "../th_checkitmfactor.php",
		data: { itm: itmcode, cunit: itmunit },
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
}

function chkform(){
	var ISOK = "YES";
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(lastRow == 0){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;NO details found!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		return false;
		ISOK = "NO";
	}
	else{
		var msgz = "";
		var myqty = "";
		var myav = "";
		var myfacx = "";
		var myprice = "";

		$("#MyTable > tbody > tr").each(function(index) {
			
			myqty = $(this).find('input[name="txtnqty"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}
			
		});
		
		if(msgz!=""){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
	}
	
	if(ISOK == "YES"){
		document.getElementById("hdnrowcnt").value = lastRow; 

		//rename input name
		var tx = 0;
		$("#MyTable > tbody > tr").each(function(index) {  
			tx = index + 1;
			$(this).find('input[type=hidden][name="txtitemcode"]').attr("name","txtitemcode"+tx);
			$(this).find('select[name="seluom"]').attr("name","seluom" + tx);
			$(this).find('input[name="txtnqty"]').attr("name","txtnqty" + tx);
			$(this).find('input[type=hidden][name="hdnmainuom"]').attr("name","hdnmainuom" + tx);
			$(this).find('input[type=hidden][name="hdnfactor"]').attr("name","hdnfactor" + tx);
			$(this).find('input[name="dremarks"]').attr("name","dremarks" + tx);			
		});

		$("#frmpos").submit();

	}

}
</script>
