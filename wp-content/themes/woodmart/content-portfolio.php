<?php
/**
 * The default template for displaying content
 */

global $woodmart_portfolio_loop, $post;

$style = woodmart_loop_prop( 'portfolio_style' );

$classes[] = 'wd-project';
$classes[] = 'portfolio-entry';
$classes[] = 'wd-col';
$classes[] = 'masonry-item';
$classes[] = 'portfolio-' . $style;

$cats = wp_get_post_terms( get_the_ID(), 'project-cat' );

if ( ! empty( $cats ) ) {
	foreach ( $cats as $key => $project_cat ) {
		$classes[] = 'proj-cat-' . $project_cat->slug;
	}
}

$info_classes = '';

if ( 'text-shown' !== $style ) {
	$info_classes .= ' color-scheme-light';
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() ) : ?>
			<figure class="entry-thumbnail color-scheme-light">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="portfolio-thumbnail">
					<?php echo woodmart_otf_get_image_html( get_post_thumbnail_id(), woodmart_loop_prop( 'portfolio_image_size' ), woodmart_loop_prop( 'portfolio_image_size_custom' ) ); // phpcs:ignore ?>
				</a>
				<div class="wd-portfolio-btns">
					<div class="portfolio-enlarge wd-action-btn wd-style-icon wd-enlarge-icon">
						<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ) ); ?>" data-elementor-open-lightbox="no">
							<span class="wd-action-icon"></span>
							<span class="wd-action-text">
								<?php esc_html_e( 'View Large', 'woodmart' ); ?>
							</span>
						</a>
					</div>
					<?php if ( woodmart_is_social_link_enabled( 'share' ) ) : ?>
						<div class="social-icons-wrapper wd-action-btn wd-style-icon wd-share-icon wd-tltp">
							<a><span class="wd-action-icon"></span></a>
							<div class="tooltip <?php echo is_rtl() ? 'right' : 'left'; ?>">
								<div class="tooltip-arrow"></div>
								<div class="tooltip-inner">
									<?php
									if ( function_exists( 'woodmart_shortcode_social' ) ) {
										echo woodmart_shortcode_social( // phpcs:ignore WordPress.Security
											array(
												'size'  => 'small',
												'style' => 'default',
												'color' => 'light',
											)
										);}
									?>
								</div>
							</div>
						</div>
					<?php endif ?>
				</div>
			</figure>
		<?php endif; ?>

		<div class="portfolio-info<?php echo esc_attr( $info_classes ); ?>">
			<?php if ( ! empty( $cats ) ) : ?>
				<div class="wrap-meta">
					<ul class="proj-cats-list">
						<?php foreach ( $cats as $key => $project_cat ) : ?>
							<?php $classes[] = 'proj-cat-' . $project_cat->slug; ?>
							<li><?php echo esc_html( $project_cat->name ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<div class="wrap-title">
				<h3 class="wd-entities-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h3>
			</div>
		</div>
	</header>
</article>
