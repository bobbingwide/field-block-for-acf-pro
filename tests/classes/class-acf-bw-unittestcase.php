<?php

/**
 * @package field-block-for-acf-pro
 * @copyright (C) Bobbing Wide 2023, 2024
 *
 * Class for ACF-field-block unit tests.
 * Extends BW_UnitTestCase to cater for ACF field block specific methods
 */
class ACF_BW_UnitTestCase extends BW_UnitTestCase {

	function setUp() : void {
		parent::setUp();
	}

	function tearDown() : void {
		$this->dont_restore_hooks();
		parent::tearDown();
	}

	/**
	 * Sets the post to the given ID and fetches it.
	 *
	 * @param $id
	 *
	 * @return void
	 */
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

	function test_dummy_test() {
		$this->assertTrue( true );
	}

}