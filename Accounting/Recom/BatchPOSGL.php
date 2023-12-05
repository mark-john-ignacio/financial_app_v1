<?php

include('../../Connection/connection_string.php');

$sqlhead = mysqli_query($con,"SELECT * FROM `transactions` Where cremarks='N' order by dcutdate, cnum, ddate");
$dcnt = mysqli_num_rows($sqlhead);
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	
	$ctyp = $row['ctype'];
	$cid = $row['ctranno'];
	$dte = $row['dcutdate'];
?>

 <form action="Recom_frame.php" name="frmsend" id="frmsend" method="post">
 	<input type="hidden" name="typ" id="typ" value="<?php echo $ctyp;?>" />
    <input type="hidden" name="id" id="id" value="<?php echo $cid;?>" />
    <input type="hidden" name="dte" id="dte" value="<?php echo $dte;?>" />
	<input type="hidden" name="cnthdr" id="cnthdr" value="<?=$dcnt?>" />
 </form>

 <script>
 	document.getElementById('frmsend').submit();
 </script>

<?php
}
else{
	
?>
 <script>
 	document.write("DONE");
 </script>
<?php
}
?>

<!--

INSERT INTO transactions (ctype,ctranno,ddate,dcutdate,cremarks)
Select A.ctype,A.ctranno,A.ddate,A.dcutdate, 'N'
From (
Select A.compcode, 'APV' as ctype,A.ctranno,B.ddate,A.dapvdate as dcutdate,'N'
From apv A left join 
	(
		Select ctranno, MAX(ddate) as ddate
		from logfile where cevent='POSTED' Group by ctranno
	) B on A.ctranno = B.ctranno
where lapproved=1 and lvoid=0

UNION ALL

Select A.compcode, 'SI' as ctype,A.ctranno,B.ddate,A.dcutdate,'N'
From sales A left join 
	(
		Select ctranno, MAX(ddate) as ddate
		from logfile where cevent='POSTED' Group by ctranno
	) B on A.ctranno = B.ctranno
where lapproved=1 and lvoid=0

UNION ALL

Select A.compcode, 'IN' as ctype,A.ctranno,B.ddate,A.dcutdate,'N'
From ntsales A left join 
	(
		Select ctranno, MAX(ddate) as ddate
		from logfile where cevent='POSTED' Group by ctranno
	) B on A.ctranno = B.ctranno
where lapproved=1 and lvoid=0

UNION ALL

Select A.compcode, 'PV' as ctype,A.ctranno,B.ddate,A.dcheckdate as dcutdate,'N'
From paybill A left join 
	(
		Select ctranno, MAX(ddate) as ddate
		from logfile where cevent='POSTED' Group by ctranno
	) B on A.ctranno = B.ctranno
where lapproved=1 and lvoid=0

UNION ALL

Select A.compcode, 'JE' as ctype,A.ctranno,B.ddate,A.djdate as dcutdate,'N'
From journal A left join 
	(
		Select ctranno, MAX(ddate) as ddate
		from logfile where cevent='POSTED' Group by ctranno
	) B on A.ctranno = B.ctranno
where lapproved=1 and lvoid=0

UNION ALL

Select A.compcode, 'OR' as ctype,A.ctranno,iFNULL(B.ddate, A.ddate),A.dcutdate,'N'
From receipt A left join 
	(
		Select ctranno, MAX(ddate) as ddate
		from logfile where cevent='POSTED' Group by ctranno
	) B on A.ctranno = B.ctranno
where lapproved=1 and lvoid=0
) A where A.compcode='002' order by A.ddate

	-->
