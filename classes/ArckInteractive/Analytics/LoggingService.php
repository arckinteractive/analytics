<?php

namespace ArckInteractive\Analytics;

use ElggCookie;
use ElggEntity;
use ElggRelationship;
use ElggUser;
use MaxMind\Db\Reader;

class LoggingService {

	const SID_COOKIE = 'analytics_sid';
	const FP_COOKIE = 'analytics_fp';
	const EXPIRY = 3600; // 1 hour of inactivity creates a new session

	/**
	 * @var Session
	 */
	protected $session;

	/**
	 * Log a page view
	 *
	 * @param array $data View data
	 *                    - page_url
	 *                    - referrer_url
	 *                    - entity_guid
	 *                    - page_owner_guid
	 *                    - time
	 * @return bool
	 */

	public function logPageView(array $data = []) {

		$session = $this->loadSession();
		if (!$session) {
			return false;
		}

		$logger = new Logger($session);

		if ($logger->logPageView($data)) {
			if ($session->time_ended < $data['time']) {
				$session->time_ended = $data['time'];
				$session->save();
			}
			return true;
		}

		return false;
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

		$session = $this->loadSession();
		if (!$session) {
			return false;
		}

		$logger = new Logger($session);
		if ($logger->logEntityView($data)) {
			if ($session->time_ended < $data['time']) {
				$session->time_ended = $data['time'];
				$session->save();
			}
			return true;
		}

		return false;
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

		$session = $this->loadSession();
		if (!$session) {
			return false;
		}

		$logger = new Logger($session);
		if ($logger->logEvent($data)) {
			if ($session->time_ended < $data['time']) {
				$session->time_ended = $data['time'];
				$session->save();
			}
			return true;
		}

		return false;
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

		$session = $this->loadSession();
		if (!$session) {
			return false;
		}

		$logger = new Logger($session);
		if ($logger->logEntityEvent($data)) {
			if ($session->time_ended < $data['time']) {
				$session->time_ended = $data['time'];
				$session->save();
			}
			return true;
		}

		return false;
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

		$session = $this->loadSession();
		if (!$session) {
			return false;
		}

		$logger = new Logger($session);
		return $logger->logBenchmark($data);
	}

	/**
	 * Get current session
	 *
	 * @return Session
	 */
	protected function loadSession() {

		if ($this->session) {
			return $this->session;
		}

		$session_id = false;

		$cookie = $_COOKIE[self::SID_COOKIE];
		if ($cookie) {
			$cookie = unserialize($cookie);
			$hmac = elgg_build_hmac([
				'sid' => $cookie['sid'],
				'ts' => $cookie['ts'],
			]);
			if ($hmac->matchesToken($cookie['token'])) {
				$session_id = $cookie['sid'];
			}
		}

		$fingerprint = $_COOKIE[self::FP_COOKIE];
		if (!$fingerprint) {
			$fingerprint = elgg_build_hmac([
				'rand' => generate_random_cleartext_password(),
				'ts' => time(),
					])->getToken();
		}

		$user = elgg_get_logged_in_user_entity();
		if (!$user && $fingerprint) {
			$ia = elgg_set_ignore_access(true);
			$users = elgg_get_entities_from_metadata([
				'types' => 'user',
				'metadata_name_value_pairs' => [
					'name' => 'analytics_fingerprint',
					'value' => $fingerprint,
				],
				'limit' => 1,
			]);
			if ($users) {
				$user = array_shift($users);
			}
			elgg_set_ignore_access($ia);
		}

		$fingerprint = $this->fingerprint($user, $fingerprint);

		if ($session_id) {
			$session = Session::load($session_id);
		}

		if ($session && $user) {
			if (!$session->user_guid) {
				$session->user_guid = $user->guid;
				$session->fingerprint = $fingerprint;
				$session->save();
			} else if ($session->user_guid != $user->guid) {
				// start a new session
				$session = false;
			}
		}

		if (!$session) {
			$data = $this->collectSessionData();
			$data['user_guid'] = (int) $user->guid;
			$data['fingerprint'] = $fingerprint;
			$session = new Session((object) $data);
			$session->save();
		}

		$data = [
			'sid' => $session->getId(),
			'ts' => time(),
		];

		$hmac = elgg_build_hmac($data);
		$data['token'] = $hmac->getToken();

		$sid_cookie = new ElggCookie(self::SID_COOKIE);
		$sid_cookie->expire = time() + self::EXPIRY;
		$sid_cookie->value = serialize($data);
		elgg_set_cookie($sid_cookie);


		$fp_cookie = new ElggCookie(self::FP_COOKIE);
		$fp_cookie->setExpiresTime('+1 year');
		$fp_cookie->value = $fingerprint;
		elgg_set_cookie($fp_cookie);

		$this->session = $session;
		
		return $session;
	}

	/**
	 * Create a unique user fingerprint so we can match previous anonymous sessions
	 * to the user (after login or registration)
	 *
	 * @param ElggUser $user            User
	 * @param string   $old_fingerprint Old fingerprint
	 * @return string
	 */
	protected function fingerprint($user = null, $old_fingerprint = '') {

		if (!$user instanceof ElggUser) {
			return $old_fingerprint;
		}

		if (!$user->analytics_fingerprint) {
			$user->analytics_fingerprint = elgg_build_hmac([
				'guid' => $user->guid,
				'ts' => time(),
					])->getToken();
		}

		$user_fingerprint = $user->analytics_fingerprint;
		if (!$old_fingerprint) {
			return $user_fingerprint;
		}

		// This is probably not a good idea:
		// we will end up wrongfully assigning sessions to multiple users
		// that maybe sharing a browser
		//if ($old_fingerprint != $user_fingerprint) {
		//	$dbprefix = elgg_get_config('dbprefix');
		//	$query = "
		//		UPDATE {$dbprefix}analytics_sessions
		//		SET user_guid = :user_guid,
		//			fingerprint = :user_fingerprint
		//		WHERE fingerprint = :old_fingerprint
		//	";
		//	$params = [
		//		':user_guid' => $user->guid,
		//		':user_fingerprint' => $user_fingerprint,
		//		':old_fingerprint' => $old_fingerprint,
		//	];
		//	update_data($query, $params);
		//}

		return $user_fingerprint;
	}

	/**
	 * Collect data about the current session
	 * @return array
	 */
	protected function collectSessionData() {

		$time_started = time();
		$time_ended = $time_started;

		$user_guid = elgg_get_logged_in_user_guid();

		$ip_address = $this->getIpAddress();

		$geolite = elgg_get_config('geolite_db');
		if (file_exists($geolite)) {
			$reader = new Reader($geolite);
			$geoip = $reader->get($ip_address);
		} else {
			$geoip = [];
		}

		$city = '';
		if (!empty($geoip['city']['names']['en'])) {
			$city = $geoip['city']['names']['en'];
		}

		$state = '';
		if (!empty($geoip['subdivisions'])) {
			$state = array_shift($geoip['subdivisions']);
			if (!empty($state['names']['en'])) {
				$state = $state['names']['en'];
			}
		}

		$country = '';
		if (!empty($geoip['country']['iso_code'])) {
			$country = $geoip['country']['iso_code'];
		}

		$latitude = '';
		if (!empty($geoip['location']['latitude'])) {
			$latitude = $geoip['location']['latitude'];
		}

		$longitude = '';
		if (!empty($geoip['location']['longitude'])) {
			$longitude = $geoip['location']['longitude'];
		}

		$timezone = '';
		if (!empty($geoip['location']['time_zone'])) {
			$timezone = $geoip['location']['time_zone'];
		}

		return [
			'user_guid' => $user_guid,
			'time_started' => $time_started,
			'time_ended' => $time_ended,
			'ip_address' => $ip_address,
			'city' => $city,
			'state' => $state,
			'country' => $country,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'timezone' => $timezone,
		];
	}

	/**
	 * Returns the IP address of the current user
	 * @return string
	 */
	protected function getIpAddress() {

		if (getenv('HTTP_CLIENT_IP')) {
			$ip_address = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip_address = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip_address = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip_address = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$ip_address = getenv('HTTP_FORWARDED');
		} else {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}

		return $ip_address;
	}

	/**
	 * Log entity events
	 *
	 * @param string $event       Event name
	 * @param string $entity_type "object"|"group"|"user"
	 * @param mixed  $entity      Entity
	 * @return void
	 */
	public static function entityEventHandler($event, $entity_type, $entity) {

		$tracked_events = [
			'create',
			'update',
			'delete',
			'publish',
		];

		if (!in_array($event, $tracked_events)) {
			return;
		}

		if (!$entity instanceof ElggEntity) {
			return;
		}

		if (_elgg_services()->request->getFirstUrlSegment() == 'action') {
			$page_url = $_SERVER['HTTP_REFERER'];
		} else {
			$page_url = implode('/', _elgg_services()->request->getUrlSegments());
		}

		$svc = new LoggingService();
		$svc->logEntityEvent([
			'page_url' => $page_url,
			'event' => $event,
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $entity->guid,
			'target_guid' => $entity->container_guid,
			'object_type' => $entity->getType(),
			'object_subtype' => $entity->getSubtype(),
			'time' => time(),
		]);
	}

	/**
	 * Log relationship events
	 *
	 * @param string $event        Event name
	 * @param string $event_type   "relationship"
	 * @param mixed  $relationship Entity
	 * @return void
	 */
	public static function relationshipEventHandler($event, $event_type, $relationship) {

		if (!$relationship instanceof ElggRelationship) {
			return;
		}

		if (_elgg_services()->request->getFirstUrlSegment() == 'action') {
			$page_url = $_SERVER['HTTP_REFERER'];
		} else {
			$page_url = implode('/', _elgg_services()->request->getUrlSegments());
		}

		$object = get_entity($relationship->guid_one);
		if (!$object) {
			return;
		}

		$svc = new LoggingService();
		$svc->logEntityEvent([
			'page_url' => $page_url,
			'event' => $relationship->relationship,
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $object->guid,
			'target_guid' => $relationship->guid_two,
			'object_type' => $object->getType(),
			'object_subtype' => $object->getSubtype(),
			'time' => time(),
		]);
	}

	/**
	 * Log a user/group profile view
	 *
	 * @param string $hook      "view"
	 * @param string $view_name View name
	 * @param array  $return    Content
	 * @param array  $params    Hook params
	 * @return void
	 */
	public static function logProfileViewHandler($hook, $view_name, $return, $params) {

		if (empty($return)) {
			return;
		}

		$vars = elgg_extract('vars', $params);

		$entity = elgg_extract('entity', $vars);
		if (!$entity) {
			$entity = elgg_get_page_owner_entity();
		}

		if (!$entity instanceof ElggEntity) {
			return;
		}

		$page_url = elgg_extract('page_url', $vars);
		$entity_guid = $entity->guid;
		$full_view = true;

		$data = [
			'page_url' => $page_url,
			'entity_guid' => $entity_guid,
			'view_name' => $view_name,
			'full_view' => $full_view,
		];

		$return .= elgg_view('analytics/logger/entity', ['data' => $data]);
		return $return;
	}

	/**
	 * Log an object listing view
	 *
	 * @param string $hook      "view"
	 * @param string $view_name View name
	 * @param array  $return    Content
	 * @param array  $params    Hook params
	 * @return void
	 */
	public static function logListingViewHandler($hook, $view_name, $return, $params) {

		if (empty($return)) {
			return;
		}

		$vars = elgg_extract('vars', $params);

		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof ElggEntity) {
			return;
		}

		$page_url = elgg_extract('page_url', $vars);
		$entity_guid = $entity->guid;

		$full_view = elgg_extract('full_view', $vars, false);

		// We don't want to overload the DB with summary listing entries
		if (!$full_view) {
			return;
		}

		$data = [
			'page_url' => $page_url,
			'entity_guid' => $entity_guid,
			'view_name' => $view_name,
			'full_view' => $full_view,
		];

		$return .= elgg_view('analytics/logger/entity', ['data' => $data]);
		return $return;
	}

	/**
	 * Log daily stats/benchmarks when cron runs
	 * @return void
	 */
	public static function logDailyBenchmarks() {

		$dbprefix = elgg_get_config('dbprefix');

		$svc = new LoggingService();

		$ia = elgg_set_ignore_access();

		$users = elgg_get_entities([
			'types' => 'user',
			'count' => true,
		]);

		$svc->logBenchmark([
			'target_guid' => elgg_get_site_entity()->guid,
			'metric' => 'users:total',
			'value' => $users,
		]);

		$banned = elgg_get_entities([
			'types' => 'user',
			'count' => true,
			'joins' => [
				"JOIN {$dbprefix}users_entity ue ON ue.guid = e.guid",
			],
			'wheres' => [
				"ue.banned = 'yes'",
			],
		]);

		$svc->logBenchmark([
			'target_guid' => elgg_get_site_entity()->guid,
			'metric' => 'users:banned',
			'value' => $banned,
		]);

		$validated = elgg_get_entities_from_metadata([
			'types' => 'user',
			'count' => true,
			'metadata_name_value_pairs' => [
				'validated' => true,
			],
		]);

		$svc->logBenchmark([
			'target_guid' => elgg_get_site_entity()->guid,
			'metric' => 'users:validated',
			'value' => $validated,
		]);

		$groups = elgg_get_entities([
			'types' => 'group',
			'count' => true,
		]);

		$svc->logBenchmark([
			'target_guid' => elgg_get_site_entity()->guid,
			'metric' => 'groups:total',
			'value' => $groups,
		]);

		$posts = elgg_get_entities([
			'types' => 'object',
			'count' => true,
		]);

		$svc->logBenchmark([
			'target_guid' => elgg_get_site_entity()->guid,
			'metric' => 'posts:total',
			'value' => $posts,
		]);

		$subtypes = get_registered_entity_types('object');

		foreach ($subtypes as $subtype) {
			$posts = elgg_get_entities([
				'types' => 'object',
				'subtypes' => $subtype,
				'count' => true,
			]);

			$svc->logBenchmark([
				'target_guid' => elgg_get_site_entity()->guid,
				'metric' => "posts:$subtype",
				'value' => $posts,
			]);
		}

		elgg_set_ignore_access($ia);
	}

}
