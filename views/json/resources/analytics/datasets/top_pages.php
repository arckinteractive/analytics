
<?php

use ArckInteractive\Analytics\ReportingService;

elgg_admin_gatekeeper();

elgg_set_viewtype('default');

$svc = ReportingService::fromRequest();

$pages = $svc->getUniquePagesViewed([
	'limit' => 20,
]);

$data = [];

foreach ($pages as $page) {
	$data[] = [
		'page_url' => elgg_view('output/url', [
			'text' => $page->page_title ? : $page->page_url,
			'href' => $page->page_url,
			'target' => '_blank',
		]),
		'views_count' => $page->views_count,
	];
}

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);

