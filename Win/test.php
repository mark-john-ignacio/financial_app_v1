<?php

foreach ($_POST['xyz'] as $key => $val) {
    echo $key . "=>". $val. "<br>";
}


$cnt = count($_POST['same']);

for($i=1; $i<=$cnt; $i++){
	echo $_POST['xyz'][$i]. "<br>";
}
?>