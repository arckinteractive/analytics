<?php

use ArckInteractive\Analytics\ReportingService;
use function GuzzleHttp\json_encode;

elgg_admin_gatekeeper();

$subtypes = get_registered_entity_types('object');

$data = [
	'datasets' => $datasets,
];

$color = function ($str) {
	$hash = md5('abcde' . $str);
	return [
		hexdec(substr($hash, 0, 2)),
		hexdec(substr($hash, 2, 2)),
		hexdec(substr($hash, 4, 2))
	];
};

foreach ($subtypes as $subtype) {
	$rgb = $color($subtype);
	$data['datasets'][$subtype]['label'] = elgg_echo("item:object:$subtype");
	$data['datasets'][$subtype]['backgroundColor'] = 'rgba(' . implode(',', $rgb) . ', 0.75)';
	$data['datasets'][$subtype]['borderWidth'] = 0;
	$data['datasets'][$subtype]['lineTension'] = 0;
}

$callback = function(ReportingService $svc, $data) use ($subtypes) {
	foreach ($subtypes as $subtype) {
		$count = (int) $svc->getPosts([
			'count' => true,
			'subtypes' => $subtype,
		]);
		$data['datasets'][$subtype]['data'][] = $count;
	}

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

