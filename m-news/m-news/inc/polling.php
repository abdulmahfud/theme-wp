<?php
/**
 * Polling: a custom post type with voting handled client-side (REST + cookie/localStorage), never during the
 * server render, so poll cards stay cache-friendly like everything else in the theme (see inc/stats.php for the
 * same pattern: atomic counters, no per-visitor state baked into cached HTML).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "Polling" post type.
 */
function mnews_register_poll_type() {
	register_post_type(
		'mnews_poll',
		array(
			'labels'        => array(
				'name'          => __( 'Polling', 'm-news' ),
				'singular_name' => __( 'Polling', 'm-news' ),
				'add_new_item'  => __( 'Tambah Polling', 'm-news' ),
				'edit_item'     => __( 'Ubah Polling', 'm-news' ),
				'all_items'     => __( 'Semua Polling', 'm-news' ),
				'search_items'  => __( 'Cari Polling', 'm-news' ),
				'not_found'     => __( 'Belum ada polling.', 'm-news' ),
			),
			'public'        => true,
			'menu_icon'     => 'dashicons-chart-bar',
			'menu_position' => 22,
			'supports'      => array( 'title', 'thumbnail' ),
			'has_archive'   => 'polling',
			'rewrite'       => array( 'slug' => 'polling' ),
			'show_in_rest'  => false, // Voting goes through inc/polling.php's own REST route, not wp/v2.
		)
	);
}
add_action( 'init', 'mnews_register_poll_type' );

/**
 * New post types need a rewrite flush once (a real theme update on an already-active site never fires
 * after_switch_theme, so this self-disabling check covers that case too).
 */
function mnews_poll_flush_rewrite_once() {
	if ( '1' !== get_option( 'mnews_poll_rewrite_flushed' ) ) {
		flush_rewrite_rules();
		update_option( 'mnews_poll_rewrite_flushed', '1', false );
	}
}
add_action( 'after_switch_theme', 'mnews_poll_flush_rewrite_once' );
add_action( 'init', 'mnews_poll_flush_rewrite_once', 20 );

/**
 * A poll's options: one per line in the admin textarea, each a stable slug + its label.
 *
 * @param int $post_id Poll ID.
 * @return array<int,array{key:string,label:string}>
 */
function mnews_poll_options( $post_id ) {
	$raw  = (string) get_post_meta( $post_id, '_mnews_poll_options', true );
	$out  = array();
	$seen = array();
	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $i => $line ) {
		$label = trim( $line );
		if ( '' === $label ) {
			continue;
		}
		$key = sanitize_title( $label );
		if ( '' === $key || isset( $seen[ $key ] ) ) {
			$key = 'opsi-' . ( $i + 1 ); // Empty/duplicate slug (e.g. two options that only differ by emoji).
		}
		$seen[ $key ] = true;
		$out[]        = array(
			'key'   => $key,
			'label' => $label,
		);
	}
	return $out;
}

/**
 * Vote counts for every option of a poll (0 for options never voted on yet).
 *
 * @param int   $post_id Poll ID.
 * @param array $options Result of mnews_poll_options() (avoids fetching it twice).
 * @return array<string,int> option key => votes.
 */
function mnews_poll_counts( $post_id, $options ) {
	$counts = array();
	foreach ( $options as $o ) {
		$counts[ $o['key'] ] = (int) get_post_meta( $post_id, '_mnews_poll_votes_' . $o['key'], true );
	}
	return $counts;
}

/**
 * Atomically add one vote to an option (same pattern as mnews_stats_incr(), on postmeta instead of options).
 *
 * @param int    $post_id Poll ID.
 * @param string $key     Option key.
 */
function mnews_poll_incr( $post_id, $key ) {
	global $wpdb;
	$meta_key = '_mnews_poll_votes_' . $key;
	// phpcs:disable WordPress.DB.DirectDatabaseQuery -- atomic counter, see file header.
	$updated = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key ) );
	if ( ! $updated && ! add_post_meta( $post_id, $meta_key, 1, true ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key ) );
	}
	// phpcs:enable
}

/**
 * Meta box: options textarea.
 */
function mnews_poll_add_meta_box() {
	add_meta_box( 'mnews_poll_options', __( 'Opsi Polling', 'm-news' ), 'mnews_poll_render_meta_box', 'mnews_poll', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mnews_poll_add_meta_box' );

/**
 * Render the box. Judul polling = judul post (pertanyaan); di sini hanya daftar jawabannya.
 *
 * @param WP_Post $post Post.
 */
function mnews_poll_render_meta_box( $post ) {
	wp_nonce_field( 'mnews_poll_options', 'mnews_poll_options_nonce' );
	$raw = (string) get_post_meta( $post->ID, '_mnews_poll_options', true );
	printf(
		'<p><label for="mnews_poll_options"><strong>%1$s</strong></label><br><textarea class="widefat" rows="6" id="mnews_poll_options" name="mnews_poll_options">%2$s</textarea><br><small>%3$s</small></p>',
		esc_html__( 'Satu pilihan jawaban per baris (minimal 2).', 'm-news' ),
		esc_textarea( $raw ),
		esc_html__( 'Judul di atas adalah pertanyaan polling. Mengubah teks di sini setelah ada suara masuk tidak menghapus suara, tapi mengganti/menghapus baris berarti suara pada pilihan lama tidak lagi tertaut ke label mana pun.', 'm-news' )
	);
}

/**
 * Save the box.
 *
 * @param int $post_id Post ID.
 */
function mnews_poll_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['mnews_poll_options_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mnews_poll_options_nonce'] ) ), 'mnews_poll_options' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['mnews_poll_options'] ) ) {
		update_post_meta( $post_id, '_mnews_poll_options', sanitize_textarea_field( wp_unslash( $_POST['mnews_poll_options'] ) ) );
	}
}
add_action( 'save_post_mnews_poll', 'mnews_poll_save_meta_box' );

/**
 * Whether this browser already voted on a poll (cookie set by the vote endpoint itself).
 *
 * @param int $post_id Poll ID.
 * @return string Option key voted for, or ''.
 */
function mnews_poll_voted_key( $post_id ) {
	$name = 'mnews_pv_' . $post_id;
	return isset( $_COOKIE[ $name ] ) ? sanitize_title( wp_unslash( $_COOKIE[ $name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only cookie check, not a state-changing request.
}

/**
 * REST routes: cast a vote, read current results.
 */
function mnews_poll_register_routes() {
	register_rest_route(
		'mnews/v1',
		'/poll-vote',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'callback'            => function ( WP_REST_Request $request ) {
				$post_id = absint( $request->get_param( 'poll_id' ) );
				$key     = sanitize_title( (string) $request->get_param( 'option' ) );
				$poll    = get_post( $post_id );

				if ( ! $poll || 'mnews_poll' !== $poll->post_type || 'publish' !== $poll->post_status ) {
					return new WP_Error( 'mnews_poll_not_found', __( 'Polling tidak ditemukan.', 'm-news' ), array( 'status' => 404 ) );
				}
				$options = mnews_poll_options( $post_id );
				$keys    = wp_list_pluck( $options, 'key' );
				if ( ! in_array( $key, $keys, true ) ) {
					return new WP_Error( 'mnews_poll_bad_option', __( 'Pilihan tidak valid.', 'm-news' ), array( 'status' => 422 ) );
				}

				$already = mnews_poll_voted_key( $post_id );
				if ( '' === $already ) {
					mnews_poll_incr( $post_id, $key );
					$already = $key;
				}
				// Idempotent from here: repeat calls (double click, retry) never add a second vote.

				$response = new WP_REST_Response(
					array(
						'voted'  => $already,
						'counts' => mnews_poll_counts( $post_id, $options ),
					),
					200
				);
				$response->header( 'Cache-Control', 'no-store' );
				$response->header( 'Set-Cookie', 'mnews_pv_' . $post_id . '=' . $already . '; Max-Age=31536000; Path=/; SameSite=Lax' );
				return $response;
			},
			'args'                => array(
				'poll_id' => array( 'required' => true ),
				'option'  => array( 'required' => true ),
			),
		)
	);

	register_rest_route(
		'mnews/v1',
		'/poll-results/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => function ( WP_REST_Request $request ) {
				$post_id = absint( $request->get_param( 'id' ) );
				$poll    = get_post( $post_id );
				if ( ! $poll || 'mnews_poll' !== $poll->post_type ) {
					return new WP_Error( 'mnews_poll_not_found', __( 'Polling tidak ditemukan.', 'm-news' ), array( 'status' => 404 ) );
				}
				$options  = mnews_poll_options( $post_id );
				$response = new WP_REST_Response(
					array(
						'voted'  => mnews_poll_voted_key( $post_id ),
						'counts' => mnews_poll_counts( $post_id, $options ),
					),
					200
				);
				$response->header( 'Cache-Control', 'no-store' );
				return $response;
			},
		)
	);
}
add_action( 'rest_api_init', 'mnews_poll_register_routes' );

/**
 * Enqueue the voting script (call from a widget's assets() so it also runs on cache hits).
 */
function mnews_enqueue_poll() {
	if ( mnews_is_amp() ) {
		return;
	}
	wp_enqueue_script(
		'mnews-poll',
		mnews_asset( 'assets/js/poll.js' )[0],
		array(),
		mnews_asset( 'assets/js/poll.js' )[1],
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
	wp_add_inline_script( 'mnews-poll', 'window.mnewsPollApi=' . wp_json_encode( esc_url_raw( rest_url( 'mnews/v1' ) ) ) . ';', 'before' );
}

/**
 * Print one poll as a card: question, image, vote buttons, meta line.
 *
 * Deliberately stateless per visitor: the counts/percentages shown here are the aggregate snapshot at cache
 * time (safe to cache, like the visitor-stats widget), never whether *this* visitor already voted — that would
 * bake one person's cookie into HTML every other visitor gets from the same cache. `poll.js` reads its own
 * vote from localStorage after the page loads and swaps a poll straight to its results view client-side, so
 * this works correctly behind a full-page cache too. Shared by the widget and the archive/single templates.
 *
 * @param int $post_id Poll ID.
 */
function mnews_render_poll_card( $post_id ) {
	$options = mnews_poll_options( $post_id );
	if ( ! $options ) {
		return;
	}
	$counts = mnews_poll_counts( $post_id, $options );
	$total  = array_sum( $counts );
	$fmt    = get_option( 'date_format' );
	?>
	<article class="mnw-poll" data-mnw-poll data-poll-id="<?php echo (int) $post_id; ?>">
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<a class="mnw-poll__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" tabindex="-1" aria-hidden="true">
				<?php
				echo get_the_post_thumbnail(
					$post_id,
					'mnews-card',
					array(
						'loading'  => 'lazy',
						'decoding' => 'async',
						'alt'      => '',
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-generated <img>. 
				?>
			</a>
		<?php endif; ?>
		<div class="mnw-poll__body">
			<h3 class="mnw-poll__q"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
			<div class="mnw-poll__options">
				<?php
				foreach ( $options as $o ) :
					$votes   = isset( $counts[ $o['key'] ] ) ? $counts[ $o['key'] ] : 0;
					$percent = $total > 0 ? round( $votes / $total * 100 ) : 0;
					?>
					<button type="button" class="mnw-poll__opt" data-option="<?php echo esc_attr( $o['key'] ); ?>" data-percent="<?php echo (int) $percent; ?>">
						<span class="mnw-poll__opt-bar" style="--mnw-pct:<?php echo (int) $percent; ?>%"></span>
						<span class="mnw-poll__opt-label"><?php echo esc_html( $o['label'] ); ?></span>
						<span class="mnw-poll__opt-pct"><?php echo (int) $percent; ?>%</span>
					</button>
				<?php endforeach; ?>
			</div>
			<p class="mnw-poll__meta">
				<?php
				/* translators: 1: number of voters, 2: poll date. */
				echo esc_html( sprintf( _n( '%1$s Pemilih · %2$s', '%1$s Pemilih · %2$s', $total, 'm-news' ), number_format_i18n( $total ), get_the_date( $fmt, $post_id ) ) );
				?>
			</p>
		</div>
	</article>
	<?php
}
