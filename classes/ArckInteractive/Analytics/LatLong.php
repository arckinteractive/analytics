<?php

namespace ArckInteractive\Analytics;

class LatLong {

	protected $lat;
	protected $long;
	protected $location;

	/**
	 * Constructor
	 * 
	 * @param float  $lat      Latitude
	 * @param float  $long     Longitude
	 * @param string $location Location name/address
	 */
	public function __construct($lat, $long, $location = '') {
		$this->lat = $lat;
		$this->long = $long;
		$this->location = $location;
	}

	/**
	 * Returns latitude
	 * @return float
	 */
	public function getLat() {
		return (float) $this->lat;
	}

	/**
	 * Returns longitude
	 * @return float
	 */
	public function getLong() {
		return (float) $this->long;
	}

	/**
	 * Returns location address
	 * @return string
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * Export lat, long and location to an array
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}
}
