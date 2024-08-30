<?= $this->extend("layouts/default")?>
<?= $this->section("title")?><?= $title ?><?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1><?= $title ?></h1>
    <div class="mb-3">
        <a href="<?= site_url('bir-forms/reporting-period') ?>" class="btn btn-primary">Back</a>
    </div>
    <div class="row">
        <div class="col-md-6">
        <?= form_open(site_url('bir-forms/reporting-period/') . $company->id) ?>
            <input type="hidden" name="_method" value="PUT">
            <?= $this->include("BIRForms/ReportingPeriod/form") ?>
            <button class="btn btn-primary">Save</button>
        <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>