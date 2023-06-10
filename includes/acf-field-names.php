<?php
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
function acf_get_possible_field_names( $field_name, $post_id ) {
	$field_groups = acf_get_field_groups();
	bw_trace2($field_groups, 'field groups', false );
	$fields = [];
	foreach ( $field_groups as $field_group ) {
		// Only process field groups which include Location Rules involving post_type. ie exclude those defined for Blocks
		$post_types = acf_process_field_group( $field_group );
		if ( $post_types ) {
			$raw_fields = acf_get_fields( $field_group['ID'] );
			bw_trace2( $raw_fields, 'raw_fields', false );
			foreach ( $raw_fields as $raw_field ) {
				$field_select_label =[];
				$field_select_label[] = $raw_field['label'];
				$field_select_label[] = '-';
				$field_select_label[] = $post_types;
				if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) {
					$field_select_label[]='-';
					$field_select_label[]=$raw_field['name'] . '/' . $raw_field['key'];
				}
				$fields[ $raw_field['name'] ]=implode( ' ', $field_select_label );
			}
		}
	}
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
function acf_process_field_group( $field_group ) {
	$post_types = [];
	$rules = $field_group['location'];
	foreach ( $rules as $ruleset ) {
		foreach ( $ruleset as $rule ) {
			if ( 'post_type' === $rule['param'] ) {
				$post_type = get_post_type_object( $rule['value']);
				$not = ( '!=' === $rule['operator'] ) ? '!' : '';
				/* Note: The post type may not be registered yet. */

				bw_trace2( $rule, 'rule', false );
				if ( $post_type ) {
					//bw_trace2( $post_type, 'post_type' );
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
 * Lists the possible ACF field names.
 *
 * The logic should cater for:
 * - fields that are defined for use in multiple post types,
 * - posts, taxonomies, users
 * - but not blocks
 * - what about nested field group structures?
 * - or fields which are local?
 *
 * @param $field_name
 * @param $post_id
 *
 * @return void
 */
function acf_list_possible_field_names( $field_name, $post_id ) {
	$field_names = acf_get_possible_field_names( $field_name, $post_id );
	foreach ( $field_names as $name => $value ) {
		echo "<br />$name $value";
	}
}

/**
 * Defines the acf-field-name field.
 *
 * @return array
 */
function acf_build_acf_field_name_field() {
	$acf_field_name = 	array(
		'key' => 'field_645f589a88304',
		'label' => 'ACF Field name',
		'name' => 'acf-field-name',
		'aria-label' => '',
		'type' => 'select',
		'instructions' => 'Select the field name of the ACF field to display',
		'required' => 1,
		'conditional_logic' => 0,
		'wrapper' => array(
			'width' => '',
			'class' => '',
			'id' => '',
		),
		'default_value' => '',
		'maxlength' => '',
		'placeholder' => 'ACF_field_name',
		'prepend' => '',
		'append' => '',
	);

	/**
	 * Set the possible values.
	 */
	$acf_field_name['choices'] = acf_get_possible_field_names( '', 0 );
	return $acf_field_name;
}

/**
 * Defines the Cycler Transition Effect (fx) field.
 *
 * @return array
 */
function acf_build_acf_cycler_field() {
	$acf_cycler_field = array(
	'key' => 'field_645e28e9f47da',
	'label' => 'Cycle Transition Effect',
	'name' => 'fx',
	'aria-label' => '',
	'type' => 'select',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => '',
		'class' => '',
		'id' => '',
	),
	'choices' => array(
		'fade' => 'fade',
		'blindX' => 'blindX',
		'blindY' => 'blindY',
		'blindZ' => 'blindZ',
		'cover' => 'cover',
		'curtainX' => 'curtainX',
		'curtainY' => 'curtainY',
		'fadeZoom' => 'fadeZoom',
		'growX' => 'growX',
		'growY' => 'growY',
		'none' => 'none',
		'scrollUp' => 'scrollUp',
		'scrollDown' => 'scrollDown',
		'scrollLeft' => 'scrollLeft',
		'scrollRight' => 'scrollRight',
		'scrollHorz' => 'scrollHorz',
		'scrollVert' => 'scrollVert',
		'shuffle' => 'shuffle',
		'slideX' => 'slideX',
		'slideY' => 'slideY',
		'toss' => 'toss',
		'turnUp' => 'turnUp',
		'turnDown' => 'turnDown',
		'turnLeft' => 'turnLeft',
		'turnRight' => 'turnRight',
		'uncover' => 'uncover',
		'wipe' => 'wipe',
		'zoom' => 'zoom',
	),
	'default_value' => 'fade',
	'return_format' => 'value',
	'multiple' => 0,
	'allow_null' => 0,
	'ui' => 0,
	'ajax' => 0,
	'placeholder' => '',
	);
	return $acf_cycler_field;
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
function acf_prepare_field_name_acf_field_name( $field ) {
	$field['choices'] = acf_get_possible_field_names( '', 0 );
	return $field;
}

