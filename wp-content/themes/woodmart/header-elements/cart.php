<?php
/**
 * Header cart element.
 *
 * @package woodmart
 */

$extra_class        = '';
$custom_icon        = '';
$custom_icon_width  = '';
$custom_icon_height = '';
$icon_classes       = '';
$icon_type          = $params['icon_type'];
$cart_position      = $params['position'];

if ( ! woodmart_woocommerce_installed() || 'disable' === $params['style'] || ( ! is_user_logged_in() && woodmart_get_opt( 'login_prices' ) ) ) {
	return;
}

if ( empty( $params['design'] ) ) {
	if ( '5' === $params['style'] ) { // For backward compatibility.
		$extra_class .= ' wd-design-2';
	} else {
		$extra_class .= ' wd-design-' . $params['style'];
	}
}

if ( in_array( $params['design'], array( 'icon', 'text' ), true ) && ! empty( $params['style'] ) ) {
	$extra_class .= ' wd-design-' . $params['style'];
}

if ( in_array( $params['design'], array( 'text-only', 'text-only-sub' ), true ) && ! empty( $params['text_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['text_design'] . '-text';
}

if ( 'bag' === $icon_type ) {
	$icon_classes .= ' wd-icon-alt';
}

if ( 'custom' === $icon_type ) {
	$extra_class .= ' wd-tools-custom-icon';
}

if ( 'side' === $cart_position ) {
	$extra_class .= ' cart-widget-opener';
}

if ( 'side' !== $cart_position && 'without' !== $cart_position ) {
	$extra_class .= ' wd-event-hover';
}

if ( ! empty( $params['design'] ) ) {
	$extra_class .= ' wd-style-' . $params['design'];
}

if ( 'dropdown' === $cart_position && ! empty( $params['bg_overlay'] ) ) {
	woodmart_enqueue_js_script( 'menu-overlay' );

	$extra_class .= ' wd-with-overlay';
}

if ( isset( $params['wrap_type'], $params['design'], $params['style'] ) && 'icon_and_text' === $params['wrap_type'] && 'text' === $params['design'] && in_array( $params['style'], array( '6', '7' ), true ) ) {
	$extra_class .= ' wd-with-wrap';
}

$dropdowns_classes = '';

if ( isset( $id ) ) {
	$extra_class .= ' whb-' . $id;
}

if ( 'light' === whb_get_dropdowns_color() ) {
	$dropdowns_classes .= ' color-scheme-light';
}

$show_tools_inner = false;

if (
	(
		empty( $params['design'] ) &&
		'8' === $params['style']
	) ||
	(
		isset( $params['design'] ) &&
		(
			(
				'text-only' === $params['design'] &&
				isset( $params['text_design'] ) &&
				in_array( $params['text_design'], array( '6', '7' ), true )
			) ||
			(
				'text' === $params['design'] &&
				isset( $params['style'], $params['wrap_type'] ) &&
				in_array( $params['style'], array( '6', '7' ), true ) &&
				'icon_and_text' === $params['wrap_type']
			) ||
			(
				'text-only-sub' === $params['design'] &&
				isset( $params['text_design'], $params['text_wrap_type'] ) &&
				in_array( $params['text_design'], array( '6', '7' ), true ) &&
				'icon_and_text' === $params['text_wrap_type']
			) ||
			(
				in_array( $params['design'], array( 'icon', 'text' ), true ) &&
				isset( $params['style'] ) &&
				'8' === $params['style']
			)
		)
	)
) {
	$show_tools_inner = true;
}

woodmart_enqueue_js_script( 'on-remove-from-cart' );
?>

<div class="wd-header-cart wd-tools-element<?php echo esc_attr( $extra_class ); ?>">
	<?php
	woodmart_enqueue_inline_style( 'header-cart' );

	if ( '3' === $params['style'] ) {
		woodmart_enqueue_inline_style( 'header-cart-design-3' );
	} elseif ( '8' === $params['style'] ) {
		woodmart_enqueue_inline_style( 'mod-tools-design-8' );
	}
	?>
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php echo esc_attr__( 'Shopping cart', 'woodmart' ); ?>">
		<?php if ( $show_tools_inner ) : ?>
			<span class="wd-tools-inner">
		<?php endif; ?>

			<?php if ( ! in_array( $params['design'], array( 'text-only', 'text-only-sub' ), true ) ) : ?>
				<span class="wd-tools-icon<?php echo esc_attr( $icon_classes ); ?>">
					<?php if ( 'custom' === $icon_type ) : ?>
						<?php echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
					<?php if ( in_array( $params['style'], array( '2', '4', '5', '6', '7', '8' ), true ) ) : ?>
						<?php woodmart_cart_count(); ?>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( in_array( $params['design'], array( 'text-only', 'text-only-sub' ), true ) ) : ?>
				<span class="wd-tools-text-cart">
					<span>
						<?php esc_html_e( 'Cart', 'woodmart' ); ?>
					</span>
					<?php if ( 'text-only' === $params['design'] || ( 'text-only-sub' === $params['design'] && ! in_array( $params['text_design'], array( '1' ), true ) ) ) : ?>
						<?php woodmart_cart_count(); ?>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php
			if (
				(
					empty( $params['design'] ) &&
					! in_array( $params['style'], array( '4', '5', '6', '7' ), true )
				) ||
				in_array( $params['design'], array( 'text-only-sub', 'text' ), true )
			) :
				?>
				<span class="wd-tools-text">
				<?php
				if (
					(
						(
							'text' === $params['design'] ||
							empty( $params['design'] )
						) &&
						in_array( $params['style'], array( '1', '3' ), true )
					) ||
					(
						'text-only-sub' === $params['design'] &&
						in_array( $params['text_design'], array( '1' ), true )
					)
				) :
					?>
						<?php woodmart_cart_count(); ?>

						<span class="subtotal-divider">/</span>
					<?php endif; ?>

					<?php woodmart_cart_subtotal(); ?>
				</span>
			<?php endif; ?>

		<?php if ( $show_tools_inner ) : ?>
			</span>
		<?php endif; ?>
	</a>
	<?php if ( 'side' !== $cart_position && 'without' !== $cart_position ) : ?>
		<?php
		woodmart_enqueue_inline_style( 'widget-general' );
		woodmart_enqueue_inline_style( 'widget-shopping-cart' );
		woodmart_enqueue_inline_style( 'widget-product-list', true );

		if ( woodmart_get_opt( 'mini_cart_quantity' ) ) {
			woodmart_enqueue_inline_style( 'woo-mod-quantity' );

			woodmart_enqueue_js_script( 'mini-cart-quantity' );
			woodmart_enqueue_js_script( 'woocommerce-quantity' );
		}
		?>
		<div class="wd-dropdown wd-dropdown-cart<?php echo esc_attr( $dropdowns_classes ); ?>">
			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</div>
	<?php endif; ?>
</div>
