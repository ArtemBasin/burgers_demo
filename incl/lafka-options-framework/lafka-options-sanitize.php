<?php

/* Text */

add_filter('lafka_sanitize_text', 'sanitize_text_field');

/* Sidebars */

add_filter('lafka_sanitize_sidebar', 'sanitize_text_field');

/* Textarea */

function lafka_sanitize_textarea($input) {
	global $allowedposttags;
	$output = wp_kses($input, $allowedposttags);
	return $output;
}

add_filter('lafka_sanitize_textarea', 'lafka_sanitize_textarea');

/* Select */

add_filter('lafka_sanitize_select', 'lafka_sanitize_enum', 10, 2);

/* Radio */

add_filter('lafka_sanitize_radio', 'lafka_sanitize_enum', 10, 2);

/* Images */

add_filter('lafka_sanitize_images', 'lafka_sanitize_enum', 10, 2);

/* Checkbox */

function lafka_sanitize_checkbox($input) {
	if ($input) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}

add_filter('lafka_sanitize_checkbox', 'lafka_sanitize_checkbox');

/* Multicheck */

function lafka_sanitize_multicheck($input, $option) {
	$output = array();
	if (is_array($input)) {
		foreach ($option['options'] as $key => $value) {
			$output[$key] = "0";
		}
		foreach ($input as $key => $value) {
			if (array_key_exists($key, $option['options']) && $value) {
				$output[$key] = "1";
			}
		}
	}
	return $output;
}

add_filter('lafka_sanitize_multicheck', 'lafka_sanitize_multicheck', 10, 2);

/* Color Picker */

add_filter('lafka_sanitize_color', 'lafka_sanitize_hex');

/* Uploader */

function lafka_sanitize_upload($input) {
	$output = esc_attr($input);
	return $output;
}

add_filter('lafka_sanitize_upload', 'lafka_sanitize_upload');

/* lafka_upload */

function lafka_sanitize_lafka_upload($input) {
	$output = esc_attr($input);

	return $output;
}

add_filter('lafka_sanitize_lafka_upload', 'lafka_sanitize_lafka_upload');

/* Editor */

function lafka_sanitize_editor($input) {
	if (current_user_can('unfiltered_html')) {
		$output = $input;
	} else {
		global $allowedtags;
		$output = wpautop(wp_kses($input, $allowedtags));
	}
	return $output;
}

add_filter('lafka_sanitize_editor', 'lafka_sanitize_editor');

/* Allowed Tags */

function lafka_sanitize_allowedtags($input) {
	global $allowedtags;
	$output = wpautop(wp_kses($input, $allowedtags));
	return $output;
}

/* Allowed Post Tags */

function lafka_sanitize_allowedposttags($input) {
	global $allowedposttags;
	$output = wpautop(wp_kses($input, $allowedposttags));
	return $output;
}

add_filter('lafka_sanitize_info', 'lafka_sanitize_allowedposttags');


/* Check that the key value sent is valid */

function lafka_sanitize_enum($input, $option) {
	$output = '';
	if (array_key_exists($input, $option['options'])) {
		$output = $input;
	}
	return $output;
}

/* Background */

function lafka_sanitize_background($input) {
	$output = wp_parse_args($input, array(
			'color' => '',
			'image' => '',
			'repeat' => 'repeat',
			'position' => 'top center',
			'attachment' => 'scroll'
	));

	$output['color'] = apply_filters('lafka_sanitize_hex', $input['color']);
	$output['image'] = apply_filters('lafka_sanitize_upload', $input['image']);
	$output['repeat'] = apply_filters('lafka_background_repeat', $input['repeat']);
	$output['position'] = apply_filters('lafka_background_position', $input['position']);
	$output['attachment'] = apply_filters('lafka_background_attachment', $input['attachment']);

	return $output;
}

add_filter('lafka_sanitize_background', 'lafka_sanitize_background');

function lafka_sanitize_background_repeat($value) {
	$recognized = lafka_recognized_background_repeat();
	if (array_key_exists($value, $recognized)) {
		return $value;
	}
	return apply_filters('lafka_default_background_repeat', current($recognized));
}

add_filter('lafka_background_repeat', 'lafka_sanitize_background_repeat');

function lafka_sanitize_background_position($value) {
	$recognized = lafka_recognized_background_position();
	if (array_key_exists($value, $recognized)) {
		return $value;
	}
	return apply_filters('lafka_default_background_position', current($recognized));
}

add_filter('lafka_background_position', 'lafka_sanitize_background_position');

function lafka_sanitize_background_attachment($value) {
	$recognized = lafka_recognized_background_attachment();
	if (array_key_exists($value, $recognized)) {
		return $value;
	}
	return apply_filters('lafka_default_background_attachment', current($recognized));
}

add_filter('lafka_background_attachment', 'lafka_sanitize_background_attachment');


/* Typography */

function lafka_sanitize_typography($input, $option) {

	$output = wp_parse_args($input, array(
			'size' => '',
			'face' => '',
			'style' => '',
			'color' => ''
	));

	if (isset($option['options']['faces']) && isset($input['face'])) {
		if (is_array($option['options']['faces']) && !( array_key_exists($input['face'], $option['options']['faces']) )) {
			$output['face'] = '';
		}
	} else {
		$output['face'] = apply_filters('lafka_font_face', $output['face']);
	}

	$output['size'] = apply_filters('lafka_font_size', $output['size']);
	$output['style'] = apply_filters('lafka_font_style', $output['style']);
	$output['color'] = apply_filters('lafka_sanitize_color', $output['color']);
	return $output;
}

add_filter('lafka_sanitize_typography', 'lafka_sanitize_typography', 10, 2);

function lafka_sanitize_font_size($value) {
	$recognized = lafka_recognized_font_sizes();
	$value_check = preg_replace('/px/', '', $value);
	if (in_array((int) $value_check, $recognized)) {
		return $value;
	}
	return apply_filters('lafka_default_font_size', $recognized);
}

add_filter('lafka_font_size', 'lafka_sanitize_font_size');

function lafka_sanitize_font_style($value) {
	$recognized = lafka_recognized_font_styles();
	if (array_key_exists($value, $recognized)) {
		return $value;
	}
	return $value;
}

add_filter('lafka_font_style', 'lafka_sanitize_font_style');

function lafka_sanitize_font_face($value) {
	$recognized = lafka_recognized_font_faces();
	if (array_key_exists($value, $recognized)) {
		return $value;
	}
	return apply_filters('lafka_default_font_face', current($recognized));
}

add_filter('lafka_font_face', 'lafka_sanitize_font_face');

/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
function lafka_recognized_background_repeat() {
	$default = array(
			'no-repeat' => esc_html__('No Repeat', 'lafka'),
			'repeat-x' => esc_html__('Repeat Horizontally', 'lafka'),
			'repeat-y' => esc_html__('Repeat Vertically', 'lafka'),
			'repeat' => esc_html__('Repeat All', 'lafka'),
	);
	return apply_filters('lafka_recognized_background_repeat', $default);
}

/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
function lafka_recognized_background_position() {
	$default = array(
			'top left' => esc_html__('Top Left', 'lafka'),
			'top center' => esc_html__('Top Center', 'lafka'),
			'top right' => esc_html__('Top Right', 'lafka'),
			'center left' => esc_html__('Middle Left', 'lafka'),
			'center center' => esc_html__('Middle Center', 'lafka'),
			'center right' => esc_html__('Middle Right', 'lafka'),
			'bottom left' => esc_html__('Bottom Left', 'lafka'),
			'bottom center' => esc_html__('Bottom Center', 'lafka'),
			'bottom right' => esc_html__('Bottom Right', 'lafka')
	);
	return apply_filters('lafka_recognized_background_position', $default);
}

/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
function lafka_recognized_background_attachment() {
	$default = array(
			'scroll' => esc_html__('Scroll Normally', 'lafka'),
			'fixed' => esc_html__('Fixed in Place', 'lafka')
	);
	return apply_filters('lafka_recognized_background_attachment', $default);
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */
function lafka_sanitize_hex($hex, $default = '') {
	if (lafka_validate_hex($hex)) {
		return $hex;
	}
	return $default;
}

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */
function lafka_recognized_font_sizes() {
	$sizes = range(9, 71);
	$sizes = apply_filters('lafka_recognized_font_sizes', $sizes);
	$sizes = array_map('absint', $sizes);
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function lafka_recognized_font_faces() {
	$default = array(
			'arial' => 'Arial',
			'verdana' => 'Verdana, Geneva',
			'trebuchet' => 'Trebuchet',
			'georgia' => 'Georgia',
			'times' => 'Times New Roman',
			'tahoma' => 'Tahoma, Geneva',
			'palatino' => 'Palatino',
			'helvetica' => 'Helvetica*'
	);
	return apply_filters('lafka_recognized_font_faces', $default);
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function lafka_recognized_font_styles() {
	$default = array(
			'normal' => esc_html__('Normal', 'lafka'),
			'italic' => esc_html__('Italic', 'lafka'),
			'bold' => esc_html__('Bold', 'lafka'),
			'bold italic' => esc_html__('Bold Italic', 'lafka')
	);
	return apply_filters('lafka_recognized_font_styles', $default);
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */
function lafka_validate_hex($hex) {
	$hex = trim($hex);
	/* Strip recognized prefixes. */
	if (0 === strpos($hex, '#')) {
		$hex = substr($hex, 1);
	} elseif (0 === strpos($hex, '%23')) {
		$hex = substr($hex, 3);
	}
	/* Regex match. */
	if (0 === preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
		return false;
	} else {
		return true;
	}
}
