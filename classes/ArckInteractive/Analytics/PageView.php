<?php

namespace ArckInteractive\Analytics;

use stdClass;

class PageView {

	private $session_id;
	private $page_url;
	private $referrer_url;
	private $entity_guid;
	private $page_owner_guid;
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
