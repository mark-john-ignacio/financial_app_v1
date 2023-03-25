<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesDetailed.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

?>

<html>
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
<title>Coop Financials</title>
</head>

<body style="padding-left:50px;">
<center><font size="+1"><b><u>Sales Detailed</u></b></font></center>
<br>

<form action="Sales/SalesDetailed.php" method="post" name="frmrep" id="frmrep" target="_blank">

<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="70" style="padding:2px">
        <button type="button" class="btn btn-danger btn-block" id="btnView">
            <span class="glyphicon glyphicon-search"></span> View Report
        </button>
    </td>
    <td style="padding-left:10px"><b>Item Type: </b></td>
    <td style="padding:2px">
    <div class="col-xs-8 nopadding">
    			<select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
                <option value="">All Items</option> 
                    <?php
                $sql = "select * from groupings where compcode='$company' and ctype='ITEMTYP' order by cdesc";
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
    <td rowspan="3" valign="top" style="padding:2px">
        <button type="button" class="btn btn-success btn-block" id="btnexcel">
            <i class="fa fa-file-excel-o"></i> To Excel
        </button>
    </td>
    <td style="padding-left:10px"><b>Customer Type: </b></td>
    <td style="padding:2px">
    <div class="col-xs-8 nopadding">
    			<select id="selcustype" name="selcustype" class="form-control input-sm selectpicker"  tabindex="4">
                <option value="">All Customers</option> 
                    <?php
                $sql = "select * from groupings where compcode='$company' and ctype='CUSTYP' order by cdesc";
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
    <td style="padding-left:10px"><b>Transaction Type: </b></td>
    <td style="padding:2px">
        <div class="col-xs-4 nopadding">
    	    <select id="seltrantype" name="seltrantype" class="form-control input-sm selectpicker"  tabindex="4">
                <option value="">All Transactions</option>   
                <option value="Trade">Trade</option>      
                <option value="Non-Trade">Non-Trade</option>           
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
    <td style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding" id="datezpick">
        <div class="col-xs-3 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />

		</div>
        
        <div class="col-xs-1 nopadding" style="vertical-align:bottom;" align="center">
        	<label style="padding:1px;">TO</label>
        </div>
 
         <div class="col-xs-3 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" />

		</div>

     </div>   

        
         <div class="col-xs-3 nopadding" id="monthpick" style="display:none">
			<select name="selmonth" id="id" class="form-control input-sm">
            	<?php 
					$now = date("Y");
					//$varyr = $now - 2014;
					
					for ($x=2022; $x<=$now; $x++){
				?>
                	<option value="<?php echo $x;?>" <?php if($x==$now){echo "selected";}?>><?php echo $x;?></option>
                <?php } ?>
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
        $('#frmrep').attr("action", "Sales/SalesDetailed.php");
        $('#frmrep').submit();
      });

      $('#btnexcel').on("click", function(){
        $('#frmrep').attr("action", "Sales/SalesDetailed_xls.php");
        $('#frmrep').submit();
      });
  });
</script>