<?php
include('../Connection/connection_string.php');

if($_REQUEST["Id"]!=""){
	$qry = "and cacctno <> '".$_REQUEST["Id"]."'";
}else{
	$qry = "";
}

$result = mysqli_query ($con, "select cacctno as id, cacctdesc as title, IFNULL(mainacct,0) as parent_id from accounts WHERE ccategory = '".$_REQUEST["Cat"]."' and mainacct='".$_REQUEST["Main"]."' and ctype='Details' ".$qry." and lcontra=0 order by cacctno"); 
	
	//echo("select cacctno as id, cacctdesc as title, IFNULL(mainacct,0) as parent_id from accounts WHERE ccategory = '".$_POST["Id"]."' order by cacctno <br>");
	
	if(mysqli_num_rows($result)!=0){
	
	$render = '<ul class="dropdown-menu">';
	
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			//$myarr = array("id" => $row["id"], "title" => $row["title"], "parent_id" => $row["parent_id"]);
			//$items[] = $row;
			
			if($_REQUEST["sel"]==$row["id"]){
				$varead = "checked";
			}
			else{
				$varead = "";
			}
			
			$render .= "<li><label><input name='selcontra' id='selcontra' value='".$row["id"]. ": " . $row["title"] . "' type='radio' class='btncontra2' ".$varead.">" . $row["title"] . "</label></li>";
		}
		
	$render .= '</ul>';
		//$items = array($myarr);
	}
	
if($_REQUEST["sel"]!=""){
	
	$result = mysqli_query ($con, "select cacctno, cacctdesc from accounts WHERE cacctno='".$_REQUEST["sel"]."' order by cacctno");
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$varInner = $row["cacctno"].": ".$row["cacctdesc"];
			
		}
	}

}
else{
	$varInner = "Select Reference Account";
}

?>


 <div class="dropdown bts_dropdown">
   <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
   	<span id='btnClogo'><?php echo $varInner;?></span> <i class="caret"></i>
   </button>

	<?php echo $render;?>

</div>

