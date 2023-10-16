<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "TBal.php";
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

  <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

  <script src="../Bootstrap/js/bootstrap.js"></script>
  <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

  <script src="../Bootstrap/js/moment.js"></script>
  <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:50px;">
  <center>
    <font size="+1"><b><u>Trial Balance</u></b></font>
  </center>
  <br>

  <form action="Accounting/TBal.php" method="post" name="frmrep" id="frmrep" target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="submit" class="btn btn-danger btn-block" id="btnView">
            <span class="glyphicon glyphicon-search"></span> View Report
          </button>
        </td>
        <td width="150" style="padding-left:10px"><b>Report Type: </b></td>
        <td style="padding:2px">
          <div class="col-xs-12 nopadding">
            <div class="col-xs-3 nopadding">
              
              <select id="selrpt" name="selrpt" class="form-control input-sm selectpicker"  tabindex="4">
                <option value="Accounting/TBal">Summary</option>   
                <option value="Accounting/TBal_Monthly">Monthly</option>               
              </select>
              
            </div>
            
          </div>   
        </td>
      </tr>
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="submit" class="btn btn-success btn-block" id="btnexcel">
            <i class="fa fa-file-excel-o"></i> To Excel
          </button>
        </td>
        <td width="150" style="padding-left:10px"><div id="dtelabel"><b>Date Range: </b></div></td>
        <td style="padding:2px">
          <div id="dterange">
            <div class="col-xs-12 nopadding">
              <div class="col-xs-3 nopadding">             
                <input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />              
              </div>
              
              <div class="col-xs-2 nopadding" style="vertical-align:bottom;" align="center">
                <label style="padding:1px;">TO</label>
              </div>
              
              <div class="col-xs-3 nopadding">              
                <input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" />              
              </div>           
            </div>   
          </div>

          <div id="dtemonth" style="display:none">
              <div class="col-xs-12 nopadding">
                <div class="col-xs-3 nopadding">          
                  <select id="selyr" name="selyr" class="form-control input-sm selectpicker"  tabindex="4">
                    <?php
                      $yrnow = date("Y");
                      $yrstart = 2023;
                      for($i=$yrnow; $i>=$yrstart ; $i--){
                    ?>
                      <option value="<?=$i?>"><?=$i?></option>     
                    <?php
                      }
                    ?>
                  </select>           
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

  $("#selrpt").on("change", function(){
    if($(this).val()=="Accounting/TBal_Monthly"){
      $("#dtelabel").html("<b>Year: </b>");
      $("#dterange").hide();
      $("#dtemonth").show();
    }else{
      $("#dtelabel").html("<b>Date Range: </b>");
      $("#dterange").show();
      $("#dtemonth").hide();
    }
  });


  $('#btnView').on("click", function(){
    $dval = $("#selrpt").val();
    $('#frmrep').attr("action", $dval+".php");
    $('#frmrep').submit();
  });

  $('#btnexcel').on("click", function(){
    $dval = $("#selrpt").val();
    $('#frmrep').attr("action", $dval+"_xls.php");
    $('#frmrep').submit();
  });
	
});

function setact(x){
	document.getElementById("frmrep").action = x;
}
</script>
