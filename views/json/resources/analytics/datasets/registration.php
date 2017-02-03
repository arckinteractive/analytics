<?php

use ArckInteractive\Analytics\ReportingService;
use function GuzzleHttp\json_encode;

elgg_admin_gatekeeper();

$data = [
	'datasets' => [
		'validated' => [
			'label' => elgg_echo('analytics:label:validated'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(93, 165, 218, 1)",
			'borderWidth' => 0,
			'data' => [],
		],
		'unvalidated' => [
			'label' => elgg_echo('analytics:label:unvalidated'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(250, 164, 58, 1)",
			'borderWidth' => 0,
			'data' => [],
		],
	],
];

$callback = function(ReportingService $svc, $data) {
	$data['datasets']['validated']['data'][] = $svc->getRegistrations([
		'count' => true,
		'validated' => true,
	]);
	$data['datasets']['unvalidated']['data'][] = $svc->getRegistrations([
		'count' => true,
		'validated' => false,
	]);
	return $data;
};

$data = ReportingService::datasetFactory($callback, $data);

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);

