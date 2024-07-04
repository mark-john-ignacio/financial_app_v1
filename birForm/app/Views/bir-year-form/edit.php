<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Manage BIR<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Edit Associations for Year: <?= $year ?></h1>
    <form id="associationForm">
        <table id="formsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Form Name</th>
                    <th>Form Code</th> <!-- Assuming you have a form_code property -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forms as $form): ?>
                    <tr>
                        <td>
                            <input type="checkbox" value="<?= $form->id ?>" id="form-<?= $form->id ?>" name="forms[]" <?= in_array($form->id, $associatedForms) ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <?= $form->form_code ?> <!-- Displaying the form code -->
                        </td>
                        <td>
                            <label for="form-<?= $form->id ?>"><?= $form->form_name ?></label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
$('#formsTable').DataTable({});

$(document).ready(function() {

    $('#associationForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post('<?= site_url('bir-year-form/' . $year_id . 'update') ?>', formData, function(response) {
            alert('Associations updated successfully!');
            window.location.href = '<?= site_url('manage-bir-forms') ?>';
        }).fail(function() {
            alert('Failed to update associations.');
        });
    });
});
</script>
<?= $this->endSection()?>