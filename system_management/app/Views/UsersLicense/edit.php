<?= $this->extend('layouts/default') ?>
<?= $this->section('title') ?>Users License<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h1>Edit Users License for <?= esc($license->company_name) ?></h1>
    <?= form_open(site_url('/users-license/') . $license->id . '/update', ['id' => 'licenseForm']) ?>
        <input type="hidden" name="_method" value="PATCH">
        <input type="hidden" name="id" value="<?= $license->id ?>">

        <div class="mb-3">
            <label for="licenseNumber" class="form-label">License Number</label>
            <input type="text" class="form-control" id="licenseNumber" name="license_number" value="<?= $license->number ?>">
        </div>
        <div class="mb-3">
            <button type="close" class="btn btn-secondary">Back</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<?= $this->endSection()?>