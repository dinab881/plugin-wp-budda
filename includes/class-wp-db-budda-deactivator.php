<?php

/**
 * Fired during plugin deactivation
 *
 * @link       budda.test
 * @since      1.0.0
 *
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/includes
 * @author     dina <dina881@gmail.com>
 */
class Wp_Db_Budda_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		global $wpdb;
		$table_name2 = $wpdb->prefix . "budda";
		$table_name1 = $wpdb->prefix . "buddadates";
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name1}" );
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name2}" );

	}

}
