<?php

$start = get_input('start');
$end = get_input('end');

echo elgg_view('analytics/charts/page_views', [
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);

echo elgg_view('analytics/stats', [
	'stats' => [
		'page_views:total',
		'page_views:unique_pages',
		'page_views:per_visitor',
	],
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);