<?php
if(!isset($_SESSION)){
session_start();
}

include('../Connection/connection_string.php');

function better_crypt($input, $rounds = 10) { 

	$crypt_options = array( 'cost' => $rounds ); 
	return password_hash($input, PASSWORD_BCRYPT, $crypt_options); 

}

if(isset($_REQUEST['btnAdd'])){

 // Original PHP code by Chirp Internet: www.chirp.com.au // Please acknowledge use of this code by including this header. 
 
 	$cUserID = $_SESSION['employeeid'];
	$OldPass = $_REQUEST['OldPass'];
	$NewPass = $_REQUEST['NewPass'];
	$cPass = $_REQUEST['passT'];
	
	$cPass_hash = better_crypt($cPass);
	
	$chkID = mysqli_query($con,"select * from users where UserID = '$cUserID'");
	while($row = mysqli_fetch_array($chkID, MYSQLI_ASSOC))
		{
			$password_hash = $row['password'];
		}
	
	
	if(password_verify($OldPass, $password_hash)) { // password is correct
		
		if($NewPass==$cPass){
			
			mysqli_query($con,"update users set password='$cPass_hash' Where Userid='$cUserID'");
			$msg='PASSWORD SUCCESSFULLY CHANGED!';
	
		}
		else{
			$msg="CONFIRM NEW PASSWORD DID NOT MATCH!";
		}
		
		
	}
	else{
		$msg = "OLD PASSWORD ERROR!";
	}
	
	
	if (mysqli_num_rows($chkID)==0) {
	mysqli_query($con,"insert into users(Userid,Fname,LName,Minit,password) 
	values('$cUserID','$cFName','$cLName','$cMI','$password_hash')");
	//echo "insert into admin(firstname,lastname,MI,cType,username,password) 
	//values('$cFName','$cLName','$cMI','$cType','$cUserID','$cPass')";
	
	}
}
else{
	$msg="";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Myx Financials</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <!-- Bootstrap theme -->
    <link href="lib/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="lib/css/theme.css" rel="stylesheet">
    
    
  </head>

  <body style="padding-top:100px">
      			<CENTER><b><?php echo $msg;?></b></CENTER>
                <br><br>
      	<form class="form-inline" role="form" method="post">
        <table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td style="padding:2px" align="center">
            <input type="password" class="form-control" placeholder="Old Password" name="OldPass" id="OldPass" required size="50" maxlength="10">
            </td>
          </tr>
          <tr>
            <td style="padding:2px" align="center">
             <input type="password" class="form-control" placeholder="New Password" name="NewPass" id="NewPass" required size="50" maxlength="10"> </td>
          </tr>

          <tr>
            <td style="padding:2px" align="center">
             <input type="password" class="form-control" placeholder="Confirm New Password" name="passT" id="passT" required maxlength="10"  size="50"></td>
          </tr>

          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="submit" class="btn btn-primary" name="btnAdd" id="btnAdd">Change Password</button><br><br><i>Maximum of 10 characters only</i> </td>
          </tr>
        </table>

        </form>


<?php
	
				mysqli_close($con);
?>


    
  </body>
</html>
