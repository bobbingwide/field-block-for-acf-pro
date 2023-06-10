<?php
/*
Plugin Name: oik-testimonials 
Plugin URI: https://www.oik-plugins.com/oik-plugins/oik-testimonials
Description: "better by far" oik testimonials 
Depends: oik base plugin, oik fields
Version: 0.6.0
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Text Domain: oik-testimonials
License: GPL2

    Copyright 2012-2023 Bobbing Wide (email : herb@bobbingwide.com )

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
 * Implement "oik_fields_loaded" action for oik-testimonials 
 * 
 * A custom category "testimonial-type" is used to categorise a large set of testimonials
 * Note: The custom category name must be different from the CPT name
 */
function oik_testimonials_init( ) {
  bw_register_custom_category( "testimonial_type", null, __( "Testimonial type", "oik-testimonials" ) );
  oik_register_oik_testimonials();
}

function oik_testimonials_register_oik_shortcodes() {
	bw_add_shortcode( "bw_testimonials", "bw_testimonials", oik_path( "shortcodes/oik-testimonials.php", "oik-testimonials" ), false );
	bw_add_shortcode( "oik_testimonials", "bw_testimonials", oik_path( "shortcodes/oik-testimonials.php", "oik-testimonials" ), false );
	add_shortcode( 'acf_testimonials', 'bw_testimonials_acf' );
}

function oik_testimonials_init_acf() {
	register_post_type( 'oik_testimonials', array(
			'labels' => array(
				'name' => 'Testimonials',
				'singular_name' => 'Testimonial',
				'menu_name' => 'Testimonials',
				'all_items' => 'All Testimonials',
				'edit_item' => 'Edit Testimonial',
				'view_item' => 'View Testimonial',
				'view_items' => 'View Testimonials',
				'add_new_item' => 'Add New Testimonial',
				'new_item' => 'New Testimonial',
				'parent_item_colon' => 'Parent Testimonial:',
				'search_items' => 'Search Testimonials',
				'not_found' => 'No testimonials found',
				'not_found_in_trash' => 'No testimonials found in the bin',
				'archives' => 'Testimonial Archives',
				'attributes' => 'Testimonial Attributes',
				'insert_into_item' => 'Insert into testimonial',
				'uploaded_to_this_item' => 'Uploaded to this testimonial',
				'filter_items_list' => 'Filter testimonials list',
				'filter_by_date' => 'Filter testimonials by date',
				'items_list_navigation' => 'Testimonials list navigation',
				'items_list' => 'Testimonials list',
				'item_published' => 'Testimonial published.',
				'item_published_privately' => 'Testimonial published privately.',
				'item_reverted_to_draft' => 'Testimonial reverted to draft.',
				'item_scheduled' => 'Testimonial scheduled.',
				'item_updated' => 'Testimonial updated.',
				'item_link' => 'Testimonial Link',
				'item_link_description' => 'A link to a testimonial.',
			),
			'description' => 'Testimonials',
			'public' => true,
			'show_in_rest' => true,
			'supports' => array(
				0 => 'title',
				1 => 'editor',
				2 => 'thumbnail',
			),
			'taxonomies' => array(
				0 => 'testimonial_type',
			),
			'delete_with_user' => false,
	) );

	register_taxonomy( 'testimonial_type', array(
			0 => 'oik_testimonials',
		), array(
			'labels' => array(
				'name' => 'Testimonial types',
				'singular_name' => 'Testimonial type',
				'menu_name' => 'Testimonial type',
				'all_items' => 'All Testimonial type',
				'edit_item' => 'Edit Testimonial type',
				'view_item' => 'View Testimonial type',
				'update_item' => 'Update Testimonial type',
				'add_new_item' => 'Add New Testimonial type',
				'new_item_name' => 'New Testimonial type Name',
				'parent_item' => 'Parent Testimonial type',
				'parent_item_colon' => 'Parent Testimonial type:',
				'search_items' => 'Search Testimonial type',
				'not_found' => 'No testimonial type found',
				'no_terms' => 'No testimonial type',
				'filter_by_item' => 'Filter by testimonial type',
				'items_list_navigation' => 'Testimonial type list navigation',
				'items_list' => 'Testimonial type list',
				'back_to_items' => '← Go to testimonial type',
				'item_link' => 'Testimonial type Link',
				'item_link_description' => 'A link to a testimonial type',
			),
			'public' => true,
			'hierarchical' => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
		) );
}

/**
 * Registers the required field groups if not already registered.
 *
 * @return void
 */
function oik_testimonials_acf_include_fields() {
	oik_require( 'includes/acf-field-names.php', 'oik-testimonials');
	$acf_field_name_field = acf_build_acf_field_name_field();
	$acf_cycler_field = acf_build_acf_cycler_field();
	oik_maybe_add_local_field_group( array(
			'key'                  =>'group_645a613de20b1',
			'title'                =>'Testimonials',
			'fields'               =>array(
				array(
					'key'              =>'field_645a613e44c52',
					'label'            =>'Author name',
					'name'             =>'_oik_testimonials_name',
					'aria-label'       =>'',
					'type'             =>'text',
					'instructions'     =>'',
					'required'         =>1,
					'conditional_logic'=>0,
					'wrapper'          =>array(
						'width'=>'',
						'class'=>'',
						'id'   =>'',
					),
					'default_value'    =>'',
					'maxlength'        =>'',
					'placeholder'      =>'Testimonial author name',
					'prepend'          =>'',
					'append'           =>'',
				),
			),
			'location'             =>array(
				array(
					array(
						'param'   =>'post_type',
						'operator'=>'==',
						'value'   =>'oik_testimonials',
					),
				),
			),
			'menu_order'           =>0,
			'position'             =>'normal',
			'style'                =>'default',
			'label_placement'      =>'top',
			'instruction_placement'=>'label',
			'hide_on_screen'       =>'',
			'active'               =>true,
			'description'          =>'',
			'show_in_rest'         =>1,
		) );
	acf_add_local_field_group( array(
			'key' => 'group_645e28e943198',
			'title' => 'acf-cycler',
			'fields' => array(
				$acf_cycler_field

			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'oik-testimonials/acf-cycler',
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
						'value' => 'oik-testimonials/acf-field',
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
	add_filter( 'acf/prepare_field/name=acf-field-name', 'acf_prepare_field_name_acf_field_name' );
}

function oik_is_field_group_registered( $title ) {
	$registered = false;
	$raw_field_groups=acf_get_raw_field_groups();
	bw_trace2( $raw_field_groups, 'raw_field_groups', false );
	foreach ( $raw_field_groups as $raw_field_group ) {
		if ( $raw_field_group['title'] === $title ) {
			$registered = true;
		}
	}
	return $registered;
}

/**
 * Adds a local field group if the group's title isn't already registered.
 *
 * @param $group
 * @return void
 */
function oik_maybe_add_local_field_group( $group ) {
	$registered = oik_is_field_group_registered( $group['title']);
	if ( !$registered ) {
		acf_add_local_field_group( $group );
	}
}

function oik_testimonials_acf_init() {
	add_shortcode( 'bw_testimonials', 'bw_testimonials_acf' );
	add_shortcode( 'acf_testimonials', 'bw_testimonials_acf' );
}

/**
 * Registers oik-testimonials ACF blocks.
 *
 * @return void
 */
function oik_testimonials_register_blocks() {
	$registered = register_block_type( __DIR__ . '/blocks/acf-cycler' );
	bw_trace2( $registered, 'registered?', false );
	$registered = register_block_type( __DIR__ . '/blocks/acf-author-name' );
	bw_trace2( $registered, 'registered?', false );
	$registered = register_block_type( __DIR__ . '/blocks/acf-field' );
	bw_trace2( $registered, 'registered?', false );
}

/**
 * Implements bw_testimonials and acf_testimonials shortcodes for ACF
 *
 * @param $atts
 * @param $content
 * @param $tag
 *
 * @return expanded|string
 */
function bw_testimonials_acf( $atts, $content=null, $tag=null ) {
	bw_trace2();
	$html = "ACF version of $tag";
	if ( did_action( 'oik_loaded')) {
		oik_require( "shortcodes/oik-testimonials.php", "oik-testimonials" );
		$atts = bw_cast_array( $atts );
		$html = bw_testimonials( $atts, $content, $tag . '-ACF' );
	}
	return $html;
}

/** 
 * Register custom post type "oik_testimonials" 
 *
 * The description is the content field - the testimonial
 * The title should contain the testimonial's author name
 * _oik_testimonial_name should also be the Author name
 * 
 */
function oik_register_oik_testimonials() {
  $post_type = 'oik_testimonials';
  $post_type_args = array();
  $post_type_args['label'] = __( 'Testimonials', "oik-testimonials" );
  $post_type_args['description'] = __( 'Testimonials', "oik-testimonials" );
  $post_type_args['taxonomies'] = array( "testimonial_type" );
  $post_type_args['has_archive'] = true;
	$post_type_args['show_in_rest'] = true;
  bw_register_post_type( $post_type, $post_type_args );
  if ( !defined( 'ACF') ) {
	  bw_register_field( "_oik_testimonials_name", "text", __( "Author name", "oik-testimonials" ), array( "#required"=>true ) );
	  bw_register_field_for_object_type( "_oik_testimonials_name", $post_type );
  }
  add_filter( "manage_edit-${post_type}_columns", "oik_testimonials_columns", 10, 2 );
  add_action( "manage_${post_type}_posts_custom_column", "bw_custom_column_admin", 10, 2 );
}

/**
 * Implement "manage_edit-oik_testimonials_column" for oik-testimonials
 */
function oik_testimonials_columns( $columns, $arg2=null ) {
  $columns["_oik_testimonials_name"] = __( "Author name", "oik-testimonials" ); 
  //bw_trace2();
  return( $columns ); 
} 

/**
 * Theme the _oik_testimonials_name field 
 */
function _bw_theme_field_default__oik_testimonials_name( $key, $value ) {
  e( $value[0] );
}

/**
 * Implement "oik_admin_menu" for oik-testimonials 
 */
function oik_testimonials_admin_menu() {
  oik_register_plugin_server( __FILE__ );
}

/**
 * Implement "oik_set_spam_fields_oik_testimonials" for oik-testimonials 
 */
function oik_testimonials_spam_fields( $fields ) {  
  bw_trace2( $fields );
  $fields['comment_content'] = bw_array_get( $fields, "post_content", null ); 
  $fields['comment_author'] = bw_array_get( $fields, "_oik_testimonials_name", null );
  $fields['comment_author_email'] = "";
  $fields['comment_author_url'] = "";
  return( $fields );
}
 
/**
 * Implement "admin_notices" action for oik_testimonials
 *
 * Note: Now supports ACF... but only when it's active!
 */ 
function oik_testimonials_activation() {
  static $plugin_basename = null;
  if ( !$plugin_basename ) {
    $plugin_basename = plugin_basename(__FILE__);
    add_action( "after_plugin_row_oik-testimonials/oik-testimonials.php", "oik_testimonials_activation" ); 
    if ( !function_exists( "oik_plugin_lazy_activation" ) ) { 
      require_once( "admin/oik-activation.php" );
    }
  }
  if ( defined( 'ACF')) {
	  $depends="oik:3.3";
  } else {
	  $depends = "oik,oik-fields";
  }
  oik_plugin_lazy_activation( __FILE__, $depends, "oik_plugin_plugin_inactive" );
}

/**
 * Debug function discovers information held in ACF.
 *
 * Note: I used this when working out which ACF functions may be used to implement oik_maybe_add_local_field_group()
 *
 * @return void
 */
function oik_testimonials_whats_in_ACF() {

	$local_enabled = acf_is_local_enabled();
	bw_trace2( $local_enabled, "local enabled?", false, BW_TRACE_DEBUG);

	$store = acf_get_local_store( '', 'acf-field-group');
	bw_trace2( $store, "store", false, BW_TRACE_DEBUG );

	$count = acf_count_local_field_groups();
	bw_trace2( $count, "count of local field groups", false, BW_TRACE_DEBUG );

	$groups = acf_get_local_field_groups();
	bw_trace2( $groups, "local field groups", false, BW_TRACE_DEBUG );

	$raw_field_groups = acf_get_raw_field_groups();
	bw_trace2( $raw_field_groups, 'raw_field_groups', false, BW_TRACE_DEBUG );

}

/**
 * Function performed when oik-testimonials.php is loaded
 *
 * If using ACF, use the ACF method of registering things:
 * - on `init` register the CPT and taxonomy
 * - on `acf/include_fields` register the Testimonials Field Group, if not already registered
 * - on `acf/init` register the shortcodes for ACF
 * - on `acf/init` register the blocks
 *
 * Always:
 * - respond to other actions and filters using oik logic with some changes if ACF is activated.
 */
function oik_testimonials_plugin_loaded() {

	if ( defined( 'ACF') ) {
		add_action( 'init', "oik_testimonials_init_acf");
		add_action( 'acf/include_fields', 'oik_testimonials_acf_include_fields', 11 );
		add_action( 'acf/init', 'oik_testimonials_acf_init');
		add_action( 'acf/init', 'oik_testimonials_register_blocks');
		//add_action( 'acf/init', 'oik_testimonials_acf_include_fields');
	}
	add_action( 'oik_loaded', 'oik_testimonials_register_oik_shortcodes' );
	add_action( 'oik_fields_loaded', 'oik_testimonials_init' );
	add_action( "oik_admin_menu", "oik_testimonials_admin_menu" );
	add_action( "admin_notices", "oik_testimonials_activation" );
	add_filter( "oik_set_spam_fields_oik_testimonials", "oik_testimonials_spam_fields" );
	//add_action( 'oik_loaded', 'oik_testimonials_whats_in_ACF');
}

oik_testimonials_plugin_loaded();