<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

$company = $_SESSION['companyid'];

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

		$sql = "select * from groupings where compcode='$company' and ctype='ITEMCLS' LIMIT 1";
		$result=mysqli_query($con,$sql);
		$rowcnt = mysqli_num_rows($result);
		
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$cclascoddef = $row["ccode"];
		
		}
		
		$sql2 = "select A.cvalue, B.cname, B.cpricever from parameters A left join customers B on A.compcode=B.compcode and A.cvalue=B.cempid where A.compcode='$company' and A.ccode='POSCUST'";
		$result2=mysqli_query($con,$sql2);
		$rowcnt2 = mysqli_num_rows($result2);
		
			if (!mysqli_query($con, $sql2)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

		while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)){
			
			$CustCode = $row2["cvalue"];
			$CustPriceVer= $row2["cpricever"];
			$CustNames = $row2["cname"];
		
		}
		
		
		?>	

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link href="../../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/DigiClock.css"> 

  <link rel="stylesheet" type="text/css" href="../../Bootstrap/slick/slick.css">
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/slick/slick-theme.css">
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/slick/slicksize.css">
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/keypadz.css?v=<?php echo time();?>">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>

	<style type="text/css">
		.modal {
		}
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
			location.href="../../main.php";
			}
			else if (e.keyCode == 13 && e.ctrlKey) {
				chksubmit();
			}
			
		});

	</script>
</head>

<body style="background-color:#2b3643; padding:10px">
<form name="frmWIN" id="frmWIN" action="POS_newsave.php" method="post">
<input type="hidden" name="ccustid" id="ccustid" value="<?php echo $CustPriceVer;?>"> 

<div class="col-lg-12 nopadding" style="background-color: #f5f5f5;">

	<div class="col-lg-6 nopadding">
       <div class="col-lg-12 nopadding">
        <div style="display:table;background-color: #f5f5f5; padding:15px;">
        
    		<!-- Time user -->
            	<div class="col-lg-12 nopadding">
                <div class="col-lg-6 nopadding" id="clock1">
                    <span class="date">
                    Cashier: <?php echo $_SESSION['employeename']; ?>
                    </span>
                    
                </div>
                <div class="col-lg-6 nopadwleft" id="clock">
                        <span class="date"><?php echo date("l F d, Y");?></span>
                </div>
            </div> 
            
    		<!-- Item barcode search --> 
            	<div class="col-lg-12 nopadwtop">
                     <div class="input-group margin-bottom-sm">
                      <input type="text" name="citemno" id="citemno" class="form-control input-sm " placeholder="(Home) Enter Barcode... (qty*barcode)" autocomplete="off"> 
                       <span class="input-group-addon"><i class="fa fa fa-barcode fa-fw"></i></span>
                    </div>
            	</div>
            
            <!-- Item table Header -->            
            	<div class="col-lg-12 nopadwtop2x">  
            
                	<div class="col-lg-12 nopadding">
                    <div class="col-lg-1 divbak">
                       <b>Del</b> 
                    </div>
                    <div class="col-lg-4 divbak">
                      <b>Product</b>  
                    </div>
                     <div class="col-lg-2 divbak">
                      <b>OnHand</b> 
                    </div>
                    <div class="col-lg-2 divbak">
                      <b>Qty </b> 
                    </div>
                    <div class="col-lg-3 divbak">
                       <b>Price </b>
                    </div>
                    
                </div>
            
            <!-- BODY -->
                <div style="height:70vh;" class="col-lg-12 nopadding pre-scrollable" id="TblItems"> 
                
                </div> 
                
    		<!-- Total -->
                <div class="col-lg-12 nopadding">
                    <div class="col-lg-3 divbak" style="text-align:right; padding:10px; font-size:20px">
                        <b>Total Items:</b>
                    </div>
                    <div class="col-lg-3 divbak" style="padding:10px; font-size:20px" id="ItmTotQty">
                      0
                    </div>
                     <div class="col-lg-2 divbak" style="text-align:right; padding:10px; font-size:20px">
                        <b>Total:</b>
                    </div>
                    <div class="col-lg-4 divbak" style="text-align:right; padding:10px; font-size:20px" id="ItmTotAmt">
                        0.00
                    </div>
               </div> 
               
            <!-- Credit Limit -->
                <div class="col-lg-12 nopadwdown" id="divLimit" style="display:none">
                    <div class="col-lg-3 divbak" style="text-align:right;  padding:10px; font-size:20px">
                        <b>Credit Limit:</b>
                    </div>
                    <div class="col-lg-3 divbak" style="padding:10px; font-size:20px" id="divCreditLim">
                       0.00
                    </div>
                     <div class="col-lg-2 divbak" style="text-align:right; padding:10px; font-size:20px">
                        <b>Balance:</b>
                    </div>
                    <div class="col-lg-4 divbak" style="text-align:right; padding:10px; font-size:20px" id="divCreditBal">
                       0.00
                    </div>          
    
                 </div> 
            
            </div>
        
        </div> <!-- DISPLAY TABLE -->   
       </div>
			 <!--
       <div class="col-lg-12 nopadwtop">
       		<div style="height:20vh; background-color: #f5f5f5; padding:15px;">
             <div class="col-lg-12 nopadding">
            	<b>Message Confirmations</b>
             </div>
             
              <div class="col-lg-12 nopadtop" id="MyMsgs">
              </div>
            </div>
       </div> -->
       
   </div> <!-- col-lg-6 -->
    
  	<div class="col-lg-6 nopadwleft">
    
     <div style="display: table; background-color: #f5f5f5; padding:15px; width:100%; table-layout: fixed;">
     
     	<!-- Item Description Searh -->
     		<div class="col-lg-12 nopadwdown">
               <div class="col-lg-2 nopadding">
                	<input type="text" name="nqtyins" id="nqtyins" class="numeric form-control input-sm " placeholder="(Insert) Qty...">
               </div>
               <div class="col-lg-10 nopadwleft">
                     <div class="input-group margin-bottom-sm">
                      <input type="text" name="citemDesc" id="citemDesc" class="form-control input-sm " placeholder="Search Item Description..." autocomplete="off"> 
                       <span class="input-group-addon"><i class="fa fa fa-search fa-fw"></i></span>
                    </div>
               </div>
            </div>  
         
            
       <!-- Items -->     
            <div class="col-lg-12 wrapper pre-scrollable" style="max-height: 70vh; min-height: 65vh">
            
							<?php
								//Columns must be a factor of 12 (1,2,3,4,6,12)
								$numOfCols = 6;
								$rowCount = 0;
								$bootstrapColWidth = (12 / $numOfCols);
								$date1 = date("Y-m-d");
							?>
              <div class="row">
                <?php

									$sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, 0 as nretailcost, 0 as npurchcost, a.cunit, a.cstatus, 0 as ltaxinc, a.cclass, 1 as nqty
									from items a 
									left join
									(
										select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
										From tblinventory a
										right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
										where a.compcode='$company' and  a.dcutdate <= '$date1' and d.cstatus = 'ACTIVE'
										group by a.citemno
									) c on a.cpartno=c.citemno
									WHERE a.compcode='$company' and a.cstatus = 'ACTIVE' order by a.cclass, a.citemdesc";

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
                  <div class="itmslist col-md-<?php echo $bootstrapColWidth; ?> nopadding" id="<?php echo $row["cscancode"]; ?>" data-itemcls="<?php echo $row["cclass"]; ?>">
                    <div style="height:80px; 
                      word-wrap:break-word;
                      background-color:#019aca; 
                      border:solid 1px #036;
                      padding:3px;
                      text-align:center;">
                      <font size="-2"><?php echo $row["citemdesc"]; ?></font>
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
		$sql = "select * from groupings where ctype='ITEMCLS' order by cdesc";
		$result=mysqli_query($con,$sql);
		$rowcnt = mysqli_num_rows($result);
		
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			
		$cntr = 0;
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$cntr = $cntr + 1;
		?>	
                <div style="height:50px; 
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
                <div style="height:50px; 
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
                <button class="form-control btn btn-sm btn-danger" name="btnCancel" id="btnCancel" type="button">
                <i class="fa fa-times fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
                RESET (DEL)</button>
                </div>
                <div class="col-lg-3 nopadwleft">
                <button class="form-control btn btn-sm btn-warning" name="btnSales" id="btnSales" type="button">
                <i class="fa fa-bar-chart fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
                SALES (F4)</button>
                </div>
                <div class="col-lg-3 nopadwleft">
                <button class="form-control btn btn-sm btn-primary" name="btnExit" id="btnExit" type="button">
                <i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i>&nbsp;
                EXIT POS (ESC)</button>
                </div>
           </div>

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
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- 1) Payment Modal -->
<div class="modal fade" id="PaymentModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog modal-pos vertical-align-center">
            <div class="modal-content">
               <div class="modal-header">
					<h4 class="nopadding">Payment</h4>
               </div>

				   <div class="row" style="padding-left:15px; padding-right:20px; height:50vh">
                   
                        <div class="col-sm-7 b-col nopadwleft">
                         
                          <div class="col-sm-12 nopadwtop2x">
                              <div class="col-sm-5 nopadwtop">
                                <b>Total Amount</b>
                              </div> 
                              <div class="col-sm-7 nopadwleft"> 
                                <input type="text" class="form-control input-sm nopadwright2x" name="GrandTot" id="GrandTot" readonly style="text-align:right;  font-size:18px" value="0.00">
                              </div>
  						  </div>
                          
                           <div class="col-sm-12 nopadwtop">
                             <div class="col-sm-5 nopadwtop">
                            	<b>Discount %</b> 
                             </div>
                             <div class="col-sm-7 nopadwleft">
                            <input type="text" class="form-control input-md nopadwright2x" name="nDicount" id="nDicount" style="text-align:right;  font-size:18px" value="0" autocomplete="off">
                             </div>
                           </div>
                           
                           <div class="col-sm-12 nopadwtop">
                             <div class="col-sm-5 nopadwtop">
                            	<b>NET AMOUNT</b> 
                             </div>
                             <div class="col-sm-7 nopadwleft">
                            <input type="text" class="form-control input-md nopadwright2x" name="nNetAmt" id="nNetAmt" style="text-align:right;  font-size:18px" value="0.00" readonly>
                             </div>
                           </div>

                           <div class="col-sm-12 nopadwtop2x">
                             <div class="col-sm-5 nopadwtop">
                            	<b>AMT PAYED</b> 
                             </div>
                             <div class="col-sm-7 nopadwleft">
                            <input type="text" class="numeric form-control input-sm nopadwright2x" name="GrandPayed" id="GrandPayed" style="text-align:right;  font-size:18px" value="0.00" autocomplete="off">
                             </div>
                           </div>
							
                           <div class="col-sm-12 nopadwtop"> 
                            <div class="col-sm-5 nopadding">
                                <b>CHANGE</b>
                            </div>
                            <div class="col-sm-7 nopadwleft">
                                <input type="text" class="form-control input-sm nopadwright2x" name="GrandChange" id="GrandChange" readonly style="text-align:right;  font-size:18px" value="0.00">
                            </div>
                           </div>

                        </div>
                        
                        <div class="col-sm-5 nopadding">
                        
                          <div class="col-sm-12 nopadwtop2x">  
                          
                             <div class="jqbtk-container">
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="7">7</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="8">8</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="9">9</button>
                                    <button type="button" class="btnpad btnx btn btn-info jqbtk-shift" data-val="100">100</button>
                                </div>
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="4">4</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="5">5</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="6">6</button>
                                    <button type="button" class="btnpad btnx btn btn-info jqbtk-shift" data-val="200">200</button>
                                </div>
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="1">1</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="2">2</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="3">3</button>
                                    <button type="button" class="btnpad btnx btn btn-info jqbtk-shift" data-val="500">500</button>
                                </div>
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btnx btn btn-default" data-val=".">.</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="0">0</button>
                                    <button type="button" class="btnpad btnx btn btn-default" data-val="DEL"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                    <button type="button" class="btnpad btnx btn btn-info jqbtk-shift" data-val="1000">1000</button>
                                </div>
                                <div class="jqbtk-row">

                                	<button type="button" class="btnpad btn btn-success jqbtk-half" data-val="EXACT">EXACT</button>
                                    <button type="button" class="btnpad btn btn-danger jqbtk-half" data-val="CLEAR">CLEAR</button>
                                    
                                </div>
                             </div>    
  						  
                          </div>
                          
                        </div>                       
                    
                   </div>
                   
                   <div class="alert alert-danger" id="PayAlert">
                      
                   </div>


               <div class="modal-footer">
					<button type="button" class="btn btn-success btn-lg" name="btnDoneEnter" id="btnDoneEnter">Done (CTRL + ENTER)</button> 
                    <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Close</button>
               </div>
            </div>
        </div>
    </div>


  <script src="../../Bootstrap/slick/slick.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    
      $(".regular").slick({
        dots: false,
        infinite: true,
        slidesToShow: 6,
        slidesToScroll: 5
      });

  </script>

</form>
</body>
</html>
<script src="../../Bootstrap/js/vue.js"></script>
<script src="../../Bootstrap/js/DigiClock.js"></script>

<script type="text/javascript">
var xCustCode = $("#ccustid").val();
var xChkBal = "";
var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;

	$(document).ready(function(e) {
		//relod div
		/*
		setTimeout(function() {
			$("#MyMsgs").load("SI_SMSREAD.php");
		}, 3000); // milliseconds = 3seconds
		*/
		
	   		$.ajax({
					url : "../../include/th_xtrasessions.php",
					type: "Post",
					async:false,
					dataType: "json",
					success: function(data)
					{	
					  console.log(data);
            $.each(data,function(index,item){
						   xChkBal = item.chkinv; //0 = Check ; 1 = Dont Check						   
					  });
					}
				});

			//to initialize readonly customer upon loading
			$("#ccustname").attr('readonly', true); citemno
			$("#citemno").focus();
			$("input.numeric").numeric();
			
			$("input.numeric").on("click", function () {
				$(this).select();
			});
			
			$("#PayAlert").hide();
	
	});
	

$(function() { 

	$("#btnCancel").on("click", function() {
		location.reload();
	});

	$("#btnPay").on("click", function() {
		checkform();
	});

	$("#btnExit").on("click", function() {
		location.href="../../main.php";
	});
	
	$("#btnDoneEnter").on("click", function() {
		chksubmit();
	});

	
	$("#citemno").keydown(function(e) {
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

				getFromScan(xCustCode,xy,finqty);
				$('#citemno').val('').change();
		}
		
		
		
	});
	
	$("#nqtyins").on("keydown", function(e) {
		if(e.keyCode==13){
			$("#citemDesc").focus();
		}
	});


	$('#citemDesc').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_productpos.php",
				dataType: "json",
				data: { query: $("#citemDesc").val(), itmbal: xChkBal },
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.desc+'</span</div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			var xy = item.id;
			var finqty = $("#nqtyins").val(); 
			
			if(finqty==""){
				finqty = 1;
			}
			
			getFromScan(xCustCode,xy,finqty)		
					
						
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
		
		getFromScan(xCustCode,currentID,1)
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
				xy = $("#nNetAmt").val();
				yz = "";
		}
		
		if(yz==1000 || yz==500 || yz==200 || yz==100){
			xy = "";
		}
				
		$('#GrandPayed').val(xy+yz);
		
		ComputeChange();
		
	});
	
	$("#GrandPayed").on("keyup", function() {
		var thispayed = $("#GrandPayed").val();
		
		if(thispayed==""){
			$("#GrandPayed").val("0.00");
			
			$("#GrandPayed").focus();
			$("#GrandPayed").select();
			
			$("#GrandChange").val("0.00");

		}
		else{
			ComputeChange();	
		}
		
	});

	$("#nDicount").on("keyup", function() {
		var thisdisc = $("#nDicount").val();
		
		if(thisdisc=="" || parseFloat(thisdisc)<=0){
			$("#nNetAmt").val($("#GrandTot").val());
		}
		else{
			
			var baseamt = parseFloat($("#GrandTot").val());
			var disc = parseFloat(thisdisc)/100;
			
			var nnet = baseamt - (baseamt * disc);
			
			$("#nNetAmt").val(nnet.toFixed(2));
			
		}

			ComputeChange();	

		
	});
	
	

});

function setdel(x){
	$("#Itm"+x).remove();
	CompAmt();
	
}

function ComputeChange(){

		var thispayed = $("#GrandPayed").val();
		var thisamt = $("#nNetAmt").val();
		
		if(parseFloat(thispayed) >= 1){
			var thischange = parseFloat(thispayed) - parseFloat(thisamt);
			
			if(thischange < 0){
				$("#GrandChange").css({'color':'#FF0000'});
	
			}
			else{
				$("#GrandChange").css({'color':'#000000'});
			}
			//$("#divGrandChange").html(parseFloat(thispayed) + " - " + parseFloat(thisamt));
			$("#GrandChange").val(thischange.toFixed(2));
		}

}

function CompAmt(){
var x = 0;
var y = 0;

   $('input[name="txtnamt[]"]').each(function() {
       x = x + parseFloat(this.value);
   });
   
  $("#ItmTotAmt").html("<input type='hidden' name='txtTotAmt' id='txtTotAmt' value='"+x+"'> " + x.toFixed(2)) 
  $("#divGrandTot").html(x.toFixed(2) + " ");
  $("#GrandTot").val(x.toFixed(2)); 
  $("#nNetAmt").val(x.toFixed(2));
  $("#hdnItmTotAmt").val(x.toFixed(2));
  
  
   $('input[name="txtnqty[]"]').each(function() {
       y = y + parseFloat(this.value);
   });
   
   $("#ItmTotQty").html("<input type='hidden' name='txtTotQty' id='txtTotQty' value='"+y+"'> " + y.toFixed(2));
   


}

function InsertQty(){
	$("#nqtyins").focus();
}  

function InsertBarcode(){
	$("#citemno").focus();
} 

	
function getFromScan(code,xy,finqty){
//check if nsa table na
var isadd = "YES";
var finprice = "";
var finamt = 0;

//alert('#Itm'+xy);
//alert($('#Itm'+code).length);

if ($('#Itm'+xy).length!=0) {
  isadd = "NO";
}

				if(isadd=="NO"){
					varnavail = $('#Itm'+xy+' input[name="txtnavail[]"]').val();
					varnqty = $('#Itm'+xy+' input[name="txtnqty[]"]').val();
					varnprice = $('#Itm'+xy+' input[name="txtnprice[]"]').val();
					varnamt = $('#Itm'+xy+' input[name="txtnamt[]"]').val();
					varcunit = $('#Itm'+xy+' input[name="txtcunit[]"]').val();
					vardesc = $('#Itm'+xy+' p[id="pD'+xy+'"]').html()
					
											
						finqty = parseFloat(finqty) + parseFloat(varnqty);
								
								if(parseFloat(finqty) > parseFloat(varnavail)){
									finqtydesc = "<font color=\"#FF0000\"> ("+finqty+")</font>";
									finqty = parseFloat(varnavail);
									
								}
								
								finprice = "<p style=\"text-indent: 20px\">@ "+varnprice+"/"+varcunit+"</p></div>";
								finamt = parseFloat(varnprice) * parseFloat(finqty);
							
						finamt = finamt.toFixed(4);
						
						if(varnqty==1){
							vardesc = vardesc + finprice;
						}

					
					$('#Itm'+xy+' p[id="pD'+xy+'"]').html("<font size=\"-1\"> " + vardesc + "</font>");
					$('#Itm'+xy+' p[id="pQ'+xy+'"]').html(finqty + " " + finqtydesc);
					$('#Itm'+xy+' p[id="pP'+xy+'"]').html(finamt);
					
					
					$('#Itm'+xy+' input[name="txtnqty[]"]').val(finqty);
					$('#Itm'+xy+' input[name="txtnamt[]"]').val(finamt);
					
					CompAmt();

				}
				else if(isadd == "YES"){

					$.ajax({
            url: '../th_productid.php',
						data: 'c_id='+xy+'&itmbal='+xChkBal,
            dataType: 'json',
            method: 'post',
            success: function (data) {
              console.log(data);
					 		$.each(data,function(index,item){

						  if(item.id==""){
								$("#AlertMsg").text("Barcode number does not exist!");
								$("#AlertModal").modal('show');
						  }
						  else{

								if(parseFloat(item.nqty)<=0){
									$("#AlertMsg").text("No more stocks on hand!");
									$("#AlertModal").modal('show');
								}
						  	else{
									finqtydesc = "";
									xprice = chkprice(item.id,item.cunit,code,xtoday);

									if(finqty==1){
										var finprice = "</div>";
										var finamt = parseFloat(xprice);
									}
									else{
																
										if(parseFloat(finqty) > parseFloat(item.nqty)){
											//alert("A");
											finqtydesc = " <font color=\"#FF0000\"> ("+finqty+")</font>";
											finqty = parseFloat(item.nqty);

											if(finqty==1){
												var finprice = "</div>";
												var finamt = parseFloat(xprice);
											}
											else {
												var finprice = "<p style=\"text-indent: 20px\">@ "+xprice+"/"+item.cunit+"</p></div>";
												var finamt = parseFloat(xprice) * parseFloat(finqty);
											}
											
										}
										else{
											//alert("B");
												var finprice = "<p style=\"text-indent: 20px\">@ "+xprice+"/"+item.cunit+"</p></div>";
												var finamt = parseFloat(xprice) * parseFloat(finqty);

										}
									}
								
							}
							
							finamt = finamt.toFixed(4);

							var divhead = "<div class=\"col-lg-12 nopadding\" id=\"Itm"+item.id+"\">";
							var divVALS = "<input type=\"hidden\" name=\"txtcitemno[]\" id=\"txtcitemno[]\" value=\""+item.id+"\" data-scancode=\""+item.id+"\" /> \
							<input type=\"hidden\" name=\"txtcunit[]\" id=\"txtcunit[]\" value=\""+item.cunit+"\" data-citmno=\""+item.id+"\" /> \
							<input type=\"hidden\" name=\"txtnavail[]\" id=\"txtnavail[]\" value=\""+item.nqty+"\" data-citmno=\""+item.id+"\" /> \
							<input type=\"hidden\" name=\"txtnqty[]\" id=\"txtnqty[]\" value=\""+finqty+"\" data-citmno=\""+item.id+"\" /> \
							<input type=\"hidden\" name=\"txtnamt[]\" id=\"txtnamt[]\" value=\""+finamt+"\" data-citmno=\""+item.id+"\" /> \
							<input type=\"hidden\" name=\"txtnprice[]\" id=\"txtnprice[]\" value=\""+xprice+"\" data-citmno=\""+item.id+"\" />";
							
							var divdel = "<div class=\"col-lg-1\"> <b><a href=\"javascript:;\" onClick=\"setdel('"+item.id+"')\" class=info><i class=\"fa fa-times-circle\" style=\"font-size:20px;color:red;\" title=\"Delete Item\"></i></a></b> </div>";
							var divitm = "<div class=\"col-lg-4\"><p id=\"pD"+item.id+"\"><font size=\"-1\"> " + item.desc + finprice + "</font></p>";
							var divonhand = "<div class=\"col-lg-2\"><p style=\"text-indent: 10px\"> "+item.nqty+" </p></div>";
                			var divqty = "<div class=\"col-lg-2\"><p style=\"text-indent: 10px\" id=\"pQ"+item.id+"\" > " + finqty + finqtydesc + " </p></div>";
              				var divprice = "<div class=\"col-lg-3\"><p style=\"text-align:right\"  id=\"pP"+item.id+"\"> "+finamt+"</p></div>"
              				var divend = "</div>";
							  							  
							$("#TblItems").append(divhead + divVALS + divdel + divitm + divonhand + divqty + divprice + divend);
							
							CompAmt();
							  
						  }
					  });
                    },
                    error: function (req, status, err) {
						//alert('th_qodetlist.php?x='+drno+"&y="+salesnos);
						console.log('Something went wrong', status, err)

							$("#AlertMsg").html('Something went wrong<br><br>'+status+"<br><br>"+err);
							$("#AlertModal").modal('show');

					}
                });
				}

}

function checkform(){

	var sum = 0;
	$("input[name='txtcitemno[]']").each( function() { 
		 sum = sum + 1;
	});
	
	if(sum==0){
		$("#AlertMsg").text("Cannot save without details!");
		$("#AlertModal").modal('show');
	}
	else{
			$("#PaymentModal").modal('show');
		
	}

}

function chksubmit(){
		var xyz = parseFloat($("#nNetAmt").val());
	    var abc = parseFloat($("#GrandPayed").val());

		if(abc>=xyz){
			
			savetran();
		}
		else{
			$("#PayAlert").html("<b>Payment Error!</b> <br> Please check if amount payed is enough to pay the total net amount.");
			$("#PayAlert").show();
			
			$('#GrandPayed').focus();
		    $('#GrandPayed').select();

		}

}

function chkprice(itmcode,itmunit,ccode,datez){
	var result;
	$.ajax ({
		url: "../th_checkitmprice.php",
		data: { itm: itmcode, cust: ccode, cunit: itmunit, dte: datez },
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
}


// BEGIN SAVING THE TRANSACTION

function savetran(){
		
	//Saving Modal Show
	$("#PayAlert").html("<b>SAVING!</b> <br> Please wait while transaction is saving!");
	$("#PayAlert").show();

	
	var id = "<?php echo $CustCode?>";
	var name = "<?php echo $CustNames?>";

	var due = document.getElementById("GrandTot").value;
	var net = document.getElementById("nNetAmt").value;
	var discnt = document.getElementById("nDicount").value;
	var payed = document.getElementById("GrandPayed").value;
	var totchange = document.getElementById("GrandChange").value;

	var SINo = "";
	var isDone = "True";

	// Returns successful data submission message when the entered information is stored in database.
	var dataString = 'ccustid=' + id + '&ccustname=' + name + '&GrandTot=' + due + '&GrandPayed=' + payed + '&totchange=' + totchange + '&nnet=' + net + '&ndisc=' + discnt;

		$.ajax({
			type: "POST",
			url: "saveHdr.php",
			data: dataString,
			cache: false,
			async: false,
			success: function(data) {
				SINo = data.trim();
				//alert(html);
				$("#PayAlert").html("Saving Now..."+SINo);
			},
			error: function (req, status, err) {
				console.log('Something went wrong', status, err)
							
				$("#PayAlert").html('Something went wrong<br><br>'+status+"<br><br>"+err);
				$("#PayAlert").show();
		
			}

		});
	
	//IF header retuned false popup alert else save details
	if(SINo=="False"){
		$("#PayAlert").html("<center><div class='alert alert-danger'>There's a problem saving the transaction. <br> Please consult your administrator</div><br><br> <button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Close</button> </center>");
		$("#PayAlert").show();
	}
	else{
		//SaveDetails
		
			$.each( $('#TblItems'), function(index) {	

				var citmno = $(this).find('input[type="hidden"][name="txtcitemno[]"]').val();
				var cuom = $(this).find('input[type="hidden"][name="txtcunit[]"]').val();
				var nqty = $(this).find('input[type="hidden"][name="txtnqty[]"]').val();
				var nprice = $(this).find('input[type="hidden"][name="txtnprice[]"]').val();
				var namt = $(this).find('input[type="hidden"][name="txtnamt[]"]').val();
		
				$.ajax ({
					url: "Savedet.php",
					data: { trancode: SINo, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});

		
	}


			if(isDone=="True"){
				
				$.ajax ({
					dataType: "text",
					url: "SI_SMS.php",
					data: { x: SINo },
					async: false,
					success: function( data ) {
						//WALA GAGAWIN
					}
				});


				$("#PayAlert").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
				$("#PayAlert").show();

					setTimeout(function() {
							location.href="index.php";
			
					}, 2000); // milliseconds = 2seconds

				
			}
	
	return false;
	
}


// END SAVING THE TRANSACTION

</script>