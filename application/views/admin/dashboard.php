<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/kohana/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h1>Section for admin</h1>
<p>If you want to log out, please <a href="/kohana/logout" class="button-link">click here</a>.</p>
<div class="content-wrapper">
    <!-- Invoices Section -->
    <div class="list-records">
        <h2>Invoices for Approval</h2>
        <table id="invoices-table">
            <thead>
            <tr>
                <th>Details</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <!-- Invoice rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Payment Systems Section -->
    <div class="list-records">
        <h2>Payment Systems</h2>
        <table id="payment-systems-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Is Active</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <!-- Payment system rows will be inserted here via JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Create Payment System Form -->
    <div class="create-form">
        <h2>Create Payment System</h2>
        <form id="create-payment-system-form">
            <label for="systemName">Name:</label>
            <input type="text" name="systemName" id="systemName" required>

            <label for="isActive">Is Active:</label>
            <input type="checkbox" name="isActive" id="isActive" value="1">

            <input type="submit" value="Create System">
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-payment-system-form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: '/kohana/payment-systems/create',
                data: $(this).serialize(),
                success: function() {
                    alert('Payment system created');
                    loadPaymentSystems(); // Reload listings
                }
            });
        });

        function loadPaymentSystems() {
            $.ajax({
                type: 'GET',
                url: '/kohana/payment-systems/get',
                success: function(response) {
                    var systemsTable = $('#payment-systems-table tbody');
                    systemsTable.empty(); // Clear existing rows
                    response.forEach(function(system) {
                        systemsTable.append(`
                            <tr>
                                <td>${system.name}</td>
                                <td><input type="checkbox" class="active-checkbox" data-system-id="${system.id}" ${system.is_active == '1' ? 'checked' : ''}></td>
                                <td>
                                     <td><button class="update-system" data-system-id="${system.id}">Update</button></td>
                                </td>
                            </tr>
                    `);
                    });
                }
            });
        }

        function loadInvoices() {
            $.ajax({
                type: 'GET',
                url: '/kohana/invoices/all',
                success: function(response) {
                    var invoicesTable = $('#invoices-table tbody');
                    invoicesTable.empty(); // Clear existing rows
                    response.forEach(function(invoice) {
                        invoicesTable.append(`<tr>
                    <td>${invoice.details}</td>
                    <td>${invoice.amount}</td>
                    <td>${invoice.status}</td>
                    <td>
                        <button onclick="approveInvoice(${invoice.id})">Approve</button>
                        <button onclick="cancelInvoice(${invoice.id})">Cancel</button>
                        <button onclick="downloadInvoice(${invoice.id})">Download PDF</button>
                    </td>
                </tr>`);
                    });
                }
            });
        }


        window.approveInvoice = function(invoiceId) {
            $.ajax({
                type: 'POST',
                url: `/kohana/invoices/${invoiceId}/approve`,
                success: function() {
                    alert('Invoice approved');
                    loadInvoices(); // Reload listings
                }
            });
        };

        window.cancelInvoice = function(invoiceId) {
            $.ajax({
                type: 'POST',
                url: `/kohana/invoices/${invoiceId}/cancel`,
                success: function() {
                    alert('Invoice cancelled');
                    loadInvoices(); // Reload listings
                }
            });
        }

        window.downloadInvoice = function(invoiceId) {
            window.location.href = `/kohana/invoices/${invoiceId}/download`;
        }

        // Bind click event dynamically to the edit buttons
        $(document).on('click', '.edit-payment-system-button', function() {
            let systemId = $(this).data('system-id');
            editPaymentSystem(systemId);
        });


        // Event listener for the Update button
        $(document).on('click', '.update-system', function() {
            var systemId = $(this).data('system-id');
            var isActive = $(`.active-checkbox[data-system-id="${systemId}"]`).is(':checked') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: `/kohana/payment-systems/${systemId}/update`,
                data: { isActive: isActive },
                success: function(response) {
                    alert('Payment system updated');
                    loadPaymentSystems(); // Reload listings to reflect changes
                }
            });
        });

        // Load invoices and payment systems when the page is ready
        loadInvoices();
        loadPaymentSystems();
    });
</script>
</body>
</html>