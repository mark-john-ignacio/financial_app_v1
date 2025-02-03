<div>
    <div class="container mt-5">
        <h1>Manage Forms</h1>
        <div class="mb-3">
            <a href="{{ route('bir-forms.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive">
            <table id="formsTable" class="display responsive" style="width:100%">
                <thead>
                    <tr>
                        <th>Form Code</th>
                        <th>Form Name</th>
                        <th>Form Filter</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#formsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("bir-forms.data") }}',
                    type: 'GET',
                },
                columns: [
                    { data: 'form_code' },
                    { data: 'form_name' },
                    { data: 'filter' },
                    { data: 'cstatus' },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <div class="d-flex justify-content-start">
                                    <a href="/bir-forms/${data.id}" class="btn btn-sm btn-primary me-2">View</a>
                                    <a href="/bir-forms/${data.id}/edit" class="btn btn-sm btn-warning me-2">Edit</a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(${data.id})">Delete</button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            window.addEventListener('swal:success', event => {
                Swal.fire(event.detail);
                table.ajax.reload();
            });

            window.addEventListener('swal:error', event => {
                Swal.fire(event.detail);
            });

            window.confirmDelete = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('delete', id);
                    }
                });
            }
        });
    </script>
    @endpush
</div>