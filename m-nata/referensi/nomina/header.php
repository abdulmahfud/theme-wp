<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <link href="http://gmpg.org/xfn/11" rel="profile">
    <link href="<?php bloginfo( 'pingback_url' ); ?>" rel="pingback">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <?php wp_head(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="<?php if (get_theme_mod('color_scheme_setting') == 'Gradasi Biru') { echo '#21409A'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Default') { echo '#1B5DAF'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Merah Orange') { echo '#c72026'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Hijau') { echo '#009076'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Ungu') { echo '#562b77'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Merah Ungu') { echo '#e6000b'; } else if(get_theme_mod('color_scheme_setting') == 'Gradasi Biru Ungu') { echo '#4ac2ed'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Orange') { echo '#ff6600'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Hitam') { echo '#000000'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Maroon') { echo '#990000'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Biru 2') { echo '#161f6d'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Orange Merah Biru') { echo '#fdbb2d'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Biru Gelap') { echo '#141E30'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Hijau Kuning') { echo '#3CA55C'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Hijau Tua') { echo '#0f9b0f'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Biru Ungu Merah') { echo '#12c2e9'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Biru 3') { echo '#0575E6'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Merah Hitam') { echo '#ff0000'; } else if (get_theme_mod('color_scheme_setting') == 'Gradasi Coklat') { echo '#4b0b0d'; } else if (get_theme_mod('color_scheme_setting') == 'Putih') { echo '#ffffff'; } else if (get_theme_mod('color_scheme_setting') == 'Biru Dongker') { echo '#131373'; } else if (get_theme_mod('color_scheme_setting') == 'Hijau') { echo '#017230'; } else if (get_theme_mod('color_scheme_setting') == 'Merah Abu Abu') { echo '#ff0000'; } else if (get_theme_mod('color_scheme_setting') == 'Biru Ungu Merah 2') { echo '#21409a'; } else if (get_theme_mod('color_scheme_setting') == 'Biru Hitam') { echo '#4366C4'; } else if (get_theme_mod('color_scheme_setting') == 'Hijau Abu Abu') { echo '#009788'; } else if (get_theme_mod('color_scheme_setting') == 'Hitam Pekat') { echo '#000000'; } else if (get_theme_mod('color_scheme_setting') == 'Hijau Abu Abu') { echo '#009788'; } else if (get_theme_mod('color_scheme_setting') == 'Hitam Pekat') { echo '#000000'; } else if (get_theme_mod('color_scheme_setting') == 'Teal Orange') { echo '#02aebc'; } else { echo '#1B5DAF'; } ?>" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<style>
	
		.search-submit { background-image: url('<?php echo esc_url(get_template_directory_uri()); ?>/img/icons8-search.svg'); background-repeat: no-repeat; background-position: 50% 50%; background-size: 40%; }
		
		<?php if (get_theme_mod('activate_breaking_news_setting') == 'aktif') { ?>
		#sidebar-right, #sidebar-single {top: 170px;}
		<?php }; ?>

		<?php if ( is_admin_bar_showing() ) { ?>
	.logged-in header {
            top: 32px;
        }
			@media screen and (max-width: 450px) {
			.logged-in header {
            top: 0;
        }
				.logged-in .duaa {
					top: 66px;
				}
				
				
			}
			
<?php } else { ?>
        .logged-in header{
            top: 0 !important;
        }
<?php }; ?>
		
	</style>
	
</head>

<body <?php body_class(); ?>>
	  <?php wp_body_open(); ?>
	<?php if (get_theme_mod('activate_banner_header_parallax_setting') == 'aktif') {
		get_sidebar('banner-mobile-top-header-parallax'); 
	} ?>
	<?php get_sidebar('banner-bawah'); ?>
	
    <header>
		<div class="header-fixed">
			<div class="header-shrink">
				
			
 <?php
function theme_prefix_the_custom_logo()
{
    
    if (function_exists('the_custom_logo')) {
        the_custom_logo();
    }
    
}
;

$custom_logo_id = get_theme_mod('custom_logo');
$logo           = wp_get_attachment_image_src($custom_logo_id, 'full');
if (has_custom_logo()) {
    echo '<a id="logo" href="';
?>
<?php
    echo esc_url(home_url('/'));
?>
<?php
    echo '" rel="home"> <img src="' . esc_url($logo[0]) . '" alt="logo" width="400" height="77" />' . '</a>';
} else {
    echo '<a id="logo" href="';
?>
<?php
    echo esc_url(home_url('/'));
?>
<?php
    echo '" rel="home"> <img src="' . get_template_directory_uri() . '/img/nomina-logo.webp' . '" alt="logo">' . '</a>';
}; ?>
				
<div class="media-social-header">
				<a title="facebook" class="facebook-header" href="<?php if(!empty(get_theme_mod('facebook-id'))) { echo esc_html(get_theme_mod('facebook-id')); } else { echo "https://facebook.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/fb-icon.svg" alt="facebook" width="35" height="35" /></a>
				<a title="twitter" class="twitter-header" href="<?php if(!empty(get_theme_mod('twitter-id'))) { echo esc_html(get_theme_mod('twitter-id')); } else { echo "https://twitter.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/twitter-icon-baru.svg" alt="twiter" width="35" height="35"  /></a>
				<a title="instagram" class="instagram-header" href="<?php if(!empty(get_theme_mod('instagram-id'))) { echo esc_html(get_theme_mod('instagram-id')); } else { echo "https://instagram.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/instagram-icon.svg" alt="instagram" width="35" height="35"  /></a>
				<a title="youtube" class="youtube-header" href="<?php if(!empty(get_theme_mod('youtube-id'))) { echo esc_html(get_theme_mod('youtube-id'));  } else { echo "https://youtube.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/youtube-icon.svg" alt="youtube" width="35" height="35"  /></a>
	<?php if(!empty(get_theme_mod('tiktok-id'))) { ?>
	<a title="tiktok" class="tiktok-header" href="<?php echo esc_html(get_theme_mod('tiktok-id')); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/tiktok-icon.svg" alt="tiktok" width="35" height="35"  /></a>
	 <?php };?>
	 <?php if(!empty(get_theme_mod('linkedin-id'))) { ?>
	<a title="linkedin" class="linkedin-header" href="<?php echo esc_html(get_theme_mod('linkedin-id')); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/linkedin-icon.svg" alt="linkedin" width="35" height="35"  /></a>
	 <?php };?>

<?php if(!empty(get_theme_mod('pinterest-id'))) { ?>
	<a title="pinterest" class="pinterest-header" href="<?php echo esc_html(get_theme_mod('pinterest-id')); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/pinterest-icon.svg" alt="pinterest" width="35" height="35"  /></a>
	 <?php };?>

			</div>
			
			<?php get_search_form(); ?>   
			<div class="hamburger-button">
				<div class="line-satu"></div>
				<div class="line-dua"></div>
				<div class="line-tiga"></div>
			</div><!-- akhir hamburger-button -->
			<div class="mobile-menu-kiri-wrap">
		<?php		if (has_custom_logo()) {
    echo '<a id="logo-menu-kiri" href="';
?>
<?php
    echo esc_url(home_url('/'));
?>
<?php
    echo '" rel="home"> <img src="' . esc_url($logo[0]) . '" alt="logo">' . '</a>';
} else {
    echo '<a id="logo-menu-kiri" href="';
?>
<?php
    echo esc_url(home_url('/'));
?>
<?php
    echo '" rel="home"> <img src="' . get_template_directory_uri() . '/img/nomina-logo.webp' . '" alt="logo">' . '</a>';
}; ?><span class="close-button-hamburger">&#10006;</span>
				<div class="clr"></div>
				<?php get_search_form(); ?>   
			<?php wp_nav_menu( array( 'theme_location' => 'menu_utama', 'menu_class' => 'mobile-menu-kiri') ); ?>	
			</div><!-- akhir mobile-menu-kiri-wrap -->
		
<div class="clr">
	
		</div>
		</div><!-- akhir header shrink -->
			<div class="fluid-nav">
		<?php			if ( has_nav_menu( 'menu_utama' ) ) {
	wp_nav_menu( array( 'theme_location' => 'menu_utama', 'menu_class' => 'menu-utama') );
} ?> </div><!-- akhir fluid nav -->
		
			<?php if (get_theme_mod('activate_breaking_news_setting') == 'aktif') { ?>
			<!-- marquee -->
	
	<div class="marquee-baru">
		<div class="inner-wrap">
		 <div class="inner">
    <p>
		<?php $query = new WP_Query( array( 'tag' => 'breaking-news', 'posts_per_page' => '5' ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
	 <a href="<?php the_permalink(); ?>"><?php the_title();?></a>   
                    <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; ?>
		  </p>
			 </div>
  </div><!-- akhir inner-wrap -->
		</div>  <!-- akhir div marquee -->
		<?php }; ?>
		
		</div><!-- akhir header fixed -->
    </header>
	<div class="add-height"></div>
		<?php get_sidebar('header'); ?>