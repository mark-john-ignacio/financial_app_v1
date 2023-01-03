<?php
include('Connection/connection_string.php');
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="include/jquery.js"></script>
<script type="text/javascript">

$(document).ready(function(){
    
	$("#add_err").css('display', 'none', 'important'); 
    $("#btnLogin").click(function(){   
		if(document.getElementById("employeeid").value == "" || document.getElementById("inputPassword").value == ""){
			$("#add_err").css('display', 'inline', 'important');
            $("#add_err").html("<div class='alert alert-warning' role='alert'><strong>ERROR!</strong> Complete the form</div>");
		}else{


			  employeeid=$("#employeeid").val();
			  password=$("#inputPassword").val();
			  selcomp=$("#selcomp").val();


				selcat=$("#selcat").val();
				
			  $.ajax({
			   type: "POST",
			   url: "include/employeelogin.php?",
			   data: "employeeid="+employeeid+"&password="+password+"&selcomp="+selcomp,
			   success: function(html){   
			   //alert(html); 
				if(html=='true')    {
				 //$("#add_err").html("right username or password");
				 if(selcat=='user'){
					 window.location="SYS/";
				 }
				 else if(selcat=='display'){
					 window.location="DIS/";
				 }
				 else if(selcat=='display2'){
					 window.location="DisApp/";
				 }
				 else if(selcat=='pos'){
					 window.location="SysFull/";
				 }
				 else{
					 window.location="main.php";
				 }

				}
				else    {
				 $("#add_err").css('display', 'inline', 'important');
				 $("#add_err").html("<div class='alert alert-warning' role='alert'>"+html+"</div>");
				}
			   },
			   beforeSend:function()
			   {
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<center><img src='images/loader.gif' widt='50' height='50' /></center>")
			   }
			  });
			return false;
		
		}
    });
		
});

</script>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Coop Financials</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-theme.min.css" rel="stylesheet">
<link href="css/theme.css" rel="stylesheet">
<link href="css/signin.css" rel="stylesheet">
<body oncontextmenu="return false">
 <div class="container">

      <form class="form-signin">
        <h2 class="form-signin-heading"><center><img src="images/COMPLOGO.png" height="90" width="90"/>&nbsp;<img src="images/Logo.png" height="86" width="190"/></center></h2>


        <select id="selcomp" name="selcomp" class="form-control input-sm selectpicker">
        <?php 
		$chkcomp = mysqli_query($con,"select * from company");

		if (mysqli_num_rows($chkcomp)!=0) {
			while($row = mysqli_fetch_array($chkcomp, MYSQLI_ASSOC)){
		?>
          <option value="<?php echo $row['compcode'];?>"><?php echo $row['compname']; ?></option>
         <?php
			}
		}
		 ?>
        </select>

   
   <br><br>
        <input type="text" id="employeeid" name="employeeid" class="form-control" placeholder="User ID" required autofocus>
			<br>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
        <br>
        <select id="selcat" name="selcat" class="form-control input-sm selectpicker">
        	  <option value="display2">Display w/ Approval</option>
        	  <option value="pos">POS Full</option>
              <option value="display">Display</option>
              <option value="user">POS User</option>
              <option value="admin">Admin</option>
        </select>
         <br>
       <div class="err" id="add_err"></div>
       
        <button class="btn btn-lg btn-primary btn-block" type="submit" id="btnLogin" name="btnLogin">Sign in</button>
      </form>

    </div></div>
</body>
</html>
