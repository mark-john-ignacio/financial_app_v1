<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];

	if($_REQUEST["typ"]=="SI"){
		$varcall = "forSI";
	}elseif($_REQUEST["typ"]=="RR"){
		$varcall = "forRR";	
	}

	mysqli_query($con,"UPDATE transactions set cremarks='Y' where `ctranno` = '".$_REQUEST["id"]."'");

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script type="text/javascript">
//function Check(){
var GenStat = "NO";

	$(document).ready(function(e) {
		
		if($("#txttyp").val() == "SI"){
			//alert("A");
			forSI();
		}else if($("#txttyp").val() == "IN"){
			//alert("A");
			forIN();
		}else if($("#txttyp").val()  == "OR"){
			//alert("B");
			forOR();
		}else if($("#txttyp").val()  == "APV"){
			//alert("B");
			forAPV();
		}else if($("#txttyp").val()  == "PV"){
			//alert("B");
			forPV();
		}else if($("#txttyp").val()  == "JE"){
			//alert("B");
			forJE();
		}
		
       
    });
//}


function forSI(){
	//alert("A");
	var itmstat = "";
	var itm = $("#txtctranno").val();

	//generate GL ENtry muna
	$.ajax ({
		dataType: "text",
		url: "../../include/th_toAcc.php",
		data: { tran: itm, type: "SI" },
		async: false,
		success: function( data ) {
			//alert(data.trim());
			if(data.trim()=="True"){
				itmstat = "OK";

				//$.ajax ({
				//	url: "th_toInv.php",
				//	data: { tran: itm, type: "SI" },
				//	async: false,
				//	success: function( data ) {
						//alert(data.trim());
				//		if(data.trim()=="True"){
							GenStat = "OK";
							//window.top.location.href = "BatchPOSGL.php";
							top.window.location="BatchPOSGL.php";
							document.write("OK");
				//		}
				//		else{
				//			document.write("ERROR: "+itm);		
				//		}
				//	}
				//});

			}
			else{
				document.write("ERROR: "+itm);	
			}
		}
	});

}

function forIN(){
	//alert("A");
	var itmstat = "";
	var itm = $("#txtctranno").val();

	//generate GL ENtry muna
	$.ajax ({
		dataType: "text",
		url: "../../include/th_toAcc.php",
		data: { tran: itm, type: "IN" },
		async: false,
		success: function( data ) {
			//alert(data.trim());
			if(data.trim()=="True"){
				itmstat = "OK";

				$.ajax ({
					url: "th_toInv.php",
					data: { tran: itm, type: "SI" },
					async: false,
					success: function( data ) {
						//alert(data.trim());
						if(data.trim()=="True"){
							GenStat = "OK";
							//window.top.location.href = "BatchPOSGL.php";
							top.window.location="BatchPOSGL.php";
							document.write("OK");
						}
						else{
							document.write("ERROR: "+itm);		
						}
					}
				});

			}
			else{
				document.write("ERROR: "+itm);	
			}
		}
	});

}

function forAPV(){
	//alert("A");
	var itmstat = "";
	var itm = $("#txtctranno").val();

	$.ajax ({
		url: "../../Accounting/APV/APV_Tran.php",
		data: { x: itm, typ: "POST" },
		async: false,
		dataType: "json",
		success: function( data ) {
			console.log(data);
			$.each(data,function(index,item){
				
				itmstat = item.stat;

				if(itmstat!="False"){
					
					top.window.location="BatchPOSGL.php";
					document.write("OK");

				}
				else{
					document.write("ERROR: "+itm);		
				}
			});
		}
	});

}

function forOR(){
	//alert("A");
	var itmstat = "";
	var itm = $("#txtctranno").val();

	$.ajax ({
		dataType: "text",
		url: "../../include/th_toAcc.php",
		data: { tran: itm, type: "OR" },
		async: false,
		success: function( data ) {
			//alert(data.trim());
			if(data.trim()=="True"){
				top.window.location="BatchPOSGL.php";
				document.write("OK");						
			}
			else{
				document.write("ERROR: "+itm);
			}
		}
	});

}

function forPV(){
	//alert("A");
	var itmstat = "";
	var itm = $("#txtctranno").val();

	$.ajax ({
		dataType: "text",
		url: "../../include/th_toAcc.php",
		data: { tran: itm, type: "PV" },
		async: false,
		success: function( data ) {
			//alert(data.trim());
			if(data.trim()=="True"){
				top.window.location="BatchPOSGL.php";
				document.write("OK");						
			}
			else{
				document.write("ERROR: "+itm);
			}
		}
	});

}

function forJE(){
	//alert("A");
	var itmstat = "";
	var itm = $("#txtctranno").val();

	$.ajax ({
		dataType: "text",
		url: "../../include/th_toAcc.php",
		data: { tran: itm, type: "JE" },
		async: false,
		success: function( data ) {
			//alert(data.trim());
			if(data.trim()=="True"){
				top.window.location="BatchPOSGL.php";
				document.write("OK");						
			}
			else{
				document.write("ERROR: "+itm);
			}
		}
	});

}
</script>

</head>
<body>

<form action="BatchPOSGL.php" name="frmpos" id="frmpos" method="post">

    <input type="text" id="txtctranno" name="txtctranno" value="<?php echo $_REQUEST["id"];?>">
    <input type="text" id="txttyp" name="txttyp" value="<?php echo $_REQUEST["typ"];?>">
    
    <button type="button" onClick="Check();">CLICK </button>

</form>


</body>
</html>

