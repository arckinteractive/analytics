<?php

namespace ArckInteractive\Analytics;

use DateTime;
use ElggObject;
use hypeJunction\MapsOpen\LatLong as Encoder;
use stdClass;

class ReportingService {

	/**
	 * @var TimeFrame
	 */
	private $time;

	/**
	 * @var LatLng
	 */
	private $latlong;

	/**
	 * @var float
	 */
	private $radius;

	/**
	 * @var string
	 */
	private $format = 'j M, Y';

	/**
	 * @var string
	 */
	private $interval = 'day';

	/**
	 * @var string
	 */
	private $modifier = '+1 day';

	/**
	 * @var int[]
	 */
	private $users = [];

	/**
	 * Constructor
	 *
	 * @param TimeFrame $time Time frame
	 */
	public function __construct(TimeFrame $time = null) {
		if (!$time) {
			$time = new TimeFrame();
		}
		$this->time = $time;
	}

	/**
	 * Change reporting time frame
	 *
	 * @param TimeFrame $time Time frame
	 * @return self
	 */
	public function setTimeFrame(TimeFrame $time = null) {
		$this->time = $time;
		return $this;
	}

	/**
	 * Returns reporting time frame
	 * @return TimeFrame
	 */
	public function getTimeFrame() {
		return $this->time;
	}

	/**
	 * Set lat/long coords for constraining the reports
	 *
	 * @param LatLong $latlong Lat/long
	 * @return self
	 */
	public function setLatLong(LatLong $latlong = null) {
		$this->latlong = $latlong;
		return $this;
	}

	/**
	 * Get current lat long constraint
	 * @return LatLong|null
	 */
	public function getLatLong() {
		return $this->latlong;
	}

	/**
	 * Set search area radius
	 * 
	 * @param float $radius Radius
	 * @return self
	 */
	public function setRadius($radius = 0) {
		$this->radius = (float) $radius;
		return $this;
	}

	/**
	 * Get current search area radius
	 * @return float
	 */
	public function getRadius() {
		return $this->radius;
	}

	/**
	 * Set users to report for
	 * 
	 * @param int[] $users User guids
	 * @return self
	 */
	public function setUsers($users = null) {
		if (isset($users)) {
			if (!is_array($users)) {
				$users = [$users];
			}
			foreach ($users as $key => $value) {
				if ($value instanceof ElggEntity) {
					$users[$key] = $value->guid;
				} else {
					$users[$key] = (int) $value;
				}
			}
		} else {
			$users = [];
		}

		$this->users = array_filter($users);
		return $this;
	}

	/**
	 * Get users report constrained for
	 * @return int
	 */
	public function getUsers() {
		return $this->users;
	}

	/**
	 * Set dataset point interval
	 *
	 * @param string $interval Interval
	 * @return self
	 */
	public function setInterval($interval) {

		$start_ts = $this->getTimeFrame()->getStart()->getTimestamp();
		$end_ts = $this->getTimeFrame()->getEnd()->getTimestamp();

		switch ($interval) {
			case 'hour' :
				$start_ts = Time::getHourStart($start_ts);
				$end_ts = Time::getHourEnd($end_ts);
				$modifier = '+1 hour';
				$format = 'H:i j M, Y';
				break;

			default :
			case 'day' :
				$start_ts = Time::getDayStart($start_ts);
				$end_ts = Time::getDayEnd($end_ts);
				$modifier = '+1 day';
				$format = 'j M, Y';
				$interval = 'day';
				break;

			case 'month' :
				$start_ts = Time::getMonthStart($start_ts);
				$end_ts = Time::getMonthEnd($end_ts);
				$modifier = '+1 month';
				$format = 'M Y';
				break;

			case 'year' :
				$start_ts = Time::getYearStart($start_ts);
				$end_ts = Time::getYearEnd($end_ts);
				$modifier = '+1 year';
				$format = 'Y';
				break;
		}

		$this->setTimeFrame(new TimeFrame((int) $start_ts, (int) $end_ts));

		$this->interval = $interval;
		$this->modifier = $modifier;
		$this->format = $format;

		return $this;
	}

	/**
	 * Get interval modifier
	 *
	 * @return string
	 */
	public function getModifier() {
		return $this->modifier;
	}

	/**
	 * Get interval label format
	 *
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * Get WHERE clause for time frame
	 * 
	 * @param string $alias    Table alias
	 * @param string $time_col Time column name
	 * @return string
	 */
	public function getTimeFrameWhereSql($alias = '', $time_col = 'time_started') {

		$time = $this->getTimeFrame();
		$start = (int) $time->getStart()->getTimestamp();
		$end = (int) $time->getEnd()->getTimestamp();

		if ($alias) {
			$time_col = "$alias.$time_col";
		}

		return "$time_col BETWEEN $start AND $end";
	}

	/**
	 * Get WHERE clause for proximity
	 *
	 * @param string $alias    Table alias
	 * @param string $lat_col  Latitude column name
	 * @param string $long_col Longitude column name
	 * @return string
	 */
	protected function getProximityWhereSql($alias = '', $lat_col = 'latitude', $long_col = 'longitude') {

		if (!$this->radius || !$this->latlong) {
			return '';
		}

		$lat = $this->latlong->getLat();
		$long = $this->latlong->getLong();

		if ($alias) {
			$lat_col = "$alias.$lat_col";
			$long_col = "$alias.$long_col";
		}

		return "(((acos(sin(($lat*pi()/180))
				*sin(($lat_col*pi()/180))+cos(($lat*pi()/180))
				*cos(($lat_col*pi()/180))
				*cos((($long-$long_col)*pi()/180)))))*180/pi())
				*60*1.1515*1.60934 <= $this->radius";
	}

	/**
	 * Get WHERE clause for users
	 *
	 * @param string $alias       Table alias
	 * @param string $guid_column User guid column name
	 * @return string
	 */
	protected function getUsersWhereSql($alias = '', $guid_column = 'user_guid') {

		if (empty($this->users)) {
			return '';
		}

		if ($alias) {
			$guid_column = "$alias.$guid_column";
		}

		$users_in = implode(',', $this->users);

		return "$guid_column IN ($users_in)";
	}

	protected function getSessionsQueryBuilder() {

		$q = new QueryBuilder();
		$q->from('analytics_sessions')
				->where($this->getTimeFrameWhereSql())
				->where($this->getProximityWhereSql())
				->where($this->getUsersWhereSql());

		return $q;
	}

	protected function getSessionsSubqueryWhereSql($alias = '', $session_id_col = 'session_id') {

		$select = $this->getSessionsQueryBuilder()
				->select('id')
				->getSql();

		if ($alias) {
			$session_id_col = "$alias.$session_id_col";
		}

		$q = new QueryBuilder();
		$q->where($session_id_col, 'IN', "($select)");

		return $q;
	}

	/**
	 * Get sessions data for a given time period
	 *
	 * @param array $options Options
	 * @return Session[]|false
	 */
	public function getSessions(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = $this->getSessionsQueryBuilder();

		if ($count) {
			$q->select('COUNT(*)', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->limit($limit)
					->offset($offset)
					->order_by('time_ended', 'DESC');
			
			$data = get_data($q->getSql(), [$this, 'rowToSession']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get average session duration
	 * @return int Seconds
	 */
	public function getAvgSessionDuration() {

		$q = $this->getSessionsQueryBuilder()
				->select('AVG(time_ended - time_started)', 'avg');

		$data = get_data_row($q->getSql());
		return empty($data) ? 0 : round($data->avg);
	}

	/**
	 * Get unique visitors data for a given time period
	 *
	 * @param array $options Options
	 * @return Session[]|false
	 */
	public function getVisitors(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = $this->getSessionsQueryBuilder();

		if ($count) {
			$q->select('COUNT(DISTINCT(fingerprint))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'sessions_count')
					->group_by('fingerprint')
					->limit($limit)
					->offset($offset)
					->order_by('sessions_count', 'DESC')
					->order_by('time_ended', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToSession']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get country data for a given time period
	 *
	 * @param array $options Options
	 * @return Session[]|false
	 */
	public function getCountries(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = $this->getSessionsQueryBuilder();

		if ($count) {
			$q->select('COUNT(DISTINCT(country))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'sessions_count')
					->group_by('country')
					->limit($limit)
					->offset($offset)
					->order_by('sessions_count', 'DESC')
					->order_by('time_ended', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToSession']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get city data for a given time period
	 *
	 * @param array $options Options
	 * @return Session[]|false
	 */
	public function getCities(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = $this->getSessionsQueryBuilder();

		if ($count) {
			$q->select('COUNT(DISTINCT(city))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'sessions_count')
					->group_by('city')
					->limit($limit)
					->offset($offset)
					->order_by('sessions_count', 'DESC')
					->order_by('time_ended', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToSession']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get posts data for a given time period
	 *
	 * @param array $options Options
	 * @return EntityEvent[]|false
	 */
	public function getPosts(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);
		$subtypes = elgg_extract('subtypes', $options);

		$q = new QueryBuilder();
		$q->from('analytics_entity_events')
				->where($this->getSessionsSubqueryWhereSql())
				->where('event', '=', "'create'")
				->where('object_type', '=', "'object'");

		if (isset($subtypes)) {
			$subtypes = (array) $subtypes;
			foreach ($subtypes as $key => $subtype) {
				$subtypes[$key] = "'" . sanitize_string($subtype) . "'";
			}
			$subtypes = array_filter($subtypes);
			if (!empty($subtypes)) {
				$q->where('object_subtype', 'IN', '(' . implode(',', $subtypes) . ')');
			}
		}

		if ($count) {
			$q->select('COUNT(DISTINCT(object_guid))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->group_by('object_guid')
					->limit($limit)
					->offset($offset)
					->order_by('time', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToEntityEvent']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get registrations data for a given time period
	 *
	 * @param array $options Options
	 * @return EntityEvent[]|false
	 */
	public function getRegistrations(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);
		$validated = elgg_extract('validated', $validated);

		$q = new QueryBuilder();
		$q->from('analytics_entity_events')
				->where($this->getSessionsSubqueryWhereSql())
				->where('event', '=', "'create'")
				->where('object_type', '=', "'user'");

		if (isset($validated)) {
			$validated = (int) $validated;
			$metastrings = elgg_get_metastring_map([
				'validated',
				$validated,
			]);
			$q->join('metadata', 'md', "object_guid = md.entity_guid AND md.name_id = '{$metastrings['validated']}'")
					->where("md.value_id = {$metastrings[$validated]}");
		}

		if ($count) {
			$q->select('COUNT(DISTINCT(object_guid))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->group_by('object_guid')
					->limit($limit)
					->offset($offset)
					->order_by('time', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToEntityEvent']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get registered users for a given time period
	 *
	 * @param array $options Options
	 * @return Session[]|false
	 */
	public function getRegisteredUsers(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = $this->getSessionsQueryBuilder();
		$q->where('user_guid', '!=', 0);
		
		if ($count) {
			$q->select('COUNT(DISTINCT(user_guid))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'sessions_count')
					->group_by('user_guid')
					->limit($limit)
					->offset($offset)
					->order_by('sessions_count', 'DESC')
					->order_by('time_ended', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToSession']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get guest users for a given time period
	 *
	 * @param array $options Options
	 * @return Session[]|false
	 */
	public function getGuestUsers(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		if ($this->getUsers()) {
			return $count ? 0 : false;
		}

		$q = $this->getSessionsQueryBuilder();
		$q->where('user_guid', '=', 0);
		
		if ($count) {
			$q->select('COUNT(DISTINCT(fingerprint))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'sessions_count')
					->group_by('fingerprint')
					->limit($limit)
					->offset($offset)
					->order_by('sessions_count', 'DESC')
					->order_by('time_ended', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToSession']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get page views data for a given time period
	 *
	 * @param array $options Options
	 * @return PageView[]|false
	 */
	public function getPageViews(array $options = []) {

		$count = elgg_extract('count', $options);
		$limit = elgg_extract('limit', $options);
		$offset = elgg_extract('offset', $options);
		$user_guid = elgg_extract('user_guid', $options);

		$q = new QueryBuilder();
		$q->from('analytics_page_views')
				->where($this->getSessionsSubqueryWhereSql($user_guid));

		if ($count) {
			$q->select('COUNT(*)', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->limit($limit)
					->offset($offset)
					->order_by('time', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToPageView']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get unique pages visited for a given time period
	 *
	 * @param array $options Options
	 * @return PageView[]|false
	 */
	public function getUniquePagesViewed(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = new QueryBuilder();
		$q->from('analytics_page_views')
				->where($this->getSessionsSubqueryWhereSql());

		if ($count) {
			$q->select('COUNT(DISTINCT(page_url))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'views_count')
					->group_by('page_url')
					->limit($limit)
					->offset($offset)
					->order_by('views_count', 'DESC')
					->order_by('time', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToPageView']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get entity views data for a given time period
	 *
	 * @param array $options Options
	 * @return PageView[]|false
	 */
	public function getEntityViews(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);

		$q = new QueryBuilder();
		$q->from('analytics_entity_views')
				->where($this->getSessionsSubqueryWhereSql());

		if ($count) {
			$q->select('COUNT(*)', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->limit($limit)
					->offset($offset)
					->order_by('time', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToEntityView']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Get entity views data for a given time period
	 *
	 * @param array $options Options
	 * @return PageView[]|false
	 */
	public function getUniqueEntitiesViewed(array $options = []) {

		$count = elgg_extract('count', $options, false);
		$limit = elgg_extract('limit', $options, 0);
		$offset = elgg_extract('offset', $options, 0);
		$type = elgg_extract('type', $options, 'object');
		$subtypes = elgg_extract('subtypes', $options);

		$q = new QueryBuilder();
		$q->from('analytics_entity_views')
				->where($this->getSessionsSubqueryWhereSql());

		if (!isset($subtypes)) {
			$subtypes = get_registered_entity_types($type);
		}

		$type = sanitize_string($type);
		$q->join('entities', 'e', 'e.guid = entity_guid')
				->where('e.type', '=', "'$type'");

		if (isset($subtypes)) {
			$subtypes = (array) $subtypes;
			foreach ($subtypes as $key => $subtype) {
				$subtypes[$key] = get_subtype_id($type, $subtype);
			}
			$subtypes = array_filter($subtypes);
			if (!empty($subtypes)) {
				$q->where('e.subtype', 'IN', '(' . implode(',', $subtypes) . ')');
			}
		}

		if ($count) {
			$q->select('COUNT(DISTINCT(entity_guid))', 'total');
			$data = get_data_row($q->getSql());
			return empty($data) ? 0 : $data->total;
		} else {
			$q->select('*')
					->select('COUNT(*)', 'views_count')
					->group_by('entity_guid')
					->limit($limit)
					->offset($offset)
					->order_by('views_count', 'DESC')
					->order_by('time', 'DESC');

			$data = get_data($q->getSql(), [$this, 'rowToEntityView']);
			return empty($data) ? false : $data;
		}
	}

	/**
	 * Construct a new session from DB row
	 *
	 * @param stdClass $row DB row
	 * @return Session
	 */
	public static function rowToSession(stdClass $row) {
		return new Session($row);
	}

	/**
	 * Construct a new page view from DB row
	 *
	 * @param stdClass $row DB row
	 * @return Session
	 */
	public static function rowToPageView(stdClass $row) {
		return new PageView($row);
	}

	/**
	 * Construct a new entity view from DB row
	 *
	 * @param stdClass $row DB row
	 * @return Session
	 */
	public static function rowToEntityView(stdClass $row) {
		return new EntityView($row);
	}

	/**
	 * Construct a new event from DB row
	 *
	 * @param stdClass $row DB row
	 * @return Session
	 */
	public static function rowToEvent(stdClass $row) {
		return new Event($row);
	}

	/**
	 * Construct a new entity event from DB row
	 *
	 * @param stdClass $row DB row
	 * @return Session
	 */
	public static function rowToEntityEvent(stdClass $row) {
		return new EntityEvent($row);
	}

	/**
	 * Prepare ReportingService from user input
	 *
	 * @return \ArckInteractive\Analytics\ReportingService
	 */
	public static function fromRequest() {

		$start = get_input('start') ?: null;
		if ($start) {
			$start = (int) Time::getDayStart($start);
		}
		$end = get_input('end') ?: null;
		if ($end) {
			$end = (int) Time::getDayEnd($end);
		} else {
			$end = (int) Time::getDayEnd($start);
		}

		$time = new TimeFrame($start, $end, null, false);

		$svc = new ReportingService($time);

		$svc->setInterval(get_input('interval', 'day'));

		$location = get_input('location');
		if (elgg_is_active_plugin('hypeMapsOpen') && $location) {
			$latlong = Encoder::fromLocation($location);
			$svc->setLatLong(new LatLong($latlong->getLat(), $latlong->getLong()));

			$radius = get_input('radius', 500);
			$svc->setRadius($radius);
		}

		$users = get_input('users', '');
		if (!is_array($users)) {
			$users = explode(',', $users);
		}
		$users = array_filter($users);
		if (!empty($users)) {
			$svc->setUsers($users);
		}

		return $svc;
	}

	/**
	 * Prepare datasets for given time intervals
	 *
	 * @param callable $callback Callback function to populate data
	 * @param array    $data     Original data
	 * @return array
	 */
	public static function datasetFactory(callable $callback, $data) {

		$svc = self::fromRequest();

		$ts = $svc->getTimeFrame()->getStart()->getTimestamp();
		$end_ts = $svc->getTimeFrame()->getEnd()->getTimestamp();

		while ($ts <= $end_ts) {

			$dt = new DateTime();
			$dt->setTimestamp($ts)->modify($svc->getModifier());
			$ts_next = $dt->getTimestamp();

			$day_time = new TimeFrame($ts, $ts_next);
			$svc->setTimeFrame($day_time);

			$data['labels'][] = date($svc->getFormat(), $ts);

			$data = call_user_func($callback, $svc, $data);

			$ts = $ts_next;
		}

		$data['datasets'] = array_values($data['datasets']);
		return $data;
	}

}
