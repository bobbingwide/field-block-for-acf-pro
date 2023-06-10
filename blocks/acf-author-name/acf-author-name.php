<?php
/**
 * acf-author-name block template.
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

$classes = ['acf-author-name'];
if( !empty( $block['className'] ) ) {
	$classes = array_merge( $classes, explode( ' ', $block['className'] ) );
}
$classes = implode( ' ', $classes);
//$anchor = '';
//if( !empty( $block['anchor'] ) )
//	$anchor = ' id="' . sanitize_title( $block['anchor'] ) . '"';

echo "<div class=\"$classes\">";
$author_name = get_field( '_oik_testimonials_name', $post_id );
echo esc_html( $author_name );
echo '</div>';