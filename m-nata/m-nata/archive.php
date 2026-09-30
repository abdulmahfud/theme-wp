<?php
/**
 * Category, tag, author and date archives (they share one layout, see template-parts/archive.php).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/archive' );
get_footer();
