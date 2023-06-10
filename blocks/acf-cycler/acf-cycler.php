<?php

/**
 * @copyright (C) Copyright Bobbing Wide 2023
 * @package oik-testimonials
 * @depends oik
 *
 */

/**
 * The following functions re-implement the jQuery cycler code that's used in the [bw_testimonials] shortcode.
 * The difference is that the jQuery selector is for
 * - either the acf-innerblocks-container, within the div with class acf-cycler * and ID returned from acf_cycler_id(),
 * - or the first repeating tag below this container.
 * The tag to use is determined from the block type of the first inner block.
 *
 * Since the code depends on oik's functions it's only invoked when oik has been loaded.
 *
 * A simpler solution wouldn't require jQuery and could probably be achieved
 * by defining the scripts to be enqueued in block.json.
 *
 * @TODO These functions are defined before the main code. They could be required from a separate file,
 * using oik_require().
 */
/**
 * Return the next unique ID for the cycler selector
 */
if (!function_exists( 'acf_cycler_id')) {
function acf_cycler_id() {
	static $cycler_id=0;
	$cycler_id ++;
	return ( "acf_cycler-$cycler_id" );
}

/**
 * Create the jQuery code to cycle the selection, including the starting div
 * This code uses jQuery cycle.all
 */
	function acf_cycler_jq( $atts, $tag, $innerblock_selector ) {
		oik_require( "shortcodes/oik-jquery.php" );
		$debug =bw_array_get( $atts, "debug", false );
		$script=bw_array_get( $atts, "script", "cycle.all" );
		$method=bw_array_get( $atts, "method", "cycle" );
		bw_jquery_enqueue_script( $script, $debug );
		$selector=acf_cycler_id();
		$parms   =_acf_cycler_cycle_parms();
		bw_jquery( "#$selector .acf-innerblocks-container $innerblock_selector", $method, $parms );
		$class=bw_array_get( $atts, "class", $tag );
		sdiv( $class, $selector );
	}

/**
 * Attempt to make the cycler responsive!
 *
 * The parameters here were set after reading other peoples questions and answers.
 * @link http://forum.jquery.com/topic/integrate-cycle-plugin-in-a-responsive-layout-using-media-queries
 *
 * Then I tried to reduce the logic to the minimum that would work for blocks containing text and images.
 * The trick was in setting the width and max-width in both the parms and the CSS
 *
 * The width: "100%" parameter ensures that the image can scale down when the main div gets too narrow for it
 * Extract from @link http://jquery.malsup.com/cycle/options.html
 *  width - container width (if the 'fit' option is true, the slides will be set to this width as well)
 *  fit - to force slides to fit container
 *
 * CSS used in oik.css
 * .bw_cycler { width: 100% !important; }
 * .bw_cycler img { max-width: 100% !important; }
 *
 */
function _acf_cycler_cycle_parms() {
	$fx = get_field( 'fx');
	$cycle_parms = array( "width" => "100%"
	, "fit" => 1
	, "fx" => $fx
	);
	$parms = bw_jkv( $cycle_parms );
	return( $parms );
}

/**
 * Determines the HTML tag to use for selecting the inner block structure to cycle.
 *
 * @param $wp_block
 *
 * @return string
 */
function acf_cycler_innerblock_selector( $wp_block ) {
	$innerblockSelector = null;
	bw_trace2( $wp_block, "wp_block", false );
	$innerBlocks = $wp_block->parsed_block['innerBlocks'];
	if ( count( $innerBlocks)) {
		$innerblockName = $innerBlocks[0]['blockName'];
		switch ( $innerblockName ) {

			case 'core/query':
				$innerblockSelector = 'ul';
				break;

			case 'core/group':
			case 'core/list':
			default:
				// already set to null.
				break;;
		}
	}
	return $innerblockSelector;
}

}

/**
 * acf-cycler block template.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param array $context The context provided to the block by the post or its parent block.
 */
bw_trace2();
bw_backtrace();
bw_trace2( $block, "block", false);
bw_trace2( $content, "content", false );
bw_trace2( $context, "context", false );
//bw_trace2( $wp_block, "wp_block", false );

$innerblock_selector = acf_cycler_innerblock_selector( $wp_block );

/*
$fx = get_field( 'fx');
echo "FX: $fx";
*/


//$classes = ['block-about'];
//if( !empty( $block['className'] ) )
//	$classes = array_merge( $classes, explode( ' ', $block['className'] ) );

//$anchor = '';
//if( !empty( $block['anchor'] ) )
//	$anchor = ' id="' . sanitize_title( $block['anchor'] ) . '"';

if ( did_action( 'oik_loaded') ) {
	acf_cycler_jq( [], 'acf-cycler', $innerblock_selector);
	echo bw_ret();
} else {
	echo '<div class="acf-cycler cycler">';
}
echo '<InnerBlocks />';
echo '</div>';