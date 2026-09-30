<?php
/**
 * Fallback template (also the posts page when a static front page is set).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/archive' );
get_footer();
