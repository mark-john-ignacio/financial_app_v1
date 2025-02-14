<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "GJournal";
include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>

  <link rel="stylesheet" type="text/css" href="../global/plugins/font-awesome/css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:50px;">
<center>
  <font size="+1"><b><u>General Journal</u></b></font>
</center>
<br>

<form action="Accounting/GJournal.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px"><button type="submit" class="btn btn-danger brn-block" id="btnsales">
      <span class="glyphicon glyphicon-search"></span> View Report
      </button></td>
    <td width="90px" style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">

      <div class="form-group nopadding">
        <div class="col-xs-8">
          <div class="input-group input-large date-picker input-daterange">
            <input type="text" class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>">
            <span class="input-group-addon">to </span>
            <input type="text" class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>">
          </div>
        </div>	
      </div>

    </td>
  </tr>
  <tr>
  <td valign="top" width="50" style="padding:2px"> 
      <button type="button" class="btn btn-success btn-block" id="btnxls">
            <i class="fa fa-file-excel-o"></i> To Excel
      </button></td>
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
           $('#btnxls').on('click', function(){
              $('#frmrep').attr("action", "Accounting/GJ_xls.php");
              $('#frmrep').submit();
           })
           $('#btnsales').on('click', function(){
            $('#frmrep').attr("action", "Accounting/GJournal.php");
            $('#frmrep').submit();
           })
});

function setact(x){
	document.getElementById("frmrep").action = x;
}
</script>
