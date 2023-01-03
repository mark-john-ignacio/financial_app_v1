<!DOCTYPE html>
    <?php 
	
    	include 'connection.php';
     
    ?>	
    <html lang="en">
    	<head>
    		<meta charset="utf-8">
    		<title>Import Excel To Mysql Database Using PHP </title>
    		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    		<meta name="description" content="Import Excel File To MySql Database Using php">
     
    		<link rel="stylesheet" href="css/bootstrap.min.css">
    		<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
    		<link rel="stylesheet" href="css/bootstrap-custom.css">
     
     
    	</head>
    	<body>    
     
    	<!-- Navbar
        ================================================== -->
          
    	<div id="wrap">
    	<div class="container">
    		<div class="row">
    			<div class="span3 hidden-phone"></div>
    			<div class="span6" id="form-login">
    				<form class="form-horizontal well" action="upload_excel.php" method="post" name="upload_excel" enctype="multipart/form-data">
    					<fieldset>
    						<legend>Import CSV/Excel file</legend>
    						<div class="control-group">
    							<div class="control-label">
    								<label>CSV/Excel File:</label>
    							</div>
    							<div class="controls">
    								<input type="file" name="file" id="file" class="input-large">
    							</div>
    						</div>
     
    						<div class="control-group">
    							<div class="controls">
    							<button type="submit" id="submit" name="Import" class="btn btn-primary button-loading" data-loading-text="Loading...">Upload</button>
    							</div>
    						</div>
    					</fieldset>
    				</form>
    			</div>
    			<div class="span3 hidden-phone"></div>
    		</div>
     
    		<table class="table table-bordered">
    			<thead>
    				  	<tr>
    				  		<th>Item Code</th>
    				  		<th>Part No</th>
    				  		<th>Item Desc</th>
    				  		<th>Qty</th>
    				  		<th>Amt</th>
     						<th>Tax</th>
                            <th>Tax Code</th>
                            <th>Tax Inc</th>
                            <th>Cost</th>
                            <th>Discount</th>
                            <th>Type</th>
     
    				  	</tr>	
    				  </thead>
    			<?php
    				$SQLSELECT = "SELECT * FROM items";
    				$result_set =  mysql_query($SQLSELECT, $conn);
    				while($row = mysql_fetch_array($result_set))
    				{
    				?>
     
    					<tr>
    						<td><?php echo $row['cinternalcode']; ?></td>
    						<td><?php echo $row['cpartno']; ?></td>
    						<td><?php echo $row['citemdesc']; ?></td>
    						<td><?php echo $row['nqty']; ?></td>
    						<td><?php echo $row['namount']; ?></td>
     						<td><?php echo $row['ntax']; ?></td>
                            <td><?php echo $row['ctaxcode']; ?></td>
                            <td><?php echo $row['ltaxinc']; ?></td>
                            <td><?php echo $row['ncost']; ?></td>
                            <td><?php echo $row['ndiscount']; ?></td>
                            <td><?php echo $row['ntype']; ?></td>
    					</tr>
    				<?php
    				}
    			?>
    		</table>
    	</div>
     
    	</div>
     
    	</body>
    </html>