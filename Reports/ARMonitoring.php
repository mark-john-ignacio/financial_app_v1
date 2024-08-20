<?php
  if(!isset($_SESSION)){
    session_start();
  }

  $_SESSION['pageid'] = "ARMonitoring";
  include('../Connection/connection_string.php');
  include('../include/denied.php');
  include('../include/access.php');

  $company = $_SESSION['companyid'];
  $lallowNT = 0;
	/*
  $result=mysqli_query($con,"select * From company where compcode='".$company."'");								
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if($row['compcode'] == $company){
			$lallowNT =  $row['lallownontrade'];
		}
	}
  */

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Myx Financials</title>

  <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
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
    <font size="+1"><b><u>AR Monitoring</u></b></font>
  </center>
  <br>

  <form action="Sales/ARMonitoring.php" method="post" name="frmrep" id="frmrep" target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="submit" class="btn btn-danger btn-block" id="btnView">
            <span class="glyphicon glyphicon-search"></span> View Report
          </button>
        </td>
        <td width="150" style="padding-left:10px"><b>Report Type: </b></td>
        <td style="padding:2px">        
          <div class="col-xs-8 nopadding">
            <select id="selrptnme" name="selrptnme" class="form-control input-sm selectpicker"  tabindex="4">
              <option value="ARMonitoring">Invoices</option>      
              <option value="QuoteMonitoring">Quotation/Billing Uninvoiced</option>           
            </select>                            
          </div>         
        </td>
      </tr>
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="submit" class="btn btn-success btn-block" id="btnexcel">
            <i class="fa fa-file-excel-o"></i> To Excel
          </button>
        </td>
        <td width="150" style="padding-left:10px"><b>Transaction Type: </b></td>
        <td style="padding:2px">
          <div class="col-xs-8 nopadding">
            <select id="selrpt" name="selrpt" class="form-control input-sm selectpicker"  tabindex="4">
              <option value="1">All Posted Transactions</option>      
              <option value="0">All Pending Transactions</option>    
              <option value="2">All Cancelled/Void Transactions</option>        
            </select>                            
          </div> 
        </td>
      </tr>
      <tr>
        <td valign="top" width="50" style="padding:2px">
          &nbsp;
        </td>
        <td width="150" style="padding-left:10px">
          <div id="dtelabel1" style="display:none">
            <select id="seldtetp" name="seldtetp" class="form-control input-sm selectpicker"  tabindex="4">
              <option value="ddate">Bill/Quote Date</option>      
              <option value="dcutdate">Due/Effectivity Date</option>           
            </select> 
          </div>

          <div id="dtelabel2" ><b>Invoice Date: </b></div>
        </td>
        <td style="padding:2px">
          <div class="form-group">
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
$(function(){

	$('.datepick').datetimepicker({
    format: 'MM/DD/YYYY'
  });

  $("#selrptnme").on("change", function(){
    if($(this).val()=="ARMonitoring"){
      $("#dtelabel1").hide();
      $("#dtelabel2").show();
    }else{
      $("#dtelabel1").show();
      $("#dtelabel2").hide();
    }
  });


  $('#btnView').on("click", function(){
    $dval = $("#selrptnme").val();
    $('#frmrep').attr("action", "Sales/"+$dval+".php");
    $('#frmrep').submit();
  });

  $('#btnexcel').on("click", function(){
    $dval = $("#selrptnme").val();
    $('#frmrep').attr("action", "Sales/"+$dval+"_xls.php");
    $('#frmrep').submit();
  });
	
});

function setact(x){
	document.getElementById("frmrep").action = x;
}
</script>
