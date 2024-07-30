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
    // Store the URL in a variable
    const itemMappingUrl = '<?= url_to("item-mapping") ?>';
    let cachedData = null;

    // Show loading Swal fire while fetching data
    Swal.fire({
        title: "Please wait...",
        html: "Fetching item codes...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    // Fetch the data once and store it in cachedData
    fetch(itemMappingUrl)
        .then(response => response.json())
        .then(data => {
            cachedData = data;
            initializeDataTable(data);
            Swal.close(); // Close the loading Swal fire
        })
        .catch(error => {
            Swal.fire({
                title: "Error",
                text: "An error occurred while fetching item codes.",
                icon: "error"
            });
            console.error("Error occurred during initial fetch:", error);
        });

    $('#fetchButton').on('click', function() {
        if (cachedData) {
            Swal.fire({
                title: "Please wait...",
                html: "Replacing item codes...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            fetchAndReplaceItemCodes(cachedData)
                .then(result => {
                    const updatedCount = result.updated;
                    Swal.fire({
                        title: "Success",
                        text: `Purchase Receiving Item codes have been replaced. Updated: ${updatedCount}`,
                        icon: "success"
                    });
                    console.log(`Updated: ${updatedCount}`); 
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error",
                        text: "An error occurred while replacing item codes.",
                        icon: "error"
                    });
                    console.error("Error occurred during fetch:", error);
                });
        } else {
            console.error("Data not yet fetched.");
        }
    });

    function fetchAndReplaceItemCodes(data) {
        return fetch('<?= url_to("replace-item-codes") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json());
    }

    function initializeDataTable(data) {
        $('#formsTable').DataTable({
            data: data,
            columns: [
                { data: 'old_code' },
                { data: 'old_item_desc' },
                { data: 'sku_code' },
                { data: 'new_code' },
                { data: 'new_item_desc' },
                { data: 'match_type' }
            ]
        });
    }
});
</script>
<?= $this->endSection() ?>