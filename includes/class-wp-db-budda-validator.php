<?php
/**
 * Created by PhpStorm.
 * User: Dina
 * Date: 13.02.2016
 * Time: 0:12
 */

class Wp_Db_Budda_Validator{

    public function validate_phone( $num ) {
        return preg_match( '/^\+\d{2}\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $num );
    }

    public function validate_dates( $dates ) {
        $arr_dates = explode(',',$dates);
        foreach($arr_dates as $date) {
            // first test: pattern matching
            if (!preg_match('!\d{4}-\d{2}-\d{2}!', $date))
                return false;
            // second test: is date valid?
            $timestamp = strtotime($date);
            if (!$timestamp)
            return false;

            // So far, so good
        return true;
        }
    }
}