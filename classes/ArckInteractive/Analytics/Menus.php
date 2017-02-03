<?php

namespace ArckInteractive\Analytics;

use ElggMenuItem;

class Menus {
	
	/**
	 * Setup user hover menu
	 * 
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:user_hover"
	 * @param ElggMenuItem[] $return User hover
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupUserHoverMenu($hook, $type, $return, $params) {

		if (!elgg_is_admin_logged_in()) {
			return;
		}

		$entity = elgg_extract('entity', $params);

		$return[] = ElggMenuItem::factory([
			'name' => 'analytics',
			'text' => elgg_echo('analytics:menu:user_hover'),
			'href' => elgg_http_add_url_query_elements('analytics/users', [
				'users' => [$entity->guid],
			]),
			'section' => 'admin',
		]);

		return $return;
	}
}
