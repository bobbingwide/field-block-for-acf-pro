<?php

/**
 * @package oik-bob-bing-wide
 * @copyright (C) Bobbing Wide 2023
 *
 * Test the functions in shortcodes/oik-dash.php
 */
class Tests_text_field extends BW_UnitTestCase {

	function setUp() : void {
		parent::setUp();
	}

	/**
	 *
	 * Calls the logic to render an ACF field block
	 * and compares the generated HTML with expected.
	 *
	 * We need to
	 * - register the field group with the required field type
	 * - create a post with the post meta set
	 * - render the acf-field block
	 *
	 * ```
	 * <!-- wp:acf-field/acf-field {
	 * "name":"acf-field/acf-field",
	 * "data":{"acf-field-name":"text","_acf-field-name":"field_645f589a88304"},
	 * "mode":"preview"}
	 * /-->
	 * ```
	 */

	/**
	 * Tests the text field type when there's no global post
	 * and the post_meta's not set.
	 *
	 * @return void
	 *
	 */
	function test_text_field_render_no_post_no_field() {
		$field = $this->maybe_create_field_group( 'text');
		$content = $this->create_acf_field_block(  $field );
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
		//$this->assertEquals( $icons[0], "menu" );
		//$this->assertEquals( $icons[0], "admin-appearance");

	}

	/**
	 * Creates the field group if necessary.
	 *
	 * @param $field_type
	 *
	 * @return array
	 */
	function maybe_create_field_group( $field_type ) {
		$field = $this->create_test_field( $field_type);
		$this->assertIsArray( $field );
		$field_group = $this->create_test_field_group( $field_type );
		return $field;

	}

	/**
	 * Tests the field type text when the fields not set for the post.
	 * @return void
	 */
	function test_text_field_render_post_no_field() {
		//$field = $this->maybe_create_field_group( 'text');
		$field = $this->create_test_field( 'text');
		print_r( $field );
		$content = $this->create_acf_field_block( $field );
		echo $content;
		$this->set_dummy_post();
		$html = do_blocks( $content );
		//echo $output;
		$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
		//$this->assertEquals( $icons[0], "menu" );
		//$this->assertEquals( $icons[0], "admin-appearance");
	}

	function set_dummy_post() {
		global $post;
		$post = $this->dummy_post( 1 );
	}

	function dummy_post( $n, $parent=0 ) {
		$args = array( 'post_type' => 'post', 'post_title' => "post title $n", 'post_excerpt' => 'Excerpt. No post ID', 'post_parent' => $parent  );
		$id = self::factory()->post->create( $args );
		$post = get_post( $id );
		$GLOBALS['post'] = null;
		$GLOBALS['id'] = 0;
		return $post;
	}


	function test_text_field_render() {
		$field = $this->create_test_field( 'text');
		$this->assertIsArray( $field );
		$field_group = $this->create_test_field_group( 'text');
		$content = $this->create_acf_field_block( $field );
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
		//$this->assertEquals( $icons[0], "menu" );
		//$this->assertEquals( $icons[0], "admin-appearance");

	}

	function create_test_field_group( $field_type ) {
		$acf_field = $this->create_test_field( $field_type );
		//register_field_group( array(
		$registered = acf_add_local_field_group( array(
			/* 'key'                  =>'group_645e28e943198', */
			'title'                =>'test_field_group',
			'fields'               =>array( $acf_field 	),
			'location'             =>array(
				array(
					array(
						'param'   =>'post_type',
						'operator'=>'==',
						'value'   =>'oik-testimonials/acf-cycler',
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
			'show_in_rest'         =>0,
		) );
		$this->assertTrue( $registered);

	}

	function create_test_field( $type ) {
		$acf_field =
			array(
				'key'              => '',
				'label'            => 'Label ' . $type,
				'name'             => 'name_' . $type,
				'aria-label'       =>'',
				'type'             => $type,
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
				'placeholder'      =>'',
				'prepend'          =>'',
				'append'           =>'',
			);

		return $acf_field;
	}

	function create_acf_field_block( $field ) {
		$block = '<!-- wp:acf-field/acf-field {';
		$block .= '"name":"acf-field/acf-field",';
		$block .= '"data":{"acf-field-name":"';
		$block .= $field['name'];
		$block .= '"},"mode":"preview"}';
		$block .= ' /-->';
		return $block;

	}


}

