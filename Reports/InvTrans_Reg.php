
<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvTrans_Reg.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

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
<center><font size="+1"><b><u>Inventory Transfer - Register</u></b></font></center>
<br>

  <form action="Inventory/InvTrans_Reg.php" method="post" name="frmrep" id="frmrep" target="_blank">
    <table width="100%" border="0" cellpadding="2">
      <tr>
        <td valign="top" width="50" style="padding:2px">
        <button type="submit" class="btn btn-danger btn-block" id="btnsales">
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
        <td valign="top" width="50" style="padding:2px">&nbsp;</td>
        <td style="padding-left:10px"><b>Item Type: </b></td>
        <td style="padding:2px">
          <div class="col-xs-8 nopadding">
                    <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
                      <option value="">ALL</option>

                    <?php
                        $sql = "select * from groupings where ctype='ITEMTYP' order by cdesc";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                    ?>   
                        <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                    <?php
                        }                        
                    ?>     
                    </select>
                    </div>
        </td>
      </tr>
      <tr>
        <td valign="top" width="50" style="padding:2px">&nbsp;</td>
        <td style="padding-left:10px"><b>Item Classification: </b></td>
        <td style="padding:2px">
          <div class="col-xs-8 nopadding">
          <select id="selclass" name="selclass" class="form-control input-sm selectpicker"  tabindex="4">
          <option value="">ALL</option>
                    <?php
                        $sql = "select * from groupings where ctype='ITEMCLS' order by cdesc";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                    ?>   
                        <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                    <?php
                        }                                               
                    ?>     
                    </select>
                    </div>
        </td>
      </tr>

      <tr>
        <td valign="top" width="50" style="padding:2px">&nbsp;</td>
        <td style="padding-left:10px"><b>Warehouse IN: </b></td>
        <td style="padding:2px">
          <div class="col-xs-8 nopadding">
          <select id="selclass" name="selclass" class="form-control input-sm selectpicker"  tabindex="4">
          <option value="">ALL</option>
                    <?php
                        $sql = "select * from locations where compcode='$company' order by cdesc";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                    ?>   
                        <option value="<?php echo $row['nid'];?>"><?php echo $row['cdesc']?></option>
                    <?php
                        }                                               
                    ?>     
                    </select>
                    </div>
        </td>
      </tr>

      <tr>
        <td valign="top" width="50" style="padding:2px">&nbsp;</td>
        <td style="padding-left:10px"><b>Warehouse Out: </b></td>
        <td style="padding:2px">
          <div class="col-xs-8 nopadding">
          <select id="selclass" name="selclass" class="form-control input-sm selectpicker"  tabindex="4">
          <option value="">ALL</option>
                    <?php
                        $sql = "select * from locations where compcode='$company' order by cdesc";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                    ?>   
                        <option value="<?php echo $row['nid'];?>"><?php echo $row['cdesc']?></option>
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

  });
  
</script>

