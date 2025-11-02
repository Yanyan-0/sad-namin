<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = '$user_id'";
$result = $conn->query($sql);

$total = 0;
?>

<h2>Your Cart</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td>₱<?= number_format($row['price'], 2) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['subtotal'], 2) ?></td>
        </tr>
        <?php $total += $row['subtotal']; ?>
    <?php } ?>
</table>

<h3>Total: ₱<?= number_format($total, 2) ?></h3>

<form method="POST" action="place_order.php">
    <button type="submit">Proceed to Checkout</button>
</form>
