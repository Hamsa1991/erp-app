(function ($) {
	'use strict';

	var currentPage = 1;
	var searchTerm = '';

	function loadWarehouses(page) {
		currentPage = page || 1;
		var $body = $('#warehouses-body');
		$body.html('<tr><td colspan="5" class="loading">Loading...</td></tr>');

		$.getJSON(App.apiUrl('warehouses/data'), {
			page: currentPage,
			per_page: 10,
			search: searchTerm
		}).done(function (response) {
			if (!response.success) {
				$body.html('<tr><td colspan="5" class="loading">Failed to load warehouses.</td></tr>');
				return;
			}

			var data = response.data;
			$body.empty();

			if (!data.items.length) {
				$body.html('<tr><td colspan="5" class="loading">No warehouses found.</td></tr>');
			} else {
				$.each(data.items, function (_, item) {
					$body.append(
						'<tr>' +
						'<td>' + item.id + '</td>' +
						'<td>' + $('<span>').text(item.name).html() + '</td>' +
						'<td>' + $('<span>').text(item.address || '—').html() + '</td>' +
						'<td>' + (item.created_at || '—') + '</td>' +
						'<td>' + (item.updated_at || '—') + '</td>' +
						'</tr>'
					);
				});
			}

			App.renderPagination($('#pagination'), data, loadWarehouses);
		});
	}

	$('#search-btn').on('click', function () {
		searchTerm = $.trim($('#search-input').val());
		loadWarehouses(1);
	});

	$('#search-input').on('keypress', function (e) {
		if (e.which === 13) {
			searchTerm = $.trim($(this).val());
			loadWarehouses(1);
		}
	});

	loadWarehouses(1);
})(jQuery);
