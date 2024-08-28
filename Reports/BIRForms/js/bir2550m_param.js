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
        $inputTaxCarriedOver17a.add($inputTaxDeferredCapitalGoods17b).add($transitionalInputTax17c).add($presumptiveInputTax17d).add($others17e).on('input', calculateTotalLess);

        // Functions
        function calculateTotalLess() {
            const part2_17a = parseFloat($part2_17a.val()) || 0;
            const part2_17b = parseFloat($part2_17b.val()) || 0;
            const part2_17c = parseFloat($part2_17c.val()) || 0;
            const part2_17d = parseFloat($part2_17d.val()) || 0;
            const part2_17e = parseFloat($part2_17e.val()) || 0;
            const totalLess = part2_17a + part2_17b + part2_17c + part2_17d + part2_17e;
            $part2_17f.val(totalLess.toFixed(2));
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