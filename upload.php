<?php
if(!isset($_SESSION)){
session_start();
}
include('Connection/connection_string.php');
include('include/denied.php');
			
			$sql = "select cempid from customers where ccustomertype='TYP001'";
            $result=mysqli_query($con,$sql);
			              
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
				$cCustCode = $row['cempid'];
					
				mysqli_query($con,"INSERT INTO customers_accts(`compcode`, `ccode`, `citemtype`, `cacctno`) 
				values('001', '$cCustCode','CRIPPLES','11703a')");

				mysqli_query($con,"INSERT INTO customers_accts(`compcode`, `ccode`, `citemtype`, `cacctno`) 
				values('001', '$cCustCode','GROCERY','11701a')");

				mysqli_query($con,"INSERT INTO customers_accts(`compcode`, `ccode`, `citemtype`, `cacctno`) 
				values('001', '$cCustCode','SPEC','11701a')");

					
			}
							

?>
