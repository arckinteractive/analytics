<?php

$start = get_input('start');
$end = get_input('end');

echo elgg_view('analytics/charts/posts', [
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);

$stats = [
	'posts:total',
	'posts:per_visitor',
];

$subtypes = get_registered_entity_types('object');
foreach ($subtypes as $subtype) {
	$stats[] = "posts:$subtype";
}

echo elgg_view('analytics/stats', [
	'stats' => $stats,
	'start' => get_input('start'),
	'end' => get_input('end'),
	'location' => get_input('location'),
	'radius' => get_input('radius'),
	'users' => get_input('users'),
]);