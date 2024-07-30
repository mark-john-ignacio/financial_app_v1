<?= $this->extend("layouts/default")?>
<?= $this->section("title")?><?= $title ?><?= $this->endSection() ?>

<?= $this->section("content")?>

<div class="container mt-5">
    <h1><?= $title ?></h1>
    <div class="mb-3 d-flex justify-content-end">
    <button id="fetchButton" class="btn btn-primary">Replace Item Codes</button>
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
    $('#fetchButton').on('click', function() {
        Swal.fire({
            title: "Please wait...",
            html: "Fetching and replacing item codes...",
            allowOutsideClick: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            },
        })
        fetchAndReplaceItemCodes()
            .then(result => {
                Swal.fire({
                    title: "Success",
                    text: "Item codes have been replaced.",
                    icon: "success"
                });
            })
            .catch(error => {
                Swal.fire({
                    title: "Error",
                    text: "An error occurred while replacing item codes.",
                    icon: "error"
                });
                console.error("Error occurred during fetch:", error);
            });
    });

    function fetchAndReplaceItemCodes() {
        return fetch('<?= url_to("item-mapping") ?>')
            .then(response => response.json())
            .then(data => {
                return fetch('<?= url_to("replace-item-codes") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            })
            .then(response => response.json());
    }

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