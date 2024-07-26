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

            <?= $this->include("birforms/form/form") ?>
            <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>