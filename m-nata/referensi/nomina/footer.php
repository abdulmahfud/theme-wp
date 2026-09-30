<?php wp_footer(); ?>
<!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap" rel="stylesheet"> -->
<!--<script type="text/javascript">
    WebFontConfig = {
        google: { families: [ 'Montserrat:400,500,600,700' ] }
    };
    (function() {
        var wf = document.createElement('script');
        wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
    })();
</script> -->
<footer>
	
	<?php if ( '' == get_theme_mod( 'diwp_logo' ) ) { 
} else { ?>
	<img class="logo-footer" src="<?php echo esc_url(get_theme_mod('diwp_logo')); ?>" alt="logo-footer" width="320" height="62" />
<?php	} ?>
	

	<div class="alamat">
		<?php echo wp_kses_post(get_theme_mod('alamat_setting'));  ?>
	</div>
	<div class="media-social-footer">
				<a title="facebook" class="facebook-header" href="<?php if(!empty(get_theme_mod('facebook-id'))) { echo esc_html(get_theme_mod('facebook-id')); } else { echo "https://facebook.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/fb-icon.svg" alt="facebook" width="35" height="35" /></a>
				<a title="twitter" class="twitter-header" href="<?php if(!empty(get_theme_mod('twitter-id'))) { echo esc_html(get_theme_mod('twitter-id')); } else { echo "https://twitter.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/twitter-icon-baru.svg" alt="twiter" width="35" height="35" /></a>
				<a title="instagram" class="instagram-header" href="<?php if(!empty(get_theme_mod('instagram-id'))) { echo esc_html(get_theme_mod('instagram-id')); } else { echo "https://instagram.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/instagram-icon.svg" alt="instagram" width="35" height="35" /></a>
		<a title="youtube" class="instagram-header" href="<?php if(!empty(get_theme_mod('youtube-id'))) { echo esc_html(get_theme_mod('youtube-id'));  } else { echo "https://youtube.com"; } ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/youtube-icon.svg" alt="youtube" width="35" height="35" /></a>
			<?php if(!empty(get_theme_mod('tiktok-id'))) { ?>
	<a title="tiktok" class="tiktok-header" href="<?php echo esc_html(get_theme_mod('tiktok-id')); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/tiktok-icon.svg" alt="tiktok" width="35" height="35"  /></a>
	 <?php };?>
	 <?php if(!empty(get_theme_mod('linkedin-id'))) { ?>
	<a title="linkedin" class="linkedin-header" href="<?php echo esc_html(get_theme_mod('linkedin-id')); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/linkedin-icon.svg" alt="tiktok" width="35" height="35"  /></a>
	 <?php };?>

<?php if(!empty(get_theme_mod('pinterest-id'))) { ?>
	<a title="pinterest" class="pinterest-header" href="<?php echo esc_html(get_theme_mod('pinterest-id')); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/pinterest-icon.svg" alt="tiktok" width="35" height="35"  /></a>
	 <?php };?>
			</div>

	<div class="footer-copyright-wrap">
		
				<?php			if ( has_nav_menu( 'menu_network' ) ) {
	$medianetwork = get_theme_mod('media-network-setting'); 
	echo "<div class='menu-network-wrap'>";
	echo "<p class='network-title'>";
	if(!empty($medianetwork)) {echo $medianetwork; } else { echo "MEDIA NETWORK"; };
	echo "</p>";
	wp_nav_menu( array( 'theme_location' => 'menu_network', 'menu_class' => 'menu-network') );
	echo "</div>";
} ?>
		
		
	<?php 
		wp_nav_menu( array( 'theme_location' => 'menu_bawah', 'menu_class' => 'menu-bawah') );		
?> 		
		
		<?php if( get_theme_mod( 'copyright') != "" ) { ?>
			<p class="footer-copyright">
				<?php echo esc_html(get_theme_mod('copyright')); ?>
			</p>
<?php } else {
			echo "<p class='footer-copyright'>Copyright © ".date('Y')." ";
	echo bloginfo('name');
	echo " - All Rights Reserved</p>";
			} ?>

		
			</div><!-- akhir footer-copyright-wrap -->
	
	 <div id="stop" class="scrollTop">
    <span><a href="#" title="scroll to top"><i class="arrow up"></i></a></span>
  </div><!-- akhir scrolltop -->
	
</footer>

	</body>
</html>