<?php
/**
 * Site footer.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

$mnews_address   = get_theme_mod( 'mnews_footer_address', '' );
$mnews_copyright = get_theme_mod( 'mnews_copyright', '' );
if ( '' === $mnews_copyright ) {
	/* translators: 1: year, 2: site name. */
	$mnews_copyright = sprintf( __( 'Copyright © %1$s %2$s - All Rights Reserved', 'm-news' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
?>
<footer class="mnw-footer">
	<div class="mnw-container">
		<?php mnews_footer_widgets(); ?>

		<?php mnews_logo( false ); ?>

		<?php if ( $mnews_address ) : ?>
			<div class="mnw-footer__address"><?php echo wp_kses_post( wpautop( $mnews_address ) ); ?></div>
		<?php endif; ?>

		<?php mnews_social_links( 'mnw-social mnw-social--footer' ); ?>

		<?php if ( has_nav_menu( 'network' ) ) : ?>
			<div class="mnw-footer__network">
				<p class="mnw-footer__heading"><?php echo esc_html( get_theme_mod( 'mnews_network_title', __( 'MEDIA NETWORK', 'm-news' ) ) ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'network',
						'container'      => false,
						'menu_class'     => 'mnw-footer-menu mnw-footer-menu--network',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'mnw-footer-menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>

		<p class="mnw-footer__copy"><?php echo esc_html( $mnews_copyright ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
