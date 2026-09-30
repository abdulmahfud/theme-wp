<?php
/**
 * Article body: inserts the "Baca Juga" box and the "Artikel – Di Dalam Konten" widget area
 * after chosen paragraphs of single posts.
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter the_content on single posts.
 *
 * @param string $content Post content.
 * @return string
 */
function mnews_content_inserts( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$after_ad   = (int) get_theme_mod( 'mnews_ad_in_article_after', 3 );
	$after_more = (int) get_theme_mod( 'mnews_readmore_after', 2 );

	$ad = '';
	if ( $after_ad > 0 && is_active_sidebar( 'single-in-article' ) ) {
		ob_start();
		dynamic_sidebar( 'single-in-article' );
		$ad = '<div class="mnw-in-article">' . ob_get_clean() . '</div>';
	}
	$more = ( $after_more > 0 ) ? mnews_readmore_html() : '';

	if ( '' === $ad && '' === $more ) {
		return $content;
	}

	$parts = preg_split( '/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
	$total = (int) floor( count( $parts ) / 2 ); // Number of closing </p>.
	if ( $total < 2 ) {
		return $content;
	}

	$out = '';
	$n   = 0;
	$len = count( $parts );
	for ( $i = 0; $i < $len; $i += 2 ) {
		$out .= $parts[ $i ];
		if ( isset( $parts[ $i + 1 ] ) ) {
			$out .= $parts[ $i + 1 ];
			++$n;
			// Never insert after the very last paragraph.
			if ( $n < $total ) {
				if ( $n === $after_more ) {
					$out .= $more;
				}
				if ( $n === $after_ad ) {
					$out .= $ad;
				}
			}
		}
	}
	return $out;
}
add_filter( 'the_content', 'mnews_content_inserts', 20 );
