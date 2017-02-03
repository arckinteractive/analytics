<?php

$start = get_input('start');
$end = get_input('end');

echo elgg_view('analytics/charts/geography', [
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);

echo elgg_view('analytics/stats', [
	'stats' => [
		'countries:total',
		'cities:total',
	],
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);
