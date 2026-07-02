<?php
/**
 * Header wishlist element.
 *
 * @package woodmart
 */

if ( ! woodmart_woocommerce_installed() || ! woodmart_get_opt( 'wishlist', 1 ) || ( woodmart_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) ) {
	return;
}

$extra_class = '';
$icon_type   = $params['icon_type'];

$extra_class .= ' wd-style-' . $params['design'];

if ( ! $params['hide_product_count'] ) {
	$extra_class .= ' wd-with-count';
}

if ( 'custom' === $icon_type ) {
	$extra_class .= ' wd-tools-custom-icon';
}

if ( 'text-only' !== $params['design'] && ! empty( $params['icon_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['icon_design'];
}

if ( 'text-only' === $params['design'] && ! empty( $params['text_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['text_design'] . '-text';
}

if ( isset( $params['wrap_type'], $params['design'], $params['icon_design'] ) && 'icon_and_text' === $params['wrap_type'] && 'text' === $params['design'] && in_array( $params['icon_design'], array( '6', '7' ), true ) ) {
	$extra_class .= ' wd-with-wrap';
}

if ( isset( $id ) ) {
	$extra_class .= ' whb-' . $id;
}

$show_tools_inner = false;

if (
	isset( $params['design'] ) &&
	(
		(
			'text-only' === $params['design'] &&
			isset( $params['text_design'] ) &&
			in_array( $params['text_design'], array( '6', '7' ), true )
		) ||
		(
			'text' === $params['design'] &&
			isset( $params['icon_design'], $params['wrap_type'] ) &&
			in_array( $params['icon_design'], array( '6', '7' ), true ) &&
			'icon_and_text' === $params['wrap_type']
		) ||
		(
			'text-only' !== $params['design'] &&
			isset( $params['icon_design'] ) &&
			'8' === $params['icon_design']
		)
	)
) {
	$show_tools_inner = true;
}

woodmart_enqueue_js_script( 'wishlist' );
?>

<div class="wd-header-wishlist wd-tools-element<?php echo esc_attr( $extra_class ); ?>" title="<?php echo esc_attr__( 'My Wishlist', 'woodmart' ); ?>">
	<a href="<?php echo esc_url( woodmart_get_wishlist_page_url() ); ?>" title="<?php echo esc_attr__( 'Wishlist products', 'woodmart' ); ?>">
		<?php if ( $show_tools_inner ) : ?>
			<?php
			if ( '8' === $params['icon_design'] ) {
				woodmart_enqueue_inline_style( 'mod-tools-design-8' );
			}
			?>
			<span class="wd-tools-inner">
		<?php endif; ?>

			<?php if ( 'text-only' !== $params['design'] ) : ?>
				<span class="wd-tools-icon">
					<?php
					if ( 'custom' === $icon_type ) {
						echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>

					<?php if ( ! $params['hide_product_count'] ) : ?>
						<span class="wd-tools-count">
							<?php echo esc_html( woodmart_get_wishlist_count() ); ?>
						</span>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( 'icon' !== $params['design'] ) : ?>
				<span class="wd-tools-text">
					<?php esc_html_e( 'Wishlist', 'woodmart' ); ?>
				</span>
			<?php endif; ?>

			<?php if ( 'text-only' === $params['design'] && ! $params['hide_product_count'] ) : ?>
				<span class="wd-tools-count">
					<?php echo esc_html( woodmart_get_wishlist_count() ); ?>
				</span>
			<?php endif; ?>

		<?php if ( $show_tools_inner ) : ?>
			</span>
		<?php endif; ?>
	</a>
</div>
