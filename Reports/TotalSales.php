<?php
if(!isset($_SESSION)){
session_start();
}
//$_SESSION['pageid'] = "TotalSales.php";

$company = $_SESSION['companyid'];
include('../Connection/connection_string.php');
//include('../include/denied.php');
//include('../include/access.php');

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min34.css">
    <script type="text/javascript" src="../js/jquery-1.10.1.js"></script>
<script language="javascript" type="text/javascript" src="../js/datetimepicker.js"></script>

<script type="text/javascript">
function chngetarget(x){
	document.getElementById("frmrep").action = x;
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
</head>

<body style="padding:10px;">
<form action="Sales/TotalSales.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td width="50" rowspan="2" valign="top" style="padding:2px"><button type="submit" class="btn btn-danger navbar-btn" id="btnsales"> <span class="glyphicon glyphicon-search"></span> View Report </button></td>
    <td width="150" style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">
      <div class="control-group">
        <div class="controls form-inline">
          <a href="javascript:NewCal('date1','mmddyyyy')">
            <input type='text' class="form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" readonly/>
            </a>
          <label for="inputValue">TO</label>
          <a href="javascript:NewCal('date2','mmddyyyy')">
            <input type='text' class="form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" readonly/>
            </a>
          </div>
        </div>
      </td>
  </tr>
  <tr>
    <td style="padding-left:10px"><b>Report Type: </b></td>
    <td style="padding:2px">
    <div class="control-group">
    <select name="selrpt" id="selrpt" onBlur="chngetarget(this.value);" class="form-control">
    	<option value="Sales/TotalSales.php">Total Sales Report</option>
        <option value="Sales/TotalSalesEmp.php">Employee Sales Report</option>
    </select>
    </div>
    </td>
  </tr>
</table>
</form>
</body>
</html>