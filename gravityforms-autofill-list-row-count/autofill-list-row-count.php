<?php
/**
 * Plugin Name: Gravity Forms Autofill List Row Count
 * Plugin URI: https://typewheel.xyz/project/autofill-list-row-count
 * Description: Adds option for autofilling the number of rows in a list to another field within a Gravity Form.
 * Version: 1.0.beta1
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

define( 'GF_AUTOFILL_LIST_ROW_COUNT_VERSION', '1.0.beta1' );

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
