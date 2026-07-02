<?php
/**
 * Rank Math integration.
 *
 * @package woodmart
 */

if ( ! defined( 'RANK_MATH_VERSION' ) ) {
	return;
}

add_action( 'category_description', 'woodmart_page_css_files_disable', 9 );
add_action( 'term_description', 'woodmart_page_css_files_disable', 9 );

add_action( 'category_description', 'woodmart_page_css_files_enable', 11 );
add_action( 'term_description', 'woodmart_page_css_files_enable', 11 );

if ( ! function_exists( 'woodmart_rank_math_exclude_layout_post_type' ) ) {
	/**
	 * Excludes WoodMart layout post type from Rank Math sitemap.
	 *
	 * @param array $post_types List of post types.
	 * @return array Filtered post types without WoodMart layout.
	 */
	function woodmart_rank_math_exclude_layout_post_type( $post_types ) {
		if ( isset( $post_types['woodmart_layout'] ) ) {
			unset( $post_types['woodmart_layout'] );
		}

		return $post_types;
	}

	add_filter( 'rank_math/excluded_post_types', 'woodmart_rank_math_exclude_layout_post_type' );
}

if ( ! function_exists( 'woodmart_rank_math_fix_title_shortcode_compatibility' ) ) {
	/**
	 * Fixes WoodMart title shortcode compatibility with Rank Math content analysis.
	 *
	 * @return void
	 */
	function woodmart_rank_math_fix_title_shortcode_compatibility() {
		if ( 'wpb' !== woodmart_get_current_page_builder() ) {
			return;
		}

		?>
		<script type="text/javascript">
			(function ($) {
				wp.hooks.addFilter('rank_math_content', 'rank-math', function (content) {
					return content.replace(/\[woodmart_title\s+([^\]]+)\]/g, function (match, attrString) {
						const parseAttributes = (str) => {
							const attrs = {};
							const regex = /(\w+)=(["'])(.*?)\2/g;
							let m;
							while ((m = regex.exec(str)) !== null) {
								attrs[m[1]] = m[3];
							}
							return attrs;
						};

						const attrs = parseAttributes(attrString);

						const tag = attrs.tag || 'h4';

						if (attrs.title) {
							const wrappedTitle = `<${tag}>${attrs.title}</${tag}>`;

							const newAttrString = attrString.replace(/title=(["'])(.*?)\1/, `title="${wrappedTitle}"`);

							return `[woodmart_title ${newAttrString}]`;
						}

						return match;
					});
				}, 9);
			})(jQuery);
		</script>
		<?php
	}

	add_filter( 'admin_footer-post.php', 'woodmart_rank_math_fix_title_shortcode_compatibility' );
}
