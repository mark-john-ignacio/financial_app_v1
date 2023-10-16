<?php
if(!isset($_SESSION)){
    session_start();
}

include('../../Connection/connection_string.php');
// include('../../include/denied.php');
// include('../../include/access2.php');

$company = $_SESSION['companyid'];

$itemcode = $_REQUEST['query'];
$date = date("Y-m-d");
$data = [];


$sql = "SELECT a.cpartno, a.cpartno as cscancode, a.citemdesc, a.cunit, a.cstatus, ifnull(c.nqty,0) as nqty, a.linventoriable
			from items a 
			left join
				(
					select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
					From tblinventory a
					right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
					where a.compcode='$company' and  a.dcutdate <= '$date' and d.lbarcode = '$itemcode'
					group by a.citemno
				 ) c on a.cpartno=c.citemno
			WHERE a.compcode='$company' and a.lbarcode = '$itemcode' Limit 1 ";

$query = mysqli_query($con, $sql);
if(mysqli_num_rows($query) != 0){
    while($row = $query -> fetch_assoc()){


        if($row['linventoriable']==0){
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


