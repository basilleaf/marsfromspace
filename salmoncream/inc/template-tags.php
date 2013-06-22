<?php
/**
 * Custom template tags for this theme.
 *
 * @package Salmon & Cream WordPress Theme
 */

if ( ! function_exists( 'salmoncream_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function salmoncream_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'navigation-post' : 'navigation-paging';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'salmoncream' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'salmoncream' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'salmoncream' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'salmoncream' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'salmoncream' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // salmoncream_content_nav

if ( ! function_exists( 'salmoncream_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function salmoncream_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'salmoncream' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'salmoncream' ), '<span class="edit-link">', '<span>' ); ?></p>
	<?php
			break;
		default :
	?>

	<?php $admincomment = ( user_can( $comment->user_id, 'manage_options' ) ? 'byadmin' : '' ); ?>

	<li <?php comment_class( $admincomment ) ?> id="li-comment-<?php comment_ID(); ?>">

		<article id="comment-<?php comment_ID(); ?>" class="comment">

			<figure class="comment-author-avatar">
				<?php echo get_avatar( $comment, 240 ); ?>
			</figure>

			<div class="comment-box">

				<div class="comment-author-vcard">

					<?php if ( user_can( $comment->user_id, 'manage_options' ) ) : ?>

						<?php _e( 'Admin - ', 'salmoncream' ); ?>

					<?php endif; ?>

					<?php comment_author_link(); ?>

				</div><!-- .comment-author-vcard -->

				<div class="comment-meta commentmetadata">
					<time datetime="<?php comment_time( 'c' ); ?>">
					<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'salmoncream' ), get_comment_date(), get_comment_time() ); ?>
					</time>
					<?php
						comment_reply_link( array_merge( $args,array(
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before' => ' &middot; '
						) ) );
					?>
					<?php edit_comment_link( __( 'Edit', 'salmoncream' ), '<span class="edit-link">', '<span>' ); ?>
				</div><!-- .comment-meta .commentmetadata -->

				<div class="comment-content">

					<?php if ( $comment->comment_approved == '0' ) : ?>
						<em><?php _e( 'Your comment is awaiting moderation.', 'salmoncream' ); ?></em>
						<br />
					<?php endif; ?>

					<?php comment_text(); ?>

				</div><!-- .comment-content -->

			</div><!-- .comment-box -->

		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for salmoncream_comment()

if ( ! function_exists( 'salmoncream_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function salmoncream_posted_on() {
	printf( __( '<span class="byline">By <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span></span> on <time class="entry-date" datetime="%4$s">%5$s</time>', 'salmoncream' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'salmoncream' ), get_the_author() ) ),
		get_the_author(),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}
endif;
/**
 * Returns true if a blog has more than 1 category
 */
function salmoncream_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so salmoncream_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so salmoncream_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in salmoncream_categorized_blog
 */
function salmoncream_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'salmoncream_category_transient_flusher' );
add_action( 'save_post', 'salmoncream_category_transient_flusher' );

/**
 * Display logo or blog name
 */
function salmoncream_display_logo() {

	$logo = of_get_option('logo_upload');

	if ( $logo )  {

		$size = getimagesize(of_get_option('logo_upload'));

		global $wpdb;
		$logo_query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$logo'";
		$logo_id = $wpdb->get_var($logo_query);

		$logo_array = wp_get_attachment_image_src( $logo_id, 'full' );

		$logo_src = $logo_array[0];
		$logo_width = $logo_array[1];
		$logo_height = $logo_array[2];

		if ( of_get_option('logo_hidpi') ) {

			$logo_width = round($logo_width / 2);
			$logo_height = round($logo_height / 2);

		}

		echo '<img src="' . $logo_src . '" alt="' . get_bloginfo( 'name' ) . '" width="' . $logo_width. '" height="' .  $logo_height . '" />';

	} else {

		echo get_bloginfo( 'name' );

	}
}