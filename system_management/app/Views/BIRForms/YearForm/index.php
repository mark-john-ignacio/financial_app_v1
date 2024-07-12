<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Manage BIR<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Manage BIR Year-Form Registration</h1>
    <div class="mb-3">
    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewModal">Add New</a>
    </div>
    <div class="table-responsive">
        <table id="associationsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Forms</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?= form_open(site_url('bir-forms/year-form/new'), ['id' => 'addNewForm']) ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewModalLabel">Select a Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select class="form-select" id="yearSelect" name="year_id">
                    <option value="">Select a Year</option>
                    <?php foreach($availableYears as $year): ?>
                        <option value="<?= $year->id ?>"><?= $year->year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Proceed</button>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#associationsTable').DataTable({
        ajax: {
            url: '<?= site_url('bir-forms/year-form/associations') ?>',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error("Error occurred during AJAX request:", error, thrown);
                console.error("Status:", xhr.status);
                console.error("ResponseText:", xhr.responseText);
            }
        },
        columns: [
            { data: 'year' },
            { data: 'forms'},
            { data: null, render: function(data, type, row) {
                return `<a href="<?= site_url("bir-forms/year-form/")?>${row.id}/edit" class="btn btn-primary">Edit</a>`;
            }}
        ],
        columnDefs: [{ orderable: false, targets: [1, 2] }],
        order: [[ 0, "desc" ]]
    });
});
</script>
<?= $this->endSection()?>