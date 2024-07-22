<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');


	$company = $_SESSION['companyid'];


?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">

<head>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">   
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px" onLoad="trans();">

			<div id="alrtmsg">
            </div>

</body>
</html>

<script>
function trans(){

var x = "POST";
var num = "<?php echo $_REQUEST['tranno']?>";
var msg = "Posted";
var id = "<?php echo $_REQUEST['id']?>";
var xcred = "<?php echo $_REQUEST['lmt']?>";
var itmstat = "";

if(x=="POST"){
			//generate GL ENtry muna
			$.ajax ({
				dataType: "text",
				url: "../../include/th_toAcc.php",
				data: { tran: num, type: "SI" },
				async: false,
				success: function( data ) {
					//alert(data.trim());
					if(data.trim()=="True"){
						itmstat = "OK";
					}
					else{
						itmstat = data.trim();	
					}
				}
			});			

	}
else{
	var itmstat = "OK";	
}


if(itmstat=="OK"){
	$.ajax ({
		url: "SI_Tran.php",
		data: { x: num, typ: x },
		async: false,
		dataType: "json",
		beforeSend: function(){

				$("#alrtmsg").attr("class", "alert alert-success");
				$("#alrtmsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");

		},
		success: function( data ) {
			
			console.log(data);
			$.each(data,function(index,item){
				
				itmstat = item.stat;
				
				if(itmstat!="False"){

					$("#alrtmsg").attr("class", "alert alert-success");
					$("#alrtmsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
					
					
								setTimeout(function() {
						
									window.parent.document.getElementById("hdnposted").value = 1;
									window.parent.document.getElementById("salesstat").innerHTML = "POSTED";
									window.parent.document.getElementById("salesstat").style.color = "#FF0000";
									window.parent.document.getElementById("salesstat").style.fontWeight = "bold";
									
									location.href = "SI_print.php?x="+num;
						
								}, 2000); // milliseconds = 2seconds


				}
				else{
					$("#alrtmsg").attr("class", "alert alert-danger");
					$("#alrtmsg").html(item.ms);
				}
			});
		}
	});
}else{				
					$("#alrtmsg").attr("class", "alert alert-danger");
					$("#alrtmsg").html("<b>ERROR: </b>There's a problem with your transaction!<br>"+itmstat);

}
}
</script>