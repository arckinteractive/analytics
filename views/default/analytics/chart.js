define(function (require) {

	var elgg = require('elgg');
	require('elgg/ready');

	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');

	window.Chart = require('chartjs');

	var chart = {
		init: function () {
			$('canvas[data-chart-type]:not(.elgg-state-ready)').each(function () {
				var $canvas = $(this);
				chart.draw($canvas);
				$canvas.addClass('elgg-state-ready');
				elgg.register_hook_handler('analytics', 'setFilter', function (hook, type, query) {
					$canvas.data('query', query);
					chart.draw($canvas);
				});
			});
		},
		draw: function ($canvas) {
			if (!$canvas.length) {
				return;
			}

			var src = $canvas.data('src');
			var query = $canvas.data('query');

			var ajax = new Ajax();
			ajax.path(src, {
				data: query
			}).done(function (response) {
				if ($canvas.data('chartjsObj')) {
					$canvas.data('chartjsObj').destroy();
				}
				var ct = new Chart($canvas, {
					type: $canvas.data('chartType'),
					data: response,
					options: $canvas.data('chartOptions')
				});
				$canvas.data('chartjsObj', ct);
			});
		}
	};

	chart.init();

	return chart;
});