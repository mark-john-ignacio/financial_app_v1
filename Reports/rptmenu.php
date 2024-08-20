<?php 
	if(!isset($_SESSION)){
		session_start();
	}
	include('../Connection/connection_string.php');
	include('../include/denied.php');

	//get the value of the employee id
	$employeeid = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : '';
	$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : '';
	$company = isset($_SESSION['companyid']) ? $_SESSION['companyid'] : ''; // Retrieve companyid from session

	//get list of reports that a user can access
	$pages = array();
	$sql = "SELECT pageid, main_id, menu_id FROM users_access WHERE userid = '$employeeid' and main_id in (65,75,83,94,101)";
	$query = mysqli_query($con, $sql);
	while($list = $query -> fetch_assoc()) {
		array_push($pages, $list["menu_id"]);
	}	

	$navmenu = array();

	$sql = "SELECT * FROM nav_menu WHERE cstatus ='ACTIVE' and id in (".implode(",",$pages).") Order by menu_order";
	$query = mysqli_query($con, $sql);
	while($list = $query -> fetch_assoc()) {	
		$navmenu[] = $list;
	}
		
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
  <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
  <link href="../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../js/bootstrap3-typeahead.min.js"></script>

	<script src="../Bootstrap/js/bootstrap.js"></script>
	<script src="../Bootstrap/js/moment.js"></script>
	<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script type="text/javascript">//<![CDATA[

		$(document).ready(function() {
			$(".divhid").hide();
			
			$("#<?php echo $_GET["id"];?>").show();
			
			if($("#hdntyp").val()=="sales"){
				$("#divname").html("<font size=\"+2\"><u>Sales Reports</u></font>");
			}
			else if($("#hdntyp").val()=="purch"){
				$("#divname").html("<font size=\"+2\"><u>Purchase Reports</u></font>");
			}
			else if($("#hdntyp").val()=="acc"){
				$("#divname").html("<font size=\"+2\"><u>Books of Account</u></font>");
			}
			else if($("#hdntyp").val()=="bir"){
				$("#divname").html("<font size=\"+2\"><u>BIR Reports</u></font>");
			}
			else if($("#hdntyp").val()=="inv"){
				$("#divname").html("<font size=\"+2\"><u>Inventory Reports</u></font>");
			}

		});

		function setI(typ,x){
			if(typ=='A'){
				document.getElementById("myreport").src = x;
			}
			else if(typ=='B'){
				document.getElementById("myreport").src = "";
				document.getElementById("transnew").action = x;
				document.getElementById("transnew").submit();
			}
		}

		function resizeIframe(obj) {
			// here you can make the height, I delete it first, then I make it again
			var x = obj.contentWindow.document.body.scrollHeight + 30;
			obj.style.height = x + 'px';      
		}   
	</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>
</head>

<body style="padding-left:10px; height:450px">
<form id="transnew" name="transnew" target="_blank" method="post">
</form>

<input type="hidden" name="hdntyp" id="hdntyp" value="<?php echo $_GET["id"];?>">

<div class="col-sm-12 nopadding">

	<div id="divname">
			
    </div>
    <hr>
</div>

  <div class="col-sm-12 nopadding">
        <div class="col-sm-3 nopadding">

      	<div id="sales" class="divhid">
            <div style="padding-left:10px; padding-top:3px">
				<ul class="ver-inline-menu tabbable margin-bottom-25">  

					<?php
						foreach($navmenu as $row){
							if($row['main_id']==65){
								?>
									<li>
										<a href="" onClick="setI('A','<?=$row['url']?>')" data-toggle="tab">
										<i class="fa fa-book"></i> <?=$row['title']?> </a>
									</li>
								<?php
							}
						}
					?>
					
              	</ul>
			</div>
        </div>
               
        <div id="purch" class="divhid">
            <div style="padding-left:10px; padding-top:3px">
				<ul class="ver-inline-menu tabbable margin-bottom-25">  
					<?php
						foreach($navmenu as $row){
							if($row['main_id']==75){
								?>
									<li>
										<a href="" onClick="setI('A','<?=$row['url']?>')" data-toggle="tab">
										<i class="fa fa-book"></i> <?=$row['title']?> </a>
									</li>
								<?php
							}
						}
					?>
              	</ul>
			</div>       
        </div>
        
		<div id="acc" class="divhid">
            <div style="padding-left:10px; padding-top:3px">
				<ul class="ver-inline-menu tabbable margin-bottom-25">  
					<?php
						foreach($navmenu as $row){
							if($row['main_id']==83){
								?>
									<li>
										<a href="" onClick="setI('A','<?=$row['url']?>')" data-toggle="tab">
										<i class="fa fa-book"></i> <?=$row['title']?> </a>
									</li>
								<?php
							}
						}
					?>
              	</ul>
			</div>       
        </div>
        
		<div id="bir" class="divhid">
            <div style="padding-left:10px; padding-top:3px">
				<ul class="ver-inline-menu tabbable margin-bottom-25">  
					<?php
						foreach($navmenu as $row){
							if($row['main_id']==94){
								?>
									<li>
										<a href="" onClick="setI('A','<?=$row['url']?>')" data-toggle="tab">
										<i class="fa fa-book"></i> <?=$row['title']?> </a>
									</li>
								<?php
							}
						}
					?>
              	</ul>
			</div>       
        </div>

        <div id="inv" class="divhid">
            <div style="padding-left:10px; padding-top:3px">
				<ul class="ver-inline-menu tabbable margin-bottom-25">  
					<?php
						foreach($navmenu as $row){
							if($row['main_id']==101){
								?>
									<li>
										<a href="" onClick="setI('A','<?=$row['url']?>')" data-toggle="tab">
										<i class="fa fa-book"></i> <?=$row['title']?> </a>
									</li>
								<?php
							}
						}
					?>
              	</ul>
			</div>       
        </div>
  

        </div>
    	<div class="col-sm-9 nopadwleft">

                <iframe src="" frameborder="0" scrolling="no" id="myreport" width="100%" height="600px"> </iframe>

        </div> 
 </div>

</body>
</html>