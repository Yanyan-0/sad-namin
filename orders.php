<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hardware_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ---------------- UPDATE TRANSACTION STATUS ---------------- */
if (isset($_POST['update_status'])) {
    $transaction_id = intval($_POST['transaction_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE transactions SET status='$new_status' WHERE transaction_id=$transaction_id");
}

/* ---------------- FETCH TRANSACTIONS ---------------- */
$query = "
    SELECT 
        t.transaction_id,
        t.user_id,
        u.fname AS first_name,
        u.lname AS last_name,
        t.total_amount,
        t.transaction_date,
        t.order_type,
        t.delivery_address,
        t.contact_number,
        t.status
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    ORDER BY t.transaction_date DESC
";
$transactions = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Abeth Hardware</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>

<div class="orders-wrapper">
    <div class="top-bar">
        <a href="admin.php" class="back-btn">← Back to Dashboard</a>
        <h2>Orders Management</h2>
    </div>

    <table class="orders-table">
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Method</th>
            <th>Address</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if ($transactions && $transactions->num_rows > 0): ?>
            <?php while ($row = $transactions->fetch_assoc()): ?>
                <?php
                    $customer_name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                    if ($customer_name === '') $customer_name = 'Guest';
                ?>
                <tr>
                    <td><?= $row['transaction_id'] ?></td>
                    <td><?= htmlspecialchars($customer_name) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['order_type'])) ?></td>
                    <td><?= htmlspecialchars($row['delivery_address'] ?? 'N/A') ?></td>
                    <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['transaction_date']) ?></td>
                    <td>
                        <span class="status-badge <?= strtolower($row['status']) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" class="update-form">
                            <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                            <select name="status">
                                <option value="Pending" <?= ($row['status'] ?? '')=='Pending'?'selected':'' ?>>Pending</option>
                                <option value="Success" <?= ($row['status'] ?? '')=='Success'?'selected':'' ?>>Success</option>
                            </select>
                            <button type="submit" name="update_status" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No transactions found.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
