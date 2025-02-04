<div id="dtContainer">
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
                    
                    <table class="table table-striped" id="formsTable">
                        <thead>
                            <tr>
                                <th>Form Code</th>
                                <th>Form Name</th>
                                <th>Form Filter</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#formsTable').DataTable({
                ajax: '{{ route("bir-forms.data") }}',
                columns: [
                    { data: 'form_code' },
                    { data: 'form_name' },
                    { data: 'filter' },
                    { data: 'cstatus' },
                    {
                        data: null,
                        render: function(data) {
                            return `<div class="flex space-x-2">
                                <a href="/bir-forms/${data.id}" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">View</a>
                                <a href="/bir-forms/${data.id}/edit" class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                <button onclick="confirmDelete(${data.id})" class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                            </div>`;
                        }
                    }
                ],
                pageLength: 10
            });
        });
    </script>
    @endpush
</div>
