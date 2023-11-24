
<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvSum.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>
<html>
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
<center><font size="+1"><b><u>Inventory Summary</u></b></font></center>
<br>

<form action="Inventory/InvSummary.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px">
    <button type="submit" class="btn btn-danger navbar-btn" id="btnsales">
    <span class="glyphicon glyphicon-search"></span> View Report
    </button>
    </td>
    <td width="80" style="padding-left:10px"><b>For: </b></td>
    <td style="padding:2px">
    <div class="col-xs-7 nopadding">
                    <div class="col-xs-8" style="padding:10px">
                        <select name="selm" id="selm" class="form-control">
                            <option value="<?php echo date("m");?>"><?php echo strftime("%B");?></option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                      </div>
                      
                      <div class="col-xs-4" style="padding:10px">  
                        <select name="sely" id="sely" class="form-control">
                            <option value="<?php echo date("Y");?>"><?php echo date("Y");?></option>
                            <option value="<?php echo date("Y",strtotime("-1 year"));?>"><?php echo date("Y",strtotime("-1 year"));?></option>
                        </select>
                	 </div>
    </div>
    </td>
  </tr>
</table>
</form>
</body>
</html>
