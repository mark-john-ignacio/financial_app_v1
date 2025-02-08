
<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    // Check if the keys are set
    $company = isset($_SESSION['companyid']) ? $_SESSION['companyid'] : "";
    $employee_cashier_name = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : "";

    if(empty($company) || empty($employee_cashier_name)){
        header("Refresh: 5");
    }

    include('../../Connection/connection_string.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Customer Monitor</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
    <div style="min-height: 100vh; position: relative;">
        <div class='row nopadwtop' id='header' style="background-color: #000; width:100%; height:55px; margin-bottom: 5px !important">
            <div style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
                <img src="../../images/LOGOTOP.png" alt="logo" class="logo-default" width="150" height="48" />
            </div>
        </div>
    </div>
    <div class="container">
        <div class="navigation">
            <p class="nav-title">CURRENT ORDER</p>
        </div>
        <div class="body-container" id="order-container">
            <!-- Order cards will be appended here -->
        </div>
        <div class="footer-container">
            <div class="nov-net">
                <span class="nov">Total Amount : </span>
                <span class="nov-price"></span>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function fetchData() {
                // Make AJAX request to fetch data from PHP script
                $.ajax({
                    url: 'Function/sitem.php',
                    type: 'GET',
                    data: { itemId: '<?php echo $employee_cashier_name; ?>' }, // Replace 'your_item_id_here' with actual item ID
                    dataType: 'json',
                    success: function (data) {
                        console.log('Data received:', data); // Debug log
                        // Populate the order container with item data
                        var orderContainer = $('#order-container');
                        orderContainer.empty(); // Clear previous content

                        // Check if data array is not empty
                        if (data && data.length > 0) {
                            var totalGrossAmount = 0;
                            var discountMatrix = 0;
                            var specialdiscount = 0;
                            var coupon = 0;

                            $.each(data, function (index, item) {
                                var card = $('<div class="card"></div>');
                                var cardPicture = $('<div class="card-picture"></div>');
                                var cardTitle = $('<div class="card-title"></div>');
                                var h3 = $('<h3></h3>').text(item.item);
                                var qtyItem = $('<span class="qty-item"></span>').html('<span class="quantity">' + item.quantity + '</span> ' + item.unit);
                                var pText = $('<div class="p-text"></div>');
                                var p1Text = $('<span class="p1-text">SUB TOTAL</span>');
                                var p2Text = $('<span class="p2-text"></span>').text('₱ ' + (item.quantity * item.price));

                                pText.append(p1Text);
                                pText.append(p2Text);

                                cardTitle.append(h3);
                                cardTitle.append(qtyItem);
                                cardTitle.append(pText);

                                card.append(cardPicture);
                                card.append(cardTitle);
                                orderContainer.append(card);

                                // Calculate total gross amount
                                totalGrossAmount += item.quantity * item.price;
                                discountMatrix = item.discount;
                                specialdiscount = item.specialDisc;
                                coupon = item.coupon;
                            });

                            // Calculate VAT and Net of VAT
                            var vatRate = 0.12; // Assuming VAT rate is 12%
                            var netOfVat = totalGrossAmount / 1.12;
                            var vatAmount = netOfVat * vatRate;
                            var grossamount = netOfVat + vatAmount;
                            var itemdiscount = discountMatrix;
                            var total = parseFloat(grossamount) - parseFloat(specialdiscount) - parseFloat(coupon) - parseFloat(itemdiscount);

                            $('.footer-container .nov-net:nth-child(1) .nov-price').text('₱ ' + parseFloat(total).toFixed(2));
                        } else {
                            $('.footer-container .nov-net:nth-child(1) .nov-price').text('₱ 0.00');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Failed to fetch data from server:', error); // Debug log
                        console.log('Raw response:', xhr.responseText); // Log raw response
                    }
                });
            }

            // Call the fetchData function every 1 second (1000ms)
            setInterval(fetchData, 1000);
        });
    </script>
</body>
</html>