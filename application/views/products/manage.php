<div class="toolbar toolbar-split">
	<div class="search-box">
		<input type="text" id="search-input" placeholder="Search products..." class="form-control">
		<button type="button" id="search-btn" class="btn btn-primary">Search</button>
	</div>
	<button type="button" id="add-product-btn" class="btn btn-success">Add Product</button>
</div>

<div class="table-responsive">
	<table class="data-table" id="products-table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Code</th>
				<th>Price</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="products-body">
			<tr><td colspan="5" class="loading">Loading...</td></tr>
		</tbody>
	</table>
</div>

<div class="pagination" id="pagination"></div>

<div class="modal" id="product-modal" style="display:none;">
	<div class="modal-content modal-lg">
		<div class="modal-header">
			<h3 id="modal-title">Add Product</h3>
			<button type="button" class="modal-close">&times;</button>
		</div>
		<form id="product-form">
			<input type="hidden" id="product-id">
			<div class="form-row">
				<div class="form-group">
					<label for="product-name">Name</label>
					<input type="text" id="product-name" required class="form-control">
				</div>
				<div class="form-group">
					<label for="product-code">Code</label>
					<input type="text" id="product-code" required class="form-control">
				</div>
				<div class="form-group">
					<label for="product-price">Price</label>
					<input type="number" id="product-price" step="0.01" min="0" required class="form-control">
				</div>
			</div>

			<h4>Warehouse Inventory</h4>
			<div id="inventory-rows">
				<?php foreach ($warehouses as $warehouse): ?>
				<div class="inventory-row" data-warehouse-id="<?php echo (int) $warehouse->id; ?>">
					<strong><?php echo html_escape($warehouse->name); ?></strong>
					<input type="hidden" class="inventory-id" value="">
					<div class="form-row">
						<div class="form-group">
							<label>Quantity</label>
							<input type="number" class="form-control inventory-quantity" min="0" value="0">
						</div>
						<div class="form-group">
							<label>Alert Quantity</label>
							<input type="number" class="form-control inventory-alert" min="0" value="0">
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="form-actions">
				<button type="button" class="btn btn-outline modal-close">Cancel</button>
				<button type="submit" class="btn btn-primary">Save Product</button>
			</div>
		</form>
		<div id="form-message" class="alert" style="display:none;"></div>
	</div>
</div>

<script>
	window.MANAGE_CONFIG = {
		warehouses: <?php echo json_encode($warehouses); ?>
	};
</script>
