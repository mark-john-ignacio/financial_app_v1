<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form-Year Association</title>
</head>
<body>
    <h1>Associate Forms with Years</h1>
    <form action="/path/to/your/controller/method" method="post">
        <label for="year">Select Year:</label>
        <select name="year" id="year">
            <?php for($i = date('Y'); $i <= date('Y') + 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <br>
        <label>Select Forms:</label>
        <br>
        <?php foreach($forms as $form): ?>
            <input type="checkbox" name="forms[]" value="<?= $form['id'] ?>"> <?= $form['name'] ?><br>
        <?php endforeach; ?>
        <button type="submit">Associate</button>
    </form>
</body>
</html>