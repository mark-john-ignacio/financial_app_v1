<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Forms<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Manage Forms</h1>
    <div class="mb-3">
    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewModal">Add New</a>
    </div>
    <div class="table-responsive">
        <table id="formsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Form Code</th>
                    <th>Form Name</th>
                    <th>Form Filter</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formsTable').DataTable({
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
            { data: null },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <a href="<?= site_url('bir-forms/form/edit') ?>/${row.id}" class="btn btn-warning">Edit</a>
                        <a href="<?= site_url('bir-forms/form/delete') ?>/${row.id}" class="btn btn-danger">Delete</a>
                    `;
                }
            }
        ]
    });
});
</script>
<?= $this->endSection() ?>