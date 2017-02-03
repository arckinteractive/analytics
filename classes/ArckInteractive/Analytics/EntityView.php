<?php

namespace ArckInteractive\Analytics;

use ElggEntity;
use stdClass;

class EntityView {

	private $session_id;
	private $page_url;
	private $entity_guid;
	private $view_name;
	private $full_view;
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

	/**
	 * Get entity viewed
	 * @return ElggEntity
	 */
	public function getEntity() {
		return get_entity($this->entity_guid);
	}
}
