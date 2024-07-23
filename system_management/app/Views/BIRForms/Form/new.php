<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>New Form<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Add New Form</h1>
    <div class="mb-3">
        <a href="<?= site_url('bir-forms/form') ?>" class="btn btn-primary">Back</a>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= form_open(site_url('bir-forms/form')) ?>
            <div class="mb-3">
                <label for="form_code" class="form-label">Form Code</label>
                <input type="text" class="form-control" id="form_code" name="form_code" required value="<?= old("form_code", esc($form->form_code)) ?>">
            </div>
            <div class="mb-3">
                <label for="form_name" class="form-label">Form Name</label>
                <input type="text" class="form-control" id="form_name" name="form_name" required value="<?= old("form_name", esc($form->form_name)) ?>">
            </div>
            <div class="mb-3">
                <label for="filter" class="form-label">Form Filter</label>
                <select class="form-select" id="filter" name="filter">
                    <option value="Annual" <?= old('filter',esc($form->filter ?? "")) == "Annual" ? 'selected' : "" ?>>Annual</option>
                    <option value="Quarterly" <?= old('filter',esc($form->filter ?? "")) == "Quarterly" ? 'selected' : "" ?>>Quarterly</option>
                    <option value="Monthly" <?= old('filter',esc($form->filter ?? "")) == "Monthly" ? 'selected' : "" ?>>Monthly</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="cstatus" class="form-label">Status</label>
                <select class="form-select" id="cstatus" name="cstatus" required>
                    <option value="ACTIVE" <?= old('cstatus',esc($form->cstatus ?? "")) == "ACTIVE" ? 'selected' : "" ?>>Active</option>
                    <option value="INACTIVE" <?= old('cstatus',esc($form->cstatus ?? "")) == "INACTIVE" ? 'selected' : "" ?>>Inactive</option>
                </select>
            </div>
            <!-- <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div> -->
            <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>