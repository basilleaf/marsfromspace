<?php
/**
 * Portfolio Post Type
 *
 * @package Salmon & Cream WordPress Theme
 */

/**
 * Registering the custom post type
 */
function salmoncream_portfolio() {
	// creating (registering) the custom type
	register_post_type( 'portfolio',
	 	// let's now add all the options for this post type
		array('labels' => array(
				'name' => __('Portfolio', 'salmoncream'), /* This is the Title of the Group */
				'singular_name' => __('Item', 'salmoncream'), /* This is the individual type */
				'all_items' => __('All Items', 'salmoncream'), /* the all items menu item */
				'add_new' => __('Add New', 'salmoncream'), /* The add new menu item */
				'add_new_item' => __('Add New Item', 'salmoncream'), /* Add New Display Title */
				'edit' => __( 'Edit', 'salmoncream' ), /* Edit Dialog */
				'edit_item' => __('Edit Item', 'salmoncream'), /* Edit Display Title */
				'new_item' => __('New Item', 'salmoncream'), /* New Display Title */
				'view_item' => __('View Item', 'salmoncream'), /* View Display Title */
				'search_items' => __('Search Items', 'salmoncream'), /* Search Custom Type Title */
				'not_found' =>  __('Nothing found in the Database.', 'salmoncream'), /* This displays if there are no entries yet */
				'not_found_in_trash' => __('Nothing found in Trash', 'salmoncream'), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 20,
			'rewrite'	=> array( 'slug' => 'portfolio', 'with_front' => false ),
			'has_archive' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array( 'title', 'thumbnail', 'revisions', 'comments')
	 	) /* end of options */
	); /* end of register post type */

	/* this adds your post categories to your custom post type */
	register_taxonomy_for_object_type('skill', 'portfolio');

}
add_action( 'init', 'salmoncream_portfolio');

/**
 * Registering the custom category 'skills'
 */
register_taxonomy( 'skill',
	array('portfolio'),
	array('hierarchical' => false,
		'labels' => array(
			'name' => __( 'Skills', 'salmoncream' ), /* name of the custom taxonomy */
			'singular_name' => __( 'Skill', 'salmoncream' ), /* single taxonomy name */
			'search_items' =>  __( 'Search Skills', 'salmoncream' ), /* search title for taxomony */
			'all_items' => __( 'All Skills', 'salmoncream' ), /* all title for taxonomies */
			'parent_item' => __( 'Parent Skill', 'salmoncream' ), /* parent title for taxonomy */
			'parent_item_colon' => __( 'Parent Skill:', 'salmoncream' ), /* parent taxonomy title */
			'edit_item' => __( 'Edit Skill', 'salmoncream' ), /* edit custom taxonomy title */
			'update_item' => __( 'Update Skill', 'salmoncream' ), /* update title for taxonomy */
			'add_new_item' => __( 'Add Skill', 'salmoncream' ), /* add new title for taxonomy */
			'new_item_name' => __( 'New Skill', 'salmoncream' ) /* name title for taxonomy */
		),
		'show_admin_column' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'skill' ),
	)
);

/**
 * Adding the meta box for featured skill, description and Item url
 */
function salmoncream_portfolio_meta_box( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_salmoncream_portfolio_';

	$meta_boxes[] = array(
		'id'         => 'salmoncream_portfolio_meta_box',
		'title'      => __( 'Portfolio Item Details', 'salmoncream' ),
		'pages'      => 'portfolio',
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => array(
			array(
				'name'    => __( 'Featured Skill', 'salmoncream'),
				'id'      => $prefix . 'featured_skill',
				'desc'	  => __( 'This will be displayed on the portfolio page below the project title.', 'salmoncream' ),
				'type'    => 'text'
			),
			array(
				'name'    => __( 'Description', 'salmoncream'),
				'id'      => $prefix . 'description',
				'dec'	  => __( 'Project informations.', 'salmoncream' ),
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 15, 'media_buttons' => false ),
			),
			array(
				'name'    => __( 'Link', 'salmoncream'),
				'id'      => $prefix . 'link',
				'desc'	  => __( 'A link to the projects website. www.yoursite.com', 'salmoncream' ),
				'type'    => 'text'
			),
			array(
				'name'    => __( 'Media', 'salmoncream'),
				'id'      => $prefix . 'media',
				'desc'	  => __( 'Use this box for images, videos etc.', 'salmoncream' ),
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 20 ),
			)
		)
	);

	return $meta_boxes;
}

add_filter( 'cmb_meta_boxes', 'salmoncream_portfolio_meta_box' );