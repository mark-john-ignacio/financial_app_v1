
<?php
  if(!isset($_SESSION)){
    session_start();
  }
  $_SESSION['pageid'] = "InvSumWh";

  include('../Connection/connection_string.php');
  include('../include/denied.php');
  include('../include/access.php');

  $company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];

  $arrseclist = array();
	$sqlempsec = mysqli_query($con,"select A.section_nid as nid, IFNULL(B.cdesc,'') as cdesc from users_sections A left join locations B on A.section_nid=B.nid where A.UserID='$employeeid' Order By B.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
    if($row0['cdesc']!==""){
		  $arrseclist[] = $row0['nid'];
    }
	}

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
<center><font size="+1"><b><u>Inventory Summary Report</u></b></font></center>
<br>

  <form action="Inventory/InvSummaryWh.php" method="post" name="frmrep" id="frmrep" target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
        <button type="submit" class="btn btn-danger btn-blocked" id="btnsales">
        <span class="glyphicon glyphicon-search"></span> View Report
        </button>
        </td>
        <td style="padding-left:10px"><b>Date Range: </b></td>
        <td style="padding:2px">

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
        </td>
      </tr>
      <tr>
      <tr>
        <td valign="top" width="50" style="padding:2px">&nbsp;</td>
        <td style="padding-left:10px"><b>Section: </b></td>
        <td style="padding:2px">

          <div class="col-xs-12 nopadding">
            <div class="col-xs-8 nopadding">

            <select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
              <?php
                $issel = 0;
                  foreach($rowdetloc as $localocs){
                    if(isset($_REQUEST['cwh'])){
                      if($_REQUEST['cwh']==$localocs['nid']){
                        $issel++;
                      }else{
                        $issel = 0;
                      }
                    }else{
                      $issel++;
                    }
                    
              ?>
                    <option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
              <?php	
                  }						
              ?>
            </select>

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

