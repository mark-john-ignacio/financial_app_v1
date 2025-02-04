<x-base::layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('BIR Forms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                <style>
                    /* Light Mode Base */
                    .dt-container {
                        padding: 1rem 0;
                        background-color: transparent;
                        color: rgb(55, 65, 81); /* gray-700 */
                    }

                    table.dataTable {
                        background-color: white;
                        border-radius: 0.5rem;
                        overflow: hidden;
                    }

                    /* Light Mode Controls */
                    .dt-length select,
                    .dt-search input {
                        border: 1px solid rgb(229, 231, 235); /* gray-200 */
                        border-radius: 0.375rem;
                        padding: 0.375rem 0.75rem;
                        background-color: white;
                        color: rgb(55, 65, 81); /* gray-700 */
                    }

                    .dt-paging .dt-button {
                        background-color: white;
                        border: 1px solid rgb(229, 231, 235); /* gray-200 */
                        color: rgb(55, 65, 81) !important; /* gray-700 */
                        margin: 0 0.25rem;
                        padding: 0.375rem 0.75rem;
                        border-radius: 0.375rem;
                    }

                    .dt-paging .dt-button.current {
                        background-color: rgb(243, 244, 246) !important; /* gray-100 */
                        border-color: rgb(229, 231, 235) !important; /* gray-200 */
                    }

                    /* Dark Mode Base */
                    .dark .dt-container {
                        color: rgb(229, 231, 235); /* gray-200 */
                    }

                    .dark table.dataTable {
                        background-color: rgb(17, 24, 39); /* gray-900 */
                    }

                    .dark table.dataTable thead th {
                        background-color: rgb(31, 41, 55); /* gray-800 */
                        color: rgb(229, 231, 235); /* gray-200 */
                        border-bottom: 1px solid rgb(75, 85, 99); /* gray-600 */
                    }

                    .dark table.dataTable tbody td {
                        background-color: rgb(17, 24, 39); /* gray-900 */
                        border-bottom: 1px solid rgb(75, 85, 99); /* gray-600 */
                        color: rgb(229, 231, 235); /* gray-200 */
                    }

                    /* Dark Mode Controls */
                    .dark .dt-container .dt-length,
                    .dark .dt-container .dt-search,
                    .dark .dt-container .dt-info,
                    .dark .dt-container .dt-processing {
                        color: rgb(229, 231, 235) !important; /* gray-200 */
                    }

                    .dark .dt-container .dt-length select,
                    .dark .dt-container .dt-search input {
                        background-color: rgb(31, 41, 55) !important; /* gray-800 */
                        border: 1px solid rgb(75, 85, 99) !important; /* gray-600 */
                        color: rgb(229, 231, 235) !important; /* gray-200 */
                        padding: 0.375rem;
                        border-radius: 0.375rem;
                    }

                    .dark .dt-container .dt-paging .dt-button {
                        background-color: rgb(31, 41, 55) !important; /* gray-800 */
                        border: 1px solid rgb(75, 85, 99) !important; /* gray-600 */
                        color: rgb(229, 231, 235) !important; /* gray-200 */
                    }

                    .dark .dt-container .dt-paging .dt-button.current,
                    .dark .dt-container .dt-paging .dt-button.current:hover {
                        background-color: rgb(55, 65, 81) !important; /* gray-700 */
                        border-color: rgb(75, 85, 99) !important; /* gray-600 */
                    }

                    .dark .dt-container .dt-paging .dt-button:hover {
                        background-color: rgb(75, 85, 99) !important; /* gray-600 */
                        color: white !important;
                    }

                    .dark .dt-container .dt-paging .dt-button.disabled,
                    .dark .dt-container .dt-paging .dt-button.disabled:hover {
                        background-color: rgb(31, 41, 55) !important; /* gray-800 */
                        color: rgb(156, 163, 175) !important; /* gray-400 */
                        cursor: not-allowed;
                    }

                    /* Dark Mode Labels and Text */
                    .dark .dt-container label,
                    .dark .dt-container .dt-info {
                        color: rgb(229, 231, 235) !important; /* gray-200 */
                    }

                    .dark .dt-container select option {
                        background-color: rgb(31, 41, 55) !important; /* gray-800 */
                        color: rgb(229, 231, 235) !important; /* gray-200 */
                    }
                </style>

                    <table id="birFormsTable" class="display stripe hover w-full">
                        <thead>
                            <tr>
                                <th>Form Code</th>
                                <th>Form Name</th>
                                <th>Filter</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->form_code }}</td>
                                    <td>{{ $record->form_name }}</td>
                                    <td>{{ $record->filter }}</td>
                                    <td>{{ $record->cstatus }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        $('#birFormsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']],
            pagingType: 'full_numbers', // Enable full number pagination
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: '«',
                    previous: '‹',
                    next: '›',
                    last: '»'
                }
            },
            drawCallback: function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.querySelector('.dataTables_wrapper').classList.add('dark');
                }
            }
        });
    });
</script>
@endpush
</x-base::layouts.app>