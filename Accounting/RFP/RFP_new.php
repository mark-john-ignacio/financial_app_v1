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
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?php echo time();?>">
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
				
					<table width="100%" border="0" cellspacing="0" cellpadding="2"  style="margin-bottom: 25px">
						<tr>
							<td><span style="padding:2px"><b>Paid To:</b></span></td>
							<td>
							<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
								<div class="col-xs-4 nopadding ">
										<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm required" required placeholder="Supplier Code..." readonly>
								</div>
								<div class="col-xs-8 nopadwleft">
										<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
								</div>
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
							
							<td valign="top" style="padding-top:8px;"><span style="padding:2px;"><b>Remarks</b></span></td>
							<td>
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<textarea class="form-control input-sm" id="txtcremarks" name="txtcremarks" rows="3"></textarea>
								</div>
							</td>
							<td valign="top" style="padding-top:8px;"><span style="padding:2px" id="chkdate"><b>Total Amount to Pay:</b></span></td>
							<td valign="top">
								<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
									<div class='col-xs-8 nopadding'>
											<input type='text' class="form-control input-sm text-right" name="txtnamount" id="txtnamount" value="0.00" readonly/>
									</div>
								</div>
							</td>											
						</tr>

					</table>

					<ul class="nav nav-tabs">
						<li class="active"><a href="#apv">APV List</a></li>
						<li><a href="#attc">Attachments</a></li>
					</ul>

					<div class="alt2" dir="ltr" style="margin: 0px; padding: 3px;border: 0px;width: 100%;text-align: left;overflow: auto">
						<div class="tab-content">  

							<div id="apv" class="tab-pane fade in active" style="padding-top:10px;">

								<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 1px solid #919b9c;width: 100%;height: 40vh;text-align: left;overflow: auto">
				
									<table id="MyTable" class="MyTable table table-condensed" width="100%">
										<thead>
											<tr>
												<th style="border-bottom:1px solid #999">APV No.</th>
												<th style="border-bottom:1px solid #999">Account Code</th>
												<th style="border-bottom:1px solid #999">Account Title</th>
												<th style="border-bottom:1px solid #999">Amount</th>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
											</tr>	
											</thead>														
										<tbody class="tbody">
										</tbody>															
									</table>

								</div>


							</div>

							<div id="attc" class="tab-pane fade in" style="padding-top:10px;">

								<i>(jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i>
								<input id="file-0" name="upload[]" type="file" multiple>

							</div>
					</div>
					
					<br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">																
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RFP.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>		
								<button type="button" class="btn btn-info btn-sm" id="btnShowApv" name="btnShowApv">
									Save<br> (CTRL+S)
								</button>																																		
								<button type="submit" class="btn btn-success btn-sm" tabindex="6">
									Save<br> (CTRL+S)
								</button>														
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
										
								<table name='MyAPVList' id='MyAPVList' class="table table-small table-hoverO" style="cursor:pointer" width="100%">
									<thead>
										<tr>
											<th> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
											<th>AP No.</th>
											<th>Date</th>
											<th>Account</th>
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

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

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

	$(".nav-tabs a").click(function(){
    $(this).tab('show');
	});


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
				
			showapvmod(item.id);

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

	$("#btnShowApv").on("click", function() {
		if($("#txtcustid").val()!==""){
			showapvmod($("#txtcustid").val());
		}else{
			$("#AlertMsg").html("<b>ERROR: </b>Pick a valid customer!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
		}
		
	});
	
	$("#allbox").click(function(e){
		var table= $(e.target).closest('table');
		$('td input:checkbox',table).not(this).prop('checked', this.checked);
	});

});

		
function showapvmod(custid){

	if ( $.fn.DataTable.isDataTable('#MyAPVList') ) {
		$('#MyAPVList').DataTable().destroy();
	}

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
						$("<td>").html("<input type='checkbox' value='"+index+"' name='chkSales[]'>"), 
						$("<td>").html(item.ctranno+"<input type='hidden' id='APVtxtno"+index+"' name='APVtxtno"+index+"' value='"+item.ctranno+"'>"),
						$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte"+index+"' value='"+item.dapvdate+"'>"),
						$("<td>").html(item.cacctno+" - "+item.cacctdesc+"<input type='hidden' id='APVAcctPay"+index+"' name='APVAcctPay"+index+"' value='"+item.cacctno+"'><input type='hidden' id='APVAcctPayDesc"+index+"' name='APVAcctPayDesc"+index+"' value='"+item.cacctdesc+"'>"),
						$("<td align='right'>").html(item.namount+"<input type='hidden' id='APVamt"+index+"' name='APVamt"+index+"' value='"+item.namount+"'>"),
						$("<td align='right'>").html(item.nbalance+"<input type='hidden' id='APVBal"+index+"' name='APVBal"+index+"' value='"+item.nbalance+"'>")
					).appendTo("#MyAPVList tbody");
									
					$("#myAPModal").modal("show");
								
				}

      });

			$('#MyAPVList').dataTable({
						"info":false, 
						"ordering":true, 
						"paging":false,
						"autoWidth": false,
						"columnDefs": [
							{ "width": "5%", "className": "text-center", "targets": 0 },
							{ "width": "10%", "targets": 1 },
							{ "width": "8%", "targets": 2 }
						],
					
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

	function InsertSI(){	

		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var tblrowcnt = tbl.length;

		$("input[name='chkSales[]']:checked").each( function () {

			xyz = $(this).val();
	    
			var a = $("#APVtxtno"+xyz).val();
			var b = $("#APVamt"+xyz).val();
			var c = $("#APVBal"+xyz).val();
			var d = $("#APVAcctPay"+xyz).val();
			var e = $("#APVAcctPayDesc"+xyz).val();

			$("<tr>").append(
				$("<td>").html(a+"<input type='hidden' id='txtcapvno"+tblrowcnt+"' name='txtcapvno"+tblrowcnt+"' value='"+a+"'>"),
				$("<td>").html(d+"<input type='hidden' id='txtapvacctid"+tblrowcnt+"' name='txtapvacctid"+tblrowcnt+"' value='"+d+"'>"),
				$("<td>").html(e+"<input type='hidden' id='txtapvacctitle"+tblrowcnt+"' name='txtapvacctitle"+tblrowcnt+"' value='"+e+"'>"),
				$("<td width='150px'>").html("<input type='text' class='numeric form-control input-sm text-right' id='txtapvbal"+tblrowcnt+"' name='txtapvbal"+tblrowcnt+"' value='"+c+"'> <input type='hidden' id='txtapvamt"+tblrowcnt+"' name='txtapvamt"+tblrowcnt+"' value='"+b+"'>")
			).appendTo("#MyTable tbody");

			tblrowcnt++;
		
			$("input.numeric").autoNumeric('destroy');
			$("input.numeric").autoNumeric('init',{mDec:2});

			$("input.numeric").on("focus", function () {
				$(this).select();
			});

			$("input.numeric").on("keyup", function () {
				comtotamt();
			});

		});

		$('#myAPModal').modal('hide');
		comtotamt();
  
	};

	function comtotamt(){
		var rowCount = $('#MyTable tr').length;			
		var gross = 0;

		if(rowCount>1){
			for (var i = 1; i <= rowCount-1; i++) {
				gross = gross + parseFloat($("#txtapvbal"+i).val().replace(/,/g,''));
			}
		}

		$("#txtnamount").val(gross);
		$("#txtnamount").autoNumeric('destroy');
		$("#txtnamount").autoNumeric('init',{mDec:2});
	}

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
