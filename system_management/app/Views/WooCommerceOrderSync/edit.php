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
                    <th>id</th>
                    <th>Recieved at</th>
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
            url: '<?= url_to("order-sync-load") ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'id' },
            { data: 'created_at' },
            { data: 'status' },
            {
                data: 'id',
                render: function(data, type, row) {
                    return `
                        <a href="<?= site_url('order_sync/order-sync-edit') ?>/` + data + `" class="btn btn-primary">Edit</a>
                    `;
                }
            }
        ]
    });
});
console.log("<?= site_url('order-sync/load-order/') . $id ?>");
</script>
<?= $this->endSection() ?>