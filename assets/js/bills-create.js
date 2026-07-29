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

		var discount = parseFloat($('#discount').val()) || 0;
		var total = Math.max(0, subtotal - discount);

		$('#subtotal').text(App.formatMoney(subtotal));
		$('#discount-display').text(App.formatMoney(discount));
		$('#grand-total').text(App.formatMoney(total));
	}

	function bindLineEvents($line) {
		$line.find('.line-product').on('change', function () {
			var $opt = $(this).find('option:selected');
			$line.find('.line-price').val($opt.data('price') || '');
			$line.find('.line-stock').val($opt.data('stock') !== undefined ? $opt.data('stock') : '');
			recalculateTotals();
		});

		$line.find('.line-quantity, .line-price').on('input', recalculateTotals);

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

	$('#warehouse-id').on('change', function () {
		var warehouseId = $(this).val();
		loadWarehouseInventory(warehouseId);
	});

	$('#discount').on('input', recalculateTotals);

	$('#add-line-btn').on('click', addLineItem);

	bindLineEvents($('.line-item').first());
	updateRemoveButtons();

	$('#bill-form').on('submit', function (e) {
		e.preventDefault();

		var details = [];
		var valid = true;

		$('.line-item').each(function () {
			var productId = $(this).find('.line-product').val();
			var quantity = parseInt($(this).find('.line-quantity').val(), 10);
			var price = parseFloat($(this).find('.line-price').val());

			if (!productId || quantity < 1 || isNaN(price)) {
				valid = false;
				return false;
			}

			details.push({
				product_id: parseInt(productId, 10),
				quantity: quantity,
				price: price
			});
		});

		if (!valid || !details.length) {
			App.showAlert($('#form-message'), 'Please fill in all product lines.', 'error');
			return;
		}

		var payload = {
			client_id: parseInt($('#client-id').val(), 10),
			warehouse_id: parseInt($('#warehouse-id').val(), 10),
			discount: parseFloat($('#discount').val()) || 0,
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
			setTimeout(function () {
				window.location.href = App.siteUrl + 'bills/detail/' + response.data.id;
			}, 1000);
		}).fail(function (xhr) {
			var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to create bill.';
			App.showAlert($('#form-message'), msg, 'error');
		});
	});
})(jQuery);
