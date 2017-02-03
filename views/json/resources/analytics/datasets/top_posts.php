
<?php

use ArckInteractive\Analytics\ReportingService;

elgg_admin_gatekeeper();

elgg_set_viewtype('default');

$svc = ReportingService::fromRequest();

$entity_views = $svc->getUniqueEntitiesViewed([
	'limit' => 20,
]);

$data = [];

foreach ($entity_views as $entity_view) {
	$entity = $entity_view->getEntity();
	if ($entity) {
		$title = $entity->getDisplayName();
	} else {
		$title = 'DELETED';
	}

	$data[] = [
		'title' => elgg_view('output/url', [
			'text' => $title ? : $entity_view->page_url,
			'href' => $entity_view->page_url,
			'target' => '_blank',
		]),
		'views_count' => $entity_view->views_count,
	];
}

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);

