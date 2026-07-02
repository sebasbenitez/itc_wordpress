<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The template for displaying slide.
 *
 * @package woodmart
 */

get_header();

global $post;

$slider_term      = wp_get_post_terms( $post->ID, 'woodmart_slider' );
$slider_id        = $slider_term ? $slider_term[0]->term_id : '';
$carousel_id      = 'slider-' . $slider_id;
$animation        = get_term_meta( $slider_id, 'animation', true );
$arrows_style     = get_term_meta( $slider_id, 'arrows_style', true );
$pagination_style = get_term_meta( $slider_id, 'pagination_style', true );

woodmart_enqueue_inline_style( 'slider' );
woodmart_enqueue_inline_style( 'mod-animations-transform-base' );
woodmart_enqueue_inline_style( 'mod-animations-transform' );
woodmart_enqueue_inline_style( 'mod-transform' );
woodmart_enqueue_js_script( 'slider-element' );
woodmart_enqueue_js_script( 'css-animations' );

if ( 'distortion' === $animation ) {
	woodmart_enqueue_inline_style( 'slider-anim-distortion' );
}

if ( '' === $pagination_style ) {
	$pagination_style = 1;
}

if ( '' === $arrows_style ) {
	$arrows_style = 1;
}

if ( $arrows_style ) {
	woodmart_enqueue_inline_style( 'slider-arrows' );
}

?>
<div class="container">
	<?php woodmart_get_slider_css( $slider_id, $carousel_id, array( $post ) ); ?>
	<div id="<?php echo esc_attr( $carousel_id ); ?>" class="wd-slider wd-carousel-container<?php echo esc_attr( woodmart_get_slider_class( $slider_id ) ); ?>">
		<div class="wd-carousel-inner">
			<div class="wd-carousel wd-grid">
				<div class="wd-carousel-wrap">
					<?php
					$slide_id        = 'slide-' . $post->ID;
					$slide_animation = woodmart_get_post_meta_value( $post->ID, 'slide_animation' );
					$slide_classes   = '';
					$slide_image     = woodmart_get_post_meta_value( $post->ID, 'image' );
					?>

					<div id="<?php echo esc_attr( $slide_id ); ?>" class="wd-carousel-item wd-slide woodmart-loaded active">
						<div class="container wd-slide-container<?php echo esc_attr( woodmart_get_slide_class( $post->ID ) ); ?>">
							<div class="wd-slide-inner<?php echo ( ! empty( $slide_animation ) && 'none' !== $slide_animation ) ? ' wd-animation wd-transform wd-animation-normal wd-animation-' . esc_attr( $slide_animation ) : ''; // phpcs:ignore ?>">
								<?php while ( have_posts() ) : ?>
									<?php the_post(); ?>
									<?php the_content(); ?>
								<?php endwhile; ?>
							</div>
						</div>

						<div class="wd-slide-bg wd-fill">
							<?php if ( ! empty( $slide_image['id'] ) ) : ?>
								<?php
								$image_size = woodmart_get_post_meta_value( $post->ID, 'image_size' );

								if ( 'custom' === $image_size ) {
									$image_width  = get_post_meta( $post->ID, 'image_size_custom_width', true );
									$image_height = get_post_meta( $post->ID, 'image_size_custom_height', true );

									if ( $image_width || $image_height ) {
										$image_size = array( (int) $image_width, (int) $image_height );
									} else {
										$image_size = 'full';
									}
								}

								$image_size = $image_size ? $image_size : 'full';

								echo woodmart_otf_get_image_html( $slide_image['id'], $image_size ); // phpcs:ignore
								?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
