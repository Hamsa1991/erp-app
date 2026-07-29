<form id="bill-form">
	<div class="form-row">
		<div class="form-group">
			<label for="client-id">Client</label>
			<select id="client-id" required class="form-control">
				<option value="">Select client...</option>
				<?php foreach ($clients as $client): ?>
				<option value="<?php echo (int) $client->id; ?>">
					<?php echo html_escape($client->first_name.' '.$client->last_name); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="warehouse-id">Warehouse</label>
			<select id="warehouse-id" required class="form-control">
				<option value="">Select warehouse...</option>
				<?php foreach ($warehouses as $warehouse): ?>
				<option value="<?php echo (int) $warehouse->id; ?>">
					<?php echo html_escape($warehouse->name); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="discount">Discount</label>
			<input type="number" id="discount" step="0.01" min="0" value="0" class="form-control">
		</div>
	</div>

	<h3>Products</h3>
	<div id="line-items">
		<div class="line-item">
			<div class="form-row">
				<div class="form-group flex-2">
					<label>Product</label>
					<select class="form-control line-product" required>
						<option value="">Select warehouse first...</option>
					</select>
				</div>
				<div class="form-group">
					<label>Quantity</label>
					<input type="number" class="form-control line-quantity" min="1" value="1" required>
				</div>
				<div class="form-group">
					<label>Unit Price</label>
					<input type="number" class="form-control line-price" step="0.01" min="0" required>
				</div>
				<div class="form-group">
					<label>Stock</label>
					<input type="text" class="form-control line-stock" readonly>
				</div>
				<div class="form-group line-actions">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-danger remove-line" disabled>&times;</button>
				</div>
			</div>
		</div>
	</div>

	<button type="button" id="add-line-btn" class="btn btn-outline">+ Add Product Line</button>

	<div class="bill-summary">
		<p>Subtotal: <strong id="subtotal">0.00</strong></p>
		<p>Discount: <strong id="discount-display">0.00</strong></p>
		<p>Total: <strong id="grand-total" class="text-primary">0.00</strong></p>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Create Bill</button>
	</div>
</form>

<div id="form-message" class="alert" style="display:none;"></div>
