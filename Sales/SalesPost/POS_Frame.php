<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Checking <?php echo $_POST['csalesno'];?></title>
</head>
<frameset rows="*,*" framespacing="0" frameborder="no" border="0">
  <frame src="Top.php?csalesno=<?php echo $_POST['csalesno'];?>&dcutdate=<?php echo $_POST['dcutdate'];?>&dcnt=<?php echo $_POST['dcnt']?>" name="mainFrame" id="mainFrame" title="mainFrame">
  <frame src="MScript.php?csalesno=<?php echo $_POST['csalesno'];?>&dcutdate=<?php echo $_POST['dcutdate'];?>" name="bottomFrame" scrolling="No" noresize="noresize" id="bottomFrame" title="bottomFrame">
</frameset>
<noframes><body>
</body></noframes>
</html>
