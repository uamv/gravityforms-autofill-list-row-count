<?php

/**
 * Plugin Name: Gravity Forms Autofill List Row Count
 * Plugin URI: https://typewheel.xyz/project/autofill-list-row-count
 * Description: Adds option for autofilling the number of rows in a list to another field within a Gravity Form.
 * Version: 1.0.beta3
 * Author: Typewheel
 * Author URI: https://typewheel.xyz/
 * Typewheel Update ID: 3
 *
 * @package Gravity Forms Autofill List Row Count
 * @version 1.0.beta1
 * @author uamv
 * @copyright Copyright (c) 2017, uamv
 * @link http://typewheel.xyz
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

define( 'GF_AUTOFILL_LIST_ROW_COUNT_VERSION', '1.0.beta3' );

require_once( 'class-typewheel-updater.php' );
require_once( 'typewheel-notice/class-typewheel-notice.php' );

add_action( 'gform_loaded', array( 'GF_Autofill_List_Row_Count_Bootstrap', 'load' ), 5 );

class GF_Autofill_List_Row_Count_Bootstrap {

    public static function load() {

        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gfautofilllistrowcount.php' );

        GFAddOn::register( 'GFAutofillListRowCount' );

    }

}

function gf_autofill_list_row_count() {
    return GFAutofillListRowCount::get_instance();
}


/**** DECLARE TYPEWHEEL NOTICES ****/
if ( ! function_exists( 'gf_autofill_list_row_count_notices' ) && apply_filters( 'show_typewheel_notices', true ) ) {

	add_action( 'admin_notices', 'gf_autofill_list_row_count_notices' );
	/**
	 * Displays a plugin notices
	 *
	 * @since    1.0
	 */
	function gf_autofill_list_row_count_notices() {

		$prefix = str_replace( '-', '_', dirname( plugin_basename(__FILE__) ) );

		// Define the notices
		$typewheel_notices = array(
			$prefix . '-give' => array(
				'trigger' => true,
				'time' => time() + 604800,
				'dismiss' => array( 'month' ),
				// 'type' => 'success',
				'content' => 'Is <strong>GF Autofill List Row Count</strong> working well for you? Please consider a <a href="https://typewheel.xyz/give/?ref=GF%20Autofill%20List%20Row%20Count" target="_blank">small donation</a> to encourage further development.',
				'icon' => 'heart',
				'style' => array( 'background-image' => 'linear-gradient( to bottom right, rgb(215, 215, 215), rgb(231, 211, 186) )', 'border-left' => '0' ),
				'location' => array( 'admin.php?page=gf_edit_forms', 'admin.php?page=gf_entries', 'admin.php?page=gf_settings', 'admin.php?page=gf_addons' ),
			),
		);

		// get the notice class
		new Typewheel_Notice( $prefix, $typewheel_notices );

	} // end display_plugin_notices

}

/**
 * Deletes activation marker so it can be displayed when the plugin is reinstalled or reactivated
 *
 * @since    1.0
 */
function gf_autofill_list_row_count_remove_activation_marker() {

	$prefix = str_replace( '-', '_', dirname( plugin_basename(__FILE__) ) );

	delete_option( $prefix . '_activated' );

}
register_deactivation_hook( dirname(__FILE__) . '/autofill-list-row-count.php', 'gf_autofill_list_row_count_remove_activation_marker' );
