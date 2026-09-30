<?php
/**
 * Site header: logo, search, social, primary navigation, optional ticker.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="mn-skip screen-reader-text" href="#mn-main"><?php esc_html_e( 'Lewati ke konten', 'm-nata' ); ?></a>

<header class="mn-header" id="top">
	<div class="mn-container mn-header__bar">
		<?php mnata_logo(); ?>

		<div class="mn-header__tools">
			<?php get_search_form(); ?>
			<?php mnata_social_links( 'mn-social mn-social--header' ); ?>
		</div>

		<button type="button" id="mn-toggle" class="mn-toggle" aria-controls="mn-nav" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'm-nata' ); ?></span>
			<?php mnata_icon( 'menu', 24 ); ?>
		</button>
	</div>

	<nav class="mn-nav" id="mn-nav" aria-label="<?php esc_attr_e( 'Menu utama', 'm-nata' ); ?>">
		<div class="mn-container">
			<?php get_search_form(); ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mn-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</nav>

	<?php mnata_ticker(); ?>
</header>

<?php mnata_area( 'header-banner', 'mn-container mn-ad-header' ); ?>
