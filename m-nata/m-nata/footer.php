<?php
/**
 * Site footer.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

$mnata_address   = get_theme_mod( 'mnata_footer_address', '' );
$mnata_copyright = get_theme_mod( 'mnata_copyright', '' );
if ( '' === $mnata_copyright ) {
	/* translators: 1: year, 2: site name. */
	$mnata_copyright = sprintf( __( 'Copyright © %1$s %2$s - All Rights Reserved', 'm-nata' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}
?>
<footer class="mn-footer">
	<div class="mn-container">
		<?php mnata_footer_widgets(); ?>

		<?php mnata_logo( false ); ?>

		<?php if ( $mnata_address ) : ?>
			<div class="mn-footer__address"><?php echo wp_kses_post( wpautop( $mnata_address ) ); ?></div>
		<?php endif; ?>

		<?php mnata_social_links( 'mn-social mn-social--footer' ); ?>

		<?php if ( has_nav_menu( 'network' ) ) : ?>
			<div class="mn-footer__network">
				<p class="mn-footer__heading"><?php echo esc_html( get_theme_mod( 'mnata_network_title', __( 'MEDIA NETWORK', 'm-nata' ) ) ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'network',
						'container'      => false,
						'menu_class'     => 'mn-footer-menu mn-footer-menu--network',
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
				'menu_class'     => 'mn-footer-menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>

		<p class="mn-footer__copy"><?php echo esc_html( $mnata_copyright ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
