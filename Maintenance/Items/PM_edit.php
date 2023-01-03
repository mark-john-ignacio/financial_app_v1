<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PM_edit.php";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');

				$company = $_SESSION['companyid'];
				$cbatchno = $_REQUEST['txtctranno'];
				
				$sql = "SELECT deffectdate, cversion, cremarks, ctranno, lapproved, lcancelled FROM `items_pm` WHERE compcode='$company' and cbatchno='$cbatchno'";
				$sqlhead=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				if (mysqli_num_rows($sqlhead)!=0) {
					$dataver = array();
					$dataverid = array();
					$cnt = 0;
					$str = "";
					
					while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
						$dEffect = $row['deffectdate'];
						$cremarks = $row['cremarks'];
						$lPosted = $row['lapproved'];
						$lCancelled = $row['lcancelled'];
						
						$dataver[] = $row['cversion'];
						$dataverid[] = $row['ctranno'];
						$cnt = $cnt + 1;
						
						if($cnt > 1){
							$str = $str.";";
						}
						
						$str = $str.$row['cversion'];
					}
				}
				
				//echo $dEffect."<br>";
				
				//echo date_format(date_create($dEffect), "m/d/Y");

function getprice($ver,$itmno,$unitz){
	global $company;
	global $con;
	
		$resultqry = mysqli_query ($con, "SELECT nprice from `items_pm_t` where compcode='$company' and ctranno='$ver' and citemno='$itmno' and cunit='$unitz'"); 
	
	if(mysqli_num_rows($resultqry)!=0){
		$rowqry = mysqli_fetch_assoc($resultqry);
		
		return $rowqry['nprice'];
	}
	
}
?>
<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css"> 
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/jquery.numeric.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

</head>

<body style="padding:5px;">

<form name="frmITEM" id="frmITEM" method="post">
	<fieldset>
    	<legend>Price Matrix Details (<?php echo $cbatchno;?>)</legend>
 	
    <input type='hidden' id="hdnversion" name="hdnversion" value="<?php echo $str;?>" />
    
     <div class="col-xs-12 nopadwdown">
        <div class="col-xs-1 nopadding">
        	<b>Description: </b>
        </div>
        <div class="col-xs-5 nopadwright2x">
        	<input type='text' class="form-control input-sm" id="txtcdescription" name="txtcdescription" value="<?php echo $cremarks; ?>" autocomplete="off" placeholder="<?php echo $palceh1; ?>" />
        </div>

       	 <div class="col-xs-4 nopadding" style="text-align:right">

      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">

        	    <div id="salesstat">
				<?php
                if($lCancelled==1){
                    echo "<font color='#FF0000'><b>CANCELLED</b></font>";
                }
                
                if($lPosted==1){
                    echo "<font color='#FF0000'><b>POSTED</b></font>";
                }
                ?>
                </div>

        </div>


    </div>
    
 	<div class="col-xs-12 nopadwdown">
    
        <div class="col-xs-1 nopadding">
        	<b>Effect Date: </b>
        </div>
        <div class="col-xs-3 nopadwright2x">
          <div class="col-xs-8 nopadding">
          <?php
          	 $date_now = date('m/d/Y');
			 $date2    = date_format(date_create($dEffect),"m/d/Y");
			//echo $date_now . " : " . $date2;
			if ($date_now > $date2) {
					$palceh = 'Pick NEW Date';
					$palceh1 = "Your old effectivity date (".date_format(date_create($dEffect), "m/d/Y").") is not allowed!";
				}else{
					$palceh = "Pick Date";
					$palceh1 = "Enter a description for your price matrix.";
				}
		  ?>
			<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $date2; ?>" placeholder="<?php echo $palceh; ?>" />
          </div>
        </div>
    </div>
    
    <div class="col-xs-12 nopadwdown">
    &nbsp;
    </div>

    <div class="col-xs-12 nopadwdown">
        <div class="col-xs-2 nopadwright2x">
			<input type='text' class="form-control input-sm" id="txtcitmno" name="txtcitmno" value="" placeholder="Enter product code..." autocomplete="off"/>
       		<input type='hidden' id="hdncunit" name="hdncunit" value="" />
       </div>
        <div class="col-xs-4 nopadding">
			<input type='text' class="form-control input-sm" id="txtcitmdesc" name="txtcitmdesc" value="" placeholder="Enter product description..." autocomplete="off" />
        </div>

        <div class="col-xs-6 nopadwleft">
                <div id="itmerradd"></div>
        </div>

    </div>


         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 350px;
					text-align: left;
					overflow: auto">
    
             <table width="100%" border="0" class="table table-hover nopadding" id="myTable">
             <thead>
                  <tr>
					<th scope="col" width="50">&nbsp;</th>
                    <th scope="col" width="120"><b>Item Code</b></th>
                    <th scope="col"><b>Item Desc</b></th>
                    <th scope="col" width="100"><b>UOM</b></th>
                    
                    <?php
                        for($i=0; $i<=$cnt-1; $i++){
                    ?>
                    <th scope="col" width="95"><?php echo $dataver[$i]; ?>
                    	<input type="hidden" name="<?php echo "ID".$dataver[$i]; ?>" id="<?php echo "ID".$dataver[$i]; ?>" value="<?php echo $dataverid[$i]; ?>" />
                    </th>
                    <?php
                        }
                    ?>
    				<th scope="col" width="10">&nbsp;</th>
                  </tr>
              </thead>
              <tbody>
              
              	<?php
                $sql = "SELECT A.citemno, C.citemdesc ,A.cunit, A.nident, C.cunit as cmainunit, D.cDesc as cunitdesc FROM `items_pm_t` A left join `items_pm` B on A.compcode=B.compcode and A.ctranno=B.ctranno left join `items` C on A.compcode=C.compcode and A.citemno=C.cpartno left join groupings D on C.compcode=D.compcode and C.cunit=D.ccode WHERE A.compcode='$company' and B.cbatchno='$cbatchno' Group By A.citemno,C.citemdesc,A.cunit,A.nident Order by A.nident";
				$sqlbodz=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				$cntdets = 0;	
				if (mysqli_num_rows($sqlbodz)!=0) {
					while($row2 = mysqli_fetch_array($sqlbodz, MYSQLI_ASSOC)){
						$cntdets = $cntdets + 1;

				?>
                	<tr>
                        <td><?php echo $cntdets;?>.</td>
                    	<td>
                        <input type="hidden" name="txtcprtno" id="txtcprtno<?php echo $cntdets;?>" value="<?php echo $row2['citemno'];?>" />
                        <?php echo $row2['citemno'];?>
                        </td>
                        <td><?php echo $row2['citemdesc'];?></td>
                        <td>
                        <div class="input-group nopadding"><input type="text" class="form-control input-xs" name="txtcprtunit" id="txtcprtunit<?php echo $cntdets;?>" value="<?php echo $row2['cunit'];?>" readonly><span class="input-group-addon input-xs primary"> <i class="glyphicon glyphicon-refresh" data-id="<?php echo $row2['citemno'];?>" data-desc="<?php echo $row2['citemdesc'];?>" data-unt="<?php echo $row2['cunit'];?>" name="uomaddon" id="uomaddon<?php echo $cntdets;?>"> </i></span></div>
                        </td>

                         <?php
                        	for($z=0; $z<=$cnt-1; $z++){
								$priceval = getprice($dataverid[$z],$row2['citemno'],$row2['cunit']);
                    	 ?>                        
                        <td> 
                        <input type="text" class="numeric form-control input-xs" name="<?php echo $dataver[$z];?>" id="<?php echo $dataver[$z];?>" required value="<?php echo $priceval;?>" autocomplete="off" />
                        </td>
                        <?php
							}
						?>
                        <td>
                        <input type="button" class="btn btn-xs btn-danger" value="Delete" id="btndel<?php echo $cntdets;?>" name="btndel" />
                        </td>
					</tr>
             <script>
				$("#btndel<?php echo $cntdets;?>").on('click', function() {
					$(this).closest('tr').remove();
				
					$('#myTable td:first-child').each(function(index){
					  //alert($(this).text() + " to " + index);
					  var indx = parseInt(index) + 1;
					  $(this).text(indx + ".");
					});

					$("#myTable > tbody > tr").each(function(index) {	
						var indx = parseInt(index) + 1;
						
						$(this).find("input[type='hidden'][name='txtcprtno']").attr("id", "txtcprtno"+indx);
						$(this).find("input[name='txtcprtunit']").attr("id", "txtcprtunit"+indx);
						$(this).find("button[name='btndel']").attr("id", "btndel"+indx);
						
					});
					
				});
				
				$("#uomaddon<?php echo $cntdets;?>").on("mouseover", function(index) {
					$(this).css('cursor','pointer');
				});
				
				$("#uomaddon<?php echo $cntdets;?>").on("click", function() {
					var idxchk = $(this).closest('td').parent()[0].sectionRowIndex;
					var x = $(this).attr("data-id");
					var y = $(this).attr("data-desc");
					var u = $(this).attr("data-unt");
					var zun = "";
					
					$('#UOMListHdr').html("UOM List: " + x + " - " + y);
					$('#unittbls tbody').empty();
					
					$.ajax({
						async: false,
						type: "POST",
						dataType: 'json',
						url: "th_loaduomperitm.php",
						data: { id: x },
						success: function (data) {
							
							console.log(data);
                       		$.each(data,function(index,item){
							var rmkschk = "";
								
								$("#myTable > tbody > tr").each(function(index) {	
									
										var varxitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
										var varxunt = $(this).find("input[name='txtcprtunit']").val();
										
										if(varxitm==x && varxunt==item.id){
											var ux = item.id;
											u = u.trim();
											ux = ux.trim();
											
											if(u == ux){
												rmkschk = "<i>Selected!</i>";
											}
											else{
												rmkschk = "<i>Existing in details!</i>";
											}
											
											return false;
										}
									
								});
								
								
								if(rmkschk==""){
									var tdval = "<a href=\"javascript:;\" onclick=\"setuom('<?php echo $cntdets;?>', '"+item.id+"')\">" + item.id + "</a>";
								}
								else{
									var tdval = item.id;
								}
								
								$("<tr>").append(
								$("<td>").html(tdval),
								$("<td>").text(item.name), 
								$("<td>").text(item.fact), 
								$("<td>").html(rmkschk)
								).appendTo("#unittbls tbody");
							});

						}
					});


					$('#UOMModal').modal('show');
				});
				

				$("input.numeric").numeric({decimalPlaces: 4, negative: false});
				$("input.numeric").on("click", function () {
					$(this).select();
				});
					</script>
                <?php
					}
				}
				?>
              </tbody>
            </table>
            
         </div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>

		<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='PM.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

    	<button type="button" class="btn btn-default btn-sm" id="btnNew" name="btnNew">New<br>(F1)</button>
 
     <button type="button" class="btn btn-danger btn-sm" onClick='document.getElementById("frmedit").submit();' id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
   
        <button type="button" class="btn btn-warning btn-sm" onClick="enabled();" id="btnEdit" name="btnEdit"> Edit<br>(CTRL+E) </button>

    	<button type="button" class="btn btn-success btn-sm" name="btnSave" id="btnSave" onClick="return chkform();">Save<br> (CTRL+S)</button>
    
 
    </td>
    
    </tr>
</table>

        </fieldset>
</form>


<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div>
               <div class="alert alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- 2) UOM -->
<div class="modal fade" id="UOMModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">

    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="UOMListHdr">UOM List</h5>
            </div>
            
            <div class="modal-body" style="height:30vh">
            	<table width="100%" border="0" class="table table-small table-highlight" id="unittbls">
                 <thead>
                  <tr>
                    <th scope="col" width="80">UOM</th>
                    <th scope="col" width="130">Desc</th>
                    <th scope="col" width="100">Factor</th>
                    <th scope="col">&nbsp;</th>
                  </tr>
                 </thead>
                 <tbody>
                 </tbody>
                </table>

			</div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>

<!-- Modal PICK VERSIONS -->
<div class="modal fade" id="myPickMod" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Pick PM Version</b></h5>        
      </div>

	  <div class="modal-body" style="height: 20vh">
    
         <div class="col-xs-12 nopadding">
			<div class="alert alert-danger nopadding" id="add_errpick"></div>        
         </div>   

			
        <div class="col-xs-12 nopadwtop">  
 
                    <div class="col-xs-1 nopadding">
                       &nbsp; 
                    </div>           
                    <div class="col-xs-3 nopadding">
                       <b>Version Code</b> 
                    </div>
                    <div class="col-xs-5 nopadwleft">
                      <b>Version Description</b>  
                    </div>
                    			
        </div>   
        
            <!-- BODY -->
                <div style="height:15vh; display:inline" class="col-lg-12 nopadding pre-scrollable" id="TblPickver">
                </div> 
                 

	</div>
    
 	<div class="modal-footer">
    			<button type="button" id="btnproceed" name="btnproceed" class="btn btn-success btn-sm">Proceed</button>
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
	</div>
    
    </div>
  </div>
</div>
<!-- Modal -->		


<form method="post" name="frmnew" id="frmnew" action="PM_new.php">
	<input type="hidden" name="hdnvers" id="hdnvers" value="">
</form>


<form method="post" name="frmedit" id="frmedit" action="PM_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cbatchno; ?>">
</form>

</body>
</html>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	 
	 if(e.keyCode == 83 && e.ctrlKey) { //CTRL+S
	    e.preventDefault();
		return chkform();
	  }
	 else if(e.keyCode == 112) { //F1
		if(document.getElementById("btnNew").className=="btn btn-default btn-sm"){
			e.preventDefault();
			$("#btnNew").click();
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL+E
		if(document.getElementById("btnEdit").className=="btn btn-warning btn-sm"){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL+Z
		if(document.getElementById("btnUndo").className=="btn btn-danger btn-sm"){
			e.preventDefault();
			document.getElementById("frmedit").submit();
		}
	  }
	  else if(e.keyCode == 27){//ESC	  
		if(document.getElementById("btnMain").className=="btn btn-primary btn-sm"){
			e.preventDefault();
			window.location.href='PM.php';
		}
	  }
	  
	});


$(function() {  
			disabled();
			
			$("input.numeric").numeric({decimalPlaces: 4, negative: false});
			$("input.numeric").on("click", function () {
				$(this).select();
			});
           
           // Bootstrap DateTimePicker v4
		   $('#date_delivery').datetimepicker({
			 	format: 'MM/DD/YYYY',  
		   });
		   
		$('#txtcitmdesc').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: {
						query: $("#txtcitmdesc").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return item.value;
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#txtcitmdesc').val(item.value).change(); 
				$('#txtcitmno').val(item.id); 
				$("#hdncunit").val(item.cunit);
				
				additm('txtcitmdesc');
			}
		
		});
		
		$("#txtcitmno").on("keydown", function (e){
			if(e.keyCode==13){
				e.preventDefault();
				
				$.ajax({
					url: "th_productid.php",
					dataType: "json",
					async: false,
					data: { query: $("#txtcitmno").val() },
					success: function (data) {
                      console.log(data);
					  $.each(data,function(index,item){
						// alert(item.id); 
						//$('#txtcitmno').val(item.id).change(); 
						$('#txtcitmdesc').val(item.value); 
						$("#hdncunit").val(item.cunit);
						
						additm('txtcitmno');

					  });
					}
				});

			}
		});

		$("#btnNew").on("click", function() {
			loadversionspick();
			$('#myPickMod').modal('show');
			
		
			//location.href = "PM_new.php";
		});


		$("#btnproceed").on("click", function() {
			var anyBoxesChecked = 0;
			var vlz = "";
						
			$("input[name='chkpricever[]']").each( function () {
				if ($(this).is(":checked")) {
					anyBoxesChecked = anyBoxesChecked + 1;
					
					if(anyBoxesChecked>1){
						vlz=vlz+";";
					}
					
					vlz=vlz+$(this).val();
				}			
			});
			
			if(anyBoxesChecked==0 || vlz==""){
				$("#add_errpick").html("<b>ERROR: </b> Please select atleast 1 before you proceed.");
				$("#add_errpick").show();
			}else{
				
				$("#hdnvers").val(vlz);
				$("#frmnew").submit();
			}

		});

		

});

function additm(xid){
	var count = $('#myTable tr').length;
	var uomchk = "";
	var cnt = 0;
								$("#myTable > tbody > tr").each(function(index) {	

										var varxitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
										var varxunt = $(this).find("input[name='txtcprtunit']").val();
										
										
										if(varxitm==$('#txtcitmno').val()){
											cnt = cnt + 1;
											if(cnt > 1){
												uomchk = uomchk + ",";
											}
											
											uomchk = uomchk + varxunt;
										}
									
								});

								$.ajax({
									async: false,
									type: "POST",
									dataType: 'text',
									url: "th_mainuom.php",
									data: { id: $('#txtcitmno').val(), uomzx: uomchk },
									success: function (data) {

										if(data.trim()=="0"){
											$('#hdncunit').val("");
										}
										else{
											$('#hdncunit').val(data);
										}
									}
								});

if($('#hdncunit').val()!=""){
	var detz = "";
	var itm = "<td><input type=\"hidden\" name=\"txtcprtno\" id=\"txtcprtno"+count+"\" value=\""+$('#txtcitmno').val()+"\" />" + $('#txtcitmno').val() + "</td>";
	var cdesc = "<td>" + $('#txtcitmdesc').val() + "</td>";
	var cunit = "<td><div class=\"input-group nopadding\"><input type=\"text\" class=\"form-control input-xs\" name=\"txtcprtunit\" id=\"txtcprtunit"+count+"\" value=\""+$('#hdncunit').val()+"\" readonly><span class=\"input-group-addon input-xs primary\"> <i class=\"glyphicon glyphicon-refresh\" data-id=\""+$('#txtcitmno').val()+"\" data-desc=\""+$('#txtcitmdesc').val()+"\" data-unt=\""+$('#hdncunit').val()+"\" name=\"uomaddon\" id=\"uomaddon"+count+"\"> </i></span></div></td>";
	var del = "<td><input type=\"button\" class=\"btn btn-xs btn-danger\" value=\"Delete\" id=\"btndel"+count+"\" name=\"btndel\" /></td>";
	
	var x = $('#hdnversion').val();
	var arrsplit =  x.split(";");
	
	var cnt = arrsplit.length;
	
	for(var i = 0; i < cnt; i++) {
		detz = detz + "<td> <input type=\"text\" class=\"numeric form-control input-xs\" name=\"" + arrsplit[i] + "\" id=\"" + arrsplit[i] + "\" required autocomplete=\"off\" /></td>";
	}
	
	$('#myTable > tbody:last-child').append('<tr>' + "<td>"+count+". </td>" + itm + cdesc + cunit + detz + del + '</tr>');
	
					
				$("#btndel"+count).on('click', function() {
					$(this).closest('tr').remove();
				
					$('#myTable td:first-child').each(function(index){
					  //alert($(this).text() + " to " + index);
					  var indx = parseInt(index) + 1;
					  $(this).text(indx + ".");
					});

					$("#myTable > tbody > tr").each(function(index) {	
						var indx = parseInt(index) + 1;
						
						$(this).find("input[type='hidden'][name='txtcprtno']").attr("id", "txtcprtno"+indx);
						$(this).find("input[name='txtcprtunit']").attr("id", "txtcprtunit"+indx);
						$(this).find("button[name='btndel']").attr("id", "btndel"+indx);
						
					});
					
				});
				
				$("#uomaddon"+count).on("mouseover", function(index) {
					$(this).css('cursor','pointer');
				});
				
				$("#uomaddon"+count).on("click", function() {
					var idxchk = $(this).closest('td').parent()[0].sectionRowIndex;
					var x = $(this).attr("data-id");
					var y = $(this).attr("data-desc");
					var u = $(this).attr("data-unt");
					var zun = "";
					
					$('#UOMListHdr').html("UOM List: " + x + " - " + y);
					$('#unittbls tbody').empty();
					
					$.ajax({
						async: false,
						type: "POST",
						dataType: 'json',
						url: "th_loaduomperitm.php",
						data: { id: x },
						success: function (data) {
							
							console.log(data);
                       		$.each(data,function(index,item){
							var rmkschk = "";
								
								$("#myTable > tbody > tr").each(function(index) {	
									
										var varxitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
										var varxunt = $(this).find("input[name='txtcprtunit']").val();
										
										if(varxitm==x && varxunt==item.id){
											var ux = item.id;
											u = u.trim();
											ux = ux.trim();
											
											if(u == ux){
												rmkschk = "<i>Selected!</i>";
											}
											else{
												rmkschk = "<i>Existing in details!</i>";
											}
											
											return false;
										}
									
								});
								
								
								if(rmkschk==""){
									var tdval = "<a href=\"javascript:;\" onclick=\"setuom('"+count+"', '"+item.id+"')\">" + item.id + "</a>";
								}
								else{
									var tdval = item.id;
								}
								
								$("<tr>").append(
								$("<td>").html(tdval),
								$("<td>").text(item.name), 
								$("<td>").text(item.fact), 
								$("<td>").html(rmkschk)
								).appendTo("#unittbls tbody");
							});

						}
					});


					$('#UOMModal').modal('show');
				});
				

				$("input.numeric").numeric({decimalPlaces: 4, negative: false});
				$("input.numeric").on("click", function () {
					$(this).select();
				});
								
				$("#itmerradd").attr("class","");
				$("#itmerradd").html("");
				$("#itmerradd").hide();

}
else{
	$("#AlertMsg").html("NO more available UOM to add!");
	$('#alertbtnOK').show();
	$("#AlertModal").modal('show');

}
				$('#txtcitmdesc').val("").change(); 
				$('#txtcitmno').val(""); 
				$("#hdncunit").val("");
				
				$('#'+xid).focus();

	
}

function setuom(x, y){
	$("#txtcprtunit"+x).val(y);
	$("#uomaddon"+x).attr("data-unt", y);
	$('#UOMModal').modal('hide');
	
}

function chkform(){
var rowCount = $('#myTable tr').length

if(rowCount==1 || $("#date_delivery").val()==""){
	$("#AlertMsg").html("Transaction cannot be saved without details.<br>Effect Date is required");
	$('#alertbtnOK').show();
	$("#AlertModal").modal('show');
}else{
	
//check for numeric textboxes without value
var valuenull = "";
$("input.numeric").each(function() {
    if($(this).val()==""){
		valuenull = "False";
	}
});

	if(valuenull == "False"){
		$("#AlertMsg").html("Blank price is not allowed");
		$('#alertbtnOK').show();
		$("#AlertModal").modal('show');
	}
	else{
		var x = $('#hdnversion').val();
		var arrsplit = x.split(";");
				
		var cnt = arrsplit.length;
		var issaved = "";

		
			var txtdeffect = $("#date_delivery").val();
			var txtcdesc = $("#txtcdescription").val();
			var trancode = "";
			
			for(var i = 0; i < cnt; i++) {				
					//UPDATING HEADERS
					trancode = $("#ID"+arrsplit[i]).val();
					
					$.ajax ({
						url: "th_updatepm.php",
						data: { deffect: txtdeffect, desc: txtcdesc, typ: arrsplit[i], tran: trancode },
						async: false,
						beforeSend: function(){
							$("#AlertMsg").html("<b>UPDATING PRICE MATRIX ("+arrsplit[i]+"): </b> Please wait a moment...");
							$('#alertbtnOK').hide();
							$("#AlertModal").modal('show');
						},
						success: function( data ) {
							if(data.trim()!="False"){
								trancode = data.trim();
					
								//REINSERT DETAILS
								var nident = 0;
								
								$("#myTable > tbody > tr").each(function() {	
									
									nident = nident + 1;
									
									var txtcitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
									var txtcuom = $(this).find("input[name='txtcprtunit']").val();
								
									var valz = $(this).find("input[name='"+arrsplit[i]+"']").val();
									//alert("code:"+ txtcitm + "&uom:" + txtcuom);
									
											$.ajax ({
												url: "th_savepmt.php",
												data: { code: txtcitm, uom: txtcuom, val: valz, tran: trancode, ident: nident },
												async: false,
												success: function( data ) {
													if(data.trim()!="True"){
														issaved = data.trim()+"\n";
													}
												}
											});
									 
								});


							}
						}
					});

			}

	if(issaved==""){
		$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading pm list... <br> Please wait!");
		$('#alertbtnOK').hide();
													
		setTimeout(function() {
			$("#AlertMsg").html("");
			$('#alertbtnOK').hide();
			$('#AlertModal').modal('hide');

					$("#txtctranno").val($("#txtctranno").val());
					$("#frmedit").submit();

		}, 3000); // milliseconds = 3seconds

	}else{
		$("#AlertMsg").html(issaved);
	}
	
	}
			
 }

}

	function loadversionspick(){
			$.ajax ({
				url: "th_loadpmver.php",
				async: false,
				dataType: 'json',
				success: function( data ) {
                      console.log(data);
					  $.each(data,function(index,item){
						  
						  var divhead = "<div class=\"itmverdet col-xs-12 nopadwtop\">";
						  var divcohkbox = "<div class=\"col-xs-1 nopadding\"> <div class\"checkbox\"> <input type=\"checkbox\" name=\"chkpricever[]\" name=\"id[]\" value=\""+item.id+ "\"> </div> </div>";
						  var divcode = "<div class=\"col-xs-3 nopadding\"> "+item.id+ "</div>";
						  var divdet = "<div class=\"col-xs-5 nopadwleft\"> "+item.name+ "</div>";
						  
						  var divend = "</div>";
						  
						  $("#TblPickver").append(divhead + divcohkbox + divcode + divdet + divend);
					  });
				}
			
			});
		
	}



function disabled(){

	$("#frmITEM :input, label").attr("disabled", true);
	
	
	$("#txtcpartno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("itmerradd").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
		document.getElementById("itmerradd").style.color = "#FF0000";
		
	}
	else{

		$("#frmITEM :input, label").attr("disabled", false);
		$('#date_delivery').data('DateTimePicker').destroy();
			
			$("#txtcpartno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

	        $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				 useCurrent: false,
			   	 minDate: moment(),
           	});

	}

}
	   
</script>