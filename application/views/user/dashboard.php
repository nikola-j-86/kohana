<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/kohana/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h1>Section for user</h1>

<div class="content-wrapper">
    <div class="create-form">
        <p>If you want to log out, please <a href="/kohana/logout" class="button-link">click here</a>.</p>
        <h2>Create Payment Invoice</h2>
        <form id="create-invoice-form">
            <label for="paymentSystemId">Payment System:</label>
            <select name="paymentSystemId" id="paymentSystemId" required>
                <?php foreach ($payment_systems as $system): ?>
                    <option value="<?php echo $system->id; ?>">
                        <?php echo htmlspecialchars($system->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="details">Details:</label>
            <input type="text" name="details" id="details" placeholder="Details" required>
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" placeholder="Amount" required>
            <input type="submit" value="Create Invoice">
        </form>
    </div>
    <div class="list-records">
        <h2>Your Invoices</h2>
        <table id="invoices-table">
            <thead>
            <tr>
                <th>Details</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <!-- Invoice rows will be inserted here -->
            </tbody>
        </table>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#create-invoice-form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: '/kohana/invoices/create',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        alert('Invoice created!');
                        loadInvoices(); // Reload invoice list
                    }
                }
            });
        });

        function loadInvoices() {
            $.ajax({
                type: 'GET',
                url: '/kohana/invoices/get',
                success: function(response) {
                    var invoicesTable = $('#invoices-table tbody');
                    invoicesTable.empty(); // Clear existing rows
                    response.forEach(function(invoice) {
                        invoicesTable.append('<tr><td>' + invoice.details + '</td><td>' + invoice.amount + '</td><td>' + invoice.status + '</td></tr>');
                    });
                }
            });
        }

        loadInvoices(); // Load invoices when the page is ready
    });
</script>
</body>
</html>