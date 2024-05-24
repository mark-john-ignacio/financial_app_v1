<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PR_new";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$id = $_SESSION['employeeid'];
                        
	$sql = "select * From users where Userid='$id'";
	$result=mysqli_query($con,$sql);
														
	$cfname = "";                                       							
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$mi = ($row['Minit']!="") ? " ".$row['Minit'] : "";
		$cfname =  $row['Lname'] . ", ". $row['Fname'] . $mi;
	}

	//get last approvedby
	$sql = "SELECT capprovedby, ccheckedby From purchrequest WHERE compcode='$company' ORDER BY ddate DESC LIMIT 1";
	$result=mysqli_query($con,$sql);														
	$clastapprvby = "";      
	$clastchkdby = "";                                  							
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$clastapprvby = $row['capprovedby'];
		$clastchkdby = $row['ccheckedby'];
	}

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

	//get locations of cost center
	@$clocs = array();
	$gettaxcd = mysqli_query($con,"SELECT nid, cdesc FROM `locations` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$clocs[] = $row; 
		}
	}

	@$arrempslist = array();
	$getempz = mysqli_query($con,"SELECT nid, cdesc, csign FROM `mrp_operators` where compcode='$company' and cstatus='ACTIVE' order By cdesc"); 
	if (mysqli_num_rows($getempz)!=0) {
		while($row = mysqli_fetch_array($getempz, MYSQLI_ASSOC)){
			@$arrempslist[] = array('nid' => $row['nid'], 'cdesc' => $row['cdesc'], 'csign' => $row['csign']); 
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<!--
	--
	-- FileType Bootstrap Scripts and Link
	--
	-->
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px">
	<input type="hidden" id="costcenters" value='<?=json_encode($clocs)?>'>
	<form action="PR_newsave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data">

		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-cart-plus"></i>New Purchase Request
				</div>
			</div>
			<div class="portlet-body">

				<ul class="nav nav-tabs">
					<li class="active"><a href="#home">PR Details</a></li>
					<li><a href="#attc">Attachments</a></li>
				</ul>

				<div class="tab-content">  

					<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top: 10px;">

						<table width="100%" border="0">
							<tr>
								<tH width="150">Requested By:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-5 nopadding">
											<select class='xsel2 form-control input-sm' id="txtcustid" name="txtcustid">`
												<option value="">&nbsp;</option>
												<?php
													foreach(@$arrempslist as $rsx){
														echo "<option value='".$rsx['nid']."'> ".$rsx['cdesc']." </option>";
													}
												?>
											</select>
										</div>
										<div class="col-xs-5 nopadwleft">
											<select class="form-control input-sm" name="selwhfrom" id="selwhfrom"> 
												<?php
													foreach($rowdetloc as $localocs){									
												?>
														<option value="<?php echo $localocs['nid'];?>"><?php echo $localocs['cdesc'];?></option>										
												<?php	
													}						
												?>
											</select>
										</div>
									</div>
								</td>
								<tH width="150" style="padding:2px">Date Needed:</tH>
								<td style="padding:2px" width="200">
									<div class="col-xs-8 nopadding">
										<input type='text' class="datepick form-control input-sm" id="date_needed" name="date_needed" />
									</div>
								</td>
							</tr>
							<tr>
								<tH>Checked/Approved:</tH>
								<td style="padding:2px">
									<div class="col-xs-5 nopadding">
										<input type='text' class="form-control input-sm" id="chkdby" name="chkdby" placeholder="Enter Checked By..." value="<?=$clastchkdby?>">
									</div>
									<div class="col-xs-5 nopadwleft">
										<input type='text' class="form-control input-sm" id="apprby" name="apprby" placeholder="Enter Approved By..." value="<?=$clastapprvby?>">
									</div>
								</td>
								<tH width="150">&nbsp;</tH>
								<td style="padding:2px;">
									<input type='hidden' id="txtremarks" name="txtremarks">
								</td>
							</tr>
							
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
						</table>

					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px;">
						<!--
						--
						-- Import Files Modal
						--
						-->
						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />
						
					</div>

				</div>

				<div class="portlet light bordered">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Details
						</div>
						<div class="inputs">
							<div class="portlet-input input-inline">
								<div class="col-xs-12 nopadding">

									<input type="hidden" name="hdnunit" id="hdnunit">
											
									<div class="col-xs-4 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Item/SKU Code..." tabindex="4"></div>
									<div class="col-xs-8 nopadwleft"><input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5"></div>
								</div> 
							</div>	  
						</div>
					</div>
					<div class="portlet-body" style="overflow: auto">
						<div style="min-height: 30vh; width: 1500px;">

							<table id="MyTable" class="MyTable table-sm table-bordered" border="1">
								<thead>	
									<tr>
										<th width="200px" style="border-bottom:1px solid #999">Part No.</th>
										<th width="300px" style="border-bottom:1px solid #999">Description</th>
										<th width="100px" style="border-bottom:1px solid #999">&nbsp;&nbsp;Item Code</td>
										<th width="80px" style="border-bottom:1px solid #999">UOM</th>
										<th width="120px" style="border-bottom:1px solid #999">Qty</th>
										<th width="250px" style="border-bottom:1px solid #999">Remarks</th>
										<th width="150px" style="border-bottom:1px solid #999">Cost Center</th>
										<th width="50px" style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>
								</thead>
								<tbody class="tbody">
								</tbody>			                    
							</table>

						</div>
					</div>
				</div>

				<div class="row nopadwtop2x">
					<div class="col-xs-12">
						<div class="portlet">
							<div class="portlet-body">
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PR.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>
			
								<button type="submit" class="btn green btn-sm" tabindex="6"  id="btnSave" onClick="return chkform();" name="btnSave">
									SAVE<br> (CTRL+S)
								</button>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

    </fieldset>

</form>

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
		 window.location.replace("PR.php");

	  }
	});

	$(document).ready(function() {

		$(".nav-tabs a").click(function(){
    	$(this).tab('show');
		});

		$('#date_needed').datetimepicker({
			format: 'MM/DD/YYYY',
			useCurrent: false,
			minDate: moment(),
			defaultDate: moment(),
		});

		$("#txtcustid").select2({
			placeholder: "Select Requested By...",
			allowClear: true
		});

		$("#file-0").fileinput({
			uploadUrl: '#',
			showUpload: false,
			showClose: false,
			allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
			overwriteInitial: false,
			maxFileSize:100000,
			maxFileCount: 5,
			browseOnZoneClick: true,
			fileActionSettings: { showUpload: false, showDrag: false,}
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
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.id+": "+item.cname+'</span><br><small><span class="dropdown-item-extra">' + item.cunit + '</span></small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					
			
					$('#txtprodnme').val(item.cname).change(); 
					$('#txtprodid').val(item.id); 
					$("#hdnunit").val(item.cunit);
					
					addItemName("");	
					
				$('.datepick').each(function(){
					$(this).datetimepicker({format: 'MM/DD/YYYY'});	
				});
				
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

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
								
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


		var xz = $("#costcenters").val();
		taxoptions = "";
		$.each(jQuery.parseJSON(xz), function() { 
			taxoptions = taxoptions + "<option value='"+this['nid']+"' data-cdesc='"+this['cdesc']+"'>"+this['cdesc']+"</option>";
		});

		var costcntr = "<select class='form-control input-xs' name='txtnSub' id='txtnSub"+lastRow+"'>  <option value='0' data-cdesc=''>NONE</option> " + taxoptions + " </select>"; 
		
		$('#MyTable > tbody:last-child').append( 
			"<tr>"
			+"<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"><input type='hidden' value='"+itmdesc+"' name=\"txtcpartdesc\" id=\"txtcpartdesc\">"+itmdesc+"</td>"
			+"<td><input type='text' class='form-control input-xs' id='txtcitemdesc' name='txtcitemdesc' placeholder='Enter remarks...' value='"+itmdesc+"' /></td>"
			+"<td><input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">&nbsp;&nbsp;"+itmcode+"</td>"
			+"<td style='padding:1px'>"+uomoptions+"</td>"
			+"<td style='padding:1px'><input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='1' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>"
			+"<td style='padding:1px'><input type='text' class='form-control input-xs' id='dremarks"+lastRow+"' name='dremarks' placeholder='Enter remarks...' /></td>"
			+'<td width="150px" style="padding:1px">'+costcntr+'</td>'
			+"<td style='padding:1px'><input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' /></td>"
		);								

		$("#del"+lastRow).on('click', function() { 
			$(this).closest('tr').remove();
		});

		$("input.numeric").autoNumeric('init',{mDec:2});
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
			$(this).find('input[type=hidden][name="txtcpartdesc"]').attr("name","txtcpartdesc"+tx);
			$(this).find('input[name="txtcitemdesc"]').attr("name","txtcitemdesc"+tx);
			$(this).find('input[type=hidden][name="txtitemcode"]').attr("name","txtitemcode"+tx);
			$(this).find('select[name="seluom"]').attr("name","seluom" + tx);
			$(this).find('input[name="txtnqty"]').attr("name","txtnqty" + tx);
			$(this).find('input[type=hidden][name="hdnmainuom"]').attr("name","hdnmainuom" + tx);
			$(this).find('input[type=hidden][name="hdnfactor"]').attr("name","hdnfactor" + tx);
			$(this).find('input[name="dremarks"]').attr("name","dremarks" + tx);			
			$(this).find('select[name="txtnSub"]').attr("name","txtnSub" + tx);
		});

		$("#frmpos").submit();

	}

}
</script>
