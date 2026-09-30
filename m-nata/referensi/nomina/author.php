<?php get_header(); ?>

<?php get_sidebar('banner-160x600-kanan'); ?>

<?php get_sidebar('banner-160x600-kiri'); ?>

<div id="category-content-wrap">
<div id="category-content">
	<h2 class="judul-label-kategori nama-penulis"><?php
    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
    ?>
    <?php  echo $curauth->display_name; ?></h2>
	<!-- start breadcrumbs -->
	<?php // if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
<!-- end breadcrumbs -->
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="category-text-wrap">
		
<?php if ( has_post_thumbnail() ) {?>
		<p><img src="<?php echo get_the_post_thumbnail_url($post->ID, 'foto-category'); ?>" alt="<?php echo get_the_post_thumbnail_caption() ?>"></p>
					
	<?php } 
	?>	<div class="kategori-mobile">	
<?php
   $category = get_the_category(); 
   echo $category[0]->cat_name;
					?>		</div>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p class="category-kategori"><?php the_category( ' | ' ); ?><span>&nbsp;| <?php the_time('l, j F Y '); echo "- "; the_time(' H:i'); echo " WIB"; ?></span></p>
		<div class="tanggal-mobile">
			<?php the_time('l, j F Y '); echo "- "; the_time(' H:i'); echo " WIB"; ?>
		</div>
	<?php the_excerpt(); ?>
		
	</div><!-- akhir category-text-wrap -->	

	
	
	
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