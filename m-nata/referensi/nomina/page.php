<?php get_header(); ?>

<?php get_sidebar('banner-160x600-kanan'); ?>

<?php get_sidebar('banner-160x600-kiri'); ?>

<div id="single-content-wrap">
<div id="page-content">
	<!-- start breadcrumbs -->
	<?php // if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
<!-- end breadcrumbs -->
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<?php if ( has_post_thumbnail() ) {?>
                <p> <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'single-foto'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>">
					</p>
	<?php $captionfoto = get_the_post_thumbnail_caption( $post ); 
	if(!empty($captionfoto)) {?>
                <p class="caption-photo"><?php echo get_the_post_thumbnail_caption() ?></p> 
	<?php } else {
} } else {
	
};
	?>
	
                <div id="page-article-text" class="single-article-text">
					
	
					<?php the_content(); ?>
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
					
		</div>
	<?php
//if ( function_exists('wpp_get_views') ) {
  //   get_the_ID() only works when used 
//    inside The Loop! (https://codex.wordpress.org/The_Loop)
//  echo "<div class='totalpembaca'><i class='fa fa-eye'></i> Berita ini ".wpp_get_views(get_the_ID())." kali dibaca</div>";
 //};
?>
	
	
	
	<div class="clr"></div>

            <?php endwhile; ?><?php endif; ?>
	<div class="clr">
		
	</div>


	
	
</div>
<?php get_sidebar('single'); ?>
<div class="clr">	
</div>
</div><!-- akhir single content wrap -->

<?php get_footer(); ?>