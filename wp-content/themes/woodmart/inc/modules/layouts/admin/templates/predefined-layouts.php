<?php
/**
 * Predefined layouts template.
 *
 * @package woodmart
 *
 * @var array $layouts Layouts.
 */

$current_tab = isset( $_GET['wd_layout_type_tab'] ) ? $_GET['wd_layout_type_tab'] : ''; // phpcs:ignore WordPress.Security

switch ( $current_tab ) {
	case 'checkout':
		$current_tab = 'checkout_form';
		break;
	case 'post':
		$current_tab = 'single_post';
		break;
	case 'archive':
		$current_tab = 'blog_archive';
		break;
	case 'my_account':
		$current_tab = 'my_account_page';
		break;
	case 'loop_item':
		$current_tab = 'product_loop_item';
		break;
}

?>
<?php foreach ( $layouts as $layout_type => $values ) : ?>
	<div class="xts-popup-predefined-layouts xts-images-set<?php echo $current_tab !== $layout_type ? ' xts-hidden' : ''; ?>" data-type="<?php echo esc_attr( $layout_type ); ?>">
		<div class="xts-popup-label"><?php esc_html_e( 'Predefined layouts', 'woodmart' ); ?></div>
		<div class="xts-btns-set">
			<?php foreach ( $values as $layout => $data ) : ?>
				<div class="xts-popup-predefined-layout xts-set-item xts-set-btn-img" data-name="<?php echo esc_attr( $layout ); ?>">
					<img src="<?php echo esc_url( WOODMART_THEME_DIR . '/inc/modules/layouts/admin/predefined/' . $layout_type . '/' . $layout . '/preview.jpg' ); ?>" alt="<?php echo esc_attr__( 'Layout preview', 'woodmart' ); ?>">
					<?php if ( ! empty( $data['url'] ) ) : ?>
						<div class="xts-import-preview-wrap">
							<a href="<?php echo esc_url( $data['url'] ); ?>" class="xts-btn xts-color-primary xts-import-item-preview xts-i-view" target="_blank">
								<?php esc_html_e( 'Live preview', 'woodmart' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; ?>
