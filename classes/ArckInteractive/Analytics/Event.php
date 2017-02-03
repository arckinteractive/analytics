<?php

namespace ArckInteractive\Analytics;

use stdClass;

class Event {

	private $session_id;
	private $page_url;
	private $event;
	private $description;
	private $target;
	private $href;
	private $time;

	/**
	 * Constructor
	 *
	 * @param stdClass $row DB row
	 */
	public function __construct(stdClass $row) {
		foreach ($row as $key => $value) {
			$this->$key = $value;
		}
	}

	public function __get($name) {
		return $this->$name;
	}

}
