<?php
/**
 * Sticky navigation element template.
 *
 * @package woodmart
 */

$extra_class = '';
$icon_type   = $params['icon_type'];

$extra_class .= ' wd-style-' . $params['style'];

if ( 'custom' === $icon_type ) {
	$extra_class .= ' wd-tools-custom-icon';
}

if ( 'text-only' !== $params['style'] && ! empty( $params['icon_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['icon_design'];
}

if ( 'text-only' === $params['style'] && ! empty( $params['text_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['text_design'] . '-text';
}

if ( isset( $params['wrap_type'], $params['style'], $params['icon_design'] ) && 'icon_and_text' === $params['wrap_type'] && 'text' === $params['style'] && in_array( $params['icon_design'], array( '6', '7' ), true ) ) {
	$extra_class .= ' wd-with-wrap';
}

if ( 'click' === $params['mouse_event'] && ! empty( $params['close_menu_mouseout'] ) ) {
	$extra_class .= ' wd-close-menu-mouseout';
} elseif ( 'hover' === $params['mouse_event'] ) {
	$extra_class .= ' wd-event-hover';
}

if ( isset( $id ) ) {
	$extra_class .= ' whb-' . $id;
}

$show_tools_inner = false;

if (
	isset( $params['style'] ) &&
	(
		(
			'text-only' === $params['style'] &&
			isset( $params['text_design'] ) &&
			in_array( $params['text_design'], array( '6', '7' ), true )
		) ||
		(
			'text' === $params['style'] &&
			isset( $params['icon_design'], $params['wrap_type'] ) &&
			in_array( $params['icon_design'], array( '6', '7' ), true ) &&
			'icon_and_text' === $params['wrap_type']
		) ||
		(
			'text-only' !== $params['style'] &&
			isset( $params['icon_design'] ) &&
			'8' === $params['icon_design']
		)
	)
) {
	$show_tools_inner = true;
}

?>
<div class="wd-tools-element wd-header-sticky-nav<?php echo esc_attr( $extra_class ); ?>">
	<a href="#" rel="nofollow" aria-label="<?php esc_attr_e( 'Open sticky navigation', 'woodmart' ); ?>">
		<?php if ( $show_tools_inner ) : ?>
			<?php
			if ( '8' === $params['icon_design'] ) {
				woodmart_enqueue_inline_style( 'mod-tools-design-8' );
			}
			?>
			<span class="wd-tools-inner">
		<?php endif; ?>

		<?php if ( 'text-only' !== $params['style'] ) : ?>
			<span class="wd-tools-icon">
				<?php if ( 'custom' === $icon_type ) : ?>
					<?php echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>

		<?php if ( 'icon' !== $params['style'] ) : ?>
			<span class="wd-tools-text"><?php echo ! empty( $params['title'] ) ? esc_html( $params['title'] ) : esc_html__( 'Menu', 'woodmart' ); ?></span>
		<?php endif; ?>

		<?php if ( $show_tools_inner ) : ?>
			</span>
		<?php endif; ?>
	</a>
</div>
