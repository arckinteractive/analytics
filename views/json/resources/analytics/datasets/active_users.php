
<?php

use ArckInteractive\Analytics\ReportingService;

elgg_admin_gatekeeper();

elgg_set_viewtype('default');

$svc = ReportingService::fromRequest();

$sessions = $svc->getRegisteredUsers([
	'limit' => 20,
]);

$data = [];

foreach ($sessions as $session) {
	$user = $session->getUser();
	if ($user) {
		$title = $user->getDisplayName();
		$url = $user->getURL();
		$icon = elgg_view_entity_icon($user, 'tiny');
	} else {
		$title = 'DELETED';
		$url = false;
		$icon = '';
	}

	$svc->setUsers([$session->user_guid]);

	$last_session = $svc->getSessions(['limit' => 1]);
	$sessions = $svc->getSessions(['count' => true]);
	$page_views = $svc->getPageViews(['count' => true]);
	$entity_views = $svc->getEntityViews(['count' => true]);
	$posts = $svc->getPosts(['count' => true]);
	
	$data[] = [
		'name' => elgg_view_image_block($icon, $title),
		'last_seen' => $last_session ? date('H:i j M, Y', $last_session[0]->time_ended) : 'NEVER',
		'sessions' => $sessions,
		'page_views' => $page_views,
		'entity_views' => $entity_views,
		'posts' => $posts,
	];
}

elgg_set_http_header('Content-type: application/json');
echo json_encode($data);

