<?php

if (!elgg_is_active_plugin('hypeMapsOpen')) {
	return;
}

echo elgg_view('page/components/map', [
	'id' => 'analytics-geography',
	'src' => elgg_http_add_url_query_elements('analytics/datasets/geography', [
		'view' => 'json',
		'start' => elgg_extract('start', $vars),
		'end' => elgg_extract('end', $vars),
		'location' => elgg_extract('location', $vars),
		'radius' => elgg_extract('radius', $vars),
		'users' => elgg_extract('users', $vars),
	]),
	'show_search' => false,
	'zoom' => 5,
]);