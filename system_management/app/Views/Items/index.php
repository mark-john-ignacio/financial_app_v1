<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Items<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Items</h1>
    <div class="mb-3 d-flex justify-content-between">
        <!-- <a href="<?= site_url('bir-forms/form/new') ?>" class="btn btn-primary">Add New</a> -->
        <a class="btn btn-secondary" href="<?= url_to("Customers\\Customers::upload_form") ?>">Mass Upload</a>
    </div>
    <div class="table-responsive">
        <table id="formsTable" class="display responsive" style="width:100%">
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Description</th>
                    <th>Main UOM</th>
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
            url: '<?= url_to("items-load") ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'item_code' },
            { data: 'item_description' },
            { data: 'unit_of_measure' },
            { data: 'cstatus' },
        ]
    });
});
</script>
<?= $this->endSection() ?>