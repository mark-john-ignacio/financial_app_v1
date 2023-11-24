<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

$company = $_SESSION['companyid'];

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>

        <?php
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

	<title>Coop Financials</title>
    
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>">
<link href="../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/DigiClock.css"> 

  <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick-theme.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slicksize.css">
  <link rel="stylesheet" type="text/css" href="../Bootstrap/css/keypadz.css">


<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>

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
		location.href="../logout.php";
	  }
	  else if (e.keyCode == 13 && e.ctrlKey) {
        alert('ctrl Entr');
      }
	  
	});

</script>
</head>

<body style="background-color:#2b3643">
<form name="frmWIN" id="frmWIN" action="" method="post">
<div class="container-fluid">
<div class="col-lg-12 nopadwtop2x">

	<div class="col-lg-6 nopadding">

        <div style="display:table;background-color: #f5f5f5; padding:15px;">
        
    		<!-- Time user -->
            	<div class="col-lg-12 nopadding">
                <div class="col-lg-6 nopadding" id="clock1">
                    <span class="date">
                    Cashier: <?php echo $_SESSION['employeename']; ?>
                    </span>
                    
                </div>
                <div class="col-lg-6 nopadwleft" id="clock">
                        <span class="date">{{ date }}</span>
                        <span class="time">{{ time }}</span>
                </div>
            </div> 
        
        	<!-- Customer type -->
            	<div class="col-lg-12 nopadwtop">
            
                <div class="col-lg-6 nopadding">
                        <select class="form-control input-sm" name="selCust" id="selCust">
                            <option value="WIN">Walk In Customer</option>
                            <option value="CUS">Select from List</option>
                        </select>
                </div>
                <div class="col-lg-6 nopadwleft">
                    <div class="input-group margin-bottom-sm">
                      <input type="text" name="ccustname" id="ccustname" class="form-control input-sm " placeholder=""> 
                      <input type="hidden" name="ccustid" id="ccustid"> 
                      <input type="hidden" name="ccustcredit" id="ccustcredit">
                      <input type="hidden" name="ccustbal" id="ccustbal">
                       <span class="input-group-addon"><i class="fa fa fa-search fa-fw"></i></span>
                    </div>
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
                <div style="height:55vh;" class="col-lg-12 nopadding pre-scrollable" id="TblItems">
                
                </div> 
                
    		<!-- Total -->
                <div class="col-lg-12 nopadding">
                    <div class="col-lg-3 divbak" style="text-align:right; padding:10px; font-size:20px">
                        <b>Total Items:</b>
                    </div>
                    <div class="col-lg-3 divbak" style="padding:10px; font-size:20px"" id="ItmTotQty">
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
                    100
                    </div>          
    
                 </div> 
            

            </div>
        
        </div> <!-- DISPLAT TABLE -->   
   </div> <!-- col-lg-5 -->
    
  	<div class="col-lg-6 nopadwleft">
    
     
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
        <div class="modal-dialog vertical-align-center">
            <div class="modal-content">
               <div class="modal-header">
					<h4 class="nopadding">Payment</h4>
               </div>

				   <div class="row" style="padding-left:15px; padding-right:20px">
                   
                        <div class="col-sm-6 b-col">
                          
                          	<h4>Total Amount</h4>
                            <div id="divGrandTot" class="well well-sm nopadding" style="text-align:right; font-size:24px">
								0.00&nbsp;
                            </div>
                            
                            <input type="hidden" name="GrandTot" id="GrandTot" value="0.00">
                        
                            		 <div class="col-sm-12 nopadwtop2x"> &nbsp;<b>VATABLE</b> </div>
                                     <div class="col-sm-12 nopadwtop2x"> &nbsp;<b>VAT EXEMPT</b> </div>
                                     <div class="col-sm-12 nopadwtop2x"> &nbsp;<b>VAT ZERO RATED</b> </div>
                                     <div class="col-sm-12 nopadwtop2x"> &nbsp;<b>VAT</b> </div>

                           <div class="col-sm-12 nopadwtop">
                            <h4>Amount Tendered</h4> 
                            <input type="text" class="numeric form-control input-md nopadwright2x" name="GrandPayed" id="GrandPayed" style="text-align:right;  font-size:24px" value="0.00">
                           </div>
							
                           <div class="col-sm-12 nopadwtop"> 
                          	<h4>Change</h4>
                            <div id="divGrandChange" class="well well-sm nopadding" style="text-align:right; font-size:24px">
								0.00&nbsp;
                            </div>
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
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btn btn-default" data-val="4">4</button>
                                    <button type="button" class="btnpad btn btn-default" data-val="5">5</button>
                                    <button type="button" class="btnpad btn btn-default" data-val="6">6</button>
                                    <button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="200">200</button>
                                </div>
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btn btn-default" data-val="1">1</button>
                                    <button type="button" class="btnpad btn btn-default" data-val="2">2</button>
                                    <button type="button" class="btnpad btn btn-default" data-val="3">3</button>
                                    <button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="500">500</button>
                                </div>
                                <div class="jqbtk-row">
                                
                                    <button type="button" class="btnpad btn btn-default" data-val=".">.</button>
                                    <button type="button" class="btnpad btn btn-default" data-val="0">0</button>
                                    <button type="button" class="btnpad btn btn-default" data-val="DEL"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                    <button type="button" class="btnpad btn btn-info jqbtk-shift" data-val="1000">1000</button>
                                </div>
                                <div class="jqbtk-row">

                                	<button type="button" class="btnpad btn btn-success jqbtk-space" data-val="EXACT">EXACT</button>
                                    
                                </div>
                                 <div class="jqbtk-row">

                                	<button type="button" class="btnpad btn btn-danger jqbtk-space" data-val="CLEAR">CLEAR</button>
                                    
                                </div>
                             </div>    

                        </div>
                        
                    
                    </div>

               <div class="modal-footer">
					<button type="button" class="btn btn-success btn-lg" data-dismiss="modal">Done (CTRL + ENTER)</button> 
                    <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Close</button>
               </div>
            </div>
        </div>
    </div>
</div>



  <script src="../Bootstrap/slick/slick.js" type="text/javascript" charset="utf-8"></script>
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
<script src="../Bootstrap/js/vue.js"></script>
<script src="../Bootstrap/js/DigiClock.js"></script>

<script type="text/javascript">

$(function() { 
//to initialize readonly customer upon loading
$("#ccustname").attr('readonly', true); citemno
$("#citemno").focus();
$("input.numeric").numeric();

$("input.numeric").on("click", function () {
	$(this).select();
});

	$("#btnCancel").on("click", function() {
		location.reload();
	});

	$("#btnPay").on("click", function() {
		checkform();
	});

	$("#btnExit").on("click", function() {
		location.href="../logout.php";
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
			//alert(itmcode[1]);
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
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.value+'</span><br><small><span class="dropdown-item-extra">' + item.nprice + '</span> '+item.nbal+ ' ' + item.cunit + '</small></div>';
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
		
		if(yz==1000 || yz==500 || yz==200 || yz==100){
			xy = "";
		}
				
		$('#GrandPayed').val(xy+yz);
		
	});

});

function setdel(x){
	$("#Itm"+x).remove();
	CompAmt();
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
	
function getDetails(xy,finqty){
	var code = "";
	
	$.post('th_getPartNo.php',{'q':xy },function( data ){ //send value to post request in ajax to the php page
			getFromScan(data,xy,finqty);
    });
		
	
}


	
function getFromScan(code,xy,finqty){
//check if nsa table na
var isadd = "YES";
var finprice = "";
var finamt = 0;

//alert('#Itm'+code);
//alert($('#Itm'+code).length);

if ($('#Itm'+code).length!=0) {
  isadd = "NO";
}

				if(isadd=="NO"){
					varnavail = $('#Itm'+code+' input[name="txtnavail[]"]').val();
					varnqty = $('#Itm'+code+' input[name="txtnqty[]"]').val();
					varnprice = $('#Itm'+code+' input[name="txtnprice[]"]').val();
					varnamt = $('#Itm'+code+' input[name="txtnamt[]"]').val();
					varcunit = $('#Itm'+code+' input[name="txtcunit[]"]').val();
					vardesc = $('#Itm'+code+' p[id="pD'+code+'"]').html()
					
											
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

					
					$('#Itm'+code+' p[id="pD'+code+'"]').html("<font size=\"-1\"> " + vardesc + "</font>");
					$('#Itm'+code+' p[id="pQ'+code+'"]').html(finqty + " " + finqtydesc);
					$('#Itm'+code+' p[id="pP'+code+'"]').text(finamt);
					
					
					$('#Itm'+code+' input[name="txtnqty[]"]').val(finqty);
					$('#Itm'+code+' input[name="txtnamt[]"]').val(finamt);
					
					CompAmt();

				}
				else if(isadd == "YES"){
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
							 
							finqtydesc = "";
							 
							if(finqty==1){
								var finprice = "</div>";
								var finamt = parseFloat(item.nprice);
							}
							else{
								
								if(parseFloat(finqty) > parseFloat(item.nqty)){
									finqtydesc = " <font color=\"#FF0000\"> ("+finqty+")</font>";
									finqty = parseFloat(item.nqty);
									
								}
								
								var finprice = "<p style=\"text-indent: 20px\">@ "+item.nprice+"/"+item.cunit+"</p></div>";
								var finamt = parseFloat(item.nprice) * parseFloat(finqty);
							}
							
							finamt = finamt.toFixed(4);
							
							var divhead = "<div class=\"col-lg-12 nopadding\" id=\"Itm"+item.citemno+"\">";
							var divVALS = "<input type=\"hidden\" name=\"txtcitemno[]\" id=\"txtcitemno[]\" value=\""+item.citemno+"\"  data-scancode=\""+item.cscancode+"\" /> \
							<input type=\"hidden\" name=\"txtncost[]\" id=\"txtncost[]\" value=\""+item.ncost+"\" data-citmno=\""+item.citemno+"\" /> \
							<input type=\"hidden\" name=\"txtcunit[]\" id=\"txtcunit[]\" value=\""+item.cunit+"\" data-citmno=\""+item.citemno+"\" /> \
							<input type=\"hidden\" name=\"txtnavail[]\" id=\"txtnavail[]\" value=\""+item.nqty+"\" data-citmno=\""+item.citemno+"\" /> \
							<input type=\"hidden\" name=\"txtnqty[]\" id=\"txtnqty[]\" value=\""+finqty+"\" data-citmno=\""+item.citemno+"\" /> \
							<input type=\"hidden\" name=\"txtnamt[]\" id=\"txtnamt[]\" value=\""+finamt+"\" data-citmno=\""+item.citemno+"\" /> \
							<input type=\"hidden\" name=\"txtnprice[]\" id=\"txtnprice[]\" value=\""+item.nprice+"\" data-citmno=\""+item.citemno+"\" />";
							
							var divdel = "<div class=\"col-lg-1\"> <b><a href=\"javascript:;\" onClick=\"setdel('"+item.citemno+"')\" class=info><i class=\"fa fa-times-circle\" style=\"font-size:20px;color:red;\" title=\"Delete Item\"></i></a></b> </div>";
							var divitm = "<div class=\"col-lg-4\"><p id=\"pD"+item.citemno+"\"><font size=\"-1\"> " + item.cdesc + finprice + "</font></p>";
							var divonhand = "<div class=\"col-lg-2\"><p style=\"text-indent: 10px\"> "+item.nqty+" </p></div>";
                			var divqty = "<div class=\"col-lg-2\"><p style=\"text-indent: 10px\" id=\"pQ"+item.citemno+"\" > " + finqty + finqtydesc + " </p></div>";
              				var divprice = "<div class=\"col-lg-3\"><p style=\"text-align:right\"  id=\"pP"+item.citemno+"\"> "+finamt+"</p></div>"
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

function chkbalance(x){

	if(x!=""){
		var ddate = new Date();
		
		$.ajax ({
				url: "get_creditbal.php",
				data: { code: x,  date: ddate},
				success: function( result ) {
					
						var valz = $("#ccustcredit").val();
						var Tot = parseFloat(valz) - parseFloat(result);
						
						if(Tot.toFixed(4) > 0){
							$("#ccustbal").val(Tot.toFixed(4));
							$("#divCreditBal").text(Tot.toFixed(4));
						}
						else{
							$("#ccustbal").val(0);
							$("#divCreditBal").text(Tot.toFixed(0));
						}
					
					//computeDue();
				},
				error: function (req, status, err) {
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
				
		rescale();
		
		
	}

}


function rescale(){
    var size = {width: $(window).width() , height: $(window).height() }
    /*CALCULATE SIZE*/
    var offset = 20;
    var offsetBody = 250;
    $('#myModal').css('height', size.height - offset );
    $('.modal-body').css('height', size.height - (offset + offsetBody));
    $('#myModal').css('top', 0);
}

$(window).bind("resize", rescale);
</script>