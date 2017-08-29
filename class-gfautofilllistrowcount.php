<?php

GFForms::include_addon_framework();

class GFAutofillListRowCount extends GFAddOn {

	protected $_version = GF_AUTOFILL_LIST_ROW_COUNT_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'autofilllistrowcount';
	protected $_path = 'gravityforms-autofill-list-row-count/autofill-list-row-count.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Typewheel: Gravity Forms Autofill List Row Count';
	protected $_short_title = 'Autofill List Row Count';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFAutofillListRowCount
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFAutofillListRowCount();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		$this->add_tooltip( 'autofill_list_row_count', sprintf(
            '<h6>%s</h6> %s',
            __( 'Sync Field Value' ),
            __( 'Insert the field ID of a list field on this form. When the number of rows is modified in the list, the value of this field will be autoupdated to match the number of rows.' )
        ) );

        add_action( 'gform_field_advanced_settings', array( $this, 'add_setting' ), 10, 2 );

        add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );

        add_filter( 'gform_pre_render', array( $this, 'sync_fields' ) );

	}


	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		return parent::scripts();
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {
		return parent::styles();
	}


	// # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

	/**
	 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
	 *
	 * @param string $button The string containing the input tag to be filtered.
	 * @param array $form The form currently being displayed.
	 *
	 * @return string
	 */



	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Adds field setting to Perks tab
	 *
	 * @since    1.0.beta1
	 */
	function add_setting( $position, $form_id ) { if ( - 1 == $position ) {?>

			<li class="autofill-list-row-count field_setting">
				<label for="visibility" class="section_label">
					<?php esc_html_e( 'Sync Field Value' ); ?></label>

				<label class="inline" for="autofill-list-row-count">
					<?php
					esc_html_e( 'Update value from row count of list field: ' );
					gform_tooltip( 'autofill_list_row_count' );
					?>
				</label>

				<input type="text" id="autofill-list-row-count" onkeyup="SetFieldProperty( 'autofillListRowCount', this.value);" />

			</li>

	<?php }}

	/**
	 * Populates field setting value and controls display on specified field types
	 *
	 * @since    1.0.beta1
	 */
	public function field_settings_js() { ?>

		<script type="text/javascript">
			(function($) {
				$(document).bind( 'gform_load_field_settings', function( event, field, form ) {
					// populates the stored value from the field back into the setting when the field settings are loaded
					$( '#autofill-list-row-count' ).attr( 'value', field['autofillListRowCount'] );
					// if our desired condition is met, we show the field setting; otherwise, hide it
					if( GetInputType( field ) == 'number' || GetInputType( field ) == 'singleproduct' || GetInputType( field ) == 'quantity' ) {
						$( '.autofill-list-row-count' ).show();
					} else {
						$( '.autofill-list-row-count' ).hide();
					}
				} );
			})(jQuery);
		</script>

	<?php }


	/**
	 * Check current sum and return form object
	 *
	 * @since    0.1
	 */
	public function sync_fields( $form ) {

		$list_counters = $this->get_list_counters( $form );

		if ( ! empty( $list_counters ) ) { ?>

			<script language="javascript">

				 jQuery(document).bind('gform_post_render', function(){

					 var FormRowToField = [
						  <?php

								foreach ( $list_counters as $field_id => $args ) {

										echo '[' . $form['id'] . ',' . $args['list'] . ',' . $field_id . ', \'' . $args['type'] . '\'],';

								}

							?>
					 ];

					 jQuery.each( FormRowToField, function( index, value ) {
						  ListFieldRowTotal( value[0], value[1], value[2], value[3] );
					 });

					 function ListFieldRowTotal( formId, fieldId, totalFieldId, fieldType ) {
						  var listField = '#field_' + formId + '_' + fieldId;
						  if ( 'product' == fieldType ) {
								var totalField = '#ginput_quantity_' + formId + '_' + totalFieldId;
						  } else {
								var totalField = '#input_' + formId + '_' + totalFieldId;
						  }

						  ListFieldRowCount( listField, totalField );

						  jQuery( listField ).on( 'click', '.add_list_item', function() {
								ListFieldRowCount( listField, totalField );
								jQuery( listField + ' .delete_list_item' ).removeProp( 'onclick' );
						  });
						  jQuery( listField ).on( 'click', '.delete_list_item', function() {
								gformDeleteListItem( this, 0 );
								ListFieldRowCount( listField, totalField );
						  });

					 }

					 function ListFieldRowCount( listField, totalField ) {
						  var totalRows = jQuery( listField ).find('table.gfield_list tbody tr').length;
						  jQuery( totalField ).val( totalRows ).change();
					 }

				 });

			</script>

		<?php }

		return $form;

	}

	/**
	 * Get the lists
	 *
	 * @since    0.1
	 */
	public function get_list_counters( $form ) {

		$lists = array();

		foreach ( $form['fields'] as $key => $field ) {

			if ( '' != $field['autofillListRowCount'] ) {

				$lists[ $field['id'] ] = array(
					'list' => $field['autofillListRowCount'],
					'type' => $field['type']
				);

			}

		}

		return $lists;

	}

	public function add_tooltip( $key, $content ) {
		$this->tooltips[ $key ] = $content;
		add_filter( 'gform_tooltips', array( $this, 'load_tooltips' ) );
	}

	public function load_tooltips( $tooltips ) {
		return array_merge( $tooltips, $this->tooltips );
	}

}
