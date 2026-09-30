<?php if(!empty(get_theme_mod('block-slider-3-setting'))) { 
$block3 = esc_html(get_theme_mod('block-slider-3-setting'));
$query = new WP_Query( array( 'posts_per_page' => 10, 'category_name' => $block3) );
if ( $query->have_posts() ) { ?>
<div id="berita-pilihan">
	<p class="judul-berita-pilihan">
		<?php echo $block3; ?>
	</p>
	<div class="owl-carousel">	
	<?php while ( $query->have_posts() ) {
		$query->the_post(); 
		//
		// Post Content here ?>
			<div class="berita-pilihan-box">
		<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'berita-pilihan'); ?>" alt="berita-pilihan-foto" width="191" height="114" />
		<p class="kategori-berita-pilihan">
			<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>
		</p>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	</div><!-- akhir berita pilihan box -->
	<?php } // end while 
		  wp_reset_postdata();
		?>
			</div>
</div><!-- akhir berita pilihan -->
<?php } }// end if ?>