<?php
if(!isset($_SESSION)){
session_start();
}

// include('../Connection/connection_string.php');

// function better_crypt($input, $rounds = 10) { 

// 	$crypt_options = array( 'cost' => $rounds ); 
// 	return password_hash($input, PASSWORD_BCRYPT, $crypt_options); 

// }

// if(isset($_REQUEST['btnAdd'])){

//  // Original PHP code by Chirp Internet: www.chirp.com.au // Please acknowledge use of this code by including this header. 
 
//  	$cUserID = $_SESSION['employeeid'];
// 	$OldPass = $_REQUEST['OldPass'];
// 	$NewPass = $_REQUEST['NewPass'];
// 	$cPass = $_REQUEST['passT'];
	
// 	$cPass_hash = better_crypt($cPass);
	
// 	$chkID = mysqli_query($con,"select * from users where UserID = '$cUserID'");
// 	while($row = mysqli_fetch_array($chkID, MYSQLI_ASSOC))
// 		{
// 			$password_hash = $row['password'];
// 		}
	
	
// 	if(password_verify($OldPass, $password_hash)) { // password is correct
		
// 		if($NewPass==$cPass){
			
// 			mysqli_query($con,"update users set password='$cPass_hash' Where Userid='$cUserID'");
// 			$msg='PASSWORD SUCCESSFULLY CHANGED!';
	
// 		}
// 		else{
// 			$msg="CONFIRM NEW PASSWORD DID NOT MATCH!";
// 		}
		
		
// 	}
// 	else{
// 		$msg = "OLD PASSWORD ERROR!";
// 	}
	
	
// 	if (mysqli_num_rows($chkID)==0) {
// 	mysqli_query($con,"insert into users(Userid,Fname,LName,Minit,password) 
// 	values('$cUserID','$cFName','$cLName','$cMI','$password_hash')");
// 	//echo "insert into admin(firstname,lastname,MI,cType,username,password) 
// 	//values('$cFName','$cLName','$cMI','$cType','$cUserID','$cPass')";
	
// 	}
// }
// else{
// 	$msg="";
// }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Myx Financials</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <!-- Bootstrap theme -->
    <!-- <link href="lib/css/bootstrap-theme.min.css" rel="stylesheet"> -->

    <!-- Custom styles for this template -->
    <!-- <link href="lib/css/theme.css" rel="stylesheet"> -->

    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
    
    
  </head>

  <body style="padding-top:100px">
      			<!-- <CENTER><b>< ?php echo $msg;?></b></CENTER> -->
                <br><br>
      	<form class="form-inline" role="form" method="post" onSubmit="return false;">
        <table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td style="padding:2px" align="center">
              <input type="password" class="form-control" placeholder="Old Password" name="OldPass" id="OldPass" size="50" required maxlength="15">
              <span class="text-danger" id="oldpassWarn"></span>
            </td>
          </tr>
          <tr>
            <td style="padding:2px" align="center">
             <input type="password" class="form-control" placeholder="New Password" name="NewPass" id="NewPass" size="50" required maxlength="15"> 
             <span class="text-danger" id="newpassWarn"></span>
            </td>
          </tr>

          <tr>
            <td style="padding:2px" align="center">
              <input type="password" class="form-control" placeholder="Confirm New Password" name="passT" id="passT" size="50" required maxlength="15">
              
            </td>
          </tr>
          <tr>
            <td style="padding:2px" align="center">
              <div class="col-xs-12 " id="warning" style="display: none">
                <div id="alphabettxt"><span id="alphabet"></span> Must have a Alphabetical characters! </div>
                <div id="numerictxt"><span id="numeric"></span> Must have a Numberic characters!</div>
                <div id="stringlentxt"><span id="stringlen"></span> Minimum of 8 characters! </div>
                  
              </div>
            </td>
          </tr>

          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="submit" class="btn btn-primary" name="btnAdd" id="btnAdd" >Change Password</button><br><br><i>Maximum of 15 characters only</i> </td>
          </tr>
        </table>

        </form>


<?php
	
				//mysqli_close($con);
?>


        <div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
					<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-top">
							<div class="modal-content">
								<div class="alert-modal-danger">
									<p id="AlertMsg"></p>
									<p><center>
										<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
									</center></p>
								</div>
							</div>
						</div>
					</div>
				</div>
  </body>
</html>

<script type="text/javascript">
  var warnings = { alpha: false, numeric: false, stringlen: false };

      $(document).ready(function(){
        $('#btnAdd').on('click', function(){
          const newpassword = $('#NewPass').val()
          const confirmpassword = $('#passT').val()
          
          const confirmNewPassword = PasswordValidation( newpassword )
          const confirmPassword = PasswordValidation( confirmpassword )
          
            if( confirmNewPassword && confirmPassword){
                $.ajax({
                  url: 'user_change_pass.php',
                  type: 'post',
                  method: 'post',
                  dataType: 'json',
                  data: {
                    id: '<?= $_SESSION['employeeid'] ?>',
                    password: $('#OldPass').val(),
                    newpassword: newpassword,
                    confirmPassword: confirmpassword
                  },
                  async: false,

                  success: function(data){
                    if(data.valid){
                      $("#AlertMsg").html("<b>"+data.msg+"</b>");
                      $("#alertbtnOK").show();
                      $("#AlertModal").modal('show');
                      setTimeout(function() {
                       // location.replace("../main.php");
                        top.location.href = "../main.php";
                      },  3000)

                    } else {
                      $("#AlertMsg").html("<b>"+data.errCode+": </b>"+data.errMsg);
                      $("#alertbtnOK").show();
                      $("#AlertModal").modal('show');
                    }
                    
                  },
                  error: function(data){
                    alert(data);
                  }
                })

            } else {
                $('#warning').css('display', 'block')
                $('#alphabet').html("<i " + (!warnings.alpha ?  "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i> ");
                $('#alphabettxt').css('color', ( !warnings.alpha ? '#FF0000' : '#000000' ))

                $('#numeric').html("<i " + ( !warnings.numeric ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i> ");
                $('#numerictxt').css('color', ( !warnings.numeric ? '#FF0000' : '#000000' ))

                $('#stringlen').html("<i " + ( !warnings.stringlen ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i>");
                $('#stringlentxt').css('color', ( !warnings.stringlen ?  '#FF0000' : '#000000' ))
              }
          })

          /**
           * Update users password
           */

          function AlphabetFilter(password){
            var filter = /^(?=.*[a-zA-Z])/;
            return filter.test(password)
          }
          function NumericFilter(password){
            var filter = /(?=.*[0-9])/;
            return filter.test(password);
          }

          function PasswordLimit(inputs){
            return inputs.length >= 8;
          }

          function PasswordValidation(inputs){
            warnings['alpha'] = AlphabetFilter(inputs)
            warnings['numeric'] = NumericFilter(inputs)
            warnings['stringlen'] = PasswordLimit(inputs)

            return warnings['alpha'] && warnings['numeric'] && warnings['stringlen'];

            // if( inputs.length < 8 ){
            //   warning['stringlen'] = false;
            //   console.log("Characters must 8 - 15" + arr)
            // }
          }	

      })
</script>
