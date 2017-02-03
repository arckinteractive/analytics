<?php

$endpoint = elgg_normalize_url(elgg_http_add_url_query_elements('analytics/datasets/top_pages', [
	'view' => 'json',
		]));

$th = [
	'page_url',
	'page_views',
];

$head = '';
foreach ($th as $heading) {
	$head .= elgg_format_element('th', [
		'data-name' => $heading,
	], elgg_echo("analytics:heading:$heading"));
}

$head = elgg_format_element('thead', [], $head);
$body = elgg_format_element('tbody');

echo elgg_format_element('table', [
	'width' => 600,
	'height' => 200,
	'id' => 'analytics-top-pages',
	'class' => 'elgg-table-alt analytics-table',
	'data-src' => $endpoint,
	'data-query' => json_encode([
		'start' => elgg_extract('start', $vars),
		'end' => elgg_extract('end', $vars),
		'location' => elgg_extract('location', $vars),
		'radius' => elgg_extract('radius', $vars),
		'users' => elgg_extract('users', $vars),
	]),
], $head . $body);
?>
<script>
	require(['analytics/table'], function(table) {
		table.init();
	});
</script>