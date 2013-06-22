<?php
/**
 * The Page Sidebar.
 *
 * @package Salmon & Cream WordPress Theme
 */
?>
	<div id="sidebar" class="content-left widget-area" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>

		<?php if ( ! dynamic_sidebar( 'sidebar-page' ) ) : ?>

			<p><?php _e( 'Fill in some content', 'salmoncream' ) ?></p>

		<?php endif; // end sidebar widget area ?>

	</div><!-- #sidebar -->
