import jQuery from 'jquery';
window.jQuery = window.$ = jQuery;

import DataTable from 'datatables.net';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

function initializeComponents($) {
    try {
        // Initialize DataTables
        $('.datatable').DataTable();
        
    } catch (error) {
        console.error('Component initialization failed:', error);
        console.error('Available plugins:', Object.keys($.fn));
    }
}

// Livewire hooks
document.addEventListener('livewire:load', function() {
    initializeComponents(jQuery);
});

jQuery(document).ready(function() {
    initializeComponents(jQuery);
});

export { jQuery, DataTable, bootstrap };