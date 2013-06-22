<?php
/**
 * Alternative layout for gallery shortcut
 *
 * @package Salmon & Cream WordPress Theme
 */

/**
 * Output a flexslider slideshow
 */
function salmoncream_modified_post_gallery( $output, $attr )
{

	global $post;

    static $instance = 0;
    $instance++;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'size'       => 'salmoncream-featured',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $selector = "gallery-{$instance}";

    $size_class = sanitize_html_class( $size );

    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-size-{$size_class} flexslider'>";

    $output = apply_filters( 'gallery_style', $gallery_div );

    $output .= '<ul class="slides">';

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {

    	$caption = ( !empty( $attachment->post_excerpt ) ) ? '<p class="flex-caption wp-caption-text gallery-caption">' . wptexturize( $attachment->post_excerpt ) . '</p>' : '';

        $image = wp_get_attachment_image( $id, $size );

        $output .= '<li>';
        $output .= $image;
        $output .= $caption;
        $output .= '</li>';

    }

    $output .= "
            </ul><!-- .slides -->
        </div>\n";

    return $output;
}
add_filter( 'post_gallery', 'salmoncream_modified_post_gallery', 10, 2);