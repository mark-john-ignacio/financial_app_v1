<?= $this->extend('layouts/default') ?>
<?= $this->section('title') ?>Users License<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h1>Users License</h1>
    <div class="table-responsive">
        <table id="usersLicenseTable" class="display responsive" style="width:100%">
            <thead>
                <tr>
                    <th>License ID</th>
                    <th>License Company</th>
                    <th>License Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersLicense as $license): ?>
                    <tr>
                        <td>
                            <?= $license->id ?>
                        </td>
                        <td>
                            <?= $license->company_name ?>
                        </td>
                        <td>
                            <?= $license->number ?>
                        </td>
                        <td>
                            <a href="<?= site_url('/users-license/') . $license->id . '/edit' ?>" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#usersLicenseTable').DataTable({
            columnDefs: [
                { orderable: false, targets: 3 }
            ]
        });
    });
</script>
<?= $this->endSection() ?>