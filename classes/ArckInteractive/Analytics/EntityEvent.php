<?php

namespace ArckInteractive\Analytics;

use stdClass;

class PageView {

	private $session_id;
	private $page_url;
	private $event;
	private $object_guid;
	private $subject_guid;
	private $target_guid;
	private $object_type;
	private $object_subtype;
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
