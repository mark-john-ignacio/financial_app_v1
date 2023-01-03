<?php
if(!isset($_SESSION)){
session_start();
}

include('../Connection/connection_string.php');

$tranno = $_REQUEST["x"];

function better_crypt($input, $rounds = 10) { 

	$crypt_options = array( 'cost' => $rounds ); 
	return password_hash($input, PASSWORD_BCRYPT, $crypt_options); 

}

if(isset($_REQUEST['btnAdd'])){

 
 	$cUserID = $_REQUEST['userid'];
	$OldPass = $_REQUEST['passw'];
	$msg= "NO MSG";
	
	$chkID = mysqli_query($con,"select * from users where UserID = '$cUserID'");
	
	if (mysqli_num_rows($chkID)!=0) {
		
		while($row = mysqli_fetch_array($chkID, MYSQLI_ASSOC))
			{
				$password_hash = $row['password'];
			}
		
		
		if(password_verify($OldPass, $password_hash)) { // password is correct
			
				
				$chkaccess = mysqli_query($con,"select * from users_access where userid = '$cUserID' and pageID='POS_cancel'");
				
				if (mysqli_num_rows($chkaccess)!=0) {
					echo "<script>location.href='trans.php?t=can2&x=".$tranno."'</script>";
				}
				else {
					$msg="YOU HAVE NO ACCESS FOR CANCELLATION!";
				}
		
			
			
		}
		else{
			$msg = "WRONG PASSWORD!";
		}
	}
	else{
		$msg = "USER ID DID NOT EXIST!";
	}
	
}
else{
	$msg="";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Coop Financials</title>

    <!-- Bootstrap core CSS -->
    <link href="lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="lib/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="lib/css/theme.css" rel="stylesheet">
    
    
  </head>

  <body style="padding-top:70px">
      			<CENTER><b><?php echo $msg;?></b><br>CANCEL TRANSACTION <b><?php echo $tranno;?><b></CENTER>
                <br><br>
      	<form class="form-inline" role="form" method="post" autocomplete="off">
        <table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td style="padding:2px" align="center">
            <input type="text" class="form-control" placeholder="Input Authorized ID..." name="userid" id="userid" required size="50" maxlength="15" autocomplete="off">
            </td>
          </tr>
          <tr>
            <td style="padding:2px" align="center">
             <input type="password" class="form-control" placeholder="Input Password..." name="passw" id="passw" required size="50" maxlength="10" autocomplete="off"> </td>
          </tr>

          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="submit" class="btn btn-primary" name="btnAdd" id="btnAdd">PROCEED</button></td>
          </tr>
        </table>

        </form>


<?php
	
				mysqli_close($con);
?>


    
  </body>
</html>
