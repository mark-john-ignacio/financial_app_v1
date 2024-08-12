<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Edit Form<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Edit Form</h1>
    <div class="mb-3">
        <a href="<?= site_url('bir-forms/form') ?>" class="btn btn-primary">Back</a>
    </div>
    <div class="row">
        <div class="col-md-6">
        <?= form_open(url_to("BIRForms\\BIRFormController::update", $form->id)) ?>
            <input type="hidden" name="_method" value="PUT">
            <?= $this->include("BIRForms/Form/form") ?>
            <button class="btn btn-primary">Save</button>
        <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>