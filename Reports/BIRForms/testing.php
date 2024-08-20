<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Remittance Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .section-title {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .checkbox-group input {
            margin-right: 10px;
        }
        .signature {
            margin-top: 20px;
            text-align: center;
        }
        .signature p {
            margin-bottom: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 60%;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="section-title">Monthly Remittance Form</h1>
    <form action="#" method="post">
        <!-- Part I: Background Information -->
        <div class="section-title">Part I – Background Information</div>
        
        <div class="form-group">
            <label for="month">1. For the Month of (MM/YYYY)</label>
            <input type="text" id="month" name="month">
        </div>

        <div class="form-group">
            <label for="due-date">2. Due Date (MM/DD/YYYY)</label>
            <input type="date" id="due-date" name="due-date">
        </div>

        <div class="form-group">
            <label for="amended">3. Amended Form?</label>
            <div class="checkbox-group">
                <input type="checkbox" id="amended-yes" name="amended" value="Yes">
                <label for="amended-yes">Yes</label>
                <input type="checkbox" id="amended-no" name="amended" value="No">
                <label for="amended-no">No</label>
            </div>
        </div>

        <div class="form-group">
            <label for="withheld">4. Any Taxes Withheld?</label>
            <div class="checkbox-group">
                <input type="checkbox" id="withheld-yes" name="withheld" value="Yes">
                <label for="withheld-yes">Yes</label>
                <input type="checkbox" id="withheld-no" name="withheld" value="No">
                <label for="withheld-no">No</label>
            </div>
        </div>

        <div class="form-group">
            <label for="atc">5. ATC</label>
            <input type="text" id="atc" name="atc">
        </div>

        <div class="form-group">
            <label for="tax-code">6. Tax Type Code</label>
            <input type="text" id="tax-code" name="tax-code">
        </div>

        <!-- Part II: Tax Remittance -->
        <div class="section-title">Part II – Tax Remittance</div>
        
        <div class="form-group">
            <label for="remittance">14. Amount of Remittance</label>
            <input type="number" id="remittance" name="remittance">
        </div>

        <div class="form-group">
            <label for="previously-remitted">15. Less: Amount Remitted from Previously Filed Form, if this is an amended form</label>
            <input type="number" id="previously-remitted" name="previously-remitted">
        </div>

        <div class="form-group">
            <label for="net-remittance">16. Net Amount of Remittance (Item 14 Less Item 15)</label>
            <input type="number" id="net-remittance" name="net-remittance">
        </div>

        <div class="form-group">
            <label for="penalties">17. Add: Penalties</label>
            <input type="number" id="penalties" name="penalties">
        </div>

        <div class="form-group">
            <label for="total-remittance">18. Total Amount of Remittance (Sum of Items 16 and 17)</label>
            <input type="number" id="total-remittance" name="total-remittance">
        </div>

        <div class="signature">
            <p>I/We declare under the penalties of perjury that this remittance form has been made in good faith, verified by me/us, and to the best of my/our knowledge and belief, is true and correct, pursuant to the provisions of the National Internal Revenue Code, as amended, and the regulations issued under authority thereof.</p>
            <div class="signature-line"></div>
            <p>Signature over Printed Name of Taxpayer/Authorized Representative/Tax Agent</p>
        </div>

        <div class="signature">
            <div class="signature-line"></div>
            <p>Signature over Printed Name of President/Vice President/Authorized Officer or Representative/Tax Agent</p>
        </div>

        <!-- Part III: Details of Payment -->
        <div class="section-title">Part III – Details of Payment</div>
        
        <div class="form-group">
            <label for="cash-payment">19. Cash/Bank Debit Memo</label>
            <input type="number" id="cash-payment" name="cash-payment">
        </div>

        <div class="form-group">
            <label for="check-payment">20. Check</label>
            <input type="number" id="check-payment" name="check-payment">
        </div>

        <div class="form-group">
            <label for="tax-memo">21. Tax Debit Memo</label>
            <input type="number" id="tax-memo" name="tax-memo">
        </div>

        <div class="form-group">
            <label for="other-payment">22. Others (specify below)</label>
            <input type="text" id="other-payment" name="other-payment">
        </div>
    </form>
</div>

</body>
</html>
