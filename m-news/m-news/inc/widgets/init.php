<?php
/**
 * Load and register the M-News widgets.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

require_once MNEWS_DIR . '/inc/widgets/abstract-widget.php';
require_once MNEWS_DIR . '/inc/widgets/post-list.php';
require_once MNEWS_DIR . '/inc/widgets/slider.php';
require_once MNEWS_DIR . '/inc/widgets/category-block.php';
require_once MNEWS_DIR . '/inc/widgets/banner-ad.php';
require_once MNEWS_DIR . '/inc/widgets/social-follow.php';
require_once MNEWS_DIR . '/inc/widgets/tag-cloud.php';
require_once MNEWS_DIR . '/inc/widgets/html-embed.php';
require_once MNEWS_DIR . '/inc/widgets/weather.php';
require_once MNEWS_DIR . '/inc/widgets/earthquake.php';
require_once MNEWS_DIR . '/inc/widgets/visitors.php';
require_once MNEWS_DIR . '/inc/widgets/polling.php';

/**
 * Register widgets.
 */
function mnews_register_widgets() {
	register_widget( 'MNews_Widget_Post_List' );
	register_widget( 'MNews_Widget_Slider' );
	register_widget( 'MNews_Widget_Category_Block' );
	register_widget( 'MNews_Widget_Banner_Ad' );
	register_widget( 'MNews_Widget_Social_Follow' );
	register_widget( 'MNews_Widget_Tag_Cloud' );
	register_widget( 'MNews_Widget_Html_Embed' );
	register_widget( 'MNews_Widget_Weather' );
	register_widget( 'MNews_Widget_Earthquake' );
	register_widget( 'MNews_Widget_Visitors' );
	register_widget( 'MNews_Widget_Polling' );
}
add_action( 'widgets_init', 'mnews_register_widgets' );
