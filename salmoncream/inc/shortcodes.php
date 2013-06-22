<?php
/**
 * Shortcodes
 *
 * @package Salmon & Cream WordPress Theme
 */

/**
 * Buttons
 */
function salmoncream_button( $atts, $content = null ){

	extract( shortcode_atts( array(
		'href' => '#',
		'target' => '_self',
		'size' => '',
		'color' => ''
		), $atts )
	);

	return '<a target="' . $target . '" href="' . esc_attr($href) . '" class="btn ' . $size . ' ' . $color . '">' . do_shortcode($content) . '</a>';

}
add_shortcode( 'button', 'salmoncream_button' );


/**
 * Alerts
 */
function salmoncream_alert( $atts, $content = null ){

	extract( shortcode_atts( array(
		'color' => ''
		), $atts )
	);

	return '<div class="alert ' . $color . '">' . do_shortcode($content) . '</div>';

}
add_shortcode( 'alert', 'salmoncream_alert' );


/**
 * Columns
 */

// one half
function salmoncream_one_half( $atts, $content = null ){

	return '<div class="column one-half">' . do_shortcode($content) . '</div>';

}
add_shortcode( 'one_half', 'salmoncream_one_half' );

// one half last
function salmoncream_one_half_last( $atts, $content = null ){

	return '<div class="column one-half column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';

}
add_shortcode( 'one_half_last', 'salmoncream_one_half_last' );

// one third
function salmoncream_one_third( $atts, $content = null ){

	return '<div class="column one-third">' . do_shortcode($content) . '</div>';

}
add_shortcode( 'one_third', 'salmoncream_one_third' );

// one third last
function salmoncream_one_third_last( $atts, $content = null ){

	return '<div class="column one-third column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';

}
add_shortcode( 'one_third_last', 'salmoncream_one_third_last' );

// one fourth
function salmoncream_one_fourth( $atts, $content = null ){

	return '<div class="column one-fourth">' . do_shortcode($content) . '</div>';

}
add_shortcode( 'one_fourth', 'salmoncream_one_fourth' );

// one fourth last
function salmoncream_one_fourth_last( $atts, $content = null ){

	return '<div class="column one-fourth column-last">' . do_shortcode($content) . '</div><div class="clear"></div>';

}
add_shortcode( 'one_fourth_last', 'salmoncream_one_fourth_last' );
