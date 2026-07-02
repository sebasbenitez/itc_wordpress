<?php
/**
 * Form template.
 *
 * @package woodmart
 */

$predefineds = array(
	'layout-1'  => 'Highly rated',
	'layout-2'  => 'ECO',
	'layout-3'  => 'Bestseller',
	'layout-4'  => 'Top deal',
	'layout-5'  => 'Pre-order',
	'layout-6'  => 'Sale',
	'layout-7'  => 'Featured',
	'layout-8'  => 'Get a gift',
	'layout-9'  => 'AI powered',
	'layout-10' => 'Top',
	'layout-11' => 'Free delivery',
	'layout-12' => 'Best deal 1',
	'layout-13' => 'Best deal 2',
	'layout-14' => 'Custom image 1',
	'layout-15' => 'Custom image 2',
	'layout-16' => 'Hot 1',
	'layout-17' => 'Hot 2',
	'layout-18' => 'Popular now 1',
	'layout-19' => 'Popular now 2',
	'layout-20' => 'Discount 1',
	'layout-21' => 'Discount 2',
	'layout-22' => 'Discount 3',
	'layout-23' => 'New 1',
	'layout-24' => 'New 2',
	'layout-25' => 'New 3',
	'layout-26' => 'New 4',
);

?>
<form>
	<div class="xts-popup-fields">
		<div class="xts-popup-field">
			<label for="wd_custom_label_name">
				<?php esc_html_e( 'Custom label name', 'woodmart' ); ?>
			</label>
			<input class="xts-custom-label-name" id="wd_custom_label_name" name="wd_custom_label_name" type="text" placeholder="<?php esc_attr_e( 'Enter label name', 'woodmart' ); ?>" required value="<?php esc_attr_e( 'New custom label', 'woodmart' ); ?>">
		</div>
	</div>

	<div class="xts-custom-labels-predefined-layouts xts-images-set">
		<div class="xts-popup-label"><?php esc_html_e( 'Predefined labels', 'woodmart' ); ?></div>
		<div class="xts-btns-set">
			<?php foreach ( $predefineds as $predefined_name => $predefined_label ) : ?>
				<div class="xts-custom-label-predefined-layout xts-set-item xts-set-btn-img" data-name="<?php echo esc_attr( $predefined_name ); ?>" data-label="<?php echo esc_attr( $predefined_label ); ?>">
					<img src="<?php echo esc_url( WOODMART_THEME_DIR . '/inc/integrations/woocommerce/modules/custom-labels/admin/predefined/' . $predefined_name . '/preview.jpg' ); ?>" alt="<?php echo esc_attr__( 'Custom label preview', 'woodmart' ); ?>">
					<span class="xts-images-set-lable"><?php echo esc_html( strtoupper( $predefined_name ) ); ?></span>
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


	<div class="xts-popup-actions xts-popup-actions-overlap">
		<button class="xts-add-custom-label-submit xts-btn xts-color-primary xts-i-add" type="submit">
			<?php esc_html_e( 'Create label', 'woodmart' ); ?>
		</button>
	</div>
</form>
