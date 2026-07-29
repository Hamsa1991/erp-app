<div class="page-header">
	<h2>Low Stock Report</h2>
	<p class="text-muted">Products with quantities below their alert threshold.</p>
</div>

<div class="toolbar toolbar-split">
	<div class="search-box">
		<input type="text" id="search-input" placeholder="Search products..." class="form-control">
		<button type="button" id="search-btn" class="btn btn-primary">Search</button>
	</div>
	<div class="toolbar-actions">
		<button type="button" id="export-csv-btn" class="btn btn-success">
			<i class="icon-download"></i> Export CSV
		</button>
	</div>
</div>

<div class="table-responsive">
	<table class="data-table" id="low-stock-table">
		<thead>
		<tr>
			<th>Product Name</th>
			<th>Code</th>
			<th>Warehouse</th>
			<th>Current Quantity</th>
			<th>Alert Quantity</th>
			<th>Needed Quantity</th>
			<th>Status</th>
		</tr>
		</thead>
		<tbody id="low-stock-body">
		<tr><td colspan="7" class="loading">Loading...</td></tr>
		</tbody>
	</table>
</div>

<div class="pagination" id="pagination"></div>

<div id="export-message" class="alert" style="display:none;"></div>
