<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">WooCommerce Settings</h3>
        </div>
        <div class="card-body">
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

            <form wire:submit.prevent="updateDefaultCustomer">
                <div class="form-group">
                    <label for="defaultCustomer">Default Customer</label>
                    <div wire:ignore>
                        <select id="customerSelect" style="width: 100%">
                            @if($defaultCustomerCode && $selectedCustomerText)
                                <option value="{{ $defaultCustomerCode }}" selected>{{ $selectedCustomerText }}</option>
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
    let select2Instance;

    function initializeSelect2() {
        if (select2Instance) {
            select2Instance.select2('destroy');
        }

        select2Instance = $('#customerSelect').select2({
            placeholder: 'Search for a customer...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("api.customers.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term || '',
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
        });

        // Set initial value if exists
        if (@this.defaultCustomerCode && @this.selectedCustomerText) {
            let initialOption = new Option(@this.selectedCustomerText, @this.defaultCustomerCode, true, true);
            select2Instance.append(initialOption).trigger('change');
        }

        select2Instance.on('select2:select', function(e) {
            @this.set('defaultCustomerCode', e.params.data.id);
            @this.set('selectedCustomerText', e.params.data.text);
        });

        select2Instance.on('select2:clear', function() {
            @this.set('defaultCustomerCode', null);
            @this.set('selectedCustomerText', null);
        });
    }

    document.addEventListener('livewire:initialized', () => {
        initializeSelect2();

        Livewire.on('updateDefaultCustomer', () => {
            initializeSelect2();
        });
    });
</script>
@endpush
