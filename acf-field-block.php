<?php
/*
Plugin Name: acf-field-block
Plugin URI: https://www.oik-plugins.com/oik-plugins/acf-field-block
Description: ACF Field block
Depends: ACF Pro
Version: 0.0.0
Author: bobbingwide
Author URI: https://bobbingwide.com/about-bobbing-wide
Text Domain: acf-field-block
License: GPL2

    Copyright 2023 Bobbing Wide (email : herb@bobbingwide.com )

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

/**
 * Registers the acf-field group for the block.
 *
 * @return void
 */
function acf_field_block_acf_include_fields() {
	require_once __DIR__ . '/includes/acf-field-names.php';
	$acf_field_name_field = acf_field_block_build_acf_field_name_field();

	acf_add_local_field_group( array(
			'key' => 'group_645f589a8cade',
			'title' => 'acf-field',
			'fields' => array(
				$acf_field_name_field
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
	add_filter( 'acf/prepare_field/name=acf-field-name', 'acf_field_block_prepare_field_name_acf_field_name' );
}

/**
 * Registers the ACF Field block.
 *
 * @return void
 */
function acf_field_block_register_blocks() {
	$registered = register_block_type( __DIR__ . '/blocks/acf-field' );
	bw_trace2( $registered, 'registered?', false );
}

/**
 * Function performed when acf-field-block.php is loaded
 *
 * Registers the `acf-field` group in response to `acf/include_fields`
 * and the `acf-field/acf-field` block in response to `acf/init`.
 *
 * Note: We can't check the 'ACF' constant until it's defined.
 * ACF PRO is loaded after acf-field-block
 */
function acf_field_block_plugin_loaded() {
	add_action( 'acf/include_fields', 'acf_field_block_acf_include_fields', 11 );
	add_action( 'acf/init', 'acf_field_block_register_blocks');
}

acf_field_block_plugin_loaded();


// Dummy trace functions when oik-bwtrace is not activated
if ( !function_exists( "bw_trace2" ) ) {
    function bw_trace2( $p=null ) { return $p; }
    function bw_backtrace() {}
}