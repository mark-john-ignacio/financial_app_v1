<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>

              	<?php
				$company = $_SESSION['companyid'];
				$citemno = $_REQUEST['txtcitemno'];
				//echo $citemno;
				if($citemno <> ""){
					$sql = "Select * From items where compcode='$company' and cpartno='$citemno'";
				}else{
					
					header('Items.php');
					die();
				}
				
				$sqlhead=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				if (mysqli_num_rows($sqlhead)!=0) {
					while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
						$cItemNo = $row['cpartno'];
						$cItemDesc = $row['citemdesc'];

					}
				}


				?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>  
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css"> 
    
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/jquery.numeric.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; height:700px">
<form name="frmITEM" id="frmITEM" method="post" enctype="multipart/form-data">
	<fieldset>
    	<legend>Item Purchase Cost</legend>
       
       			<input type="hidden" id="txtcpartno" name="txtcpartno" value="<?php echo $cItemNo;?>" />

       
        <div class="col-xs-12 nopadding">
        
            <div class="col-xs-3 nopadding">
            	<h4><b>Item Code: </b> <?php echo $cItemNo; ?></h4>
            </div>
            
            <div class="col-xs-9 nopadding">
            	<h4><b>Item Description: </b> <?php echo $cItemDesc; ?></h4>
            </div>

        </div>
        
        <input type="button" value="Add Cost" name="btnaddcost" id="btnaddcost" class="btn btn-primary btn-xs" onClick="addpurchcost();">
         	
         	<table width="95%" border="0" id="myPurchTable" cellpadding="2">
             <thead>
              <tr>
                <!--<th scope="col" width="130">Supplier Code</th>-->
                <th scope="col">Supplier Name</th>
                <th scope="col" width="100">UOM</th>
                <th scope="col" width="100">Price</th>
                <th scope="col" width="100">Effect Date</th>
                <th scope="col" width="220">Remarks</th>
                <th scope="col" width="100">&nbsp;</th>
              </tr>
             </thead>
             <tbody>
             
             </tbody>
            </table>
        
    </fieldset>
</form>
</body>
</html>

<script>
$(function() {

});

function addpurchcost(){

	var tbl = document.getElementById('myPurchTable').getElementsByTagName('tr');
	var count = tbl.length;

	var returnData = "seluom"+count;
		
	//var suppcode = "<td><input type=\"text\" class=\"form-control input-xs\" id=\"txtsuppcode\" name=\"txtsuppcode\" placeholder=\"Enter Code..\" autocomplete=\"off\" /></td>";
	var suppname = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadding\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtsuppname"+count+"\" name=\"txtsuppname"+count+"\" placeholder=\"Enter Name..\" autocomplete=\"off\" /></div><input type=\"hidden\" id=\"txtsuppid"+count+"\" name=\"txtsuppid"+count+"\" /></td>";
	
	var cunit = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><select class=\"form-control input-xs\" id=\""+returnData+"\" name=\""+returnData+"\" /></select></div></td>";
	
	var nprice = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"numeric form-control input-xs\" id=\"txtprice"+count+"\" name=\"txtprice"+count+"\" placeholder=\"0.0000\" autocomplete=\"off\" /></td>";
	
	var deffct = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"datepick form-control input-xs\" id=\"txtdeffect\" name=\"txtdeffect\" placeholder=\"Pick Date...\" autocomplete=\"off\" /></td>";
	
	var crem = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtcremarks\" name=\"txtcremarks\" placeholder=\"Enter Remarks...\" autocomplete=\"off\" /></td>";
	
	var cstat = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><button class=\"form-input btn btn-xs btn-danger\">Remove</button></div></td>";
	
	$('#myPurchTable > tbody:last-child').append('<tr>' + suppname + cunit + nprice + deffct + crem + cstat + '</tr>');
	

				  	$('#txtsuppname'+count).typeahead({						 
						autoSelect: true,
						source: function(request, response) {							
							$.ajax({
								url: "th_supplier.php",
								dataType: "json",
								data: { query: request },
								success: function (data) {
									response(data);
								}
							});
						},
						displayText: function (item) {
							return item.id + " : " + item.value;
						},
						highlighter: Object,
						afterSelect: function(item) { 					
									
							var id = $(document.activeElement).attr('id');
							//alert(id);	
							
							$('#txtsuppname'+count).val(item.value).change(); 
							$('#txtsuppid'+count).val(item.id); 
							
							$('#txtprice'+count).focus();
							
						}
					});



		    $('input.datepick').datetimepicker({
                 format: 'MM/DD/YYYY',
				 minDate: new Date(),

           	});
			
			$("input.numeric").numeric({decimalPlaces: 4, negative: false});
			$("input.numeric").on("click", function () {
				$(this).select();
			});
			
			loaduomlist(returnData);
}

function loaduomlist(selid){

			var idz = $("#txtcpartno").val();
		
			$.ajax ({
            url: "th_loaduomperitm.php",
			data: { id: idz },
			dataType: 'json',
            success: function(data) {
				console.log(data);
				$.each(data,function(index,item){
					
				  if(item.id!=""){				  
					
						$("#"+selid).append("<option value=\""+item.id+"\">"+item.name+"</option>");
					
				  }
									  
							
				});

            }
    		});

}
</script>


