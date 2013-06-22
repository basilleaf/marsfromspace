<?php
/**
 * Theme Options
 *
 * @package Salmon & Cream WordPress Theme
 */

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */
function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);

}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 */
function optionsframework_options() {

	// Infinite Scroll Options
	$infinite_scroll_options = array(
		'portfolio' => __('Portfolio', 'salmoncream'),
		'blog' => __('Blog', 'salmoncream')
	);

	// Infinite Scroll Defaults
	$infinite_scroll_defaults = array(
		'portfolio' => '0',
		'blog' => '0'
	);

	// Color Scheme Options
	$color_scheme_options = array(
		'default' => __('Default', 'salmoncream'),
		'blue' => __('Bahama Blue', 'salmoncream'),
		'gray' => __('Concrete Gray', 'salmoncream')
	);

	$options = array();

	$options[] = array(
		'name' => __('General Settings', 'salmoncream'),
		'type' => 'heading'
	);

	$options['logo_hidpi'] = array(
		'name' => __('HiDPI Logo', 'salmoncream'),
		'desc' => __('Check this box if you want to display your logo ready for Retina and other HiDPI Displays. For example: If your image file is 500x144px it will be displayed as 250x72px and super sharp.', 'salmoncream'),
		'id' => 'logo_hidpi',
		'std' => '0',
		'type' => 'checkbox'
	);

	$options['logo_upload'] = array(
		'name' => __('Logo Upload', 'salmoncream'),
		'desc' => __('Upload a logo for your website. Maximun height of 72px is recommended.', 'salmoncream'),
		'id' => 'logo_upload',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __('Favicon Upload', 'salmoncream'),
		'desc' => __('Upload a 16x16px or 32x32px (for HiDPI Displays) PNG/GIF image for the favicon of your website.', 'salmoncream'),
		'id' => 'favicon_upload',
		'type' => 'upload'
	);

	$options['infinite_scroll'] = array(
		'name' => __('Infinite Scroll', 'salmoncream'),
		'desc' => __('Check these boxes if you want to enable Infinite Scroll Feature.', 'salmoncream'),
		'id' => 'infinite_scroll',
		'std' => $infinite_scroll_defaults,
		'type' => 'multicheck',
		'options' => $infinite_scroll_options
	);

	$options[] = array(
		'name' => __('Contact Form Email Address', 'salmoncream'),
		'desc' => __('Enter the email address to receive emails from the contact form. Leave blank to use admin email.', 'salmoncream'),
		'id' => 'contact_email',
		'type' => 'text'
	);

	$options['footer_text'] = array(
		'name' => __('Footer Text', 'salmoncream'),
		'desc' => __('Insert some text to display on the left side of the footer.', 'salmoncream'),
		'id' => 'footer_text',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __('Style Options', 'salmoncream'),
		'type' => 'heading'
	);

	$options['color_scheme'] = array(
		'name' => __('Color Scheme', 'salmoncream'),
		'desc' => __('Check one of the Boxes to switch the style of the Theme.', 'salmoncream'),
		'id' => 'color_scheme',
		'std' => 'default',
		'type' => 'radio',
		'options' => $color_scheme_options
	);

	$options['custom_css'] = array(
		'name' => __('Custom CSS', 'salmoncream'),
		'desc' => __('Add some CSS to your theme.', 'salmoncream'),
		'id' => 'custom_css',
		'type' => 'textarea'
	);

	return $options;
}

/**
 * Front End Customizer
 */
function salmoncream_register_customizer($wp_customize) {

	$options = optionsframework_options();

	/* Change the Site Title Section */

	$wp_customize->remove_control('blogdescription');

	$wp_customize->add_section( 'title_tagline' , array(
		'title'		=> __('Site Title', 'salmoncream'),
		'priority'	=> 20,
	));

	/* General Settings */

	$wp_customize->add_section( 'salmoncream_general_settings', array(
		'title' => __( 'General Settings', 'salmoncream' ),
		'priority' => 130
	) );

	$wp_customize->add_setting( 'salmoncream[logo_hidpi]', array(
		'default' => $options['logo_hidpi']['std'],
		'type' => 'option'
	) );

	$wp_customize->add_control( 'salmoncream_logo_hidpi', array(
		'label' => $options['logo_hidpi']['name'],
		'section' => 'salmoncream_general_settings',
		'settings' => 'salmoncream[logo_hidpi]',
		'type' => $options['logo_hidpi']['type']
	) );

	$wp_customize->add_setting( 'salmoncream[logo_upload]', array(
		'type' => 'option'
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo_upload', array(
		'label' => $options['logo_upload']['name'],
		'section' => 'salmoncream_general_settings',
		'settings' => 'salmoncream[logo_upload]'
	) ) );

	$wp_customize->add_setting( 'salmoncream[footer_text]', array(
		'type' => 'option'
	) );

	$wp_customize->add_control( 'salmoncream[footer_text]', array(
        'label' => $options['footer_text']['name'],
        'section' => 'salmoncream_general_settings',
        'settings' => 'salmoncream[footer_text]',
        'type' => 'text'
    ) );

	$wp_customize->add_section( 'salmoncream_color_schemes', array(
		'title' => __( 'Color Schemes', 'salmoncream' ),
		'priority' => 140
	) );

	$wp_customize->add_setting( 'salmoncream[color_scheme]', array(
		'default' => $options['color_scheme']['std'],
		'type' => 'option'
	) );

	$wp_customize->add_control( 'salmoncream_color_scheme', array(
		'label' => $options['color_scheme']['name'],
		'section' => 'salmoncream_color_schemes',
		'settings' => 'salmoncream[color_scheme]',
		'type' => $options['color_scheme']['type'],
		'choices' => $options['color_scheme']['options']
	) );

}
add_action( 'customize_register', 'salmoncream_register_customizer' );

/**
 * Output the favicon
 */
function salmoncream_favicon() {
	$favicon = of_get_option('favicon_upload');
    if ( $favicon ) {
        echo '<link rel="shortcut icon" href="'. $favicon .'" />' . "\n";
    }
}
add_action( 'wp_head', 'salmoncream_favicon' );

/**
 * Output the custom css
 */
function salmoncream_custom_css() {
	$custom_css = of_get_option('custom_css');
    if ( $custom_css ) {
        echo '<style type="text/css">' . "\n" . $custom_css . "\n" . '</style>' . "\n";
    }
}
add_action( 'wp_head', 'salmoncream_custom_css' );

/**
 * Output the custom css
 */
function salmoncream_color_scheme() {
	$color_scheme = of_get_option('color_scheme');
    if ( $color_scheme && $color_scheme != 'default' ) {

		wp_dequeue_style( 'salmoncream-style' );

		wp_enqueue_style( 'salmoncream-style-' . $color_scheme, get_template_directory_uri() . '/style-' . $color_scheme . '.css' );

    }
}
add_action( 'wp_enqueue_scripts', 'salmoncream_color_scheme' );