<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$id = $_REQUEST['id'];
	
if($id=="item"){
	
	$result = mysqli_query ($con, "SELECT citemno FROM quote_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM so_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM dr_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM sales_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM purchase_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM receive_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM adjustments_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM purchreturn_t WHERE compcode='$company' and citemno='$code' UNION ALL SELECT citemno FROM salesreturn_t WHERE compcode='$company' and citemno='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `items` where `compcode` = '$company' and `cpartno` = '$code'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete item with history!";
	}
}
else if($id=="itemUOM"){
	
	$result = mysqli_query ($con, "SELECT cunit FROM quote_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM so_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM dr_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM sales_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM purchase_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM receive_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM adjustments_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM purchreturn_t WHERE compcode='$company' and cunit='$code' UNION ALL SELECT cunit FROM salesreturn_t WHERE compcode='$company' and cunit='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='ITMUNIT'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete UOM with history!";
	}
}
else if($id=="itemCLS"){
	
	$result = mysqli_query ($con, "SELECT cclass FROM items WHERE compcode='$company' and cclass='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='ITEMCLS'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete ITEM CLASSIFICATION with reference item!";
	}
}
else if($id=="itemGRP"){
	$grp = $_REQUEST['grp'];
	
	$result = mysqli_query ($con, "SELECT * FROM items WHERE compcode='$company' and cGroup1='$code' or cGroup2='$code' or cGroup3='$code' or cGroup4='$code' or cGroup5='$code' or cGroup6='$code' or cGroup7='$code' or cGroup8='$code' or cGroup9='$code' or cGroup10='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `items_groups` where `compcode` = '$company' and `ccode` = '$code' and cgroupno='$grp'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete ITEM GROUP with reference item!";
	}
}
else if($id=="itemTYP"){
	
	$result = mysqli_query ($con, "SELECT ctype FROM items WHERE compcode='$company' and ctype='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='ITEMTYP'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete ITEM TYPE with reference item!";
	}
}
else if($id=="customer" || $id=="supplier"){
	$result = mysqli_query ($con, "SELECT ccode FROM quote WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM so WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM dr WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM sales WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM purchase WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM receive WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM purchreturn WHERE compcode='$company' and ccode='$code' UNION ALL SELECT ccode FROM salesreturn WHERE compcode='$company' and ccode='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		if($id=="customer"){
			$vid = "cempid";
		}else{
			$vid = "ccode";
			
		}
		
			if (!mysqli_query($con, "DELETE from `".$id."s` where `compcode` = '$company' and `".$vid."` = '$code'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete ".$id." with history!";
	}
}

else if($id=="custTYP"){
	
	$result = mysqli_query ($con, "SELECT * FROM customers WHERE compcode='$company' and ccustomertype='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='CUSTYP'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete CUSTOMER TYPE with reference customer!";
	}
}

else if($id=="custCLS"){
	
	$result = mysqli_query ($con, "SELECT * FROM customers WHERE compcode='$company' and ccustomerclass='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='CUSTCLS'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete CUSTOMER CLASSIFICATION with reference customer!";
	}
}

else if($id=="suppTYP"){
	
	$result = mysqli_query ($con, "SELECT * FROM suppliers WHERE compcode='$company' and csuppliertype='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='SUPTYP'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete SUPPLIER TYPE with reference customer!";
	}
}
else if($id=="suppCLS"){
	
	$result = mysqli_query ($con, "SELECT * FROM suppliers WHERE compcode='$company' and csupplierclass='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='SUPCLS'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete SUPPLIER CLASSIFICATION with reference customer!";
	}
}	
else if($id=="bank"){
	
	$result = mysqli_query ($con, "SELECT cbank FROM paybill_check_t WHERE compcode='$company' and cbank='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from bank where `compcode` = '$company' and `ccode` = '$code'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete BANK with reference Bills Payment!";
	}
}
else if($id=="PMVer"){
	
	$result = mysqli_query ($con, "SELECT * FROM items_pm WHERE compcode='$company' and cversion='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `groupings` where `compcode` = '$company' and `ccode` = '$code' and ctype='ITMPMVER'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete PRICE MATRIX VERSION with reference price list!";
	}
}
else if($id=="CustGRP"){
	$grp = $_REQUEST['grp'];
	
	$result = mysqli_query ($con, "SELECT * FROM Customers WHERE compcode='$company' and cGroup1='$code' or cGroup2='$code' or cGroup3='$code' or cGroup4='$code' or cGroup5='$code' or cGroup6='$code' or cGroup7='$code' or cGroup8='$code' or cGroup9='$code' or cGroup10='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `customers_groups` where `compcode` = '$company' and `ccode` = '$code' and cgroupno='$grp'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete CUSTOMER GROUP with reference customer!";
	}
}
else if($id=="SuppGRP"){
	$grp = $_REQUEST['grp'];
	
	$result = mysqli_query ($con, "SELECT * FROM Suppliers WHERE compcode='$company' and cGroup1='$code' or cGroup2='$code' or cGroup3='$code' or cGroup4='$code' or cGroup5='$code' or cGroup6='$code' or cGroup7='$code' or cGroup8='$code' or cGroup9='$code' or cGroup10='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
		
			if (!mysqli_query($con, "DELETE from `suppliers_groups` where `compcode` = '$company' and `ccode` = '$code' and cgroupno='$grp'")) {
						echo mysqli_error($con);
			} else{
				
						echo "True";
			}
		
	}else{
		echo "Cannot delete SUPPLIER GROUP with reference supplier!";
	}
}			

?>
