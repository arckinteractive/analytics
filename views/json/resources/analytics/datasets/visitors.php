<?php

use ArckInteractive\Analytics\ReportingService;

elgg_admin_gatekeeper();

$data = [
	'datasets' => [
		'guests' => [
			'label' => elgg_echo('analytics:label:guest_users'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(93, 165, 218, 1)",
			'borderWidth' => 0,
			'data' => [],
		],
		'registered' => [
			'label' => elgg_echo('analytics:label:registered_users'),
			'lineTension' => 0,
			'backgroundColor' => "rgba(250, 164, 58, 1)",
			'borderWidth' => 0,
			'data' => [],
		],
	],
];

$callback = function(ReportingService $svc, $data) {
	$data['datasets']['registered']['data'][] = $svc->getRegisteredUsers(['count' => true]);
	$data['datasets']['guests']['data'][] = $svc->getGuestUsers(['count' => true]);
	return $data;
};

$data = ReportingService::datasetFactory($callback, $data);

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);

