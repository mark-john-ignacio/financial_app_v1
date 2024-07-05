<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Manage BIR<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Edit Associations for Year: <?= $year ?></h1>
    <?= form_open('bir-year-form/' . $year_id . '/update', ['id' => 'associationForm']) ?>
        <input type="hidden" name="_method" value="PATCH">
        <div class="mb-3">
            
        <button type="button" id="selectAll" class="btn btn-primary">Select All</button>
        </div>
        <table id="formsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Form Code</th>
                    <th>Form Name</th>
                    <th>Form Filter</th>

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
                        <td>
                            <?= $form->filter ?> <!-- Displaying the form filter -->
                        </td>   
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
$('#formsTable').DataTable({
    "order": [[ 1, "asc" ]],
});

$('#selectAll').on('click', function(){
    $('input[type="checkbox"]').prop('checked', !$(this).hasClass('active'));
    $(this).toggleClass('active');
});

</script>
<?= $this->endSection()?>