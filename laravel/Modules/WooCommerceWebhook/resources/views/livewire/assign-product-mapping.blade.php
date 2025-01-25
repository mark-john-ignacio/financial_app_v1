<div>
    <div class="flex justify-between mb-4">
        <h2 class="text-xl">Assign Product Mapping</h2>
    </div>

    <div wire:ignore>
        <table id="product-mapping-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Myx ID</th>
                    <th>Myx Product Code</th>
                    <th>Myx Product Name</th>
                    <th>Woo Product ID</th>
                    <th>Woo Product Name</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <!-- Edit Modal -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product Mapping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        <div class="mb-3">
                            <label for="myxfin_product_id" class="form-label">Myxfin Product ID</label>
                            <input type="text" wire:model.defer="myxfin_product_id" class="form-control">
                        </div>
                        <div class="mb-3">  
                            <label for="woocommerce_product_id" class="form-label">WooCommerce Product ID</label>
                            <input type="text" wire:model.defer="woocommerce_product_id" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
        let table;
        $(document).ready(function () {
            table = $('#product-mapping-table').DataTable({
                serverSide: true,
                ajax: {
                    url: '{{ route("api.woocommerce.mapping.data") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'myxfin_product_id', name: 'myxfin_product_id'},
                    {data: 'myxfin_product_code', name: 'myxfin_product_code'},
                    {data: 'myx_product_name', name: 'myx_product_name'},
                    {data: 'woocommerce_product_id', name: 'woocommerce_product_id'},
                    {data: 'woo_product_name', name: 'woo_product_name'},
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<button type="button" wire:click="editRow(${row.id})" class="btn btn-sm btn-primary">Edit</button>`;
                        }
                    }
                ],
                order: [[0, 'asc']],
                pageLength: 10
            });
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refreshTable', () => {
                table.ajax.reload(null, false);
            });

            Livewire.on('showModal', () => {
                $('#editModal').modal('show');
            });

            Livewire.on('hideModal', () => {
                $('#editModal').modal('hide');
            });
        });
    </script>
@endpush
