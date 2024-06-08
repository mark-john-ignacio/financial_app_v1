<?php
if(!isset($_SESSION)){
    session_start();
}

include('../../Connection/connection_string.php');
// include('../../include/denied.php');
// include('../../include/access2.php');

$company = $_SESSION['companyid'];
$prepared = mysqli_real_escape_string($con, $_SESSION['employeeid']);

$itemcode = $_REQUEST['code'];
$date = date("Y-m-d");
$data = [];


$sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, a.cunit, a.cstatus, a.linventoriable as isInventory, ifnull(c.nqty,0) as nqty
			from items a 
			left join
				(
					select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
					From tblinventory a
					right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
					where a.compcode='$company' and  a.dcutdate <= '$date' and d.cpartno = '$itemcode'
					group by a.citemno
				 ) c on a.cpartno=c.citemno
			WHERE a.compcode='$company' and a.cpartno = '$itemcode'";

$query = mysqli_query($con, $sql);

$sql_check = "SELECT * FROM pos_cart WHERE item = '$itemcode' AND employee_name = '$prepared'";
$result = mysqli_query($con, $sql_check);

if (mysqli_num_rows($result) == 0) {
    $sql_insert = "INSERT INTO pos_cart (`item`, `quantity`, `employee_name`) VALUES ('$itemcode', '1', '$prepared')";
    mysqli_query($con, $sql_insert);
}
else{
    $sql_update = "UPDATE pos_cart SET quantity = quantity + 1 WHERE item = '$itemcode' AND employee_name = '$prepared'";
    mysqli_query($con, $sql_update);
}

if(mysqli_num_rows($query) != 0){
    while($row = $query -> fetch_assoc()){


        if($row['isInventory']==0){
            if((float)$row['nqty']<=0){
                echo json_encode([
                    'valid' => false,
                    'msg' => "No more stock available!"
                ]);
            }else{
                $json['partno'] = $row['cpartno'];
                $json['name'] = $row['citemdesc'];
                $json['unit'] = $row['cunit'];
                $json['quantity'] = $row['nqty'];
                $json['isInventory'] = $row['isInventory'];
                array_push($data, $json);
                echo json_encode([
                    'valid' => true,
                    'data' => $data
                ]);
            }
        }else{
            $json['partno'] = $row['cpartno'];
            $json['name'] = $row['citemdesc'];
            $json['unit'] = $row['cunit'];
            $json['quantity'] = $row['nqty'];
            $json['isInventory'] = $row['isInventory'];
            array_push($data, $json);
            echo json_encode([
                'valid' => true,
                'data' => $data
            ]);
        }
        

        if($row['cstatus']=="INACTIVE"){
            echo json_encode([
                'valid' => false,
                'msg' => "Item is currently inactive!"
            ]);
        }

        
    }
        
} else {
    echo json_encode([
        'valid' => false,
        'errorMsg' => 'No Data Has Found'
    ]);
}


