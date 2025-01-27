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
                    <select wire:model="defaultCustomerId" class="form-control">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->cempid }}">
                                {{ $customer->cname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
        </div>

    </div>
</div>
