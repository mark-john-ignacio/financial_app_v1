<?php
    if(!isset($_SESSION)){
        session_start();
    }

    $_SESSION['pageid'] = "BIR_Forms";
    include("../Connection/connection_string.php");
    include('../include/denied.php');
   // include('../include/access.php');
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../global/plugins/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?<?php echo time();?>">
        <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

        <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
        <script src="../js/bootstrap3-typeahead.min.js"></script>
        <script src="../include/autoNumeric.js"></script>

        <script src="../Bootstrap/js/bootstrap.js"></script>
        <script src="../Bootstrap/js/moment.js"></script>
        <script src="../Bootstrap/js/bootstrap-datetimepicker.js?x=<?php echo time();?>"></script>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Myx Financials</title>
    </head>
    <body style="padding-left: 50px" onLoad="document.getElementById('txtcust').focus();">
        <center>
            <b><u><font size="+1">BIR Forms</font></u></b>
        </center>
        <form method="post" type="post" name="frmrep" id="frmrep" target="_blank">

            <table width="100%" border="0" cellpadding="2">
                <tr>
                    <td class="col-sm-2 nopadwtop">
                            <button type="button" class="btn btn-danger btn-block " id="btnView">
                                <span class="glyphicon glyphicon-search"></span> View Report
                            </button>
                    </td>
                    <td>
                        <div class="col-sm-2" style="padding-top: 7px">
                            <b>BIR Form: </b>
                        </div>

                        <div class="col-xs-9 nopadwleft">
                            <select class="form-control input-sm" id="trantype" name="trantype">
                                <option value="bir2550M" data-type="Monthly">2550M - Monthly Value-Added Tax Declaration</option>
                                <option value="bir2550Q" data-type="Quarterly">2550Q - Quarterly Value-Added Tax Return</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="col-sm-2 nopadwtop">
                       &nbsp;
                    </td>
                    <td>
                        <div class="col-xs-2" style="padding-top: 7px">
                            <b>Select Range: </b>
                        </div>
                        <div class="col-xs-2 nopadwleft">
                            <input type='text' class="form-control input-sm" id="dateYear" name="dateYear" />                           
                        </div>
                        <div class="col-xs-2 nopadwleft" id="divmonths">
                            <input type='text' class="form-control input-sm" id="dateMonth" name="dateMonth" />                           
                        </div>
                        <div class="col-xs-3 nopadwleft" id="divquarts"> 
                            <?php
                                $month = date('m');
                                $first = array(1,2,3);
                                $second = array(4,5,6);
                                $third = array(7,8,9);
                                $fourth = array(10,11,12);
                            ?>
                            <select class="form-control input-sm" id="trantype" name="trantype">
                                <option value="1" <?=(in_array($month, $first)) ? "selected" : ""?>>1st Quarter</option>
                                <option value="2" <?=(in_array($month, $second)) ? "selected" : ""?>>2nd Quarter</option>
                                <option value="3" <?=(in_array($month, $third)) ? "selected" : ""?>>3rd Quarter</option>
                                <option value="4" <?=(in_array($month, $fourth)) ? "selected" : ""?>>4th Quarter</option>
                            </select>
                        </div>
                    </td>
                </tr>

            </table>
        </form>

        <form name="frmbir" id="frmbir" method="post" action="BIR/bir2306.php" target="_blank">
            <input type="hidden" name="year" id="year" /> 
            <input type="hidden" name="txtctranno[]" id="txtctranno" />
        </form>
    </body>
</html>

<script type="text/javascript">
    $(document).ready(function(){

        $("#divmonths").show();
        $("#divquarts").hide();

        $('#dateYear').datetimepicker({
            defaultDate: moment(),
            format: "YYYY",
            viewMode: "years"
        }); 

        $('#dateMonth').datetimepicker({
            defaultDate: moment(),
            format: "MM",
            viewMode: "months",
        });
        
        $("#trantype").on("change", function(){
            $getd = $(this).find(':selected').data('type');

            if($getd=="Monthly"){  
                $("#divmonths").show();
                $("#divquarts").hide();
            }else if($getd=="Quarterly"){
                $("#divmonths").hide();
                $("#divquarts").show();
            }
        }); 
        
        $('#btnView').on("click", function(){
            $xurl = $("#trantype").val()+".php";
            $("#frmrep").attr("action", $xurl);
            $("#frmrep").submit();
        });
    });

</script>