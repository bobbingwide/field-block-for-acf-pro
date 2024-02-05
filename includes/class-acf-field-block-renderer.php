<?php
/**
 * Renders an ACF field.
 *
 * @package acf-field-block
 * @copyright (C) Copyright Bobbing Wide 2023, 2024
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
    private $field_name; // The name of the field to be displayed
    private $field_info; // Information about the field
    private $field; // Field value(s)
	private $display_label;

    function __construct($block, $content, $context, $is_preview, $post_id, $wp_block)
    {
        bw_trace2( $block, "block", false );
	    bw_trace2( $content, "content", false );
		bw_trace2( $context, "context", false );
	    bw_trace2( $post_id, "post_id", false );
        $this->block = $block;
        $this->content = $content;
        $this->context = $context;
        $this->is_preview = $is_preview;
        $this->post_id = $post_id;
        $this->wp_block = $wp_block;
        $this->renderer = [$this, 'field_default_renderer'];
		$this->field_name = null;
		$this->field_info = null;
		$this->display_label = false;
    }

    function render()
    {
        $this->field_name = get_field('acf-field-name');
		$this->display_label = get_field( 'display-label');
	    bw_trace2( $this->field_name, 'field_name', false);
		bw_trace2( $this->post_id, 'post id', false);
        // We can't continue if the field name isn't set.
		if ( !$this->field_name ) {
			$this->render_no_field_name();
			return;
		}
		if ( $this->post_id ) {
			$this->field_info=get_field_object( $this->field_name, $this->post_id );
		}
		bw_trace2( $this->field_info, 'field_info', false);
        if (  $this->field_info) {
            $this->field = get_field($this->field_name, $this->post_id);
            $this->render_acf_field_classes($this->field_name, $this->field_info['type'], $this->block);
            $this->render_acf_field_contents();
            echo '</div>';
        } else {
            $this->render_no_field_info();
        }

    }

	/**
	 * Display a message if the field is not set.
	 *
	 * @TODO Q: Should we only do this in the block editor?
	 * @return void
	 */
    function render_no_field_info() {
        echo '<p>';
		/* Translators: 1 Field name, 2 Post ID */
        printf( esc_html__( 'Field %1$s not set for post ID %2$d', 'acf-field-block'), $this->field_name, $this->post_id );
        echo '</p>';

    }

	function render_no_field_name() {
		echo '<p>';
		printf( esc_html__( 'Field name not found for post ID %1$d', 'acf-field-block'), $this->post_id );
		echo '</p>';

	}

    /**
     * Displays classes for ACF field block.
     */
    function render_acf_field_classes($field_name, $field_type, $block)
    {
        $classes = ['acf-field-' . $field_name];
        $classes[] = 'acf-type-' . $field_type;
        if (!empty($block['className'])) {
            $classes = array_merge($classes, explode(' ', $block['className']));
        }
		if ( isset( $block['align'] ) ) {
			$classes = array_merge( $classes, [ 'has-text-align-' . $block['align'] ] );
		}

        $classes = implode(' ', $classes);
        $anchor = $block['anchor'] ?? null;
        $wrapper_attributes = get_block_wrapper_attributes(array('class' => trim($classes), 'id' => $anchor));
        echo '<div ';
        echo $wrapper_attributes;
        echo '>';
        bw_trace2($wrapper_attributes, 'wrapper attributes');
    }


    /**
     * Gets the renderer for the field.
     *
     * @return function / method to render the field
     */
    function get_renderer()
    {
        //bw_trace2();
        $renderer = [$this, 'render_acf_field_block_' . $this->field_info['type']];
        $renderer = apply_filters('acf_field_block_get_renderer', $renderer, $this->field_info, $this->field);
        if (!is_callable($renderer)) {
            $this->not_callable = $renderer;
            $renderer = [$this, 'render_acf_field_not_callable'];
        }
        $this->renderer = $renderer;
        return $renderer;
    }

	/**
	 * Renders the ACF field contents using the required renderer.
	 *
	 * @return void
	 */
    function render_acf_field_contents()
    {
		if ( $this->display_label ) {
			$this->display_field_label();
			echo '<div class="value">';
		}
        $this->get_renderer();
        $this->invoke_renderer();
		if ( $this->display_label ) {
			echo '</div>';
		}
    }

	/**
	 * Displays the field's label.
	 *
	 * @return void
	 */
	function display_field_label() {
		echo '<div class="label">';
		echo esc_html( $this->field_info['label'] );
		echo '</div>';
	}

	/**
	 * Invokes the rendering logic for the field.
	 *
	 * @return void
	 */
    function invoke_renderer()
    {
        $result = call_user_func($this->renderer, $this->field, $this->field_info, $this->post_id, $this);
        if (false === $result) {
            esc_html_e( "Something went wrong", 'acf-field-block' );
        }
    }

    /**
     * Renders information about a non-callable render method.
     *
     * @return void
     */
    function render_acf_field_not_callable()
    {
        $method = is_array($this->not_callable) ? get_class($this->not_callable[0]) . '::' . $this->not_callable[1] : $this->not_callable;
		/* Translators: 1: Name of method/function to render the block. */
        printf( esc_html__('Error: Render method not callable: %1$s', 'acf-field-block' ), $method);
        echo '<br />';
		/* Translators: 1: Name of the field being rendered. */
        printf(esc_html__( 'Field: %1$s', 'acf-field-block'), $this->field_name);
        echo '<br />';
		/* Translators: 1: Type of the field being rendered. */
        printf( esc_html__( 'Field type: %1$s', 'acf-field-block' ), $this->field_info['type'] );
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
    function render_acf_field_block_text($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo esc_html($field);
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
    function render_acf_field_block_textarea($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo esc_html($field);
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
    function render_acf_field_block_number($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo esc_html($field);
    }

    /**
     * Renders an ACF range field.
     *
     * @link https://www.advancedcustomfields.com/resources/range/
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_range($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo esc_html($field);
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
    function render_acf_field_block_password($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo esc_html($field);
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
     * @param $post_id
     * @param $acf_field_block_class
     *
     * @return void
     */
    function render_acf_field_block_image($field, $field_info, $post_id, $acf_field_block_class)
    {
        bw_trace2( $field, 'field', false );
        // Allow for no selection.
        if (!$field) {
            return;
        }
        switch ($field_info['return_format']) {
            case 'array':
                $image_size = $field_info['preview_size'] ?? 'full';
                $field_value = wp_get_attachment_image($field['ID'], $image_size);
                echo $field_value;
                break;

            case 'id':
                $image_size = $field_info['preview_size'] ?? 'full';
                $field_value = wp_get_attachment_image($field, $image_size);
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
    function render_acf_field_block_email($field, $field_info, $post_id, $acf_field_block_class)
    {
        //$email = $field
        echo '<a href="';
        echo esc_url('mailto:' . antispambot($field));
        echo '">';
        echo esc_html(antispambot($field));
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
    function render_acf_field_block_url($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo '<a href="';
        echo esc_url($field);
        echo '">';
        echo esc_attr($field);
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
    function render_acf_field_block_file($field, $field_info, $post_id, $acf_field_block_class)
    {
        if ( !$field ) {
            return;
        }
        //bw_trace2();
        switch ($field_info['return_format']) {
            case 'array':
                echo '<a href="';
                echo esc_url( $field['url'] );
                echo '">';
                // Display the file name or title?
                echo esc_html( $field['title'] );
                echo '</a>';
                break;

            case 'id':
                $url = wp_get_attachment_url($field);
                $url = esc_url($url);
                echo "<a href=\"$url\">";
	            esc_html_e( 'Download File', 'acf-field-block' );
				echo "</a>";
                break;

            case 'url':
            default:
                echo "<a href=\"$field\">";
	            esc_html_e( 'Download File', 'acf-field-block' );
	            echo "</a>";
        }
    }

    /**
     * Displays an ACF WYSIWYG field.
     *
     * We just echo the $field since it's already been processed through `acf_the_content`.
     *
     * @link https://www.advancedcustomfields.com/resources/wysiwyg
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_wysiwyg($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo $field;
        wp_enqueue_script('wp-embed');
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
    function render_acf_field_block_oembed($field, $field_info, $post_id, $acf_field_block_class)
    {
        echo $field;
        wp_enqueue_script('wp-embed');
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
    function render_acf_field_block_gallery($field, $field_info, $post_id, $acf_field_block_class)
    {
        if ( $field && count($field)) {
            echo '<ul>';
            foreach ($field as $image) {
                echo '<li>';
                $this->render_acf_field_block_image($image, $field_info, $post_id, $acf_field_block_class);
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
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_select($field, $field_info, $post_id, $acf_field_block_class)
    {
        bw_trace2();
        $value = null;
        switch ($field_info['return_format']) {
            case 'value':
                if ($field_info['multiple']) {
                    $values = [];
                    foreach ($field as $value) {
                        $values[] = $field_info['choices'][$value];
                    }
                    $value = implode(',', $values);
                } else {
                    $value = $field_info['choices'][$field];
                }
                break;

            case 'label':
                if ($field_info['multiple']) {
                    $value = implode(',', $field);
                } else {
                    $value = $field;
                }
                break;

            case 'array':

                $values = [];
                if ($field_info['multiple']) {
                    foreach ($field as $both) {
                        $values[] = $both['label'];
                    }
                    $value = implode(',', $values);
                } else {
                    $value = $field['label'];
                }
                break;
        }
        echo esc_html($value);

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
    function render_acf_field_block_checkbox($field, $field_info, $post_id, $acf_field_block_class)
    {
        $field_info['multiple'] = 1;
        $this->render_acf_field_block_select($field, $field_info, $post_id, $acf_field_block_class);
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
    function render_acf_field_block_radio($field, $field_info, $post_id, $acf_field_block_class)
    {
        $field_info['multiple'] = 0;
        $this->render_acf_field_block_select($field, $field_info, $post_id, $acf_field_block_class);
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
    function render_acf_field_block_button_group($field, $field_info, $post_id, $acf_field_block_class)
    {
        $field_info['multiple'] = 0;
        $this->render_acf_field_block_select($field, $field_info, $post_id, $acf_field_block_class);
    }

    /**
     * Displays an ACF true_false field.
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
    function render_acf_field_block_true_false($field, $field_info, $post_id, $acf_field_block_class)
    {
        if ($field) {
			esc_html_e( "Yes", 'acf-field-block' );
        } else {
            esc_html_e( "No", 'acf-field-block' );
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
    function render_acf_field_block_link($field, $field_info, $post_id, $acf_field_block_class)
    {
        if ( !$field ) {
            return;
        }
        if ('url' === $field_info['return_format']) {
            $field = get_field($field_info['name'], $post_id, false);
        }
        $link_url = $field['url'];
        $link_title = $field['title'];
        $link_target = $field['target'] ? $field['target'] : '_self';
        $this->field_block_display_link($link_url, $link_title, $link_target);
    }

	/**
	 * Displays a link.
	 *
	 * @param $link_url
	 * @param $link_title
	 * @param $link_target
	 *
	 * @return void
	 */
    function field_block_display_link($link_url, $link_title, $link_target = '_self')
    {
        echo '<a href="';
        echo esc_url($link_url);
        echo '" target="';
        echo esc_attr($link_target);
        echo '">';
        echo esc_html($link_title);
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
    function render_acf_field_block_post_object($field, $field_info, $post_id, $acf_field_block_class)
    {
        // Allow for no selection.
        if (!$field) {
            return;
        }
        $posts = is_array($field) ? $field : [$field];
        $multiple = $field_info['multiple'];
        if (count($posts)) {
            //if ( 'object' === $field_info['return_format']) {
            if ($multiple) {
                echo '<ul>';
            }
            foreach ($posts as $post) {
                if (is_numeric($post)) {
                    $post = get_post($post);
                }
                if ($multiple) {
                    echo '<li>';
                }
                // Page_link fields can include archives which are stored as URLs
                if (is_scalar($post)) {
                    $this->field_block_display_link($post, $post);
                } else {
                    $this->field_block_display_link(get_permalink($post->ID), $post->post_title);
                }
                if ($multiple) {
                    echo '</li>';
                }
            }
            if ($multiple) {
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
    function render_acf_field_block_page_link($field, $field_info, $post_id, $acf_field_block_class)
    {
        // Allow for no selection.
        if (!$field) {
            return;
        }
        //bw_trace2( $field, "field", true);
        $field = get_field($field_info['name'], $post_id, false);
        //bw_trace2( $field, "field unformatted", true);
        $this->render_acf_field_block_post_object($field, $field_info, $post_id, $acf_field_block_class);
    }

    /**
     * Displays an ACF relationship field.
     *
     * @link https://www.advancedcustomfields.com/resources/relationship
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_relationship($field, $field_info, $post_id, $acf_field_block_class)
    {
        // Allow for no selection.
        if (!$field) {
            return;
        }
        $field_info['multiple'] = 1;
        //bw_trace2( $field, "field", true);
        //$field = get_field( $field_info['name'], $post_id, false );
        //bw_trace2( $field, "field unformatted", true);
        $this->render_acf_field_block_post_object($field, $field_info, $post_id, $acf_field_block_class);
    }

    /**
     * Displays an ACF taxonomy field.
     *
     * @link https://www.advancedcustomfields.com/resources/taxonomy
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_taxonomy($field, $field_info, $post_id, $acf_field_block_class)
    {
        // Allow for no selection.
        if (!$field) {
            return;
        }
        bw_trace2();
        $terms = is_array($field) ? $field : [$field];
        // ACF doesn't correctly set the 'multiple' field.
        // So determine the value from the field type.
        $multiple = $field_info['multiple'];
        switch ($field_info['field_type']) {
            case 'multi_select':
            case 'checkbox':
                $multiple = 1;
                break;
            case 'select':
            case 'radio':
                $multiple = 0;
        }
        if (count($terms)) {
            if ($multiple) {
                echo '<ul>';
            }
            foreach ($terms as $term) {
                if (is_numeric($term)) {
                    $term = get_term($term);
                }
                if ($multiple) {
                    echo '<li>';
                }
                // Page_link fields can include archives which are stored as URLs
                if (is_scalar($term)) {
                    $this->field_block_display_link($term, $term);
                } else {
                    $this->field_block_display_link(get_term_link($term), $term->name);
                }
                if ($multiple) {
                    echo '</li>';
                }
            }
            if ($multiple) {
                echo '</ul>';
            }

        }
    }

    /**
     * Displays an ACF user field.
     *
     * @link https://www.advancedcustomfields.com/resources/user
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_user($field, $field_info, $post_id, $acf_field_block_class)
    {
        if ( !$field ) {
            return;
        }
        // bw_trace2();
        $multiple = $field_info['multiple'];
        if ($multiple) {
            echo '<ul>';
        }
        switch ($field_info['return_format']) {
            case 'array':
                if ($multiple) {
                    foreach ($field as $user) {
                        //$ids[] = $user['ID'];
                        echo '<li>';
                        $this->field_block_display_user($user['display_name']);
                        echo '</li>';
                    }
                } else {
                    $this->field_block_display_user($field['display_name']);
                }
                break;
            case 'object':
                if ($multiple) {
                    foreach ($field as $user) {
                        //$ids[] = $user->ID;
                        echo '<li>';
                        $this->field_block_display_user($user->display_name);
                        echo '</li>';
                    }
                } else {
                    //$ids[] = $field->ID;
                    $this->field_block_display_user($field->display_name);
                }
                break;
            case 'id':
                $field = is_array($field) ? $field : [$field];
                //if ( $field_info['multiple']) {
                foreach ($field as $id) {
                    $user = get_user_by('id', $id);
                    if ($multiple) {
                        echo '<li>';
                    }
                    $this->field_block_display_user($user->display_name);
                    if ($multiple) {
                        echo '</li>';
                    }
                }
                break;
        }
        if ($multiple) {
            echo '</ul>';
        }
    }

	/**
	 * Display the user's display name.
	 *
	 * @param $display_name
	 *
	 * @return void
	 */
    function field_block_display_user($display_name)
    {
        echo esc_html($display_name);
    }

    /**
     * Displays an ACF date_picker field.
     *
     * @link https://www.advancedcustomfields.com/resources/date-picker
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_date_picker($field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Displays an ACF date_time_picker field.
     *
     * @link https://www.advancedcustomfields.com/resources/date-time-picker
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_date_time_picker($field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }
    /**
     * Displays an ACF time_picker field.
     *
     * @link https://www.advancedcustomfields.com/resources/time-picker
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_time_picker($field, $field_info, $post_id, $acf_field_block_class) {
        echo esc_html( $field );
    }

    /**
     * Displays an ACF color_picker field.
     *
     * @link https://www.advancedcustomfields.com/resources/color-picker
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_color_picker($field, $field_info, $post_id, $acf_field_block_class) {
        $field = is_array( $field) ? 'rgba(' . implode( ',', $field) . ')': $field;
        echo '<span style="background-color: ' . $field . ';">';
        echo '&nbsp;</span>&nbsp;';
        echo esc_html( $field );
    }

    /**
     * Displays an ACF group field.
     *
     * $field contains an array of sub_fields values
     * $field_info contains 'sub_fields', which is an array of the fields.
     *
     * @link https://www.advancedcustomfields.com/resources/group
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_group($field, $field_info, $post_id, $acf_field_block_class) {
        bw_trace2( $field, "field", false );
        bw_trace2( $field_info, "field_info", false );
        //echo esc_html( $field_info['name'] );
        foreach ( $field_info['sub_fields'] as $sub_field_info ) {
            $this->field_name = $sub_field_info['name'];
            //echo $this->field_name;
            //$this->field = get_sub_field( $sub_field_info['name']);
            $this->field = $field[$this->field_name ];
            //echo $this->field;
            $this->field_info = $sub_field_info;
            //$this->render_acf_field_contents();
            $this->render_acf_field_classes($this->field_name, $this->field_info['type'], $this->block);
            $this->render_acf_field_contents();
            echo '</div>';
        }
    }

    /**
     * Displays an ACF repeater field.
     *
     * $field contains a multidimensional array of sub_fields values.
     * $field_info contains 'sub_fields', which is an array of the fields.
     *
     * @link https://www.advancedcustomfields.com/resources/repeater
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_repeater($field, $field_info, $post_id, $acf_field_block_class) {
        bw_trace2( $field, "field", false );
        bw_trace2( $field_info, "field_info", false );
        //echo esc_html( $field_info['name'] );
        //echo $this->field_name;
        if ( $field && count( $field ) ) {
            echo '<ul>';
            foreach ($field as $row) {
                echo '<li>';
                foreach ($field_info['sub_fields'] as $sub_field_info) {

                    $this->field_name = $sub_field_info['name'];
                    //echo $this->field_name;
                    //$this->field = get_sub_field( $sub_field_info['name']);
                    $this->field = $row[$this->field_name];
                    //echo $this->field;
                    $this->field_info = $sub_field_info;
                    //$this->render_acf_field_contents();
                    $this->render_acf_field_classes($this->field_name, $this->field_info['type'], $this->block);
                    $this->render_acf_field_contents();
                    echo '</div>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    /**
     * Displays an ACF flexible content field.
     *
     * @link https://www.advancedcustomfields.com/resources/flexible-content
     *
     * $field contains the layouts and field values
     * $field_info contains the array of layouts and sub_fields
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_flexible_content($field, $field_info, $post_id, $acf_field_block_class) {
        bw_trace2( $field, "field", false );
        bw_trace2( $field_info, "field_info", false );

        foreach ( $field as $section ) {
            $layout_name = $section['acf_fc_layout'];
            echo '<div class="' . esc_attr( $layout_name ) . '">';
            $layout = $this->get_layout( $field_info['layouts'], $layout_name );
            $this->render_layout( $layout, $section );
            echo '</div>';
        }
    }

    /**
     * Returns the layout for the section.
     *
     * @param $layouts
     * @param $layout_name
     * @return mixed|null
     */
    function get_layout( $layouts, $layout_name ) {
        $layout = null;
        foreach ( $layouts as $layout_n ) {
            if ( $layout_name === $layout_n[ 'name']) {
                $layout = $layout_n;
            }
        }
        return $layout;
    }

    /**
     * Renders each of the subfields in a layout section.
     *
     * @param $layout
     * @param $section
     * @return void
     */
    function render_layout( $layout, $section ) {
        foreach ($layout['sub_fields'] as $sub_field_info) {
            $this->field_name = $sub_field_info['name'];
             $this->field = $section[ $this->field_name];

            $this->field_info = $sub_field_info;
            $this->render_acf_field_classes($this->field_name, $this->field_info['type'], $this->block);
            $this->render_acf_field_contents();
            echo '</div>';
        }
    }

    /**
     * Displays an ACF clone field.
     *
     * @link https://www.advancedcustomfields.com/resources/clone
     *
     * $field contains the field values
     * $field_info contains the sub_fields
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_clone( $field, $field_info, $post_id, $acf_field_block_class) {
        bw_trace2( $field, "field", false );
        bw_trace2( $field_info, "field_info", false );
        $this->render_layout( $field_info, $field );
    }

    /**
     * Displays an ACF google_map field.
     *
     * @link https://www.advancedcustomfields.com/resources/google_map
     *
     * @param $field
     * @param $field_info
     * @param $post_id
     * @param $acf_field_block_class
     * @return void
     */
    function render_acf_field_block_google_map( $field, $field_info, $post_id, $acf_field_block_class) {
        //bw_trace2( $field, "field", false );
        //bw_trace2( $field_info, "field_info", false );
        //echo esc_html( $field_info['name'] );
        // script
        $api = [];
        $api = apply_filters( 'acf/fields/google_map/api', $api );
        // bw_trace2( $api, "api", false );
        if ( $api['key']) {
            //echo '<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=Function.prototype"></script>';
            wp_enqueue_script('acf-field-google_map', plugin_dir_url( __FILE__ ) . '/acf-field-google_map.js', array('jquery'));
            wp_enqueue_script('acf-field_google-maps',
                sprintf('https://maps.googleapis.com/maps/api/js?key=%s&callback=Function.prototype', $api['key'] ),
                [
                    // setup deps, to make sure loaded only after plugin's maps.min.js
                    'acf-field-google_map'
                ],
                null,
                true
            );
        } else {
            echo "Google Maps API key not set";
        }

        $lat = $field['lat'] ?? null;
        $lng = $field['lng'] ?? null;
        if ( $this->is_preview )  {
            if ( $lat && $lng ) {
                esc_html_e( "Google Map goes here.", 'acf-field-block' );
            } else {
               esc_html_e("Please set the address for this Google Maps map", 'acf-field-block' );
            }

        }
        if ( $lat && $lng ) {
            $marker_html = $field['address'] ?? '';
            $zoom = $field['zoom'] ?? 14;
            echo '<div class="acf-map" data-zoom="' . $zoom . '">';
            echo '<div class="marker" data-lat="' . esc_attr($lat) . '" data-lng="' . esc_attr($lng ) . '">';
            echo $marker_html;
            echo '</div>';
            echo '</div>';
        }
    }
}