<<?php

/**
 * @package oik-bob-bing-wide
 * @copyright (C) Bobbing Wide 2023
 *
 * Test the range rendering in acf_field_block_renderer
 */
require_once 'classes/class-acf-bw-unittestcase.php';

class Tests_range_field extends ACF_BW_UnitTestCase {

	// @TODO Does this need to implement setUp and tearDown ?

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
	 * "data":{"acf-field-name":"field_64aa871c48dfd","_acf-field-name":"field_645f589a88304"},
	 * "mode":"preview"}
	 * /-->
	 * ```
	 */

	/**
	 * Tests the field type range when the field's set to blank or null
	 *
	 * @return void
	 */
	function test_range_field_render_post_no_field_value() {
		$field_name = 'range';
		$field_key = 'field_64aa871c48dfd';
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
	 * Tests the field type range when the field's set for the post.
	 *
	 * @return void
	 */
	function test_range_field_render_post_field_set() {
		$field_name = 'range';
		$field_key = 'field_64aa871c48dfd';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$this->set_dummy_post();
		$this->update_post_meta( $field_name, 42 );
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
	}

	/**
	 * Tests the text field type when there's no global post
	 * and the post_meta's not set.
	 *
	 * @return void
	 *
	 */
	function test_range_field_render_no_post_no_field() {
		$field_name     = 'range';
		$field_name     = 'field_64aa871c48dfd';
		$acf_field_block=$this->create_acf_field_block( $field_name );
		$content        =$acf_field_block;
		$html           = do_blocks( $content );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
	}





	/**
	 * Tests text field rendering with a dynamic post.
	 * @return void
	 */
	function test_range_field_render() {

		$field_name = 'range';
		$field_key = 'field_64aa871c48dfd';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$post = $this->dummy_post( 1 );
		$this->set_dummy_post( $post->ID);
		$this->update_post_meta( $field_name, 42 );
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );

		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );

	}





}



