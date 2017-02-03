<?php
$stat = elgg_extract('stat', $vars);
if (!$stat) {
	return;
}

$endpoint = elgg_normalize_url(elgg_http_add_url_query_elements('analytics/stats', [
	'view' => 'json',
	'stat' => $stat,
		]));

$attrs = elgg_format_attributes([
	'data-stat' => $stat,
	'data-src' => $endpoint,
	'data-query' => json_encode([
		'start' => elgg_extract('start', $vars),
		'end' => elgg_extract('end', $vars),
		'location' => elgg_extract('location', $vars),
		'radius' => elgg_extract('radius', $vars),
		'users' => elgg_extract('users', $vars),
	]),
		]);
?>
<div class="analytics-stat" <?= $attrs ?>>
	<span class="analytics-stat-value"><?= elgg_extract('value', $vars, 0) ?></span>
	<span class="analytics-stat-label"><?= elgg_echo("analytics:stat:$stat") ?></span>
</div>
<script>require(['analytics/stat'])</script>
