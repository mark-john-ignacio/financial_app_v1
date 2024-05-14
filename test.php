<?php
    include('Connection/connection_string.php');

    $sql = "SELECT * from apv where compcode='002' and ctranno in ('AP012400018',
    'AP012400022',
    'AP022400000',
    'AP022400004',
    'AP022400006',
    'AP022400008',
    'AP022400010',
    'AP022400027',
    'AP022400031',
    'AP032400000',
    'AP032400007',
    'AP032400009',
    'AP032400010',
    'AP032400017',
    'AP032400021',
    'AP032400041',
    'AP032400068',
    'AP032400086',
    'AP032400103',
    'AP032400114',
    'AP032400120',
    'AP032400156',
    'AP032400174',
    'AP032400189',
    'AP032400199',
    'AP032400204',
    'AP042400000',
    'AP042400022',
    'AP042400047',
    'AP042400062',
    'AP042400064',
    'AP042400080',
    'AP042400089',
    'AP042400120',
    'AP042400181',
    'AP052400000',
    'AP052400017')";
    $result = mysqli_query($con, $sql);
    $rowcount=mysqli_num_rows($result);

    if($rowcount>0){
        $cnt = 0;
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $cnt++;
            mysqli_query($con,"DELETE FROM `glactivity` where compcode='002' and `ctranno` = '".$row['ctranno']."'");

            mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) Select '$company','APV','$tranno',A.dapvdate,B.cacctno,B.ctitle,B.ndebit,B.ncredit,0,NOW(),B.cewtcode From apv A left join apv_t B on  A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='002' and A.ctranno='".$row['ctranno']."'");

            echo $cnt.": ".$row['ctranno']."<br>";
        }
    }

?>