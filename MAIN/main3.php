
      <?php
	 // if(!isset($_SESSION)){
session_start();
}
	  
	  require_once "../Connection/connection_string.php";
	  
	 $ccomp = $_SESSION['companyid'];
	
     $result = mysqli_query($con,"SELECT * FROM `company` WHERE compcode='$ccomp'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $cnme = $all_course_data['compname']; 
		
	 }
	 else{
		 
		 $cnme = ""; 
		 
	 }

		?>

<table width="100%" border="0" cellpadding="0">
  <tr>
    <td>
                <img src="../images/COMPLOGO.png" height="50" width="50"/>
               
               <font size="+2" color="#FFFFFF"> <?php echo $cnme;?> POS and ACCOUNTING SYSTEM </font>
    
    </td>
    
    <td align="right">

	<div class="collapse navbar-collapse bs-example-js-navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li id="fat-menu" class="dropdown">
              <a id="drop3" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                Welcome <?php echo $_SESSION['employeename']; ?>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
                <li role="presentation"><a role="menuitem" tabindex="-1" href="../Maintenance/ChangePass.php" target="myframe"><span class="glyphicon glyphicon-user"></span>Change Password</a></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="../logout.php"><span class="glyphicon glyphicon-log-out"></span>Logout</a></li>
              </ul>
            </li>
          </ul>
    </div><!-- /.nav--->
        
    </td>
  </tr>
</table>
        
