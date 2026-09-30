<?php get_header(); ?>

<?php get_sidebar('banner-160x600-kanan'); ?>

<?php get_sidebar('banner-160x600-kiri'); ?>

<div id="single-content-wrap">
<div id="single-content" <?php post_class(); ?>>
	<!-- start breadcrumbs -->
	<?php // if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
<!-- end breadcrumbs -->
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="wrap-kategori-tanggal">
                <p class="single-kategori"><a href="<?php echo get_home_url(); ?>"><span>Home</span></a> / <?php the_category( ' / ' ); ?></p>
	</div>
	<h1><?php the_title(); ?></h1>
	<div class="container-single-meta">
		<?php 
	if (get_theme_mod('fotopenulis') == "show") { ?>

		<p class="foto-penulis">
	    	<?php
				$user = wp_get_current_user();
echo get_avatar( get_the_author_meta( 'ID' ), 32 ); 
			?> </p> <?php } else {}; ?>
		<div class="group-penulis-dan-tanggal">
					<p class="nama-penulis"><span> 	<?php
			the_author_posts_link(); ?> </span> <?php $writer = get_theme_mod('writer');
											if( get_theme_mod( 'writer') != "" ) { 
												echo "- ".$writer;
											} else { echo " "; }; ?></p>
			<p class="tanggal-single"><?php the_time('l, j F Y '); ?>
				<?php if (get_theme_mod('waktupostberita') == "show") { ?>
				<span><?php echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?></span>
			<?php } else { }; ?>
			</p>
			</div>
			<div class="single-media-social">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar');return false;" title="share ke facebook"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/fb-icon.svg" alt="facebook" width="32" height="32"  />
</a>
				<a href="https://twitter.com/intent/tweet?text=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke twitter"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/twitter-icon-baru.svg" alt="twitter" width="32" height="32" />
</a>
				<a href="https://wa.me/?text=*<?php echo get_the_title(); ?>* %0A%0A<?php echo get_the_excerpt(); ?> %0A%0A_Baca selengkapnya:_ %0A <?php echo get_permalink(); ?>" data-action="share/whatsapp/share" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke whatsapp"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/whatsapp-icon.svg" alt="whatsapp" width="32" height="32"  />
</a>
					<a href="https://t.me/share/url?url=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke telegram"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/telegram-icon.svg" alt="telegram" width="32" height="32"  />
</a>
				<a href="https://social-plugins.line.me/lineit/share?url=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke line"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/line-icon.svg" alt="line" width="32" height="32"  />
</a>
				<span class="clipboard" style="display: inline-block; cursor: pointer"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/link-icon.svg" alt="copy" width="32" height="32" /></span><p class="copied">
				URL berhasil dicopy
				</p>
			</div>
	</div>
		
	
	
	<div class="media-sosial-mobile">
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar');return false;" title="share ke facebook"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/fb-icon.svg" alt="facebook icon" width="32" height="32" />
</a>
				<a href="https://twitter.com/intent/tweet?text=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke twitter"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/twitter-icon-baru.svg" alt="twitter icon" width="32" height="32"  />
</a>
				<a href="https://wa.me/?text=*<?php echo get_the_title(); ?>* %0A%0A<?php echo get_the_excerpt(); ?> %0A%0A_Baca selengkapnya:_ %0A <?php echo get_permalink(); ?>" data-action="share/whatsapp/share" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke whatsapp"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/whatsapp-icon.svg" alt="whatsapp icon" width="32" height="32"  />
</a>
		<a href="https://t.me/share/url?url=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke telegram"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/telegram-icon.svg" alt="telegram icon" width="32" height="32"  /></a>
				<a href="https://social-plugins.line.me/lineit/share?url=<?php echo get_permalink(); ?>" onclick="window.open(this.href,'window','width=640,height=480,resizable,scrollbars,toolbar,menubar') ;return false;" title="share ke line"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/line-icon.svg" alt="line icon" width="32" height="32"  />
</a>
		<span class="clipboard-mobile" style="display: inline-block; cursor: pointer"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/link-icon.svg" alt="copy" width="32" height="32" /></span><p class="copied-mobile">
				URL berhasil dicopy
				</p>
	</div>
	
	
	<div class="clr"></div>
	<?php		if ( has_post_format('video') ) { ?>

	<?php } else { ?>
	<?php if ( has_post_thumbnail() ) {?>
                <p class="foto-utama"> <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'single-foto'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>" width="800" height="533" />
					</p>
	<?php $captionfoto = get_the_post_thumbnail_caption( $post ); 
	if(!empty($captionfoto)) {?>
   <?php if(get_theme_mod('foto_caption_setting') == "Buka Tutup") { ?>
		<div style="position: relative">
						<p class="caption-info" href="#">i</p></div>
				<p class="caption-photo-buka-tutup"><?php echo get_the_post_thumbnail_caption() ?></p>
	<?php } else { ?>
                <p class="caption-photo"><?php echo get_the_post_thumbnail_caption() ?></p> 
	<?php } ?>
	
	
	
	<?php } else {
} } else {
	
}};
	?>
	
                <div id="single-article-text" class="single-article-text">
					<?php the_content(); ?>
					<?php
// Grab the metadata from the database
$penulis = get_post_meta( get_the_ID(), 'penulis', true );
$editor = get_post_meta( get_the_ID(), 'editor', true );
$sumber = get_post_meta( get_the_ID(), 'sumber', true );				

// Echo the metadata
if(!empty($penulis)) {
echo "<p class='penulis-bawah'><strong>Penulis : </strong>". esc_html( $penulis )."</p>";	
};

					
if(!empty($editor)) {
echo "<p class='editor-bawah'><strong>Editor : </strong>". esc_html( $editor )."</p>";	
};

if(!empty($sumber)) {
echo "<p class='sumber-berita-bawah'><strong>Sumber Berita : </strong>".esc_html( $sumber )."</p>";
};
?>
					<?php
// original content display
// the_content('<p>Read the rest of this page &raquo;</p>');

// split content into array
/* $content = split_content();

// output first content section in column1
echo '<div id="column1">', array_shift($content), '</div>';

// output remaining content sections in column2
echo '<div id="column2">', implode($content), '</div>';
?>
					<?php wp_link_pages( array(
	'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
	'after'       => '</div>',
	'link_before' => '<span>',
	'link_after'  => '</span>',
	) ); */
?>
						<?php
wp_link_pages(array(
    'before' => '<p class="page-link-wrap"><span class="halaman-text">Halaman : </span> ',
    'after' => '</p>',
    'next_or_number' => 'next_and_number', # activate parameter overloading
    'nextpagelink' => __('Selanjutnya', 'nomina'),
    'previouspagelink' => __('Sebelumnya', 'nomina'),
    'pagelink' => '%',
    'echo' => 1 )
);
?>

	<?php if(!empty(get_theme_mod('berlangganan_whatsapp_setting'))) { 
					    $site_url = site_url();
$url = preg_replace("(^https?://)", "", $site_url );


  echo "<div id='berlangganan-whatsapp'><strong><i><span>Follow WhatsApp Channel ". $url ." untuk update berita terbaru setiap hari </span>". "<a href='".esc_html(get_theme_mod('berlangganan_whatsapp_setting'))."'>Follow</a></i></strong><div class='clr'></div></div>";

} ?>
					
		</div>
	
	
	<?php

// Get a list of the current post's categories
global $post;
$categories = get_the_category( $post->ID );
$idnya = $post->ID;
$catidlist = '';
foreach( $categories as $category) {
    $catidlist .= $category->cat_ID . ",";
}
// Build our category based custom query arguments
$custom_query_args = array( 
	'posts_per_page' => 8, // Number of related posts to display
	'post__not_in' => array($post->ID), // Ensure that the current post is not displayed
	'orderby' => 'date', // Randomize the results
	'cat' => $catidlist, // Select posts in the same categories as the current post
);
// Initiate the custom query
$custom_query = new WP_Query( $custom_query_args );

// Run the loop and output data for the results
if ( $custom_query->have_posts() ) : ?>
	<div class="related-post-wrap">
	<p class="berita-terkait">
		<?php $beritaterkait= get_theme_mod('berita-terkait-setting'); if(!empty($beritaterkait)) {echo $beritaterkait; } else { echo "Berita Terkait"; }; ?>
	</p>
	<?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>
	<div class="related-post-box">
		
		<a href="<?php the_permalink(); ?>">
			
			<span><?php the_title(); ?></span></a>	</div>
	<?php endwhile; ?>
		<?php get_sidebar('middle'); ?>
<div class="clr"></div>
	</div><!-- akhir related post wrap -->
<?php endif;
// Reset postdata
wp_reset_postdata();
?>
	<div class="clr"></div>
	<?php 
	if (get_theme_mod('jumlahpembaca') == "show") {
if ( function_exists('wpp_get_views') ) {
  //   get_the_ID() only works when used 
//    inside The Loop! (https://codex.wordpress.org/The_Loop)
  echo "<div class='totalpembaca'>Berita ini ".wpp_get_views(get_the_ID())." kali dibaca</div>";
};
		};
?>
	
	<?php if(has_tag()) {; ?>
	<div class="tagname">
	<span>Tag :</span> <?php the_tags( '',' ' ); ?>
	</div><!-- end tagname -->
	<?php }; ?>
	

            <?php endwhile; ?><?php endif; ?>
	
	<?php 

	$additional = get_post_meta( get_the_ID(), 'text_textarea', true );	
	
	if(!empty($additional)) {
echo "<div class='additional'><strong>".esc_html( $additional )."</strong></div>";
}; ?>
	<div class="clr">
		
	</div>
<div class="desktop-berita-terbaru">
		<p class="berita-terbaru">
		<?php $beritaterbaru= get_theme_mod('berita-terbaru-setting'); if(!empty($beritaterbaru)) {echo $beritaterbaru; } else { echo "Berita Terbaru"; }; ?>
	</p>

	<?php $query = new WP_Query( array('posts_per_page' => '6' ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>

		<div class="desktop-berita-terbaru-box">
			<a href="<?php the_permalink(); ?>">
			<?php	if ( has_post_thumbnail() ) { ?>
			<p>
			<img class="foto-desktop-berita-terbaru" src="<?php echo get_the_post_thumbnail_url($post->ID, 'desktop-berita-terbaru-dua'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>" width="225" height="129" /></p> <?php } ?>
			<p class="desktop-kategori-berita-terbaru">	
			<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>	</p>
                        <p class="judul-desktop-berita-terbaru"><?php the_title();?></p>
			<div class="clr"></div>
				</a>
			</div><!-- desktop-berita-terbaru-box -->
	
                    <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; ?>
	</div>
</div>
	<?php 
	 include_once ABSPATH . 'wp-admin/includes/plugin.php';
	 if ( is_plugin_active( 'plugins/comments-from-facebook/' ) ) {
	// plugin is activated
	
	 echo do_shortcode('[wpdevart_facebook_comment curent_url="'.get_permalink().'" order_type="social" title_text="" title_text_color="#000000" title_text_font_size="22" title_text_font_famely="monospace" title_text_position="left" width="100%" bg_color="#d4d4d4" animation_effect="random" count_of_comments="10" ]');
} 
?>
	<div class="mobile-berita-terkait">
			<?php

// Get a list of the current post's categories
global $post;
$categories = get_the_category( $idnya );
$catidlist = '';
foreach( $categories as $category) {
    $catidlist .= $category->cat_ID . ",";
}
// Build our category based custom query arguments
$custom_query_args = array( 
	'posts_per_page' => 5, // Number of related posts to display
	'post__not_in' => array($post->ID), // Ensure that the current post is not displayed
	'orderby' => 'date', // Randomize the results
	'cat' => $catidlist, // Select posts in the same categories as the current post
);
// Initiate the custom query
$custom_query = new WP_Query( $custom_query_args );

// Run the loop and output data for the results
if ( $custom_query->have_posts() ) : ?>
	<div class="related-post-wrap">
	<p class="berita-terkait">
		<?php $beritaterkait= get_theme_mod('berita-terkait-setting'); if(!empty($beritaterkait)) {echo $beritaterkait; } else { echo "Berita Terkait"; }; ?>
	</p>
	<?php while ( $custom_query->have_posts() ) : $custom_query->the_post(); ?>
	<div class="related-post-box">
		<p class="mobile-tanggal-terkait">
			<?php the_time('l, j F Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?>
		</p>
		<a href="<?php the_permalink(); ?>">
			
			<span><?php the_title(); ?></span></a>	</div>
	<?php endwhile; ?>
<div class="clr"></div></div><!-- akhir related post wrap -->
<?php endif;
// Reset postdata
wp_reset_postdata();
?>
	
	</div>

	<div class="mobile-berita-terbaru">
		<p class="berita-terbaru">
		<?php $beritaterbaru= get_theme_mod('berita-terbaru-setting'); if(!empty($beritaterbaru)) {echo $beritaterbaru; } else { echo "Berita Terbaru"; }; ?>
	</p>

	<?php $query = new WP_Query( array('posts_per_page' => '5' ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>

		<div class="mobile-berita-terbaru-box">
			<?php if ( has_post_thumbnail() ) { ?>
			<img class="foto-mobile-berita-terbaru" src="<?php echo get_the_post_thumbnail_url($post->ID, 'mobile-berita-terbaru'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>" width="129" height="85" />
			<?php } ?>
			<p class="mobile-kategori-berita-terbaru">	
			<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>	</p>
                        <p class="judul-berita-terbaru"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></p><p class="tanggal-berita-terbaru"><?php the_time('l, j M Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?></p>
			<div class="clr"></div>
			</div><!-- mobile-berita-terbaru-box -->
	
                    <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; ?>
	</div>
<?php get_sidebar('single'); ?>
<div class="clr">	
</div>
</div><!-- akhir single content wrap -->

<?php get_footer(); ?>