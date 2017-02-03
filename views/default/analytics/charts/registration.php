<?php

$endpoint = elgg_normalize_url(elgg_http_add_url_query_elements('analytics/datasets/registration', [
	'view' => 'json',
		]));

echo elgg_format_element('canvas', [
	'width' => 600,
	'height' => 200,
	'id' => 'analytics-registration',
	'data-chart-type' => 'bar',
	'data-src' => $endpoint,
	'data-query' => json_encode([
		'start' => elgg_extract('start', $vars),
		'end' => elgg_extract('end', $vars),
		'location' => elgg_extract('location', $vars),
		'radius' => elgg_extract('radius', $vars),
		'users' => elgg_extract('users', $vars),
	]),
	'data-chart-options' => json_encode([
		'legend' => [
			'position' => 'bottom',
		],
		'scales' => [
			'xAxes' => [
				[
					'stacked' => true,
					'display' => false,
				]
			],
			'yAxes' => [
				[
					'stacked' => true,
					//'type' => 'time',
					'ticks' => [
						'beginAtZero' => true,
					]
				]
			],
		],
	]),
]);
?>
<script>
	require(['analytics/chart'], function(chart) {
		chart.init();
	});
</script>
