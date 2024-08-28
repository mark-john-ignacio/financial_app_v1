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
        $part2_17a.add($part2_17b).add($part2_17c).add($part2_17d).add($part2_17e).on('input', calculateTotalLess);

        $part2_18a.add($part2_18c).add($part2_18e).add($part2_18g).add($part2_18i).add($part2_18k).add($part2_18m).add($part2_18n).on('input', calculateTotalCurrentPurchases);

        $part2_17f.add($part2_18b).add($part2_18d).add($part2_18f).add($part2_18h).add($part2_18j).add($part2_18l).add($part2_18o).on('input', calculateTotalAvailableInputTax);

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

        function calculateTotalCurrentPurchases(){
            const part2_18a = parseFloat($part2_18a.val()) || 0;
            const part2_18c = parseFloat($part2_18c.val()) || 0;
            const part2_18e = parseFloat($part2_18e.val()) || 0;
            const part2_18g = parseFloat($part2_18g.val()) || 0;
            const part2_18i = parseFloat($part2_18i.val()) || 0;
            const part2_18k = parseFloat($part2_18k.val()) || 0;
            const part2_18m = parseFloat($part2_18m.val()) || 0;
            const part2_18n = parseFloat($part2_18n.val()) || 0;
            const totalCurrentPurchases18p = part2_18a + part2_18c + part2_18e + part2_18g + part2_18i + part2_18k + part2_18m + part2_18n;
            $part2_18p.val(totalCurrentPurchases18p.toFixed(2));
        }

        function calculateTotalAvailableInputTax() {
            const part2_17f = parseFloat($part2_17f.val()) || 0;
            const part2_18b = parseFloat($part2_18b.val()) || 0;
            const part2_18d = parseFloat($part2_18d.val()) || 0;
            const part2_18f = parseFloat($part2_18f.val()) || 0;
            const part2_18h = parseFloat($part2_18h.val()) || 0;
            const part2_18j = parseFloat($part2_18j.val()) || 0;
            const part2_18l = parseFloat($part2_18l.val()) || 0;
            const part2_18o = parseFloat($part2_18o.val()) || 0;
            const totalAvailableInputTax = part2_17f + part2_18b + part2_18d + part2_18f + part2_18h + part2_18j + part2_18l + part2_18o;
            $part2_19.val(totalAvailableInputTax.toFixed(2));
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