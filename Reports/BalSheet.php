<?php
  if(!isset($_SESSION)){
    session_start();
  }
  $_SESSION['pageid'] = "BalSheet";
  include('../Connection/connection_string.php');
  include('../include/denied.php');
  include('../include/access.php');

  $company = $_SESSION['companyid'];
  $sql = "select * From company";
  $result=mysqli_query($con,$sql);
  $rowcount=mysqli_num_rows($result);
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
  <font size="+1"><b><u>Balance Sheet</u></b></font>
</center>
<br>

  <form action="Accounting/BalSheet.php" method="post" name="frmrep" id="frmrep" target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="submit" class="btn btn-danger" id="btnView">
            <span class="glyphicon glyphicon-search"></span> View Report
          </button>
        </td>
        <td width="90px" style="padding-left:10px"><b>Report Type: </b></td>
        <td style="padding:2px">
          <div class="col-xs-12">
            <div class="col-xs-3 nopadding">
              
              <select id="selrpt" name="selrpt" class="form-control input-sm selectpicker"  tabindex="4">
                <option value="Accounting/BalSheet">Summary</option>   
                <option value="Accounting/BalSheet_Monthly">Monthly</option>               
              </select>
              
            </div>
            <?php
             // if($rowcount > 1){
            ?>
           <!-- <div class="col-xs-5 nopadwleft">
              
              <select id="selconso" name="selconso" class="form-control input-sm selectpicker"  tabindex="4">
                <option value="1">Per Selected Company</option>   
                <option value="2">Consolidate All Company</option>               
              </select>
              
            </div>-->
            <?php
             // }
            ?>
          </div>   
        </td>
      </tr>
      <tr>
        <td valign="top" width="50" style="padding:2px">
          <button type="button" class="btn btn-success btn-block" id="btnexcel">
            <i class="fa fa-file-excel-o"></i> To Excel
          </button>
        </td>
        <td width="90px" style="padding-left:10px"><div id="dtelabel"><b>Date Range: </b></div></td>
        <td style="padding:2px">
          <div id="dterange">
            <div class="form-group nopadding">
              <div class="col-xs-8">
                <div class="input-group input-large date-picker input-daterange">
                  <input type="text" class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>">
                  <span class="input-group-addon">to </span>
                  <input type="text" class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>">
                </div>
              </div>	
            </div>  
          </div>

          <div id="dtemonth" style="display:none">
              <div class="col-xs-12">
                <div class="col-xs-3 nopadding">          
                  <select id="selyr" name="selyr" class="form-control input-sm selectpicker"  tabindex="4">
                    <?php
                      $yrnow = date("Y");
                      $yrstart = 2022;
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
      if($(this).val()=="Accounting/BalSheet_Monthly"){
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
