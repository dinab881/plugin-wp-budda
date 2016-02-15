<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       budda.test
 * @since      1.0.0
 *
 * @package    Wp_Db_Budda
 * @subpackage Wp_Db_Budda/admin/partials
 */

require_once('class-wp-list-table-custom.php');
//Create an instance of our package class...

$plugin_name = $this->plugin_name;

$testListTable = new TT_Example_List_Table($plugin_name);
//Fetch, prepare, sort, and filter our data...
$testListTable->prepare_items();
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <div id="icon-users" class="icon32"><br/></div>
    <h2>List Table Test</h2>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="movies-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
        <?php $testListTable->display() ?>
    </form>

</div>
