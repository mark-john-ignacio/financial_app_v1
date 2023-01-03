<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Coop Financials</title>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="../../Bootstrap/css/bootstrap.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="../../global/plugins/bootstrap-datepicker/css/datepicker.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
</head>
<body>
<form method="post" action="ProcessClosing.php" name="frmdte" id="frmdet">
				<div class="col-xs-12 nopadding" id="divprocessingdate">
                 
                    <fieldset>
                    	<legend><font size="-1">Date Range</font></legend>
                        
                              <div class="col-xs-12 nopadding">
        						<div class="col-xs-3 nopadding">From: </div>
                                <div class="col-xs-9 nopadding"> <input type='text' class="form-control input-sm" id="closedate1" name="closedate1" value="<?php echo date("m/d/Y"); ?>" /> </div>
                             </div>

                              <div class="col-xs-12 nopadwtop2x">
        						<div class="col-xs-3 nopadding">To: </div>
                                <div class="col-xs-9 nopadding"> <input type='text' class="form-control input-sm" id="closedate2" name="closedate2" value="<?php echo date("m/d/Y"); ?>" /> </div>
                             </div>
                             
                             <div class="col-xs-12 nopadwtop2x text-center" id="statmsg">
                             	
                             </div>

                    </fieldset>
                    </div>
                    
                    <div id="divprocessing" style="display:none">
                    	<img src="../../images/PGIFT.gif">
                    </div>
                    

					<div class="col-xs-12 nopadding">
                    	<div class="col-xs-6 nopadding"><button type="button" class="btn btn-danger btn-sm btn-block" data-dismiss="modal" id="btnmonthcloseX" name="btnmonthcloseX">CLOSE</button></div>
                        <div class="col-xs-6 nopadding"><button type="submit" class="btn btn-success btn-sm btn-block" id="btnmonthclose" name="btnmonthclose">SUBMIT</button></div>
                     </div>
                        

</form>

<script src="../../global/plugins/jquery.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../bootstrap/js/bootstrap.js" type="text/javascript"></script>
<script src="../../global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script> 

<script>
	  
	  $(function(){
		  $("#closedate1, #closedate2").datepicker({
			  autoclose: true,
              format: 'mm/dd/yyyy',
				// onChangeDateTime:changelimits,
				 //minDate: new Date(),
        	});
			  
	  });
	  
   </script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>