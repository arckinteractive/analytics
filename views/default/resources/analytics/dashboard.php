<?php

elgg_admin_gatekeeper();

$svc = new \ArckInteractive\Analytics\ReportingService();

$title = elgg_echo('analytics:dashboard');
$main = elgg_view_layout('widgets', [
	'num_columns' => 1,
	'show_add_widgets' => true,
	'exact_match' => true,
	'show_access' => false,
	'owner_guid' => elgg_get_site_entity()->guid,
]);

$form = elgg_view_form('analytics/filter', [
	'method' => 'GET',
	'action' => current_page_url(),
], $vars);

elgg_push_breadcrumb(elgg_echo('analytics'), 'analytics');
elgg_push_breadcrumb($title);

$layout = elgg_view_layout('analytics', [
	'title' => $title,
	'content' => $main,
	'sidebar' => $form,
]);

echo elgg_view_page($title, $layout);



