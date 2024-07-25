<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Forms<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Customers</h1>
    <div class="mb-3 d-flex justify-content-between">
        <!-- <a href="<?= site_url('bir-forms/form/new') ?>" class="btn btn-primary">Add New</a> -->
        <a class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#massUploadModal">Mass Upload</a>
    </div>
    <div class="table-responsive">
        <table id="formsTable" class="display responsive" style="width:100%">
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>TIN</th>
                    <th>Status</th>
                    <!-- <th>Action</th> -->
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
            url: '<?= site_url('customers/load') ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'customer_code' },
            { data: 'customer_name' },
            { data: 'tin' },
            { data: 'cstatus' },
        ]
    });
});
</script>
<?= $this->endSection() ?>