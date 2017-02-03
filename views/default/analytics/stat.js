define(function (require) {

	var elgg = require('elgg');
	require('elgg/ready');

	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');

	var stat = {
		init: function () {
			$('.analytics-stat[data-stat]:not(.elgg-state-ready)').each(function () {
				var $elem = $(this);
				$elem.addClass('elgg-state-ready');
				stat.load($elem);
				elgg.register_hook_handler('analytics', 'setFilter', function (hook, type, query) {
					$elem.data('query', query);
					stat.load($elem);
				});
			});
		},
		load: function ($elem) {
			if (!$elem.length) {
				return;
			}

			var src = $elem.data('src');
			var query = $elem.data('query');

			var ajax = new Ajax();
			ajax.path(src, {
				data: query
			}).done(function (response) {
				$elem.find('.analytics-stat-value').text(response.value);
				$elem.find('.analytics-stat-label').text(response.label);
			});
		}
	};

	stat.init();

	return stat;
});