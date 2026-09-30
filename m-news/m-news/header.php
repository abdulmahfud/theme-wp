<?php
/**
 * Site header: logo, search, social, primary navigation, optional ticker.
 *
 * @package M_News
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
<a class="mnw-skip screen-reader-text" href="#mnw-main"><?php esc_html_e( 'Lewati ke konten', 'm-news' ); ?></a>

<header class="mnw-header" id="top">
	<div class="mnw-container mnw-header__bar">
		<?php mnews_logo(); ?>

		<div class="mnw-header__tools">
			<?php get_search_form(); ?>
			<?php mnews_social_links( 'mnw-social mnw-social--header' ); ?>
		</div>

		<button type="button" id="mnw-toggle" class="mnw-toggle" aria-controls="mnw-nav" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'm-news' ); ?></span>
			<?php mnews_icon( 'menu', 24 ); ?>
		</button>
	</div>

	<nav class="mnw-nav" id="mnw-nav" aria-label="<?php esc_attr_e( 'Menu utama', 'm-news' ); ?>">
		<div class="mnw-container">
			<?php get_search_form(); ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mnw-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</nav>

	<?php
	// Same menu again, flattened to top level, as an always-visible horizontal scroll strip on mobile (the
	// panel above stays tap-to-open there, this is just the quick-access row from the layout reference).
	wp_nav_menu(
		array(
			'theme_location'       => 'primary',
			'container'            => 'nav',
			'container_class'      => 'mnw-menu-strip',
			'container_aria_label' => __( 'Menu cepat', 'm-news' ),
			'menu_class'           => 'mnw-menu',
			'depth'                => 1,
			'fallback_cb'          => false,
		)
	);
	?>

	<?php mnews_ticker(); ?>
</header>

<?php mnews_area( 'header-banner', 'mnw-container mnw-ad-header' ); ?>
