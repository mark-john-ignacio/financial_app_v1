<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Reporting Period<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1><?= $title ?></h1>
    <div class="mb-3">
    </div>
    <div class="table-responsive">
        <table id="formsTable" class="display responsive" style="width:100%">
            <thead>
                <tr>
                    <th>Company Code</th>
                    <th>Company Name</th>
                    <th>Reporting Period</th>
                    <th>Fiscal Month</th>
                    <th>Taxpayer Size</th>
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
            url: '<?= site_url('bir-forms/reporting-period/load') ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
           }
        },
        columns: [
            { data: 'company_code' },
            { data: 'company_name' },
            { data: 'reporting_period' },
            { 
                data: 'fiscal_month', 
                render: function(data, type, row) {
                    if (row.reporting_period === "calendar") {
                        return "N/A";
                    }
                    return data;
                }
            },
            { data: 'taxpayer_size_class' },
            {
                data: null,
                render: function(data, type, row) {
                    var editUrl = "<?= site_url('bir-forms/reporting-period') ?>" + "/" + row.id + "/edit";
                    return `
                        <div class="d-flex justify-content-start">
                            <a href="${editUrl}" class="btn btn-sm btn-warning me-2">Edit</a>
                        </div>
                    `;
                }
            }
        ]
    });
});
</script>
<?= $this->endSection() ?>