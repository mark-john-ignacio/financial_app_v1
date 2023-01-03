<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	echo $_POST['tbLVL1count'];

?>
