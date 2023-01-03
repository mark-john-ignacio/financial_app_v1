
<style>
.fixedColumnValue
{    
    width:120px;
    min-height:35px;
    margin:0 0 0 0px;
    padding:0 0 0 2px; 
    font:normal 10pt Arial;
    display: inline-block;
    border:0px solid;
    word-wrap: break-word;
	white-space: pre-wrap;      /* CSS3 */ 
	white-space: -moz-pre-wrap; /* Firefox */
}
</style>
<?php
if(!isset($_SESSION)){
session_start();
}
$company = $_SESSION['companyid'];

require_once "../Connection/connection_string.php";
$q = $_POST["id"];
if (!$q) return;


$sql = "Select * from items where compcode='$company' and cclass='$q' order by citemdesc";

$rsd = mysqli_query($con,$sql);

if (mysqli_num_rows($rsd)!=0) {
	
$cntr = 0;
while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
$cntr = $cntr + 1;

?>
    <?php 
	if(!file_exists("../imgitm/".$rs["cpartno"].".jpg")){
		$imgsrc = "../images/blueX.png";
	}
	else{
		$imgsrc = "../imgitm/".$rs["cpartno"].".jpg";
	}
	?>
    
   <!--<button id="Itm<?php //echo $rs["cpartno"];?>" style="background-color:#FFF; border:1px solid" onclick="inputdis('<?php //echo $rs["cpartno"];?>')">-->
   <a href="javascript:;" onclick="inputdis('<?php echo $rs["cpartno"];?>');">
   <div style="padding:2px;">
   <div style="border:1px solid #333;">
	<table align="center" border="0px" cellpadding="0px" style="table-layout: fixed; width: 100%; height:85px">
		<tr>
			<td width="80" style="padding-left:2px"><img src="../imgitm/<?php echo $imgsrc;?>" width="80" height="75" align="absmiddle"></td>
			<!--<td>&nbsp;<font style="font-size:12px"><?php echo $rs["citemdesc"];?><br>&nbsp;<?php echo $rs["cpartno"];?></font></td>
			<td width="50"><font style="font-size:42px"><b></b></font></td>-->
			<td>
				<div class="col-xs-12">	<font style="font-size:12px"><?php echo $rs["citemdesc"];?> </font></div>
				<div class="col-xs-12">	<font style="font-size:12px"><?php echo $rs["cpartno"];?> </font></div>
			<td>
		</tr>
	</table>
  </div>
  </div>
  
  </a>
  <!-- </button>-->

   
<?php
}
}
else{
	echo "";
}
?>

<script>

function inputdis(xyval){

			$.ajax({
			type:'post',
			url:'get_productid.php',
			data: 'c_id='+xyval,                 
			success: function(value){
				var data = value.split(",");
				$('#txtprodid').val(data[0]);
				$('#txtprodnme').val(data[1]);
				$('#hdnprice').val(data[2]);
				$('#hdnunit').val(data[3]);
				$("#hdndiscount").val(data[4]);
			
	
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
			$("#hdndiscount").val("");
	 
			//closing for success: function(value){
			}
			}); 
	

}

</script>

