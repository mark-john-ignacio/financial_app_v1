(function($) {
    'use strict';

    $(document).ready(function() {
        document.title = "BIR Form No. 2550M";

        // Cache jQuery selectors
        const $part2_12a = $('#part2_12a');
        const $part2_12b = $('#part2_12b');
        const $part2_13a = $('#part2_13a');
        const $part2_13b = $('#part2_13b');
        const $part2_14 = $('#part2_14');
        const $part2_15 = $('#part2_15');
        const $part2_16a = $('#part2_16a');
        const $part2_16b = $('#part2_16b');
        const $part2_17a = $('#part2_17a');
        const $part2_17b = $('#part2_17b');
        const $part2_17c = $('#part2_17c');
        const $part2_17d = $('#part2_17d');
        const $part2_17e = $('#part2_17e');
        const $part2_17f = $('#part2_17f');
        const $part2_18a = $('#part2_18a');
        const $part2_18b = $('#part2_18b');
        const $part2_18c = $('#part2_18c');
        const $part2_18d = $('#part2_18d');
        const $part2_18e = $('#part2_18e');
        const $part2_18f = $('#part2_18f');
        const $part2_18g = $('#part2_18g');
        const $part2_18h = $('#part2_18h');
        const $part2_18i = $('#part2_18i');
        const $part2_18j = $('#part2_18j');
        const $part2_18k = $('#part2_18k');
        const $part2_18l = $('#part2_18l');
        const $part2_18m = $('#part2_18m');
        const $part2_18n = $('#part2_18n');
        const $part2_18o = $('#part2_18o');
        const $part2_18p = $('#part2_18p');
        const $part2_19 = $('#part2_19');
        const $part2_20a = $('#part2_20a');
        const $part2_20b = $('#part2_20b');
        const $part2_20c = $('#part2_20c');
        const $part2_20d = $('#part2_20d');
        const $part2_20e = $('#part2_20e');
        const $part2_20f = $('#part2_20f');
        const $part2_21 = $('#part2_21');
        const $part2_22 = $('#part2_22');
        const $part2_23a = $('#part2_23a');
        const $part2_23b = $('#part2_23b');
        const $part2_23c = $('#part2_23c');
        const $part2_23d = $('#part2_23d');
        const $part2_23e = $('#part2_23e');
        const $part2_23f = $('#part2_23f');
        const $part2_23g = $('#part2_23g');
        const $part2_24 = $('#part2_24');
        const $part2_25a = $('#part2_25a');
        const $part2_25b = $('#part2_25b');
        const $part2_25c = $('#part2_25c');
        const $part2_25d = $('#part2_25d');
        const $part2_26 = $('#part2_26');
        const $xcompute = $('.xcompute');

        // Event Listeners
        allFunctions();
        $xcompute.on('input', function() {
            allFunctions();
        });

        // Functions
        function calculate16a() {
            const part2_12a = parseFloat($part2_12a.val()) || 0;
            const part2_13a = parseFloat($part2_13a.val()) || 0;
            const part2_14 = parseFloat($part2_14.val()) || 0;
            const part2_15 = parseFloat($part2_15.val()) || 0;
            const calculated_16a = part2_12a + part2_13a + part2_14 + part2_15;
            $part2_16a.val(calculated_16a.toFixed(2));
        }

        function calculate16b() {
            const part2_12b = parseFloat($part2_12b.val()) || 0;
            const part2_13b = parseFloat($part2_13b.val()) || 0;
            const calculated_16b = part2_12b + part2_13b;
            $part2_16b.val(calculated_16b.toFixed(2));
        }


        function calculate17f() {
            const part2_17a = parseFloat($part2_17a.val()) || 0;
            const part2_17b = parseFloat($part2_17b.val()) || 0;
            const part2_17c = parseFloat($part2_17c.val()) || 0;
            const part2_17d = parseFloat($part2_17d.val()) || 0;
            const part2_17e = parseFloat($part2_17e.val()) || 0;
            const calculated_17f = part2_17a + part2_17b + part2_17c + part2_17d + part2_17e;
            $part2_17f.val(calculated_17f.toFixed(2));
        }

        function calculate18p(){
            const part2_18a = parseFloat($part2_18a.val()) || 0;
            const part2_18c = parseFloat($part2_18c.val()) || 0;
            const part2_18e = parseFloat($part2_18e.val()) || 0;
            const part2_18g = parseFloat($part2_18g.val()) || 0;
            const part2_18i = parseFloat($part2_18i.val()) || 0;
            const part2_18k = parseFloat($part2_18k.val()) || 0;
            const part2_18m = parseFloat($part2_18m.val()) || 0;
            const part2_18n = parseFloat($part2_18n.val()) || 0;
            const calculated_18p = part2_18a + part2_18c + part2_18e + part2_18g + part2_18i + part2_18k + part2_18m + part2_18n;
            $part2_18p.val(calculated_18p.toFixed(2));
        }

        function calculate19() {
            const part2_17f = parseFloat($part2_17f.val()) || 0;
            const part2_18b = parseFloat($part2_18b.val()) || 0;
            const part2_18d = parseFloat($part2_18d.val()) || 0;
            const part2_18f = parseFloat($part2_18f.val()) || 0;
            const part2_18h = parseFloat($part2_18h.val()) || 0;
            const part2_18j = parseFloat($part2_18j.val()) || 0;
            const part2_18l = parseFloat($part2_18l.val()) || 0;
            const part2_18o = parseFloat($part2_18o.val()) || 0;
            const calculated_19 = part2_17f + part2_18b + part2_18d + part2_18f + part2_18h + part2_18j + part2_18l + part2_18o;
            $part2_19.val(calculated_19.toFixed(2));
        }

        function calculate20f(){
            const part2_20a = parseFloat($part2_20a.val()) || 0;
            const part2_20b = parseFloat($part2_20b.val()) || 0;
            const part2_20c = parseFloat($part2_20c.val()) || 0;
            const part2_20d = parseFloat($part2_20d.val()) || 0;
            const part2_20e = parseFloat($part2_20e.val()) || 0;
            const calculated_20f = part2_20a + part2_20b + part2_20c + part2_20d + part2_20e;
            $part2_20f.val(calculated_20f.toFixed(2));
        }

        function calculate21() {
            const part2_19 = parseFloat($part2_19.val()) || 0;
            const part2_20f = parseFloat($part2_20f.val()) || 0;
            const calculated_21 = part2_19 - part2_20f;
            $part2_21.val(calculated_21.toFixed(2));
        }

        function calculate22() {
            const part2_16b = parseFloat($part2_16b.val()) || 0;
            const part2_21 = parseFloat($part2_21.val()) || 0;
            const calculated_22 = part2_16b - part2_21;
            $part2_22.val(calculated_22.toFixed(2));
        }

        function calculate23g() {
            const part2_23a = parseFloat($part2_23a.val()) || 0;
            const part2_23b = parseFloat($part2_23b.val()) || 0;
            const part2_23c = parseFloat($part2_23c.val()) || 0;
            const part2_23d = parseFloat($part2_23d.val()) || 0;
            const part2_23e = parseFloat($part2_23e.val()) || 0;
            const part2_23f = parseFloat($part2_23f.val()) || 0;
            const calculated_23g = part2_23a + part2_23b + part2_23c + part2_23d + part2_23e + part2_23f;
            $part2_23g.val(calculated_23g.toFixed(2));
        }

        function calculate24() {
            const part2_22 = parseFloat($part2_22.val()) || 0;
            const part2_23g = parseFloat($part2_23g.val()) || 0;
            const calculated_24 = part2_22 - part2_23g;
            $part2_24.val(calculated_24.toFixed(2));
        }

        function calculate25d() {
            const part2_25a = parseFloat($part2_25a.val()) || 0;
            const part2_25b = parseFloat($part2_25b.val()) || 0;
            const part2_25c = parseFloat($part2_25c.val()) || 0;
            const calculated_25d = part2_25a + part2_25b + part2_25c;
            $part2_25d.val(calculated_25d.toFixed(2));
        }

        function calculate26() {
            const part2_24 = parseFloat($part2_24.val()) || 0;
            const part2_25d = parseFloat($part2_25d.val()) || 0;
            const calculated_26 = part2_24 + part2_25d;
            $part2_26.val(calculated_26.toFixed(2));
        }

        function calculateOutputTax() {
            const taxpercent = 12;
            const taxDecimal = taxpercent / 100;
            const part2_12a = parseFloat($part2_12a.val()) || 0;
            const part2_13a = parseFloat($part2_13a.val()) || 0;
            const part2_18a = parseFloat($part2_18b.val()) || 0;
            const part2_18c = parseFloat($part2_18d.val()) || 0;
            const part2_18e = parseFloat($part2_18f.val()) || 0;
            const part2_18g = parseFloat($part2_18h.val()) || 0;
            const part2_18i = parseFloat($part2_18j.val()) || 0;
            const part2_18k = parseFloat($part2_18l.val()) || 0;
            const part2_18n = parseFloat($part2_18o.val()) || 0;
            const calculated_12b = part2_12a * taxDecimal;
            const calculated_13b = part2_13a * taxDecimal;
            const calculated_18b = part2_18a * taxDecimal;
            const calculated_18d = part2_18c * taxDecimal;
            const calculated_18f = part2_18e * taxDecimal;
            const calculated_18h = part2_18g * taxDecimal;
            const calculated_18j = part2_18i * taxDecimal;
            const calculated_18l = part2_18k * taxDecimal;
            const calculated_18o = part2_18n * taxDecimal;
            $part2_12b.val(calculated_12b.toFixed(2));
            $part2_13b.val(calculated_13b.toFixed(2));
            $part2_18b.val(calculated_18b.toFixed(2));
            $part2_18d.val(calculated_18d.toFixed(2));
            $part2_18f.val(calculated_18f.toFixed(2));
            $part2_18h.val(calculated_18h.toFixed(2));
            $part2_18j.val(calculated_18j.toFixed(2));
            $part2_18l.val(calculated_18l.toFixed(2));
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