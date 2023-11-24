<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];
$corno = $_REQUEST['txtctranno'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../include/jquery-maskmoney.js" type="text/javascript"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px; height:700px" onLoad="disabled();">
<?php

    	$sqlchk = mysqli_query($con,"Select a.cacctcode, a.namount, a.cortype, DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, a.namount, a.lapproved, a.lcancelled, a.lprintposted, a.cremarks, c.cacctdesc, c.nbalance From deposit a left join accounts c on a.compcode=c.compcode and a.cacctcode=c.cacctno where a.compcode='$company' and a.ctranno='$corno'");
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cacctcode'];
			$nDebitDesc = $row['cacctdesc'];	
			$nBalance = $row['nbalance'];		 
			$cPayMeth = $row['cortype'];
			$dDate = $row['dcutdate'];
			$nAmount = $row['namount'];
			$cRemarks = $row['cremarks'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
		}

?>

<form action="Deposit_editsave.php" name="frmOR" id="frmOR" method="post">
	<fieldset>
    	<legend>Bank Deposit</legend>	
        <table width="100%" border="0">
          <tr>
    <tH>Deposit No.:</tH>
    <td colspan="3" style="padding:2px;">
    <div class="col-xs-12">
    <div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmOR');"></div>
      
      <input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $corno;?>">
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      <input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
      </div>
      
    </td>
    </tr>

  <tr>
    <tH width="210">
    	
    	Deposit To Account:
    </tH>
    <td style="padding:2px;" width="500">
  <div class="col-xs-12">
    <div class="col-xs-8">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
    </div> 

        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
   </div>     
    </td>
    <tH width="150">Balance:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8">
    <input type="text" id="txtacctbal" name="txtacctbal" class="form-control input-sm" readonly value="<?php echo $nBalance;?>">
    </div>
    </td>
  </tr>
  <tr>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH width="210" valign="top">Receipt By:</tH>
    <td valign="top" style="padding:2px">
      
      
      <div class="col-xs-12">
        <div class="col-xs-6">
          <select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
            <option value="Cash" <?php if($cPayMeth=="Cash"){ echo "selected"; }?>>Cash</option>
            <option value="Cheque" <?php if($cPayMeth=="Cheque"){ echo "selected"; }?>>Cheque</option>
            <option value="All" <?php if($cPayMeth=="All"){ echo "selected"; }?>>All Methods</option>
          </select>
          </div>      
      
      </td>
    <tH style="padding:2px">DATE:</tH>
    <td style="padding:2px"><div class="col-xs-8">
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dDate; ?>"/>
      <!--</a>-->
    </div></td>
  </tr>
  <tr>
    <tH width="210" rowspan="2" valign="top">MEMO:</tH>
    <td rowspan="2" valign="top" style="padding:2px"><div class="col-xs-12">
      <div class="col-xs-10">
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cRemarks; ?></textarea>
      </div>
    </div></td>
    <th valign="top" style="padding:2px">AMOUNT RECEIVED:</th>
    <td valign="top" style="padding:2px"><div class="col-xs-8">
      <input type="text" id="txtnGross" name="txtnGross" class="form-control" value="<?php echo $nAmount;?>" readonly>
    </div></td>
    </tr>
  <tr>
    <th valign="top" style="padding:2px">&nbsp;</th>
    <td valign="top" style="padding:2px">&nbsp;</td>
  </tr>
      </table>
<br>

<button type="button" class="btn btn-xs btn-info" onClick="getInvs();">

	<!--<button type="button" class="btn btn-xs btn-primary" onClick="popup('add_asset.asp?types=asset');" name="openBtn" id="openBtn">-->
  	<table border="0">
    <tr>
      <td valign="top"><img src="../images/Find.png" border="0" height="20" width="20" />&nbsp;</td>
      <td>Load OR</td>
    </tr>
  	</table>
    </button>

    <br><br>
	  <div id="tableContainer" class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 200px;
                        text-align: left;
                        overflow: auto">
                        
            <table width="100%" border="0" cellpadding="3" id="MyTable" class="table table-striped">
            <thead>
              <tr>
                <th scope="col" width="15%">Trans No</th>
                <th scope="col">OR No.</th>
                <th scope="col">Date</th>
                <th scope="col">Remarks</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Amount</th>
                <th scope="col">&nbsp;</th>
              </tr>
            </thead>
            <tbody>
        <?php
			
			$sqlbody = mysqli_query($con,"select a.*, b.cornumber, b.dcutdate, b.cremarks, b.cpaymethod, b.namount from deposit_t a left join receipt b on a.compcode=b.compcode and a.corno=b.ctranno and a.compcode=b.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
		?>
			  <tr>
              	<td><div class='col-xs-12'><input type='hidden' name='txtcSalesNo<?php echo $cntr;?>' id='txttranno' value='<?php echo $rowbody['corno'];?>' /><?php echo $rowbody['corno'];?></div></td>
                <td><?php echo $rowbody['cornumber'];?></td>
                <td><?php echo $rowbody['dcutdate'];?></td>
                <td><?php echo $rowbody['cremarks'];?></td>
                <td><?php echo $rowbody['cpaymethod'];?></td>
                <td align='right'><div class='col-xs-12'><input type='hidden' name='txtnAmt<?php echo $cntr;?>' id='txtAmt' value='<?php echo $rowbody['namount'];?>' /><?php echo $rowbody['namount'];?></div></td>
                <td align='center'><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick='deleteRow(this);' /></td>
              </tr>
	    <?php
							}
						}

		?>
	

            </tbody>
            </table>
<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">


</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Deposit.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Deposit_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmOR');" id="btnUndo" name="btnUndo">
Undo Edit<br>(F3)
    </button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $corno;?>');" id="btnPrint" name="btnPrint">
Print<br>(F4)
    </button>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(F8)    </button>
    
    <button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
Save<br>(F2)    </button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>



<!-- Bootstrap modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">OR List</h3>
            </div>
            
            <div class="modal-body">
            
            	
                  <table name='MyORTbl' id='MyORTbl' class="table">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                      <th>Trans No</th>
                      <th>OR No</th>
                      <th>OR Date</th>
                      <th>Gross</th>
                      <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>
                
            
			</div>
			
            <div class="modal-footer">
                
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

</form>

<?php
}
else{
?>

<form action="Deposit_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Bank Deposit</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">Deposit No.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>OR No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>


<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 112) { //F1
		if(document.getElementById("btnNew").className=="btn btn-default btn-sm"){
			window.location.href='Deposit_new.php';
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
			printchk('<?php echo $corno;?>');
		}
	  }
	  else if(e.keyCode == 114){//F3
		if(document.getElementById("btnUndo").className=="btn btn-danger btn-sm"){
			e.preventDefault();
			chkSIEnter(13,'frmOR');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if(document.getElementById("btnMain").className=="btn btn-primary btn-sm"){
			e.preventDefault();
			window.location.href='Deposit.php';
		}
	  }
	});



$(function() {              
           // Bootstrap DateTimePicker v4
	        $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
	   
});
			
$('#txtcacct').typeahead({

    source: function (query, process) {
        return $.getJSON(
            'th_accounts.php',
            { query: query },
            function (data) {
				newData = [];
				map = {};
				
				$.each(data, function(i, object) {
					map[object.name] = object;
					newData.push(object.name);
				});
                
				process(newData);
            });
    },
	updater: function (item) {	
		  	
			$('#txtcacctid').val(map[item].id);
			$('#txtacctbal').val(map[item].balance);
			return item;
	}

});
	

 $("#allbox").click(function () {
        if ($("#allbox").is(':checked')) {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", false);
            });
        }
    });


function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTable').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempsalesno =  $('input[name=txtcSalesNo'+z+']');
			var tempamt =  $('input[name=txtnAmt'+z+']');
			
			var x = z-1;
			tempsalesno.attr("name", "txtcSalesNo" + x);	
			tempamt.attr("name", "txtnAmt" + x);		
			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

		}

computeGross();

}

function computeGross(){
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var x = 0;
	var tot = 0;
	for (z=1; z<=lastRow-1; z++){
		x = $('input[name=txtnAmt'+z+']').val();
		
		x = x.replace(",","");
		if(x!=0 && x!=""){
		var tot = parseFloat(x) + parseFloat(tot);	
		}
	}
	
	//alert(tot);
	document.getElementById('txtnGross').value = tot.toFixed(2);

}

function getInvs(){
	
		if($('#selpayment').val() == ""){
			alert("Cannot read Receipt By!");
		}
		else{
			
			//clear table body if may laman
			

			$('#MyORTbl tbody').empty();
			
			//get or na selected na
			var y;
			var salesnos = "";
			var rc = $('#MyTable tr').length;
			
			if(rc>1){
				for(y=1;y<=rc-1;y++){ 
				  if(y>1){
					  salesnos = salesnos + ",";
				  }
					salesnos = salesnos + $('input[name=txtcSalesNo'+y+']').val();
				}
			}

			//ajax lagay table details sa modal body
			var x = $('#selpayment').val();
			$('#invheader').html("OR List: " + $('#selpayment').val())
			
			$.ajax({
                    url: 'th_depositlist.php',
					data: 'x='+x+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
					async: false,
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                        console.log(data);
                       $.each(data,function(index,item){
                        $("<tr>").append(
						$("<td>").html("<input type='checkbox' value='"+item.ctranno+"' name='chkSales[]'>"),
                        $("<td>").text(item.ctranno),
						$("<td>").text(item.corno),
                        $("<td>").text(item.dcutdate),
						$("<td>").text(item.namount)
                        ).appendTo("#MyORTbl tbody");

                       });
					   
					   $('#myModal').modal('show');
                    },
                    error: function (err) {
                        alert(err+"\n"+"Or No receipt to be deposited!");
                    }
                });
			
			
			
		}


}

function save(){

	var i = 0;
	var rowCount = $('#MyTable tr').length;
	var totAmt = 0;
	
  $("input[name='chkSales[]']:checked").each( function () {
	   i += 1;
      // alert( $(this).val() );
	  			
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_getordetails.php?id=" + id,
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{				
					
					   console.log(data);
                       $.each(data,function(index,item){
						   $("<tr myAttr='"+item.corno+"'>").append(
							$("<td>").html("<div class='col-xs-12'><input type='hidden' name='txtcSalesNo"+rowCount+"' id='txttranno' value='"+item.ctranno+"' />"+item.ctranno+"</div>"),
							$("<td>").text(item.corno),
							$("<td>").text(item.dcutdate),
							$("<td>").text(item.cremarks),
							$("<td>").text(item.cpaymethod),
							$("<td align='right'>").html("<div class='col-xs-12'><input type='hidden' name='txtnAmt"+rowCount+"' id='txtAmt' value='"+item.namount+"' />"+item.namount+"</div>"),
							$("<td align='center'>").html("<input class='btn btn-danger btn-xs' type='button' id='row_"+rowCount+"_delete' value='delete' onClick='deleteRow(this);' />")
						   ).appendTo("#MyTable tbody");
						   					   
						});
					rowCount = rowCount + 1;
					sortORTbl();

					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

	   
	   
	   
   });
   
   
  // alert(i + " Transactions Selected!");   
   
   
   if(i==0){
	   alert("No receipt is selected!")
   }

	   
		$('#MyTable > tbody  > tr').each(function() {
			//alert($(this).index());
			
			//alert($(this).find("input").val());
			
			var x = parseInt($(this).index()) + 1;
			//alert(x);
			$(this).find("input[id=txttranno]").attr('name', "txtcSalesNo" + x);
	
			$(this).find("input[id=txtAmt]").attr('name', "txtnAmt" + x);

		});


   $('#myModal').modal('hide');
   
   computeGross();
   
}

function sortORTbl(){
	var $table=$('#MyTable');
	
	var rows = $table.find('tbody>tr').get();
	rows.sort(function(a, b) {
	var keyA = $(a).attr('myAttr');
	var keyB = $(b).attr('myAttr');
	if (keyA < keyB) return -1;
	if (keyA > keyB) return 1;
	return 0;
	});
	$.each(rows, function(index, row) {
	$table.children('tbody').append(row);
	});
}



$('#frmOR').submit(function() {
	var subz = "YES";

  	if($('#txtnGross').val() == "" || $('#txtnGross').val() == 0){
		alert("Zero or Blank AMOUNT TO BE DEPOSITED is not allowed!");
		subz = "NO";
	}

	    			
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			
			if(lastRow==0){
				alert("Deposit Details Required!");
				subz = "NO";
			}
			else{
					
					$("#hdnrowcnt").val(lastRow);

			}

	
	if(subz=="NO"){
		return false;
	}
	else{
		
		$("#frmOR").submit();
	}

});


function disabled(){

	$("#frmOR :input").attr("disabled", true);
	
	
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
$("#frmOR :input").attr("disabled", false);

	$("#txtctranno").attr("readonly", true);
	$("#btnMain").attr("disabled", true);
	$("#btnNew").attr("disabled", true);
	$("#btnPrint").attr("disabled", true);
	$("#btnEdit").attr("disabled", true);

}

function chkSIEnter(keyCode,frm){

	if(keyCode==13){			
		document.getElementById(frm).action = "Deposit_edit.php";
		document.getElementById(frm).submit();
	}
}


</script>


</body>
</html>
