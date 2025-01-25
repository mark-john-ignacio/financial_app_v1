<div>
    <div class="flex justify-between mb-4">
        <h2 class="text-xl">Assign Product Mapping</h2>
    </div>

    <table id="product-mapping-table" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Myx ID</th>
            <th>Myx Product Name</th>
            <th>Woo Product ID</th>
            <th>Woo Product Name</th>
        </tr>
        </thead>
    </table>
    <div x-data x-init="
        @if(session()->has('conversion_success'))
            setTimeout(() => {
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('conversion_success')['message'] ?? '' }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000,
                });
            }, 500);
        @endif
    ">
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#product-mapping-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("inventory-conversion.data") }}',
                        type: 'GET'
                    },
                    columns: [
                        {
                            data: 'reference_no',
                            name: 'reference_no',
                            render: function(data, type, row) {
                                return `<a href="{{ url('inventory-conversion') }}/${row.id}">${data}</a>`;
                            }
                        },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'details_count', name: 'details_count' },
                    ],
                    order: [[1, 'desc']],
                    pageLength: 10
                });
            });
        </script>
@endpush
