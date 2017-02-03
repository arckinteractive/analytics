<?php

use ArckInteractive\Analytics\LoggingService;

$page_url = get_input('page_url');
$entity_guid = get_input('entity_guid');
$view_name = get_input('view_name');
$full_view = get_input('full_view');
$time = get_input('time');

$svc = new LoggingService();
$svc->logEntityView([
	'page_url' => $page_url,
	'entity_guid' => $entity_guid,
	'view_name' => $view_name,
	'full_view' => $full_view,
	'time' => $time,
]);
