<?php
/**
 * The search form template.
 *
 * @package woodmart
 */

if ( in_array( $params['display'], array( 'form', 'dropdown' ), true ) && ! empty( $params['bg_overlay'] ) ) {
	woodmart_enqueue_js_script( 'menu-overlay' );
}

if (
	'form' === $params['display'] ||
	(
		'full-screen' === $params['display'] &&
		! empty( $params['full_screen_opener'] ) &&
		'form' === $params['full_screen_opener']
	) ||
	(
		'full-screen-2' === $params['display'] &&
		(
			empty( $params['full_screen_opener'] ) ||
			'form' === $params['full_screen_opener']
		)
	)
) {
	$params['dropdown_search_args']['is_header'] = true;

	woodmart_search_form( $params );

	return;
}

?>
<div class="wd-header-search wd-tools-element<?php echo esc_attr( $params['extra_class'] ); ?>" title="<?php echo esc_attr__( 'Search', 'woodmart' ); ?>">
	<?php
	woodmart_enqueue_inline_style( 'header-search' );

	if ( '8' === $params['icon_design'] ) {
		woodmart_enqueue_inline_style( 'mod-tools-design-8' );
	}
	?>
	<a href="#" rel="nofollow" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>">
		<?php if ( $params['icon_wrapper'] ) : ?>
			<span class="wd-tools-inner">
		<?php endif; ?>

			<?php if ( 'text-only' !== $params['style'] ) : ?>
				<span class="wd-tools-icon">
					<?php if ( 'custom' === $params['icon_type'] ) : ?>
						<?php echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore. ?>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( 'icon' !== $params['style'] ) : ?>
				<span class="wd-tools-text">
					<?php echo esc_html__( 'Search', 'woodmart' ); ?>
				</span>
			<?php endif; ?>

		<?php if ( $params['icon_wrapper'] ) : ?>
			</span>
		<?php endif; ?>
	</a>

	<?php woodmart_search_form( $params ); ?>
</div>
