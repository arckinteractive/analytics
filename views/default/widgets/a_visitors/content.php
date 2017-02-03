<?php

$start = get_input('start');
$end = get_input('end');

echo elgg_view('analytics/charts/visitors', [
	'start' => get_input('start'),
	'end' => get_input('end'),
]);

echo elgg_view('analytics/stats', [
	'stats' => [
		'visitors:total',
		'registered_users:total',
		'registered_users:percentage',
		'guest_users:total',
		'page_views:per_visitor',
		'entity_views:per_visitor',
		'posts:per_visitor',
	],
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);
