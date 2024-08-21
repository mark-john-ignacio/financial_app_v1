<?php

	$d = explode('Physical Address. . . . . . . . .',shell_exec ("ipconfig/all"));  
	$d1 = explode(':',$d[1]);  
	$d2 = explode(' ',$d1[1]);  
	echo $d2[1];

?>
  