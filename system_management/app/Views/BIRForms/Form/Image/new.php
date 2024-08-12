<?= $this->extend("layouts/default")?>
<?= $this->section("title")?><?= $title ?><?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1><?= $title ?></h1>
    <div class="mb-3">
        <a href="<?= site_url('bir-forms/form/') . $form->id ?>" class="btn btn-primary">Back</a>
    </div>
    <div class="row">
        <div class="col-md-6">
        <?= form_open_multipart(site_url("bir-forms-image/form/") . $form_image->id . "/create") ?>
            <div class="mb-3">
                <label for="form_image" class="form-label">Form Image</label>
                <input type="file" class="form-control" id="form_image" name="form_image" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>

        <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>