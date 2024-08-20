<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PurchSummary";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
<head>

  <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

  <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

  <script src="../Bootstrap/js/bootstrap.js"></script>
  <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

  <script src="../Bootstrap/js/moment.js"></script>
  <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>
</head>

<body style="padding-left:50px;">
<center>
  <font size="+1"><b><u>Purchased Summary</u></b></font>
</center>
<br>

<form action="Purchases/PurchSumItem.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px">
      <button type="button" class="btn btn-danger btn-block" id="btnView">
        <span class="glyphicon glyphicon-search"></span> View Report
      </button>
    </td>
    <td width="150" style="padding-left:10px"><b>Filter By: </b></td>
    <td style="padding:2px">
			<div class="col-xs-4 nopadding">	
			  <select name="seltyp" id="seltyp" class="form-control input-sm" onChange="setact(this.value);">            
          <option value="Purchases/PurchSumItem">Per Item</option>
          <option value="Purchases/PurchSumSupp">Per Supplier</option>
          <option value="Purchases/PurchSumInv">Per Transaction</option>
          <option value="Purchases/PurchSumMonth">Per Month</option>           
        </select>
      </div>	
      <div class="col-xs-4 nopadwleft">
          <select id="sleposted" name="sleposted" class="form-control input-sm selectpicker"  tabindex="4">
              <option value="">All Transactions</option>   
              <option value="1">Posted</option>      
              <option value="0">UnPosted</option>           
          </select> 
      </div>  
   </td>
  </tr>
  <tr>
    <td rowspan="2" valign="top" width="50" style="padding:2px">
      <button type="button" class="btn btn-success btn-block" id="btnexcel">
        <i class="fa fa-file-excel-o"></i> To Excel
      </button>
    </td>
    <td style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">
      <div class="col-xs-12 nopadding" id="datezpick">

        <div class="form-group nopadding">
            <div class="col-xs-8 nopadding">
            <div class="input-group input-large date-picker input-daterange">
                <input type="text" class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>">
                <span class="input-group-addon">to </span>
                <input type="text" class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>">
            </div>
            </div>	
        </div>

      </div>  


      <div class="col-xs-3 nopadding" id="monthpick" style="display:none"> 
        <select name="selmonth" id="id" class="form-control input-sm">
          <?php 
            $now = date("Y");
            //$varyr = $now - 2014;					
            for ($x=2023; $x<=$now; $x++){
          ?>
            <option value="<?php echo $x;?>" <?php if($x==$now){echo "selected";}?>><?php echo $x;?></option>
          <?php 
            } 
          ?>
        </select>
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

    $('#btnView').on("click", function(){
      $dval = $("#seltyp").val();
      $('#frmrep').attr("action", $dval+".php");
      $('#frmrep').submit();
    });

    $('#btnexcel').on("click", function(){
      $dval = $("#seltyp").val();
      $('#frmrep').attr("action", $dval+"_xls.php");
      $('#frmrep').submit();
    });

});

function setact(x){
	document.getElementById("frmrep").action = x;
	
	if(x=="Purchases/PurchSumMonth"){
		document.getElementById("datezpick").style.display = "none";
		document.getElementById("monthpick").style.display = "inline";
	}
	else{
		document.getElementById("monthpick").style.display = "none";
		document.getElementById("datezpick").style.display = "inline";
	}
}
</script>
