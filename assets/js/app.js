(function ($) {
	'use strict';

	window.App = {
		apiUrl: function (path) {
			return window.APP.baseUrl + 'index.php/' + path.replace(/^\//, '');
		},

		formatMoney: function (value) {
			return parseFloat(value || 0).toFixed(2);
		},

		showAlert: function ($el, message, type) {
			$el.removeClass('alert-error alert-success')
				.addClass(type === 'success' ? 'alert-success' : 'alert-error')
				.text(message)
				.show();
		},

		renderPagination: function ($container, data, onPage) {
			$container.empty();

			if (!data || data.total_pages <= 1) {
				return;
			}

			var current = data.page;
			var total = data.total_pages;

			var $prev = $('<button type="button">&laquo; Prev</button>')
				.prop('disabled', current <= 1)
				.on('click', function () { onPage(current - 1); });
			$container.append($prev);

			for (var i = 1; i <= total; i++) {
				var $btn = $('<button type="button">' + i + '</button>')
					.toggleClass('active', i === current)
					.on('click', (function (page) {
						return function () { onPage(page); };
					})(i));
				$container.append($btn);
			}

			var $next = $('<button type="button">Next &raquo;</button>')
				.prop('disabled', current >= total)
				.on('click', function () { onPage(current + 1); });
			$container.append($next);
		}
	};
})(jQuery);
