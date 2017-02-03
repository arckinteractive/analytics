<?php

/**
 * User and usage statistics
 *
 * @author Ismayil Khayredinov <ismayil@arckinteractive.com>
 */
require_once __DIR__ . '/autoloader.php';

use ArckInteractive\Analytics\LoggingService;
use ArckInteractive\Analytics\Menus;
use ArckInteractive\Analytics\Router;

elgg_register_event_handler('init', 'system', function() {

	/*
	 * Log page view
	 */
	elgg_register_action('analytics/log_page_view', __DIR__ . '/actions/analytics/log_page_view.php', 'public');
	elgg_extend_view('page/elements/foot', 'analytics/logger/page');

	/*
	 * Log entity summary/profile views
	 */
	elgg_register_action('analytics/log_entity_view', __DIR__ . '/actions/analytics/log_entity_view.php', 'public');
	elgg_register_plugin_hook_handler('view', 'profile/details', [LoggingService::class, 'logProfileViewHandler']);
	elgg_register_plugin_hook_handler('view', 'groups/profile/layout', [LoggingService::class, 'logProfileViewHandler']);
	elgg_register_plugin_hook_handler('view', 'object/default', [LoggingService::class, 'logListingViewHandler']);
	$subtypes = (array) get_registered_entity_types('object');
	foreach ($subtypes as $subtype) {
		elgg_register_plugin_hook_handler('view', "object/$subtype", [LoggingService::class, 'logListingViewHandler']);
	}
	$translations = [];
	foreach ($subtypes as $subtype) {
		$translations["analytics:stat:posts:$subtype"] = elgg_echo("item:object:$subtype");
	}
	add_translation(get_current_language(), $translations);

	/*
	 * Log events
	 */
	elgg_register_action('analytics/log_event', __DIR__ . '/actions/analytics/log_event.php', 'public');
	elgg_extend_view('elgg.js', 'analytics/logger/events.js');

	elgg_register_event_handler('all', 'object', [LoggingService::class, 'entityEventHandler'], 999);
	elgg_register_event_handler('all', 'group', [LoggingService::class, 'entityEventHandler'], 999);
	elgg_register_event_handler('all', 'user', [LoggingService::class, 'entityEventHandler'], 999);
	elgg_register_event_handler('all', 'relationship', [LoggingService::class, 'relationshipEventHandler'], 999);

	/*
	 * Log benchmarks
	 */
	elgg_register_plugin_hook_handler('cron', 'daily', [LoggingService::class, 'logDailyBenchmarks']);
	
	/*
	 * Reports
	 */
	elgg_register_page_handler('analytics', [Router::class, 'analyticsHandler']);

	/*
	 * Widgets
	 */
	$types = [
		'a_sessions',
		'a_visitors',
		'a_page_views',
		'a_entity_views',
		'a_posts',
		'a_registration',
		'a_geography',
		'a_top_pages',
		'a_top_posts',
		'a_active_users',
	];

	foreach ($types as $type) {
		$name = elgg_echo("analytics:widget:$type");
		elgg_register_widget_type($type, $name, null, ['analytics', 'admin'], true);
	}

	/*
	 * Assets
	 */
	elgg_define_js('chartjs', [
		'src' => elgg_get_simplecache_url('chartjs/Chart.js'),
		'exports' => 'Chart',
	]);

	elgg_extend_view('elgg.css', 'analytics/styles.css');
	elgg_extend_view('admin.css', 'analytics/styles.css');

	elgg_register_ajax_view('analytics/charts/map');
	
	/*
	 * Menus
	 */
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);
	
}, 1001);

elgg_register_event_handler('upgrade', 'system', function() {
	if (!elgg_is_admin_logged_in()) {
		return;
	}
	require __DIR__ . '/lib/upgrades.php';
});
