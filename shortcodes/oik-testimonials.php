<?php // (C) Copyright Bobbing Wide 2013, 2023

/** 
 * Return the next unique ID for the testimonial selector
 */
function bw_testimonial_id() { 
  static $testimonial_id = 0;
  $testimonial_id++;
  return( "bw_testimonial-$testimonial_id" );
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
 * .bw_testimonial { width: 100% !important; }
 * .bw_testimonial img { max-width: 100% !important; }
 *
 */
function _bw_testimonials_cycle_parms() {
  $cycle_parms = array( "width" => "100%"
                      , "fit" => 1
                      );
  $parms = bw_jkv( $cycle_parms );
  return( $parms );
}    

/**
 * Create the jQuery code to cycle the selection, including the starting div
 * This code uses jQuery cycle.all 
 */
function bw_testimonials_jq( $atts, $tag ) {
  oik_require( "shortcodes/oik-jquery.php" );
  $debug = bw_array_get( $atts, "debug", false );
  $script = bw_array_get( $atts, "script", "cycle.all" );
  $method = bw_array_get( $atts, "method", "cycle" );
  bw_jquery_enqueue_script( $script, $debug );
  // bw_jquery_enqueue_style( $script );
  
  // bw_jquery( $selector, $method, $parms, $windowload );
  $selector = bw_testimonial_id();
  $parms = _bw_testimonials_cycle_parms();
  bw_jquery( "#$selector", $method, $parms );
  $class = bw_array_get( $atts, "class", $tag );
  sdiv( $class, $selector );
}

/**
 * Implement [bw_testimonials] shortcode
 *
 * Defaults: *  numberposts=5, post_type=oik_testimonials, orderby=rand
 * 
 * @param array $atts - shortcode parameters
 * @param string $content - not expected
 * @param string $tag 
 * @return expanded shortcode
 */
function bw_testimonials( $atts=null, $content=null, $tag=null ) {
  bw_testimonials_jq( $atts, $tag );
  $atts['numberposts'] = bw_array_get( $atts, "numberposts", 5 );
  $atts['post_type'] = bw_array_get( $atts, "post_type", "oik_testimonials" );
  $atts['orderby'] = bw_array_get( $atts, "orderby", "rand" );
  $atts['post_parent'] = 0;
  $atts['format'] = bw_array_get( $atts, 'format', 'LE_M' );
  oik_require( "shortcodes/oik-pages.php" );
  e( bw_pages( $atts ));
  ediv();
  return( bw_ret()); 
} 

/**
 * Help hook for [bw_testimonials] 
 */
function bw_testimonials__help( $shortcode="bw_testimonials" ) {
  return( "Display testimonials" );
}

function bw_testimonials__syntax( $shortcode="bw_testimonials" ) {
  $syntax = array( "numberposts" => bw_skv( 5, "<i>number</i>", "Number of posts to show" )
                 , "script" => bw_skv( "cycle.all", "<i>script</i>", "jQuery script" ) 
                 , "method" => bw_skv( "cycle", "<i>method</i>", "jQuery method " )
                 , "class" => bw_skv( $shortcode, "CSS classes", "CSS classes" )
                 );
  $syntax += _sc_posts(); 
  $syntax['post_type'] = bw_skv( "oik_testimonials", "<i>post_type</i>", "Post type to select" );
  $syntax['orderby'] = bw_skv( "rand", "date|title|author", "Order by" );
  $syntax['order'] = bw_skv( "ASC", "DESC", "Order" );
  return( $syntax );
}                   


