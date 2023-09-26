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
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
</head>

<body style="padding-left:50px;">
<center><font size="+1"><b><u>Sales Register</u></b></font></center>
<br>
<form action="Sales/SalesJournal.php" method="post" name="frmrep" id="frmrep" onclick='return false;' target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td rowspan="2" valign="top" width="50" style="padding:2px">
    <button type="submit" class="btn btn-danger navbar-btn" id="btnsales">
    <span class="glyphicon glyphicon-search"></span> View Report
    </button>
    </td>
    <td width="150" style="padding-left:10px"><b>Report Type: </b></td>
    <td style="padding:2px">
    <div class="col-xs-5 nopadding">
        <select id="seltype" name="seltype" class="form-control input-sm" onChange="setact(this.value);">
          <option value="Sales/SalesJournal.php">Journal</option>
          <option value="Sales/SalesRecap.php">Recap</option>
          <option value="Sales/SalesRecapCust.php">Recap Per Customer</option>
        </select>
   </div>
   </td>
  </tr>
  <tr>
    <td style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding">
        <div class="col-xs-3 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />

		</div>
        
        <div class="col-xs-2 nopadding" style="vertical-align:bottom;" align="center">
        	<label style="padding:1px;">TO</label>
        </div>
 
         <div class="col-xs-3 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" />

		</div>

     </div>    
    </td>
  </tr>
  <tr>
    <td> 
      <button type="button" class="btn btn-success btn-block" id="btnxls">
            <i class="fa fa-file-excel-o"></i> To Excel
      </button></td>
  </tr>
</table>
</form>
</body>
</html>

<script type="text/javascript">
$(function() {              
           // Bootstrap DateTimePicker v4
	        $('.datepick').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
          $('#btnsales').click(function() {
             
            var selected = $('#seltype').find(":selected").val();
            $('#frmrep').attr('action', selected)
            $('#frmrep').submit()
          })


          $('#btnxls').click(function(){

            var selected = $('#seltype').find(":selected").text();
            var link = null
            switch (selected) {
              case 'Journal':
                link = 'Sales/SalesJournal_xls.php'
                break
              case 'Recap':
                link = 'Sales/SalesRecap_xls.php'
                break
              case 'Recap Per Customer':
                link = 'Sales/SalesRecapCust_xls.php'
                break
              default:
                break
            }
            $('#frmrep').attr('action', link)
            $('#frmrep').submit()
          })
	   
});


function setact(x){
	document.getElementById("frmrep").action = x;
}
</script>
