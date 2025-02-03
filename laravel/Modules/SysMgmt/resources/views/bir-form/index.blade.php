<x-base::layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mt-5">
        <div class="mb-3">
            <a href="{{ route('bir-forms.create') }}" class="btn btn-primary">Add New</a>
        </div>
        
        <livewire:sysmgmt::bir-forms-table />
    </div>

    @push('scripts')
    <script>
        Livewire.on('showAlert', params => {
            Swal.fire({
                icon: params[0].type,
                title: params[0].message,
            });
        });
    </script>
    @endpush
</x-base::layouts.app>