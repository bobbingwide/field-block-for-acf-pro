<?php
/*
Plugin Name: Field block for ACF Pro
Plugin URI: https://www.oik-plugins.com/oik-plugins/field-block-for-acf-pro
Description: Displays ACF fields in a block
Depends: advanced-custom-fields-pro
Version: 1.4.0
Author: bobbingwide
Author URI: https://bobbingwide.com/about-bobbing-wide
Text Domain: field-block-for-acf-pro
License: GPL2

    Copyright 2023, 2024 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Registers the acf-field group for the block.
 *
 * @return void
 */
function field_block_for_acf_pro_acf_include_fields() {
	$loaded = load_plugin_textdomain( 'field-block-for-acf-pro', false, 'field-block-for-acf-pro/languages' );
	require_once __DIR__ . '/includes/acf-field-names.php';
	$acf_field_name_field = field_block_for_acf_pro_build_acf_field_name_field();
	$display_label_field = field_block_for_acf_pro_build_display_label_field();
	acf_add_local_field_group( array(
			'key' => 'group_645f589a8cade',
			'title' => 'acf-field',
			'fields' => array(
				$acf_field_name_field,
				$display_label_field
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf-field/acf-field',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );
	add_filter( 'acf/prepare_field/name=acf-field-name', 'field_block_for_acf_pro_prepare_field_name_acf_field_name' );
}

/**
 * Registers the ACF field block.
 *
 * @return void
 */
function field_block_for_acf_pro_register_blocks() {
	$registered=register_block_type( __DIR__ . '/blocks/acf-field' );
}

/**
 * Function performed when field-block-for-acf-pro.php is loaded
 *
 * Registers the `acf-field` group in response to `acf/include_fields`
 * and the `acf-field/acf-field` block in response to `acf/init`.
 *
 * @return void
 */
function field_block_for_acf_pro_plugin_loaded() {
	add_action( 'acf/include_fields', 'field_block_for_acf_pro_acf_include_fields', 11 );
	add_action( 'acf/init', 'field_block_for_acf_pro_register_blocks');
    add_filter( 'acf/fields/google_map/api', 'field_block_for_acf_pro_fields_google_map_api');
}

field_block_for_acf_pro_plugin_loaded();


/**
 * May set the Google Maps API key.
 *
 * If there's a value passed then we don't need to override it.
 * The value may have come from a call to `acf_update_setting('google_api_key', $key);`
 * If not, and there's a value available elsewhere, we can try this.
 *
 * bw_get_option() is a function from the oik plugin.
 *
 * @param $api
 * @return $api
 */
function field_block_for_acf_pro_fields_google_map_api( $api ) {
    $key = null;
    if ( isset( $api['key'] ) ) {
        $key = $api['key'];
    }
    if ( empty( $key ) ) {
       if (function_exists('bw_get_option')) {
           $key = bw_get_option("google_maps_api_key");
           if ($key) {
               $api['key'] = $key;
           }
       }
    }

    return $api;
}