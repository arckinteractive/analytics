<?php

namespace ArckInteractive\Analytics;

class Router {
	
	/**
	 * Handle /analytics
	 * 
	 * @param array $segments Segments
	 * @return bool
	 */
	public static function analyticsHandler($segments) {

		$page = array_shift($segments);

		switch ($page) {

			default :
			case 'dashboard' :
				echo elgg_view_resource('analytics/dashboard');
				return true;

			case 'datasets' :
				$type = array_shift($segments);
				echo elgg_view_resource("analytics/datasets/$type");
				return true;

			case 'stats' :
				echo elgg_view_resource("analytics/stats");
				return true;
		}

		return false;
	}
}
