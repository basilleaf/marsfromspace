<?php
/**
 * The main template file.
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content tiles-grid clearfix" role="main">

		<?php

		$sticky_posts = get_option('sticky_posts');

		if ( !is_paged() && !empty( $sticky_posts ) ) :

			/* Get all the sticky posts */

			$args = array(
			    'posts_per_page' => -1,
			    'post__in' => get_option('sticky_posts')
			);

			$sticky_query = new WP_Query( $args ); ?>

			<?php if ( $sticky_query->have_posts() ) : ?>

			<div class="flexslider sticky-posts clearfix">

				<ul class="slides">

				<?php /* Start the Loop */ ?>
				<?php while ( $sticky_query->have_posts() ) : $sticky_query->the_post(); ?>

					<li>

						<?php get_template_part( 'content', 'blog-stickies' ); ?>

					</li>

				<?php endwhile; ?>

				</ul><!-- .slides -->

			</div><!-- .flexslider .sticky-posts -->

			<?php endif; ?>

		<?php endif; ?>

		<?php /* Get all the other posts without stickies */

		$args = array(
		    'posts_per_page' => get_option('posts_per_page'),
		    'paged' => get_query_var('paged'),
		    'post__not_in' => get_option('sticky_posts')
		);

		$wp_query = new WP_Query( $args ); ?>

		<?php get_template_part( 'content', 'blog-tiles' ); ?>

	</div><!-- #content -->

	<?php $infinite_scroll = of_get_option( 'infinite_scroll' ); ?>

	<?php if ( $infinite_scroll['blog'] != 0 && $wp_query->max_num_pages > 1 ) : ?>

		<div id="infinite-loader"></div><!-- #infinite-loader -->

	<?php else : ?>

		<?php salmoncream_content_nav( 'nav-below' ); ?>

	<?php endif; ?>

<?php get_footer(); ?>