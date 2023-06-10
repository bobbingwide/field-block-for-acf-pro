<?php

/**
 * @copyright (C) Copyright Bobbing Wide 2023
 * @package oik-testiminials
 * @depends ACF PRO
 */

/**
 * Displays classes for ACF field block.
 */
function acf_display_field_block_classes( $field_name, $field_type, $block ) {
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
 * Displays an ACF field block
 */
function acf_display_field_block( $block, $content, $is_preview, $post_id, $wp_block ) {
	$field_name = get_field( 'acf-field-name');
	/**
	 * Cater for blocks that haven't been updated to use acf-field-name
	 */
	if ( !$field_name) {
		$field_name = get_field( 'field-name');
		echo "Falling back to using 'field-name'. Update block to use 'acf-field-name'";

	}
	$field_info= get_field_object( $field_name, $post_id );
	acf_display_field_block_classes( $field_name, $field_info['type'], $block);
	acf_display_field( $field_name, $field_info, $post_id );
	echo '</div>';

}
/**
 * Generic display of an ACF field.
 *
 * - This logic should attempt to cater for all different field types.
 * - Except those it doesn't know about.
 * - @TODO So it should provide some hooks to enable other routines to do this.
 *
 * @param $field_name
 * @param $post_id
 *
 * @return void*
 */
function acf_display_field( $field_name, $field_info, $post_id ) {

	//$field_name=get_field( 'field-name' );
	$field     =get_field( $field_name, $post_id );
	//echo $field;

	bw_trace2( $field_info, 'field_info');
	if ( $field_info ) {
		switch ( $field_info['type'] ) {
			case 'text':
				echo esc_html( $field );
				break;
			case 'image':
				acf_display_field_image( $field, $field_info );
				break;

			case 'email':
				acf_display_field_email( $field, $field_info );
				break;
			case 'url':
				acf_display_field_url( $field, $field_info );
				break;
			case 'file':
				acf_display_field_file( $field, $field_info );
				break;
			case 'wysiwyg':
				acf_display_field_wysiwyg( $field, $field_info );
				break;
			case 'oembed':
				acf_display_field_oembed( $field, $field_info );
				break;
			case 'gallery':
				acf_display_field_gallery( $field, $field_info );
				break;
			case 'checkbox':
				$field_info['multiple'] = 1;
			case 'select':
				acf_display_field_select( $field, $field_info );
				break;
			case 'radio':
			case 'button_group':
				$field_info['multiple'] = 0;
				acf_display_field_select( $field, $field_info );
				break;

			case 'true_false':
				acf_display_field_true_false( $field, $field_info );
				break;
			case 'link':
				acf_display_field_link( $field, $field_info, $post_id );
				break;
			case 'post_object':
				acf_display_field_post_object( $field, $field_info, $post_id );
				break;
			case 'page_link':
				acf_display_field_page_link( $field, $field_info, $post_id );
				break;
			case 'relationship':
				acf_display_field_relationship( $field, $field_info, $post_id );
				break;
			case 'taxonomy':
				acf_display_field_taxonomy( $field, $field_info, $post_id );
				break;

			default:
				echo esc_html( $field );
		}
	} else {
		// get_field_object() returns false if the field isn't defined or set.
		echo "<p>Field '$field_name' not found for $post_id.";
		//echo "Is the field even defined?";

		echo '<br />';
		echo 'Perhaps you need to Publish/Update the post';
		//echo "Field: " . $field;
		echo '</p>';
		//acf_list_possible_field_names( $field_name, $post_id );

	}
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
function acf_display_field_image( $field, $field_info ) {
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
 *
 * @return void
 */
function acf_display_field_email( $field, $field_info) {
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
 *
 * @return void
 */
function acf_display_field_url( $field, $field_info ) {
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
 *
 * @return void
 */
function acf_display_field_file( $field, $field_info ) {
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
 * We just echo the $field since it's already been processed through `acf_the_content`.
 *
 * @link https://www.advancedcustomfields.com/resources/wysiwyg

 * @param $field
 * @param $field_info
 * @return void
 */
function acf_display_field_wysiwyg( $field, $field_info ) {
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
 * @return void
 */
function acf_display_field_oembed( $field, $field_info ) {
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
 * @return void
 */
function acf_display_field_gallery( $field, $field_info ) {
	if ( count( $field ) ) {
		echo '<ul>';
		foreach ( $field as $image ) {
			echo '<li>';
			acf_display_field_image( $image, $field_info );
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
 * @return void
 */
function acf_display_field_select( $field, $field_info ) {
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
 * Displays an ACF true_false field.
 *
 *
 * @link https://www.advancedcustomfields.com/resources/true-false
 *
 * @param $field
 * @param $field_info
 * @return void
 */
function acf_display_field_true_false( $field, $field_info ) {
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
 * @return void
 */
function acf_display_field_link( $field, $field_info, $post_id ) {

	if ( 'url' === $field_info['return_format'] ) {
		$field = get_field( $field_info['name'], $post_id, false );
	}
	$link_url   =$field['url'];
	$link_title =$field['title'];
	$link_target=$field['target'] ? $field['target'] : '_self';
	acf_display_link( $link_url, $link_title, $link_target );
}

function acf_display_link( $link_url, $link_title, $link_target='_self') {
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
 * @return void
 */
function acf_display_field_post_object( $field, $field_info, $post_id ) {
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
				acf_display_link( $post, $post );
			} else {
				acf_display_link( get_permalink( $post->ID ), $post->post_title );
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
 * @return void
 */
function acf_display_field_page_link( $field, $field_info, $post_id ) {
	// Allow for no selection.
	if ( !$field ) {
		return;
	}
	//bw_trace2( $field, "field", true);
	$field = get_field( $field_info['name'], $post_id, false );
	//bw_trace2( $field, "field unformatted", true);
	acf_display_field_post_object( $field, $field_info, $post_id );
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
 * @return void
 */
function acf_display_field_relationship( $field, $field_info, $post_id ) {
	// Allow for no selection.
	if ( !$field ) {
		return;
	}
	$field_info['multiple'] = 1;
	//bw_trace2( $field, "field", true);
	//$field = get_field( $field_info['name'], $post_id, false );
	//bw_trace2( $field, "field unformatted", true);
	acf_display_field_post_object( $field, $field_info, $post_id );
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
 * @return void
 */
function acf_display_field_taxonomy( $field, $field_info, $post_id ) {
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
				acf_display_link( $term, $term );
			} else {
				acf_display_link( get_term_link( $term ), $term->name );
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