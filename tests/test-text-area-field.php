<?php

/**
 * @package field-block-for-acf-pro
 * @copyright (C) Bobbing Wide 2023, 2024
 *
 * Test the text_area rendering in acf_field_block_renderer
 */
class Tests_text_area_field extends BW_UnitTestCase {

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
	 * - render the acf-field block
	 *
	 * ```
	 * <!-- wp:acf-field/acf-field {
	 * "name":"acf-field/acf-field",
	 * "data":{"acf-field-name":"field_64aa870148dfb","_acf-field-name":"field_645f589a88304"},
	 * "mode":"preview"}
	 * /-->
	 * ```
	 */

	/**
	 * Tests the field type text-area when the field's set to blank or null
	 *
	 * @return void
	 */
	function test_text_area_field_render_post_no_field_value() {
		$field_name = 'text_area';
		$field_key = 'field_64aa870148dfb';
		$content = $this->create_acf_field_block( $field_key );
		$this->set_dummy_post( 19196 );
		//$this->update_post_meta( $field_name, 'eh what??');
		//$this->update_post_meta( '_'. $field_name, $field_key);
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );

	}

	/**
	 * Tests the field type text-area when the field's set for the post.
	 *
	 * Note: At present the logic fails when it's the second unit test that calls get_field('acf-field-name');
	 * @return void
	 */
	function test_text_area_field_render_post_field_set() {
		$field_name = 'text_area';
		$field_key = 'field_64aa870148dfb';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$this->set_dummy_post();
		$this->update_post_meta( $field_name, 'Content for the Text Area field. Note: HTML is not supported in this field type.');
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
	}

	/**
	 * Tests the text-area field type when there's no global post
	 * and the post_meta's not set.
	 *
	 * @return void
	 *
	 */
	function test_text_area_field_render_no_post_no_field() {
		$field_name     = 'text_area';
		$field_name     = 'field_64aa870148dfb';
		$acf_field_block=$this->create_acf_field_block( $field_name );
		$content        =$acf_field_block;
		$html           = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
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
		bw_trace2( $post, "post" );
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
	 * Tests text-area field rendering with a dynamic post.
	 * @return void
	 */
	function test_text_area_field_render() {

		$field_name = 'text_area';
		$field_key = 'field_64aa870148dfb';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$post = $this->dummy_post( 1 );
		$this->set_dummy_post( $post->ID);
		$this->update_post_meta( $field_name, 'Content for the Text Area field. Note: HTML is not supported in this field type.');
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );

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

