<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Suppliers_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; height:700px">
<form action="Suppliers_newsave.php" name="frmITEM" id="frmITEM" method="post" onSubmit="addrowcnt();">
	<fieldset>
    	<legend>New Supplier</legend>
        
<table width="100%" border="0">
  <tr>
    <td width="150"><b>Suppliers Code</b></td>
    <td style="padding:2px"><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtcitemno" name="txtcitemno" tabindex="1" placeholder="Input Supplier Code.." required style="text-transform:uppercase" /></div><span id="user-result"></span></td>
  </tr>
  <tr>
    <td><b>Suppliers Name</b></td>
    <td style="padding:2px"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Supplier Name.." required style="text-transform:uppercase"/></div></td>
  </tr>
  <tr>
    <td><b>Account Code</b></td>
    <td style="padding:2px">
     <div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="3" placeholder="Search Acct Title.." required></div> &nbsp;&nbsp;
        	<input type="text" id="txtsalesacctD" name="txtsalesacctD" style="border:none; height:30px" readonly></td>
  </tr>
  <tr>
    <td><b>Terms</b></td>
    <td style="padding:2px"><div class="col-xs-2">
      <select id="selterms" name="selterms" class="form-control input-sm selectpicker"  tabindex="4">
        <?php
		$sql = $sql = "select * from parameters where ccode='TERMS' order by norder";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>
        <option value="<?php echo $row['cvalue'];?>"><?php echo $row['cvalue']?></option>
        <?php
				}
				

			?>
      </select>
    </div></td>
  </tr>
</table>


<p>&nbsp;</p>
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">Address Details</a></li>
    <li><a href="#menu1">Contact Details</a></li>
    <li><a href="#menu2">Supplier Products</a></li>
  </ul>

<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">
    <div class="tab-content">
    
         <div id="home" class="tab-pane fade in active" style="padding-left:30px">
             <p>
             
             
             
             </p>
         </div>

         <div id="menu1" class="tab-pane fade" style="padding-left:30px">
             <p>
             b
             </p>
         </div>

         <div id="menu2" class="tab-pane fade" style="padding-left:30px">
             <p>
             c
             </p>
         </div>
    
	</div>
</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> <button type="submit" class="btn btn-success btn-sm" tabindex="14" name="button">SAVE NEW SUPPLIER</button></td>
    </tr>
</table>
</fieldset>
</form>
</body>
</html>

<script type="text/javascript">
$(document).ready(function() {
	
	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });
	
});
</script>
