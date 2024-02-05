<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "GLedger.php";
include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>

  <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
  <link href="../global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>

  <link rel="stylesheet" type="text/css" href="../global/plugins/bootstrap-select/bootstrap-select.min.css"/>
	<link rel="stylesheet" type="text/css" href="../global/plugins/select2/select2.css"/>
	<link rel="stylesheet" type="text/css" href="../global/plugins/jquery-multi-select/css/multi-select.css"/>

  <link href="../global/css/plugins.css" rel="stylesheet" type="text/css"/>

  <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

  <script src="../Bootstrap/js/bootstrap.js"></script>
  <script src="../global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="../global/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="../global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>



<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:50px;">
<center>
  <font size="+1"><b><u>General Ledger</u></b></font>
</center>
<br>

<form action="Accounting/GLedger.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px"><button type="submit" class="btn btn-danger brn-block" id="btnsales">
      <span class="glyphicon glyphicon-search"></span> View Report
      </button></td>
      <td width="90px" style="padding-left:10px"><b>Account: </b></td>
      <td style="padding:2px">

      <?php			
       $company = $_SESSION['companyid'];

				$sqlbaks = mysqli_query($con,"Select * From accounts where compcode='$company' and ctype='Details' Order By ccategory, cacctid");											
			?>
      <div class="col-xs-8">  
        <select class="form-control select2 input-sm" name="selbanks" id="selbanks">
          <option value=""></option>
          <?php
            if (mysqli_num_rows($sqlbaks)!=0) {
              while($rows = mysqli_fetch_array($sqlbaks, MYSQLI_ASSOC)){
          ?>
            <option value="<?=$rows['cacctid']?>"><?=$rows['cacctid'].": ".strtoupper($rows['cacctdesc'])?></option> 
          <?php
              }
            }
          ?>
        </select>
      </div>
      </td>
  </tr>
  <tr>
    <td valign="top" width="50" style="padding:2px"> 
      <button type="submit" class="btn btn-success btn-block" id="btnxls">
            <i class="fa fa-file-excel-o"></i> To Excel
      </button>
    </td>
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
</table>
</form>
</body>
</html>
<script type="text/javascript">
  $(function(){

    $('.datepick').datetimepicker({
      format: 'MM/DD/YYYY'
    });

    $("#selbanks").select2({
        placeholder: "Filter All Accounts",
        allowClear: true
    }); 

    $('#btnxls').on('click', function(){
      $('#frmrep').attr("action", "Accounting/GL_exls.php");
      $('#frmrep').submit();
    })

    $('#btnsales').on('click', function(){
      $('#frmrep').attr('action', 'Accounting/GLedger.php')
      $('#frmrep').submit();
    })
  });
</script>
