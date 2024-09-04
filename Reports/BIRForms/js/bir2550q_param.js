(function($) {
    'use strict';
    
    // Inside this function, $ refers to jQuery
    $(document).ready(function() {
        function getCleanedValue(selector) {
            var value = $(selector).val();
            // return (value === "" || value === undefined) ? 0 : value.replace(/,/g, '');
            return value ? parseFloat(value.replace(/,/g, '')) || 0 : 0;
        }

        // Function to calculate total penalties
        function calculateTotalPenalties() {
            var penaltySurcharge = getCleanedValue("#surcharge");
            var penaltyInterest = getCleanedValue("#interest");
            var penaltyCompromise = getCleanedValue("#compromise");

            var totalPenalties = penaltySurcharge + penaltyInterest + penaltyCompromise;

            $("#total_penalties").autoNumeric('set', totalPenalties);  
        }

        // Function to calculate Total Tax Credits/Payment
        function calculateTotalTaxCredits_Payment(){
            var creditable_vat_withhelding = getCleanedValue("#creditable_vat_withhelding");
            var advance_vat_payments = getCleanedValue("#advance_vat_payments");
            var txt2550q_18 = getCleanedValue("#txt2550q_18");
            var other_credits_payment = getCleanedValue("#other_credits_payment");

            var TotalTaxCredits_Payment = creditable_vat_withhelding + advance_vat_payments + txt2550q_18 + other_credits_payment;

            $("#total_tax_credits_payments").autoNumeric('set', TotalTaxCredits_Payment);
        }

        // Function to calculate tax still payable
        function calculateTaxStillPayable(){
            var net_vat_payable = getCleanedValue("#net_vat_payable");
            var total_tax_credits_payments = getCleanedValue("#total_tax_credits_payments");

            var tax_still_payable = net_vat_payable - total_tax_credits_payments;

            $("#tax_still_payable").autoNumeric('set', tax_still_payable);
        }

        // Function to calculate total amount payable
        function calculateTotalAmountPayable(){
            var tax_still_payable = getCleanedValue("#tax_still_payable");
            var total_penalties = getCleanedValue("#total_penalties");

            var total_amount_payable = tax_still_payable + total_penalties;

            $("#total_amount_payable").autoNumeric('set', total_amount_payable);
        }

         // Function to calculate total sales
         function calculateTotalSales(){
            var vatable_sales_A = getCleanedValue("#vatable_sales_A");
            var zero_rated_sales = getCleanedValue("#zero_rated_sales");
            var exempt_sales = getCleanedValue("#exempt_sales");

            var total_sales_output_tax_due_A = vatable_sales_A + zero_rated_sales + exempt_sales;

            $("#total_sales_output_tax_due_A").autoNumeric('set', total_sales_output_tax_due_A);
        }

        // Function to calculate output tax due
        function calculateOutputTaxDue(){
            var vatable_sales_B = getCleanedValue("#vatable_sales_B"); 

            $("#total_sales_output_tax_due_B").autoNumeric('set', vatable_sales_B);
        }

        // Function to calculate total adjusted output tax due
        function calculateTotalAdjustedOutputTaxDue(){
            var total_sales_output_tax_due_B = getCleanedValue("#total_sales_output_tax_due_B"); 
            var output_vat_on_uncollected_recievable = getCleanedValue("#output_vat_on_uncollected_recievable");
            var output_vat_on_recovered_uncollected_recievable = getCleanedValue("#output_vat_on_recovered_uncollected_recievable"); 

            var total_adjusted_output_tax_due = total_sales_output_tax_due_B - output_vat_on_uncollected_recievable + output_vat_on_recovered_uncollected_recievable;

            $("#total_adjusted_output_tax_due").autoNumeric('set', total_adjusted_output_tax_due);
        }

         // Function to calculate 43. total
         function calculateTotal_43(){
            var input_tax_carreid_over_from_previous_quarter = getCleanedValue("#input_tax_carreid_over_from_previous_quarter"); 
            var input_tax_deferred_on_capital_goods = getCleanedValue("#input_tax_deferred_on_capital_goods");
            var transitional_input_tax = getCleanedValue("#transitional_input_tax"); 
            var presumptive_input_tax = getCleanedValue("#presumptive_input_tax"); 
            var others_42_num = getCleanedValue("#others_42_num"); 

            var total_43 =  input_tax_carreid_over_from_previous_quarter + 
                            input_tax_deferred_on_capital_goods + 
                            transitional_input_tax + 
                            presumptive_input_tax + 
                            others_42_num;

            $("#total_43").autoNumeric('set', total_43);
        }
        
          // Function to calculate total current purchases/input tax A
          function calculateTotalCurrentPurchasesInputTax_A(){
            var domestic_purchases_A = getCleanedValue("#domestic_purchases_A"); 
            var services_rendered_by_non_resident_A = getCleanedValue("#services_rendered_by_non_resident_A");
            var importations_A = getCleanedValue("#importations_A"); 
            var others_47_A_num = getCleanedValue("#others_47_A_num"); 
            var domestic_purchases_with_no_input_tax = getCleanedValue("#domestic_purchases_with_no_input_tax"); 
            var vat_exempt_importations = getCleanedValue("#vat_exempt_importations"); 

            var total_current_purchases_input_tax_A =   domestic_purchases_A + 
                                                        services_rendered_by_non_resident_A + 
                                                        importations_A + 
                                                        others_47_A_num + 
                                                        domestic_purchases_with_no_input_tax +
                                                        vat_exempt_importations;

            $("#total_current_purchases_input_tax_A").autoNumeric('set', total_current_purchases_input_tax_A);
        }

         // Function to calculate total current purchases/input tax A
         function calculateTotalCurrentPurchasesInputTax_B(){
            var total_current_purchases_input_tax_B =   getCleanedValue("#domestic_purchases_B") + 
                                                        getCleanedValue("#services_rendered_by_non_resident_B") + 
                                                        getCleanedValue("#importations_B") + 
                                                        getCleanedValue("#others_47_B_num");

            $("#total_current_purchases_input_tax_B").autoNumeric('set', total_current_purchases_input_tax_B);
        }

        // Function to calculate Total Available Input Tax
        function calculateTotalAvailableInputTax(){
            var total_available_input_tax = getCleanedValue("#total_43") + 
                                            getCleanedValue("#total_current_purchases_input_tax_B");

            $("#total_available_input_tax").autoNumeric('set', total_available_input_tax);
        }

        // Function to calculate Total Deductions from Input Tax
        function calculateTotalDeductionsFromInputTax(){
            var total_deductions_from_input_tax = getCleanedValue("#input_tax_on_purchases") + 
                                                  getCleanedValue("#input_tax_attributable_to_vat_exempt_sales") + 
                                                  getCleanedValue("#vat_refund_tcc_claimed") + 
                                                  getCleanedValue("#input_vat_on_unpaid_payable") + 
                                                  getCleanedValue("#others_56_num");

            $("#total_deductions_from_input_tax").autoNumeric('set', total_deductions_from_input_tax);
        }

         // Function to calculate Adjusted Deductions from Input Tax 
         function calculateAdjustedDeductionsFromInputTax(){
            var adjusted_deductions_from_input_tax = getCleanedValue("#total_deductions_from_input_tax") + 
                                                     getCleanedValue("#input_vat_on_settled_unpaid_payables_previously_deducted"); 
                                    
            $("#adjusted_deductions_from_input_tax").autoNumeric('set', adjusted_deductions_from_input_tax);
        }
        
         // Function to calculate Total Allowable Input Tax
         function calculateTotalAllowableInputTax(){
            var total_allowable_input_tax = getCleanedValue("#total_available_input_tax") - 
                                            getCleanedValue("#adjusted_deductions_from_input_tax"); 
                                    
            $("#total_allowable_input_tax").autoNumeric('set', total_allowable_input_tax);
        }
        
        // Function to calculate Net VAT Payable/(Excess Input Tax)
        function calculateNetVATPayable(){
            var net_vat_payable_excess_input_tax = getCleanedValue("#total_adjusted_output_tax_due") - 
                                            getCleanedValue("#total_allowable_input_tax"); 
                                    
            $("#net_vat_payable_excess_input_tax").autoNumeric('set', net_vat_payable_excess_input_tax);
            $("#net_vat_payable").autoNumeric('set', net_vat_payable_excess_input_tax);
        }

        function calculateAll() {
            calculateTotalPenalties();
            calculateTotalTaxCredits_Payment();   
            calculateTaxStillPayable();
            calculateTotalAmountPayable();
            calculateTotalSales();
            calculateOutputTaxDue();
            calculateTotalAdjustedOutputTaxDue();
            calculateTotal_43();
            calculateTotalCurrentPurchasesInputTax_A();
            calculateTotalCurrentPurchasesInputTax_B();
            calculateTotalAvailableInputTax();
            calculateTotalDeductionsFromInputTax();
            calculateAdjustedDeductionsFromInputTax();
            calculateTotalAllowableInputTax();
            calculateNetVATPayable();
        }

        function MonthYearPicker(){
            var currentYear = new Date().getFullYear();
            var nextYear = currentYear + 1;

            $(".yearpicker").datetimepicker({
                viewMode: 'years',
                format: 'YYYY',
                defaultDate: false,
                useCurrent: false,
                // minDate: moment(currentYear, 'YYYY'),
                maxDate: moment(nextYear, 'YYYY').endOf('year')
            });

            $(".monthpicker").datetimepicker({
                viewMode: 'months',
                format: 'MM',
                defaultDate: false,
                useCurrent: false
            })
        }

          
        $(document).ready(function() {

            $(".xcompute").autoNumeric('init', { 
                mDec: 2, 
                vMin: '-9999999999999999999999999999.99', // Very low minimum value
                vMax: '9999999999999999999999999999.99'   // Very high maximum value
            });

            $(".ichecks input").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $(".xcompute").on("keyup", function() {   
                calculateAll();
            });

            // Trigger calculation on page load
            calculateAll();
            MonthYearPicker()
        });
    });
})(jQuery);
