<<?php

/**
 * @package oik-bob-bing-wide
 * @copyright (C) Bobbing Wide 2023
 *
 * Test the email rendering in acf_field_block_renderer
 */
require_once 'classes/class-acf-bw-unittestcase.php';

class Tests_email_field extends ACF_BW_UnitTestCase {

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
	 * "data":{"acf-field-name":"field_64aa872a48dfe","_acf-field-name":"field_645f589a88304"},
	 * "mode":"preview"}
	 * /-->
	 * ```
	 */

	/**
	 * Tests the field type email when the field's set to blank or null
	 *
	 * @return void
	 */
	function test_email_field_render_post_no_field_value() {
		$field_name = 'email';
		$field_key = 'field_64aa872a48dfe';
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
	 * Tests the field type email when the field's set for the post.
	 *
	 * @return void
	 */
	function test_email_field_render_post_field_set() {
		$field_name = 'email';
		$field_key = 'field_64aa872a48dfe';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$this->set_dummy_post();
		$this->update_post_meta( $field_name, 'mail@example.com' );
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
		$html = $this->tag_break( $html );
		$html = $this->replace_antispambot( $html );
		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );
	}

    /**
	 * Tests text field rendering with a dynamic post.
	 * @return void
	 */
	function test_email_field_render() {

		$field_name = 'email';
		$field_key = 'field_64aa872a48dfe';
		$content = $this->create_acf_field_block( $field_key );
		//echo $content;
		$post = $this->dummy_post( 1 );
		$this->set_dummy_post( $post->ID);
		$this->update_post_meta( $field_name, 'mail@example.com' );
		$this->update_post_meta( '_'. $field_name, $field_key);

		$html = do_blocks( $content );
        $html = $this->tag_break( $html );
        $html = $this->replace_antispambot( $html );

		//echo $output;
		//$this->generate_expected_file( $html );
		$this->assertArrayEqualsFile( $html );

	}

	/**
	 * Replaces antispambot emails with a known value
	 *
	 * Note: This function could fail if there is no mailto: in the output
	 */
	function replace_antispambot( $expected_array ) {

		$found = false;
		foreach ( $expected_array as $index => $line ) {
			$replace=$this->replace_between( $expected_array[ $index ], 'href="mailto:', '">', 'email@example.com' );
            if ( $replace ) {
	            $replace=$this->replace_between( $replace, '">', '</a>', 'email@example.com' );
            }
			if ( $replace ) {
				$expected_array[ $index ]=$replace;
				$found                   =true;
			}
		}

		$this->assertTrue( $found, "No mailto: found in expected array" );
		return $expected_array;
	}





}




