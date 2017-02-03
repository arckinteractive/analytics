<?php

namespace ArckInteractive\Analytics;

use ElggUser;
use stdClass;

/**
 * Analytics session
 *
 * @property-read int $id
 *
 * @property int    $user_guid
 * @property string $fingerprint
 * @property int    $time_started
 * @property int    $time_ended
 * @property string $ip_address
 * @property string $city
 * @property string $state
 * @property string $country
 * @property float  $latitude
 * @property float  $longitude
 * @property string $timezone
 */
class Session {

	protected $id;

	/**
	 * Constructor
	 * 
	 * @param \stdClass $row DB row
	 */
	public function __construct(stdClass $row) {
		foreach ($row as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Load session from DB
	 *
	 * @param int $session_id Session ID
	 * @return self|false
	 */
	public static function load($session_id) {

		$dbprefix = elgg_get_config('dbprefix');

		$query = "
			SELECT * FROM {$dbprefix}analytics_sessions
			WHERE id = :id
		";

		$params = [
			':id' => (int) $session_id,
		];

		$row = get_data_row($query, null, $params);

		if (!$row) {
			return false;
		}

		return new self($row);
	}

	/**
	 * Save session data and get an ID
	 * @return int|false
	 */
	public function save() {

		$dbprefix = elgg_get_config('dbprefix');

		$params = [
			':id' => (int) $this->id,
			':user_guid' => (int) $this->user_guid,
			':fingerprint' => (string) $this->fingerprint,
			':time_started' => (int) $this->time_started,
			':time_ended' => (int) $this->time_ended,
			':ip_address' => (string) $this->ip_address,
			':city' => (string) $this->city,
			':state' => (string) $this->state,
			':country' => (string) $this->country,
			':latitude' => $this->latitude ? (float) $this->latitude : null,
			':longitude' => $this->longitude ? (float) $this->longitude : null,
			':timezone' => (string) $this->timezone,
		];
		
		if ($this->id) {
			$query = "
				UPDATE {$dbprefix}analytics_sessions
				SET user_guid = :user_guid,
					fingerprint = :fingerprint,
					time_started = :time_started,
					time_ended = :time_ended,
					ip_address = :ip_address,
					city = :city,
					state = :state,
					country = :country,
					latitude = :latitude,
					longitude = :longitude,
					timezone = :timezone
				WHERE id = :id
				";
			$result = update_data($query, $params);

			if (!$result) {
				return false;
			}
		} else {
			$query = "
				INSERT INTO {$dbprefix}analytics_sessions
				SET user_guid = :user_guid,
					fingerprint = :fingerprint,
					time_started = :time_started,
					time_ended = :time_ended,
					ip_address = :ip_address,
					city = :city,
					state = :state,
					country = :country,
					latitude = :latitude,
					longitude = :longitude,
					timezone = :timezone
				";

			$id = insert_data($query, $params);
			if (!$id) {
				return false;
			}

			$this->id = $id;
		}

		return $this->id;
	}

	/**
	 * Retrieve session id
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns user logged in during the session
	 * @return ElggUser|false
	 */
	public function getUser() {
		if (!$this->user_guid) {
			return false;
		}
		return get_user($this->user_guid);
	}

	/**
	 * Get page views data for a given session
	 *
	 * @param bool $count  Return count
	 * @param int  $limit  Limit
	 * @param int  $offset Offset
	 * @return PageView[]|false
	 */
	public function getPageViews($count = false, $limit = 0, $offset = 0) {

		$time = $this->getTimeFrame();

		$params = [
			':session_id' => $this->id,
		];

		$dbprefix = elgg_get_config('dbprefix');
		if ($count) {
			$query = "
				SELECT COUNT(*) AS total FROM {$dbprefix}analytics_page_view
				WHERE session_id = :session_id
			";

			$data = get_data_row($query, null, $params);
			return empty($data) ? 0 : $data->total;
		} else {
			$query = "
				SELECT * FROM {$dbprefix}analytics_page_view
				WHERE session_id = :session_id
			";
			if ($limit || $offset) {
				$offset = (int) $offset;
				$limit = (int) $limit;
				$query = "LIMIT $offset, $limit";
			}

			$data = get_data($query, [ReportingService::class, 'rowToPageView'], $params);
			return empty($data) ? false : $data;
		}
	}
}
