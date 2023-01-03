<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Checking <?php echo $_REQUEST['citemno'];?></title>
</head>
<frameset rows="*,*" framespacing="0" frameborder="no" border="0">
  <frame src="uploadtran_top.php?ctranno=<?php echo $_REQUEST['ctranno'];?>&dcutdate=<?php echo $_REQUEST['dcutdate'];?>&cnt=<?php echo $_REQUEST['cnt'];?>" name="mainFrame" id="mainFrame" title="mainFrame"> 
  <frame src="uploadtran_script.php?ctranno=<?php echo $_REQUEST['ctranno'];?>&dcutdate=<?php echo $_REQUEST['dcutdate'];?>&type=<?php echo $_REQUEST['type'];?>" name="bottomFrame" scrolling="No" noresize="noresize" id="bottomFrame" title="bottomFrame">
</frameset>
<noframes><body>
</body></noframes>
</html>
