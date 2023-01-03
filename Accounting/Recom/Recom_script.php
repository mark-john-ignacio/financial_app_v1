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


echo $_REQUEST["typ"];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script type="text/javascript">
//function Check(){
var GenStat = "NO";

	$(document).ready(function(e) {
		
		if($("#txttyp").val() == "SI"){
			//alert("A");
			forSI();
		}else if($("#txttyp").val()  == "RR"){
			//alert("B");
			forRR();
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
				url: "th_toAcc.php",
				data: { tran: itm, type: "SI" },
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

function forRR(){
	//alert("B");
var itmstat = "";
var itm = $("#txtctranno").val();

				$.ajax ({
					dataType: "text",
					url: "th_toAcc.php",
					data: { tran: itm, type: "RR" },
					async: false,
					success: function( data ) {
						//alert(data.trim());
						if(data.trim()=="True"){
							//itmstat = "OK";
							
							$.ajax ({
								url: "th_toInv.php",
								data: { tran: itm , type: "RR" },
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
										document.write("ERROR: "+itm)		
									}
								}
							});
							
						}
						else{
							document.write("ERROR: "+itm)	
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

