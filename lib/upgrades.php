<?php

if (!elgg_get_plugin_setting('db_setup', 'analytics')) {

	// Setup MySQL databases
	run_sql_script(dirname(dirname(__FILE__)) . '/install/mysql.sql');

	elgg_set_plugin_setting('db_setup', time(), 'analytics');
}