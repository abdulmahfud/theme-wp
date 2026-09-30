<?php
/**
 * Site footer: brand column + widget/menu columns (left-aligned, plain background — see CLAUDE.md "Footer"),
 * then a separate full-width copyright bar.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

$mnews_address = get_theme_mod( 'mnews_footer_address', '' );
?>
<footer class="mnw-footer">
	<div class="mnw-container mnw-footer__top">
		<div class="mnw-footer__brand">
			<?php mnews_logo( false ); ?>
			<?php if ( $mnews_address ) : ?>
				<div class="mnw-footer__address"><?php echo wp_kses_post( wpautop( $mnews_address ) ); ?></div>
			<?php endif; ?>
			<?php mnews_social_links( 'mnw-social mnw-social--footer' ); ?>
		</div>

		<?php mnews_footer_widgets(); ?>

		<?php if ( has_nav_menu( 'network' ) ) : ?>
			<div class="mnw-footer__col">
				<p class="mnw-footer__heading"><?php echo esc_html( get_theme_mod( 'mnews_network_title', __( 'MEDIA NETWORK', 'm-news' ) ) ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'network',
						'container'      => false,
						'menu_class'     => 'mnw-footer-menu mnw-footer-menu--col',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<div class="mnw-footer__col">
				<p class="mnw-footer__heading"><?php esc_html_e( 'Tautan', 'm-news' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'mnw-footer-menu mnw-footer-menu--col',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<div class="mnw-container mnw-footer__bottom">
		<p class="mnw-footer__copy"><?php echo esc_html( mnews_copyright_text() ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
