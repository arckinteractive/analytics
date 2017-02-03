define(function (require) {

	var elgg = require('elgg');
	require('elgg/ready');

	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');

	var table = {
		init: function () {
			$('.analytics-table[data-src]:not(.elgg-state-ready)').each(function () {
				var $table = $(this);
				$table.addClass('elgg-state-ready');
				table.draw($table);
				elgg.register_hook_handler('analytics', 'setFilter', function (hook, type, query) {
					$table.data('query', query);
					table.draw($table);
				});
			});
		},
		draw: function ($table) {
			if (!$table.length) {
				return;
			}

			var src = $table.data('src');
			var query = $table.data('query');

			var ajax = new Ajax();
			ajax.path(src, {
				data: query
			}).done(function (response) {
				$table.find('tbody').html('');
				$.each(response, function(i, row) {
					var $row = $('<tr />');
					$table.append($row);

					$.each(row, function(k, cell) {
						var $cell = $('<td />').html(cell);
						$row.append($cell);
					});
				});
			});
		}
	};

	table.init();

	return table;
});