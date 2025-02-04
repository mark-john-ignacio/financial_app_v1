<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('BIR Forms') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <a href="{{ route('bir-forms.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add New</a>
                    </div>
                    
                    <table class="w-full" id="formsTable">
                        <thead>
                            <tr>
                                <th class="text-left">Form Code</th>
                                <th class="text-left">Form Name</th>
                                <th class="text-left">Form Filter</th>
                                <th class="text-left">Status</th>
                                <th class="text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("bir-forms.data") }}')
                .then(response => response.json())
                .then(data => {
                    const dataTable = new simpleDatatables.DataTable("#formsTable", {
                        data: {
                            headings: ["Form Code", "Form Name", "Form Filter", "Status", "Action"],
                            data: data.data.map(item => [
                                item.form_code,
                                item.form_name,
                                item.filter,
                                item.cstatus,
                                `<div class="flex space-x-2">
                                    <a href="/bir-forms/${item.id}" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">View</a>
                                    <a href="/bir-forms/${item.id}/edit" class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                    <button onclick="confirmDelete(${item.id})" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                                </div>`
                            ])
                        },
                        searchable: true,
                        fixedHeight: true,
                        perPage: 10
                    });
                });

                window.confirmDelete = function(id) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('delete', { id: id });
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
