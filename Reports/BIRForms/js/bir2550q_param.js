(function($) {
    'use strict';
    
    document.title = "BIR Form No. 2550Q";
    
        function getCleanedValue(selector) {
            const value = $(selector).val();
            // return (value === "" || value === undefined) ? 0 : value.replace(/,/g, '');
            return value ? parseFloat(value.replace(/,/g, '')) || 0 : 0;
        }

        // Function to calculate total penalties
        function calculateTotalPenalties() {
            const penaltySurcharge = getCleanedValue("#surcharge");
            const penaltyInterest = getCleanedValue("#interest");
            const penaltyCompromise = getCleanedValue("#compromise");

            const totalPenalties = penaltySurcharge + penaltyInterest + penaltyCompromise;

            $("#total_penalties").autoNumeric('set', totalPenalties);  
        }

        // Function to calculate Total Tax Credits/Payment
        function calculateTotalTaxCredits_Payment(){
            const creditable_vat_withhelding = getCleanedValue("#creditable_vat_withhelding");
            const advance_vat_payments = getCleanedValue("#advance_vat_payments");
            const txt2550q_18 = getCleanedValue("#txt2550q_18");
            const other_credits_payment = getCleanedValue("#other_credits_payment");

            const TotalTaxCredits_Payment = creditable_vat_withhelding + advance_vat_payments + txt2550q_18 + other_credits_payment;

            $("#total_tax_credits_payments").autoNumeric('set', TotalTaxCredits_Payment);
        }

        // Function to calculate tax still payable
        function calculateTaxStillPayable(){
            const net_vat_payable = getCleanedValue("#net_vat_payable");
            const total_tax_credits_payments = getCleanedValue("#total_tax_credits_payments");

            const tax_still_payable = net_vat_payable - total_tax_credits_payments;

            $("#tax_still_payable").autoNumeric('set', tax_still_payable);
        }

        // Function to calculate total amount payable
        function calculateTotalAmountPayable(){
            const tax_still_payable = getCleanedValue("#tax_still_payable");
            const total_penalties = getCleanedValue("#total_penalties");

            const total_amount_payable = tax_still_payable + total_penalties;

            $("#total_amount_payable").autoNumeric('set', total_amount_payable);
        }

         // Function to calculate total sales
         function calculateTotalSales(){
            const vatable_sales_A = getCleanedValue("#vatable_sales_A");
            const zero_rated_sales = getCleanedValue("#zero_rated_sales");
            const exempt_sales = getCleanedValue("#exempt_sales");

            const total_sales_output_tax_due_A = vatable_sales_A + zero_rated_sales + exempt_sales;

            $("#total_sales_output_tax_due_A").autoNumeric('set', total_sales_output_tax_due_A);
        }

        // Function to calculate output tax due
        function calculateOutputTaxDue(){
            const vatable_sales_B = getCleanedValue("#vatable_sales_B"); 

            $("#total_sales_output_tax_due_B").autoNumeric('set', vatable_sales_B);
        }

        // Function to calculate total adjusted output tax due
        function calculateTotalAdjustedOutputTaxDue(){
            const total_sales_output_tax_due_B = getCleanedValue("#total_sales_output_tax_due_B"); 
            const output_vat_on_uncollected_recievable = getCleanedValue("#output_vat_on_uncollected_recievable");
            const output_vat_on_recovered_uncollected_recievable = getCleanedValue("#output_vat_on_recovered_uncollected_recievable"); 

            const total_adjusted_output_tax_due = total_sales_output_tax_due_B - output_vat_on_uncollected_recievable + output_vat_on_recovered_uncollected_recievable;

            $("#total_adjusted_output_tax_due").autoNumeric('set', total_adjusted_output_tax_due);
        }

         // Function to calculate 43. total
         function calculateTotal_43(){
            const input_tax_carreid_over_from_previous_quarter = getCleanedValue("#input_tax_carreid_over_from_previous_quarter"); 
            const input_tax_deferred_on_capital_goods = getCleanedValue("#input_tax_deferred_on_capital_goods");
            const transitional_input_tax = getCleanedValue("#transitional_input_tax"); 
            const presumptive_input_tax = getCleanedValue("#presumptive_input_tax"); 
            const others_42_num = getCleanedValue("#others_42_num"); 

            const total_43 =  input_tax_carreid_over_from_previous_quarter + 
                            input_tax_deferred_on_capital_goods + 
                            transitional_input_tax + 
                            presumptive_input_tax + 
                            others_42_num;

            $("#total_43").autoNumeric('set', total_43);
        }
        
          // Function to calculate total current purchases/input tax A
          function calculateTotalCurrentPurchasesInputTax_A(){
            const domestic_purchases_A = getCleanedValue("#domestic_purchases_A"); 
            const services_rendered_by_non_resident_A = getCleanedValue("#services_rendered_by_non_resident_A");
            const importations_A = getCleanedValue("#importations_A"); 
            const others_47_A_num = getCleanedValue("#others_47_A_num"); 
            const domestic_purchases_with_no_input_tax = getCleanedValue("#domestic_purchases_with_no_input_tax"); 
            const vat_exempt_importations = getCleanedValue("#vat_exempt_importations"); 

            const total_current_purchases_input_tax_A =   domestic_purchases_A + 
                                                        services_rendered_by_non_resident_A + 
                                                        importations_A + 
                                                        others_47_A_num + 
                                                        domestic_purchases_with_no_input_tax +
                                                        vat_exempt_importations;

            $("#total_current_purchases_input_tax_A").autoNumeric('set', total_current_purchases_input_tax_A);
        }

         // Function to calculate total current purchases/input tax A
         function calculateTotalCurrentPurchasesInputTax_B(){
            const total_current_purchases_input_tax_B =   getCleanedValue("#domestic_purchases_B") + 
                                                        getCleanedValue("#services_rendered_by_non_resident_B") + 
                                                        getCleanedValue("#importations_B") + 
                                                        getCleanedValue("#others_47_B_num");

            $("#total_current_purchases_input_tax_B").autoNumeric('set', total_current_purchases_input_tax_B);
        }

        // Function to calculate Total Available Input Tax
        function calculateTotalAvailableInputTax(){
            const total_available_input_tax = getCleanedValue("#total_43") + 
                                            getCleanedValue("#total_current_purchases_input_tax_B");

            $("#total_available_input_tax").autoNumeric('set', total_available_input_tax);
        }

        // Function to calculate Total Deductions from Input Tax
        function calculateTotalDeductionsFromInputTax(){
            const total_deductions_from_input_tax = getCleanedValue("#input_tax_on_purchases") + 
                                                  getCleanedValue("#input_tax_attributable_to_vat_exempt_sales") + 
                                                  getCleanedValue("#vat_refund_tcc_claimed") + 
                                                  getCleanedValue("#input_vat_on_unpaid_payable") + 
                                                  getCleanedValue("#others_56_num");

            $("#total_deductions_from_input_tax").autoNumeric('set', total_deductions_from_input_tax);
        }

         // Function to calculate Adjusted Deductions from Input Tax 
         function calculateAdjustedDeductionsFromInputTax(){
            const adjusted_deductions_from_input_tax = getCleanedValue("#total_deductions_from_input_tax") + 
                                                     getCleanedValue("#input_vat_on_settled_unpaid_payables_previously_deducted"); 
                                    
            $("#adjusted_deductions_from_input_tax").autoNumeric('set', adjusted_deductions_from_input_tax);
        }
        
         // Function to calculate Total Allowable Input Tax
         function calculateTotalAllowableInputTax(){
            const total_allowable_input_tax = getCleanedValue("#total_available_input_tax") - 
                                            getCleanedValue("#adjusted_deductions_from_input_tax"); 
                                    
            $("#total_allowable_input_tax").autoNumeric('set', total_allowable_input_tax);
        }
        
        // Function to calculate Net VAT Payable/(Excess Input Tax)
        function calculateNetVATPayable(){
            const net_vat_payable_excess_input_tax = getCleanedValue("#total_adjusted_output_tax_due") - 
                                            getCleanedValue("#total_allowable_input_tax"); 
                                    
            $("#net_vat_payable_excess_input_tax").autoNumeric('set', net_vat_payable_excess_input_tax);
            $("#net_vat_payable").autoNumeric('set', net_vat_payable_excess_input_tax);
        }

        // Make the calcualteAll() function accessible to the bir2550q_param.php
        window.calculateAll = function() {
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
        }; 


        // function MonthYearPicker(){
        //     var currentYear = new Date().getFullYear();
        //     var nextYear = currentYear + 1;

        //     $(".yearpicker").datetimepicker({
        //         viewMode: 'years',
        //         format: 'YYYY',
        //         defaultDate: false,
        //         useCurrent: false,
        //         // minDate: moment(currentYear, 'YYYY'),
        //         maxDate: moment(nextYear, 'YYYY').endOf('year')
        //     });

        //     $(".monthpicker").datetimepicker({
        //         viewMode: 'months',
        //         format: 'MM',
        //         defaultDate: false,
        //         useCurrent: false
        //     })
        // }

          
        $(document).ready(function() {

            $(".xcompute").autoNumeric('init', { 
                mDec: 2, 
                vMin: '-999999999999.99', // Very low minimum value
                vMax: '999999999999.99'   // Very high maximum value
            });
        
            // $(".ichecks input").iCheck({
            //     checkboxClass: 'icheckbox_square-blue',
            //     radioClass: 'iradio_square-blue',
            //     increaseArea: '20%' // optional
            // });

            $(".xcompute").on("keyup", function() {   
                calculateAll();
            });

            // Trigger calculation on page load
            calculateAll();
            // MonthYearPicker()
        });
    
        document.addEventListener('DOMContentLoaded', function() {

            // default value
            var fromDate = '';
            var toDate = '';
        
            // Function to update year end and return period based on period type and quarter
            function updateFields() {
                var periodType = document.querySelector('input[name="txt2550q_accountingperiods"]:checked').value;
                var quarter = document.querySelector('input[name="txt2550q_qrtr"]:checked') ? document.querySelector('input[name="txt2550q_qrtr"]:checked').value : null;
                var now = new Date();
                // var year =  <?php echo json_encode($year);?>;
                // var year = periodType === 'C' ? now.getFullYear() : ''; // Use the current year for Calendar, empty for Fiscal
                //var month = periodType === 'C' ? '12' : ''; // Default to December for Calendar, empty for Fiscal
                 
        
                if (periodType === 'C') {
        
                    document.getElementById('txt2550q_year_end_M').value = '12';
                    document.getElementById('txt2550q_year_end_Y').value = year;
        
                    document.getElementById('txt2550q_year_end_M').readOnly = true;
                    document.getElementById('txt2550q_year_end_Y').readOnly = true;
        
                    switch (quarter) {
                        case '1':
                            fromDate = `01/01/${year}`;
                            toDate = `03/31/${year}`;
                            break;
                        case '2':
                            fromDate = `04/01/${year}`;
                            toDate = `06/30/${year}`;
                            break;
                        case '3':
                            fromDate = `07/01/${year}`;
                            toDate = `09/30/${year}`;
                            break;
                        case '4':
                            fromDate = `10/01/${year}`;
                            toDate = `12/31/${year}`;
                            break;
                    }
                } else if (periodType === 'F' && quarter) {
                    
                    // Calculate the adjusted month and year if the month exceeds 12
                    function adjustMonthAndYear(month, year) {
                        if (month > 12) {
                            // Calculate the number of years to add
                            var yearsToAdd = Math.floor((month - 1) / 12);
                            // Adjust month and year
                            month = ((month - 1) % 12) + 1;
                            year += yearsToAdd;
                        }
                        return { month, year };
                    }
        
                    // Get the last date of a given month
                    function getLastDateOfMonth(month, year) {
                        var adjusted = adjustMonthAndYear(month, year);
                        var adjMonth = adjusted.month;
                        var adjYear = adjusted.year;
        
                        // Month is 1-based (1 for January, 2 for February, etc.), so use month + 1 to get the next month
                        // Create a date object with the day set to 0 to get the last day of the previous month
                        var lastDay = new Date(adjYear, adjMonth, 0).getDate();
                        return lastDay;
                    }          
        
                    // var yearEndDB =  <?php echo json_encode($comprdo['fiscal_month_start_end']); ?>; //console.log(typeof yearEndDB);
                    var yearEndDBparts =  yearEndDB.split(/[-/]/);
                    // console.log('YearEnd:', yearEndDBparts);
        
                    var currentMonth = now.getMonth() + 1; //console.log('current month:', currentMonth);
                    // var currentYr = now.getFullYear();
                    // var yearEnd = <?php echo json_encode($year); ?>;
                    // var currentYr = 2023;
                    
                    var fiscalYearInt = parseInt(yearEnd);
                    var fiscalMonthInt = parseInt(yearEndDBparts[0]); console.log('fiscal month int:', fiscalMonthInt)
                    
                    if (currentMonth > fiscalMonthInt) {
                        // console.log('Current month is greater than or equal to the fiscal month.'); 
                         fiscalYearInt += 1;
                        // console.log('in the if statement:', fiscalYearInt);
                    } else {
                        // console.log('Current month is less than to the fiscal month.')
                    }
        
                    document.getElementById('txt2550q_year_end_M').value = yearEndDBparts[0];
                    document.getElementById('txt2550q_year_end_Y').value = fiscalYearInt;
        
                    if (fiscalMonthInt && fiscalYearInt) {
                        
                        switch (quarter) {
                            case '1':
        
                                var endMonth = fiscalMonthInt + 3;
                                var endYear = fiscalYearInt - 1;
                                var lastDay = getLastDateOfMonth(endMonth, endYear)
        
                                fromDate = `${fiscalMonthInt + 1}/01/${endYear }`;
                                toDate = `${endMonth}/${lastDay}/${endYear}`;
                                break;
        
                            case '2':
        
                                var endMonth = fiscalMonthInt + 6;
                                var endYear = fiscalYearInt - 1;
                                var lastDay = getLastDateOfMonth(endMonth, endYear);
                                
                                fromDate = `${fiscalMonthInt + 4}/01/${endYear}`;
                                toDate = `${endMonth}/${lastDay}/${endYear}`;
                                break;
        
                            case '3':
        
                                var endMonth = fiscalMonthInt + 9;
                                var endYear = fiscalYearInt - 1;
                                var lastDay = getLastDateOfMonth(endMonth, endYear)
        
                                fromDate = `${fiscalMonthInt + 7}/01/${endYear}`;
                                toDate = `${endMonth}/${lastDay}/${endYear}`;
                                break;
        
                            case '4':
        
                                var endMonth = fiscalMonthInt;
                                var endYear = fiscalYearInt - 1;
                                var lastDay = getLastDateOfMonth(endMonth, endYear)
        
                                fromDate = `${fiscalMonthInt + 10}/01/${endYear}`;
                                toDate = `${endMonth}/${lastDay}/${fiscalYearInt}`;
                                break;
                                
                            default:
                                fromDate = '';
                                toDate = '';
                                break;
                        }
        
                        // Adjust month and year if they exceed 12
                        fromDate = adjustDate(fromDate) ;
                        toDate = adjustDate(toDate);
        
                        // Function to adjust dates if months exceed 12
                        function adjustDate(dateString) {
                            var parts = dateString.split(/[-/]/);
                            var month = parseInt(parts[0]);
                            var day = parts[1];
                            var year = parseInt(parts[2]);
        
                            if (month > 12) {
                                month -= 12;
                                year += 1;
                            }
        
                            return `${month.toString().padStart(2, '0')}/${day}/${year}`;
                        }
        
                       
                    }
                }
        
                    document.getElementById('from').value = fromDate;
                    document.getElementById('to').value = toDate;
                    
                    // console.log("from date",fromDate);
                    // console.log("to date", toDate);
                    
                    $.ajax({
                        url: "./controllers/dateHandler.php", // Ensure this points to the correct PHP file
                        type: 'POST',
                        data: {
                            fromDate: fromDate,
                            toDate: toDate
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            try {
                                // Parse JSON response if not automatically parsed
                                var data = typeof response === 'string' ? JSON.parse(response) : response;
                                
                                console.log("Data sent fromDate:", fromDate);
                                console.log("Data sent toDate:", toDate);
                                console.log("Response data:", data);
                                
                                // Access response properties
                                console.log("Received fromDate:", data.receivedFromDate);
                                console.log("Received fromDate:", data.receivedToDate);
                                // console.log("Company: ", data.company)
                                // console.log("Message:", data.message);
                                console.log("Data:", data.data);
        
                                // //A. Sales for the Quarter (Exclusive of VAT)
                                // console.log("total VATable Sales A:", data.totalVATableSalesA);
                                // console.log("Zero Rated Sales:", data.totalZeroRatedSales);
                                // console.log("Exempt Sales:", data.totalExemptSales);
        
                                //  //B. Output Tax for the Quarter
                                // console.log("total VATable Sales B:", data.totalVATableSalesB);
        
                                //A. Sales for the Quarter (Exclusive of VAT)
                                // $('#vatable_sales_A').val(formatCurrency(data.totalVATableSalesA || 0));
                                // $('#zero_rated_sales').val(formatCurrency(data.totalZeroRatedSales || 0));
                                // $('#exempt_sales').val(formatCurrency(data.totalExemptSales || 0));
        
                                // B. Output Tax for the Quarter
                                // $('#vatable_sales_B').val(formatCurrency(data.totalVATableSalesB || 0));

        
                                calculateAll();
                                if (data.error) {
                                    console.error("Error:", data.error);
                                }
                            } catch (e) {
                                console.error('Error parsing JSON response:', e);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error sending data to the server:', error);
                        }
                    });
        
                   
            }
        
             // Define the formatCurrency function
            function formatCurrency(amount, locale = 'en-PH') {
                // Ensure amount is a number
                amount = parseFloat(amount);
        
                // Check if amount is valid
                if (isNaN(amount)) {
                    console.error('Invalid amount');
                    return '';
                }
        
                    // Format the amount without currency symbol
                return amount.toLocaleString(locale, {
                    style: 'decimal',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        
            // Attach event listeners to year-end inputs for Fiscal calendar
            // $('#txt2550q_year_end_M').on('dp.change input', updateFields);
            // $('#txt2550q_year_end_Y').on('dp.change input', updateFields);
        
            // Set initial values on page load
            updateFields();
        
        });
})(jQuery);
