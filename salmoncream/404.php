<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content">

		<div class="content-right" role="main">

			<article id="post-0" class="post error404 not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'salmoncream' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'salmoncream' ); ?></p>

					<?php get_search_form(); ?>

				</div><!-- .entry-content -->
			</article><!-- #post-0 .post .error404 .not-found -->

		</div><!-- .content-right -->

		<div class="content-left widget-area">

			<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

			<?php if ( salmoncream_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
			<div class="widget widget_categories">
				<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'salmoncream' ); ?></h2>
				<ul>
				<?php
					wp_list_categories( array(
						'orderby'    => 'count',
						'order'      => 'DESC',
						'show_count' => 1,
						'title_li'   => '',
						'number'     => 10,
					) );
				?>
				</ul>
			</div>
			<?php endif; ?>

			<?php the_widget( 'WP_Widget_Archives', 'dropdown=1' ); ?>

			<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

		</div><!-- .content-left .widget-area -->

	</div><!-- #content -->

<?php get_footer(); ?>