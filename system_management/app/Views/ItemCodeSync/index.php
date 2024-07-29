<?= $this->extend("layouts/default")?>
<?= $this->section("title")?><?= $title ?><?= $this->endSection() ?>

<?= $this->section("content")?>

<div class="container mt-5">
    <h1><?= $title ?></h1>
    <div class="mb-3 d-flex justify-content-between">
    </div>
    <div class="table-responsive">
        <table id="formsTable" class="display responsive" style="width:100%">
            <thead>
                <tr>
                    <th>Old Code</th>
                    <th>Old Description</th>
                    <th>SKU Code</th>
                    <th>New Code</th>
                    <th>New Description</th>
                    <th>Match Type</th>
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
            url: '<?= url_to("item-mapping") ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'old_code' },
            { data: 'old_item_desc' },
            { data: 'sku_code' },
            { data: 'new_code' },
            { data: 'new_item_desc' },
            { data: 'match_type' }
        ]
    });
});
</script>
<?= $this->endSection() ?>