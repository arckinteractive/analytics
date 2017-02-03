<?php

use ArckInteractive\Analytics\LoggingService;

$page_url = get_input('page_url');
$event = get_input('event');
$target = get_input('target');
$href = get_input('href');
$description = get_input('description');
$time = get_input('time');

$svc = new LoggingService();
$svc->logEvent([
	'page_url' => $page_url,
	'event' => $event,
	'target' => $target,
	'href' => $href,
	'description' => $description,
	'time' => $time,
]);
