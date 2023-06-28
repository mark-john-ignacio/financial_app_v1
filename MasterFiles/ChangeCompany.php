<?php

  if(!isset($_SESSION)){
    session_start();
  }

  include('../Connection/connection_string.php');

  $result=mysqli_query($con,"select * From company where compcode='".$_REQUEST['x']."'");	
  $rowcount=mysqli_num_rows($result);

  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    $_SESSION['companyid'] = $row['compcode'];
  }

 // echo $_SERVER['HTTP_REFERER'];
  //header('Location: '.']);

?>
<script>
  top.window.location='<?=$_SERVER['HTTP_REFERER']?>';
</script>
