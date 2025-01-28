<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">WooCommerce Settings</h3>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="updateDefaultCustomer">
                <div class="form-group">
                    <label for="defaultCustomer">Default Customer</label>
                    <div wire:ignore>
                        <select id="customerSelect" class="form-control">
                            @if($defaultCustomerId)
                                <option value="{{ $defaultCustomerId }}">{{ $selectedCustomerText }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function initializeSelect2() {
        $('#customerSelect').select2({
            placeholder: 'Search for a customer...',
            allowClear: true,
            ajax: {
                url: '{{ route("api.customers.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        }).on('change', function(e) {
            @this.set('defaultCustomerId', $(this).val());
            @this.set('selectedCustomerText', $(this).find('option:selected').text());
        });
    }

    document.addEventListener('livewire:initialized', () => {
        initializeSelect2();

        Livewire.on('updateDefaultCustomer', () => {
            const select = $('#customerSelect');
            select.select2('destroy');
            initializeSelect2();
            
            if (@this.defaultCustomerId) {
                const option = new Option(@this.selectedCustomerText, @this.defaultCustomerId, true, true);
                select.append(option).trigger('change');
            }
        });
    });
</script>
@endpush
