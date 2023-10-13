<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

//get user details
$arrcompz = array();
$cntzcompany = 0;
$result=mysqli_query($con,"select * From company");								
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$cntzcompany++;
	$arrcompz[] = $row;
	if($row['compcode'] == $company){
		$compname =  $row['compname'];
		$logoname =  str_replace("../","",$row['clogoname']);
		$lallowNT =  $row['lallownontrade'];
		$lallowMRP =  $row['lmrpmodules'];
	}
} 

		$sql = "select * from groupings where ctype='ITEMCLS' LIMIT 1";
		$result=mysqli_query($con,$sql);
		$rowcnt = mysqli_num_rows($result);
		
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$cclascoddef = $row["ccode"];
		
		}
		?>	

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap2.css?v=<?php echo time();?>">
	<link href="../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
	<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

  <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick-theme.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slicksize.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/css/keypadz.css?v=<?php echo time();?>">

	<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../include/autoNumeric.js"></script>

	<script src="../Bootstrap/js/bootstrap.js"></script>
	<script src="../Bootstrap/js/moment.js"></script>

	<style type="text/css">
		.vertical-alignment-helper {
			display:table;
			height: 100%;
			width: 100%;
		}
		.vertical-align-center {
			/* To center vertically */
			display: table-cell;
			vertical-align: middle;
		}
		.modal-content {
			/* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it */
			width:inherit;
			height:inherit;
			/* To center horizontally */
			margin: 0 auto;
		}
		.alert-modal-danger {
			padding: 10px;
			border: 1px solid #888;
			border-radius: 5px;
			color: #a94442;
			background-color: #f2dede;
			border-color: #ebccd1;
		}
		.b-col {
			float: left;
		}
		.digi {
			font-family: 'Share Tech Mono', monospace;
			color: #ffffff;
			color: #017093;
			text-shadow: 0 0 20px #0aafe6, 0 0 20px rgba(10, 175, 230, 0);
			position: relative;
			font-size: 15px;
		}
		.divbak{
			background-color: #019aca;
		}
		.see_offer {
			min-height: 20px;
			background-color: rgba(0,0,0,.5);
    	color: #fff;
			position: absolute;
			bottom: 0;
			width: 100%;
		}

	</style>

	<script>
		$(document).keydown(function(e) {	 
		
			if(e.keyCode == 113) { //F2
		//  alert("saved");
			checkform();
			}
			else if(e.keyCode == 45) { //Insert
		//  alert("pressed");
			InsertQty(); 
			}
			else if(e.keyCode == 36) { //Home
		//  alert("pressed");
			InsertBarcode(); 
			}
			else if(e.keyCode == 46) { //Delete
		//  alert("pressed");
			location.reload(); 
			}  
			else if(e.keyCode == 27) { //Delete
		//  alert("pressed");
			location.href="../main.php";
			}
			else if (e.keyCode == 13 && e.ctrlKey) {
					chksubmit();
				}
			
		});

	</script>
</head>

<body>

<form name="frmWIN" id="frmWIN" action="POS_newsave.php" method="post">

	<div class="row nopadwtop2x" style="background-color: #2d5f8b; height:65px; margin-bottom: 5px !important">

		<div style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
			<a href="<?="//".$_SERVER['SERVER_NAME']."/main.php"?>">
				<img src="../images/LOGOTOP.png" alt="logo" class="logo-default" width="150" height="50" />
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>

		<div class="text-right" style="float: right;display: block;width: 400px;height: 57px;padding-left: 20px;padding-right: 20px; align-self: flex-end;">
			<font size='+3' style='color:#fff'> Point of Sale <a href="javascript:;" name="btnExit" id="btnExit"><i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true" style='color:#fff'></i></a> </font>

			

		</div>
	</div>

	
		<div class="row nopadwtop2x" style="background-color: #f5f5f5;">

			<div class="col-lg-6" style="display:table; padding:10px;">
        
    			<!-- Time user -->
          <div class="row nopadding">
            <div class="digi col-lg-6 nopadding text-left">
              <span class="date">
                Cashier: <?php echo $_SESSION['employeename']; ?>
              </span>                    
						</div>
            <div class="digi col-lg-6 nopadwleft text-right">
              <span class="date"><?=date("F d, Y");?></span>
              <span class="digital-clock time"></span>
            </div>
          </div> 
            
    			<!-- Item barcode search --> 
          <div class="row nopadwtop">
            <div class="input-group margin-bottom-sm">
              <input type="text" name="citemno" id="citemno" class="form-control input-sm " placeholder="(Home) Enter Barcode... (qty*barcode)" autocomplete="off"> 
              <span class="input-group-addon"><i class="fa fa fa-barcode fa-fw"></i></span>
            </div>
          </div>
            
					<div style="height:60vh; overflow-y: auto" class="row nopadwtop">
          	<table id="TblItems" class="TblItems" cellpadding="3px" width="100%" border="0">

								<tr class="divbak">
									<th>Description</th>
									<th>UOM</th>
									<th>Qty</th>
									<th>Price</th>
									<th>Discount</th>
									<th>Amount</th>
									<th>&nbsp;</th>
								</tr>
								<tbody class="tbody">
								</tbody>
                    
    				</table>
            
        	</div>
                
    			<!-- Total
          <div class="row nopadding">
						<div class="col-lg-3 divbak" style="text-align:left; padding:10px; font-size:20px"><b>Total Items:</b></div>
            <div class="col-lg-3 divbak" style="padding:10px; font-size:20px" id="ItmTotQty">0</div>
            <div class="col-lg-2 divbak" style="text-align:right; padding:10px; font-size:20px"><b>Total:</b></div>
            <div class="col-lg-4 divbak">
							<input type="text" name="ItmTotAmt" id="ItmTotAmt" value="0.00" readonly style="font-size:20px; border: 0;">
						</div>
          </div>   -->
					
					<table width="50%" border="0" cellspacing="0" cellpadding="0" align="right">
						<tr>
							<td nowrap align="right"><b>Net of VAT </b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnNetVAT" name="txtnNetVAT" readonly value="0" style="text-align:right; border:none;  background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
						<tr>
							<td nowrap align="right"><b>VAT </b>&nbsp;&nbsp;</td> 
							<td> <input type="text" id="txtnVAT" name="txtnVAT" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
						<tr>
							<td nowrap align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
					</table>

   		</div>
    
  		<div class="col-lg-6" style="padding:10px !important;">
     
				<div class="row nopadwdown">
					<div class="col-lg-8 nopadding">
            <div class="input-group margin-bottom-sm">
							<span class="input-group-addon"><i class="fa fa-users"></i></span>
              <input type="text" name="citemDesc" id="citemDesc" class="form-control input-sm " placeholder="Search Customer Name..." autocomplete="off" value="WALK-IN"> 
            </div>
          </div>

					<div class="col-lg-2 nopadwleft">
            <select name="seltable" id="seltable" class="form-control input-sm ">
							<option value="1">Dine-IN</option>
							<option value="2">Take-Out</option>
							<option value="2">Delivery</option>
						</select>
          </div>

					<div class="col-lg-2 nopadwleft">
            <select name="seltable" id="seltable" class="form-control input-sm ">
							<option value="1">Table 1</option>
							<option value="2">Table 2</option>
							<option value="3">Table 3</option>
							<option value="4">Table 4</option>
							<option value="5">Table 5</option>
						</select>
          </div>

				</div>

     		<!-- Item Description Searh
     		<div class="row nopadwdown">
          <div class="col-lg-2 nopadding">
            <input type="text" name="nqtyins" id="nqtyins" class="numeric form-control input-sm " placeholder="(Insert) Qty...">
          </div>
          <div class="col-lg-10 nopadwleft">
            <div class="input-group margin-bottom-sm">
              <input type="text" name="citemDesc" id="citemDesc" class="form-control input-sm " placeholder="Search Item Description..." autocomplete="off"> 
              <span class="input-group-addon"><i class="fa fa fa-search fa-fw"></i></span>
            </div>
          </div>
        </div>   -->
                     
       	<!-- Items -->     
        <div class="col-lg-12 wrapper pre-scrollable" style="max-height: 61vh; min-height: 61vh">           
					<?php
            //Columns must be a factor of 12 (1,2,3,4,6,12)
            $numOfCols = 4;
            $rowCount = 0;
            $bootstrapColWidth = (12 / $numOfCols);
						$date1 = date("Y-m-d");
          ?>
            <div class="row">
              <?php
								$sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, 0 as nretailcost, 0 as npurchcost, a.cunit, a.cstatus, 0 as ltaxinc, a.cclass, 1 as nqty, a.cuserpic
									from items a 
									left join
										(
											select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
											From tblinventory a
											right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
											where a.compcode='$company' and  a.dcutdate <= '$date1' and d.cstatus = 'ACTIVE'
											group by a.citemno
										) c on a.cpartno=c.citemno
									WHERE a.compcode='$company' and a.cstatus = 'ACTIVE' and a.ctradetype='Trade' order by a.cclass, a.citemdesc";
								$result=mysqli_query($con,$sql);
								$rowcnt = mysqli_num_rows($result);
							
								if (!mysqli_query($con, $sql)) {
									printf("Errormessage: %s\n", mysqli_error($con));
								}			
								$cntr = 0;
					
								while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
									$cntr = $cntr + 1;
									
									if((float)$row["nqty"] > 0){
				
              ?>  
                <div class="itmslist col-md-<?php echo $bootstrapColWidth; ?> nopadding" id="<?php echo $row["cpartno"]; ?>" data-itemcls="<?php echo $row["cclass"]; ?>">
                  <div style="height:100px;                     
                    background-color:#019aca; 
										background-image:url('<?=$row["cuserpic"];?>');
										background-repeat:no-repeat;
										background-position: center center;
										background-size: contain;
                    border:solid 1px #036;
                    text-align:center;
										position: relative">
											<div class="see_offer"><span><font size="-2"><?php echo $row["citemdesc"]; ?></font></span></div>
                    
                  </div>
                </div>
              <?php				
										$rowCount++;
										if($rowCount % $numOfCols == 0) echo '</div><div class="row">';
								
									}
								}
              ?>
            </div>
        </div> 
															
       	<!-- Classifications -->    
        <div class="col-lg-12 nopadwtop">          
          <section class="regular slider">

						<?php
							$sql = "select * from groupings where ctype='ITEMCLS' and ccode in (select cclass From items where compcode='$company' and cstatus = 'ACTIVE' and ctradetype='Trade') order by cdesc";
							$result=mysqli_query($con,$sql);
							$rowcnt = mysqli_num_rows($result);
							
							if (!mysqli_query($con, $sql)) {
								printf("Errormessage: %s\n", mysqli_error($con));
							}			
							$cntr = 0;
							
							while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
						?>	
              <div style="height:63px; 
                word-wrap:break-word;
                background-color:#019aca; 
                border:solid 1px #036;
                padding:3px;
                text-align:center;" class="itmclass" data-clscode="<?php echo $row["ccode"]?>">
                	<font size="-2"><?php echo $row["cdesc"]?></font>
              </div>
						<?php
							}
						
							if($cntr < 6) {
								$less = 6-$cntr;
								for($xyz=1; $xyz<=$less; $xyz++){
						?>
                <div style="height:63px; 
                  word-wrap:break-word;
                  background-color:#09C; 
                  border:#06C;
                  padding:3px;
                  text-align:center;">
                	&nbsp;
                </div>
						<?php
								}
							}
						?>
          </section>
        </div> 
 
       	<!-- Buttons -->       
				<div class="col-lg-12 nopadwtop2x">
					<div class="col-lg-3 nopadding">
						<button class="form-control btn btn-sm btn-success" name="btnPay" id="btnPay" type="button">
						<i class="fa fa-money fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
						PAYMENT (F2)</button>
					</div>
					<div class="col-lg-3 nopadwleft">
						<button class="form-control btn btn-sm btn-primary" name="btnExit" id="btnExit" type="button">
						<i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
						HOLD (INS)</button>						
					</div>
					<div class="col-lg-3 nopadwleft">
						<button class="form-control btn btn-sm btn-warning" name="btnSales" id="btnSales" type="button">
						<i class="fa fa-bar-chart fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
						RETRIEVE (F4)</button>
					</div>
					<div class="col-lg-3 nopadwleft">
						<button class="form-control btn btn-sm btn-danger" name="btnCancel" id="btnCancel" type="button">
						<i class="fa fa-plus fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
						VOID (DEL)</button>
					</div>
				</div>            

			</div>     

    </div>
  
    
	<!-- BOOTSTRAP MODALS -->

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-center">
				<div class="modal-content">
					<div class="alert-modal-danger">
						<p id="AlertMsg"></p>
						<p><center>
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Ok</button>
						</center></p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 1) Payment Modal -->
		<div class="modal fade" id="PaymentModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="nopadding">Payment</h4>
					</div>

					<div class="modal-body" style="height: 60vh !important">
						<div class="row">
											
							<div class="col-sm-6 b-col">
															
								<h4>Total Amount</h4>
								<div id="divGrandTot" class="well well-sm nopadding" style="text-align:right; font-size:24px">0.00&nbsp;</div>															
								<input type="hidden" name="GrandTot" id="GrandTot" value="0.00">													
								

								<div class="col-sm-12 nopadwtop">
									<h4>Amount Tendered</h4> 
									<input type="text" class="numeric form-control input-md nopadwright2x" name="GrandPayed" id="GrandPayed" style="text-align:right;  font-size:24px" value="0.00" autocomplete="off">
								</div>
									
								<div class="col-sm-12 nopadwtop"> 
									<h4>Change</h4>
									<div id="divGrandChange" class="well well-sm nopadding" style="text-align:right; font-size:24px">0.00&nbsp;</div>
									<input type="hidden" class="form-control input-lg" name="GrandChange" id="GrandChange">
								</div>

								<p>&nbsp;</p>
							</div>
														
							<div class="col-sm-6 nopadding">
														
								<h4>&nbsp;</h4>
								<div class="jqbtk-container">
									<div class="jqbtk-row">
										<button type="button" class="btnpad btn btn-default" data-val="7">7</button>
										<button type="button" class="btnpad btn btn-default" data-val="8">8</button>
										<button type="button" class="btnpad btn btn-default" data-val="9">9</button>
										<button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="100">100</button>
									</div>
									<div class="jqbtk-row" style="padding-top: 2px">																	
										<button type="button" class="btnpad btn btn-default" data-val="4">4</button>
										<button type="button" class="btnpad btn btn-default" data-val="5">5</button>
										<button type="button" class="btnpad btn btn-default" data-val="6">6</button>
										<button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="200">200</button>
									</div>
									<div class="jqbtk-row" style="padding-top: 2px">																	
										<button type="button" class="btnpad btn btn-default" data-val="1">1</button>
										<button type="button" class="btnpad btn btn-default" data-val="2">2</button>
										<button type="button" class="btnpad btn btn-default" data-val="3">3</button>
										<button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="500">500</button>
									</div>
									<div class="jqbtk-row" style="padding-top: 2px">																	
										<button type="button" class="btnpad btn btn-default" data-val=".">&nbsp;.</button>
										<button type="button" class="btnpad btn btn-default" data-val="0">0</button>
										<button type="button" class="btnpad btn btn-default" data-val="DEL" style="padding-right: 10px !important; padding-left: 10px !important"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
										<button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="1000">1000</button>
									</div>
									<div class="jqbtk-row"  style="padding-top: 2px">
										<button type="button" class="btnpad btn btn-success jqbtk-space" data-val="EXACT">EXACT</button>
									</div>
									<div class="jqbtk-row"  style="padding-top: 2px">
										<button type="button" class="btnpad btn btn-warning jqbtk-space" data-val="CLEAR">CLEAR</button>
									</div>	
									
									<div class="jqbtk-row"  style="padding-top: 2px">
										<button type="button" class="btnpad btn btn-success jqbtk-space" data-val="CLEAR">Done (CTRL + ENTER)</button>
									</div>	

									<div class="jqbtk-row"  style="padding-top: 2px">
										<button type="button" class="btnpad btn btn-danger jqbtk-space" data-dismiss="modal">Close</button>
									</div>	
								</div>  
								

							</div>                       
												
						</div>
											
						<div class="alert alert-danger" id="PayAlert"></div>


					</div>

				</div>
			</div>
		</div>

	<!-- SAVING MODAL-->
	<div class="modal fade" id="SavingModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog modal-sm vertical-align-center">
				<div class="modal-content">
					<div class="modal-body" id="divModalSave"></div>
				</div>
			</div>
		</div>
	</div>


</form>

</body>
</html>

<script src="../Bootstrap/slick/slick.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">


$(document).ready(function() {

  clockUpdate();
  setInterval(clockUpdate, 1000);

	//to initialize readonly customer upon loading
	$("#ccustname").attr('readonly', true); citemno
	$("#citemno").focus();

	$(".regular").slick({
    dots: false,
    infinite: true,
    slidesToShow: 6,
    slidesToScroll: 5
  });

	$("#PayAlert").hide();

	$("#btnCancel").on("click", function() {
		location.reload();
	});

	$("#btnPay").on("click", function() {
		checkform();
	});

	$("#btnExit").on("click", function() {
		location.href="../logout.php";
	});
	
	$("#btnDoneEnter").on("click", function() {
		chksubmit();
	});

	
	$("#selCust").on("change", function() {
		if ($(this).val()=="WIN") {
			$("#ccustname").attr('readonly', true);
			$("#ccustname").attr('placeholder', '');
			$("#divLimit").hide();
			
			$("#ccustname").prop('required',false);
		}
		else {
			$("#ccustname").attr('readonly', false);
			$("#ccustname").attr('placeholder', 'Search Customer Name...');
			$("#divLimit").show();
			
			$("#ccustname").prop('required',true);
			
			$("#ccustname").focus();
		}
	});
	
	$("#ccustname").typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "th_customer.php",
				dataType: "json",
				data: {
					query: $("#ccustname").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 				
						
			$('#ccustname').val(item.value).change(); 
			$("#ccustid").val(item.id);
			$("#ccustcredit").val(item.nlimit); 
			$("#divCreditLim").text(item.nlimit);
			
			chkbalance(item.id);
			
			$("#citemno").focus();			
			
		}
	});
	
	$("#citemno").on("keyup", function(e) {
		if(e.keyCode==13){
			var itm = $(this).val();

			if(itm.indexOf("*")==-1){
				var xy = itm;
				var finqty = 1;
			}else{
				var itmcode = itm.split("*", 2);
				var xy = itmcode[1];
				var finqty = itmcode[0];
				
			}
			 
				getDetails(xy,finqty);
				$('#citemno').val('').change();
		}
		
		
		
	});
	
	$("#nqtyins").on("keyup", function(e) {
		if(e.keyCode==13){
			$("#citemDesc").focus();
		}
	});
	
	$('#citemDesc').typeahead({
		autoSelect: true,
		fitToElement: true,
		source: function(request, response) {
			$.ajax({
				url: "th_product.php",
				dataType: "json",
				data: {
					query: $("#citemDesc").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			
			if(item.cstatus=="ACTIVE" && parseFloat(item.nprice) > 0){
				
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.value+'</span><br><small><span class="dropdown-item-extra">' + item.nprice + '</span> '+item.nbal+ ' ' + item.cunit + '</small></div>';
			}
			else{
				return "";
			}


		},
		highlighter: Object,
		afterSelect: function(item) { 	
		
			var xy = item.id;
			var finqty = $("#nqtyins").val(); 
			
			if(finqty==""){
				finqty = 1;
			}
			
			getDetails(xy,finqty)		
					
						
			$('#citemDesc').val('').change();	
			$("#nqtyins").val('');
			
			$("#nqtyins").focus();			
			
			
		}
	
	});
	
	$('.itmslist, .itmclass').hover(function() {
        $(this).css('cursor','pointer');
    });
	
	
	$(".itmslist").on("click", function() {
		var currentID = this.id;

		getDetails(currentID,1)
		
		//var itmcls = $(this).attr("data-itemcls");
		//alert(itmcls);
	});

	$(".itmclass").on("click", function() {
		var ClassID = $(this).attr("data-clscode");
		
		$('.itmslist').each(function(i, obj) {
			itmcls = $(this).attr("data-itemcls");
			//alert(itmcls);
			
			if(itmcls==ClassID){
				$(this).show();
			}else if(itmcls!=ClassID){
				$(this).hide();
			}
			
			
		});		
		 
		//alert(itmcls);
	});


	$('#PaymentModal').on('shown.bs.modal', function () {
		$('#GrandPayed').val("0.00");
		$('#GrandPayed').focus();
		$('#GrandPayed').select();
	}) 
	
	$(".btnpad").on("click", function() {
		var xy = $('#GrandPayed').val();
		var yz = $(this).attr("data-val");
		
		if (xy=="0.00") {
			$('#GrandPayed').val("");
			xy = "";
		}
		
		if(yz=="."){
			if(xy.indexOf(".")!=-1){ //pag may point na.. nde na pwde maglagay ulit ng point
				yz = "";
			}
		}
		
		if(yz=="DEL"){

			if(xy.length==1){
				xy = "0.00";
				yz = "";
			}
			else{
				yz = xy.slice(0, -1);
				xy = "";
			}
		}
		
		if(yz=="CLEAR"){
				xy = "0.00";
				yz = "";
		}

		if(yz=="EXACT"){
				xy = $("#GrandTot").val().replace(/,/g,'');
				yz = "";
		}
		
		if(yz==1000 || yz==500 || yz==200 || yz==100){
			xy = "";
		}
				
		$('#GrandPayed').val(xy+yz);

		$("#GrandPayed").autoNumeric('destroy');
		$("#GrandPayed").autoNumeric('init',{mDec:2});
		
		ComputeChange();
		
	});
	
	$("#GrandPayed").on("keyup", function() {
		var thispayed = $("#GrandPayed").val();
		
		if(thispayed==""){
			$("#GrandPayed").val("0.00");
			
			$("#GrandPayed").focus();
			$("#GrandPayed").select();
			
			$("#divGrandChange").html("0.00");

		}
		else{
			ComputeChange();	
		}
		
	});

});

function setdel(x){
	$("#Itm"+x).remove();
	CompAmt();
	
}

function ComputeChange(){

		var thispayed = $("#GrandPayed").val().replace(/,/g,'');
		var thisamt = $("#GrandTot").val().replace(/,/g,'');
		
		var thischange = parseFloat(thispayed) - parseFloat(thisamt);
		
		if(thischange < 0){
			$("#divGrandChange").html("<font color='#FF0000'>" +thischange.toFixed(2) + " </font>");

		}
		else{
			$("#divGrandChange").html(thischange.toFixed(2) + " ");
		}

		$("#GrandChange").val(thischange);
		$("#GrandChange").autoNumeric('destroy');
		$("#GrandChange").autoNumeric('init',{mDec:2});

}

function InsertQty(){
	$("#nqtyins").focus();
}  

function InsertBarcode(){
	$("#citemno").focus();
} 
	
function getDetails(xy,finqty){
	var code = "";
	
	$.post('th_getPartNo.php',{'q':xy },function( data ){ //send value to post request in ajax to the php page
		getFromScan(data,xy,finqty);
  });
		
}

function getFromScan(code,xy,finqty){

	var finprice = "";
	var finamt = 0;

	$.ajax({
    url: 'th_getDetail.php',
		data: 'x='+xy,
    dataType: 'json',
    method: 'post',
    success: function (data) {
		//alert(data);
      console.log(data);
			$.each(data,function(index,item){
				if(item.citemno==""){
					$("#AlertMsg").text(item.cdesc);
					$("#AlertModal").modal('show');
				}
				else{							 

					finprice = chkprice(item.citemno,item.cunit,"PM4","<?=date("m/d/Y")?>");
					//finprice = finprice.toFixed(4);

					var tbl = document.getElementById('TblItems').getElementsByTagName('tr');
					var lastRow = tbl.length;

					var a=document.getElementById('TblItems').insertRow(-1);
					//var u=a.insertCell(0);
					var v=a.insertCell(0);
						v.style.font = "normal 8pt Arial";
					var v2=a.insertCell(1);
						v2.style.width = "50px";
						v2.style.font = "normal 8pt Arial";
					var w=a.insertCell(2);
						w.style.width = "80px";
						w.style.padding = "1px";
					var x=a.insertCell(3);
						x.style.width = "100px";
						x.style.padding = "1px";
					var x2=a.insertCell(4);
						x2.style.width = "100px";
						x2.style.padding = "1px";
					var y=a.insertCell(5);
						y.style.width = "100px";
						y.style.padding = "1px";
					var z=a.insertCell(6);
						z.style.width = "25px";
						z.align = "right";
					//u.innerHTML = ""+itmcode;
					v.innerHTML = "<input type=\"hidden\" name=\"txtcitemno"+lastRow+"\" id=\"txtcitemno"+lastRow+"\" value=\""+item.citemno+"\"/> <input type='hidden' value='"+item.cdesc+"' name='txtitemdesc"+lastRow+"' id='txtitemdesc"+lastRow+"'>"+ item.cdesc+"";

					v2.innerHTML = "<input type='hidden' value='"+item.cunit+"' name='txtcunit"+lastRow+"' id='txtcunit"+lastRow+"'>"+item.cunit;

					w.innerHTML = "<input type='text' value='1' class='numeric2 form-control input-xs' style='text-align:right' name='txtnqty"+lastRow+"' id='txtnqty"+lastRow+"' tabindex=\"4\">";

					x.innerHTML = "<input type='text' value='"+finprice+"' class='numeric2 form-control input-xs' style='text-align:right' name='txtnprice"+lastRow+"' id='txtnprice"+lastRow+"' readonly>";

					x2.innerHTML = "<input type='text' value='0' class='numeric2 form-control input-xs' style='text-align:right' name='txtndisc"+lastRow+"' id='txtndisc"+lastRow+"' readonly>";

					y.innerHTML = "<input type='text' value='"+finprice+"' class='numeric form-control input-xs' style='text-align:right' name='txtnamount"+lastRow+"' id='txtnamount"+lastRow+"' readonly>";

					z.innerHTML = "<a href=\"javascript:;\" onClick=\"setdel('"+item.citemno+"')\" class=\"btn btn-xs btn-danger\"><i class=\"fa fa-trash\"></i></a>";

					$("input.numeric").autoNumeric('init',{mDec:2});
					$("input.numeric2").autoNumeric('init',{mDec:4});
					$("input.numeric, input.numeric2").on("click", function () {
						$(this).select();
					});

					$("input.numeric2").on("keyup", function () {
						ComputeAmt($(this).attr('id'));
						ComputeGross();
					});

					ComputeGross();							
							  
				}
			});
    },
    error: function (req, status, err) {
			console.log('Something went wrong', status, err)

			$("#AlertMsg").html('Something went wrong<br><br>'+status+"<br><br>"+err);
			$("#AlertModal").modal('show');

		}
  });
}

	function chkprice(itmcode,itmunit,ccode,datez){
		var result;

		$.ajax ({
			url: "../Sales/th_checkitmprice.php",
			data: { itm: itmcode, cust: ccode, cunit: itmunit, dte: datez },
			async: false,
			success: function( data ) {
				result = data;
			}
		});

		return result;
		
	}

	function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val().replace(/,/g,'');
			nprc = parseFloat(nprc);
			
			ndsc = $("#txtndisc"+r).val().replace(/,/g,'');
			ndsc = parseFloat(ndsc);
			
			if (parseFloat(ndsc) != 0) {
				nprc = parseFloat(nprc) - parseFloat(ndsc);
			}
			
			namt = nqty * nprc;
			//namt2 = namt * parseFloat($("#basecurrval").val().replace(/,/g,''));
						
			$("#txtnamount"+r).val(namt);
			$("#txtnamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('init',{mDec:2}); 

	}

	function ComputeGross(){
			var rowCount = $('#TblItems tr').length;
			
			var gross = 0;
			var nnet = 0;
			var vatz = 0;

			var nnetTot = 0;
			var vatzTot = 0;

			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
		


							if(parseFloat($("#txtnamount"+i).val().replace(/,/g,'')) > 0 ){

								nnet = parseFloat($("#txtnamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (12/100));
								vatz = nnet * (12/100);

								nnetTot = nnetTot + nnet;
								vatzTot = vatzTot + vatz;
							}
						
					

					gross = gross + parseFloat($("#txtnamount"+i).val().replace(/,/g,''));
				}
			}


			$("#txtnNetVAT").val(nnetTot);
			$("#txtnVAT").val(vatzTot);
			$("#txtnGross").val(gross);

			$("#txtnNetVAT").autoNumeric('destroy');
			$("#txtnVAT").autoNumeric('destroy');			
			$("#txtnGross").autoNumeric('destroy');


			$("#txtnNetVAT").autoNumeric('init',{mDec:2});
			$("#txtnVAT").autoNumeric('init',{mDec:2});
			$("#txtnGross").autoNumeric('init',{mDec:2});
	
			
	}

function checkform(){

	var rowCount = $('#TblItems tr').length;
	
	if(rowCount<=1){
		$("#AlertMsg").text("Cannot save without details!");
		$("#AlertModal").modal('show');
	}
	else{

		$("#divGrandTot").text($("#txtnGross").val());
		$("#GrandTot").val($("#txtnGross").val());
		$("#PaymentModal").modal('show');
		rescale();		
		
	}

}

function chksubmit(){
		var xyz = parseFloat($("#GrandTot").val());
	    var abc = parseFloat($("#GrandPayed").val());

		if(abc>=xyz){
			//$("#frmWIN").submit();
			
			savetran();
		}
		else{
			$("#PayAlert").html("<b>Payment Error!</b> <br> Please check if amount tendered is enough to pay the total amount.");
			$("#PayAlert").show();
			
			$('#GrandPayed').focus();
		    $('#GrandPayed').select();

		}

}

// BEGIN SAVING THE TRANSACTION
function savetran(){
	$("#PaymentModal").modal('hide');
	
	//Saving Modal Show
	$("#divModalSave").html("<center>Saving Transaction!</center>");
	$("#SavingModal").modal('show');
	
 // SAVING THE HEADER
 var custtype = document.getElementById("selCust").value;
   if(custtype=="CUS"){
	var id = document.getElementById("txtcustid").value;
	var name = document.getElementById("cCustName").value;
   }
   else if(custtype=="WIN"){
	var id = "WALKIN";
	var name = "WALKIN CUSTOMER";
   }
   alert(id);
	var creditlim = document.getElementById("ccustcredit").value;
   alert("2");
	var due = document.getElementById("GrandTot").value;
   alert("3");
	var payed = document.getElementById("GrandPayed").value;
	alert("4");
	var custbal = document.getElementById("ccustbal").value; 
	alert("5");
	var totalamt = document.getElementById("hdnItmTotAmt").value;
   alert("1");

	var SINo = "";
		
    
	// Returns successful data submission message when the entered information is stored in database.
	var dataString = 'ccustid=' + id + '&ccustname=' + name + '&ccustcredit=' + creditlim + '&GrandTot=' + due + '&GrandPayed=' + payed + '&ccustbal=' + custbal + "&hdnItmTotAmt=" + totalamt + "&selCust=" + custtype;

	if ((id == '' || name == '') && custtype=="CUS") {
		$("#divModalSave").html("<center><div class='alert alert-danger'>SELECT A CUSTOMER...</div></center>");
	} else {
		// AJAX code to submit form.
		$.ajax({
			type: "POST",
			url: "saveHdr.php",
			data: dataString,
			cache: false,
			success: function(html) {
				SINo = html;
				alert(html);
			},
			error: function (req, status, err) {
				console.log('Something went wrong', status, err)
			
				$("#SavingModal").modal('hide');
				
				$("#AlertMsg").html('Something went wrong<br><br>'+status+"<br><br>"+err);
				$("#AlertModal").modal('show');
		
			}

		});
	}
	
	//IF header retuned false popup alert else save details
	if(SINo=="False"){
		$("#divModalSave").html("<center><div class='alert alert-danger'>There's a problem saving the transaction. <br> Please consult your administrator</div><br><br> <button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Close</button> </center>");
	}
	else{
		
	}
	
	return false;
	
}


// END SAVING THE TRANSACTION


function clockUpdate() {
  var date = new Date();
  //$('.digital-clock').css({'color': '#fff', 'text-shadow': '0 0 6px #ff0'});
  function addZero(x) {
    if (x < 10) {
      return x = '0' + x;
    } else {
      return x;
    }
  }

  function twelveHour(x) {
    if (x > 12) {
      return x = x - 12;
    } else if (x == 0) {
      return x = 12;
    } else {
      return x;
    }
  }

  var h = addZero(twelveHour(date.getHours()));
  var m = addZero(date.getMinutes());
  var s = addZero(date.getSeconds());

  $('.digital-clock').text(h + ':' + m + ':' + s)
}

</script>