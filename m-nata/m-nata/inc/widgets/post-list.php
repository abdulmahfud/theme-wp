<?php
/**
 * Widget: Daftar Berita (latest / popular / random, any layout, optional paginated feed).
 *
 * @package M_Nata
 */

defined( 'ABSPATH' ) || exit;

/**
 * News list widget with several layouts and an optional paginated feed.
 */
class MNata_Widget_Post_List extends MNata_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'mnata_post_list',
			__( 'M-Nata: Daftar Berita', 'm-nata' ),
			__( 'Daftar berita dengan pilihan layout: daftar, ringkas, trending bernomor, grid, atau 1 besar + daftar. Bisa jadi feed "Berita Terkini" dengan halaman.', 'm-nata' )
		);
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'title'       => array(
				'type'    => 'text',
				'label'   => __( 'Judul', 'm-nata' ),
				'default' => '',
			),
			'layout'      => array(
				'type'    => 'select',
				'label'   => __( 'Layout', 'm-nata' ),
				'default' => 'list',
				'choices' => mnata_post_layout_choices(),
			),
			'order'       => array(
				'type'    => 'select',
				'label'   => __( 'Urutan', 'm-nata' ),
				'default' => 'latest',
				'choices' => array(
					'latest'  => __( 'Terbaru', 'm-nata' ),
					'popular' => __( 'Terpopuler (jumlah komentar)', 'm-nata' ),
					'random'  => __( 'Acak', 'm-nata' ),
				),
			),
			'category'    => array(
				'type'    => 'category',
				'label'   => __( 'Kategori', 'm-nata' ),
				'default' => 0,
			),
			'tag'         => array(
				'type'    => 'slug',
				'label'   => __( 'Slug tag (opsional)', 'm-nata' ),
				'default' => '',
			),
			'count'       => array(
				'type'    => 'number',
				'label'   => __( 'Jumlah berita', 'm-nata' ),
				'default' => 5,
				'min'     => 1,
				'max'     => 30,
			),
			'title_lines' => array(
				'type'    => 'number',
				'label'   => __( 'Batas baris judul (0 = otomatis)', 'm-nata' ),
				'default' => 0,
				'min'     => 0,
				'max'     => 6,
				'desc'    => __( 'Judul yang lebih panjang dipotong dengan "…". Otomatis: 3 baris untuk daftar, 2 baris untuk layout lain.', 'm-nata' ),
			),
			'show_cat'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan kategori', 'm-nata' ),
				'default' => 1,
			),
			'show_date'   => array(
				'type'    => 'checkbox',
				'label'   => __( 'Tampilkan waktu terbit', 'm-nata' ),
				'default' => 1,
			),
			'paginate'    => array(
				'type'    => 'checkbox',
				'label'   => __( 'Feed dengan nomor halaman', 'm-nata' ),
				'default' => 0,
				'desc'    => __( 'Untuk "Berita Terkini" di halaman depan. Sebaiknya hanya satu widget per halaman yang memakai ini.', 'm-nata' ),
			),
		);
	}

	/**
	 * Paginated output depends on the current page.
	 *
	 * @param array $i Instance.
	 * @return array
	 */
	protected function cache_vars( $i ) {
		return $i['paginate'] ? array( max( 1, (int) get_query_var( 'paged' ) ) ) : array();
	}

	/**
	 * Print.
	 *
	 * @param array $args Sidebar args.
	 * @param array $i    Instance.
	 */
	protected function render( $args, $i ) {
		$query = mnata_run_query( mnata_query_args( $i ) );
		if ( ! $query->have_posts() ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registered sidebar markup.
		$this->title( $args, $i['title'] );
		mnata_render_posts(
			$query,
			array(
				'layout'      => $i['layout'],
				'show_cat'    => $i['show_cat'],
				'show_date'   => $i['show_date'],
				'title_lines' => $i['title_lines'],
			)
		);
		if ( $i['paginate'] ) {
			mnata_render_pagination( $query );
		}
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
