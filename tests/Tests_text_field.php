<?php

/**
 * @package field-block-for-acf-pro
 * @copyright (C) Bobbing Wide 2023, 2024
 *
 * Test the text field rendering in acf_field_block_renderer
 */
class Tests_text_field extends BW_UnitTestCase {

	function setUp() : void {
		parent::setUp();
	}

	function tearDown() : void {
		$this->dont_restore_hooks();
		parent::tearDown();
	}

	/**
	 *
	 * Calls the logic to render an ACF field block
	 * and compares the generated HTML with expected.
	 *
	 * We need to
	 * - register the field group with the required field type
	 * - create a post with the post meta set
	 * - render the acf-field block in preview mode
	 *
	 * ```
	 * <!-- wp:acf-field/acf-field {
	 * "name":"acf-field/acf-field",
	 * "data":{"acf-field-name":"field_64aa869f48df7","_acf-field-name":"field_645f589a88304"},
	 * "mode":"preview"}
	 * /-->
	 * ```
	 */


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
	 * Test early exit when field name not set.
	 * @return void
	 */
	function test_render_no_field_name() {
		$field_key = '';
		$content = $this->create_acf_field_block( $field_key );
		$html = do_blocks( $content );
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );

	}
	/**
	 * Tests the field type text when the field's set to blank or null
	 *
	 * @return void
	 */
	function test_text_field_render_post_no_field_value() {
		//$field = $this->maybe_create_field_group( 'text');
		//$field = $this->create_test_field( 'text');
		//print_r( $field );
		$field_name = 'text';
		$field_key = 'field_64aa869f48df7';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$this->set_dummy_post( 19196 );
		//$this->update_post_meta( $field_name, 'eh what??');
		//$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		// Adding a call to set_dummy_post() tests the processing for a different post ID in the same test.
		// The output was as expected; the field name was found.
		// $this->set_dummy_post();
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
		//$this->assertEquals( $icons[0], "menu" );
		//$this->assertEquals( $icons[0], "admin-appearance");
		//acf_reset_meta( 19196 );
	}

	/**
	 * Tests the field type text when the field's set for the post.
	 *
	 * Note: At present the logic fails when it's the second unit test that calls get_field('acf-field-name');
	 * @return void
	 */
	function test_text_field_render_post_field_set() {
		//$this->reload_acf();
		//$field = $this->maybe_create_field_group( 'text');
		//$field = $this->create_test_field( 'text');
		//print_r( $field );
		$field_name = 'text';
		$field_key = 'field_64aa869f48df7';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$this->set_dummy_post();
		$this->update_post_meta( $field_name, 'eh what?');
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
		//$this->assertEquals( $icons[0], "menu" );
		//$this->assertEquals( $icons[0], "admin-appearance");

	}

	function reload_acf() {
		global $acf;
		$acf=null;
		acf();
		//do_action( 'init' );
		//do_action( 'acf/init', ACF_MAJOR_VERSION );
	}



	/**
	 * Tests the text field type when there's no global post
	 * and the post_meta's not set.
	 *
	 * @return void
	 *
	 */
	function test_text_field_render_no_post_no_field() {
		//$field = $this->maybe_create_field_group( 'text');
		$field_name     = 'text';
		$field_name     = 'field_64aa869f48df7';
		$acf_field_block=$this->create_acf_field_block( $field_name );
		$content        =$acf_field_block;
		$html           = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
		//$this->assertEquals( $icons[0], "menu" );
		//$this->assertEquals( $icons[0], "admin-appearance");

	}

	function set_dummy_post( $id=19274) {
		global $post;
		//$post = $this->dummy_post( 1 );
		$GLOBALS['post'] = null;
		$GLOBALS['id'] = 0;
		//acf_reset_meta( $id );
		$post = get_post( $id );

	}

	function update_post_meta( $field_name, $field_value ) {
		global $post;
		//bw_trace2( $post, "post" );
		$result = update_post_meta( $post->ID, $field_name, $field_value );
		// Some updates return true others false. It depends on the new value.
		//bw_trace2( $result, 'result');
		//$this->assertNotFalse( $result );
	}

	function dummy_post( $n, $parent=0 ) {
		$args = array( 'post_type' => 'test-acf-fields', 'post_title' => "post title $n", 'post_excerpt' => 'Excerpt. No post ID', 'post_parent' => $parent  );
		$id = self::factory()->post->create( $args );
		$post = get_post( $id );
		//$GLOBALS['post'] = null;
		//$GLOBALS['id'] = 0;
		return $post;
	}

	/**
	 * Tests text field rendering with a dynamic post.
	 * @return void
	 */
	function test_text_field_render() {

		$field_name = 'text';
		$field_key = 'field_64aa869f48df7';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$post = $this->dummy_post( 1 );
		$this->set_dummy_post( $post->ID);
		$this->update_post_meta( $field_name, 'rendered text field');
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );

	}

	function create_test_field_group( $field_type ) {
		$acf_field = $this->create_test_field( $field_type );
		//register_field_group( array(
		$registered = acf_add_local_field_group( array(
			'key'                  =>'group_' . acf_slugify( 'test_field_group', '_' ),
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

	function create_acf_field_block( $field_name ) {
		$block = '<!-- wp:acf-field/acf-field {';
		$block .= '"name":"acf-field/acf-field",';
		$block .= '"data":{"acf-field-name":"';
		$block .= $field_name;
		$block .= '","_acf-field-name":"field_645f589a88304"';
		$block .= '},"mode":"preview"}';
		$block .= ' /-->';
		return $block;

	}


}

