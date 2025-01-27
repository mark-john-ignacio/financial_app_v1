<div>
    <div class="flex justify-between mb-4">
        <h2 class="text-xl">Assign Product Mapping</h2>
        <button wire:click="syncNewItems" wire:loading.attr="disabled" class="btn btn-primary me-2">
            <span wire:loading.remove wire:target="syncNewItems">Sync New Items</span>
            <span wire:loading wire:target="syncNewItems">Syncing...</span>
        </button>
        <button wire:click="refreshAllProductNames" wire:loading.attr="disabled" class="btn btn-secondary">
            <span wire:loading.remove wire:target="refreshAllProductNames">Refresh Product Names</span>
            <span wire:loading wire:target="refreshAllProductNames">Refreshing...</span>
        </button>
    </div>
    @if (session()->has('message'))
        <div class="row g-3">
            <div class="col-md-6">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

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
                            <label class="form-label">WooCommerce Product ID</label>
                            <div class="input-group">
                                <input type="text" wire:model="woocommerce_product_id" class="form-control">
                                <button type="button" wire:click="checkProductName" wire:loading.attr="disabled" class="btn btn-secondary">
                                    <span wire:loading.remove wire:target="checkProductName">Check Product</span>
                                    <span wire:loading wire:target="checkProductName">Checking...</span>
                                </button>
                                @error('woocommerce_product_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($wooProductName == '__NOT_FOUND__' || $wooProductName == null)
                                <div class="text-sm text-danger mt-1">
                                    Product not found for ID: {{ $woocommerce_product_id }}
                                </div>
                            @elseif($woocommerce_product_id)
                                <div class="text-sm text-success mt-1">
                                    Product: {{ $wooProductName }}
                                </div>
                            @endif
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
