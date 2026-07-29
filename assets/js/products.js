(function ($) {
	'use strict';

	var currentPage = 1;
	var searchTerm = '';

	function loadProducts(page) {
		currentPage = page || 1;
		var $body = $('#products-body');
		$body.html('<tr><td colspan="5" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('products/data'), {
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
					$body.append(
						'<tr>' +
						'<td>' + $('<span>').text(item.name).html() + '</td>' +
						'<td>' + $('<span>').text(item.code).html() + '</td>' +
						'<td>' + App.formatMoney(item.price) + '</td>' +
						'<td>' + $('<span>').text(item.warehouse_name).html() + '</td>' +
						'<td>' + item.quantity + '</td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadProducts);
		}).fail(function () {
			$body.html('<tr><td colspan="5" class="loading">Failed to load products.</td></tr>');
		});
	}

	$('#search-btn').on('click', function () {
		searchTerm = $.trim($('#search-input').val());
		loadProducts(1);
	});

	$('#search-input').on('keypress', function (e) {
		if (e.which === 13) {
			searchTerm = $.trim($(this).val());
			loadProducts(1);
		}
	});

	loadProducts(1);
})(jQuery);
