<?php

use ArckInteractive\Analytics\ReportingService;

elgg_admin_gatekeeper();

$data = [
	'datasets' => [
		'page_views' => [
			'label' => elgg_echo('analytics:label:page_views'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(80, 151, 207, 0.25)",
			'borderColor' => "rgba(80, 151, 207, 1)",
			'data' => [],
		],
	],
];

$callback = function(ReportingService $svc, $data) {
	$data['datasets']['page_views']['data'][] = $svc->getPageViews(['count' => true]);
	return $data;
};

$data = ReportingService::datasetFactory($callback, $data);

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);

