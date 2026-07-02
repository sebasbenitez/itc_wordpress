<?php
/**
 * Gutenberg Product Categories Block Render.
 *
 * @package woodmart
 */

if ( ! function_exists( 'wd_gutenberg_product_categories' ) ) {
	/**
	 * Render Product Categories Block.
	 *
	 * @param array $block_attributes Block attributes.
	 * @return false|string
	 */
	function wd_gutenberg_product_categories( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		woodmart_replace_boolean_to_yes_no( array( 'images', 'product_count', 'hide_empty', 'shop_categories_ancestors', 'shop_categories_ancestors', 'hide_pagination_control', 'hide_prev_next_buttons', 'scroll_per_page', 'center_mode', 'wrap', 'autoplay', 'hide_scrollbar', 'autoheight', 'disable_overflow_carousel', 'dynamic_pagination_control', 'scroll_carousel_init' ), $block_attributes );

		if ( true === $block_attributes['mobile_accordion'] ) {
			$block_attributes['mobile_accordion'] = 'yes';
		}

		$block_attributes['is_wpb']            = false;
		$block_attributes['categories_design'] = ! empty( $block_attributes['categories_design'] ) ? $block_attributes['categories_design'] : woodmart_get_opt( 'categories_design' );

		$block_attributes['el_class'] = wd_get_gutenberg_element_classes( $block_attributes );
		$block_attributes['el_id']    = wd_get_gutenberg_element_id( $block_attributes );

		$block_attributes['columns_tablet'] = ! empty( $block_attributes['columnsTablet'] ) ? $block_attributes['columnsTablet'] : 'auto';
		$block_attributes['columns_mobile'] = ! empty( $block_attributes['columnsMobile'] ) ? $block_attributes['columnsMobile'] : 'auto';

		$block_attributes['slides_per_view_tablet'] = ! empty( $block_attributes['slides_per_viewTablet'] ) ? $block_attributes['slides_per_viewTablet'] : 'auto';
		$block_attributes['slides_per_view_mobile'] = ! empty( $block_attributes['slides_per_viewMobile'] ) ? $block_attributes['slides_per_viewMobile'] : 'auto';

		$block_attributes['hide_prev_next_buttons_tablet'] = ! empty( $block_attributes['hide_prev_next_buttonsTablet'] ) ? 'yes' : 'no';
		$block_attributes['hide_prev_next_buttons_mobile'] = ! empty( $block_attributes['hide_prev_next_buttonsMobile'] ) ? 'yes' : 'no';

		$block_attributes['nav_items_gap'] = ! empty( $block_attributes['navItemsGap'] ) ? $block_attributes['navItemsGap'] : '';
		$block_attributes['nav_style']     = ! empty( $block_attributes['navStyle'] ) ? $block_attributes['navStyle'] : '';

		$block_attributes['hide_pagination_control_tablet'] = ! empty( $block_attributes['hide_pagination_controlTablet'] ) ? 'yes' : 'no';
		$block_attributes['hide_pagination_control_mobile'] = ! empty( $block_attributes['hide_pagination_controlMobile'] ) ? 'yes' : 'no';

		$block_attributes['hide_scrollbar_tablet'] = ! empty( $block_attributes['hide_scrollbarTablet'] ) ? 'yes' : 'no';
		$block_attributes['hide_scrollbar_mobile'] = ! empty( $block_attributes['hide_scrollbarMobile'] ) ? 'yes' : 'no';

		$block_attributes['spacing_tablet'] = isset( $block_attributes['spacingTablet'] ) ? $block_attributes['spacingTablet'] : '';
		$block_attributes['spacing_mobile'] = isset( $block_attributes['spacingMobile'] ) ? $block_attributes['spacingMobile'] : '';

		if ( isset( $block_attributes['type'] ) && 'navigation' === $block_attributes['type'] && ( ! empty( $block_attributes['navAlignment'] ) || ! empty( $block_attributes['navAlignmentTablet'] ) || ! empty( $block_attributes['navAlignmentMobile'] ) ) ) {
			$block_attributes['el_class'] .= ' wd-align';
		}

		if ( ! empty( $block_attributes['masonry_grid'] ) ) {
			$block_attributes['style'] = 'masonry';
		}

		if ( ! empty( $block_attributes['img_size'] ) && 'custom' === $block_attributes['img_size'] && ( ! empty( $block_attributes['imgSizeCustomHeight'] ) || ! empty( $block_attributes['imgSizeCustomWidth'] ) ) ) {
			woodmart_set_loop_prop(
				'product_categories_image_size_custom',
				array(
					'width'  => $block_attributes['imgSizeCustomWidth'],
					'height' => $block_attributes['imgSizeCustomHeight'],
				)
			);
		}

		$items_bg_activated = ! empty( $block_attributes['navItemsBgColorCode'] ) ||
			! empty( $block_attributes['navItemsBgColorVariable'] ) ||
			! empty( $block_attributes['navItemsBgHoverColorCode'] ) ||
			! empty( $block_attributes['navItemsBgHoverColorVariable'] ) ||
			! empty( $block_attributes['navItemsBgActiveColorCode'] ) ||
			! empty( $block_attributes['navItemsBgActiveColorVariable'] );

		$items_box_shadow_active = (
			( ! empty( $block_attributes['navItemsBoxShadowColorCode'] ) || ! empty( $block_attributes['navItemsBoxShadowColorVariable'] ) ) &&
			( ! empty( $block_attributes['navItemsBoxShadowHorizontal'] ) || 0 === $block_attributes['navItemsBoxShadowHorizontal'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowVertical'] ) || 0 === $block_attributes['navItemsBoxShadowVertical'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowSpread'] ) || 0 === $block_attributes['navItemsBoxShadowSpread'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowBlur'] ) || 0 === $block_attributes['navItemsBoxShadowBlur'] )
		) ||
		(
			( ! empty( $block_attributes['navItemsBoxShadowHoverColorCode'] ) || ! empty( $block_attributes['navItemsBoxShadowHoverColorVariable'] ) ) &&
			( ! empty( $block_attributes['navItemsBoxShadowHoverHorizontal'] ) || 0 === $block_attributes['navItemsBoxShadowHoverHorizontal'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowHoverVertical'] ) || 0 === $block_attributes['navItemsBoxShadowHoverVertical'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowHoverSpread'] ) || 0 === $block_attributes['navItemsBoxShadowHoverSpread'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowHoverBlur'] ) || 0 === $block_attributes['navItemsBoxShadowHoverBlur'] )
		) ||
		(
			( ! empty( $block_attributes['navItemsBoxShadowActiveColorCode'] ) || ! empty( $block_attributes['navItemsBoxShadowActiveColorVariable'] ) ) &&
			( ! empty( $block_attributes['navItemsBoxShadowActiveHorizontal'] ) || 0 === $block_attributes['navItemsBoxShadowActiveHorizontal'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowActiveVertical'] ) || 0 === $block_attributes['navItemsBoxShadowActiveVertical'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowActiveSpread'] ) || 0 === $block_attributes['navItemsBoxShadowActiveSpread'] ) &&
			( ! empty( $block_attributes['navItemsBoxShadowActiveBlur'] ) || 0 === $block_attributes['navItemsBoxShadowActiveBlur'] )
		);

		$items_border_active = (
			! empty( $block_attributes['navItemsBorderType'] ) &&
			'none' !== $block_attributes['navItemsBorderType'] &&
			! empty( $block_attributes['navItemsBorderWidthTop'] ) &&
			! empty( $block_attributes['navItemsBorderWidthRight'] ) &&
			! empty( $block_attributes['navItemsBorderWidthBottom'] ) &&
			! empty( $block_attributes['navItemsBorderWidthLeft'] )
		) || (
			! empty( $block_attributes['navItemsBorderRadiusTop'] ) &&
			! empty( $block_attributes['navItemsBorderRadiusRight'] ) &&
			! empty( $block_attributes['navItemsBorderRadiusBottom'] ) &&
			! empty( $block_attributes['navItemsBorderRadiusLeft'] )
		) || (
			! empty( $block_attributes['navItemsBorderHoverType'] ) &&
			'none' !== $block_attributes['navItemsBorderHoverType'] &&
			! empty( $block_attributes['navItemsBorderHoverWidthTop'] ) &&
			! empty( $block_attributes['navItemsBorderHoverWidthRight'] ) &&
			! empty( $block_attributes['navItemsBorderHoverWidthBottom'] ) &&
			! empty( $block_attributes['navItemsBorderHoverWidthLeft'] )
		) || (
			! empty( $block_attributes['navItemsBorderActiveType'] ) &&
			'none' !== $block_attributes['navItemsBorderActiveType'] &&
			! empty( $block_attributes['navItemsBorderActiveWidthTop'] ) &&
			! empty( $block_attributes['navItemsBorderActiveWidthRight'] ) &&
			! empty( $block_attributes['navItemsBorderActiveWidthBottom'] ) &&
			! empty( $block_attributes['navItemsBorderActiveWidthLeft'] )
		);

		$menu_classes = '';

		if ( $items_bg_activated || $items_box_shadow_active || $items_border_active ) {
			$menu_classes .= ' wd-add-pd';
		}

		$block_attributes['menu_classes'] = $menu_classes;

		if ( ! empty( $block_attributes['navDisableActiveStyle'] ) ) {
			$block_attributes['menu_classes'] .= ' wd-dis-act';
		}

		return woodmart_shortcode_categories( $block_attributes );
	}
}
