<?php
//if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


//Insert into Trial Balance Month Tab
$dte1 = $_REQUEST['closedate1'];
$dte2 = $_REQUEST['closedate2'];

mysqli_query($con,"INSERT INTO trialbalmonth(`ddatefrom`, `ddateto`, `ddate`) values(STR_TO_DATE('$dte1', '%m/%d/%Y'),STR_TO_DATE('$dte2', '%m/%d/%Y'),NOW())");


//InsertTransactions in trialbaltrans table

//SALES
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'SI','N',dcutdate, 2 from sales where dcutdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");

//PURCHASES
//mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'RR','N',dreceived, 1 from receive where dreceived between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");

//ACCOUNTING MODULES

//JOURNAL ENTRIES
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'JE','N',djdate, 9 from journal where djdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");

//AP VOUCHERS
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'APV','N',dapvdate, 6 from apv where dapvdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");

//CHECK ISSUANCE
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `csortval`) Select ctranno,'PV','N',dcheckdate, 7 from paybill where dcheckdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");

//CM
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'CM','N',dcutdate, 3 from aradj where dcutdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1 and ctype='Credit'");

//DM
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'DM','N',dcutdate, 4 from aradj where dcutdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1 and ctype='Debit'");

//AR PAYMENTS
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'OR','N',dcutdate, 5 from receipt where dcutdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");

//DPOSIT
mysqli_query($con,"INSERT INTO trialbaltrans(`ctranno`, `cmodule`, `cremarks`, `ddate`, `csortval`) Select ctranno,'BD','N',dcutdate , 8from deposit where dcutdate between STR_TO_DATE('$dte1', '%m/%d/%Y') and STR_TO_DATE('$dte2', '%m/%d/%Y') and lapproved=1");


//--redirect to acct entry generation

?>
<script type="text/javascript">location.href = 'POS_Del.php';</script>
