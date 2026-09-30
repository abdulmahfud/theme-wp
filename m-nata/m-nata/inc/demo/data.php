<?php
/**
 * Demo data: what the one-click import creates. Edit here to change the demo.
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * Categories in menu order: slug => name and demo colour (used for generated images).
 *
 * @return array<string,array{name:string,rgb:int[]}>
 */
function mnata_demo_categories() {
	return array(
		'nasional'  => array(
			'name' => 'Nasional',
			'rgb'  => array( 43, 108, 176 ),
		),
		'ekonomi'   => array(
			'name' => 'Ekonomi',
			'rgb'  => array( 27, 127, 92 ),
		),
		'hukum'     => array(
			'name' => 'Hukum',
			'rgb'  => array( 106, 61, 154 ),
		),
		'kriminal'  => array(
			'name' => 'Kriminal',
			'rgb'  => array( 178, 58, 58 ),
		),
		'olahraga'  => array(
			'name' => 'Olahraga',
			'rgb'  => array( 224, 138, 0 ),
		),
		'teknologi' => array(
			'name' => 'Teknologi',
			'rgb'  => array( 14, 143, 166 ),
		),
	);
}

/**
 * Static pages linked from the footer menu.
 *
 * @return array<string,string> Title => content.
 */
function mnata_demo_pages() {
	return array(
		'Tentang Kami'        => '<p>Portal Berita M-Nata adalah media daring yang menyajikan berita terkini, akurat, dan berimbang dari berbagai bidang: nasional, ekonomi, hukum, kriminal, olahraga, dan teknologi.</p><h2>Visi</h2><p>Menjadi rujukan berita yang cepat dan tepercaya bagi masyarakat Indonesia.</p><h2>Misi</h2><ul><li>Menyajikan informasi yang akurat dan dapat dipertanggungjawabkan.</li><li>Menjaga independensi redaksi.</li><li>Memberi ruang bagi berbagai sudut pandang.</li></ul><p>Halaman ini adalah contoh dari impor demo. Silakan ubah sesuai kebutuhan media Anda.</p>',
		'Redaksi'             => '<p>Susunan redaksi contoh. Ganti dengan data redaksi Anda.</p><ul><li><strong>Pemimpin Redaksi:</strong> Nama Pemimpin Redaksi</li><li><strong>Redaktur Pelaksana:</strong> Nama Redaktur</li><li><strong>Reporter:</strong> Nama Reporter</li></ul><p>Alamat redaksi dan kontak dapat dilihat pada bagian bawah halaman.</p>',
		'Pedoman Media Siber' => '<p>Contoh halaman pedoman media siber. Isi dengan pedoman yang berlaku pada media Anda, misalnya verifikasi dan keberimbangan berita, hak jawab dan hak koreksi, pencabutan berita, iklan, serta perlindungan data pribadi.</p>',
		'Disclaimer'          => '<p>Contoh halaman disclaimer. Seluruh konten artikel demo pada tema ini adalah fiktif dan dibuat hanya untuk memperlihatkan tampilan tema. Kesamaan dengan kejadian atau pihak sebenarnya adalah kebetulan.</p>',
		'Kontak'              => '<p>Hubungi redaksi melalui surel <strong>redaksi@contoh.test</strong> atau telepon <strong>(021) 000-0000</strong>.</p><p>Ini adalah halaman contoh; ganti dengan kontak sebenarnya.</p>',
	);
}

/**
 * Theme mods (Customizer values) that make the site look like the demo.
 *
 * @return array<string,mixed>
 */
function mnata_demo_theme_mods() {
	return array(
		'mnata_grad_preset'         => 'ungu-magenta',
		'mnata_grad_nav'            => true,
		'mnata_grad_footer'         => true,
		'mnata_grad_button'         => true,
		'mnata_grad_block'          => true,
		'mnata_grad_title'          => false,
		'mnata_sticky_header'       => true,
		'mnata_side_sticky'         => true,
		'mnata_ticker_on'           => true,
		'mnata_ticker_label'        => 'Terbaru',
		'mnata_ticker_tag'          => '',
		'mnata_ticker_count'        => 5,
		'mnata_tz_label'            => 'WIB',
		'mnata_footer_address'      => "PT. Media Contoh Sentosa\nJl. Merdeka No. 1, Jakarta Pusat\nEmail: redaksi@contoh.test · Telp: (021) 000-0000",
		'mnata_network_title'       => 'MEDIA NETWORK',
		'mnata_copyright'           => '',
		'mnata_social_facebook'     => 'https://facebook.com/',
		'mnata_social_x'            => 'https://x.com/',
		'mnata_social_instagram'    => 'https://instagram.com/',
		'mnata_social_youtube'      => 'https://youtube.com/',
		'mnata_social_tiktok'       => 'https://tiktok.com/',
		'mnata_share_facebook'      => true,
		'mnata_share_x'             => true,
		'mnata_share_whatsapp'      => true,
		'mnata_share_telegram'      => true,
		'mnata_share_line'          => false,
		'mnata_share_linkedin'      => false,
		'mnata_share_pinterest'     => false,
		'mnata_share_email'         => false,
		'mnata_share_copy'          => true,
		'mnata_share_position'      => 'top',
		'mnata_share_float'         => false,
		'mnata_share_style'         => 'brand',
		'mnata_share_labels'        => false,
		'mnata_share_text'          => '{title} - {url}',
		'mnata_ad_label_on'         => true,
		'mnata_ad_label'            => 'Iklan',
		'mnata_ad_in_article_after' => 3,
		'mnata_readmore_after'      => 2,
		'mnata_related_on'          => true,
		'mnata_related_count'       => 6,
		'mnata_related_title'       => 'Berita Terkait',
		'mnata_show_author'         => true,
		'mnata_show_credits'        => true,
		'mnata_show_tags'           => true,
		'mnata_clean_wp'            => true,
	);
}

/**
 * Widget layout: area => list of [ id_base, instance ]. $m holds media IDs (b728, b160, b300, b600),
 * $c holds category term IDs by slug.
 *
 * @param array<string,int> $m Media IDs.
 * @param array<string,int> $c Category IDs.
 * @return array<string,array>
 */
function mnata_demo_widget_layout( $m, $c ) {
	$banner = static function ( $size, $w, $h, $media, $alt, $extra = array() ) {
		return array(
			'mnata_banner_ad',
			array_merge(
				array(
					'size'    => $size,
					'w'       => $w,
					'h'       => $h,
					'type'    => 'image',
					'image'   => (int) $media,
					'url'     => 'https://example.com/',
					'alt'     => $alt,
					'new_tab' => 1,
					'code'    => '',
					'device'  => 'all',
					'sticky'  => 0,
				),
				$extra
			),
		);
	};

	$block = static function ( $slug, $layout, $count, $style, $color = '#4a1d8f' ) use ( $c ) {
		return array(
			'mnata_category_block',
			array(
				'category'    => isset( $c[ $slug ] ) ? (int) $c[ $slug ] : 0,
				'title'       => '',
				'layout'      => $layout,
				'count'       => $count,
				'style'       => $style,
				'color'       => $color,
				'title_lines' => 0,
				'show_cat'    => 0,
				'show_date'   => 1,
				'more'        => 1,
			),
		);
	};

	return array(
		'header-banner'        => array( $banner( '728x90', 728, 90, $m['b728'], 'Iklan header' ) ),
		'side-banner-left'     => array( $banner( '160x600', 160, 600, $m['b160'], 'Iklan kiri' ) ),
		'side-banner-right'    => array( $banner( '160x600', 160, 600, $m['b160'], 'Iklan kanan' ) ),
		'home-main'            => array(
			array(
				'mnata_slider',
				array(
					'title'       => '',
					'model'       => 'thumbs',
					'category'    => 0,
					'tag'         => '',
					'count'       => 4,
					'title_lines' => 2,
					'show_cat'    => 1,
					'autoplay'    => 1,
				),
			),
			$block( 'nasional', 'carousel', 6, 'theme' ),
			$banner( '728x90', 728, 90, $m['b728'], 'Iklan tengah' ),
			$block( 'ekonomi', 'featured', 4, 'solid', '#1b7f5c' ),
			$block( 'hukum', 'grid-3', 3, 'plain' ),
			$block( 'olahraga', 'grid-3', 3, 'plain' ),
			$block( 'teknologi', 'grid-2', 4, 'plain' ),
			array(
				'mnata_post_list',
				array(
					'title'       => 'Berita Terkini',
					'layout'      => 'list',
					'order'       => 'latest',
					'category'    => 0,
					'tag'         => '',
					'count'       => 8,
					'title_lines' => 0,
					'show_cat'    => 1,
					'show_date'   => 1,
					'paginate'    => 1,
				),
			),
		),
		'home-sidebar'         => array(
			array(
				'mnata_post_list',
				array(
					'title'       => 'Trending',
					'layout'      => 'numbered',
					'order'       => 'popular',
					'category'    => 0,
					'tag'         => '',
					'count'       => 6,
					'title_lines' => 0,
					'show_cat'    => 0,
					'show_date'   => 0,
					'paginate'    => 0,
				),
			),
			array(
				'mnata_weather',
				array(
					'title'       => 'Prakiraan Cuaca',
					'region'      => '31.71.03.1001',
					'slots'       => 5,
					'show_detail' => 1,
				),
			),
			array(
				'mnata_earthquake',
				array(
					'title'    => 'Info Gempa',
					'mode'     => 'latest',
					'count'    => 5,
					'show_map' => 0,
				),
			),
			$banner( '300x250', 300, 250, $m['b300'], 'Iklan sidebar' ),
			array(
				'mnata_tag_cloud',
				array(
					'title' => 'Topik Terkini',
					'count' => 12,
				),
			),
			array(
				'mnata_social_follow',
				array( 'title' => 'Ikuti Kami' ),
			),
			array(
				'mnata_visitors',
				array(
					'title'     => 'Statistik Pengunjung',
					'online'    => 1,
					'today'     => 1,
					'yesterday' => 1,
					'month'     => 1,
					'total'     => 1,
					'pageviews' => 0,
					'offset'    => 0,
				),
			),
			$banner( '300x600', 300, 600, $m['b600'], 'Iklan sticky', array( 'sticky' => 1 ) ),
		),
		'single-in-article'    => array( $banner( '300x250', 300, 250, $m['b300'], 'Iklan dalam artikel' ) ),
		'single-after-content' => array( $banner( '728x90', 728, 90, $m['b728'], 'Iklan setelah artikel' ) ),
	);
}
