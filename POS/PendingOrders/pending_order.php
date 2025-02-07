<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Pending Orders</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }
        .pcard-container{
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 10px;
        }
        .pcard{
            width: 325px;
            background-color: #f0f0f0;
            border-radius:8px;
            overflow: hidden;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            margin: 15px;
            position: relative;
        }
        .pcard-header{
            display: flex;
            justify-content: space-between;
            height: 50px;
            background-color: black;
            color: white;
            font-size: 14px;
            height: 65px;
        }
        .pcard-header .left-container,
        .pcard-header .right-container{
            color: white;
            display: flex;
            flex-direction: column;
        }
        .pcard-header .left-container{
            margin-left: 25px;
            margin-top: 10px;
        }
        .pcard-header .right-container{
            margin-right: 25px;
            margin-top: 10px;
        }
        .pcard-header .left-container span {
            margin-top: 5px;
            text-transform: capitalize;
        }
        .pcard-header .right-container span{
            margin-top: 5px;
            text-transform: capitalize;
        }
        .pcard-body{
            height: auto;
            padding-bottom: 75px;
        }
        .pcard-body .ordertype-header{
            display: flex;
            flex-direction: column-reverse;
            border-top: 3px dashed black;
            border-bottom: 3px dashed ;
            text-align: center;
            padding: 10px;
            margin:15px 25px 10px 25px;
        }
        .pcard-body .ordertype-header .tablename{
            text-transform: capitalize;
            font-size: 20px;
            font-weight: bold;
        }
        .pcard-body .ordertype-header .customertype{
            font-size: 14px;
            font-weight: lighter;
        }
        .pcard-body .ordertype-header .customerordertype{
            text-transform: capitalize;
            font-size: 15px;
            font-weight: lighter;
        }
        .list-order{
            display: flex;
            flex-direction: row;
            margin-left: 24px;
            margin-top: 10px;
        }
        .list-order .orderQuantity li span{
            margin-left: -10px;
            font-weight: bold;
        }
        .list-order .counter,
        .list-order .orderName{
            margin-left: 5px;
        }
        .list-order .orderName .status{
            margin-left: 5px;
            font-size: 14px;
        }
        .dpcheckbox, 
        .hcheckbox{
            position: absolute;
            right: 25px;
            width: 14px;
            height: 14px;
            margin-left: 20px;
            transform: translateY(1px);
        }
        .orderExtra{
            margin-left: 30px;
            margin-top: 5px;
            font-size: 12px;
        }
        .pcard-footer{
            position: absolute;
            width: 100%;
            bottom: 0px;
        }
        .pcard-footer .pcard-buttons{
            display: flex;
            justify-content: center;
            height: 60px;
        }
        .pcard-footer .pcard-buttons .button-holder{
            margin-top: 15px;
            margin-bottom: 15px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
        }
        .pcard-footer .pcard-buttons .button-hold{
            background-color: lightblue;
        }
        .pcard-footer .pcard-buttons .button-done{
            background-color: lightcoral;
        }
        .pcard-footer .pcard-buttons .button-holder{
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            padding: 7px 30px 7px 30px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
    const reloadInterval = 1000;

    function startTimerWithEndTime(timerElement, card, cardHeader, endTime, waiting_time) {
        let timerInterval;

        function updateTimer() {
            const now = new Date().getTime();
            const timeLeft = endTime - now;

            if (timeLeft > 0) {
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                timerElement.text(`${minutes}m ${seconds}s`);

                if (timeLeft > waiting_time/1.5 * 60 * 1000) { 
                    cardHeader.css("background-color", "#0d800d");
                } else if (timeLeft > waiting_time/3 * 60 * 1000) {
                    cardHeader.css("background-color", "#d1bd24");
                } else if (timeLeft > waiting_time/6 * 60 * 1000) {
                    cardHeader.css("background-color", "#c71c1c");
                } else {
                    cardHeader.css("background-color", "#5e615e");
                }
            } else {
                timerElement.text("Time Expired");
                cardHeader.css("background-color", "black");
                card.hide();
                clearInterval(timerInterval); 
            }
        }

        clearInterval(timerInterval);
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    }

    function loadOrders() {
    $.ajax({
        url: 'Function/orderlist.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            // Validate data object
            if (!data || !Array.isArray(data.transactions)) {
                console.error('Invalid data format:', data);
                return;
            }
            

            const container = $('.pcard-container');
            container.empty();

            // Sort transactions by date
            const orders = data.transactions.sort((a, b) => new Date(a.ddate) - new Date(b.ddate));

            orders.forEach(order => {
                if (order && order.receipt === "No") {
                    const card = createCard(order);
                    if (card) {
                        container.append(card);
                        const timerElement = card.find('.timer');
                        const cardHeader = card.find('.pcard-header');
                        const orderDate = new Date(order.ddate).getTime();
                        const waiting_time = 30; // minutes
                        const endTime = orderDate + (waiting_time * 60 * 1000);
                        startTimerWithEndTime(timerElement, card, cardHeader, endTime, waiting_time);
                    }
                }
            });
        },
        error: function(err) {
            console.error("Error fetching transaction data:", err);
        }
    });
}

    function createCard(transaction) {
        
        // Validate transaction object
        if (!transaction || typeof transaction !== 'object') {
            console.error('Invalid transaction object:', transaction);
            return null;
        }

        // Ensure items array exists with default empty array
        transaction.items = transaction.items || [];
        const allDone = transaction.items.every(item => item.status === 'Done');

        if (allDone) {
            return null;                                              
        }

        transaction.transaction_type = transaction.transaction_type || 'Payment';
        transaction.tranno = transaction.tranno || '';
        transaction.payment_transaction = transaction.payment_transaction || '';
        transaction.ddate = transaction.ddate || new Date().toISOString();
        transaction.preparedby = transaction.preparedby || '';
        transaction.customer = transaction.customer || '';
        transaction.orderType = transaction.orderType || '';
        transaction.table = transaction.table || '';

        const card = $('<div class="pcard"></div>'); 
        const cardContent = $('<div class="pcard-content"></div>'); 

        const cardHeader = $('<div class="pcard-header"></div>');
        const leftContainer = $('<div class="left-container"></div>');
        leftContainer.append($('<span>#' + (transaction.transaction_type === 'Hold' ? transaction.tranno : transaction.payment_transaction) + '</span>'));
        leftContainer.append($('<span>' + transaction.ddate + '</span>'));

        const rightContainer = $('<div class="right-container"></div>');
        rightContainer.append($('<span>' + transaction.preparedby + '</span>'));
        rightContainer.append($('<span class="timer"></span>'));

        cardHeader.append(leftContainer);
        cardHeader.append(rightContainer);
        cardContent.append(cardHeader);

        const cardBody = $('<div class="pcard-body"></div>');
        const orderTypeHeader = $('<div class="ordertype-header"></div>');
        orderTypeHeader.append($('<span class="customertype">' + (transaction.transaction_type === 'Hold' ? '( Hold )' : '( '+ transaction.customer +' )') + '</span>'));
        orderTypeHeader.append($('<span class="customerordertype">' + transaction.orderType + '</span>'));
        orderTypeHeader.append($('<span class="tablename">' + transaction.table + '</span>'));
        cardBody.append(orderTypeHeader);

        transaction.items.forEach(item => {
            const listOrder = $('<div class="list-order"></div>');
            listOrder.append($('<div class="orderQuantity"><li><span>' + item.quantity + '</span></li></div>'));
            listOrder.append($('<span class="counter">x</span>'));
            listOrder.append(
                $('<div class="orderName"></div>').append(
                    $('<span>').text(item.citemdesc),
                    item.status === 'Done' ? $('<span class="status">').text('( ' + item.status + ' )') : ''
                )
            );


            let disableCheckbox = false;
            if (item.status === 'Done') {
                disableCheckbox = true;
            }

            // Check transaction.tranntype and add submit button accordingly
            if (transaction.transaction_type === "Payment") {
                const submitButton = $('<button class="dpcheckbox" data-tranno="' + transaction.tranno + '" data-itemid="' + item.item + '"' + '" data-payment_transaction="' + transaction.payment_transaction + '"' + '" data-transaction_type="' + transaction.transaction_type + '"' + '" data-ddate="' + transaction.ddate + '"' + (disableCheckbox ? 'disabled' : '') + '></button>');
                listOrder.append(submitButton);
            }
            if (transaction.transaction_type === "Hold") {
                const submitButton = $('<button class="hcheckbox" data-tranno="' + transaction.tranno + '" data-itemid="' + item.item + '"' + '" data-payment_transaction="' + transaction.payment_transaction + '"' + '" data-transaction_type="' + transaction.transaction_type + '"' + '" data-ddate="' + transaction.ddate + '"' + (disableCheckbox ? 'disabled' : '') + '></button>');
                listOrder.append(submitButton);
            }

            cardBody.append(listOrder);

            if (item.supplier_name) {
                const orderExtra = $('<div class="orderExtra"></div>');
                orderExtra.append($('<span>' + item.supplier_name + '</span>'));
                cardBody.append(orderExtra);
            }
        });

        cardContent.append(cardBody);

        const cardFooter = $('<div class="pcard-footer"></div>');
        const pcardButtons = $('<div class="pcard-buttons"></div>');

        if (transaction.transaction_type === "Hold" && !allDone) {
            const doneButtonPayment = $('<button class="button-holder button-done">Done</button>');
            doneButtonPayment.data('tranno', transaction.tranno);
            doneButtonPayment.data('order_date', transaction.ddate); 
            doneButtonPayment.click(function() {
                const tranno = $(this).data('tranno');
                const order_date = $(this).data('order_date');
                const button = $(this); // Cache the button for later use

                $.ajax({
                    url: 'Function/pendingorder_statushold.php',
                    method: 'POST',
                    data: {
                        tranno: tranno,
                        order_date: order_date
                    },
                    success: function(response) {
                        // Handle success response
                        console.log(response);
                    },
                    error: function(err) {
                        // Handle error
                        console.error("Error submitting payment status:", err);
                    }
                });

                // Prevent default button action (form submission)
                return false;
            });
            pcardButtons.append(doneButtonPayment);
        }

        if (transaction.transaction_type === "Payment" && !allDone) {
    const doneButtonPayment = $('<button class="button-holder button-done">Done</button>');
    const tranno = transaction.tranno; // Retrieve tranno from transaction

    if (!tranno) {
        console.error("No tranno provided in the Payment transaction.");
        return null; // Exit function to prevent further execution
    }

    doneButtonPayment.data('tranno', tranno);
    doneButtonPayment.data('order_date', transaction.ddate); 
    doneButtonPayment.click(function() {
        const tranno = $(this).data('tranno');
        const order_date = $(this).data('order_date');

        // Ensure that tranno is available
        if (!tranno) {
            console.error("No tranno provided in the Done button data.");
            return false; // Exit function to prevent further execution
        }

        $.ajax({
            url: 'Function/pendingorder_statuspayment.php',
            method: 'POST',
            data: {
                tranno: tranno,
                order_date: order_date
            },
            success: function(response) {
                console.log(response);
            },
            error: function(err) {
                console.error("Error submitting payment status:", err);
            }
        });

        return false;
    });
    pcardButtons.append(doneButtonPayment);
}


        cardFooter.append(pcardButtons);
        cardContent.append(cardFooter);

        card.append(cardContent);
        return card;
    }

    // Click event for checkbox buttons
    $('.pcard-container').on('click', '.hcheckbox', function() {
        const tranno = $(this).data('tranno');
        const payment_transaction = $(this).data('payment_transaction');
        const transaction_type = $(this).data('transaction_type');
        const ddate = $(this).data('ddate');
        const itemid = $(this).data('itemid');

        // Assuming an AJAX call to submit the checkbox data
        $.ajax({
            url: 'Function/hsubmit.php',
            method: 'POST',
            data: {
                tranno: tranno,
                payment_transaction: payment_transaction,
                transaction_type: transaction_type,
                ddate: ddate,
                itemid: itemid
            },
            success: function(response) {
                // Handle success response
                console.log(response);
            },
            error: function(err) {
                // Handle error
                console.error("Error submitting checkbox data:", err);
            }
        });
    });

    $('.pcard-container').on('click', '.dpcheckbox', function() {
        const tranno = $(this).data('tranno');
        const payment_transaction = $(this).data('payment_transaction');
        const transaction_type = $(this).data('transaction_type');
        const ddate = $(this).data('ddate');
        const itemid = $(this).data('itemid');

        // Assuming an AJAX call to submit the checkbox data
        $.ajax({
            url: 'Function/dpsubmit.php',
            method: 'POST',
            data: {
                tranno: tranno,
                payment_transaction: payment_transaction,
                transaction_type: transaction_type,
                ddate: ddate,
                itemid: itemid
            },
            success: function(response) {
                // Handle success response
                console.log(response);
            },
            error: function(err) {
                // Handle error
                console.error("Error submitting checkbox data:", err);
            }
        });
    });

    loadOrders(); // Load orders on document ready
    setInterval(loadOrders, reloadInterval); // Reload every 1000 ms (1 second)
});

    </script>
</head>
<body>
    <div stlye="min-height: 100vh; position: relative; ">
        <div class='row nopadwtop' id='header' style="background-color: #000; width:100%; height:55px; margin-bottom: 5px !important">
            <div  style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
                <img src="././images/LOGOTOP.png" alt="logo" class="logo-default" width="150" height="48" />
            </div>
            <div  style="float: right; display: flex; justify-content:space-between; align-items:center; width: 320px; height: 57px; padding-left: 20px;padding-right: 20px;">
                <div>
                    <!--<a style="color: white; text-decoration:none;" href="timer_expired.php">Expired Time</a>-->
                </div>
                <div>
                    <a style="color: white; text-decoration:none;" href="pending_order.php">Pending Orders Time</a>
                </div>
            </div>
        </div>
    </div>

    <div class="pcard-container">
       
    </div>
</body>
</html>