(function($) {
    'use strict';

    $(document).ready(function() {
        document.title = "BIR Form No. 2550M";

        // AutoNumeric configuration
        const autoNumericOptions = {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 2,
            minimumValue: -99999999999.99,
            maximumValue: 99999999999.99,
            allowDecimalPadding: false,
            watchExternalChanges: true
        };

        // Initialize AutoNumeric for all input fields
        const fields = [
            'part2_12a', 'part2_12b', 'part2_13a', 'part2_13b', 'part2_14', 'part2_15',
            'part2_16a', 'part2_16b', 'part2_17a', 'part2_17b', 'part2_17c', 'part2_17d',
            'part2_17e', 'part2_17f', 'part2_18a', 'part2_18b', 'part2_18c', 'part2_18d',
            'part2_18e', 'part2_18f', 'part2_18g', 'part2_18h', 'part2_18i', 'part2_18j',
            'part2_18k', 'part2_18l', 'part2_18m', 'part2_18n', 'part2_18o', 'part2_18p',
            'part2_19', 'part2_20a', 'part2_20b', 'part2_20c', 'part2_20d', 'part2_20e',
            'part2_20f', 'part2_21', 'part2_22', 'part2_23a', 'part2_23b', 'part2_23c',
            'part2_23d', 'part2_23e', 'part2_23f', 'part2_23g', 'part2_24', 'part2_25a',
            'part2_25b', 'part2_25c', 'part2_25d', 'part2_26'
        ];

        const autoNumericInstances = {};

        fields.forEach(field => {
            autoNumericInstances[field] = new AutoNumeric(`#${field}`, autoNumericOptions);
        });

        // Event Listeners
        allFunctions();
        getTotalSales();
        $('.xcompute').on('input', function() {
            allFunctions();
        });

        // Functions
        function getNumericValue(field) {
            return autoNumericInstances[field].getNumber() || 0;
        }

        function calculate16a() {
            const part2_12a = getNumericValue('part2_12a');
            const part2_13a = getNumericValue('part2_13a');
            const part2_14 = getNumericValue('part2_14');
            const part2_15 = getNumericValue('part2_15');
            const calculated_16a = part2_12a + part2_13a + part2_14 + part2_15;
            autoNumericInstances['part2_16a'].set(calculated_16a);
        }

        function calculate16b() {
            const part2_12b = getNumericValue('part2_12b');
            const part2_13b = getNumericValue('part2_13b');
            const calculated_16b = part2_12b + part2_13b;
            autoNumericInstances['part2_16b'].set(calculated_16b);
        }

        function calculate17f() {
            const part2_17a = getNumericValue('part2_17a');
            const part2_17b = getNumericValue('part2_17b');
            const part2_17c = getNumericValue('part2_17c');
            const part2_17d = getNumericValue('part2_17d');
            const part2_17e = getNumericValue('part2_17e');
            const calculated_17f = part2_17a + part2_17b + part2_17c + part2_17d + part2_17e;
            autoNumericInstances['part2_17f'].set(calculated_17f);
        }

        function calculate18p() {
            const part2_18a = getNumericValue('part2_18a');
            const part2_18c = getNumericValue('part2_18c');
            const part2_18e = getNumericValue('part2_18e');
            const part2_18g = getNumericValue('part2_18g');
            const part2_18i = getNumericValue('part2_18i');
            const part2_18k = getNumericValue('part2_18k');
            const part2_18m = getNumericValue('part2_18m');
            const part2_18n = getNumericValue('part2_18n');
            const calculated_18p = part2_18a + part2_18c + part2_18e + part2_18g + part2_18i + part2_18k + part2_18m + part2_18n;
            autoNumericInstances['part2_18p'].set(calculated_18p);
        }

        function calculate19() {
            const part2_17f = getNumericValue('part2_17f');
            const part2_18b = getNumericValue('part2_18b');
            const part2_18d = getNumericValue('part2_18d');
            const part2_18f = getNumericValue('part2_18f');
            const part2_18h = getNumericValue('part2_18h');
            const part2_18j = getNumericValue('part2_18j');
            const part2_18l = getNumericValue('part2_18l');
            const part2_18o = getNumericValue('part2_18o');
            const calculated_19 = part2_17f + part2_18b + part2_18d + part2_18f + part2_18h + part2_18j + part2_18l + part2_18o;
            autoNumericInstances['part2_19'].set(calculated_19);
        }

        function calculate20f() {
            const part2_20a = getNumericValue('part2_20a');
            const part2_20b = getNumericValue('part2_20b');
            const part2_20c = getNumericValue('part2_20c');
            const part2_20d = getNumericValue('part2_20d');
            const part2_20e = getNumericValue('part2_20e');
            const calculated_20f = part2_20a + part2_20b + part2_20c + part2_20d + part2_20e;
            autoNumericInstances['part2_20f'].set(calculated_20f);
        }

        function calculate21() {
            const part2_19 = getNumericValue('part2_19');
            const part2_20f = getNumericValue('part2_20f');
            const calculated_21 = part2_19 - part2_20f;
            autoNumericInstances['part2_21'].set(calculated_21);
        }

        function calculate22() {
            const part2_16b = getNumericValue('part2_16b');
            const part2_21 = getNumericValue('part2_21');
            const calculated_22 = part2_16b - part2_21;
            autoNumericInstances['part2_22'].set(calculated_22);
        }

        function calculate23g() {
            const part2_23a = getNumericValue('part2_23a');
            const part2_23b = getNumericValue('part2_23b');
            const part2_23c = getNumericValue('part2_23c');
            const part2_23d = getNumericValue('part2_23d');
            const part2_23e = getNumericValue('part2_23e');
            const part2_23f = getNumericValue('part2_23f');
            const calculated_23g = part2_23a + part2_23b + part2_23c + part2_23d + part2_23e + part2_23f;
            autoNumericInstances['part2_23g'].set(calculated_23g);
        }

        function calculate24() {
            const part2_22 = getNumericValue('part2_22');
            const part2_23g = getNumericValue('part2_23g');
            const calculated_24 = part2_22 - part2_23g;
            autoNumericInstances['part2_24'].set(calculated_24);
        }

        function calculate25d() {
            const part2_25a = getNumericValue('part2_25a');
            const part2_25b = getNumericValue('part2_25b');
            const part2_25c = getNumericValue('part2_25c');
            const calculated_25d = part2_25a + part2_25b + part2_25c;
            autoNumericInstances['part2_25d'].set(calculated_25d);
        }

        function calculate26() {
            const part2_24 = getNumericValue('part2_24');
            const part2_25d = getNumericValue('part2_25d');
            const calculated_26 = part2_24 + part2_25d;
            autoNumericInstances['part2_26'].set(calculated_26);
        }

        function calculateOutputTax() {
            const taxPercent = 12;
            const taxDecimal = taxPercent / 100;

            const part2_12a = getNumericValue('part2_12a');
            const part2_13a = getNumericValue('part2_13a');
            const part2_18a = getNumericValue('part2_18a');
            const part2_18c = getNumericValue('part2_18c');
            const part2_18e = getNumericValue('part2_18e');
            const part2_18g = getNumericValue('part2_18g');
            const part2_18i = getNumericValue('part2_18i');
            const part2_18k = getNumericValue('part2_18k');
            const part2_18n = getNumericValue('part2_18n');

            const calculated_12b = part2_12a * taxDecimal;
            const calculated_13b = part2_13a * taxDecimal;
            const calculated_18b = part2_18a * taxDecimal;
            const calculated_18d = part2_18c * taxDecimal;
            const calculated_18f = part2_18e * taxDecimal;
            const calculated_18h = part2_18g * taxDecimal;
            const calculated_18j = part2_18i * taxDecimal;
            const calculated_18l = part2_18k * taxDecimal;
            const calculated_18o = part2_18n * taxDecimal;

            autoNumericInstances['part2_12b'].set(calculated_12b);
            autoNumericInstances['part2_13b'].set(calculated_13b);
            autoNumericInstances['part2_18b'].set(calculated_18b);
            autoNumericInstances['part2_18d'].set(calculated_18d);
            autoNumericInstances['part2_18f'].set(calculated_18f);
            autoNumericInstances['part2_18h'].set(calculated_18h);
            autoNumericInstances['part2_18j'].set(calculated_18j);
            autoNumericInstances['part2_18l'].set(calculated_18l);
            autoNumericInstances['part2_18o'].set(calculated_18o);
        }

        function allFunctions() {
            calculate16a();
            calculate16b();
            calculate17f();
            calculate18p();
            calculate19();
            calculate20f();
            calculate21();
            calculate22();
            calculate23g();
            calculate24();
            calculate25d();
            calculate26();
            calculateOutputTax();
        }

        function getTotalSales() {
            const baseURL = $('#base_url').val();
            const url = baseURL + 'system_management/api/bir-forms/2550m/get-sales-month';
            console.log("URL:", url);
            $.ajax({
                url: url,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    company_code: $('#company_code').val(),
                    month: $('#month').val(),
                    year: $('#year').val()
                }),
                success: function(response) {
                    autoNumericInstances['part2_12a'].set(response.total_sales);
                    console.log("Total Sales:", response.total_sales);
                    calculateOutputTax();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", {xhr: xhr, status: status, error: error});
                }
            });
        }

    });

    document.addEventListener('DOMContentLoaded', function() {
        const taxReliefSpecify = $('#tax_relief_specify');

        // Bind iCheck events
        $('#tax_relief_yes').on('ifChecked', function() {
            taxReliefSpecify.show();
        });

        $('#tax_relief_no').on('ifChecked', function() {
            taxReliefSpecify.hide();
        });
    });

})(jQuery);
