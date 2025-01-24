<div class="bg-white p-6 rounded-lg shadow-lg">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="assign" class="space-y-4">
        <div class="form-group">
            <label for="woocommerceProductId" class="block text-sm font-medium text-gray-700">WooCommerce Product ID</label>
            <input type="text" id="woocommerceProductId" wire:model="woocommerceProductId" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('woocommerceProductId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="myxfinProductId" class="block text-sm font-medium text-gray-700">Myxfin Product ID</label>
            <input type="text" id="myxfinProductId" wire:model="myxfinProductId" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('myxfinProductId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Assign</button>
    </form>

    <h3 class="mt-6 text-lg font-medium text-gray-900">Existing Mappings</h3>
    <table class="min-w-full divide-y divide-gray-200 mt-4">
        <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WooCommerce Product ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Myxfin Product ID</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @foreach ($this->mappings as $mapping)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $mapping->woocommerce_product_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $mapping->myxfin_product_id }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
