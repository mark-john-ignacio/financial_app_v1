<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Show Form<?= $this->endSection() ?>

<?= $this->section("content")?>
<div class="container mt-5">
    <h1>Show Form</h1>
    <div class="mb-3">
        <a href="<?= site_url('bir-forms/form') ?>" class="btn btn-primary">Back</a>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= form_open(site_url('bir-forms/form')) ?>

            <?= $this->include("BIRForms/Form/form") ?>

            </form>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        // Disable all input, textarea, and select fields
        var inputs = document.querySelectorAll('input, textarea, select');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].disabled = true;
        }
    };
</script>
<?= $this->endSection() ?>

