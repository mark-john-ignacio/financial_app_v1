<?php
    if(!isset($_SESSION)){
        session_start();
    }

    $_SESSION['pageid'] = "bir2307.php";
    include("../Connection/connection_string.php");
    include('../include/denied.php');
    include('../include/access.php');

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
        <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Myx Financials</title>
    </head>
    <body style="padding-left: 50px" onLoad="document.getElementById('txtcust').focus();">
        <center>
            <b><u><font size="+1">2307 BIR FORM Report</font></u></b>
        </center>
        <form method="post" type="post" name="frmrep" id="frmrep"  target="_blank">

            <table  width="100%" border="0" cellpadding="2">
                <tr>
                    <td class="col-sm-2 nopadwtop">
                            <button type="button" class="btn btn-danger btn-block " id="btnFind">
                                <span class="glyphicon glyphicon-search"></span> View Report
                            </button>
                    </td>
                    <td>
                        <div class="col-sm-3">
                            <b>Enter Supplier: </b>
                        </div>
                
                        <div class="col-xs-9 nopadwlefts">
                            <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">

                            <input type="hidden" id="txtcustid" name="txtcustid">
                            
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="col-sm-2 nopadwtop">
                                <button type="button" class="btn btn-success btn-block" id="btnexcel">
                                    <i class="fa fa-file-excel-o"></i> To Excel
                                </button>
                    </td>
                    <td >
                        <div class="col-xs-3">
                            <b>Transaction Type: </b>
                        </div>

                        <div class="col-xs-9 nopadwlefts">
                            <select class="form-control input-sm" id="trantype" name="trantype">
                                    <option value=""> All Transactions </option>
                                    <option value="1">Posted</option>
                                    <option value="0">Unposted</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="col-sm-2 nopadwtop">&nbsp;</td>
                    <td>
                        <div class="col-xs-3" style="vertical-align:bottom;">
                            <b>Date From: </b>
                        </div>
                        <div class="col-xs-3 nopadwlefts">
                                <input type='text' class="datepick form-control input-sm" id="datefrom" name="datefrom" />
                        </div>
                        <div class="col-xs-2 nopadding" style="vertical-align:bottom;">
                            <b>Date To: </b>
                        </div>
                        <div class="col-sm-3 nopadwlefts">
                                <input type='text' class="datepick form-control input-sm" id="dateto" name="dateto" />
                            
                        </div>
                    </td>
                </tr>
            </table>

            <div class="col-xs-12 nopadwtop">
                <table class="table" border="0"  cellspacing="2" width="100%" id="supplierTable" name="supplierTable">
                    <thead> 
                        <tr>
                            <tH> &nbsp;</tH>
                            <tH> Transaction No. </tH>
                            <tH> Supplier Name </tH>
                            <tH> Date </tH>
                            <tH> Status </tH>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            
            
        </form>
        <div class="d-flex justify-content-center col-sm-2" style="margin-left: 50%;" id="select" name="select">

            <button class="btn btn-warning btn-block" id="btnView" name="btnView" > view</button>

        </div>

        <form name="frmbir" id="frmbir" method="post" action="BIR/bir2307.php" target="_blank">
            <input type="hidden" name="txtdatefrom" id="txtdatefrom" />
            <input type="hidden" name="txtdateto" id="txtdateto" />
            <input type="hidden" name="txtctranno[]" id="txtctranno" />
        </form>	
    </body>
</html>

<script type="text/javascript">
    $(function(){
        $('.datepick').datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY-MM-DD'
        });

        $('#btnView').on('click', function(){
            var tranno = [];
            $(':checkbox:checked').each(function(i){
                tranno[i] = $(this).val();
                
            });
            if(tranno.length != 0){
                birform(tranno)
            } else {
                alert("No Reference")
            }
            
        })

        $('#btnFind').on('click', function(){
                    $.ajax({
                        url: "BIR/thPaybill.php",
                        dataType: "json",
                        async: false,
                        data: { 
                            name:  $('#txtcustid').val(),
                            trantype: $('#trantype').val(),
                            dateto: $('#dateto').val(),
                            datefrom: $('#datefrom').val()
                        },
                        success: function(data){
                            if(data[0].msg != "NO"){
                                
                                $('#supplierTable > tbody').html("");
                                $.each(data,function(index,{ctranno, supplier, date, approve}){

                                    if(ctranno != undefined){
                                        $("<tr id=\"paybill"+index+"\" style=\" padding: 10px;\">").append(
                                            $("<td>").html("<input type='checkbox' name='apvno' value='" + ctranno + "'/>"),
                                            $("<td>").text(ctranno), 
                                            $("<td>").text(supplier),
                                            $("<td>").text(formatDate(date)),
                                            $("<td>").text(approve)
                                        ).appendTo("#supplierTable tbody");
                                    }
                                        
                                })
                            } else {
                                alert("No Reference Found")
                            }
                            
                        },
                        error: function(data){
                            console.log(data)
                        }
                    })
            // $('#frmrep').attr("action", "Accounting/Monthly_IVAT.php");
            // $('#frmrep').submit();
            
        })

        $('#btnexcel').on("click", function(){
            console.log('clicked')
            // $('#frmrep').attr("action", "Accounting/Monthly_IVAT_xls.php");
            // $('#frmrep').submit();
        });
    })

    $(document).ready(function(){
        $('#supplierTable tbody').empty();
        
        $('#txtcust').typeahead({

            items: 10,
            source: function(request, response) {
				$.ajax({
					url: "BIR/thSupplier.php",
					dataType: "json",
					data: { 
						query: $("#txtcust").val(),
					},
					success: function (data) {
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function ({code, type, name}) {
				return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + type + ": </b>"+ code + '</span><br><small>' + name + "</small></div>";
			},
            highlighter: Object,
			afterSelect: function(item) { 
                const supplier = item.name;
                $('#txtcust').val(supplier).change(); 
                $('#txtcustid').val(item.code);
                
            }
        })
    })

    function birform(ctranno){

        // window.location.replace("BIR/bir2306.php?txtctranno='"+ctranno+"'",);
        $('#txtdatefrom').val($('#datefrom').val());
        $('#txtdateto').val($('#dateto').val());
        $('#txtctranno').val(ctranno);
        $('#frmbir').submit();
    }

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;

        return [year, month, day].join('-');
    }
</script>