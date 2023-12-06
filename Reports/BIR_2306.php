<?php
    if(!isset($_SESSION)){
        session_start();
    }

    $_SESSION['pageid'] = "bir2306.php";
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
            <b><u><font size="+1">2306 BIR FORM Report</font></u></b>
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
                        <div class="col-sm-3">
                            <b>Enter Supplier: </b>
                        </div>

                            <div class="col-xs-9">
                                <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
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
                                    <option> All Transactions </option>
                                    <option value="1">Posted</option>
                                    <option value="0">Unposted</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="0">&nbsp;</td>
                    <td>
                        <div class="col-xs-3" style="vertical-align:bottom;">
                            <b>Select a Year: </b>
                        </div>
                        <div class="col-xs-2 nopadwlefts">
                                <input type='text' class="datepick form-control input-sm" id="dateYear" name="dateYear" />
                            
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
    $(function(){
        $('.datepick').datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        });

        
        

        $('#btnView').on('click', function(){
            $('.datepick').on('change', function(){
                
                
            });
            $.ajax({
                url: 'BIR/thYearPB.php',
                dataType: 'json',
                data: {
                    name: $('#txtcust').val(),
                    year: $('#dateYear').val(),
                    trantype: $('#trantype').val()
                },
                success: function(data){
                    console.log(data[0].payee)
                    if(data[0].msg != "NO"){
                        var tranno = [];    
                        $.each(data,function(index,{ctranno, supplier, date, approve}){
                            tranno.push(ctranno)
                        })
                        $('#year').val($("#dateYear").val())
                        $('#txtctranno').val(tranno);
                        $('#frmbir').submit();
                    } else {
                        alert('No Reference Found');
                    }

                    
                },
                error: function(data){
                    console.log(data)
                }
            })
            
        })

    });

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

                $.ajax({
                    url: 'BIR/thYearPB.php',
                    dataType: 'json',
                    data: {
                        name: $('#txtcust').val(),
                        year: $('#dateYear').val(),
                        trantype: $('#trantype').val()
                    },
                    success: function(data){
                        var suplier = [];
                        data.map(({supplier}, key) => {
                            if(!supplier.includes(supplier)){
                                suplier.push(supplier)
                                console.log(suplier)
                            }
                        })
                        
                        
                    },
                    error: function(data){
                        console.log(data)
                    }
                })
                
            }
        })
    })

    function birform(ctranno){

        // window.location.replace("BIR/bir2306.php?txtctranno='"+ctranno+"'",);
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