<?php
/**
 * Salmon & Cream WordPress Theme functions and definitions
 *
 * @package Salmon & Cream WordPress Theme
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 2000; /* pixels */

/**
 * Set a lower image compression
 */
function salmoncream_image_compression($arg) {
	return 70;
}
add_filter(' jpeg_quality', 'salmoncream_image_compression' );

if ( ! function_exists( 'salmoncream_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function salmoncream_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	require( get_template_directory() . '/inc/extras.php' );

	/**
	 * Portfolio custom post type
	 */
	require( get_template_directory() . '/inc/portfolio-post-type.php');

	/**
	 * Enable Custom-Meta-Boxes
	 */
	require( get_template_directory() . '/inc/Custom-Meta-Boxes/custom-meta-boxes.php' );

	/**
	 * Make theme available for translation
	 */
	load_theme_textdomain( 'salmoncream', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
    # add_theme_support( 'automatic-feed-links' );
    remove_action( 'wp_head', 'feed_links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Loads the Options Framework
	 */
	if ( !function_exists( 'optionsframework_init' ) ) {
		define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/options-framework/' );
		require_once dirname( __FILE__ ) . '/inc/options-framework/options-framework.php';
	}

	/*
	 * Add Theme Options
	 */
	require_once( get_template_directory() . '/inc/theme-options.php');

	/**
	 * Set the Custom Image Sizes
	 */
	add_image_size( 'salmoncream-square', 400, 400, true );
	add_image_size( 'salmoncream-square@2x', 800, 800, true );

	add_image_size( 'salmoncream-sticky', 600, 420, true );
	add_image_size( 'salmoncream-sticky@2x', 1200, 840, true );

	add_image_size( 'salmoncream-featured-small', 300, 210, true );
	add_image_size( 'salmoncream-featured-small@2x', 600, 420, true );

	add_image_size( 'salmoncream-featured', 1000, 700, true );
	add_image_size( 'salmoncream-featured@2x', 2000, 1400, true );

	/*
	 * Add Shortcodes
	 */
	require_once( get_template_directory() . '/inc/shortcodes.php' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Header Menu', 'salmoncream' ),
		'secondary' => __( 'Footer Menu', 'salmoncream' )
	) );

}
endif; // salmoncream_setup
add_action( 'after_setup_theme', 'salmoncream_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function salmoncream_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'salmoncream' ),
		'id'            => 'sidebar-blog',
		'before_widget' => '<aside id="%1$s" class="widget %2$s clearfix">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'salmoncream' ),
		'id'            => 'sidebar-page',
		'before_widget' => '<aside id="%1$s" class="widget %2$s clearfix">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'salmoncream_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function salmoncream_scripts() {
	wp_enqueue_style( 'salmoncream-googlefonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,800italic,300,600,800,400|Titan+One' );

	wp_enqueue_style( 'salmoncream-style', get_stylesheet_uri() );

	wp_enqueue_script( 'salmoncream-modernizr', get_template_directory_uri() . '/js/vendor/modernizr-2.6.2.min.js', false, '2.6.2', false );

	wp_enqueue_script( 'salmoncream-flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array( 'jquery' ), '2.1', true );

	wp_enqueue_script( 'salmoncream-main', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), '1.0', true );

	wp_localize_script( 'salmoncream-main', 'WPURLS', array( 'siteurl' => get_option('siteurl') ) );

	wp_enqueue_script( 'salmoncream-jquery-dropkick', get_template_directory_uri() . '/js/jquery.dropkick-1.0.0.js', array( 'jquery' ), '1.0.0', true );

	wp_enqueue_script( 'salmoncream-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Infinite Scroll

	$infinite_scroll = of_get_option( 'infinite_scroll' );

	if ( ( ( is_page_template( 'page-portfolio.php' ) || is_tax( 'skill' ) ) && $infinite_scroll['portfolio'] != 0 )  || ( is_home() && $infinite_scroll['blog'] != 0 ) ) {

		$post_type = ( is_page_template( 'page-portfolio.php' ) || is_tax( 'skill' ) ) ? 'portfolio' : 'post';

		$skill = '';

		if ( $post_type === 'portfolio' ) {

			$skill = get_query_var( 'skill' );

			$temp_query = new WP_Query( array( 'post_type' => $post_type, 'skill' => $skill ) );

		} elseif ( $post_type === 'post' ) {

			$temp_query = new WP_Query( array( 'post_type' => $post_type ) );

		}

		wp_enqueue_script( 'salmoncream-infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.js', array( 'jquery' ), '1.0', true );

		wp_localize_script( 'salmoncream-infinite-scroll', 'INFSCR', array( 'max_num_pages' => $temp_query->max_num_pages, 'wpurl' => get_bloginfo( 'wpurl' ), 'post_type' => $post_type , 'skill' => $skill ) );

	}
}
add_action( 'wp_enqueue_scripts', 'salmoncream_scripts' );

/**
 * Shorten excerpt length
 */
function salmoncream_excerpt_length($length) {
	if ( is_sticky() ) {
		$length = 50;
	} elseif ( is_archive() || is_search() ) {
		$length = 30;
	} else {
		$length = 20;
	}
	return $length;
}
add_filter('excerpt_length', 'salmoncream_excerpt_length', 999);

/**
 * Replace [...] in excerpts with something new
 */
function salmoncream_excerpt_more($more) {
	return '&hellip;';
}
add_filter('excerpt_more', 'salmoncream_excerpt_more');

/**
 * Only show posts in search
 */
function salmoncream_modified_searchresults($query) {
    if ($query->is_search) {
        $query->set( 'post_type', 'post' );
    }

    return $query;
}
add_filter('pre_get_posts','salmoncream_modified_searchresults');

/**
 * Custom gallery layout
 */
require( get_template_directory() . '/inc/gallery-layout.php');

/**
 * Add button CSS class to next/precious posts/comments links
 */
function salmoncream_add_btn_link_class() {
	return 'class="btn"';
}
add_filter('next_posts_link_attributes', 'salmoncream_add_btn_link_class');
add_filter('previous_posts_link_attributes', 'salmoncream_add_btn_link_class');
add_filter('next_comments_link_attributes', 'salmoncream_add_btn_link_class');
add_filter('previous_comments_link_attributes', 'salmoncream_add_btn_link_class');

/**
 * Infinite Scroll
 */
function salmoncream_infinitepaginate() {
	global $wp_query;

    $paged = $_POST['page_no'];
    $skill = $_POST['skill'];
    $post_type = $_POST['post_type'];

    if ( $post_type === 'portfolio' ) {

		# Load the portfolio items
		$wp_query = new WP_Query( array( 'post_type' => 'portfolio', 'paged' => $paged, 'skill' => $skill ) );
		get_template_part( 'content', 'portfolio' );

    } elseif ( $post_type === 'post' ) {

		# Load the posts
		$wp_query = new WP_Query( array( 'post_type' => 'post', 'paged' => $paged, 'post__not_in' => get_option('sticky_posts' ) ) );
		get_template_part( 'content', 'blog-tiles' );

    }

    exit;
}
add_action('wp_ajax_infinite_scroll', 'salmoncream_infinitepaginate');           // for logged in user
add_action('wp_ajax_nopriv_infinite_scroll', 'salmoncream_infinitepaginate');    // if user not logged in
