<?php
/**
 * Created by PhpStorm.
 * User: Dina
 * Date: 20.01.2016
 * Time: 22:11
 */
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be movies.
 */
class TT_Example_List_Table extends WP_List_Table {
    public $plugin_name;

    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     *
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     *
     * @var array
     **************************************************************************/


    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    public function __construct($plugin_name) {
        global $status, $page;

        $this->plugin_name = $plugin_name;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'booking',     //singular name of the listed records
            'plural' => 'bookings',    //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));

    }


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'booking_id':
            case 'modification_date':
            case 'firstname':
            case 'secondname':
            case 'phone':
            case 'comments':
            case 'booking_date':
            case 'approved':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    public function column_booking_id($item) {

        //Build row actions
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&booking=%s">' . __("Edit", $this->plugin_name) . '</a>', $this->plugin_name . '-addnew', 'edit', $item['booking_id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&booking=%s">' . __("Delete", $this->plugin_name) . '</a>', $_REQUEST['page'], 'delete', $item['booking_id']),
        );

        //Return the title contents
        return sprintf('%1$s %2$s',

            /*$1%s*/
            $item['booking_id'],
            /*$2%s*/
            $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'],
            /*$2%s*/
            $item['booking_id']
        );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'booking_id' => __("ID", $this->plugin_name),
            'modification_date' => __("Modification date", $this->plugin_name),
            'firstname' => __("Name", $this->plugin_name),
            'secondname' => __("Lastname", $this->plugin_name),
            'phone' => __("Phone", $this->plugin_name),
            'comments' => __("Details", $this->plugin_name),
            'booking_date' => __("Dates", $this->plugin_name),
            'approved' => __("Status", $this->plugin_name)
        );
        return $columns;
    }


    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    public function get_sortable_columns() {
        $sortable_columns = array(
            'booking_id' => array('booking_id', false),     //true means it's already sorted
            'firstname' => array('firstname', false),
            'approved' => array('approved', false),
            'secondname' => array('secondname', false),
            'modification_date' => array('modification_date', false),


        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    public function get_bulk_actions() {
        $actions = array(
            'delete' => __("Delete", $this->plugin_name),
            'approve' => __("Approve", $this->plugin_name)
        );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    public function process_bulk_action() {
        global $wpdb;
        $table_name2 = $wpdb->prefix . "budda";
        $table_name1 = $wpdb->prefix . "buddadates";


        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
            $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die(__("Nope! Security check failed!", $this->plugin_name));

        }

        $ids = array();

        if(isset($_GET['booking'])){

            foreach ((array)$_GET['booking'] as $id) {
                $id = intval($id);
                if ($id) $ids[] = (string)$id;
            }

            $limit = count($ids);

            if ($limit > 0) {
                $idsStr = join(',', array_fill(0, $limit, '%s'));
            }

        }



        $action = $this->current_action();

        switch ($action) {

            case 'delete':
                $wpdb->query(
                    $wpdb->prepare("DELETE FROM $table_name2  WHERE booking_id IN ($idsStr)",$ids)
                );
                break;

            case 'approve':
                foreach ($ids as $id) {
                    $wpdb->query($wpdb->prepare("UPDATE $table_name1 SET approved = 1 WHERE booking_id = %d", $id));

                }
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;


    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    public function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        $table_name2 = $wpdb->prefix . "budda";
        $table_name1 = $wpdb->prefix . "buddadates";


        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 3;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */

        $total_items = $wpdb->get_var("SELECT COUNT(booking_id) FROM $table_name2");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) * $per_page : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'booking_id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $mylink = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name2 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);


        //modify some data before printing
        foreach ($mylink as $row => $val) {
            $str = '';
            $status = '';
            $id = $val['booking_id'];
            $mylink2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_name1 . " WHERE booking_id = %d ", $id), ARRAY_A);


            foreach ($mylink2 as $key => $value) {
                if (sizeof($mylink2) - 1 == $key)
                    $str .= date("d.m.Y", strtotime($value['booking_date']));
                else
                    $str .= date("d.m.Y", strtotime($value['booking_date'])) . ', ';
                $status = $value['approved'];
            }

            //show dates string for bookings
            $mylink[$row]['booking_date'] = $str;


            //show status for booking (approved or not)
            if ($status == 1)
                $mylink[$row]['approved'] = __("approved", $this->plugin_name);
            if ($status == 0)
                $mylink[$row]['approved'] = __("not approved", $this->plugin_name);


        }

        $this->items = $mylink;

        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        // $data = $mylink;


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        //  $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        //$total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        // $data = array_slice($data,(($current_page-1)*$per_page),$per_page);


        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        //$this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page' => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
            )
        );
    }

    public function extra_tablenav($which) {
        global $pagenow, $plugin_page;


        $this_page = add_query_arg('page', $plugin_page, admin_url($pagenow));
        $add_link = add_query_arg('page', $this->plugin_name . '-addnew', $this_page);


        if ($which == "top") {
            echo '<h3><a href="' . $add_link . '">' . __("Add New", $this->plugin_name) . '</a></h3>';
        }
    }

}