<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-header">
    <h2>Clients Management</h2>
    <p class="text-muted">Manage your clients - add, update, or view client information.</p>
</div>

<div class="toolbar">
    <div class="search-box">
        <input type="text" id="search-input" placeholder="Search by name, email, or phone..." class="form-control">
        <button type="button" id="search-btn" class="btn btn-primary">Search</button>
    </div>
    <div class="toolbar-actions">
        <button type="button" id="add-client-btn" class="btn btn-success">+ Add Client</button>
    </div>
</div>

<!-- Modal for Add/Edit Client -->
<!-- Modal for Add/Edit Client -->
<div class="modal" id="client-modal" style="display:none;">
	<div class="modal-overlay"></div>
	<div class="modal-content">
		<div class="modal-header">
			<h3 id="modal-title">Add New Client</h3>
			<button type="button" class="modal-close" id="modal-close">&times;</button>
		</div>
		<div class="modal-body">
			<form id="client-form">
				<input type="hidden" id="client-id" name="id">

				<!-- Add this for form messages -->
				<div id="form-message" style="display:none;"></div>

				<div class="form-group">
					<label for="first_name">First Name <span class="required">*</span></label>
					<input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter first name" required>
					<div class="error-message" id="first_name-error"></div>
				</div>

				<div class="form-group">
					<label for="last_name">Last Name <span class="required">*</span></label>
					<input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter last name" required>
					<div class="error-message" id="last_name-error"></div>
				</div>

				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" class="form-control" placeholder="Enter email address">
					<div class="error-message" id="email-error"></div>
				</div>

				<div class="form-group">
					<label for="phone">Phone</label>
					<input type="text" id="phone" name="phone" class="form-control" placeholder="Enter phone number">
					<div class="error-message" id="phone-error"></div>
				</div>

				<div class="form-actions">
					<button type="button" class="btn btn-secondary" id="modal-cancel">Cancel</button>
					<button type="submit" class="btn btn-primary" id="modal-submit">Save Client</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="table-responsive">
    <table class="data-table" id="clients-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="clients-body">
            <tr><td colspan="6" class="loading">Loading...</td></tr>
        </tbody>
    </table>
</div>

<div class="pagination" id="pagination"></div>
