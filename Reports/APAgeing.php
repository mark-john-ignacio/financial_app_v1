<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "APAgeing";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

?><html>
<head>

  <link rel="stylesheet" type="text/css" href="../global/plugins/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Myx Financials</title>
</head>

<body style="padding-left:50px;">
<center>
<b><u><font size="+1">AP Ageing Report</font></u></b>

</center>
<br>
<form action="Accounting/Monthly_IVAT.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td valign="top" width="50" style="padding:2px">
      <button type="button" class="btn btn-danger btn-block" id="btnView">
        <span class="glyphicon glyphicon-search"></span> View Report
      </button>
    </td>
    <td style="padding-left:10px" width="150"><b>SI Date as Of: </b></td>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding">
        <div class="col-xs-5 nopadding">

		      <input type='text' class="datepick form-control input-sm" id="date1" name="date1" />

		    </div>
        <div class="col-xs-5 nopadwleft">
<!--
          <select class="form-control input-sm" id="selstat" name="selstat">
            <option value="">ALL Transactions</options>
            <option value="1">Posted</options>
            <option value="0">Unposted</options>
          </select>
-->
        </div>
     </div>   
    </td>
  </tr>
  <tr>
    <td valign="top" width="50" style="padding:2px">
    <button type="button" class="btn btn-success btn-block" id="btnexcel">
        <i class="fa fa-file-excel-o"></i> To Excel
      </button>
    </td>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
</form>

<br>

<div class="row">
  <div class="col-xs-6 col-xs-offset-3">

    <form action="th_agesave.php" method="post" name="frmage" id="frmage">
      <input type="hidden" name="hndtyp" id="hndtyp" value="AP">
      <input type="hidden" name="hdncnts" id="hdncnts" value="">
      <h4>Ageing Periods</h4>
        <div class="col-xs-12 nopadding">
          <div class="col-xs-6 nopadwright">
            <button type="button" class="btn btn-info btn-xs btn-block" tabindex="6" onClick="insrow();" id="btnIns" name="btnIns">
              Add Period
            </button>
          </div>

          <div class="col-xs-6 nopadwleft">
            <button type="button" class="btn btn-success btn-xs btn-block" tabindex="6" id="btnsave" name="btnsave">
              Save Settings
            </button>
          </div>
        </div>
          <br><br>
          <table width="80%" class="table table-stripped table-sm" id="myagdays">
            <tr>
              <th>Desc</th>
              <th width="80px">From</th>
              <th width="80px">To</th>
              <th width="50px">&nbsp;</th>
            </tr>
            <?php
            $sql = "select * from ageing_days where compcode='$company' and cagetype='AP' order by id";
            $result=mysqli_query($con,$sql);
            $cntr = 0;
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                  $cntr++;
            ?>
              <tr>
                <td>
                  <input type='text' class="form-control input-xs" id="cdesc<?=$cntr?>" name="cdesc" value="<?=$row['cdesc']?>" autocomplete="off"/>
                </td>
                <td>
                  <input type='number' class="form-control input-xs" id="dfrom<?=$cntr?>" name="dfrom" value="<?=$row['fromdays']?>" autocomplete="off"/>
                </td>
                <td>
                  <input type='number' class="form-control input-xs" id="dto<?=$cntr?>" name="dto" value="<?=$row['todays']?>" autocomplete="off"/>
                </td>
                <td align="center">

                  <button type="button" class="btn btn-danger btn-sm" tabindex="6" id="btndel<?=$cntr?>" name="btndel">
                    <i class="fa fa-trash"></i>
                  </button>
                  <script type="text/javascript">
                    $("#btndel<?=$cntr?>").on('click', function() {
                      $(this).closest('tr').remove();
                    });
                  </script>
                </td>
              </tr>
            <?php
                }
            ?>
          </table>
    </form>

  </div>
</div>
</body>
</html>

<script type="text/javascript">
$(document).ready(function(e) {	            
    // Bootstrap DateTimePicker v4
	  $('.datepick').datetimepicker({
      defaultDate: moment(),
      format: 'MM/DD/YYYY'
    });

    $('#btnView').on("click", function(){
        $('#frmrep').attr("action", "Accounting/APAgeing.php");
        $('#frmrep').submit();
    });

    $('#btnexcel').on("click", function(){
        $('#frmrep').attr("action", "Accounting/APAgeing_xls.php");
        $('#frmrep').submit();
    });

    $("#btnsave").on("click", function(){
      var tx3 = 0;
				$("#myagdays > tbody > tr").each(function(index) {       
								
					tx3 = index;
					$(this).find('input[name="cdesc"]').attr("name","cdesc"+tx3);
					$(this).find('input[name="dfrom"]').attr("name","dfrom"+tx3);
					$(this).find('input[name="dto"]').attr("name","dto" + tx3);
				});

        var tbl4 = document.getElementById('myagdays').getElementsByTagName('tr');
		    var lastRow3= tbl4.length-1;

        document.getElementById("hdncnts").value = lastRow3;

        $("#frmage").submit();
    });
	   
});

/*
function delrow(did){
  if (confirm("Are you sure you want to delete this row?") == true) {
    text = "You pressed OK!";

      $.ajax({
        url : "th_delageing.php",
        type: "Post",
        data: 'n_id='+ did,
        dataType: "text",
        success: function(data)
        {	
          if(data.trim()=="True"){
            window.location.href="APAgeing.php";
          }else{
            alert(data);
          }
        }
      });

  }

}
*/

function insrow(){

  var tbl = document.getElementById('myagdays').getElementsByTagName('tr');
	var lastRow = tbl.length;

  tdesc = "<td><input type='text' class='form-control input-xs' id='cdesc"+lastRow+"' name='cdesc' value='' autocomplete='off'/></td>";
  tfrom = "<td><input type='number' class='form-control input-xs' id='dfrom"+lastRow+"' name='dfrom' value='' autocomplete='off'/></td>";
  tto = "<td><input type='number' class='form-control input-xs' id='dto"+lastRow+"' name='dto' value='' autocomplete='off'/></td>";
  tdel = "<td align='center'><button type='button' class='btn btn-danger btn-sm' tabindex='6' id='btndel"+lastRow+"' name='btndel'><i class='fa fa-trash'></i></button></td>";

  $('#myagdays > tbody:last-child').append('<tr>'+ tdesc + tfrom + tto + tdel + '</tr>');

      $("#btndel"+lastRow).on('click', function() {
        $(this).closest('tr').remove();
      });
}

</script>
