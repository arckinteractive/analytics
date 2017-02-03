<?php

$start = get_input('start');
$end = get_input('end');

echo elgg_view('analytics/charts/entity_views', [
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);

echo elgg_view('analytics/stats', [
	'stats' => [
		'entity_views:total',
		'entity_views:users',
		'entity_views:groups',
		'entity_views:objects',
		'entity_views:per_visitor',
	],
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);