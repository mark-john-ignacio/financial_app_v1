<?php
if(!isset($_SESSION)){
  session_start();
}
include('../../Connection/connection_string.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<SCRIPT LANGUAGE="Javascript">
<!--
if (top.location != self.location) {
top.location = self.location;
}
//-->
</SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Recompute</title>
</head>

<frameset rows="*,*"" frameborder="NO" border="0" framespacing="0">
  <frame src="Recom_top.php?id=<?php echo $_REQUEST["id"]; ?>&dte=<?php echo $_REQUEST["dte"]; ?>&typ=<?php echo $_REQUEST["typ"]; ?>&cnthdr=<?=$_REQUEST["cnthdr"];?>" name="topFrame" id="topFrame">
  <frame src="Recom_script.php?id=<?php echo $_REQUEST["id"]; ?>&dte=<?php echo $_REQUEST["dte"]; ?>&typ=<?php echo $_REQUEST["typ"]; ?>" name="mainFrame" id="mainFrame">
</frameset>
<noframes><body>
</body>
</noframes></html>

