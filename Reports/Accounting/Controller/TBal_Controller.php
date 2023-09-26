<?php

    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "GLedger.php";
    
    include('../../../Connection/connection_string.php');

    $ctranno = mysqli_real_escape_string($con, $_POST['ctranno']);
    $module = mysqli_real_escape_string($con, $_POST['module']);
    $company = $_SESSION['companyid'];

    /**
     * {String} $module what module of General Ledger Report Transaciton
     */

    $controller = match($module){

        'DR' => "select * from dr_t a
                left join
                (
                        select a.*,b.ctin, b.cname, b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit, c.cname as cdelname, d.cname as csalesmaname 
                        from dr a 
                        left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
                        left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid 
                        left join salesman d on a.compcode=d.compcode and a.csalesman=d.ccode 
                        where a.ctranno = '$ctranno' and a.compcode='$company'
                ) b on a.ctranno = b.ctranno and a.compcode = b.compcode

                left join items c on a.compcode = c.compcode and a.citemno = c.cpartno
                where a.ctranno = '$ctranno' and a.compcode='$company'",

        'SI' => "select a.*, c.ctin, c.cname, d.citemdesc, b.csalestype, b.ddate, b.cremarks from sales_t a
                left join sales b on a.compcode = b.compcode and a.ctranno = b.ctranno
                left join customers c on a.compcode = c.compcode and b.ccode = c.cempid
                left join items d on a.compcode = d.compcode and a.citemno = d.cpartno
                where a.compcode = '$company' and a.ctranno='$ctranno'",

        'IN' => "select X.ctranno, X.creference as cref, C.ctin, B.ddate, X.nrefident, X.citemno as cpartno, A.citemdesc, X.cunit, X.nqty as totqty, 1 as nqty, X.nprice,  X.nbaseamount, X.namount, A.cunit as qtyunit, X.nfactor, X.ndiscount, A.ctype, X.ctaxcode, C.cname, B.csalestype
                from ntsales_t X
                left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
                left join ntsales B on X.compcode = B.compcode and X.ctranno = B.ctranno
                left join customers C on B.compcode = C.compcode and B.ccode = C.cempid 
                where X.compcode='$company' and X.ctranno = '$ctranno' Order By X.nident",

        'JE' => "select a.*, b.* from journal_t a
                left join journal b on a.compcode = b.compcode and a.ctranno = b.ctranno
                where a.compcode='$company' and a.ctranno = '$ctranno' order by a.nident",

        'ARADJ' => "select a.*, b.ddate, c.ctin, b.crefsi, b.crefsr, c.cname, d.cdesc From aradjustment_t a 
                left join aradjustment b on a.compcode = b.compcode and a.ctranno = b.ctranno
                left join customers c on b.compcode = c.compcode and b.ccode = c.cempid
                left join groupings d on c.compcode = d.compcode and a.nident = d.nidentity
                where a.compcode='$company' and a.ctranno = '$ctranno'",

        'OR' => "select a.*, e.ctin, b.dcutdate, e.ctin, e.ddate, c.cacctdesc, d.cdesc, e.cname from receipt_sales_t a 
                left join sales b on a.csalesno=b.ctranno and a.compcode=b.compcode 
                left join accounts c on a.cacctno=c.cacctid and a.compcode=c.compcode 
                left join groupings d on a.compcode = d.compcode and a.nidentity = d.nidentity
                left join (
                        SELECT a.compcode, a.ctranno, a.ddate, b.cname, b.ctin
                        from receipt a
                        left join customers b on a.compcode = b.compcode and a.ccode = b.cempid
                        where a.compcode ='$company' and a.ctranno = '$ctranno'
                ) e on a.compcode = e.compcode and a.ctranno = e.ctranno
                where a.compcode='$company' and a.ctranno = '$ctranno' order by a.nidentity",

        'BD' => "select a.*, b.cornumber, b.dcutdate, b.cremarks as remarks_t, b.cpaymethod, b.namount, c.cacctdesc, c.ddate, c.namount
        from deposit_t a 
        left join receipt b on a.compcode=b.compcode and a.corno=b.ctranno and a.compcode=b.compcode 
        left join (
                SELECT a.compcode, a.ctranno, b.cacctdesc, a.ddate, a.namount
                from deposit a
                left join accounts b on a.compcode = b.compcode and a.cacctcode = b.cacctid
                where a.compcode = '$company' and a.ctranno='$ctranno'
        ) c on a.compcode = c.compcode and a.ctranno = c.ctranno
        where a.compcode='$company' and a.ctranno = '$ctranno' ",

        'PV' => "Select A.cacctno, b.ctranno, d.ctin, b.bankname, b.cpayrefno, b.ddate, A.crefrr, a.capvno, DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.namount, a.nowed, a.napplied, IFNULL(b.npayed,0) as npayed, c.cacctdesc, a.newtamt, d.cname
		From paybill_t a
		left join
			(
				select x.capvno, y.ccode, y.ctranno, y.cpayrefno, y.ddate, z.cname as bankname, sum(x.napplied) as npayed
				from paybill_t x 
                                left join paybill y on x.compcode=y.compcode and x.ctranno=y.ctranno
                                left join bank z on x.compcode=z.compcode and y.cbankcode=z.ccode
				where x.compcode = '$company' and x.ctranno = '$ctranno'
				group by x.capvno
			) b on a.capvno=b.capvno
		left join accounts c on a.compcode=c.compcode and a.cacctno=c.cacctid 
                left join suppliers d on a.compcode = d.compcode and b.ccode = d.ccode
		where a.compcode='$company' and a.ctranno='$ctranno' ",        
    
        'APV' => "select a.*, b.*, c.ctin from apv_d a
                left join apv b on a.compcode = b.compcode and a.ctranno = b.ctranno
                left join suppliers c on a.compcode = c.compcode and b.ccode = c.ccode
                where a.compcode = '$company' and a.ctranno = '$ctranno'",      
        
        'APADJ' => "select a.*, b.cacctno, b.ctitle, b.ndebit, b.ncredit, b.cremarks as remark_t, c.* from apadjustment a
                left join apadjustment_t b on a.compcode=b.compcode and a.ctranno=b.ctranno
                left join suppliers c on a.compcode=c.compcode and a.ccode=c.ccode
                where a.compcode='$company' and a.ctranno='$ctranno'",
        default => [
            'errCode' => 'ERR_DATA',
            'errMsg' => 'Data no referrence'
        ]
    };

    $result = mysqli_query($con, $controller);
    $data = [];
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        array_push($data, $row);
    };
    echo json_encode([
       'valid' => true,
       'data' => $data, 
    ]);