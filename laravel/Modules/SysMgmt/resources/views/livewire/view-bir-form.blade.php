<div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Bir Form Details</h2>
    <div class="space-y-2">
        <p><strong>Form Code:</strong> {{ $record->form_code }}</p>
        <p><strong>Form Name:</strong> {{ $record->form_name }}</p>
        <p><strong>Filter:</strong> {{ $record->filter }}</p>
        <p><strong>Status:</strong> {{ $record->cstatus }}</p>
        <p><strong>Form Link:</strong> {{ $record->form_link }}</p>
        <p><strong>Params:</strong> {{ $record->params }}</p>
    </div>
</div>