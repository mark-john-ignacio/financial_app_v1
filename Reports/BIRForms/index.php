<?php 
    if(!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['pageid'] = "BIRForms";

    include("../../Connection/connection_string.php");
    include('../../include/denied.php');
    include('../../include/access.php');

    $company = $_SESSION['companyid'];

    $sql = "select * From company where compcode='$company'";
    $result=mysqli_query($con,$sql);
    
        if (!mysqli_query($con, $sql)) {
            printf("Errormessage: %s\n", mysqli_error($con));
        } 
        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $comprdo = $row['comprdo'];
    }

    @$rdocodes = array();
    $sqlhead=mysqli_query($con,"Select * from rdocodes");
    if (mysqli_num_rows($sqlhead)!=0) {
        while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
            @$rdocodes[] = array("ccode" => $row['ccode'], "cdesc" => $row['cdesc']); 
        }
    }

    @$formnames = array();
    $sqlhead=mysqli_query($con,"Select A.id, C.`year`, B.* 
    from bir_year_form_registration A 
    left join nav_menu_forms B on A.form_id=B.id
    left join bir_year C on A.year_id=C.id 
    Where A.compcode='$company' and B.cstatus='Active'
    Order By C.`year`, B.form_code");
    if (mysqli_num_rows($sqlhead)!=0) {
        while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
            @$formnames[] = $row; 
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="../../include/select2/select2.min.css">

    <link rel="stylesheet" type="text/css" href="../../global/plugins/icheck/skins/minimal/_all.css">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/select2/select2.full.min.js"></script>

    <script src="../../global/plugins/icheck/icheck.min.js"></script>

    <script src="../../include/autoNumeric.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <style>
        th, td {
            padding-top: 2px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 2px;
        }

    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>
        <div style="text-align: center; font-weight: bold; text-decoration: underline;">
            <font size="+1">BIR FORMS</font>
        </div>
        <div class='container' style='padding-top: 50px'>
            <form action="" method="post" id="frmBIRForm" target="_blank">
                <table border="0" class="table table-sm table-borderless" style="border: 0 !important; padding: 0px !important">
                    <tr valign="top">
                        <th width="100px"><button type="button" class='btn btn-danger btn-block' id="btnView" value="VIEW"><i class='fa fa-search'></i>&nbsp;&nbsp;Review Form</button></th>
                        <th width="150px">
                            <div class="input-group nopadding">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" id='years' name='years' class='yearpicker form-control input-sm' value="<?= date("Y"); ?>">
                            </div>                       
                        </th>
                        <th width="150px">
                            <select class="form-control input-sm" name="selfil" id="selfil"> 
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annually">Annually</option>
                            </select>
                        </th>
                        <th>
                        
                            <div id="divqr" style="display: none">
                                <?php
                                    $curMonth = date("m", time());
                                    $curQuarter = ceil($curMonth/3);
                                ?>
                                <select class="form-control input-sm" name="selqrtr" id="selqrtr">
                                    <option value="1"<?=($curQuarter==1) ? " selected": ""?>>1st Quarter</option>
                                    <option value="2"<?=($curQuarter==2) ? " selected": ""?>>2nd Quarter</option>
                                    <option value="3"<?=($curQuarter==3) ? " selected": ""?>>3rd Quarter</option>
                                    <option value="4"<?=($curQuarter==4) ? " selected": ""?>>4th Quarter</option>
                                </select>
                            </div>
                            <div id="divmn">
                                <?php
                                    $curMonth = intval(date("m", time()));
                                ?>
                                <select class="form-control input-sm" name="selmonth" id="selmonth">
                                    <?php
                                        for($i=1; $i<=12; $i++){
                                            $dateObj   = DateTime::createFromFormat('!m', $i);
                                            $monthName = $dateObj->format('F');
                                    ?>
                                        <option value="<?=$i?>"<?=($curMonth==$i) ? " selected": ""?>><?=$monthName?></option>
            	                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </th>
                        
                    </tr>
                    <tr valign="top">
                        <th width="100px">&nbsp;</th>
                        <th colspan="3">
                            <select class="form-control input-sm" name="selfrmname" id="selfrmname">
                                <option></option>
                                
                            </select>
                        </th>
                    </tr>
                </table>
            </form>
        </div>
</body>
</html>

<script type="text/javascript">
    var sawt = [];
    $(document).ready(function(){

        loadforms();

        $(".icheckbox").iCheck({
            checkboxClass: 'icheckbox_minimal',
            increaseArea: '20%' // optional
        });
       // $(".birforms").hide();

        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        })

        $("#selfrmname").select2({
            placeholder: "Please select a form"
        }); 

        $("#selfil").on("change", function(){
            $x = $(this).val();
            if($x=="Monthly"){
                $("#divqr").hide();
                $("#divmn").show();
            }else if($x=="Quarterly"){
                $("#divqr").show();
                $("#divmn").hide();
            }else if($x=="Annually"){
                $("#divqr").hide();
                $("#divmn").hide();
            }

            loadforms();
        });
        

        $("#btnView").on("click", function(){
            $xc = $("#selfrmname").find(':selected').attr('data-param')

            $("#frmBIRForm").attr("action", $xc+".php");
            $("#frmBIRForm").submit();
        });
        
    });

    function loadforms(){
        $('#selfrmname').find('option').not(':first').remove();

        $xc = '<?=json_encode($formnames)?>';  
        $yr = $("#years").val();
        $mo = $("#selfil").val();

        $.each(jQuery.parseJSON($xc), function() {  
            if(this['year']==$yr && $mo==this['filter']){
                $('<option>').val(this['form_link']).attr('data-param',this['params']).text(this['form_code'] + " - "+this['form_name']).appendTo('#selfrmname');
            }
        });
    }

</script>