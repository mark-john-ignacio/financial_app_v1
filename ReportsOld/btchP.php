<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesReg.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
<head>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
</head>

<body style="padding-left:10px;">
<div class="col-sm-12 nopadding">

	<div id="divname">
		<h3> <u>Batch Printing</u></h3>
    </div>
    <hr>
</div>

<br>
<form action="" method="post" name="frmrep" id="frmrep" target="_blank">


<div class="col-sm-12 nopadding">

	<div class="col-sm-2">
		<button type="button" class="btn btn-danger btn-sm btn-block" id="btngettbl">
        <span class="glyphicon glyphicon-search"></span> View Transactions
        </button>
    </div>
    
    <div class="col-sm-1 nopadding">
    	<b>Report Type: </b>
    </div>
    
    <div class="col-sm-2 nopadwleft">
    
    	<select id="seltype" name="seltype" class="form-control input-sm" onChange="setact(this.value);">
          <option value="Print/Receiving.php">Receiving</option>
          <option value="Print/SI.php">Sales Invoice</option>
        </select>
        
    </div>
    
    <div class="col-sm-4 nopadwleft">
    	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off">
        
        <input type="hidden" id="txtcustid" name="txtcustid">
    </div>
    
</div>

<div class="col-sm-12 nopadwtop">

	<div class="col-sm-2">
		
    </div>
    
    <div class="col-sm-1 nopadding">
    	<b>Date Range: </b>
    </div>
    
    <div class="col-sm-3 nopadwleft">
    
    	<div class="col-xs-5 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />

		</div>
        
        <div class="col-xs-2 nopadding" style="vertical-align:bottom;" align="center">
        	<label style="padding:1px;">TO</label>
        </div>
 
         <div class="col-xs-5 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" />

		</div>
        
    </div>
    
</div>



</form>



<div class="col-sm-12 nopadwtop pre-scrollable" id="divtbls" style="height: 75vh">
</div>

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

</body>
</html>

<script type="text/javascript">
$(function() {              
           // Bootstrap DateTimePicker v4
	        $('.datepick').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
		   
		   $("#btngettbl").on("click", function() {
			   
			   $.ajax({
                    url: $("#seltype").val(),
					data: 'dte1='+$("#date1").val()+"&dte2="+$("#date2").val()+"&id="+$("#txtcustid").val(),
                    dataType: 'text',
                    method: 'post',
                    success: function (data) {

						$("#divtbls").html(data);
						
                    },
                    error: function (req, status, err) {
						//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
 						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
                   }
                });
			   
		   });
		   
		   
		   //Search Cust name
	$('#txtcust').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "Print/th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val(), id: $("#seltype").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);						
			
		}
	
	});
	   
});


function setact(x){
	if(x=="Print/Receiving.php"){
		$("#txtcust").attr("placeholder","Search Supplier Name...");
	}else{
		$("#txtcust").attr("placeholder","Search Customer Name...");
		
	}
	document.getElementById("frmrep").action = x;
}
</script>
