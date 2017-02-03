<?php

use ArckInteractive\Analytics\ReportingService;
use ArckInteractive\Analytics\Time;

elgg_admin_gatekeeper();

$svc = ReportingService::fromRequest();

$stat = get_input('stat');

$value = 0;
$label = elgg_echo("analytics:stat:$stat");

switch ($stat) {

	case 'sessions:total' :
		$value = $svc->getSessions(['count' => true]);
		break;

	case 'visitors:total' :
		$value = $svc->getVisitors(['count' => true]);
		break;

	case 'registered_users:total' :
		$value = $svc->getRegisteredUsers(['count' => true]);
		break;

	case 'registered_users:percentage' :
		$visitors = $svc->getRegisteredUsers(['count' => true]);
		$total = elgg_get_entities([
			'types' => 'user',
			'count' => true,
		]);
		$value = round(($visitors / $total) * 100, 2) . '%';
		break;

	case 'guest_users:total' :
		$value = $svc->getGuestUsers(['count' => true]);
		break;

	case 'sessions:per_visitor' :
		$sessions = $svc->getSessions(['count' => true]);
		$visitors = $svc->getVisitors(['count' => true]);
		$value = round($sessions / $visitors, 2);
		break;

	case 'sessions:avg_duration' :
		$seconds = $svc->getAvgSessionDuration();
		$value = Time::toDuration($seconds);
		break;

	case 'page_views:total' :
		$value = $svc->getPageViews(['count' => true]);
		break;

	case 'page_views:unique_pages' :
		$value = $svc->getUniquePagesViewed(['count' => true]);
		break;

	case 'page_views:per_session' :
		$sessions = $svc->getSessions(['count' => true]);
		$page_views = $svc->getPageViews(['count' => true]);
		$value = round($page_views / $sessions, 2);
		break;

	case 'page_views:per_visitor' :
		$visitors = $svc->getVisitors(['count' => true]);
		$page_views = $svc->getPageViews(['count' => true]);
		$value = round($page_views / $visitors, 2);
		break;

	case 'entity_views:total' :
		$value = $svc->getEntityViews(['count' => true]);
		break;

	case 'entity_views:users' :
		$value = $svc->getUniqueEntitiesViewed([
			'count' => true,
			'type' => 'user',
		]);
		break;

	case 'entity_views:groups' :
		$value = $svc->getUniqueEntitiesViewed([
			'count' => true,
			'type' => 'group',
		]);
		break;

	case 'entity_views:object' :
		$value = $svc->getUniqueEntitiesViewed([
			'count' => true,
			'type' => 'object',
		]);
		break;

	case 'entity_views:per_session' :
		$sessions = $svc->getSessions(['count' => true]);
		$page_views = $svc->getEntityViews(['count' => true]);
		$value = round($page_views / $sessions, 2);
		break;

	case 'entity_views:per_visitor' :
		$visitors = $svc->getVisitors(['count' => true]);
		$page_views = $svc->getEntityViews(['count' => true]);
		$value = round($page_views / $visitors, 2);
		break;

	case 'posts:total' :
		$value = $svc->getPosts(['count' => true]);
		break;

	case 'posts:per_session' :
		$sessions = $svc->getSessions(['count' => true]);
		$posts = $svc->getPosts(['count' => true]);
		$value = round($posts / $sessions, 2);
		break;

	case 'posts:per_visitor' :
		$visitors = $svc->getVisitors(['count' => true]);
		$posts = $svc->getPosts(['count' => true]);
		$value = round($posts / $visitors, 2);
		break;

	case 'registrations:total' :
		$value = $svc->getRegistrations(['count' => true]);
		break;

	case 'registrations:validated' :
		$value = $svc->getRegistrations([
			'count' => true,
			'validated' => true,
		]);
		break;

	case 'registrations:unvalidated' :
		$value = $svc->getRegistrations([
			'count' => true,
			'validated' => false,
		]);
		break;

	case 'countries:total' :
		$value = $svc->getCountries(['count' => true]);
		break;

	case 'cities:total' :
		$value = $svc->getCities(['count' => true]);
		break;

	default :
		list($section, $subtype) = explode(':', $stat);
		if ($section == 'posts') {
			$value = (int) $svc->getPosts(['count' => true, 'subtypes' => $subtype]);
		}
		break;
}

elgg_set_http_header('Content-type: application/json');
echo json_encode([
	'value' => $value,
	'label' => $label,
]);
