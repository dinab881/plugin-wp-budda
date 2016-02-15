<?php
/**
 * Created by PhpStorm.
 * User: Dina
 * Date: 12.02.2016
 * Time: 15:10
 */
global $pagenow;
$db_firstname = $db_lastname = $db_phone = $db_details = '';


if (($pagenow == 'admin.php') && ($_GET['page'] == $this->plugin_name . '-addnew') && (isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['booking']))) {
    global $wpdb;

    $table_name2 = $wpdb->prefix . "budda";
    $table_name1 = $wpdb->prefix . "buddadates";

    $id = intval($_GET['booking']);
    $db_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name2 WHERE booking_id=%d", $id), ARRAY_A);


    $db_firstname = $db_data['firstname'];
    $db_lastname = $db_data['secondname'];
    $db_phone = $db_data['phone'];
    $db_details = $db_data['comments'];
}

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <form method="post" name="new_booking" action="">
        <input type="hidden" id="<?php echo $this->plugin_name; ?>-action"
               name="<?php echo $this->plugin_name; ?>[action]" value="add_new_booking"/>
        <?php wp_nonce_field($this->plugin_name . '-addnew'); ?>

        <div id="booking-dates"></div>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-dates">
                <input type="hidden" id="<?php echo $this->plugin_name; ?>-dates"
                       name="<?php echo $this->plugin_name; ?>[dates]" value="" class="addnew-dates"/>
            </label>
        </fieldset>

        <fieldset>
            <legend><span><?php _e('First name*', $this->plugin_name); ?></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-firstname">
                <input type="text" id="<?php echo $this->plugin_name; ?>-firstname"
                       name="<?php echo $this->plugin_name; ?>[firstname]" value="<?php echo $db_firstname; ?>"/>
            </label>
        </fieldset>


        <fieldset>
            <legend><span><?php _e('Last name*', $this->plugin_name); ?></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-lastname">
                <input type="text" id="<?php echo $this->plugin_name; ?>-lastname"
                       name="<?php echo $this->plugin_name; ?>[lastname]" value="<?php echo $db_lastname; ?>"/>
            </label>
        </fieldset>


        <fieldset>
            <legend><span><?php _e('Phone*', $this->plugin_name); ?></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-phone">
                <input type="text" id="<?php echo $this->plugin_name; ?>-phone"
                       name="<?php echo $this->plugin_name; ?>[phone]"
                       placeholder="<?php _e('+__(___)___-__-__', $this->plugin_name); ?>"
                       value="<?php echo $db_phone; ?>"/>
            </label>
        </fieldset>


        <fieldset>
            <legend><span><?php _e('Details', $this->plugin_name); ?></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-details">
                <textarea id="<?php echo $this->plugin_name; ?>-details"
                          name="<?php echo $this->plugin_name; ?>[details]"><?php echo $db_details; ?></textarea>
            </label>
        </fieldset>


        <?php submit_button(__('Send', $this->plugin_name), 'primary', 'submit', TRUE); ?>

    </form>

</div>
