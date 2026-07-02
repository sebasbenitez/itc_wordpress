<?php
/**
 * Yoast SEO integration.
 *
 * @package woodmart
 */

if ( ! function_exists( 'YoastSEO' ) ) {
	return;
}

add_action( 'category_description', 'woodmart_page_css_files_disable', 9 );
add_action( 'term_description', 'woodmart_page_css_files_disable', 9 );

add_action( 'category_description', 'woodmart_page_css_files_enable', 11 );
add_action( 'term_description', 'woodmart_page_css_files_enable', 11 );

if ( ! function_exists( 'woodmart_yoast_exclude_layout_from_accessible_post_types' ) ) {
	/**
	 * Exclude Woodmart layout from Yoast SEO accessible post types.
	 *
	 * @param array $post_types List of accessible post types.
	 *
	 * @return array
	 */
	function woodmart_yoast_exclude_layout_from_accessible_post_types( $post_types ) {
		if ( isset( $post_types['woodmart_layout'] ) ) {
			unset( $post_types['woodmart_layout'] );
		}

		return $post_types;
	}

	add_filter( 'wpseo_accessible_post_types', 'woodmart_yoast_exclude_layout_from_accessible_post_types' );
}

if ( ! function_exists( 'woodmart_yoast_exclude_layout_from_indexable_post_types' ) ) {
	/**
	 * Exclude Woodmart layout from Yoast SEO indexable post types.
	 *
	 * @param array $post_types List of indexable post types.
	 *
	 * @return array
	 */
	function woodmart_yoast_exclude_layout_from_indexable_post_types( $post_types ) {
		$post_types[] = 'woodmart_layout';

		return $post_types;
	}

	add_filter( 'wpseo_indexable_excluded_post_types', 'woodmart_yoast_exclude_layout_from_indexable_post_types' );
}
