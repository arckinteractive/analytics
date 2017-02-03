<?php

namespace ArckInteractive\Analytics;

use DateTime;
use DateTimeZone;

class TimeFrame {

	private $start;
	private $end;
	private $tz;
	private $full_day;

	/**
	 * Constructor
	 *
	 * @param mixed  $start    Start time
	 * @param mixed  $end      End time
	 * @param string $tz       Timezone
	 * @param bool   $full_day Normalize start/end to full day increments
	 */
	public function __construct($start = null, $end = null, $tz = null, $full_day = null) {
		if (!isset($end)) {
			$end = time();
		}

		if (!isset($start)) {
			$start = strtotime('-1 month', $end);
		}

		if (!isset($full_day)) {
			$full_day = $end - $start > 24 * 60 * 60;
		}
		
		$this->setStart($start);
		$this->setEnd($end);
		$this->setTimezone($tz);
		$this->setFullDay($full_day);
	}

	/**
	 * Set start time
	 * 
	 * @param mixed $start Timestamp or string
	 * @return self
	 */
	public function setStart($start) {
		$this->start = $start;
		return $this;
	}

	/**
	 * Get start time
	 * @return DateTime
	 */
	public function getStart() {

		$ts = $this->start;
		$tz = $this->tz;
		
		$dt = new DateTime(null, new DateTimeZone($tz));
		(is_int($ts)) ? $dt->setTimestamp($ts) : $dt->modify($ts);
		if ($this->full_day) {
			$dt->setTime(0, 0, 0);
		}

		return $dt;
	}

	/**
	 * Set end time
	 *
	 * @param mixed $end Timestamp or string
	 * @return self
	 */
	public function setEnd($end) {
		$this->end = $end;
		return $this;
	}

	/**
	 * Get end time
	 * @return DateTime
	 */
	public function getEnd() {

		$ts = $this->end;
		$tz = $this->tz;

		$dt = new DateTime(null, new DateTimeZone($tz));
		(is_int($ts)) ? $dt->setTimestamp($ts) : $dt->modify($ts);
		if ($this->full_day) {
			$dt->setTime(23, 59, 59);
		}

		return $dt;
	}

	/**
	 * Sets timezone
	 *
	 * @param string $timezone Timezone
	 * @return self
	 */
	public function setTimezone($timezone = null) {
		if (!Time::isValidTimezone($timezone)) {
			$timezone = Time::getClientTimezone();
		}
		$this->tz = $timezone;
		return $this;
	}

	/**
	 * Returns timezone
	 *
	 * @return string
	 */
	public function getTimezone() {
		return $this->tz;
	}

	/**
	 * Normalize start/end to full day increments
	 * 
	 * @param bool $full_day Flag
	 * @return self
	 */
	public function setFullDay($full_day = true) {
		$this->full_day = $full_day;
		return $this;
	}

	/**
	 * Is normalization enabled?
	 * @return bool
	 */
	public function getFullDay() {
		return $this->full_day;
	}
}
