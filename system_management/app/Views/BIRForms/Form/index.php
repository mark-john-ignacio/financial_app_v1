<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Forms<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Manage Forms</h1>
    <div class="mb-3">
    <a href="<?= site_url('bir-forms/form/new') ?>" class="btn btn-primary">Add New</a>
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
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("scripts")?>
<script>
$(document).ready(function() {
    var formsDatatable = $('#formsTable').DataTable({
        ajax: {
            url: '<?= site_url('bir-forms/form/load') ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'form_code' },
            { data: 'form_name' },
            { data: 'filter' },
            { data: 'cstatus' },
            {
                data: null,
                render: function(data, type, row) {
                    var showUrl = "<?= url_to('BIRForms\\BIRFormController::show', ':id') ?>".replace(':id', row.id);
                    var editUrl = "<?= url_to('BIRForms\\BIRFormController::edit', ':id') ?>".replace(':id', row.id);
                    var deleteUrl = "<?= url_to('BIRForms\\BIRFormController::delete', ':id') ?>".replace(':id', row.id);
                    return `
                        <div class="d-flex justify-content-start">
                            <a href="${showUrl}" class="btn btn-sm btn-primary me-2">View</a>
                            <a href="${editUrl}" class="btn btn-sm btn-warning me-2">Edit</a>
                            <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete('${deleteUrl}')">Delete</a>
                        </div>
                    `;
                }
            }
        ]
    });
});

function confirmDelete(deleteUrl) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    ).then(() => {
                        // Optionally, reload the page or remove the deleted item from the DOM
                        $('#formsTable').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        'There was a problem deleting your file.',
                        'error'
                    );
                }
            })
            .catch(error => {
                Swal.fire(
                    'Error!',
                    'There was a problem deleting your file.',
                    'error'
                );
            });
        }
    });
}
</script>
<?= $this->endSection() ?>