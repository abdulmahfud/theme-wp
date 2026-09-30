<?php
/**
 * Demo importer: small idempotent steps (driven one by one by the admin page, or all at once from WP-CLI:
 * `wp eval "mnews_demo_run_all();"`). Everything created is tagged with the meta `_mnews_demo`, so it can be removed again.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the wp-admin helpers the importer needs (they are not always loaded).
 */
function mnews_demo_load_admin_includes() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/post.php';
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	if ( function_exists( 'wp_raise_memory_limit' ) ) {
		wp_raise_memory_limit( 'image' );
	}
}

/**
 * Options the user can choose on the import screen (key => default).
 *
 * @return array<string,bool>
 */
function mnews_demo_option_keys() {
	return array(
		'content'   => true,
		'widgets'   => true,
		'settings'  => true,
		'logo'      => true,
		'identity'  => true,
		'permalink' => true,
		'cleanup'   => true,
	);
}

/**
 * Normalise submitted options.
 *
 * @param array $raw Raw options (key => truthy).
 * @return array<string,bool>
 */
function mnews_demo_options( $raw ) {
	$out = array();
	foreach ( mnews_demo_option_keys() as $key => $default ) {
		$out[ $key ] = isset( $raw[ $key ] ) ? (bool) $raw[ $key ] : false;
	}
	return $out;
}

/**
 * Parse the bundled article files into one flat list (category order, then file order).
 *
 * File format: "### Title | YYYY-MM-DD | tag one, tag two", then paragraphs separated by blank lines;
 * a line starting with "## " is a sub-heading.
 *
 * @return array[]
 */
function mnews_demo_articles() {
	static $list = null;
	if ( null !== $list ) {
		return $list;
	}
	$list = array();
	foreach ( array_keys( mnews_demo_categories() ) as $slug ) {
		$file = MNEWS_DIR . '/demo/articles/' . $slug . '.txt';
		if ( ! is_readable( $file ) ) {
			continue;
		}
		$blocks = preg_split( '/^### /m', (string) file_get_contents( $file ), -1, PREG_SPLIT_NO_EMPTY ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local bundled file.
		foreach ( $blocks as $block ) {
			$parts  = array_pad( explode( "\n", $block, 2 ), 2, '' );
			$meta   = array_map( 'trim', explode( '|', $parts[0] ) );
			$list[] = array(
				'cat'   => $slug,
				'title' => $meta[0],
				'date'  => isset( $meta[1] ) ? $meta[1] : '2024-06-01',
				'tags'  => isset( $meta[2] ) ? array_filter( array_map( 'trim', explode( ',', $meta[2] ) ) ) : array(),
				'body'  => $parts[1],
			);
		}
	}
	return $list;
}

/**
 * Category-coloured JPEG (gradient + soft circles), varied by $seed.
 *
 * @param int[] $rgb  Base colour.
 * @param int   $seed Variation seed.
 * @return string JPEG bytes, or an empty string when GD is missing.
 */
function mnews_demo_image( $rgb, $seed ) {
	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return '';
	}
	$w     = 1200;
	$h     = 675;
	$im    = imagecreatetruecolor( $w, $h );
	$shift = ( $seed % 5 ) * 14 - 28;
	imagealphablending( $im, true );

	for ( $y = 0; $y < $h; $y++ ) {
		$f   = $y / $h;
		$col = imagecolorallocate(
			$im,
			max( 0, min( 255, (int) ( $rgb[0] * ( 1.25 - $f * 0.7 ) + $shift ) ) ),
			max( 0, min( 255, (int) ( $rgb[1] * ( 1.25 - $f * 0.7 ) - $shift / 2 ) ) ),
			max( 0, min( 255, (int) ( $rgb[2] * ( 1.25 - $f * 0.7 ) + $shift / 3 ) ) )
		);
		imageline( $im, 0, $y, $w, $y, $col );
	}
	mt_srand( $seed * 7919 + $rgb[0] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_seeding_mt_srand -- deterministic demo art.
	for ( $i = 0; $i < 4; $i++ ) {
		$r = mt_rand( 120, 320 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		$c = imagecolorallocatealpha( $im, 255, 255, 255, mt_rand( 96, 118 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		imagefilledellipse( $im, mt_rand( 100, $w - 100 ), mt_rand( 80, $h - 80 ), $r, $r, $c ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
	}
	imagefilledrectangle( $im, 0, (int) ( $h * 0.78 ), $w, $h, imagecolorallocatealpha( $im, 0, 0, 0, 110 ) );

	ob_start();
	imagejpeg( $im, null, 80 );
	$data = ob_get_clean();
	imagedestroy( $im );
	return (string) $data;
}

/**
 * Placeholder banner (PNG) with its size written on it.
 *
 * @param int $w Width.
 * @param int $h Height.
 * @return string PNG bytes, or an empty string when GD is missing.
 */
function mnews_demo_banner( $w, $h ) {
	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return '';
	}
	$im = imagecreatetruecolor( $w, $h );
	for ( $x = 0; $x < $w; $x++ ) {
		$f = $x / max( 1, $w - 1 );
		imageline(
			$im,
			$x,
			0,
			$x,
			$h,
			imagecolorallocate( $im, (int) ( 74 + 111 * $f ), (int) ( 29 - 6 * $f ), (int) ( 143 + 15 * $f ) )
		);
	}
	$white = imagecolorallocate( $im, 255, 255, 255 );
	imagestring( $im, 5, 10, 10, sprintf( 'IKLAN %dx%d', $w, $h ), $white );
	imagestring( $im, 3, 10, 32, 'contoh banner', $white );

	ob_start();
	imagepng( $im );
	$data = ob_get_clean();
	imagedestroy( $im );
	return (string) $data;
}

/**
 * Save bytes as a media library item tagged as demo content.
 *
 * @param string $filename File name.
 * @param string $bytes    Binary data.
 * @param string $title    Attachment title.
 * @param string $mime     MIME type.
 * @param int    $parent_id Parent post ID.
 * @param string $caption  Caption.
 * @return int Attachment ID, or 0.
 */
function mnews_demo_sideload( $filename, $bytes, $title, $mime, $parent_id = 0, $caption = '' ) {
	if ( '' === $bytes ) {
		return 0;
	}
	$upload = wp_upload_bits( $filename, null, $bytes );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => $mime,
			'post_title'     => $title,
			'post_excerpt'   => $caption,
			'post_status'    => 'inherit',
		),
		$upload['file'],
		$parent_id
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	update_post_meta( $id, '_mnews_demo', 1 );
	return (int) $id;
}

/**
 * Get or create the demo categories. Returns slug => term ID.
 *
 * @return array<string,int>
 */
function mnews_demo_category_ids() {
	$ids = array();
	foreach ( mnews_demo_categories() as $slug => $cat ) {
		$term = get_term_by( 'name', $cat['name'], 'category' );
		if ( ! $term ) {
			$res  = wp_insert_term( $cat['name'], 'category', array( 'slug' => $slug ) );
			$term = is_wp_error( $res ) ? null : get_term( $res['term_id'], 'category' );
		}
		if ( $term ) {
			$ids[ $slug ] = (int) $term->term_id;
		}
	}
	return $ids;
}

/**
 * Create a menu with the given items unless a menu with that name already has items.
 *
 * @param string  $name  Menu name.
 * @param array[] $items Items for wp_update_nav_menu_item().
 * @return int Menu ID.
 */
function mnews_demo_menu( $name, $items ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		if ( wp_get_nav_menu_items( $menu->term_id ) ) {
			return (int) $menu->term_id;
		}
		$menu_id = (int) $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return 0;
		}
	}
	foreach ( $items as $item ) {
		wp_update_nav_menu_item( $menu_id, 0, array_merge( array( 'menu-item-status' => 'publish' ), $item ) );
	}
	return (int) $menu_id;
}

/**
 * Step: media (logo + banner placeholders).
 *
 * @param array $opts Options.
 * @return string Log line.
 */
function mnews_demo_step_media( $opts ) {
	$media = get_option( 'mnews_demo_media', array() );
	$media = is_array( $media ) ? $media : array();
	$made  = 0;

	if ( ! empty( $opts['logo'] ) && empty( $media['logo'] ) ) {
		$file = MNEWS_DIR . '/demo/logo.png';
		if ( is_readable( $file ) ) {
			$media['logo'] = mnews_demo_sideload( 'm-news-logo.png', (string) file_get_contents( $file ), 'Logo M-News', 'image/png' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local bundled file.
			++$made;
		}
	}
	if ( ! empty( $opts['widgets'] ) ) {
		$sizes = array(
			'b728' => array( 728, 90 ),
			'b160' => array( 160, 600 ),
			'b300' => array( 300, 250 ),
			'b600' => array( 300, 600 ),
		);
		foreach ( $sizes as $key => $wh ) {
			if ( empty( $media[ $key ] ) ) {
				$media[ $key ] = mnews_demo_sideload( 'm-news-banner-' . $wh[0] . 'x' . $wh[1] . '.png', mnews_demo_banner( $wh[0], $wh[1] ), 'Banner contoh ' . $wh[0] . 'x' . $wh[1], 'image/png' );
				++$made;
			}
		}
	}
	update_option( 'mnews_demo_media', $media, false );
	/* translators: %d: number of images. */
	return sprintf( __( 'Gambar disiapkan (%d baru).', 'm-news' ), $made );
}

/**
 * Step: categories, pages and menus.
 *
 * @param array $opts Options.
 * @return string Log line.
 */
function mnews_demo_step_structure( $opts ) {
	$cats = mnews_demo_category_ids();

	$page_ids = array();
	if ( ! empty( $opts['content'] ) ) {
		foreach ( mnews_demo_pages() as $title => $content ) {
			$found    = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'any',
					'title'          => $title,
					'posts_per_page' => 1,
				)
			);
			$existing = $found ? $found[0] : null;
			if ( $existing ) {
				$page_ids[ $title ] = (int) $existing->ID;
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_title'   => $title,
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_mnews_demo', 1 );
				if ( 'Trending' === $title ) {
					update_post_meta( $id, '_wp_page_template', 'template-trending.php' );
				}
				$page_ids[ $title ] = (int) $id;
			}
		}
	}

	// Primary menu.
	$primary = array(
		array(
			'menu-item-title' => __( 'Beranda', 'm-news' ),
			'menu-item-type'  => 'custom',
			'menu-item-url'   => home_url( '/' ),
		),
	);
	foreach ( mnews_demo_categories() as $slug => $cat ) {
		if ( isset( $cats[ $slug ] ) ) {
			$primary[] = array(
				'menu-item-title'     => $cat['name'],
				'menu-item-type'      => 'taxonomy',
				'menu-item-object'    => 'category',
				'menu-item-object-id' => $cats[ $slug ],
			);
		}
	}
	$menus = array( 'primary' => mnews_demo_menu( 'Utama', $primary ) );

	// Footer menu (pages) and network menu (example links).
	if ( $page_ids ) {
		$footer = array();
		foreach ( $page_ids as $title => $id ) {
			$footer[] = array(
				'menu-item-title'     => $title,
				'menu-item-type'      => 'post_type',
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $id,
			);
		}
		$menus['footer'] = mnews_demo_menu( 'Footer', $footer );
	}
	$network = array();
	foreach ( array( 'Portal Contoh 1', 'Portal Contoh 2', 'Portal Contoh 3' ) as $label ) {
		$network[] = array(
			'menu-item-title' => $label,
			'menu-item-type'  => 'custom',
			'menu-item-url'   => 'https://example.com/',
		);
	}
	$menus['network'] = mnews_demo_menu( 'Jaringan Media', $network );

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations = is_array( $locations ) ? $locations : array();
	foreach ( $menus as $location => $menu_id ) {
		if ( $menu_id ) {
			$locations[ $location ] = $menu_id;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	return __( 'Kategori, halaman, dan menu dibuat.', 'm-news' );
}

/**
 * Step: import one article by its index in mnews_demo_articles().
 *
 * @param int $index Index.
 * @return array{done:bool,title:string,skipped:bool}
 */
function mnews_demo_step_post( $index ) {
	$articles = mnews_demo_articles();
	if ( ! isset( $articles[ $index ] ) ) {
		return array(
			'done'    => true,
			'title'   => '',
			'skipped' => true,
		);
	}
	$a   = $articles[ $index ];
	$key = md5( $a['title'] );

	$found = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'meta_key'       => '_mnews_demo_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $found ) {
		return array(
			'done'    => false,
			'title'   => $a['title'],
			'skipped' => true,
		);
	}

	$cats    = mnews_demo_category_ids();
	$colors  = mnews_demo_categories();
	$hash    = crc32( $a['title'] );
	$html    = '';
	$first   = '';
	$writers = array( 'Daniel Satria', 'Sari Wulandari', 'Bimo Pratama', 'Laras Ayu', 'Rendra Mahesa' );
	$editors = array( 'Hendra Kusuma', 'Maya Anggraini', 'Ridwan Alamsyah' );
	$sources = array( 'Konferensi pers', 'Keterangan tertulis', 'Wawancara redaksi', 'Data dinas terkait' );

	foreach ( preg_split( "/\n\s*\n/", trim( $a['body'] ) ) as $para ) {
		$para = trim( $para );
		if ( '' === $para ) {
			continue;
		}
		if ( 0 === strpos( $para, '## ' ) ) {
			$html .= '<h2>' . esc_html( substr( $para, 3 ) ) . "</h2>\n";
		} else {
			$html .= '<p>' . esc_html( $para ) . "</p>\n";
			if ( '' === $first ) {
				$first = wp_trim_words( $para, 32, '…' );
			}
		}
	}

	$post_id = wp_insert_post(
		array(
			'post_title'    => $a['title'],
			'post_content'  => $html,
			'post_excerpt'  => $first,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_author'   => get_current_user_id() ? get_current_user_id() : 1,
			'post_date'     => $a['date'] . sprintf( ' %02d:%02d:00', 6 + ( $hash % 14 ), $hash % 60 ),
			'post_category' => isset( $cats[ $a['cat'] ] ) ? array( $cats[ $a['cat'] ] ) : array(),
			'tags_input'    => $a['tags'],
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		return array(
			'done'    => false,
			'title'   => $a['title'],
			'skipped' => true,
		);
	}

	update_post_meta( $post_id, '_mnews_demo', 1 );
	update_post_meta( $post_id, '_mnews_demo_key', $key );
	update_post_meta( $post_id, '_mnews_writer', $writers[ $hash % count( $writers ) ] );
	update_post_meta( $post_id, '_mnews_editor', $editors[ $hash % count( $editors ) ] );
	update_post_meta( $post_id, '_mnews_source', $sources[ $hash % count( $sources ) ] );

	$rgb = isset( $colors[ $a['cat'] ] ) ? $colors[ $a['cat'] ]['rgb'] : array( 90, 90, 90 );
	$att = mnews_demo_sideload( 'demo-' . $a['cat'] . '-' . ( $index + 1 ) . '.jpg', mnews_demo_image( $rgb, $index + 1 ), $a['title'], 'image/jpeg', $post_id, 'Ilustrasi. (Foto: Dokumentasi)' );
	if ( $att ) {
		set_post_thumbnail( $post_id, $att );
	}

	return array(
		'done'    => false,
		'title'   => $a['title'],
		'skipped' => false,
	);
}

/**
 * Step: place the demo widgets (replaces widgets previously placed by the importer, keeps everything else).
 *
 * @return string Log line.
 */
function mnews_demo_step_widgets() {
	$media  = get_option( 'mnews_demo_media', array() );
	$media  = wp_parse_args(
		is_array( $media ) ? $media : array(),
		array(
			'b728' => 0,
			'b160' => 0,
			'b300' => 0,
			'b600' => 0,
		)
	);
	$layout = mnews_demo_widget_layout( $media, mnews_demo_category_ids() );

	// Remove what an earlier import placed.
	mnews_demo_remove_widgets();

	$sidebars = wp_get_sidebars_widgets();
	$placed   = array();
	foreach ( $layout as $area => $widgets ) {
		$ids = array();
		foreach ( $widgets as $w ) {
			list( $base, $instance ) = $w;
			// Skip image banners whose image could not be created (GD missing).
			if ( 'mnews_banner_ad' === $base && empty( $instance['image'] ) ) {
				continue;
			}
			$option = get_option( 'widget_' . $base, array() );
			$option = is_array( $option ) ? $option : array();
			$nums   = array_filter( array_keys( $option ), 'is_int' );
			$number = $nums ? max( $nums ) + 1 : 2;

			$option[ $number ]      = $instance;
			$option['_multiwidget'] = 1;
			update_option( 'widget_' . $base, $option );

			$ids[]    = $base . '-' . $number;
			$placed[] = $base . '-' . $number;
		}
		$sidebars[ $area ] = $ids;
	}
	wp_set_sidebars_widgets( $sidebars );
	update_option( 'mnews_demo_widgets', $placed, false );
	mnews_demo_seed_polls();
	mnews_demo_seed_videos();

	/* translators: %d: number of widgets. */
	return sprintf( __( '%d widget dipasang di area home, sidebar, dan artikel.', 'm-news' ), count( $placed ) );
}

/**
 * Two example polls, so the Polling widget/archive has something to show right after import.
 * Safe to re-run: skipped if a demo poll already exists (checked by _mnews_demo_key like the demo articles).
 */
function mnews_demo_seed_polls() {
	$polls = array(
		array(
			'q'       => __( 'Menurut Anda, mana yang paling mendesak dibenahi tahun ini?', 'm-news' ),
			'options' => array( __( 'Transportasi umum', 'm-news' ), __( 'Harga kebutuhan pokok', 'm-news' ), __( 'Lapangan kerja', 'm-news' ), __( 'Layanan kesehatan', 'm-news' ) ),
		),
		array(
			'q'       => __( 'Apakah Anda setuju jam kerja fleksibel diterapkan lebih luas?', 'm-news' ),
			'options' => array( __( 'Setuju', 'm-news' ), __( 'Tidak setuju', 'm-news' ), __( 'Tergantung bidang pekerjaan', 'm-news' ) ),
		),
	);
	foreach ( $polls as $i => $p ) {
		$key = 'poll-' . ( $i + 1 );
		if ( get_posts(
			array(
				'post_type'      => 'mnews_poll',
				'meta_key'       => '_mnews_demo_key',
				'meta_value'     => $key,
				'fields'         => 'ids',
				'posts_per_page' => 1,
			)
		) ) { // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'   => 'mnews_poll',
				'post_status' => 'publish',
				'post_title'  => $p['q'],
				'post_date'   => gmdate( 'Y-m-d H:i:s', time() - ( $i + 1 ) * DAY_IN_SECONDS ),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_mnews_poll_options', implode( "\n", $p['options'] ) );
			update_post_meta( $id, '_mnews_demo', 1 );
			update_post_meta( $id, '_mnews_demo_key', $key );
		}
	}
}

/**
 * One short video post per category, so the "Video" widget/grid has something to show right after import
 * (a plain article flagged with a video URL, same as a real editor would do — see inc/meta.php). Same stable,
 * neutral clip on every one on purpose: one external dependency to vet, not six.
 * Safe to re-run: skipped per post if it already exists (checked by _mnews_demo_key like the demo articles).
 */
function mnews_demo_seed_videos() {
	$video_url = 'https://www.youtube.com/watch?v=jNQXAC9IVRw';
	$cats      = mnews_demo_category_ids();
	$colors    = mnews_demo_categories();
	$videos    = array(
		'nasional'  => __( 'Video: Suasana Rapat Kerja Nasional Bahas Prioritas Pembangunan', 'm-news' ),
		'ekonomi'   => __( 'Video: Peninjauan Harga Pasar Menjelang Akhir Tahun', 'm-news' ),
		'hukum'     => __( 'Video: Jalannya Sidang Lanjutan Kasus Sengketa Lahan', 'm-news' ),
		'kriminal'  => __( 'Video: Rekaman Olah TKP Kasus Pencurian Kendaraan', 'm-news' ),
		'olahraga'  => __( 'Video: Momen Final Turnamen Bulu Tangkis Junior', 'm-news' ),
		'teknologi' => __( 'Video: Demo Langsung Aplikasi Pemantau Kualitas Udara', 'm-news' ),
	);

	$n = 0;
	foreach ( $videos as $slug => $title ) {
		++$n;
		$key = 'video-' . $slug;
		if ( get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'any',
				'meta_key'       => '_mnews_demo_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'fields'         => 'ids',
				'posts_per_page' => 1,
			)
		) ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'    => $title,
				'post_content'  => '<p>' . esc_html__( 'Liputan video singkat untuk melengkapi laporan tertulis. Konten pada video ini adalah materi contoh yang disertakan bersama tema untuk memperlihatkan tampilan artikel bertipe video.', 'm-news' ) . '</p>',
				'post_excerpt'  => __( 'Liputan video singkat untuk melengkapi laporan tertulis.', 'm-news' ),
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_author'   => get_current_user_id() ? get_current_user_id() : 1,
				// Dated within the same old-news range as the regular demo articles (not "now"), so these don't
				// dominate "latest" widgets like the hero slider just for being freshly imported.
				'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( '2024-09-01' ) + $n * 24 * DAY_IN_SECONDS ),
				'post_category' => isset( $cats[ $slug ] ) ? array( $cats[ $slug ] ) : array(),
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		update_post_meta( $post_id, '_mnews_demo', 1 );
		update_post_meta( $post_id, '_mnews_demo_key', $key );
		update_post_meta( $post_id, '_mnews_video', $video_url );

		$rgb = isset( $colors[ $slug ] ) ? $colors[ $slug ]['rgb'] : array( 90, 90, 90 );
		$att = mnews_demo_sideload( 'demo-video-' . $slug . '.jpg', mnews_demo_image( $rgb, $n + 50 ), $title, 'image/jpeg', $post_id, __( 'Ilustrasi. (Foto: Dokumentasi)', 'm-news' ) );
		if ( $att ) {
			set_post_thumbnail( $post_id, $att );
		}
	}
}

/**
 * Remove the widgets a previous import placed.
 */
function mnews_demo_remove_widgets() {
	$old = get_option( 'mnews_demo_widgets', array() );
	if ( ! is_array( $old ) || ! $old ) {
		return;
	}
	$sidebars = wp_get_sidebars_widgets();
	foreach ( $sidebars as $area => $ids ) {
		if ( is_array( $ids ) ) {
			$sidebars[ $area ] = array_values( array_diff( $ids, $old ) );
		}
	}
	wp_set_sidebars_widgets( $sidebars );

	foreach ( $old as $widget_id ) {
		if ( ! preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) ) {
			continue;
		}
		$option = get_option( 'widget_' . $m[1], array() );
		if ( is_array( $option ) && isset( $option[ (int) $m[2] ] ) ) {
			unset( $option[ (int) $m[2] ] );
			update_option( 'widget_' . $m[1], $option );
		}
	}
	delete_option( 'mnews_demo_widgets' );
}

/**
 * Step: Customizer values, site identity, permalinks, cleanup.
 *
 * @param array $opts Options.
 * @return string Log line.
 */
function mnews_demo_step_settings( $opts ) {
	$done = array();

	if ( ! empty( $opts['settings'] ) ) {
		foreach ( mnews_demo_theme_mods() as $name => $value ) {
			set_theme_mod( $name, $value );
		}
		$done[] = __( 'pengaturan tampilan', 'm-news' );
	}
	if ( ! empty( $opts['logo'] ) ) {
		$media = get_option( 'mnews_demo_media', array() );
		if ( ! empty( $media['logo'] ) ) {
			set_theme_mod( 'custom_logo', (int) $media['logo'] );
			$done[] = __( 'logo', 'm-news' );
		}
	}
	if ( ! empty( $opts['identity'] ) ) {
		update_option( 'blogname', 'Portal Berita M-News' );
		update_option( 'blogdescription', 'Berita terkini, akurat, dan terpercaya' );
		$done[] = __( 'judul & slogan', 'm-news' );
	}
	if ( ! empty( $opts['permalink'] ) ) {
		global $wp_rewrite;
		update_option( 'timezone_string', 'Asia/Jakarta' );
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		flush_rewrite_rules();
		$done[] = __( 'permalink & zona waktu', 'm-news' );
	}
	if ( ! empty( $opts['content'] ) || ! empty( $opts['widgets'] ) ) {
		update_option( 'show_on_front', 'posts' );
	}
	if ( ! empty( $opts['cleanup'] ) ) {
		foreach ( array(
			'post' => 'hello-world',
			'page' => 'sample-page',
		) as $type => $slug ) {
			$default = get_page_by_path( $slug, OBJECT, $type );
			if ( $default && ! get_post_meta( $default->ID, '_mnews_demo', true ) ) {
				wp_delete_post( $default->ID, true );
			}
		}
		$done[] = __( 'konten bawaan WordPress dihapus', 'm-news' );
	}

	/* translators: %s: list of things that were set. */
	return $done ? sprintf( __( 'Diatur: %s.', 'm-news' ), implode( ', ', $done ) ) : __( 'Tidak ada pengaturan yang diubah.', 'm-news' );
}

/**
 * Step: finish.
 *
 * @return string Log line.
 */
function mnews_demo_step_finish() {
	update_option( 'mnews_demo_imported', time(), false );
	delete_option( 'mnews_demo_dismissed' );
	if ( function_exists( 'mnews_cache_flush' ) ) {
		mnews_cache_flush();
	}
	return __( 'Selesai. Cache dibersihkan.', 'm-news' );
}

/**
 * Step: remove one batch of demo content. Returns how many items remain.
 *
 * @return int
 */
function mnews_demo_step_remove() {
	$ids = get_posts(
		array(
			'post_type'      => array( 'post', 'page', 'attachment', 'mnews_poll' ),
			'post_status'    => 'any',
			'meta_key'       => '_mnews_demo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => 1, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'posts_per_page' => 10,
			'fields'         => 'ids',
		)
	);
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	if ( count( $ids ) < 10 ) {
		mnews_demo_remove_widgets();
		$media = get_option( 'mnews_demo_media', array() );
		if ( ! empty( $media['logo'] ) && (int) get_theme_mod( 'custom_logo' ) === (int) $media['logo'] ) {
			remove_theme_mod( 'custom_logo' );
		}
		delete_option( 'mnews_demo_media' );
		delete_option( 'mnews_demo_imported' );
		if ( function_exists( 'mnews_cache_flush' ) ) {
			mnews_cache_flush();
		}
		return 0;
	}
	return 1;
}

/**
 * Run the whole import in one go (WP-CLI / tests).
 *
 * @param array|null $opts Options; all on by default.
 * @return string[] Log lines.
 */
function mnews_demo_run_all( $opts = null ) {
	mnews_demo_load_admin_includes();
	$opts = mnews_demo_options( null === $opts ? mnews_demo_option_keys() : $opts );
	$log  = array();

	$log[] = mnews_demo_step_media( $opts );
	$log[] = mnews_demo_step_structure( $opts );
	if ( $opts['content'] ) {
		$total = count( mnews_demo_articles() );
		for ( $i = 0; $i < $total; $i++ ) {
			$r     = mnews_demo_step_post( $i );
			$log[] = ( $r['skipped'] ? '= ' : '+ ' ) . $r['title'];
		}
	}
	if ( $opts['widgets'] ) {
		$log[] = mnews_demo_step_widgets();
	}
	$log[] = mnews_demo_step_settings( $opts );
	$log[] = mnews_demo_step_finish();
	return $log;
}
