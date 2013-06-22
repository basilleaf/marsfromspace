<?php
/**
 * Template for displaying portfolio items by skill
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content tiles-grid clearfix" role="main">

		<?php $paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;

		$skill = get_query_var( 'skill' );

		$args = array(
			'post_type' => 'portfolio',
		    'paged' => $paged,
		    'skill' => $skill
		);

		$wp_query = new WP_Query( $args ); ?>

		<?php get_template_part( 'content', 'portfolio' ); ?>

	</div><!-- #content -->

	<?php if ( $wp_query->max_num_pages > 1) : ?>

		<?php $infinite_scroll = of_get_option( 'infinite_scroll' ); ?>

		<?php if ( $infinite_scroll['portfolio'] != 0 ) : ?>

			<div id="infinite-loader"></div><!-- #infinite-loader -->

		<?php else : ?>

			<nav role="navigation" id="nav-below" class="navigation-paging">
				<h1 class="screen-reader-text"><?php _e( 'Portfolio navigation', 'salmoncream' ); ?></h1>

				<?php if ( get_next_posts_link() ) : ?>
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older', 'salmoncream' ) ); ?></div>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer <span class="meta-nav">&rarr;</span>', 'salmoncream' ) ); ?></div>
				<?php endif; ?>
			</nav><!-- #nav-below -->

		<?php endif; ?>

	<?php endif; ?>

<?php get_footer(); ?>