(function($) {
    'use strict';

    $(document).ready(function() {
        document.title = "BIR Form No. 0619-E";

        // AutoNumeric configuration
        const autoNumericOptions = {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 2,
            minimumValue: -99999999999.99,
            maximumValue: 99999999999.99,
            allowDecimalPadding: false,  // Allow decimal input
            watchExternalChanges: true
        };

        // Initialize AutoNumeric for all input fields
        const fields = [
            'amount_of_remittance',
            'amount_remitted_previous',
            'net_amount_of_remittance',
            'penalty_surcharge',
            'penalty_interest',
            'penalty_compromise',
            'total_penalties',
            'total_amount_of_remittance'
        ];

        const autoNumericInstances = {};

        fields.forEach(field => {
            autoNumericInstances[field] = new AutoNumeric(`#${field}`, autoNumericOptions);
        });

        // Event Listeners
        $('#amount_of_remittance, #amount_remitted_previous').on('input', calculateNetAmount);
        $('#penalty_surcharge, #penalty_interest, #penalty_compromise').on('input', calculateTotalPenalties);
        $('.xcompute').on('input', calculateTotalAmount);

        // Functions
        function calculateNetAmount() {
            const amount14 = autoNumericInstances.amount_of_remittance.getNumber() || 0;
            const amount15 = autoNumericInstances.amount_remitted_previous.getNumber() || 0;
            const netAmount = amount14 - amount15;
            autoNumericInstances.net_amount_of_remittance.set(netAmount);
        }

        function calculateTotalPenalties() {
            const surcharge = autoNumericInstances.penalty_surcharge.getNumber() || 0;
            const interest = autoNumericInstances.penalty_interest.getNumber() || 0;
            const compromise = autoNumericInstances.penalty_compromise.getNumber() || 0;
            const totalPenalties = surcharge + interest + compromise;
            autoNumericInstances.total_penalties.set(totalPenalties);
        }

        function calculateTotalAmount() {
            const netAmount = autoNumericInstances.net_amount_of_remittance.getNumber() || 0;
            const totalPenalties = autoNumericInstances.total_penalties.getNumber() || 0;
            const totalAmount = netAmount + totalPenalties;
            autoNumericInstances.total_amount_of_remittance.set(totalAmount);
        }
    });
})(jQuery);
