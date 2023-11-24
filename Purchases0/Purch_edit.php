<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Purch_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];
$cpono = $_REQUEST['txtcpono'];

$sqlhead = mysqli_query($con,"select a.cpono, a.ccode, a.cremarks, a.cpurchasetype, a.ddate, DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, DATE_FORMAT(a.dneeded,'%m/%d/%Y') as dneeded, a.ngross, a.cpreparedby, a.lcancelled, a.lapproved, a.lprintposted, a.ccustacctcode, b.cname from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.cpono = '$cpono'");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="disabled(); document.getElementById('txtcpono').focus(); ">
<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$DateNeeded = $row['dneeded'];
		$SalesType = $row['cpurchasetype'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
?>
<form action="Purch_editsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Purchase Order</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>PO NO.:</tH>
    <td colspan="2" style="padding:2px"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
      
      
      <input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cpono;?>">
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
    </td>
    <td style="padding:2px" align="center">
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
    </td>
    </tr>
  <tr>
    <tH width="100">SUPPLIER:</tH>
    <td style="padding:2px">
    	<div class="col-xs-5">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..." value="<?php echo $CustName;?>" autocomplete="off">
        </div> 
        &nbsp;&nbsp;
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px" readonly value="<?php echo $CustCode;?>">
    </td>
    <tH width="150">DATE:</tH>
    <td style="padding:2px;">
     <div class="col-xs-8">
		<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $Date; ?>" readonly/>

     </div>
    </td>
  </tr>
  <tr>
    <tH width="100">REMARKS:</tH>
    <td style="padding:2px"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?php echo $Remarks; ?>"></div></td>
    <tH width="150" style="padding:2px">DATE NEEDED:</tH>
    <td style="padding:2px">
    <div class="col-xs-8">
		<input type='text' class="datepick form-control input-sm" id="date_needed" name="date_needed" value="<?php echo $DateNeeded; ?>" />

     </div>
    </td>
  </tr>
  
    <tr>
    <td colspan="2">&nbsp;</td>
    <th style="padding:2px"><!--<span style="padding:2px">PURCHASE TYPE:</span>--></th>
    <td>
    &nbsp;
    <!--<div class="col-xs-5">
        <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="3">
          <option value="Grocery" <?php // if($SalesType=="Grocery"){ echo "selected"; } ?>>Grocery</option>
          <option value="Cripples" <?php //if($SalesType=="Cripples"){ echo "selected"; } ?>>Cripples</option>
        </select>
   </div>--></td>
    </tr>

  <tr>
    <td colspan="4">&nbsp;</td>
    </tr>
<tr>
    <td colspan="2">
      <div class="col-xs-12 nopadwdown">
        <div class="col-xs-3 nopadding">
          <input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." width="25" tabindex="4">
        </div>

        <div class="col-xs-8 nopadwleft">
          <input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="Search Product Name..." size="80" tabindex="5">
        </div>
      </div>

		<input type="hidden" name="hdnprice" id="hdnprice">
        <input type="hidden" name="hdnunit" id="hdnunit">
        <input type="hidden" name="hdnfactor" id="hdnfactor">
    </td>
    <td><b>TOTAL AMOUNT : </b></td>
    <td><input type="text" id="txtnGross" name="txtnGross" readonly value="<?php echo $Gross; ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>

</tr>
</table>
         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">

					<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description</th>
                        <th style="border-bottom:1px solid #999">UOM</th>
						<th style="border-bottom:1px solid #999">Qty</th>
						<th style="border-bottom:1px solid #999">Price</th>
						<th style="border-bottom:1px solid #999">Amount</th>
                        <th style="border-bottom:1px solid #999">Date Needed</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
					<tbody class="tbody">
                    <?php 
						$sqlbody = mysqli_query($con,"select a.citemno, a.cunit, a.nfactor, a.nqty, a.nprice, a.namount, DATE_FORMAT(a.ddateneeded,'%m/%d/%Y') as ddateneeded, b.citemdesc from purchase_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.cpono = '$cpono' order by nident");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
						
					?>
					<tr>
						<td><input type='hidden' value='<?php echo $rowbody['citemno'];?>' name='txtitemcode<?php echo $cntr; ?>' id='txtitemcode<?php echo $cntr; ?>'><?php echo $rowbody['citemno'];?></td>
						<td><input type='hidden' value='<?php echo $rowbody['citemdesc'];?>' name='txtitemdesc<?php echo $cntr; ?>' id='txtitemdesc<?php echo $cntr; ?>'><?php echo $rowbody['citemdesc'];?></td>
                        <td><input type='hidden' value='<?php echo $rowbody['cunit'];?>' name='txtcunit<?php echo $cntr; ?>' id='txtcunit<?php echo $cntr; ?>'><?php echo $rowbody['cunit'];?></td>
						<td width="100px" style="padding:1px"><input type='text' value='<?php echo $rowbody['nqty'];?>' class='numeric form-control input-xs' style='text-align:right' name='txtnqty<?php echo $cntr; ?>' id='txtnqty<?php echo $cntr; ?>' /></td>
						<td width="100px" style="padding:1px"><input type='text' value='<?php echo $rowbody['nprice'];?>' class='numeric form-control input-xs' style='text-align:right' name='txtnprice<?php echo $cntr; ?>' id='txtnprice<?php echo $cntr; ?>' /> <input type='hidden' value='<?php echo $rowbody['nfactor'];?>' name='hdnfactor<?php echo $cntr; ?>' id='hdnfactor<?php echo $cntr; ?>'></td>
						<td width="100px" style="padding:1px"><input type='text' value='<?php echo $rowbody['namount'];?>' class='form-control input-xs' style='text-align:right' name='txtnamount<?php echo $cntr; ?>' id='txtnamount<?php echo $cntr; ?>' readonly></td>
                        <td width="80px" align="right" style="position:relative">
                        <input type='text' class='datepick form-control input-xs' id='dneed<?php echo $cntr; ?>' name='dneed<?php echo $cntr; ?>' value='<?php echo $rowbody['ddateneeded'];?>' /></a>
                        </td>
                        <td width="80px" align="right">
                        
                      <input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr; ?>_delete' value='delete' onClick="deleteRow(this);"/></td>
                      	
					</tr>
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
    <input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
 
 
 <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Purch.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Purch_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(F3)
    </button>

   <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cpono;?>');" id="btnPrint" name="btnPrint">
Print<br>(F4)
    </button>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(F8)    </button>
    
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(F2)    </button>
    
    </td>

  </tr>
</table>

    </fieldset>
</form>
<?php
}
else{
?>
<form action="Purch_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Purchase Order</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">PO NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PO No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>



<!-- PRINT OUT MODAL-->
<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-contnorad">   
            <div class="modal-bodylong">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
        
               <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
    
            	
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

</body>
</html>

<script type="text/javascript">

	$(document).keypress(function(e) {	 
	 if(e.keyCode == 112) { //F1
		if(document.getElementById("btnNew").className=="btn btn-default btn-sm"){
			window.location.href='Purch_new.php';
		}
	  }
	  else if(e.keyCode == 113){//F2
		if(document.getElementById("btnSave").className=="btn btn-success btn-sm"){
			return chkform();
		}
	  }
	  else if(e.keyCode == 119){//F8
		if(document.getElementById("btnEdit").className=="btn btn-warning btn-sm"){
			enabled();
		}
	  }
	  else if(e.keyCode == 115){//F4
		if(document.getElementById("btnPrint").className=="btn btn-info btn-sm"){
			printchk('<?php echo $cpono;?>');
		}
	  }
	  else if(e.keyCode == 114){//F3
		if(document.getElementById("btnUndo").className=="btn btn-danger btn-sm"){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if(document.getElementById("btnMain").className=="btn btn-primary btn-sm"){
			e.preventDefault();
			$("#btnMain").click();
		}
	  }

	});


$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });
	
	
	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_supplier.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
		}
	});
	
	$('#txtprodnme').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "th_product.php",
				dataType: "json",
				data: {
					query: $("#txtprodnme").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.value+'</span><br><small><span class="dropdown-item-extra">' + item.nprice + '</span></small></div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					
	
		$('.datepick').each(function(){
			$(this).data('DateTimePicker').destroy();
		});
						
			$('#txtprodnme').val(item.value).change(); 
			$('#txtprodid').val(item.id); 
			$("#hdnprice").val(item.nprice);
			$("#hdnunit").val(item.cunit);
			$("#hdnfactor").val(item.nfactor);
			
			addItemName("");
					
			$('.datepick').datetimepicker({format: 'MM/DD/YYYY'});
		}
	
	});


	$("#txtprodid").keyup(function(e){
		if(e.keyCode == 13){

		$.ajax({
        type:'post',
        url:'get_productid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){
			
            var data = value.split(",");
            $('#txtprodid').val(data[0]);
            $('#txtprodnme').val(data[1]);
			$('#hdnprice').val(data[2]);
			$('#hdnunit').val(data[3]);
			$('#hdnfactor').val(data[4]);
		

		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
			var rowCount = $('#MyTable tr').length;
			var isItem = "NO";
			var itemindex = 1;
		
			if(rowCount > 1){
			 var cntr = rowCount-1;
			 
			 for (var counter = 1; counter <= cntr; counter++) {
				// alert(counter);
				if($("#txtprodid").val()==$("#txtitemcode"+counter).val()){
					isItem = "YES";
					itemindex = counter;
					//alert($("#txtitemcode"+counter).val());
					//alert(isItem);
				//if prd id exist
				}
			//for loop
			 }
		   //if rowcount >1
		   }
		//if value is not blank
		 }
		 
		if(isItem=="NO"){		

		$('.datepick').each(function(){
			$(this).data('DateTimePicker').destroy();
		});

			myFunctionadd();
			computeGross();	
		
		$('.datepick').datetimepicker({format: 'MM/DD/YYYY'});
			
	    }
	    else{
			//alert("ITEM NOT IN THE MASTERLIST!");
			addqty();
		}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnprice").val("");
		$("#hdnunit").val("");
		$("#hdnfactor").val("");
 
	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if ebter is clicked
		}
		
	});
	

});

function addItemName(tranno){
	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
		var rowCount = $('#MyTable tr').length;
		var isItem = "NO";
		var itemindex = 1;
		
		if(rowCount > 1){
			 var cntr = rowCount-1;
			 
			 for (var counter = 1; counter <= cntr; counter++) {
				// alert(counter);
				if($("#txtprodid").val()==$("#txtitemcode"+counter).val()){
					isItem = "YES";
					itemindex = counter;
					//alert($("#txtitemcode"+counter).val());
					//alert(isItem);
				}
			 }
		 }
		 
	 if(isItem=="NO"){	
	 //	myFunctionadd(tranno);
	//	ComputeGross();	
			myFunctionadd();		
			computeGross();	
	 }
	 else{
		
		addqty();	
			
	 }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnprice").val("");
		$("#hdnunit").val("");
		$("#hdnfactor").val("");
		
	 }

}

function myFunctionadd(){
	//alert("hello");
	
	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmprice = document.getElementById("hdnprice").value;
	var itmunit = document.getElementById("hdnunit").value;
	var dneeded= document.getElementById("date_needed").value;
	var itmfactor= document.getElementById("hdnfactor").value;
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('MyTable').insertRow(-1);
	var u=a.insertCell(0);
	var v=a.insertCell(1);
	var v2=a.insertCell(2);
	var w=a.insertCell(3);
		w.style.width = "100px";
		w.style.padding = "1px";
	var x=a.insertCell(4);
		x.style.width = "100px";
		x.style.padding = "1px";
	var y=a.insertCell(5);
		y.style.width = "100px";
		y.style.padding = "1px";
	var dt=a.insertCell(6);
		dt.style.width = "80px";
		dt.style.position = "relative";
		dt.align = "right";
	var z=a.insertCell(7);
		z.style.width = "80px";
		z.align = "right";
	
	u.innerHTML = "<input type='hidden' value='"+itmcode+"' name='txtitemcode"+lastRow+"' id='txtitemcode"+lastRow+"'>"+itmcode;
	v.innerHTML = "<input type='hidden' value='"+itmdesc+"' name='txtitemdesc"+lastRow+"' id='txtitemdesc"+lastRow+"'>"+itmdesc;
	v2.innerHTML = "<input type='hidden' value='"+itmunit+"' name='txtcunit"+lastRow+"' id='txtcunit"+lastRow+"'>"+itmunit;
	w.innerHTML = "<input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name='txtnqty"+lastRow+"' id='txtnqty"+lastRow+"' />";
	x.innerHTML = "<input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right' name='txtnprice"+lastRow+"' id='txtnprice"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor"+lastRow+"' id='hdnfactor"+lastRow+"'> ";
	y.innerHTML = "<input type='text' value='"+itmprice+"' class='form-control input-xs' style='text-align:right' name='txtnamount"+lastRow+"' id='txtnamount"+lastRow+"' readonly>";
	dt.innerHTML = "<input type='text' class='datepick form-control input-xs' id='dneed"+lastRow+"' name='dneed"+lastRow+"' value='"+dneeded+"' />";
	z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='delete' onClick=\"deleteRow(this);\"/>";


									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {
									  computeamt($(this).attr('name'));
									});
									
									computeGross();

}

function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTable').deleteRow(i);
	 document.getElementById('hdnrowcnt').value = lastRow - 2;
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempcitemno = document.getElementById('txtitemcode' + z);
			var tempcdesc = document.getElementById('txtitemdesc' + z);
			var tempnqty= document.getElementById('txtnqty' + z);
			var tempcunit= document.getElementById('txtcunit' + z);
			var tempnprice = document.getElementById('txtnprice' + z);
			var tempfactor = document.getElementById('hdnfactor' + z);
			var tempnamount= document.getElementById('txtnamount' + z);
			var tempdneeded= document.getElementById('dneed' + z);
			var tempdneedhref= document.getElementById('dneedhref' + z);
			
			var x = z-1;
			tempcitemno.id = "txtitemcode" + x;
			tempcitemno.name = "txtitemcode" + x;
			tempcdesc.id = "txtitemdesc" + x;
			tempcdesc.name = "txtitemdesc" + x;

			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode) };

			tempnqty.id = "txtnqty" + x;
			tempnqty.name = "txtnqty" + x;
			tempcunit.id = "txtcunit" + x;
			tempcunit.name = "txtcunit" + x;
			tempnprice.id = "txtnprice" + x;
			tempnprice.name = "txtnprice" + x;
			tempfactor.id = "hdnfactor" + x;
			tempfactor.name = "hdnfactor" + x;
			tempnamount.id = "txtnamount" + x;
			tempnamount.name = "txtnamount" + x;
			tempdneeded.id = "dneed" + x;
			tempdneeded.name = "dneed" + x;
			tempdneedhref.id = "dneedhref" + x;
			tempdneedhref.name = "dneedhref" + x;
						

		}
		
	computeGross();
}

function computeamt(nme){
	
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val();
			nqty = parseFloat(nqty)
			nnet = $("#txtnprice"+r).val();
			nnet = parseFloat(nnet);
			
			namt = nqty * nnet;
			namt = namt.toFixed(4);
						
			$("#txtnamount"+r).val(namt);

			computeGross();
			

	
}

function computeGross(){
	
			var rowCount = $('#MyTable tr').length;
			var gross = 0;
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtnamount"+i).val();
					
					gross = gross + parseFloat(amt);
				}
			}
			
			$("#txtnGross").val(gross);


}

function addqty(){
	
	var itmcode = document.getElementById("txtprodid").value;
	//var itmdesc = document.getElementById("txtprodnme").value;
	//
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	var TotQty = 0;
	var TotAmt = 0;
	
	for (z=1; z<=lastRow; z++){
		if(document.getElementById("txtitemcode"+z).value==itmcode){
			var itmqty = document.getElementById("txtnqty"+z).value;
			var itmprice = document.getElementById("txtnprice"+z).value;
			
			TotQty = parseFloat(itmqty) + 1;
			document.getElementById("txtnqty"+z).value = TotQty;
			
			TotAmt = parseFloat(document.getElementById("txtnamount" + z).value) + parseFloat(itmprice);
			document.getElementById("txtnamount" + z).value = TotAmt.toFixed(2);
		}

	}
	
computeGross();

}

function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){
		alert("Customer Required!");
		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(lastRow == 0){
		alert("No details found!");
		return false;
		ISOK = "NO";
	}
	else{
		var msgz = "";
		for (z=1; z<=lastRow; z++){
			if(document.getElementById("txtnqty"+z).value == 0 || document.getElementById("txtnqty"+z).value == ""){
				msgz = msgz + "\n Zero or blank qty is not allowed: row " + z;	
			}
		}
		
		if(msgz!=""){
			alert("Details Error: "+msgz);
			return false;
			ISOK = "NO";
		}
	}
	
	if(ISOK == "YES"){
		document.getElementById("hdnrowcnt").value = lastRow;
		document.getElementById("frmpos").submit();
	}

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Purch_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){
	$("#frmpos :input").attr("disabled", true);
	
	
	$("#txtcpono").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
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
		
		document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			$("#txtcpono").val($("#hdntranno").val());
			$("#txtcpono").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
	
	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{
		  var url =  "Purch_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		$("#PrintModal").modal('show');

	}
}
</script>

