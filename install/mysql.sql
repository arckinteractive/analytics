CREATE TABLE IF NOT EXISTS `prefix_analytics_sessions` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`fingerprint` VARCHAR(255) NOT NULL,
	`user_guid` bigint(20) unsigned DEFAULT 0,
	`time_started` int(11) NOT NULL,
	`time_ended` int(11) NOT NULL,
	`ip_address` varchar(46) NOT NULL,
	`city` text NOT NULL,
	`state` text NOT NULL,
	`country` varchar(2) NOT NULL,
	`latitude` varchar(20) DEFAULT NULL,
    `longitude` varchar(20) DEFAULT NULL,
	`timezone` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `user_guid` (`user_guid`),
	KEY `fingerprint` (`fingerprint`),
	KEY	`time_started` (`time_started`),
	KEY	`time_ended` (`time_ended`),
	KEY `city` (`city`(255)),
	KEY `state` (`state`(255)),
	KEY `country` (`country`),
	KEY `latitude` (`latitude`),
	KEY `longitude` (`longitude`),
	KEY `timezone` (`timezone`(255))
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_analytics_page_views` (
	`session_id` bigint(20) unsigned NOT NULL,
	`page_url` text NOT NULL,
	`page_title` text NOT NULL,
	`referrer_url` text NOT NULL,
	`entity_guid` bigint(20) unsigned DEFAULT 0,
	`page_owner_guid` bigint(20) unsigned DEFAULT 0,
	`time` int(11) NOT NULL,
	KEY `session_id` (`session_id`),
	KEY `entity_guid` (`entity_guid`),
	KEY `page_owner_guid` (`page_owner_guid`),
	KEY `page_url` (`page_url`(255)),
	KEY `page_title` (`page_title`(255)),
	KEY `referrer_url` (`referrer_url`(255)),
	KEY	`time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_analytics_entity_views` (
	`session_id` bigint(20) unsigned NOT NULL,
	`page_url` text NOT NULL,
	`entity_guid` bigint(20) unsigned DEFAULT 0,
	`view_name` text NOT NULL,
	`full_view` enum('yes','no') NOT NULL DEFAULT 'no',
	`time` int(11) NOT NULL,
	KEY `session_id` (`session_id`),
	KEY `entity_guid` (`entity_guid`),
	KEY `page_url` (`page_url`(255)),
	KEY `view_name` (`view_name`(255)),
	KEY `full_view` (`full_view`),
	KEY	`time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_analytics_events` (
	`session_id` bigint(20) unsigned NOT NULL,
	`page_url` text NOT NULL,
	`event` VARCHAR(255) NOT NULL,
	`description` text NOT NULL,
	`target` VARCHAR(255) NOT NULL,
	`href` text NOT NULL,
	`time` int(11) NOT NULL,
	KEY `session_id` (`session_id`),
	KEY `page_url` (`page_url`(255)),
	KEY `href` (`href`(255)),
	KEY	`event` (`event`),
	KEY	`target` (`target`),
	KEY	`time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_analytics_entity_events` (
	`session_id` bigint(20) unsigned NOT NULL,
	`page_url` text NOT NULL,
	`event` VARCHAR(255) NOT NULL,
	`subject_guid` bigint(20) unsigned DEFAULT 0,
	`object_guid` bigint(20) unsigned DEFAULT 0,
	`target_guid` bigint(20) unsigned DEFAULT 0,
	`object_type` VARCHAR(255) NOT NULL,
	`object_subtype` VARCHAR(255) NOT NULL,
	`time` int(11) NOT NULL,
	KEY `session_id` (`session_id`),
	KEY `page_url` (`page_url`(255)),
	KEY	`event` (`event`),
	KEY	`subject_guid` (`subject_guid`),
	KEY	`object_guid` (`object_guid`),
	KEY	`target_guid` (`target_guid`),
	KEY	`object_type` (`object_type`),
	KEY	`object_subtype` (`object_subtype`),
	KEY	`time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_analytics_benchmarks` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`target_guid` bigint(20) unsigned DEFAULT 0,
	`metric` varchar(255) DEFAULT NULL,
	`value` varchar(255) DEFAULT NULL,
	`time` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `target_guid` (`target_guid`),
	KEY	`time` (`time`),
	KEY `metric` (`metric`),
	KEY `value` (`value`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

