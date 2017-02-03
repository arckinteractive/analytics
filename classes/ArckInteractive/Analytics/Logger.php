<?php

namespace ArckInteractive\Analytics;

use ElggEntity;

class Logger {

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * Constructor
	 * 
	 * @param Session $session Analytics session
	 */
	public function __construct(Session $session) {
		$this->session = $session;
	}

	/**
	 * Log a page view
	 *
	 * @param array $data View data
	 *                    - page_url
	 *                    - page_title
	 *                    - referrer_url
	 *                    - entity_guid
	 *                    - page_owner_guid
	 *                    - time
	 * @return bool
	 */
	public function logPageView(array $data = []) {
		$page_url = elgg_extract('page_url', $data);
		$page_title = elgg_extract('page_title', $data);
		$referrer_url = elgg_extract('referrer_url', $data);
		$entity_guid = elgg_extract('entity_guid', $data);
		$page_owner_guid = elgg_extract('page_owner_guid', $data);
		$time = elgg_extract('time', $data) ?: time();

		if (!$page_url) {
			return false;
		}

		$dbprefix = elgg_get_config('dbprefix');
		$query = "
			INSERT INTO {$dbprefix}analytics_page_views
			SET session_id = :session_id,
				page_url = :page_url,
				page_title = :page_title,
				referrer_url = :referrer_url,
				entity_guid = :entity_guid,
				page_owner_guid = :page_owner_guid,
				time = :time
		";

		$params = [
			':session_id' => $this->session->getId(),
			':page_url' => elgg_normalize_url($page_url),
			':page_title' => (string) $page_title,
			':referrer_url' => $referrer_url ? elgg_normalize_url($referrer_url) : '',
			':entity_guid' => (int) $entity_guid,
			':page_owner_guid' => (int) $page_owner_guid,
			':time' => (int) $time,
		];

		return insert_data($query, $params) !== false;
	}

	/**
	 * Log an entity view
	 *
	 * @param array $data View data
	 *                    - page_url
	 *                    - entity_guid
	 *                    - view_name
	 *                    - full_view
	 *                    - time
	 * @return bool
	 */
	public function logEntityView(array $data = []) {
		$page_url = elgg_extract('page_url', $data);
		$entity_guid = elgg_extract('entity_guid', $data);
		$view_name = elgg_extract('view_name', $data);
		$full_view = elgg_extract('full_view', $data);
		$time = elgg_extract('time', $data) ?: time();

		if (!$page_url) {
			return false;
		}

		$dbprefix = elgg_get_config('dbprefix');
		$query = "
			INSERT INTO {$dbprefix}analytics_entity_views
			SET session_id = :session_id,
				page_url = :page_url,
				entity_guid = :entity_guid,
				view_name = :view_name,
				full_view = :full_view,
				time = :time
		";

		$params = [
			':session_id' => $this->session->getId(),
			':page_url' => elgg_normalize_url($page_url),
			':entity_guid' => (int) $entity_guid,
			':view_name' => (string) $view_name,
			':full_view' => $full_view ? 'yes' : 'no',
			':time' => (int) $time,
		];

		return insert_data($query, $params) !== false;
	}

	/**
	 * Log an event
	 *
	 * @param array $data Event data
	 *                    - page_url
	 *                    - event
	 *                    - description
	 *                    - href
	 *                    - target
	 *                    - time
	 * @return bool
	 */
	public function logEvent(array $data = []) {

		$page_url = elgg_extract('page_url', $data);
		$event = elgg_extract('event', $data);
		$target = elgg_extract('target', $data);
		$href = elgg_extract('href', $data);
		$description = elgg_extract('description', $data);
		$time = elgg_extract('time', $data) ?: time();

		if (!$page_url) {
			return false;
		}

		$dbprefix = elgg_get_config('dbprefix');
		$query = "
			INSERT INTO {$dbprefix}analytics_events
			SET session_id = :session_id,
				page_url = :page_url,
				event = :event,
				target = :target,
				href = :href,
				description = :description,
				time = :time
		";

		$params = [
			':session_id' => $this->session->getId(),
			':page_url' => elgg_normalize_url($page_url),
			':event' => (string) $event,
			':target'=> (string) $target,
			':href' => $href ? elgg_normalize_url($href) : '',
			':description' => (string) $description,
			':time' => (int) $time,
		];

		return insert_data($query, $params) !== false;

	}

	/**
	 * Log an entity event
	 *
	 * @param array $data Event data
	 *                    - page_url
	 *                    - event
	 *                    - subject_guid
	 *                    - object_guid
	 *                    - object_type
	 *                    - object_subtype
	 *                    - target_guid
	 *                    - time
	 * @return bool
	 */
	public function logEntityEvent(array $data = []) {

		$page_url = elgg_extract('page_url', $data);
		$event = elgg_extract('event', $data);
		$subject_guid = elgg_extract('subject_guid', $data);
		$object_guid = elgg_extract('object_guid', $data);
		$target_guid = elgg_extract('target_guid', $data);
		$object_type = elgg_extract('object_type', $data);
		$object_subtype = elgg_extract('object_subtype', $data);
		$time = elgg_extract('time', $data) ?: time();

		$dbprefix = elgg_get_config('dbprefix');
		$query = "
			INSERT INTO {$dbprefix}analytics_entity_events
			SET session_id = :session_id,
				page_url = :page_url,
				event = :event,
				subject_guid = :subject_guid,
				object_guid = :object_guid,
				target_guid = :target_guid,
				object_type = :object_type,
				object_subtype = :object_subtype,
				time = :time
		";

		$params = [
			':session_id' => $this->session->getId(),
			':page_url' => elgg_normalize_url($page_url),
			':event' => (string) $event,
			':subject_guid' => (int) $subject_guid,
			':object_guid' => (int) $object_guid,
			':target_guid' => (int) $target_guid,
			':object_type' => (string) $object_type,
			':object_subtype' => (string) $object_subtype,
			':time' => (int) $time,
		];

		return insert_data($query, $params) !== false;

	}

	/**
	 * Log a time-specific benchmark
	 *
	 * @param array $data Benchmark data
	 *                    - target_guid
	 *                    - metric
	 *                    - value
	 *                    - time
	 * @return bool
	 */
	public function logBenchmark(array $data = []) {

		$target_guid = elgg_extract('target_guid', $data);
		$metric = elgg_extract('metric', $data);
		$value = elgg_extract('value', $data);
		$time = elgg_extract('time', $data) ?: time();

		$dbprefix = elgg_get_config('dbprefix');
		$query = "
			INSERT INTO {$dbprefix}analytics_benchmarks
			SET target_guid = :target_guid,
				metric = :metric,
				value = :value,
				time = :time
		";

		$params = [
			':target_guid' => (int) $target_guid,
			':metric' => (string) $metric,
			':value' => isset($value) ? $value : '',
			':time' => (int) $time,
		];

		return insert_data($query, $params) !== false;

	}
}
