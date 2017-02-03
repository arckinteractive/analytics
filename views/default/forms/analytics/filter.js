define(function(require) {

	var elgg = require('elgg');
	var $ = require('jquery');

	var Ajax = require('elgg/Ajax');
	var ajax = new Ajax();

	$(document).on('submit', '.elgg-form-analytics-filter', function(e) {
		e.preventDefault();
		var $form = $(this);
		var data = ajax.objectify($form);
		elgg.trigger_hook('analytics', 'setFilter', data);
	});
});