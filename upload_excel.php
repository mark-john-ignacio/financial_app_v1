    <?php
    include 'connection.php';
    if(isset($_POST["Import"])){
     
     
    		echo $filename=$_FILES["file"]["tmp_name"];
     
     
    		 if($_FILES["file"]["size"] > 0)
    		 {
     
    		  	$file = fopen($filename, "r");
    	         while (($emapData = fgetcsv($file, 10000)) !== FALSE)
    	         {
     
	 					//echo $emapData[0];
    	          //It wiil insert a row to our subject table from our csv file`
    	          // $sql = "INSERT into customers (`cempid`, `clname`, `cfname`, `cminitial`, `cacctcodegrocery`, `cscctcodecripples`, `ccustomertype`, `lstatus`) values('$emapData[0]','$emapData[2]','$emapData[3]','$emapData[4]','$emapData[5]','$emapData[6]','$emapData[7]',0)";
				   
				   $sql = "INSERT INTO `items`(`cinternalcode`, `cpartno`, `citemdesc`, `namount`, `nqty`, `ntax`, `ctaxcode`, `ltaxinc`, `ncost`, `ndiscount`, `ntype`) values('$emapData[3]','$emapData[0]','$emapData[1]','$emapData[6]','$emapData[4]','$emapData[9]','$emapData[10]','$emapData[11]','$emapData[7]','$emapData[8]','$emapData[2]')";
				   
    	         //we are using mysql_query function. it returns a resource on true else False on error
    	        // echo $sql . "\n";
				 $result = mysql_query( $sql, $conn );
				  	
				  echo mysql_errno($conn) . ": " . mysql_error($conn) . "\n";
				  
    				if(! $result )
    				{
    				echo "<script type=\"text/javascript\">
    							alert(\"Invalid File:Please Upload CSV File.\");
    							window.location = \"upload_item.php\"
    						</script>";
     
    				}
     
    	         }
    	         fclose($file);
    	         //throws a message if data successfully imported to mysql database from excel file
    	         echo "<script type=\"text/javascript\">
    						alert(\"CSV File has been successfully Imported.\");
    						window.location = \"upload_item.php\"
    					</script>";
     
     
     
    			 //close of connection
    			mysql_close($conn); 
     
     
     
    		 }
    	}	 
    ?>		