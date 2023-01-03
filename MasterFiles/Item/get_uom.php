<?php
if(!isset($_SESSION)){
	session_start();
}


require_once "../../Connection/connection_string.php";

$first=$_REQUEST["x"];

$query="select * from groupings WHERE compcode='".$_SESSION['companyid']."' and ctype='ITMUNIT' order by cdesc";
$data=mysqli_query($con,$query);

echo "<select name='selunit".$first."' id='selunit".$first."' class='form-control input-sm selectpicker'>";
while($rs = mysqli_fetch_array($data, MYSQLI_ASSOC)){
	
 if ($_REQUEST["y"]==""){
   echo "<option value=\"".$rs["ccode"]."\">".$rs["cdesc"]."</option>";
 }else{
	 if($rs["ccode"]==$_REQUEST["y"]){
		 $ststs = "selected";
	 }
	 else{
		 $ststs = "";
	 }
	 
   echo "<option value=\"".$rs["ccode"]."\" ".$ststs.">".$rs["cdesc"]."</option>";
 }
}
echo "</select>";
?>
