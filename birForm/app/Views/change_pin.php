<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Pin Verify<?= $this->endSection() ?>

<?= $this->section("content")?>

<?= form_open(site_url("/set-pin")) ?>
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <label for="new_pin" class="form-label">New Pin:</label>
                            <input type="password" class="form-control" id="new_pin" name="new_pin">
                        </div>
                        <button type="submit" class="btn btn-primary">Set Pin</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<?= $this->endSection()?>