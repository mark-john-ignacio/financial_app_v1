<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "StockLedger.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


$first_day_this_month = date('m-01-Y'); // hard-coded '01' for first day
$last_day_this_month  = date("Y-m-t", strtotime($first_day_this_month));

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>

	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:50px;">
<center><font size="+1"><b><u>Income Statement</u></b></font></center>
<br>

<form action="Accounting/IncomeStatement.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px">
    <button type="submit" class="btn btn-danger navbar-btn" id="btnsales">
    <span class="glyphicon glyphicon-search"></span> View Report
    </button>
    </td>
    <td width="150" style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">
      <div class="col-xs-12 nopadding">
        <div class="col-xs-3 nopadding">
          
          <input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date_format(date_create($first_day_this_month),"m/d/Y"); ?>" />
          
          </div>
        
        <div class="col-xs-2 nopadding" style="vertical-align:bottom;" align="center">
          <label style="padding:1px;">TO</label>
          </div>
        
        <div class="col-xs-3 nopadding">
          
          <input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date_format(date_create($last_day_this_month),"m/d/Y"); ?>" />
          
          </div>
        
        </div>   
    </td>

  </tr>
</table>
</form>
</body>
</html>

<script type="text/javascript">
$(function(){

	        $('.datepick').datetimepicker({
                 format: 'MM/DD/YYYY'
           });

	//proddesc searching	
	
$('#txtCust').typeahead({
	autoSelect: true,
    source: function(request, response) {
        $.ajax({
            url: "th_product.php",
            dataType: "json",
            data: {
                query: $("#txtCust").val()
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
					
		$('#txtCust').val(item.value).change(); 
		$("#txtCustID").val(item.id);
		
	}

});

});
</script>
