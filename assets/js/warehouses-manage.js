(function ($) {
	'use strict';

	var currentPage = 1;
	var searchTerm = '';

	// Load warehouses function
	function loadWarehouses(page) {
		currentPage = page || 1;
		var $body = $('#warehouses-body');
		$body.html('<tr><td colspan="4" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('warehouses/data'), {
			page: currentPage,
			per_page: 10,
			search: searchTerm
		}).done(function (response) {
			if (!response.success) {
				$body.html('<tr><td colspan="4" class="loading">Failed to load warehouses.</td></tr>');
				return;
			}

			var data = response.data;
			$body.empty();

			if (!data.items || !data.items.length) {
				$body.html('<tr><td colspan="4" class="loading">No warehouses found.</td></tr>');
			} else {
				$.each(data.items, function (_, warehouse) {
					$body.append(
						'<tr data-id="' + warehouse.id + '">' +
						'<td>' + $('<span>').text(warehouse.id).html() + '</td>' +
						'<td>' + $('<span>').text(warehouse.name).html() + '</td>' +
						'<td>' + $('<span>').text(warehouse.address || '-').html() + '</td>' +
						'<td class="action-buttons">' +
						'<button type="button" class="btn btn-sm btn-outline edit-btn">Edit</button> ' +
						'<button type="button" class="btn btn-sm btn-outline delete-btn">Delete</button>' +
						'</td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadWarehouses);
		}).fail(function () {
			$body.html('<tr><td colspan="4" class="loading">Failed to load warehouses.</td></tr>');
		});
	}

	// Reset form function
	function resetForm() {
		$('#warehouse-id').val('');
		$('#warehouse-name').val('');
		$('#warehouse-address').val('');
		$('#modal-title').text('Add Warehouse');
		$('.error-message').empty();
		$('#form-message').hide();
	}

	// Open modal function
	function openModal(title) {
		$('#modal-title').text(title);
		$('#warehouse-modal').addClass('show').show();
	}

	// Close modal function
	function closeModal() {
		$('#warehouse-modal').removeClass('show').hide();
		resetForm();
	}

	// Load warehouse details for editing
	function loadWarehouseDetails(id) {
		$.getJSON(App.apiUrl('warehouses/' + id)).done(function (response) {
			if (!response.success) {
				App.showAlert($('#form-message'), 'Failed to load warehouse details.', 'error');
				return;
			}

			var warehouse = response.data;
			$('#warehouse-id').val(warehouse.id);
			$('#warehouse-name').val(warehouse.name);
			$('#warehouse-address').val(warehouse.address || '');

			openModal('Edit Warehouse');
		}).fail(function () {
			App.showAlert($('#form-message'), 'Failed to load warehouse details.', 'error');
		});
	}

	// Delete warehouse function
	function deleteWarehouse(id) {
		if (!confirm('Are you sure you want to delete this warehouse? This will also remove all associated product inventory.')) {
			return;
		}

		$.ajax({
			url: App.apiUrl('warehouses/delete/' + id),
			method: 'DELETE',
			contentType: 'application/json'
		}).done(function (response) {
			if (response.success) {
				loadWarehouses(currentPage);
			} else {
				alert('Failed to delete warehouse: ' + response.message);
			}
		}).fail(function () {
			alert('Error deleting warehouse');
		});
	}

	// Event: Add warehouse button
	$('#add-warehouse-btn').on('click', function () {
		resetForm();
		openModal('Add Warehouse');
	});

	// Event: Close modal
	$('.modal-close').on('click', closeModal);

	// Event: Click outside modal to close
	$('#warehouse-modal').on('click', function (e) {
		if ($(e.target).is('#warehouse-modal') || $(e.target).is('.modal-overlay')) {
			closeModal();
		}
	});

	// Event: Cancel button
	$('#modal-cancel').on('click', closeModal);

	// Event: Edit button (delegated)
	$('#warehouses-body').on('click', '.edit-btn', function () {
		var id = $(this).closest('tr').data('id');
		loadWarehouseDetails(id);
	});

	// Event: Delete button (delegated)
	$('#warehouses-body').on('click', '.delete-btn', function () {
		var id = $(this).closest('tr').data('id');
		deleteWarehouse(id);
	});

	// Event: Form submit
	$('#warehouse-form').on('submit', function (e) {
		e.preventDefault();

		var id = $('#warehouse-id').val();
		var payload = {
			name: $('#warehouse-name').val().trim(),
			address: $('#warehouse-address').val().trim()
		};

		// Validate name
		if (!payload.name) {
			$('#name-error').text('Name is required');
			return;
		}

		var url = id
			? App.apiUrl('warehouses/update/' + id)
			: App.apiUrl('warehouses/create');

		var method = 'POST';

		// Clear previous errors
		$('.error-message').empty();

		$.ajax({
			url: url,
			method: method,
			contentType: 'application/json',
			data: JSON.stringify(payload)
		}).done(function (response) {
			if (!response.success) {
				// Handle field-specific errors
				if (response.errors) {
					$.each(response.errors, function (field, message) {
						$('#' + field + '-error').text(message);
					});
				}
				App.showAlert($('#form-message'), response.message || 'Validation failed.', 'error');
				return;
			}

			App.showAlert($('#form-message'), id ? 'Warehouse updated successfully.' : 'Warehouse created successfully.', 'success');
			loadWarehouses(currentPage);
			setTimeout(closeModal, 800);
		}).fail(function (xhr) {
			var response = xhr.responseJSON;
			if (response && response.errors) {
				$.each(response.errors, function (field, message) {
					$('#' + field + '-error').text(message);
				});
				App.showAlert($('#form-message'), response.message || 'Validation failed.', 'error');
			} else {
				App.showAlert($('#form-message'), 'Failed to save warehouse.', 'error');
			}
		});
	});

	// Event: Search
	$('#search-btn').on('click', function () {
		searchTerm = $.trim($('#search-input').val());
		loadWarehouses(1);
	});

	// Event: Search on enter key
	$('#search-input').on('keypress', function (e) {
		if (e.which === 13) {
			searchTerm = $.trim($(this).val());
			loadWarehouses(1);
		}
	});

	// Initial load
	loadWarehouses(1);
})(jQuery);
