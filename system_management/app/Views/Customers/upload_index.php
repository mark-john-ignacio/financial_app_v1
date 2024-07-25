<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Forms<?= $this->endSection() ?>

<?= $this->section("content")?>
<!-- Static Form -->
<div class="container mt-5">
  <div class="card">
    <div class="card-header">
      <h1 class="card-title fs-5">Mass Upload Customers</h1>
    </div>
    <?php if (isset($errors)): ?>
    <div class="alert alert-danger">
      <ul>
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
        <input class="form-control" type="file" id="formFile" name="file" accept=".xlsx, .xls">
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      <button type="back" class="btn btn-secondary me-2">Cancel</button>
      <button type="submit" class="btn btn-primary me-2">Upload</button>
    </div>
    <?= form_close() ?>
  </div>
</div>

<?= $this->endSection() ?>