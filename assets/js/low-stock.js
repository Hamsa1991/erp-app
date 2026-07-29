(function ($) {
	'use strict';

	var currentPage = 1;
	var searchTerm = '';

	// Load low stock data
	function loadLowStock(page) {
		currentPage = page || 1;
		var $body = $('#low-stock-body');
		$body.html('<tr><td colspan="7" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('reports/low-stock'), {
			page: currentPage,
			per_page: 10,
			search: searchTerm
		}).done(function (response) {
			if (!response.success) {
				$body.html('<tr><td colspan="7" class="loading">Failed to load report.</td></tr>');
				return;
			}

			var data = response.data;
			$body.empty();

			if (!data.items || !data.items.length) {
				$body.html('<tr><td colspan="7" class="loading">No low stock items found.</td></tr>');
			} else {
				$.each(data.items, function (_, item) {
					var needed = item.alert_quantity - item.quantity;
					var statusClass = needed > 5 ? 'badge-danger' : 'badge-warning';
					var statusText = needed > 5 ? 'Critical' : 'Warning';

					$body.append(
						'<tr>' +
						'<td>' + $('<span>').text(item.product_name).html() + '</td>' +
						'<td>' + $('<span>').text(item.product_code).html() + '</td>' +
						'<td>' + $('<span>').text(item.warehouse_name).html() + '</td>' +
						'<td><strong>' + item.quantity + '</strong></td>' +
						'<td>' + item.alert_quantity + '</td>' +
						'<td><strong class="text-danger">' + needed + '</strong></td>' +
						'<td><span class="badge ' + statusClass + '">' + statusText + '</span></td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadLowStock);
		}).fail(function () {
			$body.html('<tr><td colspan="7" class="loading">Failed to load report.</td></tr>');
		});
	}

	// Export to CSV
	function exportCSV() {
		var $message = $('#export-message');
		$message.hide();

		$.ajax({
			url: App.apiUrl('reports/low-stock/export'),
			method: 'GET',
			dataType: 'json',
			data: {
				search: searchTerm
			}
		}).done(function (response) {
			if (!response.success) {
				App.showAlert($message, 'Failed to export: ' + response.message, 'error');
				return;
			}

			// Create CSV download
			var csvData = response.data;
			var csvContent = "data:text/csv;charset=utf-8,";

			// Add headers
			csvContent += "Product Name,Product Code,Warehouse,Current Quantity,Alert Quantity,Needed Quantity,Status\n";

			// Add data rows
			$.each(csvData, function (_, item) {
				var row = [
					item.product_name,
					item.product_code,
					item.warehouse_name,
					item.quantity,
					item.alert_quantity,
					item.alert_quantity - item.quantity,
					item.quantity < item.alert_quantity ? 'Low Stock' : 'OK'
				];
				csvContent += row.join(',') + '\n';
			});

			// Create download link
			var encodedUri = encodeURI(csvContent);
			var link = document.createElement("a");
			link.setAttribute("href", encodedUri);
			link.setAttribute("download", "low_stock_report_" + new Date().toISOString().split('T')[0] + ".csv");
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);

			App.showAlert($message, 'CSV exported successfully!', 'success');
			setTimeout(function() {
				$message.hide();
			}, 3000);

		}).fail(function () {
			App.showAlert($message, 'Failed to export CSV.', 'error');
		});
	}

	// Event: Export CSV
	$('#export-csv-btn').on('click', function () {
		exportCSV();
	});

	// Event: Search
	$('#search-btn').on('click', function () {
		searchTerm = $.trim($('#search-input').val());
		loadLowStock(1);
	});

	// Event: Search on enter key
	$('#search-input').on('keypress', function (e) {
		if (e.which === 13) {
			searchTerm = $.trim($(this).val());
			loadLowStock(1);
		}
	});

	// Initial load
	loadLowStock(1);
})(jQuery);
