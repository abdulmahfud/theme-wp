<?php
/**
 * SEO markup: Open Graph / Twitter Card, description, and NewsArticle + BreadcrumbList JSON-LD.
 * Skipped completely when a dedicated SEO plugin is active (avoids duplicate tags).
 *
 * @package M_News
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a known SEO plugin is handling meta/schema.
 *
 * @return bool
 */
function mnews_seo_plugin_active() {
	$active = defined( 'WPSEO_VERSION' )
		|| class_exists( 'RankMath' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' );
	return (bool) apply_filters( 'mnews_seo_plugin_active', $active );
}

/**
 * Plain-text description of the current view.
 *
 * @return string
 */
function mnews_meta_description() {
	if ( is_singular() ) {
		$text = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', get_queried_object_id() ) ) );
		return wp_trim_words( trim( preg_replace( '/\s+/', ' ', $text ) ), 32, '…' );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$term_desc = trim( wp_strip_all_tags( term_description() ) );
		if ( '' !== $term_desc ) {
			return wp_trim_words( $term_desc, 32, '…' );
		}
	}
	$tagline = (string) get_bloginfo( 'description' );
	return '' !== $tagline ? $tagline : (string) get_bloginfo( 'name' );
}

/**
 * Best image for sharing: featured image, then the site logo.
 *
 * @return array{0:string,1:int,2:int}|null
 */
function mnews_social_image() {
	$id = is_singular() ? get_post_thumbnail_id( get_queried_object_id() ) : 0;
	if ( ! $id ) {
		$id = (int) get_theme_mod( 'custom_logo' );
	}
	if ( ! $id ) {
		return null;
	}
	$src = wp_get_attachment_image_src( $id, 'full' );
	return $src ? array( $src[0], (int) $src[1], (int) $src[2] ) : null;
}

/**
 * Print Open Graph, Twitter Card and description tags.
 */
function mnews_print_meta_tags() {
	if ( mnews_seo_plugin_active() || is_404() || is_search() ) {
		return;
	}

	$is_article = is_singular( 'post' );
	$title      = wp_get_document_title();
	$desc       = mnews_meta_description();
	$url        = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$img        = mnews_social_image();

	$tags = array(
		'og:type'      => $is_article ? 'article' : 'website',
		'og:site_name' => get_bloginfo( 'name' ),
		'og:locale'    => str_replace( '-', '_', get_bloginfo( 'language' ) ),
		'og:title'     => $title,
		'og:url'       => $url,
	);
	if ( $desc ) {
		$tags['og:description'] = $desc;
	}
	if ( $img ) {
		$tags['og:image']        = $img[0];
		$tags['og:image:width']  = (string) $img[1];
		$tags['og:image:height'] = (string) $img[2];
	}
	if ( $is_article ) {
		$tags['article:published_time'] = get_the_date( 'c', get_queried_object_id() );
		$tags['article:modified_time']  = get_the_modified_date( 'c', get_queried_object_id() );
	}

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	foreach ( $tags as $property => $content ) {
		printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $img ? 'summary_large_image' : 'summary' );
}
add_action( 'wp_head', 'mnews_print_meta_tags', 5 );

/**
 * Print JSON-LD for single posts.
 */
function mnews_print_json_ld() {
	if ( mnews_seo_plugin_active() || ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	$cats    = get_the_category( $post_id );
	$img     = mnews_social_image();

	$article = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'NewsArticle',
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
		'headline'         => wp_html_excerpt( wp_strip_all_tags( get_the_title( $post_id ) ), 110 ),
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
		),
	);

	$author_id = (int) get_post_field( 'post_author', $post_id );
	$author    = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
	if ( $author ) {
		$article['author'] = array(
			'@type' => 'Person',
			'name'  => $author,
			'url'   => get_author_posts_url( $author_id ),
		);
	} else {
		$article['author'] = array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
		);
	}

	$logo = (int) get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_src( $logo, 'full' );
		if ( $src ) {
			$article['publisher']['logo'] = array(
				'@type'  => 'ImageObject',
				'url'    => $src[0],
				'width'  => (int) $src[1],
				'height' => (int) $src[2],
			);
		}
	}
	if ( $img ) {
		$article['image'] = array( $img[0] );
	}
	$desc = mnews_meta_description();
	if ( $desc ) {
		$article['description'] = $desc;
	}
	if ( $cats ) {
		$article['articleSection'] = $cats[0]->name;
	}

	$crumbs = array();
	foreach ( mnews_breadcrumb_items( true ) as $pos => $item ) {
		$entry = array(
			'@type'    => 'ListItem',
			'position' => $pos + 1,
			'name'     => $item[0],
		);
		if ( $item[1] ) {
			$entry['item'] = $item[1];
		}
		$crumbs[] = $entry;
	}
	$breadcrumb = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $crumbs,
	);

	foreach ( array( $article, $breadcrumb ) as $data ) {
		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with hex-encoded tags.
		);
	}
}
add_action( 'wp_head', 'mnews_print_json_ld', 6 );
