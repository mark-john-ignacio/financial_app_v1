<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "ARAgeing.php";

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
<center>
<b><u><font size="+1">AR Ageing</font></u></b>

</center>
<br>
<form action="Accounting/ARAgeingDetailed.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
      <td valign="top" width="50" style="padding:2px">
    <button type="submit" class="btn btn-danger navbar-btn" id="btnsales">
    <span class="glyphicon glyphicon-search"></span> View Report
    </button>
    </td>

    <td style="padding-left:10px" width="100"><b>Date As Of: </b></td>
    <td style="padding:2px" width="150">
		      <input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" /> 
    </td>

    <td style="padding-left:10px" width="100" align="right"><b>Report Type: </b></td>
    <td style="padding-left:2px">
      <div class="col-xs-7">
        <select class="form-control input-sm" name="selagrptype" id="selagrptype">
            <option value="Detailed">Detailed</option>
            <option value="Summary">Summary</option>
            
        </select>
      </div>
    </td>
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

          $("#selagrptype").on("change", function(){
            if($(this).val()=="Summary"){
              $("#frmrep").action = "Accounting/ARAgeingSummary.php";
            }else{
              $("#frmrep").action = "Accounting/ARAgeingDetailed.php";
            }


          });
	   
});
</script>
