(function($) {
    'use strict';

    $(document).ready(function() {
        document.title = "BIR Form No. 2550M";

        // Cache jQuery selectors
        const $vatSales12a = $('#vat_sales_12a');
        const $vatSales12b = $('#vat_sales_12b');
        const $salesToGovernment13a = $('#sales_to_government_13a');
        const $salesToGovernment13b = $('#sales_to_government_13b');
        const $zeroRatedSales14 = $('#zero_rated_sales_14');
        const $exemptSales15 = $('#exempt_sales_15');
        const $totSales16a = $('#tot_sales_16a');
        const $totSales16b = $('#tot_sales_16b');
        const $inputTaxCarriedOver17a = $('#input_tax_carried_over_17a');
        const $inputTaxDeferredCapitalGoods17b = $('#input_tax_deferred_capital_goods_17b');
        const $transitionalInputTax17c = $('#transitional_input_tax_17c');
        const $presumptiveInputTax17d = $('#presumptive_input_tax_17d');
        const $others17e = $('#others_17e');
        const $totalLess17f = $('#total_less_17f');
        const $purchaseCapitalGoodsNotExceeding1m18a = $('#purchase_capital_goods_not_exceeding_1m_18a');
        const $purchaseCapitalGoodsNotExceeding1m18b = $('#purchase_capital_goods_not_exceeding_1m_18b');
        const $purchaseCapitalGoodsExceeding1m18c = $('#purchase_capital_goods_exceeding_1m_18c');
        const $purchaseCapitalGoodsExceeding1m18d = $('#purchase_capital_goods_exceeding_1m_18d');
        const $domesticPurchasesGoodsNonCapital18e = $('#domestic_purchases_goods_non_capital_18e');
        const $domesticPurchasesGoodsNonCapital18f = $('#domestic_purchases_goods_non_capital_18f');
        const $importationGoodsNonCapital18g = $('#importation_goods_non_capital_18g');
        const $importationGoodsNonCapital18h = $('#importation_goods_non_capital_18h');
        const $domesticPurchaseServices18i = $('#domestic_purchase_services_18i');
        const $domesticPurchaseServices18j = $('#domestic_purchase_services_18j');
        const $servicesRenderedNonResident18k = $('#services_rendered_non_resident_18k');
        const $servicesRenderedNonResident18l = $('#services_rendered_non_resident_18l');
        const $purchasesNotQualifiedForInputTax18m = $('#purchases_not_qualified_for_input_tax_18m');
        const $others18n = $('#others_18n');
        const $others18o = $('#others_18o');
        const $totalCurrentPurchases18p = $('#total_current_purchases_18p');
        const $totalAvailableInputTax19 = $('#total_available_input_tax_19');
        const $inputTaxPurchasesCapitalGoodsExceeding1mDeferred20a = $('#input_tax_purchases_capital_goods_exceeding_1m_deferred_20a');
        const $inputTaxSaleToGovernmentClosedExpense20b = $('#input_tax_sale_to_government_closed_expense_20b');
        const $inputTaxAllocableExemptSales20c = $('#input_tax_allocable_exempt_sales_20c');
        const $vatRefundTccClaimed20d = $('#vat_refund_tcc_claimed_20d');
        const $others20e = $('#others_20e');
        const $total20f = $('#total_20f');
        const $totalAllowableInputTax21 = $('#total_allowable_input_tax_21');
        const $netVatPayable22 = $('#net_vat_payable_22');
        const $creditableVatWithheld23a = $('#creditable_vat_withheld_23a');
        const $advancePaymentSugarFlourIndustries23b = $('#advance_payment_sugar_flour_industries_23b');
        const $vatWithheldSalesToGovernment23c = $('#vat_withheld_sales_to_government_23c');
        const $vatPaidReturnPreviouslyFiled23d = $('#vat_paid_return_previously_filed_23d');
        const $advancePaymentsMade23e = $('#advance_payments_made_23e');
        const $others23f = $('#others_23f');
        const $totalTaxCreditsPayments23g = $('#total_tax_credits_payments_23g');
        const $taxStillPayableOverpayment24 = $('#tax_still_payable_overpayment_24');
        const $surcharge25a = $('#surcharge_25a');
        const $interest25b = $('#interest_25b');
        const $compromise25c = $('#compromise_25c');
        const $totalPenalties25d = $('#total_penalties_25d');
        const $totalAmountPayableOverpayment26 = $('#total_amount_payable_overpayment_26');
        const $xcompute = $('.xcompute');

        // Event Listeners
        $inputTaxCarriedOver17a.add($inputTaxDeferredCapitalGoods17b).add($transitionalInputTax17c).add($presumptiveInputTax17d).add($others17e).on('input', calculateTotalLess);

        // Functions
        function calculateTotalLess() {
            const inputTaxCarriedOver = parseFloat($inputTaxCarriedOver17a.val()) || 0;
            const inputTaxDeferredCapitalGoods = parseFloat($inputTaxDeferredCapitalGoods17b.val()) || 0;
            const transitionalInputTax = parseFloat($transitionalInputTax17c.val()) || 0;
            const presumptiveInputTax = parseFloat($presumptiveInputTax17d.val()) || 0;
            const others = parseFloat($others17e.val()) || 0;
            const totalLess = inputTaxCarriedOver + inputTaxDeferredCapitalGoods + transitionalInputTax + presumptiveInputTax + others;
            $totalLess17f.val(totalLess.toFixed(2));
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