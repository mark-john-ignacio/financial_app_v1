
<?php
  if(!isset($_SESSION)){
    session_start();
  }
  $_SESSION['pageid'] = "InvLoc";

  include('../Connection/connection_string.php');
  include('../include/denied.php');
  include('../include/access.php');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>

	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding-left:50px;">
<center><font size="+1"><b><u>Inventory Summary per Location</u></b></font></center>
<br>

  <form action="Inventory/InvSumLoc.php" method="post" name="frmrep" id="frmrep" target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
        <button type="submit" class="btn btn-danger btn-blocked" id="btnsales">
        <span class="glyphicon glyphicon-search"></span> View Report
        </button>
        </td>
        <td style="padding-left:10px"><b>As Of: </b></td>
        <td style="padding:2px">

          <div class="col-xs-12 nopadding">
            <div class="col-xs-3 nopadding">

              <input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />

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

  });
  
</script>

