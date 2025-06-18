<?php 
include 'db_connect.php';
$order = $conn->query("SELECT * FROM orders where id = {$_GET['id']}");
foreach($order->fetch_array() as $k => $v){
	$$k = $v;
}
$items = $conn->query("SELECT o.*, p.name FROM order_items o INNER JOIN products p ON p.id = o.product_id WHERE o.order_id = $id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <link rel="stylesheet" href="assets/css/receipt.css">
</head>
<body>

<div class="receipt-container">
	<p class="text-center"><b><?php echo $amount_tendered > 0 ? "Receipt" : "Bill" ?></b></p>
	<hr>
	<div class="flex">
		<div class="w-100">
			<h3>Reshma Crackers,</h3>
			<p>Sivakasi</p>
			<P>Contact NO:<span class="text-bold">123456789</span></P>
			<?php if($amount_tendered > 0): ?>
				<p>Invoice Number: <b><?php echo $ref_no ?></b></p>
			<?php endif; ?>
			<p>Date: <b><?php echo date("M d, Y", strtotime($date_created)) ?></b></p>
		</div>
	</div>
	<hr>
	<p><b>Order List</b></p>
	<table>
		<thead>
			<tr>
				<td><b>QTY</b></td>
				<td><b>Order</b></td>
				<td class="text-right"><b>Amount</b></td>
			</tr>
		</thead>
		<tbody>
			<?php while($row = $items->fetch_assoc()): ?>
			<tr>
				<td><?php echo $row['qty'] ?></td>
				<td>
					<p><?php echo $row['name'] ?></p>
					<?php if($row['qty'] > 0): ?>
						<small>(<?php echo number_format($row['price'], 2) ?>)</small>
					<?php endif; ?>
				</td>
				<td class="text-right"><?php echo number_format($row['amount'], 2) ?></td>
			</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<hr>
	<table>
		<tbody>
			<tr>
				<td><b>Total Amount</b></td>
				<td class="text-right"><b><?php echo number_format($total_amount, 2) ?></b></td>
			</tr>
			<?php if($amount_tendered > 0): ?>
			<tr>
				<td><b>Amount Received</b></td>
				<td class="text-right"><b><?php echo number_format($amount_tendered, 2) ?></b></td>
			</tr>
			<tr>
				<td><b>Balance</b></td>
				<td class="text-right"><b><?php echo number_format($amount_tendered - $total_amount, 2) ?></b></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<hr>
	<p class="text-center"><b>Order No.</b></p>
	<h4 class="text-center"><b><?php echo $order_number ?></b></h4>
</div>

</body>
<style>
	/* assets/css/receipt.css */

body {
    font-family: 'Arial', sans-serif;
    font-size: 14px;
    color: #333;
    background: #f8f8f8;
    margin: 0;
    padding: 20px;
}

.receipt-container {
    max-width: 400px;
    margin: auto;
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 10px 0;
}

.flex {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.w-100 {
    width: 100%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

table td,
table th {
    padding: 6px 0;
}

thead td {
    border-bottom: 1px solid #ccc;
}

h4 {
    margin: 0;
    font-size: 18px;
}

small {
    font-size: 12px;
    color: #666;
}

b {
    font-weight: 600;
}


</style>
</html>
