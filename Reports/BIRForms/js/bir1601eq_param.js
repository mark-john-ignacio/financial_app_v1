
var sawt = [];
    $(document).ready(function(){

        $(".xcompute").autoNumeric('init',{mDec:2});
        $(".xcompute").on("click", function () {
            $(this).select();
        });

        $(".ichecks input").iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
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
        });

        $("#selfrmname").on("change", function(){
            $xc = $(this).find(':selected').attr('data-param')

            $('.birforms').each(function(i, obj) {
                if($(this).attr("id")==$xc){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            });
            
        });
        

        $("#btnView").on("click", function(){
            $("#frmBIRForm").attr("action", $("#selfrmname").val());
            $("#frmBIRForm").submit();
        });

        $(".xcompute").on("keyup", function(){   
            $TotalTaxesWithheld = $("#txt1601eq_totewt").val().replace(/,/g,'');

            $less1 = ($("#txt1601eq_less1").val()=="") ? 0 : $("#txt1601eq_less1").val().replace(/,/g,'');
            $less2 = ($("#txt1601eq_less2").val()=="") ? 0 : $("#txt1601eq_less2").val().replace(/,/g,'');
            $taxrmmited = ($("#txt1601eq_prev").val()=="") ? 0 : $("#txt1601eq_prev").val().replace(/,/g,'');
            $overremit = ($("#txt1601eq_overr").val()=="") ? 0 : $("#txt1601eq_overr").val().replace(/,/g,''); 
            $othrpay = ($("#txt1601eq_otrpay").val()=="") ? 0 : $("#txt1601eq_otrpay").val().replace(/,/g,'');

            $totrem = parseFloat($less1) + parseFloat($less2) + parseFloat($taxrmmited) + parseFloat($overremit) + parseFloat($othrpay);
            $("#txt1601eq_totrem").val($totrem);
            $("#txt1601eq_totrem").autoNumeric('destroy');
			$("#txt1601eq_totrem").autoNumeric('init',{mDec:2});


            $totsdue = parseFloat($TotalTaxesWithheld) - parseFloat($totrem);
            $("#txt1601eq_taxdue").val($totsdue);
            $("#txt1601eq_taxdue").autoNumeric('destroy');
			$("#txt1601eq_taxdue").autoNumeric('init',{mDec:2});


            $penaltysur = ($("#txt1601eq_pensur").val()=="") ? 0 : $("#txt1601eq_pensur").val().replace(/,/g,'');
            $penaltyint = ($("#txt1601eq_penint").val()=="") ? 0 : $("#txt1601eq_penint").val().replace(/,/g,'');
            $penaltycom = ($("#txt1601eq_pencom").val()=="") ? 0 : $("#txt1601eq_pencom").val().replace(/,/g,'');

            $totpenalty = parseFloat($penaltysur) + parseFloat($penaltyint) + parseFloat($penaltycom);
            $("#txt1601eq_pentot").val($totpenalty);
            $("#txt1601eq_pentot").autoNumeric('destroy');
			$("#txt1601eq_pentot").autoNumeric('init',{mDec:2});

            txt1601eq_gtot = $totsdue + $totpenalty;
            $("#txt1601eq_gtot").val(txt1601eq_gtot);
            $("#txt1601eq_gtot").autoNumeric('destroy');
			$("#txt1601eq_gtot").autoNumeric('init',{mDec:2});
        });
        
    })