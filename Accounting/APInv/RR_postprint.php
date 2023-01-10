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

<body style="padding:5px" onLoad="POSTDR();">

			<div id="alrtmsg">
            </div>

</body>
</html>

<script type="text/javascript">
	
function POSTDR(){
	
				var itmstat = "";
				var x = "POST";
				var num = "<?php echo $_REQUEST['tranno']?>";

				$.ajax ({
				dataType: "text",
				url: "../../include/th_toAcc.php",
				data: { tran: num, type: "RR" },
				async: false,
				success: function( data ) {
					//alert(data.trim());
					if(data.trim()=="True"){
						//itmstat = "OK";
						
						$.ajax ({
							url: "../../include/th_toInv.php",
							data: { tran: num, type: "RR" },
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
						itmstat = data.trim();	
					}
				}
			});

	if(itmstat=="OK"){
	$("#alrtmsg").show();
	
		$.ajax ({
			url: "RR_Tran.php",
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
						$("#alrtmsg").html(item.stat);	
						
								setTimeout(function() {
						
									window.parent.document.getElementById("hdnposted").value = 1;
									window.parent.document.getElementById("salesstat").innerHTML = "POSTED";
									window.parent.document.getElementById("salesstat").style.color = "#FF0000";
									window.parent.document.getElementById("salesstat").style.fontWeight = "bold";
									
									location.href = "RR_print.php?x="+num;
						
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
