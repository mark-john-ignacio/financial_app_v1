<?php
    include('Connection/connection_string.php');
    $compcode = '001';
    $sql = "SELECT * from apv where compcode='$compcode' and ctranno in ('AP012400009',
    'AP012400013',
    'AP012400021',
    'AP012400024',
    'AP012400025',
    'AP012400026',
    'AP012400028',
    'AP012400029',
    'AP012400033',
    'AP012400036',
    'AP012400037',
    'AP012400040',
    'AP022400006',
    'AP022400015',
    'AP022400021',
    'AP022400022',
    'AP022400027',
    'AP022400031',
    'AP022400032',
    'AP022400034',
    'AP022400037',
    'AP022400038',
    'AP022400039',
    'AP032400000',
    'AP032400002',
    'AP032400014',
    'AP032400022',
    'AP032400027',
    'AP032400042',
    'AP032400049',
    'AP032400055',
    'AP042400000',
    'AP042400001',
    'AP042400010',
    'AP042400019',
    'AP042400022',
    'AP042400036',
    'AP042400042',
    'AP042400043',
    'AP042400046',
    'AP042400047',
    'AP042400048',
    'AP042400050',
    'AP042400054',
    'AP042400055',
    'AP052400000',
    'AP052400013',
    'AP052400015',
    'AP052400018',
    'AP052400029',
    'AP052400038')";
    $result = mysqli_query($con, $sql);
    $rowcount=mysqli_num_rows($result);

    if($rowcount>0){
        $cnt = 0;
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $cnt++;
            mysqli_query($con,"DELETE FROM `glactivity` where compcode='$compcode' and `ctranno` = '".$row['ctranno']."'");

            mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) Select '$compcode','APV','".$row['ctranno']."',A.dapvdate,B.cacctno,B.ctitle,B.ndebit,B.ncredit,0,NOW(),B.cewtcode From apv A left join apv_t B on  A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='002' and A.ctranno='".$row['ctranno']."'");

            echo $cnt.": ".$row['ctranno']."<br>";
        }
    }

?>