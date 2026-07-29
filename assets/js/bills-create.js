(function ($) {
	'use strict';

	var inventoryCache = {};

	function loadWarehouseInventory(warehouseId) {
		if (!warehouseId) return;

		$.getJSON(App.apiUrl('api/inventory'), { warehouse_id: warehouseId })
			.done(function (response) {
				if (response.success) {
					inventoryCache[warehouseId] = response.data;
					updateProductSelects(warehouseId);
				}
			});
	}

	function updateProductSelects(warehouseId) {
		var items = inventoryCache[warehouseId] || [];

		$('.line-product').each(function () {
			var $select = $(this);
			var currentVal = $select.val();
			$select.empty().append('<option value="">Select product...</option>');

			$.each(items, function (_, item) {
				if (item.is_available == 0) return;
				$select.append(
					$('<option>', {
						value: item.product_id,
						text: item.product_name + ' (' + item.product_code + ') — Stock: ' + item.quantity,
						'data-price': item.product_price,
						'data-stock': item.quantity
					})
				);
			});

			if (currentVal) $select.val(currentVal);
		});
	}

	function recalculateTotals() {
		var subtotal = 0;

		$('.line-item').each(function () {
			var qty = parseInt($(this).find('.line-quantity').val(), 10) || 0;
			var price = parseFloat($(this).find('.line-price').val()) || 0;
			subtotal += qty * price;
		});

		// Get discount as percentage (0-100)
		var discountPercent = parseFloat($('#discount').val()) || 0;

		// Validate discount is between 0 and 100
		if (discountPercent < 0) discountPercent = 0;
		if (discountPercent > 100) discountPercent = 100;
		$('#discount').val(discountPercent);

		// Calculate discount amount as percentage of subtotal and round to 2 decimal places
		var discountAmount = Math.round((subtotal * discountPercent) / 100 * 100) / 100;
		var total = Math.round(Math.max(0, subtotal - discountAmount) * 100) / 100;

		$('#subtotal').text(App.formatMoney(subtotal));
		$('#discount-display').text(App.formatMoney(discountAmount));
		$('#grand-total').text(App.formatMoney(total));
	}

	function bindLineEvents($line) {
		$line.find('.line-product').on('change', function () {
			var $opt = $(this).find('option:selected');
			var stock = parseInt($opt.data('stock'), 10) || 0;
			var currentQty = parseInt($line.find('.line-quantity').val(), 10) || 1;

			// Validate quantity against available stock
			if (currentQty > stock && stock > 0) {
				$line.find('.line-quantity').val(stock);
				App.showAlert($('#form-message'), 'Quantity adjusted to available stock: ' + stock, 'warning');
			}

			$line.find('.line-price').val($opt.data('price') || '');
			$line.find('.line-stock').val(stock);
			recalculateTotals();
		});

		$line.find('.line-quantity').on('input', function () {
			var stock = parseInt($line.find('.line-stock').val(), 10) || 0;
			var qty = parseInt($(this).val(), 10) || 1;

			// Validate quantity doesn't exceed stock
			if (qty > stock && stock > 0) {
				$(this).val(stock);
				App.showAlert($('#form-message'), 'Quantity cannot exceed available stock: ' + stock, 'warning');
			}

			recalculateTotals();
		});

		$line.find('.line-price').on('input', recalculateTotals);

		$line.find('.remove-line').on('click', function () {
			$line.remove();
			updateRemoveButtons();
			recalculateTotals();
		});
	}

	function updateRemoveButtons() {
		var $lines = $('.line-item');
		$lines.find('.remove-line').prop('disabled', $lines.length <= 1);
	}

	function addLineItem() {
		var $first = $('.line-item').first();
		var $clone = $first.clone();
		$clone.find('select').val('');
		$clone.find('.line-quantity').val(1);
		$clone.find('.line-price').val('');
		$clone.find('.line-stock').val('');
		$('#line-items').append($clone);

		var warehouseId = $('#warehouse-id').val();
		if (warehouseId) updateProductSelects(warehouseId);

		bindLineEvents($clone);
		updateRemoveButtons();
	}

	// Validate discount input
	$('#discount').on('input', function () {
		var val = parseFloat($(this).val()) || 0;

		// Clamp value between 0 and 100
		if (val < 0) $(this).val(0);
		if (val > 100) $(this).val(100);

		recalculateTotals();
	});

	// Also validate on blur to ensure value is in range
	$('#discount').on('blur', function () {
		var val = parseFloat($(this).val()) || 0;

		if (val < 0) $(this).val(0);
		if (val > 100) $(this).val(100);

		recalculateTotals();
	});

	$('#warehouse-id').on('change', function () {
		var warehouseId = $(this).val();
		loadWarehouseInventory(warehouseId);
	});

	$('#add-line-btn').on('click', addLineItem);

	bindLineEvents($('.line-item').first());
	updateRemoveButtons();

	$('#bill-form').on('submit', function (e) {
		e.preventDefault();

		var details = [];
		var valid = true;
		var errorMsg = '';

		$('.line-item').each(function () {
			var productId = $(this).find('.line-product').val();
			var quantity = parseInt($(this).find('.line-quantity').val(), 10);
			var price = parseFloat($(this).find('.line-price').val());
			var stock = parseInt($(this).find('.line-stock').val(), 10) || 0;

			if (!productId) {
				valid = false;
				errorMsg = 'Please select a product for all lines.';
				return false;
			}

			if (quantity < 1 || isNaN(quantity)) {
				valid = false;
				errorMsg = 'Please enter a valid quantity for all lines.';
				return false;
			}

			if (quantity > stock && stock > 0) {
				valid = false;
				errorMsg = 'Quantity exceeds available stock for one or more products.';
				return false;
			}

			if (isNaN(price) || price < 0) {
				valid = false;
				errorMsg = 'Please enter a valid price for all lines.';
				return false;
			}

			details.push({
				product_id: parseInt(productId, 10),
				quantity: quantity,
				price: Math.round(price * 100) / 100
			});
		});

		if (!valid || !details.length) {
			App.showAlert($('#form-message'), errorMsg || 'Please fill in all product lines correctly.', 'error');
			return;
		}

		var discountPercent = parseFloat($('#discount').val()) || 0;

		// Validate discount is between 0 and 100
		if (discountPercent < 0 || discountPercent > 100) {
			App.showAlert($('#form-message'), 'Discount must be between 0 and 100 percent.', 'error');
			return;
		}

		var payload = {
			client_id: parseInt($('#client-id').val(), 10),
			warehouse_id: parseInt($('#warehouse-id').val(), 10),
			discount: discountPercent,
			details: details
		};

		$.ajax({
			url: App.apiUrl('api/bills/create'),
			method: 'POST',
			contentType: 'application/json',
			data: JSON.stringify(payload)
		}).done(function (response) {
			if (!response.success) {
				App.showAlert($('#form-message'), response.message, 'error');
				return;
			}

			App.showAlert($('#form-message'), 'Bill created successfully!', 'success');

			// Use the redirect URL from the response if available
			if (response.data && response.data.redirect_url) {
				setTimeout(function () {
					window.location.href = response.data.redirect_url;
				}, 1000);
			} else {
				// Fallback to the old way
				setTimeout(function () {
					window.location.href = '/index.php/bills/detail/' + response.data.id;
				}, 1000);
			}
		}).fail(function (xhr) {
			var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to create bill.';
			App.showAlert($('#form-message'), msg, 'error');
		});
	});
})(jQuery);
