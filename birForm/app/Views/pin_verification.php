<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pin Verification</title>
</head>
<body>
    <h1>Enter Pin to Access</h1>
    <?= form_open("/verify_pin") ?>
        <label for="pin">Pin Code:</label>
        <input type="password" id="pin" name="pin">
        <button type="submit">Verify</button>
    </form>

    <?= form_open("/set_pin") ?>
        <label for="new_pin">New Pin:</label>
        <input type="password" id="new_pin" name="new_pin">
        <button type="submit">Set Pin</button>
    </form>
</body>
</html>

