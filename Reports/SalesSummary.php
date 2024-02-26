<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "SalesSummary.php";

    include('../Connection/connection_string.php');
    include('../include/denied.php');
    include('../include/access.php');

    $company = $_SESSION['companyid'];

    $yr1 = date("Y");
    $sql = "select YEAR(dcutdate) as nyear from sales where compcode='$company' and lcancelled=0 and lvoid=0 order by dcutdate ASC LIMIT 1";
    $result=mysqli_query($con,$sql);	

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $yr1 = $row['nyear'];
    }

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
<center><font size="+1"><b><u>Sales Summary</u></b></font></center>
<br>

<form action="Sales/SalesSumItem.php" method="post" name="frmrep" id="frmrep" target="_blank"> 
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="70" style="padding:2px">
        <button type="button" class="btn btn-danger btn-block" id="btnView">
            <span class="glyphicon glyphicon-search"></span> View Report
        </button>
    </td>
    <td width="150" style="padding-left:10px"><b>Report Type: </b></td>
    <td>
        <div class="col-xs-8 nopadding">	
			<SELECT name="seltyp" id="seltyp" class="form-control input-sm">
            
            	<option value="Sales/SalesSumItem">Per Item</option>
                <option value="Sales/SalesSumCust">Per Customer</option>
                <option value="Sales/SalesSumInv">Per Transaction</option>
                <option value="Sales/SalesSumCustItem">Per Customer/Item</option>
                <option value="Sales/SalesSumMonth">Per Item Monthly</option>
                <option value="Sales/SalesSumCustMonthly">Per Customer Monthly</option>
               <!-- <option value="Sales/SalesSumCutOff.php">Per Customer/CutOFf</option>-->
                
            </SELECT> 
        </div>	
   </td>
  </tr>
  <tr>
    <td rowspan="4" valign="top" style="padding:2px">
        <button type="button" class="btn btn-success btn-block" id="btnexcel">
            <i class="fa fa-file-excel-o"></i> To Excel
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
        <div class="col-xs-8 nopadding">
            <input type="hidden" name="seltrantype" id="seltrantype" value="">
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
            <select name="selmonth" id="selmonth" class="form-control input-sm">
                <?php 
                    $now = date("Y");
                    //$varyr = $now - 2014;  = date("Y");
                    
                    for ($x=$yr1; $x<=$now; $x++){
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
        $dval = $("#seltyp").val();
        $('#frmrep').attr("action", $dval+".php");
        $('#frmrep').submit();
    });

    $('#btnexcel').on("click", function(){
        $dval = $("#seltyp").val();
        $('#frmrep').attr("action", $dval+"_xls.php");
        $('#frmrep').submit();
    }); 

    $('#seltyp').on("change", function(){  
        if($(this).val()=="Sales/SalesSumMonth" || $(this).val()=="Sales/SalesSumCustMonthly"){
            $('#monthpick').show();
            $('#datezpick').hide();
        }else{
            $('#monthpick').hide();
            $('#datezpick').show();
        }
    });
});
</script>
