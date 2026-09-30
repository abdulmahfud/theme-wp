<?php if(!empty(get_theme_mod('block-slider-2-setting'))) { 
$block2 = esc_html(get_theme_mod('block-slider-2-setting'));
$query = new WP_Query( array( 'posts_per_page' => 10, 'category_name' => $block2) );
if ( $query->have_posts() ) { ?>
<div id="berita-rekomendasi">
	<p class="judul-berita-rekomendasi">
		<?php echo $block2; ?>
	</p>
	<div class="owl-carousel owl-theme">	
	<?php while ( $query->have_posts() ) {
		$query->the_post(); 
		//
		// Post Content here ?>
			<div class="berita-rekomendasi-box">
		<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'berita-rekomendasi-update'); ?>" alt="berita-rekomendasi-foto" width="255" height="380" />
	<div class="text-berita-rekomendasi">
		<p class="kategori-berita-rekomendasi">
			<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>
		</p>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	</div>
	</div><!-- akhir berita rekomendasi box -->
	<?php } // end while 
		  wp_reset_postdata();
		?>
			</div>
</div><!-- akhir berita rekomendasi -->
<?php } } // end if ?>