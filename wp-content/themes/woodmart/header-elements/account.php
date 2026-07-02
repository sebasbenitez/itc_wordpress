<?php
/**
 * Header account element.
 *
 * @package woodmart
 */

if ( ! woodmart_woocommerce_installed() ) {
	return '';
}

$links            = woodmart_get_header_links( $params );
$my_account_style = $params['display'];
$login_side       = 'side' === $params['form_display'];
$icon_type        = $params['icon_type'];
$extra_class      = '';

$classes  = '';
$classes .= ( ! empty( $link['dropdown'] ) ) ? ' menu-item-has-children' : '';

if ( 'text' !== $params['display'] && ! empty( $params['icon_design'] ) ) {
	$classes .= ' wd-design-' . $params['icon_design'];
}

if ( 'text' === $params['display'] && ! empty( $params['text_design'] ) ) {
	$classes .= ' wd-design-' . $params['text_design'] . '-text';
}


switch ( $my_account_style ) {
	case 'text':
		$classes .= ' wd-style-text-only';
		break;
	case 'icon':
		$classes .= ' wd-style-icon';
		break;
	case 'icon_with_text':
		$classes .= ' wd-style-text';
		break;
}

if ( ! is_user_logged_in() && $params['login_dropdown'] && $login_side ) {
	woodmart_enqueue_js_script( 'login-sidebar' );
	$classes .= ' login-side-opener';
}

if ( 'custom' === $icon_type && 'text' !== $my_account_style ) {
	$classes .= ' wd-tools-custom-icon';
}

if ( ! empty( $params['bg_overlay'] && ( ( $params['login_dropdown'] && 'dropdown' === $params['form_display'] && ! is_account_page() ) || is_user_logged_in() ) ) ) {
	woodmart_enqueue_js_script( 'menu-overlay' );

	$classes .= ' wd-with-overlay';
}

if (
	isset( $params['wrap_type'], $params['display'], $params['icon_design'] ) &&
	'icon_and_text' === $params['wrap_type'] &&
	'icon_with_text' === $params['display'] &&
	in_array( $params['icon_design'], array( '6', '7' ), true )
) {
	$classes .= ' wd-with-wrap';
}

if ( isset( $id ) ) {
	$classes .= ' whb-' . $id;
}

if ( empty( $links ) ) {
	return '';
}

$show_tools_inner = false;

if (
	isset( $params['display'] ) &&
	(
		(
			'text' === $params['display'] &&
			isset( $params['text_design'] ) &&
			in_array( $params['text_design'], array( '6', '7' ), true )
		) ||
		(
			'icon_with_text' === $params['display'] &&
			isset( $params['icon_design'], $params['wrap_type'] ) &&
			in_array( $params['icon_design'], array( '6', '7' ), true ) &&
			'icon_and_text' === $params['wrap_type']
		) ||
		(
			'text' !== $params['display'] &&
			isset( $params['icon_design'] ) &&
			'8' === $params['icon_design']
		)
	)
) {
	$show_tools_inner = true;
}

?>
<div class="wd-header-my-account wd-tools-element wd-event-hover<?php echo esc_attr( $classes ); ?>">
	<?php woodmart_enqueue_inline_style( 'header-my-account' ); ?>
	<?php foreach ( $links as $key => $link ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
		<a href="<?php echo esc_url( $link['url'] ); ?>" title="<?php echo esc_attr__( 'My account', 'woodmart' ); ?>">
			<?php if ( $show_tools_inner ) : ?>
				<?php
				if ( '8' === $params['icon_design'] ) {
					woodmart_enqueue_inline_style( 'mod-tools-design-8' );
				}
				?>
				<span class="wd-tools-inner">
			<?php endif; ?>

				<?php if ( 'text' !== $my_account_style ) : ?>
					<span class="wd-tools-icon">
						<?php
						if ( 'custom' === $icon_type && 'text' !== $my_account_style ) {
							echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</span>
				<?php endif; ?>

				<?php if ( 'icon' !== $my_account_style ) : ?>
					<span class="wd-tools-text">
						<?php echo wp_kses( $link['label'], 'default' ); ?>
					</span>
				<?php endif; ?>

			<?php if ( $show_tools_inner ) : ?>
				</span>
			<?php endif; ?>
		</a>

		<?php
		if ( ! empty( $link['dropdown'] ) ) {
			echo apply_filters( 'woodmart_account_element_dropdown', $link['dropdown'] );} // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	<?php endforeach; ?>
</div>
