<?php

use ArckInteractive\Analytics\LoggingService;

$page_url = get_input('page_url');
$page_title = get_input('page_title');
$entity_guid = get_input('entity_guid');
$page_owner_guid = get_input('page_owner_guid');
$referrer_url = get_input('referrer_url');
$time = get_input('time');

$svc = new LoggingService();
$svc->logPageView([
	'page_url' => $page_url,
	'page_title' => $page_title,
	'referrer_url' => $referrer_url,
	'entity_guid' => $entity_guid,
	'page_owner_guid' => $page_owner_guid,
	'time' => $time,
]);
