<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "RFP_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">

	<form action="RFP_newsave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data" onsubmit="return chkform()">
		<fieldset>
				<legend>Request For Payment</legend>
				
					<table width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
							<td><span style="padding:2px"><b>Paid To:</b></span></td>
							<td>
							<div class="col-xs-12"  style="padding-left:2px">
								<div class="col-xs-4 nopadding ">
										<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm required" required placeholder="Supplier Code..." readonly>
								</div>
								<div class="col-xs-8 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
								</div>
							</div>
							</td>
							<td><span style="padding:2px"><b>APV No.:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class="col-xs-6 nopadding">
										<input type="text" class="form-control input-sm required" id="txtrefapv" name="txtrefapv" width="20px" placeholder="Search APV No..." required autocomplete="off" tabindex="4" readonly>
									</div>
									<div class="col-xs-2 nopadwleft">
										<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchapv" id="btnsearchapv"><i class="fa fa-search"></i></button>
									</div>

								</div>
							</td>
						</tr>
					
						
						<tr>
							<td width="150"><span style="padding:2px" id="paymntdesc"><b>Bank Name</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px" id="paymntdescdet">
									<div class="col-xs-3 nopadding">
										<input type="text" id="txtBank" class="form-control input-sm required" name="txtBank" value="" placeholder="Bank Code" readonly required>
									</div>
									<div class="col-xs-1 nopadwleft">
										<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchbank" id="btnsearchbank"><i class="fa fa-search"></i></button>
									</div>
									<div class="col-xs-8 nopadwleft">
										<input type="text" class="form-control input-sm required" id="txtBankName" name="txtBankName" width="20px" tabindex="1" placeholder="Bank Name..." required value="" autocomplete="off" readonly>
									</div>
									
								</div>

							</td>

							<td width="150"><span style="padding:2px"><b>Payment Method</b></span></td>
							<td>
								<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
									<div class="col-xs-8 nopadding">
										<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
											<option value="cheque">Cheque</option>
											<option value="cash">Cash</option>
											<option value="bank transfer">Bank Transfer</option>
											<option value="mobile payment">Mobile Payment</option>
											<option value="credit card">Credit Card</option>
											<option value="debit card">Debit Card</option>
										</select>
									</div>
							</td>														
						</tr>

						<tr>
							<td><span style="padding:2px"><b>Remarks</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<input type="text" class="form-control input-sm" id="txtcremarks" name="txtcremarks" tabindex="1" placeholder="Remarks..." value="" autocomplete="off">
								</div>
							</td>
							<td><span style="padding:2px" id="chkdate"><b>Due Date:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class='col-xs-8 nopadding'>
											<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?php echo date("m/d/Y"); ?>" />
									</div>
								</div>
							</td>
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><span style="padding:2px" id="chkdate"><b>Amount to Pay:</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class='col-xs-8 nopadding'>
											<input type='text' class="form-control input-sm text-right" name="txtnamount" id="txtnamount" value="0.00" />
											<input type='hidden' name="txtnamountbal" id="txtnamountbal" value="0.00" /> 
									</div>
								</div>
							</td>
						</tr>

					</table>

					<h4>Attachments <small><i>(jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></small></h4> 
					<input id="file-0" name="upload[]" type="file" multiple>

					<br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
													
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>																					
												
								<button type="submit" class="btn btn-success btn-sm" tabindex="6">Save<br> (CTRL+S)</button>
											
							</td>
						</tr>
						
					</table>

			</fieldset>

	</form>

				<!-- DETAILS ONLY -->
				<div class="modal fade" id="myAPModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="APListHeader">AP List</h3>
							</div>
										
							<div class="modal-body pre-scrollable">
										
								<table name='MyAPVList' id='MyAPVList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th>AP No.</th>
											<th>Date</th>
											<th>Payment For</th>
											<th>Total Payable</th>
											<th>Payable Balance</th>
										</tr>
									</thead>
									<tbody>
																			
									</tbody>
								</table>

							</div> 
										
							<div class="modal-footer">
								<button type="button" id="btnSave2" onClick="InsertSI()" class="btn btn-primary">Insert</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							</div>        	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->

				<!-- Banks List -->
				<div class="modal fade" id="myChkModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="BanksListHeader">Bank List</h3>
							</div>
							
							<div class="modal-body pre-scrollable">
							
								<table name='MyDRDetList' id='MyDRDetList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th>Bank Code</th>
											<th>Bank Name</th>
											<th>Bank Acct No</th>
										</tr>
									</thead>
									<tbody>																
									</tbody>
								</table>
							</div>         	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Banks modal -->

				<!-- 1) Alert Modal -->
				<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
					<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-top">
							<div class="modal-content">
								<div class="alert-modal-danger">
									<p id="AlertMsg"></p>
									<p><center>
										<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
									</center></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Alert modal -->

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("RFP.php");
	  }
	});


$(document).ready(function() {

	$("#txtnamount").autoNumeric('init',{mDec:2,wEmpty:'zero'});

  $('.datepick').datetimepicker({
    format: 'MM/DD/YYYY',
  });

	$("#file-0").fileinput({
    theme: 'fa5',
    uploadUrl: '#',
		showUpload: false,
		showClose: false,
		allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
		overwriteInitial: false,
		maxFileSize:2000,
		maxFileCount: 5,
		fileActionSettings: { showUpload: false, showDrag: false,}
  });

	$('#txtcust').typeahead({
		
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "../th_supplier.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 

			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
				
		//	showapvmod(item.id);

		}
	});

	$("#btnsearchbank").on("click", function() {

		$('#MyDRDetList tbody').empty();
		
			$.ajax({
        url: 'th_banklist.php',
        dataType: 'json',
				async:false,
        method: 'post',
        success: function (data) {
        // var classRoomsTable = $('#mytable tbody');
          console.log(data);
          $.each(data,function(index,item){

							$("<tr id=\"bank"+index+"\">").append(
								$("<td>").text(item.ccode),
								$("<td>").text(item.cname),
								$("<td>").text(item.cbankacctno)
							).appendTo("#MyDRDetList tbody");
								
						$("#bank"+index).on("click", function() {
							$("#txtBank").val(item.ccode);
							$("#txtBankName").val(item.cname);
							
							$("#myChkModal").modal("hide");
						});

          });

        },
        error: function (req, status, err) {

					$("#AlertMsg").html("<b>ERROR: </b>Something went wrong!<br>Status: "+ status + "<br>Error: "+err);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');

					console.log('Something went wrong', status, err);
				}
      });

		
		$("#myChkModal").modal("show");
	});
	
	$("#btnsearchapv").on("click", function() {
		var custid = $("#txtcustid").val();
		showapvmod(custid)
	});

});
		
function showapvmod(custid){

	$('#MyAPVList tbody').empty();

	$.ajax({
    url: 'th_APVlist.php',
		data: { code: custid },
    dataType: 'json',
		async:false,
    method: 'post',
    success: function (data) {

      console.log(data);
      $.each(data,function(index,item){
						
				if(item.ctranno=="NO"){
					alert("No Available Reference.");
									
					$('#txtcust').val("").change(); 
					$("#txtcustid").val("");

				}
				else{
			
					$("<tr id=\"APV"+index+"\">").append(
						$("<td>").html("<a href='javascript:;' onclick='InsertSI("+index+")'>"+item.ctranno+"</a> <input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'>"),
						$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
						$("<td>").html(item.cpaymentfor+"<input type='hidden' id='APVPayFor"+index+"' name='APVPayFor"+index+"' value='"+item.cpaymentfor+"'>"),
						$("<td>").html(item.namount+"<input type='hidden' id='APVamt"+index+"' name='APVamt"+index+"' value='"+item.namount+"'>"),
						$("<td>").html(item.nbalance+"<input type='hidden' id='APVBal"+index+"' name='APVBal"+index+"' value='"+item.nbalance+"'>")
					).appendTo("#MyAPVList tbody");
									
					$("#myAPModal").modal("show");
								
				}

      });

    },
    error: function (req, status, err) {

			$("#AlertMsg").html("<b>ERROR: </b>Something went wrong!<br>Status: "+ status + "<br>Error: "+err);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			console.log('Something went wrong', status, err);
		}
  });

}

	function InsertSI(xyz){	
	    
		var a = $("#APVtxtno"+xyz).val();
		var b = $("#APVamt"+xyz).val();
		var c = $("#APVBal"+xyz).val();

		$("#txtrefapv").val(a);
		$("#txtnamount").val(c);
		$("#txtnamountbal").val(c);

		$("#txtnamount").autoNumeric('destroy');
		$("#txtnamount").autoNumeric('init',{mDec:2});

		$('#myAPModal').modal('hide');
  
	};

	function chkform(){
		
		var emptyFields = $('input.required').filter(function() {
			return $(this).val() === "";
		}).length;

		if (emptyFields === 0) {
			return true;
		} else {
			
			$("#AlertMsg").html("<b>ERROR: </b>Required Fields!<br>Supplier Code/Name, Bank Code/Name, and APV No.");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
		}

	}

</script>
