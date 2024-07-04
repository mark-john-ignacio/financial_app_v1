<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Manage BIR<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Manage BIR Form-Year Registration</h1>
    <table id="associationsTable" class="display">
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

<script>
$(document).ready(function() {
    $('#associationsTable').DataTable({
        ajax: {
            url: '<?= site_url('manage-bir-forms/associations') ?>',
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
                return `<a href="bir-year-form/${row.id}/edit" class="btn btn-primary">Edit</a>`;
            }}
        ]
    });
});
</script>
<?= $this->endSection()?>