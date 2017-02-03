<?php

use ArckInteractive\Analytics\TimeFrame;

$start = get_input('start');
$end = get_input('end');

$time = new TimeFrame($start, $end, null, false);

echo elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('analytics:label:start'),
	'name' => 'start',
	'value' => $time->getStart()->getTimestamp(),
	'timestamp' => false,
]);

echo elgg_view_field([
	'#type' => 'date',
	'#label' => elgg_echo('analytics:label:end'),
	'name' => 'end',
	'value' => $time->getEnd()->getTimestamp(),
	'timestamp' => false,
	'datepicker_opts' => [
		'maxDate' => '+0d',
	]
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('analytics:label:interval'),
	'name' => 'interval',
	'value' => get_input('interval', 'day'),
	'options_values' => [
		'hour' => elgg_echo('analytics:interval:hour'),
		'day' => elgg_echo('analytics:interval:day'),
		'month' => elgg_echo('analytics:interval:month'),
		'year' => elgg_echo('analytics:interval:year'),
	],
	'timestamp' => false,
	'datepicker_opts' => [
		'maxDate' => '+0d',
	]
]);

if (elgg_is_active_plugin('hypeMapsOpen')) {
	echo elgg_view_field([
		'#type' => 'location',
		'#label' => elgg_echo('analytics:label:location'),
		'name' => 'location',
		'value' => get_input('location'),
	]);
}

if (elgg_is_active_plugin('hypeMapsOpen')) {
	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('analytics:label:radius'),
		'name' => 'radius',
		'value' => get_input('radius', 500),
	]);
}

$users = get_input('users', '');
if (!is_array($users)) {
	$users = explode(',', $users);
}

echo elgg_view_field([
	'#type' => 'tokeninput/users',
	'#label' => elgg_echo('analytics:label:users'),
	'name' => 'users',
	'value' => array_filter($users),
	'multiple' => true,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('analytics:filter'),
		]);

elgg_set_form_footer($footer);
?>
<script>
	require(['forms/analytics/filter']);
</script>