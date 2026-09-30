<?php get_header(); ?>

<?php get_sidebar('banner-160x600-kanan'); ?>

<?php get_sidebar('banner-160x600-kiri'); ?>

<div id="content-wrap">
<div id="content-left-wrap">
<!-- <div class="demo">
                <ul id="lightSlider">
						<?php // $query = new WP_Query( array( 'tag__in' => 17, 'posts_per_page' => '5') ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
                    <li data-thumb="<?php // echo get_the_post_thumbnail_url(get_the_ID(), 'headline-thumbnail'); ?>"> <p class="text-box-headline">
						Headline
					</p>
                        <h2><a href="<?php // the_permalink(); ?>"><?php // the_title();?></a></h2><img src="<?php // echo get_the_post_thumbnail_url(get_the_ID(), 'headline'); ?>"  />
                         </li>
                   <?php /*
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; 
					// Reset postdata
wp_reset_postdata(); */
					?>
		</ul></div> -->
	<!-- <div class="clr"></div> -->

<div class="flexslider" style="display: none">
  <ul class="slides">
	  <?php $query = new WP_Query( array( 'tag' => 'headline', 'posts_per_page' => '5') ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
    <li data-thumb="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'headline-thumbnail'); ?>"><img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'headline'); ?>" alt="headline-foto" width="750" height="370" />
		<p class="headline-label">
			<?php $headlineistilah = get_theme_mod('headline-istilah-setting'); if(!empty($headlineistilah)) {echo $headlineistilah; } else { echo "Headline"; }; ?>
		</p>
		<h2 class="headline-judul">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
    </li>
		
     <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; 
					// Reset postdata
wp_reset_postdata();
					?>
  </ul>
</div>
	
	<?php if(get_theme_mod('headlinemobile') == "model1") { ?>
	<!-- headline mobile 1 -->
<div class="flexslider-mobile">
  <ul class="slides">
	  <?php 
 $slider_headline = get_theme_mod('slider_headline_setting');														   
$query = new WP_Query( array( 'tag' => $slider_headline, 'posts_per_page' => '5') ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
    <li><img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'headline-mobile'); ?>" alt="headline-foto" width="370" height="250" />
		<p class="headline-label-mobile">
			<?php
$category = get_the_category(); 
echo $category[0]->cat_name;
					?>
		</p>
		<h2 class="headline-judul-mobile">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
    </li>
		
     <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; 
				//	 Reset postdata
wp_reset_postdata();
					?>
  </ul>
</div><!-- akhir headline mobile -->
	<?php } else if(get_theme_mod('headlinemobile') == "model3") { ?>

	<!-- headline mobile 3 -->
	
	<ul id="lightSliderMobile">
	<?php 
 $slider_headline = get_theme_mod('slider_headline_setting');	
$query = new WP_Query( array( 'tag' => $slider_headline, 'posts_per_page' => '5' ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
	<li><div class="headline-tiga-mobile">
		<?php the_post_thumbnail('headline-tiga-mobile'); ?>
		<div class="headline-tiga-text-wrap-mobile">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2><p class="headline-tiga-waktu-mobile"><?php the_time('l, j M Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?></p><!-- akhir headline-waktu-mobile -->
			</div><!-- akhir headline-text-wrap-mobile -->
	</div></li><!-- akhir headline-terkini-mobile -->
                    <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; ?>
	</ul>
	
	<!-- akhir headline mobile 3 -->
	<?php } else { ?>
	<!-- headline mobile 2 -->
	<div class="flexslider-mobile">
  <ul class="slides">
	  <?php 
$slider_headline = get_theme_mod('slider_headline_setting');	
$query = new WP_Query( array( 'tag' => $slider_headline, 'posts_per_page' => '5') ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
    <li><img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'headline-mobile'); ?>" alt="headline-foto" width="370" height="250" />
		<div class="wrap-text-headline-dua">
		<p class="headline-label-mobile-dua">
			<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>
		</p>
		<h2 class="headline-judul-mobile-dua">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
	<p class="tanggal"><?php the_time('l, j M Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?></p>
		</div>
		    </li>
		
     <?php 
                            endwhile;
                            else :
                            _e( '&nbsp;', 'nomina' );
                            endif; 
					// Reset postdata
wp_reset_postdata();
					?>
  </ul>
</div><!-- akhir headline mobile -->
<?php } ?>
	
	<!-- SLIDER SWIPPER -->
	<div class="slider-container">
<div
      style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
      class="swiper mySwiper2"
    >
      <div class="swiper-wrapper">
 <?php 
		  $slider_headline = get_theme_mod('slider_headline_setting');
		  $query = new WP_Query( array( 'tag' => $slider_headline, 'posts_per_page' => '4' ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>
		  
		     <div class="swiper-slide"><div style="background: rgba(0,0,0,0) linear-gradient(transparent 0%,rgba(0,0,0,.4) 90%,rgba(0,0,0,.8) 100%) repeat scroll 0% 0%">
				 <p class="judul-headline">
					 	<?php $headlineistilah = get_theme_mod('headline-istilah-setting'); if(!empty($headlineistilah)) {echo $headlineistilah; } else { echo "Headline"; }; ?>
				 </p>
				 <?php	
   $category = get_the_category(); 
   $category_name = $category[0]->cat_name;
   $category_id = get_cat_ID($category_name);
   $category_link = get_category_link( $category_id );
   ?>
				 <p class="slider-text">
					 <a class="slider-kategori" href="<?php echo esc_url( $category_link ); ?>"><?php echo $category_name; ?></a>
					 <a class="judul-slider" href="<?php the_permalink(); ?>">
					 <?php the_title(); ?>
					 </a>
				 </p>
          <img class="foto-slider" src="<?php echo get_the_post_thumbnail_url($post->ID, 'foto-slide-headline-swipper'); ?>" alt="foto slider headline" width="790" height="430" />
				 </div>
        </div>
			
			
			<?php endwhile; ?><?php wp_reset_postdata();
else: ?>
<?php endif; ?>
		        </div>
 <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  <div class="swiper mySwiper">
      <div class="swiper-wrapper">
 <?php $query = new WP_Query( array( 'tag' => $slider_headline, 'posts_per_page' => '4' ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();?>		
		  <div class="swiper-slide">
           <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'foto-thumbnail-slide-headline'); ?>" alt="thumbnail" width="157" height="94" />
			   <p class="judul-thumbnail">
					 <?php the_title(); ?>
				 </p>
		</div>
			  <?php endwhile; ?><?php wp_reset_postdata();
else: ?>
<?php endif; ?>
        
	  </div>
	</div>
</div><!-- akhir slider container -->
	<!-- AKHIR SLIDER SWIPPER -->
<!--<div id="berita-pilihan">
	<p class="judul-berita-pilihan">
		Berita Pilihan
	</p>
	<div class="owl-carousel"> -->
		 <?php
   /* $query = new WP_Query( array( 'posts_per_page' => 6 ) );
    while ( $query->have_posts() ) {
        $query->the_post();
        ?>
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
        <?php
    }
    wp_reset_postdata(); */
    ?>
<!--	</div>
</div> --><!-- akhir berita pilihan -->
	
<div id="news-feed">
    <p class="news-feed-judul-block"><span><?php $beritaterkini = get_theme_mod('berita-terkini-setting'); if(!empty($beritaterkini)) {echo $beritaterkini; } else { echo "BERITA TERKINI"; }; ?></span></p>
    <?php
if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        // Your loop code ?>
	<div class="news-feed-list">
		<a class="news-feed-link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) { ?>
		<figure><img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'newsfeed-home'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>" width="270" height="150" class="newsfeed-image"/>
			<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'newsfeed-home-mobile'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>" width="85" height="85" class="newsfeed-image-mobile" /></figure>  <?php }; ?>
		<div class="news-feed-text-block">
				<p class="kategori"><?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?></p>
			
				<h2 class="news-feed-judul"><?php the_title(); ?></h2>
			<p class="tanggal"><?php the_time('l, j M Y '); echo "- "; the_time(' H:i'); ?><?php $zonawaktu = get_theme_mod('zonawaktu');
											if( get_theme_mod( 'zonawaktu') != "" ) { ?>
											<?php echo " ". esc_html($zonawaktu);
											}; ?></p>
			<div class="the-excerpt">
				<?php //the_excerpt(); ?>
			</div>
				
		</div><!-- akhir news-feed-text-block -->
	<div class="clr"></div>
			</a>
	</div><!-- akhir news-feed-list -->
	<?php if( $wp_query->current_post == 1 ) { 
get_sidebar('newsfeed-satu');
};
		if( $wp_query->current_post == 2 ) { 
// include 'berita-pilihan.php';
include 'block1.php';
};
	
	if( $wp_query->current_post == 5 ) { 
get_sidebar('newsfeed-dua');
};
	 if( $wp_query->current_post == 6 ) { 
// include 'berita-rekomendasi.php';
include 'block2.php';
	 };
	
	if( $wp_query->current_post == 8 ) { 
get_sidebar('newsfeed-tiga');
};
	
			if( $wp_query->current_post == 9 ) { 
include 'block3.php';
};

		if( $wp_query->current_post == 12 ) { 
include 'block4.php';
};
	
			if( $wp_query->current_post == 15 ) { 
include 'block5.php';
};
	
		if( $wp_query->current_post == 18 ) { 
include 'block6.php';
};
	
		if( $wp_query->current_post == 21 ) { 
include 'block7.php';
};
	
		if( $wp_query->current_post == 24 ) { 
include 'block8.php';
};
	
	if( $wp_query->current_post == 27 ) { 
include 'block9.php';
};
	
	if( $wp_query->current_post == 30 ) { 
include 'block10.php';
};

	
	?>
    <?php endwhile; ?>
		<div class="next-wrap">
			<?php the_posts_pagination( array(
    'mid_size'  => 2,
    'prev_text' => __( 'Sebelumnya', 'nomina' ),
    'next_text' => __( 'Selanjutnya', 'nomina' ),
) ); ?>
	
	</div>
<?php else :
    esc_html_e( 'Sorry, no posts were found.', 'nomina' );
endif;
?>
	
</div><!-- akhir #news-feed -->
		
<div class="clr"></div>
</div><!-- akhir #content-left-wrap -->
		<?php get_sidebar('right'); ?>
	<div class="clr"></div>
</div><!-- akhir #content-wrap -->
<?php get_footer(); ?>