define(function (require) {

	var elgg = require('elgg');
	var $ = require('jquery');

	var Ajax = require('elgg/Ajax');
	var ajax = new Ajax(false);

	var logger = {
		logPageView: function (data) {
			data = data || {};

			data = $.extend(true, {}, {
				page_url: window.location.href,
				page_title: document.title,
				entity_guid: 0,
				page_owner_guid: elgg.get_page_owner_guid(),
				referrer_url: document.referrer,
				time: Math.floor(Date.now() / 1000)
			}, data);

			return ajax.action('analytics/log_page_view', {
				data: data
			});
		},
		logEntityView: function (data) {
			data = data || {};

			data = $.extend({}, {
				page_url: window.location.href,
				entity_guid: 0,
				view_name: '',
				full_view: false,
				time: Math.floor(Date.now() / 1000)
			}, data);

			return ajax.action('analytics/log_entity_view', {
				data: data
			});
		},
		logEvent: function (data) {
			data = data || {};

			data = $.extend({}, {
				page_url: window.location.href,
				event: '',
				target: '',
				description: '',
				href: '',
				time: Math.floor(Date.now() / 1000)
			}, data);

			return ajax.action('analytics/log_event', {
				data: data
			});
		}
	};

	return logger;
});