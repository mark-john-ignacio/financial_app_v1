(function($) {
    'use strict';

    $(document).ready(function() {

        document.title = "BIR Form No. 0619-E";


        // Cache jQuery selectors
        const $amountOfRemittance = $('#amount_of_remittance');
        const $amountRemittedPrevious = $('#amount_remitted_previous');
        const $netAmountOfRemittance = $('#net_amount_of_remittance');
        const $penaltySurcharge = $('#penalty_surcharge');
        const $penaltyInterest = $('#penalty_interest');
        const $penaltyCompromise = $('#penalty_compromise');
        const $totalPenalties = $('#total_penalties');
        const $totalAmountOfRemittance = $('#total_amount_of_remittance');
        const $xcompute = $('.xcompute');


        // Event Listeners
        $amountOfRemittance.add($amountRemittedPrevious).on('input', calculateNetAmount);
        $penaltySurcharge.add($penaltyInterest).add($penaltyCompromise).on('input', calculateTotalPenalties);

        $xcompute.on('input', calculateTotalAmount);

        // Functions
        function calculateNetAmount() {
            const amount14 = parseFloat($amountOfRemittance.val()) || 0;
            const amount15 = parseFloat($amountRemittedPrevious.val()) || 0;
            const netAmount = amount14 - amount15;
            $netAmountOfRemittance.val(netAmount.toFixed(2));
        }

        function calculateTotalPenalties() {
            const surcharge = parseFloat($penaltySurcharge.val()) || 0;
            const interest = parseFloat($penaltyInterest.val()) || 0;
            const compromise = parseFloat($penaltyCompromise.val()) || 0;
            const totalPenalties = surcharge + interest + compromise;
            $totalPenalties.val(totalPenalties.toFixed(2));
        }

        function calculateTotalAmount() {
            const netAmount = parseFloat($netAmountOfRemittance.val()) || 0;
            const totalPenalties = parseFloat($totalPenalties.val()) || 0;
            const totalAmount = netAmount + totalPenalties;
            $totalAmountOfRemittance.val(totalAmount.toFixed(2));
        }
    });
})(jQuery);