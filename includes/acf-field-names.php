<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2023, 2024
 * @package field-block-for-acf-pro
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Gets the possible field names.
 *
 * The logic should cater for:
 * - fields that are defined for use in multiple post types
 * - posts, taxonomies, users
 * - but not blocks
 * - what about nested field group structures?
 * - or fields which are local?
 * - or ACF options
 *
 * @param $field_name
 * @param $post_id
 *
 * @return array
 */
function field_block_for_acf_pro_get_possible_field_names( $field_name, $post_id ) {
	$field_groups = acf_get_field_groups();
	$fields = [];
	foreach ( $field_groups as $field_group ) {
		if ( $field_group['active']) {
			// Only process field groups which include Location Rules involving post_type. ie exclude those defined for Blocks
			$post_types=field_block_for_acf_pro_process_field_group( $field_group );
			if ( $post_types ) {
				$raw_fields=acf_get_fields( $field_group );
				foreach ( $raw_fields as $raw_field ) {
					if ( ! empty( $raw_field['name'] ) ) {
						$field_select_label  =[];
						$field_select_label[]=$raw_field['label'];
						$field_select_label[]='-';
						$field_select_label[] = $field_group['title'];
						$field_select_label[]='-';
						$field_select_label[]=$post_types;

						if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) {
							$field_select_label[]='-';
							$field_select_label[]=$raw_field['name'] . '/' . $raw_field['key'];
						}
						$fields[ $raw_field['key'] ]=implode( ' ', $field_select_label );
					} else {

					}
				}
			}
		}
	}
	asort( $fields );
    return $fields;
}

/**
 * Checks if the Field Group is relevant.
 *
 * We'll process this field group if any location param is 'post_type'.
 *
 * The location array is a multidimensional array.
 * - The top level entries are the ORs.
 * - The nested entries are the ANDs
 *
 * These location rules are to display the meta box for
 * - Testimonials ( oik_testimonials ) which are scheduled to be published in the future.
 *   Note: this seems an unlikely restriction but hey ho!
 * - Pages

 * ```
 *  [location] => Array
	[0] => Array
		[0] => Array
			[param] => (string) "post_type"
			[operator] => (string) "=="
			[value] => (string) "oik_testimonials"
		[1] => Array
			[param] => (string) "post_status"
			[operator] => (string) "=="
			[value] => (string) "future"

	[1] => Array
		[0] => Array
			[param] => (string) "post_type"
			[operator] => (string) "=="
			[value] => (string) "page"
 * ```
 */
function field_block_for_acf_pro_process_field_group( $field_group ) {
	$post_types = [];
	$rules = $field_group['location'];
	foreach ( $rules as $ruleset ) {
		foreach ( $ruleset as $rule ) {
			if ( 'post_type' === $rule['param'] ) {
				$post_type = get_post_type_object( $rule['value']);
				$not = ( '!=' === $rule['operator'] ) ? '!' : '';
				/* Note: The post type may not be registered yet. */
				if ( $post_type ) {
					$post_types[] = $not . $post_type->label;
				} else {
					$post_types[] = $not . $rule['value'];
				}
			}
		}
	}
	return implode( ',', $post_types );
}

/**
 * Defines the acf-field-name field.
 *
 * @return array
 */
function field_block_for_acf_pro_build_acf_field_name_field() {
	$acf_field_name = 	array(
		'key' => 'field_645f589a88304',
		'label' => __('ACF field name', 'acf-field-block' ),
		'name' => 'acf-field-name',
		'aria-label' => '',
		'type' => 'select',
		'instructions' => __('Select the field name of the ACF field to display.', 'acf-field-block' ),
		'required' => 1,
		'conditional_logic' => 0,
		'wrapper' => array(
			'width' => '',
			'class' => '',
			'id' => '',
		),
		'default_value' => '',
		'maxlength' => '',
		'placeholder' => __('ACF_field_name', 'acf-field-block' ),
		'prepend' => '',
		'append' => '',
	);


	/**
	 * Set the possible values.
	 */
	$acf_field_name['choices'] = field_block_for_acf_pro_get_possible_field_names( '', 0 );
	return $acf_field_name;
}

/**
 * Implements `acf/prepare_field/name=acf-field-name` filter.
 *
 * Sets the choices for the select field.
 * This caters for post types which weren't registered when `acf/include_fields` was actioned.
 *
 * @param $field
 * @return mixed
 */
function field_block_for_acf_pro_prepare_field_name_acf_field_name( $field ) {
	$field['choices'] = field_block_for_acf_pro_get_possible_field_names( '', 0 );
	return $field;
}

/**
 * Defines the display-label yes/no field.
 *
 * @return array
 */
function field_block_for_acf_pro_build_display_label_field() {

	$display_label=array(
		"key"              =>"field_65c0af48ebef0",
		"label"            =>"Display label",
		"name"             =>"display-label",
		"aria-label"       =>"",
		"type"             =>"true_false",
		"instructions"     =>"",
		"required"         =>0,
		"conditional_logic"=>0,
		"wrapper"          => [
			"width"=> "",
			"class"=> "",
			"id"=> ""
		],
		"message"=> "Select to display the field's label",
		"default_value"=> 0,
		"ui"=> 0,
		"ui_on_text"=> "",
		"ui_off_text"=> ""
	);
	return $display_label;
}