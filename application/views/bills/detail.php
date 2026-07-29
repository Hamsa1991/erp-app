<div class="page-header">
	<a href="<?php echo site_url('bills'); ?>" class="back-link">&larr; Back to Bills</a>
	<h2>Bill #<?php echo (int) $bill->id; ?></h2>
</div>

<div class="detail-grid">
	<div class="detail-card">
		<h3>Client</h3>
		<p><strong><?php echo html_escape($bill->client_first_name.' '.$bill->client_last_name); ?></strong></p>
		<?php if ($bill->client_email): ?>
		<p><?php echo html_escape($bill->client_email); ?></p>
		<?php endif; ?>
		<?php if ($bill->client_phone): ?>
		<p><?php echo html_escape($bill->client_phone); ?></p>
		<?php endif; ?>
	</div>
	<div class="detail-card">
		<h3>Warehouse</h3>
		<p><strong><?php echo html_escape($bill->warehouse_name); ?></strong></p>
		<?php if ($bill->warehouse_address): ?>
		<p><?php echo nl2br(html_escape($bill->warehouse_address)); ?></p>
		<?php endif; ?>
	</div>
	<div class="detail-card">
		<h3>Summary</h3>
		<p>Subtotal: <strong><?php echo number_format((float) $bill->total, 2); ?></strong></p>
		<p>Discount: <strong><?php echo number_format((float) $bill->discount, 2); ?></strong></p>
		<p>Total: <strong class="text-primary"><?php echo number_format((float) $bill->total_after_discount, 2); ?></strong></p>
		<p class="text-muted">Created: <?php echo html_escape($bill->created_at); ?></p>
	</div>
</div>

<h3>Line Items</h3>
<div class="table-responsive">
	<table class="data-table">
		<thead>
			<tr>
				<th>Product</th>
				<th>Code</th>
				<th>Quantity</th>
				<th>Unit Price</th>
				<th>Line Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($bill->details as $detail): ?>
			<tr>
				<td><?php echo html_escape($detail->product_name); ?></td>
				<td><?php echo html_escape($detail->product_code); ?></td>
				<td><?php echo (int) $detail->quantity; ?></td>
				<td><?php echo number_format((float) $detail->price, 2); ?></td>
				<td><?php echo number_format((float) $detail->price * (int) $detail->quantity, 2); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
