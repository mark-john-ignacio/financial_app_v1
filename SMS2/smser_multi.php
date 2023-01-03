<?
/*
 Simple and easy for modification, PHP script for SMS sending through HTTP with you own Sender ID and delivery reports. 
 You just have to type your account information on www.proovl.com and upload file on server.

 https://www.facebook.com/2WaySMSGateway
 https://www.twitter.com/2waysms
 
  Video Instruction:  https://www.youtube.com/watch?v=SAcQafaW7ek
 
 Istruction:
 
  Find 3 parameters in <body> and type your account information on PROOVL
 
1. $user = "*************"; // Change ********, and put your Proovl user ID
2. $token = "***************"; // Change ********, and put your Proovl authentication token
3. $from = "***************"; // Change ********, phone number under your Proovl account
 

*/


?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP Bulk SMS script</title>
<meta name="description" content="PHP script for Bulk SMS sending. Send Bulk SMS with API connection.">
<style type="text/css">
body{
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif; 
	font-size:12px;
}
p, h1, form, button{border:0; margin:0; padding:0;}
.spacer{clear:both; height:1px;}
/* ----------- My Form ----------- */
.myform{
	margin:0 auto;
	width:400px;
	padding:14px;
}
/* ----------- stylized ----------- */
	#stylized{
		border:solid 2px #b7ddf2;
		background:#ebf4fb;
	}
	#stylized h1 {
		font-size:14px;
		font-weight:bold;
		margin-bottom:8px;
	}
	#stylized p{
		font-size:11px;
		color:#666666;
		margin-bottom:20px;
		border-bottom:solid 1px #b7ddf2;
		padding-bottom:10px;
	
}
	</style> 
		<script type="text/javascript">
	
//Edit the counter/limiter value as your wish
var count = "160";   //Example: var count = "175";
function limiter(){
var tex = document.myform.text.value;
var len = tex.length;
if(len > count){
        tex = tex.substring(0,count);
        document.myform.text.value =tex;
        return false;
}
document.myform.limit.value = count-len;
}


</script> 

</head>

<body>
<?

// Video Instruction:  https://www.youtube.com/watch?v=SAcQafaW7ek

$user = "********"; // Change ********, and put your Proovl user ID
$token = "********"; // Change ********, and put your Proovl authentication token
$from = "********"; // Change ********, phone number under your Proovl account

$option = $_REQUEST["option"];
$text = $_REQUEST["text"];
$to = $_REQUEST["to"];

	switch ($option) {

	case sendsms:
		
		if ($text == "") { echo "<center>Error!<br>Text not entered<br><a href=\"javascript:history.back(-1)\">Go Back</a></center>"; die; } else { }
		if ($to == "") { echo "<center>Error!<br>Number not entered<br><a href=\"javascript:history.back(-1)\">Go Back</a></center>"; die; } else { }
		
	// You can change explode to (, \n ; . -) or something else. 
	
		$to_arr = explode(";", $to);

		foreach ($to_arr as $to_x){	
		
	$url = "https://www.proovl.com/api/send.php";

	$postfields = array(
		'user' => "$user",
		'token' => "$token",
		'route' => "$route",
		'from' => "$from",
		'to' => "$to_x",
		'text' => "$text"
	);

	if (!$curld = curl_init()) {
		exit;
	}

	curl_setopt($curld, CURLOPT_POST, true);
	curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($curld, CURLOPT_URL,$url);
	curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($curld);
	
	$result = explode(';',$output);

	curl_close ($curld);
	
	$created = date('Y-m-d H:i:s');
		
		}
		
		
		if ($result[0] == "Error") {
			echo "<center>Error message:<b> $result[1] </b><br><br>script setup video instruction:<br>  <a href=\"https://www.youtube.com/watch?v=SAcQafaW7ek\" target=\"_blank\">https://www.youtube.com/watch?v=SAcQafaW7ek</a><br><br><a href=\"smser_multi.php\" ><b>Go Back</b></a></center>";

	} else {
		echo "<center>Date: $created <br>";
			echo "To: $to <br>";
			echo "Message ID: $result[1] <br>";
			echo "Message Status: $result[0] <br>";
			echo "<br><a href=\"smser_multi.php\"><b>Send New SMS Message</b></a></center>";
	}
		
		//Header("Location: $PHP_SELF");
	break;

	default:

	echo "<div id=\"stylized\" class=\"myform\">"
	   ."<b>Proovl sms api demo</b>"
	   ."<form name=\"myform\" method=post action=\"$PHP_SELF?option=sendsms\">"
	   ."<table width=100% border=\"0\">"
	   ."<tr>"
		 ."<td>Numbers<br>(separate with ;)</td>"
		 ."<td><textarea style=\"resize: none;width:80%;border: 1px solid #523f6d;outline:none;\" placeholder=\"xxxxxxx;xxxxxxx;xxxxxxx;\"  rows=\"4\" cols=\"25\" name=\"to\"></textarea></td>"
	   ."</tr>"
	   ."<tr style=\"padding:10px;\">"
		 ."<td>Message: </td>"
		 ."<td><textarea style=\"resize: none;width:80%;padding;5px;border: 1px solid #523f6d;outline:none;\" wrap=physical rows=\"4\" cols=\"25\" name=\"text\" onkeyup=limiter()></textarea></td>"
	   ."</tr>"
	 	   ."<tr>"
	   ."<td></td>"
      ."<td>Character left: <script type=\"text/javascript\">"
       ."document.write(\"<input type=text name=limit size=4 readonly value=\"+count+\">\");"
       ."</script><br></td>"
	   ."</tr>"
	   ."<tr>"
		 ."<td>&nbsp;</td>"
		 ."<td><input style=\"width:8em;font-size:10px;\" type=submit name=submit value=Send>"
		 ."<div class=\"spacer\"></div></td>"
	   ."</tr>"
	   ."</table>"
	   ."</form>"
	   	   ."<a href=https://www.proovl.com/sms-api>Proovl Api</a>"
	."</div>";


	}

?>

</body>
</html>