<div class="toolbar toolbar-split">
	<div class="search-box">
		<input type="text" id="search-input" placeholder="Search warehouses..." class="form-control">
		<button type="button" id="search-btn" class="btn btn-primary">Search</button>
	</div>
	<button type="button" id="add-warehouse-btn" class="btn btn-success">Add Warehouse</button>
</div>

<div class="table-responsive">
	<table class="data-table" id="warehouses-table">
		<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Address</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody id="warehouses-body">
		<tr><td colspan="4" class="loading">Loading...</td></tr>
		</tbody>
	</table>
</div>

<div class="pagination" id="pagination"></div>

<!-- Modal for Add/Edit Warehouse -->
<div class="modal" id="warehouse-modal" style="display:none;">
	<div class="modal-overlay"></div>
	<div class="modal-content">
		<div class="modal-header">
			<h3 id="modal-title">Add Warehouse</h3>
			<button type="button" class="modal-close" id="modal-close">&times;</button>
		</div>
		<div class="modal-body">
			<form id="warehouse-form">
				<input type="hidden" id="warehouse-id" name="id">

				<div id="form-message" style="display:none;"></div>

				<div class="form-group">
					<label for="warehouse-name">Name <span class="required">*</span></label>
					<input type="text" id="warehouse-name" name="name" class="form-control" placeholder="Enter warehouse name" required>
					<div class="error-message" id="name-error"></div>
				</div>

				<div class="form-group">
					<label for="warehouse-address">Address</label>
					<textarea id="warehouse-address" name="address" class="form-control" placeholder="Enter warehouse address" rows="3"></textarea>
					<div class="error-message" id="address-error"></div>
				</div>

				<div class="form-actions">
					<button type="button" class="btn btn-secondary" id="modal-cancel">Cancel</button>
					<button type="submit" class="btn btn-primary" id="modal-submit">Save Warehouse</button>
				</div>
			</form>
		</div>
	</div>
</div>
