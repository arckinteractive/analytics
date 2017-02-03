<?php

use ArckInteractive\Analytics\ReportingService;
use hypeJunction\MapsOpen\Marker;

elgg_admin_gatekeeper();

if (!elgg_is_active_plugin('hypeMapsOpen')) {
	forward('', '404');
}

elgg_set_viewtype('default');

$svc = ReportingService::fromRequest();

$count = $svc->getSessions(['count' => true]);

$markers = [];

$limit = 100;
for ($offset = 0; $offset < $count; $offset += $limit) {
	$sessions = $svc->getSessions([
		'limit' => $limit,
		'offset' => $offset,
	]);
	foreach ($sessions as $session) {
		if (!$session->latitude && !$session->longitude) {
			continue;
		}

		$marker = Marker::fromLatLong($session->latitude, $session->longitude);
		$marker->color = 'red';
		$marker->tooltip = elgg_view('analytics/session', [
			'session' => $session,
			'full_view' => false,
		]);

		$markers[] = $marker->toArray();
	}
}

elgg_set_http_header('Content-type: application/json');
echo json_encode([
	'markers' => $markers,
]);

