<div class="page-header">
	<h2>Warehouses</h2>
	<p class="text-muted">View all warehouse locations and details.</p>
</div>

<div class="toolbar">
	<div class="search-box">
		<input type="text" id="search-input" placeholder="Search by name..." class="form-control">
		<button type="button" id="search-btn" class="btn btn-primary">Search</button>
	</div>
</div>

<div class="table-responsive">
	<table class="data-table" id="warehouses-table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Address</th>
				<th>Created</th>
				<th>Updated</th>
			</tr>
		</thead>
		<tbody id="warehouses-body">
			<tr><td colspan="5" class="loading">Loading...</td></tr>
		</tbody>
	</table>
</div>

<div class="pagination" id="pagination"></div>
