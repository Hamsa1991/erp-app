(function ($) {
	'use strict';

	var currentPage = 1;

	function loadBills(page) {
		currentPage = page || 1;
		var $body = $('#bills-body');
		$body.html('<tr><td colspan="8" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('bills/data'), {
			page: currentPage,
			per_page: 10
		}).done(function (response) {
			if (!response.success) {
				$body.html('<tr><td colspan="8" class="loading">Failed to load bills.</td></tr>');
				return;
			}

			var data = response.data;
			$body.empty();

			if (!data.items.length) {
				$body.html('<tr><td colspan="8" class="loading">No bills found.</td></tr>');
			} else {
				$.each(data.items, function (_, item) {
					var clientName = item.client_first_name + ' ' + item.client_last_name;
					var detailUrl = App.apiUrl('bills/detail/' + item.id).replace('/index.php/', '/index.php/');

					$body.append(
						'<tr>' +
						'<td>#' + item.id + '</td>' +
						'<td>' + $('<span>').text(clientName).html() + '</td>' +
						'<td>' + $('<span>').text(item.warehouse_name).html() + '</td>' +
						'<td>' + App.formatMoney(item.total) + '</td>' +
						'<td>' + App.formatMoney(item.discount) + '</td>' +
						'<td><strong>' + App.formatMoney(item.total_after_discount) + '</strong></td>' +
						'<td>' + (item.created_at || '—') + '</td>' +
						'<td><a href="' +  'bills/detail/' + item.id + '" class="btn btn-sm btn-outline">View</a></td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadBills);
		});
	}

	loadBills(1);
})(jQuery);
