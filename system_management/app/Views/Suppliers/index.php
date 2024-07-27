<?= $this->extend("layouts/default")?>
<?= $this->section("title")?><?= $title ?><?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1><?= $title ?></h1>
    <div class="mb-3 d-flex justify-content-between">
        <!-- <a href="<?= site_url('bir-forms/form/new') ?>" class="btn btn-primary">Add New</a> -->
        <a class="btn btn-secondary" href="<?= url_to("suppliers-upload-form") ?>">Mass Upload</a>
    </div>
    <div class="table-responsive">
        <table id="formsTable" class="display responsive" style="width:100%">
            <thead>
                <tr>
                    <th>Supplier Code</th>
                    <th>Supplier Name</th>
                    <th>TIN No.</th>
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
            url: '<?= url_to("suppliers-load") ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'supplier_code' },
            { data: 'supplier_name' },
            { data: 'tin' },
            { data: 'status' },
        ]
    });
});
</script>
<?= $this->endSection() ?>