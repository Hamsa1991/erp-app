(function ($) {
	'use strict';

	var currentPage = 1;
	var searchTerm = '';

	// Load clients function
	function loadClients(page) {
		currentPage = page || 1;
		var $body = $('#clients-body');
		$body.html('<tr><td colspan="6" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('clients'), {
			page: currentPage,
			per_page: 10,
			search: searchTerm
		}).done(function (response) {
			if (!response.success) {
				$body.html('<tr><td colspan="6" class="loading">Failed to load clients.</td></tr>');
				return;
			}

			var data = response.data;
			$body.empty();

			if (!data.items || !data.items.length) {
				$body.html('<tr><td colspan="6" class="loading">No clients found.</td></tr>');
			} else {
				$.each(data.items, function (_, client) {
					$body.append(
						'<tr data-id="' + client.id + '">' +
						'<td>' + $('<span>').text(client.id).html() + '</td>' +
						'<td>' + $('<span>').text(client.first_name).html() + '</td>' +
						'<td>' + $('<span>').text(client.last_name).html() + '</td>' +
						'<td>' + $('<span>').text(client.email || '-').html() + '</td>' +
						'<td>' + $('<span>').text(client.phone || '-').html() + '</td>' +
						'<td class="action-buttons">' +
						'<button type="button" class="btn btn-sm btn-outline edit-btn">Edit</button> ' +
						'<button type="button" class="btn btn-sm btn-outline delete-btn">Delete</button>' +
						'</td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadClients);
		}).fail(function () {
			$body.html('<tr><td colspan="6" class="loading">Failed to load clients.</td></tr>');
		});
	}

	// Reset form function
	function resetForm() {
		$('#client-id').val('');
		$('#first_name').val('');
		$('#last_name').val('');
		$('#email').val('');
		$('#phone').val('');
		$('#modal-title').text('Add Client');
		$('.error-message').empty();
		$('#form-message').hide();
	}

	// Open modal function
	function openModal(title) {
		$('#modal-title').text(title);
		$('#client-modal').show();
	}

	// Close modal function
	function closeModal() {
		$('#client-modal').hide();
		resetForm();
	}

	// Load client details for editing
	function loadClientDetails(id) {
		$.getJSON(App.apiUrl('clients/' + id)).done(function (response) {
			if (!response.success) {
				App.showAlert($('#form-message'), 'Failed to load client details.', 'error');
				return;
			}

			var client = response.data;
			$('#client-id').val(client.id);
			$('#first_name').val(client.first_name);
			$('#last_name').val(client.last_name);
			$('#email').val(client.email || '');
			$('#phone').val(client.phone || '');

			openModal('Edit Client');
		}).fail(function () {
			App.showAlert($('#form-message'), 'Failed to load client details.', 'error');
		});
	}

	// Delete client function
	function deleteClient(id) {
		if (!confirm('Are you sure you want to delete this client?')) {
			return;
		}

		$.ajax({
			url: App.apiUrl('clients/delete/' + id),
			method: 'DELETE',
			contentType: 'application/json'
		}).done(function (response) {
			if (response.success) {
				loadClients(currentPage);
			} else {
				alert('Failed to delete client: ' + response.message);
			}
		}).fail(function () {
			alert('Error deleting client');
		});
	}

	// Event: Add client button
	$('#add-client-btn').on('click', function () {
		resetForm();
		openModal('Add Client');
	});

	// Event: Close modal
	$('.modal-close').on('click', closeModal);

	// Event: Click outside modal to close
	$('#client-modal').on('click', function (e) {
		if ($(e.target).is('#client-modal')) closeModal();
	});

	// Event: Cancel button
	$('#modal-cancel').on('click', closeModal);

	// Event: Edit button (delegated)
	$('#clients-body').on('click', '.edit-btn', function () {
		var id = $(this).closest('tr').data('id');
		loadClientDetails(id);
	});

	// Event: Delete button (delegated)
	$('#clients-body').on('click', '.delete-btn', function () {
		var id = $(this).closest('tr').data('id');
		deleteClient(id);
	});

	// Event: Form submit
	$('#client-form').on('submit', function (e) {
		e.preventDefault();

		var id = $('#client-id').val();
		var payload = {
			first_name: $('#first_name').val(),
			last_name: $('#last_name').val(),
			email: $('#email').val(),
			phone: $('#phone').val()
		};

		var url = id
			? App.apiUrl('clients/update/' + id)
			: App.apiUrl('clients/create');

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

			App.showAlert($('#form-message'), id ? 'Client updated successfully.' : 'Client created successfully.', 'success');
			loadClients(currentPage);
			setTimeout(closeModal, 800);
		}).fail(function (xhr) {
			var response = xhr.responseJSON;
			if (response && response.errors) {
				$.each(response.errors, function (field, message) {
					$('#' + field + '-error').text(message);
				});
				App.showAlert($('#form-message'), response.message || 'Validation failed.', 'error');
			} else {
				App.showAlert($('#form-message'), 'Failed to save client.', 'error');
			}
		});
	});

	// Event: Search
	$('#search-btn').on('click', function () {
		searchTerm = $.trim($('#search-input').val());
		loadClients(1);
	});

	// Event: Search on enter key
	$('#search-input').on('keypress', function (e) {
		if (e.which === 13) {
			searchTerm = $.trim($(this).val());
			loadClients(1);
		}
	});

	// Initial load
	loadClients(1);
})(jQuery);
