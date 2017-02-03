<?php

$start = get_input('start');
$end = get_input('end');

echo elgg_view('analytics/charts/sessions', [
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);

echo elgg_view('analytics/stats', [
	'stats' => [
		'sessions:total',
		'visitors:total',
		'sessions:avg_duration',
		'sessions:per_visitor',
		'page_views:total',
		'page_views:per_session',
		'entity_views:total',
		'entity_views:per_session',
		'posts:total',
		'posts:per_session',
	],
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);