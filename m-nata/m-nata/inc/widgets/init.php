<?php
/**
 * Load and register the M-Nata widgets.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

require_once MNATA_DIR . '/inc/widgets/abstract-widget.php';
require_once MNATA_DIR . '/inc/widgets/post-list.php';
require_once MNATA_DIR . '/inc/widgets/slider.php';
require_once MNATA_DIR . '/inc/widgets/category-block.php';
require_once MNATA_DIR . '/inc/widgets/banner-ad.php';
require_once MNATA_DIR . '/inc/widgets/social-follow.php';
require_once MNATA_DIR . '/inc/widgets/tag-cloud.php';
require_once MNATA_DIR . '/inc/widgets/html-embed.php';
require_once MNATA_DIR . '/inc/widgets/weather.php';
require_once MNATA_DIR . '/inc/widgets/earthquake.php';
require_once MNATA_DIR . '/inc/widgets/visitors.php';

/**
 * Register widgets.
 */
function mnata_register_widgets() {
	register_widget( 'MNata_Widget_Post_List' );
	register_widget( 'MNata_Widget_Slider' );
	register_widget( 'MNata_Widget_Category_Block' );
	register_widget( 'MNata_Widget_Banner_Ad' );
	register_widget( 'MNata_Widget_Social_Follow' );
	register_widget( 'MNata_Widget_Tag_Cloud' );
	register_widget( 'MNata_Widget_Html_Embed' );
	register_widget( 'MNata_Widget_Weather' );
	register_widget( 'MNata_Widget_Earthquake' );
	register_widget( 'MNata_Widget_Visitors' );
}
add_action( 'widgets_init', 'mnata_register_widgets' );
