<?php

add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support(
    'html5',
    array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
      'script',
      'style',
      'navigation-widgets',
    )
  );
add_theme_support('automatic-feed-links');
add_theme_support( "responsive-embeds" );
add_theme_support( 'post-formats', array( 'video' ));

/* Disable WordPress Admin Bar for all users but admins. */
  // show_admin_bar(false);

// register image size
add_image_size('list', 150, 100, true);
add_image_size('related-foto', 182, 100, true);
add_image_size('single-foto', 800, 9999, false );
add_image_size('headline', 750, 370, true);
add_image_size('foto-category', 200, 135, true);
add_image_size('headline-thumbnail', 148, 110.39, true);
add_image_size('headline-mobile', 370, 250, true);
add_image_size('headline-tiga-mobile', 430, 250, true);
add_image_size('recent-post-by-widget-big-thumbnail', 360, 200, true);
add_image_size('mobile-berita-terbaru', 129, 85, true);
add_image_size('berita-pilihan', 233, 139, true);
add_image_size('berita-rekomendasi-update', 255, 380, true);
add_image_size('foto-fb-share', 600, 315, true);
add_image_size('newsfeed-firstimage', 300, 200, true);
add_image_size('newsfeed-home', 270, 150, true);
add_image_size('newsfeed-home-mobile', 85, 85, true);
add_image_size('foto-slide-headline-swipper', 790, 430, true);
add_image_size('foto-thumbnail-slide-headline', 157, 94, true);
add_image_size('desktop-berita-terbaru-dua', 225, 129, true);


// register style 
function fungsi_styleku() {
    wp_enqueue_style('styleku', get_template_directory_uri().'/style.css', false, '1.7', 'all');
}
add_action( 'wp_enqueue_scripts', 'fungsi_styleku');

function lightslidercss() {
    wp_enqueue_style('lightslidercss', get_template_directory_uri().'/css/lightslider.min.css', false, '1.1', 'all');
}
add_action( 'wp_enqueue_scripts', 'lightslidercss');

function flexslidercss() {
    wp_enqueue_style('flexslidercss', get_template_directory_uri().'/css/flexslider.css', false, '1.1', 'all');
}
add_action( 'wp_enqueue_scripts', 'flexslidercss');

function owlcss() {
    wp_enqueue_style('owlcss', get_template_directory_uri().'/css/owl.carousel.min.css', false, '2.1', 'all');
}
add_action( 'wp_enqueue_scripts', 'owlcss');

/* function swiperCss() {
    wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper@8/swiper-bundle.min.css', false, '8.2.4', 'all');
}
add_action( 'wp_enqueue_scripts', 'swiperCss'); */

function swiperCss() {
    wp_enqueue_style('swiper-css', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.css', false, '11.0.5', 'all');
}
add_action( 'wp_enqueue_scripts', 'swiperCss'); 

// register script
function scriptAndJquery(){
    wp_enqueue_script('script', get_template_directory_uri() . '/js/script.js', array('jquery'),'1.0',true);
};
add_action('wp_enqueue_scripts','scriptAndJquery');

// register flexslider js
function flexsliderjs(){
    wp_enqueue_script('flexjs', get_template_directory_uri() . '/js/jquery.flexslider.js', array('jquery'),'1.0',true);
};
add_action('wp_enqueue_scripts','flexsliderjs');

function owljs(){
    wp_enqueue_script('owljs', get_template_directory_uri() . '/js/owl.carousel.min.js','2.0',true);
};
add_action('wp_enqueue_scripts','owljs');

//Load jquery on Footer
/* add_action('wp_enqueue_scripts', 'register_jquery');
function register_jquery() {
    wp_deregister_script('jquery'); // remove original jQuery
    wp_register_script( // add custom jQuery in footer
       'jquery', 
       'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js',
       array(), // dependencies
       null, // version
       true); // load in footer?
    wp_enqueue_script('jquery');
}*/

function lightslider(){
    wp_enqueue_script('lightslider', get_template_directory_uri() . '/js/lightslider.min.js','1.0',false);
};
add_action('wp_enqueue_scripts','lightslider');

/* function scriptSwipe(){
    wp_enqueue_script('script-swipe', 'https://unpkg.com/swiper@8/swiper-bundle.min.js', array(), '8.2.4', false);
};
add_action('wp_enqueue_scripts','scriptSwipe'); */

function scriptSwipe(){
    wp_enqueue_script('script-swipe', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js', array(), '11.0.5', false);
};
add_action('wp_enqueue_scripts','scriptSwipe');

// register logo
    function theme_prefix_setup() { 
    add_theme_support( 'custom-logo', array(
        'height'      => 77,
        'width'       => 400,
        'flex-width' => false,
    ) );

}
add_action( 'after_setup_theme', 'theme_prefix_setup' );

// register menu
function menu_utama() {
  register_nav_menu('menu_utama',__( 'menu utama', 'nomina' ));
}
add_action( 'init', 'menu_utama' );

// register menu
function menu_bawah() {
  register_nav_menu('menu_bawah',__( 'menu bawah', 'nomina' ));
}
add_action( 'init', 'menu_bawah' );

// register menu network
function menu_network() {
  register_nav_menu('menu_network',__( 'menu network', 'nomina' ));
}
add_action( 'init', 'menu_network' );

// register sidebar Banner Mobile Top Header Parallax
function sidebarBannerMobileTopHeaderParallax() {
    register_sidebar( array(
        'name'          => 'Banner Mobile Top Header Parallax 320 x 480',
        'id'            => 'sidebar-banner-mobile-top-header-parallax',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 320px x 480px',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '<h2 class="judul-sidebar-banner-mobile-top-header-parallax">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarBannerMobileTopHeaderParallax' );

// register sidebar Header
function sidebarHeader() {
    register_sidebar( array(
        'name'          => 'Banner Header 970 × 250 ',
        'id'            => 'sidebar-header',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 970px x 250px',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-header">',
        'after_title'   => '</div>',
    ) );
}
add_action( 'widgets_init', 'sidebarHeader' );


// register sidebar right widget 
function sidebarRight() {
    register_sidebar( array(
        'name'          => 'Sidebar Right 300 x 250',
        'id'            => 'sidebar-right',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 300px x 250px',
        'before_widget' => '<div class="before-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-right">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarRight' );

// register sidebar single widget 
function sidebarSingle() {
    register_sidebar( array(
        'name'          => 'Sidebar Single 300 x 250',
        'id'            => 'sidebar-single',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 300px x 250px',
        'before_widget' => '<div class="before-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-single">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarSingle' );



// register sidebar sidebarBanner160x600kanan widget
function sidebarBanner160x600kanan() {
    register_sidebar( array(
        'name'          => 'Banner 160x600 Kanan',
        'id'            => 'sidebar-banner-160x600-kanan',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 160px x 600px',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-sidebar-banner-160x600-kanan">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarBanner160x600kanan' );

// register sidebar sidebarBanner160x600kiri widget
function sidebarBanner160x600kiri() {
    register_sidebar( array(
        'name'          => 'Banner 160x600 Kiri',
        'id'            => 'sidebar-banner-160x600-kiri',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 160px x 600px',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-sidebar-banner-160x600-kiri">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarBanner160x600kiri' );

// register sidebar banner bawah
function sidebarBannerBawah() {
    register_sidebar( array(
        'name'          => 'Banner Bawah 728 x 90',
        'id'            => 'sidebar-banner-bawah',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 728px x 90px',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-banner-bawah">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarBannerBawah' );



function sidebarMiddle() {
    register_sidebar( array(
        'name'          => 'Sidebar Artikel Pinggir Kiri 160 x 600',
        'id'            => 'sidebar-middle',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 160px x 600px',
        'before_widget' => '<div class="sidebar-middle-wrap">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="judul-sidebar-middle">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarMiddle' );

function sidebarNewsfeedSatu() {
    register_sidebar( array(
        'name'          => 'Sidebar Newsfeed 1 336 x 280',
        'id'            => 'sidebar-newsfeed-satu',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 336px x 280px',
        'before_widget' => '<div class="newsfeed-satu-container"><div class="sidebar-newsfeed-satu-wrap">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="judul-sidebar-newsfeed-satu">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarNewsfeedSatu' );

function sidebarNewsfeedDua() {
    register_sidebar( array(
        'name'          => 'Sidebar Newsfeed 2 336 x 280',
        'id'            => 'sidebar-newsfeed-dua',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 336px x 280px',
        'before_widget' => '<div class="newsfeed-dua-container"><div class="sidebar-newsfeed-dua-wrap">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="judul-sidebar-newsfeed-dua">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarNewsfeedDua' );

function sidebarNewsfeedTiga() {
    register_sidebar( array(
        'name'          => 'Sidebar Newsfeed 3 336 x 280',
        'id'            => 'sidebar-newsfeed-tiga',
        'description'   => 'Bisa diisi dengan Widget Gambar ukuran 336px x 280px',
        'before_widget' => '<div class="newsfeed-tiga-container"><div class="sidebar-newsfeed-tiga-wrap">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h2 class="judul-sidebar-newsfeed-tiga">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'sidebarNewsfeedTiga' );


//limit the_excerpt
function custom_excerpt_length( $length ) {
        return 24;
    }
    add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

//change read more the_excerpt
function new_excerpt_more( $more ) {
    return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

//Exclude pages from WordPress Search
if (!is_admin()) {
function wpb_search_filter($query) {
if ($query->is_search) {
$query->set('post_type', 'post');
}
return $query;
}
add_filter('pre_get_posts','wpb_search_filter');
}

add_action( 'cmb2_admin_init', 'cmb2_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 */
function cmb2_sample_metaboxes() {

    /**
     * Initiate the metabox
     */
    $cmb = new_cmb2_box( array(
        'id'            => 'test_metabox',
        'title'         => __( 'Penulis, Editor dan Sumber Berita', 'nomina' ),
        'object_types'  => array( 'post', ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // Keep the metabox closed by default
    ) );
    
        // Regular text field
    $cmb->add_field( array(
        'name'       => __( 'Penulis', 'nomina' ),
        // 'desc'       => __( 'field description (optional)', 'cmb2' ),
        'id'         => 'penulis',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
        // 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
        // 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
        // 'on_front'        => false, // Optionally designate a field to wp-admin only
        // 'repeatable'      => true,
    ) );

    // Regular text field
    $cmb->add_field( array(
        'name'       => __( 'Editor', 'nomina' ),
        // 'desc'       => __( 'field description (optional)', 'cmb2' ),
        'id'         => 'editor',
        'type'       => 'text',
        'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
        // 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
        // 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
        // 'on_front'        => false, // Optionally designate a field to wp-admin only
        // 'repeatable'      => true,
    ) );

    // URL text field
    $cmb->add_field( array(
        'name' => __( 'Sumber Berita', 'nomina' ),
        // 'desc' => __( 'field description (optional)', 'cmb2' ),
        'id'   => 'sumber',
        'type' => 'text',
        // 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
        // 'repeatable' => true,
    ) );
    
    
    /**
     * Initiate the metabox
     */
    $add = new_cmb2_box( array(
        'id'            => 'additional',
        'title'         => __( 'Additional Text', 'nomina' ),
        'object_types'  => array( 'post', ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // Keep the metabox closed by default
    ) );

    // textarea
    $add->add_field( array(
    'name' => 'Text',
    //'desc' => 'field description (optional)',
    //'default' => 'standard value (optional)',
    'id' => 'text_textarea',
    'type' => 'textarea'
) );

}





/** Remove Dashicons from Admin Bar for non logged in users **/
add_action('wp_print_styles', 'jltwp_adminify_remove_dashicons', 100);

/** Remove Dashicons from Admin Bar for non logged in users **/
function jltwp_adminify_remove_dashicons()
{
    if (!is_admin_bar_showing() && !is_customize_preview()) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }
}

// add class to youtube iframe

 function wpse_287229_embed_html( $cache, $url, $attr, $post_ID ) {
    if ( 
        strpos( $cache, 'youtube.com' ) !== false || 
        strpos( $cache, 'youtu.be' ) !== false 
    ) {
        $cache = str_replace( '<iframe ', '<iframe class="my-youtube-video" ', $cache ); // YouTube doesn't have a class on its iframe.
    }

    if ( strpos( $cache, 'soundcloud.com' ) !== false ) {
        // str_replace optimised for SoundCloud here.
    }

    // etc.

    return $cache;
}
add_filter( 'embed_oembed_html', 'wpse_287229_embed_html', 10, 4 ); 

/**
 * Add prev and next links to a numbered page link list
 */
function wp_link_pages_args_prevnext_add($args)
{
    global $page, $numpages, $more, $pagenow;

    if ($args['next_or_number'] !== 'next_and_number') 
        return $args; # exit early

    $args['next_or_number'] = 'number'; # keep numbering for the main part
    if (!$more)
        return $args; # exit early

   /* if ($page - 1) # there is a previous page
        $args['before'] .= _wp_link_page($page-1)
            . $args['link_before']. $args['previouspagelink'] . $args['link_after'] . '</a>'
        ; */

    if ($page < $numpages) # there is a next page
        $args['after'] = _wp_link_page($page+1)
            . $args['link_before'] . ' ' . $args['nextpagelink'] . $args['link_after'] . '</a>'
            . $args['after']
        ;

    return $args;
}

add_filter('wp_link_pages_args', 'wp_link_pages_args_prevnext_add');


// Put JavaScript into Template
/* function enable_ajax_functionality() {
  wp_localize_script( 'ajaxify', 'ajaxify_function', array('ajaxurl' => admin_url('admin-ajax.php')) );
}
add_action('template_redirect', 'enable_ajax_functionality');

// Add two actions to wp_ajax, where we get posts array back as json
function test_ajax() {
  header( "Content-Type: application/json");
  $posts_array = get_posts();
  echo json_encode($posts_array);
  die();
}
add_action("wp_ajax_nopriv_test_ajax", "test_ajax");
add_action("wp_ajax_test_ajax", "test_ajax");


function enqueue_infinite_scroll_script() {
    wp_enqueue_script( 'infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.js', array('jquery'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_infinite_scroll_script' ); */






 if (get_theme_mod('activate_banner_middle_article_parallax_setting') == 'aktif') {

function insert_sidebar_after_third_paragraph( $content ) {
    // Check if we're inside a single post
    if ( is_single() ) {
        // Find the third paragraph
        $paragraphs = explode( '</p>', $content );
        if ( count( $paragraphs ) > 2 ) {
            $new_content = '';
            // Insert the sidebar after the third paragraph
            for ( $i = 0; $i < count( $paragraphs ); $i++ ) {
                $new_content .= $paragraphs[$i] . '</p>';
                if ( $i == 2 ) {
                    $new_content .= '<div class="ad-middle"><p class="ad-title">ADVERTISEMENT</p><div class="ad-content"><div class="ad-content-a"><div class="ad-content-b"><div class="ad-content-c">'.get_theme_mod('banner_setting').'</div></div></div></div><p class="scroll-to-resume">SCROLL TO RESUME CONTENT</p></div>';
                }
            }
            $content = $new_content;
        }
    }
    return $content;
}
add_filter( 'the_content', 'insert_sidebar_after_third_paragraph' );

 };

// add text box in customizer

add_action( 'customize_register', 'nomina_customizer' );

function nomina_customizer( $wp_customize ) {
    


// add section Media Sosial
    $wp_customize->add_section( 'nomina-section' , array(
    'title'      => __( 'Media Sosial', 'nomina' ),
    'priority'   => 30,
    'panel'             => 'depan_id'
) );

// add setting facebook
    $wp_customize->add_setting( 'facebook-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://facebook.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control facebook
$wp_customize->add_control( 'facebook-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Facebook', 'nomina'  )
) );
    
// add setting twitter
$wp_customize->add_setting( 'twitter-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://twitter.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control twitter
$wp_customize->add_control( 'twitter-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Twitter', 'nomina'  )
) );
    
// add setting instagram
$wp_customize->add_setting( 'instagram-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://instagram.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control instagram
$wp_customize->add_control( 'instagram-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Instagram', 'nomina'  )
) );
    
// add setting youtube
$wp_customize->add_setting( 'youtube-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://youtube.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control youtube
$wp_customize->add_control( 'youtube-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Youtube', 'nomina'  )
) );
    
// add setting tiktok
$wp_customize->add_setting( 'tiktok-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://tiktok.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control tiktok
$wp_customize->add_control( 'tiktok-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Tiktok', 'nomina'  )
) );

// add setting linkedin
$wp_customize->add_setting( 'linkedin-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://linkedin.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control linkedin
$wp_customize->add_control( 'linkedin-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Linkedin', 'nomina'  )
) );


// add setting pinterest
$wp_customize->add_setting( 'pinterest-id', array(
  'capability' => 'edit_theme_options',
  'default' => 'https://pinterest.com',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control pinterest
$wp_customize->add_control( 'pinterest-id', array(
  'type' => 'text',
  'section' => 'nomina-section', 
  'label' => __( 'URL Pinterest', 'nomina'  )
) );
    
// Create panel Halaman Depan

$wp_customize->add_panel( 'depan_id', array(
    'title'          => 'Nomina Setting',
    'priority'       => 20,
) );

// Create section script
    
  $wp_customize->add_section( 'script_id', array(
        'title' => __( 'Script Tambahan', 'nomina' ),
        'priority' => 90,
      'panel'             => 'depan_id',
    ) );


// add setting script
    
$wp_customize->add_setting( 'script_header_setting', array(
    'capability' => 'edit_theme_options',
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );

// add control script
    
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'script_header_control', array(
        'label' => __( 'Script Header', 'nomina' ),
        'section' => 'script_id',
        'settings' => 'script_header_setting',
        'type' => 'textarea',
            'description' => __( 'Masukkan kode script anda disini, misal script dari Google Adsense atau yang lain', 'nomina'  ),
    ) ) );

    
    

    
    
// Create section banner middle
    
  $wp_customize->add_section( 'banner_id', array(
        'title' => __( 'Banner Parallax', 'nomina' ),
        'priority' => 80,
      'panel'             => 'depan_id',
    ) );



// Create settings activate_banner_header_parallax
$wp_customize->add_setting( 'activate_banner_header_parallax_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control activate_banner_header_parallax
$wp_customize->add_control( 'activate_banner_header_parallax_control', array(
    'label'      => 'Aktifkan Banner Mobile Top Header Parallax',
    'section'    => 'banner_id',
    'settings'   => 'activate_banner_header_parallax_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'aktif' => 'Aktifkan',
          'nonakif' => 'Nonaktifkan',
        ),
) );

    
// Create settings activate_banner_middle_article_parallax
$wp_customize->add_setting( 'activate_banner_middle_article_parallax_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control activate_banner_middle_article_parallax
$wp_customize->add_control( 'activate_banner_middle_article_parallax_control', array(
    'label'      => 'Aktifkan Banner Middle Article Parallax',
    'section'    => 'banner_id',
    'settings'   => 'activate_banner_middle_article_parallax_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'aktif' => 'Aktifkan',
          'nonakif' => 'Nonaktifkan',
        ),
) );
    
// add setting script
    
$wp_customize->add_setting( 'banner_setting', array(
    'capability' => 'edit_theme_options',
        'default' => '',
    'sanitize_callback' => '',
       //'sanitize_callback' => 'sanitize_textarea_field',
    ) );

// add control script
    
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'banner_control', array(
        'label' => __( 'Banner Middle Article Parallax', 'nomina' ),
        'section' => 'banner_id',
        'settings' => 'banner_setting',
        'type' => 'textarea',
            'description' => __( 'Masukkan kode script banner anda disini atau bila berupa gambar langsung, anda bisa copy kode html ini dan mengganti link sumber/src gambarnya, &lt;img src=&quot;https://demo.baturetnostudio.com/nomina/wp-content/uploads/2023/03/230220-alfagift-3-480x600-1.jpg&quot; alt=&quot;ads&quot; /&gt;. Ukuran gambar 480px x 600px ', 'nomina'  ),
    ) ) );

    
    
// Sanitize text
    function sanitize_text( $text ) {
        return sanitize_text_field( $text );
    }
    
    
// Create section alamat
    
  $wp_customize->add_section( 'alamat_id', array(
        'title' => __( 'Footer', 'nomina' ),
        'priority' => 30,
      'panel'             => 'depan_id',
    ) );


     $wp_customize->add_setting( 'diwp_logo', array(
        'default' => get_theme_file_uri('/img/nomina-logo.webp'), // Add Default Image URL 
        'sanitize_callback' => 'esc_url_raw'
    ));
 
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'diwp_logo_control', array(
        'label' => 'Upload Logo Footer',
        'priority' => 20,
        'section' => 'alamat_id',
        'settings' => 'diwp_logo',
        'description' => __( 'Ukuran logo footer adalah 320px x 62px', 'nomina'  ),
        'button_labels' => array(// All These labels are optional
                    'select' => 'Select Logo',
                    'remove' => 'Remove Logo',
                    'change' => 'Change Logo',
                    )
    )));
    
    
// add setting script
    
$wp_customize->add_setting( 'alamat_setting', array(
    'capability' => 'edit_theme_options',
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ) );

// add control script
    
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'alamat_control', array(
        'label' => __( 'Alamat Redaksi atau Tentang Kami', 'nomina' ),
        'section' => 'alamat_id',
        'settings' => 'alamat_setting',
        'type' => 'textarea',
            'description' => __( 'Masukkan alamat kantor redaksi anda disini atau deskripsi singkat tentang website anda disini. Anda bisa menggunakan &lt;br&gt; untuk membuat baris baru', 'nomina'  ),
    ) ) );
    
    
    
    
// Create our panels

/* $wp_customize->add_panel( 'depan_id', array(
    'title'          => 'Halaman Depan',
) ); */
    
// add section Homepage
/* $wp_customize->add_section( 'homepage', 
 array(
    'title'       => __( 'Homepage', 'nomina' ), 
    'priority'    => 20,
    'capability'  => 'edit_theme_options', 
 ) 
); */

// FONT UTAMA

$wp_customize->add_section( 'font_section' , array(
        'title'      => __( 'Fonts', 'nomina' ),
        'priority'   => 20,
        'panel' => 'depan_id',
    ) );

$wp_customize->add_setting( 'font_setting', array(
        'default'   => 'Montserrat',
        'transport' => 'refresh',
      'capability' => 'edit_theme_options',
  'sanitize_callback' => 'wp_filter_nohtml_kses',
    ) );


$wp_customize->add_control( 'font_control', array(
        'label'    => __( 'Font Utama', 'nomina' ),
        'section'  => 'font_section',
        'settings' => 'font_setting',
        'type'     => 'select',
        'choices'  => array(
            'Arial' => 'Arial',
            'Alegreya' => 'Alegreya',
            'Archivo' => 'Archivo',
            'Arvo' => 'Arvo',
            'Barlow' => 'Barlow',
            'Barlow Condensed' => 'Barlow Condensed',
            'Belleza' => 'Belleza',
            'Bitter' => 'Bitter',
            'Cardo' => 'Cardo',
            'Chivo' => 'Chivo',
            'Crimson Text' => 'Crimson Text',
            'Cormorant' => 'Cormorant',
            'Cormorant Garamond' => 'Cormorant Garamond',
            'Dosis' => 'Dosis',
            'DM Sans' => 'DM Sans',
            'DM Serif Display' => 'DM Serif Display',
            'EB Garamond' => 'EB Garamond',
            'Epilogue' => 'Epilogue',
            'Exo' => 'Exo',
            'Exo 2' => 'Exo 2',
            'Fira Sans' => 'Fira Sans',
            'Fjalla One' => 'Fjalla One',
            'Forum' => 'Forum',
            'Heebo' => 'Heebo',
            'Headland One' => 'Headland One',
            'Hind' => 'Hind',
            'Hind Guntur' => 'Hind Guntur',
            'IBM Plex Sans' => 'IBM Plex Sans',
            'IBM Plex Sans Condensed' => 'IBM Plex Sans Condensed',
            'Inter' => 'Inter',
            'Istok Web' => 'Istok Web',
            'Karla' => 'Karla',
            'Lato' => 'Lato',
            'Libre Baskerville' => 'Libre Baskerville',
            'Lora' => 'Lora',
            'Manrope' => 'Manrope',
            'Mate' => 'Mate',
            'Merriweather' => 'Merriweather',
            'Montserrat' => 'Montserrat (Default)',
            'Mukta' => 'Mukta',
            'Muli' => 'Muli',
            'Mulish' => 'Mulish',
            'Nobile' => 'Nobile',
            'Noto Sans' => 'Noto Sans',
            'Noto Serif' => 'Noto Serif',
            'Nunito' => 'Nunito',
            'Nunito Sans' => 'Nunito Sans',
            'Old Standard TT' => 'Old Standard TT',
            'Open Sans' => 'Open Sans',
            'Oswald' => 'Oswald',
            'Outfit' => 'Outfit',
            'Overpass' => 'Overpass',
            'Philosopher' => 'Philosopher',
            'Playfair Display' => 'Playfair Display',
            'Poiret One' => 'Poiret One',
            'Poppins' => 'Poppins',
            'PT Sans' => 'PT Sans',
            'PT Serif' => 'PT Serif',
            'Quattrocento' => 'Quattrocento',
            'Quicksand' => 'Quicksand',
            'Raleway' => 'Raleway',
            'Red Hat Display' => 'Red Hat Display',
            'Roboto' => 'Roboto',
            'Roboto Condensed' => 'Roboto Condensed',
            'Roboto Flex' => 'Roboto Flex',
            'Roboto Serif' => 'Roboto Serif',
            'Roboto Slab' => 'Roboto Slab',
            'Rubik' => 'Rubik',
            'Space Grotesk' => 'Space Grotesk',
            'Spline Sans' => 'Spline Sans',
            'Sora' => 'Sora',
            'Source Serif Pro' => 'Source Serif Pro',
            'Tenor Sans' => 'Tenor Sans',
            'Titillium Web' => 'Titillium Web',
            'Ubuntu' => 'Ubuntu',
            'Vidaloka' => 'Vidaloka',
            'Vollkorn' => 'Vollkorn',
            'Work Sans' => 'Work Sans',
        ),
    ) );

// FONT PARAGRAPH

$wp_customize->add_setting( 'font_paragraph', array(
        'default'   => 'Arial',
        'transport' => 'refresh',
      'capability' => 'edit_theme_options',
  'sanitize_callback' => 'wp_filter_nohtml_kses',
    ) );


$wp_customize->add_control( 'font_control_paragraph', array(
        'label'    => __( 'Font Paragraph', 'nomina' ),
        'section'  => 'font_section',
        'settings' => 'font_paragraph',
        'type'     => 'select',
        'choices'  => array(
            'Arial' => 'Arial (Default)',
            'Alegreya' => 'Alegreya',
            'Archivo' => 'Archivo',
            'Arvo' => 'Arvo',
            'Barlow' => 'Barlow',
            'Barlow Condensed' => 'Barlow Condensed',
            'Belleza' => 'Belleza',
            'Bitter' => 'Bitter',
            'Cardo' => 'Cardo',
            'Chivo' => 'Chivo',
            'Crimson Text' => 'Crimson Text',
            'Cormorant' => 'Cormorant',
            'Cormorant Garamond' => 'Cormorant Garamond',
            'Dosis' => 'Dosis',
            'DM Sans' => 'DM Sans',
            'DM Serif Display' => 'DM Serif Display',
            'EB Garamond' => 'EB Garamond',
            'Epilogue' => 'Epilogue',
            'Exo' => 'Exo',
            'Exo 2' => 'Exo 2',
            'Fira Sans' => 'Fira Sans',
            'Fjalla One' => 'Fjalla One',
            'Forum' => 'Forum',
            'Heebo' => 'Heebo',
            'Headland One' => 'Headland One',
            'Hind' => 'Hind',
            'Hind Guntur' => 'Hind Guntur',
            'IBM Plex Sans' => 'IBM Plex Sans',
            'IBM Plex Sans Condensed' => 'IBM Plex Sans Condensed',
            'Inter' => 'Inter',
            'Istok Web' => 'Istok Web',
            'Karla' => 'Karla',
            'Lato' => 'Lato',
            'Libre Baskerville' => 'Libre Baskerville',
            'Lora' => 'Lora',
            'Manrope' => 'Manrope',
            'Mate' => 'Mate',
            'Merriweather' => 'Merriweather',
            'Montserrat' => 'Montserrat',
            'Mukta' => 'Mukta',
            'Muli' => 'Muli',
            'Mulish' => 'Mulish',
            'Nobile' => 'Nobile',
            'Noto Sans' => 'Noto Sans',
            'Noto Serif' => 'Noto Serif',
            'Nunito' => 'Nunito',
            'Nunito Sans' => 'Nunito Sans',
            'Old Standard TT' => 'Old Standard TT',
            'Open Sans' => 'Open Sans',
            'Oswald' => 'Oswald',
            'Outfit' => 'Outfit',
            'Overpass' => 'Overpass',
            'Philosopher' => 'Philosopher',
            'Playfair Display' => 'Playfair Display',
            'Poiret One' => 'Poiret One',
            'Poppins' => 'Poppins',
            'PT Sans' => 'PT Sans',
            'PT Serif' => 'PT Serif',
            'Quattrocento' => 'Quattrocento',
            'Quicksand' => 'Quicksand',
            'Raleway' => 'Raleway',
            'Red Hat Display' => 'Red Hat Display',
            'Roboto' => 'Roboto',
            'Roboto Condensed' => 'Roboto Condensed',
            'Roboto Flex' => 'Roboto Flex',
            'Roboto Serif' => 'Roboto Serif',
            'Roboto Slab' => 'Roboto Slab',
            'Rubik' => 'Rubik',
            'Space Grotesk' => 'Space Grotesk',
            'Spline Sans' => 'Spline Sans',
            'Sora' => 'Sora',
            'Source Serif Pro' => 'Source Serif Pro',
            'Tenor Sans' => 'Tenor Sans',
            'Titillium Web' => 'Titillium Web',
            'Ubuntu' => 'Ubuntu',
            'Vidaloka' => 'Vidaloka',
            'Vollkorn' => 'Vollkorn',
            'Work Sans' => 'Work Sans',
        ),
    ) );

// FONT SIZE PARAGRAPH

$wp_customize->add_setting( 'font_size_paragraph', array(
        'default'   => '16px',
        'capability' => 'edit_theme_options',
        'transport' => 'refresh',
         'sanitize_callback' => 'wp_filter_nohtml_kses',
    ) );

    $wp_customize->add_control( 'font_size_control_paragraph', array(
        'label'    => __( 'Font Size Paragraph', 'nomina' ),
        'section'  => 'font_section',
        'settings' => 'font_size_paragraph',
        'type'     => 'select',
        'choices'  => array(
            '10px' => esc_html('10px'),
            '11px' => esc_html('11px'),
            '12px' => esc_html('12px'),
            '13px' => esc_html('13px'),
            '14px' => esc_html('14px'),
            '15px' => esc_html('15px'),
            '16px' => esc_html('16px (Default)'),
            '17px' => esc_html('17px'),
            '18px' => esc_html('18px'),
            '19px' => esc_html('19px'),
            '20px' => esc_html('20px'),
            '21px' => esc_html('21px'),
            '22px' => esc_html('22px'),
            '23px' => esc_html('23px'),
            '24px' => esc_html('24px'),
            '25px' => esc_html('25px'),
            '26px' => esc_html('26px'),
            '27px' => esc_html('27px'),
            '28px' => esc_html('28px'),
        ),
    ) );

// FONT SIZE MENU
$wp_customize->add_setting( 'font_size_menu', array(
        'default'   => '12px',
        'capability' => 'edit_theme_options',
        'transport' => 'refresh',
         'sanitize_callback' => 'wp_filter_nohtml_kses',
    ) );

    $wp_customize->add_control( 'font_size_control_menu', array(
        'label'    => __( 'Font Size Menu', 'nomina' ),
        'section'  => 'font_section',
        'settings' => 'font_size_menu',
        'type'     => 'select',
        'choices'  => array(
            '10px' => esc_html('10px'),
            '11px' => esc_html('11px'),
            '12px' => esc_html('12px (Default)'),
            '13px' => esc_html('13px'),
            '14px' => esc_html('14px'),
            '15px' => esc_html('15px'),
            '16px' => esc_html('16px'),
            '17px' => esc_html('17px'),
            '18px' => esc_html('18px'),
            '19px' => esc_html('19px'),
            '20px' => esc_html('20px'),
            '21px' => esc_html('21px'),
            '22px' => esc_html('22px'),
            '23px' => esc_html('23px'),
            '24px' => esc_html('24px'),
            '25px' => esc_html('25px'),
            '26px' => esc_html('26px'),
            '27px' => esc_html('27px'),
            '28px' => esc_html('28px'),
        ),
    ) );
    
// Create settings align_judul_berita
$wp_customize->add_setting( 'align_judul_berita_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control align_judul_berita
$wp_customize->add_control( 'align_judul_berita_control', array(
    'label'      => 'Perataan Judul Berita Single',
    'section'    => 'font_section',
    'settings'   => 'align_judul_berita_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'left' => 'Rata Kiri',
          'center' => 'Rata Tengah',
        ),
) );

// Create settings align_breadcrumb
$wp_customize->add_setting( 'align_breadcrumb_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control align_breadcrumb
$wp_customize->add_control( 'align_breadcrumb_control', array(
    'label'      => 'Perataan Breadcrumb Single',
    'section'    => 'font_section',
    'settings'   => 'align_breadcrumb_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'left' => 'Rata Kiri',
          'center' => 'Rata Tengah',
        ),
) );


// Create settings align_menu_utama
$wp_customize->add_setting( 'align_menu_utama_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control align_breadcrumb
$wp_customize->add_control( 'align_menu_utama_control', array(
    'label'      => 'Perataan Menu Utama',
    'section'    => 'font_section',
    'settings'   => 'align_menu_utama_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'left' => 'Rata Kiri',
          'center' => 'Rata Tengah',
        ),
) );
    

// Create settings foto caption
$wp_customize->add_setting( 'foto_caption_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control foto caption
$wp_customize->add_control( 'foto_caption_control', array(
    'label'      => 'Caption Foto Berita',
    'section'    => 'font_section',
    'settings'   => 'foto_caption_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'Buka Tutup' => 'Buka Tutup',
          'Di Bawah Foto' => 'Di Bawah Foto',
        ),
) );

    
    // Create section Headline Mobile
$wp_customize->add_section( 'headlinemobile_id' , array(
    'title'             => 'Headline Slider Mobile',
    'panel'             => 'depan_id',
) );

// Create settings Headline Mobile
$wp_customize->add_setting( 'headlinemobile' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );


// Create control Headline Mobile
$wp_customize->add_control( 'headlinemobile', array(
    'label'      => 'Headline Mobile',
    'section'    => 'headlinemobile_id',
    'settings'   => 'headlinemobile',
    'type'       => 'radio',
        'choices'    => array( 
          'model1' => 'Model 1',
          'model2' => 'Model 2',
        'model3' => 'Model 3',
        ),
) );
    


// Create control Arrow Menu Button
$wp_customize->add_control( 'arrowbutton', array(
    'label'      => 'Hide Arrow Primary Menu Button Icon',
    'section'    => 'arrowid',
    'settings'   => 'arrowbutton',
    'type'       => 'radio',
        'choices'    => array( 
          'show' => 'Show',
          'hide' => 'Hide',
        ),
) );
    
// Create section waktu indonesia
$wp_customize->add_section( 'waktuid' , array(
    'title'             => 'Zona Waktu Indonesia',
    'panel'             => 'depan_id',
) );
    

// Create settings waktu indonesia
$wp_customize->add_setting( 'zonawaktu' , array(
      'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh'
) );
    
    


// add control waktu indonesia
$wp_customize->add_control( 'zonawaktu', array(
  'type' => 'select',
  'section' => 'waktuid',
  'label' => esc_html__( 'Zona Waktu Indonesia', 'nomina' ),
  'choices' => array(
    'WIB' => esc_html__( 'WIB', 'nomina'  ),
    'WITA' => esc_html__( 'WITA', 'nomina'  ),
    'WIT' => esc_html__( 'WIT', 'nomina'  ),
   '' => esc_html__( 'Kosong', 'nomina'  ),
  )
) );
        
    
    
// Create section Ganti istilah writer pada author
$wp_customize->add_section( 'writerid' , array(
    'title'             => 'Ganti istilah',
    'panel'             => 'depan_id',
) );

// Create settings Ganti istilah writer
$wp_customize->add_setting( 'writer' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );


// add control Ganti istilah writer
$wp_customize->add_control( 'writer', array(
  'type' => 'select',
  'section' => 'writerid',
  'label' => __( 'Ganti Istilah "Writer" pada Author dengan yang lain', 'nomina'  ),
  'choices' => array(
    '' => esc_html__( 'Kosong', 'nomina'  ),  
    'Penulis' => esc_html__( 'Penulis', 'nomina'  ),
      'Penulis Berita' => esc_html__( 'Penulis Berita', 'nomina'  ),
    'Reporter' => esc_html__( 'Reporter', 'nomina'  ),
    'Writer' => esc_html__( 'Writer', 'nomina'  ),
    'Redaksi' => esc_html__( 'Redaksi', 'nomina'  ),
    'Redaktur' => esc_html__( 'Redaktur', 'nomina'  ),
      'Team' => esc_html__( 'Team', 'nomina'  ),
      'Tim' => esc_html__( 'Tim', 'nomina'  ),
      'Editor' => esc_html__( 'Editor', 'nomina'  ),
      'Editorial Team' => esc_html__( 'Editorial Team', 'nomina'  ),
      'Editorial Staff' => esc_html__( 'Editorial Staff', 'nomina'  ),
      'Author' => esc_html__( 'Author', 'nomina'  ),
      'Co-Author' => esc_html__( 'Co-Author', 'nomina'  ),
      'Kontributor' => esc_html__( 'Kontributor', 'nomina'  ),
      'Wartawan' => esc_html__( 'Wartawan', 'nomina'  ),
      'Jurnalis' => esc_html__( 'Jurnalis', 'nomina'  ),
      'Pewarta' => esc_html__( 'Pewarta', 'nomina'  ),
      'Publisher' => esc_html__( 'Publisher', 'nomina'  ),
      'Admin' => esc_html__( 'Admin', 'nomina'  ),
      'Administrator' => esc_html__( 'Administrator', 'nomina'  ),
      'Tim Kreatif' => esc_html__( 'Tim Kreatif', 'nomina'  ),
      'Freelancer' => esc_html__( 'Freelancer', 'nomina'  ),
      'Staff' => esc_html__( 'Staff', 'nomina'  )
  )
) );
    
    // Create settings Ganti istilah Berita Terkini
$wp_customize->add_setting( 'berita-terkini-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Berita Terkini
$wp_customize->add_control( 'berita-terkini-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'berita-terkini-setting',
  'label' => __( 'Berita Terkini', 'nomina'  ),
    'description' => __( 'Ganti istilah "Berita Terkini" dengan istilah yang lain', 'nomina'  )
) );    
    
    // Create settings Ganti istilah Berita pada halaman Category
$wp_customize->add_setting( 'berita-category-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Berita pada halaman Category
$wp_customize->add_control( 'berita-category-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'berita-category-setting',
  'label' => __( 'Berita', 'nomina'  ),
    'description' => __( 'Ganti istilah "Berita" pada halaman Category dengan istilah yang lain', 'nomina'  )
) );    
    
    // Create settings Ganti istilah Topik pada halaman Tag
$wp_customize->add_setting( 'topik-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Topik pada halaman Tag
$wp_customize->add_control( 'topik-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'topik-setting',
  'label' => __( 'Topik', 'nomina'  ),
    'description' => __( 'Ganti istilah "Topik" pada halaman Tag dengan istilah yang lain', 'nomina'  )
) );
    
    // Create settings Ganti istilah Berita Terbaru pada halaman single
$wp_customize->add_setting( 'berita-terbaru-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Berita Terbaru pada halaman single
$wp_customize->add_control( 'berita-terbaru-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'berita-terbaru-setting',
  'label' => __( 'Berita Terbaru', 'nomina'  ),
    'description' => __( 'Ganti istilah "Berita Terbaru" pada halaman single dengan istilah yang lain', 'nomina'  )
) );    
    
    // Create settings Ganti istilah Berita Terkait pada halaman single
$wp_customize->add_setting( 'berita-terkait-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Berita Terkait pada halaman single
$wp_customize->add_control( 'berita-terkait-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'berita-terkait-setting',
  'label' => __( 'Berita Terkait', 'nomina'  ),
    'description' => __( 'Ganti istilah "Berita Terkait" pada halaman single dengan istilah yang lain', 'nomina'  )
) );    
    
// Create settings Ganti istilah Cari berita pada kolom pencarian
$wp_customize->add_setting( 'cari-berita-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Cari berita pada kolom pencarian
$wp_customize->add_control( 'cari-berita-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'cari-berita-setting',
  'label' => __( 'Cari Berita', 'nomina'  ),
    'description' => __( 'Ganti istilah "Cari Berita" pada kolom pencarian dengan istilah yang lain', 'nomina'  )
) );        
    

// Create settings Ganti istilah Headline pada Headline Slider
$wp_customize->add_setting( 'headline-istilah-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Headline pada Headline Slider
$wp_customize->add_control( 'headline-istilah-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'headline-istilah-setting',
  'label' => __( 'Headline', 'nomina'  ),
    'description' => __( 'Ganti istilah "Headline" pada Headline Slider dengan istilah yang lain', 'nomina'  )
) );    
    
// Create settings Ganti istilah Media Network pada footer
$wp_customize->add_setting( 'media-network-setting' , array(
    'capability' => 'edit_theme_options',
    // 'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'sanitize_callback' => 'sanitize_text_field',
) );
    
    // add control Ganti istilah Media Network pada footer
$wp_customize->add_control( 'media-network-control', array(
  'type' => 'text',
  'section' => 'writerid', 
    'settings'   => 'media-network-setting',
  'label' => __( 'Media Network', 'nomina'  ),
    'description' => __( 'Ganti istilah "Media Network" pada footer dengan istilah yang lain', 'nomina'  )
) );
    
    // Create section jumlah pembaca
$wp_customize->add_section( 'jumlahpembaca_id' , array(
    'title'             => 'Hide Jumlah Pembaca Post Single',
    'panel'             => 'depan_id',
) );

// Create settings jumlah pembaca
$wp_customize->add_setting( 'jumlahpembaca' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );


// Create control Jumlah Pembaca
$wp_customize->add_control( 'jumlahpembaca', array(
    'label'      => 'Hide Jumlah Pembaca di Post Single',
    'section'    => 'jumlahpembaca_id',
    'settings'   => 'jumlahpembaca',
    'type'       => 'radio',
        'choices'    => array( 
          'hide' => 'Hide',
          'show' => 'Show',
        ),
) );
    
    

    
    
    
// Create section foto penulis berita
$wp_customize->add_section( 'fotopenulis_id' , array(
    'title'             => 'Hide Foto Penulis Berita',
    'panel'             => 'depan_id',
) );

// Create settings foto penulis berita
$wp_customize->add_setting( 'fotopenulis' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );


// Create control foto penulis berita
$wp_customize->add_control( 'fotopenulis', array(
    'label'      => 'Hide Foto Penulis Berita',
    'section'    => 'fotopenulis_id',
    'settings'   => 'fotopenulis',
    'type'       => 'radio',
        'choices'    => array( 
          'hide' => 'Hide',
          'show' => 'Show',
        ),
) );


// Create section waktu post berita
$wp_customize->add_section( 'waktu_post_id' , array(
    'title'             => 'Hide Waktu Post',
    'panel'             => 'depan_id',
) );

// Create settings waktu post berita
$wp_customize->add_setting( 'waktupostberita' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );


// Create control waktu post berita
$wp_customize->add_control( 'waktupostberita', array(
    'label'      => 'Hide Waktu Post di Single',
    'section'    => 'waktu_post_id',
    'settings'   => 'waktupostberita',
    'type'       => 'radio',
        'choices'    => array( 
          'hide' => 'Hide',
          'show' => 'Show',
        ),
) );
    
    
  // add section Block Slider
    $wp_customize->add_section( 'block-slider-section' , array(
    'title'      => __( 'Block Slider', 'nomina' ),
    'priority'   => 100,
    'panel'             => 'depan_id'
) );
    
    // Create settings activate_breaking_news
$wp_customize->add_setting( 'activate_breaking_news_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// Create control activate_breaking_news
$wp_customize->add_control( 'activate_breaking_news_control', array(
    'label'      => 'Aktifkan Breaking News',
    'section'    => 'block-slider-section',
    'settings'   => 'activate_breaking_news_setting',
    'type'       => 'radio',
        'choices'    => array( 
          'aktif' => 'Aktifkan',
          'nonakif' => 'Nonaktifkan',
        ),
    'description' => __( 'Setelah Diaktifkan, silakan beri tag "Breaking News" pada post beritanya untuk menampilkan Breaking News / Running Text', 'nomina'  )
) );
    
    // add settings slider headline
$wp_customize->add_setting( 'slider_headline_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
) );
    
// add control slider headline
$wp_customize->add_control( 'slider_headline_control', array(
    'label'      => 'Slider Headline',
    'section'    => 'block-slider-section',
    'settings'   => 'slider_headline_setting',
    'type'       => 'radio',
        'choices'    => array( 
          '' => 'dari Post-Post Terbaru',
          'Headline' => 'dari Tag Headline',
        ),
) );

    
    // add setting Block Slider 1
    $wp_customize->add_setting( 'block-slider-1-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 1
$wp_customize->add_control( 'block-slider-1-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-1-setting',
  'label' => __( 'Block Slider 1', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 1', 'nomina'  )
) );
    
    // add setting Block Slider 2
    $wp_customize->add_setting( 'block-slider-2-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 2
$wp_customize->add_control( 'block-slider-2-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-2-setting',
  'label' => __( 'Block Slider 2', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 2', 'nomina'  )
) );

// add setting Block Slider 3
    $wp_customize->add_setting( 'block-slider-3-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 3
$wp_customize->add_control( 'block-slider-3-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-3-setting',
  'label' => __( 'Block Slider 3', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 3', 'nomina'  )
) );
    
// add setting Block Slider 4
    $wp_customize->add_setting( 'block-slider-4-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 4
$wp_customize->add_control( 'block-slider-4-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-4-setting',
  'label' => __( 'Block Slider 4', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 4', 'nomina'  )
) );
    
// add setting Block Slider 5
    $wp_customize->add_setting( 'block-slider-5-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 5
$wp_customize->add_control( 'block-slider-5-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-5-setting',
  'label' => __( 'Block Slider 5', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 5', 'nomina'  )
) );

// add setting Block Slider 6
    $wp_customize->add_setting( 'block-slider-6-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 6
$wp_customize->add_control( 'block-slider-6-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-6-setting',
  'label' => __( 'Block Slider 6', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 6', 'nomina'  )
) );
    
// add setting Block Slider 7
    $wp_customize->add_setting( 'block-slider-7-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 7
$wp_customize->add_control( 'block-slider-7-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-7-setting',
  'label' => __( 'Block Slider 7', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 7', 'nomina'  )
) );

// add setting Block Slider 8
    $wp_customize->add_setting( 'block-slider-8-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 8
$wp_customize->add_control( 'block-slider-8-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-8-setting',
  'label' => __( 'Block Slider 8', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 8', 'nomina'  )
) );

// add setting Block Slider 9
    $wp_customize->add_setting( 'block-slider-9-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 9
$wp_customize->add_control( 'block-slider-9-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-9-setting',
  'label' => __( 'Block Slider 9', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 9', 'nomina'  )
) );

// add setting Block Slider 10
    $wp_customize->add_setting( 'block-slider-10-setting', array(
  'capability' => 'edit_theme_options',
        'type'          => 'theme_mod',
            'transport'     => 'refresh',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control Block Slider 10
$wp_customize->add_control( 'block-slider-10-control', array(
  'type' => 'text',
  'section' => 'block-slider-section', 
    'settings'   => 'block-slider-10-setting',
  'label' => __( 'Block Slider 10', 'nomina'  ),
    'description' => __( 'Tulis nama kategori untuk Block Slider 10', 'nomina'  )
) );

    



    
    // add setting copyright
$wp_customize->add_setting( 'copyright', array(
  'capability' => 'edit_theme_options',
  'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );

// add control copyright
$wp_customize->add_control( 'copyright', array(
  'type' => 'text',
  'section' => 'alamat_id', 
  'label' => __( 'Copyright Footer Text',  'nomina' ),
    'description' => __( 'Tulis Copyright Footer Text', 'nomina'  ),
) );
    
    
    $wp_customize->add_section( 'color_scheme' , array(
    'title'             => 'Color Scheme',
    'panel'             => 'depan_id',
     'priority'   => 40,
) );
    
    $wp_customize->add_setting( 'color_scheme_setting' , array(
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
        'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
) );



$wp_customize->add_control( 'color_scheme_control', array(
    'label'      => 'Ganti Warna Theme',
    'section'    => 'color_scheme',
    'settings'   => 'color_scheme_setting',
    'type'       => 'radio',
        'choices'    => array(
            'Default' => 'Default',
            'Gradasi Biru' => 'Gradasi Biru',
            'Gradasi Merah Orange' => 'Gradasi Merah Orange',
            'Gradasi Hijau' => 'Gradasi Hijau',
            'Gradasi Ungu' => 'Gradasi Ungu',
            'Gradasi Merah Ungu' => 'Gradasi Merah Ungu',
            'Gradasi Biru Ungu' => 'Gradasi Biru Ungu',
            'Gradasi Orange' => 'Gradasi Orange',
            'Gradasi Hitam' => 'Gradasi Hitam',
            'Hitam Pekat' => 'Hitam Pekat',
            'Gradasi Maroon' => 'Gradasi Maroon',
            'Gradasi Biru 2' => 'Gradasi Biru 2',
            'Gradasi Orange Merah Biru' => 'Gradasi Orange Merah Biru',
            'Gradasi Biru Gelap' => 'Gradasi Biru Gelap',
            'Gradasi Hijau Kuning' => 'Gradasi Hijau Kuning',
            'Gradasi Hijau Tua' => 'Gradasi Hijau Tua',
            'Gradasi Biru Ungu Merah' => 'Gradasi Biru Ungu Merah',
            'Gradasi Biru Ungu Merah 2' => 'Gradasi Biru Ungu Merah 2',
            'Gradasi Biru 3' => 'Gradasi Biru 3',
            'Gradasi Merah Hitam' => 'Gradasi Merah Hitam',
          'Gradasi Coklat' => 'Gradasi Coklat',
            'Putih' => 'Putih',
            'Biru Dongker' => 'Biru Dongker',
            'Hijau' => 'Hijau',
            'Merah Abu Abu' => 'Merah Abu Abu',
            'Biru Hijau' => 'Biru Hijau',
            'Biru Hitam' => 'Biru Hitam',
            'Hijau Abu Abu' => 'Hijau Abu Abu',
            'Teal Orange' => 'Teal Orange',
        ),
) );
    
    

// Create section Berlangganan
$wp_customize->add_section( 'berlangganan_section' , array(
    'title'             => 'Berlangganan',
    'panel'             => 'depan_id',
) );    
    
// Create settings Berlangganan Whatsapp Setting
$wp_customize->add_setting( 'berlangganan_whatsapp_setting' , array(
    'sanitize_callback' => 'wp_filter_nohtml_kses', //removes all HTML from content
    'type'          => 'theme_mod',
    'transport'     => 'refresh',
      'default' => '',
  'sanitize_callback' => 'sanitize_text_field',
) );    
    
// Create control Berlangganan Whatsapp Setting
$wp_customize->add_control( 'berlangganan_whatsapp_control', array(
    'label'      => 'Whatsapp Channel',
    'section'    => 'berlangganan_section',
    'settings'   => 'berlangganan_whatsapp_setting',
    'type'       => 'text',
   'label' => __( 'URL WhatsApp Channel', 'nomina'  ),
    'description' => __( 'Masukkan url whatsapp channel', 'nomina'  )
) );    






    
}



// akhir add text box in customizer

function calling_google_font_script(){
     // Getting the set font from options.
     $temp_font_name = get_theme_mod('font_setting');
    
    if(empty($temp_font_name)) {
        $temp_font_name = "Montserrat";
    } else {
        $temp_font_name = get_theme_mod('font_setting');
    };
     // Adding Font in Google URL
     $google_font = esc_url('https://fonts.googleapis.com/css2?family='. $temp_font_name.':ital,wght@0,400;0,700;1,400;1,700&display=swap');
     // Now Calling google font in website
     wp_enqueue_style( 'google-fonts', esc_url_raw($google_font), array(), null );
}

add_action('wp_enqueue_scripts','calling_google_font_script');


function calling_google_font_paragraph_script(){
     // Getting the set font from options.
     $temp_font_paragraph_name = get_theme_mod('font_paragraph');
    
    if(empty($temp_font_paragraph_name) OR $temp_font_paragraph_name == "Arial") {
        
    } else {
        $temp_font_paragraph_name = get_theme_mod('font_paragraph');
         // Adding Font in Google URL
     $google_font_paragraph = esc_url('https://fonts.googleapis.com/css2?family='. $temp_font_paragraph_name.':ital,wght@0,400;0,700;1,400;1,700&display=swap');
        
           // Now Calling google font in website
     wp_enqueue_style( 'google-fonts-paragraph', esc_url_raw($google_font_paragraph), array(), null );

    };
      
}

add_action('wp_enqueue_scripts','calling_google_font_paragraph_script');

function my_customize_css()
{
    ?>
         <style>
             .menu-utama > li > a, .headline-judul a, .news-feed-judul, .news-feed-judul-block, .news-feed-list .kategori, .judul-sidebar-right, .judul-sidebar-left, .judul-sidebar-single, .single-kategori a span, .judul-label-kategori span, .kategori-mobile, .category-kategori a, .wpp-list > li > a, .recent-post-widget a, .page-numbers, .menu-bawah li a, #category-content h2 a, .category-text-wrap, .judul-label-kategori, .category-kategori, .headline-label, .search-form .search-field, .recent-post-widget .waktu, .single-kategori a, #single-content h1, .tanggal-single, .related-post-wrap, .caption-photo, .tagname, .tagname a, .footer-copyright, .judul-berita-pilihan, .kategori-berita-pilihan, #berita-pilihan h2 a, #page-content h1, .judul-berita-rekomendasi, .kategori-berita-rekomendasi, #berita-rekomendasi h2, .mobile-berita-terbaru .berita-terbaru, .judul-berita-terbaru a, .mobile-kategori-berita-terbaru, .tanggal-berita-terbaru, .news-feed-text-block .tanggal, .menu-utama > li > ul.sub-menu > li a, .mobile-menu-kiri li a, .judul-headline, a.slider-kategori, a.judul-slider, .judul-thumbnail, .alamat, .before-widget, .before-widget select, .before-widget ul li a, .before-widget ul li, .nama-penulis, .desktop-berita-terbaru .berita-terbaru, .desktop-berita-terbaru-box p, .desktop-kategori-berita-terbaru, .error404 p, .headline-label-mobile-dua, h2.headline-judul-mobile-dua a, .wrap-text-headline-dua .tanggal, .judul-berita-pilihan, .before-widget h2, .judul-sidebar-right, .judul-sidebar-single, .marquee-baru a, .marquee-baru .inner a, .headline-tiga-text-wrap-mobile h2 a, .totalpembaca, h2.headline-judul-mobile a, .headline-label-mobile, .menu-network-wrap, .network-title, .menu-network-wrap a { 
    font-family: '<?php $fontutama = get_theme_mod( 'font_setting' ); 
if ( empty( $fontutama ) ) {
echo "Montserrat";
} else {
    echo esc_html(get_theme_mod('font_setting')); } ?>', sans-serif; }  
             
.single-article-text, .single-article-text p { 
    font-family: '<?php $fontparagraph = get_theme_mod( 'font_paragraph' ); 
if ( empty( $fontparagraph ) ) {
echo "Arial";
} else {
    echo esc_html(get_theme_mod('font_paragraph')); } ?>', sans-serif; 
    font-size: <?php $fontsizeparagraph = get_theme_mod( 'font_size_paragraph' ); 
if ( empty( $fontsizeparagraph ) ) {
echo "16px";
} else {
    echo esc_html(get_theme_mod('font_size_paragraph')); } ?>; }    
             
#single-content figcaption.wp-element-caption, .wp-caption, .caption-photo, .caption-photo-buka-tutup {
      font-family: '<?php $fontparagraph = get_theme_mod( 'font_paragraph' ); 
if ( empty( $fontparagraph ) ) {
echo "Arial";
} else {
    echo esc_html(get_theme_mod('font_paragraph')); } ?>', sans-serif; 
             }

        
                     
.menu-utama > li > a, .menu-bawah > li > a, .menu-utama > li > ul.sub-menu > li a { 
    font-size: 
    <?php $fontsizemenu = get_theme_mod( 'font_size_menu' );
    if ( empty( $fontsizemenu ) ) {
echo "12px";
} else {
    echo esc_html(get_theme_mod('font_size_menu')); } ?>; }
             
             
<?php if(get_theme_mod('align_menu_utama_setting') == 'center') { ?>
.menu-utama { text-align: center; }
        <?php    } else { ?>
                 .menu-utama { text-align: left; }
        <?php    } ?>
             
             <?php if(get_theme_mod('align_judul_berita_setting') == 'center') { ?>
             #single-content h1 { text-align: center; }
        @media only screen and (max-width: 750px) {
            .media-sosial-mobile {
                text-align: center;
            } 
            #single-content .container-single-meta {
                justify-content: center;
                align-items: center;
            }
        } 
<?php   } else { ?>
             #single-content h1 { text-align: left}
             
<?php   } ?>
             
<?php if(get_theme_mod('align_breadcrumb_setting') == 'center') { ?>
    .single-kategori { text-align: center; }
                @media only screen and (max-width: 750px) {
            .media-sosial-mobile {
                text-align: center;
            } 
            #single-content .container-single-meta {
                justify-content: center;
                align-items: center;
            }

<?php   } else { ?>
    .single-kategori { text-align: left; }
<?php } ?>
    
                <?php   if( get_theme_mod('color_scheme_setting') == "Gradasi Biru" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile { background: rgb(33,64,154); background: linear-gradient(90deg, rgba(33,64,154,1) 0%, rgba(1,123,189,1) 100%);} .menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #21409A}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #21409A; }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #21409A;}
.line-satu, .line-dua, .line-tiga {background: #21409A; }
                    
    <?php } else if( get_theme_mod('color_scheme_setting') == "Default" ) { ?>
             .fluid-nav, footer, .menu-utama, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile  {background-color: #1B5DAF}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #1B5DAF}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #1B5DAF; }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #1B5DAF;}
.menu-utama > li > a:hover { background: #2D54A7; }
.line-satu, .line-dua, .line-tiga {background: #F51700; }
             
<?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Merah Orange" ) { ?>
 .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile { background: rgb(199,32,38);
background: linear-gradient(90deg, rgba(199,32,38,1) 0%, rgba(237,101,46,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #c72026}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #c72026 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #c72026}
.line-satu, .line-dua, .line-tiga {background: #c72026 }
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Hijau" ) { ?>
 .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile { background: rgb(0,144,118);
background: linear-gradient(90deg, rgba(0,144,118,1) 0%, rgba(16,169,62,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #009076}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #009076 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #009076}
.line-satu, .line-dua, .line-tiga {background: #009076 }
.menu-utama > .current-menu-item > a {background-color: #00735e;}
footer { border-color: #fcc43f}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Ungu" ) { ?>
              .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(86,43,119);
background: linear-gradient(90deg, rgba(86,43,119,1) 0%, rgba(169,37,140,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #562b77}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #562b77 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #562b77}
.line-satu, .line-dua, .line-tiga {background: #562b77 }
.menu-utama > .current-menu-item > a {background-color: #45225f}
footer { border-color: #45225f}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Merah Ungu" ) { ?>
               .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(230,0,11);
background: linear-gradient(90deg, rgba(230,0,11,1) 0%, rgba(38,0,98,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #e6000b}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #e6000b }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #e6000b}
.line-satu, .line-dua, .line-tiga {background: #e6000b }
.menu-utama > .current-menu-item > a {background-color: #b80009}
footer { border-color: #b80009}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Biru Ungu" ) { ?>
   .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(74,194,237);
background: linear-gradient(90deg, rgba(74,194,237,1) 0%, rgba(110,136,215,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #4ac2ed}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #4ac2ed }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #4ac2ed}
.line-satu, .line-dua, .line-tiga {background: #4ac2ed }
.menu-utama > .current-menu-item > a {background-color: #3b9bbe}
footer { border-color: #3b9bbe}
.menu-utama > li > ul.sub-menu {border-color: #3b9bbe }
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Orange" ) { ?>
 .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(255,102,0);
background: linear-gradient(90deg, rgba(255,102,0,1) 0%, rgba(255,52,0,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #ff6600}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #ff6600 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #ff6600}
.line-satu, .line-dua, .line-tiga {background: #ff6600 }
.menu-utama > .current-menu-item > a {background-color: #cc5200}
footer { border-color: #cc5200}
.menu-utama > li > ul.sub-menu {border-color: #cc5200 }
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Hitam" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(0,0,0);
background: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(77,77,77,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #4d4d4d }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #4d4d4d }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #4d4d4d}
.line-satu, .line-dua, .line-tiga {background: #4d4d4d }
.menu-utama > .current-menu-item > a {background-color: #3e3e3e}
footer { border-color: #3e3e3e}
.menu-utama > li > ul.sub-menu {border-color: #3e3e3e }

 <?php } else if ( get_theme_mod('color_scheme_setting') == "Hitam Pekat" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #000000; } 
.menu-utama { background: transparent;}
                    #category-content h2 a, .single-kategori a {color: #000000}
                    .headline-tiga-text-wrap-mobile h2 a {color: #ffffff;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #000000 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #1a1a1a }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #000000}
.line-satu, .line-dua, .line-tiga {background: #000000 }
.menu-utama > .current-menu-item > a {background-color: #1a1a1a}
footer { border-color: #1a1a1a}
.menu-utama > li > ul.sub-menu {border-color: #1a1a1a }

                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Maroon" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(153,0,0);
background: linear-gradient(90deg, rgba(153,0,0,1) 0%, rgba(112,0,13,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #990000 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #990000 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #990000}
.line-satu, .line-dua, .line-tiga {background: #990000 }
.menu-utama > .current-menu-item > a {background-color: #7a0000}
footer { border-color: #7a0000}
.menu-utama > li > ul.sub-menu {border-color: #7a0000 }
                    
    <?php }  else if ( get_theme_mod('color_scheme_setting') == "Gradasi Biru 2" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(22,31,109);
background: linear-gradient(90deg, rgba(22,31,109,1) 0%, rgba(0,171,225,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #161f6d }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #161f6d }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #161f6d}
.line-satu, .line-dua, .line-tiga {background: #161f6d }
.menu-utama > .current-menu-item > a {background-color: #121957}
footer { border-color: #121957}
.menu-utama > li > ul.sub-menu {border-color: #121957 }
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Orange Merah Biru" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #1a2a6c;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #fdbb2d, #b21f1f, #1a2a6c);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #fdbb2d, #b21f1f, #1a2a6c); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #fdbb2d }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #fdbb2d }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #fdbb2d}
.line-satu, .line-dua, .line-tiga {background: #fdbb2d }
.menu-utama > .current-menu-item > a {background-color: #ca9624}
footer { border-color: #ca9624}
.menu-utama > li > ul.sub-menu {border-color: #ca9624 }
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Biru Gelap" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #141E30;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #243B55, #141E30);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #243B55, #141E30); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #141E30 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #141E30 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #141E30}
.line-satu, .line-dua, .line-tiga {background: #141E30 }
.menu-utama > .current-menu-item > a {background-color: #434b59}
footer { border-color: #434b59}
.menu-utama > li > ul.sub-menu {border-color: #434b59}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Hijau Kuning" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #3CA55C;  /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #B5AC49, #3CA55C);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #B5AC49, #3CA55C); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */



} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #3CA55C }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #3CA55C }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #3CA55C}
.line-satu, .line-dua, .line-tiga {background: #3CA55C }
.menu-utama > .current-menu-item > a {background-color: #30844a}
footer { border-color: #30844a}
.menu-utama > li > ul.sub-menu {border-color: #30844a}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Hijau Tua" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #000000;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #0f9b0f, #000000);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #0f9b0f, #000000); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #0f9b0f }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #0f9b0f }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #0f9b0f}
.line-satu, .line-dua, .line-tiga {background: #0f9b0f }
.menu-utama > .current-menu-item > a {background-color: #0c7c0c}
footer { border-color: #0c7c0c}
.menu-utama > li > ul.sub-menu {border-color: #0c7c0c}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Biru Ungu Merah" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #12c2e9;  /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #f64f59, #c471ed, #12c2e9);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #f64f59, #c471ed, #12c2e9); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #12c2e9 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #12c2e9 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #12c2e9}
.line-satu, .line-dua, .line-tiga {background: #12c2e9 }
.menu-utama > .current-menu-item > a {background-color: #0e9bba}
footer { border-color: #0e9bba}
.menu-utama > li > ul.sub-menu {border-color: #0e9bba}

    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Biru Ungu Merah 2" ) { ?>
               .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #21409a;
background: linear-gradient(90deg, rgba(33,64,154,1) 0%, rgba(121,33,102,1) 49%, rgba(237,60,63,1) 100%);} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #21409a}
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #21409a }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #21409a}
.line-satu, .line-dua, .line-tiga {background: #21409a }
.menu-utama > .current-menu-item > a {background-color: 172d6c}
footer { border-color: #21409a;}        
                    .headline-tiga-text-wrap-mobile h2 a { color: #fff; }
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Biru 3" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #0575E6;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #021B79, #0575E6);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #021B79, #0575E6); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #0575E6 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #0575E6 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #0575E6}
.line-satu, .line-dua, .line-tiga {background: #0575E6 }
.menu-utama > .current-menu-item > a {background-color: #045eb8}
footer { border-color: #045eb8}
.menu-utama > li > ul.sub-menu {border-color: #045eb8}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Merah Hitam" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(255,0,0);
background: linear-gradient(90deg, rgba(255,0,0,1) 0%, rgba(0,0,0,1) 100%);
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #ff0000 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #ff0000 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #ff0000}
.line-satu, .line-dua, .line-tiga {background: #ff0000 }
.menu-utama > .current-menu-item > a {background-color: #cc0000}
footer { border-color: #cc0000}
.menu-utama > li > ul.sub-menu {border-color: #cc0000}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Gradasi Coklat" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(75,11,13);
background: linear-gradient(90deg, rgba(75,11,13,1) 0%, rgba(111,50,3,1) 100%);
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #4b0b0d }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #4b0b0d }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #4b0b0d}
.line-satu, .line-dua, .line-tiga {background: #4b0b0d }
.menu-utama > .current-menu-item > a {background-color: #3c090a}
footer { border-color: #3c090a}
.menu-utama > li > ul.sub-menu {border-color: #3c090a}
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Putih" ) { ?>
             .fluid-nav, footer, .scroll-to-continue {background: #fff;
} 
.menu-utama { background: transparent;}
.menu-utama li a, footer, .alamat, .menu-bawah li a, .footer-copyright { color: #000;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover, #category-content h2 a, .single-kategori a {color: #000 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #000 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #000}
.line-satu, .line-dua, .line-tiga, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile  {background: #000 }
.menu-utama > .current-menu-item > a, .menu-utama li a:hover {background-color: #e6e6e6}
footer { border-color: #333333}
.menu-utama > li > ul.sub-menu {border-color: #333333}
.menu-utama > li > ul.sub-menu > li a:hover {
    background: #ccc;
    color: #000;
             } 
                    
    <?php } else if ( get_theme_mod('color_scheme_setting') == "Biru Dongker" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #131373;
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #131373 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #131373 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #131373}
.line-satu, .line-dua, .line-tiga {background: #131373 }
.menu-utama > .current-menu-item > a {background-color: #0f0f5c}
footer { border-color: #0f0f5c}
.menu-utama > li > ul.sub-menu {border-color: #0f0f5c}
             
             
<?php } else if ( get_theme_mod('color_scheme_setting') == "Hijau" ) { ?>
             .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #017230;
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #017230 }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #017230 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #017230}
.line-satu, .line-dua, .line-tiga {background: #017230 }
.menu-utama > .current-menu-item > a {background-color: #015b26}
footer { border-color: #015b26}
.menu-utama > li > ul.sub-menu {border-color: #015b26}
             
             
<?php } else if ( get_theme_mod('color_scheme_setting') == "Merah Abu Abu" ) { ?>
             .fluid-nav, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #ff0000;
} 
                    footer {background: #F0F0F0;}
                    .alamat, .menu-bawah li a, .footer-copyright {color: #000;}
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #ff0000 }
.search-submit, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #ff0000 }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #ff0000}
.line-satu, .line-dua, .line-tiga {background: #ff0000 }
.menu-utama > .current-menu-item > a {background-color: #e60000}
                    .menu-utama > li > ul.sub-menu { background: #cc0000}
footer { border-color: #ff0000}
.menu-utama > li > ul.sub-menu {border-color: #ff0000} 
             
             
<?php } else if ( get_theme_mod('color_scheme_setting') == "Biru Hijau" ) { ?>
             .fluid-nav, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #4174B7;
} 
                    footer {background: #4174B7;}
                    /* .alamat, .menu-bawah li a, .footer-copyright {color: #000;} */
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #4174B7 }
.search-submit, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover, .judul-headline, .kategori-berita-rekomendasi, .scrollTop {background: #27A74E }
.search-submit, .search-form .search-field, .page-link-wrap span.current, a.post-page-numbers {border-color: #27A74E}
.line-satu, .line-dua, .line-tiga {background: #27A74E }
.menu-utama > .current-menu-item > a {background-color: #27A74E}
                    .menu-utama > li > ul.sub-menu { background: #4174B7}
footer, .mobile-menu-kiri-wrap form.search-form { border-color: #4174B7}
.menu-utama > li > ul.sub-menu {border-color: #27A74E}
                    .news-feed-list .kategori, .single-kategori a span, .desktop-kategori-berita-terbaru, .category-kategori a {color: #27A74E; }
                    
                    
<?php }  else if ( get_theme_mod('color_scheme_setting') == "Biru Hitam" ) { ?>
.fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: rgb(75,11,13);
background: linear-gradient(90deg, rgba(67,102,196,1) 0%, rgba(19,19,19,1) 100%);} 

.menu-utama { background: transparent;}

.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #4366C4 }

.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #4366C4 }

.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #4366C4}

.line-satu, .line-dua, .line-tiga {background: #4366C4 }

.menu-utama > .current-menu-item > a {background-color: #2f4789}

footer { border-color: #2f4789}

.menu-utama > li > ul.sub-menu {border-color: #2f4789}
        
        
<?php   } else if ( get_theme_mod('color_scheme_setting') == "Hijau Abu Abu" ) { ?>

 .fluid-nav, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #009788;} 

footer {background: #F3F4F8;}

.alamat, .menu-bawah li a, .footer-copyright, .network-title, .menu-network li a {color: #000;}

.menu-utama { background: transparent;}

.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: #009788 }

.search-submit, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: #009788 }

.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: #009788}

.line-satu, .line-dua, .line-tiga {background: #009788 }

.menu-utama > .current-menu-item > a {background-color: #006a5f}

.menu-utama > li > ul.sub-menu { background: #cc0000}

footer { border-color: #009788}

.menu-utama > li > ul.sub-menu {border-color: #009788} 
                    
<?php } else if ( get_theme_mod('color_scheme_setting') == "Teal Orange" ) { ?>
    
       .fluid-nav, footer, .scroll-to-continue, .ad-middle .ad-title, .ad-middle .scroll-to-resume, .tagname span, .tagname a:hover, .headline-tiga-text-wrap-mobile {background: #02aebc;
background: linear-gradient(90deg, rgba(2, 174, 188, 1) 0%, rgba(0, 108, 120, 1) 100%);
} 
.menu-utama { background: transparent;}
.nama-penulis a, single-kategori a, .single-article-text p a, .single-article-text h1 a, .single-article-text h2 a, .single-article-text h3 a, .single-article-text h4 a, .single-article-text h5 a, .single-article-text h6 a, .single-article-text ul li a, .single-article-text ol li a, .single-article-text div a, .single-article-text a, .judul-desktop-berita-terbaru:hover, .wpp-list > li > a.wpp-post-title:before, .recent-post-widget a:hover {color: rgba(2, 174, 188, 1) }
.search-submit, .menu-utama > li > ul.sub-menu, .nav-links .current, .page-numbers:hover, .menu-utama > li > a:hover, .page-link-wrap span.current, .page-link-wrap a:hover {background: rgba(2, 174, 188, 1) }
.search-submit, .search-form .search-field, .mobile-menu-kiri-wrap form.search-form, .page-link-wrap span.current, a.post-page-numbers {border-color: rgba(2, 174, 188, 1)}
.line-satu, .line-dua, .line-tiga {background: rgba(2, 174, 188, 1) }
.menu-utama > .current-menu-item > a {background-color: #F27A2C}
footer { border-color: #F27A2C}
.menu-utama > li > ul.sub-menu {border-color: #F27A2C}
.headline-tiga-text-wrap-mobile h2 a {color: #fff;}
    
    
    <?php }; ?>

             
             
         </style>
    <?php } // tutup function my_customize_css 

add_action( 'wp_head', 'my_customize_css');
 

if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
    add_action('admin_footer','removed_widgets');
}

// remove widget on sidebar after theme installation
function removed_widgets(){
    //get all registered sidebars
    global $wp_registered_sidebars;
    //get saved widgets
    $widgets = get_option('sidebars_widgets');
    //loop over the sidebars and remove all widgets
    foreach ($wp_registered_sidebars as $sidebar => $value) {
        unset($widgets[$sidebar]);
    }
    //update with widgets removed
    update_option('sidebars_widgets',$widgets);
};

require('js/jquery-ui-themes/themes/eggplant/eggplant.php');

// Custom CSS to Admin Area

add_action('admin_head', 'my_custom_style');

function my_custom_style() {
  echo '<style>
   .nomina_prolibrary-field-input {margin-top: 20px; margin-bottom: 20px}
   h3.status_lisensi {font-size: 2em}
   span#event {display: block !important}
   .nomina_button {background: #2271B1 !important; color: #fff !important;}
  </style>';
}

// limit default wordpress most popular tag
 function prefix_widget_tag_cloud_args( $args ) {
    $args['number'] = 5;
    return $args;
}
add_filter( 'widget_tag_cloud_args', 'prefix_widget_tag_cloud_args', 10, 1 ); 

?>