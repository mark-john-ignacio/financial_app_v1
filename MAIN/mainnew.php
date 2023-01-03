<?php
if(!isset($_SESSION)){
session_start();
}

$_SESSION['pageid'] = "main.php";

include('../include/denied.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/main.css">
    
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap.js"></script>

<script src="../include/coolclock.js" type="text/javascript"></script>
<script>
function resizeIframe() {
      var iFrameID = document.getElementById('myframe');
      if(iFrameID) {
            // here you can make the height, I delete it first, then I make it again
            iFrameID.height = "";
            iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
      }   
  }

</script>
<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon"/>

</head>


<body bgcolor="#eee"  onLoad="CoolClock.findAndCreateClocks();">
<div class="container">
    <div class="row">
        <div class="col-md-2 no-float nav">Navigation</div>
        <div class="col-md-10 no-float">Content</div>
    </div>
</div>


</body>
</html>
