define(function (require) {

	var elgg = require('elgg');
	require('elgg/ready');

	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');
	var ajax = new Ajax();
	
	elgg.register_hook_handler('analytics', 'setFilter', function (hook, type, query) {

		ajax.view('analytics/charts/map', {
			data: query
		}).done(function (output) {
			$('#analytics-geography').closest('.maps-container').replaceWith($(output));
		});

	});

});