<html>
<title> SMS Reader</title>

<body>

<?php
$rows = file("../SMSCaster/RecvSms.csv");
$last_row = array_pop($rows);
$data = str_getcsv($last_row);

echo "ID: ".$data[0]."<br>";
echo "COM: ".$data[1]."<br>";
echo "FROM: ".$data[2]."<br>";
echo "MESSAGE: ".$data[3]."<br>";
echo "DATE: ".$data[4]."<br>";


?>
</body>
</html>