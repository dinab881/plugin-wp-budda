<?php
/**
 * Created by PhpStorm.
 * User: Dina
 * Date: 07.02.2016
 * Time: 17:55
 */


require_once('../../../wp-load.php');
?>

< pre >
<?php
global $wpdb;
        $wpdb->show_errors();
        $table_name2 = $wpdb->prefix . "budda";
		$table_name1 = $wpdb->prefix . "buddadates";
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');





            $sql="CREATE TABLE IF NOT EXISTS `".$table_name1."`(
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` datetime NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
   UNIQUE KEY booking_id_dates (booking_id, booking_date)
) $charset_collate;";
print_r($sql);
            dbDelta($sql,false);


/*echo $table_name2;
$sql = "DROP TABLE IF_EXISTS '".$table_name2."'";
print_r($sql);
$e = $wpdb->query($sql);
die(var_dump($e));*/


            $sql =  "CREATE TABLE IF NOT EXISTS `".$table_name2."`(
  `booking_id` bigint(20) UNSIGNED AUTO_INCREMENT,
  `modification_date` datetime DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `secondname` varchar(50) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`booking_id`)
) $charset_collate;";
print_r($sql);
            dbDelta($sql,false);



		/*$firstname = 'Paul';
		$secondname = 'Smith';
		$phone='0774589321';

		$wpdb->insert(
            $table_name1,
            array(
                'booking_id' => 1,
                'booking_date' => current_time( 'mysql' )

            ),
            array(
                '%d',
                '%s'
            )
        );

		$wpdb->insert(
            $table_name1,
            array(
                'booking_id' => 2,
                'booking_date' => current_time( 'mysql' )

            )
        );

		$wpdb->insert(
            $table_name2,
            array(
                'firstname' => $firstname,
                'secondname' => $secondname,
                'phone' => $phone
            )
        );*/



$wpdb->print_error();
?>
< /pre >
