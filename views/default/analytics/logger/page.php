<?php

use UFCOE\Elgg\SiteUrl;

$page_url = elgg_extract('page_url', $vars);
$entity_guid = elgg_extract('entity_guid', $vars);
$page_owner_guid = elgg_extract('page_owner_guid', $vars);
$referrer_url = elgg_extract('referrer_url', $vars);

if (!isset($page_url)) {
	$page_url = current_page_url();
}

if (!isset($entity_guid)) {
	// Try retrieving guid from URL
	$site_url = new SiteUrl(elgg_get_site_url());
	$path = $site_url->getSitePath($page_url);
	if ($path) {
		$entity_guid = $path->getGuid();
	}
}

$data = [
	'page_url' => $page_url,
	'entity_guid' => $entity_guid,
];
?>
<script>
	require(['analytics/logger'], function (logger) {
		logger.logPageView(<?= json_encode(array_filter($data)) ?>);
	});
</script>
