<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Forms<?= $this->endSection() ?>

<?= $this->section("content")?>
<!-- Static Form -->
<div class="container mt-5">
  <div class="card">
    <div class="card-header">
      <h1 class="card-title fs-5">Mass Upload Customers</h1>
    </div>
    <?php if (isset($errors) && is_array($errors) && count($errors) > 0): ?>
    <div class="alert alert-danger">
      <h5 class="alert-heading">Error(s) occurred:</h5>
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
    <?php endif ?>
    <?= form_open_multipart('customers/upload', ['id' => 'uploadForm']) ?>
    <div class="card-body">
      <div class="mb-3">
        <label for="formFile" class="form-label">Upload Excel File</label>
        <input class="form-control" type="file" id="formFile" name="userfile" accept=".xlsx, .xls">
      </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
      <a type="button" href= "<?= url_to("Customers\\Customers::downloadTemplate")?>" class="btn btn-info me-2">Download Template</a>
      <div>
      <a type="button" href= "<?= site_url("customers") ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </div>
    <?= form_close() ?>
  </div>
</div>

<?= $this->endSection() ?>