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

function getprice($ver,$itmno){
	global $company;
	global $con;
	
		$resultqry = mysqli_query ($con, "SELECT nprice from `items_pm_t` where compcode='$company' and ctranno='$ver' and citemno='$itmno'"); 
	
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
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css"> 
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/jquery.numeric.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>

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
          	 $date_now = new DateTime();
			 $date2    = new DateTime($dEffect);
			
			if ($date_now > $date2) {
					$palceh = 'Pick NEW Date';
					$palceh1 = "Your old effectivity date (".date_format(date_create($dEffect), "m/d/Y").") is not allowed!";
				}else{
					$palceh = "Pick Date";
					$palceh1 = "Enter a description for your price matrix.";
				}
		  ?>
			<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($dEffect), "m/d/Y"); ?>" placeholder="<?php echo $palceh; ?>" />
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
                    <th scope="col" width="120"><b>Item Code</b></th>
                    <th scope="col"><b>Item Desc</b></th>
                    <th scope="col" width="80"><b>UOM</b></th>
                    
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
					
				if (mysqli_num_rows($sqlbodz)!=0) {
					while($row2 = mysqli_fetch_array($sqlbodz, MYSQLI_ASSOC)){

				?>
                	<tr>
                    	<td>
                        <input type="hidden" name="txtcprtno" id="txtcprtno" value="<?php echo $row2['citemno'];?>" />
                        <?php echo $row2['citemno'];?>
                        </td>
                        <td><?php echo $row2['citemdesc'];?></td>
                        <td>
                        	<?php
							$selectd = "";
							
							if($row2['cmainunit'] == $row2['cunit']){
								$selectd = "selected";
							}
							else{
								$selectd = "";
							}
							
                        	$uomoptions = "<option value='".$row2['cmainunit']."' ".$selectd.">".$row2['cunitdesc']."</option>";
							
							$resuom = mysqli_query ($con, "SELECT A.cunit, B.cDesc FROM items_factor A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$row2['citemno']."' AND A.cstatus='ACTIVE'"); 

								$varselected = "";
								while($rowuom = mysqli_fetch_array($resuom, MYSQLI_ASSOC)){
									//echo $rowuom['cunit'] ."==". $row2['cunit'] ."<br>";
									if($rowuom['cunit'] == $row2['cunit']){
										$selectd = "selected";
									}
									else{
										$selectd = "";
									}

									 $uomoptions = $uomoptions . "<option value='".$rowuom['cunit']."' ".$selectd.">".$rowuom['cDesc']."</option>";
							
								}

							?>

                        <select class='form-control input-xs' name="txtcprtunit" id="txtcprtunit">
                            <?php echo $uomoptions; ?>
                        </select>
                        
                        </td>
                         <?php
                        	for($z=0; $z<=$cnt-1; $z++){
								$priceval = getprice($dataverid[$z],$row2['citemno']);
                    	 ?>                        
                        <td> 
                        <input type="text" class="numeric form-control input-xs" name="<?php echo $dataver[$z];?>" id="<?php echo $dataver[$z];?>" required value="<?php echo $priceval;?>" autocomplete="off" />
                        </td>
                        <?php
							}
						?>
                        <td>
                        <input type="button" class="btn btn-xs btn-danger" value="Delete" id="del<?php echo $row2['citemno'];?>" />
                        </td>
					</tr>
                    <script>
									$("#del<?php echo $row2['citemno'];?>").on('click', function() {
										$(this).closest('tr').remove();
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
            <div class="modal-content">
               <div class="alert-modal-danger">
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
var isMERON = "";	
//CHECK IF CODE EXIST IN TABLE
$("#myTable > tbody > tr").each(function() {
	var txtcitm = $(this).find("input[name='txtcprtno']").val();
	
	if(txtcitm==$('#txtcitmno').val()){
		isMERON = "TRUE";
	}
	
});

if(isMERON=="TRUE"){
	$("#itmerradd").attr("class","alert alert-danger nopadding");
	$("#itmerradd").html("<b>ERROR: </b> Item already added!");
	$("#itmerradd").show();

}
else{
		var uomoptions = "";
		$.ajax ({
			url: "../th_loaduomperitm.php",
			data: { id: $('#txtcitmno').val() },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					uomoptions = uomoptions + '<option value='+item.id+'>'+item.name+'</option>';
				});
						
											 
			}
		});


	var detz = "";
	var itm = "<td><input type=\"hidden\" name=\"txtcprtno\" id=\"txtcprtno\" value=\""+$('#txtcitmno').val()+"\" />" + $('#txtcitmno').val() + "</td>";
	var cdesc = "<td>" + $('#txtcitmdesc').val() + "</td>";
	var cunit = "<td><select class='form-control input-xs' name=\"txtcprtunit\" id=\"txtcprtunit\">"+uomoptions+"</select></td>";
	var del = "<td><input type=\"button\" class=\"btn btn-xs btn-danger\" value=\"Delete\" id=\"del"+$('#txtcitmno').val()+"\" /></td>";
	
	var x = $('#hdnversion').val();
	var arrsplit =  x.split(";");
	
	var cnt = arrsplit.length;
	
	for(var i = 0; i < cnt; i++) {
		detz = detz + "<td> <input type=\"text\" class=\"numeric form-control input-xs\" name=\"" + arrsplit[i] + "\" id=\"" + arrsplit[i] + "\" required autocomplete=\"off\" /></td>";
	}
	
	$('#myTable > tbody:last-child').append('<tr>'+itm + cdesc + cunit + detz + del + '</tr>');
	
					
				$("#del"+$('#txtcitmno').val()).on('click', function() {
					$(this).closest('tr').remove();
				});

				$("input.numeric").numeric({decimalPlaces: 4, negative: false});
				$("input.numeric").on("click", function () {
					$(this).select();
				});

				$("#itmerradd").attr("class","");
				$("#itmerradd").html("");
				$("#itmerradd").hide();

}

				$('#txtcitmdesc').val("").change(); 
				$('#txtcitmno').val(""); 
				$("#hdncunit").val("");
				
				$('#'+xid).focus();

	
}

function chkform(){
var rowCount = $('#myTable tr').length

if(rowCount==1){
	$("#AlertMsg").html("Transaction cannot be saved without details.");
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
									
									var txtcitm = $(this).find("input[name='txtcprtno']").val();
									var txtcuom = $(this).find("select[name='txtcprtunit']").val();
								
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
				 minDate: new Date(),

           	});

	}

}
	   
</script>