<?php

/**
 * Fired during plugin activation
 *
 * @link       budda.test
 * @since      1.0.0
 *
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/includes
 * @author     dina <dina881@gmail.com>
 */
class Wp_Db_Budda_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$table_name2 = $wpdb->prefix . "budda";
		$table_name1 = $wpdb->prefix . "buddadates";
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');





		$sql="CREATE TABLE IF NOT EXISTS `".$table_name1."` (
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` datetime NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
   UNIQUE KEY booking_id_dates (booking_id, booking_date)
) $charset_collate;";

			dbDelta($sql);



			$sql =  "CREATE TABLE IF NOT EXISTS `".$table_name2."` (
  `booking_id` bigint(20) UNSIGNED AUTO_INCREMENT,
  `modification_date` datetime DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `secondname` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`booking_id`)
) $charset_collate;";
			dbDelta($sql);



		$startData = array(
			array(
				'booking_id' => '1',
				'modification_date' => '',
				'firstname' => 'Paul',
				'secondname' => 'Smith',
				'phone' => '+38(097)256-30-21',
				'comments' => '',
				'booking_date' => array('2016-02-25', '2016-02-20'),
				'approved' => '0'
			),

			array(
				'booking_id' => '2',
				'modification_date' => '',
				'firstname' => 'Jess',
				'secondname' => 'Simplton',
				'phone' => '+38(096)444-39-01',
				'comments' => '',
				'booking_date' => array('2016-02-19', '2016-02-29'),
				'approved' => '0'
			),

			array(
				'booking_id' => '3',
				'modification_date' => '',
				'firstname' => 'Judi',
				'secondname' => 'Loo',
				'phone' => '+38(067)777-80-22',
				'comments' => 'We need individual food menu, because we are vegeterians. What can you suppose?',
				'booking_date' => array('2016-01-01', '2016-01-08'),
				'approved' => '0'
			),

			array(
				'booking_id' => '4',
				'modification_date' => '',
				'firstname' => 'Jim',
				'secondname' => 'Hantington',
				'phone' => '+38(095)111-16-35',
				'comments' => '',
				'booking_date' => array('2016-02-07'),
				'approved' => '0'
			),

			array(
				'booking_id' => '5',
				'modification_date' => '',
				'firstname' => 'Lesly',
				'secondname' => 'Winkley',
				'phone' => '+38(090)222-30-69',
				'comments' => 'You have children rooms?',
				'booking_date' => array('2016-02-07', '2016-02-07'),
				'approved' => '0'
			)
		);


		foreach($startData as $key => $val){
			$toDb = array();
			foreach ($val as $key1 => $val1) {
				if($key1 == 'booking_date' || $key1 == 'booking_id' || $key1 == 'approved' || empty($val1)) continue;
				$toDb[$key1] = $val1;
			}
			$wpdb->insert(
				$table_name2,
				$toDb

			);
		}


		foreach($startData as $key => $val){
			$toDb = array();
			foreach ($val as $key1 => $val1) {
				if(($key1 != 'booking_date' && $key1 != 'booking_id' && $key1 != 'approved') || empty($val1)) continue;

				if($key1 != 'booking_date')
				  $toDb[$key1] = $val1;
				else {
					foreach($val1 as $k => $v){
						$toDb[$key1] = $val1[$k];
						$wpdb->insert(
							$table_name1,
							$toDb
						);
					}
				}

			}

		}



	}

}
