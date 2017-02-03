<?php

use ArckInteractive\Analytics\ReportingService;

elgg_admin_gatekeeper();

$data = [
	'datasets' => [
		'sessions' => [
			'label' => elgg_echo('analytics:label:sessions'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(80, 151, 207, 0.05)",
			'borderColor' => "rgba(80, 151, 207, 1)",
			'data' => [],
		],
		'visits' => [
			'label' => elgg_echo('analytics:label:visitors'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(250, 164, 58, 0.05)",
			'borderColor' => "rgba(250, 164, 58, 1)",
			'data' => [],
		],
	],
];

$callback = function(ReportingService $svc, $data) {
	$data['datasets']['sessions']['data'][] = $svc->getSessions([
		'count' => true,
	]);
	$data['datasets']['visits']['data'][] = $svc->getVisitors([
		'count' => true,
	]);
	return $data;
};

$data = ReportingService::datasetFactory($callback, $data);

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);


