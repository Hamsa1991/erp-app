<div class="page-header">
	<h2>Products Catalog</h2>
	<p class="text-muted">Browse available products with warehouse stock levels.</p>
</div>

<div class="toolbar">
	<div class="search-box">
		<input type="text" id="search-input" placeholder="Search by name or code..." class="form-control">
		<button type="button" id="search-btn" class="btn btn-primary">Search</button>
	</div>
</div>

<div class="table-responsive">
	<table class="data-table" id="products-table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Code</th>
				<th>Price</th>
				<th>Warehouse</th>
				<th>Quantity</th>
			</tr>
		</thead>
		<tbody id="products-body">
			<tr><td colspan="5" class="loading">Loading...</td></tr>
		</tbody>
	</table>
</div>

<div class="pagination" id="pagination"></div>
