// import jQuery from 'jquery';
// import DataTable from 'datatables.net';
// import * as bootstrap from 'bootstrap';

// // Assign jQuery first
// window.jQuery = window.$ = jQuery;

// // Add jQuery bridge for Bootstrap plugins
// jQuery.fn.modal = function(...args) {
//     const modal = bootstrap.Modal.getInstance(this[0]) || new bootstrap.Modal(this[0]);
//     if (typeof args[0] === 'string') {
//         modal[args[0]]();
//     }
//     return this;
// };

// // Make bootstrap globally available
// window.bootstrap = bootstrap;

// // Initialize after DOM ready
// jQuery(function($) {
//     try {
//         // Initialize DataTables
//         $('.datatable').DataTable({
//             responsive: false
//         });

//         // Initialize Bootstrap components
//         $('.modal').each(function() {
//             new bootstrap.Modal(this);
//         });
        
//     } catch (e) {
//         console.error('Component initialization failed:', e);
//     }
// });

// // Export for module usage 
// export { DataTable, bootstrap };