<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Pin Verify<?= $this->endSection() ?>

<?= $this->section("content")?>
<?= site_url() ?>
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <p class="h3 card-title">Enter Pin to Access</p>
                        <?php if (session()->has("message")): ?>
                            <div class="alert alert-success" role="alert">
                                <?= session("message") ?>
                            </div>September. 
                        <?php endif; ?>

                        <?php if (session()->has("error")): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= session("error") ?>
                            </div>
                        <?php endif; ?>
                        <?= form_open(site_url("verify-pin"), ['class' => '']) ?>
                            <div class="mb-3">
                                <label for="pin" class="form-label">Pin Code:</label>
                                <input type="password" id="pin" name="pin" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection()?>