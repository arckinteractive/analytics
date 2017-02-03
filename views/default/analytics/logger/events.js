require(['elgg', 'jquery', 'analytics/logger', 'elgg/ready'], function (elgg, $, logger) {

	function getTarget($elem) {
		var target = '';

		var id = $($elem).attr('id');
		if (id) {
			target = '#' + id;
		}

		var cl = $elem.attr('class');
		if (cl) {
			target += '.' + cl.split(' ').join('.');
		}

		return target;
	}

	$(document).on('click', 'a', function (e) {
		if (e.isDefaultPrevented()) {
			return;
		}

		if (!$(e.target).attr('href')) {
			return;
		}
		
		logger.logEvent({
			event: 'click',
			target: getTarget($(e.target)),
			href: $(e.target).attr('href'),
			description: $(e.target).text()
		});
	});

	$(document).on('submit', 'form', function (e) {

		if (e.isDefaultPrevented()) {
			return;
		}

		logger.logEvent({
			event: 'submit',
			target: getTarget($(e.target)),
			href: $(e.target).attr('action')
		});
	});

});

