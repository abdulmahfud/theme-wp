<?php
/**
 * Search form. No ids so it can appear twice (header + mobile menu).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="mnw-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Cari berita…', 'm-news' ); ?>" aria-label="<?php esc_attr_e( 'Cari berita', 'm-news' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'Cari', 'm-news' ); ?>"><?php mnews_icon( 'search', 18 ); ?></button>
</form>
