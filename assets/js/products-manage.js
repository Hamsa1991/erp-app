(function ($) {
	'use strict';

	var currentPage = 1;
	var searchTerm = '';

	function loadProducts(page) {
		currentPage = page || 1;
		var $body = $('#products-body');
		$body.html('<tr><td colspan="5" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('products/manage/data'), {
			page: currentPage,
			per_page: 10,
			search: searchTerm
		}).done(function (response) {
			if (!response.success) {
				$body.html('<tr><td colspan="5" class="loading">Failed to load products.</td></tr>');
				return;
			}

			var data = response.data;
			$body.empty();

			if (!data.items.length) {
				$body.html('<tr><td colspan="5" class="loading">No products found.</td></tr>');
			} else {
				$.each(data.items, function (_, item) {
					var statusClass = item.is_available == 1 ? 'badge-success' : 'badge-danger';
					var statusText = item.is_available == 1 ? 'Available' : 'Disabled';
					var toggleLabel = item.is_available == 1 ? 'Disable' : 'Enable';

					$body.append(
						'<tr data-id="' + item.id + '">' +
						'<td>' + $('<span>').text(item.name).html() + '</td>' +
						'<td>' + $('<span>').text(item.code).html() + '</td>' +
						'<td>' + App.formatMoney(item.price) + '</td>' +
						'<td><span class="badge ' + statusClass + '">' + statusText + '</span></td>' +
						'<td class="action-buttons">' +
						'<button type="button" class="btn btn-sm btn-outline edit-btn">Edit</button> ' +
						'<button type="button" class="btn btn-sm btn-outline toggle-btn">' + toggleLabel + '</button>' +
						'</td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadProducts);
		});
	}

	function resetForm() {
		$('#product-id').val('');
		$('#product-name').val('');
		$('#product-code').val('');
		$('#product-price').val('');
		$('#modal-title').text('Add Product');
		$('.inventory-id').val('');
		$('.inventory-quantity').val(0);
		$('.inventory-alert').val(0);
		$('#form-message').hide();
	}

	function openModal(title) {
		$('#modal-title').text(title);
		$('#product-modal').show();
	}

	function closeModal() {
		$('#product-modal').hide();
		resetForm();
	}

	function loadProductDetails(id) {
		$.getJSON(App.apiUrl('api/products/' + id)).done(function (response) {
			if (!response.success) return;

			var product = response.data;
			$('#product-id').val(product.id);
			$('#product-name').val(product.name);
			$('#product-code').val(product.code);
			$('#product-price').val(product.price);

			$('.inventory-row').each(function () {
				var warehouseId = $(this).data('warehouse-id');
				var inv = (product.inventory || []).find(function (i) {
					return parseInt(i.warehouse_id, 10) === parseInt(warehouseId, 10);
				});

				$(this).find('.inventory-id').val(inv ? inv.id : '');
				$(this).find('.inventory-quantity').val(inv ? inv.quantity : 0);
				$(this).find('.inventory-alert').val(inv ? inv.alert_quantity : 0);
			});

			openModal('Edit Product');
		});
	}

	function saveInventory(productId) {
		var promises = [];

		$('.inventory-row').each(function () {
			var $row = $(this);
			var payload = {
				product_id: productId,
				warehouse_id: $row.data('warehouse-id'),
				quantity: parseInt($row.find('.inventory-quantity').val(), 10) || 0,
				alert_quantity: parseInt($row.find('.inventory-alert').val(), 10) || 0
			};

			promises.push(
				$.ajax({
					url: App.apiUrl('api/inventory/upsert'),
					method: 'POST',
					contentType: 'application/json',
					data: JSON.stringify(payload)
				})
			);
		});

		return $.when.apply($, promises);
	}

	$('#add-product-btn').on('click', function () {
		resetForm();
		openModal('Add Product');
	});

	$('.modal-close').on('click', closeModal);

	$('#product-modal').on('click', function (e) {
		if ($(e.target).is('#product-modal')) closeModal();
	});

	$('#products-body').on('click', '.edit-btn', function () {
		var id = $(this).closest('tr').data('id');
		loadProductDetails(id);
	});

	$('#products-body').on('click', '.toggle-btn', function () {
		var id = $(this).closest('tr').data('id');
		$.post(App.apiUrl('products/toggle/' + id)).done(function (response) {
			if (response.success) loadProducts(currentPage);
		});
	});

	$('#product-form').on('submit', function (e) {
		e.preventDefault();

		var id = $('#product-id').val();
		var payload = {
			name: $('#product-name').val(),
			code: $('#product-code').val(),
			price: parseFloat($('#product-price').val())
		};

		var url = id
			? App.apiUrl('api/products/update/' + id)
			: App.apiUrl('api/products/create');

		var method = id ? 'POST' : 'POST';

		$.ajax({
			url: url,
			method: method,
			contentType: 'application/json',
			data: JSON.stringify(payload)
		}).done(function (response) {
			if (!response.success) {
				App.showAlert($('#form-message'), response.message, 'error');
				return;
			}

			var productId = id || response.data.id;

			saveInventory(productId).always(function () {
				App.showAlert($('#form-message'), 'Product saved successfully.', 'success');
				loadProducts(currentPage);
				setTimeout(closeModal, 800);
			});
		}).fail(function (xhr) {
			var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Save failed.';
			App.showAlert($('#form-message'), msg, 'error');
		});
	});

	$('#search-btn').on('click', function () {
		searchTerm = $.trim($('#search-input').val());
		loadProducts(1);
	});

	loadProducts(1);
})(jQuery);
