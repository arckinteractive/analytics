<?php

use ArckInteractive\Analytics\Session;
use ArckInteractive\Analytics\Time;

$session = elgg_extract('session', $vars);
if (!$session instanceof Session) {
	return;
}

$user = $session->getUser();
if (!$user) {
	$user = new ElggUser();
	$user->name = elgg_echo('analytics:anonymous');
}

$icon = elgg_view_entity_icon($user, 'small', [
	'use_hover' => false,
	'use_link' => false,
]);

$date = date('j M, Y', $session->time_started);
$diff = $session->time_ended - $session->time_started;

$subtitle = [];
$subtitle[] = elgg_echo('analytics:session_time', [$date, Time::toDuration($diff)]);
$subtitle[] = elgg_echo('analytics:ip_address', [$session->ip_address]);

echo elgg_view('object/elements/summary', [
	'entity' => $user,
	'tags' => false,
	'title' => $user->getDisplayName(),
	'subtitle' => implode('<br />', $subtitle),
	'icon' => $icon,
]);