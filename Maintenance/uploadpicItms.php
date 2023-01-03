<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">

</head>

<body>
<br>
<center>
<form action="uploadItms.php?" method="post" enctype="multipart/form-data">
    <b>Select image to upload:<br><i>(file size limit 500kb)</i></b>
    <br><br>
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
</center>
<?php

//echo $_REQUEST['id'];
if(!isset($_SESSION)){
session_start();
}

$_SESSION['picidItms'] = $_REQUEST['id'];

//echo $_SESSION['picidItms'];
?>
</body>
</html>