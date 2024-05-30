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

<table width="100%" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="top" style="padding:5px;" bgcolor="#336b9e" valign="middle">
    <?php
    	include('main3.php')
	?>
      
	</td>
  </tr>
</table>

<table width="100%" border="0" align="center" bgcolor="#FFFFFF">
  <tr>
  	<td style="padding-top:5px; padding-bottom:5px; border-right:1px solid #CCC;" align="center" valign="top" height="50px">
        <center>	
            <canvas style="width: 150px; height: 150px;" height="150" width="150" id="clockid" class="CoolClock:Sand"></canvas>
        </center>
		<b>
		<?php
        		echo date('l jS \of F Y');

		?>
        </b>
	</td>

    <td valign="top" rowspan="2">
    <iframe id="myframe" name="myframe" scrolling="no" style="width:100%; display:block; margin:0px; padding:0px; border:0px" src="index.html" onLoad="resizeIframe();"></iframe>
    </td>

  </tr>
  <tr>
    <td width="200" valign="top" id="tdB">
		<?php
    	include('main2.php')
		?>

    </td>
  </tr>
</table>



</body>
</html>
