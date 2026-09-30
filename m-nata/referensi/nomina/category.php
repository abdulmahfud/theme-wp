<?php get_header(); ?>

<?php get_sidebar('banner-160x600-kanan'); ?>

<?php get_sidebar('banner-160x600-kiri'); ?>

<div id="category-content-wrap">
<div id="category-content">
	<h2 class="judul-label-kategori"><span class="spansatu"><?php $beritacategory = get_theme_mod('berita-category-setting'); if(!empty($beritacategory)) {echo $beritacategory." "; } else { echo "Berita "; }; ?></span><span><?php echo single_cat_title(); ?></span></h2>
	<!-- start breadcrumbs -->
	<?php // if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
<!-- end breadcrumbs -->
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="category-text-wrap">
		
<?php if ( has_post_thumbnail() ) {?>
		<p>
		<img src="<?php echo get_the_post_thumbnail_url($post->ID, 'foto-category'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>" class="img-category-desktop" width="200" height="135" />					
	<?php } 
	?>	<div class="kategori-mobile">	
<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>		</div>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p class="category-kategori"><?php the_category( ' | ' ); ?><span>&nbsp;| <?php the_time('l, j F Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?></span></p>
		<div class="tanggal-mobile">
			<?php the_time('l, j F Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?>
		</div>
	<?php the_excerpt(); ?>
		
	</div><!-- akhir category-text-wrap -->	

	
	
	
	<div class="clr"></div>

            <?php endwhile; ?>
	<div class="next-wrap">
			<?php the_posts_pagination( array(
    'mid_size'  => 2,
    'prev_text' => __( 'Sebelumnya', 'nomina' ),
    'next_text' => __( 'Selanjutnya', 'nomina' ),
) ); ?>
	
	</div>
	<?php endif; ?>
	<div class="clr">
		
	</div>


	
	
</div>
<?php get_sidebar('single'); ?>
<div class="clr">	
</div>
</div><!-- akhir single content wrap -->

<?php get_footer(); ?>