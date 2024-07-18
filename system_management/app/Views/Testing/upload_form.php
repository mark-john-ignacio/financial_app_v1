<?= $this->extend('layouts/default') ?>
<?= $this->section('title') ?>Upload<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php foreach ($errors as $error): ?>
    <li><?= esc($error) ?></li>
<?php endforeach ?>

<?= form_open_multipart('upload/upload') ?>
    <input type="file" name="userfile" size="20">
    <br><br>
    <input type="submit" value="upload">
</form>

<?= $this->endSection() ?>