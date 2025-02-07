<?php
  if(!isset($_SESSION)){
    session_start();
  }
  $_SESSION['pageid'] = "SalesReg";

  include('../Connection/connection_string.php');
  include('../include/denied.php');
  include('../include/access.php');
?>
<html>
<head>

  <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
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
  <center><font size="+1"><b><u>Sales Register</u></b></font></center>
  <br>
  <form action="Accounting/SalesJournal.php" method="post" name="frmrep" id="frmrep" onclick='return false;' target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="submit" class="btn btn-danger btn-block" id="btnsales">
          <span class="glyphicon glyphicon-search"></span> View Report
          </button>
        </td>
        <td width="100" style="padding-left:10px"><b>Report Type: </b></td>
        <td style="padding:2px">

          <div class="col-xs-8 nopadding">
            <select id="seltype" name="seltype" class="form-control input-sm" onChange="setact(this.value);">
              <option value="Accounting/SalesLedger">Ledger</option>
              <option value="Accounting/SalesRecap">Recap</option>
              <option value="Accounting/SalesRecapCust">Recap Per Customer</option>
            </select>
          </div>
          
        </td>
      </tr>
      <tr>
        <td valign="top" width="50" style="padding:2px"> 
          <button type="button" class="btn btn-success btn-block" id="btnxls">
            <i class="fa fa-file-excel-o"></i> To Excel
          </button>
        </td>
        <td style="padding-left:10px"><b>Date Range: </b></td>
        <td style="padding:2px">

          <div class="form-group nopadding">
            <div class="col-xs-8 nopadding">
              <div class="input-group input-large date-picker input-daterange">
                <input type="text" class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>">
                <span class="input-group-addon">to </span>
                <input type="text" class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>">
              </div>
            </div>	
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

    $('#btnsales').click(function() {
        
      var selected = $('#seltype').find(":selected").val();
      $('#frmrep').attr('action', selected+".php")
      $('#frmrep').submit()
    });


    $('#btnxls').click(function(){

      var selected = $('#seltype').find(":selected").val();   
      //alert(selected);
      $('#frmrep').attr('action', selected+"_xls.php")
      $('#frmrep').submit()
    });
	   
  });

  function setact(x){
    document.getElementById("frmrep").action = x;
  }
</script>
