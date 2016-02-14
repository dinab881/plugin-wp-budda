<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       budda.test
 * @since      1.0.0
 *
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/admin
 * @author     dina <dina881@gmail.com>
 */
class Wp_Db_Budda_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Db_Budda_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Db_Budda_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-db-budda-admin.css', array(), $this->version, 'all' );

		if ( 'budda-booking_page_wp-db-budda-addnew' == get_current_screen() -> id ) {

			wp_enqueue_style('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-cbf-admin.css', array( 'wp-color-picker' ), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Db_Budda_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Db_Budda_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( 'budda-booking_page_wp-db-budda-addnew' == get_current_screen() -> id ) {


			wp_enqueue_script( $this->plugin_name.'-ajax-dates', plugin_dir_url( __FILE__ ) . 'js/wp-db-budda-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );

			$data_nonce = wp_create_nonce('get_booked_data');

			$param_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $data_nonce,
				'get_action' => NULL,
				'id' => NULL
			);

			if((isset($_GET['action']) && ($_GET['action']=='edit')) && isset($_GET['booking'])){
				$get_action = 'edit';
				$id = intval($_GET['booking']);

				$param_array =	array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => $data_nonce,
					'get_action' => $get_action,
					'id' => $id

				);

			}

			wp_localize_script( $this->plugin_name.'-ajax-dates', 'ajax_booked_dates',$param_array);








		}




	}
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
		add_menu_page( __('Bookings listing',$this->plugin_name), __('Budda Booking',$this->plugin_name), 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));

		//add_submenu_page( $this->plugin_name, __('Budda Booking',$this->plugin_name), 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
		add_submenu_page( $this->plugin_name, __('Budda Bookings',$this->plugin_name), __('Budda Bookings',$this->plugin_name), 'manage_options', $this->plugin_name);

		add_submenu_page($this->plugin_name,  __('Add new booking',$this->plugin_name), __('Add new',$this->plugin_name), 'manage_options', $this->plugin_name.'-addnew', array($this, 'display_plugin_addnew_page'));


	}

	public function add_new_booking(){
		if( !current_user_can( "manage_options" ) )
			wp_die( "Insufficient privileges!" );



		if(sizeof($_POST)>0 && is_array($_POST[$this->plugin_name])) {

			if (!isset($_POST[$this->plugin_name]["action"]) && $_POST[$this->plugin_name]["action"] != 'add_new_booking')
				return;

			check_admin_referer($this->plugin_name . '-addnew');


			$message = '';
			require_once plugin_dir_path( dirname( __FILE__ ) ).'includes/class-wp-db-budda-validator.php';
			$validatorCls = new Wp_Db_Budda_Validator();


			$myErrors = new WP_Error();
			if(!isset($_POST[$this->plugin_name]['dates']) || empty($_POST[$this->plugin_name]['dates'])) {
				$myErrors->add('required_dates', __('Please select dates for booking', $this->plugin_name));
			}
			elseif($validatorCls->validate_dates($_POST[$this->plugin_name]['dates']) !== true) {
				$myErrors->add('invalid_dates', __('Dates are invalid', $this->plugin_name));
			}

			if(!isset($_POST[$this->plugin_name]['firstname']) || empty($_POST[$this->plugin_name]['firstname'])){
				$myErrors->add('required_firstname', __('Please fill your first name', $this->plugin_name));
			}

			if(!isset($_POST[$this->plugin_name]['lastname']) || empty($_POST[$this->plugin_name]['lastname'])) {
				$myErrors->add('required_lastname', __('Please fill your last name', $this->plugin_name));
			}

			if(!isset($_POST[$this->plugin_name]['phone']) || empty($_POST[$this->plugin_name]['phone'])) {
				$myErrors->add('require_phone', __('Please fill phone number', $this->plugin_name));
			}
			elseif(!$validatorCls->validate_phone($_POST[$this->plugin_name]['phone'])) {
				$myErrors->add('invalid_phone', __('Phone has invalid format', $this->plugin_name));
			}

			if (is_wp_error($myErrors) && ! empty( $myErrors->errors )){


				$class = "error";

				foreach($myErrors->get_error_messages() as $msg){
					$message.= $msg.'</br>';
				}
				echo"<div class=\"$class\"> <p>$message</p></div>";

			}

			else{
				global $wpdb;
				$table_name2 = $wpdb->prefix . "budda";
				$table_name1 = $wpdb->prefix . "buddadates";



				$valid_data = array();

				$valid_data['dates'] = sanitize_text_field($_POST[$this->plugin_name]['dates']);

				$arr_dates = explode(',',$valid_data['dates']);

				$valid_data['firstname'] = sanitize_text_field($_POST[$this->plugin_name]['firstname']);
				$valid_data['lastname'] = sanitize_text_field($_POST[$this->plugin_name]['lastname']);
				$valid_data['phone'] = sanitize_text_field($_POST[$this->plugin_name]['phone']);
				$valid_data['details'] = sanitize_text_field($_POST[$this->plugin_name]['details']);


				if((isset($_GET['action']) && ($_GET['action']=='edit')) && isset($_GET['booking'])){


					$id = intval($_GET['booking']);

					$user_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name2 WHERE booking_id = %d", $id));


					if($user_count>0){

						$wpdb->update(
							$table_name2,
							array( 'booking_id' => $id ),
							array(
								'firstname' => $valid_data['firstname'],
								'secondname' => $valid_data['lastname'],
								'phone' => $valid_data['phone'],
								'comments' => $valid_data['details']
							)
						);


						$wpdb->query(
							$wpdb->prepare("DELETE FROM $table_name1 WHERE booking_id = %d",$id)
						);

						$num_deleted = $wpdb->rows_affected;


						if($num_deleted>0) {

							foreach ($arr_dates as $date) {
								$wpdb->insert(
									$table_name1,
									array(
										'booking_id' => $id,
										'booking_date' => $date

									)
								);
							}
						}


					}

				}

				else {

					$wpdb->insert(
						$table_name2,
						array(
							'firstname' => $valid_data['firstname'],
							'secondname' => $valid_data['lastname'],
							'phone' => $valid_data['phone'],
							'comments' => $valid_data['details']
						)
					);

					$id = $wpdb->insert_id;

					foreach ($arr_dates as $date) {
						$wpdb->insert(
							$table_name1,
							array(
								'booking_id' => $id,
								'booking_date' => $date

							)
						);
					}
				}
				$class = "updated";
				$message = __('Information was successfully saved', $this->plugin_name);
				echo"<div class=\"$class\"> <p>$message</p></div>";

			}
		}
	}
	public function booked_dates_ajax_handler(){

		global $wpdb;
		check_ajax_referer('get_booked_data');

		$table_name2 = $wpdb->prefix."budda";
		$table_name1 = $wpdb->prefix."buddadates";

		$get_edit = $_POST['get_action'];
		$booking_id = intval($_POST['id']);


		if($get_edit == 'edit' && $booking_id>0){

			$dates1=$wpdb->get_col($wpdb->prepare("SELECT DATE(booking_date) FROM $table_name1 WHERE booking_id = %d",$booking_id));

			$dates2=$wpdb->get_col($wpdb->prepare("SELECT DATE(booking_date) FROM $table_name1 WHERE booking_id != %d",$booking_id));

			$dates = array('current_user_dates' => $dates1, 'booked_dates' => $dates2);

		}
		else {
			$dates=array('booked_dates' =>$wpdb->get_col("SELECT DATE(booking_date) FROM $table_name1"));
		}





		wp_send_json( $dates );
	}


	/*public function edit_dates_ajax_handler(){
		global $pagenow;
		if (( $pagenow == 'admin.php' ) && ($_GET['page'] ==  $this->plugin_name.'-addnew') && ($_GET['action'] == 'edit') && (isset($_GET['booking'])) ) {
			$id = intval($_GET['booking']) ;

		}
		$data = array(
			'id' => $id
		);
		wp_send_json( $data );


	}*/




    /*public function add_custom_wp_list_table(){
		require_once( 'partials/class-wp-list-table-custom.php' );
	}*/

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */

	public function display_plugin_setup_page() {
		include_once( 'partials/wp-db-budda-admin-display.php' );
	}

	public function display_plugin_addnew_page() {
		include_once( 'partials/wp-db-budda-admin-display-form.php' );
	}

}
