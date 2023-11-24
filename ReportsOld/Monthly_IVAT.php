<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Monthly_IVAT.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
<head>

  <link rel="stylesheet" type="text/css" href="../global/plugins/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>
</head>

<body style="padding-left:50px;">
<center>
<b><u><font size="+1">BIR Monthly Input VAT and W/Tax Report</font></u></b>

</center>
<br>
<form action="Accounting/Monthly_IVAT.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px">
      <button type="button" class="btn btn-danger btn-block" id="btnView">
        <span class="glyphicon glyphicon-search"></span> View Report
      </button>
    </td>
    <td style="padding-left:10px" width="150"><b>Pick Month/Year: </b></td>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding">
        <div class="col-xs-5 nopadding">

		      <input type='text' class="datepick form-control input-sm" id="date1" name="date1" />

		    </div>
        <div class="col-xs-5 nopadwleft">

          <select class="form-control input-sm" id="selstat" name="selstat">
            <option value="">ALL Transactions</options>
            <option value="1">Posted</options>
            <option value="0">Unposted</options>
          </select>

        </div>
     </div>   
    </td>
  </tr>
  <tr>
    <td valign="top" width="50" style="padding:2px">
    <button type="button" class="btn btn-success btn-block" id="btnexcel">
        <i class="fa fa-file-excel-o"></i> To Excel
      </button>
    </td>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>

<script type="text/javascript">
$(function() {              
           // Bootstrap DateTimePicker v4
	        $('.datepick').datetimepicker({
              defaultDate: moment(),
              viewMode: 'years',
              format: 'MM/YYYY'
           });


    $('#btnView').on("click", function(){
        $('#frmrep').attr("action", "Accounting/Monthly_IVAT.php");
        $('#frmrep').submit();
    });

    $('#btnexcel').on("click", function(){
        $('#frmrep').attr("action", "Accounting/Monthly_IVAT_xls.php");
        $('#frmrep').submit();
    });
	   
});
</script>
