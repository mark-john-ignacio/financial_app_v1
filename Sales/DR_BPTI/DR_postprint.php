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
	
	
var xChkLimitWarn = "";
var balance = "";


	var xBalance = 0;
	var itmstat = "";
	var x = "";
	var num = "";
	var id = "";
	var xcred = ""; 

function POSTDR(){
	
	   			$.ajax({
					url : "../../include/th_xtrasessions.php",
					type: "Post",
					async:false,
					dataType: "json",
					success: function(data)
					{	
					   console.log(data);
                       $.each(data,function(index,item){
						   xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
						   
					   });
					}
				});

	
				var x = "POST";
				var num = "<?php echo $_REQUEST['tranno']?>";
				var id = "<?php echo $_REQUEST['id']?>";
				var xcred = "<?php echo $_REQUEST['lmt']?>"; 
				
				if(x=="POST"){
					var msg = "POSTED";
				}
				else if(x=="CANCEL"){
					var msg = "CANCELLED";
				}

			
//---------------------------------------------
			
		if(x=="POST"){
		//alert(id);
			if(xChkLimitWarn==1){
				var xinvs = 0;
				var xors = 0;
				
					$.ajax ({
						url: "../th_creditlimit.php",
						data: { id: id },
						async: false,
						dataType: "json",
						success: function( data ) {
														
							console.log(data);
							$.each(data,function(index,item){
								if(item.invs!=null){
									xinvs = item.invs;
								}
								
								if(item.ors!=null){
									xors = item.ors;
								}
								
							});
						}
					});
				
				//alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
					
				xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);
			
			}
		}

//---------------------------------------------
		$("#alrtmsg").show();
		
		$.ajax ({
			url: "DR_Tran.php",
			data: { x: num, typ: x, warn: xChkLimitWarn, bal: xBalance },
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
					}
					else{
						$("#alrtmsg").attr("class", "alert alert-danger");
						$("#alrtmsg").html(item.ms);
					}
				});
			}
		});
		
//----------------------------

			if(itmstat=="Posted"){
				//Pag Posted .. Insert to inventory table -MINUS
		
				$.ajax ({
					url: "../../include/th_toInv.php",
					data: { tran: num, type: "DR" },
					async: false,
					success: function( data ) {
							itmstat = data.trim();
					}
				});
		
				if(itmstat!="False"){ //Proceed sa insert Account Entry
			
					$.ajax ({
						url: "../../include/th_toAcc.php",
						data: { tran: num, type: "DR" },
						async: false,
						success: function( data ) {
							if(data.trim()!="False"){
								
								$("#alrtmsg").attr("class", "alert alert-success");
								$("#alrtmsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully Posted...");
			
								setTimeout(function() {
					
								
									window.parent.document.getElementById("hdnposted").value = 1;
									window.parent.document.getElementById("salesstat").innerHTML = "POSTED";
									window.parent.document.getElementById("salesstat").style.color = "#FF0000";
									window.parent.document.getElementById("salesstat").style.fontWeight = "bold";
									location.href = "DR_print.php?x="+num;
						
								}, 2000); // milliseconds = 2seconds
						


		
							}
							else{

								$("#alrtmsg").attr("class", "alert alert-danger");
								$("#alrtmsg").html("<b>ERROR: </b>There's a problem generating your account entry!");
			
							}
						}
					});
				}
				else{

								$("#alrtmsg").attr("class", "alert alert-danger");
								$("#alrtmsg").html("<b>ERROR: </b>There's a problem generating your inventory and account entry!");

				}
		
			}
			
//------------------------------------

		}
	
</script>
