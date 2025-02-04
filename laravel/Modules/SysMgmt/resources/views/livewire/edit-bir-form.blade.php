<div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Edit Bir Form</h2>
    <form wire:submit.prevent="submitEdit">
        <div class="space-y-4">
            <div>
                <label for="form_code" class="block font-medium">Form Code</label>
                <input id="form_code" type="text" wire:model.defer="record.form_code" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label for="form_name" class="block font-medium">Form Name</label>
                <input id="form_name" type="text" wire:model.defer="record.form_name" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label for="filter" class="block font-medium">Filter</label>
                <input id="filter" type="text" wire:model.defer="record.filter" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label for="cstatus" class="block font-medium">Status</label>
                <select id="cstatus" wire:model.defer="record.cstatus" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-2">
            <button type="button" wire:click="$emit('closeModal')" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </div>
    </form>
</div>