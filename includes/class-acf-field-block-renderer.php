<?php
/**
 * Renders an ACF field.
 *
 * @package acf-field-block
 * @copyright (C) Copyright Bobbing Wide 2023
 * @depends ACF PRO
 */

class acf_field_block_renderer
{

    private $renderer; // = [ $this, 'field_default_renderer'];
    private $not_callable; // Invalid name of the rendering method / function
    private $block;
    private $content;
    private $context;
    private $is_preview;
    private $post_id;
    private $wp_block;
    private $field_name;
    private $field_info;

    function __construct( $block, $content, $context, $is_preview, $post_id, $wp_block ) {
        bw_trace2();
        $this->block = $block;
        $this->content = $content;
        $this->context = $context;
        $this->is_preview = $is_preview;
        $this->post_id = $post_id;
        $this->wp_block = $wp_block;
        $this->renderer = [$this, 'field_default_renderer'];
    }

    function render() {
        //echo "Rendering block";
        $this->field_name = get_field( 'acf-field-name');
        // What if the field name isn't set?

        $this->field_info = get_field_object( $this->field_name, $this->post_id );
        if ( $this->field_info ) {
            $this->field = get_field( $this->field_name, $this->post_id );
            $this->render_acf_field_classes($this->field_name, $this->field_info['type'], $this->block);
            $this->render_acf_field_contents();
            echo '</div>';
        } else {
            //gob();
            echo $this->field_name;
            print_r( $this->field_info );
            echo ':';
            echo $this->post_id;
            echo 'eh?';
        }

    }

    /**
     * Displays classes for ACF field block.
     */
    function render_acf_field_classes( $field_name, $field_type, $block ) {
        $classes=[ 'acf-field-' . $field_name ];
        //$classes[] = $field_name;
        $classes[] = 'acf-type-' .$field_type;
        if ( ! empty( $block['className'] ) ) {
            $classes=array_merge( $classes, explode( ' ', $block['className'] ) );
        }

        $classes=implode( ' ', $classes );
        $anchor = $block['anchor'] ?? null;
        //if( !empty( $block['anchor'] ) )
        //	$anchor = ' id="' . sanitize_title( $block['anchor'] ) . '"';

        //echo "<div class=\"$classes\">";
        $wrapper_attributes = get_block_wrapper_attributes( array( 'class' => trim( $classes ), 'id' => $anchor ) );
        echo '<div ';
        echo $wrapper_attributes;
        echo '>';
        bw_trace2( $wrapper_attributes, 'wrapper attributes');
    }


    /**
     * Gets the renderer for the field.
     *
     */
    function get_renderer() {
        bw_trace2();
        $renderer = [ $this, 'render_acf_field_block_' . $this->field_info['type'] ];
        $renderer = apply_filters( 'acf_field_block_get_renderer', $renderer, $this->field_info, $this->field );
        if ( !is_callable( $renderer )) {
            $this->not_callable = $renderer;
            $renderer = [ $this, 'render_acf_field_not_callable' ];
        }
        $this->renderer = $renderer;
        return $renderer;
    }

    function render_acf_field_contents() {
        $this->get_renderer();
        $this->invoke_renderer();
    }

    function invoke_renderer() {
        $result = call_user_func( $this->renderer, $this->field, $this->field_info, $this->post_id, $this  );
        if ( false === $result ) {
            echo "Something went wrong";
        }

    }

    /**
     * Renders information about a non-callable render method.
     *
     * @return void
     */
    function render_acf_field_not_callable() {
        $method = is_array( $this->not_callable) ? get_class( $this->not_callable[0] ) .'::'. $this->not_callable[1] : $this->not_callable;
        printf( 'Error: Render method not callable: %1$s' ,  $method );
        echo '<br />';
        printf(  'Field: %1$s' , $this->field_name );
        echo '<br />';
        echo "Field type: " . $this->field_info['type'];
    }

    /**
     * Renders an ACF text field.
     * 
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_text( $field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Renders an ACF textarea field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_textarea( $field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Renders an ACF number field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_number( $field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Renders an ACF range field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_range( $field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Renders an ACF password field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_password( $field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Displays an ACF image.
     *
     * return_format | processing
     * ------------- | ---------
     * array | use $field['id'] and fetch the attachment using wp_get_attachment_image()
     * id | fetch the attachment - as for array
     * url | use the URL ASIS. This is the most basic solution.
     *
     * @link https://www.advancedcustomfields.com/resources/image/

     * @param $field
     * @param $field_info
     *
     * @return void
     */
    function render_acf_field_block_image( $field, $field_info, $post_id, $acf_field_block_class ) {
        bw_trace2();
        switch ( $field_info['return_format']) {
            case 'array':
                $image_size = $field_info['preview_size'] ?? 'full';
                $field_value = wp_get_attachment_image( $field['ID'], $image_size );
                echo $field_value;
                break;

            case 'id':
                $image_size = $field_info['preview_size'] ?? 'full';
                $field_value = wp_get_attachment_image( $field, $image_size );
                echo $field_value;
                break;

            case 'url':
            default:
                echo "<img src=\"$field\"/>";
        }
        //bw_format_attachment();_image
    }

    /**
     * Displays an email field.
     *
     * Uses antispambot() to obfuscate the email address.
     * But note that most browser's inspector's will display the easy to read version.
     *
     * @link https://www.advancedcustomfields.com/resources/email/
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     *
     * @return void
     */
    function render_acf_field_block_email( $field, $field_info, $post_id, $acf_field_block_class) {
        //$email = $field
        echo '<a href="';
        echo esc_url( 'mailto:' . antispambot( $field ) );
        echo '">';
        echo esc_html( antispambot( $field ) );
        echo '</a>';
    }

    /**
     * Displays an URL field
     *
     * @link https://www.advancedcustomfields.com/resources/url/
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     *
     * @return void
     */
    function render_acf_field_block_url( $field, $field_info, $post_id, $acf_field_block_class ) {
        echo '<a href="';
        echo esc_url( $field );
        echo '">';
        echo esc_attr( $field );
        echo '</a>';
    }

    /**
     * Displays an ACF file.
     *
     * return_format | processing
     * ------------- | ---------
     * array | Display a link to the file
     * id | use the attachment URL
     * url | use the URL ASIS.
     *
     * @link https://www.advancedcustomfields.com/resources/file

     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     *
     * @return void
     */
    function render_acf_field_block_file( $field, $field_info, $post_id, $acf_field_block_class ) {
        bw_trace2();
        switch ( $field_info['return_format']) {
            case 'array':
                echo '<a href="';
                echo $field['url'];
                echo '">';
                // Display the file name or title?
                echo $field['title'];
                echo '</a>';
                break;

            case 'id':
                $url = wp_get_attachment_url( $field );
                $url = esc_html( $url );
                echo "<a href=\"$url\">Download File</a>";
                break;

            case 'url':
            default:
                echo "<a href=\"$field\">Download File</a>";
        }
    }

    /**
     * Displays an ACF WYSIWYG field.
     *
     * We just echo the $field since it's already been processed through `acf_field_block_the_content`.
     *
     * @link https://www.advancedcustomfields.com/resources/wysiwyg

     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_wysiwyg( $field, $field_info, $post_id, $acf_field_block_class ) {
        echo $field;
        wp_enqueue_script( 'wp-embed');
    }

    /**
     * Displays an ACF oEmbed field.
     *
     * Echo the $field and enqueue the wp-embed script for the front end.
     *
     * @link https://www.advancedcustomfields.com/resources/oembed

     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_oembed( $field, $field_info, $post_id, $acf_field_block_class ) {
        echo $field;
        wp_enqueue_script( 'wp-embed');
    }

    /**
     * Displays an ACF gallery field.
     *
     * Displays an array of images in a gallery.
     * Uses the logic to display an image but within a list.
     *
     * @link https://www.advancedcustomfields.com/add-ons/gallery-field/

     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_gallery( $field, $field_info, $post_id, $acf_field_block_class ) {
        if ( count( $field ) ) {
            echo '<ul>';
            foreach ( $field as $image ) {
                echo '<li>';
                $this->render_acf_field_block_image( $image, $field_info, $post_id, $acf_field_block_class );
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    /**
     * Displays an ACF select field.
     *
     * Displays the selected value(s) of a select field.
     * Also used for checkbox, which is always 'multiple'.
     * And radio and button_group which are never multiple.
     *
     * @link https://www.advancedcustomfields.com/resources/select/
     * @link https://www.advancedcustomfields.com/resources/checkbox/
     * @link https://www.advancedcustomfields.com/resources/radio-button/
     * @link https://www.advancedcustomfields.com/resources/button-group/

     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_select( $field, $field_info, $post_id, $acf_field_block_class ) {
        bw_trace2();
        $value = null;
        switch ( $field_info['return_format'] ) {
            case 'value':
                if ( $field_info['multiple'] ) {
                    $values = [];
                    foreach ( $field as $value ) {
                        $values[] = $field_info['choices'][ $value ];
                    }
                    $value = implode( ',', $values );
                } else {
                    $value = $field_info['choices'][ $field ];
                }
                break;

            case 'label':
                if ( $field_info['multiple'] ) {
                    $value = implode( ',', $field );
                } else {
                    $value = $field;
                }
                break;

            case 'array':

                $values = [];
                if ( $field_info['multiple'] ) {
                    foreach ( $field as $both ) {
                        $values[] = $both['label'];
                    }
                    $value = implode( ',', $values ) ;
                } else {
                    $value = $field['label'] ;
                }
                break;
        }
        echo esc_html( $value );

    }

    /**
     * Renders an ACF checkbox field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_checkbox( $field, $field_info, $post_id, $acf_field_block_class) {
        $field_info['multiple'] = 1;
        $this->render_acf_field_block_select( $field, $field_info, $post_id, $acf_field_block_class );
    }

    /**
     * Renders an ACF radio field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_radio( $field, $field_info, $post_id, $acf_field_block_class) {
        $field_info['multiple'] = 0;
        $this->render_acf_field_block_select( $field, $field_info, $post_id, $acf_field_block_class );
    }

    /**
     * Renders an ACF button_group field.
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_button_group( $field, $field_info, $post_id, $acf_field_block_class) {
        $field_info['multiple'] = 0;
        $this->render_acf_field_block_select( $field, $field_info, $post_id, $acf_field_block_class );
    }

    /**
     * Displays an ACF true_false field.
     *
     *
     * @link https://www.advancedcustomfields.com/resources/true-false
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     *
     * @return void
     */
    function render_acf_field_block_true_false( $field, $field_info, $post_id, $acf_field_block_class ) {
        if ( $field) {
            echo "Yes";
        } else {
            echo "No";
        }
    }

    /**
     * Displays an ACF link field.
     *
     * If the `return_format` has been set to `url` rather than `array` we don't get the link title
     * or target. This isn't particularly user friendly. So we force it to return the array by
     * getting the field again, this time without formatting.
     *
     * @link https://www.advancedcustomfields.com/resources/link
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_link( $field, $field_info, $post_id, $acf_field_block_class ) {

        if ( 'url' === $field_info['return_format'] ) {
            $field = get_field( $field_info['name'], $post_id, false );
        }
        $link_url   =$field['url'];
        $link_title =$field['title'];
        $link_target=$field['target'] ? $field['target'] : '_self';
        $this->field_block_display_link( $link_url, $link_title, $link_target );
    }

    function field_block_display_link( $link_url, $link_title, $link_target='_self') {
        echo '<a href="';
        echo esc_url( $link_url );
        echo '" target="';
        echo esc_attr( $link_target );
        echo '">';
        echo esc_html( $link_title );
        echo '</a>';
    }

    /**
     * Displays an ACF post object field.
     *
     * Also used for displaying page_link fields.
     *
     * @link https://www.advancedcustomfields.com/resources/post-object
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_post_object( $field, $field_info, $post_id, $acf_field_block_class ) {
        // Allow for no selection.
        if ( !$field ) {
            return;
        }
        $posts= is_array( $field ) ? $field : [ $field ];
        $multiple = $field_info['multiple'];
        if ( count( $posts )) {
            //if ( 'object' === $field_info['return_format']) {
            if ( $multiple ) {
                echo '<ul>';
            }
            foreach ( $posts as $post ) {
                if ( is_numeric( $post ) ) {
                    $post=get_post( $post );
                }
                if ( $multiple ) {
                    echo '<li>';
                }
                // Page_link fields can include archives which are stored as URLs
                if ( is_scalar( $post )) {
                    $this->field_block_display_link( $post, $post );
                } else {
                    $this->field_block_display_link( get_permalink( $post->ID ), $post->post_title );
                }
                if ( $multiple ) {
                    echo '</li>';
                }
            }
            if ( $multiple ) {
                echo '</ul>';
            }
        }

    }

    /**
     * Displays an ACF page link field.
     *
     * Not as useful as post_object since the field returns the URL for archive.
     *
     * @link https://www.advancedcustomfields.com/resources/page-link
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_page_link( $field, $field_info, $post_id, $acf_field_block_class ) {
        // Allow for no selection.
        if ( !$field ) {
            return;
        }
        //bw_trace2( $field, "field", true);
        $field = get_field( $field_info['name'], $post_id, false );
        //bw_trace2( $field, "field unformatted", true);
        $this->render_acf_field_block_post_object( $field, $field_info, $post_id, $acf_field_block_class );
    }

    /**
     * Displays an ACF relationship field.
     *
     *
     * @link https://www.advancedcustomfields.com/resources/relationship
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_relationship( $field, $field_info, $post_id, $acf_field_block_class ) {
        // Allow for no selection.
        if ( !$field ) {
            return;
        }
        $field_info['multiple'] = 1;
        //bw_trace2( $field, "field", true);
        //$field = get_field( $field_info['name'], $post_id, false );
        //bw_trace2( $field, "field unformatted", true);
        $this->render_acf_field_block_post_object( $field, $field_info, $post_id, $acf_field_block_class );
    }

    /**
     * Displays an ACF taxonomy field.
     *
     *
     * @link https://www.advancedcustomfields.com/resources/taxonomy
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_taxonomy( $field, $field_info, $post_id, $acf_field_block_class ) {
        // Allow for no selection.
        if ( ! $field ) {
            return;
        }
        bw_trace2();
        $terms   =is_array( $field ) ? $field : [ $field ];
        // ACF doesn't correctly set the 'multiple' field.
        // So determine the value from the field type.
        $multiple=$field_info['multiple'];
        switch ( $field_info['field_type']) {
            case 'multi_select':
            case 'checkbox':
                $multiple = 1;
                break;
            case 'select':
            case 'radio':
                $multiple = 0;
        }
        if ( count( $terms ) ) {
            if ( $multiple ) {
                echo '<ul>';
            }
            foreach ( $terms as $term ) {
                if ( is_numeric( $term ) ) {
                    $term=get_term( $term );
                }
                if ( $multiple ) {
                    echo '<li>';
                }
                // Page_link fields can include archives which are stored as URLs
                if ( is_scalar( $term ) ) {
                    $this->field_block_display_link( $term, $term );
                } else {
                    $this->field_block_display_link( get_term_link( $term ), $term->name );
                }
                if ( $multiple ) {
                    echo '</li>';
                }
            }
            if ( $multiple ) {
                echo '</ul>';
            }

        }
    }

}